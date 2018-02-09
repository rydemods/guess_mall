<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-3";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sql = "SELECT coll_loc,coll_num FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$coll_loc = $row->coll_loc;
	$coll_num = $row->coll_num;
}
pmysql_free_result($result);


//달력을 위한 변수
$todayY = $_POST['todayY'];	//현재 연도
$todayM = $_POST['todayM'];	//현재 월
$todayD = $_POST['todayD'];	//현재 일

if(!($todayY&&$todayM)){
	$todayY = date('Y');
	$todayM = date('m');
}

$todayStr = date("Y-m-d",mktime(0,0,0,$todayM,1,$todayY));

//+1이나 -1을 해서 들어온 날짜에는 십의자리에 0이 붙지 않기 때문에 다시 변환해야 함.
$todayArr = explode("-", $todayStr);
$todayY = $todayArr[0];	//현재 연도
$todayM = $todayArr[1];	//현재 월
$todayD = $todayArr[2];	//현재 일


//특가 상품 정보
$sql_oneday = "SELECT 
					a.*
					,b.productname
					, b.img_i
					, b.img_s
					, b.img_m
					, b.img_l 
					, b.tinyimage
				from tblproductoneday a
				LEFT JOIN tblproduct b on a.productcode = b.productcode
				where a.applydate like '{$todayY}-{$todayM}%'";

$result = pmysql_query($sql_oneday,get_db_conn());
while($row=pmysql_fetch_object($result)){
	$applyDateArr = explode("-",$row->applydate);
	$data[$applyDateArr[2]] = $row;
}

$imagepath=$Dir.DataDir."shopimages/product/";
//debug($data);
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	form=document.form1;
	if(form.up_collyes[0].checked) {
		if(form.up_coll_loc[0].checked!=true && form.up_coll_loc[1].checked!=true && form.up_coll_loc[2].checked!=true) {
			alert("관련상품 위치설정을 하세요.");
			return;
		}
	}
	document.form1.type.value="modify"
	document.form1.submit();
}

function goSubmit(day){
	document.form1.action = "market_onedayprice_regist.php";
	document.form1.todayD.value=day;
	document.form1.submit();
}

function goDetail(day){
	document.form1.action = "market_onedayprice_detail.php";
	document.form1.todayD.value=day;
	document.form1.submit();
}

function goMonth(mode,num){
	var dnum = num;
	
	if(mode=="y"){
		document.form1.todayY.value = dnum;
	}else if(mode=="m"){
		document.form1.todayM.value = dnum;
	}
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅 지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>오늘의 특가 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">오늘의 특가 설정</div>
				</td>
			</tr>
			<form name=form1 id="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type="hidden" name="todayY" value="<?=$todayY?>">
			<input type="hidden" name="todayM" value="<?=$todayM?>">
			<input type="hidden" name="todayD">
			<tr>
				<td>
                <div class="table_style01">
                <div align="center">
                	<a href="javascript:goMonth('y','<?=$todayY-1?>')">&lt;&lt;</a>&nbsp;&nbsp;<a href="javascript:goMonth('m','<?=$todayM-1?>')">&lt;</a>
                	<?=$todayY?>.<?=$todayM?>
                	<a href="javascript:goMonth('m','<?=$todayM+1?>')">&gt;</a>&nbsp;&nbsp;<a href="javascript:goMonth('y','<?=$todayY+1?>')">&gt;&gt;</a>
                </div>
                <!-- 달력테이블 -->
				<TABLE cellSpacing=0 cellPadding=0 width="1260px" border=0 style="border-left:1px solid #b9b9b9;">
				<colgroup>
					<col width="180px"/>
					<col width="180px"/>
					<col width="180px"/>
					<col width="180px"/>
					<col width="180px"/>
					<col width="180px"/>
					<col width="180px."/>
				</colgroup>
				<TR>
					<?php
						$dayname = array("일","월","화","수","목","금","토");
						for($cal_col=0;$cal_col<7;$cal_col++){
					?>
					<TD class="table_cell" align="center"><?=$dayname[$cal_col]?></TD>
					<?php
						}
					?>
				</TR>
					<?php
						$last_day = date("t",strtotime($todayStr));	//총 일수
						$start_week = date("w",strtotime($todayStr)); //시작요일
						$total_week = ceil(($last_day + $start_week)/7);  //총 row  수
						$day = 1;
						for($cal_row=0;$cal_row<$total_week;$cal_row++){
					?>
				<TR>
					<?
						for($cal_col=0;$cal_col<7;$cal_col++){
							//오늘 이전 상품들은 수정 불가하도록 만들기 위한부분
							$todayNum = date('Ymd',mktime(0,0,0,$todayM,$day,$todayY));
							$realToday = date('Ymd');
							
							if($todayNum>$realToday){
								$onclick = "goSubmit('{$day}');";
							}else{
								$onclick = "alert('날짜가 지난 특가는 수정할 수 없습니다.')";
							}
							
					//등록된 특가가 있는지 없는지에 따라 onclick이 지정된 곳이 달라진다.
					if($data[$day]->productcode){
					?>
						<TD class="td_con1" align="" height="90" style="vertical-align: top;cursor: pointer;">
					<?php
					}else{
					?>
						<TD class="td_con1" align="" height="90" style="vertical-align: top;cursor: pointer;" onclick="<?=$onclick?>">
					<?php
					} 
					if(!($cal_row==0&&$cal_col<$start_week)&&$day<$last_day+1){ 
					?>
					<div><?=$day?></div>
					<?php 
							if($data[$day]->productcode){
					?>
					<div style="text-align: center;">
						<!--일별 특가 내용-->
						<table cellSpacing="0" cellPadding="0px" style="border: 0px solid;">
							<tr>
								<td rowspan="3" style="border: 0px solid;" onclick="<?=$onclick?>">
								<?php if($data[$day]->tinyimage){ ?>
									<img src="<?=$imagepath.$data[$day]->tinyimage?>" width="50px">
								<?php }?>
								</td >
								<td style="border: 0px solid;padding: 0px 0px;" onclick="<?=$onclick?>">
									<?php
										$encoding = "EUC-KR";
										$charNumber = "40";
										$productname = mb_strimwidth($data[$day]->productname,0,$charNumber,"...",$encoding);
									?>
									<?=$productname?>
								</td>
							</tr>
							<tr>
								<td style="border: 0px solid;padding: 0px 0px;" onclick="<?=$onclick?>">
									<?php if($data[$day]->dcprice){	?>
									<?=$data[$day]->dcprice?> 원
									<?php }?>
								</td>
							</tr>
							<tr>
								<td style="border: 0px solid;padding: 0px 0px;"><input type="button" value="특가 상세" onclick="goDetail('<?=$day?>');"></td>
							</tr>
						</table>
						<!--일별 특가 내용끝-->
					</div>
					<?php 
							}
						$day++;
						}
					?>
					</TD>
					<?php
							}
					?>
				</TR>
					<?php
						}
					?>
				</TABLE>
				<!-- 달력테이블 끝 -->
                </div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			</form>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>오늘의 특가 설정</span></dt>
							<dd>
							- 달력을 클릭하게 되면 해당 날짜의 특가 상품을 설정 할 수 있는 페이지로 이동됩니다.<br>
							- 날짜가 지난 상품은 수정 할 수 없으며 특가 상세만 볼 수 있습니다.<br>
							- 특가 상세에는 고객들이 문자 예약 내역이나 조르기 내역을 볼 수 있습니다.
							</dd>
	
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php 
include("copyright.php");
