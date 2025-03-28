<?php
	session_start();                                     /* Here session is start */
	require_once("../codelibrary/inc/variables.php"); /* This file is required for database connection.  */
	require_once("../codelibrary/inc/functions.php"); /* This file contain all function.  */
	if(isset($_REQUEST)){ @extract($_REQUEST);}                                  /* This is used for get the request. */
	validate_user();                                     /* Validate user for open/access this page with login. */
	/* log4php start here */
	include('../log4php/Logger.php');
	Logger::configure('../logger-config.xml');
	$logger = Logger::getLogger("-Add IPDhead-");
	if($did){
		$nn="select * from health_manageripdhead where ipdHeadId='$did'";
		$sql= mysql_query_db("select * from health_manageripdhead where ipdHeadId='$did'");
		$line= mysql_fetch_db($sql);
		//$logger->warn("Edit IPD head by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query is-".$nn);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Add Services</title>
        <link rel="icon" href="../images/favicon.png" type="image/png" /> <!-- Title-->
        <link href="../css/style.css" rel="stylesheet" type="text/css">                       <!-- CSS link for design the whole phase-->
        <link href="../awesome/css/font-awesome.css" rel="stylesheet" type="text/css">     <!-- Font awesome link for use font awesome icon-->
        <link href="../awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> <!-- Font awesome link for use font awesome icon-->
	    <script src="../ajax.js"></script>
        <!-- validation css and js start here -->
        <link rel="stylesheet" href="../validation/css/vstyle.css">
        <script src="../validation/js/jquery-1.11.1.min.js"></script>
        <script src="../validation/js/jquery.validate.min.js"></script>
        <script src="../validation/js/additional-methods.min.js"></script>
		<!--Drop box with search box-->
		<script src="../js/global.js"></script>
		<script src="../js/chosen-jquery.js"></script>
		<link href="../css/chosen.css" rel="stylesheet" type="text/css"> 
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
		<script src="../css/bootstrap.min.js"></script>
        <!-- validation css and js end -->
        <script>
            function _isNumberKey(evt){
                var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57))
				return false;
				return true;
			}
		</script>
        <script language="javascript" type="text/javascript">
			function checkall(objForm)
			{
				len = objForm.elements.length;
				var i=0;
				for( i=0 ; i<len ; i++){
					if (objForm.elements[i].type=='checkbox') 
					objForm.elements[i].checked=objForm.check_all.checked;
				}
			}
			function del_prompt(frmobj,comb,id)
			{
				if(comb=='Delete'){
					if(confirm ("Are you sure you want to delete Record(s)")){
						frmobj.action = "del-ipdHead";
						frmobj.submit();
					}
					else{ 
						return false;
					}
				}
				else if(comb=='Deactivate'){
					frmobj.action = "del-ipdHead";
					frmobj.submit();
				}
				else if(comb=='Activate'){
					frmobj.action = "del-ipdHead";
					frmobj.submit();
				}
			}
		</script>                 
	</head>
    <body onload="_startTime()">
        <div class="full">
            <?php include("ipd-dashboard-menu.php");?>
            <div class="clearfix10"></div>
            <div class="full">
                <div class="mauto95">
                    <div class="full">
                        <?php include("mis-sidebar.php");?>
					</div>
                    <div class="full"><!--full80r-->
						<form name="add_department" id="add_department" method="post" action="add-ipdHead-code">
							<input type="hidden" name="did" value="<?php echo $did;?>">
							<input type="hidden" name="csrftoken" value="<?php echo csrf_token();?>">
							<div class="full grey_bck22 box_shadow66">
								<div class="full line_hgt35 pink_bck">
									<div class="mauto95">
										<div class="full">
											<div class="full50 white18"> <?php if($did){?>Edit<?php } else {?>Add<?php }?> IPD Head</div>                                        
										</div>
									</div>
								</div>
								<div class="mauto95">
									<div class="full">
										<div class="clearfix10"></div>
										<div class="full">
											<div class="full30">
												<div class="full20 grey14 line_hgt25"> Head Name</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full75">
													<div class="full">
														<input type="text" name="ipdHeadName" id="ipdHeadName" value="<?php echo ucfirst($line['ipdHeadName']);?>"  placeholder="Head Name" required class="dash_txt_box">
													</div>                                        
												</div>
											</div>
											<div class="full15">
												<div class="full30 grey14 line_hgt25"> Code</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full60">
													<div class="full">
														<input type="text" name="code" id="code" value="<?php echo $line['code'];?>" maxlength="50" placeholder="Code" class="dash_txt_box" required>
													</div>                                        
												</div>
											</div>
											<div class="full20 disnone">
												<div class="full30 grey14 line_hgt25">IRDA Code</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full60">
													<div class="full">
														<input type="text" name="irda_code" id="irda_code" value="<?php if(!empty($line['irda_code'])){echo $line['irda_code'];} else { echo "0";}?>" maxlength="50" placeholder="IRDA" class="dash_txt_box">
													</div>                                        
												</div>
											</div>
											<div class="full25 disnone">
												<div class="full25 grey14 line_hgt25"> IRDA Head</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full55">
													<div class="full">
														<select name="irda_master" id="irda_master" required class="select_txt">
															<option value="">-- Select Head --</option>
															<?php 
																$rlist= mysql_query_db("select * from health_ipd_irda_master where status=1");
																while($frlist= mysql_fetch_db($rlist)) {
																?>
																<option value="<?php echo $frlist['id'];?>"<?php if($frlist['id']==$line['irda_master'] || $frlist['id']=='8'){ echo "selected";}?>><?php echo $frlist['name'];?></option>
															<?php }?>
														</select>
													</div>                                        
												</div>
											</div>
											<div class="full15">
												<div class="full30 grey14 line_hgt25">Rate</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full55">
													<div class="full">
														<input type="text" name="ipdHeadRate" id="ipdHeadRate" value="<?php echo ucfirst($line['ipdHeadRate']);?>" maxlength="8" placeholder="Head Rate" onkeypress="return _isNumberKey(event)" required class="dash_txt_box">
													</div>                                        
												</div>
											</div>
											<div class="full40">
												<div class="full20 grey14 line_hgt25"> Rate List</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full55">
													<div class="full">
														<select name="rateListId" id="rateListId" required class="select_txt">
															<?php 
																$rlist= mysql_query_db("select * from health_ratelist where status=1");
																while($frlist= mysql_fetch_db($rlist)) {
																?>
																<option value="<?php echo $frlist['rateListId'];?>"<?php if($frlist['rateListId']==$line['rateListId']){ echo "selected";}?>><?php echo $frlist['name'];?></option>
															<?php }?>
														</select>
													</div>                                        
												</div>
											</div>
										</div>
										<div class="clearfix10"></div>
										<div class="full">
											<div class="full25">
												<div class="full30 grey14 line_hgt25"> Room Type</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full65">
													<div class="full">
														<select required name="roomId" class="chosen-select select_txt grey14 ">
															<option value="0">Common Charges</option>
															<?php
																$deptt= mysql_query_db("select * from health_roomcategory where roomStatus=1");
																while($fdeptt= mysql_fetch_db($deptt)){
																?>
																<option value="<?php echo $fdeptt['roomCategoryId'];?>"<?php if($fdeptt['roomCategoryId']==$line['roomId']){ echo "selected";}?>><?php echo $fdeptt['roomCategoryName'];?></option>
															<?php }?>    
														</select>
													</div>                                        
												</div>
											</div>
											<div class="full25">
												<div class="full30 grey14 line_hgt25 txt_right"> Priority</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full60">
													<div class="full">
														<select required name="ipdPriority" id="ipdPriority" class="select_txt grey14 chosen-select">
															<option value="">-- Select Priority --</option>					
															<?php
																$priorityValue= mysql_query_db("select * from health_priority where status=1 order by priority ASC");
																while($fpriority= mysql_fetch_db($priorityValue)){
																?>
																<option value="<?php echo $fpriority['priority'];?>"<?php if($fpriority['priority']==$line['priority']){ echo "selected";}?>><?php echo $fpriority['head']; echo "(".$fpriority['priority'].")";?></option>
															<?php }?>    
														</select>
													</div> 
												</div>
											</div>
											<div class="full20">
												<div class="full30 grey14 line_hgt25 txt_right"> TPA/Panel</div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full55">
													<div class="full">
														<select name="tpaId" id="tpaId" required class="select_txt chosen-select">
															<option value="0">-- NA --</option>	
															<?php 
																$rlist= mysql_query_db("select * from health_tpa where status=1");
																while($frlist= mysql_fetch_db($rlist)) {
																?>
																<option value="<?php echo $frlist['id'];?>"<?php if($frlist['id']==$line['tpaId']){ echo "selected";}?>><?php echo $frlist['tpa'];?></option>
															<?php }?>
														</select>
													</div>                                        
												</div>
											</div>
											<div class="full20">
												<div class="full30 grey14 line_hgt25"> Department </div>
												<div class="full5 txt_center line_hgt25"> : </div>
												<div class="full55">
													<select name="departmentId" class="select_txt grey14 chosen-select">
														<option value="">-- Select Department --</option>
														<?php 
															$depar= mysql_query_db("select * from health_department where status=1");
															while($fdepar= mysql_fetch_db($depar)){
															?>    
															<option value="<?php echo $fdepar['id'];?>"<?php if($fdepar['id']==$line['departmentId']){ echo "selected";}?>><?php echo $fdepar['depttName'];?></option>
														<?php }?>
													</select>                                       
												</div>
											</div>
											<div class="full5">
												<input type="submit" name="submit" value=" <?php if($did){?>Edit<?php } else {?>Add<?php }?>" class="dash_btn" style="width:120px">
											</div>
										</div>
										<div class="clearfix10"></div>
									</div>
								</div>
								<div class="clearfix10"></div>
							</div>
						</form>
                        <div class="clearfix20"></div>
                        <div class="full line_hgt35 pink_bck">
							<div class="mauto95">
								<div class="full">
									<div class="clearfix10"></div>
									<div class="full10 printgrey16">Search By</div>  
									<div class="full80 white18 txt_right">	
										<form name="srch_frm" method="post">
											<div class="full20 disnone">
												<select  name="departmentId" class="select_txt grey14">
													<option value="">-- Select Department --</option>
													<?php 
														$depar= mysql_query_db("select * from health_department where status=1");
														while($fdepar= mysql_fetch_db($depar)){
														?>    
														<option value="<?php echo $fdepar['id'];?>"><?php echo $fdepar['depttName'];?></option>
													<?php }?>
												</select> 
											</div>
											<div class="full65" style="margin:0px 5px">
												<div class="full white18" style="text-align:left;line-height:10px">
													<input type="text" name="keyword" value="<?php echo $keyword;?>" class="dash_txt_box150" style="width:400px" placeholder="Name">
													<input type="submit" name="submit" value=" Search" class="dash_btn" style="height: 28px;">
													<?php if($keyword){?>
														<input type="button" name="button" value="Back" class="dash_btn" style="height: 28px;" onclick="location.href='add-ipdHead'">
													<?php }?>
												</div>  
											</div>
										</form> 
									</div>
								</div>
							</div>
						</div>
                        <div class="clearfix10"></div>
                        <?php
							$start = 0;
							if (isset($_GET['start']))
							$start = $_GET['start'];
							$pagesize = 200;
							if (isset($_GET['pagesize']))
							$pagesize = $_GET['pagesize'];
							$order_by = 'ipdHeadId';
							if (isset($_GET['order_by']))
							$order_by = $_GET['order_by'];
							$order_by2 = 'desc';
                            if($keyword){
                                $wh.="where (ipdHeadName like '%$keyword%')";
							}
                            $nn="select * from health_manageripdhead $wh order by by ipdHeadId DESC";
                            //echo "select * from health_manageripdhead $wh order by $order_by $order_by2 limit $start,$pagesize";
							$depart= mysql_query_db("select * from health_manageripdhead $wh order by $order_by $order_by2 limit $start,$pagesize");
                            $reccnt = mysql_num_db(executeQuery("select * from health_manageripdhead $wh"));
							$num= mysql_num_db($depart);
                            if($keyword){
                                //$logger->warn("IPD Head search by ".$_SESSION['sess_uname']." - ".$curDateTime." and IP: ".$ipAddress.", query is-".$nn);
							}
                            if($reccnt>0){
							?>    
							<div class="full">
								<form name="frm_list" method="post">
									<div class="full grey_bck22">
										<div class="full grey16 txt_center line_hgt30">
											<div class="full" style="max-height:750px; overflow-y:scroll;">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tr style="background-color:#ededed; line-height:25px;" class="grey14 ">
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:4%;">S.No.</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:5.3%;">Code</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:12%;">Head Name</td>
														<!--<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:5.3%;">IRDA Code</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;">IRDA Head</td>-->
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;">Deptt Name</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:6%;">Rate List</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:6%;">TPA/Panel</td>
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:5%;">Rate</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:8%;">Room Type</td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;">Priority</td>
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:6%;">Action</td>
													<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:7%;">Status</a></td>                                
													<td class=" grey_brdr_btm txt_center" style=" width:5%;"><input name="check_all" type="checkbox" id="check_all" value="check_all" onClick="checkall(this.form)">&nbsp;</td>
												</tr>
												<?php
													$i=1;
													while($fdepart=mysql_fetch_db($depart))
													{
														//room id
														if($fdepart['roomId']==0)
														{
															$room="Common Charges";
														}
														else{
															$roomId= mysql_query_db("select * from health_roomcategory where roomCategoryId='".$fdepart['roomId']."'");
															$roomline= mysql_fetch_db($roomId);
															$room=$roomline['roomCategoryName'];
														}
														//priority code
														$priorityId= mysql_query_db("select * from health_priority where priority='".$fdepart['priority']."'");
														$priorityDetails= mysql_fetch_db($priorityId);
														$rateListname= mysql_query_db("select * from health_ratelist where rateListId='".$fdepart['rateListId']."' and status=1");
														$frateListname= mysql_fetch_db($rateListname);
														$dptt= mysql_query_db("select * from health_department where id='".$fdepart['departmentId']."'");
														$fdptt= mysql_fetch_db($dptt);
														$rlist= mysql_query_db("select * from health_ipd_irda_master where id='".$fdepart['irda_master']."'");
														$frlist= mysql_fetch_db($rlist);
														$tpaname= mysql_query_db("select * from health_tpa where id='".$fdepart['tpaId']."' and status=1");
														$ftpa= mysql_fetch_db($tpaname);
													?>
													<tr style="background-color:#FFFFFF; line-height:25px;" class="grey14 ">
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:4%;"><?php echo $i;?></td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:5.3%;"><?php echo ucwords($fdepart['code']);?></td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:12%;"><?php echo ucwords($fdepart['ipdHeadName']);?></td>
														<!-- <td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:5.3%;"><?php echo ucwords($fdepart['irda_code']);?></td>
															<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;"><?php echo ucwords($frlist['name']);?></td>
														-->
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;"><?php echo ucwords($fdptt['depttName']);?></td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:8%;"><?php echo ucwords($frateListname['name']);?></td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;"><?php echo ucwords($ftpa['tpa']);?></td>
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:7%;"><?php echo ucwords($fdepart['ipdHeadRate'])." /-";?></td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:12%;"><?php echo $room;?></td>
														<td class="grey_brdr_rght grey_brdr_btm padd_left10" style="width:10%;"><?php echo $fdepart['priority'].". ".$priorityDetails['head'];?></td>
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:6%;"><?php if($fdepart['ipdHeadStatus']==1){?>Active<?php } else if($fdepart['ipdHeadStatus']==0){?>Deactivate<?php }?></td>
														<td class="grey_brdr_rght grey_brdr_btm txt_center" style="width:7%;"><a href="add-ipdHead?did=<?php echo $fdepart['ipdHeadId'];?>" style="text-decoration: none;" class="blue12">Edit</a></td>                                
														<td class=" grey_brdr_btm txt_center" style=" width:5%;"><input type="checkbox" name="ids[]" value="<?php print $fdepart['ipdHeadId']?>"></td>
													</tr>
												<?php  $i++;} ?>
												<tr class="grey16 line_hgt25" style="">
													<td colspan="10" class="grey_brdr_top"><?php include("../codelibrary/inc/paging.inc.php"); ?></td>
													<td class="grey_brdr_top">&nbsp;</td>
												</tr>
											</table>
										</div>
										<div class="full txt_right line_hgt20" style="display:">
											<span class="padd_right20">
												<input type="submit" name="submit" value="Activate" class="button" onclick="return del_prompt(this.form,this.value,'del-ipdHead.php')">
												<input type="submit" name="submit" value="Deactivate" class="button" onclick="return del_prompt(this.form,this.value,'del-ipdHead.php')">
											</span>
										</div>
									</div>
								</form>
							</div>
						<?php }?>
					</div>
				</div>
			</div>
		</div>
        <div class="clearfix10"></div>
        <?php //include("../footer-inc-index.php");?>
	</div>
	<!--end city css js-->
	<script>
        // just for the demos, avoids form submit
        jQuery.validator.setDefaults({
            //debug: true,
            success: "valid"
		});
        $( "#add_department" ).validate({
            ignore: "input:hidden:not(input:hidden.required)",
            rules: {
				field: {
                    required: true
				},
                ipdHeadName: {
                    required: true,
				},
                ipdHeadRate: {
                    required: true,
				},
                ipdHeadType: {
                    required: true,
				},
			},
            errorPlacement: function (error, element) {
                var name = $(element).attr("name");
                error.appendTo($("#" + name + "_validate"));
			}, 
		});
	</script> 
</body>
</html>