<?php
	session_start();                                     /* Here session is start */
	//require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	//require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	//variable file
	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	ob_start();
	ini_set('date.timezone', 'Asia/Kolkata');
    $dbserver="localhost";
    $dbname="softcure";
    $dbuser="root";
    $dbpass="";
    define('SITE_PATH',"http://localhost/softcure/");
	define('SITE_SEO',"SoftCure Solutions");
	define('SITE_TITLE',"Softcure HMS");
	define('SITE_ADMIN_TITLE',"SoftCure Solutions");
	define('SITE_ADMIN_HEADER',"SoftCure Admin Panel");
	define('PAGESIZE',"50");
	define('MSG_TITLE',"");
	define('VERSION'," V 1.1.1.1.1");
	global $mysqli;
	$mysqli = new mysqli($dbserver, $dbuser, $dbpass, $dbname);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		echo $mysqli->host_info . "\n";
	}
	function mysql_query_db($sqlq)
	{
		global $mysqli;
		$result=$mysqli->query($sqlq) or die("<span style='FONT-SIZE:11px; FONT-COLOR: #000000; font-family=tahoma;'><center>An Internal Error has Occured. Please report following error to the webmaster.<br> ".$sqlq."<br><br>".$mysqli->error."</center></FONT>");
		return $result;
	}
	function mysql_num_db($resultn) 
	{
		$numrow=$resultn->num_rows;
		return $numrow;
	}
	function mysql_fetch_db($resultn)
	{
		$frow=$resultn->fetch_array();
		return $frow;
	}
	function mysql_insert_id_db()
	{
		global $mysqli;
		$last_row=mysqli_insert_id($mysqli);
		return $last_row;
	}
	function mysql_escape($var_esc)
	{
		global $mysqli;
		$escape_string=$mysqli->real_escape_string($var_esc);
		return $escape_string;
	}
	function htmlsa($var_htmsa)
	{
		if($_SERVER['HTTP_HOST']=="localhost" || $_SERVER['HTTP_HOST']=="s3") {
			//$var_htmsa=htmlentities($var_htmsa); will not run on localhost
		}
		else
		{
			$var_htmsa=htmlentities($var_htmsa);
		}
		//$var_htmsa=addslashes($var_htmsa);
		return $var_htmsa;
	}
	function cleanInput($input) {
		$search = array(
		'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
		'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
		'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
		);
		$output = preg_replace($search, '', $input);
		return $output;
	}
	function sanitize($input) {
		if (is_array($input)) {
			//print_r($input);
			foreach($input as $var=>$val) {
				$output[$var] = htmlsa(sanitize($val));
			}
		}
		else {
			if($input != strip_tags($input)) {
				// contains HTML
				$output=addslashes($input);
			}
			else
			{
				if (get_magic_quotes_gpc()) {
					$input = stripslashes(htmlsa($input));
				}
				$input  = cleanInput($input);
				$output = trim(mysql_escape($input));
			}
			$output = str_replace("\r\n",'', $output);
			$output = str_replace("\\r\\n",'', $output);
		}
		return $output;
	}
	//$_GET=sanitize($_GET);
	//$_REQUEST=sanitize($_REQUEST);
	//$_POST = sanitize($_POST);
	if($_REQUEST){$_REQUEST = array_map('sanitize', $_REQUEST);}
	if($_POST){$_POST = array_map('sanitize', $_POST);}
	if($_GET){$_GET = array_map('sanitize', $_GET);}
