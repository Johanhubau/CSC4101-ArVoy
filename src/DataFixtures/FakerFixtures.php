<?php
// src/DataFixtures/FakerFixtures.php
namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Comment;
use App\Entity\Document;
use App\Entity\Owner;
use App\Entity\Personne;
use App\Entity\Region;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\UnavailablePeriod;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
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
        $faker = Faker\Factory::create('en_EN');

        $owners = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                "password"
            ));
            $user->setEmail($faker->unique()->email);
            $user->setRoles([ "ROLE_OWNER", "ROLE_USER" ]);
            $manager->persist($user);

            $owner = new Owner();
            $owner->setFirstname($faker->firstName);
            $owner->setLastname($faker->lastName);
            $owner->setAddress($faker->streetAddress);
            $owner->setCountry("FR");
            $owner->setUser($user);
            $owner->setBirthdate($faker->dateTime());
            $owner->setTelephone($faker->phoneNumber);
            $owner->setValidated(rand(1,2) == 1 ? true : false);

            $document = new Document();
            $document->path = $faker->imageUrl(300, 300, 'cats');
            $document->name = $faker->md5;
            $owner->setImage($document);
            $manager->persist($document);

            $user->setOwner($owner);
            $manager->persist($owner);
            $owners[] = $owner;
        }

        $rooms = [];
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

                $rooms[] = $room;
                $owner = $owners[array_rand($owners)];
                $room->setOwner($owner);
                $owner->addRoom($room);

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

        $clients = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                "password"
            ));
            $user->setEmail($faker->unique()->email);
            $user->setRoles([ "ROLE_CLIENT", "ROLE_USER" ]);
            $manager->persist($user);

            $client = new Client();
            $client->setFirstname($faker->firstName);
            $client->setLastname($faker->lastName);
            $client->setAddress($faker->streetAddress);
            $client->setBirthdate($faker->dateTime());
            $client->setCountry("FR");
            $client->setUser($user);
            $client->setTelephone($faker->phoneNumber);

            $document = new Document();
            $document->path = $faker->imageUrl(400, 200, 'cats');
            $document->name = $faker->md5;
            $client->setImage($document);

            $user->setClient($client);
            $manager->persist($client);
            $manager->persist($document);
            $clients[] = $client;
        }

        for ($i = 0; $i < 10; $i++) {
            $period = new UnavailablePeriod();
            $period->setDescription($faker->text);
            $room = $rooms[array_rand($rooms)];
            $period->setRoom($room);
            $period->setUntil($faker->dateTimeThisYear());
            $period->setStart($faker->dateTimeThisYear($period->getUntil()));
            $room->addUnavailablePeriod($period);
            $manager->persist($period);
        }


        $occupants = [];
        for ($i = 0; $i < 20; $i++) {
            $client = new Client();
            $client->setFirstname($faker->firstName);
            $client->setLastname($faker->lastName);
            $client->setAddress($faker->streetAddress);
            $client->setBirthdate($faker->dateTime());
            $client->setCountry("FR");
            $client->setTelephone($faker->phoneNumber);
            $manager->persist($client);
            $occupants[] = $client;
        }

        for ($i = 0; $i < 40; $i++) {
            try {
                $client = $clients[array_rand($clients)];
                $room = $rooms[array_rand($rooms)];
                $reservation = new Reservation();
                $reservation->setClient($client);
                $reservation->setUntil($faker->dateTimeThisYear);
                $reservation->setStart($faker->dateTime($reservation->getUntil()));
                $reservation->setMessage($faker->sentence);
                $reservation->setValidated(rand(1, 2) == 1);

                $reservation->setRoom($room);
                for ($j = 0; $j < rand(1, $room->getCapacity()); $j++) {
                    $reservation->addOccupant($occupants[array_rand($occupants)]);
                }

                $manager->persist($reservation);

                if ($reservation->getValidated()) {
                    for ($k = 0; $k < 3; $k++) {
                        $comment = new Comment();
                        $comment->setReservation($reservation);
                        $comment->setComment(join("\n", $faker->sentences));
                        $comment->setDate($faker->dateTimeBetween($reservation->getUntil()));
                        $comment->setRating(rand(1, 5));
                        $comment->setAccepted(rand(1, 2) == 1);
                        $room->addComment($comment);
                        $manager->persist($comment);
                        $reservation->addComment($comment);
                    }
                }
                $manager->persist($reservation);
            } catch (Exception $e) {}
        }

        $manager->flush();
    }
}