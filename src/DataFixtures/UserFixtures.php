<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Create user account
        $user = new User();
        $user
            ->setFirstName('Barry')
            ->setLastName('Alen')
            ->setPhone('+33 123456789')
            ->setEmail('enzo.candotti16@hotmail.fr')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setRoles(['ROLE_USER'])
        ;
        
        $manager->persist($user);
        $this->addReference('simple-user', $user);

        // Create admin account
        $admin = new User();
        $admin
            ->setFirstName('Suprem')
            ->setLastName('Admin')
            ->setPhone('+33 66666666')
            ->setEmail('admin@candotti-blog.com')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setRoles(['ROLE_ADMIN'])
        ;
        
        $manager->persist($admin);

        $manager->flush();
    }
}
