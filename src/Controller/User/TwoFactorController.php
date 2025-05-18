<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TwoFactorController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/2fa', name: 'app_2fa')]
    public function index(Request $request, TokenStorageInterface $tokenStorage): Response
    {
        // In a real application, this would be triggered after the first authentication step
        // For this example, we'll simulate it by checking if the user is already authenticated
        $token = $tokenStorage->getToken();

        if (!$token) {
            // If not authenticated, redirect to login
            $this->addFlash('error', 'You must be logged in to access this page.');
            return $this->redirectToRoute('app_login');
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException('User not found.');
        }

        // Store the user ID in the session for the 2FA process
        $request->getSession()->set('2fa_user_id', $user->getId());

        // Generate a TOTP secret if the user doesn't have one
        if (!$user->hasTotpSecret()) {
            // Create a new TOTP object with Google Authenticator compatible settings
            $totp = TOTP::create();
            $totp->setLabel($user->getEmail()); // Set the label for the authenticator app
            $totp->setIssuer('ManaTeam\'s - TheEndPage'); // Set the issuer name

            // Ensure we're using settings compatible with Google Authenticator
            // Google Authenticator uses SHA1, 6 digits, and 30-second period
            $totp->setDigest('sha1');
            $totp->setDigits(6);
            $totp->setPeriod(30);

            // Save the secret to the user entity
            $user->setTotpSecret($totp->getSecret());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Debug: Log that we've generated a new TOTP secret and its parameters
            error_log("Generated new TOTP secret for user ID: " . $user->getId());
            error_log("TOTP Parameters - Secret: " . $totp->getSecret() . ", Digits: " . $totp->getDigits() . ", Algorithm: " . $totp->getDigest() . ", Period: " . $totp->getPeriod());
        } else {
            // Create a TOTP object with the existing secret
            $totp = TOTP::create($user->getTotpSecret());
            $totp->setLabel($user->getEmail());
            $totp->setIssuer('ManaTeam\'s - TheEndPage');

            // Ensure we're using settings compatible with Google Authenticator
            // Google Authenticator uses SHA1, 6 digits, and 30-second period
            $totp->setDigest('sha1');
            $totp->setDigits(6);
            $totp->setPeriod(30);

            // Debug: Log the TOTP parameters
            error_log("Using existing TOTP secret for user ID: " . $user->getId());
            error_log("TOTP Parameters - Secret: " . $totp->getSecret() . ", Digits: " . $totp->getDigits() . ", Algorithm: " . $totp->getDigest() . ", Period: " . $totp->getPeriod());
        }

        // Generate the provisioning URI for the QR code
        $qrCodeUri = $totp->getProvisioningUri();

        // Debug: Log that we're setting up 2FA for this user
        error_log("Setting up 2FA for user ID: " . $user->getId());

        // Get any error messages from the authenticator
        $error = $request->getSession()->get('2fa_error');
        $request->getSession()->remove('2fa_error');

        // Debug: Log any error messages
        if ($error) {
            error_log("2FA Error from session: " . $error);
        }

        return $this->render('user/two_factor/index.html.twig.twig', [
            'error' => $error,
            'qrCodeUri' => $qrCodeUri,
            'secret' => $totp->getSecret(),
        ]);
    }

    #[Route('/2fa/verify', name: 'app_2fa_verify', methods: ['POST'])]
    public function verify(Request $request): Response
    {
        // This route is handled by the TwoFactorAuthenticator
        // We don't need to do anything here as the authenticator will handle the request
        // But in case it doesn't, let's add a fallback

        // Debug: Log that we've reached the verify method
        error_log("Reached TwoFactorController::verify method - this should not happen normally");
        error_log("Request parameters: " . json_encode($request->request->all()));

        // Get the code from the request
        $code = $request->request->get('code');
        $userId = $request->getSession()->get('2fa_user_id');

        if (!$userId) {
            $this->addFlash('error', 'Your session has expired. Please log in again.');
            return $this->redirectToRoute('app_login');
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_login');
        }

        // Check if the user has a TOTP secret
        if (!$user->hasTotpSecret()) {
            $this->addFlash('error', 'TOTP secret not set for this user.');
            return $this->redirectToRoute('app_2fa');
        }

        // Create a TOTP object with the user's secret
        $totp = TOTP::create($user->getTotpSecret());

        // Configure TOTP for Google Authenticator compatibility
        $totp->setLabel($user->getEmail());
        $totp->setIssuer('Symfony 2FA Demo');

        // Ensure we're using settings compatible with Google Authenticator
        // Google Authenticator uses SHA1, 6 digits, and 30-second period
        $totp->setDigest('sha1');
        $totp->setDigits(6);
        $totp->setPeriod(30);

        // Debug: Log TOTP parameters
        error_log("TOTP Parameters in Controller Verify - Secret: " . $totp->getSecret() . ", Digits: " . $totp->getDigits() . ", Algorithm: " . $totp->getDigest() . ", Period: " . $totp->getPeriod());

        // Verify the code with a time window to account for time drift
        // This allows codes from 30 seconds before and after to be valid
        if ($totp->verify($code, null, 1)) {
            // If valid, redirect to success page
            error_log("Valid TOTP code verified in controller: $code");
            return $this->redirectToRoute('login_success');
        } else {
            // If not valid, redirect back to 2FA page with error
            error_log("Invalid TOTP code in controller: $code");
            $this->addFlash('error', 'Invalid verification code. Please try again.');
            return $this->redirectToRoute('app_2fa');
        }
    }
}
