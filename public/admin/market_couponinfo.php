<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-3";
$MenuCode = "market";
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

if ($type=="up") {
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
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('적립금/쿠폰 관련 설정이 완료되었습니다.'); }</script>\n";

	$log_content = "## 적립금설정 ## - 사용여부 : $up_reserveuse, 가입적립금 : $up_reserve_join, 적립금이 $up_canuse 이상 사용가능, 쿠폰:$up_coupon_ok, 추가적립기준:$reserve_useadd";
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
?>

<?php include("header.php"); ?>

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
	form.submit();
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쿠폰발행 서비스 설정 &gt;<span>쿠폰기본 설정</span></p></div></div>
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
					<div class="title_depth3">쿠폰 기본 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원에 대한 쿠폰 지급 조건과 사용가능 조건을 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쿠폰 기본 설정 조건</div>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
                <tr>
                	<td>
                    	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <th><span>쿠폰 사용</span></th>
                                    <td>
                                        <input type=radio id=up_use_y name=up_use_yn value="Y" <?=$checked[use_yn]['Y']?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_use_y>사용</label>&nbsp;&nbsp;<input type=radio id=up_use_n name=up_use_yn value="N"<?=$checked[use_yn]['N']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_use_n>사용안함</label>
                                    </td>
                                </tr>
								<tr>
									<th><span>쿠폰 생성 제한</span></th>
									<td>
										<select name=up_made_limit class="select_selected"  style="width:200px">
											<option  <?php if($made_limit==1) echo "selected "; ?> value=0>장바구니 쿠폰만 생성</option>
											<option  <?php if($made_limit==2) echo "selected "; ?> value=0>상품쿠폰만 생성</option>
											<option  <?php if($made_limit==3) echo "selected "; ?> value=0>장바구니쿠폰 + 상품쿠폰 생성</option>
										</select>
									</td>
								</tr>
								<tr>								
									<th><span>금액절삭</span></th>
									<td>
									<SELECT name=amount_floor class="select">
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
									<th><span>할인 동시 노출</span></th>
									<td>
										<select name=up_made_limit class="select_selected"  style="width:200px">
											<option  <?php if($made_limit==1) echo "selected "; ?> value=0>쿠폰만 노출</option>
											<option  <?php if($made_limit==2) echo "selected "; ?> value=0>적립금만 노출</option>
											<option  <?php if($made_limit==3) echo "selected "; ?> value=0>쿠폰+적립금 동시 노출</option>
										</select><br>
										<div style='display:block;padding-top:5px;line-height:19px;color:#999999'>
										&nbsp;- ‘적립금만 노출’로 선택시, 쿠폰 기능을 사용으로 하더라도, 쿠폰을 선택할 수 있는 버튼이 나타나지 않습니다.<br>
										&nbsp;- ‘쿠폰만 노출’로 선택하는 경우, 쿠폰 적용시 적립금할인은 적용되지 않습니다.
										</div>
									</td>
								</tr>
                                <tr>
                                    <th><span>상품/장바구니 동시사용 여부</span></th>
                                    <td>
                                        <input type=radio id=up_useand_pc_y name=up_useand_pc_yn value="Y" <?=$checked[useand_pc_yn]['Y']?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_useand_pc_y>동시 사용</label>&nbsp;&nbsp;<input type=radio id=up_useand_pc_n name=up_useand_pc_yn value="N"<?=$checked[useand_pc_yn]['N']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_useand_pc_n>동시 사용안함</label>
                                    </td>
                                </tr>
                                <tr>
                                    <th rowspan=3><span>취소/반품/교환시 쿠폰 복원</span></th>
                                    <td>
                                        <span style='display:inline-block;width:100px'>구매 취소 시</span><input type=radio id=up_cancel_restore_y name=up_cancel_restore_yn value="Y" <?=$checked[useand_pc_yn]['Y']?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_restore_y>자동복원</label>&nbsp;&nbsp;<input type=radio id=up_cancel_restore_n name=up_cancel_restore_yn value="N"<?=$checked[useand_pc_yn]['N']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_restore_n>자동복원 안 함(쿠폰 사용 여부를 확인 함)</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style='display:inline-block;width:100px'>반품 취소 시</span><input type=radio id=up_cancel_regoods_y name=up_cancel_regoods_yn value="Y" <?=$checked[useand_pc_yn]['Y']?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_regoods_y>자동복원</label>&nbsp;&nbsp;<input type=radio id=up_cancel_regoods_n name=up_cancel_regoods_yn value="N"<?=$checked[useand_pc_yn]['N']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_regoods_n>자동복원 안 함(쿠폰 사용 여부를 확인 함)</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style='display:inline-block;width:100px'>교환 시</span><input type=radio id=up_cancel_rechange_y name=up_cancel_rechange_yn value="Y" <?=$checked[useand_pc_yn]['Y']?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_rechange_y>자동복원</label>&nbsp;&nbsp;<input type=radio id=up_cancel_rechange_n name=up_cancel_rechange_yn value="N"<?=$checked[useand_pc_yn]['N']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_cancel_rechange_n>자동복원 안 함(쿠폰 사용 여부를 확인 함)</label>
                                    </td>
                                </tr>
                            </table>
                        </div>
					</td>
                </tr>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쿠폰/적립금 동시사용 조건</div>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
                <tr>
                	<td>
                    	<div class="table_style01">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<th><span>구매시 동시사용 여부</span></th>
									<td>
										<input type=radio id=up_useand_reserve_y name=up_useand_reserve_yn value="Y" <?=$checked[useand_reserve_yn]['Y']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_useand_reserve_y>동시 사용</label>&nbsp;&nbsp;<input type=radio id=up_useand_reserve_n name=up_useand_reserve_yn value="N" <?=$checked[useand_reserve_yn]['Y']?>><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=up_useand_reserve_n>동시 사용안함</label><br>
										<div style='display:block;padding-top:5px;line-height:19px;color:#999999'>
										&nbsp;- 적립금과 쿠폰을 동시사용여부를 설정할 수 있습니다.<br>
										&nbsp;- ‘사용안함’으로 설정하는 경우 적립금과 쿠폰 중 한가지만 사용할 수 있습니다.
										</div>
									</td>
								</tr>
                            </table>
                        </div>
					</td>
                </tr>
				</TABLE>
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
							<dt><span>쿠폰기본 설정 안내</span></dt>
							<dd>- 
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
