<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORIES = [
        'Action',
        'Aventure',
        'Sport',
        'Fantastique',
        'Horreur',
        'Comedie',
        'Science Fiction',
        'Policier',
        'Western'
    ];

    const CATEGORY_REFERENCE = 'category_';

    public function load(ObjectManager $manager): void
    {

        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $this->addReference(self::CATEGORY_REFERENCE . $categoryName, $category);
        }
        $manager->flush();
    }
}
