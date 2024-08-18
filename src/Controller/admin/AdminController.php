<?php

namespace App\Controller\admin;

use App\Entity\Produit;
use App\Entity\Stock;
use App\Entity\Taille;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/produits', name: 'admin.produits.')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProduitRepository $repository): Response
    {
        $produits = $repository->findAll();
        return $this->render('admin/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $produit->setImageFile($imageFile); // Set imageFile, VichUploader will handle it
            }

            // Handle stock quantities for each taille
            $tailles = ['XS', 'S', 'M', 'L', 'XL'];
            foreach ($tailles as $tailleNom) {
                $quantity = $form->get('stock' . $tailleNom)->getData();
                if ($quantity !== null && $quantity > 0) {
                    $stock = new Stock();
                    $stock->setQuantity($quantity);
                    $stock->setProduit($produit);

                    // Find or create the corresponding Taille entity
                    $taille = $em->getRepository(Taille::class)->findOneBy(['nom' => $tailleNom]);
                    if (!$taille) {
                        $taille = new Taille();
                        $taille->setNom($tailleNom);
                        $em->persist($taille);
                    }
                    $stock->setTaille($taille);

                    $produit->addStock($stock);
                }
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté.');
            return $this->redirectToRoute('admin.produits.index');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $produit->setImageFile($imageFile); // Set imageFile, VichUploader will handle it
            }

            // Clear existing stocks for this produit
            foreach ($produit->getStocks() as $stock) {
                $produit->removeStock($stock);
                $em->remove($stock);
            }

            // Handle stock quantities for each taille
            $tailles = ['XS', 'S', 'M', 'L', 'XL'];
            foreach ($tailles as $tailleNom) {
                $quantity = $form->get('stock' . $tailleNom)->getData();
                if ($quantity !== null && $quantity > 0) {
                    $stock = new Stock();
                    $stock->setQuantity($quantity);
                    $stock->setProduit($produit);

                    // Find or create the corresponding Taille entity
                    $taille = $em->getRepository(Taille::class)->findOneBy(['nom' => $tailleNom]);
                    if (!$taille) {
                        $taille = new Taille();
                        $taille->setNom($tailleNom);
                        $em->persist($taille);
                    }
                    $stock->setTaille($taille);

                    $produit->addStock($stock);
                }
            }

            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été modifié.');
            return $this->redirectToRoute('admin.produits.index');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        // Validate CSRF token
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();

            $this->addFlash('success', 'Le produit a bien été supprimé.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.produits.index');
    }
}
