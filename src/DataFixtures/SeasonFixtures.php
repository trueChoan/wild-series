<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Service\Slugify;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Season;


class SeasonFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(Slugify $slugify)
    {
        $this->slug = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $seasonRef = 0;
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
                $season->setSlug($this->slug->generate($i));
                $season->setNumber($i);
                $season->setProgram($this->getReference('program_' . $j));
                $this->addReference('season_' .  $seasonRef, $season);
                $manager->persist($season);
                $seasonRef++;
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
