<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/author', name: 'author_')]
class AuthorController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function all(AuthorRepository $authorRepo): Response
    {
        $authors = $authorRepo->findAll();
        return $this->render('author/all.html.twig', [
            'authors' => $authors
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('author_all');
        }
        
        $author = new Author;
        $authorNew = $this->createForm(AuthorType::class, $author);
        $authorNew->handleRequest($request);
        if($authorNew->isSubmitted() && $authorNew->isValid()){
            $author->setUser($this->getUser());
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('user_show', [
                'id' => $this->getUser()->getId()
            ]);
        }
        return $this->render('author/edit.html.twig', [
            'form' => $authorNew,
            'title' => 'Nouvel auteur'
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, Author $author, EntityManagerInterface $em): Response
    {
        if($author->getUser() !== $this->getUser())
        {
            return $this->redirectToRoute('author_all');
        }

        $authorForm = $this->createForm(AuthorType::class, $author);
        $authorForm->handleRequest($request);
        if($authorForm->isSubmitted() && $authorForm->isValid()){
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('user_show', [
                'id' => $this->getUser()->getId()
            ]);
        }
        return $this->render('author/edit.html.twig', [
            'form' => $authorForm,
            'title' => 'Modifier auteur'
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Request $request, Author $author, EntityManagerInterface $entityManager): Response
    {
        
    if($author->getUser() !== $this->getUser())
    {
        $this->redirectToRoute('author_all');
    }
    if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->getPayload()->getString('_token'))) {
        $entityManager->remove($author);
        $entityManager->flush();
    }

    return $this->redirectToRoute('user_show', [
        'id' => $this->getUser()->getId(),
    ], Response::HTTP_SEE_OTHER);
    }

}
