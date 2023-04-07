<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        $number = 42222;

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }

    #[Route('/teams/{teamId}')]
    public function teams(int $teamId): Response
    {
        $number = $teamId;

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }

    #[Route('/register')]
    public function register(): Response
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }
}