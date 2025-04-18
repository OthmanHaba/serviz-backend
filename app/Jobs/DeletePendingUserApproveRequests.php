<?php

namespace App\Jobs;

use App\Enums\ServiceStatus;
use App\Models\ActiveRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeletePendingUserApproveRequests implements ShouldQueue
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
        ActiveRequest::whereStatus(ServiceStatus::PendingUserApproved)->delete();
    }
}
