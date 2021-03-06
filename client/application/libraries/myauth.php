<?php

class myauth {

    public $_date_format;
    public $_encryption_key;
    public $_error_delim_prefix = '<p>';
    public $_error_delim_suffix = '</p>';
    public $_cookie_elem_prefix = 'ba_';
    private $_error;

    public function __construct() {
        //session_start();
        $this->_assign_libraries();
        $this->_date_format = $this->config->item('date_format', 'bitauth');
        $this->_encryption_key = $this->config->item('encryption_key');
    }

    public function _assign_libraries() {
        if ($CI = & get_instance()) {
            $this->input = $CI->input;
            $this->load = $CI->load;
            $this->config = $CI->config;
            $this->lang = $CI->lang;
			
			$this->dataset_db = $this->load->model('dataset_db');
			
            $this->load->helper('url');
            $this->uri = $CI->uri;

            $CI->load->library('session');
            $this->session = $CI->session;

            $CI->load->library('encrypt');
            $this->encrypt = $CI->encrypt;

            $this->load->database();
            $this->db = $CI->db;

            $this->lang->load('bitauth');
            $this->load->config('bitauth', TRUE);


            return;
        }
        log_message('error', $this->lang->line('bitauth_instance_na'));
        show_error($this->lang->line('bitauth_instance_na'));
    }

    public function get_users() {

        $this->db->select("*");
        $this->db->from("tbl_users_v");
        $this->db->where('tbl_users_v.enabled', "t");
        $query = $this->db->get();

        if ($query && $query->num_rows()) {

            $temp_result = array();
            foreach ($query->result_array() as $row) {
                if (!empty($row['hak_akses']) AND !empty($row['hak_data'])) {
                    $roles_akses = explode(';', $row['hak_akses']);
                    $roles_data = explode(';', $row['hak_data']);
                }
                $temp_result[] = array(
                    'user_id' => $row['user_id'],
                    'userdata_id' => $row['userdata_id'],
                    'id_group' => $row['id_group'],
                    'is_proyek' => $row['is_proyek'],
                    'id_relasi' => $row['id_relasi'],
                    'username' => $row['username'],
                    'password' => $row['password'],
                    'password_last_set' => $row['password_last_set'],
                    'password_never_expired' => $row['password_never_expired'],
                    'remember_me' => $row['remember_me'],
                    'activation_code' => $row['activation_code'],
                    'active' => $row['active'],
                    'forgot_code' => $row['forgot_code'],
                    'forgot_generated' => $row['forgot_generated'],
                    'enabled' => $row['enabled'],
                    'last_login' => $row['last_login'],
                    'last_login_ip' => $row['last_login_ip'],
                    'created' => $row['created'],
                    'fullname' => $row['fullname'],
                    'email' => $row['email'],
                    'nama_group' => $row['nama_group'],
                    'unit_kerja' => $row['unit_kerja'],
                    'hak_data' => json_encode($roles_data),
                    'hak_akses' => json_encode($roles_akses)
                );
            }
            return $temp_result;
        }
        return FALSE;
    }

    public function get_user_by_username($username) {
        $this->db->where('tbl_users_v.username', $username);
        $this->db->limit(1, 0);
        $users = $this->get_users();

        if (is_array($users) && !empty($users)) {
			//print_r($users);
            return $users[0];
        }
        return FALSE;
    }

