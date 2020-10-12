<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Ad;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=1; $i<=30; $i++) {
            $ad = new Ad();

            $faker = Factory::create();

            $title = $faker->sentence(6);
            $introduction = $faker->paragraph(2);
            $content = "<p>" .join("</p><p>",$faker->paragraphs(6))."</p>";
            $image = $faker->imageUrl(1000,350);

            $ad -> setTitle($title.$i)
                -> setPrice(mt_rand(35,250))
                -> setIntroduction($introduction)
                -> setContent($content)
                -> setCoverImage($image)
                -> setRooms(mt_rand(1,8));

            for($j=1; $j<=mt_rand(2,5); $j++) {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                      $manager->persist($image);
            }

            $manager->persist($ad);
        }
        

        $manager->flush();
    }
}
