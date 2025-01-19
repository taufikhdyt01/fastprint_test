<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $url = "https://recruitment.fastprint.co.id/tes/api_tes_programmer";
        
        // Generate MD5 password
        $date = date('d'); 
        $month = date('n');
        $year = date('y');
        
        $password = "bisacoding-{$date}-{$month}-{$year}";
        $md5_password = md5($password);
        
        // Set headers
        $headers = array(
            'Username: tesprogrammer190125C15',
            'Password: ' . $md5_password
        );
        
        // Initialize CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true); // untuk mendapatkan headers response
        
        // Execute CURL
        $response = curl_exec($ch);
        
        // Get header size
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        // Separate header and body
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        curl_close($ch);
        
        // Debug output
        echo "<pre>";
        echo "Password yang di-generate: " . $password . "\n";
        echo "MD5 Password: " . $md5_password . "\n";
        echo "Response Headers:\n" . $header . "\n";
        echo "Response Body:\n" . $body;
        echo "</pre>";
    }
}
