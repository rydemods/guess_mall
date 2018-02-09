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
$up_reserve_maxprice=$_POST["up_reserve_maxprice"];
$up_usecheck=$_POST["up_usecheck"];
$up_reservemoney=$_POST["up_reservemoney"];
$up_reservepercent=$_POST["up_reservepercent"];
$up_coupon_ok=$_POST["up_coupon_ok"];
$up_rcall_type=$_POST["up_rcall_type"];

if($up_usecheck==1) $reserve_limit=0;
else if($up_usecheck==2) $reserve_limit=$up_reservemoney;
else if($up_usecheck==3) $reserve_limit=-$up_reservepercent;
else $reserve_limit=0;

/* 포인트 지급 
$loginCount	= $_POST["loginCount"];		// 로그인시 제한 카운트
$loginPoint		= $_POST["loginPoint"];		// 로그인시 지급 포인트
$photoCount	= $_POST["photoCount"];		// 리뷰등록시 제한 카운트
$photoPoint	= $_POST["photoPoint"];		// 포토리뷰등록시 지급 포인트
$textrPoint		= $_POST["textrPoint"];		// 텍스트리뷰등록시 지급 포인트
$boardCount	= $_POST["boardCount"];	// 게시글작성시 제한 카운트
$boardPoint	= $_POST["boardPoint"];		// 게시글작성시 지급 포인트
*/

/* 쿠폰 기본 설정 */
$up_made_limit				= $_POST["up_made_limit"];				// 쿠폰 생성 제한
$up_amount_floor			= $_POST["up_amount_floor"];				// 금액절삭
$up_useand_pc_yn			= $_POST["up_useand_pc_yn"];			// 상품/장바구니 동시사용 여부
$up_cancel_restore_yn	= $_POST["up_cancel_restore_yn"];		// 결제 취소 시 쿠폰복원
$up_cancel_regoods_yn	= $_POST["up_cancel_regoods_yn"];	// 반품 취소 시 쿠폰복원

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
		$sets = " reserve_join = 0, reserve_maxuse = -1 ";
	} else {
		$sets = " reserve_join = '{$up_reserve_join}', reserve_maxuse = '{$up_canuse}' ";
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "rcall_type		= '{$up_rcall_type}', ";
	$sql.= "reserve_limit	= '{$reserve_limit}', ";
	$sql.= "reserve_maxprice= '{$up_reserve_maxprice}', ";
	$sql.= "reserve_useadd	= '{$reserve_useadd}', ";
	$sql.= $sets.", ";
	$sql.= "coupon_ok		= '{$up_coupon_ok}' ";
	pmysql_query($sql,get_db_conn());

    /*
	//로그인, 리뷰, 게시글 작성 시 적립금 적용기준을 파일로 저장한다.
    $f = fopen($Dir."conf/config.point.php","w");
	fwrite($f,"<?\n");
	fwrite($f,"\$pointSet['login']['count'] = '$loginCount'; \n");
	fwrite($f,"\$pointSet['login']['point'] = '$loginPoint'; \n");

	fwrite($f,"\$pointSet['photo']['count'] = '$photoCount'; \n");
	fwrite($f,"\$pointSet['photo']['point'] = '$photoPoint'; \n");

	fwrite($f,"\$pointSet['textr']['count'] = '$photoCount'; \n");
	fwrite($f,"\$pointSet['textr']['point'] = '$textrPoint'; \n");

	fwrite($f,"\$pointSet['board']['count'] = '$boardCount'; \n");
	fwrite($f,"\$pointSet['board']['point'] = '$boardPoint'; \n");

	fwrite($f,"?>\n");
	fclose($f);
	@chmod($Dir."conf/config.point.php",0777);
    */

	//쿠폰 기본설정시 쿠폰 사용 여부를 제외한 나머지를 저장한다.
	list($cp_num)=pmysql_fetch_array(pmysql_query("select num from tblcoupon "));
	if (!$cp_num) { 
		$sql = "INSERT INTO tblcoupon DEFAULT VALUES RETURNING num";
		$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		$cp_num = $row2[0];	
	} 

	$sql = "UPDATE tblcoupon SET ";
	$sql.= "made_limit		= '{$up_made_limit}', ";
	$sql.= "amount_floor	= '{$up_amount_floor}', ";
	$sql.= "useand_pc_yn= '{$up_useand_pc_yn}', ";
	$sql.= "cancel_restore_yn	= '{$up_cancel_restore_yn}', ";
	$sql.= "cancel_regoods_yn		= '{$up_cancel_regoods_yn}' ";
	$sql.= "WHERE num='{$cp_num}' ";
	pmysql_query($sql,get_db_conn());	
	
	//쿠폰정보의 금액 절삭을 모두 업데이트한다.
	$sql = "UPDATE tblcouponinfo SET amount_floor	= '{$up_amount_floor}' ";
	pmysql_query($sql,get_db_conn());	


	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('적립금/쿠폰 관련 설정이 완료되었습니다.');location.href='shop_reserve_coupon_info.php'; }</script>\n";

	$log_content = "## 적립금설정 ## - 사용여부 : $up_reserveuse, 가입적립금 : $up_reserve_join, 적립금이 $up_canuse 이상 사용가능, 추가적립기준:$reserve_useadd";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$log_content = "## 쿠폰설정 ## - 사용여부:$up_coupon_ok, 생성 제한 : $up_made_limit, 금액절삭 : $up_amount_floor, 상품/장바구니:$up_useand_pc_yn, 결제 취소 복원:$up_cancel_restore_yn, 반품 취소 복원:$up_cancel_regoods_yn, 적립금/쿠폰:$up_rcall_type";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

