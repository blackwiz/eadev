<?php
// php-export-data by Eli Dickinson, http://github.com/elidickinson/php-export-data

/**
 * ExportData is the base class for exporters to specific file formats. See other
 * classes below.
 * major modified from originial class
 */
abstract class ExportData {

	public $filename; // file mode: the output file name; browser mode: file name for download; string mode: not used
	protected $_param; // array parameter untuk style dalam array
	protected $_idstyle; // parameter style ID
	protected $_title; // parameter judul worksheet
	protected $_cell; // parameter style untuk satu cell
	protected $_freeze; // parameter freeze berdasarkan cell
	protected $_col; // parameter kolom

	public function __construct($filename = "exportdata") {
		$this->filename = $filename;
	}
	
	/* 
	 * fungsi global yang pertama kali dijalankan
	 * mengisi variabel param di class anak dengan variabel _param class induk
	 * menjalankan sendHttpHeaders untuk membuang semua variabel buffer ke dalam file yang langsung di download
	 * tidak memberatkan server karena variabel langsung dibuang ke file download
	 * menjalankan fungsi generateHeader
	 * bersihkan variabel _param
	 */
	public function initialize() {
		$this->arrStyle = $this->_param;
		$this->sendHttpHeaders();
		$this->write($this->generateHeader());
		unset($this->_param);
	}
	
	/*
	 * fungsi untuk membuat worksheet baru
	 * baca variabel _title dan isi ke variabel title pada class anak untuk nama worksheet
	 * bersihkan variabel _title
	 */
	public function startSheet() {
		$this->title = !empty($this->_title)? $this->_title : NULL;
		$this->write($this->openSheet());
		unset($this->_title);
	}
	
	/*
	 * fungsi untuk mengisi nilai pada cell atau range
	 * baca variabel _idstyle dan isi ke variabel idStyle pada class anak untuk style cell
	 * bersihkan _idstyle
	 */
	public function addRow($row) {
		$this->idStyle = !empty($this->_idstyle)? $this->_idstyle : NULL;
		$this->write($this->generateRow($row));
		unset($this->_idstyle);
	}
	
	/*
	 * fungsi untuk menutup worksheet
	 * baca variabel _freeze dan isi ke variabel freeze pada class anak untuk opsi worksheet freeze
	 * bersihkan variabel _freeze
	 */
	public function endSheet() {
		$this->freeze = !empty($this->_freeze)? $this->_freeze : NULL;
		$this->write($this->closeSheet());
		unset($this->_freeze);
	}
	
	/*
	 * fungsi yang dijalankan paling akhir
	 * generate footer
	 * bersihkan buffer
	 */
	public function finalize() {
		$this->write($this->generateFooter());
		//$this->sendHttpHeaders();
		flush();
	}
	
	/*
	 * fungsi untuk mengumpulkan style pada tag styles
	 * menerima parameter array
	 * mengembalikan nilai parameter untuk diteruskan ke fungsi yang lain
	 */
	public function setStyle($param) {
		$this->_param = $param;
		return $this; 
	}
	
	/*
	 * fungsi untuk menentukan style pada objek
	 * menerima parameter type string
	 * isi cellStyle pada class anak dengan IdStyle 
	 */
	public function applyTo($param){
		if (strpos($param,':') !== false){
			$ranges = explode(':',$param);
			//pindahkan nilai setiap index array ke variabel sesuai jumlah index array
			list($rangeStart, $rangeEnd)	= $ranges;
			list($startCol, $startRow)	= sscanf($rangeStart,'%[A-Z]%d');
			list($endCol, $endRow)		= sscanf($rangeEnd,'%[A-Z]%d');
			for($c=$startCol;$c<=$endCol;$c++){
				$this->cellStyle[] = $c;
				$this->cellStyle[$c] = array(
											'startrow' => $startRow,
											'endrow' => $endRow,
											'style' => $this->_idstyle
										);
				/*for($r=$startRow;$r<=$endRow;$r++){
					$this->cell[] = $c.$r;
					$this->cellStyle[$c.$r] = !empty($this->_idstyle) ? $this->_idstyle : NULL;
				}*/
			}
		} else {
			/*$this->cell = $param;
			$this->cellStyle = !empty($this->_idstyle) ? $this->_idstyle : NULL;*/
			$this->cellStyle[$c] = $this->_idstyle;
		}
		
	}
	
