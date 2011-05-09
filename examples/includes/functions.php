<?php
	/** 
	* The functions.php file contains all sorts of functions to process/retreive 
	* data from the C interface and simplify writing a custom module/website.
	*
	* This includes the following : 
	* <ul>
	*	<li>Data retrieval</li>
	*	<li>Processing</li>
	*	<li>Summary generation</li>
	*	<li>Basic sorting</li>
	*	<li>Basic filtering</li>
	*	<li>Conversion</li>
	*	<li>Variable validation/HTML generation</li>
	* </ul><br />
	* <b>Sidenote(s) :</b>
	* <ul>
	*	<li>All of the object-oriented files contain a small amount of 
	*	functionality specific to that certain object (parsing, keygeneration, ...)
	* </ul><br />
	* <b>Metadata</b>
	* @link https://computing.llnl.gov/linux/slurm/
	* @author Peter Vermeulen <nmb.peterv@gmail.com>
	* @version 1.0
	* @license http://www.gnu.org/licenses/gpl-2.0.html
	* @package includes
	*/ 
/**
* A required include because most of the functions defined in this file use the 
* constants defined in 'constants.php'.
*/
require_once("constants.php");

if(isset($_POST['action']) && !empty($_POST['action'])) {
	$action = $_POST['action'];
	switch($action) {
		case 'filter_nodes_partition_state':
			if(isset($_POST['pname']) && !empty($_POST['pname']) && isset($_POST['state'])) {
				$pname = $_POST['pname'];
				if($_POST['state']>=0) {
					$nstate = $_POST['state'];
					
					$nodeArr = filter_nodes_partition_state($pname,$nstate);
					
					$out = "";
					if(is_array($nodeArr)) {
						$out = implode("<|>", $nodeArr);
					} else {
						$out = -1;
					}
					echo $out;
				}
			}
	}
}

////////////////////////////////////////////////////////////////////////////////
//	DATA RETREIVAL FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* Calls the <b>slurm_get_control_configuration_keys()</b> and <b>slurm_get_control_configuration_values()</b> functions<br /> and combines the output from both functions into an associative array.<br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*foreach(get_configuration() as $key => $value) {
*	$out .= "<tr><td>".$key."</td>".validate_data_value($value,true)."</tr>";
*}
*...
* </code><br />
* @return array an associative array containing the configuration
* @see slurm_get_control_configuration_keys(),slurm_get_control_configuration_values()
*/
function get_configuration()
{
	$keys = slurm_get_control_configuration_keys();
	$values = slurm_get_control_configuration_values();
	$rslt_arr = array();
	$lngth = count($keys);

	for($i=0;$i<$lngth;$i++) {
		$rslt_arr[$keys[$i]] = $values[$i];
	}
	return $rslt_arr;
}

/**
* Calls the <b>slurm_version()</b> function to retrieve the version number, an 
* optional parameter can be passed on to retreive different parts of the version number.<br />
* These optional parameters are defined as constants in the 'constants.php' file and start with 'SLURM_VERSION'.
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*echo "Major :".get_slurm_version(SLURM_VERSION_MAJOR);
*echo "Full :".get_slurm_version();
* </code>
* @param int an optional parameter representing the part of the version you'd like to see returned 
* @return String string value representing the full version or part of the version
* @see SLURM_VERSION_MAJOR, SLURM_VERSION_MINOR, SLURM_VERSION_MICRO, slurm_version()
*/
function get_slurm_version($option=4)
{
	$result = slurm_version($option);
	switch($option)
	{
		case 0:
		case 1:
		case 2:
			break;
		default:
			$result = $result[0].".".$result[1].".".$result[2];
			break;
	}
	return "".$result;	
}

/**
* Accepts a partitionName and filters the nodes into separate arrays by state<br />
* These arrays are the following : 
* <ul>
*	<li><b>NOT_AVAILABLE</b> : contains all the nodes in an <b>unknown</b>,<b>down</b> or <b>error</b> state</li>
*	<li><b>IDLE</b> : contains all the idle nodes</li>
*	<li><b>ALLOCATED</b> : contains all the allocated nodes</li>
* </ul>
* Afterwards each of these 3 arrays will be processed into a hostlist and returned to the calling function.<br />However should the nodelist
* for any one of these arrays be empty a "N/A" string value will be added instead.
* <br /><b>Usage</b>
* <code>
*$your_part_name = "whatever";
*$out = "<table><caption>Hostlist Summary for : ".$your_part_name."<caption>";
*foreach(get_hostlists_from_partition($your_part_name) as $host_key => $host_value) {
*	$out .= "<tr><td>".$host_key."</td><td>".$host_value."</td></tr>";
*}
*echo $out."</table>";
* </code>
* @param String the name of a partition
* @return array An associative array containing 3 elements
* @see slurm_get_partition_node_names(), slurm_hostlist_to_array(), slurm_get_node_state_by_name(), slurm_array_to_hostlist()
*/
function get_hostlists_from_partition($partitionName)
{
	$results 	= array("NOT_AVAILABLE" => array(),"IDLE" => array(), "ALLOCATED" => array());

	$partition_nodenames = array();
	$partition_tmp = slurm_get_partition_node_names($partitionName);
	foreach($partition_tmp as $hostl) {
		foreach(slurm_hostlist_to_array($hostl) as $inner_value) {
			array_push($partition_nodenames,$inner_value);
		}
	}
	foreach($partition_nodenames as $nodename) {
		switch(slurm_get_node_state_by_name($nodename))
		{
			case SLURM_NODE_STATE_IDLE :
				array_push($results["IDLE"], $nodename);
				break;
			case SLURM_NODE_STATE_ALLOCATED :
				array_push($results["ALLOCATED"], $nodename);
				break;
			case SLURM_NODE_STATE_UNKNOWN:
                        case SLURM_NODE_STATE_DOWN:
                        case SLURM_NODE_STATE_ERROR:
			default:
				array_push($results["NOT_AVAILABLE"], $nodename);
                                break;
		}
	} 
	foreach($results as $key => $val) {
		if(count($val) != 0) {
			$tmp = slurm_array_to_hostlist($val);
			$val = $tmp["HOSTLIST"];
		} else {
			$val = "N/A";
		}
		$results[$key] = $val;
	}
	return $results;
}

