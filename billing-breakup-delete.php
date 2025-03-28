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
	$did=decryptSoft($did);
	//code for edit/delete permissions
	$userAccess= mysql_query_db("select * from health_user where userId='".$_SESSION['sess_uid']."'");
	$fuserAccess= mysql_fetch_db($userAccess);
	$role=$fuserAccess['role'];
	//end of code for edit/delete permissions
	if($role=='0'){
		header("location:billing.php?ipdId=".$ipdId);
	}
	// for delete
	if($did){
		$findipdsql= mysql_query_db("select * from health_patientipdhead where patientIpdHeadId='$did'");	
		$findipdline= mysql_fetch_db($findipdsql);
		$ipdHeadId=$findipdline['ipdHeadId'];
		$ipdId2=$findipdline['ipdId'];
		$quantity=$findipdline['quantity'];
		$rate=$findipdline['ipdHeadRate'];
		$headName=$findipdline['headName'];
		$uhid=$findipdline['patientId'];
		$item=$findipdline['headName']." - Rs ".$findipdline['totalPrice'];
		$findIpdfinalbill= mysql_query_db("select * from health_ipdfinalbill where ipdHeadId='$ipdHeadId' and ipdId='$ipdId2' and price='$rate'");	
		$findIpdlinebill= mysql_fetch_db($findIpdfinalbill);
		$billQuantity=$findIpdlinebill['quantity'];
		if($billQuantity==$quantity){
			//echo "same"; die;
			mysql_query_db("delete from health_ipdfinalbill where id='".$findIpdlinebill['id']."'");
			mysql_query_db("delete from health_patientipdhead where patientIpdHeadId='$did'");
		}
		else{
			//code to reduce quantity only 	
			$newQuantity=($billQuantity-$quantity);
			$billPrice=$findIpdlinebill['price'];
			$totalPrice=($billPrice*$newQuantity);
			mysql_query_db("update health_ipdfinalbill set quantity='$newQuantity',totalPrice='$totalPrice' where id='".$findIpdlinebill['id']."'");
			mysql_query_db("delete from health_patientipdhead where patientIpdHeadId='$did'");
		}	
		$nnDelete="delete from health_patientipdhead where ipdHeadId='".$findipdline['ipdHeadId']."' and ipdId='$ipdId' and ipdHeadRate='$rate'";
		//$logger->info("delete from patientipdhead where ipdId='$ipdId' and service='$headName' is successful by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query is ".$nnDelete);	
		$savedate=date('Y-m-d');
		//echo "insert into health_logs set action='Delete in IPD Bill IPD ID: $ipdId', date='', user='".$_SESSION['sess_uname']."', ipaddress='$ipAddress', lastupdate='$curDateTime'";
		mysql_query_db("insert into health_logs set patientId='$uhid',head='IPD', action='Delete in IPD Bill Services IPD ID: $ipdId for item: $item ', date='$savedate', user='".$_SESSION['sess_uname']."', ipaddress='$ipAddress', lastupdate='$curDateTime'");
	}
	$ipd= mysql_query_db("select * from health_ipd where ipdId='$ipdId'");
	$fipd= mysql_fetch_db($ipd);
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
?>
<?php
	function convert_number_to_words($number) {
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
		);
		if (!is_numeric($number)) {
			return false;
		}
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
			);
			return false;
		}
		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}
		$string = $fraction = null;
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
		switch (true) {
			case $number < 21:
            $string = $dictionary[$number];
            break;
			case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
			}
            break;
			case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
			}
            break;
			default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
			}
            break;
		}
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
		return $string;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title><?php echo "&nbsp";?></title> <!-- Title-->
        <link href="../css/style.css" rel="stylesheet" type="text/css"> <!--<?php //echo $_GET['print'] == 1 ? 'print' : 'screen'; ?>.css-->                        <!-- CSS link for design the whole phase-->
        <link href="../awesome/css/font-awesome.css" rel="stylesheet" type="text/css">     <!-- Font awesome link for use font awesome icon-->
        <link href="../awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> <!-- Font awesome link for use font awesome icon-->
	    <script src="../ajax.js"></script>
        <!-- validation css and js start here -->
        <link rel="stylesheet" href="../validation/css/vstyle.css">
		<script src="../css/bootstrap.min.js"></script>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
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
			margin: 0;
			}
		</style>
		<style type="text/css">
			.printgrey16{
			font-size:14px;
			}
			.printgrey14
			{
			font-size:14px;
			}
			.line_hgt15{
			line-height:15px;
			}
			.clearfix7{
			clear: both;
			height: 4px;
			}
		</style>
	</head>
    <body>
        <div class="full">
			<!--cash patient-->
			<!--For TPA-->
			<div class="full">
                <div class="full"><!--mauto80-->
                    <div class="finalprintSize printBorder">
                        <div class="full">
                            <div class="full">
                                <div class="full">
									<?php include("ipd-dashboard-menu.php");?>
								</div>
                                <div class="full txt_center bold printgrey16"><?php if($fipd['dayCare']==1){ echo "Day Care Breakup Bill";} else{echo "IPD Breakup Bill";}?> </div>
                                <div class="full border_bottom"></div>
								<div class="clearfix10"></div>
                                <div class="mauto95 printgrey16"><strong>Patient Details-:  UHID: <?php echo $fipd['patientId'];?></strong></div>
                                <div class="mauto95">
									<!-- <div class="print_left">&nbsp;</div>-->
                                    <div class="full"><!--print_right-->
                                        <div class="clearfix10"></div>
                                        <div class="full">
                                            <div class="full50">
                                                <div class="full95">
													<div class="full printgrey14"><strong>IPD No : </strong> <?php echo $fipd['ipdId'];?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Patient Name :  </strong> <?php echo ucfirst($line['patientInitial']." ".$line['patientName']);?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Father/Guardian :  </strong> <?php echo ucfirst($line['fatherHusInitial']." ".$line['fatherHusName']);?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Age/Gender/Marital : </strong> <?php if(!empty($line['age_y'])){echo $line['age_y']."Y";}?> <?php if(!empty($line['age_m'])){echo $line['age_m']."M";}?> <?php if(!empty($line['age_d'])){echo $line['age_d']."D";}?>/<?php echo ucfirst($line['gender']);?>/<?php echo ucfirst($line['marritalStatus']);?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Address : </strong><?php echo $line['address'];?></div>
													<div class="clearfix7"></div>
												</div>
											</div>
                                            <div class="full50r">
                                                <div class="full95">
													<div class="full printgrey14"><strong>Print Date :</strong> <?php echo date('d-M-Y',strtotime($today));?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Payer Name : </strong> <?php echo $payer['tpa'];?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Department : </strong> <?php echo $linedeptt['depttName'];?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Doctor : </strong> <?php echo $linedoctor['doctorName'];?></div>
													<div class="clearfix7"></div>
													<div class="full printgrey14"><strong>Admission Date: </strong> <?php $datetime = new DateTime($fipd['dateTime'] ); echo $datetime->format( 'd-M-Y ' );  echo date("h:i:sA",strtotime($fipd['postTime'])); ?></div>
													<div class="clearfix7"></div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<table cellpadding="0" cellspacing="0" width="97%" class="border_all">
                                                    <tr class="printgrey16 bold line_hgt30 black_bck">
														<td style="width:5%;" class="border_right txt_center border_bottom">S.No.</td>
														<td style="width:15%;" class="border_right txt_center border_bottom">Date</td>
														<td style="width:7%;" class="border_right txt_center border_bottom">Code</td>
														<td style="width:38%;text-align:left;padding-left:5px" class="border_right txt_center border_bottom">Particular</td>
														<td style="width:10%;" class="border_right txt_center border_bottom">Rate (Rs)</td>
														<td style="width:5%;" class="border_right txt_center border_bottom">Unit</td>
														<td style="width:10%;" class="border_right txt_center border_bottom">User</td>
														<td style="width:20%;" class="border_right txt_center border_bottom">Amount (Rs)</td>
														<td style="width:10%;" class="txt_center border_bottom">Delete</td>
													</tr>
                                                    <?php
														$grossAmount=0;
														$totaldue=0;
														$priority=mysql_query_db("select distinct priority from health_patientipdhead where ipdId='$ipdId' order by priority ASC");
														//$allPriority=mysql_fetch_db($priority);
														while($allPriority=mysql_fetch_db($priority)){
															$prioritysql= mysql_query_db("select * from health_priority where priority='".$allPriority['priority']."'");	
															$priorityline= mysql_fetch_db($prioritysql);
															$patientIpd= mysql_query_db("select * from health_patientipdhead where priority='".$allPriority['priority']."' and ipdId='$ipdId' order by ipdHeadDate");	
															$i=1;$subtotal=0;
															while($patientIpdline= mysql_fetch_db($patientIpd))
															{
																//FOR SURGEON Name
																$surgeonId=$patientIpdline['doctorId'];
																if($surgeonId!=0 && $surgeonId!="")
																{
																	$sqlSurgeon=mysql_query_db("select * from health_doctors where id='$surgeonId'");
																	$lineSurgeon=mysql_fetch_db($sqlSurgeon);
																	$sqldeptt=mysql_query_db("select * from health_department where id='".$lineSurgeon['depttId']."'");
																	$linedeptt=mysql_fetch_db($sqldeptt);
																}	
																$userBreakup= mysql_query_db("select * from health_user where userId='".$patientIpdline['updateBy']."'");
																$fuserBreakup= mysql_fetch_db($userBreakup);
															?>
															<?php if($i==1) {?>
																<tr class="printgrey16 line_hgt15 grey_backgroundff">
																	<td style="width:80%;font-weight:bold;padding-left:10px" colspan="8" class="padd_right20  border_bottom txt_left"><?php echo $priorityline['head'];?></td>
																</tr>
															<?php } ?>
															<tr class="printgrey16 line_hgt15 grey_backgroundff">
																<td style="width:5%;" class="border_right border_bottom txt_center"><?php echo $i;?></td>
																<td style="width:15%;" class="border_right border_bottom txt_center"><?php echo date("d-m-Y",strtotime($patientIpdline['ipdHeadDate']));?></td>
																<td style="width:7%;" class="border_right border_bottom txt_center"><?php echo $patientIpdline['code'];?></td>
																<td style="width:25%;text-align:left;padding-left:5px" class="border_right border_bottom txt_center"><?php echo $patientIpdline['headName'];?><?php //echo $patientIpdline['priority'];?> <?php if($surgeonId!=0){ echo "(Dr.".$lineSurgeon['doctorName']."-".$linedeptt['depttName'].")";}?></td>
																<td style="width:5%;text-align:right;padding-right:10px" class="border_right border_bottom txt_center"><?php echo $patientIpdline['ipdHeadRate'];?></td>
																<td style="width:10%;" class="border_right border_bottom txt_center"><?php echo $patientIpdline['quantity'];?></td> 
																<td style="width:10%;" class="border_right border_bottom txt_center"><?php echo $fuserBreakup['userName'];?></td> 
																<td style="width:20%;text-align:right;" class="border_right txt_center border_bottom"><?php  echo number_format($patientIpdline['totalPrice'], 2, '.', ',');?></td>
																<td style="width:10%;" class="txt_center border_bottom"><a href="billing-breakup-delete?did=<?php echo encryptSoft($patientIpdline['patientIpdHeadId']);?>&ipdId=<?php echo encryptSoft($ipdId);?>" onclick="return confirm('Are you sure for delete?')"><i class="fa fa-trash" aria-hidden="true" style="color:#CC0000;"></i></a></td>
															</tr>
															<?php $i++;  
																$grossAmount=$grossAmount+$patientIpdline['totalPrice'];
																$subtotal=$subtotal+$patientIpdline['totalPrice'];
															} 
														?>
														<tr class="printgrey16 line_hgt15 grey_backgroundff">
															<td style="width:80%;" colspan="7" class="padd_right20 border_right border_bottom txt_right">Total Amount (Rs)</td>
															<td style="width:20%;text-align:right;" class="txt_center border_right border_bottom"><?php echo number_format($subtotal, 2, '.', ',');?></td>
														</tr>
														<?php  	
														}
													?>
                                                    <!--code for Diagnosis---->
													<?php
														$diagnosis=mysql_query_db("select distinct departmentId from health_patientdiagnosishead where ipdId='$ipdId' order by departmentId ASC");
														$dnum=mysql_num_db($diagnosis);
														if($dnum!=0){
														?>
														<tr class="printgrey16 line_hgt15 grey_backgroundff">
															<td style="width:80%;font-weight:bold;" colspan="7" class="padd_right20  border_bottom txt_left">Investigations Charges</td>
														</tr>
														<?php
															while($alldiagnosis=mysql_fetch_db($diagnosis)){
																$diagnosissql= mysql_query_db("select * from health_department_diagnosis where id='".$alldiagnosis['departmentId']."'");	
																$diagnosisline= mysql_fetch_db($diagnosissql);
																$patientIpddiagnosis= mysql_query_db("select * from health_patientdiagnosishead where departmentId='".$alldiagnosis['departmentId']."' and ipdId='$ipdId' order by headName");	
																$i=1;
																$diag_total=0;
																while($patientIpddiagnosisline= mysql_fetch_db($patientIpddiagnosis))
																{
																	$userBreakupDiag= mysql_query_db("select * from health_user where userId='".$patientIpddiagnosisline['updateBy']."'");
																	$fuserBreakupDiag= mysql_fetch_db($userBreakupDiag);
																?>
																<?php if($i==1) {?>
																	<tr class="printgrey16 line_hgt15 grey_backgroundff">
																		<td style="width:80%;font-weight:bold;" colspan="7" class="padd_right20  border_right border_bottom txt_left"><?php echo $diagnosisline['depttName'];?></td>
																	</tr>
																<?php } ?>
																<tr class="printgrey16 line_hgt15 grey_backgroundff">
																	<td style="width:5%;" class="border_right border_bottom txt_center"><?php echo $i;?></td>
																	<td style="width:15%;" class="border_right border_bottom txt_center"><?php echo date("d-m-Y",strtotime($patientIpddiagnosisline['ipdHeadDate']));?></td>
																	<td style="width:7%;" class="border_right border_bottom txt_center"><?php echo $patientIpddiagnosisline['code'];;?></td>
																	<td style="width:30%;text-align:left;padding-left:5px" class="border_right border_bottom txt_center"><?php echo $patientIpddiagnosisline['headName'];?><?php //echo $patientIpdline['priority'];?></td>
																	<td style="width:5%;text-align:right;padding-right:10px" class="border_right border_bottom txt_center"><?php echo $patientIpddiagnosisline['ipdHeadRate'];?></td>
																	<td style="width:10%;" class="border_right border_bottom txt_center"><?php echo $patientIpddiagnosisline['quantity'];?></td> 
																	<td style="width:10%;" class="border_right border_bottom txt_center"><?php echo $fuserBreakupDiag['userName'];?></td> 
																	<td style="width:20%;text-align:right;" class="txt_center border_bottom border_right"><?php echo number_format($patientIpddiagnosisline['totalPrice'], 2, '.', ',');?></td>
																	<td style="width:10%;" class="txt_center border_bottom ">&nbsp;</td>
																</tr>
																<?php $i++;  
																	$grossAmount=$grossAmount+$patientIpddiagnosisline['totalPrice'];
																	$diag_total=$diag_total+$patientIpddiagnosisline['totalPrice'];
																}?>
																<tr class="printgrey16 line_hgt15 grey_backgroundff">
																	<td style="width:80%;" colspan="7" class="padd_right20 border_right border_bottom txt_right">Total Amount (Rs)</td>
																	<td style="width:20%;text-align:right;" class="border_right txt_center border_bottom"><?php echo number_format($diag_total, 2, '.', ',');?></td>
																</tr>
																<?php
																}
														}// end of if num!=0
													?>
													<!--end code for Diagnosis---->
													<tr class="printgrey16 line_hgt15 black_bck ">
														<td style="width:80%;" colspan="8" class="padd_right20 border_right border_bottom txt_right">&nbsp;</td>
													</tr>
													<tr class="printgrey16 line_hgt15 black_bck ">
                                                        <td style="width:80%;" colspan="7" class="padd_right20 border_right border_bottom txt_right">Total Bill Amount (Rs)</td>
                                                        <td style="width:20%;text-align:right;" class="txt_center border_right border_bottom"><?php echo number_format($grossAmount, 2, '.', ',');?></td>
													</tr>
													<?php
														$depositAmount=0;
														$payment= mysql_query_db("select * from health_payment where ipdId='$ipdId'");
														while($fpayment=mysql_fetch_db($payment)){
															$depositAmount=$depositAmount+$fpayment['creditAmount'];
														}
													?>
													<tr class="printgrey16 line_hgt15 black_bck ">
													    <td style="width:80%;" colspan="7" class="padd_right20 border_right border_bottom txt_right">Amount Paid by Member (Rs)</td>
                                                        <td style="width:20%;text-align:right;" class="txt_center border_right border_bottom"><?php if($depositAmount){echo number_format($depositAmount, 2, '.', ',');}  else { echo "0.00";}?></td>
													</tr>
													<?php if($fpay['discountAmount']!=0){?>
														<tr class="printgrey16 line_hgt15 black_bck ">
															<td style="width:80%;" colspan="7" class="padd_right20 border_right border_bottom txt_right">Discount Amount (Rs)</td>
															<td style="width:20%;text-align:right;" class="txt_center border_right border_bottom"><?php echo number_format($fpay['discountAmount'], 2, '.', ',');?></td>
														</tr>
														<?php }
														$totaldue=$grossAmount-$depositAmount;
														$netAmount=$totaldue-$fpay['discountAmount'];
													?>
													<tr class="printgrey16 line_hgt15 black_bck bold">
                                                        <td style="width:80%;" colspan="7" class="padd_right20 border_right txt_right">Balance Payable Amount (Rs)<br>
															<?php //echo "Rupees ".ucwords(convert_number_to_words($netAmount-$fpay['discountAmount']))." Only"?>
														</td>
                                                        <td style="width:20%;text-align:right;" class="txt_center border_right"><?php echo number_format($netAmount, 2, '.', ',');?></td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="clearfix20"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>										
</body>
</html>