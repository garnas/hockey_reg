<?php

namespace App\Controller;

use App\Repository\OrganizerRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, TeamRepository $teamRepository): Response
    {
        $teams = $teamRepository->findAll();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('team/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'teams' => $teams
        ]);
    }

    #[Route('/organizer/login', name: 'app_orga_login')]
    public function organizerLogin(AuthenticationUtils $authenticationUtils,
                                   OrganizerRepository $organizerRepository,
                                    UserPasswordHasherInterface $passwordHasher

    ): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('organizer/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        $security->logout(false);
        $this->addFlash("success", "Logout...");
        return $this->redirect('app_home');
    }

}
