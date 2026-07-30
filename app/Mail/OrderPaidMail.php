<?php
namespace App\Mail;

use App\Models\Orders;
use App\Models\ProductOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $items;

    public function __construct(Orders $order)
    {
        $this->order = $order;
        // Fetch items along with the product details using your defined relationship
        $this->items = ProductOrder::with('product')
            ->where('order_id', $order->id)
            ->get();
    }

   public function build()
{
    return $this->subject('Payment Confirmed - Order #' . $this->order->id)
        ->view('emails.order-paid')
        ->with([
            'order' => $this->order,
            'items' => $this->items,
        ]);
}
}