<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect_to('login');
        }
        $this->load->model('Asset_model');
        $this->load->model('Kategori_model');
        $this->load->model('Lokasi_model');
    }

    public function index() {
        $data['title'] = 'Dashboard';
        $data['subtitle'] = 'Overview aset IT perusahaan';
        
        $data['total_assets'] = $this->Asset_model->count_all();
        $data['count_dipakai'] = $this->Asset_model->count_by_status('DIPAKAI');
        $data['count_tersedia'] = $this->Asset_model->count_by_status('TERSEDIA');
        $data['count_rusak'] = $this->Asset_model->count_by_status('RUSAK');
        $data['count_tidak_layak'] = $this->Asset_model->count_by_status('TIDAK_LAYAK');
        
        $data['recent_assets'] = $this->Asset_model->get_recent(5);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer', $data);
    }
}
