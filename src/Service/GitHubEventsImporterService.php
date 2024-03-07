<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Event;
use App\Utils\FileStreamInterface;
use App\Utils\GzFileStreamInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GitHubEventsImporterService implements GitHubEventsImporterServiceInterface
{
    private EntityManagerInterface $entityManager;
    private FileStreamInterface $fileStream;
    private GzFileStreamInterface $gzFileStream;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileStreamInterface $fileStream,
        GzFileStreamInterface $gzFileStream
    )
    {
        $this->entityManager = $entityManager;
        $this->fileStream = $fileStream;
        $this->gzFileStream = $gzFileStream;
    }

    public function importEvents(string $date, string $hour): void
    {
        $dateTime = $date . '-' . $hour;
        $filename = 'https://data.gharchive.org/' . $dateTime . '.json.gz';
        $jsonData = $this->fileStream->getFileContents($filename);

        $handle = $this->gzFileStream->gzOpen('data://text/plain;base64,' . base64_encode($jsonData), 'rb');

        if (!$handle) {
            throw new \RuntimeException('Error opening the file: ' . $filename);
        }

        while (false !== $this->gzFileStream->gzEof($handle)) {
            $line = json_decode($this->gzFileStream->gzGets($handle, null), true);
            try {
                if (!empty($line)) {
                    $event = $this->denormalizeEvent($line);
                    $this->entityManager->persist($event);
                    $this->entityManager->flush();
                }
            } catch (\InvalidArgumentException $invalidArgumentException) {
                throw new \InvalidArgumentException('Error invalid argument ' . $invalidArgumentException->getMessage());
            } catch (\Exception $exception) {
                throw new \Exception('Error processing event: ' . $exception->getMessage());
            } catch (ExceptionInterface $e) {
                throw new \Exception('Error deserializing event: ' . $e->getMessage());
            }
        }

        $this->gzFileStream->gzClose($handle);
    }

    /**
     * @throws ExceptionInterface
     */
    private function denormalizeEvent(array $data): ?Event
    {
        $normalizers = [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];
        $serializer = new Serializer($normalizers, ['json' => new JsonEncoder()]);
        return $serializer->denormalize($data, Event::class);
    }
}