	/*
	 * fungsi untuk menentukan style dari parameter
	 * parameter type string
	 * mengembalikan nilai untuk diteruskan ke fungsi lain
	 */
	public function applyStyle($ids){
		$this->_idstyle = $ids;
		return $this;
	}
	
	/*
	 * fungsi untuk mengisi judul worksheet
	 * parameter type string
	 * mengembalikan nilai untuk diteruskan ke fungsi lain
	 */
	public function titleSheet($title){
		$this->_title = $title;
		return $this;
	}
	
	/*
	 * fungsi untuk freeze
	 * parameter cell type string
	 * mengembalikan nilai untuk diteruskan ke fungsi lain
	 */
	public function freeze($cell){
		$this->_freeze = $cell;
		return $this;
	}
	
	/* 
	 * fungsi merge dengan parameter range cell
	 * langsung mengisi nilai variabel publik di class anaknya
	 * cell yang dimerge harus tampil ditampung pada array cellShow
	 * atribut cell yang dimerge ss:MergeAcross untuk merge horizontal
	 * atribut cell yang dimerge ss:MergeDown untuk merge vertikal
	 * cell yang kena merge harus dihilangkan ditampung pada array cellHide
	 * atribut cell dikumpulkan di array merger
	 */
	public function merge($range){
		//merge harus pake range cell
		if (strpos($range,':') !== false){
			$res = '';
			$ranges = explode(':',$range);
			//pindahkan nilai setiap index array ke variabel sesuai jumlah index array
			list($rangeStart, $rangeEnd)	= $ranges;
			list($startCol, $startRow)	= sscanf($rangeStart,'%[A-Z]%d');
			list($endCol, $endRow)		= sscanf($rangeEnd,'%[A-Z]%d');
			$h=0;
			//kumpulkan cell yang dimerge
			$this->cellShow[] = $rangeStart;
			for($c=$startCol;$c<=$endCol;$c++){
				//hitung jumlah merge horizontal
				$h++;
				for($r=$startRow;$r<=$endRow;$r++){
					//jangan sampe cell yang dimerge ditandai sebagai yang kena merge
					if($rangeStart != $c.$r){
						//kumpulkan cell yang kena merge
						$this->cellHide[] = $c.$r;
					}
				}
			}
			//hitung jumlah merge vertikal
			$v = $endRow-$startRow;
			
			//variabel h kelebihan 1 nilai
			if($h > 1){
				$res .= 'ss:MergeAcross="'.--$h.'"';
			}
			if($v > 0){
				$res .= 'ss:MergeDown="'.$v.'"';
			}
			//kumpulkan atribut merge
			$this->merger[] = $res;
		} else {
			throw new Exception('Merge must be set on a range of cells.');
		}
	}
	
	public function col($col){
		$this->_col = $col;
		return $this;
	}
	
	public function width($width){
		$this->width[$this->_col] = $width;
	}
	
	public function debug(){
		die(print_r($this->debug));
	}

	abstract public function sendHttpHeaders();

	protected function write($data) {
		echo $data;
	}

	protected function generateHeader() {
	// can be overridden by subclass to return any data that goes at the top of the exported file
	}
	
	protected function openSheet() {
	// can be overridden by subclass to return any data that goes at the top of the exported file
	}
	
	protected function closeSheet() {
	// can be overridden by subclass to return any data that goes at the top of the exported file
	}
	
	protected function generateFooter() {
	// can be overridden by subclass to return any data that goes at the bottom of the exported file
	}

	// In subclasses generateRow will take $row array and return string of it formatted for export type
	abstract protected function generateRow($row);

}
