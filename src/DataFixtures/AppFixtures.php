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
    public const GDE_REGION_REFERENCE = 'gde-region';

    public const CLIENT_REFERENCE = 'client-user';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $idf = new Region();
        $idf->setCountry("FR");
        $idf->setName("Ile de France");
        $idf->setPresentation("La région française capitale");
        $manager->persist($idf);
        $this->addReference(self::IDF_REGION_REFERENCE, $idf);

        $gde = new Region();
        $gde->setCountry("FR");
        $gde->setName("Grand-Est");
        $gde->setPresentation("La région française qu'elle est à l'Est");
        $manager->persist($gde);
        $this->addReference(self::GDE_REGION_REFERENCE, $gde);

        $manager->flush();

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

        $room1 = new Room();
        $room1->setSummary("Superbe étable");
        $room1->setDescription("Franchement nickel, il y a même des chevaux");
        $room1->addRegion($this->getReference(self::IDF_REGION_REFERENCE));
        $room1->setCapacity(1);
        $room1->setSuperficy(12);
        $room1->setPrice(865);
        $room1->setOwner($owner);

        $manager->persist($room1);

        $room2 = new Room();
        $room2->setSummary("Chez Roger");
        $room2->setDescription("Un peu perdu");
        $room2->addRegion($this->getReference(self::GDE_REGION_REFERENCE));
        $room2->setCapacity(2);
        $room2->setSuperficy(34);
        $room2->setPrice(154);
        $room2->setOwner($owner);

        $manager->persist($room2);
        $manager->flush();
    }
}
