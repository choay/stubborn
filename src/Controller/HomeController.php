<?php
namespace App\Controller;

use App\Entity\Produit;
use App\Data\SearchData;
use App\Form\ProduitType;
use App\Form\SearchType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $repository): Response
    {
        $produits = $repository->findBy([], ['id' => 'DESC'], 3);
        return $this->render('home/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/produits', name: 'boutique')]
    public function showBoutique(Request $request, ProduitRepository $repository): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);

        $queryBuilder = $repository->createQueryBuilder('p');

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($data->searchPrix)) {
                [$minPrice, $maxPrice] = explode('-', $data->searchPrix);
                $queryBuilder->andWhere('p.prix >= :minPrice')
                    ->andWhere('p.prix <= :maxPrice')
                    ->setParameter('minPrice', $minPrice)
                    ->setParameter('maxPrice', $maxPrice);
            }
        }

        $produits = $queryBuilder->getQuery()->getResult();

        return $this->render('home/boutique.html.twig', [
            'produits' => $produits,
            'form' => $form->createView(),
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/produits/{id}', name: 'show')]
    public function show(ProduitRepository $produitRepository, int $id): Response
    {
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        $form = $this->createForm(ProduitType::class, $produit);

        return $this->render('home/show.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
}
