<?php

namespace App\Exports;

use App\ActivityLog;
use Maatwebsite\Excel\Concerns\FromCollection;

class ActivityLogExport implements FromCollection
{
    public function collection()
    {
        return ActivityLog::all();
    }
}
