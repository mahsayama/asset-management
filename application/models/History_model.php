<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_by_asset($asset_id) {
        $this->db->select('asset_histories.*, users.name as user_name, users.email as user_email');
        $this->db->from('asset_histories');
        $this->db->join('users', 'users.id = asset_histories.changed_by_id', 'left');
        $this->db->where('asset_histories.asset_id', $asset_id);
        $this->db->order_by('asset_histories.event_date', 'DESC');
        $this->db->order_by('asset_histories.id', 'DESC');
        return $this->db->get()->result();
    }

    public function log_history($asset_id, $user_id, $description) {
        $user_id = $user_id ?: 1;
        $data = array(
            'asset_id' => $asset_id,
            'changed_by_id' => $user_id,
            'event_date' => date('Y-m-d H:i:s'),
            'description' => $description,
        );
        return $this->db->insert('asset_histories', $data);
    }
}
