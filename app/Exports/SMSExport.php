<?php

namespace App\Exports;

use App\SMS;
use Maatwebsite\Excel\Concerns\FromCollection;

class SMSExport implements FromCollection
{
    public function collection()
    {
        return SMS::all();
    }
}
