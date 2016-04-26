<?php

class search_form {

    public $_seminggu = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
    public $_nama_bln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

    public function __construct() {
        $CI = & get_instance();
        $this->db = & $CI->db;
    }

    public function searchForm($params = array()) {
        if (!is_array($params))
            $params = array();
        $_cols = "";
        $_ops = "";
        $arr_options = "";

        foreach ($params as $k => $v) {
            $_cols .= "<option value='" . $k . "' >" . $v['title'] . "</option>";
            if (is_array($v['ops'])) {
                $tmp = '[';
                foreach ($v['ops'] as $kk => $vv) {
                    if ($tmp != '[')
                        $tmp .= ',';
                    $tmp .= "\"$vv\"";
                }
                $arr_options .= "myopt['{$k}'] = $tmp];\n";
            }
        }
        //echo $arr_options;
        $cols = $_cols;
        $kolom = 1;

        $html = '<div class="row-fluid">';
        $html .='<div class="span12">';
        $html .='<div class="box">';
        $html .='<div class="box_title"><h4><span>Search Form</span></h4></div>';
        $html .='<div class = "box_content">';
        for ($x = 1; $x <= 6; $x++) {
            $cek = $kolom % 2;

            if ($cek != 0) {
                $html .='<div class = "row-fluid">';
                $html .='<div class = "span6">';
                $html .='<div class = "row-fluid">';
                $html .= "<div class=\"span5\"><select name='cols[]' onchange='select_options(this," . $x . ")' class='span12 cols_cari' id='col" . $x . "'><option value=0 selected></option>" . $cols . "</select></div>";
                $html .= "<div class=\"span2\"><select name='ops[]' id='ops" . $x . "' class='span12 ops_cari'>" . $_ops . "<option value=0></option></select></div>";
                $html .= "<div class=\"span5\" id='td_val" . $x . "'><input name='vals[]' id='vals" . $x . "' type='text' class='span12 text'/></div>";
                $html .='</div>';
                $html .='</div>';
//                $html .='</div>';
            } else {
//                $html .='<div class = "row-fluid">';
                $html .='<div class = "span6">';
                $html .='<div class = "row-fluid">';
                $html .= "<div class=\"span5\"><select name='cols[]' onchange='select_option(this," . $x . ")' class='span12 cols_cari' id='col" . $x . "'><option value=0 selected></option>" . $cols . "</select></div>";
                $html .= "<div class=\"span2\"><select name='ops[]' id='ops" . $x . "' class='span12 ops_cari'>" . $_ops . "<option value=0></option></select></div>";
                $html .= "<div class=\"span5\" id='td_val" . $x . "'><input name='vals[]' id='vals" . $x . "' type='text' class='span12 text'/></div>";
                $html .='</div>';
                $html .='</div>';
                $html .='</div>';
            }

            $kolom++;
        }
        $html .='<div class = "row-fluid">';
        $html .= "<button type=\"button\" id='button_search' class=\"btn btn-primary \"><i class=\"icon-search icon-white\"></i> Search</button>";
        $html .= "&nbsp;<button type=\"button\" id='reset_search' class=\"btn btn-success\"><i class=\"icon-refresh icon-white\"></i> Reset</button>";
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';
        return $html;
    }

    public function searchFormNew($params = array()) {
        if (!is_array($params))
            $params = array();
        $_cols = "";
        $_ops = "";
        $arr_options = "";

        foreach ($params as $k => $v) {
            $_cols .= "<option value='" . $k . "' >" . $v['title'] . "</option>";
            if (is_array($v['ops'])) {
                $tmp = '[';
                foreach ($v['ops'] as $kk => $vv) {
                    if ($tmp != '[')
                        $tmp .= ',';
                    $tmp .= "\"$vv\"";
                }
                $arr_options .= "myopt['{$k}'] = $tmp];\n";
            }
        }
        //echo $arr_options;
        $cols = $_cols;
        $kolom = 1;

        $html = '<div class="bs-docs-example">';
        //$html .='<div class="row-fluid">';

        $html .='<div class="templatesearch" style="display: none">';
        $html .='<div class="row-fluid span12">';
        $html .='<div class="row-fluid span12">';
        $html .='<select disabled class="input-mini form_search_cols" id="cols" name="cols[]" onchange=\'select_options(this, $(this).parents(".xsearch").attr("id"))\'>';
        $html .= '<option></option>';
        $html .= $cols;
        $html .='</select>&nbsp;';
        $html .='<select class="input-mini span1" id="ops" name="ops[]"></select>&nbsp;';
        $html .='<span id="td_val"><input disabled class="input-mini" id="vals" name="vals[]" type="text"/></span>&nbsp;';
        $html .='<a href="#" class="btn" onclick="search_remove(this);"><i class="icon-remove"></i></a>';
        $html .='</div>';
        $html .='</div>';
        $html .='</div>';

        $html .='<div id="1" class="row-fluid xsearch">';
        $html .='<div class="row-fluid span12">';
        $html .='<select class="input-mini form_search_cols" id="cols" name="cols[]" onchange=\'select_options(this, $(this).parents(".xsearch").attr("id"))\'>';
        $html .= '<option></option>';
        $html .= $cols;
        $html .='</select>&nbsp;';
        $html .='<select class="input-mini span1" id="ops1" name="ops[]"></select>&nbsp;';
        $html .='<span id="td_val1"><input class="input-mini" id="vals1" name="vals[]" type="text"/></span>&nbsp;';
        $html .='<a href="#" class="btn" onclick="search_add();"><i class="icon-plus"></i></a>';
        $html .='</div>';
        $html .='</div>';

        $html .='<div class="topsearchadd"></div>';

        $html .='<div class = "row-fluid">';
        $html .= "<button type=\"button\" id='button_search' class=\"btn btn-primary \"><i class=\"icon-search icon-white\"></i> Search</button>";
        $html .= "&nbsp;<button type=\"button\" id='reset_search' class=\"btn btn-warning\"><i class=\"icon-refresh icon-white\"></i> Reset</button>";
        $html .='</div>';
        $html .='</div>';
        //$html .='</div>';
        return $html;
    }

