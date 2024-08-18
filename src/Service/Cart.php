<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProduitRepository;

class Cart
{
private RequestStack $requestStack;
private ProduitRepository $produitRepository;

public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository)
{
$this->requestStack = $requestStack;
$this->produitRepository = $produitRepository;
}

private function getSession()
{
return $this->requestStack->getCurrentRequest()->getSession();
}

public function add(int $id, int $quantity = 1): void
{
$session = $this->getSession();
$cart = $session->get('cart', []);

if (isset($cart[$id])) {
$cart[$id]['quantity'] += $quantity;
} else {
$cart[$id] = [
'produit' => $this->produitRepository->find($id),
'quantity' => $quantity,
];
}

$session->set('cart', $cart);
}

public function remove(int $id): void
{
$session = $this->getSession();
$cart = $session->get('cart', []);

if (isset($cart[$id])) {
unset($cart[$id]);
}

$session->set('cart', $cart);
}

public function getFullCart(): array
{
$session = $this->getSession();
$cart = $session->get('cart', []);
$fullCart = [];

foreach ($cart as $id => $item) {
$product = $item['produit'];
$quantity = $item['quantity'];

if ($product) {
$fullCart[] = [
'produit' => $product,
'quantity' => $quantity
];
}
}

return $fullCart;
}

public function getTotal(): float
{
$session = $this->getSession();
$cart = $session->get('cart', []);
$total = 0;

foreach ($cart as $item) {
$product = $item['produit'];
$quantity = $item['quantity'];

if ($product) {
$total += $product->getPrix() * $quantity;
}
}

return $total;
}
}
