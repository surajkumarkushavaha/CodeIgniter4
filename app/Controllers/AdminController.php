<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\OrderModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $productModel;
    protected $orderModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        
        helper(['form', 'url']);
    }
    
    public function login()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required',
                'password' => 'required'
            ];
            
            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');
                
                $user = $this->userModel->where('username', $username)->first();
                
                if ($user && $this->userModel->verifyPassword($password, $user['password']) && $user['is_admin']) {
                    $session = session();
                    $session->set([
                        'admin_id' => $user['id'],
                        'admin_username' => $user['username'],
                        'is_admin' => true,
                        'logged_in' => true
                    ]);
                    
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->back()->with('error', 'Invalid credentials');
                }
            }
        }
        
        return view('admin/login');
    }
    
    public function dashboard()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }
        
        $data = [
            'total_products' => $this->productModel->countAll(),
            'total_orders' => $this->orderModel->countAll()
        ];
        
        return view('admin/dashboard', $data);
    }
    
    public function products()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }
        
        $data['products'] = $this->productModel->findAll();
        return view('admin/products', $data);
    }
    
    public function orders()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }
        
        $data['orders'] = $this->orderModel->getOrdersWithItems();
        return view('admin/orders', $data);
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}