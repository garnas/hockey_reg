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

    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return $this->render('main/test.html.twig');
    }

    #[Route('/imprint', name: 'app_imprint')]
    public function imprint(): Response
    {
        return $this->render('main/imprint.html.twig');
    }

    #[Route('/privacy', name: 'app_privacy')]
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

    #[Route('/timetable', name: 'app_timetable')]
    public function timetable(TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        $teams = $teamRepository->findAll();
        return $this->render('main/parent_timetable.html.twig');
    }

    #[Route('/accomodations', name: 'app_accomodations')]
    public function accomodations(): Response
    {
        return $this->render("main/accomodations.html.twig");
    }
    #[Route('/spielplan/{turnierForm}', name: 'app_spielplan')]
    public function spielplan(string $turnierForm): Response
    {
        $links = [
            "a" => "EUHC_SPIELPLAN_A",
            "b" => "EUHC_SPIELPLAN_B",
        ];
//        return $this->redirect($_ENV["EUHC_SPIELPLAN_B"]);
        return $this->render("main/spielplan.html.twig", [
            "link" => $links[$turnierForm]
        ]);
    }


}
