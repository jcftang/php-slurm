<?php
/**
* This file contains the documentation for the C functionality and is only to be
* used for these means (this file doesn't include the functionality itself).
* 
* When extending the functionality on C side, please check the <b>slurm_php.h</b>,
* i've defined some helper functions not available on php side that might be useful.
*
* <b>Scheme</b>
* <ul>
*	<li>The c function-calls that directly access the slurm interface are prefixed with <b>slurm_</b></li>
*	<li>Internally the functions are written in the Linux kernel style</li>
* </ul>
*
* <b>Error codes</b>
* <ul>
*	<li><b>-3</b> No/incorrect variables where passed on</li>
*	<li><b>-2</b> An error occurred whilst trying to communicate with the daemon</li>
*	<li><b>-1</b> Your query produced no results</li>
* </ul>
*
* <b>Version Formatting Options</b>
* <ul>
*	<li><b>0</b> - major of the version number</li>
*	<li><b>1</b> - minor of the version number</li>
*	<li><b>2</b> - micro of the version number</li>
*	<li><b>default</b> - full version number</li>
* </ul>
*
* <b>Sidenotes</b>
* <ul>
*	<li>Due to the configuration being quite large, i decided to create 2 functions to return the keys and values separately. 
*	(to prevent a buffer overflow)</li>
* </ul>
* <b>Metadata</b>
* @link https://computing.llnl.gov/linux/slurm/
* @author Peter Vermeulen <nmb.peterv@gmail.com>
* @version 1.0
* @license http://www.gnu.org/licenses/gpl.html
* @package c
*/

/*****************************************************************************\
 *	SLURM PHP HOSTLIST FUNCTIONS
\*****************************************************************************/

/**
 * slurm_hostlist_to_array - converts a hostlist string to a numerically indexed array.
 *
 * @param String $host_list - string value containing the hostlist
 * @return array numerically indexed array containing the names of the nodes
 */
function slurm_hostlist_to_array($host_list) {
	return NULL;
}

/**
 * slurm_array_to_hostlist - convert an array of nodenames into a hostlist string
 *
 * @param array node_arr - Numerically indexed array containing a nodename on each index
 * @return String String variable containing the hostlist string
 */
function slurm_array_to_hostlist($node_arr) {
	return NULL;
}

/*****************************************************************************\
 *	SLURM STATUS FUNCTIONS
\*****************************************************************************/

/**
 * slurm_ping - Issues the slurm interface to return the status of the slurm primary and secondary controller
 *
 * @return array associative array containing the status ( status = 0 if online, = -1 if offline ) of both controllers
 */
function slurm_ping() {
	return NULL
}

/**
 * slurm_slurmd_status - Issues the slurm interface to return the status of the slave daemon (running on this machine)
 *
 * @return array associative array containing the status or a negative long variable containing an error code
 */
function slurm_slurmd_status() {
	return NULL;
}

/**
 * slurm_version - Returns the slurm version number in the requested format
 *
 * @param int option - long/integer value linking to the formatting of the version number
 * @return array long value containing the specific formatted version number a numeric
 *	array containing the version number or a negative long variable containing
 *	an error code.
 */
function slurm_version($option) {
	return NULL;
}

/*****************************************************************************\
 *	SLURM PARTITION READ FUNCTIONS
\*****************************************************************************/

/**
 * slurm_print_partition_names - Creates and returns a numerically indexed array containing the names of the partitions
 *
 * @return array numerically indexed array containing the partitionnames or a negative long
 *	variable containing an error code
 */
function slurm_print_partition_names() {
	return NULL;
}

/**
 * slurm_get_specific_partition_info - Searches for the requested partition and if found it returns an associative array containing the information about this specific partition
 *
 * @param String $name - a string variable containing the partitionname
 * @param int $lngth - optional parameter containing the length of the partitionname
 * @return array an associative array containing the information about a specific partition,
 *	or a negative long value containing an error code
 */
function slurm_get_specific_partition_info($name, $lngth) {
	return NULL;
}

