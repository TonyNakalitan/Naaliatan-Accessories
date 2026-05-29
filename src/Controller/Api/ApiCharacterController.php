<?php

namespace App\Controller\Api;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/characters')]
#[IsGranted('ROLE_CUSTOMER')]
class ApiCharacterController extends AbstractController
{
    public function __construct(private CharacterRepository $characterRepository)
    {
    }

    #[Route('', name: 'api_characters_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $search = trim($request->query->get('search', ''));

        if ($search) {
            $characters = $this->characterRepository->searchCharacters($search);
        } else {
            $characters = $this->characterRepository->findBy([], ['name' => 'ASC']);
        }

        return $this->json(array_map(fn(Character $character) => $this->serializeCharacter($character), $characters));
    }

    #[Route('/{id}', name: 'api_characters_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $character = $this->characterRepository->find($id);

        if (!$character) {
            return $this->json(['message' => 'Character not found.'], 404);
        }

        return $this->json($this->serializeCharacter($character));
    }

    private function serializeCharacter(Character $character): array
    {
        return [
            'id' => $character->getId(),
            'name' => $character->getName(),
            'alignment' => $character->getAlignment(),
            'description' => $character->getDescription(),
            'image' => $character->getImage(),
            'createdAt' => $character->getCreatedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}
