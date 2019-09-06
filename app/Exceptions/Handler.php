<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use \Mobile_Detect;
use Auth;
use App\Customer;
use App\ProductCategory;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if ($this->isHttpException($exception)){
            $statusCode = $exception->getStatusCode();
            switch ($statusCode) 
            {
                case '404': 
                    /*---retrieving customer information if logged in---*/
                    if (Auth::check()) {
                        $customer_information_object = Customer::
                            where('id', Auth::user()->id)
                            ->with('milk', 'chocolate', 'cart', 'wishlist')
                            ->first()
                            ->toArray();

                        //calculate account balance
                        $customer_information['wallet_balance'] = round(($customer_information_object['milk']['milk_value'] * $customer_information_object['milkshake']) - $customer_information_object['chocolate']['chocolate_value'], 2);

                        //get cart count
                        $customer_information['cart_count'] = sizeof($customer_information_object['cart']);

                        //get wishlist count
                        $customer_information['wishlist_count'] = sizeof($customer_information_object['wishlist']);

                        //unread messages
                        $customer_id = Auth::user()->id;
                        $unread_messages = DB::select(
                            "SELECT count(*) AS unread FROM messages, conversations WHERE conversations.id = messages.message_conversation_id AND message_sender <> '$customer_id' AND (message_read NOT LIKE '%$customer_id%') AND conv_key LIKE '%$customer_id%'"
                        );

                        $customer_information['unread_messages'] = $unread_messages[0]->unread;

                    }else{
                        $customer_information['wallet_balance'] = 0;
                        $customer_information['cart_count'] = 0;
                        $customer_information['wishlist_count'] = 0;
                    }

                    $detect = new Mobile_Detect;
                    if( $detect->isMobile() && !$detect->isTablet() ){
                        return response()
                            ->view('mobile.main.general.404', [
                                'customer_information' => $customer_information
                            ], 404);
                    }else{
                        /*---selecting search bar categories (level 2 categories)---*/
                        $search_bar_pc_options = ProductCategory::
                        where('pc_level', 2) 
                        ->orderBy('pc_description')   
                        ->get(['id', 'pc_description', 'pc_slug']);
                        return response()
                                ->view('app.main.general.404', [
                                    'search_bar_pc_options' => $search_bar_pc_options,
                                    'customer_information' => $customer_information
                                ], 404);
                    };


                    break;
            }
        } 


    
        return parent::render($request, $exception);
    }


    protected function unauthenticated($request, AuthenticationException $exception){
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $guard = array_get($exception->guards(), 0);

        switch ($guard) {
            case 'manager':
                $login = 'manager.login';
                break;

            case 'vendor':
                $login = 'vendor.login';
                break;

            case 'sales-associate':
                $login = 'sales-associate.login';
                break;

            case 'delivery-partner':
                $login = 'delivery-partner.login';
                break;
            
            default:
                $login = 'login';
                break;
        }

        return redirect()->guest(route($login));
    }
}
