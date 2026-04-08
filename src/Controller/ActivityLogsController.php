<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\Repository\ActivityLogRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/activity-logs')]
class ActivityLogsController extends AbstractController
{
    public function __construct(private ActivityLogRepository $activityLogRepository)
    {
    }

    #[Route('/', name: 'app_admin_activity_logs_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): Response
    {
        // Fetch all logs for DataTables to handle pagination
        $logs = $this->activityLogRepository->findBy([], ['createdAt' => 'DESC']);
        
        // Calculate today's stats
        $today = new \DateTimeImmutable('today');
        $todayEnd = new \DateTimeImmutable('tomorrow');
        $todayLogs = $this->activityLogRepository->findByDateRange($today, $todayEnd);
        
        $todayCount = count($todayLogs);
        $loginCount = count(array_filter($todayLogs, fn($log) => $log->getAction() === 'LOGIN'));
        $updateCount = count(array_filter($todayLogs, fn($log) => $log->getAction() === 'UPDATE'));
        
        return $this->render('ActivityLogsFolder/index.html.twig', [
            'logs' => $logs,
            'todayCount' => $todayCount,
            'loginCount' => $loginCount,
            'updateCount' => $updateCount,
        ]);
    }

    #[Route('/filter', name: 'app_admin_activity_logs_filter')]
    #[IsGranted('ROLE_ADMIN')]
    public function filter(Request $request): Response
    {
        $action = $request->query->get('action');
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        $logs = [];

        if ($action) {
            $logs = $this->activityLogRepository->findByAction($action, 100);
        } elseif ($startDate && $endDate) {
            $start = new \DateTimeImmutable($startDate);
            $end = new \DateTimeImmutable($endDate . ' 23:59:59');
            $logs = $this->activityLogRepository->findByDateRange($start, $end);
        } else {
            $logs = $this->activityLogRepository->findRecentLogs(100);
        }

        // Calculate today's stats
        $today = new \DateTimeImmutable('today');
        $todayEnd = new \DateTimeImmutable('tomorrow');
        $todayLogs = $this->activityLogRepository->findByDateRange($today, $todayEnd);
        
        $todayCount = count($todayLogs);
        $loginCount = count(array_filter($todayLogs, fn($log) => $log->getAction() === 'LOGIN'));
        $createCount = count(array_filter($todayLogs, fn($log) => $log->getAction() === 'CREATE'));
        $updateCount = count(array_filter($todayLogs, fn($log) => $log->getAction() === 'UPDATE'));
        $deleteCount = count(array_filter($todayLogs, fn($log) => $log->getAction() === 'DELETE'));

        return $this->render('ActivityLogsFolder/index.html.twig', [
            'logs' => $logs,
            'currentPage' => 1,
            'totalPages' => 1,
            'todayCount' => $todayCount,
            'loginCount' => $loginCount,
            'createCount' => $createCount,
            'updateCount' => $updateCount,
            'deleteCount' => $deleteCount,
            'filters' => [
                'action' => $action,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    #[Route('/stats', name: 'app_admin_activity_logs_stats')]
    #[IsGranted('ROLE_ADMIN')]
    public function stats(): Response
    {
        $actionStats = $this->activityLogRepository->getActionStats();
        
        return $this->render('ActivityLogsFolder/stats.html.twig', [
            'actionStats' => $actionStats,
        ]);
    }

    #[Route('/user/{id}', name: 'app_admin_activity_logs_user')]
    public function userLogs(int $id, UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        
        // Security: Users can only view their own logs, admins can view any
        if (!$this->isGranted('ROLE_ADMIN') && $id !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('You can only view your own activity logs');
        }
        
        // Get all logs for the user (DataTables will handle pagination)
        $logs = $this->activityLogRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );
        
        return $this->render('ActivityLogsFolder/user_logs.html.twig', [
            'logs' => $logs,
            'user' => $user,
        ]);
    }
}
