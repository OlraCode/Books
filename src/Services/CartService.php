<?php

namespace App\Services;

use App\Entity\Book;
use App\Entity\CartItem;
use App\Entity\User;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;

class CartService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartItemRepository $repository,
    ) {
    }

    public function addBook(User $user, Book $book): void
    {
        if ($this->hasBook($user, $book)) {
            throw new DomainException('Este livro já está no carrinho');
        }

        $item = new CartItem;
        $item->setBook($book);
        $item->setUser($user);

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    /** @return Books[] */
    public function getBooks(User $user): array
    {
        $items = $this->repository->findByUser($user->getId());

        return $items;
    }

    public function hasBook(User $user, Book $book): bool
    {
        $items = $this->getBooks($user);
        $books = array_map(fn ($item) => $item->getBook(), $items);

        return in_array($book, $books);
    }

    public function removeBook(User $user, Book $book): void
    {
        $this->repository->removeBook($user->getId(), $book->getId());
    }
}
