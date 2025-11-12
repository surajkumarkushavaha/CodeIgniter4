<?php namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'cart';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'product_id', 'quantity'];
    protected $useTimestamps = true;
    
    public function getCartWithProducts($userId = 1)
    {
        $builder = $this->db->table('cart c');
        $builder->select('c.*, p.name, p.price, p.description, pi.image_path');
        $builder->join('products p', 'c.product_id = p.id');
        $builder->join('product_images pi', 'p.id = pi.product_id AND pi.is_primary = 1', 'left');
        $builder->where('c.user_id', $userId);
        $builder->where('p.status', 'active');
        
        return $builder->get()->getResultArray();
    }
    
    public function getCartTotal($userId = 1)
    {
        $builder = $this->db->table('cart c');
        $builder->select('SUM(c.quantity * p.price) as total');
        $builder->join('products p', 'c.product_id = p.id');
        $builder->where('c.user_id', $userId);
        $builder->where('p.status', 'active');
        
        $result = $builder->get()->getRowArray();
        return $result['total'] ?? 0;
    }
}