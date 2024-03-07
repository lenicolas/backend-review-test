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
    public const PULL_REQUEST_EVENT_REVIEW_COMMENT_EVENT = 'PullRequestReviewCommentEvent';
    public const COMMIT_COMMENT_EVENT = 'CommitCommentEvent';
    public const ISSUE_COMMENT_EVENT = 'IssueCommentEvent';

    protected static array $choices = [
        self::COMMIT => 'Commit',
        self::COMMENT => 'Comment',
        self::PULL_REQUEST => 'Pull Request',
        self::PUSH_EVENT => 'Push Event',
        self::PULL_REQUEST_EVENT => 'Push Event',
        self::ISSUE_COMMENT_EVENT => 'Issue Comment Event',
        self::COMMIT_COMMENT_EVENT => 'Commit Comment Event',
    ];

    /**
     * @var array|string[]
     */
    protected static array $eventTypeGitHubArchives = [
        self::PUSH_EVENT => self::COMMIT,
        self::PULL_REQUEST => self::PULL_REQUEST,
        self::COMMIT => self::COMMIT,
        self::COMMENT => self::COMMENT,
        self::PULL_REQUEST_EVENT_REVIEW_COMMENT_EVENT => self::COMMENT,
        self::ISSUE_COMMENT_EVENT => self::COMMENT,
        self::COMMIT_COMMENT_EVENT => self::COMMENT,
    ];

    /**
     * @return array|string[]
     */
    public static function getEventTypeGitHubArchives(): array
    {
        return self::$eventTypeGitHubArchives;
    }
}
