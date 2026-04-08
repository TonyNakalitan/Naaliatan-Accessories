<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class CharacterManagementController extends AbstractController
{
    public function __construct(private CharacterRepository $characterRepository)
    {
    }

    // Admin routes
    #[Route('/admin/characters', name: 'app_admin_character_management_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(): Response
    {
        return $this->index();
    }

    #[Route('/admin/characters/new', name: 'app_admin_character_management_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->new($request, $entityManager, 'admin');
    }

    #[Route('/admin/characters/{id}/edit', name: 'app_admin_character_management_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminEdit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $character, $entityManager, 'admin');
    }

    #[Route('/admin/characters/{id}', name: 'app_admin_character_management_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminDelete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        return $this->delete($request, $character, $entityManager, 'admin');
    }

    #[Route('/admin/characters/{id}/show', name: 'app_admin_character_management_show')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(Character $character): Response
    {
        return $this->show($character);
    }

    // Staff routes
    #[Route('/staff/characters', name: 'app_staff_character_management_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(): Response
    {
        return $this->index();
    }

    #[Route('/staff/characters/new', name: 'app_staff_character_management_new')]
    #[IsGranted('ROLE_STAFF')]
    public function staffNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->new($request, $entityManager, 'staff');
    }

    #[Route('/staff/characters/{id}/edit', name: 'app_staff_character_management_edit')]
    #[IsGranted('ROLE_STAFF')]
    public function staffEdit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        return $this->edit($request, $character, $entityManager, 'staff');
    }

    #[Route('/staff/characters/{id}/show', name: 'app_staff_character_management_show')]
    #[IsGranted('ROLE_STAFF')]
    public function staffShow(Character $character): Response
    {
        return $this->show($character);
    }

    // Shared implementation methods
    private function index(): Response
    {
        $characters = $this->characterRepository->findBy([], ['name' => 'ASC']);
        
        return $this->render('CharacterManagementFolder/index.html.twig', [
            'characters' => $characters,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isStaff' => $this->isGranted('ROLE_STAFF'),
        ]);
    }

    private function new(Request $request, EntityManagerInterface $entityManager, string $role): Response
    {
        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('character_images_directory'),
                        $newFilename
                    );
                    $character->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading image: ' . $e->getMessage());
                }
            }
            
            $character->setCreatedBy($this->getUser());
            $entityManager->persist($character);
            $entityManager->flush();

            // Debug: Ensure character has ID after flush
            $characterId = $character->getId();
            
            // Log the character creation activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('CREATE');
            $activityLog->setTargetData('Character: ' . $character->getName() . ' (ID: ' . $characterId . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Character "' . $character->getName() . '" (ID: ' . $characterId . ') created successfully!');

            $routeName = $role === 'admin' ? 'app_admin_character_management_index' : 'app_staff_character_management_index';
            return $this->redirectToRoute($routeName);
        }

        return $this->render('CharacterManagementFolder/new.html.twig', [
            'character' => $character,
            'form' => $form->createView(),
        ]);
    }

    private function edit(Request $request, Character $character, EntityManagerInterface $entityManager, string $role): Response
    {
        // Staff can only edit if they didn't create it (admin characters)
        if ($this->isGranted('ROLE_STAFF') && $character->getCreatedBy() && $character->getCreatedBy()->isStaff()) {
            $this->addFlash('error', 'You cannot edit staff-created characters.');
            $routeName = $role === 'admin' ? 'app_admin_character_management_index' : 'app_staff_character_management_index';
            return $this->redirectToRoute($routeName);
        }

        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('character_images_directory'),
                        $newFilename
                    );
                    $character->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading image: ' . $e->getMessage());
                }
            }
            
            $entityManager->flush();

            // Log the character edit activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('UPDATE');
            $activityLog->setTargetData('Character: ' . $character->getName() . ' (ID: ' . $character->getId() . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Character updated successfully!');

            $routeName = $role === 'admin' ? 'app_admin_character_management_index' : 'app_staff_character_management_index';
            return $this->redirectToRoute($routeName);
        }

        return $this->render('CharacterManagementFolder/edit.html.twig', [
            'character' => $character,
            'form' => $form->createView(),
        ]);
    }

    private function delete(Request $request, Character $character, EntityManagerInterface $entityManager, string $role): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $characterName = $character->getName();
            $characterId = $character->getId();

            $entityManager->remove($character);
            $entityManager->flush();

            // Log the character deletion activity
            $activityLog = new ActivityLog();
            $activityLog->setUser($this->getUser());
            $activityLog->setUsername($this->getUser()->getUserIdentifier());
            $activityLog->setRole(json_encode($this->getUser()->getRoles()));
            $activityLog->setAction('DELETE');
            $activityLog->setTargetData('Character: ' . $characterName . ' (ID: ' . $characterId . ')');
            $entityManager->persist($activityLog);
            $entityManager->flush();

            $this->addFlash('success', 'Character deleted successfully!');
        }

        $routeName = $role === 'admin' ? 'app_admin_character_management_index' : 'app_staff_character_management_index';
        return $this->redirectToRoute($routeName);
    }

    private function show(Character $character): Response
    {
        return $this->render('CharacterManagementFolder/show.html.twig', [
            'character' => $character,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
            'isStaff' => $this->isGranted('ROLE_STAFF'),
        ]);
    }
}
