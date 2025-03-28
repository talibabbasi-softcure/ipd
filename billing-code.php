<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-Billing Apply-");
	/* log4php end here */
	$dateTime=date("Y-m-d H:i:s");
	$save_date=$_POST['billDate'];
	if($ipdId){
		// for billing
		$id_bill_count=count((array)$id_bill);
		if($id_bill_count!=0)
		{ //when bill format not able to change rate,unit then no work of this code
			for($j=0;$j<$id_bill_count;$j++){
				//updating all whether change or not
				mysql_query_db("update health_ipdfinalbill set price='".$rate_bill[$j]."',quantity='".$unit_bill[$j]."',totalPrice='".$ttl_price_bill[$j]."',lastUpdate='$dateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where id='".$id_bill[$j]."'");    
			}
		}
		else{
			//nothing
		}
		//for final billing	
		$grossAmount=floatval($grossAmount);
		$additionAmount=floatval($additionAmount);
		$discountAmount=floatval($discountAmount);
		$FinalNetAmount=($grossAmount+$additionAmount)-$discountAmount;
		$FinalNetAmount=round($FinalNetAmount);
		//first time code to run
		$check= mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
		$num=mysql_num_db($check);
		if($num==0){
			//occupancy free
			//mysql_query_db("update health_occupancy set status=0 where ipdId='$ipdId'");
			//for report purpose
			//mysql_query_db("update health_financeipd set finalbill='1',postDate='$save_date' where ipdId='$ipdId'");
			//first time bill;
			$nn="insert into health_ipdbill set payer='$payer',additionAmount='$additionAmount',additionco='$additionco', billTime='$billTime',ipdId='$ipdId', patientId='$patientId',grossAmount='$grossAmount',discountAmount='$discountAmount',netAmount='$FinalNetAmount',billDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'";
			$nn2="insert into health_discount set patientId='$patientId',discountAmount='$discountAmount',discountDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'";
			mysql_query_db("insert into health_ipdbill set payer='$payer',additionAmount='$additionAmount',additionco='$additionco',billTime='$billTime',ipdId='$ipdId', co='$co', patientId='$patientId',grossAmount='$grossAmount',discountAmount='$discountAmount',netAmount='$FinalNetAmount',billDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'");
			$billId=mysql_insert_id_db($sql);
			mysql_query_db("insert into health_discount set ipdId='$ipdId', co='$co', patientId='$patientId',discountAmount='$discountAmount',discountDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress'");
			//query to give bill id to ipdfinalbill
			mysql_query_db("update health_ipdfinalbill set BillId='$billId' where ipdId='$ipdId'");
			$_SESSION['sess_ipd_msg']=1;
			//$logger->warn("Billing done on PUID ".$patientId." by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress." query is ".$nn); 
		} 
		else 
		{   
			if($submit!='Final Submit'){
				//occupancy free
				//mysql_query_db("update health_occupancy set status=0 where ipdId='$ipdId'");
				//for report purpose
				//mysql_query_db("update health_financeipd set finalbill='1',postDate='$save_date' where ipdId='$ipdId'");
				$nn="update health_ipdbill set billTime='$billTime',grossAmount='$grossAmount',netAmount='$FinalNetAmount',billDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdId='$ipdId'";
				mysql_query_db("update health_ipdbill set payer='$payer',additionAmount='$additionAmount',additionco='$additionco',billTime='$billTime',co='$co',grossAmount='$grossAmount',discountAmount='$discountAmount',netAmount='$FinalNetAmount',billDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdId='$ipdId'");
				mysql_query_db("update health_discount set co='$co',discountAmount='$discountAmount',discountDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdId='$ipdId'");
				$bill_ID= mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
				$fbill_ID=mysql_fetch_db($bill_ID);
				$billId=$fbill_ID['billId'];
				//query to give bill id to ipdfinalbill
				mysql_query_db("update health_ipdfinalbill set BillId='$billId' where ipdId='$ipdId'");
				$_SESSION['sess_ipd_msg']=2;
				//$logger->warn("Bill Update on ipdId ".$ipdId." by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress." query is ".$nn);
			}
			else{
				//occupancy free
				//mysql_query_db("update health_occupancy set status=0 where ipdId='$ipdId'");
				//for report purpose
				//mysql_query_db("update health_financeipd set finalbill='1' where ipdId='$ipdId'");
				$nn="update health_ipdbill set billTime='$billTime',grossAmount='$grossAmount',netAmount='$FinalNetAmount',lastUpdate='$curDateTime',ipAddress='$ipAddress' where ipdId='$ipdId'";
				mysql_query_db("update health_ipdbill set payer='$payer',additionAmount='$additionAmount',additionco='$additionco',billTime='$billTime',billDate='$save_date',co='$co',grossAmount='$grossAmount',discountAmount='$discountAmount',netAmount='$FinalNetAmount',lastUpdate='$curDateTime',ipAddress='$ipAddress' where ipdId='$ipdId'");
				mysql_query_db("update health_discount set co='$co',discountAmount='$discountAmount',discountDate='$save_date',lastUpdate='$curDateTime',updateBy='".$_SESSION['sess_uid']."',ipAddress='$ipAddress' where ipdId='$ipdId'");
				$bill_ID= mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
				$fbill_ID=mysql_fetch_db($bill_ID);
				$billId=$fbill_ID['billId'];
				//query to give bill id to ipdfinalbill
				mysql_query_db("update health_ipdfinalbill set BillId='$billId' where ipdId='$ipdId'");
				$_SESSION['sess_ipd_msg']=2;
				//$logger->warn("Bill Update on ipdId ".$ipdId." by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress." query is ".$nn);
			}
		} 
		if($submit=='Final Submit'){
			//occupancy free
			//mysql_query_db("update health_occupancy set status=0 where ipdId='$ipdId'");
			//$nn7="update health_ipdbill set billDate='$save_date' where ipdId='$ipdId'";
			//mysql_query_db("update health_ipdbill set billDate='$save_date' where ipdId='$ipdId'");
			//mysql_query_db("update health_ipdbill set updateBy='".$_SESSION['sess_uid']."' where ipdId='$ipdId'");
			mysql_query_db("update health_ipd set ipdStatus='2' where ipdId='$ipdId'"); 
			// so that bill will locked
			//mysql_query_db("update health_financeipd set finalbill='1' where ipdId='$ipdId'");
			$_SESSION['sess_ipd_msg']=5;
			//$logger->warn("Final Bill Update on ipdId ".$ipdId." by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress." query is ".$nn7);
		}
		if($submit=='Gross Bill'){
			header("location:gross-billing-print?billId=".encryptSoft($billId));
		}
		else{
			header("location:billing-print-preview?billId=".encryptSoft($billId));
			//header("location:billing-print.php?billId=".$billId);
		}
	}
	else{
		header("location:billing.php");
	}
?>