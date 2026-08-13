<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {
        $this->db->order_by('name', 'ASC');
        return $this->db->get('users')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('users', array('id' => $id))->row();
    }

    public function get_by_username_or_email($login_input) {
        $this->db->where('name', $login_input);
        $this->db->or_where('email', $login_input);
        return $this->db->get('users')->row();
    }

    public function create($data) {
        return $this->db->insert('users', $data);
    }

    public function update_password($user_id, $hashed_password) {
        $this->db->where('id', $user_id);
        return $this->db->update('users', array('password' => $hashed_password));
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    public function count_all() {
        return $this->db->count_all('users');
    }
}
