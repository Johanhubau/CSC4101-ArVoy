<?php

namespace App\DataFixtures;

use App\Entity\Staff;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $superadmin_staff = new Staff();
        $superadmin_staff->setFirstname("Patrick");
        $superadmin_staff->setLastname("Martin");
        $superadmin_staff->setTitle("CEO");

        $superadmin = new User();
        $superadmin->setPassword($this->passwordEncoder->encodePassword(
            $superadmin,
            'tototo'
        ));
        $superadmin->setEmail("test@example.com");
        $superadmin->setRoles([ "ROLE_SUPERADMIN" ]);

        $manager->flush();
    }
}
