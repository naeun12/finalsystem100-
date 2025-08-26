<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\otpModel;
use Illuminate\Foundation\Bus\Dispatchable;


class DeleteExpiredOtpsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         $now = now();
        $expiredCount = otpModel::whereNotNull('otpExpiresAt')
            ->where('otpExpiresAt', '<=', $now)
            ->forceDelete();
    }
}