    public function CheckPassword($passIn, $passDb) {
        $password = $this->encrypt->sha1($passIn . $this->config->item('encryption_key'));
        if ($password == $passDb) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function login($username, $password) {
        if (empty($username)) {
            $this->set_error($this->lang->line('bitauth_username_required'));
            return FALSE;
        }
        if (empty($password)) {
            $this->set_error($this->lang->line('bitauth_username_required'));
            return FALSE;
        }
		
        $params = array('username' => $username);
        $user = $this->get_user_by_username($username);
        if ($user !== FALSE) {
            if ($this->CheckPassword($password, $user["password"]) || ($password === NULL)) {
                $data = array(
                    'last_login' => $this->timestamp(),
                    'last_login_ip' => ip2long($_SERVER['REMOTE_ADDR'])
                );
                $this->set_session_values($user);
                $this->update_user($user["user_id"], $data);
                $this->dataset_db->insert_logs(
												array(
													'log_username' => $username,
													'log_node' => $_SERVER['REMOTE_ADDR'],
													'log_description' => 'User berhasil melakukan login',
													'log_params' => json_encode($params)
													 )
											  );
                return TRUE;
            }
        }

        $this->set_error(sprintf($this->lang->line('bitauth_login_failed'), $this->lang->line('bitauth_username')));
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $username,
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User gagal melakukan login',
											'log_params' => json_encode($params)
											 )
									  );
        return FALSE;
    }

    public function logged_in() {
		return (bool) $this->session->userdata($this->_cookie_elem_prefix . 'username');
		//return (bool) $_SESSION["user_data"][$this->_cookie_elem_prefix . 'username'];
		//die(print_r($this->session->userdata));
		//die(print_r($_SESSION["user_data"]));
    }

    public function getPrefix() {
        return $this->_cookie_elem_prefix;
    }
    
    public function set_session_values($values) {
        $session_data = array();
        $data_for_session = array(
            "user_id",
            "userdata_id",
            "username",
            "id_group",
            "is_proyek",
            "id_relasi",
            "nama_group",
            "unit_kerja",
            "hak_data",
            "hak_akses"
        );
        //$_SESSION["user_data"] = array();
        foreach ($values as $_key => $_value) {
            if (in_array($_key, $data_for_session)) {
				if ($_key !== 'password') {
					$this->$_key = $_value;
					if ($_key == 'roles') {
						$_value = $this->encrypt->encode($_value);
					}
					$session_data[$this->_cookie_elem_prefix . $_key] = $_value;
				}
			}
        }
        //$_SESSION["user_data"] = $session_data;
        $this->session->set_userdata($session_data);
		//die(print_r($this->session->userdata));
    }

    public function get_session_values() {
        $session_data = $this->session->all_userdata();
        //$session_data = $_SESSION["user_data"];
        foreach ($session_data as $_key => $_value) {
            if (substr($_key, 0, strlen($this->_cookie_elem_prefix)) !== $this->_cookie_elem_prefix) {
                continue;
            }

            $_key = substr($_key, strlen($this->_cookie_elem_prefix));

            if (!isset($this->$_key)) {
                if ($_key == 'roles') {
                    $_value = $this->encrypt->decode($_value);
                }

                $this->$_key = $_value;
            } else {
                log_message('error', sprintf($this->lang->line('bitauth_data_error'), $_key));
                show_error(sprintf($this->lang->line('bitauth_data_error'), $_key));
            }
        }
    }

    public function set_error($str, $update_session = TRUE) {
        $this->_error = $str;

        if ($update_session == TRUE) {
            $this->session->set_flashdata('bitauth_error', $this->_error);
        }
    }

    public function get_error($incl_delim = TRUE) {
        if ($incl_delim) {
            return $this->_error_delim_prefix . $this->_error . $this->_error_delim_suffix;
        }

        return $this->_error;
    }

    public function set_error_delimiters($prefix, $suffix) {
        $this->_error_delim_prefix = $prefix;
        $this->_error_delim_suffix = $suffix;
    }

    public function has_role() {

        $user = $this->get_user_by_username($this->session->userdata($this->_cookie_elem_prefix . "username"));
        //$user = $this->get_user_by_username($_SESSION["user_data"][$this->_cookie_elem_prefix . "username"]);

        if (!in_array($this->_getIdModule(), json_decode($user["hak_akses"]))) {
            redirect('forbidden');
        }
    }

    public function _getIdModule() {
        $method = $this->uri->rsegment(1);
        $query = $this->db
                ->select("tbl_modules.id_modules")
                ->where("lower(tbl_modules.link)", strtolower($method))
                ->limit(1, 0)
                ->get("tbl_modules");

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['id_modules'];
        } else {
            return array();
        }
    }

    public function update_user($id, $data) {
        if (!is_array($data) && !is_object($data)) {
            $this->set_error($this->lang->line('bitauth_edit_user_datatype'));
            return FALSE;
        }

        $data = (array) $data;
        $this->db->where("user_id", $id);
        $this->db->update("tbl_user", $data);

        return TRUE;
    }

    public function timestamp($time = NULL, $format = NULL) {
        if ($time === NULL) {
            $time = time();
        }
        if ($format === NULL) {
            $format = $this->_date_format;
        }

        if ($this->config->item('time_reference') == 'local') {
            return date($format, $time);
        }
        return gmdate($format, $time);
    }

    public function timestampIndo() {
        $timezone = "Asia/Jakarta";
        if (function_exists('date_default_timezone_set'))
            date_default_timezone_set($timezone);
        $bla = date("Y-m-d H:i:s");
        return $bla;
    }


}
