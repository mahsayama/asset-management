<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login() {
        if ($this->session->userdata('user_id')) {
            redirect_to('dashboard');
        }

        $error = '';
        if ($this->input->method() === 'post') {
            $username = trim($this->input->post('username', TRUE));
            $password = $this->input->post('password', TRUE);

            if (!empty($username) && !empty($password)) {
                $user = $this->User_model->get_by_username_or_email($username);

                if ($user && password_verify($password, $user->password)) {
                    $this->session->set_userdata(array(
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'logged_in' => TRUE,
                    ));
                    redirect_to('dashboard');
                } else {
                    $error = 'Username atau Password salah!';
                }
            } else {
                $error = 'Username dan Password wajib diisi!';
            }
        }

        $data['error'] = $error;
        $this->load->view('auth/login', $data);
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect_to('login');
    }
}
