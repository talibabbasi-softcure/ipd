<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-Bill Print-");
	/* log4php end here */
	//decode
	$billId=decryptSoft($billId);
	$pay=mysql_query_db("select * from health_ipdbill where billId='$billId'");
	$fpay=mysql_fetch_db($pay);
	$user= mysql_query_db("select * from health_user where userId='".$fpay['updateBy']."'");
	$fuser= mysql_fetch_db($user);
	$ipd= mysql_query_db("select * from health_ipd where ipdId='".$fpay['ipdId']."'");
	$fipd= mysql_fetch_db($ipd);
	$ipdFee= mysql_query_db("select * from health_financeipd where ipdId='".$fipd['ipdId']."'");
	$fipdFee= mysql_fetch_db($ipdFee);
	$sql_payer=mysql_query_db("select tpa from health_tpa where id='".$fipd['payer']."'");
	$payer=mysql_fetch_db($sql_payer);
	$sql_insurance=mysql_query_db("select name from health_insurance where id='".$fipd['insuranceId']."'");
	$insurance=mysql_fetch_db($sql_insurance);	
	$sql=mysql_query_db("select * from health_patient where patientId='".$fipd['patientId']."'");
	$line=mysql_fetch_db($sql);
	$user= mysql_query_db("select * from health_user where userId='".$fpay['updateBy']."'");
	$fuser= mysql_fetch_db($user);
	$disch= mysql_query_db("select * from health_discharge where ipdId='".$fpay['ipdId']."'");
	$fdisch= mysql_fetch_db($disch);
	$bill= mysql_query_db("select * from health_ipdbill where ipdId='".$fpay['ipdId']."'");
	$fbill= mysql_fetch_db($bill);
	$bedNo= mysql_query_db("select * from health_allocation where ipdId='".$fpay['ipdId']."'");
	$fbedNo= mysql_fetch_db($bedNo);
	$sqldoctor=mysql_query_db("select * from health_doctors where id='".$fipd['doctorId']."'");
	$linedoctor=mysql_fetch_db($sqldoctor);
	$deptt= mysql_query_db("select * from health_department where id='".$fipd['depttId']."'");
	$fdeptt= mysql_fetch_db($deptt);
	$roomNo= mysql_query_db("select * from health_allocation where ipdId='".$fipd['ipdId']."'");
	$froomNo= mysql_fetch_db($roomNo);
	$alloc= mysql_query_db("select * from health_roomalias where id='".$froomNo['roomNo']."'");
	$falloc= mysql_fetch_db($alloc);
	$roomCategory= mysql_query_db("select * from health_roomcategory where roomCategoryId='".$froomNo['category']."'");
	$froomCategory= mysql_fetch_db($roomCategory);
	$post_u=getUhidYear($line['patientId']);
	$post_i=getIpdYear($fipd['ipdId']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Detailed Print</title> 
        <link href="../css/style.css" rel="stylesheet" type="text/css"> <!--<?php //echo $_GET['print'] == 1 ? 'print' : 'screen'; ?>.css-->                        <!-- CSS link for design the whole phase-->
        <link href="../awesome/css/font-awesome.css" rel="stylesheet" type="text/css">     <!-- Font awesome link for use font awesome icon-->
        <link href="../awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> <!-- Font awesome link for use font awesome icon-->
		<link href="../css/print-setup-billing.css" rel="stylesheet" type="text/css"> 
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
			margin:0;
			margin-top:30px;
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
				<div class="page-header">
					<div class="full printHeader">                                    
						<div class="full">
							<?php include "../header.php";?>
						</div>							
						<div class="full border_bottom greyback bold">
							<div class="full txt_center bold printgrey18 black_bck">IPD LAB Consolidated Bill</div>
						</div>
						<div class="clearfix10"></div>
					</div>                              
				</div>
				<!----header fixed ends--->
				<table class="full">
					<thead>
						<tr>
							<td>
								<!--place holder for the fixed-position header-->
								<div class="page-header-space"></div>
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<div class="full page">												
									<div class="full">
										<div class="full50">
											<div class="full95">
											<div class="full printgrey14"><strong>IPD No: </strong> <?php echo $pre_i.$fipd['ipdId'].$post_i;?> UHID : </strong><?php echo $pre_u.$line['patientId'].$post_u;?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Patient Name:  </strong> <?php echo ucfirst($line['patientInitial']." ".$line['patientName']);?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Father/Guardian:  </strong> <?php echo ucfirst($line['fatherHusInitial']." ".$line['fatherHusName']);?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Age/Gen/Marital: </strong><?php if(!empty($line['age_y'])){echo $line['age_y']."Y";}?> <?php if(!empty($line['age_m'])){echo $line['age_m']."M";}?> <?php if(!empty($line['age_d'])){echo $line['age_d']."D";}?>/<?php echo ucfirst($line['gender']);?>/<?php echo ucfirst($line['marritalStatus']);?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Address: </strong><?php echo $line['address'];?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Contact No: </strong><?php echo $line['mobileNumber'];?></div>
											<div class="clearfix7"></div>
											<div class="full printblk14"><strong>Billing Category: </strong> <?php if($fipdFee['billingCategory']==1){echo "Cash";} else { echo "Credit";}?></div>
											<div class="clearfix7"></div>
										</div>
									</div>
									<div class="full50r">
										<div class="full95">
											<div class="full printgrey14"><strong>Bill No: </strong> 0<?php echo $fpay['billId'];?>&nbsp;&nbsp;<strong>Date : </strong> <?php echo date('d-M-Y',strtotime($fpay['billDate'])); ?> <?php if($fpay['billTime']!==''){echo date('h:i:s A',strtotime($fpay['billTime']));}?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Insurance Co.: </strong> <?php if($fipd['insuranceId']!=0){echo $insurance['name'];} else { echo "NA";}?> <strong>Panel : </strong> <?php if($fipd['payer']!=0){echo $payer['tpa'];} else { echo "NA";}?></div>
											<div class="clearfix7"></div>
											<div style="display:none">
												<div class="full printblk14"><strong>Rank : </strong> <?php if(!empty($line['custom3'])){echo $line['custom3'];} else { echo "NA";}?></div>
                                                <div class="clearfix7"></div>
											</div>
											<div class="full printblk14"><strong>Department : </strong> <?php echo ucfirst($fdeptt['depttName']);?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Doctor: </strong> <?php echo $linedoctor['doctorName'];?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Allocation : </strong><?php echo $froomCategory['roomCategoryName'];?> <strong>Unit No :</strong> <?php echo $falloc['alias'];?></div>
											<div class="clearfix7"></div>
											<div class="full printgrey14"><strong>Admission Date: </strong> <?php $datetime = new DateTime($fipd['dateTime'] ); echo $datetime->format( 'd-M-Y ' ); echo date("h:i:sA",strtotime($fipd['postTime'])); ?></div>
											<div class="clearfix7"></div>
											<?php if($fdisch['dischargeDate']){?>
                                                <div class="full printgrey14"><strong>Discharge Date: </strong> <?php $datetime = new DateTime($fdisch['dischargeDate'] );  echo $datetime->format( 'd-M-Y' ); echo " "; echo date("h:i:sA",strtotime($fdisch['dischargeTime']));?></div>
											<?php }?>
										</div>
									</div>
								</div>	
								<table class="full" style="border-collapse:collapse">
									<tr class="printgrey14 bold line_hgt10 black_bck">
										<td style="width:5%;" class="border_all txt_center">S.No.</td>
										<td style="width:10%;" class="txt_center border_all">Date</td>
										<td style="width:5%;" class="border_all txt_center border_all">Code</td>
										<td style="width:50%;text-align:left;padding-left:5px" class="border_right txt_center border_all">Particular</td>
										<td style="width:10%;" class="border_all txt_center border_all">Rate(Rs)</td>
										<td style="width:5%;" class="border_all txt_center border_all">Unit</td>
										<td style="width:10%;" class="txt_center border_all">Amount(Rs)</td>
									</tr>
									<?php
										$grossAmount=0;
										$totaldue=0;
										$diagnosis=mysql_query_db("select distinct departmentId from health_patientdiagnosishead where ipdId='".$fpay['ipdId']."' order by departmentId ASC");
										$dnum=mysql_num_db($diagnosis);
										if($dnum!=0){
										?>
										<tr class="printgrey14 line_hgt15 grey_backgroundff">
											<td style="width:80%;font-weight:bold;" colspan="7" class="padd_right20  border_all txt_left">Investigations Charges</td>
										</tr>
										<?php
											while($alldiagnosis=mysql_fetch_db($diagnosis)){
												$diagnosissql= mysql_query_db("select * from health_department_diagnosis where id='".$alldiagnosis['departmentId']."'");	
												$diagnosisline= mysql_fetch_db($diagnosissql);
												$patientIpddiagnosis= mysql_query_db("select * from health_patientdiagnosishead where departmentId='".$alldiagnosis['departmentId']."' and ipdId='".$fpay['ipdId']."' order by ipdHeadDate ASC");	
												$i=1;
												$diag_total=0;
												while($patientIpddiagnosisline= mysql_fetch_db($patientIpddiagnosis))
												{
													$surgeonId=$patientIpddiagnosisline['doctorId'];
													if($surgeonId!=0 && $surgeonId!="")
													{
														$sqlSurgeon=mysql_query_db("select * from health_doctors where id='$surgeonId'");
														$lineSurgeon=mysql_fetch_db($sqlSurgeon);
														$sqldeptt=mysql_query_db("select * from health_department where id='".$lineSurgeon['depttId']."'");
														$linedeptt=mysql_fetch_db($sqldeptt);
													}	
												?>
												<?php if($i==1) {?>
													<tr class="printgrey14 line_hgt15 grey_backgroundff">
                                                        <td style="width:80%;font-weight:bold;" colspan="7" class="padd_right20  border_all txt_left"><?php echo $diagnosisline['depttName'];?></td>
													</tr>
												<?php } ?>
												<tr class="printgrey14 line_hgt15 grey_backgroundff">
													<td style="width:5%;" class="border_right border_all txt_center"><?php echo $i;?></td>
													<td style="width:15%;" class="border_right border_all txt_center"><?php echo date("d-m-Y",strtotime($patientIpddiagnosisline['ipdHeadDate']));?></td>
													<td style="width:10%;" class="border_right border_all txt_center"><?php echo $patientIpddiagnosisline['code'];;?></td>
													<td style="width:35%;text-align:left;padding-left:5px" class="border_right border_all txt_center"><?php echo implode("<br/>", str_split($patientIpddiagnosisline['headName'],35));?><?php if($surgeonId!=0){ echo "(Dr.".$lineSurgeon['doctorName'].")";}?></td>
													<td style="width:5%;text-align:right;padding-right:10px" class="border_right border_all txt_center"><?php echo $patientIpddiagnosisline['ipdHeadRate'];?></td>
													<td style="width:10%;" class="border_right border_all txt_center"><?php echo $patientIpddiagnosisline['quantity'];?></td> 
													<td style="width:20%;text-align:right;" class="txt_center border_all"><?php echo number_format($patientIpddiagnosisline['totalPrice'], 2, '.', ',');?></td>
												</tr>
												<?php $i++;  
													$grossAmount=$grossAmount+$patientIpddiagnosisline['totalPrice'];
													$diag_total=$diag_total+$patientIpddiagnosisline['totalPrice'];
												}?>
												<tr class="printgrey14 line_hgt15 grey_backgroundff">
													<td style="width:80%;" colspan="6" class="padd_right20 border_right border_all txt_right">Total Amount (Rs)</td>
													<td style="width:20%;text-align:right;" class="txt_center border_all"><?php echo number_format($diag_total, 2, '.', ',');?></td>
												</tr>
												<?php
												}
										}// end of if num!=0
									?>
									<!--end code for Diagnosis---->
									<tr class="printgrey14 line_hgt15 black_bck ">
										<td style="width:80%;" colspan="7" class="padd_right20 border_all txt_right">&nbsp;</td>
									</tr>
									<tr class="printgrey14 line_hgt15">
										<td style="width:80%;" colspan="6" class="padd_right20 border_right border_all txt_right">Total LAB Bill Amount (Rs)</br>
											<?php echo num_to_words($grossAmount);?>
										</td>
										<td style="width:20%;text-align:right;" class="txt_center border_all"><?php echo number_format($grossAmount, 2, '.', ',');?></td>
									</tr>
								</table>
								<div class="clearfix20"></div>
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
									<div class="clearfix20"></div>
									<div class="full50 printgrey14"><?php echo $footerMark;?></div>
									<div class="full50r printgrey14 txt_right">(<?php echo $fuser['userName'];?>)</div>
								</div>
								<div class="full">
									<div class="full50 printgrey14 disnone">
										Patient/Attendant Signature
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
	<?php 
		//$logger->warn("Bill print on Bill ID ".$billId." is success by ".$_SESSION['sess_uname']." on date and time is ".$curDateTime." and IP Address is ".$ipAddress);
	?>
	<script>
		function myconfig(){
			window.print();
			window.onafterprint = function(){
				<?php 
					if($page=='final')
					{
					?>
					document.location.href = "final-bill.php";
					<?php
					}
					else if($page=='due')
					{
					?>
					document.location.href = "ipd-due.php";
					<?php	
					}
					else if(isset($line['ipdId'])){?>
					document.location.href = "billing.php?ipdId=<?php echo $line['ipdId'];?>";
					<?php } else { ?>
					document.location.href = "billing.php";
				<?php } ?>	
			}
		}
	</script>
</body>
</html>