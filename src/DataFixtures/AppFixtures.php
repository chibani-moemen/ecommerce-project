<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Meuble;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [];

        $categorieNames = [
            'Séjour',
            'Chambre',
            'Bureau',
            'Cuisine'
        ];

        foreach ($categorieNames as $name) {
            $categorie = new Categorie();
            $categorie->setNom($name);

            $manager->persist($categorie);

            $categories[] = $categorie;
        }

        $products = [
            [
                'nom' => 'Canapé Moderne',
                'description' => 'Canapé confortable pour le salon.',
                'prix' => 2500,
                'stock' => 5,
                'image' => 'canape.jpg',
                'categorie' => 0
            ],
            [
                'nom' => 'Lit Double',
                'description' => 'Lit moderne pour chambre.',
                'prix' => 3200,
                'stock' => 3,
                'image' => 'lit.jpg',
                'categorie' => 1
            ],
            [
                'nom' => 'Bureau Gaming',
                'description' => 'Bureau ergonomique pour travail.',
                'prix' => 1800,
                'stock' => 7,
                'image' => 'bureau.jpg',
                'categorie' => 2
            ],
            [
                'nom' => 'Table Cuisine',
                'description' => 'Grande table de cuisine.',
                'prix' => 1400,
                'stock' => 4,
                'image' => 'table.jpg',
                'categorie' => 3
            ]
        ];

        foreach ($products as $data) {
            $meuble = new Meuble();

            $meuble->setNom($data['nom']);
            $meuble->setDescription($data['description']);
            $meuble->setPrix($data['prix']);
            $meuble->setStock($data['stock']);
            $meuble->setImage($data['image']);
            $meuble->setCategorie($categories[$data['categorie']]);

            $manager->persist($meuble);
        }

        $manager->flush();
    }
}