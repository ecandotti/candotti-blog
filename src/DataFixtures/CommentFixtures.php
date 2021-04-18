<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=1; $i < 31; $i++) { 
            for ($j=1; $j < 5; $j++) {
                $comment = new Comment();
                $comment
                    ->setUser($this->getReference('admin'))
                    ->setArticle($this->getReference('article'.$i))
                    ->setStatus("V")
                    ->setContent('foiqjfpozeijrozjerfj => '.$j)
                    ->setCreateAt(new DateTime())
                ;

            }
            $manager->persist($comment);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ArticleFixtures::class,
            UserFixtures::class,
        );
    }
}
