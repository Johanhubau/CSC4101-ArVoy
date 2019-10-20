<?php

namespace App\DataFixtures;

use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const IDF_REGION_REFERENCE = 'idf-region';

    public const SUPER_ADMIN_REFERENCE = 'superadmin-user';

    public function load(ObjectManager $manager)
    {
        $region = new Region();
        $region->setCountry("FR");
        $region->setName("Ile de France");
        $region->setPresentation("La région française capitale");
        $manager->persist($region);

        $manager->flush();

        $this->addReference(self::IDF_REGION_REFERENCE, $region);

        $superadmin = new User();
        $superadmin->setPassword('$argon2id$v=19$m=65536,t=4,p=1$VW9WY1cvSGFSSWtxSmwwWg$fFSsKfVTRvoFtw9zZQKE4bgetIc3/1I31t8S0YvuK7o');
        $superadmin->setEmail("test@example.com");
        $superadmin->setRoles([ "ROLE_SUPERADMIN" ]);

        $this->addReference(self::SUPER_ADMIN_REFERENCE, $superadmin);

        $owner = new Owner();
        $owner->setFirstname("Patrick");
        $owner->setLastname("Martin");
        $owner->setAddress("1 rue de l'Église");
        $owner->setCountry("FR");
        $owner->setUser($this->getReference(self::SUPER_ADMIN_REFERENCE));
        $owner->setTelephone("0033606060606");
        $owner->setValidated(false);
        $manager->persist($owner);

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
