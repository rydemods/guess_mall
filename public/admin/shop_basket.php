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
$up_val=$_POST["up_val"];
$up_oneshot_ok=$_POST["up_oneshot_ok"];
$up_baskettime=$_POST["up_baskettime"];
$up_baskettruetime=$_POST["up_baskettruetime"];
$up_stock=$_POST["up_stock"];

if ($type=="up") {
	$etctype=$up_val;
	if ($up_baskettime!="0"){
		$etctype.= "BASKETTIME={$up_baskettruetime}";
		$up_baskettime=$up_baskettruetime;
	}
	if ($up_stock=="N"){
		$etctype.= "STOCK={$up_stock}";
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "oneshot_ok	= '{$up_oneshot_ok}', ";
	$sql.= "etctype		= '{$etctype}' ";
	$result = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('장바구니 관련 기능 설정이 완료되었습니다.'); }</script>";
}

$baskettime=0;
$sql = "SELECT oneshot_ok, etctype FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row = pmysql_fetch_object($result)) {
	$oneshot_ok=$row->oneshot_ok;
	if (ord($row->etctype)) {
		$etctemp = explode("",$row->etctype);
		$cnt = count($etctemp);
		$etcvalue="";
		for ($i=0;$i<$cnt;$i++) {
			if (substr($etctemp[$i],0,11)=="BASKETTIME=") $baskettime=substr($etctemp[$i],11);	#장바구니 설정시간 
			else if (substr($etctemp[$i],0,6)=="STOCK=") $stock=substr($etctemp[$i],6);			#재고수량 부족시 멘트
			else if(ord($etctemp[$i])) $etcvalue.=$etctemp[$i]."";
		}
	}
}
pmysql_free_result($result);

if(ord($stock)==0) $stock="Y";
${"check_oneshot_ok".$oneshot_ok} = "checked";
${"check_stock".$stock} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>장바구니 관련 기능설정</span></p></div></div>
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
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">장바구니 관련 기능설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>장바구니에 관련된 기능을 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">스피드구매 기능 사용<span>장바구니 상단에서 카테고리의 상품을 장바구니 직접 담을 수 있는 기능입니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=up_val value="<?=$etcvalue?>">
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>스피드구매 사용여부</span></th>
					<TD class="td_con1"><input type=radio id="idx_oneshot_ok1" name=up_oneshot_ok value="Y" <?=$check_oneshot_okY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_oneshot_ok1>스피드구매 사용</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_oneshot_ok2" name=up_oneshot_ok value="N" <?=$check_oneshot_okN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_oneshot_ok2>스피드구매 미사용</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">장바구니 보관 시간<span>장바구니에 담은 상품의 보관 시간을 설정합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>장바구니 보관시간 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_baskettime1" name=up_baskettime value="0" <?php if($baskettime=="0") echo "checked ";?> onclick="document.form1.up_baskettruetime.disabled=true"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_baskettime1>브라우저 종료시 삭제</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_baskettime2" name=up_baskettime value="1" <?php if($baskettime!="0") echo "checked ";?> onclick="document.form1.up_baskettruetime.disabled=false"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_baskettime2>시간설정</label> <select name=up_baskettruetime class="select"> 
<?php
	$maxtime=72;
	for($i=3;$i<=$maxtime;$i+=3) {
		echo "<option value=$i";
		if($baskettime==$i) echo " selected";
		echo ">{$i}시간</option>\n";
	}
?>
					<option value="96" <?php if ($baskettime=="96") echo "selected"; ?>>4일
					<option value="120" <?php if ($baskettime=="120") echo "selected"; ?>>5일
					<option value="144" <?php if ($baskettime=="144") echo "selected"; ?>>6일
					<option value="168" <?php if ($baskettime=="168") echo "selected"; ?>>7일
					</select></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">재고수량 부족 알림</div>
				</td>
			</tr>
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>장바구니에서 구매수량을 조절할 때 안내문구를 설정할 수 있습니다.</li>
                                <li>재고수량&nbsp;&nbsp;<b>&nbsp;&nbsp;</b>표기 : 해당 상품의 재고가 ??개 입니다.</li>
                                <li>재고수량&nbsp;미표기 : 해당 상품의 재고가 부족합니다.</li>
                            </ul>
                        </div>
                </td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>재고수량 알림 선택</span></th>
					<TD class="td_con1"><input type=radio id="idx_stock1" name=up_stock value="Y" <?=$check_stockY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_stock1>표기</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_stock2" name=up_stock value="N" <?=$check_stockN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_stock2>미표기</label></TD>
				</TR>
				</TABLE>
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
							<dt><span><b>스피드 구매</b>는 <b><font class="font_orange">장바구니 상단에서 상품을 선택할 수 있는 기능</font>입니다.&nbsp;</b></span></dt>
							<dd><img src="images/shop_basket_img1.gif" border="0"></dd>
							
						</dl>
												
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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
