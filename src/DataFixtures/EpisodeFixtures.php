<?php

namespace App\DataFixtures;

use Faker\Factory;
use app\Entity\Episode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($j = 0; $j < count(ProgramFixtures::PROGRAMS); $j++) {
            for ($s = 1; $s <= 5; $s++) {

                // $ref = $this->getReference('season_'  . $j . $s);
                //dd($ref);
                // if (isset($ref)) {
                for ($i = 1; $i <= 24; $i++) {
                    $episode = new Episode();
                    $episode->setTitle($faker->sentence(4));
                    $episode->setNumber($i);
                    $episode->setSynopsis($faker->paragraphs(2, true));
                    $episode->setSeason($this->getReference('season_'  . $j . $s));
                    $manager->persist($episode);
                }
            }
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
