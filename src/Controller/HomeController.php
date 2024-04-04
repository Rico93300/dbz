<?php

namespace App\Controller;

use App\Repository\RaceRepository;
use App\Repository\PersonnageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    
    public function index(PersonnageRepository $personnageRepository, RaceRepository $raceRepository): Response
    {
        return $this->render('home/index.html.twig', 
            [
            'personnages' => $personnageRepository->findAll(),
            'races' => $raceRepository->findAll()
        ]);
    }
}
    

