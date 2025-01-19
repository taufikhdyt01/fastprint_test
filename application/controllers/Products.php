<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->database();
    }

    private function process_initial_data($data) {
        // Extract unique categories
        $categories = array_unique(array_column($data, 'kategori'));
        foreach($categories as $category) {
            $this->db->insert('kategori', ['nama_kategori' => $category]);
        }
        
        // Insert statuses
        $statuses = [
            ['nama_status' => 'bisa dijual'],
            ['nama_status' => 'tidak bisa dijual']
        ];
        $this->db->insert_batch('status', $statuses);
        
        // Get category and status mappings
        $category_map = [];
        $query = $this->db->get('kategori');
        foreach($query->result() as $row) {
            $category_map[$row->nama_kategori] = $row->id_kategori;
        }
        
        $status_map = [];
        $query = $this->db->get('status');
        foreach($query->result() as $row) {
            $status_map[$row->nama_status] = $row->id_status;
        }
        
        // Process products
        foreach($data as $item) {
            $product_data = [
                'id_produk' => $item['id_produk'],
                'nama_produk' => $item['nama_produk'],
                'harga' => $item['harga'],
                'kategori_id' => $category_map[$item['kategori']],
                'status_id' => $status_map[$item['status']]
            ];
            $this->db->insert('produk', $product_data);
        }
    }

    public function save_api_data() {
        $url = "https://recruitment.fastprint.co.id/tes/api_tes_programmer";
        
        // Generate credentials using server time with leading zeros
        $date = sprintf("%02d", date('d'));  // 2 digit dengan leading zero
        $month = sprintf("%02d", date('n')); // 2 digit dengan leading zero
        $year = date('y');
        
        $username = "tesprogrammer190125C16";
        $password = "bisacoding-{$date}-{$month}-{$year}";
        $md5_password = md5($password);
        
        // Set headers
        $headers = array(
            "Username: {$username}",
            "Password: {$md5_password}"
        );
        
        // Set POST data
        $postData = array(
            'username' => $username,
            'password' => $md5_password
        );
        
        // Initialize CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        
        // Execute CURL
        $response = curl_exec($ch);
        
        // Get header size
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        // Separate header and body
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        curl_close($ch);

        // Parse JSON response
        $data = json_decode($body, true);
        
        if(isset($data['data']) && is_array($data['data'])) {
            try {
                $this->process_initial_data($data['data']);
                echo "<h3>Status:</h3>";
                echo "<pre>";
                echo "Username: " . $username . "\n";
                echo "Password (pre-MD5): " . $password . "\n";
                echo "MD5 Password: " . $md5_password . "\n";
                echo "\nData berhasil disimpan ke database\n";
                echo "\nJumlah data yang disimpan: " . count($data['data']) . " produk";
                echo "</pre>";
            } catch (Exception $e) {
                echo "Error saat menyimpan ke database: " . $e->getMessage();
            }
        } else {
            echo "<pre>";
            echo "Gagal memproses data. Response dari API:\n\n";
            echo "Headers:\n" . $header . "\n\n";
            echo "Body:\n" . $body;
            echo "</pre>";
        }
    }

    public function index() {
        echo "Halaman index - akan dibuat untuk menampilkan data";
    }
}