$sql2 = "SELECT rcall_type,reserve_limit,reserve_maxprice,reserve_useadd,reserve_maxuse,reserve_join,coupon_ok ";
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
${"check_coupon_ok".$coupon_ok} = "checked";
${"check_rcall_type".$rcall_type} = "checked";


$sql2 = "SELECT * FROM tblcoupon ";
$result = pmysql_query($sql2,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
	$made_limit				= $row->made_limit;				// 쿠폰 생성 제한
	$amount_floor				= $row->amount_floor;				// 금액절삭
	$useand_pc_yn			= $row->useand_pc_yn;			// 상품/장바구니 동시사용 여부
	$cancel_restore_yn		= $row->cancel_restore_yn;		// 결제 취소 시 쿠폰복원
	$cancel_regoods_yn	= $row->cancel_regoods_yn;	// 반품 취소 시 쿠폰복원	
} else {
	$useand_pc_yn			= "Y";		// 상품/장바구니 동시사용 여부
	$cancel_restore_yn		= "N";		// 결제 취소 시 쿠폰복원
	$cancel_regoods_yn	= "N";		// 반품 취소 시 쿠폰복원	
}
pmysql_free_result($result);

${"check_useand_pc_yn".$useand_pc_yn} = "checked";	
${"check_cancel_restore_yn".$cancel_restore_yn} = "checked";
${"check_cancel_regoods_yn".$cancel_regoods_yn} = "checked";

?>

<?php 
include("header.php");
include_once($Dir."conf/config.point.php");
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

