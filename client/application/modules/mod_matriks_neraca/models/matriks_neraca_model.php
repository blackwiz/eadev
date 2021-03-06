<?php

class matriks_neraca_model extends CI_Model {

    protected $_table;
    protected $_countAll;

    public function __construct() {
        parent::__construct();
        $this->config->load('database_tables', TRUE);
        $this->_table = $this->config->item('database_tables');
    }

    /*
    function getAll($tahun, $bulan, $proyek, $unitkerja ) {

        $this->db->start_cache();
        $_table = $proyek > 0 ? $this->_table['view_matriks_labarugi'] : $this->_table['view_matriks_labarugi_konsolidasi'] ;
        $this->db->from($_table);
        //$this->db->from("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->select('*');
        //$query = $this->db->get("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->where('tahun', $tahun);
        $this->db->where('bulan', $bulan);
        if($proyek > 0){
			$this->db->where('proyek', $proyek);
		} else {
			$this->db->where('subunitkerja', $unitkerja);
		}
        $query = $this->db->get($_table);
        $this->_countAll = $this->db->count_all_results();
        $this->db->flush_cache();

        //return $query->result_array();
        $tmp_result = array();
		
        $i = 0;
        $j = 0;
        $sum_tot = 0;
		$sum_tot_sd = 0;
        $id_parents = '';
        $desk		=	Array(
								"<b>HARGA POKOK PRODUKSI</b>","",
								"<b>LABA (RUGI) KOTOR SEBELUM LABA (RUGI) KSO</b>",
								"<b>LABA (RUGI) KOTOR SETELAH LABA (RUGI) KSO</b>",
								"<b>LABA (RUGI) USAHA</b>",
								"<b>LABA (RUGI) SEBELUM BUNGA DAN PAJAK  (EBIT)</b>",
								"<b>LABA (RUGI) SEBELUM PAJAK (EBT)</b>"
							  );
		foreach($query->result_array() as $row){
			$i++;
			$isleaf = $row['childs'] > 0 ? FALSE : TRUE;
			$uraian = $row['parents'] == 0 ? '<b>'.$row['uraian'].'</b>' : $row['uraian'];
			
			if($row['parents'] != $id_parents && $id_parents != '' && $row['levels'] != 2){
				$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total_ini'		=> $tot,
					'total_sd'		=> $tot_sd,
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
				
				if($idj == 'J' || $idj == 'N'){
					$sum_tot += $tot;
					$sum_tot_sd += $tot_sd;
				} else {
					$sum_tot -= $tot;
					$sum_tot_sd -= $tot_sd;
				}
				
				if($desk[$j] != ''){
					$tmp_result[] = array(
						'id' 			=> 'sub-'.$idj,
						'uraian' 		=> $desk[$j],
						'total_ini'		=> $sum_tot,
						'total_sd'		=> $sum_tot_sd,
						'level'			=> 0,
						'parent' 		=> 0,
						'isLeaf' 		=> TRUE,
						'expanded' 		=> TRUE,
						'loaded' 		=> TRUE
					);
				}
				$j++;
			}
			
			if($row['parents'] == 0){
				$id_parents = $row['id'];
				$strsum = '<b>Jumlah '.$row['uraian'].'</b>';
				$tot = 0;
				$tot_sd = 0;
				$idj = substr($row['nmlama'],0,1);
			}
			
			if($idj == 'J'){
				$cur = abs($row['bulan_ini']);
				$sum = abs($row['sd_bulan_ini']);
			} else {
				$cur = $row['bulan_ini'];
				$sum = $row['sd_bulan_ini'];
			}
			
			$tmp_result[] = array(
				'id' 			=> $row['id'],
				'uraian' 		=> $uraian,
				'total_ini'		=> $cur,
				'total_sd'		=> $sum,
				'level'			=> $row['levels'],
				'parent' 		=> $row['parents'],
				'isLeaf' 		=> $isleaf,
				'expanded' 		=> TRUE,
				'loaded' 		=> TRUE
			);
			
			if($row['levels'] != 2){
				$tot += $cur;
				$tot_sd += $sum;
			}
		}
		$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total_ini'		=> $tot,
					'total_sd'		=> $tot_sd,
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
		$tmp_result[] = array(
					'id' 			=> 'sub'.$idj,
					'uraian' 		=> '<b>LABA (RUGI) BERSIH SETELAH PAJAK (EAT)</b>',
					'total_ini'		=> $sum_tot,
					'total_sd'		=> $sum_tot_sd,
					'level'			=> 0,
					'parent' 		=> 0,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
        return $tmp_result;

    } 
    */
	
