<?php namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'price', 'status'];
    protected $useTimestamps = true;
    
    public function getProductsWithImages()
    {
        $builder = $this->db->table('products p');
        $builder->select('p.*, GROUP_CONCAT(pi.image_path) as images');
        $builder->join('product_images pi', 'p.id = pi.product_id', 'left');
        $builder->where('p.status', 'active');
        $builder->groupBy('p.id');
        
        return $builder->get()->getResultArray();
    }
    
    public function getProductWithImages($id)
    {
        $builder = $this->db->table('products p');
        $builder->select('p.*, pi.image_path, pi.is_primary');
        $builder->join('product_images pi', 'p.id = pi.product_id', 'left');
        $builder->where('p.id', $id);
        
        return $builder->get()->getResultArray();
    }
}