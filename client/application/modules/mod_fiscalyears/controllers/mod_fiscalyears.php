<?php

if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class mod_fiscalyears extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library("myauth");
        if (!$this->myauth->logged_in()) {
            if (IS_AJAX) {
                header("HTTP/1.1 401 Unauthorized");
                exit;
            } else {
                $this->session->set_userdata("redir", current_url());
                redirect("mod_user/user_auth");
            }
        }
        $this->myauth->has_role();
        $this->load->model("fiscalyears_model");
        $this->load->model("dataset_db");
        $this->load->library("searchform");
    }

    public function index() {
        $this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", "#", "form_fiscalyears_list", "cus-application", "List Fiscal Years", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_fiscalyears_new", "cus-application-form-add", "Add Fiscal Years", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "#", "form_fiscalyears_delete", "cus-application-form-delete", "Delete Fiscal Years", "tooltip", "right");
        $this->toolbar->eGroupButton();
        $data["toolbars"] = $this->toolbar->generate();

        $DataModel = array(
            array(
                "text" => "Nama Group",
                "value" => "text:LOWER(nama_group)",
                "type" => "text",
                "callBack" => "",
                "ops" => array("like", "not like", "=", "!=")
            ),
            array(
                "text" => "Keterangan",
                "value" => "text:LOWER(keterangan)",
                "type" => "text",
                "callBack" => "",
                "ops" => array("like", "not like", "=", "!=")
            )
        );

        $defaultvalue = array();

        $data["searchform"] = $this->searchform->setMultiSearch("true")->setDataModel($DataModel)->setDefaultValue($defaultvalue)->genSearchForm();
        $data["ptitle"] = "Fiscal Years";
        $data["navs"] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata("tabs");
        if (!$tabs)
            $tabs = array();
        $tabs["mod_fiscalyears"] = $this->dataset_db->getModule("mod_fiscalyears");
        $this->session->set_userdata("tabs", $tabs);
        $data["current_tab"] = $tabs["mod_fiscalyears"]["link"];
        $data["content"] = $this->load->view("fiscalyears_list", $data, true);
        $this->load->vars($data);
        $this->load->view("default_view");
    }

    public function fiscalyears_list_json() {
        $page = $this->input->post("page");
        $limit = $this->input->post("rows");
        $sidx = $this->input->post("sidx");
        $sord = $this->input->post("sord");

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 10;
        $sidx = !empty($sidx) ? $sidx : "date(yearperiod_start)";
        $sord = !empty($sord) ? $sord : "asc";

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;

        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata("ba_user_id"));
        $query = $this->perioda_model->getAllYear($limit, $offset, $sidx, $sord, $userconfig["kolom2"]);
        $count = $this->perioda_model->countAll();

        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }

        if ($page > $total_pages)
            $page = $total_pages;

        $responce["page"] = $page;
        $responce["total"] = $total_pages;
        $responce["records"] = $count;
        $i = 0;
        foreach ($query as $row) {
            $responce["rows"][$i]["id"] = $row["yearperiod_id"];
            $responce["rows"][$i]["cell"] = array(
                "",
                $row["yearperiod_id"],
                $row["yearperiod_key"],
                $row["id_proyek"],
                $row["nama_kategoriproyek"],
                $row["proyek"],
                $row["yearperiod_start"],
                $row["yearperiod_end"],
                $row["yearperiod_closed"]
            );
            $i++;
        }

        echo json_encode($responce);
    }

    public function fiscalyears_form_add() {
        $data["content"] = $this->load->view("fiscalyears_add", "", true);
        $this->load->vars($data);
        $this->load->view("default_picker");
    }

    public function fiscalyears_add() {
        
    }

    public function fiscalyears_view() {
        $data["content"] = $this->load->view("fiscalyears_view", "", true);
        $this->load->vars($data);
        $this->load->view("default_picker");
    }

    public function fiscalyears_edit() {
        
    }

    public function fiscalyears_delete() {
        
    }

}
