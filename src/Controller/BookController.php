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
class BookeController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('author_all');
        }

        $book = new Book;
        $bookForm = $this->createForm(BookType::class, $book);
        $bookForm->handleRequest($request);
        if($bookForm->isSubmitted() && $bookForm->isValid()){
            $book->setUser($this->getUser());
            $em->persist($book);
            $em->flush();
        }
        return $this->render('book/new.html.twig', [
            'form' => $bookForm
        ]);
    }
}
