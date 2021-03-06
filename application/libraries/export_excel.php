<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExportDataExcel exports data into an XML format  (spreadsheetML) that can be 
 * read by MS Excel 2003 and newer as well as OpenOffice
 * 
 * Creates a workbook with a single worksheet (title specified by
 * $title).
 * 
 * Note that using .XML is the "correct" file extension for these files, but it
 * generally isn't associated with Excel. Using .XLS is tempting, but Excel 2007 will
 * throw a scary warning that the extension doesn't match the file type.
 * 
 * Based on Excel XML code from Excel_XML (http://github.com/oliverschwarz/php-excel)
 *  by Oliver Schwarz
 */

require_once APPPATH . "/third_party/exportxls/ExportData.php";

class Export_Excel extends ExportData {
	
	const XmlHeader = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<?mso-application progid=\"Excel.Sheet\"?>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
	const XmlFooter = "</Workbook>";

	public $encoding = 'UTF-8'; // encoding type to specify in file.
	// Note that you're on your own for making sure your data is actually encoded to this encoding

	//public $merger,$cellShow,$cellHide,$freeze,$param,$style,$idStyle,$title,$cell,$cellStyle;
	
	public $arrStyle = array(); // Parameter menampung array style 
	public $style; // Variabel string menampung style hasil generate dari array
	public $idStyle; // Variabel string menampung Id Style
	public $merger = array(); // Atribute merger : ss::MergerAcross ss::MergerDown
	public $cellShow = array(); // kumpulan cell yang di merge
	public $cellHide = array(); // kumpulan cell yang kena merge
	public $width = array(); // kumpulan ukuran kolom
	public $freeze; // inisialisasi cell yang di freeze
	public $title; // string judul worksheet
	public $cell; // string cell
	public $cellStyle; // string menampung id style untuk satu cell saja 
	
	public $x = 0; // counter baris
	public $y = 'A'; // counter kolom
	public $z = 1; // counter index (ss:index)
	
	public $debug; // debuging
    
    public function __construct() {
        parent::__construct();
    }

	function generateHeader() {

		// workbook header
		$output = stripslashes(sprintf(self::XmlHeader, $this->encoding)) . "\n";
		
		$output .= "<OfficeDocumentSettings xmlns=\"urn:schemas-microsoft-com:office:office\"></OfficeDocumentSettings>\n<ExcelWorkbook xmlns=\"urn:schemas-microsoft-com:office:excel\"></ExcelWorkbook>\n";
		
		// Set up styles
		//$output .= "<Styles>\n";
		
		$output .= $this->generateStyles();
		
		//$output .= "</Styles>\n";

		return $output;
	}
	
	private function generateStyles() {
		$output = '';
		if(!empty($this->arrStyle)){
			$this->style .= "<Styles>\n";
			//die(print_r($param));
			foreach($this->arrStyle as $k1 => $v1){
				$this->style .= "<Style ss:ID=\"$k1\">\n";
				foreach($v1 as $k2 => $v2){
						$this->style .= $k2=="Borders"?"<$k2>\n":"<$k2 ";
						foreach($v2 as $k3 => $v3){
							if($k2=="Borders" && $k3=="All" && is_array($v3)) {
								$attr = "";
								foreach($v3 as $k4 => $v4){
									$attr .= "ss:$k4=\"$v4\" ";
								}
								$this->style .= "<Border ss:Position=\"Top\" $attr />\n";
								$this->style .= "<Border ss:Position=\"Bottom\" $attr />\n";
								$this->style .= "<Border ss:Position=\"Left\" $attr />\n";
								$this->style .= "<Border ss:Position=\"Right\" $attr />\n";
							} elseif(is_array($v3)) {
								$this->style .= $k2=="Borders"?"<Border ":"<$v3 ";
								foreach($v3 as $k4 => $v4){
									$this->style .= "ss:$k4=\"$v4\" ";
								}
								$this->style .= "/>\n";
							} else {
								$this->style .= "ss:$k3=\"$v3\" ";
							}
						}
						$this->style .= $k2=="Borders"?"</$k2>\n":"/>\n";
				}
				$this->style .= "</Style>\n";
			}
			$this->style .= "</Styles>\n";
		}
		
		$output .= $this->style;
		
		return $output;
	}
	
	private function generateOption() {
		$output = '';
		
		$output .= "<WorksheetOptions xmlns=\"urn:schemas-microsoft-com:office:excel\">\n";
		$output .= "<PageSetup>\n<Layout x:Orientation=\"Landscape\"/>\n</PageSetup>\n<FitToPage/>\n";
		if(!is_null($this->freeze)){
			list($Col, $Row) = sscanf($this->freeze,'%[A-Z]%d');
			$j=1;
			for($i='A';$i<$Col;$i++){
				$j++;
			}
			if(!empty($Row)){
				//$output .= "<Selected/>\n<FreezePanes/>\n<FrozenNoSplit/>\n<SplitHorizontal>".$Row."</SplitHorizontal>\n<TopRowBottomPane>1</TopRowBottomPane>\n<ActivePane>".++$Row."</ActivePane>\n";
				$output .= "<Selected/>\n<FreezePanes/>\n<FrozenNoSplit/>\n<SplitHorizontal>".$Row."</SplitHorizontal>\n<TopRowBottomPane>".$Row."</TopRowBottomPane>\n<ActivePane>2</ActivePane>\n";
			} else {
				//$output .= "<Selected/>\n<FreezePanes/>\n<FrozenNoSplit/>\n<SplitVertical>".$j."</SplitVertical>\n<TopRowBottomPane>1</TopRowBottomPane>\n<ActivePane>".++$j."</ActivePane>\n";
				$output .= "<Selected/>\n<FreezePanes/>\n<FrozenNoSplit/>\n<SplitVertical>".$j."</SplitVertical>\n<LeftColumnRightPane>".$j."</LeftColumnRightPane>\n<ActivePane>2</ActivePane>\n";
			}
		}
		
		$output .= "</WorksheetOptions>\n";
		
		return $output;
	}
	
	function openSheet() {
		$output = '';
		
		// worksheet header
		$output .= sprintf("<Worksheet ss:Name=\"%s\">\n <Table>\n", htmlentities($this->title != NULL ? $this->title : 'Sheet'));
		//$output .= print_r($this->width);
		foreach($this->width as $key => $val){
			$output .= "<Column ss:Width=\"".$val."\"/>\n";
		}
		
		$this->x = 0;
		return $output;
	}
	
	function closeSheet() {
		$output = '';
		
		// worksheet footer
		$output .= "</Table>\n";
		$output .= $this->generateOption();
		$output .= "</Worksheet>\n";
		
		return $output;
	}

	function generateFooter() {
		$output = '';

		// workbook footer
		$output .= self::XmlFooter;
		//die(print_r($this->cellShow));
		return $output;
	}

	function generateRow($row) {
		$output = '';
		
		foreach ($row as $k => $v) {
			$this->x++;
			$this->y='A';
			$this->z=1;
		$output .= " <Row>\n";
			foreach($v as $key => $val) {
				$output .= $this->generateCell($val);
				$this->y++;
				$this->z++;
			}
		$output .= " </Row>\n";
		}
		
		return $output;
	}

	private function generateCell($item) {
		$output = '';
		$merge = '';
		
		// cek jika terdapat merge pada cell yang aktif
		if(in_array($this->y.$this->x, $this->cellShow)) {
			$key = array_search($this->y.$this->x, $this->cellShow);
			$merge = $this->merger[$key];
		}
		
		// cek jika terdapat inisialisasi style pada cell yang aktif
		//if($this->cell && ($this->y.$this->x == $this->cell || $this->y == $this->cell || $this->x == $this->cell || (is_array($this->cell) && in_array($this->y.$this->x, $this->cell)))) {
		if(is_array($this->cellStyle) && in_array($this->y, $this->cellStyle)) {
			//if(!is_null($this->cellStyle) && is_array($this->cellStyle)){
			if($this->x >= $this->cellStyle[$this->y]['startrow'] && $this->x <= $this->cellStyle[$this->y]['endrow']){
				$style = $this->cellStyle[$this->y]['style'];
				//$style = $this->cellStyle[$this->y.$this->x];
			}//else{
				//$style = '';
			//}
			//$merge = $merge ? $merge : '';
		} else {
			$style = $this->idStyle;
		}
		// Tell Excel to treat as a number. Note that Excel only stores roughly 25 digits, so keep
		// as text if number is longer than that.
		if(preg_match("/^-?\d+(?:[.,]\d+)?$/",$item) && (strlen($item) < 25)) {
			$type = 'Number';
		}
		// Sniff for valid dates; should look something like 2010-07-14 or 7/14/2010 etc. Can
		// also have an optional time after the date.
		//
		// Note we want to be very strict in what we consider a date. There is the possibility
		// of really screwing up the data if we try to reformat a string that was not actually
		// intended to represent a date.
		elseif(preg_match("/^(\d{1,2}|\d{4})[\/\-]\d{1,2}[\/\-](\d{1,2}|\d{4})([^\d].+)?$/",$item)) {
			$type = 'DateTime';
		} else {
			$type = 'String';
		}

		$item = str_replace('&#039;', '&apos;', htmlspecialchars($item, ENT_QUOTES));
		$output .= " ";
		if(!in_array($this->y.$this->x, $this->cellHide)) {
			$output .= "<Cell $merge cord=\"".$this->y.$this->x."\" ss:Index=\"".$this->z."\" ss:StyleID=\"".$style."\">";
			$output .= sprintf("<Data ss:Type=\"%s\">%s</Data>", $type, $item);
			$output .= "</Cell>\n";
		}
		return $output;
	}

	function sendHttpHeaders() {
		//cek browser karena mikocok dengan IE nya agak nyeleneh :P
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE)
		{
			header('Content-Type: "application/vnd.ms-excel"; charset="' . $this->encoding .'"');
			header('Content-Disposition: attachment; filename="'. basename($this->filename) .'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
		}
		else
		{
			header('Content-Type: "application/vnd.ms-excel"; charset="' . $this->encoding .'"');
			header('Content-Disposition: attachment; filename="'. basename($this->filename) .'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
		}
	}

}
