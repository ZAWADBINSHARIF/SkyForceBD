<?php


namespace App\Traits;

use App\Enums\OrderStatus;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\BulkSMSBDService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @property array $post_data
 * @property float $amount
 * @property string $transaction_id
 * 
 * Customer
 * @property string $customerName
 * @property ?string $fullAddress
 * @property string $email
 * @property string $phoneNumber
 * @property string $additionalNote
 * 
 * Product
 * @property string $product_name
 * @property string $product_category
 * @property string $product_profile
 * 
 * Shipping
 * @property bool $shipping_method
 * @property string $ship_name
 * @property string $ship_city
 * @property string $ship_address
 * @property string $ship_country
 * 
 * 
 * @property string $products
 */
trait WithSslCommerz
{
    # Here you have to receive all the order data to initate the payment.
    # Let's say, your oder transaction informations are saving in a table called "orders"
    # In "orders" table, order unique identity is "transaction_id". "status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

    public function setPostData()
    {
        $post_data = array();

        $post_data['total_amount'] = $this->amount; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid("SSL-"); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $this->customerName;
        $post_data['cus_email'] = $this->email;
        $post_data['cus_add1'] = $this->fullAddress;
        $post_data['cus_city'] = $post_data['cus_add1'];
        $post_data['cus_phone'] = $this->phoneNumber;

        # PRODUCT INFORMATION
        $post_data['product_name'] = $this->product_name;
        $post_data['product_category'] = $this->product_category;
        $post_data['product_profile'] = $this->product_profile;

        # SHIPMENT INFORMATION

        if ($this->shipping_method) {
            $post_data['shipping_method'] = 'YES';
            $post_data['ship_name'] = $this->ship_name;
            $post_data['ship_add1'] = $this->ship_address;
            $post_data['ship_city'] = $this->ship_city;
            $post_data['ship_country'] = $this->ship_country;
        } else {
            $post_data['shipping_method'] = 'NO';
        }

        $this->post_data = $post_data;
    }

    public function paymentForAdvance(string $order_id)
    {
        Transaction::create([
            'order_id' => $order_id,
            'transaction_number' => $this->post_data['tran_id'],
            'payment_amount' => $this->post_data['total_amount']
        ]);

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($this->post_data, 'hosted');

        if (!is_array($payment_options)) {
            $payment_options = array();

            Log::error('SSLCOMMERZ payment failed', ['response' => $payment_options]);

            Log::error('SSLCOMMERZ payment failed', [
                'response' => $payment_options,
                'user_id' => Auth::guard('customer')->user()->id ?? null,
                'post_data' => $this->post_data,
            ]);
        }
    }

    public function paymentForOrderRequest()
    {
        $createdOrder = Order::create([
            'customer_id' => Auth::guard('customer')->user()->id ?? null,
            'products' => $this->products,
            'customer_name' => $this->customerName,
            'customer_phone' => $this->phoneNumber,
            'customer_remark' => $this->additionalNote
        ]);

        Transaction::create([
            'order_id' => $createdOrder->id,
            'transaction_number' => $this->post_data['tran_id'],
            'payment_amount' => $this->post_data['total_amount']
        ]);

        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($this->post_data, 'hosted');

        if (!is_array($payment_options)) {
            $payment_options = array();

            $sms = app(BulkSMSBDService::class);

            $sms->send([$createdOrder->customer_phone], "Thank you for requesting order on our website. Order id: #{$createdOrder->order_number_short_code}. Our agent will call you soon.");

            Log::error('SSLCOMMERZ payment failed', ['response' => $payment_options]);

            Log::error('SSLCOMMERZ payment failed', [
                'response' => $payment_options,
                'user_id' => Auth::guard('customer')->user()->id ?? null,
                'post_data' => $this->post_data,
            ]);
        }
    }
}
