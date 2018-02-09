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

//print_r($_POST);

$search=$_POST["search"];

////////////////////////

$type=$_POST["type"];
$ci_no = $_POST["ci_no"];
$coupon_code=$_POST["coupon_code"];
$uid=$_POST["uid"];

$imagepath=$Dir.DataDir."shopimages/etc/";

if($type=="stop" && ord($coupon_code)) {	//발급중지

	$sql = "UPDATE tblcouponinfo SET display='N',issue_type='D' WHERE coupon_code = '{$coupon_code}' ";
	pmysql_query($sql,get_db_conn());
	if(!pmysql_errno()) $onload="<script>window.onload=function(){ alert('해당 쿠폰에 대해서 발급중지 처리가 완료되었습니다.\\n\\n기존 발급된 쿠폰만 사용가능합니다.'); }</script>";
	$coupon_code='';

} else if($type=="delete" && ord($coupon_code)) {	//완전삭제

	$sql = "DELETE FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblcouponissue WHERE coupon_code = '{$coupon_code}' ";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblcouponpaper WHERE coupon_code = '{$coupon_code}' ";
	pmysql_query($sql,get_db_conn());
	if(file_exists($imagepath."COUPON{$coupon_code}.gif")) {
		unlink($imagepath."COUPON{$coupon_code}.gif");
	}
	
	pmysql_query("DELETE FROM tblcouponcategory WHERE coupon_code = '{$coupon_code}'", get_db_conn());
	pmysql_query("DELETE FROM tblcouponproduct WHERE coupon_code = '{$coupon_code}'", get_db_conn());

	if(!pmysql_errno()) $onload="<script>window.onload=function(){ alert('해당 쿠폰의 모든 내역이 완전 삭제되었습니다.'); }</script>";
	$coupon_code='';

} else if($type=="issueagain" && ord($coupon_code) && ord($uid)) {	//회원에게 발급한 쿠폰 재발급

	$sql = "UPDATE tblcouponissue SET used='N' WHERE coupon_code = '{$coupon_code}' AND id = '{$uid}' ";
	pmysql_query($sql,get_db_conn());
	if(!pmysql_errno()) $onload="<script>window.onload=function(){ alert('{$uid} 회원님께 해당 쿠폰을 재발급 되었습니다.'); }</script>";

} else if($type=="issuedelete" && ord($ci_no) && ord($coupon_code) && ord($uid)) {	//회원에게 발급한 쿠폰 삭제

	$sql = "DELETE FROM tblcouponissue WHERE ci_no = ".$ci_no." AND coupon_code = '{$coupon_code}' AND id = '{$uid}' ";
	pmysql_query($sql,get_db_conn());
    exdebug($sql);
	$sql = "UPDATE tblcouponinfo SET issue_no = issue_no - 1 WHERE coupon_code = '{$coupon_code}'";
	pmysql_query($sql,get_db_conn());
	if(!pmysql_errno()) $onload="<script>window.onload=function(){ alert('{$uid} 회원님에게 발급된 쿠폰이 삭제되었습니다.'); }</script>";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CouponView(code) {
	window.open("about:blank","couponview","width=650,height=650,scrollbars=no");
	document.cform.coupon_code.value=code;
	document.cform.submit();
}

function CouponModify(code) {
	window.open("about:blank","couponmodify","width=650,height=650,scrollbars=yes");
	document.mform.coupon_code.value=code;
	document.mform.submit();
}

function CouponIssue(code){
	document.form1.coupon_code.value=code;
	document.form1.block2.value="";
	document.form1.gotopage2.value="";
	document.form1.submit();
}

function CouponStop(code) {
	if(confirm("기존 회원에게 발급된 쿠폰은 사용이 가능합니다.\n\n해당 쿠폰 발급을 중지하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.type.value="stop";
		document.form1.submit();
	}
}

function CouponDelete(code) {
	if(confirm("기존 회원에게 발급된 쿠폰까지 모두 삭제됩니다.\n\n해당 쿠폰을 완전 삭제하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

function IssueCouponAgain(code,uid) {
	if(confirm(uid+" 회원님에게 쿠폰을 재발급 하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.uid.value=uid;
		document.form1.type.value="issueagain";
		document.form1.submit();
	}
}

function IssueCouponDelete(ci_no, code,uid) {
	if(confirm(uid+" 회원님에게 발급한 쿠폰을 삭제하시겠습니까?")) {
		document.form1.ci_no.value=ci_no;
        document.form1.coupon_code.value=code;
		document.form1.uid.value=uid;
		document.form1.type.value="issuedelete";
		document.form1.submit();
	}
}

function GoPage(block,gotopage) {
	document.form1.type.value = "";
    document.form1.ci_no.value = "";
	document.form1.coupon_code.value = "";
	document.form1.uid.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.block2.value="";
	document.form1.gotopage2.value="";
	document.form1.submit();
}

function GoPage2(block,gotopage) {
	document.form1.type.value = "";
	document.form1.uid.value = "";
	document.form1.block2.value = block;
	document.form1.gotopage2.value = gotopage;
	document.form1.submit();
}

function id_search() {
	document.form1.type.value='';
	document.form1.uid.value='';
	document.form1.submit();
}

function search_default() {
	document.form1.type.value='';
	document.form1.uid.value='';
	document.form1.search.value='';
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 쿠폰발행 서비스 설정 &gt;<span>발급된 쿠폰 내역관리</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ci_no value="<?=$ci_no?>">
            <input type=hidden name=coupon_code value="<?=$coupon_code?>">
			<input type=hidden name=uid>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=block2 value="<?=$block2?>">
			<input type=hidden name=gotopage2 value="<?=$gotopage2?>">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">발급된 쿠폰 내역관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>현재 진행중인 쿠폰내역과 정보를 확인할 수 있는 메뉴 입니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">발급한 쿠폰 내역</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=30></col>
				<col width=75></col>
				<col width=75></col>
				<col width=></col>
				<col width=200></col>
				<col width=150></col>
				<col width=60></col>
				<col width=60></col>
				<!-- <col width=90></col> -->
				<col width=90></col>
				<TR align=center>
					<th>No</th>
					<th>쿠폰코드</th>
					<th>쿠폰종류</th>
					<th>쿠폰명</th>
					<th>할인/적립</th>
					<th>유효기간</th>
					<th>쿠폰수정</th>
					<th>발급내역</th>
					<!-- <th>발급중지</th> -->
					<th><b><font color="red">완전삭제</font></b></th>
				</TR>
<?php
				$sql = "SELECT COUNT(*) as t_count FROM tblcouponinfo WHERE vender='0' ";
				$result = pmysql_query($sql,get_db_conn());
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;	
				$gotopage = $paging->gotopage;	

				$sql = "SELECT * FROM tblcouponinfo WHERE vender='0' ORDER BY date DESC";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$cnt++;

					if($coupon_code==$row->coupon_code) {
						$coupon_name=$row->coupon_name;
					}

                    if($row->coupon_use_type == "1") $coupon_use_type = "[장바구니]";
                    else $coupon_use_type = "[상품쿠폰]";

					if($row->sale_type<=2) $dan="%";
					else $dan="원";

					if($row->sale_type%2==0) $sale = "할인";
					else $sale = "적립";

					if($row->date_start>0) {
						$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
					} else {
                        $date = abs($row->date_start)."일동안, ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
					}
					$maxPrice = $row->sale_max_money?"&nbsp;[최대 ".number_format($row->sale_max_money)."원 할인]":'';
					echo "<TR align=center>\n";
					echo "	<TD>{$number}</TD>\n";
					echo "	<TD><A HREF=\"javascript:CouponView('{$row->coupon_code}');\"><B>{$row->coupon_code}</B></A></TD>\n";
					echo "	<TD><span class=\"font_blue\">{$coupon_use_type}</span></TD>\n";
					echo "	<TD><div class=\"ta_l\">{$row->coupon_name}</div></TD>\n";
					echo "	<TD><span class=\"".($sale=="할인"?"font_orange":"font_blue")."\"><b><NOBR>".number_format($row->sale_money).$dan." {$sale}".$maxPrice."<NOBR></b></span></TD>\n";
					echo "	<TD><NOBR>{$date}</NOBR></TD>\n";
					echo "	<TD><a href=\"javascript:CouponModify('{$row->coupon_code}');\"><img src=\"img/btn/btn_cate_modify.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:CouponIssue('{$row->coupon_code}');\"><img src=\"images/btn_search2.gif\" border=\"0\"></a></TD>\n";
					/*echo "	<TD>";
					if($row->issue_type!="D") {
						echo "<a href=\"javascript:CouponStop('{$row->coupon_code}');\"><img src=\"images/btn_stop.gif\" border=\"0\"></a>";
					} else {
						echo "&nbsp;";
					}
					echo "</TD>\n";*/
					echo "	<TD><a href=\"javascript:CouponDelete('{$row->coupon_code}');\"><img src=\"images/btn_del7.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan=8 align=center>발급한 쿠폰내역이 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align=center class="font_size">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<?php if(ord($coupon_code)){?>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
				$sql = "SELECT COUNT(*) as cnt, COUNT(CASE WHEN (b.used='Y') THEN 1 ELSE NULL END) as cnt2, 
				COUNT(CASE WHEN b.id like '%{$search}%' THEN 1 ELSE NULL END) as cnt3 
				FROM tblcouponinfo a, tblcouponissue b WHERE a.coupon_code = '{$coupon_code}' 
				AND a.vender=0 AND a.coupon_code=b.coupon_code ";
				$result=pmysql_query($sql,get_db_conn());
				$row = pmysql_fetch_object($result);
				pmysql_free_result($result);
				$totalnum=$row->cnt;
				$usenum=$row->cnt2;
				$t_count2 = $row->cnt;
				if(ord($search)) $t_count2 = $row->cnt3;
				$paging = new Paging((int)$t_count2,10,20,'GoPage2');
				$gotopage2 = $paging->gotopage;
?>
				<tr>
					<td>
						<!-- 소제목 -->
						<div class="title_depth3_sub">발급받은 회원내역</div>
					</td>
				</tr>
				<tr>
					<td height=3 align=right style="padding-bottom:3pt;"><img src="images/icon_cuponname.gif" border="0" align=absmiddle><B><span class="font_orange"><?=$coupon_name?></span></B>&nbsp;<img src="images/icon_cupon_bal.gif" border="0" align=absmiddle><B><?=$totalnum?></B>개 <img src="images/icon_cupon_use.gif" border="0" align=absmiddle><?=$usenum?>개</td>
				</tr>
				<tr>
					<td>
					<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<col width=30></col>
					<col width=></col>
					<col width=135></col>
					<col width=150></col>
					<col width=100></col>
					<col width=100></col>
					<TR>
						<th>No</th>
						<th>아이디</th>
						<th>발급일</th>
						<th>유효기간</th>
						<th>사용여부</th>
						<th>비고</th>
					</TR>
<?php
					$sql = "SELECT * FROM tblcouponissue WHERE coupon_code = '{$coupon_code}' ";
					if(ord($search)) $sql.= "AND id LIKE '%{$search}%' ";
					$sql.= "ORDER BY date DESC";
					$sql = $paging->getSql($sql);
					$result = pmysql_query($sql,get_db_conn());
                    //exdebug($sql);
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count2-($setup['list_num'] * ($gotopage2-1))-$cnt);
						$cnt++;

						$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
						$regdate = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
						$used="<span class=\"font_orange\">미사용</span>";
						if($row->used=="Y") $used="<span class=\"font_blue\">사용함</span>";
						echo "<TR align=center>\n";
						echo "	<TD class=\"td_con2\">{$number}</TD>\n";
						echo "	<TD class=\"td_con1\">{$row->id}</TD>\n";
						echo "	<TD class=\"td_con1\">{$regdate}</TD>\n";
						echo "	<TD class=\"td_con1\">{$date}</TD>\n";
						echo "	<TD class=\"td_con1\">{$used}</TD>\n";
						if($row->used=="Y") {
							//echo "	<TD class=\"td_con1\"><a href=\"javascript:IssueCouponAgain('{$row->coupon_code}','{$row->id}');\"><img src=\"images/btn_again.gif\" border=\"0\"></a></TD>\n";
                            echo "	<TD class=\"td_con1\">-</TD>\n";
						} else {
                            // 쿠폰 중복 발급되는 형식으로 변경되었으므로, 해당쿠폰의 pk 값을 물고 가야됨. 2016-01-27 jhjeong
							echo "	<TD class=\"td_con1\"><a href=\"javascript:IssueCouponDelete('{$row->ci_no}', '{$row->coupon_code}','{$row->id}');\"><img src=\"images/btn_del7.gif\" border=\"0\"></a></TD>\n";
						}
						echo "</TR>\n";
					}
					pmysql_free_result($result);
					if ($cnt==0) {
						echo "<tr><td class=td_con2 colspan=6 align=center>회원에게 발급된 쿠폰내역이 없습니다.</td></tr>";
					}
?>

					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td align=center class="font_size">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="100%" class="main_sfont_non" height=6></td>
				</tr>
				<tr>
					<td width="100%" class="main_sfont_non">
					<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" width="100%">
					<tr>
						<td width="100%" bgcolor="white"><p align="center"><SELECT class="select">
						<option>아이디 검색</option>
						</SELECT> <INPUT class="input" size=30 name=search value="<?=$search?>"> <a href="javascript:id_search();"><img src="images/icon_search.gif" alt=검색 align=absMiddle border=0></a><A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align=absMiddle border=0 hspace="2"></A></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<?php }?>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>						
						<dl>
							<dt><span>발급된 쿠폰 내역관리</span></dt>
							<dd>
								- 쿠폰코드 클릭시 해당 쿠폰에 대한 자세한 내용을 확인할 수 있습니다.<br>
								- [조회] 버튼 클릭시 해당 쿠폰을 발급받은 회원을 확인할 수 있습니다.<br>
														<b>&nbsp;&nbsp;</b>발급받은 회원내역에서 [재발급] 버튼 클릭시 해당 쿠폰이 재발급 됩니다.<br>
														<b>&nbsp;&nbsp;</b>발급받은 회원내역에서 [삭제] 버튼 클릭시 해당 쿠폰이 삭제 됩니다.<br>
								- [발급중지] 버튼 클릭시 해당 쿠폰 발급을 중지합니다. 단, <span class="font_blue">발급중지 전에 이미 발급된 쿠폰은 사용 가능합니다.</span><br>
								- [완전삭제] 버튼 클릭시 해당 쿠폰 발급을 중지하며 또한 <span class="font_orange">완전삭제 전에 이미 발급된 쿠폰도 함께 삭제됩니다.</span><br>
								- <span class="font_orange">유효기간이 지난 쿠폰의 경우 [완전삭제]를 통해 정리</span>를 해주시기 바랍니다.
							</dd>	
						</dl>						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>

			<form name=cform action="coupon_view.php" method=post target=couponview>
				<input type=hidden name=coupon_code>
			</form>
			<form name=mform action="coupon_modify.php" method=post target=couponmodify>
				<input type=hidden name=coupon_code>
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
<?=$onload?>
<?php 
include("copyright.php");
