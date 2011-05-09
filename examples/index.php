<?php
	ob_start();

	$GLOBALS['_var_title'] = "Slurm Homepage";

	require_once("includes/functions.php");
	require_once("includes/doc_top.php");
?>
<div id="main">
	<div id="header_bar"><span>Slurm status page</span></div>
	<div id="top_base">
		<?php
			$daemons_ok = require_once("ping_check.php");
		?>
	</div>
	<div id="container_links">
		<?php 
			require_once("actions.php");
		?>
	</div>
</div>
<?php 
	require_once("includes/doc_bottom.php");
?>
