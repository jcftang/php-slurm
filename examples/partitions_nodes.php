<?php
        ob_start();
	$GLOBALS["_var_title"] = "Show partitions/nodes";
        require_once("includes/index_stub_top.php");
	
	//
	//	BEGINNING OF BLOCK
	//

	$error_message = NULL;
	$nameArray = slurm_print_partition_names();
	require_once("objects/Partition.php");
	require_once("objects/Node.php");
	require_once("objects/Job.php");
	if(isset($nameArray) && $nameArray!=NULL && is_array($nameArray)) {
		$recordCount = count($nameArray);
		if($recordCount > 0) {
			$tmp_state_arr = CF_NODE_SUMMARY();
			$partitions = process_partition_names($nameArray);
			$job_partitions_status = get_full_job_summary();
			
			$nodes = array();
			
			foreach($partitions as $part) {
				array_push($nodes,get_nodes_from_partition($part));
			}

			$out = "<div class='div_commander'>";
			$out .= "<table class='table_regular' id='node_state_grid'><caption>Node States</caption>";
			$out_partition_links = "<table class='table_fixed'><caption>Partitions</caption>";
			$nrOfArrays=count($partitions);
			
			////
			////	START PARTITION TABLE
			////
			
			for($ipart=0;$ipart<$nrOfArrays;$ipart++) {
				
				/*
				* Create the node grid for the partition
				*/

				$length = count($nodes[$ipart]);
				$current_col = 0;
				$current_node_arr = $nodes[$ipart];
				$out_tmp ="<tr>";
				$hostlist_row = "<tr>";
				for($x=0;$x<$length;$x++) {
					if($current_col==GRID_COL_MIN) {
						$current_col = 0;
						$out_tmp .= "</tr><tr>";
					}
					$checker = true;
					$nde = $current_node_arr[$x];
					foreach($tmp_state_arr as $value) {
						if($nde->node_state==$value[2]) {
							$out_tmp .= "<td class='".$value[0]."' id='".$nde->name."'>".$nde->name."</td>";
							$checker = false;
							break;
						}
					}
					if($checker) {
						$out_tmp .= "<td class='".UNKNOWN_COLOR_CLASS."' id='".$nde->name."'>".$nde->name."</td>";
						$checker = true;
					}
					$current_col++;
				}
				while($current_col!=GRID_COL_MIN) {
					$out_tmp .= "<td class='table_header_partition'></td>";
					$current_col++;
				}

				/*
				* Create the header for this partition's part of the table
				*/

				$outh = "<tr><th class='table_header_partition' id='".$partitions[$ipart]->PartitionName."' colspan='".GRID_COL_MIN."'>";
				$outh .= "<span class='not_over_link2'><a class='is_link2' onclick=\"loadPage('nodes.php?hostlist=".$partitions[$ipart]->PartitionName;
				$outh .= "&action=-1')\">";
				$outh .= $partitions[$ipart]->PartitionName."[".$length."]</a></span>";
				$outh .= "<span class='right_floater'><a href='#TOP'>Go to Top</a></span>";
				$outh .= "<table class='right_floater2'><tr>";
                                $outh .= "<td class='right_floater3'><a onclick=\"loadPage('jobs.php?partition=".$partitions[$ipart]->PartitionName."&action=PENDING')\">";
				$outh .= "Pending=".$job_partitions_status["PARTITION"][$partitions[$ipart]->PartitionName]["JOBS"][0]."</a></td>";
				$outh .= "<td class='right_floater3'><a onclick=\"loadPage('jobs.php?partition=".$partitions[$ipart]->PartitionName."&action=RUNNING')\">";
				$outh .= "Running=".$job_partitions_status["PARTITION"][$partitions[$ipart]->PartitionName]["JOBS"][1]."</a></td>";
				$outh .= "<td class='right_floater3'><a onclick=\"loadPage('jobs.php?partition=".$partitions[$ipart]->PartitionName."')\">";
				$outh .= "All Jobs</a></td></tr></table>";
				$outh .= "</th></tr>";
				$out_partition_links .= "<tr><td><a href='#".$partitions[$ipart]->PartitionName."'>".$partitions[$ipart]->PartitionName."[".$length."]</a></td></tr>";
				$outh_desc_partition = "<tr>";
				$tmp_index=0;
				
				/*
				* Create partition specific nodes/states summary
				*/
				
				$node_states_arr = get_partition_node_summary($partitions[$ipart],0);
				foreach($tmp_state_arr as $value) {
					$outh_desc_partition .= "<th class='table_header_summary'>".$value[1]."<span ";
					if($node_states_arr[$value[2]]!=0) {
						$outh_desc_partition .= "class='big_number' id='";
					} else {
						$outh_desc_partition .= "class='big_number_over' id='";
					}
					$outh_desc_partition .= $partitions[$ipart]->PartitionName.'['.$value[2]."'";
					if($node_states_arr[$value[2]]!=0) {
						$outh_desc_partition .= "onclick=\"loadPage('nodes.php?hostlist=".$partitions[$ipart]->PartitionName;
						$outh_desc_partition .= "&action=".$value[2]."')\"";
					}
					$outh_desc_partition .= ">".$node_states_arr[$value[2]]."</span></th>";
					$tmp_index++;
				}
				while($tmp_index!=(GRID_COL_MIN)) {
					$outh_desc_partition .= "<th class='table_header_summary'></th>";
					$tmp_index++;
				}

				/*
				* Create hostlist summary for the 3 basic states ( Down,Idle,Allocated ) 
				*/

				$tmp_index = 0;
				foreach(get_hostlists_from_partition($partitions[$ipart]->PartitionName) as $host_key => $host_value) {
					$tmp_index+=2;
				 	$hostlist_row .= "<th colspan='2' class='not_over_link2'>".$host_key;
                                        $hostlist_row .= "<br />";
					$hostlist_row .= "<span class='is_link2' onclick=\"loadPage('nodes.php?hostlist=".$host_value."')\">";
                                        $hostlist_row .= $host_value."</span></th>";
				}
				while($tmp_index!=(GRID_COL_MIN)) {
					$hostlist_row .= "<th class='table_header_summary'></th>";
					$tmp_index++;
				}
				$hostlist_row .= "</tr>";

				/*
				* Finish up this partition's output
				*/

				$outh_desc_partition .= "</tr>";
				$outh .= $outh_desc_partition;
				$outh .= "<tr><th class='table_header_partition' colspan='".GRID_COL_MIN."'></th></tr>";
				$out .= $outh.$out_tmp.$hostlist_row;
				$out .= "<tr><th class='table_header_partition' colspan='".GRID_COL_MIN."'></th></tr>";
                                $out .= "<tr><td colspan='".GRID_COL_MIN."' class='invisible_to_your_eyes'></td></tr>";
                                $out .= "</tr>";
			}
			$out .= "</table></div>";
			
			////
			////	END PARTITION TABLE
			////

			////
			////	START SIDEBAR TABLE
			////
	
			$out .= "<div class='secondary_functionality'>";

			/*
			* Partition Links
			*/	

			$out .= "<div class='left_floater'>".$out_partition_links."</table></div>";

			/*
			* Legend Table
			*/

			$out .= "<div class='left_floater'>";
			$out .= require_once('grid_legend.php');
			$out .= "</div>";

			/*
			* Node summary table
			*/

			$full_states_arr = get_full_node_summary();
			$out .= "<table class='table_fixed'><caption>Nodes Summary</caption>";
			for($i=0;$i<count($tmp_state_arr);$i++) {
				$out .= "<tr><td>".$tmp_state_arr[$i][1]."</td><td>".$full_states_arr[$i]."</td></tr>";
			}
			$out .= "</table>";

                        /*
			* Jobs summary table
			*/
			$out .= "<table class='table_fixed'><caption>Jobs Summary</caption>";
			$out .= "<tr><td class='not_over_link2'><span class='is_link3' onclick=\"loadPage('jobs.php?action=PENDING')\">";
			$out .= "Pending</td><td>".$job_partitions_status["TOTAL"]["JOBS"][0]."</span></td></tr>";
			$out .= "<tr><td class='not_over_link2'><span class='is_link3' onclick=\"loadPage('jobs.php?action=RUNNING')\">";
			$out .= "Running</td><td>".$job_partitions_status["TOTAL"]["JOBS"][1]."</a></span></td></tr>";
			$out .= "</table></div>";

			$out .= "</div>";

			////
			////	END SIDEBAR TABLE
			////

			echo $out."<script type='text/javascript' src='script/partitions_nodes_hovers.js'></script>";
		} else {
			$error_message = "No partitions could be found";
			require_once(SLURM_NOGOOD);
		}
	} else {
		require_once(SLURM_NODAEMON);
	}
	
	//
	//	END OF BLOCK
	//

	require_once("includes/index_stub_bottom.php");
?>
