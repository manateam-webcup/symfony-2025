<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        // Skip if this is not the main request
        if (!$event->isMainRequest()) {
            return;
        }

        // Try to get locale from session
        $session = $request->getSession();
        if ($session && $session->has('_locale')) {
            $request->setLocale($session->get('_locale'));
            return;
        }

        // If no locale in session, try to get from browser's Accept-Language header
        $preferredLanguage = $request->getPreferredLanguage(['en', 'fr']);
        if ($preferredLanguage) {
            $request->setLocale($preferredLanguage);
            // Store in session for future requests
            if ($session) {
                $session->set('_locale', $preferredLanguage);
            }
            return;
        }

        // Fallback to default locale
        $request->setLocale($this->defaultLocale);
    }

    public static function getSubscribedEvents()
    {
        return [
            // Must be registered before the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}