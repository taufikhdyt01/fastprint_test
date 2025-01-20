<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
    private $api_url;
    private $password_format;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->config('api_credentials');
        $this->api_url = $this->config->item('api_url');
        $this->password_format = $this->config->item('api_password_format');
        $this->load->model('product_model');
        $this->load->database();
        $this->load->library(['form_validation', 'session']);
        $this->load->helper('url');
    }

    public function index()
    {
        $data['products'] = $this->product_model->get_products_with_relations();
        $this->load->view('products/index', $data);
    }

    public function sellable()
    {
        $data['products'] = $this->product_model->get_sellable_products();
        $this->load->view('products/index', $data);
    }

    public function add()
    {
        $data['categories'] = $this->db->get('kategori')->result();
        $data['statuses'] = $this->db->get('status')->result();
        $this->load->view('products/form', $data);
    }

    public function edit($id)
    {
        $data['product'] = $this->product_model->get_product_detail($id);
        
        if (!$data['product']) {
            show_404();
        }

        $data['categories'] = $this->db->get('kategori')->result();
        $data['statuses'] = $this->db->get('status')->result();
        $this->load->view('products/form', $data);
    }

    public function store()
    {
        if (!$this->validate_product_form()) {
            return $this->add();
        }

        $this->product_model->insert($this->get_product_data_from_post());
        $this->set_success_message('Produk berhasil ditambahkan');
        redirect('products');
    }

    public function update($id)
    {
        if (!$this->validate_product_form()) {
            return $this->edit($id);
        }

        $this->product_model->update($id, $this->get_product_data_from_post());
        $this->set_success_message('Produk berhasil diperbarui');
        redirect('products');
    }

    public function delete($id)
    {
        $this->product_model->delete($id);
        $this->set_success_message('Produk berhasil dihapus');
        redirect('products');
    }

    public function sync_api()
    {
        try {
            $api_response = $this->fetch_api_data();
            
            if ($this->is_api_error($api_response['data'])) {
                $this->set_error_message('Gagal sync data: ' . $api_response['data']['ket']);
                return redirect('products');
            }

            $result = $this->sync_products_data($api_response['data']['data']);
            $this->set_sync_success_message($result);
            
        } catch (Exception $e) {
            $this->set_error_message('Error: ' . $e->getMessage());
        }

        redirect('products');
    }

    private function fetch_api_data()
    {
        $username = $this->get_api_username();
        $password = $this->generate_api_password();

        return $this->make_api_request($username, $password);
    }

    private function get_api_username()
    {
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);

        preg_match('/x-credentials-username: ([^\n]+)/', $headers, $matches);
        $username = trim($matches[1]);

        log_message('debug', 'Username from API header: ' . $username);
        
        return preg_replace('/\s+\(.*\)/', '', $username);
    }

    private function generate_api_password()
{
    $currentDate = new DateTime();
    $password = sprintf(
        $this->password_format,
        $currentDate->format('d'),
        $currentDate->format('m'),
        $currentDate->format('y')
    );

    log_message('debug', 'Generated API password: ' . $password);

    return md5($password);
}

    private function make_api_request($username, $password)
    {
        $ch = curl_init($this->api_url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'username' => $username,
                'password' => $password
            ]),
            CURLOPT_HTTPHEADER => [
                "Username: {$username}",
                "Password: {$password}"
            ]
        ]);

        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        return [
            'header' => substr($response, 0, $header_size),
            'body' => substr($response, $header_size),
            'data' => json_decode(substr($response, $header_size), true)
        ];
    }

    private function sync_products_data($data)
    {
        $this->sync_categories($data);
        $this->sync_statuses();
        
        $category_map = $this->get_category_map();
        $status_map = $this->get_status_map();

        return $this->sync_products($data, $category_map, $status_map);
    }

    private function sync_categories($data)
    {
        $categories = array_unique(array_column($data, 'kategori'));
        foreach ($categories as $category) {
            $this->insert_if_not_exists('kategori', 'nama_kategori', $category);
        }
    }

    private function sync_statuses()
    {
        $statuses = ['bisa dijual', 'tidak bisa dijual'];
        foreach ($statuses as $status) {
            $this->insert_if_not_exists('status', 'nama_status', $status);
        }
    }

    private function insert_if_not_exists($table, $column, $value)
    {
        $exists = $this->db->where($column, $value)->get($table)->row();
        if (!$exists) {
            $this->db->insert($table, [$column => $value]);
        }
    }

    private function get_category_map()
    {
        return $this->get_id_name_map('kategori', 'id_kategori', 'nama_kategori');
    }

    private function get_status_map()
    {
        return $this->get_id_name_map('status', 'id_status', 'nama_status');
    }

    private function get_id_name_map($table, $id_field, $name_field)
    {
        $map = [];
        $query = $this->db->get($table);
        foreach ($query->result() as $row) {
            $map[$row->$name_field] = $row->$id_field;
        }
        return $map;
    }

    private function sync_products($data, $category_map, $status_map)
    {
        $stats = ['inserted' => 0, 'updated' => 0];

        foreach ($data as $item) {
            $product_data = [
                'nama_produk' => $item['nama_produk'],
                'harga' => $item['harga'],
                'kategori_id' => $category_map[$item['kategori']],
                'status_id' => $status_map[$item['status']]
            ];

            $exists = $this->db->where('id_produk', $item['id_produk'])->get('produk')->row();

            if ($exists) {
                $this->db->where('id_produk', $item['id_produk'])->update('produk', $product_data);
                $stats['updated']++;
            } else {
                $product_data['id_produk'] = $item['id_produk'];
                $this->db->insert('produk', $product_data);
                $stats['inserted']++;
            }
        }

        return $stats;
    }

    private function validate_product_form()
    {
        $this->form_validation->set_rules([
            [
                'field' => 'nama_produk',
                'label' => 'Nama Produk',
                'rules' => 'required'
            ],
            [
                'field' => 'harga',
                'label' => 'Harga',
                'rules' => 'required|numeric'
            ],
            [
                'field' => 'kategori_id',
                'label' => 'Kategori',
                'rules' => 'required'
            ],
            [
                'field' => 'status_id',
                'label' => 'Status',
                'rules' => 'required'
            ]
        ]);

        return $this->form_validation->run();
    }

    private function get_product_data_from_post()
    {
        return [
            'nama_produk' => $this->input->post('nama_produk'),
            'harga' => $this->input->post('harga'),
            'kategori_id' => $this->input->post('kategori_id'),
            'status_id' => $this->input->post('status_id')
        ];
    }

    private function set_success_message($message)
    {
        $this->session->set_flashdata('success', $message);
    }

    private function set_error_message($message)
    {
        $this->session->set_flashdata('error', $message);
    }

    private function set_sync_success_message($result)
    {
        $message = sprintf(
            'Sync data berhasil! %d produk baru ditambahkan, %d produk diupdate.',
            $result['inserted'],
            $result['updated']
        );
        $this->set_success_message($message);
    }

    private function is_api_error($data)
    {
        return isset($data['error']) && $data['error'] == 1;
    }
}