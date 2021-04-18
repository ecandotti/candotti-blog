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
                ->setContent('<p>Le Monde et ses partenaires peuvent, indépendamment ou conjointement, déposer lors de votre visite sur ce site des cookies et technologies similaires, afin de collecter des informations, y compris des données personnelles, telles que : adresse IP, identifiants uniques, données de navigation, données de géolocalisation.

Ces données sont traitées par Le Monde et/ou ses partenaires aux fins suivantes : analyse et amélioration de l’expérience utilisateur et de l’offre de contenus, produits et services du Monde, mesure et analyse d’audience, intéraction avec les réseaux sociaux, lutte contre la fraude, affichage de publicités et contenus personnalisés sur le site du Monde ou de tiers, mesure de performance et d’attractivité des publicités et du contenu. Pour plus d’informations, consulter notre politique de confidentialité.

A l’exception de ceux nécessaires au fonctionnement du site ainsi que, sous certaines conditions, à la mesure d’audience, les cookies et technologies similaires ne peuvent être déposés qu’avec votre consentement. Vous pouvez librement donner, refuser ou retirer votre consentement à tout moment en accédant à notre outil de paramétrage des cookies. Si vous ne consentez pas à l’utilisation de ces technologies, nous considérerons que vous vous opposez également à tout dépôt de cookie que certains partenaires justifient par un intérêt légitime.</p>')
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
