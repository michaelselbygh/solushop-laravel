<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Auth;

use App\AccountTransaction;
use App\OrderItem;
use App\Product;
use App\Vendor;

class VendorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:vendor');
    }

    public function index()
    {
        $dashboard["transactions"] = AccountTransaction::where([
            ['trans_debit_account', "=", Auth::guard('vendor')->user()->id]
        ])->get()->toArray();

        return view('portal.main.vendor.dashboard')
            ->with("dashboard", $dashboard);
    }

    public function showProducts(){
        return view("portal.main.vendor.products")
            ->with("products", Product::where([
                ["product_state", "<>", 4],
                ["product_vid", "=", Auth::guard('vendor')->user()->id]
            ])
            ->with('vendor', 'images', 'state')
            ->get()
            ->toArray());
    }

    public function processProducts(Request $request){
        switch ($request->product_action) {
            case 'delete':
                /*--- change product state ---*/
                Product::
                    where([
                        ['id', "=", $request->product_id]
                    ])->update([
                        'product_state' => 4
                    ]);
                /*--- log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Deleted';
                })
                ->log(Auth::guard('manager')->user()->email." deleted product ".$request->product_id);
                return redirect()->back()->with("success_message", "Product ".$request->product_id." deleted successfully.");
                break;
            
            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
        
    }

}
