<?php
	require_once("includes/functions.php");
	
	/*
	*  Use the node summary array to create a color legend
	*/
	
	$legend = "<table class='table_fixed'><caption>Node States Legend</caption>";
	if(isset($no_col) && $no_col) {
		$legend .= "<tr><th>#State</th><th>Meaning</th></tr>";
		foreach(CF_NODE_SUMMARY() as $value) {
			$legend .= "<tr><td>".$value[2]."</td><td>".$value[1]."</td></tr>";
		}
	} else {
		$legend .= "<tr><th>#State</th><th>Meaning</th><th>Color</th></tr>";
		foreach(CF_NODE_SUMMARY() as $value) {
			$legend .= "<tr><td>".$value[2]."</td><td>".$value[1]."</td><td class='".$value[0]."'></td></tr>";
		}
	}
	$legend .= "</table>";
	return $legend;
?>
