<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class perioda_model extends CI_Model {

    protected $_table;
    protected $_countAll;

    public function __construct() {
        parent::__construct();
        $this->config->load('database_tables', TRUE);
        $this->_table = $this->config->item('database_tables');
    }

    public function getAll($limit, $offset, $sidx, $sord, $year_id, $id_proyek) {

        $this->db->start_cache();
        $this->db->where("id_proyek", $id_proyek);
        $this->db->where("yearperiod_id", $year_id);
        $this->db->from($this->_table['perioda_v']);
        $this->_countAll = $this->db->count_all_results();
        $this->db->select("period_id, id_proyek, nama_kategoriproyek, proyek, period_name, period_start, period_end, period_closed, period_freeze");
        $this->db->order_by($sidx, $sord);
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->_table['perioda_v']);
        $this->db->flush_cache();

        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'period_id' => $row['period_id'],
                'id_proyek' => $row['id_proyek'],
                'nama_kategoriproyek' => $row['nama_kategoriproyek'],
                'proyek' => $row['proyek'],
                'period_name' => $row['period_name'],
                'period_start' => $row['period_start'],
                'period_end' => $row['period_end'],
                'period_closed' => $row['period_closed']
            );
        }
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menampilkan data periode akunting',
											'log_params' => json_encode(array('id_proyek' => $id_proyek, 'yearperiod_id' => $year_id))
										)
									  );
        return $temp_result;
    }

    public function countAll() {
        return $this->_countAll;
    }

    public function getAllYear($limit, $offset, $sidx, $sord, $id_proyek) {

        $this->db->start_cache();
        $this->db->where("id_proyek", $id_proyek);
        $this->db->from($this->_table['periodayear_v']);
        $this->_countAll = $this->db->count_all_results();
        $this->db->select("yearperiod_id, yearperiod_key, id_proyek, nama_kategoriproyek, proyek, yearperiod_start, yearperiod_end, yearperiod_closed");
        $this->db->order_by($sidx, $sord);
        $this->db->limit($limit, $offset);
        $query = $this->db->get($this->_table['periodayear_v']);
        $this->db->flush_cache();

        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'yearperiod_id' => $row['yearperiod_id'],
                'yearperiod_key' => $row['yearperiod_key'],
                'id_proyek' => $row['id_proyek'],
                'nama_kategoriproyek' => $row['nama_kategoriproyek'],
                'proyek' => $row['proyek'],
                'yearperiod_start' => $row['yearperiod_start'],
                'yearperiod_end' => $row['yearperiod_end'],
                'yearperiod_closed' => $row['yearperiod_closed']
            );
        }
        return $temp_result;
    }

    public function createperiode($start_date, $end_date, $id_proyek) {
        $query = $this->db->query("SELECT yearperiodcreateauto('" . $start_date . "', '" . $end_date . "', " . $id_proyek . ") as ret");
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menambahkan data periode akunting',
											'log_params' => json_encode(array('start_date' => $start_date, 'end_date' => $end_date, 'id_proyek' => $id_proyek))
										)
									  );
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function periodebulan_create($pname, $start_date, $end_date, $year_id, $id_user, $quarter) {
        $query = $this->db->query("select periodcreate('" . $start_date . "', '" . $end_date . "', " . $year_id . ", " . $id_user . ", " . $quarter . ",'" . $pname . "') as ret");
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menambahkan data periode bulan akunting',
											'log_params' => json_encode(array(
																			'start_date' => $start_date, 
																			'end_date' => $end_date, 
																			'pname' => $pname,
																			'year_id' => $year_id,
																			'quarter' => $quarter
																			)
																		)
										)
									  );
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function periodebulan_delete($period_id, $id_proyek) {
        $query = $this->db->query("select perioddelete(" . $period_id . ", " . $id_proyek . ") as ret");
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menghapus data periode bulan akunting',
											'log_params' => json_encode(array(
																			'period_id' => $period_id, 
																			'id_proyek' => $id_proyek
																			)
																		)
										)
									  );
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }
	
	public function periodebulan_update($data, $id_proyek, $key_period) {
        $this->db->where('id_proyek', $id_proyek);
        $this->db->where('period_key', $key_period);
        $this->db->update($this->_table['period'], $data);
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User mengedit data periode bulan akunting',
											'log_params' => json_encode(array(
																			'period_id' => $period_id, 
																			'id_proyek' => $id_proyek,
																			'data' => $data
																			)
																		)
										)
									  );
        return $this->db->affected_rows();;
    }
	
    public function getPeriodBulan($id) {
        $this->db->select("*");
        $this->db->from($this->_table['period']);
        $this->db->where("period_id", $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return array(
                'period_id' => $row['period_id'],
                'period_key' => $row['period_key'],
                'id_proyek' => $row['id_proyek'],
                'period_yearperiod_key' => $row['period_yearperiod_key'],
                'period_start' => $row['period_start'],
                'period_end' => $row['period_end'],
                'period_closed' => $row['period_closed'],
                'period_freeze' => $row['period_freeze'],
                'period_initial' => $row['period_initial'],
                'period_name' => $row['period_name'],
                'period_quarter' => $row['period_quarter'],
                'period_number' => $row['period_number']
            );
        } else {
            return array();
        }
    }

    public function perioda_bulan_lock($period_id, $id_proyek) {
        $query = $this->db->query("select periodclose(" . $period_id . ", " . $id_proyek . ") as ret");
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User mengunci data periode bulan akunting',
											'log_params' => json_encode(array(
																			'period_id' => $period_id, 
																			'id_proyek' => $id_proyek
																			)
																		)
										)
									  );
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function perioda_bulan_unlock($period_id, $id_proyek) {
        $query = $this->db->query("select periodopen(" . $period_id . ", " . $id_proyek . ") as ret");
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User membuka kunci data periode bulan akunting',
											'log_params' => json_encode(array(
																			'period_id' => $period_id, 
																			'id_proyek' => $id_proyek
																			)
																		)
										)
									  );
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function perioda_tahun_get($id) {
        $this->db->select("*");
        $this->db->from($this->_table['yearperiod']);
        $this->db->where("yearperiod_id", $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return array(
                'yearperiod_id' => $row['yearperiod_id'],
                'yearperiod_key' => $row['yearperiod_key'],
                'id_proyek' => $row['id_proyek'],
                'yearperiod_start' => $row['yearperiod_start'],
                'yearperiod_end' => $row['yearperiod_end'],
                'yearperiod_closed' => $row['yearperiod_closed']
            );
        } else {
            return array();
        }
    }
	
	public function perioda_tahun_update($data, $id_proyek, $key_yearperiod) {
        $this->db->where('id_proyek', $id_proyek);
        $this->db->where('yearperiod_key', $key_yearperiod);
        $this->db->update($this->_table['yearperiod'], $data);
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User mengedit data periode tahun akunting',
											'log_params' => json_encode(array(
																			'yearperiod_key' => $key_yearperiod, 
																			'id_proyek' => $id_proyek,
																			'data' => $data
																			)
																		)
										)
									  );
        return $this->db->affected_rows();;
    }
	
    public function perioda_tahun_edit($year_id, $id_proyek, $start_date, $end_date) {
        $query = $this->db->query("select yearperioddateschange(" . $year_id . ", " . $id_proyek . ",'" . $start_date . "','" . $end_date . "') as ret");
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User mengedit data periode tahun akunting',
											'log_params' => json_encode(array(
																			'year_id' => $year_id, 
																			'id_proyek' => $id_proyek,
																			'start_date' => $start_date,
																			'end_date' => $end_date
																			)
																		)
										)
									  );
		if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function perioda_tahun_delete($year_id, $id_proyek) {
        $query = $this->db->query("select yearperioddelete(" . $year_id . ", " . $id_proyek . ") as ret");
         $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menghapus data periode tahun akunting',
											'log_params' => json_encode(array(
																			'year_id' => $year_id, 
																			'id_proyek' => $id_proyek
																			)
																		)
										)
									  );
		if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function perioda_tahun_lock($year_id, $id_proyek) {
        $query = $this->db->query("select yearperiodclose(" . $year_id . ", " . $id_proyek . ") as ret");
         $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User mengunci data periode tahun akunting',
											'log_params' => json_encode(array(
																			'year_id' => $year_id, 
																			'id_proyek' => $id_proyek
																			)
																		)
										)
									  );
		if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

    public function perioda_tahun_unlock($year_id, $id_proyek) {
        $query = $this->db->query("select yearperiodopen(" . $year_id . ", " . $id_proyek . ") as ret");
         $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User membuka kunci data periode tahun akunting',
											'log_params' => json_encode(array(
																			'year_id' => $year_id, 
																			'id_proyek' => $id_proyek
																			)
																		)
										)
									  );
		if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['ret'];
        } else {
            return false;
        }
    }

}
