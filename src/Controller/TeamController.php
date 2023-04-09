<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{

    #[Route('/teams', name: 'app_teams')]
    public function teams(TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        $teams = $teamRepository->findAll();
        return $this->render('teams/teams.html.twig', [
            'teams' => $teams,
            'user' => $user,
        ]);
    }

    #[Route('/my-team', name: 'app_myteam')]
    public function myTeam(Request $request, PlayerRepository $playerRepository, TeamRepository $teamRepository): Response
    {
        /** @var Team $team */
        $team = $this->getUser();

        $player = new Player();

        $addPlayerForm = $this->createForm(PlayerType::class, $player);
        $addPlayerForm->handleRequest($request);

        if (
            $addPlayerForm->isSubmitted()
            && $addPlayerForm->isValid()
        ) {
            if (
                $player->isCaptain()
                && $team->getPlayers()->exists(function($key, $value) {
                    return $value->isCaptain();
                })
            ) {
                $this->addFlash("error", "There can only be one team captain.");
            } else {
                $team->addPlayer($player);
                $playerRepository->save($player, true);
                $this->addFlash("success", "Player has been added.");
                return $this->redirectToRoute('app_myteam');
            }
        }

        $toggleCaptainForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_toggle_captain', ['playerId' => 0]))
            ->setMethod('UPDATE')
            ->getForm();

        $removePlayerForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_remove_player', ['playerId' => 0]))
            ->setMethod('DELETE')
            ->getForm();

        $teams = $teamRepository->findAll();
        return $this->render('teams/myteam.html.twig', [
            'addPlayerForm' => $addPlayerForm->createView(),
            'removePlayerForm' => $removePlayerForm->createView(),
            'toggleCaptainForm' => $toggleCaptainForm->createView(),
            'team' => $team,
            'teams' => $teams
        ]);
    }
}
