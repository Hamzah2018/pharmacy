<?php

namespace App\Http\Controllers\pharmacy;


use App\Enum\OrderEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\UserOrderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{
   // show orders

  public function index()
  {

  $pharmacy_id = Auth::user()->id;
  $orders=Order::select('user_id','pharmacy_id','status','created_at')->where('pharmacy_id', $pharmacy_id)->with(['user:id,name,avatar'])->get();

   return view('pharmacy.dashboard.orders', compact('orders'));
  // return response( $request);


   }

    public function getAll()
    {
         $orders = Auth::user()->pharmacyOrders()->get();
         return view('pharmacy.dashboard.orders', compact('orders'));
    }

    public function orderRefusal($id)
    {
        $order = Order::find($id);

        if ($order)
        {
            $order->update(['status' => OrderEnum::REFUSAL_ORDER]);

            // send and save notification in DB
            $user  = User::find($order->user_id);
            $data  = [
              'pharmacy' => Auth::user(),
              'order'    => $order,
              'message'  => 'عذراً لا يتوفر لدينا طلبك'
            ];
            Notification::send($user, new UserOrderNotification($data));

            return back()->with('status', 'لقد تم رفض الطلب');
        }

        return back()->with('status', 'هُناك خطأ، يُرجى التأكد من صحة رقم الطلب');
    }
}
