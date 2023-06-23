<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testDocAccess(): void
    {
        $client = static::createClient();

        $client->request('GET', '/docs');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'API Platform');
    }

    public function testAdminRedirect(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        $this->assertResponseRedirects();
        $this->assertSelectorTextContains('title', 'login');
    }

    public function testAdminAccess(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $adminUser = $userRepository->findOneByEmail('admin@corppooling.com');
        $client->loginUser($adminUser);

        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
    }

    public function testLogin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('user1@example.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        // check reponse
        $client->request('GET', '/api/user/me');
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['email'] === $testUser->getEmail());
    }

    // public function testRegister(): void
    // {
    //     $client = static::createClient();
    //     $userRepository = static::getContainer()->get(UserRepository::class);

    //     // retrieve the test user
    //     $testUser = $userRepository->findOneByEmail('user1@example.com');

    //     // simulate $testUser being logged in
    //     $client->loginUser($testUser);

    //     // check reponse
    //     $client->request('GET', '/api/user/me');
    //     $response = $client->getResponse();
    //     $this->assertResponseIsSuccessful();
    //     $responseData = json_decode($response->getContent(), true);
    //     $this->assertTrue($responseData['email'] === $testUser->getEmail());
    // }
}