function checkreserve(val){
	for(i=0;i<3;i++){
		if(i==(val-1)) {
			document.form1.up_usecheck[i].checked=true;
		} else {
			document.form1.up_usecheck[i].checked=false;
		}
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>적립금/쿠폰 설정</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">적립금/쿠폰 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>구매자에 대한 적립금/쿠폰 지급 조건과 사용가능 조건, 기본 지급비율을 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">적립금 설정</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>기본설정</b></li>
                            <li style='margin-top:8px'>- <span class="font_orange">카테고리별 적립금 등록</span> : <a href="javascript:parent.topframe.GoMenu(4,'product_reserve.php');"><span class="font_blue">상품관리 > 상품 일괄관리 > 적립금 일괄수정</span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(원 또는 % 단위로 일괄등록)</li>
                            <li>- <span class="font_orange">상품별 적립금 등록</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <a href="javascript:parent.topframe.GoMenu(4,'product_allupdate.php');"><span class="font_blue">상품관리 > 상품 일괄관리 > 상품 일괄 간편수정</span></a>&nbsp;&nbsp;&nbsp;&nbsp;(원 단위로 상품별 개별등록)</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>구매시 적립금 사용여부</span></th>
					<TD class="td_con1"><input type=radio id="idx_reserveuse1" name=up_reserveuse value="Y" <?=$check_reserveuseY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserveuse1>사용함&nbsp;&nbsp;&nbsp;&nbsp;: 누적된 적립금을 구매 결제시 공제</label><br>
					<input type=radio id="idx_reserveuse2" name=up_reserveuse value="N" <?=$check_reserveuseN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserveuse2>사용안함 : 주문시에 사용가능한 누적 적립금 및 사용금액 입렵항목이 미표시</label>					</TD>
				</TR>
				<TR>
					<th><span>사용 가능한 결제수단</span></th>
					<TD class="td_con1"><input type=radio id="idx_money1" name=up_money value="Y" <?=$check_moneyY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_money1>모든 결제수단에서 사용 가능(권장)</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_money2" name=up_money value="N" <?=$check_moneyN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_money2>현금결제시만 사용가능</label></TD>
				</TR>
				<TR>
					<th style="width:300"><span>적립금 사용하여 결제시 추가적립 설정</span></th>
					<TD class="td_con1">
                    <div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="585"><input type=radio id="idx_remoney1" name=up_remoney value="Y" <?=$check_remoneyY?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney1>적립금 사용하여 결제해도 최종적립금으로 정상 추가</label></td>
                        </tr>
                        <tr>
                            <td width="585"><input type=radio id="idx_remoney2" name=up_remoney value="U" <?=$check_remoneyU?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney2>사용한 적립금을 제외한 구매금액 대비 적립</label><span class="font_blue">(구매금액-사용적립금)</span></td>
                        </tr>
                        <tr>
                            <td width="585"><input type=radio id="idx_remoney3" name=up_remoney value="A" <?=$check_remoneyA?> onclick='document.form1.up_reprice.disabled=true;'><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney3>적립금을 사용하여 결제할 경우 최종적립금이 추가가 안됨</label><span class="font_blue">(회원 등급별 추가적립은 무조건 적립)</span></td>
                        </tr>
                        <tr>
                            <td width="585"><input type=radio id="idx_remoney4" name=up_remoney value="N" <?=$check_remoneyN?> onclick='document.form1.up_reprice.disabled=false;'><input type=text name=up_reprice value="<?=$reprice?>" size=8 maxlength=6 class="input"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_remoney4>원 이상 적립금 사용시 추가 적립안됨</label></td>
                        </tr>
                        <tr>
                            <td width="585" class="font_orange" style="padding-top:6pt;">&nbsp;* 고객이 적립금을 사용하여 <b>구매시 추가적립여부를 선택</b>하실 수 있습니다.</td>
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
				<td style="padding-bottom:3pt;">
				<!-- 도움말 -->
				<div class="help_info01_wrap" style='min-height:30px;width:auto;'>
					<ul style='margin:15px 0px 15px 50px;'>
                        <li><b style='font-size:14px;'>적용기준</b></li>
						<li style='margin-top:8px'>1) 회원이 적립금 적용기준 이상이 되면 주문서에 자동으로 [적립금 입력창] 생성됩니다.</li>
						<li>2) 회원이 사용가능한 누적적립금의 1회 사용한도를 금액 또는 비율(%)로 설정하실 수 있습니다.</li>
					</ul>
				</div>				
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
					<th><span>사용 가능한 누적 적립금</span></th>
					<TD class="td_con1"><select name=up_canuse class="select_selected" style="width:100px">
<?php
	$i = 0;
	while($i < 200001) {
		$r_select='';
		if($canuse==$i){
			$r_select = "selected";
		}
		echo "<option value=\"{$i}\" {$r_select}>".number_format($i)."</option>\n";
		if($i<1000) { $i = $i +100; }
		else if($i<10000) { $i = $i +1000; }
		elseif($i<20000) { $i = $i +5000; }
		elseif($i<100000) { $i = $i +10000; }
		else { $i = $i +20000; }
	}
