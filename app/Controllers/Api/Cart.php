<?php namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CartModel;
use App\Models\ProductModel;

class Cart extends ResourceController
{
    protected $modelName = 'App\Models\CartModel';
    protected $format = 'json';
    
    protected $cartModel;
    protected $productModel;
    
    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }
    
    // GET /api/cart - Get cart items
    public function index()
    {
        try {
            $userId = $this->request->getGet('user_id') ?? 1;
            $cartItems = $this->cartModel->getCartWithProducts($userId);
            $cartTotal = $this->cartModel->getCartTotal($userId);
            
            $formattedItems = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => (float)$item['price'],
                    'quantity' => (int)$item['quantity'],
                    'subtotal' => (float)($item['price'] * $item['quantity']),
                    'image' => $item['image_path']
                ];
            }, $cartItems);
            
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'items' => $formattedItems,
                    'total' => (float)$cartTotal,
                    'item_count' => count($cartItems)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to fetch cart items'
            ], 500);
        }
    }
    
    // POST /api/cart - Add to cart
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            $validation = \Config\Services::validation();
            $validation->setRules([
                'product_id' => 'required|numeric',
                'quantity' => 'required|numeric|greater_than[0]'
            ]);
            
            if (!$validation->run($data)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ], 400);
            }
            
            $product = $this->productModel->find($data['product_id']);
            if (!$product) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Check if item already in cart
            $existingItem = $this->cartModel->where([
                'user_id' => 1,
                'product_id' => $data['product_id']
            ])->first();
            
            if ($existingItem) {
                // Update quantity
                $newQuantity = $existingItem['quantity'] + $data['quantity'];
                $this->cartModel->update($existingItem['id'], [
                    'quantity' => $newQuantity
                ]);
                $cartId = $existingItem['id'];
            } else {
                // Add new item
                $cartId = $this->cartModel->insert([
                    'user_id' => 1,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity']
                ]);
            }
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Item added to cart successfully',
                'data' => ['id' => $cartId]
            ], 201);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to add item to cart'
            ], 500);
        }
    }
    
    // PUT /api/cart/{id} - Update cart item
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            $cartItem = $this->cartModel->find($id);
            if (!$cartItem) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Cart item not found'
                ], 404);
            }
            
            $validation = \Config\Services::validation();
            $validation->setRules([
                'quantity' => 'required|numeric|greater_than[0]'
            ]);
            
            if (!$validation->run($data)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ], 400);
            }
            
            $this->cartModel->update($id, [
                'quantity' => $data['quantity']
            ]);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Cart item updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to update cart item'
            ], 500);
        }
    }
    
    // DELETE /api/cart/{id} - Remove from cart
    public function delete($id = null)
    {
        try {
            $cartItem = $this->cartModel->find($id);
            if (!$cartItem) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Cart item not found'
                ], 404);
            }
            
            $this->cartModel->delete($id);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Item removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to remove item from cart'
            ], 500);
        }
    }
}