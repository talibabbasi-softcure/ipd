<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-IPD Index-");
	////$logger->warn("Login success in OPD by ".$_SESSION['sess_uname']." on date and time is ".$curDateTime." and IP Address is ".$ipAddress);
	/* log4php end here */
	//decode
	$ipdIdAdvance=decryptSoft($ipdIdAdvance);
	if($ipdIdAdvance){
		$_SESSION['msg']="No-Msg";
		$sql_ipd= mysql_query_db("select * from health_ipd_advance where ipdIdAdvance='$ipdIdAdvance'");
		$line_ipd= mysql_fetch_db($sql_ipd);
		$sql= mysql_query_db("select * from health_patient where patientId='".$line_ipd['patientId']."'");
		$line= mysql_fetch_db($sql);
		//$logger->warn("Revisit/Update Patient, ID is ".$ipdId." is edit by ".$_SESSION['sess_uname']." on date and time is ".$curDateTime." and IP Address is ".$ipAddress." and Query is ".$nn);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title><?php echo SITE_TITLE;?></title>
        <link rel="icon" href="../images/favicon.png" type="image/png" /> <!-- Title-->
        <link href="../css/style.css" rel="stylesheet" type="text/css">                       <!-- CSS link for design the whole phase-->
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
		<!-- time start---->
		<script>
			function pulsar(obj) {
				obj.value=obj.value.toUpperCase();
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
			function _isCharKey(event) {
				var key = event.keyCode;
				return ((key >= 65 && key <= 90) || (key >= 97 && key <= 122) || key == 8 || key == 32);
			};
		</script>
		<!-- time end---->
		<!-- select city from ajax-->
		<script>
			function _doctorAjax(val99)
			{
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/doctor-ajax";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="id98="+val99;	
				var strURL = url;
				document.getElementById('doctorIdAjax').innerHTML="<img src='images/loding.gif'>";
				var strResultFunc = "displaysubResult99";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult99(strIn55) 
			{
				document.getElementById('doctorIdAjax').innerHTML=strIn55;
			}
			function _doctorFee(val95)
			{
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/doctorIpdFee-ajax";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="id95="+val95;	
				var strURL = url;
				document.getElementById('doctorFee').innerHTML="<img src='images/loding.gif'>";
				var strResultFunc = "displaysubResult95";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult95(strIn54) 
			{
				document.getElementById('doctorFee').innerHTML=strIn54;
			}
			function _cityAjax(val79)
			{
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/city-ajax";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="id79="+val79;	
				var strURL = url;
				document.getElementById('city-id55').innerHTML="<img src='images/loding.gif'>";
				var strResultFunc = "displaysubResult79";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult79(strIn75) 
			{   
				document.getElementById('city-id55').innerHTML=strIn75;
			}
			function _cityAjaxGaur(val77)
			{
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/city-ajaxgaur";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="id77="+val77;	
				var strURL = url;
				document.getElementById('city-id55gaur').innerHTML="<img src='images/loding.gif'>";
				var strResultFunc = "displaysubResult77";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult77(strIn77) 
			{   
				document.getElementById('city-id55gaur').innerHTML=strIn77;
			}
			function _roomNoAjax(val37, allpat37, rmct)
			{
				//alert(val37);
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/roomNo-ajax";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="id37="+val37+"&allpat37="+allpat37+"&rmct="+rmct;	
				var strURL = url;
				document.getElementById('ttlRoom').innerHTML="<img src='../images/loding.gif'>";
				var strResultFunc = "displaysubResult37";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult37(strIn37) 
			{   strIn37=strIn37.split('^');
				document.getElementById('ttlRoom').innerHTML=strIn37[0];
				document.getElementById('ttlbed').innerHTML=strIn37[1];
			}
			function _bedsNoAjax(val35, allpat)
			{
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/bedNo-ajax";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="id35="+val35+"&allpat="+allpat;	
				var strURL = url;
				document.getElementById('ttlbed').innerHTML="<img src='../images/loding.gif'>";
				var strResultFunc = "displaysubResult35";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult35(strIn35) 
			{   
				document.getElementById('ttlbed').innerHTML=strIn35;
			}
			function _mobileload(val95)
			{
				var n = val95.length;
				if(n<10){
					return false;
				}
				url = document.location.href;
				xend = url.lastIndexOf("/") + 1;
				var base_url = url.substring(0, xend);
				url="../allajax/findpatientipd";
				if (url.substring(0, 4) != 'http') 
				{
					url = base_url + url;		
				}
				var strSubmit="mobile="+val95;	
				var strURL = url;
				document.getElementById('loadautoipd').innerHTML="<img src='../images/loding.gif'>";
				var strResultFunc = "displaysubResult55";
				xmlhttpPost(strURL, strSubmit, strResultFunc)
				return true; 
			}
			function displaysubResult55(strIn54) 
			{
				document.getElementById('loadautoipd').innerHTML=strIn54;
				document.getElementById("loader1").style.display = "block";
			}
		</script>
		<!-- datepicker css and js start here --> 
		<!--
			<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			<link rel="stylesheet" href="/resources/demos/style.css">
			<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		-->
		<link rel="stylesheet" href="../css/jquery-ui.css">
		<script src="../validation/js/jquery.validate.min.js"></script>
		<script src="../validation/js/additional-methods.min.js"></script>
		<script src="../js/jquery-1.12.4.js"></script>
		<script src="../js/jquery-ui.js"></script>
		<!--Drop box with search box-->
		<script src="../js/global.js"></script>
		<script src="../js/chosen-jquery.js"></script>
		<link href="../css/chosen.css" rel="stylesheet" type="text/css">
		<script src="../css/bootstrap.min.js"></script>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
		<script>
			$( function() {
				$( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' }).datepicker("setDate", new Date());
			} );
		</script>
		<!-- datepicker css and js end here --> 
		<style>
			#loader1{
			background-color:rgba(255,255,255,1);
			box-shadow: ;
			position:fixed;
			padding:10px;	
			top:30px;
			left:60%;
			right:20%;
			z-index:2;
			display:none;
			border:1px solid grey;
			height:350px;
			width:450px;
			}	
			#loader2{
			background-color:rgba(255,255,255,1);
			box-shadow: ;
			position:fixed;
			padding:10px;	
			top:0px;
			left:5%;
			right:20%;
			z-index:2;
			display:none;
			border:1px solid grey;
			height:50px;
			width:40px;
			}
		</style>
		<script>
			$(document).ready(function(){
				$("#current").click(function(){
					$("#loader2").css("display","block");	
					$("#loader2").animate({height: "350px",width:"500px"});
				});
			});
		</script>
	</head>
    <body>
		<div id="loader1" style="max-height:350px; overflow-y:scroll;">
			<div id="close1" style="background-color:red;color:#fff;float:right;border-radius:6px;padding:6px; font-size:20px">X
			</div>
			<div>
				<h3 style="background-color:#20d246;color:#fff;font-family:calibri;padding:4px">Find Patient</h3>
				<div class="full" id="loadautoipd">
					<!---autoload--->
				</div>
				<div class="clearfix20"></div>
				<div class="full">                                  
					<div class="full" style="float:right">
						<div class="full">
							<a id="close2" name="submit" class="dash_btn" style="width: 100px; height:30px;padding:4px">Close</a>
						</div>
					</div>				
				</div>
			</div>
		</div>
		<!--second loader--->
		<div id="loader2" style="max-height:350px; overflow-y:scroll;">
			<div id="close3" style="background-color:red;color:#fff;float:right;border-radius:6px;padding:6px; font-size:20px">X
			</div>
			<div>
				<span class="grey14"><?php
					$masterCategory=mysql_query_db("select * from health_roomcategory order by roomCategoryId Asc");
					while($fmasterCategory=mysql_fetch_db($masterCategory)){
						$roomalias=mysql_query_db("select * from health_roomalias where status=1 and roomCategoryId='".$fmasterCategory['roomCategoryId']."' order by id Asc");
					?>
					<h3 style="background-color:#f58b9a;color:#000;font-family:calibri;padding:2px;width:50%"><?php echo "<b>".$fmasterCategory['roomCategoryName']."</b>";?>&nbsp;&nbsp;&nbsp;<img src="../images/room.png" height="15px"></h3>
					<?php
						while($room=mysql_fetch_db($roomalias))
						{
							$findOccupancy=mysql_query_db("select id from health_occupancy where roomId='".$room['id']."' and status=1");
							$num=mysql_num_db($findOccupancy);
							if($num==0){
							?>
							<span style="background-color:#54f728;padding:2px 5px">
							<?php echo $room['alias']; echo "&nbsp;";?></span>
							<?php
							}
							else{
								?><span style="background-color:#ededed;padding:2px 5px">
							<?php echo $room['alias']; echo "&nbsp;";?></span>
							<?php
							}
						}
						echo "<br>";
					}
				?>
				</span>
				<div class="clearfix20"></div>
				<div class="full">                                  
					<div class="full" style="float:right">
						<div class="full">
							<span class="grey14 bold">Green color indicates free room. &nbsp; </span><a id="close4" name="submit" class="dash_btn" style="height:30px;padding:4px">Close</a>
						</div>
					</div>				
				</div>
			</div>
		</div>
		<!--Modal Ends-->
		<div class="full">
			<?php include("ipd-dashboard-menu.php");?>
			<div class="clearfix10"></div>
			<!--php alert-->
			<div class="mauto80 alert alert-danger alert-dismissible disnone" id="php-alert">
				<a href="#" class="close" data-dismiss="alert" aria-label="close" style="padding:0px 10px;font-size:25px;color:#000;float:right">&times;</a>
				<strong>Alert ! <?php echo $_SESSION['validationError'];?> </strong>
			</div>
			<div class="full">
				<div class="mauto95">
					<!--<div class="full18">
						<?php //include("opd-sidemenu.php");?>
					</div>-->
					<script>
						function _showVia(z)
						{  if(z==1)
							{
								document.getElementById('ipdlabel').innerHTML='UHID';
								document.getElementById('via_1').style.display='block';
								} else if(z==5){
								document.getElementById('ipdlabel').innerHTML='TPA Name';
								document.getElementById('via_1').style.display='block';
							}
							else{
								document.getElementById('via_1').style.display='none';
							}
							document.getElementById('modeOfAdmission2').value=z;
							document.getElementById('modeOfAdmission').value=z;
						}
					</script>
					<div class="full grey_bck22 box_shadow66"><!--full80r-->
						<div class="full line_hgt35 newblue_bck">  <!--right_side_head-->
							<div class="mauto95">
								<div class="full">
									<div class="full33 white14">Advance IPD Booking </div>
									<div class="full33">
										<div  class="full printgrey14 line_hgt32 txt_center yellow_bck" style="padding: 2px; font-size:12px;">
											<?php 
												//$modeCount=count($modeOfAdmission);
												//$for_via=$modeCount-1;
											//for($i=1;$i<$modeCount;$i++){?>
											<input type="radio" name="modeOfAdmission22" id="modeOfAdmission22" value="1"<?php if($modeOfAdmission2==1){ echo "checked";}  ?> onclick="_showVia(1)"> Already Registered &nbsp;&nbsp;
											<!--
												<input type="radio" name="modeOfAdmission22" id="modeOfAdmission22" value="2"<?php if(($modeOfAdmission2==2)||($modeOfAdmission2=='')){ echo "checked";}  ?> onclick="_showVia(2)"> GENERAL&nbsp;&nbsp;
												<input type="radio" name="modeOfAdmission22" id="modeOfAdmission22" value="3"<?php if($modeOfAdmission2==3){ echo "checked";}  ?> onclick="_showVia(3)"> EMERGENCY  &nbsp;&nbsp;
												<input type="radio" name="modeOfAdmission22" id="modeOfAdmission22" value="4"<?php if($modeOfAdmission2==4){ echo "checked";}  ?> onclick="_showVia(4)"> Day Care  &nbsp;&nbsp;
												<input type="radio" name="modeOfAdmission22" id="modeOfAdmission22" value="5"<?php if($modeOfAdmission2==5){ echo "checked";}  ?> onclick="_showVia(5)"> VIA TPA&nbsp;&nbsp;
											--->
											<?php //}?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mauto95">
							<div class="full">
								<div class="clearfix20"></div>
								<form name="via_opd" method="get">
									<input type="hidden" name="modeOfAdmission2" id="modeOfAdmission2" value="">
									<div class="full">
										<div class="full white_bck" style="padding:5px; display: none;" id="via_1">
											<div class="full40">
												<div class="full">
													<div class="full25 grey14 line_hgt30"> 
														<label for="ID" id="ipdlabel"></label>
													</div>
													<div class="full5 txt_center line_hgt30"> : </div>
													<div class="full70">
														<div class="full">
															<input type="text" name="Id" id="Id" value="<?php echo $Id;?>" placeholder="Enter UHID" maxlength="8" onkeypress="return _isNumberKey(event)" class="dash_txt_box79 padd_left10">
														</div>
													</div>
												</div>
											</div>
											<div class="full40">
												<input type="submit" name="submit" value="Search" class="dash_btn">
											</div>
										</div>
										<div class="clearfix10"></div>
									</div>
								</form>
								<form name="add_invoice" id="add_invoice" method="post" action="ipd-advance-registration-code" enctype="multipart/form-data">
									<input type="hidden" name="add_invoice" value="1">
									<input type="hidden" name="csrftoken" value="<?php echo csrf_token();?>">
									<!--<input type="hidden" name="puid" value="<?php //echo $puid;?>">-->
									<input type="hidden" name="ipdIdAdvance" value="<?php echo $ipdIdAdvance;?>">
									<input type="hidden" name="uhid" value="<?php echo $Id;?>">
									<?php if($modeOfAdmission2){?>
										<input type="hidden" name="modeOfAdmission" id="modeOfAdmission" value="<?php echo $modeOfAdmission2;?>">
										<?php } else {?>
										<input type="hidden" name="modeOfAdmission" id="modeOfAdmission" value="2">
									<?php }?>
									<?php
										if($modeOfAdmission2){
											//if($modeOfAdmission2==1){
											// $moa=mysql_query_db("select * from health_opd where opdId='$Id'");
											//} else if($modeOfAdmission2==2){
											//   $moa=mysql_query_db("select * from health_tpa where tpaId='$Id'");
											// }
											// $fmoa= mysql_fetch_db($moa);
											$sql=mysql_query_db("select * from health_patient where patientId='$Id'");
											$line= mysql_fetch_db($sql);
											//print_r($line); die;
										}
									?>
									<div class="full">
										<div class="full40">
											<div class="full">
												<div class="full85 line_hgt25 white14 table-header padd_left10">
													<div class="full50">
														Patient Details
													</div>
													<div class="full20r disnone">
														Booking <a href="adv-booking"><img src="../images/booking.png" height="20px"></a>
													</div>
												</div>
												<div class="clearfix10"></div>    
												<div class="full">
													<div class="full25 grey14 line_hgt25">Mobile<span class="mandate" >&nbsp;*</span></div>
													<div class="full5 txt_center line_hgt25"> : </div>
													<div class="full65">
														<div class="full">
															<div class="full65"><input type="text" name="mobileNumber" id="mobileNumber" value="<?php if($line['mobileNumber']){ echo ucwords($line['mobileNumber']);}?>" required  title=" Enter Mobile" placeholder="Mobile Number" maxlength="10" onkeypress="return _isNumberKey(event)" class="dash_txt_box75 padd_left10">
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25"> Department<span class="mandate" >&nbsp;*</span> </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full70">
													<div class="full">
														<select name="departmentId" class="select_txt82 grey14 chosen-select" id="departmentId" required  title=" Select Department" onChange="_doctorAjax(this.value)">
															<option value="">-- Select Department --</option>
															<?php
																$deptt= mysql_query_db("select * from health_department where status=1");
																while($fdeptt= mysql_fetch_db($deptt)){
																?>
																<option value="<?php echo $fdeptt['id'];?>"<?php if($fdeptt['id']==$line_ipd['depttId']){ echo "selected";}?>><?php echo $fdeptt['depttName'];?></option>
															<?php }?>
														</select>
													</div>
													<!--<div class="msg_div" id="department_id_validate"></div>-->
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Consulting Doctor<span class="mandate" >&nbsp;*</span> </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full70">
													<div class="full">
														<div class="full" id="doctorIdAjax">
															<select name="doctorId" class="select_txt82 grey14 chosen-select" id="doctorId" required  title=" Select Doctor Name" onChange="_doctorFee(this.value)">
																<option value="">-- Select Doctor --</option>
																<?php
																	$doctor= mysql_query_db("select * from health_doctors where doctorStatus=1 and doctorCategory='1'");
																	while($fdoctor= mysql_fetch_db($doctor)){
																	?>
																	<option value="<?php echo $fdoctor['id'];?>"<?php if($fdoctor['id']==$line_ipd['doctorId']){ echo "selected";}?>><?php echo $fdoctor['doctorName'];?></option>
																<?php }?>
															</select>
														</div>
														<!--<div class="full33 grey14 line_hgt30" id="doctorFee"></div>-->
													</div>
													<!--<div class="msg_div" id="doctor_id_validate"></div>-->
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Ref. By </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full70">
													<!--<input onkeyup="pulsar(this)"  type="text" name="refDoc" id="refDoc" value="<?php if($line_ipd['refDoc']){ echo ucwords($line_ipd['refDoc']);}?>" maxlength="40" placeholder="Referral Name"  title=" Referral Name" class="select_txt82">-->
													<div class="full">
														<select name="reffDoctorId" class="select_txt82 grey14 chosen-select" id="reffDoctorId">
															<option value="0">-- Select Reff Doctor --</option>
															<?php
																$reffList= mysql_query_db("select * from health_reffered_doctor where reffStatus=1 order by reffName asc");
																while($freffList= mysql_fetch_db($reffList)){
																?>
																<option value="<?php echo $freffList['reffId'];?>"<?php if($freffList['reffId']==$line['reffDoctorId']){ echo "selected";}?>><?php echo $freffList['reffName'];?></option>
															<?php }?>
														</select>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25"> Patient's Name<span class="mandate" >&nbsp;*</span> </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full70">
													<div class="full">
														<div class="full25">
															<select name="patientInitial" class="select_txt82 grey14">
																<?php 
																	for($i=1;$i<count($nameInitialArr);$i++){
																	?>    
																	<option value="<?php echo $nameInitialArr[$i];?>"<?php if($nameInitialArr[$i]==$line['patientInitial']){ echo "selected";}?>><?php echo $nameInitialArr[$i];?></option>
																<?php }?>
															</select>
														</div>
														<div class="full70">
															<div class="full">
																<input onkeyup="pulsar(this)"  type="text" name="patientName" id="patientName" value="<?php if($line['patientName']){ echo ucwords($line['patientName']);}?>" maxlength="40" placeholder="Patient's Name" required  title=" Enter Patient Name" class="dash_txt_box83" <?php if(isset($Id)){ echo "readonly";}?>  <?php if(isset($Id)){echo "style='background-color:grey;color:#fff;'";}?>  onkeypress="return _isCharKey(event)">
															</div>
															<!--<div class="msg_div" id="patientName_validate"></div> -->
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25"> S/o/D/o/W/o Name <span class="mandate" >&nbsp;*</span> </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full70">
													<div class="full">
														<div class="full25">
															<select name="fatherHusInitial" class="select_txt82 grey14" id="fatherHusInitial" required>
																<?php 
																	for($j=1;$j<count($nameInitialArrL);$j++){
																	?>
																	<option value="<?php echo $nameInitialArrL[$j];?>"<?php if($nameInitialArrL[$j]==$line['fatherHusInitial']){ echo "selected";}?>><?php echo $nameInitialArrL[$j];?></option>
																<?php }?>
															</select>
														</div>
														<div class="full70">
															<div class="full">
																<input onkeyup="pulsar(this)"  type="text" name="fatherHusName" id="fatherHusName" value="<?php if($line['fatherHusName']){ echo ucwords($line['fatherHusName']);}?>" maxlength="40" placeholder="Father/Husband's Name" required  title=" Enter Father Name" class="dash_txt_box83" <?php if(isset($Id)){ echo "readonly";}?>  <?php if(isset($Id)){echo "style='background-color:grey;color:#fff;'";}?> onkeypress="return _isCharKey(event)" >
															</div>
															<!--<div class="msg_div" id="father_name_validate"></div>-->
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full">
													<div class="full">
														<div class="full25 grey14 line_hgt25">Age/Gender/Marital<span class="mandate" >&nbsp;*</span></div>
														<div class="full5 txt_center line_hgt25"> : </div>
														<div class="full70">
															<div class="full">
																<div class="full">
																	<input type="text" name="age" id="age" value="<?php if($line['age']){ echo ucwords($line['age']);}?>" required  title=" Enter Age" placeholder="Age" maxlength="5" onkeypress="rreturn _isNumberKey(event)" class="post_ad_txt70 padd_left10">
																	<select name="age_type" class="post_ad_sel90 grey14" style="width:40px">
																		<?php
																			$count25=count($agetype);
																			for($i=1;$i<$count25;$i++){
																			?>
																			<option value="<?php echo $agetype[$i];?>"<?php if($agetype[$i]==$line['age_type']){ echo "selected";}?>><?php echo $agetype[$i];?></option>
																		<?php }?>
																	</select>
																	<select name="gender" class="post_ad_sel90 grey14" style="width:60px">
																		<?php
																			$count22=count($genderArray);
																			for($i=1;$i<$count22;$i++){
																			?>
																			<option value="<?php echo $genderArray[$i];?>"<?php if($genderArray[$i]==$line['gender']){ echo "selected";}?>><?php echo $genderArray[$i];?></option>
																		<?php }?>
																	</select>
																	<select style="width:90px" name="marritalStatus" id="marritalStatus" required  title="Marital Status" class="post_ad_sel90 grey14">
																		<?php 
																			for($i=0;$i<count($marritalStatus);$i++){
																			?>    
																			<option value="<?php echo $marritalStatus[$i];?>"<?php if($marritalStatus[$i]==$line['marritalStatus']){ echo "selected";}?>><?php echo $marritalStatus[$i];?></option>
																		<?php }?>
																	</select>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25"> Address<span class="mandate" >&nbsp;*</span> </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<textarea onkeyup="pulsar(this)"  name="address" id="address" required placeholder="Patient Address" title=" Enter Address" maxlength="200" class="txt_area_add"><?php if($line['address']){ echo ucwords($line['address']);}?></textarea>
													</div>
												</div>
											</div>
											<div class="full" style="display:none;">
												<div class="full25 grey14 line_hgt25">State</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<div class="full">
															<select name="stateId" class="select_txt82 grey14" id="stateId" title=" Select State" onchange="_cityAjax(this.value)">
																<option value="">-- Select State --</option>
																<?php
																	$stateId= mysql_query_db("select * from health_states where country_id=101");
																	while($fstateId= mysql_fetch_db($stateId)){
																	?>
																	<option value="<?php echo $fstateId['id'];?>"<?php if($fstateId['id']==$line['stateId']){ echo "selected";}?>><?php echo ucfirst($fstateId['name']);?></option>
																<?php }?>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="full" style="display:none;">
												<div class="full25 grey14 line_hgt25">City</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<div class="full" id="city-id55">
															<select name="cityId" class="select_txt82 grey14" id="cityId"  title=" Select City">
																<option value="">-- Select City --</option>
																<?php
																	$cityId= mysql_query_db("select * from health_cities where state_id='".$line['stateId']."'");
																	while($fcityId= mysql_fetch_db($cityId)){
																	?>
																	<option value="<?php echo $fcityId['id'];?>"<?php if($fcityId['id']==$line['cityId']){ echo "selected";}?>><?php echo ucfirst($fcityId['name']);?></option>
																<?php }?>
															</select>
														</div>
														<!--<div class="msg_div" id="cityId_validate"></div>-->
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Pin Code</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full50">
														<div class="full"><input type="text" name="patientZipcode" id="patientZipcode" value="<?php echo $line['patientZipcode'];?>"  title=" Enter Zip Code" placeholder="Pin Code" maxlength="6" onkeypress="return _isNumberKey(event)" class="dash_txt_box79 padd_left10"></div>
														<!--<div class="msg_div" id="zipCode_validate"></div>-->
													</div>
													<div class="full50">
														<div class="full20 grey14 line_hgt25">Blood</div>
														<div class="full5 txt_center line_hgt25"> : </div>
														<div class="full50">
															<select name="bloodGroup" id="bloodGroup" required title=" Select Blood Group" class="select_txt71">
																<option value="NA" selected>NA</option>
																<?php 
																	for($i=1;$i<count($bloodGroupArr);$i++){
																	?>
																	<option value="<?php echo $bloodGroupArr[$i];?>"<?php if($line['bloodGroup']==$bloodGroupArr[$i]){ echo "selected";}?>><?php echo $bloodGroupArr[$i];?></option>
																<?php }?>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full">
													<div class="full25 grey14 line_hgt25">Patient Image</div>
													<div class="full5 txt_center line_hgt25"> : </div>
													<div class="full65">
														<div class="full">
															<div class="full80 border_all padd3 white_bck">
																<input type="file" name="patientImage">
															</div>
															<?php if($line['patientImage']){?>
																<div class="clearfix7"></div>
																<div class="full">
																	<?php  //echo '<img src="data:image;base64,'.$line['patientImage'].'" width="100" height="100">'; ?>
																</div>
															<?php }?>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
										</div>
										<div class="full40">
											<div class="full85 line_hgt25 white14 table-header padd_left10">Payer Details</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Payer Name</div>
												<div class="full5 txt_center line_hgt25 grey14"> : </div>
												<div class="full65">
													<select name="payer" class="select_txt82 grey14 chosen-select" id="payer"   title=" Select Payer">
														<option value="0">-- NA --</option>
														<?php
															$deptt= mysql_query_db("select * from health_tpa where status=1");
															while($fdeptt= mysql_fetch_db($deptt)){
															?>
															<option value="<?php echo $fdeptt['id'];?>"<?php if($fdeptt['id']==$line_ipd['payer']){ echo "selected";}?>><?php echo $fdeptt['tpa'];?></option>
														<?php }?>
													</select>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Card No.</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full65">
														<div class="full"><input type="text" name="cardNo" id="cardNo" value="<?php echo $line['custom1'];?>" placeholder="Card No." maxlength="100" class="dash_txt_box75 padd_left10"></div>
														<!--<div class="msg_div" id="phoneNumber_validate"></div>-->
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Service No.</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full65">
														<div class="full"><input type="text" name="refNo" id="refNo" value="<?php echo $line['custom2'];?>" placeholder="Service No." maxlength="100" class="dash_txt_box75 padd_left10"></div>
														<!--<div class="msg_div" id="phoneNumber_validate"></div>-->
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Rank</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full65">
														<div class="full"><input type="text" name="rank" id="rank" value="<?php echo $line['custom3'];?>" placeholder="Rank" maxlength="200" class="dash_txt_box75 padd_left10"></div>
														<!--<div class="msg_div" id="phoneNumber_validate"></div>-->
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Rate List</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full30">
													<div class="full">
														<select name="rateListId" id="rateListId" required class="select_txt">
															<?php 
																$rlist= mysql_query_db("select * from health_ratelist where status=1");
																while($frlist= mysql_fetch_db($rlist)) {
																?>
																<option value="<?php echo $frlist['rateListId'];?>"<?php if($frlist['rateListId']==$line_ipd['rateListId']){ echo "selected";}?>><?php echo $frlist['name'];?></option>
															<?php }?>
														</select> 
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Job</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<div class="full grey14 line_hgt25">
															<input type="radio" name="jobType" id="tpaCategory1" value="1" <?php if($line_ipd['jobType']=='1' || !isset($line_ipd['jobType'])){?>checked<?php }?>>Service 
															<input type="radio" name="jobType" id="tpaCategory2" value="2"<?php if($line_ipd['jobType']=='2'){?>checked<?php }?>> Retired   
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full25 grey14 line_hgt25">Billing</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<div class="full grey14 line_hgt25">
															<input type="radio" name="billingCategory" id="billingCategory1" value="1" <?php if($ffinnace['billingCategory']=='1' || !isset($ffinnace['billingCategory'])){?>checked<?php }?>> Cash 
															<input type="radio" name="billingCategory" id="billingCategory2" value="2" <?php if($ffinnace['billingCategory']=='2'){?>checked<?php }?>> Credit  
														</div>
													</div>
												</div>
											</div>
											<div class="full" style="display:none">
												<div class="full25 grey14 line_hgt25">State</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<div class="full">
															<select name="guardianState" class="select_txt82 grey14" id="guardianState" onchange="_cityAjaxGaur(this.value)">
																<option value="">-- Select State --</option>
																<?php
																	$stateId= mysql_query_db("select * from health_states where country_id=101");
																	while($fstateId= mysql_fetch_db($stateId)){
																	?>
																	<option value="<?php echo $fstateId['id'];?>"<?php if($fgardetails['guardianState']==$fstateId['id']){ echo "selected";}?>><?php echo ucfirst($fstateId['name']);?></option>
																<?php }?>
															</select>
														</div>
														<!-- <div class="msg_div" id="stateId_validate"></div>-->
													</div>
												</div>
											</div>
											<div class="full"  style="display:none">
												<div class="full25 grey14 line_hgt25">City</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<div class="full" id="city-id55gaur">
															<select name="guardianCity" class="select_txt82 grey14" id="guardianCity">
																<option value="">-- Select City --</option>
																<?php
																	$cityId= mysql_query_db("select * from health_cities where state_id='".$fgardetails['guardianState']."'");
																	while($fcityId= mysql_fetch_db($cityId)){
																	?>
																	<option value="<?php echo $fcityId['id'];?>"<?php if($fgardetails['guardianCity']==$fcityId['id']){ echo "selected";}?>><?php echo ucfirst($fcityId['name']);?></option>
																<?php }?>
															</select>
														</div>
														<!--<div class="msg_div" id="cityId_validate"></div>-->
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="clearfix10"></div>
										</div>
										<div class="full20">
											<div class="full pink_bck">
												<div class="mauto95">
													<div class="full35 white14 line_hgt25">Booking Date</div>
													<div class="full5 txt_center line_hgt25 white14"> : </div>
													<div class="full60">
														<?php $editdate="datepicker"; ?>
														<input readonly type="text" name="ipd_cur_date" id="<?php if($ipdId==''){echo $editdate;} ?>" value="<?php if($line_ipd['dateTime']){ echo $line_ipd['dateTime']; }else{echo $today;} ?>" placeholder="Select Date" required class="dash_txt_box" style="width:148px; height:22px; ">
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full">
												<div class="full97 line_hgt25 white14 pink_bck padd_left10">Allocation</div>
												<div class="clearfix10"></div>
												<div class="full" id="roomNo">
													<div class="clearfix10"></div>
													<div class="full">
														<div class="full30 grey14 line_hgt25">Allocation</div>
														<div class="full5 txt_center line_hgt25"> : </div>
														<div class="full65">
															<div class="full">
																<div class="full">													<!--onchange="_roomBedSelect(this.value), _roomNoAjax(this.value, '<?php //echo $allpat;?>', '<?php //echo $roomcateg;?>')">-->
																	<select name="categoryAllocation" class="select_txt grey14" id="category">
																		<option value="">-- Select Category --</option>
																		<?php
																			$roomcat= mysql_query_db("select * from health_roomcategory where roomStatus=1 order by roomCategoryId asc");
																			while($froomcat= mysql_fetch_db($roomcat)){
																			?>
																			<option value="<?php echo $froomcat['roomCategoryId'];?>"<?php if($line_ipd['allocationId']==$froomcat['roomCategoryId']){ echo "selected";}?>><?php echo $froomcat['roomCategoryName'];?></option>
																		<?php }?>
																	</select>
																</div>
															</div>
														</div>
													</div>
													<div class="full" id="roomBed" style="display:none">
														<div class="clearfix10"></div>
														<div class="full30 grey14 line_hgt25">Unit No.</div>
														<div class="full5 txt_center line_hgt25"> : </div>
														<div class="full65">
															<div class="full">
																<div class="full" id="ttlRoom">
																	<select name="roomNo" class="select_txt grey14" id="roomNo" title=" Select Room No">
																		<option value="<?php if(isset($falloc55['roomNo'])){echo $falloc55['roomNo'];}?>"> <?php if(isset($falloc55['roomNo'])){echo $froom55['alias'];}?></option>
																	</select>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix10"></div>
											<div class="full line_hgt25 grey14 padd_left10">
												<div class="full25">Nationality</div> 
												<div class="full10 txt_center line_hgt25"> : </div>
												<div class="full65">
													<select name="patientNationality" id="patientNationality" required class="select_txt grey14 chosen-select">
														<?php //for($i=0;$i<count($patientNationality);$i++)
															$cntry= mysql_query_db("select * from health_countries");
															while($fcntry= mysql_fetch_db($cntry)) {
															?>
															<option value="<?php echo $fcntry['id'];?>"<?php if($fcntry['id']==$line['patientNationality']){ echo "selected";} else if($fcntry['id']==101){ echo "selected";}?>><?php echo $fcntry['name'];?></option>
															<!--<option value="<?php //echo $patientNationality[$i];?>"<?php //if($line['patientNationality']==$patientNationality[$i]){ echo "selected";}?>><?php //echo $patientNationality[$i];?></option>-->
														<?php }?>
													</select> 
												</div>
											</div>
											<div class="clearfix20" style="height:20px;"></div>
											<div class="full grey14 line_hgt25 txt_right">
												<input type="submit" name="submit" value=" <?php if($ipdIdAdvance!=''){?>Update<?php } else {?>Register<?php }?>" class="dash_btn" id="submit_btn"> 
											</div>
										</div>
									</div>
								</form>		   
							</div>
						</div>
						<div class="clearfix5"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix10"></div>
		<?php include("../footer-inc.php");?>
	</div>
	<!--end city css js-->
	<!--<script src="../validation/js/jquery-1.11.1.min.js"></script>-->
	<script>
		$(document).ready(function() {
			// validation code
			var validationError = <?php echo json_encode($_SESSION['validationError']) ?>;
			validationError=$.trim(validationError);
			if(validationError!=''){ 
				$("#php-alert").css("display", "block"); 
				//$("#email").focus()
			}
			var formValues = <?php echo json_encode($_SESSION['formValues']) ?>;
			//console.log(formValues);
			$.each(formValues, function(key, value) {
				//console.log(key);
				$('#'+key).val(value);
			});
			//alert("jj");
			$('#tpaCategory2').click(function() {
				$('#billingCategory2').attr('checked', true);
				//$('#paymentMode').attr("readonly", "true"); 
			});
			$('#tpaCategory1').click(function() {
				$('#billingCategory1').attr('checked', true);
			});
			$("#close1").click(function(){
				$("#loader1").css("display","none");
			});
			$("#close2").click(function(){	
				$("#loader1").css("display","none");
			});
			$("#close3").click(function(){
				$("#loader2").css("display","none");
			});
			$("#close4").click(function(){	
				$("#loader2").css("display","none");
			});
			$('#add_invoice').on("submit",function(){
				//$("#submit_btn").attr("disabled","true").val("Processing...").css("background-color","rgba(123,123,123)");
			});
		});
	</script> 
	<style>
		.error{
		font-size:10px;
		}
	</style>
	<script src="../validation/js/jquery.validate.min.js"></script>
	<script src="../validation/js/additional-methods.min.js"></script>
	<script>
		// just for the demos, avoids form submit
		jQuery.validator.setDefaults({
			//debug: true,
			//success: "valid"
		});
		$( "#add_invoice" ).validate({
			ignore: "input:hidden:not(input:hidden.required)",
			rules: {
				email: {
					email: true,
				},
			},
			submitHandler: function() {
				$("#submit_btn").attr("disabled","true").val("Processing...").css("background-color","rgba(123,123,123)");
				return true;
			},
		});
	</script>
	<?php //  unset validation sessions
		unset($_SESSION['formValues']); 
		unset($_SESSION['validationError']);
	?>
</body>
</html>	