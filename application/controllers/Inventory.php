<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Inventory extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect_to('login');
        }
        $this->load->model('Asset_model');
        $this->load->model('Kategori_model');
        $this->load->model('Lokasi_model');
        $this->load->model('History_model');
    }

    private function is_async() {
        return $this->input->is_ajax_request() || strtolower($this->input->get_request_header('X-Requested-With') ?? '') === 'xmlhttprequest' || strtolower($this->input->get_request_header('HX-Request') ?? '') === 'true';
    }

    public function index() {
        $query = trim($this->input->get('q', TRUE) ?? '');
        $category = trim($this->input->get('category', TRUE) ?? '');
        $location = trim($this->input->get('location', TRUE) ?? '');
        $status = trim($this->input->get('status', TRUE) ?? '');
        $sort = $this->input->get('sort', TRUE) ?: '-created_at';
        $page = max(1, (int)($this->input->get('page', TRUE) ?: 1));

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total_rows = $this->Asset_model->count_filtered($query, $category, $location, $status);
        $assets = $this->Asset_model->get_paginated($limit, $offset, $query, $category, $location, $status, $sort);

        $total_pages = max(1, ceil($total_rows / $limit));

        $data = array(
            'title' => 'Inventory Aset',
            'subtitle' => 'Kelola database seluruh aset IT.',
            'assets' => $assets,
            'total_rows' => $total_rows,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'query' => $query,
            'category_filter' => $category,
            'location_filter' => $location,
            'status_filter' => $status,
            'sort_current' => $sort,
            'kategori_list' => $this->Kategori_model->get_all(),
            'lokasi_list' => $this->Lokasi_model->get_all(),
            'status_choices' => Asset_model::status_choices(),
        );

        if ($this->input->get_request_header('HX-Request') && $this->input->get_request_header('HX-Target') === 'asset-table-body') {
            $this->load->view('inventory/table', $data);
            return;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventory/index', $data);
        $this->load->view('templates/footer', $data);
    }

    public function create() {
        if ($this->input->method() === 'post') {
            $name = trim($this->input->post('name', TRUE));
            $serial_number = trim($this->input->post('serial_number', TRUE));
            $barcode_id = trim($this->input->post('barcode_id', TRUE));
            $kategori_id = $this->input->post('kategori_id', TRUE) ?: NULL;
            $lokasi_id = $this->input->post('lokasi_id', TRUE) ?: NULL;
            $purchase_date = $this->input->post('purchase_date', TRUE) ?: NULL;
            $price_raw = $this->input->post('price', TRUE);
            $status = $this->input->post('status', TRUE) ?: 'TERSEDIA';
            $note = trim($this->input->post('note', TRUE));
            $current_user = trim($this->input->post('current_user', TRUE));
            $current_dept = trim($this->input->post('current_dept', TRUE));
            $prev_user = trim($this->input->post('prev_user', TRUE));
            $prev_dept = trim($this->input->post('prev_dept', TRUE));

            $price = (!empty($price_raw)) ? preg_replace('/[^\d]/', '', (string)$price_raw) : NULL;

            if (empty($name) || empty($serial_number)) {
                $this->session->set_flashdata('error', 'Nama aset dan Serial Number wajib diisi!');
                redirect_to('tambah');
            }

            $asset_id = $this->Asset_model->create(array(
                'name' => $name,
                'serial_number' => $serial_number,
                'barcode_id' => $barcode_id,
                'kategori_id' => $kategori_id,
                'lokasi_id' => $lokasi_id,
                'purchase_date' => $purchase_date,
                'price' => $price,
                'status' => $status,
                'note' => $note,
                'current_user' => $current_user,
                'current_dept' => $current_dept,
                'prev_user' => $prev_user,
                'prev_dept' => $prev_dept,
            ));

            $user_id = $this->session->userdata('user_id') ?: 1;
            $desc = 'Aset baru didaftarkan ke sistem.';
            if (!empty($current_user)) {
                $deptStr = !empty($current_dept) ? " ({$current_dept})" : "";
                $desc .= " Ditugaskan ke '{$current_user}'{$deptStr}.";
            }
            if (!empty($prev_user)) {
                $prevDeptStr = !empty($prev_dept) ? " ({$prev_dept})" : "";
                $desc .= " Catatan riwayat lama: Pernah digunakan oleh '{$prev_user}'{$prevDeptStr}.";
            }

            $this->History_model->log_history($asset_id, $user_id, $desc);

            $this->session->set_flashdata('success', 'Mantap! Aset berhasil ditambahkan.');
            redirect_to('inventory');
        }

        $data['title'] = 'Tambah Aset';
        $data['subtitle'] = 'Pendaftaran data aset baru';
        $data['kategoriList'] = $this->Kategori_model->get_all();
        $data['lokasiList'] = $this->Lokasi_model->get_all();
        $data['statusChoices'] = Asset_model::status_choices();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventory/create', $data);
        $this->load->view('templates/footer', $data);
    }

    public function edit($id) {
        $asset = $this->Asset_model->get_by_id($id);
        if (!$asset) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $name = trim($this->input->post('name', TRUE));
            $serial_number = trim($this->input->post('serial_number', TRUE));
            $barcode_id = trim($this->input->post('barcode_id', TRUE));
            $kategori_id = $this->input->post('kategori_id', TRUE) ?: NULL;
            $lokasi_id = $this->input->post('lokasi_id', TRUE) ?: NULL;
            $purchase_date = $this->input->post('purchase_date', TRUE) ?: NULL;
            $price_raw = $this->input->post('price', TRUE);
            $status = $this->input->post('status', TRUE) ?: 'TERSEDIA';
            $note = trim($this->input->post('note', TRUE));
            $current_user = trim($this->input->post('current_user', TRUE));
            $current_dept = trim($this->input->post('current_dept', TRUE));
            $prev_user = trim($this->input->post('prev_user', TRUE));
            $prev_dept = trim($this->input->post('prev_dept', TRUE));

            $price = (!empty($price_raw)) ? preg_replace('/[^\d]/', '', (string)$price_raw) : NULL;

            if (empty($name) || empty($serial_number)) {
                $this->session->set_flashdata('error', 'Nama aset dan Serial Number wajib diisi!');
                redirect_to('edit/' . $id);
            }

            // Automatic Handover Shift
            $oldUser = trim($asset->current_user ?? '');
            $oldDept = trim($asset->current_dept ?? '');

            if ($oldUser !== '' && $oldUser !== $current_user) {
                if (empty($prev_user) || $prev_user === trim($asset->prev_user ?? '')) {
                    $prev_user = $oldUser;
                    $prev_dept = $oldDept;
                }
            }

            $update_data = array(
                'name' => $name,
                'serial_number' => $serial_number,
                'barcode_id' => $barcode_id,
                'kategori_id' => $kategori_id,
                'lokasi_id' => $lokasi_id,
                'purchase_date' => $purchase_date,
                'price' => $price,
                'status' => $status,
                'note' => $note,
                'current_user' => $current_user,
                'current_dept' => $current_dept,
                'prev_user' => $prev_user,
                'prev_dept' => $prev_dept,
            );

            $changes = $this->determine_changes($asset, $update_data);

            $this->Asset_model->update($id, $update_data);

            if (!empty($changes)) {
                $user_id = $this->session->userdata('user_id') ?: 1;
                $this->History_model->log_history($id, $user_id, implode('; ', $changes) . '.');
            }

            $this->session->set_flashdata('success', 'Data aset berhasil diperbarui.');
            redirect_to('inventory');
        }

        $data['title'] = 'Edit Data Aset';
        $data['subtitle'] = 'Perbarui detail data aset';
        $data['asset'] = $asset;
        $data['kategoriList'] = $this->Kategori_model->get_all();
        $data['lokasiList'] = $this->Lokasi_model->get_all();
        $data['statusChoices'] = Asset_model::status_choices();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventory/edit', $data);
        $this->load->view('templates/footer', $data);
    }

    public function show($id) {
        $asset = $this->Asset_model->get_by_id($id);
        if (!$asset) {
            show_404();
        }

        $histories = $this->History_model->get_by_asset($id);

        $data['title'] = 'Detail Aset - ' . $asset->name;
        $data['subtitle'] = 'Informasi lengkap & riwayat aktivitas aset';
        $data['asset'] = $asset;
        $data['histories'] = $histories;

        $choices = Asset_model::status_choices();
        $data['status_display'] = $choices[$asset->status] ?? $asset->status;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventory/show', $data);
        $this->load->view('templates/footer', $data);
    }

    public function destroy($id) {
        $asset = $this->Asset_model->get_by_id($id);
        $asset_name = $asset ? $asset->name : '';

        if ($asset) {
            $this->Asset_model->delete($id);
        }

        if ($this->is_async()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => $asset_name ? "Aset '{$asset_name}' telah berhasil dihapus." : "Aset telah dihapus."
                )));
        }

        $this->session->set_flashdata('error', "Aset '{$asset_name}' telah dihapus.");
        redirect_to('inventory');
    }

    public function bulk_destroy() {
        $ids = $this->input->post('ids', TRUE);
        if (empty($ids) || !is_array($ids)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('success' => false, 'message' => 'Tidak ada aset yang dipilih.')));
        }

        $count = $this->Asset_model->bulk_delete($ids);

        if ($this->is_async()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => "Berhasil menghapus {$count} data aset terpilih."
                )));
        }

        $this->session->set_flashdata('error', "Berhasil menghapus {$count} data aset terpilih.");
        redirect_to('inventory');
    }

    public function import_excel() {
        if (!empty($_FILES['excel_file']['name'])) {
            try {
                $filePath = $_FILES['excel_file']['tmp_name'];
                $spreadsheet = IOFactory::load($filePath);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $imported_count = 0;
                $user_id = $this->session->userdata('user_id') ?: 1;

                foreach ($sheetData as $rowIndex => $row) {
                    if ($rowIndex === 1) continue;

                    $name = trim($row['A'] ?? '');
                    $serial_number = trim($row['B'] ?? '');
                    if (empty($name) || empty($serial_number)) continue;

                    $barcode_id = trim($row['C'] ?? '');
                    $kategori_nama = trim($row['D'] ?? '');
                    $lokasi_nama = trim($row['E'] ?? '');
                    $current_user = trim($row['F'] ?? '');
                    $current_dept = trim($row['G'] ?? '');
                    $status_raw = strtoupper(trim($row['H'] ?? 'TERSEDIA'));
                    $price_raw = trim($row['I'] ?? '');

                    $kategori_id = !empty($kategori_nama) ? $this->Kategori_model->first_or_create($kategori_nama) : NULL;
                    $lokasi_id = !empty($lokasi_nama) ? $this->Lokasi_model->first_or_create($lokasi_nama) : NULL;
                    $price = (!empty($price_raw)) ? preg_replace('/[^\d]/', '', (string)$price_raw) : NULL;

                    $status = 'TERSEDIA';
                    if (strpos($status_raw, 'PAKAI') !== false) $status = 'DIPAKAI';
                    if (strpos($status_raw, 'RUSAK') !== false) $status = 'RUSAK';
                    if (strpos($status_raw, 'HILANG') !== false) $status = 'HILANG';
                    if (strpos($status_raw, 'TIDAK') !== false) $status = 'TIDAK_LAYAK';

                    $asset_id = $this->Asset_model->create(array(
                        'name' => $name,
                        'serial_number' => $serial_number,
                        'barcode_id' => $barcode_id,
                        'kategori_id' => $kategori_id,
                        'lokasi_id' => $lokasi_id,
                        'current_user' => $current_user,
                        'current_dept' => $current_dept,
                        'status' => $status,
                        'price' => $price,
                    ));

                    $this->History_model->log_history($asset_id, $user_id, 'Aset baru diimpor dari file Excel.');
                    $imported_count++;
                }

                $this->session->set_flashdata('success', 'Mantap! Berhasil mengimpor ' . $imported_count . ' data aset.');
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'Gagal impor data. Error: ' . $e->getMessage());
            }
        }
        redirect_to('inventory');
    }

    public function download_template() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = array('Nama Aset', 'Serial Number', 'Barcode', 'Kategori', 'Lokasi', 'User Saat Ini', 'Departemen Saat Ini', 'Status (Tersedia/Dipakai/Rusak/Maintenance)', 'Harga');
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_aset.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function determine_changes($asset, $update_data) {
        $changes = array();

        $oldUser = isset($asset->current_user) && trim($asset->current_user) !== '' ? trim($asset->current_user) : '-';
        $oldDept = isset($asset->current_dept) && trim($asset->current_dept) !== '' ? trim($asset->current_dept) : '-';
        $newUser = isset($update_data['current_user']) && trim($update_data['current_user']) !== '' ? trim($update_data['current_user']) : '-';
        $newDept = isset($update_data['current_dept']) && trim($update_data['current_dept']) !== '' ? trim($update_data['current_dept']) : '-';

        if ($oldUser !== $newUser || $oldDept !== $newDept) {
            if ($oldUser === '-' && $newUser !== '-') {
                $deptStr = ($newDept !== '-') ? " ({$newDept})" : "";
                $changes[] = "Penugasan aset pertama kali ke '{$newUser}'{$deptStr}";
            } elseif ($oldUser !== '-' && $newUser === '-') {
                $deptStr = ($oldDept !== '-') ? " ({$oldDept})" : "";
                $changes[] = "Aset dikembalikan oleh '{$oldUser}'{$deptStr}";
            } elseif ($oldUser !== '-' && $newUser !== '-' && $oldUser !== $newUser) {
                $oldStr = "'{$oldUser}'" . (($oldDept !== '-') ? " ({$oldDept})" : "");
                $newStr = "'{$newUser}'" . (($newDept !== '-') ? " ({$newDept})" : "");
                $changes[] = "Pengalihan aset dari {$oldStr} ke {$newStr}";
            } elseif ($oldUser !== '-' && $oldUser === $newUser && $oldDept !== $newDept) {
                $changes[] = "Perubahan divisi '{$oldUser}' dari '{$oldDept}' menjadi '{$newDept}'";
            }
        }

        $fieldLabels = array(
            'name' => 'Nama Aset',
            'serial_number' => 'Nomor Seri',
            'barcode_id' => 'ID Barcode',
            'kategori_id' => 'Kategori',
            'lokasi_id' => 'Lokasi',
            'purchase_date' => 'Tanggal Beli',
            'price' => 'Harga',
            'status' => 'Status',
            'note' => 'Keterangan',
            'prev_user' => 'User Lama',
            'prev_dept' => 'Departemen Lama',
        );

        foreach ($update_data as $field => $newVal) {
            if (in_array($field, array('current_user', 'current_dept'))) continue;

            $oldVal = $asset->$field ?? null;

            if ($field === 'purchase_date') {
                $oldDateStr = $oldVal ? date('Y-m-d', strtotime($oldVal)) : null;
                $newDateStr = $newVal ? date('Y-m-d', strtotime($newVal)) : null;
                if ($oldDateStr === $newDateStr) continue;
            }

            if ($field === 'price') {
                $oldPrice = (is_null($oldVal) || $oldVal === '') ? null : (float)$oldVal;
                $newPrice = (is_null($newVal) || $newVal === '') ? null : (float)$newVal;
                if ($oldPrice === $newPrice) continue;
            }

            $oldValStr = (is_null($oldVal) || $oldVal === '') ? '' : trim((string)$oldVal);
            $newValStr = (is_null($newVal) || $newVal === '') ? '' : trim((string)$newVal);

            if ($field !== 'purchase_date' && $field !== 'price' && $oldValStr === $newValStr) continue;

            $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));

            if ($field === 'kategori_id') {
                $oldValDisp = $oldVal ? ($this->Kategori_model->get_by_id($oldVal)->nama ?? '-') : '-';
                $newValDisp = $newVal ? ($this->Kategori_model->get_by_id($newVal)->nama ?? '-') : '-';
            } elseif ($field === 'lokasi_id') {
                $oldValDisp = $oldVal ? ($this->Lokasi_model->get_by_id($oldVal)->nama ?? '-') : '-';
                $newValDisp = $newVal ? ($this->Lokasi_model->get_by_id($newVal)->nama ?? '-') : '-';
            } elseif ($field === 'status') {
                $choices = Asset_model::status_choices();
                $oldValDisp = $choices[$oldVal] ?? $oldVal;
                $newValDisp = $choices[$newVal] ?? $newVal;
            } elseif ($field === 'price') {
                $oldValDisp = $oldVal ? 'Rp ' . number_format($oldVal, 0, ',', '.') : '-';
                $newValDisp = $newVal ? 'Rp ' . number_format($newVal, 0, ',', '.') : '-';
            } elseif ($field === 'purchase_date') {
                $oldValDisp = $oldVal ? date('d M Y', strtotime($oldVal)) : '-';
                $newValDisp = $newVal ? date('d M Y', strtotime($newVal)) : '-';
            } else {
                $oldValDisp = ($oldValStr === '') ? '-' : $oldValStr;
                $newValDisp = ($newValStr === '') ? '-' : $newValStr;
            }

            if ($oldValDisp === '-') {
                $changes[] = "Mengisi {$label}: '{$newValDisp}'";
            } elseif ($newValDisp === '-') {
                $changes[] = "Menghapus {$label} (sebelumnya '{$oldValDisp}')";
            } else {
                $changes[] = "Mengubah {$label} dari '{$oldValDisp}' menjadi '{$newValDisp}'";
            }
        }

        return $changes;
    }
}
