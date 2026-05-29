<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CharacterRepository;
use App\Repository\ProductRepository;
use App\Service\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductManagementController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private CharacterRepository $characterRepository,
        private CloudinaryService $cloudinaryService,
    ) {
    }

    // Admin routes
    #[Route('/admin/product-management', name: 'app_admin_product_management_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(Request $request): Response
    {
        return $this->index($request);
    }

    #[Route('/admin/product-management/new', name: 'app_admin_product_management_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->new($request, $entityManager, 'admin');
    }

    #[Route('/admin/product-management/{id}/edit', name: 'app_admin_product_management_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminEdit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $product, $entityManager, 'admin');
    }

    #[Route('/admin/product-management/{id}', name: 'app_admin_product_management_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDelete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->delete($request, $product, $entityManager, 'admin');
    }

    #[Route('/admin/product-management/{id}', name: 'app_admin_product_management_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(Product $product): Response
    {
        return $this->show($product);
    }

    // Alternative routes for /admin/products (used by templates)
    #[Route('/admin/products/{id}/edit', name: 'app_admin_products_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminProductsEdit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $product, $entityManager, 'admin');
    }

    #[Route('/admin/products/{id}', name: 'app_admin_products_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminProductsDelete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->delete($request, $product, $entityManager, 'admin');
    }

    // Staff routes
    #[Route('/staff/product-management', name: 'app_staff_product_management_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(Request $request): Response
    {
        return $this->index($request);
    }

    #[Route('/staff/product-management/new', name: 'app_staff_product_management_new')]
    #[IsGranted('ROLE_STAFF')]
    public function staffNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('error', 'Staff are not permitted to add new products.');
        return $this->redirectToRoute('app_staff_product_management_index');
    }

    #[Route('/staff/product-management/{id}/edit', name: 'app_staff_product_management_edit')]
    #[IsGranted('ROLE_STAFF')]
    public function staffEdit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $product, $entityManager, 'staff');
    }

    #[Route('/staff/product-management/{id}', name: 'app_staff_product_management_show')]
    #[IsGranted('ROLE_STAFF')]
    public function staffShow(Product $product): Response
    {
        return $this->show($product);
    }

    // Shared implementation methods
    private function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $status = $request->query->get('status');

        // Load all matching products for the management view
        $products = $this->productRepository->findFilteredProducts($search, $status);
        $totalProducts = count($products);
        $outOfStockCount = $this->productRepository->getOutOfStockCount();

        return $this->render('ProductManagementFolder/index.html.twig', [
            'products' => $products,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isStaff' => $this->isGranted('ROLE_STAFF'),
            'currentPage' => 1,
            'totalPages' => 1,
            'totalProducts' => $totalProducts,
            'outOfStockCount' => $outOfStockCount,
            'limit' => $totalProducts,
            'search' => $search,
            'status' => $status,
        ]);
    }

    private function new(Request $request, EntityManagerInterface $entityManager, string $role): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for duplicate product code
            $existingProduct = $this->productRepository->findOneBy(['productCode' => $product->getProductCode()]);
            if ($existingProduct) {
                $this->addFlash('error', 'A product with code "' . $product->getProductCode() . '" already exists. Please use a different product code.');
                
                // Get all characters for horizontal scroll layout
                $characters = $this->characterRepository->findAll();
                
                return $this->render('ProductManagementFolder/new.html.twig', [
                    'product' => $product,
                    'form' => $form->createView(),
                    'characters' => $characters,
                ]);
            }

            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                try {
                    $product->setImage($this->cloudinaryService->upload($imageFile, 'products'));
                } catch (\Exception $e) {
                    $this->addFlash('error', 'There was an error uploading the image.');
                }
            }
            
            $product->setCreatedBy($this->getUser());
            $entityManager->persist($product);
            $entityManager->flush();

            // Log the product creation activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('CREATE');
            $activityLog->setTargetData('Product: ' . $product->getName() . ' (ID: ' . $product->getId() . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully!');

            $routeName = $role === 'admin' ? 'app_admin_product_management_index' : 'app_staff_product_management_index';
            return $this->redirectToRoute($routeName);
        }

        // Get all characters for horizontal scroll layout
        $characters = $this->characterRepository->findAll();

        return $this->render('ProductManagementFolder/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'characters' => $characters,
        ]);
    }

    private function edit(Request $request, Product $product, EntityManagerInterface $entityManager, string $role): Response
    {
        // Staff can only edit if they didn't create it (admin products)
        if ($this->isGranted('ROLE_STAFF') && $product->getCreatedBy() && $product->getCreatedBy()->isStaff()) {
            $this->addFlash('error', 'You cannot edit staff-created products.');
            $routeName = $role === 'admin' ? 'app_admin_product_management_index' : 'app_staff_product_management_index';
            return $this->redirectToRoute($routeName);
        }

        $originalProductCode = $product->getProductCode();
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for duplicate product code (only if it was changed)
            if ($product->getProductCode() !== $originalProductCode) {
                $existingProduct = $this->productRepository->findOneBy(['productCode' => $product->getProductCode()]);
                if ($existingProduct && $existingProduct->getId() !== $product->getId()) {
                    $this->addFlash('error', 'A product with code "' . $product->getProductCode() . '" already exists. Please use a different product code.');
                    
                    // Reset to original product code
                    $product->setProductCode($originalProductCode);
                    
                    return $this->render('ProductManagementFolder/edit.html.twig', [
                        'product' => $product,
                        'form' => $form->createView(),
                        'characters' => $this->characterRepository->findAll(),
                    ]);
                }
            }

            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Delete old image from Cloudinary if exists
                if ($product->getImage()) {
                    $this->cloudinaryService->delete($product->getImage());
                }

                try {
                    $product->setImage($this->cloudinaryService->upload($imageFile, 'products'));
                } catch (\Exception $e) {
                    $this->addFlash('error', 'There was an error uploading the image.');
                }
            }
            
            $entityManager->flush();

            // Log the product edit activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('UPDATE');
            $activityLog->setTargetData('Product: ' . $product->getName() . ' (ID: ' . $product->getId() . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully!');

            $routeName = $role === 'admin' ? 'app_admin_product_management_index' : 'app_staff_product_management_index';
            return $this->redirectToRoute($routeName);
        }

        // Get all characters for horizontal scroll layout
        $characters = $this->characterRepository->findAll();

        return $this->render('ProductManagementFolder/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'characters' => $characters,
        ]);
    }

    private function delete(Request $request, Product $product, EntityManagerInterface $entityManager, string $role): Response
    {
        if ($this->isCsrfTokenValid('delete_product', $request->request->get('_token'))) {
            $productName = $product->getName();
            $productId = $product->getId();

            $entityManager->remove($product);
            $entityManager->flush();

            // Log the product deletion activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('DELETE');
            $activityLog->setTargetData('Product: ' . $productName . ' (ID: ' . $productId . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully!');
        }

        $routeName = $role === 'admin' ? 'app_admin_product_management_index' : 'app_staff_product_management_index';
        return $this->redirectToRoute($routeName);
    }

    private function show(Product $product): Response
    {
        return $this->render('ProductManagementFolder/show.html.twig', [
            'product' => $product,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isStaff' => $this->isGranted('ROLE_STAFF'),
        ]);
    }
}
