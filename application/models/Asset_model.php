<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public static function status_choices() {
        return array(
            'TERSEDIA' => 'Tersedia',
            'DIPAKAI' => 'Sedang Dipakai',
            'RUSAK' => 'Rusak',
            'HILANG' => 'Hilang',
            'TIDAK_LAYAK' => 'Tidak Layak Pakai',
        );
    }

    public function get_filtered_query($query = '', $category = '', $location = '', $status = '', $sort = '-created_at') {
        $this->db->select('assets.*, kategori.nama as kategori_nama, lokasi.nama as lokasi_nama');
        $this->db->from('assets');
        $this->db->join('kategori', 'kategori.id = assets.kategori_id', 'left');
        $this->db->join('lokasi', 'lokasi.id = assets.lokasi_id', 'left');

        if ($query !== '') {
            $this->db->group_start();
            $this->db->like('assets.name', $query);
            $this->db->or_like('assets.serial_number', $query);
            $this->db->or_like('assets.barcode_id', $query);
            $this->db->or_like('assets.current_user', $query);
            $this->db->or_like('assets.current_dept', $query);
            $this->db->group_end();
        }

        if ($category !== '') {
            $this->db->where('assets.kategori_id', $category);
        }

        if ($location !== '') {
            $this->db->where('assets.lokasi_id', $location);
        }

        if ($status !== '') {
            $this->db->where('assets.status', $status);
        }

        if (strpos($sort, '-') === 0) {
            $column = substr($sort, 1);
            $this->db->order_by('assets.' . $column, 'DESC');
        } else {
            $this->db->order_by('assets.' . $sort, 'ASC');
        }
    }

    public function count_filtered($query = '', $category = '', $location = '', $status = '') {
        $this->get_filtered_query($query, $category, $location, $status);
        return $this->db->count_all_results();
    }

    public function get_paginated($limit, $offset, $query = '', $category = '', $location = '', $status = '', $sort = '-created_at') {
        $this->get_filtered_query($query, $category, $location, $status, $sort);
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        $this->db->select('assets.*, kategori.nama as kategori_nama, lokasi.nama as lokasi_nama');
        $this->db->from('assets');
        $this->db->join('kategori', 'kategori.id = assets.kategori_id', 'left');
        $this->db->join('lokasi', 'lokasi.id = assets.lokasi_id', 'left');
        $this->db->where('assets.id', $id);
        return $this->db->get()->row();
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert('assets', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('assets', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('assets');
    }

    public function bulk_delete($ids) {
        if (empty($ids) || !is_array($ids)) return 0;
        $this->db->where_in('id', $ids);
        $this->db->delete('assets');
        return $this->db->affected_rows();
    }

    public function get_recent($limit = 5) {
        $this->db->select('assets.*, kategori.nama as kategori_nama, lokasi.nama as lokasi_nama');
        $this->db->from('assets');
        $this->db->join('kategori', 'kategori.id = assets.kategori_id', 'left');
        $this->db->join('lokasi', 'lokasi.id = assets.lokasi_id', 'left');
        $this->db->order_by('assets.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function count_by_status($status) {
        $this->db->where('status', $status);
        return $this->db->count_all_results('assets');
    }

    public function total_valuation() {
        $this->db->select_sum('price');
        $query = $this->db->get('assets');
        return $query->row()->price ?? 0;
    }

    public function count_all() {
        return $this->db->count_all('assets');
    }
}
