<?php namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'image_path', 'is_primary'];
    protected $useTimestamps = true;
}