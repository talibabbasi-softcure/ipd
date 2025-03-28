<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-Add IPDhead Calculate-");
	/* log4php end here */
	include("../system/rateLimit.php");  // form submission limit
	/*Get data from prevois page to store in database*/
	$priority=$_POST['ipdPriority'];
	if($departmentId==''){
		$departmentId=0;
	}
	if($ipdHeadName==''){
		$_SESSION['sess_opd_msg']=3;
		header("location:add-ipdHead.php");
		exit();
	}
	if($_POST['did']){
		$did=$_POST['did'];
		$nn="update health_manageripdhead set rateListId='$rateListId',irda_master='$irda_master',code='$code', departmentId='$departmentId',tpaId='$tpaId',irda_code='$irda_code', ipdHeadName='$ipdHeadName',ipdHeadRate='$ipdHeadRate',roomId='$roomId',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdHeadId='$did'";
		mysql_query_db("update health_manageripdhead set  rateListId='$rateListId',irda_master='$irda_master',code='$code', departmentId='$departmentId',tpaId='$tpaId',irda_code='$irda_code',priority='$priority', ipdHeadName='$ipdHeadName',ipdHeadRate='$ipdHeadRate',roomId='$roomId',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdHeadId='$did'");
		//$logger->warn("IPDhead updatde by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query-".$nn);
		$_SESSION['sess_opd_msg']=2;
	}
	else{
		$nn="insert into health_manageripdhead set  rateListId='$rateListId',irda_master='$irda_master',code='$code', departmentId='$departmentId',tpaId='$tpaId', ipdHeadName='$ipdHeadName',ipdHeadRate='$ipdHeadRate',roomId='$roomId',ipdHeadStatus=1,lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'";
		mysql_query_db("insert into health_manageripdhead set  rateListId='$rateListId',irda_master='$irda_master',code='$code', departmentId='$departmentId',tpaId='$tpaId',irda_code='$irda_code',priority='$priority',ipdHeadName='$ipdHeadName',ipdHeadRate='$ipdHeadRate',roomId='$roomId',ipdHeadStatus=1,lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'");
		//$logger->warn("IPD head added by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query-".$nn);
		//$departmentId=mysql_insert_id_db($sql);
		$_SESSION['sess_opd_msg']=1;
	}
	//header("location:add-department.php?printID=".$patientId);
	header("location:add-ipdHead.php");