/**
 * slurm_get_partition_node_names - Searches for the requested partition and if found it parses the nodes into a numerically indexed array, which is then returned to the calling function.
 *
 * @param String $name - a string variable containing the partitionname
 * @param int $lngth - optional parameter containing the length of the partitionname
 * @return array a numerically indexed array containing the names of all the nodes connected
 *	to this partition, or a negative long value containing an error code
 */
function slurm_get_partition_node_names($name, $lngth) {
	return NULL;
}

/*****************************************************************************\
 *	SLURM NODE CONFIGURATION READ FUNCTIONS
\*****************************************************************************/

/**
 * slurm_get_node_names - Creates and returns a numerically index array containing the nodenames.
 *
 * @return array a numerically indexed array containing the requested nodenames,
 *	or a negative long value containing an error code
 */
function slurm_get_node_names() {
	return NULL;
}
/**
 * slurm_get_node_elements - Creates and returns an associative array containing all the nodes indexed by nodename and as value an 
 * associative array containing their information.
 *
 * @return array an associative array containing the nodes as keys and their
 *	information as value, or a long value containing an error code
 */
function slurm_get_node_elements() {
	return NULL;
}

/**
 * slurm_get_node_element_by_name - Searches for the requested node and if found it parses its information into an associative array, which is then returned to the calling function.
 *
 * @param String $name - a string variable containing the nodename
 * @param int $lngth - optional parameter containing the length of the nodename
 * @return array an assocative array containing the requested information or a
 *	long value containing an error code
 */
function slurm_get_node_element_by_name($name, $lngth) {
	return NULL;
}

/**
 * slurm_get_node_state_by_name - Searches for the requested node and if found it returns the state of that node
 *
 * @param String $name - a string variable containing the nodename
 * @param int $lngth - optional parameter containing the length of the nodename
 * @return int a long value containing the state of the node [0-7] or a
 *	negative long value containing the error code
 */
function slurm_get_node_state_by_name($name, $lngth) {
	return NULL;
}

/**
 * slurm_get_node_states - Creates a numerically indexed array containing the state of each node (only the state !) as a long value. This function could be used to create a summary of the node states without having to do a lot of processing (or having to deal with overlapping nodes between partitions).
 *
 * @return array a numerically indexed array containing node states
 */
function slurm_get_node_states() {
	return NULL;
}

/*****************************************************************************\
 *	SLURM CONFIGURATION READ FUNCTIONS
\*****************************************************************************/

/**
 * slurm_get_control_configuration_keys - Retreives the keys from the configuration file
 * @return array a numerically indexed array containing keys that describe the values of the 
 *	configuration of the slurm daemon, or a long value containing an error code
 * @see slurm_get_control_configuration_values()
 */
function slurm_get_control_configuration_keys() {
	return NULL;
}

/**
 * slurm_get_control_configuration_values - Retreives the values from the configuration file
 *
 * @return array a numerically indexed array containing the values of the
 *	configuration of the slurm daemon, or a long value containing
 *	an error code
 * @see slurm_get_control_configuration_values()
 */
function slurm_get_control_configuration_values() {
	return NULL;
}

/*****************************************************************************\
 *	SLURM JOB READ FUNCTIONS
\*****************************************************************************/

/**
 * slurm_load_job_information - Loads the information of all the jobs, parses it and returns the values as an associative array where each key is the job id linking to an associative array with the information of the job
 *
 * @return array an associative array containing the information of all jobs, or
 *	a long value containing an error code.
 */
function slurm_load_partition_jobs() {
	return NULL;
}

/*
 * slurm_load_partition_jobs - Retreive the information of all the jobs running on a single partition.
 *
 * @param String $name - a string variable containing the partitionname
 * @param int $lngth - optional parameter containing the length of the partitionname
 * @return array an associative array containing the information of all the jobs
 *	running on this partition. Or a long value containing an error
 *	code
 */
function slurm_load_job_information($name,$lngth) {
	return NULL;
}
?>
