<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TwoFactorAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // This is a simplified 2FA implementation
        // In a real application, you would check for a specific route or parameter
        $isSupported = $request->attributes->get('_route') === 'app_2fa_verify'
            && $request->isMethod('POST');

        // Debug: Log whether this authenticator supports the request
        error_log("TwoFactorAuthenticator supports request: " . ($isSupported ? 'yes' : 'no'));
        error_log("Route: " . $request->attributes->get('_route'));
        error_log("Method: " . $request->getMethod());

        return $isSupported;
    }

    public function authenticate(Request $request): Passport
    {
        $code = $request->request->get('code');
        $userId = $request->getSession()->get('2fa_user_id');

        // Debug: Log all request parameters
        error_log("2FA Request parameters: " . json_encode($request->request->all()));
        error_log("2FA Session data: 2fa_user_id=" . $userId);

        if (!$code) {
            throw new AuthenticationException('Please enter a verification code.');
        }

        if (!$userId) {
            throw new AuthenticationException('Your session has expired. Please log in again.');
        }

        // Get the user from the database
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        // Check if the user has a TOTP secret
        if (!$user->hasTotpSecret()) {
            throw new AuthenticationException('TOTP secret not set for this user.');
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
        error_log("TOTP Parameters in Authenticator - Secret: " . $totp->getSecret() . ", Digits: " . $totp->getDigits() . ", Algorithm: " . $totp->getDigest() . ", Period: " . $totp->getPeriod());

        // Verify the code with a time window to account for time drift
        // This allows codes from 30 seconds before and after to be valid
        if (!$totp->verify($code, null, 1)) {
            // Debug: Log the invalid code
            error_log("Invalid TOTP code: $code for User ID: $userId");
            throw new AuthenticationException('Invalid verification code. Please try again.');
        }

        // Debug: Log the valid code
        error_log("Valid TOTP code verified: $code for User ID: $userId");

        return new SelfValidatingPassport(
            new UserBadge($userId, function($userIdentifier) {
                // Look up the user by ID instead of by identifier
                return $this->entityManager->getRepository(User::class)->find($userIdentifier);
            }),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Clear the 2FA session data
        $request->getSession()->remove('2fa_user_id');

        return new RedirectResponse($this->urlGenerator->generate('login_success'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Debug: Log authentication failure
        error_log("TwoFactorAuthenticator authentication failure: " . $exception->getMessage());

        // Store a more user-friendly error message
        if (strpos($exception->getMessage(), 'Invalid 2FA code format') !== false) {
            $request->getSession()->set('2fa_error', 'Please enter a 6-digit code.');
        } else {
            $request->getSession()->set('2fa_error', $exception->getMessage());
        }

        return new RedirectResponse($this->urlGenerator->generate('app_2fa'));
    }
}
