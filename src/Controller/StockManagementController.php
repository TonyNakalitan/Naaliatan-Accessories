<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\StockTransaction;
use App\Form\StockType;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Repository\StockTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StockManagementController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private StockRepository $stockRepository,
        private StockTransactionRepository $stockTransactionRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // Admin routes
    #[Route('/admin/stock-management', name: 'app_admin_stock_management_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(): Response
    {
        return $this->index();
    }

    #[Route('/admin/stock-management/create', name: 'app_admin_stock_management_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminCreate(Request $request): Response
    {
        return $this->create($request, 'admin');
    }

    #[Route('/admin/stock-management/restock/{id}', name: 'app_admin_stock_management_restock')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminRestock(Product $product): Response
    {
        return $this->restock($product);
    }

    #[Route('/admin/stock-management/restock/{productId}/from-stock/{stockId}', name: 'app_admin_stock_management_restock_from_stock', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminRestockFromStock(int $productId, int $stockId, Request $request): Response
    {
        return $this->restockFromStock($productId, $stockId, $request, 'admin');
    }

    #[Route('/admin/stock-management/store/{id}', name: 'app_admin_stock_management_store')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminStore(Product $product, Request $request): Response
    {
        return $this->store($product, $request, 'admin');
    }

    #[Route('/admin/stock-management/stock/delete/{id}', name: 'app_admin_stock_management_delete_stock', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDeleteStock(Stock $stock, Request $request): Response
    {
        return $this->deleteStock($stock, $request, 'admin');
    }

    #[Route('/admin/stock-management/history', name: 'app_admin_stock_management_history')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminHistory(Request $request): Response
    {
        return $this->history($request);
    }

    #[Route('/admin/stock-management/product-history/{id}', name: 'app_admin_stock_management_product_history')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminProductHistory(Product $product): Response
    {
        return $this->productHistory($product);
    }

    // Staff routes
    #[Route('/staff/stock-management', name: 'app_staff_stock_management_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(): Response
    {
        return $this->index();
    }

    #[Route('/staff/stock-management/create', name: 'app_staff_stock_management_create')]
    #[IsGranted('ROLE_STAFF')]
    public function staffCreate(Request $request): Response
    {
        return $this->create($request, 'staff');
    }

    #[Route('/staff/stock-management/restock/{id}', name: 'app_staff_stock_management_restock')]
    #[IsGranted('ROLE_STAFF')]
    public function staffRestock(Product $product): Response
    {
        return $this->restock($product);
    }

    #[Route('/staff/stock-management/restock/{productId}/from-stock/{stockId}', name: 'app_staff_stock_management_restock_from_stock', methods: ['POST'])]
    #[IsGranted('ROLE_STAFF')]
    public function staffRestockFromStock(int $productId, int $stockId, Request $request): Response
    {
        return $this->restockFromStock($productId, $stockId, $request, 'staff');
    }

    #[Route('/staff/stock-management/store/{id}', name: 'app_staff_stock_management_store')]
    #[IsGranted('ROLE_STAFF')]
    public function staffStore(Product $product, Request $request): Response
    {
        return $this->store($product, $request, 'staff');
    }

    #[Route('/staff/stock-management/history', name: 'app_staff_stock_management_history')]
    #[IsGranted('ROLE_STAFF')]
    public function staffHistory(Request $request): Response
    {
        return $this->history($request);
    }

    #[Route('/staff/stock-management/product-history/{id}', name: 'app_staff_stock_management_product_history')]
    #[IsGranted('ROLE_STAFF')]
    public function staffProductHistory(Product $product): Response
    {
        return $this->productHistory($product);
    }

    // Shared implementation methods
    private function index(): Response
    {
        $stocks = $this->stockRepository->findBy([], ['createdAt' => 'DESC']);
        $products = $this->productRepository->findBy([], ['name' => 'ASC']);
        $lowStockProducts = $this->productRepository->findLowStockProducts();
        
        return $this->render('StockManagementFolder/index.html.twig', [
            'stocks' => $stocks,
            'products' => $products,
            'lowStockProducts' => $lowStockProducts,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isStaff' => $this->isGranted('ROLE_STAFF'),
        ]);
    }

    private function create(Request $request, string $role): Response
    {
        $stock = new Stock();
        $stock->setCreatedBy($this->getUser());

        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($stock);
            
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('CREATE');
            $activityLog->setTargetData(sprintf(
                'Stock Created: %s - Quantity: %d | Notes: %s',
                $stock->getProduct()->getName(),
                $stock->getQuantity(),
                $stock->getNotes() ?: 'None'
            ));
            $this->entityManager->persist($activityLog);
            
            $this->entityManager->flush();

            $this->addFlash('success', sprintf(
                'Stock created successfully! Added %d units of %s to stock.',
                $stock->getQuantity(),
                $stock->getProduct()->getName()
            ));

            $routeName = $role === 'admin' ? 'app_admin_stock_management_index' : 'app_staff_stock_management_index';
            return $this->redirectToRoute($routeName);
        }

        $products = $this->productRepository->findBy([], ['name' => 'ASC']);

        return $this->render('StockManagementFolder/create.html.twig', [
            'form' => $form->createView(),
            'products' => $products,
        ]);
    }

    private function restock(Product $product): Response
    {
        $availableStocks = $this->stockRepository->findBy(['product' => $product], ['createdAt' => 'ASC']);
        
        return $this->render('StockManagementFolder/restock.html.twig', [
            'product' => $product,
            'availableStocks' => $availableStocks,
        ]);
    }

    private function restockFromStock(int $productId, int $stockId, Request $request, string $role): Response
    {
        $product = $this->productRepository->find($productId);
        $stock = $this->stockRepository->find($stockId);

        if (!$product || !$stock) {
            throw $this->createNotFoundException('Product or Stock not found');
        }

        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('restock_' . $stockId, $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            $routeName = $role === 'admin' ? 'app_admin_stock_management_restock' : 'app_staff_stock_management_restock';
            return $this->redirectToRoute($routeName, ['id' => $productId]);
        }

        $quantityToRestock = (int) $request->request->get('quantity', $stock->getQuantity());
        
        if ($quantityToRestock <= 0) {
            $this->addFlash('error', 'Invalid quantity to restock.');
            $routeName = $role === 'admin' ? 'app_admin_stock_management_restock' : 'app_staff_stock_management_restock';
            return $this->redirectToRoute($routeName, ['id' => $productId]);
        }
        
        if ($quantityToRestock > $stock->getQuantity()) {
            $this->addFlash('error', 'Cannot restock more than available stock quantity.');
            $routeName = $role === 'admin' ? 'app_admin_stock_management_restock' : 'app_staff_stock_management_restock';
            return $this->redirectToRoute($routeName, ['id' => $productId]);
        }

        $currentStock = $product->getStockQuantity();
        $newStock = $currentStock + $quantityToRestock;
        $product->setStockQuantity($newStock);

        if ($quantityToRestock >= $stock->getQuantity()) {
            $this->entityManager->remove($stock);
        } else {
            $stock->setQuantity($stock->getQuantity() - $quantityToRestock);
        }

        $transaction = new StockTransaction();
        $transaction->setProduct($product);
        $transaction->setUser($this->getUser());
        $transaction->setType(StockTransaction::TYPE_RESTOCK);
        $transaction->setQuantity($quantityToRestock);
        $transaction->setNotes('Restocked from stock inventory');
        $this->entityManager->persist($transaction);

        $activityLog = new ActivityLog();
        $activityLog->setUser($this->getUser());
        $activityLog->setUsername($this->getUser()->getUserIdentifier());
        $activityLog->setRole(json_encode($this->getUser()->getRoles()));
        $activityLog->setAction('RESTOCK');
        $activityLog->setTargetData(sprintf(
            'Product: %s - Restocked: +%d | New Stock: %d',
            $product->getName(),
            $quantityToRestock,
            $newStock
        ));
        $this->entityManager->persist($activityLog);

        $this->entityManager->flush();

        $this->addFlash('success', sprintf(
            'Successfully restocked %d units to %s. New stock level: %d units.',
            $quantityToRestock,
            $product->getName(),
            $newStock
        ));

        $routeName = $role === 'admin' ? 'app_admin_stock_management_index' : 'app_staff_stock_management_index';
        return $this->redirectToRoute($routeName);
    }

    private function store(Product $product, Request $request, string $role): Response
    {
        if ($request->isMethod('POST')) {
            $quantityToStore = (int) $request->request->get('quantity');
            
            if ($quantityToStore <= 0 || $quantityToStore > $product->getStockQuantity()) {
                $this->addFlash('error', 'Invalid quantity to store.');
                $routeName = $role === 'admin' ? 'app_admin_stock_management_index' : 'app_staff_stock_management_index';
                return $this->redirectToRoute($routeName);
            }

            $stock = new Stock();
            $stock->setProduct($product);
            $stock->setQuantity($quantityToStore);
            $stock->setNotes($request->request->get('notes', 'Stored from product inventory'));
            $stock->setCreatedBy($this->getUser());
            $this->entityManager->persist($stock);

            $currentStock = $product->getStockQuantity();
            $newStock = $currentStock - $quantityToStore;
            $product->setStockQuantity($newStock);

            $transaction = new StockTransaction();
            $transaction->setProduct($product);
            $transaction->setUser($this->getUser());
            $transaction->setType(StockTransaction::TYPE_ADJUSTMENT);
            $transaction->setQuantity(-$quantityToStore);
            $transaction->setNotes('Stored back to stock inventory');
            $this->entityManager->persist($transaction);

            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('ADJUST');
            $activityLog->setTargetData(sprintf(
                'Product: %s - Stored: -%d | New Stock: %d',
                $product->getName(),
                $quantityToStore,
                $newStock
            ));
            $this->entityManager->persist($activityLog);

            $this->entityManager->flush();

            $this->addFlash('success', sprintf(
                'Successfully stored %d units of %s back to stock inventory.',
                $quantityToStore,
                $product->getName()
            ));

            $routeName = $role === 'admin' ? 'app_admin_stock_management_index' : 'app_staff_stock_management_index';
            return $this->redirectToRoute($routeName);
        }

        return $this->render('StockManagementFolder/store.html.twig', [
            'product' => $product,
        ]);
    }

    private function deleteStock(Stock $stock, Request $request, string $role): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_stock_' . $stock->getId(), $submittedToken)) {
            $this->addFlash('error', 'Invalid CSRF token.');
            $routeName = $role === 'admin' ? 'app_admin_stock_management_index' : 'app_staff_stock_management_index';
            return $this->redirectToRoute($routeName);
        }

        $productName = $stock->getProduct()->getName();
        $quantity = $stock->getQuantity();

        $this->entityManager->remove($stock);

        $activityLog = new ActivityLog();
        $activityLog->setUser($this->getUser());
        $activityLog->setUsername($this->getUser()->getUserIdentifier());
        $activityLog->setRole(json_encode($this->getUser()->getRoles()));
        $activityLog->setAction('DELETE');
        $activityLog->setTargetData(sprintf(
            'Stock Deleted: %s - Quantity: %d',
            $productName,
            $quantity
        ));
        $this->entityManager->persist($activityLog);

        $this->entityManager->flush();

        $this->addFlash('success', sprintf(
            'Stock entry deleted: %d units of %s',
            $quantity,
            $productName
        ));

        $routeName = $role === 'admin' ? 'app_admin_stock_management_index' : 'app_staff_stock_management_index';
        return $this->redirectToRoute($routeName);
    }

    private function history(Request $request): Response
    {
        $transactions = $this->stockTransactionRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('StockManagementFolder/history.html.twig', [
            'transactions' => $transactions,
        ]);
    }

    private function productHistory(Product $product): Response
    {
        $transactions = $this->stockTransactionRepository->findBy(
            ['product' => $product],
            ['createdAt' => 'DESC']
        );

        return $this->render('StockManagementFolder/product_history.html.twig', [
            'product' => $product,
            'transactions' => $transactions,
        ]);
    }
}
