<?php

namespace App\DataFixtures;

use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public const IDF_REGION_REFERENCE = 'idf-region';

    public const CLIENT_REFERENCE = 'client-user';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $region = new Region();
        $region->setCountry("FR");
        $region->setName("Ile de France");
        $region->setPresentation("La région française capitale");
        $manager->persist($region);

        $manager->flush();

        $this->addReference(self::IDF_REGION_REFERENCE, $region);

        $user = new User();
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'test'
        ));
        $user->setEmail("test@example.com");
        $user->setRoles([ "ROLE_CLIENT", "ROLE_USER" ]);
        $manager->persist($user);

        $this->addReference(self::CLIENT_REFERENCE, $user);

        $owner = new Owner();
        $owner->setFirstname("Patrick");
        $owner->setLastname("Martin");
        $owner->setAddress("1 rue de l'Église");
        $owner->setCountry("FR");
        $owner->setUser($this->getReference(self::CLIENT_REFERENCE));
        $owner->setTelephone("0033606060606");
        $owner->setValidated(false);
        $manager->persist($owner);

        $user->setOwner($owner);

        $manager->flush();

        $room = new Room();
        $room->setSummary("Beau poulailler ancien à Évry");
        $room->setDescription("très joli espace sur paille");
        $room->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
        $room->setCapacity(3);
        $room->setSuperficy(82);
        $room->setPrice(238);
        $room->setOwner($owner);

        $manager->persist($room);

        $manager->flush();
    }
}
