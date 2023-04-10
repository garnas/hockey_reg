<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/', name: 'app_imprint')]
    public function imprint(): Response
    {
        return $this->render('main/imprint.html.twig');
    }

    #[Route('/', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('main/dataprotection.html.twig');
    }

    #[Route('/teams', name: 'app_teams')]
    public function teams(TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        $teams = $teamRepository->findAll();
        return $this->render('main/teams.html.twig', [
            'teams' => $teams,
            'user' => $user,
        ]);
    }

}
