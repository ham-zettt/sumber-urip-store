<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $period = in_array($request->input('period'), ['today', 'week', 'month'], true) ? $request->input('period') : 'today';
        $periodStart = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()->startOfDay(),
        };
        $periodLabel = match ($period) {
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            default => 'Hari Ini',
        };

        $stats = Transaction::query()
            ->whereBetween('created_at', [$periodStart, now()])
            ->where('status', 'selesai')
            ->selectRaw('sum(total) as total_revenue, count(*) as transaction_count, sum((select coalesce(sum(quantity), 0) from transaction_items where transaction_items.transaction_id = transactions.id)) as items_sold')
            ->first();

        $latestTransactions = Transaction::query()
            ->withSum('items', 'quantity')
            ->latest('created_at')
            ->limit(3)
            ->get();

        $totalBalance = (int) (Transaction::query()
            ->where('status', 'selesai')
            ->sum('total') ?? 0);

        $lowStockProducts = Product::query()
            ->where('track_stock', true)
            ->where('is_active', true)
            ->whereRaw('stock <= min_stock')
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        $topProducts = TransactionItem::query()
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.status', 'selesai')
            ->where('transactions.created_at', '>=', now()->subDays(30))
            ->selectRaw('transaction_items.product_id, products.name, products.unit, sum(transaction_items.quantity) as total_qty, sum(transaction_items.subtotal) as total_revenue')
            ->groupBy('transaction_items.product_id', 'products.name', 'products.unit')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'period',
            'periodLabel',
            'totalBalance',
            'lowStockProducts',
            'topProducts',
            'latestTransactions'
        ));
    }

    public function chart(Request $request): JsonResponse
    {
        $range = $request->integer('range', 7);
        $days = in_array($range, [7, 30]) ? $range : 7;

        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyStats = Transaction::query()
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, sum(total) as total, count(*) as count, sum((select coalesce(sum(quantity), 0) from transaction_items where transaction_items.transaction_id = transactions.id)) as items')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        $counts = [];
        $items = [];
        for ($i = 0; $i < $days; $i++) {
            $dt = now()->subDays($days - 1 - $i);
            $date = $dt->format('Y-m-d');
            $labels[] = $dt->translatedFormat('d M');
            $data[] = (int) ($dailyStats[$date]?->total ?? 0);
            $counts[] = (int) ($dailyStats[$date]?->count ?? 0);
            $items[] = (int) ($dailyStats[$date]?->items ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'revenue' => $data,
            'counts' => $counts,
            'items' => $items,
        ]);
    }
}
