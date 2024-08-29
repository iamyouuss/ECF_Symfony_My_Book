<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


#[Route('/book', name: 'book_')]
class BookController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // if(!$this->getUser())
        // {
        //     return $this->redirectToRoute('author_all');
        // }

        $book = new Book;
        $bookForm = $this->createForm(BookType::class, $book);
        $bookForm->handleRequest($request);
        if($bookForm->isSubmitted() && $bookForm->isValid()){
            $book->setUser($this->getUser());
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('user_show', [
                'id' => $this->getUser()->getId()
            ]);
        }
        return $this->render('book/new.html.twig', [
            'form' => $bookForm
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        if($book->getUser() !== $this->getUser())
        {
            return $this->redirectToRoute('author_all');
        }

        $bookForm = $this->createForm(BookType::class, $book);
        $bookForm->handleRequest($request);
        if($bookForm->isSubmitted() && $bookForm->isValid()){
            $em->persist($book);
            $em->flush();
            return $this->redirectToRoute('user_show', [
                'id' => $this->getUser()->getId()
            ]);
        }
        return $this->render('book/new.html.twig', [
            'form' => $bookForm,
            'title' => 'Modifier auteur'
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        
    if($book->getUser() !== $this->getUser())
    {
        $this->redirectToRoute('author_all');
    }
    if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($book);
        $entityManager->flush();
    }

    return $this->redirectToRoute('user_show', [
        'id' => $this->getUser()->getId(),
    ], Response::HTTP_SEE_OTHER);
    }
}
