<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\NewPasswordType;
use App\Form\TeamOrganizerFormType;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/team')]
class CrudTeamController extends AbstractController
{
    #[Route('/', name: 'app_teams_orga', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('teams/teams-orga.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,
                         Team $team,
                         TeamRepository $teamRepository,
                         UserPasswordHasherInterface $passwordHasher): Response
    {

        // My Team and Organizer

        //Update team
        $teamUpdateForm = $this->createForm(TeamType::class, $team);
        $teamUpdateForm->handleRequest($request);
        if ($teamUpdateForm->isSubmitted() && $teamUpdateForm->isValid()) {
            $teamRepository->save($team, true);

            return $this->redirectToRoute('app_myteam');
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
            return $this->redirectToRoute('app_myteam');
        }

        // Organizer only

        if ($this->isGranted("ROLE_ORGANIZER")) {
            $teamOrganizerForm = $this->createForm(TeamOrganizerFormType::class, $team);
            $teamOrganizerForm->handleRequest($request);
            if ($teamOrganizerForm->isSubmitted() && $teamOrganizerForm->isValid()) {
                $this->addFlash("success", "Team updated.");
                $teamRepository->save($team, true);
                return $this->redirectToRoute('app_teams_orga');
            }
        }

        return $this->render('teams/edit.html.twig', [
            'team' => $team,
            'teamUpdateForm' => $teamUpdateForm,
            'newPasswordForm' => $newPasswordForm,
            'teamOrganizerForm' => $teamOrganizerForm ?? null,
        ]);

    }

    #[Route('team/{id}/delete', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, TeamRepository $teamRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $teamRepository->remove($team, true);
        }

        return $this->redirectToRoute('app_teams_orga', [], Response::HTTP_SEE_OTHER);
    }
}
