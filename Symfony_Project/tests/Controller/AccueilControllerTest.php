<?php

namespace App\Tests\Controller;

use App\Repository\ArtisteRepository;
use App\Repository\BenevoleRepository;
use App\Repository\LieuRepository;
use App\Repository\OeuvreRepository;
use App\Repository\PhotoRepository;
use App\Repository\TechniqueRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

final class AccueilControllerTest extends WebTestCase
{
    public function testVolunteerCanOpenEverySectionFromTheHomePage(): void
    {
        $client = $this->createAuthenticatedClient(['ROLE_BENEVOLE']);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Accueil des bénévoles');
        self::assertSelectorCount(6, '.card');

        foreach (self::sections() as [$path, $repositoryClass, $title]) {
            $repository = $this->createMock($repositoryClass);
            $repository->expects(self::once())->method('findAll')->willReturn([]);
            static::getContainer()->set($repositoryClass, $repository);

            $client->clickLink('Gérer les '.mb_strtolower($title));

            self::assertResponseIsSuccessful();
            self::assertSelectorTextContains('h1', $title);
            self::assertSelectorExists('a[href="'.$path.'/new"]');
            $client->clickLink('Accueil');
            self::assertResponseIsSuccessful();
        }
    }

    #[DataProvider('protectedPaths')]
    public function testAnonymousVisitorsAreSentToLogin(string $path): void
    {
        $client = static::createClient();
        $client->request('GET', $path);

        self::assertResponseRedirects('/login');
    }

    #[DataProvider('protectedPaths')]
    public function testUsersWithoutTheVolunteerRoleCannotManageContent(string $path): void
    {
        $client = $this->createAuthenticatedClient(['ROLE_USER']);
        $client->request('GET', $path);

        self::assertResponseStatusCodeSame(403);
    }

    public static function sections(): iterable
    {
        yield ['/oeuvre', OeuvreRepository::class, 'Œuvres'];
        yield ['/photo', PhotoRepository::class, 'Photos'];
        yield ['/technique', TechniqueRepository::class, 'Techniques'];
        yield ['/artiste', ArtisteRepository::class, 'Artistes'];
        yield ['/lieu', LieuRepository::class, 'Lieux'];
        yield ['/benevole', BenevoleRepository::class, 'Bénévoles'];
    }

    public static function protectedPaths(): iterable
    {
        yield 'accueil' => ['/'];
        foreach (self::sections() as [$path]) {
            yield $path => [$path];
            yield $path.' nouveau' => [$path.'/new'];
        }
    }

    private function createAuthenticatedClient(array $roles): KernelBrowser
    {
        $client = static::createClient();
        $client->disableReboot();
        static::getContainer()->set('security.user.provider.concrete.app_user_provider', new InMemoryUserProvider([
            'benevole-test@example.com' => ['password' => null, 'roles' => $roles],
        ]));
        $client->loginUser(new InMemoryUser('benevole-test@example.com', null, $roles));

        return $client;
    }
}
