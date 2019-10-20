<?php

namespace App\DataFixtures;

use App\Entity\Owner;
use App\Entity\Region;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const IDF_REGION_REFERENCE = 'idf-region';

    public function load(ObjectManager $manager)
    {
        $region = new Region();
        $region->setCountry("FR");
        $region->setName("Ile de France");
        $region->setPresentation("La région française capitale");
        $manager->persist($region);

        $manager->flush();

        $this->addReference(self::IDF_REGION_REFERENCE, $region);

        $owner = new Owner();
        $owner->setFirstname("Patrick");
        $owner->setLastname("Martin");
        $owner->setAddress("1 rue de l'Église");
        $owner->setCountry("FR");
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
