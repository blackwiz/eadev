<?php

if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class fiscalyears_model extends CI_Model {

    protected $_table;
    protected $_countAll;

    public function __construct() {
        parent::__construct();
        $this->config->load("database_tables", TRUE);
        $this->_table = $this->config->item("database_tables");
    }

}
