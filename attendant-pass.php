<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	//require_once("../otp_call_ipd.php");
	//require_once("../barcode.php");
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-Consent Print-");
	$today=date("Y-m-d");
	/* log4php end here */
	//decode
	$ipdId=decryptSoft($ipdId);
	$ipd= mysql_query_db("select * from health_ipd where ipdId='$ipdId'");
	$fipd= mysql_fetch_db($ipd);
	$pay= mysql_query_db("select * from health_payment where ipdId='$ipdId' and paymentDate='$today' order by receiptNo ASC LIMIT 0, 1");
	$paynum=mysql_num_db($pay);
	$sql=mysql_query_db("select * from health_patient where patientId='".$fipd['patientId']."'");
	$line=mysql_fetch_db($sql);
	$sqlguardian=mysql_query_db("select * from health_guardian_details where patientId='".$fipd['patientId']."'");
	$lineguardian=mysql_fetch_db($sqlguardian);
	$doctor= mysql_query_db("select * from health_doctors where id='".$fipd['doctorId']."'");
	$fdoctor= mysql_fetch_db($doctor);
	$staff= mysql_query_db("select * from health_reffered_doctor where reffId='".$fipd['reffDoctorId']."'");
	$fstaff= mysql_fetch_db($staff);
	$deptt= mysql_query_db("select * from health_department where id='".$fipd['depttId']."'");
	$fdeptt= mysql_fetch_db($deptt);
	$user= mysql_query_db("select * from health_user where userId='".$fipd['userId']."'");
	$fuser= mysql_fetch_db($user);
	$ipdFee= mysql_query_db("select * from health_financeipd where ipdId='".$fipd['ipdId']."'");
	$fipdFee= mysql_fetch_db($ipdFee);
	$roomNo= mysql_query_db("select * from health_allocation where ipdId='".$fipd['ipdId']."'");
	$froomNo= mysql_fetch_db($roomNo);
	$alloc= mysql_query_db("select * from health_roomalias where id='".$froomNo['roomNo']."'");
	$falloc= mysql_fetch_db($alloc);
	$roomCategory= mysql_query_db("select * from health_roomcategory where roomCategoryId='".$froomNo['category']."'");
	$froomCategory= mysql_fetch_db($roomCategory);
	$sql_payer=mysql_query_db("select tpa from health_tpa where id='".$fipd['payer']."'");
	$payer=mysql_fetch_db($sql_payer);
	$sql_insurance=mysql_query_db("select name from health_insurance where id='".$fipd['insuranceId']."'");
	$insurance=mysql_fetch_db($sql_insurance);	
	$sqlguardian=mysql_query_db("select * from health_guardian_details where patientId='".$fipd['patientId']."'");
	$lineguardian=mysql_fetch_db($sqlguardian);	
	$cntry=mysql_query_db("select * from health_countries where id='".$line['patientNationality']."'");
	$fcntry=mysql_fetch_db($cntry);		
	$state= mysql_query_db("select * from health_states where id='".$line['stateId']."'");
	$fstate= mysql_fetch_db($state);
	$city= mysql_query_db("select * from health_cities where id='".$line['cityId']."'");
	$fcity= mysql_fetch_db($city);
	$cntry=mysql_query_db("select * from health_countries where id='".$line['patientNationality']."'");
	$fcntry=mysql_fetch_db($cntry);	
	$post_u=getUhidYear($line['patientId']);	 
	$post_i=getIpdYear($fipd['ipdId']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title><?php echo ucfirst($line['patientInitial']." ".$line['patientName']);?></title> <!-- Title-->
        <link href="../css/style.css" rel="stylesheet" type="text/css"> 
        <link href="../css/print-setup-billing.css" rel="stylesheet" type="text/css"> 
		<link href="../awesome/css/font-awesome.css" rel="stylesheet" type="text/css">     <!-- Font awesome link for use font awesome icon-->
        <link href="../awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> <!-- Font awesome link for use font awesome icon-->
		<script src="../ajax.js"></script>
        <!-- validation css and js start here -->
        <link rel="stylesheet" href="../validation/css/vstyle.css">
        <!-- validation css and js end -->
        <script>
			function _isNumberKey(evt){
                var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57))
				return false;
				return true;
			}
		</script>
		<style media="print">
			@page {
			size: auto;
			margin-top:20px;
			}
			body{
			-webkit-print-color-adjust: exact !important;
			}
		</style>
		<style>
			table { page-break-inside:auto; }
			tr    { page-break-inside:avoid; page-break-after:auto }
			thead { display:table-header-group }
			tfoot { display:table-footer-group }
		</style>
	</head>
    <body onload="myconfig()">
        <div class="full">			
			<div class="mauto90">
				<div class="page-header1">
					<div class="full">
						<?php include "../header.php";?>
					</div>
					<div class="full border_bottom"></div>
					<div class="full txt_center bold printgrey22 greyback"> <img src="../images/pass.png" height="30px"> Attendant Pass <img src="../images/pass.png" height="30px"></div>
					<div class="full border_bottom"></div>
					<div class="clearfix10"></div>
					<div class="full50">
						<div class="full95">
							<div class="full printgrey14"><strong>Patient Name:  </strong> <?php echo ucfirst($line['patientInitial']." ".$line['patientName']);?></div>
							<div class="clearfix7"></div>
							<div class="full printgrey14"><strong>Contact No: </strong><?php echo $line['mobileNumber'];?></div>
							<div class="clearfix7"></div>
							<div class="full printgrey14"><strong>Address: </strong><?php echo $line['address'];?></div>
							<div class="clearfix7"></div>
						</div>
					</div>
					<div class="full50r">
						<div class="full95">
							<div class="full printgrey14"><strong>IPD No: <?php echo $pre_i.$fipd['ipdId'].$post_i;?> UHID : <?php echo $pre_u.$line['patientId'].$post_u;?></strong> </div>
							<div class="clearfix7"></div>
						<div class="full printgrey14"><strong>Allocation : </strong><?php echo $froomCategory['roomCategoryName'];?> <strong>Unit No :</strong> </strong><?php echo $falloc['alias'];?></div>
						<div class="clearfix7"></div>
						<div class="full printgrey14"><strong>Admission Date: </strong> <?php $datetime = new DateTime($fipd['dateTime'] ); echo $datetime->format( 'd-M-Y ' ); echo date("h:i:sA",strtotime($fipd['postTime'])); ?></div>
						<div class="clearfix7"></div>
					</div>
				</div>
				<div class="full border_bottom"></div>
				<div class="clearfix10"></div>
				<div class="full95">
					<div class="full printgrey14"><strong>Attendant Name:  </strong> ..........................................................  </div>
					<div class="clearfix20"></div>
					<div class="full printgrey14"><strong>Contact No: </strong>................................................................ </div>
					<div class="clearfix20"></div>
					<div class="full printgrey14"><strong>Relation: </strong>..................................................................... </div>
					<div class="clearfix20"></div>
				</div>
				<div class="full printgrey14">
					Valid for only ...... day(s) only. Please submit this slip to the security/guard/gate incharge. 
					/ 
					यह पास केवल ............ दिन के लिए वैध है | कृपया इस पर्ची को सुरक्षा/गार्ड/गेट इंचार्ज को जमा करें।
				</div>
				<div class="clearfix10"></div> 
			</div>
			<table class="full">
				<thead>
					<tr>
						<td>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<div class="full page">
								<div class="clearfix30"></div>
								<!---end of td for print table-->
							</div>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td>
							<!--place holder for the fixed-position footer-->
							<div class="page-footer-space">
								<div class="full">
									<div class="full50 printgrey14">
										Attendant Signature
									</div>
									<div class="full50r printgrey14">
										<span style="float:right">Authorised Signatory</span>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<script>
		function myconfig(){
			window.print();
			window.onafterprint = function(){
				document.location.href = "report.php?reportBy=1";
			}
		}
	</script>
</body>
</html>