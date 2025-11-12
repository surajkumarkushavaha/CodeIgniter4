<?php namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'total_amount', 'status', 'payment_status', 'payment_method', 'transaction_id'];
    protected $useTimestamps = true;
    
    public function getOrdersWithItems()
    {
        $builder = $this->db->table('orders o');
        $builder->select('o.*, oi.product_id, oi.quantity, oi.price, p.name');
        $builder->join('order_items oi', 'o.id = oi.order_id');
        $builder->join('products p', 'oi.product_id = p.id');
        
        return $builder->get()->getResultArray();
    }
}