/**
* This function retrieves all the nodes belonging to a certain partition and 
* processes them into separate Node objects
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*$nameArray = slurm_print_partition_names();
*$partitions = process_partition_names($nameArray);
*$nodes = array();
*foreach($partitions as $part) {
*	array_push($nodes,get_nodes_from_partition($part));
*}
*var_dump($nodes);
* </code>
* @param Partition a Partition object
* @return array An array containing the nodes belonging to a specific partition
* @see Partition.php, Node.php, slurm_get_node_element_by_name()
*/
function get_nodes_from_partition($partition)
{
	require_once("objects/Node.php");
	$node_tmp = array();
        foreach(slurm_hostlist_to_array($partition->Nodes) as $split_node) {
		$temp_nde = slurm_get_node_element_by_name($split_node);
		$nde = new Node();
		$nde->parse_node_array($temp_nde[$split_node]);
                array_push($node_tmp,$nde);
	}
	return $node_tmp;
}

////////////////////////////////////////////////////////////////////////////////
//	PROCESSING FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* Receives an array of Node objects and processes them to create an associative array
* where the node state is the key and the hostlist of the nodes in that state is the value.
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>If there are no nodes for a certain state, the value returned for that state will be "N/A"</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*$part = new Partition();
*$part->parse_partition_array(slurm_get_specific_partition_info("your_partition_name"));
*$nodes = get_nodes_from_partition($part);
*foreach(get_hostlists_from_array($nodes) as $key => $val) {
*	if($val != "N/A") {
*		... Do something with this states' hostlist
*	} else {
*		... Do something if no nodes are in this state
*	}
*}
* </code>
* @param array An array containing the node objects to be processed
* @return array An associative array containing the specific hostlists for each state
* @see Node.php, Partition.php, get_nodes_from_partition(), slurm_array_to_hostlist()
*/
function get_hostlists_from_array($node_array)
{
	$results = array();
	$tmp = array("UNKNOWN","DOWN","IDLE","ALLOCATED","ERROR","MIXED","FUTURE","END");
	$filtered = array();
	for($i = 0;$i <= SLURM_LAST_STATE;$i++) {
		array_push($filtered,array());
	}
	foreach($node_array as $nde) {
		if($nde->node_state<SLURM_LAST_STATE) {	
			array_push($filtered[$nde->node_state],$nde->name);
		} else {
			array_push($filtered[0],$nde->name);
		}
	}
	for($i = 0;$i <= SLURM_LAST_STATE;$i++) {
		if(count($filtered[$i])==0) {	
			$results[$tmp[$i]] = "N/A";
		} else {
			$tmp_hl = slurm_array_to_hostlist($filtered[$i]);
			$results[$tmp[$i]] = $tmp_hl["HOSTLIST"];
		}
	}
	return $results;
}

/**
*Receives an array containing names of partitions on your system and retreives/processes
*the specific data for that partition into a Partition object. 
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*$nameArray = slurm_print_partition_names();
*$partitions = process_partition_names($nameArray);
*foreach($partitions as $part) {
*	... Do something with the Partition object
*}
* </code>
* @param array An array containing names of partitions
* @return array An array containing Partition objects
* @see Partition.php, slurm_get_specific_partition_info()
*/
function process_partition_names($nameArray)
{
	$length = count($nameArray);
	if($length == 0) {
		return NULL;
	}
	$result = array();
	$part;
	for($i=0;$i<$length;$i++) {
		$part = new Partition();
		$part->parse_partition_array(slurm_get_specific_partition_info($nameArray[$i]));
		array_push($result,$part);
	}
	return $result;
}

/**
*Receives a raw array of job data and processes it into an array of Job objects, an optional value
*can be passed along to sort the Job objects.
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>If the array is being sorted, the sorted array will be associative in nature and the <br />
*	keys of this associative array will be the different Jobstates.<br />If however the array isn't sorted<br />
*	it will be a numerically indexed array containing Job objects</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*$job_arr = slurm_load_job_information();
*$job_arr_processed = process_raw_job_array($job_arr);
*foreach($job_arr as $key => $val) {
*	... Do something with the Job object ($val)
*}
* </code>
* @param array $raw A raw array of job data returned from a call to the C module
* @param int $sorted An optional value that if set to 1 will call sort_jobs_by_state() to sort the job array (default = 1)
* @return array An array containing processed Job objects ( if the array is sorted, then this sorted array will be associative in nature )
* @see Job.php,sort_jobs_by_state()
*/
function process_raw_job_array($raw,$sorted=1)
{
	$length = count($raw);
	if($length == 0) {
		return NULL;
	}

	$result = array();
	$job;
	foreach($raw as $key => $value) {
		$job = new Job();
		$job->parse_job_array($value);
		array_push($result,$job);
	}
	
	switch($sorted) {
		case 1:
			return sort_jobs_by_state($result);
		default:
			return $result;
	}
}

