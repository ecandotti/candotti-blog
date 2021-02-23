<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i < 4; $i++) {
            $category = new Category();
            $category
                ->setName('Category n°'. $i)
            ;

            $manager->persist($category);
            $this->addReference('category_'.$i, $category);
        }
        $manager->flush();
    }
}
