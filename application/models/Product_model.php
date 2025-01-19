<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {
    
    private $table = 'produk';
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_all() {
        return $this->db->get($this->table)->result();
    }
    
    public function get_by_status($status_id) {
        return $this->db->where('status_id', $status_id)->get($this->table)->result();
    }
    
    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }
    
    public function update($id, $data) {
        return $this->db->where('id_produk', $id)->update($this->table, $data);
    }
    
    public function delete($id) {
        return $this->db->where('id_produk', $id)->delete($this->table);
    }
}