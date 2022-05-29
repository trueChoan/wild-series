<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Season;


class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($j = 0; $j < count(ProgramFixtures::PROGRAMS); $j++) {
            $season = new Season();
            $season->setYear($faker->year());
            $year = $season->getYear();
            $season->setProgram($this->getReference('program_' . $j));
            $seasonNumber = $season->getProgram()->getSeasonNumber();

            for ($i = 1; $i <= $seasonNumber; $i++) {
                $season = new Season();
                $season->setYear($year++);
                $season->setDescription($faker->realText());
                $season->setNumber($i);
                $season->setProgram($this->getReference('program_' . $j));
                $this->addReference('season_' . $j . $i, $season);
                $manager->persist($season);
            }
        }
        $manager->flush();
    }


    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont SeasonFixtures dépend
        return [
            ProgramFixtures::class,
        ];
    }
}
