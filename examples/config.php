<?php
	ob_start();
	$GLOBALS['_var_title'] = "Slurm Config";
	require_once("includes/index_stub_top.php");
	
	//
	//	BEGINNING OF BLOCK
	//

	/*
		Use the array returned from get_configuration and use it to create a table 	
	*/

	$out = "<div class='div_commander'><table class='table_regular'><caption>Configuration</caption><tr><th>Description</th><th>Value</th></tr>";
	foreach(get_configuration() as $key => $value) {
		$out .= "<tr><td>".$key."</td>".validate_data_value($value,true)."</tr>";
	}
	$out .= "</table></div>";
	echo $out;

	//
	//	END OF BLOCK
	//

	require_once("includes/index_stub_bottom.php");
?>
