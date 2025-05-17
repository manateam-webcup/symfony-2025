<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/success', name: 'login_success')]
    public function checkloginStatus(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($lastUsername) {
            $this->addFlash('success', 'Welcome back, ' . $lastUsername . '!');

            // Check if user has admin role
            $user = $this->getUser();
            if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('admin_pending_endings');
            }

            return $this->redirectToRoute('app_ending_index');
        } else {
            $this->addFlash('error', 'You are not logged in.');
            return $this->render('base.html.twig');
        }
    }
}
