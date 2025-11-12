<?php namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;
use App\Models\ProductImageModel;

class Products extends ResourceController
{
    protected $modelName = 'App\Models\ProductModel';
    protected $format = 'json';
    
    protected $productModel;
    protected $productImageModel;
    
    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productImageModel = new ProductImageModel();
    }
    
    // GET /api/products - Get all products with images
    public function index()
    {
        try {
            $products = $this->productModel->getProductsWithImages();
            
            $formattedProducts = array_map(function($product) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'description' => $product['description'],
                    'price' => (float)$product['price'],
                    'status' => $product['status'],
                    'images' => $product['images'] ? explode(',', $product['images']) : [],
                    'created_at' => $product['created_at']
                ];
            }, $products);
            
            return $this->respond([
                'status' => 'success',
                'data' => $formattedProducts
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }
    
    // GET /api/products/{id} - Get single product
    public function show($id = null)
    {
        try {
            $product = $this->productModel->getProductWithImages($id);
            
            if (!$product) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], 404);
            }
            
            $images = array_column($product, 'image_path');
            $primaryImage = array_filter($product, function($img) {
                return $img['is_primary'];
            });
            
            $formattedProduct = [
                'id' => $product[0]['id'],
                'name' => $product[0]['name'],
                'description' => $product[0]['description'],
                'price' => (float)$product[0]['price'],
                'status' => $product[0]['status'],
                'images' => array_values(array_filter($images)),
                'primary_image' => $primaryImage ? reset($primaryImage)['image_path'] : null,
                'created_at' => $product[0]['created_at']
            ];
            
            return $this->respond([
                'status' => 'success',
                'data' => $formattedProduct
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to fetch product'
            ], 500);
        }
    }
    
    // POST /api/products - Create product
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required',
                'price' => 'required|numeric'
            ]);
            
            if (!$validation->run($data)) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ], 400);
            }
            
            $productId = $this->productModel->insert([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'price' => $data['price'],
                'status' => 'active'
            ]);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => ['id' => $productId]
            ], 201);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to create product'
            ], 500);
        }
    }
    
    // PUT /api/products/{id} - Update product
    public function update($id = null)
    {
        try {
            $data = $this->request->getJSON(true);
            
            $product = $this->productModel->find($id);
            if (!$product) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], 404);
            }
            
            $this->productModel->update($id, $data);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to update product'
            ], 500);
        }
    }
    
    // DELETE /api/products/{id} - Delete product
    public function delete($id = null)
    {
        try {
            $product = $this->productModel->find($id);
            if (!$product) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Product not found'
                ], 404);
            }
            
            $this->productModel->delete($id);
            
            return $this->respond([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Failed to delete product'
            ], 500);
        }
    }
}