	function getAll($periode,$unitkerja,$proyek) {

        $this->db->start_cache();
        //$_table = $proyek > 0 ? $this->_table['view_matriks_neraca'] : $this->_table['view_matriks_neraca_konsolidasi'] ;
        $_table = $this->_table['view_matriks_neraca'];
        $this->db->from($_table);
        //$this->db->from("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->select('*');
        //$query = $this->db->get("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->where('id_periode', $periode);
        $this->db->where('proyek', $proyek);
        /*if($proyek > 0){
			$this->db->where('proyek', $proyek);
		} else {
			$this->db->where('subunitkerja', $unitkerja);
		}*/
        $query = $this->db->get($_table);
        $this->_countAll = $this->db->count_all_results();
        $this->db->flush_cache();

        //return $query->result_array();
        $tmp_result = array();
		
        $i = 0;
        $j = 0;
        $sum_tot = 0;
		$sum_tot_sd = 0;
        $id_parents = '';
        
        $ktProject	=	$proyek > 0 && in_array($unitkerja,Array("1","6","7","8","9","10"))? "PUSAT" : "" ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("5"))?	"PERALATAN"	: $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("2","3"))?	"PRODUKSI" : $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("4"))?	"KHUSUS" : $ktProject ;
		
		switch ($ktProject) {
			
			case	"PUSAT"		:	
					$isi_lap	=	Array("A","B","C","D","E","F","G","H","I");
					$tdk_tampil	=	Array("A03","A04","A05","A07","F04","G03");
					$kata2		=	Array("","","","","<b>JUMLAH ASET</b>",
										  "","","","");
			break;
					
			case	"PERALATAN"		:	
					$isi_lap	=	Array("A","B","F","G","H","I");
					$tdk_tampil	=	Array("A02","A04","A11","B03","F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
					$kata2		=	Array("","","","","","","","","");
			break;
					
			case	"PRODUKSI"		:	
					$isi_lap	=	Array("A","B","F","G","H","I");
					$tdk_tampil	=	Array("A02","A04","A11","B03","F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
					$kata2		=	Array("","","","","","","","","");
			break;
					
			case	"KHUSUS"		:	
					$isi_lap	=	Array("A","B","F","G","H","I");
					$tdk_tampil	=	Array("A02","A04","A11","B03","F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
					$kata2		=	Array("","","","","","","","","");
			break;	
			
			default	: $isi_lap		=	Array("A","B","C","D","E","F","G","H","I");
					  $tdk_tampil	=	Array("");
					  $kata2		=	Array("","","","","<b>JUMLAH ASET</b>",
										  "","","","");
			break;	
		}					  
		/*echo $proyek;
		echo $ktProject;
		print_r($kata2);*/
		foreach($query->result_array() as $row){
			$i++;
			$isleaf = $row['childs'] > 0 ? FALSE : TRUE;
			$uraian = $row['parents'] == 0 ? '<b>'.$row['uraian'].'</b>' : $row['uraian'];
			
			if($row['parents'] != $id_parents && $id_parents != '' && in_array($idj,$isi_lap) && $row['levels'] != 2){
				$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total'			=> myFormatMoney($total),
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
			}
			
			if($row['parents'] != $id_parents && $id_parents != '' && $row['levels'] != 2){	
				if($idj == 'F'){
					$sum_tot = 0;
				} else {
					$sum_tot += $total;
				}
				
				if($kata2[$j] != ''){
					$tmp_result[] = array(
						'id' 			=> 'sub-'.$idj,
						'uraian' 		=> $kata2[$j],
						'total'			=> myFormatMoney($sum_tot),
						'level'			=> 0,
						'parent' 		=> 0,
						'isLeaf' 		=> TRUE,
						'expanded' 		=> TRUE,
						'loaded' 		=> TRUE
					);
				}
				$j++;
			}
			
			if($row['parents'] == 0){
				$id_parents = $row['id'];
				$strsum = '<b>Jumlah '.$row['uraian'].'</b>';
				$total = 0;
				$idj = substr($row['nmlama'],0,1);
			}
			
			//$cur = abs($row['total']);
			$cur = $row['total'];
		
			if(in_array(substr($row['nmlama'],0,1),$isi_lap) && !in_array($row['nmlama'],$tdk_tampil)){
				$tmp_result[] = array(
					'id' 			=> $row['id'],
					'uraian' 		=> $uraian,
					'total'			=> myFormatMoney($cur),
					'level'			=> $row['levels'],
					'parent' 		=> $row['parents'],
					'isLeaf' 		=> $isleaf,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
			}
		
			if($row['levels'] != 2){
				$total += $cur;
			}
		}
		$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total'			=> myFormatMoney($total),
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
		$tmp_result[] = array(
					'id' 			=> 'sub'.$idj,
					'uraian' 		=> '<b>JUMLAH KEWAJIBAN & EKUITAS</b>',
					'total'			=> myFormatMoney($sum_tot),
					'level'			=> 0,
					'parent' 		=> 0,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
        return $tmp_result;

    }
    
    function getAllAssets($periode,$unitkerja,$proyek) {

        $this->db->start_cache();
        //$_table = $proyek > 0 ? $this->_table['view_matriks_neraca'] : $this->_table['view_matriks_neraca_konsolidasi'] ;
        $_table = $this->_table['view_matriks_neraca'];
        $this->db->from($_table);
        //$this->db->from("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->select('*');
        //$query = $this->db->get("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->where('id_periode', $periode);
        $this->db->where('proyek', $proyek);
        /*if($proyek > 0){
			$this->db->where('proyek', $proyek);
		} else {
			$this->db->where('subunitkerja', $unitkerja);
		}*/
        $query = $this->db->get($_table);
        $this->_countAll = $this->db->count_all_results();
        $this->db->flush_cache();

        //return $query->result_array();
        $tmp_result = array();
		
        $i = 0;
        $sum_tot = 0;
        $id_parents = '';
        
        $ktProject	=	$proyek > 0 && in_array($unitkerja,Array("1","6","7","8","9","10"))? "PUSAT" : "" ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("5"))?	"PERALATAN"	: $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("2","3"))?	"PRODUKSI" : $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("4"))?	"KHUSUS" : $ktProject ;
		
		switch ($ktProject) {
			
			case	"PUSAT"		:	
					$isi_lap	=	Array("A","B","C","D","E");
					$tdk_tampil	=	Array("A03","A04","A05","A07");
			break;
					
			case	"PERALATAN"		:	
					$isi_lap	=	Array("A","B");
					$tdk_tampil	=	Array("A02","A04","A11","B03");
			break;
					
			case	"PRODUKSI"		:	
					$isi_lap	=	Array("A","B");
					$tdk_tampil	=	Array("A02","A04","A11","B03");
			break;
					
			case	"KHUSUS"		:	
					$isi_lap	=	Array("A","B");
					$tdk_tampil	=	Array("A02","A04","A11","B03");
			break;	
			
			default	: $isi_lap		=	Array("A","B","C","D","E");
					  $tdk_tampil	=	Array("");
			break;	
		}					  
		/*echo $proyek;
		echo $ktProject;
		print_r($kata2);*/
		foreach($query->result_array() as $row){
			$i++;
			$isleaf = $row['childs'] > 0 ? FALSE : TRUE;
			$uraian = $row['parents'] == 0 ? '<b>'.$row['uraian'].'</b>' : $row['uraian'];
			
			if($row['parents'] != $id_parents && $id_parents != '' && in_array($idj,$isi_lap) && $row['levels'] != 2){
				$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total'			=> myFormatMoney($total),
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
			}
			
			if($row['parents'] == 0){
				$id_parents = $row['id'];
				$strsum = '<b>Jumlah '.$row['uraian'].'</b>';
				$total = 0;
				$idj = substr($row['nmlama'],0,1);
			}
			
			//$cur = abs($row['total']);
			$cur = $row['total'];
			
			if($row['levels'] != 2){
					if($row['total'] != 0){
						$curtot = '<a class="link_edit" href="javascript:void(0);" onclick="showUrlInDialog(root + \'mod_matriks_neraca/getDetailGroup/'.$row['nmlama'].'/'.$row['id_periode'].'\', \'tes\', \'Detail Neraca\', \'form_detail_neraca\');"><img src="' . base_url() . 'media/edit.png" /> '.myFormatMoney($row['total']).'</a>';
					} else {
						$curtot = myFormatMoney($row['total']);
					}
				} else {
					if($row['total'] != 0){
						$curtot = '<a class="link_edit" href="javascript:void(0);" onclick="showUrlInDialog(root + \'mod_matriks_neraca/getDetailCoa/'.$row['id'].'/'.$row['id_periode'].'\', \'tes\', \'Detail Neraca\', \'form_detail_neraca\');"><img src="' . base_url() . 'media/edit.png" /> '.myFormatMoney($row['total']).'</a>';
					} else {
						$curtot = myFormatMoney($row['total']);
					}
				}
				
			if(in_array(substr($row['nmlama'],0,1),$isi_lap) && !in_array($row['nmlama'],$tdk_tampil)){
				$tmp_result[] = array(
					'id' 			=> $row['id'],
					'uraian' 		=> $uraian,
					'total'			=> $curtot,
					'level'			=> $row['levels'],
					'parent' 		=> $row['parents'],
					'isLeaf' 		=> $isleaf,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
				if($row['levels'] != 2){
					$sum_tot += $row['total'];
				}
			}
		
			if($row['levels'] != 2){
				$total += $cur;
			}
			
			/*if($row['parents'] != $id_parents && $id_parents != '' && $row['levels'] != 2){	
				$sum_tot += $total;
			}*/
		}
		/*$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total'			=> myFormatMoney($total),
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);*/
		$tmp_result[] = array(
					'id' 			=> 'total',
					'uraian' 		=> 'JUMLAH ASSETS',
					'total'			=> myFormatMoney($sum_tot),
					'level'			=> 0,
					'parent' 		=> 0,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
				
        return $tmp_result;

    }

	function getAllEkuitas($periode,$unitkerja,$proyek) {

        $this->db->start_cache();
        //$_table = $proyek > 0 ? $this->_table['view_matriks_neraca'] : $this->_table['view_matriks_neraca_konsolidasi'] ;
        $_table = $this->_table['view_matriks_neraca'];
        $this->db->from($_table);
        //$this->db->from("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->select('*');
        //$query = $this->db->get("get_labarugi(('$tahun'),('$bulan'),('$hari'),($proyek)) T (proyek INT, id INT, uraian CHARACTER VARYING, bulan_ini numeric, sd_bulan_ini numeric, parents integer, levels integer)");
        $this->db->where('id_periode', $periode);
        $this->db->where('proyek', $proyek);
        /*if($proyek > 0){
			$this->db->where('proyek', $proyek);
		} else {
			$this->db->where('subunitkerja', $unitkerja);
		}*/
        $query = $this->db->get($_table);
        $this->_countAll = $this->db->count_all_results();
        $this->db->flush_cache();

        //return $query->result_array();
        $tmp_result = array();
		
        $i = 0;
        $sum_tot = 0;
        $id_parents = '';
        
        $ktProject	=	$proyek > 0 && in_array($unitkerja,Array("1","6","7","8","9","10"))? "PUSAT" : "" ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("5"))?	"PERALATAN"	: $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("2","3"))?	"PRODUKSI" : $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("4"))?	"KHUSUS" : $ktProject ;
		
		switch ($ktProject) {
			
			case	"PUSAT"		:	
					$isi_lap	=	Array("F","G","H","I");
					$tdk_tampil	=	Array("F04","G03");
			break;
					
			case	"PERALATAN"		:	
					$isi_lap	=	Array("F","G","H","I");
					$tdk_tampil	=	Array("F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
			break;
					
			case	"PRODUKSI"		:	
					$isi_lap	=	Array("F","G","H","I");
					$tdk_tampil	=	Array("F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
			break;
					
			case	"KHUSUS"		:	
					$isi_lap	=	Array("F","G","H","I");
					$tdk_tampil	=	Array("F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
			break;	
			
			default	: $isi_lap		=	Array("F","G","H","I");
					  $tdk_tampil	=	Array("");
			break;	
		}					  
		/*echo $proyek;
		echo $ktProject;
		print_r($kata2);*/
		foreach($query->result_array() as $row){
			$i++;
			$isleaf = $row['childs'] > 0 ? FALSE : TRUE;
			$uraian = $row['parents'] == 0 ? '<b>'.$row['uraian'].'</b>' : $row['uraian'];
			
			if($row['parents'] != $id_parents && $id_parents != '' && in_array($idj,$isi_lap) && $row['levels'] != 2){
				$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total'			=> myFormatMoney($total),
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
			}
			
			if($row['parents'] == 0){
				$id_parents = $row['id'];
				$strsum = '<b>Jumlah '.$row['uraian'].'</b>';
				$total = 0;
				$idj = substr($row['nmlama'],0,1);
			}
			
			//$cur = abs($row['total']);
			$cur = $row['total'];
			if($row['levels'] != 2){
					if($row['total'] != 0){
						$curtot = '<a class="link_edit" href="javascript:void(0);" onclick="showUrlInDialog(root + \'mod_matriks_neraca/getDetailGroup/'.$row['nmlama'].'/'.$row['id_periode'].'\', \'tes\', \'Detail Neraca\', \'form_detail_neraca\');"><img src="' . base_url() . 'media/edit.png" /> '.myFormatMoney($row['total']).'</a>';
					} else {
						$curtot = myFormatMoney($row['total']);
					}
				} else {
					if($row['total'] != 0){
						$curtot = '<a class="link_edit" href="javascript:void(0);" onclick="showUrlInDialog(root + \'mod_matriks_neraca/getDetailCoa/'.$row['id'].'/'.$row['id_periode'].'\', \'tes\', \'Detail Neraca\', \'form_detail_neraca\');"><img src="' . base_url() . 'media/edit.png" /> '.myFormatMoney($row['total']).'</a>';
					} else {
						$curtot = myFormatMoney($row['total']);
					}
				}
				
			if(in_array(substr($row['nmlama'],0,1),$isi_lap) && !in_array($row['nmlama'],$tdk_tampil)){
				$tmp_result[] = array(
					'id' 			=> $row['id'],
					'uraian' 		=> $uraian,
					'total'			=> $curtot,
					'level'			=> $row['levels'],
					'parent' 		=> $row['parents'],
					'isLeaf' 		=> $isleaf,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
				if($row['levels'] != 2){
					$sum_tot += $row['total'];
				}
				
			}
		
			if($row['levels'] != 2){
				$total += $cur;
			}
			
			/*if($row['parents'] != $id_parents && $id_parents != '' && $row['levels'] != 2){	
				$sum_tot += $total;
			}*/
		}
		
		$tmp_result[] = array(
					'id' 			=> 'j'.$i,
					'uraian' 		=> $strsum,
					'total'			=> myFormatMoney($total),
					'level'			=> 1,
					'parent' 		=> $id_parents,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
				
		$tmp_result[] = array(
					'id' 			=> 'total',
					'uraian' 		=> 'JUMLAH KEWAJIBAN & EKUITAS',
					'total'			=> myFormatMoney($sum_tot),
					'level'			=> 0,
					'parent' 		=> 0,
					'isLeaf' 		=> TRUE,
					'expanded' 		=> TRUE,
					'loaded' 		=> TRUE
				);
				
        return $tmp_result;

    }
    
    public function countAll() {
        return $this->_countAll;
    }
	
	public function getAllForExcel($periode,$unitkerja,$proyek) {
        $this->db->select('*');
        //$_table = $proyek > 0 ? $this->_table['view_matriks_labarugi'] : $this->_table['view_matriks_labarugi_konsolidasi'] ;
        $_table = $this->_table['view_matriks_neraca'];
        $this->db->from($_table);
        $this->db->where('id_periode', $periode);
        $this->db->where('proyek', $proyek);
        /*if($proyek > 0){
			$this->db->where('proyek', $proyek);
		} else {
			$this->db->where('subunitkerja', $unitkerja);
		}*/
        $query = $this->db->get();

        $i = 0;
        $j = 0;
        $sum_tot = 0;
		$sum_tot_sd = 0;
        $id_parents = '';
        $ktProject	=	$proyek > 0 && in_array($unitkerja,Array("1","6","7","8","9","10"))? "PUSAT" : "" ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("5"))?	"PERALATAN"	: $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("2","3"))?	"PRODUKSI" : $ktProject ;
		$ktProject	=	$proyek > 0 && in_array($unitkerja,Array("4"))?	"KHUSUS" : $ktProject ;
		
		switch ($ktProject) {
			
			case	"PUSAT"		:	
					$isi_lap	=	Array("A","B","C","D","E","F","G","H","I");
					$tdk_tampil	=	Array("A03","A04","A05","A07","F04","G03");
					$kata2		=	Array("","","","","<b>JUMLAH ASET</b>",
										  "","","","");
			break;
					
			case	"PERALATAN"		:	
					$isi_lap	=	Array("A","B","F","G","H","I");
					$tdk_tampil	=	Array("A02","A04","A11","B03","F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
					$kata2		=	Array("","","","","","","","","");
			break;
					
			case	"PRODUKSI"		:	
					$isi_lap	=	Array("A","B","F","G","H","I");
					$tdk_tampil	=	Array("A02","A04","A11","B03","F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
					$kata2		=	Array("","","","","","","","","");
			break;
					
			case	"KHUSUS"		:	
					$isi_lap	=	Array("A","B","F","G","H","I");
					$tdk_tampil	=	Array("A02","A04","A11","B03","F02","F03","G01","G02","G04","G05","I01","I02","I03","I04","I05");
					$kata2		=	Array("","","","","","","","","");
			break;	
			
			default	: $isi_lap		=	Array("A","B","C","D","E","F","G","H","I");
					  $tdk_tampil	=	Array("");
					  $kata2		=	Array("","","","","<b>JUMLAH ASET</b>",
										  "","","","");
			break;	
		}					  
		/*echo $proyek;
		echo $ktProject;
		print_r($kata2);*/
		foreach($query->result_array() as $row){
			$i++;
			$uraian = $row['parents'] == 0 ? $row['uraian'] : ($row['levels'] == 1 ? '   '.$row['uraian'] : '      '.$row['uraian']);
			
			if($row['parents'] != $id_parents && $id_parents != '' && in_array($idj,$isi_lap) && $row['levels'] != 2){
				$tmp_result[] = array(
					'uraian' 		=> $strsum,
					'total'			=> $total
				);
			}
			
			if($row['parents'] != $id_parents && $id_parents != '' && $row['levels'] != 2){	
				if($idj == 'F' && $row['parents'] == 0){
					$sum_tot = 0;
					$sum_tot += $total;
				} else {
					$sum_tot += $total;
				}
				
				if($kata2[$j] != ''){
					$tmp_result[] = array(
						'uraian' 		=> $kata2[$j],
						'total'			=> $sum_tot
					);
				}
				$j++;
			}
			
			if($row['parents'] == 0){
				$id_parents = $row['id'];
				$strsum = 'Jumlah '.$row['uraian'];
				$total = 0;
				$idj = substr($row['nmlama'],0,1);
			}
			
			$cur = abs($row['total']);
		
			if(in_array(substr($row['nmlama'],0,1),$isi_lap) && !in_array($row['nmlama'],$tdk_tampil)){
				$tmp_result[] = array(
					'uraian' 		=> $uraian,
					'total'			=> $cur
				);
			}
		
			if($row['levels'] != 2){
				$total += $cur;
			}
		}
		$tmp_result[] = array(
					'uraian' 		=> $strsum,
					'total'			=> $total
				);
		$tmp_result[] = array(
					'uraian' 		=> 'JUMLAH KEWAJIBAN & EKUITAS',
					'total'			=> $sum_tot
				);
        return $tmp_result;
    }
    
    public function getBulan() {

        $this->db->select('b.id_library, b.nama_library');
        $this->db->from($this->_table['library'] . ' a');
        $this->db->join($this->_table['library'] . ' b', 'b.parent = a.id_library', 'left outer');
        $this->db->where('a.id_library = 15');
        $this->db->order_by('b.id_library', 'asc');
        $query = $this->db->get();

        $temp_result = array();
        $i = 1;
        foreach ($query->result_array() as $row) {
            $temp_result[$i++] = $row['nama_library'];
        }
        return $temp_result;
    }

	public function getDetailGroup($group, $periode, $unitkerja, $proyek){
        $this->db->select('a.*');
        $this->db->from($this->_table['view_listjurnal_approved'].' a');
        $this->db->join('tbl_group_neraca b','a.kdperkiraan >= b.bawah AND a.kdperkiraan <= b.atas','left');
        $this->db->join('period c','a.id_proyek = c.id_proyek','left');
        $this->db->where('a.id_proyek',$proyek);
        $this->db->where('b.nmlama',$group);
        $this->db->where('c.period_id',$periode);
        $query = $this->db->get();
        $this->db->flush_cache();
        
		$tmp = array();
        $tmp_result = array();
        foreach ($query->result_array() as $row) {
				$tmp[$row['nobukti']]['tanggal'] 		= $row['tanggal'];
				$tmp[$row['nobukti']]['nobukti'] 		= $row['nobukti'];
				$tmp[$row['nobukti']]['kode_proyek'] 	= $row['kode_proyek'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['id_jurnal'] 	= $row['id_jurnal'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['coa'] 		= $row['coa'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['rekanan'] 	= $row['rekanan'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['keterangan'] 	= $row['keterangan'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['debit'] 		= $row['debit'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['kredit'] 		= $row['kredit'];
				}
		
        $i = 0;
		foreach($tmp as $row){
			$i++;
			$check = 0;
			foreach($row['desc'] as $val){
				//print_r($val['']);
				//$i++;
				if ($check == 0) {
					$tmp_result[] = array(
						'idnya' 		=> $i,
						'nomor' 		=> $i,
						'tanggal' 		=> $row['tanggal'],
						'nobukti' 		=> $row['nobukti'],
						//'id_jurnal' 	=> "",
						'kode_proyek' 	=> $row['kode_proyek'],
						'coa' 			=> $val["coa"],
						'rekanan' 		=> $val["rekanan"],
						'keterangan' 	=> $val["keterangan"],
						'debit' 		=> myFormatMoney($val["debit"]),
						'kredit' 		=> myFormatMoney($val["kredit"])
					);
				} else {
					$tmp_result[] = array(
						'idnya' 		=> $val["id_jurnal"],
						'nomor' 		=> "",
						'tanggal' 		=> "",
						'nobukti' 		=> "",
						//'id_jurnal' 	=> $val["id_jurnal"],
						'kode_proyek' 	=> "",
						'coa' 			=> $val["coa"],
						'rekanan' 		=> $val["rekanan"],
						'keterangan' 	=> $val["keterangan"],
						'debit' 		=> myFormatMoney($val["debit"]),
						'kredit' 		=> myFormatMoney($val["kredit"])
					);
				}
				$check++;
			}
			$tmp_result[] = array(
					'idnya' 		=> "i".$i,
				    'nomor' 		=> "",
					'tanggal' 		=> "",
					'nobukti' 		=> "",
					//'id_jurnal' 	=> "",
					'kode_proyek' 	=> "",
					'coa' 			=> "",
					'rekanan' 		=> "",
					'keterangan' 	=> "",
					'debit' 		=> "",
					'kredit' 		=> ""
				);
		}
		//print_r($tmp_result);
        return $tmp_result;
	}

	public function getDetailCoa($coa, $periode, $unitkerja, $proyek){
        $this->db->select('a.*');
        $this->db->from($this->_table['view_listjurnal_approved'].' a');
        //$this->db->join('tbl_group_neraca b','a.kdperkiraan >= b.bawah AND a.kdperkiraan <= b.atas','left');
        $this->db->join('period c','a.id_proyek = c.id_proyek','left');
        $this->db->where('a.id_proyek',$proyek);
        //$this->db->where('b.nmlama',$group);
        $this->db->where('a.kdperkiraan',$coa);
        $this->db->where('c.period_id',$periode);
        $query = $this->db->get();
        $this->db->flush_cache();
        
		$tmp = array();
        $tmp_result = array();
        foreach ($query->result_array() as $row) {
				$tmp[$row['nobukti']]['tanggal'] 		= $row['tanggal'];
				$tmp[$row['nobukti']]['nobukti'] 		= $row['nobukti'];
				$tmp[$row['nobukti']]['kode_proyek'] 	= $row['kode_proyek'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['id_jurnal'] 	= $row['id_jurnal'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['coa'] 		= $row['coa'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['rekanan'] 	= $row['rekanan'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['keterangan'] 	= $row['keterangan'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['debit'] 		= $row['debit'];
				$tmp[$row['nobukti']]['desc'][$row['id_jurnal']]['kredit'] 		= $row['kredit'];
				}
		
        $i = 0;
		foreach($tmp as $row){
			$i++;
			$check = 0;
			foreach($row['desc'] as $val){
				//print_r($val['']);
				//$i++;
				if ($check == 0) {
					$tmp_result[] = array(
						'idnya' 		=> $i,
						'nomor' 		=> $i,
						'tanggal' 		=> $row['tanggal'],
						'nobukti' 		=> $row['nobukti'],
						//'id_jurnal' 	=> "",
						'kode_proyek' 	=> $row['kode_proyek'],
						'coa' 			=> $val["coa"],
						'rekanan' 		=> $val["rekanan"],
						'keterangan' 	=> $val["keterangan"],
						'debit' 		=> myFormatMoney($val["debit"]),
						'kredit' 		=> myFormatMoney($val["kredit"])
					);
				} else {
					$tmp_result[] = array(
						'idnya' 		=> $val["id_jurnal"],
						'nomor' 		=> "",
						'tanggal' 		=> "",
						'nobukti' 		=> "",
						//'id_jurnal' 	=> $val["id_jurnal"],
						'kode_proyek' 	=> "",
						'coa' 			=> $val["coa"],
						'rekanan' 		=> $val["rekanan"],
						'keterangan' 	=> $val["keterangan"],
						'debit' 		=> myFormatMoney($val["debit"]),
						'kredit' 		=> myFormatMoney($val["kredit"])
					);
				}
				$check++;
			}
			$tmp_result[] = array(
					'idnya' 		=> "i".$i,
				    'nomor' 		=> "",
					'tanggal' 		=> "",
					'nobukti' 		=> "",
					//'id_jurnal' 	=> "",
					'kode_proyek' 	=> "",
					'coa' 			=> "",
					'rekanan' 		=> "",
					'keterangan' 	=> "",
					'debit' 		=> "",
					'kredit' 		=> ""
				);
		}
		//print_r($tmp_result);
        return $tmp_result;
	}
	
	public function getProyekName($id) {

        $this->db->select('nama_proyek');
        $this->db->from('list_proyek_v');
        $this->db->where('id_proyek', $id);
        $query = $this->db->get();

        $result = '';
        foreach ($query->result_array() as $row) {
            $result = $row['nama_proyek'];
        }
        return $result;
    }
	
	public function getPeriodName($id) {

        $this->db->select('period_name');
        $this->db->from('perioda_v');
        $this->db->where('period_id', $id);
        $query = $this->db->get();

        $result = '';
        foreach ($query->result_array() as $row) {
            $result = $row['period_name'];
        }
        return $result;
    }
}
