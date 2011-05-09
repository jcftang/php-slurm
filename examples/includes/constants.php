<?php
	/** 
	* This file represents a series of constants used throughout
	* the basic website. 
	* 
	* <ul>
	*	 <li>
	*		<b>Slurm version constants</b> (SLURM_VERSION_*)
	*		<br />
	*		Used when calling the <b>get_slurm_version</b> function
	*		<br />
	*		<code>
	*require_once('includes/functions.php');
	*$ver = get_slurm_version(SLURM_VERSION_MAJOR);
	*		</code><br />
	*	</li>
	*	<li>
	*		<b>Slurm node states</b> (SLURM_NODE_STATE_*)
	*		<br />
	*		This group of constants represent the different states 
	*		a node can be in.
	*
	*		These constants are used in all types of functions to differentiate between
	*		for instance a down node or an idle node.
	*		<br />
	*		<code>
	*require_once('includes/functions.php');
	*...
	*foreach($partition_nodenames as $nodename) {
	*	switch(slurm_get_node_state_by_name($nodename))
        *	{
	*		case SLURM_NODE_STATE_IDLE :
	*			array_push($idle, $nodename);
	*			break;
	*		case SLURM_NODE_STATE_ALLOCATED :
	*			array_push($allocated, $nodename);
        *			break;
	*		case SLURM_NODE_STATE_UNKNOWN:
	*		case SLURM_NODE_STATE_DOWN:
	*		case SLURM_NODE_STATE_ERROR:
	*		default:
	*			array_push($not_available, $nodename);
        *			break;
	*	}
	*}
	*...
	*		</code>
	*		<br />
	*		As an extra i've added a constant called <b>SLURM_LAST_STATE</b>, this constant
	*		contains the numerical representation of the last 'normal' node state.
	*
	*		The reason for this lies in the fact that broken nodes with error reasons have a
	*		special state with a numerical representation of 2049. To identify these nodes you could just check if 
	*		the value is above the SLURM_LAST_STATE's value.
	*		<br />
	*		<code>
	*require_once('includes/functions.php');
	*...
	*if($nde->node_state>SLURM_LAST_STATE) {
	*	... Identified non-normal node
	*} else {
	*	... Normal node
	*}
	*...
	*		</code><br />
	*	</li>
	*	<li>
	*		<b>Slurm error codes</b> (SLURM_ERROR_*)
	*		<br />
	*		Whenever something goes wrong, a certain error code is returned.
	*		These error codes are always negative values of the long datatype.
	*
	*		Possible values :
	*		<ul>
	*			<li><b>-3</b> : no/incorrect variables where passed on</li>
	*			<li><b>-2</b> : An error occurred whilst trying to communicate with the daemon</li>
	*			<li><b>-1</b> : Your query produced no results</li>
	*		</ul>
	*		<br />
	*		<code>
	*require_once('include/functions.php');
	*$result = slurm_get_specific_partition_info('random_string');
	*if(is_array($result)) {
	*	... Partition exists
	*} else {
	*	switch($result) {
	*		case -3:
	*			... incorrect variable
	*		case -2:
	*			... calling the daemon failed
	*		case -1:
	*			... No results
	*	}
	*}
	*		</code><br />
	*	</li>
	*	<li>
	*		<b>Conditional Filepaths</b>
	*		<br />
	*		Constants containing the links to the different conditional files that are used when a certain error occurs. 
	*		<br /><br />These constants where defined so that should you decide to move the conditional files to a different location,
	*		you'd only have to change the url's once.
	*		<br /><br />
	*		<b>Sidenotes</b>
	*		<ul>
	*			<li>When including the <b>SLURM_NOGOOD</b> file, you need to define a variable called <b>error_message</b>, this variable
	*		should contain the error message that you'd like to show if an error occurs.</li>
	*			<li>When changing these variables you need  to use the path starting from the root directory of
	*		your website. Don't use the path starting from the current working directory ( unless they're the same )</li>
	*		</ul>
	*		<br /><br />
	*		The following is an excerpt from the functions.php file
	*		<br />
	*		<code>
	*require_once('includes/functions.php');
	*function check_error_code($error_code)
	*{
	*	switch($error_code) {
	*		case SLURM_ERROR_VAR:
	*			$error_message = "No/incorrect variables passed on";
	*			require_once(SLURM_NOGOOD);
	*			break;
	*		case SLURM_ERROR_DNO:
	*			require_once(SLURM_NODAEMON);		
	*			break;
	*		case SLURM_ERROR_NMF:
	*			$error_message = "Your query didn't give any matches";
	*			require_once(SLURM_NOGOOD);
	*			break;
	*	}
	*}</code><br /></li>
	*	<li>
	*		<b>Color classes</b> (*_COLOR_CLASS | JOB_*_COLOR_CLASS)
	*		<br />
	*		Constants defined to link css classes to the different node states/job states, this way you can easily link a specific color to 
	*		a node or job when it's in a certain state.
	*		<br /><br />
	*		<b>Sidenote</b>
	*		<ul>
	*			<li>The color classes that these constants link to are actual css classes defined in 'styles/basic.css', if you'd like to add
	*			your own color themes, please define them in the 'styles/local.css' file.</li>
	*			<li>The color classes are split into 2 different sections<ul><li>*_COLOR_CLASS : Node states</li><li>JOB_*_COLOR_CLASS : Job States</li></ul></li>
	*			<li>The classes relating to nodestates are also used in the CF_NODE_SUMMARY array, when working with nodes it's preferred to use this
	*			array instead of the specific color classes separately</li>
	*		</ul><br /><br />
	*		The following is an excerpt from the Job.php object-oriented class
	*		<br /><code>
	*...
	*switch($value) {
	*	case 'PENDING':
	*		$color_class = JOB_PENDING_COLOR_CLASS;
	*		break;
	*	case 'RUNNING':
	*		$color_class = JOB_RUNNING_COLOR_CLASS;
	*		break;
	*	case 'COMPLETED':
	*		$color_class = JOB_COMPLETE_COLOR_CLASS;
	*		break;
	*	default:
	*		$color_class = DEFAULT_COLOR_CLASS;
	*		break;
	*}
	*if(($key=="NodeList") && ($value!=NULL)) {
	*	$out .= "<td class='".$color_class."'><span class='styled_link'><a onclick=\"loadPage('nodes.php?hostlist=".$value."')\">".$value."</a></span></td>";
	*}
	*...
	*</code><br />
	*	</li>
	*	<li>
	*	<b>Specific application variables</b>
	*	<br />
	*	Constants defined to allow for a quick change of certain aspects of the website.
	*	<br />
	*	<ul>
	*		<li>GRID_COL_MIN : The minimum amount of columns for the grid table
	*		<ul>
	*			<li><b>Example</b>
	*				The following code creates a separator in a table using the GRID_COL_MIN value<br />
	*				<code>
	*$out .= "<tr><td colspan='".GRID_COL_MIN."' class='invisible_to_your_eyes'></td></tr>";
	*				</code>
	*			</li>
	*		</ul>
	*		</li>
	*	</ul><br />
	*	</li>
	*</ul><br />
	* <b>Metadata</b>
	* @link https://computing.llnl.gov/linux/slurm/
	* @author Peter Vermeulen <nmb.peterv@gmail.com>
	* @version 1.0
	* @license http://www.gnu.org/licenses/gpl-2.0.html
	* @package includes
	* @see Node.php, Job.php, Partition.php, functions.php
	*/

	

	//
	// Slurm version constants
	//
	///////////////////////////
	/**
	*/
	define("SLURM_VERSION_MAJOR",0);
	define("SLURM_VERSION_MINOR",1);
	define("SLURM_VERSION_MICRO",2);

	//
	// Slurm node states
	//
	///////////////////////////

	define("SLURM_NODE_STATE_UNKNOWN",	0);
	define("SLURM_NODE_STATE_DOWN",		1);
	define("SLURM_NODE_STATE_IDLE",		2);
	define("SLURM_NODE_STATE_ALLOCATED",	3);
	define("SLURM_NODE_STATE_ERROR",	4);
	define("SLURM_NODE_STATE_MIXED",	5);
	define("SLURM_NODE_STATE_FUTURE",	6);
	define("SLURM_NODE_STATE_END",		7);

	define("SLURM_LAST_STATE",		7);

	//
	// Slurm error codes
	//
	///////////////////////////

	define("SLURM_ERROR_VAR",		-3);
	define("SLURM_ERROR_DNO",		-2);
	define("SLURM_ERROR_NMF",		-1);

	//
	// Conditional Filepaths
	//
	///////////////////////////

	define("SLURM_NODAEMON",		"conditionals/nodaemon.php");
	define("SLURM_NOGOOD",			"conditionals/nogood.php");

	//
	// Color classes
	//
	///////////////////////////

	define("DOWN_COLOR_CLASS",		"color_red1");
	define("ERROR_COLOR_CLASS",		"color_red1");
	define("UNKNOWN_COLOR_CLASS",		"color_red1");
	define("IDLE_COLOR_CLASS",		"color_green0");
	define("FUTURE_COLOR_CLASS",		"color_green1");
	define("END_COLOR_CLASS",		"color_green2");
	define("ALLOCATED_COLOR_CLASS",		"color_orange0");
	define("MIXED_COLOR_CLASS",		"color_orange1");
	define("DEFAULT_COLOR_CLASS",		"color_red0");

	define("JOB_PENDING_COLOR_CLASS", 	"color_orange0");
	define("JOB_RUNNING_COLOR_CLASS", 	"color_green0");
	define("JOB_COMPLETE_COLOR_CLASS", 	"color_white0");

	//
	// Specific application variables
	//
	///////////////////////////

	define("GRID_COL_MIN",			8);

	//
	// COMBINED CONSTANT FUNCTIONS
	//
	///////////////////////////

	/**
	* Returns an array combining the color class constants with the node
	* states and their string representations. 
	* 
	* The returned array is a 2D array where each element contains 3 elements
	* <ul>
	*	<li><b>Color Class</b>
	*	<pre>    The color class linked to this state</pre></li>
	*	<li><b>String representation</b>
	*	<pre>    The string representation of this state</pre></li>
	*	<li><b>Node State</b>
	*	<pre>    The numerical value of this node's state</pre></li>
	* </ul><br />
	*<b>Sidenote :</b>
	*It might seem confusing to see a function inside of a file that is being
	*used to represent constant variables. The reason for this lies in the fact
	*that php doesn't supply a way of defining array constants.
	*<br />
	*But as you might notice, i used the standard of declaring a constant with
	*uppercase letters for the function name, so this function is basically being
	*used as a constant array.
	*
	*<b>Usage</b>
	* <code>
	*require_once('includes/functions.php');
	*...
	*$out = "";
	*$combined_arr = CF_NODE_SUMMARY();
	*foreach($nodes as $node) {
	*	if($node->node_state<SLURM_LAST_STATE) {
	*		$out .= "<td class='".$combined_arr[$node->node_state][0]."'>".$combined_arr[$node->node_state][1]."</td>";
	*	} else {
	*		$out .= "<td class='".$combined_arr[0][0]."'>".$combined_arr[0][1]."</td>";
	*	}
	*}
	*echo $out;
	* </code><br />
	* @return array
	*/
	function CF_NODE_SUMMARY()
	{
	        $tmp =  array(  
         	                array(UNKNOWN_COLOR_CLASS,	"UNKNOWN",      SLURM_NODE_STATE_UNKNOWN),
                	        array(DOWN_COLOR_CLASS,         "DOWN",         SLURM_NODE_STATE_DOWN),
                        	array(IDLE_COLOR_CLASS,         "IDLE",         SLURM_NODE_STATE_IDLE),
	                        array(ALLOCATED_COLOR_CLASS,    "ALLOCATED",    SLURM_NODE_STATE_ALLOCATED),
        	                array(ERROR_COLOR_CLASS,        "ERROR",        SLURM_NODE_STATE_ERROR),
                	        array(MIXED_COLOR_CLASS,        "MIXED",        SLURM_NODE_STATE_MIXED),
                        	array(FUTURE_COLOR_CLASS,       "FUTURE",       SLURM_NODE_STATE_FUTURE),
	                        array(END_COLOR_CLASS,          "END",          SLURM_NODE_STATE_END));
        	return $tmp;
	}
?>
