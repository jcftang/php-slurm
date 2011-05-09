<?php
	ob_start();
        require_once("functions.php");
        require_once("doc_top.php");
?>
<div id="main">
        <div id="header_bar"><span>Slurm status page</span></div>
        <div id="top_base">
                <?php $daemons_ok = require_once("ping_check.php"); ?>
        </div>
        <div id="container_links">
                <?php require_once("actions.php"); ?>
        </div>
	<div id="function_results">

