<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class RegistrationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testRegister(): void
    {
        // Register a new user
        $this->client->followRedirects();
        
        $this->client->request('GET', '/register');
        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Cadastrar');

        $this->client->submitForm('Cadastrar', [
            'registration_form[email]' => 'me@example.com',
            'registration_form[plainPassword]' => 'password',
            'registration_form[agreeTerms]' => true,
        ]);

        // Ensure the response redirects after submitting the form, the user exists, and is not verified
        self::assertCount(1, $this->userRepository->findByEmail('me@example.com'));
        self::assertFalse(($user = $this->userRepository->findOneByEmail('me@example.com'))->isVerified());

        // Login the new user
        $this->client->loginUser($user);
    }
}
