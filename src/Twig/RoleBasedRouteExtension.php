<?php

namespace App\Twig;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoleBasedRouteExtension extends AbstractExtension
{
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('role_path', [$this, 'generateRolePath']),
            new TwigFunction('role_url', [$this, 'generateRoleUrl']),
        ];
    }

    /**
     * Generate path with role prefix based on current user's role
     * 
     * @param string $baseName Base route name without role prefix (e.g., 'product_management_index')
     * @param array $parameters Route parameters
     * @return string Generated path
     */
    public function generateRolePath(string $baseName, array $parameters = []): string
    {
        $routeName = $this->getRoleBasedRouteName($baseName);
        return $this->urlGenerator->generate($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generate URL with role prefix based on current user's role
     * 
     * @param string $baseName Base route name without role prefix (e.g., 'product_management_index')
     * @param array $parameters Route parameters
     * @return string Generated URL
     */
    public function generateRoleUrl(string $baseName, array $parameters = []): string
    {
        $routeName = $this->getRoleBasedRouteName($baseName);
        return $this->urlGenerator->generate($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Get role-based route name
     * 
     * @param string $baseName Base route name (e.g., 'product_management_index' or 'app_product_management_index')
     * @return string Full route name with role prefix (e.g., 'app_admin_product_management_index')
     */
    private function getRoleBasedRouteName(string $baseName): string
    {
        $user = $this->security->getUser();
        
        if (!$user) {
            return $baseName;
        }

        // Remove 'app_' prefix if present
        $cleanName = str_starts_with($baseName, 'app_') ? substr($baseName, 4) : $baseName;
        
        // Determine role prefix
        $rolePrefix = 'staff'; // Default to staff
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $rolePrefix = 'admin';
        }
        
        // Build full route name
        return 'app_' . $rolePrefix . '_' . $cleanName;
    }
}
