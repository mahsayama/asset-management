<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('kategori')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('kategori', array('id' => $id))->row();
    }

    public function first_or_create($nama) {
        $existing = $this->db->get_where('kategori', array('nama' => $nama))->row();
        if ($existing) {
            return $existing->id;
        }
        $this->db->insert('kategori', array('nama' => $nama));
        return $this->db->insert_id();
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('kategori');
    }

    public function count_all() {
        return $this->db->count_all('kategori');
    }
}
