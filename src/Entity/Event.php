<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'event')]
#[ORM\Index(
    columns: ['type'],
    name: 'IDX_EVENT_TYPE'
)]
#[ORM\Entity]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue('NONE')]
    private int $id;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $count = 1;

    #[ORM\ManyToOne(targetEntity: Actor::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'actor_id', referencedColumnName: 'id')]
    private Actor $actor;

    #[ORM\ManyToOne(targetEntity: Repo::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'repo_id', referencedColumnName: 'id')]
    private Repo $repo;

    /**
     * @var \Iterator[]
     */
    #[ORM\Column(type: 'json', nullable: false, options: ['jsonb' => true])]
    private array $payload;

    #[ORM\Column(type: 'datetime_immutable', nullable: false)]
    #[Assert\DateTime]
    private \DateTimeImmutable $createAt;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    private ?string $comment;

    /**
     * @param array<\Iterator> $payload
     */
    public function __construct(int $id, string $type, Actor $actor, Repo $repo, array $payload, \DateTimeImmutable $createdAt, ?string $comment)
    {
        $this->setId($id);
        $this->setType($type);
        $this->setActor($actor);
        $this->setRepo($repo);
        $this->setPayload($payload);
        $this->setCreateAt($createdAt);
        $this->setComment($comment);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getActor(): Actor
    {
        return $this->actor;
    }

    public function getRepo(): Repo
    {
        return $this->repo;
    }

    /**
     * @return \Iterator[]
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreateAt(): \DateTimeImmutable
    {
        return $this->createAt;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setId(int $id): Event
    {
        $this->id = $id;
        return $this;
    }

    public function setType(string $type): Event
    {
        EventType::assertValidChoice($type);
        $this->type = EventType::getEventTypeGitHubArchives()[$type];
        return $this;
    }

    public function setCount(int $count): Event
    {
        $this->count = $count;
        return $this;
    }

    public function setActor(Actor $actor): Event
    {
        $this->actor = $actor;
        return $this;
    }

    public function setRepo(Repo $repo): Event
    {
        $this->repo = $repo;
        return $this;
    }

    public function setPayload(array $payload): Event
    {
        $this->payload = $payload;
        if (EventType::COMMIT === $this->type) {
            $count = $payload['size'] ?? 1;
            $this->setCount($count);
        }
        return $this;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): Event
    {
        $this->createAt = $createAt;
        return $this;
    }

    public function setComment(?string $comment): Event
    {
        $this->comment = $comment;
        return $this;
    }
}
