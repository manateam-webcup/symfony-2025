<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/locale/{locale}', name: 'app_change_locale')]
    public function changeLocale(Request $request, string $locale): Response
    {
        // Validate locale (only allow 'en' and 'fr')
        if (!in_array($locale, ['en', 'fr'])) {
            $locale = 'fr'; // Default to French if invalid locale
        }

        // Store the locale in the session
        $request->getSession()->set('_locale', $locale);

        // Get the referer URL to redirect back to the previous page
        $referer = $request->headers->get('referer');
        
        // If no referer, redirect to homepage
        if (!$referer) {
            return $this->redirectToRoute('app_homepage');
        }

        // Redirect back to the previous page
        return $this->redirect($referer);
    }
}