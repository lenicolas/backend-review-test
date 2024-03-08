<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\Actor;
use App\Entity\Repo;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    protected Actor $actor;
    protected Repo $repo;
    protected function setUp(): void
    {
        $this->actor = $this->getMockBuilder(Actor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->repo = $this->getMockBuilder(Repo::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testSetPayload()
    {
        $event = new Event(1, 'PushEvent', $this->actor, $this->repo, ['size' => 2], new \DateTimeImmutable(), 'comment');
        $event->setPayload(['size' => 3]);

        $this->assertEquals(3, $event->getCount());
    }

    public function test__construct()
    {
        $event = new Event(1, 'PushEvent', $this->actor, $this->repo, ['size' => 2], new \DateTimeImmutable(), 'comment');

        $this->assertInstanceOf(Event::class, $event);
    }

    public function testSetType()
    {
        $event = new Event(1, 'PushEvent', $this->actor, $this->repo, ['size' => 2], new \DateTimeImmutable(), 'comment');
        $event->setType('PullRequestEvent');

        $this->assertEquals('PR', $event->getType());
    }
}
