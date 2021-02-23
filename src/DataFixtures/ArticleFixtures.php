<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i < 31; $i++) {
            $article = new Article();
            $article
                ->setCategory($this->getReference('category_'.rand(1,3)))
                ->setAuthor($this->getReference('simple-user'))
                ->setTitle('I\'m the title n°'. $i)
                ->setSubtitle('Hi \'m the subtitle')
                ->setCreateAt(new DateTime())
                ->setReadTime(4)
                ->setContent('Loremzoefinpeoifnpzeoifneproinezporfnrepoizfnpfirpofienpfnpzoifnzopeipriznfpoinfpofnezrpipzofneprnfoziorfnpoefnzorifnzpefizeprofinpzeorfn')
            ;

            $manager->persist($article);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategoryFixtures::class,
            UserFixtures::class,
        );
    }
}
