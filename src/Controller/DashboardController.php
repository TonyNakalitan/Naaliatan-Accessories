<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use App\Repository\CharacterRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Repository\StockTransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private ProductRepository $productRepository,
        private CharacterRepository $characterRepository,
        private ActivityLogRepository $activityLogRepository,
        private StockRepository $stockRepository,
        private StockTransactionRepository $stockTransactionRepository
    ) {
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDashboard(Request $request): Response
    {
        $stats = [
            'totalUsers' => $this->userRepository->count([]),
            'totalStaff' => $this->userRepository->countByRole('ROLE_STAFF'),
            'totalProducts' => $this->productRepository->getTotalProductsCount(),
            'totalCharacters' => $this->characterRepository->getTotalCharactersCount(),
            'lowStockProducts' => $this->productRepository->getLowStockCount(),
            'outOfStockProducts' => $this->productRepository->getOutOfStockCount(),
            'totalStockValue' => $this->productRepository->getTotalStockValue(),
        ];

        $recentActivities = $this->activityLogRepository->findRecentLogs(10);
        $characterStats = $this->characterRepository->getAlignmentStats();

        // Pagination for low stock products
        $lowStockPage = max(1, $request->query->getInt('low_stock_page', 1));
        $lowStockLimit = 5;
        $totalLowStock = $this->productRepository->getLowStockCount();
        $totalLowStockPages = ceil($totalLowStock / $lowStockLimit);

        // Only use pagination if there are more than 5 items
        if ($totalLowStock > 5) {
            $lowStockProducts = $this->productRepository->findLowStockProductsPaginated($lowStockPage, $lowStockLimit);
        } else {
            $lowStockProducts = $this->productRepository->findLowStockProducts(5);
        }

        return $this->render('DashboardFolder/admin_dashboard.html.twig', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'lowStockProducts' => $lowStockProducts,
            'characterStats' => $characterStats,
            'lowStockPagination' => [
                'current_page' => $lowStockPage,
                'total_pages' => $totalLowStockPages,
                'total_items' => $totalLowStock,
                'limit' => $lowStockLimit,
                'show_pagination' => $totalLowStock > 5
            ]
        ]);
    }

    #[Route('/staff/dashboard', name: 'app_staff_dashboard')]
    #[IsGranted('ROLE_STAFF')]
    public function staffDashboard(): Response
    {
        $stats = [
            'totalProducts' => $this->productRepository->getTotalProductsCount(),
            'totalCharacters' => $this->characterRepository->getTotalCharactersCount(),
            'totalStockItems' => $this->stockRepository->getTotalStockItemsCount(),
            'lowStockProducts' => $this->productRepository->getLowStockCount(),
            'outOfStockProducts' => $this->productRepository->getOutOfStockCount(),
            'totalStockValue' => $this->productRepository->getTotalStockValue(),
            'restockedToday' => $this->stockTransactionRepository->getTotalRestockedToday(),
        ];

        $lowStockProducts = $this->productRepository->findLowStockProducts(5);
        $outOfStockProducts = $this->productRepository->findOutOfStockProducts();
        $recentTransactions = $this->stockTransactionRepository->findRecentTransactions(10);
        $recentActivities = $this->activityLogRepository->findByUser($this->getUser()->getId(), 10);

        return $this->render('DashboardFolder/staff_dashboard.html.twig', [
            'stats' => $stats,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'recentTransactions' => $recentTransactions,
            'recentActivities' => $recentActivities,
        ]);
    }

    // Redirect old /dashboard route to role-based dashboard
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user->isAdmin()) {
            return $this->redirectToRoute('app_admin_dashboard');
        } elseif ($this->isGranted('ROLE_STAFF')) {
            return $this->redirectToRoute('app_staff_dashboard');
        } elseif ($this->isGranted('ROLE_CUSTOMER')) {
            return $this->redirectToRoute('app_customer_dashboard');
        }

        return $this->redirectToRoute('app_login');
    }
}
