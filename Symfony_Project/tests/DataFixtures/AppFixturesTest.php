<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\Entity\Artiste;
use App\Entity\Benevole;
use App\Entity\Lieu;
use App\Entity\Oeuvre;
use App\Entity\Technique;
use App\Entity\User;
use App\Security\BenevoleChecker;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixturesTest extends KernelTestCase
{
    public function testDemoDataHasUsableCredentialsAndConsistentRelations(): void
    {
        self::bootKernel();
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $objects = [];
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::exactly(18))->method('persist')->willReturnCallback(
            static function (object $object) use (&$objects): void {
                $objects[$object::class][] = $object;
            }
        );
        $manager->expects(self::once())->method('flush');

        (new AppFixtures($hasher))->load($manager);

        self::assertCount(1, $objects[User::class]);
        self::assertCount(2, $objects[Benevole::class]);
        self::assertCount(3, $objects[Artiste::class]);
        self::assertCount(3, $objects[Lieu::class]);
        self::assertCount(3, $objects[Technique::class]);
        self::assertCount(6, $objects[Oeuvre::class]);
        $user = $objects[User::class][0];
        self::assertSame(AppFixtures::EMAIL, $user->getEmail());
        self::assertTrue($hasher->isPasswordValid($user, AppFixtures::PASSWORD));
        self::assertSame(['ROLE_BENEVOLE'], $user->getRoles());
        (new BenevoleChecker())->checkPostAuth($user);
        self::assertSame($user->getEmail(), $objects[Benevole::class][0]->getEmail());

        foreach ($objects[Oeuvre::class] as $oeuvre) {
            self::assertContains($oeuvre->getLieux(), $objects[Lieu::class]);
            self::assertTrue($oeuvre->getLieux()->getOeuvres()->contains($oeuvre));
            self::assertNotEmpty($oeuvre->getArtistes());
            self::assertNotEmpty($oeuvre->getTechnique());
            foreach ($oeuvre->getArtistes() as $artiste) {
                self::assertTrue($artiste->getOeuvres()->contains($oeuvre));
            }
            foreach ($oeuvre->getTechnique() as $technique) {
                self::assertTrue($technique->getOeuvres()->contains($oeuvre));
            }
            self::assertGreaterThan($oeuvre->getDateCreation(), $oeuvre->getDateDestruction());
        }
        self::assertSame($objects[Oeuvre::class][3], $objects[Oeuvre::class][0]->getRecouvertePar());
        self::assertSame($objects[Oeuvre::class][0], $objects[Oeuvre::class][3]->getOeuvreRecouverte());
    }
}
