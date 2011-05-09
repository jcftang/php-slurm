<?php
	ob_start();
	$GLOBALS["_var_title"] = "Slurm - Nodes";
	require_once("includes/index_stub_top.php");
	
	//
	//	BEGINNING OF BLOCK;
	//

	require_once("objects/Node.php");
	$node_arr = array();
	if(isset($_GET['hostlist']) && $_GET['hostlist']!="N/A") {
		if(isset($_GET['action']) && $_GET['action']<SLURM_LAST_STATE) {
			$node_array = filter_nodes_partition_state($_GET['hostlist'],$_GET['action']);
			foreach($node_array as $value) {
				$node = slurm_get_node_element_by_name($value);
				$node_arr[$value] = $node[$value];
			}
		} else { 
			foreach(slurm_hostlist_to_array($_GET['hostlist']) as $split_node) {
				$temp_nde = slurm_get_node_element_by_name($split_node);
				$node_arr[$split_node] = $temp_nde[$split_node];
			}
		}
	} else {
		$node_arr = slurm_get_node_elements();
	}
	$error_message = NULL;
	if(isset($node_arr) && is_array($node_arr)) {
		if(is_array($node_arr) && count($node_arr)!=0) {
			$node_out = "<div class='div_commander' id='node_main_window'><table id='nodeinfo' class='table_regular'><caption>Node Overview</caption><tr>";
			$node_out_top = "<div class='secondary_functionality_nojs_nofix' id='node_hostlist_container'><table class='table_regular' id='sec_func_inner'>";
			$node_out_top .= "<caption>Hostlist Summary</caption><tr>";

       			$node_out .=  get_table_head_from_class(get_class_vars("Node"));
			$tmp_arr = array();
			$node_arr = process_raw_node_array($node_arr,3);
			$node_out .= $node_arr["ONE_LINE"];
			if(isset($_GET['hostlist']) && $_GET['hostlist'] != NULL && $_GET['hostlist']!= "N/A") {
				foreach($node_arr["NODES"] as $val) {
					array_push($tmp_arr,$val->name);
				}
				$tmp_arr = slurm_array_to_hostlist($tmp_arr);
				$node_out_top .= "<th class='table_header_summary'>All Nodes on page</th></tr><tr><td>".$tmp_arr["HOSTLIST"]."</td>";
			} else {
				$tmp_arr = $node_arr["NODES"];
				foreach(get_hostlists_from_array($tmp_arr) as $key => $val) {
					$node_out_top .= "<td class='table_header_summary'>".$key."<span class='";
					if($val != "N/A") {
						$node_out_top .= "small_number_clickable' onclick=\"loadPage('nodes.php?hostlist=".$val."')\">".$val;
					} else {
						$node_out_top .= "small_number'>".$val;
					}
					$node_out_top .= "</span></td>";
				}
			}
			$node_out_top .= "</tr></table></div><div class='secondary_functionality_nojs_nofix' id='node_summary_container'>";
			$node_out_top .= "<table class='table_regular' id='sec_func_inner2'><caption>Node Summary</caption><tr>";
			foreach($node_arr["SUMMARY"] as $key => $val) {
				$node_out_top .= "<td class='table_header_summary'>".$key."<span class='small_number'>".$val."</span></td>";
			}
			$node_out_top .= "</tr></table></div>";
			$node_out .= "</table></div>";
			$node_out .= "<div class='secondary_functionality' id='legend_grid'><div class='left_floater'>";
			$no_col	= true;
			$node_out .= require_once("grid_legend.php");
			$node_out .= "</div></div>";
			$node_out .= "<script type='text/javascript' src='script/nodes.js'></script>";
			echo $node_out_top.$node_out;
		} else {
			$error_message = "No nodes are available";
			require_once(SLURM_NOGOOD);
		}
	} else {
		$error_message = "Daemon not available";
		require_once(SLURM_NODAEMON);
	}

	//
	//	END OF BLOCK
	//

	require_once("includes/index_stub_bottom.php");
?>
