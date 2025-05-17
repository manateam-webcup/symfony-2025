<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class LoginLimiter
{
    public function __construct(
        private RequestStack $requestStack,
        private RateLimiterFactory $loginLimiter
    ) {
    }

    public function consume(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return true;
        }

        // Use IP address as limiter key
        $limiterKey = $request->getClientIp();
        
        // If no IP address is available, don't rate limit
        if (null === $limiterKey) {
            return true;
        }

        $limiter = $this->loginLimiter->create($limiterKey);
        
        // Check if limit is reached
        if (false === $limiter->consume(1)->isAccepted()) {
            return false;
        }

        return true;
    }
}