?>
<?php
	$noOfDaysMonth=date('t');
	$monthName=date('M');
	$cur_year=date('Y');
	$cur_date=date('d-M-Y');   /* Current date */
	$save_date=date('Y-m-d');  /* Current date for ave in database*/
	$cur_time=date('H:i:s');   /* Current time */
	$curDateTime=date('Y-m-d H:i:s');
	$today=date('Y-m-d');
	$ipAddress=$_SERVER['REMOTE_ADDR'];
	//require code
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	//validate_user();                                     /* Validate user for open/access this page with login. */
	$dateTime=date("Y-m-d h:i:s");
	/*auto four charges
		RMO Date wise
		Room Rent Date wise
		Nursing Date wise 
		Food Charges Date wise
	end */
	//fetch all ipd from occupancy
	$ipdOccupancy=mysql_query_db("select * from health_occupancy where status='1'");
	$numipdOccupancy= mysql_num_db($ipdOccupancy);
	if($numipdOccupancy!=0){
		while($allOccupancy=mysql_fetch_db($ipdOccupancy)){
			$ipdId=$allOccupancy['ipdId'];
			$ipd= mysql_query_db("select * from health_ipd where ipdId='$ipdId'");
			$fipd= mysql_fetch_db($ipd);
			if($fipd['payer']!=1)
			{
				continue;//no auto code for tpa patient
			}
			$sql=mysql_query_db("select * from health_patient where patientId='".$fipd['patientId']."'");
			$line=mysql_fetch_db($sql);
			$patientId=$line['patientId'];
			//calculating auto insert ipdmanager ids for a specific IPD
			$ipdHeaders=array();
			//finding Room Charge
			$room= mysql_query_db("select ipdHeadId from health_manageripdhead where priority=1 and ipdHeadStatus=1 and tpaId=1 and roomId='".$allOccupancy['categoryId']."'");
			$num=mysql_num_db($room);
			if($num!=0){
				$froom= mysql_fetch_db($room);
				$ipdHeaders[]=$froom['ipdHeadId'];
			}
			//finding nursing charge 
			$nursing= mysql_query_db("select ipdHeadId from health_manageripdhead where priority=2 and ipdHeadStatus=1 and tpaId=1 and roomId='".$allOccupancy['categoryId']."'");
			$num=mysql_num_db($nursing);
			if($num!=0){
				$fnursing= mysql_fetch_db($nursing);
				$ipdHeaders[]=$fnursing['ipdHeadId'];
			}
			//finding rmo charge 
			$rmo= mysql_query_db("select ipdHeadId from health_manageripdhead where priority=7 and ipdHeadStatus=1 and tpaId=1 and roomId='".$allOccupancy['categoryId']."'");
			$num=mysql_num_db($rmo);
			if($num!=0){
				$frmo= mysql_fetch_db($rmo);
				$ipdHeaders[]=$frmo['ipdHeadId'];
			}
			//finding food charge 
			$food= mysql_query_db("select ipdHeadId from health_manageripdhead where priority=16 and ipdHeadStatus=1 and tpaId=1 and roomId='".$allOccupancy['categoryId']."'");
			$num=mysql_num_db($food);
			if($num!=0){
				$ffood= mysql_fetch_db($food);
				$ipdHeaders[]=$ffood['ipdHeadId'];
			}
			//code to execute in bulk/combine form
			$ipdHeader=implode(',',$ipdHeaders);
			$patientIpdHeader=$ipdHeaders;
			$count=count($patientIpdHeader); 
			//find ipd already in ipdfinalbill
			$ipdAgain=mysql_query_db("select * from health_ipdfinalbill where ipdId='$ipdId'");
			$numAgain= mysql_num_db($ipdAgain);
			//breakup format
			for($j=0;$j<$count;$j++){	    
				//entry for auto date wise ipd treatment
				$quantity=1;
				$ipdHead= mysql_query_db("select * from health_managerIpdHead where ipdHeadId='".$patientIpdHeader[$j]."'");
				$fipdHead= mysql_fetch_db($ipdHead);
				$ipdHeadRate=$fipdHead['ipdHeadRate'];
				$priority=$fipdHead['priority'];
				$irda_code=$fipdHead['irda_code'];
				$headName=$fipdHead['ipdHeadName'];
				$headName=addslashes($headName);
				$totalPrice=$quantity*$ipdHeadRate;
				$code=$fipdHead['code'];
				mysql_query_db("insert into health_patientipdhead set code='$code',ipdId='$ipdId',headName='$headName',patientId='$patientId',ipdHeadId='$patientIpdHeader[$j]',ipdHeadDate='$today',priority='$priority',irda_code='$irda_code',ipdHeadRate='$ipdHeadRate',quantity='$quantity',totalPrice='$totalPrice',lastUpdate='$dateTime',updateBy='',ipAddress='$ipAddress'");    
			} // loop ends
			//summary format for billing
			for($j=0;$j<$count;$j++){	    
				//entry to ipdfinal bill
				$quantity=1;
				//find ipd already in ipdfinalbll or not
				$ipdHead= mysql_query_db("select * from health_managerIpdHead where ipdHeadId='".$patientIpdHeader[$j]."'");
				$fipdHead= mysql_fetch_db($ipdHead);
				$ipdHeadRate=$fipdHead['ipdHeadRate'];
				if($numAgain==0) // first time
				{
					$priority=$fipdHead['priority'];
					$irda_code=$fipdHead['irda_code'];
					$headName=$fipdHead['ipdHeadName'];
					$headName=addslashes($headName);
					$code=$fipdHead['code'];
					$totalPrice=$quantity*$ipdHeadRate;
					mysql_query_db("insert into health_ipdfinalbill set code='$code',ipdId='$ipdId',patientId='$patientId',ipdHeadId='$patientIpdHeader[$j]',headName='$headName',price='$ipdHeadRate',quantity='$quantity',totalPrice='$totalPrice',priority='$priority',irda_code='$irda_code',lastUpdate='$dateTime',updateBy='',ipAddress='$ipAddress'");    
				}
				else{  //entry again
					$addIpdAgain=mysql_query_db("select * from health_ipdfinalbill where ipdId='$ipdId' and ipdHeadId='".$patientIpdHeader[$j]."' and price='$ipdHeadRate'");
					$numHeadRepeat=mysql_num_db($addIpdAgain);
					if($numHeadRepeat!=0) //if ipdhead is exist with same rate as earlier
					{
						$faddIpdAgain= mysql_fetch_db($addIpdAgain);
						$addId=$faddIpdAgain['id'];
						//$oldipdHeadRate=$faddIpdAgain['price']; //old price 
						$newquantity=$quantity+$faddIpdAgain['quantity'];
						$totalPrice=$newquantity*$ipdHeadRate;
						mysql_query_db("update health_ipdfinalbill set quantity='$newquantity',price='$ipdHeadRate',totalPrice='$totalPrice',lastUpdate='$dateTime',updateBy='',ipAddress='$ipAddress' where id='$addId'");    
					}
					else{
						//if ipdid exist but ipdhead is coming with diff price, so need new entry
						//$ipdHead= mysql_query_db("select * from health_managerIpdHead where ipdHeadId='".$patientIpdHeader[$j]."'");
						//$fipdHead= mysql_fetch_db($ipdHead);
						//$ipdHeadRate=$fipdHead['ipdHeadRate'];
						$priority=$fipdHead['priority'];
						$irda_code=$fipdHead['irda_code'];
						$headName=$fipdHead['ipdHeadName'];
						$headName=addslashes($headName);
						$code=$fipdHead['code'];
						$totalPrice=$quantity*$ipdHeadRate;
						mysql_query_db("insert into health_ipdfinalbill set code='$code',ipdId='$ipdId',patientId='$patientId',ipdHeadId='$patientIpdHeader[$j]',headName='$headName',price='$ipdHeadRate',quantity='$quantity',totalPrice='$totalPrice',priority='$priority',irda_code='$irda_code',lastUpdate='$dateTime',updateBy='',ipAddress='$ipAddress'");    
					}
				}//end of else	
			}//end of loop
		}//end of while
	}//if occupancy
?>