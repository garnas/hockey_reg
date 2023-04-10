<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/organizer')]
class OrganizerController extends AbstractController
{
    #[Route('/teams', name: 'app_orga_teams', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('organizer/teams.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('team/{id}/delete', name: 'app_orga_remove_team', methods: ['POST'])]
    public function delete(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $teamRepository->remove($team, true);
        }

        return $this->redirectToRoute('app_orga_teams', [], Response::HTTP_SEE_OTHER);
    }

}
