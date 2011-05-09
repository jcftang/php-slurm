<?php
	ob_start();
	$GLOBALS["_var_title"] = "Slurm - Partitions";
	require_once("includes/index_stub_top.php");

	//
	//	BEGINNING OF BLOCK
	//

	$error_message = NULL;
	$nameArray = slurm_print_partition_names();
	require_once("objects/Partition.php");
	require_once("objects/Node.php");
	if(isset($nameArray) && $nameArray!=NULL && is_array($nameArray)) {
		if(count($nameArray) > 0) {
			$partitionInfo = "";
			$partition_objects = process_partition_names($nameArray);

			$count_arr = array(0,0);
			foreach($partition_objects as $value) {
				$count_arr[0] += $value->TotalCPUs;
				$count_arr[1] += $value->TotalNodes;
				$partitionInfo .= $value->get_as_row();
			}

			$partitionInfo = get_table_head_from_class(get_class_vars("Partition")).$partitionInfo;
			$partitionInfo = "<div class='div_commander'><table class='table_regular'><caption>Partition Data</caption><tr>".$partitionInfo;
			$partitionInfo .= Partition::get_total_row($count_arr)."</table></div>";

			//	Sinfo-style table comes below here 
			
			$sinfo = "<div class='div_commander'><table class='table_regular'><caption>Sinfo</caption><tr>";
			$tmp = array("Nodelist","Nodes","Partition","State","CPUS","S:C:T","Memory","TMP_DISK","Weight","Features","Reason");
			$sinfo_cells = "";

			$nodes = array();

			//	<:~ Here be dragons ~:>
			foreach($tmp as $val) {
				$sinfo .= "<th class='table_header_summary'>".$val."</th>";
			}
			$sinfo .= "</tr>";

			//	SINFO SORTING BEGIN

			foreach($partition_objects as $tmp_part) {
				$partition_nodes = get_nodes_from_partition($tmp_part);
				$partition_nodes_sorted = split_into_associative_arrays($partition_nodes);
				foreach($partition_nodes_sorted as $key => $val) {
					$name_arr = array();
					foreach($val as $nde) {
						array_push($name_arr,$nde->name);
					}
					$arr = slurm_array_to_hostlist($name_arr);
					$sinfo .= create_sinfo_row_from_node($val[0],$tmp_part,$arr["HOSTLIST"],count($val));
				}
			}

			//	SINFO SORTING END

			$sinfo .= "</table></div>";

			echo $partitionInfo.$sinfo;
		} else {
			$error_message = "No partitions could be found";
			require_once(SLURM_NOGOOD);}
	} else {
		require_once(SLURM_NODAEMON);
	}

	//
	//	END OF BLOCK
	//

	require_once("includes/index_stub_bottom.php");
?>
