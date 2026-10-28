<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

final class LoginLogoutControllerTest extends WebTestCase
{
    public function testLoginPageDisplaysTheForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form[action="/login"][method="post"]');
        self::assertSelectorExists('input[name="email"]');
        self::assertSelectorExists('input[name="password"]');
        self::assertSelectorExists('input[name="_csrf_token"]');
    }

    public function testValidCredentialsCreateASessionAndAllowLogout(): void
    {
        $client = $this->createAuthenticationClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            'email' => 'login-test@example.com',
            'password' => 'test-password',
        ]);

        self::assertResponseRedirects('/oeuvre');

        $client->request('GET', '/login');
        self::assertResponseRedirects('/oeuvre');

        $client->request('POST', '/logout', ['_csrf_token' => 'csrf-token'], server: [
            'HTTP_ORIGIN' => 'http://localhost',
        ]);
        self::assertResponseRedirects('/login');

        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="password"]');
    }

    public function testInvalidPasswordDisplaysAnErrorAndKeepsTheEmail(): void
    {
        $client = $this->createAuthenticationClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            'email' => 'login-test@example.com',
            'password' => 'incorrect-password',
        ]);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.alert-danger');
        self::assertInputValueSame('email', 'login-test@example.com');
        self::assertInputValueSame('password', '');
    }

    public function testInvalidCsrfTokenRejectsValidCredentials(): void
    {
        $client = $this->createAuthenticationClient();
        $client->request('GET', '/login');
        $client->submitForm('Se connecter', [
            'email' => 'login-test@example.com',
            'password' => 'test-password',
            '_csrf_token' => 'invalid-token',
        ]);

        self::assertResponseRedirects('/login');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.alert-danger');
    }

    public function testUserUsesEmailAsIdentifierAndKeepsOnlyAssignedRoles(): void
    {
        $user = (new User())->setEmail('login-test@example.com')->setRoles(['ROLE_BENEVOLE']);

        self::assertSame('login-test@example.com', $user->getUserIdentifier());
        self::assertSame(['ROLE_BENEVOLE'], $user->getRoles());
    }

    private function createAuthenticationClient(): KernelBrowser
    {
        $client = static::createClient();
        $client->disableReboot();

        $user = new InMemoryUser('login-test@example.com', null, ['ROLE_BENEVOLE']);
        $password = static::getContainer()->get(UserPasswordHasherInterface::class)
            ->hashPassword($user, 'test-password');

        static::getContainer()->set('security.user.provider.concrete.app_user_provider', new InMemoryUserProvider([
            'login-test@example.com' => ['password' => $password, 'roles' => ['ROLE_BENEVOLE']],
        ]));

        return $client;
    }
}
