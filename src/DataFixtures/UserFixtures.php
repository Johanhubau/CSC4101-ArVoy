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

        $manager->flush();

        $superadmin = new User();
        $superadmin->setPassword($this->passwordEncoder->encodePassword(
            $superadmin,
            'admin'
        ));
        $superadmin->setEmail("admin@example.com");
        $superadmin->setRoles([ "ROLE_SUPERADMIN", "ROLE_ADMIN", "ROLE_MODERATOR", "ROLE_CLIENT", "ROLE_USER" ]);
        $superadmin->setStaff($superadmin_staff);
        $superadmin_staff->setUser($superadmin);

        $manager->flush();
    }
}
