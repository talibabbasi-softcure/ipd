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
	$ipdId=decryptSoft($ipdId);
	$ipd= mysql_query_db("select * from health_ipd where ipdId='$ipdId'");
	$fipd= mysql_fetch_db($ipd);
	$ipdFee= mysql_query_db("select * from health_financeipd where ipdId='".$fipd['ipdId']."'");
	$fipdFee= mysql_fetch_db($ipdFee);
	$pay=mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
	$fpay=mysql_fetch_db($pay);
	$sql_payer=mysql_query_db("select tpa from health_tpa where id='".$fipd['payer']."'");
	$payer=mysql_fetch_db($sql_payer);
	$sql=mysql_query_db("select * from health_patient where patientId='".$fipd['patientId']."'");
	$line=mysql_fetch_db($sql);
	$user= mysql_query_db("select * from health_user where userId='".$_SESSION['sess_uid']."'");
	$fuser= mysql_fetch_db($user);
	$bedNo= mysql_query_db("select * from health_allocation where ipdId='$ipdId'");
	$fbedNo= mysql_fetch_db($bedNo);
	$sqldoctor=mysql_query_db("select * from health_doctors where id='".$fipd['doctorId']."'");
	$linedoctor=mysql_fetch_db($sqldoctor);
	$sqldeptt=mysql_query_db("select * from health_department where id='".$fipd['depttId']."'");
	$linedeptt=mysql_fetch_db($sqldeptt);
	$roomNo= mysql_query_db("select * from health_allocation where ipdId='".$fipd['ipdId']."'");
	$froomNo= mysql_fetch_db($roomNo);
	$alloc= mysql_query_db("select * from health_roomalias where id='".$froomNo['roomNo']."'");
	$falloc= mysql_fetch_db($alloc);
	$roomCategory= mysql_query_db("select * from health_roomcategory where roomCategoryId='".$froomNo['category']."'");
	$froomCategory= mysql_fetch_db($roomCategory);
	$numAdd=0;
	$addDoctor=mysql_query_db("select * from health_ipd_additional_doctor where ipdId='".$fipd['ipdId']."' order by id desc limit 0,1");
	$faddDoctor=mysql_fetch_db($addDoctor); 
	$numAdd=mysql_num_db($addDoctor);
	if($numAdd!=0){
		$doctorName2=mysql_query_db("select * from health_doctors where id='".$faddDoctor['doctorId']."'");
		$fdoctorName2=mysql_fetch_db($doctorName2);
		$deptt2= mysql_query_db("select * from health_department where id='".$fdoctorName2['depttId']."'");
		$fdeptt2= mysql_fetch_db($deptt2);
	}
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
        <title><?php echo "&nbsp";?></title> <!-- Title-->
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
			<!--cash patient-->
			<!--For TPA-->
			<div class="mauto90">
				<div class="page-header">
					<div class="full printHeader">
						<div class="full">
							<?php include "../header.php";?>
						</div>
						<div class="full border_bottom greyback bold">
							<div class="full txt_center bold printgrey16"><?php if($fipd['dayCare']==1){ echo "Day Care Estimate";} else{echo "IPD Estimate";}?> </div>
						</div>
						<div class="clearfix10"></div>
					</div>
				</div>
				<!--print table---> 
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
                                                <div class="full printgrey12">
													<?php $path="barcode/images/".$fipd['ipdId'].".png";?>
												<img src="<?php echo $path;?>"></div>
												<div class="clearfix3"></div>
                                                <div class="full printgrey14"><strong>IPD No : </strong> <?php echo $pre_i.$fipd['ipdId'].$post_i;?> UHID: <?php echo $pre_u.$fipd['patientId'].$post_u;?></div>
                                                <div class="clearfix7"></div>
                                                <div class="full printgrey14"><strong>Patient Name :  </strong> <?php echo ucfirst($line['patientInitial']." ".$line['patientName']);?></div>
                                                <div class="clearfix7"></div>
                                                <div class="full printgrey14"><strong>Father/Guardian :  </strong> <?php echo ucfirst($line['fatherHusInitial']." ".$line['fatherHusName']);?></div>
                                                <div class="clearfix7"></div>
                                                <div class="full printgrey14"><strong>Age/Gen/Marital : </strong> <?php if(!empty($line['age_y'])){echo $line['age_y']."Y";}?> <?php if(!empty($line['age_m'])){echo $line['age_m']."M";}?> <?php if(!empty($line['age_d'])){echo $line['age_d']."D";}?>/<?php echo ucfirst($line['gender']);?>/<?php echo ucfirst($line['marritalStatus']);?></div>
                                                <div class="clearfix7"></div>
											    <div class="full printgrey14"><strong>Contact No: </strong><?php echo $line['mobileNumber'];?></div>
                                                <div class="clearfix7"></div>
											    <div class="full printgrey12"><strong>Address : </strong> <?php echo ucfirst($line['address']);?>
													-<?php echo strtoupper($fcity['name']);?> <?php echo strtoupper($fstate['name']);?> <?php //echo strtoupper($fcntry['name']);?>
												</div> 
												<div class="clearfix7"></div>
												<div class="full printgrey14"><strong>Billing Category:</strong> <?php if($fipdFee['billingCategory']==1){echo "Cash";} else { echo "Credit";}?></div>
                                                <div class="clearfix7"></div>
												<?php if(!empty($fipd['treatment'])){?>
													<div class="full printblk14 greyback"><strong>Procedure / Treatment : </strong> <?php echo $fipd['treatment'];?></div>
													<div class="clearfix7"></div>
												<?php }?>
												<?php if(!empty($fipd['provDiagnosis'])){?>
													<div class="full printblk14 greyback"><strong>Provisional Diagnosis : </strong> <?php echo $fipd['provDiagnosis'];?></div>
													<div class="clearfix7"></div>
												<?php }?>
											</div>
										</div>
										<div class="full50r">
											<div class="full95">
												<div class="full printgrey14"><strong>Print Date :</strong> <?php echo date('d-M-Y',strtotime($today));?></div>
                                                <div class="clearfix7"></div>
												<div class="full printgrey14"><strong>Insurance Co.: </strong> <?php if($fipd['insuranceId']!=0){echo $insurance['name'];} else { echo "NA";}?> <strong>Panel : </strong> <?php if($fipd['payer']!=0){echo $payer['tpa'];} else { echo "NA";}?></div>
                                                <div class="clearfix7"></div>
												<div class="full printgrey14"><strong>TPA/Panel : </strong> <?php if($fipd['payer']!=0){echo $payer['tpa'];} else { echo "NA";}?></div>
                                                <div class="clearfix7"></div>
												<div class="full printgrey14"><strong>AL/CCN No.: </strong> <?php if(!empty($fipd['claimNo'])){echo strtoupper($fipd['claimNo']);} else { echo "NA";}?>  <strong>Policy/Service No. : </strong> <?php if(!empty($line['custom2'])){echo strtoupper($line['custom2']);} else { echo "NA";}?></div>
                                                <div class="clearfix7"></div>
												<div style="display:none">
													<div class="full printblk14"><strong>Rank : </strong> <?php if(!empty($line['custom3'])){echo $line['custom3'];} else { echo "NA";}?></div>
													<div class="clearfix7"></div>
												</div>
												<div class="full printgrey14"><strong>Department : </strong> <?php echo $linedeptt['depttName'];?></div>
                                                <div class="clearfix7"></div>
												<div class="full printgrey14"><strong>Doctor : </strong> <?php echo $linedoctor['doctorName'];?></div>
                                                <div class="clearfix7"></div>
												<?php 
													if($numAdd!=0){ ?>
													<div class="full printgrey14"><strong>Additional Consultant: </strong> Dr. <?php echo $fdoctorName2['doctorName'];?> - <?php echo ucfirst($fdeptt2['depttName']);?></div>
													<div class="clearfix7"></div>
												<?php } ?>
												<div class="full printgrey14"><strong>Allocation : </strong><?php echo $froomCategory['roomCategoryName'];?> <strong>Unit No :</strong> <?php echo $falloc['alias'];?></div>
												<div class="clearfix7"></div>
                                                <div class="full printgrey14"><strong>Admission Date: </strong> <?php $datetime = new DateTime($fipd['dateTime'] ); echo $datetime->format( 'd-M-Y ' );  echo date("h:i:sA",strtotime($fipd['postTime'])); ?></div>
                                                <div class="clearfix7"></div>
											</div>
										</div>
									</div>
									<table class="full" style="border-collapse:collapse">
										<tr class="printgrey14 bold line_hgt10 greyback">
											<td style="width:5%;" class="border_all txt_center">S.No.</td>
											<td style="width:5%;" class="border_all txt_center">Code</td>
											<td style="width:45%;text-align:left;padding-left:5px" class="border_all txt_center">Particular</td>
											<td style="width:10%;" class="border_all txt_center ">Rate (Rs)</td>
											<td style="width:5%;" class="border_all txt_center ">Unit</td>
											<td style="width:15%;" class="border_all txt_center ">Amount (Rs)</td>
										</tr>
										<?php
											$i=1;
											$totaldue=0;
											$grossAmount1=0;
											//for package heading
											$chck=mysql_query_db("select * from health_ipdfinalbill where ipdId='$ipdId' and package=1 and diagnosisId=0 order by priority ASC");
											$nchck= mysql_num_db($chck);
											if($nchck!=0){
												$g=1;
												while($fchck=mysql_fetch_db($chck)){
												?>
												<tr class="printgrey14 line_hgt10 greyback">
													<td style="width:5%;" class="border_all txt_center">PACK.<?php echo $g;?></td>
													<td style="width:10%;" class="border_all txt_center"><?php echo $fchck['id'];?></td>
													<td style="width:45%;text-align:left;padding-left:5px" class="border_all txt_center"><?php echo $fchck['code'];?> <?php echo strtoupper(implode("<br/>", str_split($fchck['headName'],40)));?>  <?php if(isset($depttName)){ echo "(". $depttName. ")";}?> <?php if($surgeonId!=0){ echo "(Dr.".$lineSurgeon['doctorName']."-".$linedeptt['depttName'].")";}?></td>
													<td style="width:5%;text-align:right;padding-right:10px" class="border_all txt_center"><?php echo $fchck['price'];?></td>
													<td style="width:5%;" class="border_all txt_center"><?php echo $fchck['quantity'];?></td> 
													<td style="width:15%;text-align:right;" class="txt_center border_all"><?php echo number_format($fchck['totalPrice'], 2, '.', ',');?></td>
												</tr>
												<?php
													$g++;
													$grossAmount1=$grossAmount1+$fchck['totalPrice'];
												}
											?>
											<tr class="grey16 line_hgt25 bold">
												<td colspan="5" class="border_right border_bottom txt_right">Total</td> 
												<td style="width:15%;" class="border_right txt_right border_bottom"><?php echo number_format($grossAmount1, 2, '.', ',');?></td>
											</tr>
											<tr class="printgrey14 line_hgt25   bold">
												<td style="width:100%;" colspan="6" class="padd_right20 border_right border_all txt_left">Package Details</td>
											</tr>
											<?php
											}
											//end of package heading			
											$grossAmount=0;
											$priority=mysql_query_db("select distinct priority from health_ipdfinalbill where ipdId='$ipdId' and package=0 order by priority ASC");
											while($allPriority=mysql_fetch_db($priority)){
												$prioritysql= mysql_query_db("select * from health_priority where priority='".$allPriority['priority']."'");	
												$priorityline= mysql_fetch_db($prioritysql);
												$j=1;
												$chck=mysql_query_db("select * from health_ipdfinalbill where ipdId='$ipdId' and diagnosisId=0 and priority='".$allPriority['priority']."'");
												while($fchck= mysql_fetch_db($chck)){
													//FOR SURGEON Name
													$surgeonId=$fchck['doctorId'];
													if($surgeonId!=0 && $surgeonId!="")
													{
														$sqlSurgeon=mysql_query_db("select * from health_doctors where id='$surgeonId'");
														$lineSurgeon=mysql_fetch_db($sqlSurgeon);
														$sqldeptt=mysql_query_db("select * from health_department where id='".$lineSurgeon['depttId']."'");
														$linedeptt=mysql_fetch_db($sqldeptt);
													}
													//to find deppt name of dr if it is dr
													$dv=mysql_query_db("select * from health_ipdfinalbill where code like 'DV%'	and id='".$fchck['id']."'");		
													$num_dv=mysql_num_db($dv);	
													if($num_dv!=0){
														$ipdHeadId=$fchck['ipdHeadId'];
														//echo $ipdHeadId;
														$custipdHeadId=",".$ipdHeadId.",";
														//echo $custipdHeadId;
														//echo "select * from health_doctors where ipdVisit like '%$custipdHeadId%'"; echo "<br>";
														$drId=mysql_query_db("select * from health_doctors where ipdVisit like '%$custipdHeadId%'");
														$fdrId=mysql_fetch_db($drId);
														$depttId=$fdrId['depttId'];
														$deptt= mysql_query_db("select * from health_department where id='$depttId'");
                                                        $fdeptt= mysql_fetch_db($deptt);
														$depttName=$fdeptt['depttName'];
													}
													else{
														if(isset($depttName)){
															unset($depttName);
														}
													}
												?>
												<?php if($j==1) {?>
													<tr class="printgrey14 line_hgt15">
                                                        <td style="width:80%;font-weight:bold;padding-left:10px" colspan="6" class="padd_right20  border_all txt_left"><?php echo $priorityline['head'];?></td>
													</tr>
												<?php } ?>
												<tr class="printgrey14 line_hgt10">
													<td style="width:5%;" class="border_all txt_center"><?php echo $i;?></td>
													<td style="width:10%;" class="border_all txt_center"><?php echo $fchck['code'];?></td>
													<td style="width:45%;text-align:left;padding-left:5px" class="border_all txt_center"><?php echo strtoupper(implode("<br/>", str_split($fchck['headName'],40)));?>  <?php if(isset($depttName)){ echo "(". $depttName. ")";}?> <?php if($surgeonId!=0){ echo "(Dr.".$lineSurgeon['doctorName']."-".$linedeptt['depttName'].")";}?></td>
													<td style="width:5%;text-align:right;padding-right:10px" class="border_all txt_center"><?php echo $fchck['price'];?></td>
													<td style="width:5%;" class="border_all txt_center"><?php echo $fchck['quantity'];?></td> 
													<td style="width:15%;text-align:right;" class="txt_center border_all"><?php echo number_format($fchck['totalPrice'], 2, '.', ',');?></td>
												</tr>
												<?php $i++; $j++; $grossAmount=$grossAmount+$fchck['totalPrice'];
												}
											}?>
											<!---investigation starts--->
											<?php
												$diagnochck=mysql_query_db("select * from health_patientdiagnosishead where ipdId='$ipdId' order by ipdHeadDate ASC");
												$dnum=mysql_num_db($diagnochck);
												if($dnum!=0){
												?>
												<tr class="printgrey14 line_hgt15">
													<td style="width:80%;font-weight:bold;" colspan="6" class="padd_right20  border_bottom border_all txt_left">Investigations Charges</td>
												</tr>
												<?php
													while($fdiagnochck= mysql_fetch_db($diagnochck)){
														$surgeonId=$fdiagnochck['doctorId'];
														if($surgeonId!=0 && $surgeonId!="")
														{
															$sqlSurgeon=mysql_query_db("select * from health_doctors where id='$surgeonId'");
															$lineSurgeon=mysql_fetch_db($sqlSurgeon);
															$sqldeptt=mysql_query_db("select * from health_department where id='".$lineSurgeon['depttId']."'");
															$linedeptt=mysql_fetch_db($sqldeptt);
														}	
													?>
                                                    <tr class="printgrey14 line_hgt10">
														<td style="width:5%;" class="border_all txt_center"><?php echo $i;?></td>
														<td style="width:10%;" class="border_all txt_center"><?php echo $fdiagnochck['code'];?></td>
														<td style="width:40%;text-align:left;padding-left:5px;word-wrap:break-word; white-space: nowrap;" class="border_all txt_center"><?php echo implode("<br/>", str_split($fdiagnochck['headName'],35));?> <?php if($surgeonId!=0){ echo "(Dr.".$lineSurgeon['doctorName'].")";}?></td>
														<td style="width:5%;text-align:right;padding-right:10px" class="border_all txt_center"><?php echo $fdiagnochck['ipdHeadRate'];?></td>
														<td style="width:5%;" class="border_all txt_center"><?php echo $fdiagnochck['quantity'];?></td> 
														<td style="width:20%;text-align:right;" class="txt_center border_all"><?php echo number_format($fdiagnochck['totalPrice'], 2, '.', ',');?></td>
													</tr>
                                                    <?php $i++;  $grossAmount=$grossAmount+$fdiagnochck['totalPrice'];
													}
												}
											?>
											<!--Pharma-->
											<?php
												$pharmaBill=mysql_query_db("select * from health_ipd_pharmacy_bill where ipdId='$ipdId' order by date");
												$numpharmaBill= mysql_num_db($pharmaBill);
												if($numpharmaBill!=0){
												?>
												<tr class="printgrey14 line_hgt15">
													<td style="width:80%;font-weight:bold;padding-left:10px" colspan="6" class="padd_right20  border_all txt_left">Medicines Details</td>
												</tr>
												<?php
													while($fpharmaBill=mysql_fetch_db($pharmaBill)) {
														$pharmaType= mysql_query_db("select * from health_ipd_pharmacy_type where id='".$fpharmaBill['pharmaType']."'");
														$fpharmaType= mysql_fetch_db($pharmaType);	
													?>
													<tr class="printgrey14 line_hgt15">
                                                        <td style="width:80%;font-weight:bold;padding-left:10px" colspan="6" class="padd_right20  border_all txt_left"><?php echo implode("<br/>", str_split($fpharmaType['name'],40));?> </td>
													</tr>
													<?php
														$pharmacyList= mysql_query_db("select * from health_ipd_pharmacy where billId='".$fpharmaBill['id']."'");
														while($fpharmacyList=mysql_fetch_db($pharmacyList)){
														?>
														<tr class="printgrey14 line_hgt25 white_bck">
															<td style="width:5%;" class="border_all  txt_center"><?php echo $i;?></td>
															<td style="width:15%;" class="border_all  txt_center">IP: <?php echo $fpharmacyList['id'];?></td>
															<td style="width:25%;" class="border_all  txt_left">
																<?php echo strtoupper(implode("<br/>", str_split($fpharmacyList['medicine'],40)));?>
															</td>
															<td style="width:5%;padding-right:10px" class="border_all txt_right"> <?php echo number_format($fpharmacyList['rate'], 2, '.', ',');?></td> 
															<td style="width:5%;" class="border_all txt_center"><?php echo $fpharmacyList['quantity'];?></td> 
															<td style="width:5%;" class="border_all txt_right"><?php echo number_format($fpharmacyList['mrp'], 2, '.', ',');?></td> 
														</tr>
														<?php
															$i++;
															$grossAmount=$grossAmount+$fpharmacyList['mrp'];
														}
													} 
												}
											?>
											<!--pharma ends-->
											<tr class="printgrey14 line_hgt10 greyback">
												<td style="width:90%;" colspan="5" class="padd_right20 border_right border_all txt_right">Gross Amount (Rs)<br>
												</td>
												<td style="width:10%;text-align:right" class="txt_center border_all"><?php echo number_format(($grossAmount), 2, '.', ',');?></td>
											</tr>
											<?php if($fpay['additionAmount']!=0 && $fpay['additionAmount']!=''){?>
												<tr class="printgrey14 line_hgt10">
													<td style="width:90%;" colspan="5" class="padd_right20 border_right border_all txt_right">
														<?php echo strtoupper($fpay['additionco']);?>
														Addition (Rs)
													</td>
													<td style="width:10%;text-align:right" class="txt_center border_all"><?php echo number_format(($fpay['additionAmount']), 2, '.', ',');?></td>
												</tr>
												<?php 
													$grossAmount=$grossAmount+$fpay['additionAmount'];
												}
											?>
											<?php 
												if($num_settl!=0)
												{?>
												<tr class="printgrey14 line_hgt15">
													<td style="width:90%;" colspan="5" class="padd_right20 border_right border_all txt_right">Deduction on Settlement (Rs)<br>
													</td>
													<td style="width:10%;text-align:right" class="txt_center border_all"><?php echo number_format($totalDeduction, 2, '.', ',');?></td>
												</tr>
												<?php
												}
												if($fpay['discountAmount']!=0){?>
												<tr class="printgrey14 line_hgt15">
													<td style="width:90%;" colspan="5" class="padd_right20 border_right border_all txt_right"> <?php echo $fpay['co'];?> Discount (Rs) </td>
													<td style="width:10%;text-align:right" class="txt_center border_all"><?php echo number_format($fpay['discountAmount'], 2, '.', ',');?></td>
												</tr>
												<?php 
												}
												$netAmount=$grossAmount-$fpay['discountAmount']-$totalDeduction;
											?>
											<tr class="printgrey14 line_hgt15 greyback disnon">
												<td style="width:90%;" colspan="5" class="padd_right20 border_right border_all txt_right">Total Bill Amount (Rs)<br>
													<?php echo num_to_words($netAmount);?>
												</td>
												<td style="width:10%;text-align:right" class="txt_center border_all"><?php echo number_format(($netAmount), 2, '.', ',');?></td>
											</tr>
											<?php
												$depositAmount=0;
												$payment= mysql_query_db("select * from health_payment where ipdId='$ipdId'");
												while($fpayment=mysql_fetch_db($payment)){
													$depositAmount=$depositAmount+$fpayment['creditAmount'];
												}
											?>
											<tr class="printgrey14 line_hgt10">
												<td style="width:90%;" colspan="5" class="padd_right20 border_right border_all txt_right">Amount Paid  (Rs)</td>
												<td style="width:10%;text-align:right" class="txt_center border_all"><?php if($depositAmount){echo number_format($depositAmount, 2, '.', ',');} else { echo "0.00";}?></td>
											</tr>
											<?php 
												$totaldue=$grossAmount-$depositAmount;
												$netAmount=$totaldue-$fpay['discountAmount']-$totalDeduction;
											?>
											<tr class="printgrey14 line_hgt15 bold greyback">
												<td style="width:90%;" colspan="5" class="padd_right20 border_all txt_right">Balance Payable Amount (Rs)<br>
													<?php echo num_to_words($netAmount);?>
												</td>
												<td style="width:10%;text-align:right" class="txt_center border_all"><?php echo number_format(round($netAmount), 2, '.', ',');?></td>
											</tr>
									</table>
									<div class="clearfix20"></div>
									<span class="printgrey14 bold line_hgt10">-:Amount Deposited:-</span>
									<table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
										<tr class="printgrey14 bold line_hgt10 greyback">
											<td style="width:10%;" class="border_all txt_center ">S.No.</td>
											<td style="width:20%;" class="border_all txt_center ">Date</td>
											<td style="width:15%;" class="border_all txt_center ">Receipt No.</td>
											<td style="width:15%;" class="border_all txt_center ">Details</td>
											<td style="width:25%;" class="border_all txt_center ">Mode</td>
											<td style="width:25%;" class="border_all txt_center ">Amount (Rs)</td>
										</tr>
										<?php
											$r=1;
											$recCount=1;
											$paymentDeposit= mysql_query_db("select * from health_payment where ipdId='".$fipd['ipdId']."'");
											while($fpaymentDeposit=mysql_fetch_db($paymentDeposit)){
												$pDate=$fpaymentDeposit['paymentDate'];
												$rNo=$fpaymentDeposit['receiptNo'];
												$amount=$fpaymentDeposit['creditAmount'];
												$mode=$fpaymentDeposit['paymentMode'];
												if($amount!=0){
													if($pDate=='0000-00-00'){
														$advDeposit= mysql_query_db("select * from health_ipd_advance where ipdId='".$fipd['ipdId']."'");
														$advDeposit1=mysql_fetch_db($advDeposit);
														$advDeposit2= mysql_query_db("select * from health_payment_advance where ipdIdAdvance='".$advDeposit1['ipdIdAdvance']."'");
														while($fadvDeposit=mysql_fetch_db($advDeposit2)){
															$pDate=$fadvDeposit['paymentDate'];
															$rNo='Adv/'.$fadvDeposit['receiptNo'];
															$amount=$fadvDeposit['creditAmount'];
															$mode=$fadvDeposit['paymentMode'];
														?>
														<tr class="printgrey14 line_hgt10">
															<td style="width:10%;" class="border_all txt_center "><?php echo $r;?></td>
															<td style="width:20%;" class="border_all txt_center "><?php echo date("d-M-Y",strtotime($pDate));?></td>
															<?php if($tpaReceipts!=1){?>
																<td style="width:15%;" class="border_all txt_center "><?php echo $rNo;?></td>
																<?php } else{?>
																<td style="width:15%;" class="border_all txt_center "><?php echo $pre_i.$fipd['ipdId'];?>/<?php  echo $recCount;?></td> 	 
															<?php }?>
															<td style="width:15%;" class="border_all txt_center ">Advance Payment</td>
															<td style="width:25%;" class="border_all txt_center "><?php echo $mode;?> <?php if($fadvDeposit['paymentMode']=='Online'){echo "-".$fadvDeposit['particular'];}?></td>
															<td style="width:25%;" class="txt_right border_all"><?php echo number_format($amount, 2, '.', ',');?></td>
														</tr>
													<?php $r++; } }
													else{
													?>	
													<tr class="printgrey14 line_hgt10">
														<td style="width:10%;" class="border_all txt_center "><?php echo $r;?>.</td>
														<td style="width:20%;" class="border_all txt_center "><?php echo date("d-M-Y",strtotime($pDate));?></td>
														<?php if($tpaReceipts!=1){?>
															<td style="width:15%;" class="border_all txt_center "><?php echo $rNo;?></td>
															<?php } else{?>
															<td style="width:15%;" class="border_all txt_center "><?php echo $pre_i.$fipd['ipdId'];?>/<?php  echo $recCount;?></td> 	 
														<?php }?>
														<td style="width:15%;" class="border_all txt_center "><?php echo strtoupper($fpaymentDeposit['details']);?></td>
														<td style="width:25%;" class="border_all txt_center "><?php echo $mode;?> <?php if($fpaymentDeposit['paymentMode']=='Online'){echo "-".$fpaymentDeposit['particular'];}?></td>
														<td style="width:25%;" class="txt_right border_all"><?php echo number_format($amount, 2, '.', ',');?></td>
													</tr>	
													<?php
														$r++;	
													}
												}
												$recCount++;
											} ?>
											<tr class="printgrey14 line_hgt10">
												<td style="width:25%;" colspan="5" class="border_all txt_right">Total Amount Paid 
													<?php echo num_to_words($depositAmount);?>
												</td>
												<td style="width:25%;" class="border_all txt_right "><?php echo number_format($depositAmount, 2, '.', ',');?></td>
											</tr>
									</table>
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
										<div class="clearfix20"></div>
										<div class="full50 printgrey14"><?php _generateBill();?></div>
										<div class="full50r printgrey14 txt_right">(<?php echo $fuser['userName'];?>)</div>
									</div>
									<div class="full">
										<div class="full50 printgrey14">
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
		<script>
			function myconfig(){
				window.print();
				window.onafterprint = function(){
			        document.location.href = "billing?ipdId=<?php echo encryptSoft($ipdId);?>";
				}
			}
		</script>
	</body>
</html>