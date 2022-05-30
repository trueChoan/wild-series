<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        ['name' => 'The Office', 'poster' => 'office.jpg',  'category' => 'Comedie'],
        ['name' => 'Walking Dead',  'poster' => 'walking.jpg', 'category' => 'Horreur'],
        ['name' => 'Seigneur des anneaux',  'poster' => 'sda.jpg', 'category' => 'Fantastique'],
        ['name' => 'GOT',  'poster' => 'got.jpg', 'category' => 'Fantastique'],
        ['name' => 'Dune',  'poster' => 'dune.jpg', 'category' => 'Fantastique'],
        ['name' => 'Moon Knight',  'poster' => 'moon-knight.jpg', 'category' => 'Fantastique'],
        ['name' => 'Formule 1 drive to survive',  'poster' => 'drive.jpeg', 'category' => 'Sport'],
        ['name' => 'Rick et Morty',  'poster' => 'morty.jpg', 'category' => 'Aventure']

    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        foreach (self::PROGRAMS as $key => $show) {
            $program = new Program();
            $program->setTitle($show['name']);
            $program->setSeasonNumber($faker->numberBetween(1, 10));
            $program->setSynopsis($faker->realText(300));
            $program->setPoster($show['poster']);
            $program->setNote(rand(0, 5));
            $program->setCategory($this->getReference('category_' . $show['category']));
            $this->addReference('program_' . $key, $program);

            $manager->persist($program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
            CategoryFixtures::class,
        ];
    }
}
