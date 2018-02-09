<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_reserveuse=$_POST["up_reserveuse"];
$up_money=$_POST["up_money"];
$up_remoney=$_POST["up_remoney"];
$up_reprice=$_POST["up_reprice"];
$up_reserve_join=$_POST["up_reserve_join"];
$up_canuse=$_POST["up_canuse"];
$up_e_canuse=$_POST["up_e_canuse"];
$up_reserve_maxprice=$_POST["up_reserve_maxprice"];
$up_e_reserve_maxprice=$_POST["up_e_reserve_maxprice"];
$up_usecheck=$_POST["up_usecheck"];
$up_reservemoney=$_POST["up_reservemoney"];
$up_reservepercent=$_POST["up_reservepercent"];
$up_point_cut=$_POST["up_point_cut"];
$up_point_updown=$_POST["up_point_updown"];

$up_rcall_type=$_POST["up_rcall_type"];

$st_per=$_POST["st_per"];
$en_per=$_POST["en_per"];
$ins_per=$_POST["ins_per"];
$brand_idx=$_POST["brand_idx"];


if($up_usecheck==1) $reserve_limit=0;
else if($up_usecheck==2) $reserve_limit=$up_reservemoney;
else if($up_usecheck==3) $reserve_limit=-$up_reservepercent;
else $reserve_limit=0;

/* 포인트 지급설정*/
$agree_point=$_POST[agree_point]?$_POST[agree_point]:0;
$app_point=$_POST[app_point]?$_POST[app_point]:0;
$protext_down_point=$_POST[protext_down_point]?$_POST[protext_down_point]:0;
$protext_up_point=$_POST[protext_up_point]?$_POST[protext_up_point]:0;
$poto_point=$_POST[poto_point]?$_POST[poto_point]:0;
$over_point=$_POST[over_point]?$_POST[over_point]:0;
$proreview_point=$_POST[proreview_point]?$_POST[proreview_point]:0;
$mody_one_point=$_POST[mody_one_point]?$_POST[mody_one_point]:0;
$mody_two_point=$_POST[mody_two_point]?$_POST[mody_two_point]:0;
$mody_thr_point=$_POST[mody_thr_point]?$_POST[mody_thr_point]:0;

