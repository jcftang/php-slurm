<?php
	ob_start();
	$GLOBALS['_var_title'] = "Slurm - Function Check";
	require_once("includes/index_stub_top.php");

	//
	//	BEGINNING OF BLOCK
	//
	
	/*
		Field declarations for the different function purposes
		
		Each separate array is an array where the first element represents
		the type of the functions, the second element is an array containing
		all the c functions that are available for this type.
	*/ 
	
	$status_functions = array("Status Functions",
				array(	"slurm_ping",
					"slurm_slurmd_status",
					"slurm_version"));
	$partition_read_functions = array("Partition Read Functions",
					array(	"slurm_print_partition_names",
						"slurm_get_specific_partition_info",
						"slurm_get_partition_node_names"));
	$node_configuration_read_functions = array("Node Configuration Read Functions",
						array(	"slurm_get_node_names",
							"slurm_get_node_elements",
							"slurm_get_node_element_by_name",
							"slurm_get_node_state_by_name",
							"slurm_get_node_states"));
	$configuration_read_functions = array("Configuration Read Functions",
						array(	"slurm_get_control_configuration_keys",
							"slurm_get_control_configuration_values"));
	$job_read_functions = array("Job Read Functions",
				array(	"slurm_load_job_information",
					"slurm_load_partition_jobs"));
	$hostlist_functions = array("Hostlist Functions",
				array(	"slurm_hostlist_to_array",
					"slurm_array_to_hostlist"));

	/*
		Parent array containing all the separate arrays
	*/

	$functionArray = array(	$status_functions,
				$partition_read_functions,
				$node_configuration_read_functions,
				$job_read_functions,
				$hostlist_functions);

	/*
		Process the function array to obtain our preferred output
	*/

	$function_table = "<div class='div_commander'><table class='table_regular' id='table_functions'><caption>Installed Functions</caption>";
	if(count($functionArray)!=0) {
		foreach($functionArray as $value) {
			if(is_array($value)) {
				$function_table .= "<tr><th class='table_header_summary' colspan='2'>".$value[0]."</th></tr>";
				if(is_array($value[1])) {
					foreach($value[1] as $func) {
						if(function_exists($func)) {
							$function_table .= "<tr><td>".$func."</td><td class='function_check_exist'></td></tr>";
						} else {
							$function_table .= "<tr><td>".$func."</td><td class='function_check_noexist'></td></tr>";
						}
					}
				} else {
					$function_table .= "<tr><td>No functions found ?</td></tr>";
				}
				$function_table .= "<tr><td colspan='2' class='invisible_to_your_eyes'></td></tr>";
			}
		}
	} else {
		$function_table .= "<tr><td>No functions found ?</td></tr>";
	}
	$function_table .= "</table></div>";
	
	echo $function_table;
	
	//
	//	END OF BLOCK
	//
	
	require_once("includes/index_stub_bottom.php");
?>
