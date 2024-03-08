<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'repo')]
#[ORM\Entity]
class Repo
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue('NONE')]
    private int $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string')]
    #[Assert\Url]
    private string $url;

    public function __construct(int $id, string $name, string $url)
    {
        $this->setId($id);
        $this->setName($name);
        $this->setUrl($url);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setId(int $id): Repo
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): Repo
    {
        $this->name = $name;
        return $this;
    }

    public function setUrl(string $url): Repo
    {
        $this->url = $url;
        return $this;
    }
}
