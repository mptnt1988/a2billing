#!/usr/bin/php -q
<?php 
/***************************************************************************
 *            a2billing_batch_cache.php
 *
 *  Fri Oct 21 11:51 2008
 *  Copyright  2008  A2Billing
 *  ADD THIS SCRIPT IN A CRONTAB JOB
 *
	crontab -e
	* / 1 * * * * php /var/lib/asterisk/agi-bin/libs_a2billing/crontjob/a2billing_batch_cache.php
	
	field	 allowed values
	-----	 --------------
	minute	 		0-59
	hour		 	0-23
	day of month	1-31
	month	 		1-12 (or names, see below)
	day of week	 	0-7 (0 or 7 is Sun, or use names)

	#Run command every 5 minutes during 6-13 hours
	* / 5 6-13 * * mon-fri test.script    !!! no space between * / 5
	 
****************************************************************************/

set_time_limit(0);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
//dl("pgsql.so"); // remove "extension= pgsql.so !

include_once (dirname(__FILE__)."/lib/Class.Table.php");
include (dirname(__FILE__)."/lib/interface/constants.php");
include (dirname(__FILE__)."/lib/Class.A2Billing.php");
include (dirname(__FILE__)."/lib/Misc.php");
include (dirname(__FILE__)."/lib/ProcessHandler.php");

if (!defined('PID')) {
    define("PID","/tmp/a2billing_batch_cache_pid.php");
}

// CHECK IF THE CRONT PROCESS IS ALREADY RUNNING
if (ProcessHandler::isActive()) {
	// Already running!
	die();
} else {
	ProcessHandler::activate();
	// run the cront
}


$verbose_level=1;
$nb_record = 100;
$wait_time= 10;

$A2B = new A2Billing();
$A2B -> load_conf($agi, NULL, 0, $idconfig);

write_log(LOGFILE_CRONT_BATCH_PROCESS, basename(__FILE__).' line:'.__LINE__."[#### BATCH BEGIN ####]");

if (!$A2B -> DbConnect()){				
	if ($verbose_level>=1) echo "[Cannot connect to the database]\n";
	write_log(LOGFILE_CRONT_BATCH_PROCESS, basename(__FILE__).' line:'.__LINE__."[Cannot connect to the database]");
	exit;
}

$instance_table = new Table();

if ($A2B->config["global"]['cache_enabled']){
	if (empty($A2B -> config["global"]['cache_path'])){				
		if ($verbose_level>=1)
			 echo "[Path to the cache is not defined]\n";
		
		write_log(LOGFILE_CRONT_BATCH_PROCESS, basename(__FILE__).' line:'.__LINE__."[Path to the cache is not defined]");
		exit;
	}
	
	if ( ! file_exists( $A2B -> config["global"]['cache_path'] ) ){
		if ($verbose_level>=1) 
			echo "[File doesn't exist or permission denied]\n";
		
		write_log(LOGFILE_CRONT_BATCH_PROCESS, basename(__FILE__).' line:'.__LINE__."[File doesn't exist or permission denied]");
		exit;
	}
	
	if ($db = sqlite_open($A2B -> config["global"]['cache_path'], 0666, $sqliteerror)) {
	 // sqlite_query($db,'CREATE TABLE foo (bar varchar(10))');
	  
		  
		for (;;) {
			$result = sqlite_array_query($db,"SELECT rowid , * from cc_call limit $nb_record",SQLITE_ASSOC);
			if(sizeof($result)>0){
				$column = "";
				$values = "";
				$delete_id = "( ";
				for($i=0;$i<sizeof($result);$i++)
				{
					$j=0;
					if($i==0)
						 $values .= "( ";
					else
						 $values .= ",( ";
						 
					$delete_id .= $result[$i]['rowid'];
					if(sizeof($result)>0 && $i<sizeof($result)-1)
						 $delete_id .= " , ";
					
					foreach($result[$i] as $key => $value){	
						$j++;
						if($key=="rowid") 
							continue;					
						if($i==0){ 
							$column .= " $key ";
							if($j<sizeof($result[$i])) 
								$column .= ",";
						}
						$values .= " '$value' ";
						if($j < sizeof($result[$i])) 
							$values .= ",";
						
					}
					$values .= " )";
				
				}
				$delete_id .= " )";
				$INSERT_QUERY = "INSERT INTO cc_call ( $column ) VALUES $values";
				if ($verbose_level>=1)
					 echo "QUERY INSERT : [$INSERT_QUERY]\n";
				$instance_table -> SQLExec ($A2B -> DBHandle, $INSERT_QUERY);
				$DELETE_QUERY = "DELETE FROM cc_call WHERE rowid in $delete_id";
				if ($verbose_level>=1) 
					echo "QUERY DELETE : [$DELETE_QUERY]\n";
				sqlite_query($db,$DELETE_QUERY);
			}
			echo "Waiting ....\n";
			sleep($wait_time);	
		}
	
	  
	} else {
		if ($verbose_level>=1) 
			echo "[Error to connect to cache : $sqliteerror]\n";
		write_log(LOGFILE_CRONT_BATCH_PROCESS, basename(__FILE__).' line:'.__LINE__."[Error to connect to cache : $sqliteerror]\n");
	} 
		
	
}
         
        

if ($verbose_level>=1) 
	echo "#### END RECURRING SERVICES \n";
write_log(LOGFILE_CRONT_BATCH_PROCESS, basename(__FILE__).' line:'.__LINE__."[#### BATCH PROCESS END ####]");
	
?>