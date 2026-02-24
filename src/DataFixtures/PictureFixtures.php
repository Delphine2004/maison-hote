<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PictureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $pictureData = [
            ['picture' => 'picture-1.webp'],
            ['picture' => 'picture-2.webp'],
            ['picture' => 'picture-3.webp'],
            ['picture' => 'picture-4.webp'],
            ['picture' => 'picture-5.webp']
        ];

        foreach ($pictureData as $data) {

            $picture = new Picture();
            $picture->setPicture($data['picture']);
            $manager->persist($picture);
        }

        $manager->flush();
    }
}
