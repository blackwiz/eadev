<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class site_library {

    public function __construct() {
        if ($CI = & get_instance()) {
            $this->load = $CI->load;
            $this->load->database();
            $this->db = $CI->db;
            $this->dataset_db = $CI->load->model('dataset_db');
            $this->session = $CI->session;
            return;
        }
    }

    /*
     * @param $date
     * @return integer
     */

    public function getPeriodeId($date) {
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $proyek = $userconfig["kolom2"];
        $query = $this->db->query("SELECT getperiodid('" . $date . "'," . $proyek . ") as id");
        //$query = $this->db->query("SELECT getperiodid('" . $date . "') as id");

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['id'];
        } else {
            return false;
        }
    }

    /*
     * add by asep
     * move from modul bukubesar
     * now we can call this func from main
     * @return array 
     */

    public function getarrayperiod() {
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
        $proyek = $userconfig["kolom2"];

        $sql = "SELECT period_id,
                period_name,
                period_start || ' s/d ' || period_end as period_range
                FROM period
                WHERE period.id_proyek = $proyek
                ORDER BY period_start ASC";

        $query = $this->db->query($sql);
        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'image' => "",
                'description' => $row['period_range'],
//                'description' => $row['period_range'],
                'value' => $row['period_id'],
                'text' => ""
//                'text' => $row['period_name']
            );
        }
        return $temp_result;
    }

    public function getKodePerkiraan($id_proyek) {
		$jenis = $this->getJenisProyek($id_proyek);
        $this->db->select('dperkir_id,kdperkiraan,nmperkiraan');
        $this->db->from('tbl_dperkir');
        $this->db->where('dperkir_jenis_id', $jenis);
        $this->db->order_by('kdperkiraan');
        $query = $this->db->get();
        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[$row['dperkir_id']] = $row['kdperkiraan'] . ' - ' . $row['nmperkiraan'];
        }
        return $temp_result;
    }
    
	private function getJenisProyek($id_proyek){
		$this->db->select("dperkir_jenis_id");
		$this->db->from("tbl_proyek");
		$this->db->where("id_proyek",$id_proyek);
		$query = $this->db->get();
		$row = $query->row_array();
		return $row['dperkir_jenis_id'];
	}
	
    public function getSelectedKodePerkiraan($idRekanan, $idProyek) {
        $this->db->select("tbl_dperkir.dperkir_id,tbl_dperkir.kdperkiraan, tbl_dperkir.nmperkiraan");
        $this->db->from('tbl_dperkir');
        $this->db->join('tbl_bukubantu', "tbl_dperkir.dperkir_id = tbl_bukubantu.bukubantu_dperkir_id", "right");
        $this->db->join('tbl_rekanan', "tbl_bukubantu.bukubantu_kdrekanan = tbl_rekanan.kode_rekanan", "left");
        $this->db->where("tbl_rekanan.id_rekanan", $idRekanan);
        $this->db->where("tbl_bukubantu.bukubantu_id_proyek", $idProyek);
        $query = $this->db->get();

        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = $row['dperkir_id'];
        }
        return $temp_result;
    }

    /*
     * @return ARRAY
     */

    public function getperiodkey($idproyek) {
        $sql = "SELECT 
                period_id,
                period_key,
                period_name,
                period_start || ' s/d ' || period_end as period_range
                FROM period
                WHERE period.id_proyek = " . $idproyek . "
                ORDER BY period_start ASC";

        $query = $this->db->query($sql);
        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'image' => "",
                'description' => $row['period_range'],
                'value' => $row['period_key'],
                'text' => ""
            );
        }

        return $temp_result;
    }

    public function toolbars($params = array()) {
        if (!is_array($params))
            $params = array();

        $html = '<div style="margin: 0;" class="btn-toolbar">';

        foreach ($params as $value) {
            $html .= '<div class="btn-group">';
            foreach ($value as $key => $value2) {

                if (isset($value2["tag"]) AND !empty($value2["tag"])) {
                    $html .= '<a class="btn">4</a>';
                } else {
                    $html .= '<button class="btn">4</button>';
                }
            }
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }
    
	public function getIcons() {
        $this->db->select("icon_id, icon_name, icon_desc, icon_disable, icon_isimage, icon_ishtml, icon_value");
        $this->db->from("tbl_icon");
        $this->db->order_by("icon_name");
        $query = $this->db->get();
        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[] = array(
                'icon_id' => $row['icon_id'],
                'icon_name' => $row['icon_name'],
                'icon_desc' => $row['icon_desc'],
                'icon_disable' => $row['icon_disable'],
                'icon_isimage' => $row['icon_isimage'],
                'icon_ishtml' => $row['icon_ishtml'],
                'icon_value' => $row['icon_value']
            );
        }
        return $temp_result;
    }
}

