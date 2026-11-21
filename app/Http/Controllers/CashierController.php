<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('cashier.index', compact('products'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:tunai,qris'],
            'paid_amount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
            'is_custom_date' => ['nullable', 'boolean'],
            'transaction_date' => ['nullable', 'required_if:is_custom_date,true', 'date', 'before_or_equal:today'],
        ]);

        try {
            $transaction = $this->processTransaction($data);
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['checkout' => $e->getMessage()]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Transaksi berhasil dicatat.',
                'transaction' => $transaction,
                'receipt_url' => route('transaksi.show', $transaction),
            ]);
        }

        return redirect()
            ->route('transaksi.show', $transaction)
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    /**
     * @param array{
     *     items: array<array{product_id: int, quantity: int}>,
     *     payment_method: 'tunai'|'qris',
     *     paid_amount: int,
     *     note?: string|null,
     *     is_custom_date?: bool|null,
     *     transaction_date?: string|null
     * } $data
     */
    private function processTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $productIds = array_column($data['items'], 'product_id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            $itemsToSave = [];

            foreach ($data['items'] as $itemData) {
                $product = $products[$itemData['product_id']] ?? null;

                if (! $product) {
                    throw new \Exception('Produk tidak ditemukan.');
                }

                if ($product->track_stock && $product->stock < $itemData['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi (tersedia: {$product->stock} {$product->unit}).");
                }

                $subtotal = $product->price * $itemData['quantity'];
                $total += $subtotal;

                $itemsToSave[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $subtotal,
                ];

                if ($product->track_stock) {
                    $product->decrement('stock', $itemData['quantity']);
                }
            }

            $changeAmount = 0;
            if ($data['payment_method'] === 'tunai') {
                if ($data['paid_amount'] < $total) {
                    throw new \Exception('Uang yang diterima kurang dari total.');
                }
                $changeAmount = $data['paid_amount'] - $total;
            }

            $isCustomDate = ! empty($data['is_custom_date']) && ! empty($data['transaction_date']);
            $txDate = $isCustomDate
                ? Carbon::parse($data['transaction_date'])->setTime(now()->hour, now()->minute, now()->second)
                : now();

            $invoiceNumber = $this->generateInvoiceNumber($txDate);

            $transaction = new Transaction([
                'invoice_number' => $invoiceNumber,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'paid_amount' => $data['paid_amount'] ?: $total,
                'change_amount' => $changeAmount,
                'status' => 'selesai',
                'note' => $data['note'] ?? null,
            ]);

            if ($isCustomDate) {
                $transaction->created_at = $txDate;
                $transaction->updated_at = $txDate;
            }

            $transaction->save();

            foreach ($itemsToSave as $itemData) {
                $item = new TransactionItem(array_merge($itemData, ['transaction_id' => $transaction->id]));
                if ($isCustomDate) {
                    $item->created_at = $txDate;
                    $item->updated_at = $txDate;
                }
                $item->save();

                $product = $products[$itemData['product_id']];
                if ($product->track_stock) {
                    $movement = new StockMovement([
                        'product_id' => $product->id,
                        'type' => 'keluar',
                        'quantity' => $itemData['quantity'],
                        'note' => "Transaksi {$invoiceNumber}",
                        'transaction_id' => $transaction->id,
                        'movement_date' => $txDate->toDateString(),
                    ]);
                    if ($isCustomDate) {
                        $movement->created_at = $txDate;
                        $movement->updated_at = $txDate;
                    }
                    $movement->save();
                }
            }

            return $transaction->load('items.product');
        });
    }

    private function generateInvoiceNumber(?Carbon $date = null): string
    {
        $datePart = ($date ?? now())->format('Ymd');
        $prefix = "INV-{$datePart}-";

        $lastNumber = Transaction::where('invoice_number', 'like', "{$prefix}%")
            ->latest('id')
            ->value('invoice_number');

        $sequence = 1;
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, strlen($prefix));
            $sequence = $lastSequence + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
