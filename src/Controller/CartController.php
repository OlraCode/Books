<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\CartItem;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    public function __construct(private CartService $cart, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $items = $this->cart->getBooks($this->getUser());

        $total = array_reduce($items, fn ($acumuled, CartItem $value) => $acumuled + $value->getBook()->getPriceInCents(), 0);
        $total = 'R$' . number_format($total / 100, 2, ',', '.');

        return $this->render('cart/index.html.twig', compact('items', 'total'));
    }

    #[Route('/cart/add/{book}', name: 'app_cart_add', methods: ["POST"])]
    public function addBook(Book $book): Response
    {
        $this->cart->addBook($this->getUser(), $book);

        $this->addFlash('success', 'Livro adicionado ao carrinho');

        return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id): Response
    {
        $item = $this->entityManager->getReference(CartItem::class, $id);
        $this->entityManager->remove($item);
        $this->entityManager->flush();

        $this->addFlash('success', 'Livro removido do carrinho');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/book/remove/{book}', name: 'app_cart_remove_book', methods: ["POST"])]
    public function removeBook(Book $book): Response
    {
        $this->cart->removeBook($this->getUser(), $book);

        $this->addFlash('success', 'Livro removido do carrinho');

        return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
    }
}
