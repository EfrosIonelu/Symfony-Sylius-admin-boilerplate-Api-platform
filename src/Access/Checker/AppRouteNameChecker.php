<?php

declare(strict_types=1);

namespace App\Access\Checker;

use Odiseo\SyliusRbacPlugin\Access\Checker\RouteNameCheckerInterface;

final class AppRouteNameChecker implements RouteNameCheckerInterface
{
    public function __construct(
        private RouteNameCheckerInterface $decoratedChecker,
    ) {
    }

    public function isAdminRoute(string $routeName): bool
    {
        return $this->decoratedChecker->isAdminRoute($routeName)
            || false !== strpos($routeName, 'app_admin');
    }
}
