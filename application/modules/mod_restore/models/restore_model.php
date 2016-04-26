<?php

class restore_model extends CI_Model {

    protected $_table;
    protected $_countAll;

    public function __construct() {
        parent::__construct();
        $this->config->load('database_tables', TRUE);
        $this->_table = $this->config->item('database_tables');
    }

    public function InsertData($table, $data){
		$this->db->insert_batch($table, $data);
	}

}
