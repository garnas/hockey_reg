<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\RegistrationFormType;
use App\Repository\TeamRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Exception\RfcComplianceException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, TeamRepository $teamRepository): Response
    {
        $team = new Team();
        $form = $this->createForm(RegistrationFormType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $team->setPassword(
                $userPasswordHasher->hashPassword(
                    $team,
                    $form->get('plainPassword')->getData()
                )
            );

            $team->setPaid(false);
            $team->addTeamRole();
            $teamRepository->save($team, true);

            // generate a signed url and email it to the user
            try {
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $team,
                    (new TemplatedEmail())
                        ->from(new Address('mailbot@einrad.hockey', 'Unicycle Hockey'))
                        ->to($team->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
            } catch (RfcComplianceException $e) {
                $teamRepository->remove($team, true);
                $this->addFlash('error', 'Could not send verification email.');
                $this->addFlash('error', $e->getMessage());
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            } catch (TransportExceptionInterface $e) {
                $teamRepository->remove($team, true);
                $this->addFlash('error', 'Could not send verification email.');
                $this->addFlash('error', $e->getMessage());
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            $this->addFlash('success', 'An email verification has been send.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, TeamRepository $teamRepository, Security $security): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $teamRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        if ($user->isVerified()) {
            $this->addFlash("success", "Your email is already verified.");
            return $this->redirectToRoute('app_login');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }
        $security->login($user);
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_team_my');
    }
}
