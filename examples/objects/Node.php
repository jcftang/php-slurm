<?php
/**
* Node.php - Object Oriented Class
*
* This class is an object-oriented representation of a Node and it's detailed information.
* <br />This class also contains several functions to help in parsing and processing data specific to an instance of this Node object.
* <b>Metadata</b>
* @link https://computing.llnl.gov/linux/slurm/
* @author Peter Vermeulen <nmb.peterv@gmail.com>
* @version 1.0
* @license http://www.gnu.org/licenses/gpl-2.0.html
* @package objects 
*/
	class Node
	{
		/**
		* The name of the node [Default=NULL]
		* @access public
		* @var string
		*/
		public $name 			= NULL;
		/**
		* The type of architecture that the node is running on [Default=NULL]
		* <b>Example</b>
		* <ul>
		*	<li>x86_x64</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $arch 			= NULL;
		/**
		* A unix timestamp formatted as the standard date and time string, representing the boot time for the slurmctld
		* <b>Example</b>
		* <ul>
		*	<li>Tue May 3 16:57:17 2011</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $boot_time 		= NULL;
		/**
		* The amount of cpus belonging to this node
		* @access public
		* @var int
		*/
		public $cpus 			= NULL;
		/**
		* The amount of cores belonging to this node
		* @access public
		* @var int
		*/
		public $cores 			= NULL;
		/**
		* An array containing the features for this node
		* @access public
		* @var array
		*/
		public $features 		= NULL;
		/**
		* A generic resource 
		* @access public
		* @var string
		*/
		public $gres 			= NULL;
		/**
		* The state of this node 
		* @access public
		* @var int
		* @see constants.php
		*/
		public $node_state 		= NULL;
		/**
		* The operating system that the node is running under
		* @access public
		* @var int
		*/
		public $os			= NULL;
		/**
		* The amount of ram that this node has
		* <b>Example</b>
		* <ul>
		*	<li>Linux</li>
		* </ul><br /><br />
		* @access public
		* @var int
		*/
		public $real_memory		= NULL;
		/**
		* The reason as to why a node is having a specific behavior
		* @access public
		* @var string
		*/
		public $reason			= NULL;
		/**
		* A unix timestamp formatted as the standard date and time string, this represents the time at which the reason was posted
		* <b>Example</b>
		* <ul>
		*	<li>Tue May 3 16:57:17 2011</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $reason_time		= NULL;
		/**
		* The user id of the user who posted a reason on this node (reasons are used to give detail about specific node states, for instance to 
		* explain why a node is down).
		* @access public
		* @var int
		*/
		public $reason_uid		= NULL;
		/**
		* A unix timestamp formatted as the standard date and time string, this represents the time at which the slurm slave daemon booted
		* <b>Example</b>
		* <ul>
		*	<li>Tue May 3 16:57:17 2011</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $slurmd_start_time	= NULL;
		/**
		* The amount of sockets for this sode
		* @access public
		* @var int
		*/
		public $sockets			= NULL;
		/**
		* The amount of threads for execution
		* @access public
		* @var int
		*/
		public $threads			= NULL;
		/**
		* The amount of disk space allocated to this node
		* @access public
		* @var int
		*/
		public $tmp_disk		= NULL;
		/**
		* The partition weight, this is used in scheduling
		* @access public
		* @var int
		*/
		public $weight			= NULL;

		/**
		* This function uses the fields in this class to create a table row where each data cell contains a specific field's content.
		* 
		* This function also does a check against the node state to see if the node is not in an unknown, down or error state.<br />
		* If however the node where in any of these 3 states, the css class 'node_down' would be added to it.
		* @return String A string variable containing a table row representing this instance and its fields
		* @access public
		* @see constants.php
		*/
		public function get_as_row() {
			$members = get_object_vars($this);
			$out = "<tr>";
			switch($this->node_state) {
				case SLURM_NODE_STATE_IDLE:
				case SLURM_NODE_STATE_ALLOCATED:
				case SLURM_NODE_STATE_MIXED:
				case SLURM_NODE_STATE_FUTURE:
				case SLURM_NODE_STATE_END:
					foreach($members as $key => $value) {
						$out .= validate_data_value($value);
					}
					break;
				case SLURM_NODE_STATE_DOWN:
                                case SLURM_NODE_STATE_ERROR:
                                case SLURM_NODE_STATE_UNKNOWN:
				default:
					foreach($members as $key => $value) {
						$out .= validate_data_value($value,false,"node_down");
					}
					break;
			}
			$out	.=	"</tr>";
			return $out;
		}
		
		/**
		* This function parses a raw node array returned from slurm, and uses the data of the <br />
		* array to fill in it's fields accordingly.
		* 
		* @param array $node_arr a raw array of data specific to one node
		* @access public
		*/
		public function parse_node_array($node_arr) {
			$this->name 			= $node_arr["Name"];
			$this->arch 			= $node_arr["Arch."];
			$this->boot_time 		= $node_arr["Boot Time"];
			$this->cpus 			= $node_arr["#CPU'S"];
			$this->cores			= $node_arr["#Cores/CPU"];
			$this->features			= $node_arr["Features"];
			$this->gres			= $node_arr["GRES"];
			$this->node_state 		= $node_arr["State"];
			$this->os			= $node_arr["OS"];
			$this->real_memory		= $node_arr["Real Mem"];
			$this->reason			= $node_arr["Reason"];
			$this->reason_uid		= $node_arr["Reason User Id"];
			$this->slurmd_start_time	= $node_arr["Slurmd Startup Time"];
			$this->sockets			= $node_arr["#Sockets/Node"];
			$this->threads			= $node_arr["#Threads/Core"];
			$this->tmp_disk			= $node_arr["TmpDisk"];
			$this->weight			= $node_arr["Weight"];
		}
		
		/**
		* Creates a key to be used when grouping nodes together, this key is not unique but rather contains<br />
		* the fields that might be the same over several nodes. 
		* 
		* The key itself is a simple concatenation between a list of fields without any delimiters.
		*
		* <b>Included fields</b>
		* <ul>
		*	<li>node_state</li>
		*	<li>First element of features array</li>
		*	<li>cpus</li>
		*	<li>tmp_disk</li>
		*	<li>weight</li>
		*	<li>cores</li>
		*	<li>sockets</li>
		*	<li>threads</li>
		*	<li>real_memory</li>
		* </ul>
		* <b>Sidenotes</b>
		* <ul>
		*	<li>If a field isn't filled in, the string "NULL" will be added instead</li>
		* </ul>
		* @return string The key for this node
		* @access public
		* @see constants.php
		*/
		public function create_my_key() {
			$str = "";
			($this->node_state == NULL)       ? $str .= "NULL" : $str .= $this->node_state;
			($this->features[0] == NULL)      ? $str .= "NULL" : $str .= $this->features[0];
			($this->cpus == NULL)             ? $str .= "NULL" : $str .= $this->cpus;
			($this->tmp_disk == NULL)         ? $str .= "NULL" : $str .= $this->tmp_disk;
			($this->weight == NULL)           ? $str .= "NULL" : $str .= $this->weight;
			($this->cores == NULL)            ? $str .= "NULL" : $str .= $this->cores;
			($this->sockets == NULL)          ? $str .= "NULL" : $str .= $this->sockets;
			($this->threads == NULL)          ? $str .= "NULL" : $str .= $this->threads;
			($this->real_memory == NULL)	  ? $str .= "NULL" : $str .= $this->real_memory;
			return $str;
		}
	}
?>
