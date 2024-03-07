<?php

namespace App\Entity;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EventType extends AbstractEnumType
{
    public const COMMIT = 'COM';
    public const COMMENT = 'MSG';
    public const PULL_REQUEST = 'PR';
    public const PUSH_EVENT = 'PushEvent';
    public const PULL_REQUEST_EVENT = 'PullRequestEvent';

    protected static array $choices = [
        self::COMMIT => 'Commit',
        self::COMMENT => 'Comment',
        self::PULL_REQUEST => 'Pull Request',
        self::PUSH_EVENT => 'Push Event',
        self::PULL_REQUEST_EVENT => 'Push Event',
    ];

    protected static array $eventTypeGitHubArchives = [
        self::PUSH_EVENT => self::COMMIT,
        self::PULL_REQUEST => self::PULL_REQUEST,
    ];

    public static function getEventTypeGitHubArchives(): array
    {
        return self::$eventTypeGitHubArchives;
    }
}
