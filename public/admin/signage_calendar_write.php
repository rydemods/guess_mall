<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$no=$_REQUEST[no];
$mode=$_REQUEST[mode]?$_REQUEST[mode]:"ins";
$calendarProduct=$_POST["calendarProduct"];
$view_display="none";

switch($mode){

	case "del" : 
						if($no){
							$qry="delete from tblsignage_calendar where no='".$no."'";
							pmysql_query($qry);

							echo "<script>alert('삭제되었습니다.');</script>";
							echo "<script>document.location.href='signage_calendar_list.php';</script>";
						}else{
							echo "<script>alert('오류가 발생하였습니다.');</script>";
							echo "<script>document.location.href='signage_calendar_list.php';</script>";
						}
						
						
						break;
					
	case "ins" : 
						$t_title="등록"; 
						$checked["s_storetype"]["1"]="checked";
						break;	 

	case "mod" : 
						$t_title="수정"; 
						$sql="select * from tblsignage_calendar where no='".$no."'";
						$result=pmysql_query($sql);
						$data=pmysql_fetch_object($result);

						$checked["s_storetype"][$data->s_storetype]="checked";
						
						if($data->s_storetype=="2"){
							$view_display="";

							$s_storesum=explode("@#",$data->s_store);
							if(count($s_storesum)){
								foreach($s_storesum as $k){
									$checked["s_store"][$k]="checked";		
								}
							}
							$checked["s_storetype"][$data->s_storetype]="checked";
						}

						$checked["s_viewyn"][$data->s_viewyn]="checked";

						$bProductSql = "SELECT * ";
						$bProductSql.= "FROM tblproduct ";
						$bProductSql.= "WHERE productcode= '".trim($data->s_productcode)."'";

						$bProductResult = pmysql_query($bProductSql,get_db_conn());
						while($bProductRow = pmysql_fetch_array($bProductResult)){
							$thisProduct[] = $bProductRow;
						}

						

						break;	 
				
	case "submit_ins" : 
						$calendarProduct=$_POST["calendarProduct"];
						$s_storetype=$_POST["s_storetype"];
						if($s_storetype=="1")$s_store="";
						else $s_store=$_POST["s_store"];						
						$s_date=$_POST["s_date"];
						$s_viewyn=$_POST["s_viewyn"]?$_POST["s_viewyn"]:"N";

						
						if($s_store)	$s_storesum=implode("@#",$s_store);

						$sql="insert into tblsignage_calendar (s_productcode, s_store, s_storetype, s_date, s_viewyn, s_regdt) values ('".$calendarProduct[0]."','".$s_storesum."','".$s_storetype."','".$s_date."','".$s_viewyn."',now())";
					
						pmysql_query($sql);

						
						echo "<script>alert('등록되었습니다.');</script>";
						echo "<script>document.location.href='signage_calendar_list.php';</script>";
						break;
					
	case "submit_mod" :  
						$calendarProduct=$_POST["calendarProduct"];
						$s_storetype=$_POST["s_storetype"];
						if($s_storetype=="1")$s_store="";
						else $s_store=$_POST["s_store"];	
						$s_date=$_POST["s_date"];
						$s_viewyn=$_POST["s_viewyn"]?$_POST["s_viewyn"]:"N";
						
						if($s_store)	$s_storesum=implode("@#",$s_store);

						$sql="update tblsignage_calendar set s_productcode='".$calendarProduct[0]."', s_store='".$s_storesum."', s_storetype='".$s_storetype."', s_date='".$s_date."', s_viewyn='".$s_viewyn."' where no='".$no."'";
						pmysql_query($sql);

						echo "<script>alert('수정되었습니다.');</script>";
						echo "<script>document.location.href='signage_calendar_list.php';</script>";
						break;
	
}


#매장정보
$store_sql="select * from tblsignage_store order by name";
$srore_result=pmysql_query($store_sql);

?>

<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">

function store_open(num){
	if(num=="1") $("#store_view").hide();
	else if (num=="2") $("#store_view").show();
}