if ($type=="up") {


	// 적립금 관련 설정 및 쿠폰 사용 여부, 적립금/쿠폰 동시 사용여부를 저장한다.
	if($up_rcall_type=="Y" && $up_money=="Y") $up_rcall_type="Y";
	else if($up_rcall_type=="N" && $up_money=="Y") $up_rcall_type="N";
	else if($up_rcall_type=="Y" && $up_money=="N") $up_rcall_type="M";
	else if($up_rcall_type=="N" && $up_money=="N") $up_rcall_type="T";

	if($up_remoney=="Y") $reserve_useadd=-1;
	else if($up_remoney=="U") $reserve_useadd=-2;
	else if($up_remoney=="A") $reserve_useadd=0;
	else $reserve_useadd = $up_reprice;

	if ($up_reserveuse == "N") {#적립금 사용하지 않음
		$sets = " reserve_join = 0, reserve_maxuse = -1, e_reserve_maxuse = '-1' ";
	} else {
		$sets = " reserve_join = '{$up_reserve_join}', reserve_maxuse = '{$up_canuse}', e_reserve_maxuse = '{$up_e_canuse}' ";
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "rcall_type		= '{$up_rcall_type}', ";
	$sql.= "reserve_limit	= '{$reserve_limit}', ";
	$sql.= "reserve_maxprice= '{$up_reserve_maxprice}', ";
	$sql.= "e_reserve_maxprice= '{$up_e_reserve_maxprice}', ";
	$sql.= "reserve_useadd	= '{$reserve_useadd}', ";
	$sql.= "point_cut	= '{$up_point_cut}', ";
	$sql.= "point_updown	= '{$up_point_updown}', ";
	$sql.= $sets;
	pmysql_query($sql,get_db_conn());

	
	foreach($brand_idx as $bi){
		pmysql_query("delete from tblproductbrand_point where bridx='".$bi."'");
		$br_count=count($st_per[$bi]);
		for($i=0;$i<$br_count;$i++){
			$start_per = $st_per[$bi][$i] ? $st_per[$bi][$i] : "0";
			$end_per = $en_per[$bi][$i] ? $en_per[$bi][$i] : "0";
			$insert_per = $ins_per[$bi][$i] ? $ins_per[$bi][$i] : "0";
			
			pmysql_query("insert into tblproductbrand_point (bridx, st_per, en_per, ins_per, point_date) values ('".$bi."','".$start_per."','".$end_per."','".$insert_per."','".date("YmdHis")."')");
		}
	}

	//로그인, 리뷰, 게시글 작성 시 적립금 적용기준을 파일로 저장한다.
    $f = fopen($Dir."conf/config.point.new.php","w");
	fwrite($f,"<?\n");
	fwrite($f,"\$pointSet_new['agree_point'] = '$agree_point'; \n");
	fwrite($f,"\$pointSet_new['app_point'] = '$app_point'; \n");
	fwrite($f,"\$pointSet_new['protext_down_point'] = '$protext_down_point'; \n");
	fwrite($f,"\$pointSet_new['protext_up_point'] = '$protext_up_point'; \n");
	fwrite($f,"\$pointSet_new['poto_point'] = '$poto_point'; \n");
	fwrite($f,"\$pointSet_new['over_point'] = '$over_point'; \n");
	fwrite($f,"\$pointSet_new['proreview_point'] = '$proreview_point'; \n");
	fwrite($f,"\$pointSet_new['mody_one_point'] = '$mody_one_point'; \n");
	fwrite($f,"\$pointSet_new['mody_two_point'] = '$mody_two_point'; \n");
	fwrite($f,"\$pointSet_new['mody_thr_point'] = '$mody_thr_point'; \n");
	fwrite($f,"?>\n");
	fclose($f);
	@chmod($Dir."conf/config.point.new.php",0777);

	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('포인트 관련 설정이 완료되었습니다.');location.href='shop_reserve.php'; }</script>\n";

	$log_content = "## 적립금설정 ## - 사용여부 : $up_reserveuse, 가입적립금 : $up_reserve_join, 포인트 $up_canuse 이상 사용가능, E포인트 $up_e_canuse 이상 사용가능, 추가적립기준:$reserve_useadd, 적립금/쿠폰 동시사용여부 :$up_rcall_type";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

$sql2 = "SELECT rcall_type,reserve_limit,reserve_maxprice,e_reserve_maxprice,reserve_useadd,reserve_maxuse,e_reserve_maxuse,reserve_join,coupon_ok,point_cut,point_updown ";
$sql2.= "FROM tblshopinfo ";
$result = pmysql_query($sql2,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
	$reserve_join = $row->reserve_join;
	if ($row->reserve_maxuse ==-1) {
		$reserveuse = "N";
		$canuse = 0;
	} else {
		$reserveuse = "Y";
		$canuse = abs($row->reserve_maxuse);
	}
	if ($row->e_reserve_maxuse ==-1) {
		$e_canuse = 0;
	} else {
		$e_canuse = abs($row->e_reserve_maxuse);
	}
	if ($row->rcall_type=="Y") {
		$rcall_type = $row->rcall_type;
		$money="Y";
	} else if ($row->rcall_type=="N") {
		$rcall_type = $row->rcall_type;
		$money="Y";
	} else if ($row->rcall_type=="M") {
		$rcall_type="Y";
		$money="N"; 
	} else {
		$rcall_type="N";
		$money="N";
	}
	$reserve_limit = $row->reserve_limit;
	$reserve_maxprice = $row->reserve_maxprice;
	$e_reserve_maxprice = $row->e_reserve_maxprice;
	$coupon_ok = $row->coupon_ok;

	if($row->reserve_useadd==-1){
		$remoney="Y";
		$reprice="0";
	}else if($row->reserve_useadd==-2){
		$remoney="U";
		$reprice="0";
	}else if($row->reserve_useadd==0){
		$remoney="A";
		$reprice="0";
	}else {
		$remoney="N";
		$reprice=$row->reserve_useadd;
	}
}
pmysql_free_result($result);

${"check_reserveuse".$reserveuse} = "checked";
${"check_money".$money} = "checked";
${"check_remoney".$remoney} = "checked";
${"check_rcall_type".$rcall_type} = "checked";
$checked["up_point_updown"][$row->point_updown] = "selected";
$checked["up_point_cut"][$row->point_cut] = "selected";



$brand_sql = "SELECT a.*, b.brandname, b.productcode_a, b.bridx, b.staff_rate, b.coupon_useper FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender ";
$brand_sql.= "ORDER BY a.disabled ASC, a.vender DESC, lower(b.brandname) DESC ";
$brand_result=pmysql_query($brand_sql);

?>

<?php 
include("header.php");
include_once($Dir."conf/config.point.new.php");
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	if(form.up_remoney[3].checked){
		if(isNaN(form.up_reprice.value)){
			alert('숫자만 입력하시기 바랍니다.');
			form.up_reprice.focus();
			return;
		}
		if(parseInt(form.up_reprice.value)<=0){
			alert('금액은 0원 이상 입력하셔야 합니다.');
			form.up_reprice.focus();
			return;
		}
	}

	form.type.value="up";
	if (confirm("적용하시겠습니끼?")) {
		form.submit();
	}
}

