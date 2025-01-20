<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model 
{
    const TABLE = 'produk';
    const CATEGORY_TABLE = 'kategori';
    const STATUS_TABLE = 'status';
    
    public function __construct() 
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_products_with_relations() 
    {
        return $this->db->select([
                self::TABLE.'.*', 
                self::CATEGORY_TABLE.'.nama_kategori', 
                self::STATUS_TABLE.'.nama_status'
            ])
            ->from(self::TABLE)
            ->join(self::CATEGORY_TABLE, self::CATEGORY_TABLE.'.id_kategori = '.self::TABLE.'.kategori_id')
            ->join(self::STATUS_TABLE, self::STATUS_TABLE.'.id_status = '.self::TABLE.'.status_id')
            ->get()
            ->result();
    }
    
    public function get_sellable_products() 
    {
        return $this->db->select([
                self::TABLE.'.*', 
                self::CATEGORY_TABLE.'.nama_kategori', 
                self::STATUS_TABLE.'.nama_status'
            ])
            ->from(self::TABLE)
            ->join(self::CATEGORY_TABLE, self::CATEGORY_TABLE.'.id_kategori = '.self::TABLE.'.kategori_id')
            ->join(self::STATUS_TABLE, self::STATUS_TABLE.'.id_status = '.self::TABLE.'.status_id')
            ->where(self::STATUS_TABLE.'.nama_status', 'bisa dijual')
            ->get()
            ->result();
    }
    
    public function get_product_detail($id) 
    {
        return $this->db->select([
                self::TABLE.'.*', 
                self::CATEGORY_TABLE.'.nama_kategori', 
                self::STATUS_TABLE.'.nama_status'
            ])
            ->from(self::TABLE)
            ->join(self::CATEGORY_TABLE, self::CATEGORY_TABLE.'.id_kategori = '.self::TABLE.'.kategori_id')
            ->join(self::STATUS_TABLE, self::STATUS_TABLE.'.id_status = '.self::TABLE.'.status_id')
            ->where(self::TABLE.'.id_produk', $id)
            ->get()
            ->row();
    }
    
    public function insert($data) 
    {
        return $this->db->insert(self::TABLE, $this->sanitize_data($data));
    }
    
    public function update($id, $data) 
    {
        return $this->db->where('id_produk', $id)
            ->update(self::TABLE, $this->sanitize_data($data));
    }
    
    public function delete($id) 
    {
        return $this->db->where('id_produk', $id)
            ->delete(self::TABLE);
    }

    public function get_categories()
    {
        return $this->db->get(self::CATEGORY_TABLE)->result();
    }

    public function get_statuses()
    {
        return $this->db->get(self::STATUS_TABLE)->result();
    }

    public function sync_categories($categories)
    {
        foreach ($categories as $category) {
            $exists = $this->db->where('nama_kategori', $category)
                ->get(self::CATEGORY_TABLE)
                ->row();
            
            if (!$exists) {
                $this->db->insert(self::CATEGORY_TABLE, ['nama_kategori' => $category]);
            }
        }
    }

    public function sync_statuses($statuses)
    {
        foreach ($statuses as $status) {
            $exists = $this->db->where('nama_status', $status)
                ->get(self::STATUS_TABLE)
                ->row();
            
            if (!$exists) {
                $this->db->insert(self::STATUS_TABLE, ['nama_status' => $status]);
            }
        }
    }

    public function get_category_map()
    {
        $map = [];
        $categories = $this->db->get(self::CATEGORY_TABLE)->result();
        
        foreach ($categories as $category) {
            $map[$category->nama_kategori] = $category->id_kategori;
        }
        
        return $map;
    }

    public function get_status_map()
    {
        $map = [];
        $statuses = $this->db->get(self::STATUS_TABLE)->result();
        
        foreach ($statuses as $status) {
            $map[$status->nama_status] = $status->id_status;
        }
        
        return $map;
    }

    private function sanitize_data($data)
    {
        // Memastikan data aman sebelum disimpan ke database
        return array_map('strip_tags', $data);
    }
}