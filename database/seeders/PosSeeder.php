<?php
namespace Database\Seeders;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PosSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $customers = [
            ['full_name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'phone' => '0917 123 4567'],
            ['full_name' => 'John Reyes', 'email' => 'john.reyes@example.com', 'phone' => '0918 234 5678'],
            ['full_name' => 'Angela Cruz', 'email' => 'angela.cruz@example.com', 'phone' => '0919 345 6789'],
            ['full_name' => 'Carlo Mendoza', 'email' => 'carlo.mendoza@example.com', 'phone' => '0920 456 7890'],
            ['full_name' => 'Sofia Garcia', 'email' => 'sofia.garcia@example.com', 'phone' => '0921 567 8901'],
        ];
            $users = [
            ['username' => 'admin01', 'full_name' => 'Rainier Ian Sayson'],
            ['username' => 'cashier01', 'full_name' => 'Ella Dela Cruz'],
            ['username' => 'cashier02', 'full_name' => 'Miguel Ramos'],
            ['username' => 'manager01', 'full_name' => 'Patricia Lim'],
            ['username' => 'staff01', 'full_name' => 'Daniel Flores'],
        ];
            // Repeatable without overwriting edits to existing demonstration records.
            foreach ($customers as $record) {
                Customer::firstOrCreate(['email' => $record['email']], $record);
            }
            foreach ($users as $record) {
                User::firstOrCreate(['username' => $record['username']], $record);
            }
        });
    }
}
