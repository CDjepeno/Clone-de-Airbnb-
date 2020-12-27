<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker     = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle("ROLE_ADMIN");
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName("Christopher")
                  ->setLastName("Djepeno")
                  ->setEmail("chris@gmail.com")
                  ->setHash($this->encoder->encodePassword($adminUser, "dulonx"))
                  ->setPicture("https://randomuser.me/api/portraits/men/53.jpg")
                  ->setIntroduction($faker->sentence())
                  ->setDescription("<p>" .join("</p><p>",$faker->paragraphs(3))."</p>")
                  ->addUserRole($adminRole);
         $manager->persist($adminUser);

        // Nous gérons les utilisateurs
        $users=[];
        $genres = ['male','female'];
        for($i=1; $i<=10; $i++) {
            $user      = new User;

            $genre     = $faker->randomElement($genres);
            $picture   = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) .'.jpg';

            if($genre == "male") {
                $picture = $picture . 'men/' . $pictureId;
            } else {
                $picture = $picture . 'women/' . $pictureId;
            }

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription("<p>" .join("</p><p>",$faker->paragraphs(3))."</p>")
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        // Nous gerons les annonces
        for($i=1; $i<=30; $i++) {
            $ad              = new Ad();

            $title           = $faker->sentence(6);
            $introduction    = $faker->paragraph(2);
            $backgroundColor = trim($faker->safeHexcolor, '#');
            $foregroundColor = trim($faker->safeHexcolor, '#');
            $imageR          = "https://dummyimage.com/600x400/" . $backgroundColor . "/". $foregroundColor ."&text=" . "Appartement" ;
            $imageP          = "https://dummyimage.com/600x400/" . $backgroundColor . "/". $foregroundColor ."&text=" . "photos appartement" ;
            $content         = "<p>" .join("</p><p>",$faker->paragraphs(6))."</p>";

            $user = $users[mt_rand(0, count($users) -1)];

            $ad -> setTitle($title.$i)
                -> setPrice(mt_rand(35,250))
                -> setIntroduction($introduction)
                -> setContent($content)
                -> setCoverImage($imageR)
                -> setRooms(mt_rand(1,8))
                -> setAuthor($user);
                
            // Gestion des images
            for($j=1; $j<=mt_rand(2,5); $j++) {
                $image = new Image();

                $image->setUrl($imageP)
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                $manager->persist($image);
            }

            // Gestion des réservations
            for($j=1; $j <=mt_rand(0,10); $j++) {
                $booking   = new Booking();

                $createdAt = $faker->dateTimeBetween('- 6months');
                $startDate = $faker->dateTimeBetween('-3 months'); 
                $duration  = mt_rand(3,10);
                // Ont clone la startdate pour ne pas avoir la même date de $endDate
                $endDate   = (clone $startDate)->modify("+$duration days");
                $amount    = $ad->getPrice() * $duration;
                $booker    = $users[mt_rand(0, count($users) -1)];
                $comment   = $faker->paragraph();

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);

                $manager->persist($booking);

                // Gestion des commentaires
                if(mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph())
                            ->setRating(mt_rand(1, 5))
                            ->setAuthor($booker)
                            ->setAd($ad);

                    $manager->persist($comment);
                }
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
