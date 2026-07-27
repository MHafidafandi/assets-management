<?php

namespace App\Jobs;

use App\Exports\UsersExport;
use App\Models\DataJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessUserExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $exportJobId,
        public array $fields = []
    ) {}

    public function handle(): void
    {
        $exportJob = DataJob::findOrFail($this->exportJobId);
        $exportJob->update(['status' => 'processing']);

        try {
            $fileName = 'exports/users_' . now()->format('Ymd_His') . '_' . $exportJob->uuid . '.xlsx';
            Excel::store(new UsersExport($this->fields), $fileName, 'public');

            $exportJob->update([
                'status'    => 'completed',
                'file_path' => $fileName,
            ]);
        } catch (\Throwable $e) {
            $exportJob->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