/**
*Receives a raw array of node data and processes it into an array of Node objects, an optional integer value
*can be passed along to create an additional summary and/or oneline.
*
*<b>Summary</b>
*<ul>
*	<li>An associative array where each key links to an integer value, the keys are the following : 
*		<ul>
*			<li><b>#Nodes</b> : (int) The amount of nodes in the array</li>
*			<li><b>Cores</b> : (int) The sum of cores for all the nodes</li>
*			<li><b>CPU's</b> : (int) The sum of cpu's on all the nodes</li>
*			<li><b>Real Memory</b> : (String) The amount of real memory over all the nodes, the value is converted to Gb and appended with " Gb"</li>
*			<li><b>Sockets</b> : (int) The sum of sockets on all the nodes</li>
*			<li><b>Threads</b> : (int) The sum of threads on all the nodes</li>
*			<li><b>Tmp Disk</b> : (String) The amount of temp disk over all the nodes, the value is converted to Gb and appended with " Gb"</li>
*		</ul>
*	</li>
*</ul><br />
*<b>One Liner</b>
*<ul>
*	<li>A string value created by concatenating the get_as_row function on each node</li>
*</ul>
*
*<b>Possible options</b>
* <ul>
*	<li><b>0</b> : Only process the node array</li>
*	<li><b>1</b> : Process the node array and create a summary</li>
*	<li><b>2</b> : Process the node array and create a oneline string value representing the nodes
*	<li><b>3</b> : Process the node array and create both a summary and a online string value
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*$node_arr = slurm_get_node_elements();
*$nodes_processed = process_raw_node_array($node_arr,3);
*$one_liner_table = "<table>".$node_arr["ONE_LINE"]."</table>";
*$summary_table = "<table><caption>Summary</caption><tr>";
*foreach($node_arr["SUMMARY"] as $key => $val) {
*	$summary_table .= "<td>".$key." = ".$val."</td>";
*}
*... Do something with the nodes/one liner/summary table
* </code>
* @param array $node_arr An array containing raw node data
* @param int $option An optional value with which you can specify any extra processing that you'd want
* @return array an associative array containing the requested processed result ( if default option isn't changed the array is a regular array containing nodes )
* @see c_mb_to_gb(),fill_node_summary(),Node.php, 
*/
function process_raw_node_array($node_arr,$option=0) {
	$processed = array();
	$nde;
	//	Option Values
	//	-------------------------------
	//	0	Summary=NO  OneLine=NO
	//	1	Summary=YES OneLine=NO
	//	2	Summary=NO  OneLine=YES
	//	3	Summary=YES OneLine=YES
	//	DEFAULT Summary=NO  OneLine=NO
	//	-------------------------------
	switch($option) {
		case 1:
		case 3:
			$nodes = array();
			$sum_arr = array(
					"#Nodes"=>0,
					"Cores"=>0,
					"CPU's"=>0,
					"Real Memory"=>0,
					"Sockets"=>0,
					"Threads"=>0,
					"Tmp Disk"=>0);
			switch($option) {
				case 1:
					foreach($node_arr as $key => $value) {
						$nde = new Node();
						$nde->parse_node_array($value);
						$sum_arr = fill_node_summary($sum_arr,$nde);
						array_push($nodes,$nde);
					}
					$sum_arr["#Nodes"] = count($node_arr);
					$sum_arr["Real Memory"] = c_mb_to_gb($sum_arr["Real Memory"]);
					$sum_arr["Tmp Disk"] = c_mb_to_gb($sum_arr["Tmp Disk"]);
					$processed["NODES"] = $nodes;
					$processed["SUMMARY"] = $sum_arr;
					break;
				case 3:
					$one_line = "";
					foreach($node_arr as $key => $value) {
						$nde = new Node();
						$nde->parse_node_array($value);
						$one_line .= $nde->get_as_row();
						array_push($nodes,$nde);
						$sum_arr = fill_node_summary($sum_arr,$nde);
					}
					$sum_arr["#Nodes"] = count($node_arr);
					$sum_arr["Real Memory"] = c_mb_to_gb($sum_arr["Real Memory"]);
					$sum_arr["Tmp Disk"] = c_mb_to_gb($sum_arr["Tmp Disk"]);
					$processed["NODES"] = $nodes;
					$processed["SUMMARY"] = $sum_arr;
					$processed["ONE_LINE"] = $one_line;
					break;
			}
			break;
		case 2:
			$nodes = array();
			$one_line = "";
			foreach($node_arr as $key => $value) {
				$nde = new Node();
				$nde->parse_node_array($value);
				$one_line .= $nde->get_as_row();
				array_push($nodes,$nde);
			}
			$processed["NODES"] = $nodes;
			$processed["ONE_LINE"] = $one_line;
			break;
		case 0:
		default:
			foreach($node_arr as $key => $value) {
				$nde = new Node();
				$nde->parse_node_array($value);
				array_push($processed,$nde);
			}
			break;
	}
	return $processed;
}

