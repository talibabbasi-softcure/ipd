<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-IPD Billing-");
	/* log4php end here */
	//decode
	$did=decryptSoft($did);
	$rd=decryptSoft($rd);
	$rdAdd=decryptSoft($rdAdd);
	if(isset($ipdSubmit)){
		$_SESSION['ipdId']=$ipdId;
	}
	else if(isset($ipdId)){
		$ipdId=decryptSoft($ipdId);
		$_SESSION['ipdId']=$ipdId;
	}
	if($_SESSION['ipdId'] && !isset($ipdId) && !isset($ipdSubmit)){
		$ipdId=$_SESSION['ipdId'];
	}
	$blank=1;//when page is blank
	//code for edit/delete permissions
	$userAccess= mysql_query_db("select * from health_user where userId='".$_SESSION['sess_uid']."'");
	$fuserAccess= mysql_fetch_db($userAccess);
	$role=$fuserAccess['role'];
	//end of code for edit/delete permissions
	$totalDeduction=0;
	//fetching session ipdId
	if($_SESSION['ipdId'] && !isset($ipdId)){
		$ipdId=decryptSoft($ipdId);
		$ipdId=$_SESSION['ipdId'];
	}
	if($ipdId){
		$lckcancel=mysql_query_db("select * from health_ipd where ipdId='$ipdId' and ipdStatus='0'");
		$nlckcancel= mysql_num_db($lckcancel);
		if($nlckcancel==1){
			$alertType=1; //cancelled
			$ipdId='';
			$did='';
		}	 
	}
	if($did){
		$nn3="delete from health_ipdfinalbill where id='$did' and ipdId='$ipdId'";
		$findipdsql= mysql_query_db("select * from health_ipdfinalbill where id='$did'");	
		$findipdline= mysql_fetch_db($findipdsql);
		$price=$findipdline['price'];
		$headName=$findipdline['headName'];
		//to update ipdfinalbill, no need as generate bill is mandatory
		/**
			$deduction=$findipdline['totalPrice']; 
			$ipdbill=mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
			$numipdbill=mysql_num_db($ipdbill);
			if($numipdbill!=0){
			$nipdbill=mysql_fetch_db($ipdbill);
			$gross=$nipdbill['grossAmount']-$deduction;
			$net=$nipdbill['netAmount']-$deduction; 
			mysql_query_db("update health_ipdbill set grossAmount='$gross',netAmount='$net' where ipdId='$ipdId'");
			}
		**/
		if($findipdline['diagnosisId']==0){ //ipd head
			mysql_query_db("delete from health_ipdfinalbill where id='$did'");
			//fetch details before to delete
			$getData=mysql_query_db("select * from health_patientipdhead where ipdHeadId='".$findipdline['ipdHeadId']."' and ipdId='$ipdId' and ipdHeadRate='$price'");
			$fgetData=mysql_fetch_db($getData);
			$item=$fgetData['headName']." - Rs ".$fgetData['totalPrice'];
			$uhid=$fgetData['patientId'];
			mysql_query_db("delete from health_patientipdhead where ipdHeadId='".$findipdline['ipdHeadId']."' and ipdId='$ipdId' and ipdHeadRate='$price'");
		}
		else{
			//diagnosis rows head, but not work because i have remove delete button form this page
			mysql_query_db("delete from health_patientdiagnosishead where diagnosisId='".$findipdline['diagnosisId']."' and ipdId='$ipdId'");
			//inactive receipts
			mysql_query_db("update health_financediagnosis_ipd set status=0 where diagnosisId='".$findipdline['diagnosisId']."'");
			mysql_query_db("update health_diagnosis_ipd set status=0 where id='".$findipdline['diagnosisId']."'");
			mysql_query_db("delete from health_ipdfinalbill where id='$did'");		
		}
		$nnDelete="delete from health_patientipdhead where ipdHeadId='".$findipdline['ipdHeadId']."' and ipdId='$ipdId' and ipdHeadRate='$price'";
		//$logger->info("delete from patientipdhead where ipdId='$ipdId' and service='$headName' is successful by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query is ".$nnDelete);
		$savedate=date('Y-m-d');
		//echo "insert into health_logs set action='Delete in IPD Bill IPD ID: $ipdId', date='', user='".$_SESSION['sess_uname']."', ipaddress='$ipAddress', lastupdate='$curDateTime'";
		mysql_query_db("insert into health_logs set patientId='$uhid',head='IPD', action='Delete in IPD Bill Services IPD ID: $ipdId for item: $item ', date='$savedate', user='".$_SESSION['sess_uname']."', ipaddress='$ipAddress', lastupdate='$curDateTime'");
	}
	if($rd){//for discount
		mysql_query_db("update health_ipdbill set discountAmount=0, co='' where ipdId='$rd'");
		mysql_query_db("update health_discount set co='',discountAmount=0 where ipdId='$rd'");		
	}
	if($rdAdd){//for addition delete
		mysql_query_db("update health_ipdbill set additionAmount='', additionco='' where ipdId='$rdAdd'");		
	}
	if($ipdId){
		//settlement
		$settl=mysql_query_db("select * from health_ipd_settlement where ipdId='$ipdId'");
		$num_settl=mysql_num_db($settl);
		if($num_settl!=0)
		{
			$getSettl=mysql_fetch_db($settl);
			$totalDeduction=$getSettl['deduct_1']+$getSettl['deduct_2']+$getSettl['deduct_3']+$getSettl['deduct_4']+$getSettl['deduct_5']+$getSettl['deduct_6']+$getSettl['deduct_7']+$getSettl['deduct_8']+$getSettl['deduct_9']+$getSettl['deduct_10']+$getSettl['tdsDeduction']+$getSettl['discountAmount'];
			$totalDeduction=ceil($totalDeduction);
		}
		//basic information
		//$logger->warn("Search by ipdId ".$ipdId." is successful by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query is ".$nn);
		$ipdsql= mysql_query_db("select * from health_ipd where ipdId='$ipdId'");	
		$ipdline= mysql_fetch_db($ipdsql);	
		$ipd_num=mysql_num_db($ipdsql);
		if($ipd_num!=0){
			$patientId=$ipdline['patientId'];
			$patientList= mysql_query_db("select * from health_patient where patientId='".$ipdline['patientId']."'");
			$fpatientList= mysql_fetch_db($patientList);
			$depttName= mysql_query_db("select * from health_department where id='".$ipdline['depttId']."'");
			$fdepttName= mysql_fetch_db($depttName);
			$doctName= mysql_query_db("select * from health_doctors where id='".$ipdline['doctorId']."'");
			$fdoctName= mysql_fetch_db($doctName);
			$ipdFee= mysql_query_db("select * from health_financeipd where ipdId='$ipdId'");
			$fipdFee= mysql_fetch_db($ipdFee);
			$allocationType= mysql_query_db("select * from health_allocation where ipdId='$ipdId'");
			$fallocationType= mysql_fetch_db($allocationType);
			$roomAllocation= mysql_query_db("select * from health_roomcategory where roomCategoryId='".$fallocationType['category']."'");
			$froomAllocation= mysql_fetch_db($roomAllocation);
			$alloc= mysql_query_db("select * from health_roomalias where id='".$fallocationType['roomNo']."'");
			$falloc= mysql_fetch_db($alloc);
			$sql_payer=mysql_query_db("select tpa from health_tpa where id='".$ipdline['payer']."'");
			$payer=mysql_fetch_db($sql_payer);
			$lck=mysql_query_db("select * from health_ipd where ipdId='$ipdId' and ipdStatus='3' ");
			$nlck= mysql_num_db($lck);
			if($nlck==0){
				$blank=0;
				$ipdCounter=mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
				$nipdCounter=mysql_num_db($ipdCounter);
				if($nipdCounter!=0){
					$sql_dis= mysql_query_db("select * from health_ipdbill where ipdId='$ipdId'");
					$fdis= mysql_fetch_db($sql_dis);
					$net_amount1=$fdis['netAmount'];
					$dis_amount1=$fdis['discountAmount'];
					$co=$fdis['co'];
					$dob=$fdis['billDate'];
					$tob=$fdis['billTime'];
					$addco=$fdis['additionco'];
					$addAmount=$fdis['additionAmount'];
					if($dis_amount1==0){
						$nodiscount=1;
					}
					else{
						$nodiscount=2; 
						$x="disabled";
					}
				}
			}
			else {
				$role=0;
				$alertType=3; //Patient is locked, final bill has been generated. Go to admin to unlock !");
				$ipdId='';
			}
		} // end if ipd exist
		else{ 
			$role=0;
			$blank=1;
			$alertType=2; //No Patient Record
			$patientId='';
			$ipdId='';
		} 
	} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<title>Billing</title>
		<link rel="icon" href="../images/favicon.png" type="image/png" /> <!-- Title-->
		<link href="../css/style.css" rel="stylesheet" type="text/css">                      <!-- CSS link for design the whole phase-->
		<link href="../awesome/css/font-awesome.css" rel="stylesheet" type="text/css">     <!-- Font awesome link for use font awesome icon-->
		<link href="../awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> <!-- Font awesome link for use font awesome icon-->
		<script src="../ajax.js"></script>
		<!-- validation css and js start here -->
		<link rel="stylesheet" href="../validation/css/vstyle.css">
		<!-- validation css and js end -->
		<script src="../css/bootstrap.min.js"></script>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
		<!--sweet code--->	
		<script src="../js-web/jquery-3.6.0.min.js"></script>
		<script src="../js-web/popper.min.js"></script>
		<script src="../js-web/bootstrap.min.js"></script>
		<script src="../js-web/sweetalert2.min.js"></script>
		<script src="../js-web/webcam-easy.js"></script>
		<link rel="stylesheet" href="../css/sweetalert2.min.css"/>
		<link href="../css/jquery.multiselect.css" rel="stylesheet" type="text/css">
		<!-- multiple select box with checkbox start here -->
		<link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="../js/jquery-1.12.4.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<!--Drop box with search box-->
		<script src="../js/global.js"></script>
		<script src="../js/chosen-jquery.js"></script>
		<link href="../css/chosen.css" rel="stylesheet" type="text/css">
		<script>
			function _isNumberKey(evt){
				var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !=46)
				return false;
				return true;
			}
			function _isFloatNumberKey(evt){
				var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !=46)
				return false;
				return true;
			}
			function _startTime() {
				var today = new Date();
				var h = today.getHours();
				var m = today.getMinutes();
				var s = today.getSeconds();
				m = checkTime(m);
				s = checkTime(s);
				document.getElementById('txt').innerHTML= h + ":" + m + ":" + s;
				var t = setTimeout(_startTime, 500);
			}
			function checkTime(i) {
				if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
				return i;
			}
			function pulsar(obj) {
				obj.value=obj.value.toUpperCase();
			}
		</script>
		<script>
			function _show_total_bill(z,n){
				var totla_bill_amount=0;
				var pr=parseFloat(document.getElementById('id_'+z).value);
				var un=parseFloat(document.getElementById('unit_'+z).value);
				var bill_unit=pr*un;
				document.getElementById('ttl_'+z).value=parseFloat(bill_unit);
				for(var i=1;i<=n;i++){
					totla_bill_amount=parseFloat(totla_bill_amount)+parseFloat(document.getElementById('ttl_'+i).value);
				}
				document.getElementById('grossAmount').value=parseFloat(totla_bill_amount);
				document.getElementById('grossAmount_div').innerHTML=parseFloat(totla_bill_amount);
				var deposit=parseInt(document.getElementById('deposit_amount').value);
				document.getElementById('FinalNetAmount').value=parseFloat(totla_bill_amount)- deposit;
				document.getElementById('FinalNetAmount_div').innerHTML=parseFloat(totla_bill_amount)- deposit;
			}
		</script>
		<script language="javascript" type="text/javascript">
			function del_prompt(frmobj,comb,id)
			{
				if(comb=='Final Submit'){
					if(confirm ("Are you sure to submit final bill & IP Lock ?")){
						frmobj.action = id;
						//alert(id);
						frmobj.submit();
						} else { 
						return false;
					}
				}
			}
		</script>
		<script>
			$( function() {
				$( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' }).datepicker(<?php echo $today;?>);
			} );
		</script>
		<style type="text/css">
			.hide{
			display:none;
			}
			.show{
			}
		</style>
		<!--loader-->
		<style>
			#loader { 
			border: 12px solid #f3f3f3; 
			border-radius: 50%; 
			border-top: 12px solid #008081; 
			width: 70px; 
			height: 70px; 
			animation: spin 1s linear infinite; 
			z-index:1000;
			}  
			@keyframes spin { 
			100% { 
			transform: rotate(360deg); 
			} 
			} 
			.center { 
			position: absolute; 
			top: 0; 
			bottom: 0; 
			left: 0; 
			right: 0; 
			margin: auto; 
			}
		</style>
		<script> 
			document.onreadystatechange = function() { 
				if (document.readyState !== "complete") { 
					document.querySelector( 
					"body").style.visibility = "hidden"; 
					document.querySelector( 
					"#loader").style.visibility = "visible"; 
					} else { 
					document.querySelector( 
					"#loader").style.display = "none"; 
					document.querySelector( 
					"body").style.visibility = "visible"; 
				} 
			}; 
			
			function _searchPatient(ipdId)
				{
				var search = window.location.search + (window.location.search ? "&" : "?");
                search = "?ipdId="+ipdId;
                window.location.href = window.location.protocol + "//" + window.location.host + window.location.pathname + search;
				return true; 
				}
		</script>
		<!--loader end here-->
	</head>
	<body>
		<div id="loader" class="center"></div>
		<div class="full">
			<?php include("ipd-dashboard-menu.php");?>
			<div class="clearfix20"></div>
			<div class="full">
				<div class="mauto95">
					<!--<div class="full18">
						<?php //include("opd-sidemenu.php");?>
					</div>-->
					<div class="full grey_bck22 box_shadow66"><!--full80r-->
						<div class="full line_hgt35 pink_bck">  <!--right_side_head-->
							<div class="mauto95">
								<div class="full">
									<div class="full50 white18"> Patient Details</div>
								</div>
							</div>
						</div>
						<div class="mauto95">
							<div class="full">
								<div class="clearfix20"></div>
								<div class="full">
									<form accept-charset="UTF-8" name="treat_frm" method="post">
										<div class="full">
										<div class="full30">
												<div class="full20 grey14 line_hgt25">Search Patient </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full60">
													<div class="full">
														<div class="full">
															<select name="templateId" class="select_txt82 grey14 chosen-select"  title="Search Patient" onChange="_searchPatient(this.value)">
																<option value="">-- Select Patient --</option>
																<?php
																$temp= mysql_query_db("select * from health_ipd where ipdStatus=1");
																while($ftemp= mysql_fetch_db($temp)){
																$patientList2= mysql_query_db("select * from health_patient where patientId='".$ftemp['patientId']."'");
																$fpatientList2= mysql_fetch_db($patientList2);
																?>
																<option value="<?php echo encryptSoft($ftemp['ipdId']);?>" <?php if($ftemp['ipdId']==$ipdId){ echo "selected";}?>> <?php echo ucwords(substr($fpatientList2['patientName'], 0, 40));?> - IPD: <?php echo $ftemp['ipdId']; ?>   </option>
																<?php }?>
															</select>
														</div>
													   
													</div>
												   
												</div>
											</div>
											<div class="full30">
												<div class="full40 grey14 line_hgt25"> Enter IPD No. </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full40">
													<div class="full">
														<input type="text" autofocus name="ipdId" id="ipdId" placeholder="IPD" required value="<?php if($ipdId){ echo $ipdId;}?>" onkeypress="return _isNumberKey(event)" class="dash_txt_box">
													</div>
												</div>
											</div>
											<div class="full40">
												<div class="full30 grey14 line_hgt25">
													<input type="submit" name="ipdSubmit" value="Submit" class="dash_btn" style="width: 90%;">
												</div>
												<div class="full30 grey14 line_hgt25">
													<?php if($nipdCounter!=0){?><img src="../images/check.png" height="30px"> <?php }?>
												</div>
											</div>
											<div class="full20r" style="display:<?php if($role=='0'){echo 'none';}?>">
												<div class="full grey14 line_hgt25" style="display:<?php if(!isset($ipdId)){echo 'none';}?>">
													<a href="billing-breakup-delete?ipdId=<?php echo encryptSoft($ipdId);?>" class="sub-button">Modify Breakup <i class="fa fa-trash" aria-hidden="true" style="color:#CC0000;"></i></a>  
												</div>
											</div>
											<div class="clearfix10"></div>
										</div> 
									</form>
								</div>
							</div>
						</div>
						<div class="clearfix20"></div>
						<div class="medi_dtls150 txt_center grey16">Patient's Details</div>
						<div class="full grey16" style="border-bottom:1px solid #CCCCCC;"></div>
						<div class="clearfix20"></div>
						<div class="full">
							<div class="mauto95">
								<div class="full">
									<div class="clearfix10"></div>
									<div class="full">
										<div class="full25">
											<div class="full45 black16">IPD No.</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full40 grey14 line_hgt22"> <?php echo $ipdId;?></div>
										</div>
										<div class="full25">
											<div class="full20 black16">Name.</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full75 grey14 line_hgt22"> <?php echo $fpatientList['patientInitial']." ".$fpatientList['patientName'];?></div>
										</div>
										<div class="full25">
											<div class="full33 black16">Father's Name</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full60 grey14 line_hgt22"> <?php echo $fpatientList['fatherHusInitial']." ".$fpatientList['fatherHusName'];?> </div>
										</div>
										<div class="full25">
											<div class="full33 black16">Age/Gender</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full60 grey14 line_hgt22"> <?php if(!empty($fpatientList['age_y'])){echo $fpatientList['age_y']."Y";}?> <?php if(!empty($fpatientList['age_m'])){echo $fpatientList['age_m']."M";}?> <?php if(!empty($fpatientList['age_d'])){echo $fpatientList['age_d']."D";}?> / <?php echo ucfirst($fpatientList['gender']);?> </div>
										</div>
									</div>
									<div class="clearfix7"></div>
									<div class="full">
										<div class="full25">
											<div class="full45 black16">Occupancy</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full40 grey14 line_hgt22"> <?php echo $froomAllocation['roomCategoryName']; echo $falloc['alias'];?> </div>
										</div>
										<div class="full25">
											<div class="full50">
												<div class="full45 black16">Room / Bed No.</div>
												<div class="full9 black16 txt_center"> : </div>
												<div class="full45 grey14 line_hgt22"> <?php echo $falloc['alias'];?> </div>
											</div>
											<div class="full50 disnone">
												<div class="full45 black16">Bed No.</div>
												<div class="full9 black16 txt_center"> : </div>
												<div class="full45 grey14 line_hgt22"> <?php echo $fallocationType['bedNo'];?> </div>
											</div>
										</div>
										<div class="full25">
											<div class="full33 black16">Department</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full60 grey14 line_hgt22"> <?php echo $fdepttName['depttName'];?> </div>
										</div>
										<div class="full25">
											<div class="full33 black16">Doctor's Name</div>
											<div class="full5 black16 txt_center"> : </div>
											<div class="full60 grey14 line_hgt22"> <?php echo $fdoctName['doctorName'];?> </div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix20"></div>
					</div>
				</div>
			</div>
			<div class="clearfix20"></div>
			<div class="full">
				<div class="mauto95">
					<form accept-charset="UTF-8" name="add_invoice" id="add_invoice" method="post" action="billing-code" enctype="multipart/form-data">
						<input type="hidden" name="patientId" value="<?php echo $patientId;?>">
						<input type="hidden" name="ipdId" value="<?php echo $ipdId;?>">
						<input type="hidden" name="alertTypeBox" id="alertTypeBox" value="<?php echo $alertType;?>">
						<input type="hidden" id="getMyDeduction" value="<?php echo $totalDeduction;?>">
						<input type="hidden" name="payer" value="<?php echo $ipdline['payer'];?>">
						<input type="hidden" id="doa" value="<?php echo $ipdline['dateTime'];?>">
						<?php if($blank==0){?>
							<div class="full grey_bck22 box_shadow66"><!--full80r-->
								<div class="full line_hgt35 pink_bck">  <!--right_side_head-->
									<div class="mauto95">
										<div class="full">
											<div class="full50 white18"> Billing Summary</div>
											<div class="full40r white18">
												<div class="full20 black16">Date & Time</div> 
												<div class="full5 black16 txt_center"> : </div>
												<div class="full60"> 
													<div class="full50"><input readonly type="text" name="billDate" id="datepicker" required class="dash_txt_box" value="<?php if($dob!=""){echo $dob;} else {echo $today;}?>" style="width:120px; height:22px;"></div>
													<div class="full50"><input  step="any" type="time" name="billTime" value="<?php if($tob!=""){echo $tob;} else{echo date("H:i:s");} ?>" placeholder="Time" required class="dash_txt_box" style="width:120px; height:22px;"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="mauto95">
									<div class="full">
										<div class="clearfix10"></div>
										<div class="full">
											<div class="full">
												<div class="full25">                                     											
													<div class="full90 grey14 line_hgt25"><strong>Admission Date : </strong>
														<?php 
															$datetime = new DateTime( $ipdline['dateTime'] ); echo $datetime->format('d-M-Y');
															echo " "; echo date("h:i:s A", strtotime($ipdline['postTime']));
														?>
													</div>
												</div>
												<div class="full20"> 
													<div class="full95 grey14 line_hgt25"><strong>Discharge Date: </strong>
														<?php $dish= mysql_query_db("select * from health_discharge where  ipdId='$ipdId'");
															$fdish= mysql_fetch_db($dish);
														?>
														<?php if($fdish['dischargeDate']){$datetime = new DateTime($fdish['dischargeDate'] ); echo $datetime->format( 'd-M-Y' );} else { echo "NA";}?>
													</div>
												</div>
												<div class="full20" style="display:none">
													<div class="full50 grey14 line_hgt25"><strong> Type of Discharge : </strong></div>
													<div class="full30">
														<div class="full grey14 line_hgt25">
															<?php 
																$dish1= mysql_query_db("select * from health_dischargetype where  dischargeTypeId='".$fdish['typeOfDischarge']."'");
																$fdish1= mysql_fetch_db($dish1);
															if(isset($fdish1['dischargeName'])){echo ucfirst($fdish1['dischargeName']);} else{ echo "NA";}?>
														</div>
													</div>
												</div>
												<div class="full15">
													<div class="full30 grey14 line_hgt25"><strong> Billing:</strong></div>
													<div class="full10">
														<div class="full  grey14 line_hgt25">
															<?php
																$dis_today=date("Y-m-d");
																if($fdish['dischargeDate']){
																	$now =$fdish['dischargeDate']; // or your date as well
																	} else {
																	$now =$dis_today;
																}
																$date1_ts = strtotime($now);
																$date2_ts = strtotime($dis_today);
																$diff = $date2_ts - $date1_ts;
																$days= round($diff / 86400);
															?>
															<?php if($fipdFee['billingCategory']==1){echo "Cash";} else { echo "Credit";}?>
															<?php //echo $days;?>
															<input type="hidden" name="days" value="<?php echo $days;?>">
														</div>
													</div>
												</div>
												<div class="full40">
													<div class="full10 grey14 line_hgt25"><strong> Payer</strong></div>
													<div class="full5 txt_center line_hgt25"> : </div>
													<div class="full80" style="background-color:#fdc51c;padding:0px 3px">
														<div class="full  grey14 line_hgt25">
															<?php
																if($payer['tpa']!=''){
																	echo $payer['tpa'];
																}
																else{
																	echo "NA";
																}
															?>
														</div>
													</div>
												</div>
												<div class="clearfix10"></div>
											</div> 
										</div>
									</div>
								</div>
								<div class="full">
									<div class="mauto95">
										<div class="full">
											<table cellpadding="0" cellspacing="0" width="100%" class="border_all">
												<tr class="grey16 bold line_hgt30 black_bck">
													<td style="width:5%;" class="border_right txt_center border_bottom">S.No.</td> 
													<td style="width:15%;" class="border_right txt_center border_bottom">Code</td> 
													<td style="width:25%;" class="border_right txt_center border_bottom">Particular</td>
													<td style="width:15%;" class="border_right txt_center border_bottom">Rate</td>
													<td style="width:15%;" class="border_right txt_center border_bottom">Unit</td>
													<td style="width:15%;" class="border_right txt_center border_bottom">Amount (Rs) </td>
													<td style="width:10%;display:<?php if($role=='0'){echo 'none';}?>" class="txt_center border_bottom">Delete</td>
												</tr>
												<?php
													//for package list items at top
													$grossAmount1=0;
													$chck=mysql_query_db("select * from health_ipdfinalbill where ipdId='$ipdId' and package=1 and diagnosisId=0 order by priority ASC");
													$nchck= mysql_num_db($chck);
													if($nchck!=0){
														$g=1;
														while($fchck=mysql_fetch_db($chck)){
														?>
														<tr class="grey16 line_hgt25 yellow_bck">
															<td style="width:5%;" class="border_right border_bottom txt_center">PACK.<?php echo $g;?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input type="hidden" name="id_bill[]" value="<?php echo $fchck['id'];?>"><?php echo $fchck['code'];?></td>
															<td style="width:25%;" class="border_right border_bottom txt_center"><?php echo implode("<br/>", str_split($fchck['headName'],56));?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text" name="rate_bill[]" id="id_<?php echo $g;?>" value="<?php echo $fchck['price'];?>" class="dueBilltxt" onkeyup="_show_total_bill('<?php echo $g;?>','<?php echo $nchck;?>')"></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text" name="unit_bill[]" id="unit_<?php echo $g;?>" value="<?php echo $fchck['quantity'];?>" class="dueBilltxt" onkeyup="_show_total_bill('<?php echo $g;?>','<?php echo $nchck;?>')"></td> 
															<td style="width:15%;" class="border_right txt_center border_bottom"><input readonly type="text" name="ttl_price_bill[]" id="ttl_<?php echo $g;?>" value="<?php echo $fchck['totalPrice'];?>" readonly class="dueBilltxt readonly_bck"></td>
															<td style="width:10%;display:<?php if($role=='0'){echo 'none';}?>" class="txt_center border_bottom"><a href="billing?did=<?php echo encryptSoft($fchck['id']);?>&ipdId=<?php echo encryptSoft($ipdId);?>" onclick="return confirm('Are you sure for delete?')"><i class="fa fa-trash" aria-hidden="true" style="color:#CC0000;"></i></a></td>
														</tr>
														<?php
															$g++;
															$grossAmount1=$grossAmount1+$fchck['totalPrice'];
														} 
													?>
													<tr class="grey16 line_hgt25 bold">
														<td style="width:5%;" class="border_right border_bottom txt_center"></td>
														<td style="width:15%;" class="border_right border_bottom txt_center"></td>
														<td style="width:25%;" class="border_right border_bottom txt_center"></td>
														<td style="width:15%;" class="border_right border_bottom txt_center"></td>
														<td style="width:15%;" class="border_right border_bottom txt_center">Package Total</td> 
														<td style="width:15%;" class="border_right txt_center border_bottom"><?php echo number_format($grossAmount1, 2, '.', ',');?></td>
														<td style="width:15%;" class="border_right border_bottom txt_center"></td> 
													</tr>
													<tr class="grey16 line_hgt25 bold">
														<td colspan="7" class="border_right border_bottom txt_left">Breakup Details</td>
													</tr>
												<?php }?>
												<?php
													$grossAmount=0;
													$chck=mysql_query_db("select * from health_ipdfinalbill where ipdId='$ipdId' and package=0 and diagnosisId=0 order by priority ASC");
													$nchck= mysql_num_db($chck);
													if($nchck!=0){
														$g=1;
														while($fchck=mysql_fetch_db($chck)){
														?>
														<tr class="grey16 line_hgt25 white_bck">
															<td style="width:5%;" class="border_right border_bottom txt_center"><?php echo $g;?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input type="hidden" name="id_bill[]" value="<?php echo $fchck['id'];?>"><?php echo $fchck['code'];?></td>
															<td style="width:25%;" class="border_right border_bottom txt_center"><?php echo implode("<br/>", str_split($fchck['headName'],56));?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text" name="rate_bill[]" id="id_<?php echo $g;?>" value="<?php echo $fchck['price'];?>" class="dueBilltxt" onkeyup="_show_total_bill('<?php echo $g;?>','<?php echo $nchck;?>')"></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text" name="unit_bill[]" id="unit_<?php echo $g;?>" value="<?php echo $fchck['quantity'];?>" class="dueBilltxt" onkeyup="_show_total_bill('<?php echo $g;?>','<?php echo $nchck;?>')"></td> 
															<td style="width:15%;" class="border_right txt_center border_bottom"><input readonly type="text" name="ttl_price_bill[]" id="ttl_<?php echo $g;?>" value="<?php echo $fchck['totalPrice'];?>" readonly class="dueBilltxt readonly_bck"></td>
															<td style="width:10%;display:<?php if($role=='0'){echo 'none';}?>" class="txt_center border_bottom"><a href="billing?did=<?php echo encryptSoft($fchck['id']);?>&ipdId=<?php echo encryptSoft($ipdId);?>" onclick="return confirm('Are you sure for delete?')"><i class="fa fa-trash" aria-hidden="true" style="color:#CC0000;"></i></a></td>
														</tr>
														<?php
															$g++;
															$grossAmount=$grossAmount+$fchck['totalPrice'];
														} 
													}
												?>
												<!--for diagnosis-->
												<?php
													$diagnochck=mysql_query_db("select * from health_patientdiagnosishead where ipdId='$ipdId' order by ipdHeadDate ASC");
													$ndiagnochck= mysql_num_db($diagnochck);
													if($ndiagnochck!=0){
														while($fdiagnochck=mysql_fetch_db($diagnochck)) {
														?>
														<tr class="grey16 line_hgt25 white_bck">
															<td style="width:5%;" class="border_right border_bottom txt_center"><?php echo $g;?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><?php echo $fdiagnochck['code'];?></td>
															<td style="width:25%;" class="border_right border_bottom txt_center"><?php echo implode("<br/>", str_split($fdiagnochck['headName'],55));?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text"   value="<?php echo $fdiagnochck['ipdHeadRate'];?>" class="dueBilltxt" onkeyup="_show_total_bill('<?php echo $g;?>','<?php echo $nchck;?>')"></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text"   value="<?php echo $fdiagnochck['quantity'];?>" class="dueBilltxt" onkeyup="_show_total_bill('<?php echo $g;?>','<?php echo $nchck;?>')"></td> 
															<td style="width:15%;" class="border_right txt_center border_bottom"><input readonly type="text"  value="<?php echo $fdiagnochck['totalPrice'];?>" readonly class="dueBilltxt readonly_bck"></td>
															<td style="width:10%;" class="txt_center border_bottom"><a href="billing?diagnodid=<?php echo encryptSoft($fdiagnochck['patientIpdHeadId']);?>&ipdId=<?php echo encryptSoft($ipdId);?>" onclick="return confirm('Are you sure for delete?')"></a></td>
														</tr>
														<?php
															$g++;
															$grossAmount=$grossAmount+$fdiagnochck['totalPrice'];
														} 
													}
												?>
												<!--for pharma-->
												<?php
													$pharmaBill=mysql_query_db("select * from health_ipd_pharmacy_bill where ipdId='$ipdId' order by date");
													$numpharmaBill= mysql_num_db($pharmaBill);
													if($numpharmaBill!=0){
														while($fpharmaBill=mysql_fetch_db($pharmaBill)) {
															$pharmaType= mysql_query_db("select * from health_ipd_pharmacy_type where id='".$fpharmaBill['pharmaType']."'");
															$fpharmaType= mysql_fetch_db($pharmaType);	
														?>
														<tr class="grey16 line_hgt25 white_bck">
															<td style="width:5%;" class="border_right border_bottom txt_center"><?php echo $g;?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center">IPP: <?php echo $fpharmaBill['id'];?></td>
															<td style="width:25%;" class="border_right border_bottom txt_center"><?php echo implode("<br/>", str_split($fpharmaType['name'],40));?></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text"   value="<?php echo $fpharmaBill['total'];?>"    class="dueBilltxt"></td>
															<td style="width:15%;" class="border_right border_bottom txt_center"><input readonly class="dueBilltxt readonly_bck" type="text"   value="1" class="dueBilltxt"></td> 
															<td style="width:15%;" class="border_right txt_center border_bottom"><input readonly type="text"  value="<?php echo $fpharmaBill['total'];?>" readonly class="dueBilltxt readonly_bck"></td>
															<td style="width:10%;" class="txt_center border_bottom"><a href="#" onclick="return confirm('Are you sure for delete?')"> </a></td>
														</tr>
														<?php
															$g++;
															$grossAmount=$grossAmount+$fpharmaBill['total'];
														} 
													}
												?>
												<!--end pharma-->
												<script>
													function _calDiscount(){
														//alert("hello");
														//var due=document.getElementById('dueAmount22').value;
														var dis=document.getElementById('discountAmount').value;
														//alert(dis);
														var f=document.getElementById('FinalNetAmount').value;
														//alert(f);
														var final=f-dis;
														document.getElementById('FinalNetAmount55').value=final;
														document.getElementById('FinalNetAmount55').innerHTML=final;
													}
												</script>
												<tr class="grey16 line_hgt25 black_bck ">
													<td style="width:2%;" class="padd_right20 border_right txt_right"></td>
													<td style="width:72%;" colspan="4" class="padd_right20 border_right border_bottom txt_right">Gross Amount <input type="hidden" name="grossAmount" id="grossAmount" value="<?php echo $grossAmount;?>"></td>
													<td style="width:20%;" class="border_right txt_center border_bottom" id="grossAmount_div">
														<?php echo number_format($grossAmount, 2, '.', ',');?>/-
													</td>
													<td style="width:10%;" class="txt_center border_bottom" id="grossAmount_div">
														&nbsp;
													</td>
												</tr>
												<!--addition charge-->
												<tr class="grey16 line_hgt25 black_bck ">
													<td style="width:2%;" class="padd_right20 border_right txt_right"></td>
													<td style="width:72%;" colspan="4" class="padd_right20 border_right border_bottom txt_right">
														<input type="text" placeholder="C/O for Addtion" value="<?php echo $addco; ?>" name="additionco" id="additionco"  maxlength="500" class="dash_txt_box txt_center" style="height:25px; width:400px;">
														Addition <input <?php if($role=='0'){echo 'readonl';}?> type="text" name="additionPer" id="additionPer" value="" placeholder="%" maxlength="3" onkeypress="return _isFloatNumberKey(event)" class="post_ad_txt40 padd_left10 <?php if($addAmount!=""){ echo 'disnone';}?>">
													</td>
													<td style="width:20%;" class="border_right txt_center border_bottom" id="deposit_amount_div">
														<label class="<?php if($addAmount==""){ echo 'disnone';}?>">  <?php echo $addAmount;?></label>
														<input type="text" name="additionAmount" id="additionAmount" value="<?php echo $addAmount;?>" maxlength="20" onkeypress="return _isNumberKey(event)" class="dash_txt_box txt_center <?php if($addAmount!=""){ echo 'disnone';}?>"" style="text-align:center;height:25px; width: 100px;" onchange="_2calDiscount()">
														</td>
														<td style="width:10%;display:<?php if($role=='0'){echo 'none';}?>" class="txt_center border_bottom">
														<a href="billing?rdAdd=<?php echo encryptSoft($ipdId)?>&ipdId=<?php echo encryptSoft($ipdId)?>" onclick="return confirm('Are you sure for delete Addition?')"><i class="fa fa-trash" aria-hidden="true" style="color:#CC0000;"></i></a>
													</td>
												</tr>
												<!--ends---->
												<tr class="grey16 line_hgt25 black_bck ">
													<td style="width:2%;" class="padd_right20 border_right txt_right"></td>
													<td style="width:72%;" colspan="4" class="padd_right20 border_right border_bottom txt_right">
														<input type="text" placeholder="C/O for Discount" value="<?php echo $co; ?>" name="discountco" id="discountco"  maxlength="500" class="dash_txt_box txt_center" style="height:25px; width:400px;" onkeyup="pulsar(this)">
														Discount  <input <?php if($role=='0'){echo 'readonl';}?> type="text" name="discountPer" id="discountPer" value="<?php if($cardDiscount!=""){echo $cardDiscount;}?>" placeholder="%" maxlength="5" onkeypress="return _isFloatNumberKey(event)" onchange="_validateDicount(this.value)" class="post_ad_txt40 padd_left10 <?php if($nodiscount==2){ echo 'hide';}?>">
													</td>
													<td style="width:20%;" class="border_right txt_center border_bottom" id="deposit_amount_div">
														<label class="<?php if($nodiscount==1){ echo 'hide';}?>">  <?php echo $dis_amount1;?></label>
														<input class="<?php if($nodiscount==2){ echo 'hide';}?>"   type="text" name="discountAmount" id="discountAmount" value="<?php echo $dis_amount1;?>" maxlength="7" onkeypress="return _isNumberKey(event)" class="dash_txt_box txt_center" style="text-align:center;height:25px; width: 100px;" onchange="_2calDiscount()">
													</td>
													<td style="width:10%;display:<?php if($role=='0'){echo 'none';}?>" class="txt_center border_bottom" id="grossAmount_div">
														<a href="billing?rd=<?php echo encryptSoft($ipdId)?>&ipdId=<?php echo encryptSoft($ipdId)?>" onclick="return confirm('Are you sure for delete discount?')"><i class="fa fa-trash" aria-hidden="true" style="color:#CC0000;"></i></a>
													</td>
												</tr>
												<?php 
													if($num_settl!=0)
													{?>
													<tr class="grey16 line_hgt25 black_bck ">
														<td style="width:2%;" class="padd_right20 border_right txt_right"></td>
														<td style="width:72%;" colspan="4" class="padd_right20 border_right border_bottom txt_right">Total Deduction on Settlement (Rs) </td>
														<td style="width:20%;" class="border_right txt_center border_bottom" id="grossAmount_div">
															<?php echo number_format($totalDeduction, 2, '.', ',');?>/-
														</td>
														<td style="width:10%;" class="txt_center border_bottom" id="grossAmount_div">
															&nbsp;
														</td>
													</tr>
													<?php
													}
													$depositAmount=0;
													$payment= mysql_query_db("select * from health_payment where ipdId='$ipdId'");
													while($fpayment=mysql_fetch_db($payment)){
														$depositAmount=$depositAmount+$fpayment['creditAmount'];
													}
												?>
												<tr class="grey16 line_hgt25 black_bck ">
													<td style="width:2%;" class="padd_right20 border_right txt_right"></td>
													<td style="width:72%;" colspan="4" class="padd_right20 border_right border_bottom txt_right">Amount Deposited <input type="hidden" name="depositAmount" value="<?php if($depositAmount){ echo $depositAmount;} else { echo "0";}?>" id="deposit_amount"></td>
													<td style="width:20%;" class="border_right txt_center border_bottom" id="deposit_amount_div">
														<?php if($depositAmount){echo $depositAmount;} else { echo "0";}?>/-
													</td>
													<td style="width:10%;" class="txt_center border_bottom" id="grossAmount_div">
														&nbsp;
													</td>
												</tr>
												<?php
													$totaldue=($grossAmount+$addAmount)-$depositAmount;
													$netAmount=$totaldue-$dis_amount1-$totalDeduction;;
												?>
												<tr class="grey16 line_hgt25 black_bck bold">
													<td style="width:2%;" class="padd_right20 border_right txt_right"></td>
													<td style="width:72%;" colspan="4" class="padd_right20 border_right txt_right">Balance Amount (Rs) <input type="hidden" name="FinalNetAmount" id="FinalNetAmount" value="<?php echo $netAmount;?>"></td>
													<td style="width:20%;" class="border_right txt_center" id="FinalNetAmount_div">
														<label id="FinalNetAmount55"><?php echo number_format($netAmount, 2, '.', ',');?></label>/-
													</td>
												</tr>
											</table>
										</div>
										<div class="clearfix20"></div>
										<div class="full">
											<div class="full50 disnone"><input type="submit" name="submit" value="Final Submit" class="dash_btn" style="width: 150px; height:40px;" onclick="return del_prompt(this.form,this.value,'billing-code.php')"></div>
											<div class="full30r">
												<!--  <input type="submit" name="submit" value="Gross Bill" class="dash_btn" style="width:150px; height:40px;">-->
												<input type="submit" name="submit" value="Generate Bill" class="dash_btn" style="width:150px; height:40px;" onclick="return confirm('Are you sure to generate bill ?')">
												&nbsp;<a href="billing-consolidated-print?ipdId=<?php echo encryptSoft($ipdId);?>"><input type="button" name="consolidated" value="Estimate" class="dash_btn" style="width:150px; height:40px;"></a>
											</div>
											<div class="full20r"></div>
										</div>
									</div>
								</div>
								<div class="clearfix20"></div>
							</div>
							<?php }
							else{?>
						</form>
					</div>
				</div>
				<div class="clearfix100"></div>
				<div class="clearfix100"></div>
				<div class="clearfix100"></div>
			<?php }?>
		</div>
		<div class="clearfix10"></div>
		<script>
			$(document).ready(function(){
				$('#add_invoice').on("submit",function(){
					var doa=$("#doa").val();
					var dob=$("input[name=billDate]").val();
					if(dob<doa){
						Swal.fire({
							icon: 'error',
							title: ' Billing date should not be less than Admission date !',
							text: 'Please check Billing date !',
							confirmButtonColor: '#018ED3'
						});
						return false;
					}
					else{
						return true;
					}	
				});
				//alert Code		
				var alertType=$("#alertTypeBox").val();
				//alert(alertType);
				if(alertType==1){
					Swal.fire({
						icon: 'error',
						title: 'IPD is cancelled!',
						text: 'Go to admin to Re-admit',
						confirmButtonColor: '#C82333'
					});
				}
				else if(alertType==2){
					Swal.fire({
						icon: 'info',
						title: ' IPD is Invalid !',
						text: 'Please enter a valid IPD No.',
						confirmButtonColor: '#018ED3'
					});
				}
				else if(alertType==3){
					Swal.fire({
						icon: 'info',
						title: ' IP is Locked !',
						text: 'Go to admin to unlock !',
						confirmButtonColor: '#018ED3'
					});
				}
				else{
					//nothing
				}
				//alert Ends
				$("#discountPer").keyup(function (){
					var per=$("#discountPer").val(); 
					if(per!=""){
						var discountco=per+"%";
						$('#discountco').val(discountco);
						//var amount=$("#FinalNetAmount").val();
						var add=$('#additionAmount').val();
						if(add==""){
							add=0;
						}
						//alert(add);
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						var dis=(amount*per)/100;
						//round off
						dis=Math.round(dis);
						//alert(dis);
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(dis)+parseFloat(deposit)+parseFloat(getMyDeduction));
						//var finalAmount=amount-dis-deposit-getMyDeduction;
						finalAmount=Math.round(finalAmount);
						$('#discountAmount').val(dis);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);
					}
					else{
						//$('#discountAmount').val(0);
						//$('#discountPer').val(0);
						var add=$('#additionAmount').val();
						if(add==""){
							add=0;
						}
						var dis=0;
						//var amount=$("#FinalNetAmount").val();
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(dis)+parseFloat(deposit)+parseFloat(getMyDeduction));
						//var finalAmount=amount-dis-deposit-getMyDeduction;;
						finalAmount=Math.round(finalAmount);
						$('#discountAmount').val(dis);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);
					}
				});
				$("#discountAmount").keyup(function (){
					var dis=$("#discountAmount").val(); 
					var add=$('#additionAmount').val();
					if(add==""){
						add=0;
					}
					if(dis!=""){
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						//var finalAmount=amount-dis;
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(dis)+parseFloat(deposit)+parseFloat(getMyDeduction));
						finalAmount=Math.round(finalAmount);
						//alert(finalAmount);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);
					}
				});
				$("#additionPer").keyup(function(){
					var per=$("#additionPer").val(); 
					if(per!=""){
						var additionco=per+"% Addon";
						$('#additionco').val(additionco);
						//var amount=$("#FinalNetAmount").val();
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						//alert(getMyDeduction);
						var add=(amount*per)/100;
						//round off
						add=Math.round(add);
						//alert(add);
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(deposit)+parseFloat(getMyDeduction));
						finalAmount=Math.round(finalAmount);
						//alert(finalAmount);
						$('#additionAmount').val(add);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);
					}
					else{
						//$('#discountAmount').val(0);
						//$('#discountPer').val(0);
						var add=0;
						//var amount=$("#FinalNetAmount").val();
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(deposit)+parseFloat(getMyDeduction));
						finalAmount=Math.round(finalAmount);
						$('#additionAmount').val(add);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);
					}
				});
				$("#additionAmount").keyup(function (){
					var add=$("#additionAmount").val(); 
					if(add!=""){
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(deposit)+parseFloat(getMyDeduction));
						finalAmount=Math.round(finalAmount);
						$('#additionAmount').val(add);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);
					}
					else{
						var add=0;	
						var amount=$("#grossAmount").val();
						var deposit=$("#deposit_amount").val();
						var getMyDeduction=$("#getMyDeduction").val();
						var finalAmount=(parseFloat(amount)+parseFloat(add))-(parseFloat(deposit)+parseFloat(getMyDeduction));
						finalAmount=Math.round(finalAmount);
						$('#additionAmount').val(add);
						$('#FinalNetAmount55').val(finalAmount);
						$('#FinalNetAmount55').html(finalAmount);	
					}
				});
			});
		</script>
	</div>
</body>
</html>	