<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\otpModel;

class DeleteExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otps:delete-expired-otps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired OTP records from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        // Soft delete expired OTPs
       $expiredCount = otpModel::whereNotNull('otpExpiresAt	') 
            ->where('otpExpiresAt	', '<=', $now) 
            ->forceDelete(); 

        $this->info($expiredCount . ' expired OTPs permanently deleted.');
    }
}