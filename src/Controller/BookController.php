<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Message\DeleteBookMessage;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book')]
final class BookController extends AbstractController
{
    #[IsGranted('PUBLIC_ACCESS')]
    #[Route(name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, BookRepository $repository): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $price = $form->get('price')->getData();
            $priceInCents = $price * 100;

            $book->setPriceInCents($priceInCents);

            $entityManager->persist($book);

            $coverImage = $form->get('cover')->getData();
            if ($coverImage) {
                $repository->addCover($book, $coverImage);
            }

            $entityManager->flush();

            $this->addFlash('success', "Livro '{$book->getTitle()}' adicionado com sucesso");
            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', compact('book'));
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager, BookRepository $repository): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->get('price')->setData($book->getPriceInReals());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $price = $form->get('price')->getData();
            $priceInCents = $price * 100;
            $book->setPriceInCents($priceInCents);

            $coverImage = $form->get('cover')->getData();
            
            if ($coverImage) {
                $repository->addCover($book, $coverImage);
            }

            $entityManager->flush();

            $this->addFlash('success', "Livro '{$book->getTitle()}' editado com sucesso");
            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager, MessageBusInterface $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
        }

        $message->dispatch(new DeleteBookMessage($book));   

        $this->addFlash('success', "Livro '{$book->getTitle()}' removido com sucesso");
        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
