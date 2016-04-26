<?
$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
$this->output->set_header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
$this->output->set_header("Cache-Control: no-cache, must-revalidate" );
$this->output->set_header("Pragma: no-cache" );
$this->output->set_header("Content-type: text/x-json");

$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $num,\n";
$json .= "rows: ";
$rows = array();
if (isset($db)) {
	 
	foreach($db->result_array() as $row):
	
	$key = key($row);
	$id = $row[$key];
	unset($row[$key]);
	$cell = array();
		foreach ($row as $item) 
			{ 
				$cell[] = $item;				
			}
	$rows[] = array(
	 
		"id" => $id,
		"cell" => $cell
		);
		
	endforeach; 
	
}

$json .= json_encode($rows);
if (isset($moredata))
	$json .= ",".json_encode(array('moredata'=>$moredata));
$json .= "\n";
$json .= "}";
echo $json;
?>