    public function toolbars($params = array(), $perBlock = 3) {
        if (!is_array($params))
            $params = array();


//        $toolbar_config = array(
//            'item_new' => base_url() . $this->_moduleName . '/new_group',
//            'item_delete' => '#',
//            'item_edit' => '#',
//            'table' => base_url() . $this->_moduleName,
//            'item_cancel' => '#',
//            'item_save' => '#',
//            'item_undo' => '#',
//            'item_find' => '#',
//            'item_first' => '#',
//            'item_prev' => '#',
//            'item_next' => '#',
//            'item_last' => '#',
//            'item_print' => '#',
//            'record' => '#',
//            'item_affected' => '#',
//            'item_approve' => '#',
//            'item_disapprove' => '#',
//            'item_ref' => '#',
//            'export_xls' => '#',
//            'export_pdf' => '#'
//            'item_served' => '#'
//        );

        $html = "<div class=\"toolbar\">";
        $no = 1;
        $counter = 1;
        foreach ($params as $k => $v) {

            if ($no == 1) {
                if ($counter == count($params)) {
                    $html .= "\n\t<a href=\"" . $v . "\"><span><b class=\"" . $k . "\"></b></span></a>";
                } else {
                    $html .= "\n\t<a href=\"" . $v . "\" class=\"nleft\"><span><b class=\"" . $k . "\"></b></span></a>";
                }
            } elseif ($no >= $perBlock) {
                if ($counter == count($params)) {
                    $html .= "\n\t<a href=\"" . $v . "\"><span><b class=\"" . $k . "\"></b></span></a>";
                } else {
                    $html .= "\n\t<a href=\"" . $v . "\" class=\"nright\"><span><b class=\"" . $k . "\"></b></span></a>";
                }
                $no = 0;
            } else {
                if ($counter == count($params)) {
                    $html .= "\n\t<a href=\"" . $v . "\"><span><b class=\"" . $k . "\"></b></span></a>";
                } else {
                    $html .= "\n\t<a href=\"" . $v . "\" class=\"nmid\"><span><b class=\"" . $k . "\"></b></span></a>";
                }
            }
            $no++;
            $counter++;
        }
        $html .= "</div>";
        return $html;
    }

    public function getBulan() {
        return $this->_nama_bln;
    }

    public function getTahun() {
        $now = date("Y");
        $temp = array();
        for ($x = $now; $x >= $now - 10; $x--) {
            $temp[$x] = $x;
        }

        return $temp;
    }

    public function myFormatMoney($amount, $prefix = "") {
        if ($prefix <> "" and !empty($prefix)) {
            $prefix = $prefix;
        } else {
            $prefix = "";
        }

        if ($amount < 0)
            return '(' . $prefix . ' ' . number_format(abs($amount), 2, ",", ".") . ')';
        return $prefix . ' ' . number_format($amount, 2, ",", ".");
    }

    public function toolbar($params = array()) {
        if (!is_array($params))
            $params = array();

        $html = '<div style="margin: 0;" class="btn-toolbar">';
        foreach ($params as $key => $value) {
            $html .= '<div class="btn-group">';

            foreach ($value as $key => $value2) {
                $html .= '<' . (isset($value2["tag"]) ? $value2["tag"] : "a") . ' class="' . (isset($value2["class"]) ? $value2["class"] : "btn") . '" href="' . $value2["link"] . '" id="' . $key . '" ' . (isset($value2["event"]) ? $value2["event"] : "") . ' ><i class="' . $value2["icon"] . '"></i></' . (isset($value2["tag"]) ? $value2["tag"] : "a") . '>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

}
