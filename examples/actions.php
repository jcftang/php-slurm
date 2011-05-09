<?php
	/*
	* Field declaration/initialization
	*/
	
	$array = array();
	$array[0] = array("Load Partitions","partitions.php");
	$array[1] = array("Load Nodes","nodes.php");
	$array[2] = array("Check Functions","function_check.php");
	$array[3] = array("Show Configuration","config.php");
	$array[4] = array("Show Partitions/Nodes","partitions_nodes.php");
	$array[5] = array("Jobs","jobs.php");
	// Field used to set the maximum allowed rows for our actions grid
	$row_max = 2;
	// Field used to set the maximum allowed collumns for our actions grid
	$col_max = ceil(count($array)/$row_max);
	// Field used to grab the correct element from our actions array 
	$x = count($array);
	
	/*
	* Processing fields to obtain output
	*/
	
	$out = "<table class='table_buttons' id='link_container'><caption>Actions</caption>";
	for($y=0;$y<$row_max;$y++) {
		$out .= "<tr>";
		for($col_index=0;$col_index<$col_max;$col_index++) {
			if($ok && $daemons_ok) {
				$out .= "<td class='not_over_link'><span class='is_link' onclick=\"loadPage('".$array[$x-1][1]."')\";>".$array[$x-1][0]."</span></td>";
			} else {
				$out .= "<td class='not_over_link'><span class='no_link'>".$array[$x-1][0]."</span></td>";
			}
			$x--;
			if($x==0) {
				break;
			}
		}
		$out .= "</tr>";
		if($x==0) {
			break;
		}
	}
	$out .= "</table>";
	echo $out;
?>
