<?php 
	$out = "<table class='table_regular' id='slurm_ping'><caption>Slurm Controller Status</caption>";
	$ok = false;
	if(function_exists("slurm_ping")) {
		$slurm_ping = slurm_ping();
		foreach($slurm_ping as $key => $value) {
			$out .= "<tr><td>".$key."</td>";
			if($value==0) {
				$ok = true;
				$out .= "<td class='color_green'>Online</td></tr>";
			} else if($value<0){
				$out .= "<td class='color_red'>Offline</td></tr>";
			}
		}
	} else {
		$out .= "<tr><td colspan='2'>Function slurm_ping() doesn't exist</td></tr>";
	}
	$bool = function_exists("slurm_ping()");
	echo $out;
	return $ok;
?>
