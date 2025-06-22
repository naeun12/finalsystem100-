<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\tenant\OtpModels;
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
        $expiredCount = OtpModels::whereNotNull('otpExpires_at')
            ->where('otpExpires_at', '<=', $now)
            ->forceDelete();
    }
}
