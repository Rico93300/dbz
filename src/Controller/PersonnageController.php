<?php

namespace App\Controller;

use App\Entity\Race;
use App\Entity\Personnage;
use App\Form\PersonnageType;
use App\Repository\RaceRepository;
use App\Repository\PersonnageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/personnage')]
class PersonnageController extends AbstractController
{
    #[Route('/', name: 'app_personnage_index', methods: ['GET'])]
    public function index(PersonnageRepository $personnageRepository, RaceRepository $raceRepository): Response
    {
        return $this->render('personnage/index.html.twig', [
            'personnages' => $personnageRepository->findAll(),
            'races' => $raceRepository->findAll()
        ]);
    }


    #[Route('/filter/{id}', name: 'app_personnage_filter', methods: ['GET'])]
    public function filter(int $id, PersonnageRepository $personnageRepository, RaceRepository $raceRepository): Response
    {
        return $this->render('personnage/index.html.twig', [

            'personnages' => $personnageRepository->findBy(['race' => $id]),

            'races' => $raceRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_personnage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RaceRepository $RaceRepository): Response
    {

        if ($RaceRepository->findAll() == []) {
            $this->addFlash('warning', "Il n'y a pas de race,veuillez en ajouter une avant de creer un personnage");
            return $this->redirectToRoute('app_race_new');
        }



        $personnage = new Personnage();
       


        $form = $this->createForm(PersonnageType::class, $personnage);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {


            $image = $form->get('image')->getData();
            if ($image != null) {
                $imageName = uniqid() . '.' . $image->guessExtension();
                $personnage->setImage($imageName);;

                $image->move($this->getParameter('personnage_image_directory'), $imageName);
            }


            $entityManager->persist($personnage);
            $entityManager->flush();

            return $this->redirectToRoute('app_personnage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personnage/new.html.twig', [
            'personnage' => $personnage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personnage_show', methods: ['GET'])]
    public function show(Personnage $personnage): Response
    {
        return $this->render('personnage/show.html.twig', [
            'personnage' => $personnage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_personnage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personnage $personnage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonnageType::class, $personnage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            // je verifie qu'une nouvelle image a ete envoyé avec le formulaire 
            if ($image != null) {
                // je verifie l'existance d'une encienne image au produit 
                // si c'est le cas je supprime l'ancienne image 
                if (file_exists($this->getParameter('personnage_image_directory') . $personnage->getImage())) {
                    unlink($this->getParameter('personnage_image_directory') . $personnage->getImage());
                }

                // puis je telechager la nouvelle image et change le nom de l'image en base de donnees

                $imgName = uniqid() . '.' . $image->guessExtension();
                $personnage->setImage($imgName);
                $image->move($this->getParameter('personnage_image_directory'), $imgName);
            }





            $entityManager->persist($personnage);
            $entityManager->flush();

            return $this->redirectToRoute('app_personnage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personnage/edit.html.twig', [
            'personnage' => $personnage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personnage_delete', methods: ['POST'])]
    public function delete(Request $request, Personnage $personnage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $personnage->getId(), $request->getPayload()->get('_token'))) {

            if (file_exists('uploads/image/' . $personnage->getImage())) {
                unlink('uploads/image/' . $personnage->getImage());
            }
            $entityManager->remove($personnage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_personnage_index', [], Response::HTTP_SEE_OTHER);
    }
}
