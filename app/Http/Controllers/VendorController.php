<?php

namespace App\Http\Controllers;

use Slydepay\Order\Order as SlydepayOrder;
use Slydepay\Order\OrderItem as SlydepayOrderItem;
use Slydepay\Order\OrderItems as SlydepayOrderItems;
use Slydepay\Slydepay;

use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Auth;
use Image;

use App\AccountTransaction;
use App\CartItem;
use App\Conversation;
use App\Count;
use App\Customer;
use App\Message;
use App\MessageFlag;
use App\OrderItem;
use App\Product;
use App\ProductCategory;
use App\ProductImage;
use App\StockKeepingUnit;
use App\Vendor;
use App\VendorSubscription;
use App\VSPackage;
use App\VSPayment;
use App\WishlistItem;

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
        ])
        ->orWhere([
            ['trans_credit_account', "=", Auth::guard('vendor')->user()->id]
        ])->get()->toArray();

        $vendor_id = Auth::guard('vendor')->user()->id;

        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;

        return view('portal.main.vendor.dashboard')
            ->with("dashboard", $dashboard)
            ->with("vendor", $vendor);
    }

    public function showProducts(){

        $vendor_id = Auth::guard('vendor')->user()->id;
        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;

        return view("portal.main.vendor.products")
            ->with("products", Product::where([
                ["product_state", "<>", 4],
                ["product_vid", "=", Auth::guard('vendor')->user()->id]
            ])
            ->with('vendor', 'images', 'state')
            ->get()
            ->toArray())
            ->with("vendor", $vendor);
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
                ->log(Auth::guard('manager')->user()->name." deleted product ".$request->product_id);
                return redirect()->route("vendor.show.products")->with("success_message", "Product ".$request->product_id." deleted successfully.");
                break;
            
            default:
                return redirect()->route("vendor.show.products")->with("error_message", "Something went wrong. Please try again.");
                break;
        }
        
    }

    public function showAddProduct(){
        /*--- Check for subscription and allowance for new product ---*/
        if (is_null(VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->with('package')->first()) OR VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->with('package')->first()->vs_days_left < 1) {
            return redirect()->route('vendor.show.products')->with("error_message", "You currently have no active subscriptions. Please subscribe to be able to upload a product.");
        }elseif(Product::where('product_vid', Auth::guard('vendor')->user()->id)->whereIn('product_state', [1, 2, 3, 5])->get()->count() >= VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->with('package')->first()->package->vs_package_product_cap){
            return redirect()->route('vendor.show.products')->with("error_message", "You have currently exceeded the maximum allowed products for upload on your current subscription. Please upgrade to continue uploading");
        }

        /*--- Category Options ---*/
        $product["category_options"] = ProductCategory::orderBy('pc_description')->where('pc_level', 3)->get()->toArray();
        $vendor_id = Auth::guard('vendor')->user()->id;
        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;
 
        return view("portal.main.vendor.add-product")
            ->with("product", $product)
            ->with("vendor", $vendor);
    }
 
    public function processAddProduct(Request $request){
        /*--- Validate Details ---*/
        /*--- Validate form data  ---*/
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'features' => 'required',
            'category' => 'required',
            'selling_price' => 'required',
            'discount' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            $messageType = "error_message";
            $messageContent = "";

            foreach ($validator->messages()->getMessages() as $field_name => $messages)
            {
                $messageContent .= $messages[0]." "; 
            }

            return redirect()->back()->with($messageType, $messageContent);
        }

        /*--- Validate Images ---*/
        for ($i=0; $i < sizeof($request->product_images); $i++) { 
            if($request->product_images[$i]->getClientOriginalExtension() != "jpg"){
                return back()->with("error_message", "Images must be of type jpg");
            }

            list($width, $height) = getimagesize($request->product_images[$i]);
            if ($width != $height or $height < 600) {
                return back()->with("error_message", "Images must be minimum height 600px with aspect ratio of 1");
            }

            if(filesize($request->product_images[$i]) > 5000000){
                return back()->with("error_message", "One or more images exceed the allowed size for upload.");
            }
        }

        /*--- Validate and generate Product Slug ---*/
        if((Product::where([
            ['product_vid', '=', Auth::guard('vendor')->user()->id],
            ['product_name', '=', $request->name]
        ])->get()->count()) > 0){
            $product_slug_count = Product::where([
                ['product_vid', '=', Auth::guard('vendor')->user()->id],
                ['product_name', '=', $request->name]
            ])->get()->count();
            $product_slug_count++;
            $product_slug = str_slug($request->name)."-".$product_slug_count;
        }else{
            $product_slug = str_slug($request->name);
        }

        /*--- Generate product id and set detail variables ---*/
        $count = Count::first();
        $count->product_count++;

        $product = New Product;
        $product_id = "P-".date("Ymd")."-".$count->product_count;
        $product->id = $product_id;
        $product->product_vid = Auth::guard('vendor')->user()->id;
        $product->product_name = ucwords(strtolower($request->name));
        $product->product_slug = $product_slug;
        $product->product_features = $request->features;
        $product->product_cid = $request->category;
        $product->product_settlement_price = 0;
        $product->product_selling_price = $request->selling_price;
        $product->product_discount = $request->discount;
        $product->product_dd = 1;
        $product->product_dc = 1;
        $product->product_description = $request->description;
        $product->product_tags = $request->tags;
        $product->product_type = $request->type;
        $product->product_state = 2;
        $product->product_views = 0;


        /*--- Save product stock --- */
        $count->sku_count++;

        $sku = new StockKeepingUnit;
        $sku->id                        = "S-".($count->sku_count);
        $sku->sku_product_id            = $product_id;
        $sku->sku_variant_description   = $request->input('variantDescription0');
        $sku->sku_selling_price         = $product->product_selling_price;
        $sku->sku_settlement_price      = $product->product_settlement_price;
        $sku->sku_discount              = $product->product_discount;
        $sku->sku_stock_left            = $request->input('stock0');
        $sku->save();

        for ($i=1; $i < $request->newSKUCount; $i++) { 
            if ((ucfirst(trim($request->input('variantDescription'.$i))) != "None") AND ($request->input('stock'.$i) >= 0)) {
                //insert sku
                $count->sku_count++;

                $sku = new StockKeepingUnit;
                $sku->id                        = "S-".($count->sku_count);
                $sku->sku_product_id            = $product_id;
                $sku->sku_variant_description   = $request->input('variantDescription'.$i);
                $sku->sku_selling_price         = $product->product_selling_price;
                $sku->sku_settlement_price      = $product->product_settlement_price;
                $sku->sku_discount              = $product->product_discount;
                $sku->sku_stock_left            = $request->input('stock'.$i);
                $sku->save();

            }
        }

        /*--- Save product images --- */
        for ($i=0; $i < sizeof($request->product_images); $i++) { 
                    
            $product_image = new ProductImage;
            $product_image->pi_product_id = $product_id;
            $product_image->pi_path = $product_id.rand(1000, 9999);

            $img = Image::make($request->product_images[$i]);

            //save original image
            $img->save('app/assets/img/products/original/'.$product_image->pi_path.'.jpg');

            //save main image
            $img->resize(600, 600);
            $img->insert('portal/images/watermark/stamp.png', 'center');
            $img->save('app/assets/img/products/main/'.$product_image->pi_path.'.jpg');

            //save thumbnail
            $img->resize(300, 300);
            $img->save('app/assets/img/products/thumbnails/'.$product_image->pi_path.'.jpg');

            //store image details
            $product_image->save();
        }


        /*--- Save product --- */
        $product->save();
        $count->save();

        /*--- log activity ---*/
        activity()
        ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Product Added';
        })
        ->log(Auth::guard('vendor')->user()->name." added product ".$product_id);
        return redirect()->back()->with("success_message", "Product ".$request->name." added successfully.");

        
    }
 
    public function showProduct($productSlug){
        if (is_null(Product::where('product_slug', $productSlug)->first())) {
            return redirect()->back()->with("error_message", "Product not found");
        }

        $product =  Product::
        where([
            ['product_slug', "=", $productSlug],
            ['product_vid', "=", Auth::guard('vendor')->user()->id]
        ])
        ->with('images', 'skus', 'vendor', 'state')
        ->first()
        ->toArray();

        /*--- Build SKU array ---*/
        $sku_array = [];
        for ($i=0; $i < sizeof($product["skus"]); $i++) { 
            $sku_array[$i] = $product["skus"][$i]["id"];
        }


        /*--- Stats ---*/
        $product["stats"]["wishlist"] = WishlistItem::
            where('wi_product_id', $product["id"])
            ->count();

        $product["stats"]["cart"] = CartItem::
        whereIn('ci_sku', $sku_array)
        ->count();

        $product["stats"]["purchases"] = OrderItem::
        whereIn('oi_sku', $sku_array)
        ->whereIn('oi_state', [2, 3, 4])
        ->count();

        /*--- Category Options ---*/
        $product["category_options"] = ProductCategory::orderBy('pc_description')->where('pc_level', 3)->get()->toArray();

        $vendor_id = Auth::guard('vendor')->user()->id;

        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;
        return view("portal.main.vendor.product")
            ->with("product", $product)
            ->with("vendor", $vendor);
    }
 
    public function processProduct(Request $request, $productSlug){
        switch ($request->product_action) {
            case 'update_details':
                /*--- Validate form data  ---*/
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'features' => 'required',
                    'category' => 'required',
                    'selling_price' => 'required',
                    'discount' => 'required',
                    'type' => 'required'
                ]);

                if ($validator->fails()) {
                    $messageType = "error_message";
                    $messageContent = "";

                    foreach ($validator->messages()->getMessages() as $field_name => $messages)
                    {
                        $messageContent .= $messages[0]." "; 
                    }

                    return redirect()->back()->with($messageType, $messageContent);
                }

                $product = Product::where([
                    ['product_slug', "=", $productSlug],
                    ['product_vid', '=', Auth::guard('vendor')->user()->id]
                ])->first();

                /*--- Validate Product Slug ---*/
                if(trim(strtolower($request->name)) != trim(strtolower($product->product_name))){
                    if((Product::where([
                        ['product_vid', '=', Auth::guard('vendor')->user()->id],
                        ['product_name', '=', $request->name],
                        ['id', '<>', $product->id]
                    ])->get()->count()) > 0){
                        $product_slug_count = Product::where([
                            ['product_vid', '=', Auth::guard('vendor')->user()->id],
                            ['product_name', '=', $request->name],
                            ['id', '<>', $product->id]
                        ])->get()->count();
                        $product_slug_count++;
                        $product_slug = str_slug($request->name)."-".$product_slug_count;
                    }else{
                        $product_slug = str_slug($request->name);
                    }
                }else{
                    $product_slug = $product->product_slug;
                }


                /*--- Update Details ---*/
                
                $product->product_name = ucwords(strtolower($request->name));
                $product->product_slug = $product_slug;
                $product->product_features = $request->features;
                $product->product_cid = $request->category;
                $product->product_selling_price = $request->selling_price;
                $product->product_discount = $request->discount;
                $product->product_description = $request->description;
                $product->product_tags = $request->tags;
                $product->product_type = $request->type;
                $product->save();

                /*--- Change state to pending approval ---*/
                Product::where([
                    ['product_slug', "=", $productSlug],
                    ['product_vid', '=', Auth::guard('vendor')->user()->id]
                ])->update([
                    'product_state' => 2
                ]);

                /*--- log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Details Updated';
                })
                ->log(Auth::guard('vendor')->user()->name." updated details of product ".$product->id);
                return redirect()->route("vendor.show.product", $product_slug)->with("success_message", "Product ".$request->name." details updated successfully.");
                break;

            case 'update_stock':
                $product = Product::where('product_slug', $productSlug)->first();
                /*--- update old stock ---*/
                for ($i=0; $i < $request->skuCount; $i++) { 
                    $sku = StockKeepingUnit::where('id', $request->input('sku'.$i))->first();
                    $sku->sku_stock_left = $request->input('stock'.$i);
                    $sku->save();
                }

                /*--- add new stock (if any) ---*/
                if ($request->newSKUCount > $request->skuCount) {
                    //select product
                    $product = Product::where('product_slug', $productSlug)->first();
                    for ($i=$request->skuCount; $i < $request->newSKUCount; $i++) { 
                        if ((ucfirst(trim($request->input('variantDescription'.$i))) != "None") AND ($request->input('stock'.$i) >= 0)) {
                            //insert sku
                            $count = Count::first();
                            $count->sku_count++;

                            $sku = new StockKeepingUnit;
                            $sku->id                        = "S-".($count->sku_count);
                            $sku->sku_product_id            = $product->id;
                            $sku->sku_variant_description   = $request->input('variantDescription'.$i);
                            $sku->sku_selling_price         = $product->product_selling_price;
                            $sku->sku_settlement_price      = $product->product_settlement_price;
                            $sku->sku_discount              = $product->product_discount;
                            $sku->sku_stock_left            = $request->input('stock'.$i);
                            $sku->save();

                            //update count
                            $count->save();
                        }
                    }

                }

                /*--- Change state to pending approval ---*/
                Product::where([
                    ['product_slug', "=", $productSlug],
                    ['product_vid', '=', Auth::guard('vendor')->user()->id]
                ])->update([
                    'product_state' => 2
                ]);

                /*--- log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Stock Updated';
                })
                ->log(Auth::guard('vendor')->user()->name." updated stock of product ".$product->id);
                return redirect()->back()->with("success_message", "Product ".$product->name." stock updated successfully.");
                break;

            case 'delete_image':
                $product = Product::where('product_slug', $productSlug)->first();
                //select image
                $image = ProductImage::where('id', $request->image_id)->first();

                //delete files
                $main_image_path = "app/assets/img/products/main/";
                $thumbnail_image_path = "app/assets/img/products/thumbnails/";
                $original_image_path = "app/assets/img/products/original/";

                File::delete($main_image_path.$image->pi_path.'.jpg');
                File::delete($thumbnail_image_path.$image->pi_path.'.jpg');
                File::delete($original_image_path.$image->pi_path.'.jpg');

                //delete image
                $image->delete();


                /*--- log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Image Deleted';
                })
                ->log(Auth::guard('vendor')->user()->name." deleted image ".$request->image_id." of product ".$product->id);
                return redirect()->back()->with("success_message", "Image ".$request->image_id." deleted successfully.");
                break;
            
            case 'add_images':
                $product = Product::where('product_slug', $productSlug)->first();
                //validate images
                for ($i=0; $i < sizeof($request->product_images); $i++) { 
                    if($request->product_images[$i]->getClientOriginalExtension() != "jpg"){
                        return back()->with("error_message", "Images must be of type jpg");
                    }

                    list($width, $height) = getimagesize($request->product_images[$i]);
                    if ($width != $height or $height < 600) {
                        return back()->with("error_message", "Images must be minimum height 600px with aspect ratio of 1");
                    }

                    if(filesize($request->product_images[$i]) > 5000000){
                        return back()->with("error_message", "One or more images exceed the allowed size for upload.");
                    }
                }

                //process images
                for ($i=0; $i < sizeof($request->product_images); $i++) { 
                    
                    $product_image = new ProductImage;
                    $product_image->pi_product_id = $product->id;
                    $product_image->pi_path = $product->id.rand(1000, 9999);

                    $img = Image::make($request->product_images[$i]);

                    //save original image
                    $img->save('app/assets/img/products/original/'.$product_image->pi_path.'.jpg');

                    //save main image
                    $img->resize(600, 600);
                    $img->insert('portal/images/watermark/stamp.png', 'center');
                    $img->save('app/assets/img/products/main/'.$product_image->pi_path.'.jpg');

                    //save thumbnail
                    $img->resize(300, 300);
                    $img->save('app/assets/img/products/thumbnails/'.$product_image->pi_path.'.jpg');

                    //store image details
                    $product_image->save();
                }

                /*--- Change state to pending approval ---*/
                Product::where([
                    ['product_slug', "=", $productSlug],
                    ['product_vid', '=', Auth::guard('vendor')->user()->id]
                ])->update([
                    'product_state' => 2
                ]);

                /*--- log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Product Image(s) Uploaded';
                })
                ->log(Auth::guard('vendor')->user()->name." uploaded images for product ".$product->id);
                return redirect()->back()->with("success_message", "Upload Successful.");
                break;

            default:
                return redirect()->back()->with("error_message", "Something went wrong. Please try again.");
                break;
        }
    }

    public function showOrders(){
        $orders =  DB::select(
            "SELECT distinct order_items.id, order_items.oi_name, order_items.oi_quantity, products.id as product_id, product_name, product_slug, oi_sku, order_items.updated_at from order_items, stock_keeping_units, products, product_images where oi_state = '2' and order_items.oi_sku = stock_keeping_units.id and stock_keeping_units.sku_product_id = products.id and products.product_vid = :vendor_id order by order_items.oi_name",
            ["vendor_id" => Auth::guard('vendor')->user()->id]
        );

        for ($i=0; $i < sizeof($orders); $i++) { 
            $orders[$i]->image = ProductImage::where('pi_product_id', $orders[$i]->product_id)->first()->toArray();
        }

        $vendor_id = Auth::guard('vendor')->user()->id;

        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;

        return view("portal.main.vendor.orders")
        ->with("orders", $orders)
            ->with("vendor", $vendor);
    }

    public function showConversations(){

        $conversations = Conversation::where([
            ['conv_key', 'LIKE', '%'.Auth::guard('vendor')->user()->id.'%']
        ])->get()->toArray();

        $vendor_id = Auth::guard('vendor')->user()->id;

        for ($i=0; $i < sizeof($conversations); $i++) { 
            $conversation_key = explode("|", $conversations[$i]["conv_key"]);

            $conversations[$i]["customer"] = Customer::where([
                ['id', "=", trim($conversation_key[0])]
            ])->get()->toArray();

            //getting unread messages
            $unread_messages = DB::select(
                "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key = '".$conversations[$i]['conv_key']."'"
            );
            $conversations[$i]['unread_messages'] = $unread_messages[0]->unread;
            
        }


        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;
        
        return view('portal.main.vendor.conversations')
            ->with("conversations", $conversations)
            ->with("vendor", $vendor);
    }

    public function showConversation($conversationID){
        /* Get conversation details */
        $conversation["record"] = Conversation::where([
            ['id', '=', $conversationID],
            ['conv_key', 'LIKE', '%'.Auth::guard('vendor')->user()->id.'%']
        ])->first()->toArray();
        $conversation["participant_ids"] = explode("|", $conversation["record"]["conv_key"]);
        $conversation["customer"] = Customer::where('id', $conversation["participant_ids"][0])->first()->toArray();
        /* Get conversation messages */
        $conversation["messages"] = Message::where('message_conversation_id', $conversationID)->get()->toArray();
        
        //update read
        Message::
        where([
            ['message_conversation_id', "=", $conversation["record"]["id"]],
            ['message_sender', "<>", Auth::guard('vendor')->user()->id],
            ['message_read', "NOT LIKE", "%".Auth::guard('vendor')->user()->id."%"]
        ])
        ->update([
            'message_read' => DB::raw('CONCAT(message_read, "'.'|'.Auth::guard('vendor')->user()->id.'")')
        ]);

        $vendor_id = Auth::guard('vendor')->user()->id;

        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;

        return view("portal.main.vendor.view-conversation")
            ->with("conversation", $conversation)
            ->with("vendor", $vendor);
    }

    public function processConversation(Request $request, $conversationID){
         
        $message = new Message;
        $message->message_sender = Auth::guard('vendor')->user()->id;
        $message->message_content = $request->message;
        $message->message_conversation_id = $conversationID;
        $message->message_timestamp = date("Y-m-d H:i:s");
        $message->message_read = "Init|";
        $message->save();

        /*--- flag message where necessary ---*/
        $flag_keywords =  ['call', 'meet', 'talk', 'whatsapp', 'facebook', 'instagram', 'phone', 'ring', 'message', 'reduce', 'reduction', 'discount', 'twitter', 'email'];
        if (is_numeric($request->message_content)){
            $flag = 1;
        }

        for ($i=0; $i < sizeof($flag_keywords); $i++) { 
            if (strpos(strtolower($request->message), $flag_keywords[$i]) !== false) {
                $flag = 1;
                break;
            }
        }

        if(isset($flag)){
            //insert flag
            $message_flag = new MessageFlag;
            $message_flag->mf_mid = $message->id;
            $message_flag->save();

            /*--- log activity ---*/
            activity()
            ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Message Flagged!';
            })
            ->log("Flag raised on message sent by ".Auth::guard('vendor')->user()->name.' ['.$request->message.']');
        }

        /*--- log activity ---*/
        activity()
        ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
        ->tap(function(Activity $activity) {
            $activity->subject_type = 'System';
            $activity->subject_id = '0';
            $activity->log_name = 'Management Message Sent';
        })
        ->log(Auth::guard('vendor')->user()->name." sent a message [ $request->message ] in conversation ID $conversationID");

        return redirect()->back()->with("success_message", "Message sent successfully.");
    }

    public function showTermsOfUse(){

        $vendor_id = Auth::guard('vendor')->user()->id;
        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;
 
        return view("portal.main.vendor.terms-of-use")
            ->with("vendor", $vendor);
    }

    public function showSubscription(){
        $vendor_id = Auth::guard('vendor')->user()->id;
        $unread_messages = DB::select(
            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$vendor_id' AND (message_read NOT LIKE '%$vendor_id%') AND conv_key LIKE '%$vendor_id%'"
        );

        $vendor['unread_messages'] = $unread_messages[0]->unread;


        $subscription["options"] = VSPackage::all();
        if(!is_null(VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->with('package')->first())){
            $subscription["active"] = VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->with('package')->first();
            $subscription["active"]->product_count = Product::where('product_vid', Auth::guard('vendor')->user()->id)->whereIn('product_state', [1, 2, 3, 5])->get()->count();
        }
        

        return view("portal.main.vendor.subscription")
            ->with("subscription", $subscription)
            ->with("vendor", $vendor);
    }

    public function processSubscription(Request $request){
        /*--- Validate Submissions ---*/
        if (!in_array($request->package, [1, 2, 3])) {
            return redirect()->back()->with("error_message", "Subscription package not found.");
        }elseif($request->duration < 1){
            return redirect()->back()->with("error_message", "Duration must be 1 or more months.");
        }

        /*--- Check if selected package is free or paid ---*/
        if ($request->package == 1) {
            //free (Gold)
            if(is_null(VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first())){
                //subscribe
                $vendor_subscription = New VendorSubscription;
                $vendor_subscription->vs_vendor_id = Auth::guard('vendor')->user()->id;
                $vendor_subscription->vs_vsp_id = $request->package;
                $vendor_subscription->vs_days_left = $request->duration * 30;
                $vendor_subscription->save();

                /*--- Log activity ---*/
                activity()
                ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                ->tap(function(Activity $activity) {
                    $activity->subject_type = 'System';
                    $activity->subject_id = '0';
                    $activity->log_name = 'Subscription Change';
                })
                ->log(Auth::guard('vendor')->user()->name." subscribed to package ".$vendor_subscription->vs_vsp_id." for ".($request->duration * 30)." days");

                return redirect()->back()->with('success_message', 'Subscription successful and is valid for '.($request->duration * 30).' days');

            }else{
                if(VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first()->vs_vsp_id == $request->package){
                    //extend
                    $vendor_subscription = VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first();
                    $vendor_subscription->vs_days_left = $vendor_subscription->vs_days_left + ($request->duration * 30);

                    /*--- Log activity ---*/
                    activity()
                    ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Subscription Extension';
                    })
                    ->log(Auth::guard('vendor')->user()->name." extended subscription to ".$vendor_subscription->vs_days_left." days");

                    $vendor_subscription->save();
                    return redirect()->back()->with('success_message', 'Subscription extended by '.($request->duration * 30).' days');
                }else{
                    //update
                    $vendor_subscription = VendorSubscription::where('vs_vendor_id', Auth::guard('vendor')->user()->id)->first();
                    $vendor_subscription->vs_vsp_id = $request->package;
                    $vendor_subscription->vs_days_left = $request->duration * 30;

                    /*--- Log activity ---*/
                    activity()
                    ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
                    ->tap(function(Activity $activity) {
                        $activity->subject_type = 'System';
                        $activity->subject_id = '0';
                        $activity->log_name = 'Subscription Change';
                    })
                    ->log(Auth::guard('vendor')->user()->name." changed subscription to package ".$vendor_subscription->vs_vsp_id." for ".($request->duration * 30)." days");

                    $vendor_subscription->save();
                    return redirect()->back()->with('success_message', 'Subscription successful and is valid for '.($request->duration * 30).' days');

                }
            }
        }else{
            //check if package exists
            if (is_null(VSPackage::
                where('id', $request->package)
                ->first())) {

                return redirect()->back()->with("error_message", "Package not found");
                
            }

            $vs_package = VSPackage::
            where('id', $request->package)
            ->first()
            ->toArray();
            //generate slydepay order

            /*--- log activity ---*/
            activity()
            ->causedBy(Vendor::where('id', Auth::guard('vendor')->user()->id)->get()->first())
            ->tap(function(Activity $activity) {
                $activity->subject_type = 'System';
                $activity->subject_id = '0';
                $activity->log_name = 'Subscription Purchase Checkout';
            })
            ->log(Auth::guard('vendor')->user()->name.' checked out a subscription package purchase ['.$vs_package["vs_package_description"].'] for '.$request->duration." ".str_plural('month', $request->duration));

            /*--- generate order externally (Slydepay) ---*/
            $slydepay = new Slydepay("ceo@solutekworld.com", "1466854163614");

        
            $order_items = new SlydepayOrderItems([
                new SlydepayOrderItem("Vendor Subscription", $vs_package["vs_package_description"], $vs_package["vs_package_cost"], $request->duration),
            ]);

            $shipping_cost = 0; 
            $tax = 0;

            // Create the Order object for this transaction. 
            $slydepay_order = SlydepayOrder::createWithId(
                $order_items,
                rand(1000, 9999), 
                $shipping_cost,
                $tax,
                "Vendor Subscription on Solushop Ghana",
                "No comment"
            );

            try{
                $response = $slydepay->processPaymentOrder($slydepay_order);
                $redirect_url = $response->redirectUrl();
                $redirect_url_break = explode("=", $redirect_url);

                //generate subscription up payment
                $VSPayment = new VSPayment;
                $VSPayment->vs_payment_vendor_id    = Auth::guard('vendor')->user()->id;
                $VSPayment->vs_payment_vsp_id       = $request->package;
                $VSPayment->vs_payment_vspq         = $request->duration;
                $VSPayment->vs_payment_amount       = $vs_package["vs_package_cost"] * $request->duration;
                $VSPayment->vs_payment_token        = $redirect_url_break[1];
                $VSPayment->vs_payment_type         = "New";
                $VSPayment->vs_payment_state        = "UNPAID";
                $VSPayment->save();

                return redirect($redirect_url);
            } catch (Slydepay\Exception\ProcessPayment $e) {
                echo $e->getMessage();
            }
        }
    }

}
