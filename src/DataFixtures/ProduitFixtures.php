<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Entity\Stock;
use App\Entity\Taille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer les tailles
        $taillesDisponibles = ['S', 'M', 'L', 'XL'];
        $tailles = [];

        foreach ($taillesDisponibles as $tailleNom) {
            $taille = new Taille();
            $taille->setNom($tailleNom);
            $tailles[$tailleNom] = $taille;
            $manager->persist($taille);
        }

        // Créer les produits
        for ($i = 1; $i <= 10; $i++) {
            $produit = new Produit();
            $produit->setNom("Produit $i")
                ->setImage("produit_$i.jpeg")
                ->setPrix(rand(10, 75));

            $manager->persist($produit);

            // Associer les tailles et quantités au produit via l'entité Stock
            foreach ($tailles as $taille) {
                $stock = new Stock();
                $stock->setProduit($produit)
                    ->setTaille($taille)
                    ->setQuantity(rand(0, 50)); // Quantité aléatoire pour chaque taille

                $manager->persist($stock);
            }
        }

        // Enregistrer les données en base
        $manager->flush();
    }
}
