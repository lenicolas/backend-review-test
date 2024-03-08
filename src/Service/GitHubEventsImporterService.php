<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Repository\ReadEventRepositoryInterface;
use App\Utils\FileDataReaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GitHubEventsImporterService implements GitHubEventsImporterServiceInterface
{
    private EntityManagerInterface $entityManager;
    private FileDataReaderInterface $fileDataReader;
    private ReadEventRepositoryInterface $readEventRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileDataReaderInterface $fileDataReader,
        ReadEventRepositoryInterface $readEventRepository
    ) {
        $this->entityManager = $entityManager;
        $this->fileDataReader = $fileDataReader;
        $this->readEventRepository = $readEventRepository;
    }

    public function importEvents(string $date, string $hour): void
    {
        $dateTime = $date.'-'.$hour;
        $filename = 'https://data.gharchive.org/'.$dateTime.'.json.gz';
        $jsonData = $this->fileDataReader->getFileData($filename);
        $handle = $this->fileDataReader->openFile($jsonData);
        while (false === $this->fileDataReader->isEndOfFile($handle)) {
            try {
                $line = $this->fileDataReader->readLine($handle);
                $this->processEvent($line);
            } catch (\InvalidArgumentException $invalidArgumentException) {
                // log type not manage
            } catch (\Exception $exception) {
                // log Error processing event: '.$exception->getMessage());
            } catch (ExceptionInterface $e) {
                // log Error deserializing event
            }
        }
    }

    /**
     * @throws ExceptionInterface
     */
    private function processEvent(string $line): void
    {
        $lineDecode = json_decode($line, true);
        if (!empty($lineDecode)) {
            $event = $this->denormalizeEvent($lineDecode);
            if (!($event instanceof Event)) {
                throw new \RuntimeException('Invalid event, cannot create event');
            }

            if ($this->readEventRepository->exist($event->getId())) {
                throw new InvalidArgumentException('Event already exist');
            }

            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }
    }

    /**
     * @param array<\Iterator> $data
     *
     * @throws ExceptionInterface
     */
    private function denormalizeEvent(array $data): ?Event
    {
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];
        $serializer = new Serializer($normalizers, ['json' => new JsonEncoder()]);

        return $serializer->denormalize($data, Event::class);
    }

}
