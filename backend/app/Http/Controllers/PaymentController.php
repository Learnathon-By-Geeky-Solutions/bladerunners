<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Library\SslCommerz\SslCommerzNotification;

class PaymentController extends Controller
{
    /** Show add‑balance form */
    public function showAddBalanceForm($user)
    {
        $user = User::findOrFail($user);
        return view('add_balance', ['user_id' => $user->id]);
    }

    /** Process form and start SSLCOMMERZ */
    public function process(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:sslcommerz,stripe',
            'user_id'        => 'required|uuid|exists:users,id',
        ]);

        // Authenticate by UUID
        $user = User::findOrFail($request->user_id);
        Auth::login($user);

        $amount = $request->amount;
        $method = $request->payment_method;
        $tranId = uniqid();
        $currency = 'BDT';

        // Record Pending transaction
        Transaction::create([
            'user_id'        => $user->id,
            'payment_method' => $method,
            'transaction_id' => $tranId,
            'amount'         => $amount,
            'currency'       => $currency,
            'status'         => 'Pending',
        ]);

        if ($method === 'sslcommerz') {
            $base = rtrim(config('app.url'), '/');

            $post_data = [
                'total_amount'     => $amount,
                'currency'         => $currency,
                'tran_id'          => $tranId,
                'cus_name'         => $user->name,
                'cus_email'        => $user->email,
                'cus_add1'         => $user->profile_picture ?? '',
                'cus_phone'        => $user->payment_phone ?? '017xxxxxxxx',
                'shipping_method'  => 'NO',
                'product_name'     => 'Account Top‑Up',
                'product_category' => 'Wallet',
                'product_profile'  => 'general',
                // Explicit, absolute callbacks
                'success_url'      => $base . route('payment.ssl-success', [], false),
                'fail_url'         => $base . route('payment.ssl-fail',    [], false),
                'cancel_url'       => $base . route('payment.ssl-cancel',  [], false),
                'ipn_url'          => $base . route('payment.ssl-ipn',     [], false),
            ];

            $sslc = new SslCommerzNotification();
            return $sslc->makePayment($post_data, 'hosted');
        }

        abort(501, 'Stripe not implemented.');
    }

    /** Success callback */
    public function sslSuccess(Request $request)
    {
        $order = Transaction::where('transaction_id', $request->tran_id)
                            ->where('status','Pending')
                            ->firstOrFail();

        $sslc  = new SslCommerzNotification();
        $valid = $sslc->orderValidate(
            $request->all(), 
            $request->tran_id, 
            $request->amount, 
            $request->currency
        );

        if (!$valid) {
            return view('payment.failure');
        }

        $order->update([
            'status'   => 'Completed',
            'metadata' => $request->all(),
        ]);

        $order->user->increment('balance', $order->amount);

        return view('payment.success', [
            'amount'   => $order->amount,
            'currency' => $order->currency,
        ]);
    }

    /** Failure & Cancel are identical presentation */
    public function sslFail(Request $request)
    {
        Transaction::where('transaction_id',$request->tran_id)
                   ->where('status','Pending')
                   ->update(['status'=>'Failed']);
        return view('payment.failure');
    }

    public function sslCancel(Request $request)
    {
        Transaction::where('transaction_id',$request->tran_id)
                   ->where('status','Pending')
                   ->update(['status'=>'Canceled']);
        return view('payment.failure');
    }

    /** IPN (server‑to‑server) */
    public function sslIpn(Request $request)
    {
        if (!$request->filled('tran_id')) {
            echo "IPN: Missing tran_id"; return;
        }

        $order = Transaction::where('transaction_id',$request->tran_id)
                            ->where('status','Pending')
                            ->first();
        if (!$order) {
            echo "IPN: Invalid or duplicate"; return;
        }

        $sslc  = new SslCommerzNotification();
        $valid = $sslc->orderValidate(
            $request->all(), 
            $request->tran_id, 
            $order->amount, 
            $order->currency
        );
        if ($valid) {
            $order->update(['status'=>'Completed','metadata'=>$request->all()]);
            $order->user->increment('balance', $order->amount);
            echo "IPN: Completed"; return;
        }

        echo "IPN: Validation failed";
    }
}
