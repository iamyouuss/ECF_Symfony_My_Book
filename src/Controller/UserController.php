<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/user', name: 'user_')]
class UserController extends AbstractController
{
    // #[Route('/', name: 'profile', methods: ['GET'])]
    // public function index(): Response
    // {
    //     return $this->render('user/index.html.twig', [
    //         'user' => $this->getUser(),
    //     ]);
    // }

    // #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('user/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if($this->getUser()->getId() !== $user->getId()){
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('password')->get('oldPassword')->getData();
            if($oldPassword){
                if($userPasswordHasherInterface->isPasswordValid($user, $oldPassword)){
                                $newPassword = $form->get('password')->get('newPassword')->getData();
                                $user->setPassword(
                                    $userPasswordHasherInterface->hashPassword(
                                        $user,
                                        $newPassword
                                    )
                                );
                            }
            }
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
    }
}
