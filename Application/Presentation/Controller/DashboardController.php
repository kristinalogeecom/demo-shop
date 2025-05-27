<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\ServiceInterface\DashboardServiceInterface;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;

/**
 * Handles HTTP requests related to admin dashboard statistics.
 */
class DashboardController
{
    private DashboardServiceInterface $dashboardService;

    public function __construct(DashboardServiceInterface $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Returns aggregated statistics for the admin dashboard.
     *
     * @return Response JSON response with dashboard data.
     */
    public function getDashboardStats(): Response
    {
        return new JsonResponse($this->dashboardService->getDashboardStats());
    }
}
