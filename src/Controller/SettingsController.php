<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SettingsController extends AbstractController
{
    // Admin routes
    #[Route('/admin/settings', name: 'app_admin_settings_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(): Response
    {
        return $this->index();
    }

    // Staff routes
    #[Route('/staff/settings', name: 'app_staff_settings_index')]
    #[IsGranted('ROLE_STAFF')]
    public function staffIndex(): Response
    {
        return $this->index();
    }

    // Shared implementation
    private function index(): Response
    {
        return $this->render('SettingsFolder/index.html.twig');
    }
}
