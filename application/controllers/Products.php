<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->database();
        $this->load->library(['form_validation', 'session']);
        $this->load->helper('url');
    }

    private function process_initial_data($data)
    {
        // Extract unique categories
        $categories = array_unique(array_column($data, 'kategori'));
        foreach ($categories as $category) {
            // Cek apakah kategori sudah ada
            $exists = $this->db->where('nama_kategori', $category)->get('kategori')->row();
            if (!$exists) {
                $this->db->insert('kategori', ['nama_kategori' => $category]);
            }
        }

        // Insert statuses jika belum ada
        $statuses = [['nama_status' => 'bisa dijual'], ['nama_status' => 'tidak bisa dijual']];
        foreach ($statuses as $status) {
            $exists = $this->db
                ->where('nama_status', $status['nama_status'])
                ->get('status')
                ->row();
            if (!$exists) {
                $this->db->insert('status', $status);
            }
        }

        // Get category and status mappings
        $category_map = [];
        $query = $this->db->get('kategori');
        foreach ($query->result() as $row) {
            $category_map[$row->nama_kategori] = $row->id_kategori;
        }

        $status_map = [];
        $query = $this->db->get('status');
        foreach ($query->result() as $row) {
            $status_map[$row->nama_status] = $row->id_status;
        }

        // Process products
        $inserted = 0;
        $updated = 0;
        foreach ($data as $item) {
            $product_data = [
                'nama_produk' => $item['nama_produk'],
                'harga' => $item['harga'],
                'kategori_id' => $category_map[$item['kategori']],
                'status_id' => $status_map[$item['status']],
            ];

            // Cek apakah produk sudah ada
            $exists = $this->db
                ->where('id_produk', $item['id_produk'])
                ->get('produk')
                ->row();

            if ($exists) {
                // Update data jika sudah ada
                $this->db->where('id_produk', $item['id_produk'])->update('produk', $product_data);
                $updated++;
            } else {
                // Insert baru jika belum ada
                $product_data['id_produk'] = $item['id_produk'];
                $this->db->insert('produk', $product_data);
                $inserted++;
            }
        }

        return [
            'inserted' => $inserted,
            'updated' => $updated,
        ];
    }

    public function save_api_data()
    {
        $url = 'https://recruitment.fastprint.co.id/tes/api_tes_programmer';

        // Pertama, lakukan request GET untuk mendapatkan username yang valid
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $initial_response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($initial_response, 0, $header_size);

        // Extract username dari header
        preg_match('/x-credentials-username: ([^\n]+)/', $headers, $matches);
        $username = trim($matches[1]);
        // Ambil hanya username tanpa keterangan dalam kurung
        $username = preg_replace('/\s+\(.*\)/', '', $username);

        // Generate password
        $date = sprintf('%02d', date('d'));
        $month = sprintf('%02d', date('n'));
        $year = date('y');

        $password = "bisacoding-{$date}-{$month}-{$year}";
        $md5_password = md5($password);

        // Set headers untuk request POST
        $headers = ["Username: {$username}", "Password: {$md5_password}"];

        // Set POST data
        $postData = [
            'username' => $username,
            'password' => $md5_password,
        ];

        // Lakukan request POST
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($ch);

        $data = json_decode($body, true);

        return [
            'header' => $header,
            'body' => $body,
            'data' => $data,
        ];
    }

    public function sync_api()
    {
        $response = $this->save_api_data();
        $data = $response['data'];

        if (isset($data['error']) && $data['error'] == 1) {
            $this->session->set_flashdata('error', 'Gagal sync data: ' . $data['ket']);
        } elseif (isset($data['data']) && is_array($data['data'])) {
            try {
                $result = $this->process_initial_data($data['data']);
                $this->session->set_flashdata('success', sprintf('Sync data berhasil! %d produk baru ditambahkan, %d produk diupdate.', $result['inserted'], $result['updated']));
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'Error saat menyimpan ke database: ' . $e->getMessage());
            }
        } else {
            $error_msg = "Gagal memproses data. Response dari API:\n\n";
            $error_msg .= "Headers:\n" . $response['header'] . "\n\n";
            $error_msg .= "Body:\n" . $response['body'];
            $this->session->set_flashdata('error', nl2br($error_msg));
        }

        redirect('products');
    }

    public function index()
    {
        // Join dengan tabel kategori dan status untuk mendapatkan nama kategori dan status
        $this->db->select('produk.*, kategori.nama_kategori, status.nama_status');
        $this->db->from('produk');
        $this->db->join('kategori', 'kategori.id_kategori = produk.kategori_id');
        $this->db->join('status', 'status.id_status = produk.status_id');
        $data['products'] = $this->db->get()->result();

        $this->load->view('products/index', $data);
    }

    public function sellable()
    {
        // Tampilkan hanya produk yang bisa dijual
        $this->db->select('produk.*, kategori.nama_kategori, status.nama_status');
        $this->db->from('produk');
        $this->db->join('kategori', 'kategori.id_kategori = produk.kategori_id');
        $this->db->join('status', 'status.id_status = produk.status_id');
        $this->db->where('status.nama_status', 'bisa dijual');
        $data['products'] = $this->db->get()->result();

        $this->load->view('products/index', $data);
    }

    public function add()
    {
        $data['categories'] = $this->db->get('kategori')->result();
        $data['statuses'] = $this->db->get('status')->result();
        $this->load->view('products/form', $data);
    }

    public function store()
    {
        $this->_validate();

        if ($this->form_validation->run() == false) {
            $this->add();
            return;
        }

        $data = [
            'nama_produk' => $this->input->post('nama_produk'),
            'harga' => $this->input->post('harga'),
            'kategori_id' => $this->input->post('kategori_id'),
            'status_id' => $this->input->post('status_id'),
        ];

        $this->product_model->insert($data);
        $this->session->set_flashdata('success', 'Produk berhasil ditambahkan');
        redirect('products');
    }

    public function edit($id)
    {
        $this->db->select('produk.*, kategori.nama_kategori, status.nama_status');
        $this->db->from('produk');
        $this->db->join('kategori', 'kategori.id_kategori = produk.kategori_id');
        $this->db->join('status', 'status.id_status = produk.status_id');
        $this->db->where('produk.id_produk', $id);
        $data['product'] = $this->db->get()->row();

        if (!$data['product']) {
            show_404();
        }

        $data['categories'] = $this->db->get('kategori')->result();
        $data['statuses'] = $this->db->get('status')->result();
        $this->load->view('products/form', $data);
    }

    public function update($id)
    {
        $this->_validate();

        if ($this->form_validation->run() == false) {
            $this->edit($id);
            return;
        }

        $data = [
            'nama_produk' => $this->input->post('nama_produk'),
            'harga' => $this->input->post('harga'),
            'kategori_id' => $this->input->post('kategori_id'),
            'status_id' => $this->input->post('status_id'),
        ];

        $this->product_model->update($id, $data);
        $this->session->set_flashdata('success', 'Produk berhasil diperbarui');
        redirect('products');
    }

    public function delete($id)
    {
        $this->product_model->delete($id);
        $this->session->set_flashdata('success', 'Produk berhasil dihapus');
        redirect('products');
    }

    private function _validate()
    {
        $this->form_validation->set_rules('nama_produk', 'Nama Produk', 'required');
        $this->form_validation->set_rules('harga', 'Harga', 'required|numeric');
        $this->form_validation->set_rules('kategori_id', 'Kategori', 'required');
        $this->form_validation->set_rules('status_id', 'Status', 'required');
    }
}
