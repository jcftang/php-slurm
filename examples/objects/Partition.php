<?php
/**
* Partition.php - Object Oriented Class
*
* This class is an object-oriented representation of a Partition and it's detailed information.
* <br />This class also contains several functions to help in parsing and processing data specific to an instance of this Partition object.
* <b>Metadata</b>
* @link https://computing.llnl.gov/linux/slurm/
* @author Peter Vermeulen <nmb.peterv@gmail.com>
* @version 1.0
* @license http://www.gnu.org/licenses/gpl-2.0.html
* @package objects 
*/
	class Partition
	{	
		/**
		* The name of this partition
		*
		* @access public
		* @var string
		*/
		public $PartitionName 		= NULL;
		/**
		* Represents nodes assigned to running jobs
		* 
		* @access public
		* @var string
		*/
		public $AllocNodes 		= NULL;
		/**
		* What groups are allowed to access these nodes
		* 
		* @access public
		* @var string
		*/
		public $AllowGroups 		= NULL;
		/**
		* Represents if this partition is the default partition or not (YES if it is, NO if it isn't)
		*
		* @access public
		* @var string
		*/
		public $Default 		= NULL;
		/**
		* A unix timestamp formatted as the standard date and time string, if the job has no time set, this will be the value entered
		* <b>Example</b>
		* <ul>
		*	<li>Tue May 3 16:57:17 2011</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $DefaultTime 		= NULL;
		/**
		* Whether root can run jobs or not
		* 
		* @access public
		* @var string
		*/
		public $DisableRootJobs 	= NULL;
		/**
		* Represents if this partition is hidden or not (YES if it is, NO if it isn't)
		*
		* @access public
		* @var string
		*/
		public $Hidden 			= NULL;
		/**
		* The amount of nodes that can maximally be assigned to a job running on this partition
		*
		* @access public
		* @var int
		*/
		public $MaxNodes 		= NULL;
		/**
		* The amount of nodes that minimally needs to be assigned to a job for it to be able to run on this partition
		*
		* @access public
		* @var int
		*/
		public $MinNodes		= NULL;
		/**
		* the maximum time that a job can run for on this partition
		* <b>Example</b>
		* <ul>
		*	<li>4-00:00:00</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $MaxTime			= NULL;
		/**
		* The hostlist that represents all the nodes on this system
		*
		* @access public
		* @var string
		*/
		public $Nodes			= NULL;
		/**
		* For scheduling purposes
		*
		* @access public
		* @var string
		*/
		public $Priority		= NULL;
		/**
		* If the partition is only available to root users
		*
		* @access public
		* @var string
		*/ 
		public $RootOnly		= NULL;
		/**
		* Whether the nodes share resources or not
		*
		* @access public
		* @var string
		*/
		public $Shared			= NULL;
		/**
		* For scheduling purposes
		* 
		* @access public
		* @var string
		*/
		public $PreemptMode		= NULL;
		/** 
		* The state that the partition is in
		*
		* @access public
		* @var string
		*/
		public $State			= NULL;
		/**
		* The amount of CPU's on this partition
		*
		* @access public
		* @var int
		*/
		public $TotalCPUs		= NULL;
		/**
		* The amount of nodes on this partition
		*
		* @access public
		* @var int
		*/
		public $TotalNodes		= NULL;

		/**
		* This function uses the fields in this class to create a table row where each data cell contains a specific field's content.
		* 
		* This function also does a check against the hostlist for that partition and creates a link to the nodes.php page referring the hostlist
		* @return String A string variable containing a table row representing this instance and its fields
		* @access public
		*/
		public function get_as_row() {
			$members = get_object_vars($this);
			$out = "<tr>";
			foreach($members as $key => $value) {
				if($key != "Nodes") {
					$out .= validate_data_value($value);
					continue;
				} else {
					if(strcmp($value,"NULL")==0) {
						$out .= "<td>N/A</td>";
					} else {
						$out .= "<td><span class='styled_link'><a onclick=\"loadPage('nodes.php?hostlist=".$value."')\">".$value."</a></span></td>";
					}
				}
			}
			$out .=	"</tr>";
			return $out;
		}

		/**
		* This function receives an array containing the total amount of cpu's and nodes for all partitions to create a table row where the last 2 cells<br />
		* are the cpu's and nodes for all the partitions. (It uses a count of the class_vars to calculate the length of the actual row).
		* 
		* @param array an array containing the amount of cpu's in the first element and the amount of nodes in the second element
		* @return String A string variable containing a table row representing the amount of cpus and nodes
		* @access public
		*/
		public static function get_total_row($arr) {
			$count = count(get_class_vars("Partition"));
			$out = "<tr>";
			for($i = 0;$i < $count -2;$i++) {
				$out .= "<th class='th_style'></th>";
			}
			$out .= validate_data_value($arr[0],false,"th_style");
			$out .= validate_data_value($arr[1],false,"th_style");
			return $out;
		}
		
		/**
		* This function parses a raw partition array returned from slurm, and uses the data of the <br />
		* array to fill in it's fields accordingly.
		* 
		* @param array $part_arr a raw array of data specific to one partition
		* @access public
		*/
		public function parse_partition_array($part_arr) {
			$this->PartitionName	= $part_arr["PartitionName"];
			$this->AllocNodes	= $part_arr["AllocNodes"];
			$this->Default		= $part_arr["Default"];
			$this->DefaultTime	= $part_arr["DefaultTime"];
			$this->DisableRootJobs	= $part_arr["DisableRootJobs"];
			$this->Hidden		= $part_arr["Hidden"];
			$this->MaxNodes		= $part_arr["MaxNodes"];
			$this->MinNodes		= $part_arr["MinNodes"];
			$this->MaxTime		= $part_arr["MaxTime"];
			$this->Nodes		= $part_arr["Nodes"];
			$this->Priority		= $part_arr["Priority"];
			$this->RootOnly		= $part_arr["RootOnly"];
			$this->Shared		= $part_arr["Shared"];
			$this->PreemptMode	= $part_arr["PreemptMode"];
			$this->State		= $part_arr["State"];
			$this->TotalCPUs	= $part_arr["TotalCPUs"];
			$this->TotalNodes	= $part_arr["TotalNodes"];
		}
	}
?>
