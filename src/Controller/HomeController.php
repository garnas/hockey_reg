<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/teams', name: 'app_teams')]
    public function home(TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        $teams = $teamRepository->findAll();
        return $this->render('teams/index.html.twig', [
            'teams' => $teams,
            'user' => $user,
        ]);
    }

    #[Route('/teamcenter', name: 'app_teamcenter')]
    public function teamcenter(): Response
    {
        return $this->render('teams/teamcenter/teamcenter.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
