<?php

namespace App\DataFixtures;

use Exception;
use Faker\Factory;
use app\Entity\Episode;
use App\DataFixtures\SeasonFixtures;
use App\DataFixtures\ProgramFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $seasonRef = 0;
        try {
            while ($this->getReference('season_' . $seasonRef)) {
                for ($i = 1; $i <= 24; $i++) {
                    $episode = new Episode();
                    $episode->setTitle($faker->sentence(4));
                    $episode->setNumber($i);
                    $episode->setSynopsis($faker->paragraphs(2, true));
                    $episode->setSeason($this->getReference('season_'  . $seasonRef));
                    $manager->persist($episode);
                }
                $seasonRef++;
            }
        } catch (Exception $e) {
        }


        $manager->flush();
    }


    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont EpisodeFixtures dépend
        return [
            SeasonFixtures::class,
        ];
    }
}