?>
						</select> 포인트 이상 적립된 경우에만 사용가능</TD>
				</TR>
				<TR>
					<th><span>사용 가능한 상품 구매액</span></th>
					<TD class="td_con1"><input type=text name=up_reserve_maxprice value="<?=$reserve_maxprice?>" size=10 maxlength=7 class="input"> 포인트 이상 구매시 적립금 사용가능(배송비 제외)</TD>
				</TR>
				<TR>
					<th><span>적립금 1회 사용한도</span></th>
					<TD class="td_con1">
                        <div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td><input type=checkbox name=up_usecheck value=1 <?=($reserve_limit==0?"checked":"")?> onclick="checkreserve('1')"> 누적 적립금 전체를 1회에 사용가능</td>
                        </tr>
                        <tr>
                            <td><input type=checkbox name=up_usecheck value=2 <?=($reserve_limit>0?"checked":"")?> onclick="checkreserve('2')"> <B>누적적립금</B>의 <select name=up_reservemoney class="select">
    <?php
        $i = 1000;
        while($i < 200001) {
            $r_select='';
            if($reserve_limit==$i) {
                $r_select = "selected";
            }
            echo "<option value=\"{$i}\" {$r_select}>{$i}</option>\n";
            if($i<10000) { $i = $i +1000; }
            elseif($i<20000) { $i = $i +5000; }
            elseif($i<100000) { $i = $i +10000; }
            else { $i = $i +20000; }
        }
    ?>
                            </select> <B>포인트</B> 까지 사용가능</td>
                        </tr>
                        <tr>
                            <td><IMG height=5 width=0><input type=checkbox name=up_usecheck value=3 <?=($reserve_limit<0?"checked":"")?> onclick="checkreserve('3')"> <B>상품구매액</B>의 <select name=up_reservepercent class="select">
    <?php
        for($i=1;$i<=100;$i++){
            $r_select='';
            if(abs($reserve_limit)==$i) {
                $r_select = "selected";
            }
            echo "<option value=\"{$i}\" {$r_select}>{$i}</option>\n";
        }
    ?>
                            </select> <B>%</B> 까지 사용가능</td>
                        </tr>
                        </table>
                        </div>
					</TD>
				</TR>
				<!-- <TR>
					<th><span>로그인</span></th>
					<TD class="td_con1">
						하루 &nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name="loginCount" value="<?=$pointSet['login']['count']?>" size=10 label="로그인 횟수" onkeypress="return isNumberKey(event)" class=input> 번 &nbsp;&nbsp;
						<input type='text' name="loginPoint" value="<?=$pointSet['login']['point']?>" size=10  label="로그인 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트 지급
					</TD>
				</TR>
				<TR>
					<th><span>리뷰</span></th>
					<TD class="td_con1">
						제품당 &nbsp;<input type='text' name="photoCount" value="<?=$pointSet['photo']['count']?>" size=10 label="포토리뷰 횟수" onkeypress="return isNumberKey(event)" class=input> 번 &nbsp;&nbsp;
						포토리뷰 작성시 <input type='text' name="photoPoint" value="<?=$pointSet['photo']['point']?>" size=10 label="포토리뷰 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트 지급&nbsp;&nbsp;/&nbsp;&nbsp;
						텍스트 리뷰 작성시 <input type='text' name="textrPoint" value="<?=$pointSet['textr']['point']?>" size=10 label="텍스트리뷰 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트 지급
					</TD>
				</TR>
				<TR>
					<th><span>게시글 작성</span></th>
					<TD class="td_con1">
						하루 &nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name="boardCount" value="<?=$pointSet['board']['count']?>" size=10 label="게시글작성 횟수" onkeypress="return isNumberKey(event)" class=input> 번 &nbsp;&nbsp;
		<input type='text' name="boardPoint" value="<?=$pointSet['board']['point']?>" size=10 label="게시글작성 포인트" onkeypress="return isNumberKey(event)" class=input> 포인트 지급
					</TD>
				</TR> -->
				</table>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쿠폰 설정</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>기본설정</b></li>
                            <li style='margin-top:8px'>1) <a href="javascript:parent.topframe.GoMenu(7,'market_couponnew.php');"><span class="font_blue">마케팅지원 > 쿠폰발행 서비스 설정</span></a> 에서 쿠폰 생성, 발급대상, 발급조회를 할 수 있습니다.</li>
                            <li>2) 쿠폰을 발행했더라도 쿠폰사용안함인 경우 회원들이 사용할 수 없습니다.</li>
                        </ul>
                    </div>                           
                </td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<th><span>쿠폰 사용 여부</span></th>
							<td>
								<input type=radio id="idx_coupon_ok1" name=up_coupon_ok value="Y" <?=$check_coupon_okY?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_ok1>사용함</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_coupon_ok2" name=up_coupon_ok value="N" <?=$check_coupon_okN?> onclick="javascript:if (!confirm('새쿠폰 발행 및 기존 쿠폰 발급이 모두 중지 됩니다. 선택하시겠습니까?')) {document.form1.up_coupon_ok.value='Y';}"><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_ok2>사용안함</label>
							</td>
						</tr>
						<tr>
							<th><span>쿠폰 생성 제한</span></th>
							<td>
								<select name=up_made_limit class="select_selected"  style="width:220px">
									<option  <?php if($made_limit==2) echo "selected "; ?> value=2>상품 쿠폰만 생성</option>
									<option  <?php if($made_limit==1) echo "selected "; ?> value=1>장바구니 쿠폰만 생성</option>
									<option  <?php if($made_limit==3) echo "selected "; ?> value=3>상품쿠폰 + 장바구니쿠폰 생성</option>
								</select>
							</td>
						</tr>
						<tr>								
							<th><span>금액절삭</span></th>
							<td>
							<SELECT name=up_amount_floor class="select">
		<?php
							$arfloor = array(1=>"일원단위, 예)12344 → 12340","십원단위, 예)12344 → 12300","백원단위, 예)12344 → 12000","천원단위, 예)12344 → 10000");
							$arcnt = count($arfloor);
							for($i=1;$i<$arcnt;$i++){
								echo "<option value=\"{$i}\"";
								if($amount_floor==$i) echo " selected";
								echo ">{$arfloor[$i]}</option>";
							}
		?>
							</SELECT>
							</td>
						</tr>
						<tr>
							<th><span>상품/장바구니 동시사용 여부</span></th>
							<td>
								<input type=radio id=up_useand_pc_y name=up_useand_pc_yn value="Y" <?=$check_useand_pc_ynY?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_useand_pc_y>동시 사용</label>&nbsp;&nbsp;<input type=radio id=up_useand_pc_n name=up_useand_pc_yn value="N"<?=$check_useand_pc_ynN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_useand_pc_n>동시 사용안함</label>
							</td>
						</tr>
						<tr>
							<th rowspan=3><span>취소/반품 환불 시 쿠폰 복원</span></th>
							<td>
								<span style='display:inline-block;width:100px'>결제 취소 시</span><input type=radio id=up_cancel_restore_y name=up_cancel_restore_yn value="Y" <?=$check_cancel_restore_ynY?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_restore_y>자동복원</label>&nbsp;&nbsp;<input type=radio id=up_cancel_restore_n name=up_cancel_restore_yn value="N"<?=$check_cancel_restore_ynN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_restore_n>자동복원 안 함(쿠폰 사용 여부를 확인 함)</label>
							</td>
						</tr>
						<tr>
							<td>
								<span style='display:inline-block;width:100px'>반품 취소 시</span><input type=radio id=up_cancel_regoods_y name=up_cancel_regoods_yn value="Y" <?=$check_cancel_regoods_ynY?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_regoods_y>자동복원</label>&nbsp;&nbsp;<input type=radio id=up_cancel_regoods_n name=up_cancel_regoods_yn value="N"<?=$check_cancel_regoods_ynN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_regoods_n>자동복원 안 함(쿠폰 사용 여부를 확인 함)</label>
							</td>
						</tr>
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">적립금/쿠폰 설정</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>공통설정</b></li>
                            <li style='margin-top:8px'>1) 고객이 상품구매시 적립금과 쿠폰을 동시 사용할 수 있는지 설정할 수 있습니다.</li>
                            <li>2) 동시 사용불가 일 경우 회원은 누적 적립금 사용 또는 쿠폰 중 중 택1만 가능합니다.</li>
                        </ul>
                    </div>                        
                </td>
			</tr>
            <tr>
                <td>
                   	<div class="table_style01">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <th><span>적립금/쿠폰 동시 사용여부</span></th>
                                <td>
                                    <input type=radio id="idx_rcall_type1" name=up_rcall_type value="Y" <?=$check_rcall_typeY?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_rcall_type1>동시 사용</label>  &nbsp;&nbsp;<input type=radio id="idx_rcall_type2" name=up_rcall_type value="N" <?=$check_rcall_typeN?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_rcall_type2>동시 사용안함</label>
                                </td>
                            </tr>
                        </table>
                    </div>
				</td>
            </tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
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
