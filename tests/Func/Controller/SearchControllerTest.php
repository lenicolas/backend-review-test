<?php

namespace App\Tests\Func\Controller;

use App\DataFixtures\EventFixtures;
use Doctrine\ORM\Tools\SchemaTool;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    private static $client;

    protected function setUp(): void
    {
        static::$client = static::createClient();

        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $metaData = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metaData);

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();

        $this->databaseTool->loadFixtures(
            [EventFixtures::class]
        );
    }

    public function testSearchShouldReturnEmptyResponse()
    {
        $client = static::$client;

        $response = $client->request(
            'GET',
            sprintf('/api/search?date=%d&keyword=%s', "2024-03-06", "test"),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertEmpty($response);
    }
}