////////////////////////////////////////////////////////////////////////////////
//	SUMMARY FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* Creates a "detailed" summary of the jobs on your system, this includes the following
* <ul>
*	<li>TOTAL
*		<ul>
*			<li>JOBS
*				<ul>
*					<li>count of pending jobs</li>
*					<li>count of running jobs</li>
*				</ul>
*			<li>CPUS
*				<ul>
*					<li>sum of the cpus over all the pending jobs</li>
*					<li>sum of the cpus over all the running jobs</li>
*				</ul>
*			</li>
*		</ul>
*	</li>
*	<li>PARTITION
*		<ul>
*			<li>"partitionname" (there's an entry for each partition)
*				<ul>
*					<li>JOBS
*						<ul>
*							<li>count of pending jobs on this partition</li>
*							<li>count of running jobs on this partition</li>
*						</ul>
*					<li>CPUS
*						<ul>
*							<li>sum of the cpus over all the pending jobs on this partition</li>
*							<li>sum of the cpus over all the running jobs on this partition</li>
*						</ul>
*					</li>
*				</ul>
*			</li>
*		</ul>
*	</li>
* </ul>
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>The upper case characters in the above list are the actual keys being used, the partitionname however is not in uppercase!</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*...
*$out = "<table>";
*$tmp = get_full_job_summary();
*$total = $tmp["TOTAL"];
*$out .= "<tr><th colspan='3'>Total</th></tr>";
*$out .= "<tr><th></th><th>Jobs</th><th>CPU's</th></tr>";
*$out .= "<tr><td>Pending</td><td>".$total["JOBS"][0]."</td><td>".$total["CPUS"][0]."</td></tr>";
*$out .= "<tr><td>Running</td><td>".$total["JOBS"][1]."</td><td>".$total["CPUS"][1]."</td></tr>";
*$part_total = $tmp["PARTITION"];
*foreach($part_total as $key => $value) {
*	$out .= "<tr><th colspan='3'>".$key."</th></tr>";
*	$out .= "<tr><th></th><th>Jobs</th><th>CPU's</th></tr>";
*	$out .= "<tr><td>Pending</td><td>".$value["JOBS"][0]."</td><td>".$value["CPUS"][0]."</td></tr>";
*	$out .= "<tr><td>Running</td><td>".$value["JOBS"][1]."</td><td>".$value["CPUS"][1]."</td></tr>";
*}
*$out .= "</table>";
* </code>
* @return array an associative array containing a detailed summary of the job data
* @see Job.php, slurm_print_partition_names()
* @example rrdtool/update_rrdtool.php Simple example of using get_full_job_summary() with php-rrdtool
*/
function get_full_job_summary()
{
	$jobs = slurm_load_job_information();
	if(count($jobs>0)) {
		$partNames = slurm_print_partition_names();
		$return_arr = array("TOTAL"=> array("JOBS"=>array(0,0),"CPUS"=>array(0,0)),"PARTITION"=>array());
		foreach($partNames as $val) {
			//	Job Check 0 = pending | 1 = running
			//	Cpus	  0 = Idle | 1 = In Use
			$new_arr = array("JOBS" => array(0,0),"CPUS" => array(0,0));	
			$return_arr["PARTITION"][$val] = $new_arr;
		}
		foreach($jobs as $key_job => $value_job) {
			switch($value_job["JobState"]) {
				case "PENDING":
					$return_arr["TOTAL"]["JOBS"][0]++;
					$return_arr["TOTAL"]["CPUS"][0]+=$value_job["NumCPUs"];
					$return_arr["PARTITION"][$value_job["Partition"]]["JOBS"][0]++;
					$return_arr["PARTITION"][$value_job["Partition"]]["CPUS"][0] += $value_job["NumCPUs"];
					break;
				case "RUNNING":
					$return_arr["TOTAL"]["JOBS"][1]++;
					$return_arr["TOTAL"]["CPUS"][1]+=$value_job["NumCPUs"];
					$return_arr["PARTITION"][$value_job["Partition"]]["JOBS"][1]++;
					$return_arr["PARTITION"][$value_job["Partition"]]["CPUS"][1] += $value_job["NumCPUs"];
					break;
				default:
					break;
			}
		}
		return $return_arr;
	} else {
		return NULL;
	}
}

/**
* Creates an array acting as a summary of the amount of nodes per state on your system, <br />
* the returned array is an 8-element array where each index acts as the specific state value.
* 
* <b>Sidenote(s) :</b>
* <ul>
*	<li>Should any nodes be in an unrecognized state (such as the elusive 2049) it will be added to<br />the UNKNOWN states' count</li>
*	<li>The SLURM_NODE_* constants are the same as the index values</b></li>
*	<li>An optional nodearray can be passed on to this function, if the optional value is passed on<br />the function will create a summary of those 
*	specific nodes, instead of creating a summary of all the nodes</li>
*	<li>To access this function on a partition-specific level, use the get_partition_node_summary()</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*$full_states_arr = get_full_node_summary();
*$tmp_state_arr = CF_NODE_SUMMARY();
*$out = "<table><caption>Nodes Summary</caption>";
*for($i=0;$i<count($tmp_state_arr);$i++) {
*	$out .= "<tr><td>".$tmp_state_arr[$i][1]."</td><td>".$full_states_arr[$i]."</td></tr>";
*}
*$out .= "</table>";
*echo $out;
* </code>
* @param array an optional array of node objects to be analyzed. If not passed on, the function will process all nodes.
* @return array a numerically indexed array where each index represents the count of nodes in a specific state
* @see constants.php,Node.php,get_partition_node_summary(),get_full_node_core_summary(),get_full_node_cpu_summary(), slurm_get_node_states()
*/
function get_full_node_summary($tmp=NULL) {
	$full_states_arr = array(0,0,0,0,0,0,0,0);
	$nodeArray;
	if($tmp == NULL) {
		$nodeArray = slurm_get_node_states();
	} else {
		$nodeArray = array();
		foreach($tmp as $node) {
			array_push($nodeArray,$node->node_state);
		}
	}
	foreach($nodeArray as $val) {
		if($val<SLURM_LAST_STATE) {
			$full_states_arr[$val]++;
		} else {
			$full_states_arr[0]++;
		}
	}
	return $full_states_arr;
}

/**
* A helper function defined to aid the process_raw_node_array() function when you pass it an<br />
* optional value that allows it to create a summary. 
*
* This function can be used separately but be warned, if you want to do this you need to define an associative array 
* that contains the following keys/values
* <ul>
*	<li>[Key=Cores][Value=0]</li>
*	<li>[Key=CPU's][Value=0]</li>
*	<li>[Key=Real Memory][Value=0]</li>
*	<li>[Key=Sockets][Value=0]</li>
*	<li>[Key=Threads][Value=0]</li>
*	<li>[Key=Tmp Disk][Value=0]</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*$nodes = array();
*$sum_arr = array(
*		"#Nodes"=>0,
*		"Cores"=>0,
*		"CPU's"=>0,
*		"Real Memory"=>0,
*		"Sockets"=>0,
*		"Threads"=>0,
*		"Tmp Disk"=>0);
*$node_arr = slurm_get_node_elements();
*foreach($node_arr as $key => $value) {
*	$nde = new Node();
*	$nde->parse_node_array($value);
*	$sum_arr = fill_node_summary($sum_arr,$nde);
*	array_push($nodes,$nde);
*}
*... Do something with the array
* </code>
* 
* @param array $table_summary An associative array containing summary values for specific fields in the node object
* @param Node $nde The node object of which the fields need to be added to the summary listing
* @return array the updated table summary (to be returned for output processing, or to be used in the next loop of an iteration)
* @see Node.php,process_raw_node_array()
*/
function fill_node_summary($table_summary,$nde) {
	$table_summary["Cores"]		+= $nde->cores;
	$table_summary["CPU's"]		+= $nde->cpus;
	$table_summary["Real Memory"]	+= $nde->real_memory;
	$table_summary["Sockets"]	+= $nde->sockets;
	$table_summary["Threads"]	+= $nde->threads;
	$table_summary["Tmp Disk"]	+= $nde->tmp_disk;
	return $table_summary;
}

/**
* Creates an array acting as a summary of the sum of cpu's for the nodes in that state, <br />
* the returned array is an 8-element array where each index acts as the specific state value.
* 
* <b>Sidenote(s) :</b>
* <ul>
*	<li>Should any nodes be in an unrecognized state (such as the elusive 2049) it will be added to<br />the UNKNOWN states' index</li>
*	<li>The SLURM_NODE_* constants are the same as the index values</b></li>
*	<li>An optional nodearray can be passed on to this function, if the optional value is passed on<br />the function will create a summary of those 
*	specific nodes, instead of creating a summary of all the nodes</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*$cpu_arr = get_full_node_cpu_summary();
*$tmp_state_arr = CF_NODE_SUMMARY();
*$out = "<table><caption>Nodes Summary</caption>";
*for($i=0;$i<count($tmp_state_arr);$i++) {
*	$out .= "<tr><td>".$tmp_state_arr[$i][1]."</td><td>".$cpu_arr[$i]."</td></tr>";
*}
*$out .= "</table>";
*echo $out;
* </code>
* @param array an optional array of node objects to be analyzed. If not passed on, the function will process all nodes.
* @return array a numerically indexed array where each index represents the sum of the amount of cpu's for nodes in a specific state
* @see constants.php,Node.php,get_partition_node_summary(),get_full_node_core_summary(),get_full_node_summary(), slurm_get_node_elements()
*/
function get_full_node_cpu_summary($tmp=NULL) {
	$full_cpu_arr = array(0,0,0,0,0,0,0,0);
	if($tmp == NULL) {
		foreach(slurm_get_node_elements() as $arr) {
			if($arr["State"]<SLURM_LAST_STATE) {
				$full_cpu_arr[$arr["State"]] += $arr["#CPU'S"];
			} else {
				$full_cpu_arr[0] += $arr["#CPU'S"];
			}
		}
	} else {
		foreach($tmp as $nde) {
			if($nde->node_state<SLURM_LAST_STATE) {
				$full_cpu_arr[$nde->node_state] += $nde->cpus;
			} else {
				$full_cpu_arr[0] += $nde->cpus;
			}
		}
	}
	return $full_cpu_arr;
}

/**
* Creates an array acting as a summary of the sum of cores for the nodes in that state, <br />
* the returned array is an 8-element array where each index acts as the specific state value.
* 
* <b>Sidenote(s) :</b>
* <ul>
*	<li>Should any nodes be in an unrecognized state (such as the elusive 2049) it will be added to<br />the UNKNOWN states' index</li>
*	<li>The SLURM_NODE_* constants are the same as the index values</b></li>
*	<li>An optional nodearray can be passed on to this function, if the optional value is passed on<br />the function will create a summary of those 
*	specific nodes, instead of creating a summary of all the nodes</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*$core_arr = get_full_node_cpu_summary();
*$tmp_state_arr = CF_NODE_SUMMARY();
*$out = "<table><caption>Nodes Summary</caption>";
*for($i=0;$i<count($tmp_state_arr);$i++) {
*	$out .= "<tr><td>".$tmp_state_arr[$i][1]."</td><td>".$core_arr[$i]."</td></tr>";
*}
*$out .= "</table>";
*echo $out;
* </code>
* @param array an optional array of node objects to be analyzed. If not passed on, the function will process all nodes.
* @return array a numerically indexed array where each index represents the sum of the amount of cores for nodes in a specific state
* @see constants.php,Node.php,get_partition_node_summary(),get_full_node_cpu_summary(),get_full_node_summary(), slurm_get_node_elements()
*/
function get_full_node_core_summary($tmp=NULL) {
        $full_core_arr = array(0,0,0,0,0,0,0,0);
        if($tmp == NULL) {
                foreach(slurm_get_node_elements() as $arr) {
                        if($arr["State"]<SLURM_LAST_STATE) {
                                $full_core_arr[$arr["State"]] += $arr["#Cores/CPU"];
                        } else {
                                $full_core_arr[0] += $arr["#Cores/CPU"];
                        }
                }
        } else {
                foreach($tmp as $nde) {
                        if($nde->node_state<SLURM_LAST_STATE) {
                                $full_core_arr[$nde->node_state] += $nde->cores;
                        } else {
                                $full_core_arr[0] += $nde->cores;
                        }
                }
        }
        return $full_core_arr;
}

/**
* Receives a value and option, based on the option it will generate the requested summary array for your specific value.
* 
* <b>Value ?</b>
* <ul>
*	<li>The value can be any of the following :
*		<ul>
*			<li>Parsed partition object</li>
*			<li>Array of nodes</li>
*			<li>Partitionname</li>
*		</ul>
*	<li>
* </ul><br />
* <b>Possible Options</b>
* <ul>
*	<li>0 : <b>job summary</b></li>
*	<li>1 : <b>cpu summary</b></li>
*	<li>2 : <b>socket summary</b></li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*require_once('objects/Partition.php');
*...
*$node_states_arr = get_partition_node_summary("your partition name",0);
*$part = new Partition();
*$part->parse_partition_array(slurm_get_specific_partition_info("your partition name"));
*$cpu_states_arr = get_partition_node_summary($part,1);
*$core_states_arr = get_partition_node_summary(slurm_get_node_elements(),2);
*$tmp = CF_NODE_SUMMARY();
*$out = "<table><tr><th></th><th>#Nodes</th><th>#CPU's</th><th>#Cores</th></tr>";
*for($i=0;$i<=SLURM_LAST_STATE;$i++) {
*	$out .= "<tr><td>".$tmp[$i][1]."</td><td>".$node_states_arr[$i]."</td><td>".$cpu_states_arr[$i]."</td><td>".$core_states_arr."</td></tr>";
*}
*echo $out."</table>";
* </code>
* @param string/Partition/array $value this value can be the name of a partition, an array of nodes or a parsed Partition object
* @param int $option the requested operation to be executed
* @return array a numerically indexed array representing the return-value for the option that you requested
* @see constants.php,Node.php,Partition.php,get_full_node_cpu_summary(),get_full_node_core_summary(),get_full_node_summary()
*/
function get_partition_node_summary($value,$option) {
        require_once("objects/Partition.php");
        if(strcmp(gettype($value),"object")==0) {
		switch($option) {
			case 0:
				return get_full_node_summary(get_nodes_from_partition($value));
			case 1:
				return get_full_node_cpu_summary(get_nodes_from_partition($value));
			case 2:
				return get_full_node_core_summary(get_nodes_from_partition($value));
		}
        } else if(is_array($value)) {
		switch($option) {
			case 0:
				return get_full_node_summary($value);
			case 1:
				return get_full_node_cpu_summary($value);
			case 2:
				return get_full_node_core_summary($value);
		}
	}
        $part = new Partition();
        $part->parse_partition_array(slurm_get_specific_partition_info($value));
	switch($option) {
		case 0:
			return get_full_node_summary(get_nodes_from_partition($part));
		case 1:
			return get_full_node_cpu_summary(get_nodes_from_partition($part));
		case 2:
			return get_full_node_core_summary(get_nodes_from_partition($part));
	}
}

////////////////////////////////////////////////////////////////////////////////
//	SORT FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* This function sorts out an array of parsed Job objects into an associative array by JobState.<br />
* There's a list of predefined keys to allow for a specific order (so that when you're reading out the array, you'd actually get the Jobs
* sorted starting with the more important states (COMPLETED,RUNNING,PENDING,...)
* 
* <b>Predefined keys</b>
* <ul>
*	<li>COMPLETED</li>
*	<li>RUNNING</li>
*	<li>PENDING</li>
*	<li>FAILED</li>
*	<li>TIMEOUT</li>
*	<li>CANCELLED</li>
* </ul>
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>Any states that don't occur in the list of predefined keys will be added at the back of the array at runtime</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*$raw = slurm_load_job_information();
*$result = array();
*foreach($raw as $key => $value) {
*	$job = new Job();
*	$job->parse_job_array($value);
*	array_push($result,$job);
*}
*$result = sort_jobs_by_state($result);
*... Do something with this result
* </code>
* @param array an array of parsed Job objects
* @return array an associative array where the Jobs have been sorted into subarrays by their Jobstate
* @see Job.php,process_raw_job_array()
*/
function sort_jobs_by_state($jobs)
{
	$sort_array =  array(
				"COMPLETED"=>array(),
				"RUNNING"=>array(),
				"PENDING"=>array(),
				"FAILED"=>array(),
				"TIMEOUT"=>array(),
				"CANCELLED"=>array());

	foreach($jobs as $val) {
                if(!array_key_exists($val->JobState,$sort_array)) {
                	$sort_array[$val->JobState] = array();
                }
        	array_push($sort_array[$val->JobState],$val);
        }
	return $sort_array;
}

/**
* This function splits an array of any parsed objects into an associative array where each subarray contains elements with the same key.<br />
* The only requirement for this function to successfully process the aray is that the objects have a "create_my_key()" function, which returns<br />a string-value key
* based on their fields.
*
* <br /><b>Usage</b>
* <code>
*require_once('includes/functions.php');
*require_once('objects/Partition.php');
*$part = new Partition();
*$part->parse_partition_array(slurm_get_specific_partition_info("your_partition_name"));
*$partition_nodes = get_nodes_from_partition($part);
*$partition_nodes_sorted = split_into_associative_arrays($partition_nodes);
*foreach($partition_nodes_sorted as $key => $val) {
*	foreach($val as $nde) {
*		... Do something with the separate nodes
*	}
*}
* </code>
* @param array an array of parsed objects
* @return array an associative array where the objects have been grouped by their keys
* @see Job.php,Node.php,Partition.php
*/
function split_into_associative_arrays($origin)
{
	$key_assoc_arr = array();
	foreach($origin as $inner) {
		$str = $inner->create_my_key();
		if(!array_key_exists($str,$key_assoc_arr)) {
			$key_assoc_arr[$str] = array();
		}
		array_push($key_assoc_arr[$str], $inner);
	}
	return $key_assoc_arr;
}

////////////////////////////////////////////////////////////////////////////////
//	FILTER FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* Retrieves the names of all the nodes that are in a certain state on a certain partition
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>If the state requested is the UNKNOWN state, any nodes who are in a state higher<br />than the SLURM_LAST_STATE will also be added to the unknown list
*	</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*$node_array = filter_nodes_partition_state("your_hostlist",SLURM_NODE_STATE_IDLE);
*$node_arr = array();
*foreach($node_array as $value) {
*	array_push($node_arr,slurm_get_node_element_by_name($value));
*}
*$node_arr = process_raw_node_array($node_arr);
*... Do something with the processed nodes
* </code>
* @param String $partitionName the name of the partition from which you'd like to request the nodes
* @param int $state The state that you'd like to filter on
* @return array an array containing the names of nodes
* @see Node.php, slurm_get_partition_node_names(),slurm_hostlist_to_array(), slurm_get_node_state_by_name()
*/
function filter_nodes_partition_state($partitionName,$state)
{
	$hostlist = slurm_get_partition_node_names($partitionName);
	$nodeArr = array();
	$nde_state;
	if($state==-1) {
		foreach($hostlist as $val) {
			foreach(slurm_hostlist_to_array($val) as $nodename) {
				array_push($nodeArr,$nodename);
			}
		}
	} else {
		foreach($hostlist as $val) {
			foreach(slurm_hostlist_to_array($val) as $nodename) {
				$nde_state = slurm_get_node_state_by_name($nodename);
				if($nde_state==$state) {
					array_push($nodeArr,$nodename);
				} else if($state==SLURM_NODE_STATE_UNKNOWN) {
					if($nde_state>SLURM_LAST_STATE) {
						array_push($nodeArr,$nodename);
					}
				}
			}
		}
	}
	return $nodeArr;
}

/**
* Groups an array of nodes into smaller arrays by state
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>Any nodes who are in a state higher<br />than the SLURM_LAST_STATE will be added to the unknown list
*	</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*$nodes_ungrouped = slurm_get_node_elements();
*$nodes_ungrouped = process_raw_node_array($nodes_ungrouped);
*$nodes_grouped = filter_nodearray_by_state($nodes_ungrouped);
*$out = "";
*for($i=0;$i<count($nodes_grouped);$i++) {
*	... Do something with the nodes for a specific state
*}
* </code>
* @param array The array of nodes that you'd like to see grouped
* @return array A grouped array of nodes by state
* @see Node.php
*/
function filter_nodearray_by_state($nodes)
{
	$filtered_result = array(
					array(),		//	unknown
					array(),		//	down
					array(),		//	idle
					array(),		//	allocated
					array(),		//	error
					array(),		//	mixed
					array(),		//	future
					array());		//	end
	foreach($nodes as $nde) {
		if($nde->node_state < SLURM_LAST_STATE) {
			array_push($filtered_result[$nde->node_state],$nde);
		} else {
			array_push($filtered_result[0],$nde);
		}
	}
	return $filtered_result;
}

////////////////////////////////////////////////////////////////////////////////
//	CONVERSION FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* Converts a megabyte value into a gigabyte value and then appends it with " GB"
*
* <br /><b>Usage</b>
* The following is an excerpt from process_raw_node_array() :
* <code>
*$sum_arr["Real Memory"] = c_mb_to_gb($sum_arr["Real Memory"]);
* </code>
* @param int The value that you'd like to see converted from MB to GB
* @return String The converted value, appended with " GB"
*/
function c_mb_to_gb($val) {
	return round($val/1024,2)." GB";
}

/**
* Converts a numerical node state into it's string representation
*
* <br /><b>Usage</b>
* The following is an excerpt from create_sinfo_row_from_node() :
* <code>
*$tmp .= validate_data_value(c_state_to_string($val->node_state));
* </code>
* @param int The integer representation of a node state
* @return String The string representation of the given node state
* @see Node.php,constants.php
*/
function c_state_to_string($val) {
	$arr = CF_NODE_SUMMARY();
	if($val<SLURM_LAST_STATE) {
		return $arr[$val][1];
	} else {
		return $arr[0][1];
	}
}

////////////////////////////////////////////////////////////////////////////////
//	VALIDATION/HTML FUNCTIONALITY
////////////////////////////////////////////////////////////////////////////////

/**
* This function is used to parse an error code and do an operation based on the error code 
*
* <b>Error Code -> Operation</b>
* <ul>
*	<li><b>-3</b><ul><li>Sets the error message to "No/incorrect variables passed on" and then loads the nogood.php file located in the conditionals folder
*			</li></ul>
*	</li>
*	<li><b>-2</b><ul><li>loads the nodaemon.php file located in the conditionals folder
*			</li></ul>
*	</li>
*	<li><b>-1</b><ul><li>Sets the error message to "Your query didn't give any matches" and then loads the nogood.php file located in the conditionals folder
*			</li></ul>
*	</li>
* </ul>
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>The error codes are defined as constants in includes/constants.php and start with SLURM_ERROR_*
*	</li>
* </ul>
* <br /><b>Usage</b>
* The following is an excerpt from create_sinfo_row_from_node() :
* <code>
* require_once('includes/functions.php');
* ...
* $nameArray = slurm_print_partition_names();
* if(is_array($nameArray)) {
*	... good to go
* } else {
*	check_error_code($nameArray);
* }
* </code>
* @param int The error code to be checked
* @see constants.php
*/
function check_error_code($error_code)
{
	switch($error_code) {
		case SLURM_ERROR_VAR:
			$error_message = "No/incorrect variables passed on";
			require_once(SLURM_NOGOOD);
			break;
		case SLURM_ERROR_DNO:
			require_once(SLURM_NODAEMON);		
			break;
		case SLURM_ERROR_NMF:
			$error_message = "Your query didn't give any matches";
			require_once(SLURM_NOGOOD);
			break;
	}
}

/**
* Validates the passed on value and css class, based on this validation the function then generates a table data cell accordingly.
*
* <b>Validation</b>
* <ul>
*	<li>The validation done on the value is to check if the value is an array or not, if it is, the generated cell will contain
*	a list instead of plain text</li>
*	<li>The validation of the css class is the same, if no css class is provided the class attribute will not be set</li>
* </ul>
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>This function is mostly used as a back-end function, it's better to use the other generation functions which offer more functionality
*	</li>
*	<li>There are no optional values, so if you don't want to add a css class, just pass on "" as css class</li>
* </ul>
* @param String $value The value that needs to be validated
* @param String $css_class The css class that you'd like to see added to the td value
* @return String a generated table data cell with the validated value and the css class inserted
* @see validate_data_value()
*/
function return_valid_td_css($value,$css_class) {
	if(strcmp(gettype($value),"array")==0) {
		$tmp = "<td";
		if(strcmp($css_class,"")!=0) {
			$tmp .= " class='".$css_class."'";
		}
		$tmp .= "><ul>";
		foreach($value as $inner_value) {
			$tmp .= "<li>".$inner_value."</li>";
		}
		return $tmp."</ul></td>";
	} else {
		$tmp = "<td";
		if(strcmp($css_class,"")!=0) {
			$tmp .= " class='".$css_class."'";
		}
		return $tmp.">".$value."</td>";
	}
}

/**
* This function acts as an extra validation layer with preset css class ('color_warning') to the return_valid_td_css function, the extra validation
* checks to see if the value is NULL or not, if it is then this function will change the value to be the string "N/A".
*
* <b>Optional Parameters</b>
* <ul>
*	<li><b>$warning </b>[Default value=false]: a boolean value that if true sets the css-class to 'color_warning' ( which is
*	a css class to be used for warnings/error messages</li>
*	<li><b>$css_class </b>[Default value=""]: a css class to be added should the $warning be set to false</li>
* </ul>
* <br /><b>Usage</b>
*<code>
*require_once('includes/functions.php');
*foreach(get_configuration() as $key => $value) {
*	$out .= "<tr><td>".$key."</td>".validate_data_value($value,true)."</tr>";
*}
*</code>
* @param String $value The value that needs to be validated
* @param String $warning a boolean value that (if set to true) will add the css-class 'color_warning' to the generated table data cell
* @param String $css_class The css class that you'd like to see added to the td value
* @return String a generated table data cell with the validated value and the requested options implemented
* @see return_valid_td_css()
*/
function validate_data_value($value,$warning=false,$css_class="")
{
	if(strcmp(gettype($value),"NULL")==0) {
		if($warning) {
			return return_valid_td_css("N/A",'color_warning');
		} else {
			return return_valid_td_css("N/A",$css_class);
		}
	} else {
		return return_valid_td_css($value,$css_class);
	}
}
/**
* Retreives an associative array and uses the keys to generate a table row where each key occupies a th cell.
* I use this function to create a header row out of an object oriented class's members
* <br /><b>Usage</b>
*<code>
*require_once('includes/functions.php');
*...
*$node_out = "<table><caption>header test</caption>";
*$node_out .=  get_table_head_from_class(get_class_vars("Node"));
*... 
*</code>
* @return String A table row filled with th cells where each cell's content is the key of an assoc array
*/
function get_table_head_from_class($class_vars) {
	$tmp = "<tr>";
	foreach($class_vars as $key => $value) {
		$tmp .= "<th>". $key."</th>";
	}
	return $tmp."</tr>";
}

/**
* This function is being used to turn several variables (after some processing) into a table row that replicates a row as show in the 'sinfo -Nel' command.
*
* <b>Sidenote(s) :</b>
* <ul>
*	<li>This function validates all the variables itself by using the validate_data_value() function
*	</li>
*	<li>Not all of the available fields are shown, if you'd like to add any extra fields please do so between the START/END ADDITIONAL FIELDS part of the function
*	<li>The sinfo command groups nodes with the same properties together, so the $val object actually acts as a representative of its group</li>
* </ul>
* <br /><b>Usage</b>
* <code>
*$part->parse_partition_array(slurm_get_specific_partition_info("your_partition_name"));
*$partition_nodes = get_nodes_from_partition($part);
*$partition_nodes_sorted = split_into_associative_arrays($partition_nodes);
*foreach($partition_nodes_sorted as $key => $val) {
*	$name_arr = array();
*	foreach($val as $nde) {
*		array_push($name_arr,$nde->name);
*	}
*	$arr = slurm_array_to_hostlist($name_arr);
*	$sinfo .= create_sinfo_row_from_node($val[0],$part,$arr["HOSTLIST"],count($val));
*}
* </code>
* @param Node $val A processed node object that acts as a representative of the group that it belongs to
* @param Partition $tmp_part A partition object (partition that the node belongs to)
* @param String $hostlist The hostlist representation of the node-group
* @param int $countval The count of all the nodes in this group
* @return String A table row
* @see validate_data_value(),Partition.php,Node.php
*/
function create_sinfo_row_from_node($val,$tmp_part,$hostlist,$countval=1) {
	$tmp = "<tr>";
	if(strcmp(gettype($hostlist),"NULL")==0) {
		$tmp .= "<td>N/A</td>";
	} else {
		$tmp .= "<td><span class='styled_link'><a onclick=\"loadPage('nodes.php?hostlist=".$hostlist."')\">".$hostlist."</a></span></td>";
	}
	$tmp .= validate_data_value($countval);
	$tmp .= validate_data_value($tmp_part->PartitionName);
	$tmp .= validate_data_value(c_state_to_string($val->node_state));
	$tmp .= validate_data_value($val->cpus);
	$tmp .= validate_data_value($val->sockets.":".$val->cores.":".$val->threads);
	$tmp .= validate_data_value($val->real_memory);
	$tmp .= validate_data_value($val->tmp_disk);
	$tmp .= validate_data_value($val->weight);
	$tmp .= validate_data_value($val->features);
	$tmp .= validate_data_value($val->reason);
	//
	// START ADDITIONAL FIELDS
	//

	// END ADDITIONAL FIELDS

	$tmp .= "</tr>";
	return $tmp;
}

?>