function add(brand){
	var row = "<tr>";
		row += "<td  style=\"border:0px;\">할인율 <input type=text name=\"st_per["+brand+"][]\" value=\"\" size=10 class=\"input\">% ~ <input type=text name=\"en_per["+brand+"][]\" value=\"\" size=10 class=\"input\">% 이하 <input type=text name=\"ins_per["+brand+"][]\" value=\"\" size=10 class=\"input\">% 포인트 적립 &nbsp;&nbsp;&nbsp;<span style='cursor:pointer'><img src='images/btn_del.gif' align=absmiddle></span></td>";
		row += "</tr>";
	$("#table_"+brand).append(row);

}

$(function() {
	$(".table").on("click", "span", function() {
		$(this).closest("tr").remove();
	});
});

function del(){
	$(this).closest("tr").remove();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 운영설정 &gt;<span>포인트 정책설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">포인트 정책설정</div>
				</td>
			</tr>
			
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">포인트 정책설정</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>포인트 사용여부*</span></th>
					<TD class="td_con1"><input type=radio id="idx_reserveuse1" name=up_reserveuse value="Y" <?=$check_reserveuseY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserveuse1>사용함&nbsp;&nbsp;&nbsp;&nbsp;: 누적된 적립금을 구매 결제시 공제</label><br>
					<input type=radio id="idx_reserveuse2" name=up_reserveuse value="N" <?=$check_reserveuseN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserveuse2>사용안함 : 주문시에 사용가능한 누적 적립금 및 사용금액 입렵항목이 미표시</label>					</TD>
				</TR>
				<tr>
					<th><span>포인트/쿠폰 동시 사용여부*</span></th>
					<td>
						<input type=radio id="idx_rcall_type1" name=up_rcall_type value="Y" <?=$check_rcall_typeY?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_rcall_type1>동시 사용</label>  &nbsp;&nbsp;<input type=radio id="idx_rcall_type2" name=up_rcall_type value="N" <?=$check_rcall_typeN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_rcall_type2>동시 사용안함</label>
					</td>
				</tr>
				<TR style="display:none">
					<th><span>사용 기준*</span></th>
					<TD class="td_con1"><input type=radio id="idx_money1" name=up_money value="Y" <?=$check_moneyY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_money1>모든 결제수단에서 사용 가능(권장)</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_money2" name=up_money value="N" <?=$check_moneyN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_money2>현금결제시만 사용가능</label></TD>
				</TR>
				<TR style="display:none">
					<th style="width:300"><span>포인트 결제시 추가적립 설정*</span></th>
					<TD class="td_con1">
                    <div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        
                        <tr>
                            <td width="585"><input type=radio id="idx_remoney2" name=up_remoney value="U" <?=$check_remoneyU?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney2>사용한 포인트를 제외한 구매금액 대비 적립</label><span class="font_blue">(구매금액-(사용포인트+쿠폰할인))</span></td>
                        </tr>
						<tr>
                            <td width="585"><input type=radio id="idx_remoney1" name=up_remoney value="Y" <?=$check_remoneyY?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney1>포인트 사용하여 결제해도 포인트 추가 적립</label></td>
                        </tr>
                        <tr>
                            <td width="585"><input type=radio id="idx_remoney3" name=up_remoney value="A" <?=$check_remoneyA?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney3>포인트 사용하여 결제 시 포인트 추가 적립 안됨</label></td>
                        </tr>
                        <tr style="display:none">
                            <td width="585"><input type=radio id="idx_remoney4" name=up_remoney value="N" <?=$check_remoneyN?> onclick='document.form1.up_reprice.disabled=false;'><input type=text name=up_reprice value="<?=$reprice?>" size=8 maxlength=6 class="input"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney4>원 이상 적립금 사용시 추가 적립안됨</label></td>
                        </tr>
                        
                        </table>
                        </div>
					<?php if($remoney!="N") echo "<script>document.form1.up_reprice.disabled=true;</script>"; ?>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt;padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="sub_manual_wrap mb-5">
					<div class="title"><p>매뉴얼</p></div>
					<ul class="help_list">
						<li>회원이 포인트 적용기준 이상이 되면 주문서에 자동으로 [포인트 입력창] 생성됩니다.</li>
						<li>추가적립은 고객이 포인트를 사용하여 구매 시 추가적립여부를 설정합니다.</li>
						<li>적립방식은 변경한 시점의 주문기준으로 적용됩니다.</li>
						<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
					</ul>
				</div>			
				<!-- <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>적용기준</b></li>
						<li style='margin-top:8px'>1) 회원이 적립금 적용기준 이상이 되면 주문서에 자동으로 [적립금 입력창] 생성됩니다.</li>
						<li>2) 회원이 사용가능한 누적적립금의 1회 사용한도를 금액 또는 비율(%)로 설정하실 수 있습니다.</li>
					</ul>
				</div> -->
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR style='display:none;'>
					<th><span>신규회원 축하적립금</span></th>
					<TD class="td_con1"><select name=up_reserve_join class="select_selected"  style="width:100px">
						<option  <?php if($reserve_join==0) echo "selected "; ?> value=0>없음
						<?php
							$i = 100;
							while($i < 50001) {
								$r_select='';
								if($reserve_join==$i) {
									$r_select = "selected";
								}
								echo "<option  value=\"{$i}\" {$r_select}>".number_format($i)."</option>\n";
								if($i<500) { $i = $i +100; }
								elseif($i<2000) { $i = $i +500; }
								elseif($i<5000) { $i = $i +1000; }
								else { $i = $i +5000; }
							}
						?>
						</select> 포인트&nbsp;&nbsp;<span class="font_orange">* 회원가입 즉시 제공되는 적립금</span></TD>
				</TR>
				<TR>
					<th><span>포인트 절사*</span></th>
					<TD class="td_con1">포인트를 %단위로 입력 시 
						<select name=up_point_cut class="select_selected" style="width:100px">
							<option value="0" <?=$checked["up_point_cut"]["0"]?>>0</option>
							<option value="1" <?=$checked["up_point_cut"]["1"]?>>1</option>
							<option value="10" <?=$checked["up_point_cut"]["10"]?>>10</option>
							<option value="100" <?=$checked["up_point_cut"]["100"]?>>100</option>
						</select>원 단위로
						<select name=up_point_updown class="select_selected" style="width:100px">
							<option value="D" <?=$checked["up_point_updown"]["D"]?>>내림</option>
							<option value="B" <?=$checked["up_point_updown"]["B"]?>>반올림</option>
							<option value="U" <?=$checked["up_point_updown"]["U"]?>>올림</option>
						</select>하여 지급 
						<div class="font_orange" style="padding-top:6pt; font-size:9px;padding-left:10px;">- ex. 700원 7%로 적립금 49원일 경우, [10원 단위로 내림] 설정 시 적립금은 0원 / [1원 단위로 내림] 설정 시 적립금은 40원 지급</div>
						
					</TD>
				</TR>
				<TR>
					<th><span>사용 가능한 누적 포인트*</span></th>
					<TD class="td_con1">
						통합포인트는 <input type=text name=up_canuse value="<?=$canuse?$canuse:"0"?>" size=10 class="input">포인트 이상 적립된 경우에만 사용가능<br>
						E포인트는 <input type=text name=up_e_canuse value="<?=$e_canuse?$e_canuse:"0"?>" size=10 class="input">포인트 이상 적립된 경우에만 사용가능

					</TD>
				</TR>
				<TR>
					<th><span>사용 가능한 상품 구매액</span></th>
					<TD class="td_con1">
						통합포인트는 <input type=text name=up_reserve_maxprice value="<?=$reserve_maxprice?$reserve_maxprice:"0"?>" size=10 class="input">원 이상 구매 시 포인트 사용가능(배송비 제외)<br>
						E포인트는 <input type=text name=up_e_reserve_maxprice value="<?=$e_reserve_maxprice?$e_reserve_maxprice:"0"?>" size=10 class="input">원 이상 구매 시 포인트 사용가능(배송비 제외)
					</TD>
				</TR>
				<TR style="display:none;">
					<th><span>적립금 1회 사용한도</span></th>
					<TD class="td_con1">
                        <input type=radio name=up_usecheck value=1 <?=($reserve_limit==0?"checked":"")?>>누적포인트의 전체를 1회에 사용가능<Br>
                        <input type=radio name=up_usecheck value=2 <?=($reserve_limit>0?"checked":"")?>>누적포인트의 <input type=text name=up_reservemoney value="<?=($reserve_limit>0?$reserve_limit:"0")?>" size=10 class="input">포인트 까지 사용가능<br>
						<input type=radio name=up_usecheck value=3 <?=($reserve_limit<0?"checked":"")?>>상품구매액의 <input type=text name=up_reservepercent value="<?=($reserve_limit<0?str_replace("-","",$reserve_limit):"0")?>" size=10 maxlength=3 class="input">% 까지 사용가능(최대 100%까지 설정가능)
					</TD>
				</TR>
				</table>
                </div>
				</td>
			</tr>
			
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
					<div class="sub_manual_wrap mb-5">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>포인트 절사에서 적립금 단위와 방식을 초기상태로 유지할 경우, <b>소수점 이하 금액만 자동으로 내림하여 계산</b>됩니다. </li>
							<li>포인트 절사는 적립율에 따라 [원]으로 변경되는 금액에 대한 부분을 설정합니다.</li>
							<li>적립금 1회 사용한도에서 회원이 사용 가능한 누적포인트의 1회 사용한도를 포인트 또는 구매액에 따른 비율(%)로 설정하실 수 있습니다.</li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
					</div>		
                    <!-- <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>기본설정</b></li>
                            <li style='margin-top:8px'>1) <a href="javascript:parent.topframe.GoMenu(7,'market_couponnew.php');"><span class="font_blue">마케팅지원 > 쿠폰발행 서비스 설정</span></a> 에서 쿠폰 생성, 발급대상, 발급조회를 할 수 있습니다.</li>
                            <li>2) 쿠폰을 발행했더라도 쿠폰사용안함인 경우 회원들이 사용할 수 없습니다.</li>
                        </ul>
                    </div> -->                           
                </td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">포인트 지급 기준 설정</div>
				</td>
			</tr>

			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>신규 회원가입시</span></th>
					<TD class="td_con1">
						<input type=text name=agree_point value="<?=$pointSet_new['agree_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR style="display:none">
					<th><span>APP 다운로드시<BR>(APP 첫 로그인 시)</span></th>
					<TD class="td_con1">
						<input type=text name=app_point value="<?=$pointSet_new['app_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR>
					<th><span>텍스트 상품평 작성시</span></th>
					<TD class="td_con1">
						100자 이하<input type=text name=protext_down_point value="<?=$pointSet_new['protext_down_point']?>" size=10 class="input">E포인트 지급<br>
						100자 이상<input type=text name=protext_up_point value="<?=$pointSet_new['protext_up_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR>
					<th><span>포토 상품평 작성시</span></th>
					<TD class="td_con1">
						<input type=text name=poto_point value="<?=$pointSet_new['poto_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR>
					<th><span>추가정보 기입시</span></th>
					<TD class="td_con1">
						<input type=text name=over_point value="<?=$pointSet_new['over_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR>
					<th><span>개별 상품평 3번째이내 작성시</span></th>
					<TD class="td_con1">
						<input type=text name=proreview_point value="<?=$pointSet_new['proreview_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR style="display:none">
					<th><span>월간 전체 상품평 1등</span></th>
					<TD class="td_con1">
						<input type=text name=mody_one_point value="<?=$pointSet_new['mody_one_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR style="display:none">
					<th><span>월간 전체 상품평 2등</span></th>
					<TD class="td_con1">
						<input type=text name=mody_two_point value="<?=$pointSet_new['mody_two_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>
				<TR style="display:none">
					<th><span>월간 전체 상품평 3등</span></th>
					<TD class="td_con1">
						<input type=text name=mody_thr_point value="<?=$pointSet_new['mody_thr_point']?>" size=10 class="input">E포인트 지급
					</TD>
				</TR>

				
				</table>
                </div>
				</td>
			</tr>

			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
					<div class="sub_manual_wrap mb-5">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>추가정보는 회원가입 & 마이페이지 정보수정에서 추가정보(신체 사이즈, 기타 등)를 말합니다.</li>
							<li><b>[상품평 3번째 이내 작성]에는 관리자에서 등록한 리뷰도 순위에 포함됩니다.</b></li>
							<li>사용하지 않을 항목은 입력하지 않거나, 0으로 입력 시 적용되지 않습니다. </li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
					</div>
                    <!-- <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>공통설정</b></li>
                            <li style='margin-top:8px'>1) 고객이 상품구매시 적립금과 쿠폰을 동시 사용할 수 있는지 설정할 수 있습니다.</li>
                            <li>2) 동시 사용불가 일 경우 회원은 누적 적립금 사용 또는 쿠폰 중 중 택1만 가능합니다.</li>
                        </ul>
                    </div> -->                    
                </td>
			</tr>


			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">브랜드별 통합 포인트 적립기준설정</div>
				</td>
			</tr>

			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>

				<?while($brand_data=pmysql_fetch_object($brand_result)){
					$brand_per_qry="select * from tblproductbrand_point where bridx='".$brand_data->bridx."' order by no asc";
					$brand_per_result=pmysql_query($brand_per_qry);
					$brand_per_num=pmysql_num_rows($brand_per_result);
				
					?>
				<input type="hidden" name="brand_idx[]" value=<?=$brand_data->bridx?>>
				<TR>
					<th><span><?=$brand_data->brandname?></span></th>
					<TD class="td_con1">
						<table width=100% id=table_<?=$brand_data->bridx?> class=table cellpadding=0 cellspacing=0 border=0 style="border:0px;">
							<col class=engb align=center>
							<?
							if($brand_per_num){
							$bq=0;while($brand_per_data=pmysql_fetch_object($brand_per_result)){?>
							<tr>
								<td width=100% style="border:0px;">
									할인율 <input type=text name="st_per[<?=$brand_data->bridx?>][]" value="<?=$brand_per_data->st_per?>" size=10 class="input">% ~ <input type=text name="en_per[<?=$brand_data->bridx?>][]" value="<?=$brand_per_data->en_per?>" size=10 class="input">% 이하 <input type=text name="ins_per[<?=$brand_data->bridx?>][]" value="<?=$brand_per_data->ins_per?>" size=10 class="input">% 포인트 적립
									<?if(!$bq){?>
									&nbsp;&nbsp;&nbsp;<a href="javascript:add(<?=$brand_data->bridx?>)"><img src="images/btn_add1.gif" align=absmiddle></a>
									<?}else{?>
									&nbsp;&nbsp;&nbsp;<span style='cursor:pointer'><img src='images/btn_del.gif' align=absmiddle></span></td>
									<?}?>
								</td>
							</tr>
							<?$bq++;}
							}else{
							?>
							<tr>
								<td width=100% style="border:0px;">
									할인율 <input type=text name="st_per[<?=$brand_data->bridx?>][]" value="" size=10 class="input">% ~ <input type=text name="en_per[<?=$brand_data->bridx?>][]" value="" size=10 class="input">% 이하 <input type=text name="ins_per[<?=$brand_data->bridx?>][]" value="" size=10 class="input">% 포인트 적립
									<?if(!$bq){?>
									&nbsp;&nbsp;&nbsp;<a href="javascript:add(<?=$brand_data->bridx?>)"><img src="images/btn_add1.gif" align=absmiddle></a>
									<?}else{?>
									&nbsp;&nbsp;&nbsp;<span style='cursor:pointer'><img src='images/btn_del.gif' align=absmiddle></span></td>
									<?}?>
								</td>
							</tr>
							<?}?>
						</table>
						
					</TD>
				</TR>
				<?}?>
				
				</table>
                </div>
				</td>
			</tr>


           
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a></td>
			</tr>
			</form>
			<!-- <tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>적립금 설정 안내</span></dt>
							<dd>- <b>적립금이 없는 쇼핑몰로 운영할 경우</b> : 현금결제 추가적립 공란+상품의 개별 적립금을 공란으로 설정<br>
							<b>&nbsp;&nbsp;</b>배송비는 적립금 계산에서 제외됩니다.<br>
							<b>&nbsp;&nbsp;</b>적립금은 배송완료 후 적립됩니다.(주문 취소시 적립금도 자동삭제, 비회원은 적립되지 않습니다.)<Br><br>
							- <b>사용한 적립금을 제외한 구매금액 대비 적립<span class="font_orange">(구매금액-사용적립금)</span>에 대한 안내</b><br>
							<b>&nbsp;&nbsp;</b><span class="font_blue"><b>적립금 미사용</b></span> : 상품가격(10,000원)&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= 결제액(&nbsp;<span class="font_blue">10,000원</span> )에 대한 <span class="font_blue"><b>300원 적립(일반적립금)</b></span><br>
							<b>&nbsp;&nbsp;</b><span class="font_orange"><b>적립금</b>&nbsp;&nbsp;<b>&nbsp;&nbsp;사용</b></span> : 상품가격(10,000원) - 
							<span class="font_orange">사용적립금(2,000원)</span> = 결제액(<b>&nbsp;&nbsp;</b><span class="font_orange">8,000원</span> )에 대한 <span class="font_orange"><b>240원 적립</b></span>
							</dd>
						</dl>
					</div>
				</td>
			</tr> -->
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
