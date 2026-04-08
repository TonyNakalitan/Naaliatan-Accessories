<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private CharacterRepository $characterRepository,
        private ProductRepository $productRepository,
        private UserRepository $userRepository,
        private OrderRepository $orderRepository
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Gather statistics for dashboard
        $stats = [
            'totalProducts' => $this->productRepository->count([]),
            'totalCharacters' => $this->characterRepository->count([]),
            'totalUsers' => $this->userRepository->count([]),
            'todayOrders' => $this->getTodayOrdersCount(),
        ];

        return $this->render('HomeFolder/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    private function getTodayOrdersCount(): int
    {
        try {
            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            
            $tomorrow = clone $today;
            $tomorrow->modify('+1 day');
            
            $orders = $this->orderRepository->createQueryBuilder('o')
                ->where('o.createdAt >= :today')
                ->andWhere('o.createdAt < :tomorrow')
                ->setParameter('today', $today)
                ->setParameter('tomorrow', $tomorrow)
                ->getQuery()
                ->getResult();
            
            return count($orders);
        } catch (\Exception $e) {
            return 0;
        }
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('HomeFolder/about.html.twig');
    }

    #[Route('/products', name: 'app_products')]
    public function products(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 5;
        
        $totalCharacters = $this->characterRepository->count([]);
        $totalPages = (int) ceil($totalCharacters / $limit);
        $offset = ($page - 1) * $limit;
        
        $characters = $this->characterRepository->findBy(
            [],
            ['name' => 'ASC'],
            $limit,
            $offset
        );
        
        // Calculate stats for the stats overview section
        $totalProducts = $this->productRepository->count([]);
        $topRated = max(0, $totalProducts - 5); // Placeholder for top rated products
        $specialOffers = max(0, intval($totalProducts * 0.1)); // Placeholder for special offers
        
        return $this->render('HomeFolder/products.html.twig', [
            'characters' => $characters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCharacters' => $totalCharacters,
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalCharacters' => $totalCharacters,
                'topRated' => $topRated,
                'specialOffers' => $specialOffers,
            ],
        ]);
    }

    #[Route('/view-products', name: 'app_view_products')]
    #[Route('/view-products/character/{characterId}', name: 'app_view_products_by_character')]
    public function viewProducts(?int $characterId = null): Response
    {
        $character = null;
        
        if ($characterId) {
            $character = $this->characterRepository->find($characterId);
            if (!$character) {
                $this->addFlash('error', 'Character not found.');
                return $this->redirectToRoute('app_view_products');
            }
            $products = $this->productRepository->findBy(['character' => $character], ['createdAt' => 'DESC']);
        } else {
            $products = $this->productRepository->findBy([], ['createdAt' => 'DESC']);
        }
        
        // Calculate stats for the stats overview section
        $totalProducts = $this->productRepository->count([]);
        $totalCharacters = $this->characterRepository->count([]);
        $topRated = max(0, $totalProducts - 5); // Placeholder for top rated products
        $specialOffers = max(0, intval($totalProducts * 0.1)); // Placeholder for special offers
        
        return $this->render('HomeFolder/view_product.html.twig', [
            'products' => $products,
            'character' => $character,
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalCharacters' => $totalCharacters,
                'topRated' => $topRated,
                'specialOffers' => $specialOffers,
            ],
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('HomeFolder/contact.html.twig');
    }
}
