<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	//require_once("../barcode/barcode.php");/* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-IPD Registration-");
	$ipdId=$_POST['ipdId'];
	$doctorId=$_POST['consultantId'];
	if($ipdId!='' && $doctorId!=''){	
		$addDoctor=mysql_query_db("select * from health_ipd_additional_doctor where ipdId='$ipdId' order by id desc limit 0,1");
		$numAdd=mysql_num_db($addDoctor);
		if($numAdd==0){
			mysql_query_db("insert into health_ipd_additional_doctor set doctorId='$doctorId', ipdId='$ipdId',reason='$reasonAddCon',date='$today',time='$cur_time',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'");	
		}
		else{
			mysql_query_db("update health_ipd_additional_doctor set doctorId='$doctorId', ipdId='$ipdId',reason='$reasonAddCon',date='$today',time='$cur_time',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdId='$ipdId'");
		}
	}
	header("location:report.php");
?>