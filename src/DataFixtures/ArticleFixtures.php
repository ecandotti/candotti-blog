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
                ->setCategory('humor')
                ->setUser($this->getReference('admin'))
                ->setTitle('I\'m the title n°'. $i)
                ->setSubtitle('Hi \'m the subtitle')
                ->setCreateAt(new DateTime())
                ->setPublishAt(new DateTime())
                ->setReadTime(4)
                ->setContent('
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae vulputate enim. Proin tempor nisi placerat diam molestie pellentesque. 
                Proin feugiat libero massa, eu sodales risus dapibus nec. Donec sed magna consequat, dapibus mauris in, pulvinar metus. Fusce diam metus, 
                sagittis sed ipsum vel, facilisis dignissim neque. Pellentesque dapibus nisl eget velit egestas, ut aliquet nisl eleifend. 
                Nunc sit amet sagittis diam. Maecenas id pellentesque sapien.
                Etiam ac justo id ante tincidunt laoreet. Ut scelerisque nec dui nec fermentum. Pellentesque condimentum varius purus. 
                Vestibulum a venenatis nisl. Sed tempus efficitur arcu. Nulla aliquam elementum ante in vulputate. Curabitur pulvinar, augue sed efficitur semper, 
                elit mauris bibendum nisi, vel ultrices diam nisl sit amet felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. 
                Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse iaculis sem eu nisi tristique viverra.
                Nam cursus arcu in nibh rhoncus, nec ullamcorper lectus laoreet. Vivamus pretium lacus blandit commodo consectetur. Aliquam tempus ex nibh, 
                et egestas nunc tincidunt eleifend. Phasellus sollicitudin, erat id pharetra pretium, elit lacus sollicitudin magna, et eleifend nibh odio venenatis erat. 
                Curabitur dapibus quam magna, vel auctor purus volutpat et. Proin nec ullamcorper lorem, vitae vehicula justo. Duis non nibh turpis. Ut bibendum pretium gravida. 
                Mauris commodo leo vel malesuada vehicula. Nunc in eros rhoncus, elementum ex id, ornare ex. Nam sodales pulvinar risus in elementum. Pellentesque a lacus at urna malesuada maximus. 
                Donec ornare, nunc quis aliquet imperdiet, nulla nisl lacinia felis, nec sagittis velit orci eget eros. Donec convallis semper sapien eu euismod.
                ')
            ;

            $manager->persist($article);
            $this->addReference('article'.$i, $article);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
