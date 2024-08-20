<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CartController extends AbstractController
{
    private ProduitRepository $produitRepository;
    private Cart $cart;

    public function __construct(ProduitRepository $produitRepository, Cart $cart)
    {
        $this->produitRepository = $produitRepository;
        $this->cart = $cart;
    }

    #[isGranted('ROLE_ADMIN')]
    #[Route('/cart', name: 'cart_index')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $this->cart->getFullCart(),
            'total' => $this->cart->getTotal(),
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(int $id, Request $request): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);

        $produit = $this->produitRepository->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $this->cart->add($id, $quantity);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id): Response
    {
        $this->cart->remove($id);
        return $this->redirectToRoute('cart_index');
    }
}
