<?php

namespace App\Http\Controllers;

use App\Exports\ActivityLogExport;
use App\Exports\SMSExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

use App\ActivityLog;
use App\Count;
use App\SMS;
use App\VendorSubscription;

class CronsController extends Controller
{
    public function generateReports(){
        //generate exports
        Excel::store(new ActivityLogExport, '/reports/activity/'.date('Y-m-d').'.csv');
        Excel::store(new SMSExport, '/reports/sms/'.date('Y-m-d').'.csv');

        //empty tables
        SMS::truncate();
        ActivityLog::truncate();
    }


    public function processSMSQueue(){
        
    }

    public function updateCounts(){
        $count = Count::first();

        $count->customer_count = 0;
        $count->product_count = 0;
        $count->vendor_count = 0;
        $count->order_count = 0;

        $count->save();
        
    }

    public function checkVendorSubscriptions(){
        
    }
}
