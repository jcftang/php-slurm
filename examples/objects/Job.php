<?php
/**
* Job.php - Object Oriented Class
*
* This class is an object-oriented representation of a Job and it's detailed information.
* <br />This class also contains several functions to help in parsing and processing data specific to an instance of this Job object.
* <b>Sidenotes</b>
* <ul>
*	<li>this class only contains a selection of the variables that the actual job array returned by slurm has</li>
* </ul><br />
* <b>Metadata</b>
* @link https://computing.llnl.gov/linux/slurm/
* @author Peter Vermeulen <nmb.peterv@gmail.com>
* @version 1.0
* @license http://www.gnu.org/licenses/gpl-2.0.html
* @package objects 
*/
	class Job
	{	
		/**
		* The unique numerical id that this job was given when it was registered on the system
		*
		* @access public
		* @var ing
		*/
		public $JobId		= NULL;
		/**
		* The name that this job was given by the user
		*
		* @access public
		* @var string
		*/
		public $Name		= NULL;
		/**
		* The id of the user who's running the job
		*
		* @access public
		* @var string
		*/
		public $UserId		= NULL;
		/**
		* The group that the user belongs to
		*
		* @access public
		* @var string
		*/
		public $GroupId		= NULL;
		/**
		* The job priority
		*
		* @access public
		* @var string
		*/
		public $Priority	= NULL;
		/**
		* The account that the user is running this from 
		*
		* @access public
		* @var string
		*/
		public $Account		= NULL;
		/**
		* The state of the job ( is it running, pending, cancelled, ... )
		*
		* @access public
		* @var string
		*/
		public $JobState	= NULL;
		/**
		* The name of the partition that this job will (or is going to be) run on
		*
		* @access public
		* @var string
		*/
		public $Partition       = NULL;
		/**
		* The amount of cpu's requested for this job
		*
		* @access public
		* @var int
		*/
		public $NumCPUs		= NULL;
		/**
		* The hostlist of the nodes that this job is running on
		*
		* @access public
		* @var string
		*/
		public $NodeList	= NULL;
		/**
		* Sysadmin defined variable, used for scheduling purposes
		*
		* @access public
		* @var string
		*/
		public $Licenses	= NULL;
		/**
		* A unix timestamp formatted as the standard date and time string, representing the expected start time for this job
		* <b>Example</b>
		* <ul>
		*	<li>Tue May 3 16:57:17 2011</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $StartTime       = NULL;
		/**
		* A unix timestamp formatted as the standard date and time string, representing the expected end time for this job
		* <b>Example</b>
		* <ul>
		*	<li>Tue May 3 16:57:17 2011</li>
		* </ul><br /><br />
		* @access public
		* @var string
		*/
		public $EndTime		= NULL;
		/**
		* The reason as to why a job is being run
		*
		* @access public
		* @var string
		public $Reason		= NULL;

		/**
		* This function uses the fields in this class to create a table row where each data cell contains a specific field's content.
		* 
		* <b>Sidenotes</b>
		* <ul>
		*	<li>This function checks the JobState and uses it to add a specific color class (JOB_*_COLOR_CLASS)</li>
		* 	<li>If a hostlist is set then this function will automatically add a link to the nodes.php page for that specific hostlist</li>
		*	<li>An optional parameter can be given, this optional parameter can have these values<ul>
		*		<li><b>RUNNING</b> : If the job is not in a running state, the row will not be generated <li>
		*		<li><b>PENDING</b> : If the job is not in a pending state, the row will not be generated <li>
		*		<li><b>ALL</b> [DEFAULT]: the row will always be generated<li>
		*	</ul></li>
		* </ul>
		*
		* @return String A string variable containing a table row representing this instance and its fields
		* @access public
		* @see constants.php
		*/
		public function get_as_row($action="ALL") {
			$members = get_object_vars($this);
			$out = "<tr>";
			$color_class = NULL;
			if($action!="ALL") {
				if(($action=="RUNNING") && ($members['JobState'] != "RUNNING")) {
					return;
				}
				if(($action=="PENDING") && ($members['JobState'] != "PENDING")) {
					return;
				}
			}
			foreach($members as $key => $value) {
				switch($value) {
					case 'PENDING':
						$color_class = JOB_PENDING_COLOR_CLASS;
						break;
					case 'RUNNING':
						$color_class = JOB_RUNNING_COLOR_CLASS;
						break;
					case 'COMPLETED':
						$color_class = JOB_COMPLETE_COLOR_CLASS;
						break;
					default:
						$color_class = DEFAULT_COLOR_CLASS;
						break;
				}
				if(($key=="NodeList") && ($value!=NULL)) {
					$out .= "<td class='".$color_class."'><span class='styled_link'><a onclick=\"loadPage('nodes.php?hostlist=";
					$out .= $value."')\">".$value."</a></span></td>";
				} else {
					$out .= validate_data_value($value,false,$color_class);
				}
			}
			$out .= "</tr>";
			return $out;
		}

		/**
		* This function parses a raw job array returned from slurm, and uses the data of the <br />
		* array to fill in it's fields accordingly.
		*
		* <b>Sidenotes</b>
		* <ul>
		*	<li>This function uses regex to clip the UserId and GroupId from "(...)", this was something specific to the system that i've<br />
		*	been testing on, and if this is not apparent on your system comment it out</li>
		* </ul>
		* 
		* @param array $job_arr a raw array of data specific to one job
		* @access public
		*/
		public function parse_job_array($job_arr) {
			$pattern = '/\([0-9a-zA-Z]*\)/';

			$this->JobId = $job_arr["JobId"];
			$this->Name = $job_arr["Name"];
			$this->UserId = preg_replace($pattern,"",$job_arr["UserId"]);
			$this->GroupId = preg_replace($pattern,"",$job_arr["GroupId"]);
			$this->Priority = $job_arr["Priority"];
			$this->Account = $job_arr["Account"];
			$this->JobState = $job_arr["JobState"];
			$this->Partition = $job_arr["Partition"];
			$this->NumCPUs = $job_arr["NumCPUs"];
			$this->NodeList = $job_arr["NodeList"];
			$this->Licenses = $job_arr["Licenses"];
			$this->StartTime= $job_arr["StartTime"];
			$this->EndTime= $job_arr["EndTime"];
			$this->Reason = $job_arr["Reason"];
		}

		/**
		* Creates a key to be used when grouping jobs together, this key is not unique but rather contains<br />
		* the fields that might be the same over several jobs.
		* 
		* The key itself is a simple concatenation between a list of fields without any delimiters.
		*
		* <b>Included fields</b>
		* <ul>
		*	<li>Partition</li>
		*	<li>Priority</li>
		* </ul>
		* <b>Sidenotes</b>
		* <ul>
		*	<li>If a field isn't filled in, the string "NULL" will be added instead</li>
		*	<li>I didn't add many fields to this key due to the fact that i was already sorting the jobs on JobState</li>
		* </ul>
		* @return string The key for this node
		* @access public
		* @see constants.php
		*/
		public function create_my_key() {
                        $str = "";
			($this->Partition == NULL)	? $str .= "NULL" : $str .= $this->Partition;
			($this->Priority == NULL)	? $str .= "NULL" : $str .= $this->Priority;
                        return $str;
                }

		/**
		* Comparison function that is used for running usort on a jobArray, this checks if the Partition is alphabetically higher,lower or equal in value.
		* <br />If equal in value it will run another check, but this time against the priority, if the priority is higher the comparison will return -1,
		* <br /> if lower it will return 1.
		* @param Job $a The first Job element to use in comparison
		* @param Job $b The job element that $a is being compared to
		* @return int The result of the comparison
		*/
		public function cmp($a, $b)
		{
			if(strcmp($a->Partition,$b->Partition)==0) {
				if($a->Priority > $b->Priority) {
					return -1;
				} else if($a->Priority < $b->Priority) {
					return 1;
				} else {
					return 0;
				}
			} else if(strcmp($a->Partition,$b->Partition)==-1) {
				return -1;
			} else {
				return 1;
			}
		}
	}
?>
