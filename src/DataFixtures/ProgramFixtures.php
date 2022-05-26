<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        ['name' => 'The Office', 'category' => 'Comedie'],
        ['name' => 'Walking Dead', 'category' => 'Horreur'],
        ['name' => 'Seigneur des anneaux', 'category' => 'Fantastique'],
        ['name' => 'Moon Knight', 'category' => 'Fantastique'],
        ['name' => 'Formule 1 drive to survive', 'category' => 'Sport'],
        ['name' => 'Rick et Morty', 'category' => 'Aventure']

    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PROGRAMS as $show) {
            $program = new Program();
            $program->setTitle($show['name']);
            $program->setSynopsis("société de vente de papier, Dunder Mifflin");
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
