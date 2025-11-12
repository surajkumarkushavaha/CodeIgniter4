<?php namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CartModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

class Checkout extends ResourceController
{
    protected $format = 'json';
    
    protected $cartModel;
    protected $orderModel;
    protected $productModel;
    
    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }
    
    // POST /api/checkout - Process checkout
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            $userId = 1; // Hardcoded as per requirement
            
            // Get cart items
            $cartItems = $this->cartModel->getCartWithProducts($userId);
            
            if (empty($cartItems)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 400);
            }
            
            // Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }
            
            // Create order
            $orderId = $this->orderModel->insert([
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);
            
            // Add order items
            $orderItemModel = new \App\Models\OrderItemModel();
            foreach ($cartItems as $item) {
                $orderItemModel->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }
            
            // Process payment (simplified)
            $paymentResult = $this->processPayment($data, $total);
            
            if ($paymentResult['success']) {
                // Update order status
                $this->orderModel->update($orderId, [
                    'payment_status' => 'paid',
                    'payment_method' => $data['payment_method'] ?? 'stripe',
                    'transaction_id' => $paymentResult['transaction_id'],
                    'status' => 'processing'
                ]);
                
                // Clear cart
                $this->cartModel->where('user_id', $userId)->delete();
                
                return $this->respond([
                    'status' => 'success',
                    'message' => 'Order placed successfully',
                    'data' => [
                        'order_id' => $orderId,
                        'transaction_id' => $paymentResult['transaction_id'],
                        'total' => $total
                    ]
                ], 201);
            } else {
                // Payment failed
                $this->orderModel->update($orderId, [
                    'payment_status' => 'failed'
                ]);
                
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Payment failed: ' . $paymentResult['message']
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Checkout failed'
            ], 500);
        }
    }
    
    private function processPayment($paymentData, $amount)
    {
        // Simplified payment processing
        // In real scenario, integrate with Stripe, PayPal, etc.
        
        // Mock payment processing - always successful for demo
        return [
            'success' => true,
            'transaction_id' => 'txn_' . uniqid(),
            'message' => 'Payment processed successfully'
        ];
        
        // For real Stripe integration, you would use:
        /*
        \Stripe\Stripe::setApiKey('your_secret_key');
        
        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => 'usd',
                'payment_method' => $paymentData['payment_method_id'],
                'confirm' => true,
                'return_url' => base_url('payment/success')
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        */
    }
}