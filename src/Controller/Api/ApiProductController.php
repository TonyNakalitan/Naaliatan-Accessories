<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\CharacterRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/products')]
#[IsGranted('ROLE_CUSTOMER')]
class ApiProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private CharacterRepository $characterRepository
    ) {
    }

    #[Route('', name: 'api_products_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $search = trim($request->query->get('search', ''));
        $characterId = $request->query->getInt('character', 0);

        if ($search) {
            $products = $this->productRepository->searchProducts($search);
        } elseif ($characterId) {
            $products = $this->productRepository->findByCharacter($characterId);
        } else {
            $products = $this->productRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->json(array_map(fn(Product $product) => $this->serializeProduct($product), $products));
    }

    #[Route('/{id}', name: 'api_products_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found.'], 404);
        }

        return $this->json($this->serializeProduct($product));
    }

    private function serializeProduct(Product $product): array
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'productCode' => $product->getProductCode(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'stockQuantity' => $product->getStockQuantity(),
            'image' => $product->getImage(),
            'createdAt' => $product->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            'character' => $product->getCharacter() ? [
                'id' => $product->getCharacter()->getId(),
                'name' => $product->getCharacter()->getName(),
            ] : null,
        ];
    }
}
