<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mod_importdbf extends CI_Controller {

    protected $_userconfig = array();

    public function __construct() {
        parent::__construct();
        session_start();
        $this->load->library('myauth');
        if (!$this->myauth->logged_in()) {
            if (IS_AJAX) {
                header('HTTP/1.1 401 Unauthorized');
                exit;
            } else {
                $this->session->set_userdata('redir', current_url());
                redirect('mod_user/user_auth');
            }
        }
        $this->myauth->has_role();
        $this->load->model('dataset_db');
        $this->load->model('importdbf_model');
        $this->load->library('dbf_class');
        $this->load->library("searchform");
        $this->_userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
    }

    public function index() {

        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_importdbf", "form_importdbf_list", "cus-application", "List Import Data", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_importdbf/form_importdbf_add", "form_importdbf_add", "cus-application-form-add", "Add Import Data", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_importdbf_delete", "cus-application-form-delete", "Delete Import Data", "tooltip", "right");
        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();

        $DataModel = array(
            array(
                'text' => 'Nama Proyek',
                'value' => 'text:LOWER(nama_proyek)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            )
        );

        $defaultvalue = array();

        $data['searchform'] = $this->searchform->setMultiSearch("true")->setDataModel($DataModel)->setDefaultValue($defaultvalue)->genSearchForm();
        $data['ptitle'] = "Import Data";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
        $tabs['mod_importdbf'] = $this->dataset_db->getModule('mod_importdbf');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_importdbf']['link'];
        $data['content'] = $this->load->view('importdbf_list', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function importdbf_json() {

        $search = isset($_GET['_search']) ? $_GET['_search'] : 'false';
        $page = $this->input->post('page');
        $limit = $this->input->post('rows');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 10;
        $sidx = !empty($sidx) ? $sidx : "date(period_start)";
        $sord = !empty($sord) ? $sord : "asc";

        if (strtolower($search) == "true") {
            $cols = isset($_GET['cols']) ? $_GET['cols'] : '';
            $ops = isset($_GET['ops']) ? $_GET['ops'] : '';
            $vals = isset($_GET['vals']) ? $_GET['vals'] : '';

            $cari = array();
            for ($x = 0; $x < count($cols); $x++) {
                $cari[$x]['cols'] = $cols[$x];
                $cari[$x]['ops'] = $ops[$x];
                $cari[$x]['vals'] = $vals[$x];
            }
        } else {
            $cari = "";
        }

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;
        $query = $this->importdbf_model->getAll($this->_userconfig["kolom2"], $limit, $offset, $sidx, $sord, $cari, $search);
        $count = $this->importdbf_model->countAll();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages)
            $page = $total_pages;

        $responce['page'] = $page;
        $responce['total'] = $total_pages;
        $responce['records'] = $count;
        $i = 0;
        foreach ($query as $row) {
            $responce['rows'][$i]['id'] = $row['importdata_id'];
            $responce['rows'][$i]['cell'] = array(
                '<a class="tooltips" title="" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Import List" href="' . base_url() . 'mod_importdbf/importdbf_view/' . $row['importdata_id'] . '"><img src="' . base_url() . 'media/edit.png" /></a>',
                '<a class="tooltips" title="" data-placement="bottom" data-toggle="tooltip" data-original-title="Edit Import Data" href="' . base_url() . 'mod_importdbf/importdbf_editgl/' . $row['importdata_id'] . '"><img src="' . base_url() . 'media/edit.png" /></a>',
                $row['importdata_id'],
                $row['proyek'],
                $row['period_name'],
                $row['period_start'],
                $row['period_end'],
                $row['period_flag']
            );
            $i++;
        }
        echo json_encode($responce);
    }

    public function form_importdbf_add() {
        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_importdbf", "form_importdbf_list", "cus-application", "List Import Data", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_importdbf/form_importdbf_add", "form_importdbf_add", "cus-application-form-add", "Add Import Data", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_importdbf_delete", "cus-application-form-delete", "Delete Import Data", "tooltip", "right");
        $this->toolbar->eGroupButton();
        $data['toolbars'] = $this->toolbar->generate();

        $data['ptitle'] = "Import Data";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
        $tabs['mod_importdbf'] = $this->dataset_db->getModule('mod_importdbf');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_importdbf']['link'];
        $data['content'] = $this->load->view('importdbf_add', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function importdbf_add() {
        $this->form_validation->set_rules("form_importdbf_periode", "Periode", "required|xss_clean");
        $this->form_validation->set_rules("userfile", "File Upload", "xss_clean");

        if ($this->form_validation->run() == TRUE) {
            $field["importdata_id_proyek"] = $this->_userconfig["kolom2"];
            $field["importdata_periodkey"] = $this->input->post("form_importdbf_periode");
            $insert = $this->importdbf_model->insert_import($field);

            if ($insert) {
                $config['upload_path'] = 'temp/';
                $config['allowed_types'] = 'dbf';
                $config['max_size'] = '1024000';
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
//                $error = array('error' => $this->upload->display_errors());
//                print_r($error);
                    echo "gagal";
                } else {
                    $upload_data = $this->upload->data();
                    $this->dbf_class->validate($upload_data["full_path"]);
                    if ($this->dbf_class->logging() == "success") {
                        $temp_result = array();
                        $this->dbf_class->test();
                        $temp_result = $this->dbf_class->dbf2array();

                        if ((!empty($temp_result)) AND (is_array($temp_result))) {
                            $insert_data = array();
                            foreach ($temp_result as $value) {
                                $insert_data[] = array(
                                    'importdatadetail_txcode' => $value["TX_CODE"],
                                    'importdatadetail_date' => $value["DATE"],
                                    'importdatadetail_txno' => $value["TX_NO"],
                                    'importdatadetail_itno' => $value["IT_NO"],
                                    'importdatadetail_desc' => $value["DESC"],
                                    'importdatadetail_amount' => $value["AMOUNT"],
                                    'importdatadetail_dk' => "D",
                                    'importdatadetail_glcode' => $value["D_CODE"],
                                    'importdatadetail_kdperkiraan' => "",
                                    'importdatadetail_kdnasabah' => "",
                                    'importdatadetail_kdsbdaya' => "",
                                    'importdatadetail_issaved' => 'f',
                                    'importdatadetail_importdata_id' => $insert
                                );
                                $insert_data[] = array(
                                    'importdatadetail_txcode' => $value["TX_CODE"],
                                    'importdatadetail_date' => $value["DATE"],
                                    'importdatadetail_txno' => $value["TX_NO"],
                                    'importdatadetail_itno' => $value["IT_NO"],
                                    'importdatadetail_desc' => $value["DESC"],
                                    'importdatadetail_amount' => $value["AMOUNT"],
                                    'importdatadetail_dk' => "K",
                                    'importdatadetail_glcode' => $value["C_CODE"],
                                    'importdatadetail_kdperkiraan' => "",
                                    'importdatadetail_kdnasabah' => "",
                                    'importdatadetail_kdsbdaya' => "",
                                    'importdatadetail_issaved' => 'f',
                                    'importdatadetail_importdata_id' => $insert
                                );
                            }
                            $insert = $this->importdbf_model->insert($insert_data);
                            if ($insert) {
                                unset($insert_data);
                            } else {
                                die('data gagal di masukan');
                            }
                        }

                        //echo "<pre><tt>";
                        //print_r($temp_result);
                        //echo "</tt></pre>";
                        
                    redirect('mod_importdbf');
                    } else {
                        echo "gagal";
                    }
                }
            } else {
                echo "gagal";
            }
        } else {
            echo "gagal";
        }
    }

    public function importdbf_editgl($id = false) {
        try {
            if ($id) {
                if (isInteger($id)) {
                    $this->toolbar->create_toolbar();
                    $this->toolbar->cGroupButton();
                    $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_importdbf", "form_importdbf_list", "cus-application", "List Import Data", "tooltip", "right");
                    $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_importdbf/form_importdbf_add", "form_importdbf_add", "cus-application-form-add", "Add Import Data", "tooltip", "right");
                    $this->toolbar->addLink("", "btn tooltips", "#", "form_importdbf_delete", "cus-application-form-delete", "Delete Import Data", "tooltip", "right");
                    $this->toolbar->eGroupButton();
                    $this->toolbar->cGroupButton();
                    $this->toolbar->addButton("", "btn tooltips", "button", "form_importdbf_save", "form_importdbf_save", "cus-accept", "Simpan Jurnal", "tooltip", "right");
                    $this->toolbar->eGroupButton();
                    $data['toolbars'] = $this->toolbar->generate();

                    $data["id"] = $id;
                    $data['ptitle'] = "Import Data";
                    $data['navs'] = $this->dataset_db->buildNav(0);
                    $tabs = $this->session->userdata('tabs');
                    if (!$tabs)
                        $tabs = array();
                    $tabs['mod_importdbf'] = $this->dataset_db->getModule('mod_importdbf');
                    $this->session->set_userdata('tabs', $tabs);
                    $data['current_tab'] = $tabs['mod_importdbf']['link'];
                    $data['content'] = $this->load->view('test', $data, true);
                    $this->load->vars($data);
                    $this->load->view('default_view');
                } else {
                    redirect('forbidden');
                }
            } else {
                redirect('forbidden');
            }
        } catch (Exception $ex) {
            redirect('forbidden');
        }
    }

    public function importdbf_datajson($id = false) {
        $search = isset($_GET['_search']) ? $_GET['_search'] : 'false';
        $page = $this->input->post('page');
        $limit = $this->input->post('rows');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 10;
        $sidx = !empty($sidx) ? $sidx : "importdatadetail_id";
        $sord = !empty($sord) ? $sord : "asc";

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;


        $this->importdbf_model->getAllData($this->_userconfig["kolom2"], $id, $limit, $offset, $sidx, $sord);
        $count = $this->importdbf_model->countAll();
        $data = $this->importdbf_model->formaterArray();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages)
            $page = $total_pages;
        $responce['page'] = $page;
        $responce['total'] = $total_pages;
        $responce['records'] = $count;
        $i = 0;
        foreach ($data as $row) {
            $responce['rows'][$i]['id'] = $row['id'];
            $responce['rows'][$i]['cell'] = array(
                $row['id'],
                $row['check'],
                $row['txno'],
                $row['date'],
                $row['txcode'],
                $row['itno'],
                $row['desc'],
                $row['glcode'],
                $row['dperkir_id'],
                $row['kode'],
                $row['kdperkiraan'],
                $row['kdnasabah'],
                $row['kdsbdaya'],
                $row['debet'],
                $row['kredit']
            );
            $i++;
        }
        echo json_encode($responce);
    }

    public function importdbf_edit() {
        $this->form_validation->set_rules("id", "ID", "required|xss_clean");
        $this->form_validation->set_rules("name", "Name", "required|xss_clean");
        $this->form_validation->set_rules("val", "Nilai", "xss_clean");

        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post("id");
            $name = $this->input->post("name");

            switch ($name) {
                case "kdperkiraan":
                    $field["importdatadetail_kdperkiraan"] = $this->input->post("val");
                    $field["importdatadetail_dperkir_id"] = $this->input->post("val_id");
                    break;
                case "kdnasabah":
                    $field["importdatadetail_kdnasabah"] = $this->input->post("val");
                    break;
                default:
                    $field["importdatadetail_kdsbdaya"] = $this->input->post("val");
                    break;
            }

            $update = $this->importdbf_model->update($field, $id);
            if ($update) {
                $data['success'] = "<p>Data Berhasil Dimasukan</p>";
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            } else {
                $data['error'] = "<p>Data Gagal Dimasukan</p>";
                $json['json'] = $data;
                $this->load->view('template/ajax', $json);
            }
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function cek_kodeperkiraan($val) {
        if (!$this->importdbf_model->cek_kodeperkiraan($val)) {
            $this->form_validation->set_message("cek_kodeperkiraan", "Harap Inputkan Kode Perkiraan Yang Valid.");
            return false;
        } else {
            return true;
        }
    }

    public function importdbf_save() {
        $this->form_validation->set_rules("id", "ID", "required|xss_clean");
        $this->form_validation->set_rules('jq_checkbox_added', 'Jurnal', 'required');

        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post("id");
            $nomor_bukti = $this->input->post("jq_checkbox_added");
            $this->importdbf_model->updateSaved($nomor_bukti);
            $data = $this->importdbf_model->getsavejurnal($this->_userconfig["kolom2"], $id, $nomor_bukti);
			//echo "<pre>";
			//print_r($data);
            $jurnal = array();
            if (!empty($data) AND is_array($data)) {
                foreach ($data as $row) {
                    $nobukti = $this->importdbf_model->getNobukti($row["date"], $this->_userconfig["kolom2"], $row["jenis"]);

                    foreach ($row["detail"] as $row1) {
                        $gid = $this->importdbf_model->getGid($this->_userconfig["kolom2"]);
                        foreach ($row1 as $row2) {
                            $jurnal[] = array(
                                'tanggal' => $row["date"],
                                'nobukti' => $nobukti,
								'dperkir_id' => $row2["dperkir_id"],
                                'id_proyek' => $this->_userconfig["kolom2"],
                                'kdnasabah' => $row2["kdnasabah"],
                                'keterangan' => $row2["desc"],
                                'dk' => $row2["dk"],
                                'rupiah' => ($row2["dk"] == "D" ) ? $row2["amount"] : $row2["amount"] * -1,
                                'create_id' => $this->session->userdata('ba_user_id'),
                                'create_time' => $this->myauth->timestampIndo(),
                                'gid' => $gid,
                                'tempjurnal_jenisjurnal_id' => $row["txcode"],
                                'no_dokumen'	=> $row["txno"]
                            );
                        }
                    }
                }

                $this->importdbf_model->savetotempjurnal($jurnal);
            }
            $data['success'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        } else {
            $data['error'] = validation_errors();
            $json['json'] = $data;
            $this->load->view('template/ajax', $json);
        }
    }

    public function test($id = false) {
        try {
            if ($id) {
                if (isInteger($id)) {
                    $param = array(
                        array(
                            'form_importdbf_list' => array(
                                'tag' => 'a',
                                'class' => 'btn',
                                'link' => base_url() . 'mod_importdbf',
                                'event' => '',
                                'icon' => 'cus-application'
                            ),
                            'form_importdbf_new' => array(
                                'tag' => 'a',
                                'class' => 'btn',
                                'link' => base_url() . 'mod_importdbf/form_importdbf_add',
                                'event' => '',
                                'icon' => 'cus-application-form-add'
                            ),
                            'form_importdbf_delete' => array(
                                'tag' => 'a',
                                'class' => 'btn',
                                'link' => '#',
                                'event' => '',
                                'icon' => 'cus-application-form-delete'
                            )
                        )
                    );
                    $data["id"] = $id;
                    $data['toolbars'] = $this->search_form->toolbar($param);
                    $data['ptitle'] = "Import Data";
                    $data['navs'] = $this->dataset_db->buildNav(0);
                    $tabs = $this->session->userdata('tabs');
                    if (!$tabs)
                        $tabs = array();
                    $tabs['mod_importdbf'] = $this->dataset_db->getModule('mod_importdbf');
                    $this->session->set_userdata('tabs', $tabs);
                    $data['current_tab'] = $tabs['mod_importdbf']['link'];
                    $data['content'] = $this->load->view('importdbf_editdata', $data, true);
                    $this->load->vars($data);
                    $this->load->view('default_view');
                } else {
                    redirect('forbidden');
                }
            } else {
                redirect('forbidden');
            }
        } catch (Exception $ex) {
            redirect('forbidden');
        }
    }

    public function getSession() {
        $session = $this->session->all_userdata();

        echo "<pre>";
        print_r($session);
    }

}
