<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


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
        foreach (self::PROGRAMS as $show) {
            $program = new Program();
            $program->setTitle($show['name']);
            $program->setSynopsis("société de vente de papier, Dunder Mifflin");
            $program->setPoster($show['poster']);
            $program->setNote(rand(0, 5));
            $program->setCategory($this->getReference('category_' . $show['category']));
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
