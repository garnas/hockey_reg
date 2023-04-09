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

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }

    #[Route('/teams', name: 'app_teams')]
    public function teams(TeamRepository $teamRepository): Response
    {
        $user = $this->getUser();
        $teams = $teamRepository->findAll();
        return $this->render('teams/index.html.twig', [
            'teams' => $teams,
            'user' => $user,
        ]);
    }

    #[Route('/player/{playerId}', name: 'app_remove_player', methods: ['POST'])]
    public function removePlayer(int $playerId, PlayerRepository $playerRepository): Response
    {
        /** @var Team $team */
        $team = $this->getUser();
        $player = $playerRepository->find($playerId);
        if (
            $player !== null
            && $player->getTeam() === $team
        ) {
            $playerRepository->remove($player, true);
            $this->addFlash("success", "Player has been removed.");
        } else {
            $this->addFlash("error", "No player has been removed.");
        }
        return $this->redirectToRoute('app_teamcenter');
    }

    #[Route('/teamcenter', name: 'app_teamcenter')]
    public function teamcenter(Request $request, PlayerRepository $playerRepository, TeamRepository $teamRepository): Response
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
                return $this->redirectToRoute('app_teamcenter');
            }
        }

        $removePlayerForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_remove_player', ['playerId' => 0]))
            ->setMethod('DELETE')
            ->getForm();

        $teams = $teamRepository->findAll();
        return $this->render('teams/teamcenter/teamcenter.html.twig', [
            'addPlayerForm' => $addPlayerForm->createView(),
            'removePlayerForm' => $removePlayerForm->createView(),
            'team' => $team,
            'teams' => $teams
        ]);
    }
}
