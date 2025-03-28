<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); 
	//require_once("../otp_call.php");/* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-OPD Registration -");
	/* log4php end here */
	include("../system/rateLimit.php");  // form submission limit
	$addiction='';
	$symptoms=addslashes($symptoms); 
	//add in table if not exist in table
	$nArray=array();
	$nArray=explode("<br>",$symptoms);
	$nArray=array_unique($nArray);
	//print_r($nArray);
	foreach($nArray as $key){
		//echo $key;echo "<br>";
		//ADD COMP IN MASTER IF NOT EXIST
		//echo "select * from health_emr_presenting_complaints where name='$key'";
		$comList= mysql_query_db("select * from health_emr_presenting_complaints where name='$key'");
		$comNum= mysql_num_db($comList);
		if($comNum==0){
			echo "insert into health_emr_presenting_complaints set name='$key' and status=1";
			mysql_query_db("insert into health_emr_presenting_complaints set name='$key',status=1");
		}
	}
	/*
		//club symptoms values to textarea
		$nameSymptoms=$_POST['nameSymptoms'];
		$durationSymptoms=$_POST['durationSymptoms'];
		$count=count($nameSymptoms);
		$mergeSymptoms="";
		for($j=0;$j<$count;$j++){
		if($nameSymptoms[$j]!=''){
		if(($j+1)==$count){
		$mergeSymptoms.=$nameSymptoms[$j]." : ".$durationSymptoms[$j];	
		}
		else{
		$mergeSymptoms.=$nameSymptoms[$j]." : ".$durationSymptoms[$j].", ";
		}
		}
		}
		$symptoms=strtoupper(addslashes($mergeSymptoms)); 
	*/
	//club medical history values to textarea
	$nameMedical=$_POST['nameMedical'];
	$durationMedical=$_POST['durationMedical'];
	$medicationMedical=$_POST['medicationMedical'];
	$count=count((array)$nameMedical);
	$mergeMedical="";
	for($j=0;$j<$count;$j++){
		if($nameMedical[$j]!=''){
			if(($j+1)==$count){
				$mergeMedical.=$nameMedical[$j]." : ".$durationMedical[$j]." : ".$medicationMedical[$j];	
			}
			else{
				$mergeMedical.=$nameMedical[$j]." : ".$durationMedical[$j]." : ".$medicationMedical[$j].", ";
			}   
			//add in master if not exist in db
			$comList= mysql_query_db("select * from health_emr_past_medical_history where name='".strtoupper($nameMedical[$j])."'");
			$comNum= mysql_num_db($comList);
			if($comNum==0){
				//echo "insert into health_emr_presenting_complaints set name='$key' and status=1";
				mysql_query_db("insert into health_emr_past_medical_history set name='".strtoupper($nameMedical[$j])."',status=1");
			}
		}
	}
	$past_medical=strtoupper(addslashes($mergeMedical));
	//club surical history values to textarea
	$nameSurgical=$_POST['nameSurgical'];
	$dateSurgical=$_POST['dateSurgical'];
	$surgeonSurgical=$_POST['surgeonSurgical'];
	$hospitalSurgical=$_POST['hospitalSurgical'];
	$count=count((array)$nameSurgical);
	$mergeSurgical="";
	for($j=0;$j<$count;$j++){
		if($nameSurgical[$j]!=''){
			if(($j+1)==$count){
				$mergeSurgical.=$nameSurgical[$j]." : ".$dateSurgical[$j]." : ".$surgeonSurgical[$j]." : ".$hospitalSurgical[$j];
			}
			else{
				$mergeSurgical.=$nameSurgical[$j]." : ".$dateSurgical[$j]." : ".$surgeonSurgical[$j]." : ".$hospitalSurgical[$j].", ";
			}
			//add in master if not exist in db
			$comList= mysql_query_db("select * from health_emr_past_surgical_history where name='".strtoupper($nameSurgical[$j])."'");
			$comNum= mysql_num_db($comList);
			if($comNum==0){
				//echo "insert into health_emr_past_surgical_history set name='".$nameSurgical[$j]."',status=1";
				mysql_query_db("insert into health_emr_past_surgical_history set name='".strtoupper($nameSurgical[$j])."',status=1");
			}
		}
	}
	$past_surgical=strtoupper(addslashes($mergeSurgical)); 
	//club addiction history values to textarea
	$nameAddiction=$_POST['nameAddiction'];
	$durationAddiction=$_POST['durationAddiction'];
	$unitsAddiction=$_POST['unitsAddiction'];
	$frequencyAddiction=$_POST['frequencyAddiction'];
	$actionAddiction=$_POST['actionAddiction'];
	$count=count((array)$nameAddiction);
	$mergeAddiction="";
	for($j=0;$j<$count;$j++){
		if($nameAddiction[$j]!=''){
			if(($j+1)==$count){
				$mergeAddiction.=$nameAddiction[$j]." : ".$durationAddiction[$j]." : ".$unitsAddiction[$j]." : ".$frequencyAddiction[$j]." : ".$actionAddiction[$j];  
			}
			else{	
				$mergeAddiction.=$nameAddiction[$j]." : ".$durationAddiction[$j]." : ".$unitsAddiction[$j]." : ".$frequencyAddiction[$j]." : ".$actionAddiction[$j].", ";  
			}
		}
	}
	$addiction=strtoupper(addslashes($mergeAddiction));
	//club Personal history values to textarea
	if(!empty(trim($_POST['diet']))){
		$mergePersonal="Diet:".$_POST['diet'].", Appetite:".$_POST['appetite'].", Sleep:".$_POST['sleep'].", Bladder:".$_POST['bladder'].", Bowel:".$_POST['bowel'];
		$personal_history=strtoupper(addslashes($mergePersonal));
	}
	else{
		$personal_history='';	
	}
	if($patientId){
		if(trim($notesId)!=""){
			$_SESSION['sess_notes_msg']=2;
			//edit old entry
			mysql_query_db("update health_emr_patient set bsType='$bsType',bs='$bs',symptoms='$symptoms',addiction='$addiction',current_treatment='$current_treatment',bsa='$bsa',sbp='$sbp',dbp='$dbp',map='$map',egfr='$egfr',temp='$temp',pulse='$pulse',res='$res',height='$height',weight='$weight',oxygen='$oxygen',bmi='$bmi',bmi_result='$bmi_result',pain_score='$pain_score',personal_history='$personal_history',patientType='$patientType',marrital='$marrital',presenting_complaints='$presenting_complaints',past_medical='$past_medical',past_medical_family='$past_medical_family',past_surgical='$past_surgical',sexual_history='$sexual_history',exposure='$exposure',ejaculatory='$ejaculatory',child_history='$child_history',gen_examination='$gen_examination',local_examination='$local_examination',comments='$comments',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress',lastUpdate='$curDateTime' where id='$notesId'");
			if($ipdId!='')
			{
				header("location:doctor-notes?ipdId=".encryptSoft($ipdId));	 
			}
			else{
				header("location:doctor-notes?opdId=".encryptSoft($opdId));
			}	
		} 
		else {
			//fresh entry
			$nn="insert into health_emr_patient set bsType='$bsType',bs='$bs',symptoms='$symptoms',addiction='$addiction',current_treatment='$current_treatment',bsa='$bsa',patientId='$patientId',opdId='$opdId',ipdId='$ipdId',date='$save_date',time='$cur_time',sbp='$sbp',dbp='$dbp',map='$map',egfr='$egfr',temp='$temp',pulse='$pulse',res='$res',height='$height',weight='$weight',oxygen='$oxygen',bmi='$bmi',bmi_result='$bmi_result',pain_score='$pain_score',patientType='$patientType',personal_history='$personal_history',marrital='$marrital',presenting_complaints='$presenting_complaints',past_medical='$past_medical',past_medical_family='$past_medical_family',past_surgical='$past_surgical',sexual_history='$sexual_history',exposure='$exposure',ejaculatory='$ejaculatory',child_history='$child_history',gen_examination='$gen_examination',local_examination='$local_examination',comments='$comments',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'";
			mysql_query_db("insert into health_emr_patient set bsType='$bsType',bs='$bs',symptoms='$symptoms',addiction='$addiction',current_treatment='$current_treatment',bsa='$bsa',patientId='$patientId',opdId='$opdId',ipdId='$ipdId',date='$save_date',time='$cur_time',sbp='$sbp',dbp='$dbp',map='$map',egfr='$egfr',temp='$temp',pulse='$pulse',res='$res',height='$height',weight='$weight',oxygen='$oxygen',bmi='$bmi',bmi_result='$bmi_result',pain_score='$pain_score',patientType='$patientType',personal_history='$personal_history',marrital='$marrital',presenting_complaints='$presenting_complaints',past_medical='$past_medical',past_medical_family='$past_medical_family',past_surgical='$past_surgical',sexual_history='$sexual_history',exposure='$exposure',ejaculatory='$ejaculatory',child_history='$child_history',gen_examination='$gen_examination',local_examination='$local_examination',comments='$comments',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress',lastUpdate='$curDateTime'");
			$insert_id=mysql_insert_id_db($sql);
			//$logger->warn("Add Surgical patient  notes  and his/her patient ID is ".$patientId." is edit by ".$_SESSION['sess_uname']." on date and time is ".$curDateTime." and IP Address is ".$ipAddress." query is ".$nn);
			$_SESSION['sess_notes_msg']=1;
			if($ipdId!='')
			{
				header("location:doctor-notes?ipdId=".encryptSoft($ipdId));	 
			}
			else{
				header("location:doctor-notes?opdId=".encryptSoft($opdId));
			}
		}
	}
	else {
		$_SESSION['sess_notes_msg']=3;
		header("location:doctor-notes.php");
	}
?>
