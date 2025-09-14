<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;

class CleanupUsers extends Command
{
    protected $signature = 'users:cleanup';

    protected $description = '3 kundan eski foydalanuvchilarni ochirish';

    public function handle(UserService $userService)
    {
        $this->info('Tasdiqlanmagan foydalanuvchilarni tozalash boshlandi...');

        $deletedCount = $userService->cleanupUnverifiedUsers();

        $this->info("Muvaffaqiyatli ochirildi: {$deletedCount} ta tasdiqlanmagan foydalanuvchi.");

        return Command::SUCCESS;
    }
}