function checkform(mode){
	$("#mode").val(mode);
	$("#form1").submit();
}
	
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디지털사이니즈 &gt; <span>캘린더 설정 등록/수정</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
<td valign="top">
<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
<tr>
<td>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<col width=240 id="menu_width"></col>
<col width=10></col>
<col width=></col>
<tr>
	<td valign="top">
	<?php include("menu_signage.php"); ?>
	</td>

	<td></td>
	<td>
	<form name=form1 id=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post >
	<table cellpadding="0" cellspacing="0" width="100%">
	
		
		<input type="hidden" name="mode" id="mode" value="<?=$mode?>">
		<input type="hidden" name="no" value="<?=$no?>">

		<?include("layer_prlistPop.php");?>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
							<!-- 페이지 타이틀 -->
							<div class="title_depth3">캘린더 <?=$t_title?></div>
						</td>
					</tr>			
					<tr><td height="20"></td></tr>
					<tr>
						<td>
							<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
									<tr>
										<th><span>노출 매장 선택</span></th>
										<td colspan="3">
											<input type="radio" name="s_storetype" value="1" onclick="store_open('1')" <?=$checked["s_storetype"]["1"]?>> 공통
											<input type="radio" name="s_storetype" value="2" onclick="store_open('2')" <?=$checked["s_storetype"]["2"]?>> 매장	
										</td>
									</tr>
									
									<tr style="display:<?=$view_display?>" id="store_view">
										<th><span>매장 등록</span></th>
										<td colspan="3">
											<?while($store_data=pmysql_fetch_object($srore_result)){?>
												<input type="checkbox" name="s_store[]" value="<?=$store_data->no?>" <?=$checked["s_store"][$store_data->no]?>> <?=$store_data->name?>
											<?}?>
										</td>
									</tr>
									
									<tr>
										<th><span>날짜</span></th>
										<td colspan="3">
											<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=s_date value="<?=$data->s_date?>" class="input_bd_st01">
										</td>
									</tr>
									
									<tr>
										<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','calendarProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
										<td align="left">
											
											<div style="margin-top:0px; margin-bottom: 0px;">							
												<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_calendarProduct">	
												<input type="hidden" name="limit_calendarProduct" id="limit_calendarProduct" value="1"/>								
													<colgroup>
														<col width=20></col>
														<col width=50></col>
														<col width=></col>
													</colgroup>
													
												<?foreach($thisProduct as $ProductKey=>$Product){?>	
													<tr align="center">
														<td style='border:0px'>
															<a name="pro_upChange" style="cursor: hand;">
																<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
															</a>
															<br>
															<a name="pro_downChange" style="cursor: hand;">
																<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
															</a>
														</td>
														<td style='border:0px'>
															<!-- <img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/> -->
															<img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $Product['tinyimage'] );?>" border="1"/>
															<input type='hidden' name='calendarProduct[]' value='<?=$Product[productcode]?>'>
														</td>
														<td style='border:0px' align="left"><?=$Product[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$Product[productcode]?>','calendarProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
														</td>
													</tr>
												<?}?>
												</table>
											</div>
										</td>
									</tr>

									<tr>
										<th><span>노출</span></th>
										<td colspan="3">
											<input type="checkbox" name="s_viewyn" value="Y" <?=$checked["s_viewyn"]["Y"]?>> * 체크시 노출됩니다.
										</td>
									</tr>
									
								</table>
							</div>
						</td>
					</tr>
					<tr><td height=20></td></tr>
					</table>
				</td>
			</tr>
			<tr>
				
				<td align=center>
				
				<?if($mode=="mod"){?>
					<a href="javascript:checkform('submit_mod');"><img src="<?=$Dir."/admin/images/btn_modify_com.gif"?>"></a>
				<?}else if($mode=="ins"){?>
					<a href="javascript:checkform('submit_ins');"><img src="<?=$Dir."/admin/images/btn_confirm_com.gif"?>"></a>
				<?}?>
				</a>
				<a href="signage_calendar_list.php"><img src="<?=$Dir."/admin/images/btn_list_com.gif"?>"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			
			<tr>
				<td>
					<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>메뉴얼</p></div>
						<dl>
							<dt><span>제목제목제목</span></dt>
							<dd>
								  - 내용내용내용  <br />
								  - 내용내용내용 <br />
								  - 내용내용내용
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			
			<tr><td height=20></td></tr>
			<tr><td height="50"></td></tr>
			<tr><td height=20 colspan=2></td></tr>
			
		
	</table>
	</form>
	</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php
include("copyright.php");

?>

</body>
</html>
