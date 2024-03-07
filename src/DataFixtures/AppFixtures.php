<?php

namespace App\DataFixtures;

use App\Entity\Fille;
use App\Entity\Tweet;
use App\Entity\UserAccount;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;


    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // Création de users
        $users = Array();


        for ($i = 0; $i < 10; $i++) {
            $users[$i] = new UserAccount();
            $users[$i]->setFirstName($faker->firstName);
            $users[$i]->setLastName($faker->lastName);
            $users[$i]->setEmail($faker->email);
            $users[$i]->setAtCreated(new \DateTime($faker->date()));

            $hashedPassword = $this->passwordHasher->hashPassword(
                $users[$i],
                $faker->password
            );

            $users[$i]->setPassword($hashedPassword);

            $manager->persist($users[$i]);
        }

        $tweets = Array();

        for ($i = 0; $i < 1000; $i++) {
            $tweets[$i] = new Tweet();
            $tweets[$i]->setUser($users[$i % 3]);
            $tweets[$i]->setContent(implode(" ", $faker->words("15")));
            $tweets[$i]->setNumberLikes(0);
            $tweets[$i]->setAtCreated(new \DateTime($faker->date()));

            $manager->persist($tweets[$i]);
        }

        $manager->flush();
    }
}
