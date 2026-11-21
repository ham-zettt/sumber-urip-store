<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

function createCompletedTransaction(string $createdAt, int $total): Transaction
{
    $transaction = Transaction::create([
        'invoice_number' => 'INV-'.str()->random(8),
        'total' => $total,
        'payment_method' => 'tunai',
        'paid_amount' => $total,
        'change_amount' => 0,
        'status' => 'selesai',
    ]);

    $transaction->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

    return $transaction;
}

it('scopes store statistics to the selected period', function () {
    $this->actingAs(User::factory()->create());
    $this->travelTo(Carbon::parse('2026-11-21 12:00:00'));

    createCompletedTransaction('2026-11-21 09:00:00', 100000);
    createCompletedTransaction('2026-11-18 09:00:00', 200000);
    createCompletedTransaction('2026-11-05 09:00:00', 300000);
    createCompletedTransaction('2026-10-30 09:00:00', 999000);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Hari Ini')
        ->assertSee('Rp 100.000');

    $this->get(route('dashboard', ['period' => 'week']))
        ->assertOk()
        ->assertSee('Minggu Ini')
        ->assertSee('Rp 300.000');

    $this->get(route('dashboard', ['period' => 'month']))
        ->assertOk()
        ->assertSee('Bulan Ini')
        ->assertSee('Rp 600.000');
});

it('shows the three latest transactions on the dashboard', function () {
    $this->actingAs(User::factory()->create());
    $this->travelTo(Carbon::parse('2026-11-21 12:00:00'));

    createCompletedTransaction('2026-11-21 10:00:00', 10000);
    $latest = createCompletedTransaction('2026-11-21 11:00:00', 20000);
    createCompletedTransaction('2026-11-20 11:00:00', 30000);
    createCompletedTransaction('2026-11-19 11:00:00', 40000);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Transaksi Terbaru')
        ->assertSee($latest->invoice_number)
        ->assertDontSee('Tag & Kategori Populer');
});
