<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $serviceData = [
            [
                'name' => 'Petit déjeuner',
                'price' => '12€ par personne et par jour',
                'description' => 'Commencez la journée en douceur avec viennoiseries fraîches, fruits de saison, boissons chaudes et produits locaux, servi en chambre ou en salle.'
            ],
            [
                'name' => 'Repas',
                'price' => 'En fonction de la prestation',
                'description' => 'Une expérience culinaire sur mesure élaborée selon vos envies et les produits du terroir. Dîner romantique ou repas en groupe, chaque prestation est adaptée à vos attentes.'
            ],
            [
                'name' => 'Bouteille de Champagne',
                'price' => '70€',
                'description' => 'Célébrez vos moments précieux avec une bouteille de Champagne servie bien fraîche dans votre chambre. Idéale pour une occasion spéciale ou un séjour en amoureux.'
            ],
            [
                'name' => 'Conciergerie',
                'price' => 'En fonction de la prestation',
                'description' => 'Notre conciergerie organise votre séjour : restaurants, activités, transferts, visites guidées et plus encore. Chaque détail est pris en charge pour un séjour parfait.'
            ],

        ];

        foreach ($serviceData as $data) {

            $service = new Service();
            $service->setName($data['name']);
            $service->setPrice($data['price']);
            $service->setDescription($data['description']);
            $manager->persist($service);
        }

        $manager->flush();
    }
}
