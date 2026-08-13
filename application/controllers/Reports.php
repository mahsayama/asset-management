<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

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
        $data['title'] = 'Laporan Aset';
        $data['subtitle'] = 'Rekapitulasi & analisa data aset IT';

        $kategoris = $this->Kategori_model->get_all();
        $category_stats = array();
        foreach ($kategoris as $cat) {
            $count = $this->db->where('kategori_id', $cat->id)->count_all_results('assets');
            if ($count > 0) {
                $category_stats[] = array(
                    'nama' => $cat->nama,
                    'count' => (int)$count
                );
            }
        }
        $uncategorized = $this->db->where('kategori_id IS NULL', NULL, FALSE)->count_all_results('assets');
        if ($uncategorized > 0) {
            $category_stats[] = array(
                'nama' => 'Tanpa Kategori',
                'count' => (int)$uncategorized
            );
        }

        $lokasis = $this->Lokasi_model->get_all();
        $location_stats = array();
        foreach ($lokasis as $loc) {
            $count = $this->db->where('lokasi_id', $loc->id)->count_all_results('assets');
            if ($count > 0) {
                $location_stats[] = array(
                    'nama' => $loc->nama,
                    'count' => (int)$count
                );
            }
        }
        $unlocated = $this->db->where('lokasi_id IS NULL', NULL, FALSE)->count_all_results('assets');
        if ($unlocated > 0) {
            $location_stats[] = array(
                'nama' => 'Tanpa Lokasi',
                'count' => (int)$unlocated
            );
        }

        $data['category_stats'] = $category_stats;
        $data['location_stats'] = $location_stats;
        $data['total_assets'] = $this->Asset_model->count_all();
        $data['total_valuation'] = $this->Asset_model->total_valuation();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('reports/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function export_csv() {
        $assets = $this->Asset_model->get_paginated(10000, 0, '', '', '', '', '-created_at');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_aset_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Nama Aset', 'Serial Number', 'Barcode ID', 'Kategori', 'Lokasi', 'User Saat Ini', 'Departemen', 'Status', 'Harga'));

        foreach ($assets as $asset) {
            fputcsv($output, array(
                $asset->name,
                $asset->serial_number,
                $asset->barcode_id ?: '-',
                $asset->kategori_nama ?: '-',
                $asset->lokasi_nama ?: '-',
                $asset->current_user ?: '-',
                $asset->current_dept ?: '-',
                $asset->status,
                $asset->price ?: 0
            ));
        }

        fclose($output);
        exit;
    }
}
