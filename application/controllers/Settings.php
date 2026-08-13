<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect_to('login');
        }
        $this->load->model('User_model');
        $this->load->model('Kategori_model');
        $this->load->model('Lokasi_model');
    }

    private function is_async() {
        return $this->input->is_ajax_request() || strtolower($this->input->get_request_header('X-Requested-With') ?? '') === 'xmlhttprequest' || strtolower($this->input->get_request_header('HX-Request') ?? '') === 'true';
    }

    public function index() {
        if ($this->input->method() === 'post') {
            $type = trim($this->input->post('type', TRUE) ?? '');
            if ($type === 'lokasi' || $type === 'kategori') {
                return $this->store_master_data();
            }
        }

        $data['title'] = 'System Settings';
        $data['subtitle'] = 'Pengaturan hak akses admin & master data aset.';

        $data['users'] = $this->User_model->get_all();
        $data['kategoris'] = $this->Kategori_model->get_all();
        $data['lokasis'] = $this->Lokasi_model->get_all();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('settings/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function store_master_data() {
        $type = trim($this->input->post('type', TRUE) ?? '');
        $nama = trim($this->input->post('nama', TRUE) ?? '');

        if (empty($nama)) {
            return $this->send_json_error('Nama ' . ($type === 'lokasi' ? 'gedung/lokasi' : 'kategori') . ' wajib diisi!');
        }

        if ($type === 'lokasi') {
            $this->Lokasi_model->first_or_create($nama);
            $html = $this->load->view('settings/lokasi_list_partial', array('lokasis' => $this->Lokasi_model->get_all()), TRUE);
            $message = "Gedung / Lokasi '{$nama}' berhasil ditambahkan.";
        } elseif ($type === 'kategori') {
            $this->Kategori_model->first_or_create($nama);
            $html = $this->load->view('settings/kategori_list_partial', array('kategoris' => $this->Kategori_model->get_all()), TRUE);
            $message = "Kategori Aset '{$nama}' berhasil ditambahkan.";
        } else {
            return $this->send_json_error('Tipe master data tidak valid.');
        }

        if ($this->is_async()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => $message,
                    'html' => $html,
                    'type' => $type
                )));
        }

        $this->session->set_flashdata('success', $message);
        redirect_to('settings');
    }

    public function destroy_master_data($type, $id) {
        if ($type === 'kategori') {
            $this->Kategori_model->delete($id);
        } elseif ($type === 'lokasi') {
            $this->Lokasi_model->delete($id);
        }

        if ($this->is_async()) {
            if ($type === 'lokasi') {
                $html = $this->load->view('settings/lokasi_list_partial', array('lokasis' => $this->Lokasi_model->get_all()), TRUE);
            } else {
                $html = $this->load->view('settings/kategori_list_partial', array('kategoris' => $this->Kategori_model->get_all()), TRUE);
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Master data ' . ucfirst($type) . ' berhasil dihapus.',
                    'html' => $html,
                    'type' => $type
                )));
        }

        $this->session->set_flashdata('success', 'Master data berhasil dihapus.');
        redirect_to('settings');
    }

    public function store_user() {
        $name = trim($this->input->post('name', TRUE));
        $email = trim($this->input->post('email', TRUE));
        $password = $this->input->post('password', TRUE);
        $confirm = $this->input->post('password_confirmation', TRUE);

        if (empty($name) || empty($email) || empty($password)) {
            return $this->send_json_error('Semua kolom wajib diisi!');
        }

        if ($password !== $confirm) {
            return $this->send_json_error('Konfirmasi password tidak cocok!');
        }

        if (strlen($password) < 6) {
            return $this->send_json_error('Password minimal 6 karakter!');
        }

        $existing_user = $this->User_model->get_by_username_or_email($email);
        if (!$existing_user) {
            $existing_user = $this->User_model->get_by_username_or_email($name);
        }
        if ($existing_user) {
            return $this->send_json_error('Username atau Email sudah terdaftar!');
        }

        $this->User_model->create(array(
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        if ($this->is_async()) {
            $html = $this->load->view('settings/user_table_partial', array('users' => $this->User_model->get_all()), TRUE);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Akun Administrator baru berhasil ditambahkan.',
                    'html' => $html
                )));
        }

        $this->session->set_flashdata('success', 'Akun Administrator baru berhasil ditambahkan.');
        redirect_to('settings');
    }

    public function update_password() {
        $old_password = $this->input->post('old_password', TRUE);
        $new_password = $this->input->post('new_password', TRUE);
        $confirm = $this->input->post('new_password_confirmation', TRUE);

        if (empty($old_password) || empty($new_password)) {
            return $this->send_json_error('Password lama & baru wajib diisi!');
        }

        if ($new_password !== $confirm) {
            return $this->send_json_error('Konfirmasi password baru tidak cocok!');
        }

        if (strlen($new_password) < 6) {
            return $this->send_json_error('Password baru minimal 6 karakter!');
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($user_id);

        if (!$user || !password_verify($old_password, $user->password)) {
            return $this->send_json_error('Password lama yang dimasukkan salah.');
        }

        $this->User_model->update_password($user_id, password_hash($new_password, PASSWORD_BCRYPT));

        if ($this->is_async()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Password Anda berhasil diperbarui.'
                )));
        }

        $this->session->set_flashdata('success', 'Password Anda berhasil diperbarui.');
        redirect_to('settings');
    }

    public function destroy_user($id) {
        if ((int)$id === (int)$this->session->userdata('user_id')) {
            return $this->send_json_error('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.', 400);
        }

        $this->User_model->delete($id);

        if ($this->is_async()) {
            $html = $this->load->view('settings/user_table_partial', array('users' => $this->User_model->get_all()), TRUE);
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Akun Administrator telah dihapus.',
                    'html' => $html
                )));
        }

        $this->session->set_flashdata('success', 'Akun Administrator telah dihapus.');
        redirect_to('settings');
    }

    private function send_json_error($message, $status_code = 422) {
        if ($this->is_async()) {
            return $this->output
                ->set_status_header($status_code)
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => $message
                )));
        }
        $this->session->set_flashdata('error', $message);
        redirect_to('settings');
    }
}
