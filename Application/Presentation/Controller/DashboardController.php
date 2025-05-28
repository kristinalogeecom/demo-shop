<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\ServiceInterface\DashboardServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\Response;

/**
 * Handles HTTP requests related to admin dashboard statistics.
 */
class DashboardController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function dashboardPage(Request $request): Response
    {
        return new HtmlResponse('AdminDashboard');
    }

    /**
     * Returns aggregated statistics for the admin dashboard.
     *
     * @param Request $request The current HTTP request
     * @return Response JSON response with dashboard data.
     * @throws \Exception
     */
    public function getDashboardStats(Request $request): Response
    {
        return new JsonResponse($this->dashboardService()->getDashboardStats());
    }

    /**
     * @throws \Exception
     */
    private function dashboardService(): DashboardServiceInterface
    {
        return ServiceRegistry::get(DashboardServiceInterface::class);
    }
}
