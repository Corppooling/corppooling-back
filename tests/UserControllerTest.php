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

        // $this->assertResponseIsSuccessful();
        $this->assertResponseRedirects();
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

    public function testCompanyInTrips(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('user2@example.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        $company = $testUser->getCompany();
        $companyId = $company->getId();
        // check reponse
        $client->request('GET', "/api/trips?company.id=$companyId");
        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $responseData = json_decode($response->getContent(), true);
        if (count($responseData['hydra:member'])) {
            foreach ($responseData['hydra:member'] as $value) {
                $this->assertTrue($value['company']['id'] === $companyId);
            }
        } else {
            $this->assertTrue($responseData['hydra:totalItems'] === 0);
        }
    }

    // public function testRegister(): void
    // {
    //     $client = static::createClient();
    //     $userRepository = static::getContainer()->get(UserRepository::class);
    //     $client->request('POST', '/api/register');


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
