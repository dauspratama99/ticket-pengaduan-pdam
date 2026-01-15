<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ticket;
use Iluminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function getStatistic() 
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $totalTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])->count();

        $activeTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                         ->where('status', '!=', 'resolved')
                         ->count();

        $resolvedTickets = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                         ->where('status', 'resolved')
                         ->count();

        $avgResolutionTime = Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])
                         ->where('status', 'resolved')
                         ->whereNotNull('completed_at')
                         ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) AS avg_times'))
                         ->value('avg_time') ?? 0;

        $statusDistribution = [
            'open' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])->where('status', 'open')->count(),
            'onprogress' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])->where('status', 'onprogress')->count(),
            'resolved' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])->where('status', 'resolved')->count(),
            'rejected' => Ticket::whereBetween('created_at', [$currentMonth, $endOfMonth])->where('status', 'rejected')->count(),
        ];

        $dashboardData = [
            'total_tickets' => $totalTickets,
            'active_tickets' => $activeTickets,
            'resolved_tickets' => $resolvedTickets,
            'avg_resolution_time' => $avgResolutionTime,
            'status_distribution' => $statusDistribution,
        ];
    }   
}
