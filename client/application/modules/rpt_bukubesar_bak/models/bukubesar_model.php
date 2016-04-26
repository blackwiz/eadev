<?php

class bukubesar_model extends CI_Model {

    protected $_table;
    protected $_countAll;
    protected $_arrayBukuBesar = array();

    public function __construct() {
        parent::__construct();
        $this->config->load('database_tables', TRUE);
        $this->_table = $this->config->item('database_tables');
        $this->load->library('search_form');
    }

    public function getKodeperkir() {
        $query = $this->db->get('kodeperkiraan_v');

        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[$row['kdperkiraan']] = $row['nmperkiraan'];
        }
        return $temp_result;
    }

    public function getdateperiod($id) {
        $sql = "SELECT period_start, period_end FROM period  WHERE period_id = " . $id;
        $query = $this->db->query($sql);
        $temp_result = array();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $temp_result["period_start"] = $row["period_start"];
            $temp_result["period_end"] = $row["period_end"];
            return $temp_result;
        } else {
            return false;
        }
    }

    public function getBukuBesar($limit, $offset, $sidx, $sord, $cari, $search = array()) {
        if (!is_array($cari))
            $cari = array();

        if (strtolower($search) == "true") {
            $sql = "select * from getgledger(" . $cari["subunit_proyek"] . ", '" . $cari["periode_awal"] . "', '" . $cari["periode_akhir"] . "', '" . $cari["coa_awal"] . "', '" . $cari["coa_akhir"] . "')";
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            
//            $sql .= " order by ledger_period_id, ledger_trialbal_kdperkiraan asc";
            $sql .= " limit " . $limit . " offset " . $offset;

            $query = $this->db->query($sql);
            
            $data["count"] = $count;
            $data["rec"] = $query->result_array();
            
            
            /*
            $temp_result = array();
            $x = 0;
            foreach ($query->result_array() as $row) {
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["id_proyek"] = $row["ledger_id_proyek"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["nama_proyek"] = $row["ledger_nama_proyek"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["kode"] = $row["ledger_trialbal_kdperkiraan"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["coa"] = $row["ledger_trialbal_kdperkiraan"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["period_id"] = $row["ledger_period_id"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["period_name"] = $row["ledger_period_name"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["period_start"] = $row["ledger_period_start"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["period_end"] = $row["ledger_period_end"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["saldo_awal"] = $row["ledger_trialbal_beginning"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["saldo_akhir"] = $row["ledger_trialbal_ending"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["sub_total_debet"] = $row["ledger_trialbal_debits"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["sub_total_kredit"] = $row["ledger_trialbal_credits"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["id_jurnal"] = $row["ledger_id_jurnal"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["nobukti"] = $row["ledger_nobukti"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["rekanan"] = $row["ledger_kode_rekanan"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["tanggal"] = $row["ledger_tanggal"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["coa_debet"] = $row["ledger_coa_debet"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["coa_kredit"] = $row["ledger_coa_kredit"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["keterangan"] = $row["ledger_keterangan"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["debet"] = $row["ledger_debet"];
                $temp_result[$row["ledger_trialbal_kdperkiraan"]]["periode"][$row["ledger_period_id"]]["detail"][$row["ledger_id_jurnal"]]["kredit"] = $row["ledger_kredit"];
                $x++;
            }
            $this->_arrayBukuBesar = $temp_result;
             * 
             */
            return $data;
        } else {
            return false;
        }
    }

    public function arrayToexcel() {
        $bulan = $this->search_form->getBulan();

        $temp_result = array();
        foreach ($this->_arrayBukuBesar as $key => $value) {

            $temp_result[$key]["nama_proyek"] = $value["nama_proyek"];
            $temp_result[$key]["tahun"] = $value["tahun"];
            $temp_result[$key]["kode_perkiraan"] = $value["coa"];

            foreach ($value["bulan"] as $key2 => $value2) {

                $sub_total_debet = 0;
                $sub_total_kredit = 0;
                $no = 1;

                $temp_result[$key]["data"][] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "Saldo Awal " . $bulan[$value2["bln"]],
                    "coa" => "",
                    "debet" => $value2["saldo_awal"],
                    "kredit" => ""
                );

                foreach ($value2["detail"] as $key3 => $value3) {

                    $temp_result[$key]["data"][] = array(
                        "nomor" => $no,
                        "tanggal" => $value3["tanggal"],
                        "no_bukti" => $value3["nobukti"],
                        "rekanan" => $value3["rekanan"],
                        "uraian" => $value3["keterangan"],
                        "coa" => $value3["kdperkiraan"],
                        "debet" => $value3["debet"],
                        "kredit" => $value3["kredit"]
                    );
                    $sub_total_debet += $value3["debet"];
                    $sub_total_kredit += $value3["kredit"];
                    $no++;
                }
                $temp_result[$key]["data"][] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "Sub Total Transaksi",
                    "coa" => "",
                    "debet" => $sub_total_debet + $value2["saldo_awal"],
                    "kredit" => (!empty($sub_total_kredit) or $sub_total_kredit != 0) ? $sub_total_kredit : "0"
                );

                $temp_result[$key]["data"][] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "Saldo Akhir",
                    "coa" => "",
                    "debet" => $value2["saldo_akhir"],
                    "kredit" => ""
                );
                $temp_result[$key]["data"][] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "",
                    "coa" => "",
                    "debet" => "",
                    "kredit" => ""
                );
            }
        }

        return $temp_result;
    }

    public function arraybukubesar() {
        $temp_result = array();
        foreach ($this->_arrayBukuBesar as $value) {
            foreach ($value["periode"] as $value2) {
                $temp_result[] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "<strong>" . $value["kode"] . " " . $value2["period_name"] . " : " . $value2["period_start"] . " s/d " . $value2["period_end"] . "</strong>",
                    "coa" => "",
                    "debet" => "",
                    "kredit" => ""
                );
                $temp_result[] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "Saldo Awal",
                    "coa" => "",
                    "debet" => myFormatMoney($value2["saldo_awal"]),
                    "kredit" => ""
                );

                $nomor = 1;
                foreach ($value2["detail"] as $value3) {
//                    if (empty($value3["id_jurnal"]) || $value3["id_jurnal"] == "") {
//                        continue;
//                    } else {
                        $temp_result[] = array(
                            "nomor" => $nomor,
                            "tanggal" => $value3["tanggal"],
                            "no_bukti" => $value3["nobukti"],
                            "rekanan" => $value3["rekanan"],
                            "uraian" => $value3["keterangan"],
                            "coa" => $value3["coa_kredit"],
                            "debet" => myFormatMoney($value3["debet"]),
                            "kredit" => myFormatMoney($value3["kredit"])
                        );
//                    }
                    $nomor++;
                }
                $temp_result[] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "Sub Total",
                    "coa" => "",
                    "debet" => myFormatMoney($value2["sub_total_debet"]),
                    "kredit" => myFormatMoney($value2["sub_total_kredit"])
                );
                $temp_result[] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "Saldo Akhir",
                    "coa" => "",
                    "debet" => myFormatMoney($value2["saldo_akhir"]),
                    "kredit" => ""
                );
                $temp_result[] = array(
                    "nomor" => "",
                    "tanggal" => "",
                    "no_bukti" => "",
                    "rekanan" => "",
                    "uraian" => "",
                    "coa" => "",
                    "debet" => "",
                    "kredit" => ""
                );
            }
        }
        return $temp_result;
    }

    public function getPeriod($proyek) {
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

    public function _getSaldoAwal($id_proyek, $kodeperkiraan, $bulan, $tahun) {

        $id_proyek = !empty($id_proyek) ? $id_proyek : 0;
        $kodeperkiraan = !empty($kodeperkiraan) ? $kodeperkiraan : 0;
        $bulan = !empty($bulan) ? $bulan : 0;
        $tahun = !empty($tahun) ? $tahun : 0;

        $sql = "select
(sum(a.debet) - sum(a.kredit)) as sisa
from
(
SELECT
jur.id_proyek,
 jur.kdperkiraan,
 date_part('MONTH', jur.tanggal) as bulan,
 date_part('YEAR', jur.tanggal) as tahun,
 (CASE WHEN upper(jur.dk) = 'D' THEN SUM(rupiah) else 0 END) AS debet,
 (CASE WHEN upper(jur.dk) = 'K' THEN SUM(rupiah) else 0 END) AS kredit
FROM
tbl_jurnal jur
WHERE
date_part('MONTH', jur.tanggal) < " . $this->db->escape($bulan) . " AND
date_part('YEAR', jur.tanggal) = " . $this->db->escape($tahun) . " AND
jur.id_proyek = " . $this->db->escape($id_proyek) . " AND
jur.kdperkiraan = " . $this->db->escape($kodeperkiraan) . "
GROUP BY
jur.id_proyek,
 jur.kdperkiraan,
 jur.tanggal,
 jur.dk
) as a
group by
a.id_proyek,
 a.kdperkiraan";

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["sisa"];
        } else {
            return false;
        }
    }

}
