<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'actor')]
#[ORM\Entity]
class Actor
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue('NONE')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $login;

    #[ORM\Column(type: 'string')]
    #[Assert\Url]
    private string $url;

    #[ORM\Column(type: 'string')]
    #[Assert\Url]
    private string $avatarUrl;

    public function __construct(int $id, string $login, string $url, string $avatarUrl)
    {
        $this->setId($id);
        $this->setLogin($login);
        $this->setUrl($url);
        $this->setAvatarUrl($avatarUrl);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function setId(int $id): Actor
    {
        $this->id = $id;
        return $this;
    }

    public function setLogin(string $login): Actor
    {
        $this->login = $login;
        return $this;
    }

    public function setUrl(string $url): Actor
    {
        $this->url = $url;
        return $this;
    }

    public function setAvatarUrl(string $avatarUrl): Actor
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }


}
