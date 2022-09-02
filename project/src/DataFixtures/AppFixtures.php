<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $image1 = new Image;
        $image1->setChemin('https://via.placeholder.com/700x120/000000/FFFFFF/?text=Information')
              ->setAlt('Information')
              ->setPublished(true);

        $category1 = new Category;
        $category1->setImage($image1)
                 ->setLabel("Information")
                 ->setContenu("Retrouvez l'ensemble de nos articles informatifs")
                 ->setPublished(true);


        $image2 = new Image;
        $image2->setChemin('https://via.placeholder.com/700x120/000000/FFFFFF/?text=Programmation')
                ->setAlt('Programmation')
                ->setPublished(true);

        $category2 = new Category;
        $category2->setImage($image2)
                    ->setLabel("Programmation")
                    ->setContenu("Retrouvez l'ensemble de nos articles sur la programmation Web")
                    ->setPublished(true);

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->flush();
    }
}
