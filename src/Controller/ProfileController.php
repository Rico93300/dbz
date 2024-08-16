<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Renvoyer la vue du profil en passant l'utilisateur en paramètre
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,user $profile, EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher, $id): Response
    {
         $form = $this->createForm(ProfileType::class, $profile);
         $form->handleRequest($request);
    

         if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $profile->setPassword(
                    $userPasswordHasher->hashPassword(
                    $profile,
                    $form->get('plainPassword')->getData()
                )
            );
                $entityManager->flush();

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
        
        return $this->render('profile/edit.html.twig', [
            'user' => $profile,
            'form' => $form,
        ]);
    }
}