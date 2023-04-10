<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Form\NewPasswordType;
use App\Form\PlayerType;
use App\Form\TeamOrganizerFormType;
use App\Form\TeamType;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class TeamController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request                     $request,
                         Team                        $team,
                         TeamRepository              $teamRepository,
                         UserPasswordHasherInterface $passwordHasher): Response
    {

        // My Team and Organizer

        //Update team
        $teamUpdateForm = $this->createForm(TeamType::class, $team);
        $teamUpdateForm->handleRequest($request);
        if ($teamUpdateForm->isSubmitted() && $teamUpdateForm->isValid()) {
            $teamRepository->save($team, true);

            return $this->redirectToRoute('app_team_my');
        }

        // Update password
        $newPasswordForm = $this->createForm(NewPasswordType::class, $team);
        $newPasswordForm->handleRequest($request);
        if ($newPasswordForm->isSubmitted() && $newPasswordForm->isValid()) {
            $team->setPassword(
                $passwordHasher->hashPassword(
                    $team,
                    $newPasswordForm->get('plainPassword')->getData()
                )
            );
            $teamRepository->save($team, true);
            return $this->redirectToRoute('app_team_my');
        }

        // Organizer only
        if ($this->isGranted("ROLE_ORGANIZER")) {
            $teamOrganizerForm = $this->createForm(TeamOrganizerFormType::class, $team);
            $teamOrganizerForm->handleRequest($request);
            if ($teamOrganizerForm->isSubmitted() && $teamOrganizerForm->isValid()) {
                $this->addFlash("success", "Team updated.");
                $teamRepository->save($team, true);
                return $this->redirectToRoute('app_orga_teams');
            }
        }

        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'teamUpdateForm' => $teamUpdateForm,
            'newPasswordForm' => $newPasswordForm,
            'teamOrganizerForm' => $teamOrganizerForm ?? null,
        ]);

    }

    #[Route('/{id}', name: 'app_team_my', defaults: ["id" => 0])]
    public function team(?int $id, Request $request, PlayerRepository $playerRepository, TeamRepository $teamRepository): Response
    {
        /** @var Team $team */
        $team = $this->isGranted("ROLE_TEAM") ? $this->getUser() : $teamRepository->find($id);

        if ($team === null) {
            return $this->redirectToRoute('app_login');
        }

        $player = new Player();
        $addPlayerForm = $this->createForm(PlayerType::class, $player);
        $addPlayerForm->handleRequest($request);

        if (
            $addPlayerForm->isSubmitted()
            && $addPlayerForm->isValid()
        ) {
            if (
                $player->isCaptain()
                && $team->getPlayers()->exists(function ($key, $value) {
                    return $value->isCaptain();
                })
            ) {
                $this->addFlash("error", "There can only be one team captain.");
            } else {
                $team->addPlayer($player);
                $playerRepository->save($player, true);
                $this->addFlash("success", "Player has been added.");
                if ($this->isGranted("ROLE_ORGANIZER")) {
                    return $this->redirect('/team/' . $team->getId());
                }
                return $this->redirectToRoute('app_team_my');
            }
        }

        $toggleCaptainForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_team_toggle_captain', ['playerId' => 0]))
            ->setMethod('UPDATE')
            ->getForm();

        $removePlayerForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('app_team_remove_player', ['playerId' => 0]))
            ->setMethod('DELETE')
            ->getForm();

        $teams = $teamRepository->findAll();
        return $this->render('team/my-team.html.twig', [
            'addPlayerForm' => $addPlayerForm->createView(),
            'removePlayerForm' => $removePlayerForm->createView(),
            'toggleCaptainForm' => $toggleCaptainForm->createView(),
            'team' => $team,
            'teams' => $teams
        ]);
    }
}
