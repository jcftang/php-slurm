<?php
	$GLOBALS["_var_title"] = "Slurm - Jobs";
	require_once("includes/index_stub_top.php");
	
	//
	//	BEGINNING OF BLOCK;
	//

	require_once("objects/Job.php");
	
	/*
	* Process passed partition parameter (if any)
	*/
	
	$out = "";
	if(isset($_GET['partition']) && !empty($_GET['partition'])) {
		$job_arr = slurm_load_partition_jobs($_GET['partition']);
		if(!is_array($job_arr)) {
			$id_warn = "wnmp";
			$out .= "<div class='secondary_functionality_nojs' id='".$id_warn."'><table class='table_fixed'><caption>Warning</caption>";
			$out .= "<tr><td>No Partitions found by that name, so we're showing the full list <span class='styled_link'><a onclick=\"closeMe('".$id_warn."')\">Ok</a></span></td></tr>";
			$out .= "</table></div>";
			$job_arr = slurm_load_job_information();
		}
	} else {
		$job_arr = slurm_load_job_information();
	}

	/*
	* Process the job array to obtain the output
	*/

	if(count($job_arr) > 0) {

		$out = "<div class='div_commander'><table class='table_regular'><caption>Job Data</caption>";
		$members = get_class_vars("Job");
		$length = count($members);
		$out .= get_table_head_from_class($members);

		$job_arr = process_raw_job_array($job_arr);

		foreach($job_arr as $key => $val) {
			if(is_array($val) && count($val)>0) {
				usort($val, array("Job","cmp"));
				if(isset($_GET['action']) && !empty($_GET['action'])) {
					foreach($val as $inner) {
						$out .= $inner->get_as_row($_GET['action']);
					}
				} else { 
					foreach($val as $inner) {
						$out .= $inner->get_as_row();
					}
				}
				$out .= "<tr><td colspan='".$length."'></td></tr>";
			}
		}
		$out .= "</table></div>";
		$out .= "<script type='text/javascript' src='script/jobs.js'></script>";
		echo $out;
	} else {
		$error_message = "No jobs could be found";
		require_once(SLURM_NOGOOD);
	}

	//
	//	END OF BLOCK
	//

	require_once("includes/index_stub_bottom.php");
?>
