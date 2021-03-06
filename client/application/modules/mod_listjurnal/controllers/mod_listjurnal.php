<?php

class mod_listjurnal extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('myauth');
        if (!$this->myauth->logged_in()) {
            $this->session->set_userdata('redir', current_url());
            redirect('mod_user/user_auth');
        }
        $this->myauth->has_role();
        $this->load->model('listjurnal_model');
        $this->load->model('dataset_db');
        $this->load->library('search_form');
        $this->load->library('searchform');
    }

    public function index() {
		$this->toolbar->create_toolbar();
        $this->toolbar->cGroupButton();
        $this->toolbar->addLink("", "btn tooltips", base_url() . "mod_listjurnal", "form_listjurnal_list", "cus-table", "Laporan List Jurnal", "tooltip", "right");
        $this->toolbar->addLink("", "btn tooltips", "javascript:void(0);", "form_listjurnal_excel", "cus-page-excel", "To Excel", "tooltip", "right");
        $this->toolbar->eGroupButton();
        $data['toolbar'] = $this->toolbar->generate();

		$DataModel = array(
            array(
                'text' => 'IsApproved',
                'value' => 'text:LOWER(isapprove)',
                'type' => 'custom',
                'callBack' => 'getBoolean',
                'ops' => array("=", "!=")
            ),
            array(
                'text' => 'Periode',
                'value' => 'text:period_id',
                'type' => 'custom',
                'callBack' => 'getperiod',
                'ops' => array("=", "!=")
            ),
            array(
                'text' => 'Tanggal',
                'value' => 'date:date(tanggal)',
                'type' => 'date',
                'callBack' => '',
                'ops' => array("=", "!=", ">", ">=", "<", "<=")
            ),
            array(
                'text' => 'Keterangan',
                'value' => 'text:LOWER(keterangan)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            ),
            array(
                'text' => 'Nomor Bukti',
                'value' => 'text:LOWER(nobukti)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            ),
            array(
                'text' => 'Nomor Referensi',
                'value' => 'text:LOWER(no_dokumen)',
                'type' => 'text',
                'callBack' => '',
                'ops' => array("like", "not like", "=", "!=")
            )
        );

        $defaultvalue = array(
            array(
                'text' => 'Periode',
                'value' => 'text:period_id',
                'defvalue' => $this->site_library->getPeriodeId(date("Y-m-d")),
                'type' => 'custom',
                'callBack' => 'getperiod',
                'ops' => array("=")
            ),
            array(
                'text' => 'IsApproved',
                'value' => 'text:LOWER(isapprove)',
                'defvalue' => 'false',
                'type' => 'custom',
                'callBack' => 'getBoolean',
                'ops' => array("=")
            )
        );

        $data['searchform'] = $this->searchform->setMultiSearch("true")->setDataModel($DataModel)->setDefaultValue($defaultvalue)->genSearchForm();
        $data['ptitle'] = "List Jurnal";
        $data['navs'] = $this->dataset_db->buildNav(0);
        $tabs = $this->session->userdata('tabs');
        if (!$tabs)
            $tabs = array();
		
		$data['unitkerja'] = $this->dataset_db->getSubUnitkerja();
		$data['kode_proyek'] = $this->dataset_db->getDataProyek();
        $tabs['mod_listjurnal'] = $this->dataset_db->getModule('mod_listjurnal');
        $this->session->set_userdata('tabs', $tabs);
        $data['current_tab'] = $tabs['mod_listjurnal']['link'];
        $data['content'] = $this->load->view('listjurnal_list', $data, true);
        $this->load->vars($data);
        $this->load->view('default_view');
    }

    public function listjurnal_json() {
        $search = isset($_GET['_search']) ? $_GET['_search'] : 'false';
        $page = $this->input->post('page');
        $limit = $this->input->post('rows');
        $sidx = $this->input->post('sidx');
        $sord = $this->input->post('sord');

        $page = !empty($page) ? $page : 1;
        $limit = !empty($limit) ? $limit : 10;
        $sidx = !empty($sidx) ? $sidx : "no_dokumen";
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
            $cari = array(
                array(
                    'cols' => 'text:LOWER(isapprove)',
                    'ops' => '=',
                    'vals' => 'false'
                ),
                array(
                    'cols' => 'text:period_id',
                    'ops' => '=',
                    'vals' => $this->site_library->getPeriodeId(date("Y-m-d"))
                )
            );
        }

        $offset = ($page * $limit) - $limit;
        $offset = ($offset < 0) ? 0 : $offset;

        if (!$sidx)
            $sidx = 1;
        $query = $this->listjurnal_model->getAll($limit, $offset, $sidx, $sord, $cari, $search);
        $count = $this->listjurnal_model->countAll();

		//echo "<pre>";
		//print_r($query);
		//echo "</pre>";

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
        //print_r($query);
        foreach ($query as $key => $row) {
            $responce['rows'][$i]['id'] = $row['idnya'];
            $responce['rows'][$i]['cell'] = array($row['nomor'],$row['tanggal'], $row['no_dokumen'], $row['nobukti'], $row['nama_proyek'], $row['keterangan'], $row['coa'], $row['rekanan'], $row['debit'], $row['kredit']);
            $i++;
        }
        echo json_encode($responce);
    }
	
	public function to_excel() {
        $this->load->library('export_excel');
        $database = $this->listjurnal_model->getAllForExcel();
        
        $userconfig = $this->dataset_db->getUserconfig($this->session->userdata('ba_user_id'));
		$proyek = $this->listjurnal_model->getProyekName($userconfig["kolom2"]);
		
		if ($this->input->post()) {
            $cols = $this->input->post('cols');
            $ops = $this->input->post('ops');
            $vals = $this->input->post('vals');
			
            $cari = array();
            for ($x = 0; $x < count($cols); $x++) {
                $cari[$x]['cols'] = $cols[$x];
                $cari[$x]['ops'] = $ops[$x];
                $cari[$x]['vals'] = $vals[$x];
            }
            
			//die(print_r($cari));
			$filter = array();
			$filter_count = 0;
			foreach ($cari as $row) {
				 if (!empty($row['cols']) AND !empty($row['ops']) AND !empty($row['vals'])) {
					$temp = ''; 
					$filter_count++; 
                    $fields = explode(":", $row['cols']);
                    switch ($fields[1]) {
                        case "period_id":
							$temp .= 'Periode ';
							$temp .= $row['ops'].' ';
							$temp .= $this->listjurnal_model->getPeriodName($row['vals']);
                            break;
                        case "LOWER(isapprove)":
                            $temp .= 'Is Approve ';
							$temp .= $row['ops'].' ';
							$temp .= $row['vals'];
                            break;
                        case "date(tanggal)":
                            $temp .= 'Tanggal ';
							$temp .= $row['ops'].' ';
							$temp .= $row['vals'];
                            break;
                        case "LOWER(nobukti)":
                            $temp .= 'Nomor Bukti ';
							$temp .= $row['ops'].' ';
							$temp .= $row['vals'];
                            break;
                    }
                    $filter[$filter_count] = array($temp,'','');
                }
            }
		}
		
		if ($filter_count > 0) {
			$filtering = $filter_count + 3;
		}
		
		if (count($database) > 0) {
			$result = $database;
			$last_line = count($database) + $filtering + 5;
		}
		
		//die(print_r($filter_result));
		
		$title = array(
            array('PT Brantas Abipraya','',''),
            array($proyek,'',''),
            array('List Jurnal ','','')
        );
		
		//die(print_r($title));
		
        $header = array(
            array('No', 'Tanggal', 'No Bukti', 'Keterangan', 'COA', 'Rekanan', 'Nilai',''),
            array('','','','','','','Debit', 'Kredit')
        );
        
        $styleArray = array(
            'title' => array(
                'Alignment' => array(
					'Horizontal' => 'Center',
					'Vertical' => 'Center'
                ),
				'Font'	=> array(
					'Bold'	=>	'1',
					'Size'	=>	'14'
				)
			),
			'filter' => array(
                'Alignment' => array(
					'Horizontal' => 'Left',
					'Vertical' => 'Center',
					'WrapText' => '1'
                ),
				'Font'	=> array(
					'Size'	=>	'10'
				)
			),
			'header' => array(
				'Alignment' => array(
					'Horizontal' => 'Center',
					'Vertical' => 'Center'
                ), 	
				'Borders' => array(
					'All' => array(
						'LineStyle' => 'Continuous',
						'Weight' => '1',
						'Color' => '#000000'
					)
				),
				'Font'	=> array(
					'Bold'	=>	'1',
					'Size'	=>	'14'
				),
				'Interior' => array(
					'Color' => '#8DB4E2',
					'Pattern' => 'Solid'
				)
			),
			'data' => array(	
				'Borders' => array(
					'Bottom' => array(
						'Position'	=> 'Bottom',
						'LineStyle' => 'Continuous',
						'Weight' => '1',
						'Color' => '#000000'
					),
					'Left' => array(
						'Position'	=> 'Left',
						'LineStyle' => 'Continuous',
						'Weight' => '1',
						'Color' => '#000000'
					),
					'Right' => array(
						'Position'	=> 'Right',
						'LineStyle' => 'Continuous',
						'Weight' => '1',
						'Color' => '#000000'
					)
				)
			),
			'money' => array(	
				'Alignment' => array(
					'Horizontal' => 'Right',
					'Vertical' => 'Center'
                ), 			
				'Borders' => array(
					'All' => array(
						'LineStyle' => 'Continuous',
						'Weight' => '1',
						'Color' => '#000000'
					)
				),
				'NumberFormat' => array(
					'Format'	=>	'#,##0.00_);[Red]\(#,##0.00\)'
				)
			),
			'date' => array(	
				'Borders' => array(
					'All' => array(
						'LineStyle' => 'Continuous',
						'Weight' => '1',
						'Color' => '#000000'
					)
				),
				'NumberFormat' => array(
					'Format'	=>	'dd/mm/yyyy;@'
				)
			)
        );
        
        //die(print_r($database));
        
        $excel = new Export_Excel();
		$excel->filename = "ListJurnal.xls";

		$excel->setStyle($styleArray)->initialize();
		$excel->merge('A1:H1');
		$excel->merge('A2:H2');
		$excel->merge('A3:H3');
		for($i=4;$i<=$filtering;$i++){
			$excel->merge('A'.$i.':H'.$i);
		}
		$a=$filtering+1;
		$b=$filtering+2;
		$excel->merge('A'.$a.':A'.$b);
		$excel->merge('B'.$a.':B'.$b);
		$excel->merge('C'.$a.':C'.$b);
		$excel->merge('D'.$a.':D'.$b);
		$excel->merge('E'.$a.':E'.$b);
		$excel->merge('F'.$a.':F'.$b);
		$excel->merge('G'.$a.':H'.$a);
		$excel->col('A')->width('30');
		$excel->col('B')->width('70');
		$excel->col('C')->width('90');
		$excel->col('D')->width('160');
		$excel->col('E')->width('80');
		$excel->col('F')->width('150');
		$excel->col('G')->width('75');
		$excel->col('H')->width('75');
		$excel->titleSheet('List Jurnal')->startSheet();
		$excel->applyStyle('title')->addRow($title);
		$excel->applyStyle('filter')->addRow($filter);
		$excel->applyStyle('header')->addRow($header);
		$excel->applyStyle('date')->applyTo('B'.$b.':B'.$last_line);
		$excel->applyStyle('money')->applyTo('G'.$b.':H'.$last_line);
		$excel->applyStyle('data')->addRow($database);
		$excel->freeze('A'.$b)->endSheet();
		$excel->finalize();
		
        exit;
    }
	
    /*public function to_excel() {
        $this->load->library('excel');
        $database = $this->listjurnalapp_model->getAllForExcel();
		
		if (count($database) > 0) {
			$result = $database;
			$last_line = count($database) + 5;
		}

        $header = array(
            array('No', 'Tanggal', 'No Bukti', 'Proyek', 'COA', 'Rekanan', 'Keterangan', 'Debit', 'Kredit')
        );
        
        $styleHeader = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E1E0F7'),
            ),
            'font' => array(
                'bold' => true,
            )
        );
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            )
        );
		
        $this->excel->getProperties()->setTitle("List Jurnal Approved");
        $this->excel->getProperties()->setCreator("PT.Brantas Abipraya");
        $this->excel->getProperties()->setSubject("List Jurnal Approved");
        $this->excel->getProperties()->setDescription("Laporan List Jurnal Approved");
        $this->excel->getProperties()->setKeywords("List Jurnal Approved");
        $this->excel->getProperties()->setCategory("Laporan");

		$this->excel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A5:I5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A5:I6')->applyFromArray($styleHeader);

		$this->excel->getActiveSheet()->getStyle('A7:I' . $last_line)->applyFromArray($styleArray);
		$this->excel->getActiveSheet()->getStyle('H7:I' . $last_line)->getNumberFormat()->setFormatCode("[Black]#,##0.00;[Red](#,##0.00)");
		//$this->excel->getActiveSheet()->getStyle('H7:I' . $last_line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);

		$this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		$this->excel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToPage(true);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToHeight(0);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('A1:I1');
		$this->excel->getActiveSheet()->mergeCells('A2:I2');
		$this->excel->getActiveSheet()->mergeCells('A3:I3');
		$this->excel->getActiveSheet()->mergeCells('A4:I4');
		$this->excel->getActiveSheet()->mergeCells('A5:A6');
		$this->excel->getActiveSheet()->mergeCells('B5:B6');
		$this->excel->getActiveSheet()->mergeCells('C5:C6');
		$this->excel->getActiveSheet()->mergeCells('D5:D6');
		$this->excel->getActiveSheet()->mergeCells('E5:E6');
		$this->excel->getActiveSheet()->mergeCells('F5:F6');
		$this->excel->getActiveSheet()->mergeCells('G5:G6');
		$this->excel->getActiveSheet()->mergeCells('H5:H6');
		$this->excel->getActiveSheet()->mergeCells('I5:I6');
		$this->excel->getActiveSheet()->freezePane('A7');

		$this->excel->getActiveSheet()->setCellValue('A1', 'PT Brantas Abipraya');
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A2', 'List Jurnal Approved');
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$this->excel->getActiveSheet()->fromArray($header, null, 'A5');
		$this->excel->getActiveSheet()->fromArray($result, null, 'A7');
            
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="List_Jurnal_Approved"');
        $objWriter->save("php://output");
        exit;
    }*/

	public function getDataProyek() {
        $this->form_validation->set_rules("id", "id", "required|xss_clean");
        $this->form_validation->set_rules("id_proyek", "id_proyek", "required|xss_clean");
        if ($this->form_validation->run() == TRUE) {
            $id = $this->input->post("id");
            $id_proyek = $this->input->post("id_proyek");
            $proyek = $this->dataset_db->getDataProyek($id);
            foreach ($proyek as $key => $value) {
                if($id_proyek == $key) {
                    echo "<option value=\"" . $key . "\" selected>" . $value . "</option>";
                } else {
                    echo "<option value=\"" . $key . "\">" . $value . "</option>";
                }
            }
        }
    }
    
}
