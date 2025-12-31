<?php

namespace Database\Seeders;

use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = User::first();

    // Ensure we have a user to attach transactions to
    if (!$user) {
      $this->command->warn("No user found. Please create a user first.");
      return;
    }

    $transactions = [
      [
        'shop_name' => 'KENCANA MOTOR',
        'amount' => 180000,
        'description' => 'Karet Champ, Roller Beat',
        'date' => '2025-06-19',
        'created_at' => '2025-12-29 08:47:43',
      ],
      [
        'shop_name' => 'Shopeefood',
        'amount' => 25000,
        'description' => 'Nasi Padang',
        'date' => '2025-12-30',
        'created_at' => '2025-12-30 05:59:15',
      ],
      [
        'shop_name' => 'Shopeefood',
        'amount' => 25000,
        'description' => 'Nasi Padang',
        'date' => '2025-12-30',
        'created_at' => '2025-12-30 05:59:57',
      ],
      [
        'shop_name' => 'shopeefood',
        'amount' => 25000,
        'description' => 'Nasi Padang',
        'date' => '2025-12-30',
        'created_at' => '2025-12-30 06:20:51',
      ],
      [
        'shop_name' => 'shopeefood',
        'amount' => 25000,
        'description' => 'Nasi Padang Rendang',
        'date' => '2025-12-30',
        'created_at' => '2025-12-30 06:55:41',
      ],
      [
        'shop_name' => 'KENCANA MOTOR',
        'amount' => 180000,
        'description' => 'karet champ, roller beat H',
        'date' => '2025-12-29',
        'created_at' => '2025-12-30 06:59:43',
      ],
    ];

    foreach ($transactions as $data) {
      Transaction::create([
        'user_id' => $user->id,
        'category_id' => null, // Assuming no category for now or use a default one
        'amount' => $data['amount'],
        'type' => TransactionType::Expense,
        'description' => $data['description'], // Mapping item/description here
        'shop_name' => $data['shop_name'],
        'date' => $data['date'],
        'created_at' => Carbon::parse($data['created_at']),
        'updated_at' => Carbon::parse($data['created_at']), // Keeping updated_at same as input time for consistency
      ]);
    }
  }
}
