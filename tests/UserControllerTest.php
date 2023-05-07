<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('user1@example.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/api/user/me');

        $this->assertResponseIsSuccessful();
    }
}
