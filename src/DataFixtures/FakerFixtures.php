<?php
// src/DataFixtures/FakerFixtures.php
namespace App\DataFixtures;

use App\Entity\Document;
use App\Entity\Owner;
use App\Entity\Personne;
use App\Entity\Region;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakerFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $owners = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                "tototo"
            ));
            $user->setEmail($faker->email);
            $user->setRoles([ "ROLE_CLIENT", "ROLE_USER" ]);
            $manager->persist($user);

            $owner = new Owner();
            $owner->setFirstname($faker->firstName);
            $owner->setLastname($faker->lastName);
            $owner->setAddress($faker->streetAddress);
            $owner->setCountry("FR");
            $owner->setUser($user);
            $owner->setTelephone($faker->phoneNumber);
            $owner->setValidated(false);
            $manager->persist($owner);
            $owners[] = $owner;
        }

        for ($i = 0; $i < 10; $i++) {
            $region = new Region();
            $region->setName($faker->country);
            $region->setCountry("FR");
            $region->setPresentation($faker->text);

            for ($j = 0; $j < 4; $j++) {
                $room = new Room();
                $room->setSummary($faker->sentence);
                $room->setDescription($faker->text);
                $room->addRegion($region);
                $room->setCapacity($faker->numberBetween(1, 5));
                $room->setSuperficy($faker->numberBetween(0, 90));
                $room->setPrice($faker->numberBetween(140, 850));
                $room->setOwner($owners[array_rand($owners)]);
                $document = new Document();
                $document->path = $faker->imageUrl(400, 200, 'abstract');
                $document->name = $faker->md5;
                $room->setImage($document);
                $manager->persist($room);
            }
            $document = new Document();
            $document->path = $faker->imageUrl(400, 200, 'city');
            $document->name = $faker->md5;
            $region->setImage($document);
            $manager->persist($region);
        }

        $manager->flush();
    }
}