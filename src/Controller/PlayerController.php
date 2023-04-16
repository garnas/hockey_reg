<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/player')]
class PlayerController extends AbstractController
{
    #[Route('/{playerId}/delete', name: 'app_team_remove_player', methods: ['POST'])]
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
        return $this->redirectToRoute('app_team_my');
    }

    #[Route('/captain/{playerId}', name: 'app_team_toggle_captain', methods: ['POST'])]
    public function toggleCaptain(int $playerId, PlayerRepository $playerRepository): Response
    {
        /** @var Team $team */
        $team = $this->getUser();
        $player = $playerRepository->find($playerId);
        if (
            $player !== null
            && $player->getTeam() === $team
        ) {

            $team->getPlayers()->map(function($p) use ($player) {
                if ($p->getId() !== $player->getId()) {
                    $p->setCaptain(false);
                }
            });

            $player->setCaptain(!$player->isCaptain());

            $playerRepository->save($player, true);
            $this->addFlash("success", "Captain has been changed.");
        } else {
            $this->addFlash("error", "Error - Captain could not be changed.");
        }

        return $this->redirectToRoute('app_team_my');
    }

}
