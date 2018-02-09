<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");

//리스트 세팅
$setup[page_num] = 10;
$setup[list_num] = 20;

$block=$_REQUEST["block"];
$block2=$_REQUEST["block2"];
$gotopage=$_REQUEST["gotopage"];
$gotopage2=$_REQUEST["gotopage2"];
$search=$_POST["search"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

//if (emtpy($gotopage)) {
	//$gotopage = 1;
//}

if ($block2 != "") {
	$nowblock2 = $block2;
	$curpage2  = $block2 * $setup[page_num] + $gotopage2;
} else {
	$nowblock2 = 0;
}

if (empty($gotopage2)) {
	$gotopage2 = 1;
}
////////////////////////

$type=$_POST["type"];
$coupon_code=$_POST["coupon_code"];
$uid=$_POST["uid"];

if($type=="stop" && strlen($coupon_code)>0) {	//발급중지
	$sql = "UPDATE tblcouponinfo SET display='N',issue_type='D' ";
	$sql.= "WHERE coupon_code='".$coupon_code."' ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	pmysql_query($sql,get_db_conn());
	if(!pmysql_errno()) {
		echo "<html></head><body onload=\"alert('해당 쿠폰에 대해서 발급중지 처리가 완료되었습니다.\\n\\n기존 발급된 쿠폰만 사용가능합니다.');parent.GoInit('');\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.');\"></body></html>";exit;
	}
	exit;
} else if($type=="delete" && strlen($coupon_code)>0) {	//완전삭제
	$sql = "SELECT * FROM tblcouponinfo ";
	$sql.= "WHERE coupon_code='".$coupon_code."' ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblcouponinfo ";
		$sql.= "WHERE coupon_code='".$coupon_code."' ";
		$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
		pmysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblcouponissue ";
		$sql.= "WHERE coupon_code='".$coupon_code."' ";
		pmysql_query($sql,get_db_conn());
		if(file_exists($Dir.DataDir."shopimages/etc/COUPON".$coupon_code.".gif")) {
			unlink($Dir.DataDir."shopimages/etc/COUPON".$coupon_code.".gif");
		}
		if(!pmysql_errno()) {
			echo "<html></head><body onload=\"alert('해당 쿠폰의 모든 내역이 완전 삭제되었습니다.');parent.GoInit('');\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.');\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 쿠폰내역이 존재하지 않습니다.');\"></body></html>";exit;
	}
	pmysql_free_result($result);
	exit;
} else if($type=="issueagain" && strlen($coupon_code)>0 && strlen($uid)>0) {	//회원에게 발급한 쿠폰 재발급
	$sql = "SELECT * FROM tblcouponinfo ";
	$sql.= "WHERE coupon_code='".$coupon_code."' ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$sql = "UPDATE tblcouponissue SET used='N' WHERE coupon_code = '".$coupon_code."' AND id = '".$uid."' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_errno()) {
			echo "<html></head><body onload=\"alert('".$uid." 회원님께 해당 쿠폰을 재발급 되었습니다.');parent.GoInit('".$coupon_code."');\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.');\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 쿠폰내역이 존재하지 않습니다.');\"></body></html>";exit;
	}
	pmysql_free_result($result);
	exit;
} else if($type=="issuedelete" && strlen($coupon_code)>0 && strlen($uid)>0) {	//회원에게 발급한 쿠폰 삭제
	$sql = "SELECT * FROM tblcouponinfo ";
	$sql.= "WHERE coupon_code='".$coupon_code."' ";
	$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$sql = "DELETE FROM tblcouponissue WHERE coupon_code = '".$coupon_code."' AND id = '".$uid."' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_errno()) {
			echo "<html></head><body onload=\"alert('".$uid." 회원님에게 발급된 쿠폰이 삭제되었습니다.');parent.GoInit('".$coupon_code."');\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.');\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.');\"></body></html>";exit;
	}
	pmysql_free_result($result);
	exit;
}
?>
<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CouponView(code) {
	window.open("about:blank","couponview","width=650,height=650,scrollbars=no");
	document.cform.coupon_code.value=code;
	document.cform.submit();
}

function CouponIssue(code){
	document.form1.coupon_code.value=code;
	document.form1.block2.value="";
	document.form1.gotopage2.value="";
	document.form1.target="";
	document.form1.submit();
}

function CouponStop(code) {
	if(confirm("기존 회원에게 발급된 쿠폰은 사용이 가능합니다.\n\n해당 쿠폰 발급을 중지하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.type.value="stop";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function CouponDelete(code) {
	if(confirm("기존 회원에게 발급된 쿠폰까지 모두 삭제됩니다.\n\n해당 쿠폰을 완전 삭제하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.type.value="delete";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function IssueCouponAgain(code,uid) {
	if(confirm(uid+" 회원님에게 쿠폰을 재발급 하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.uid.value=uid;
		document.form1.type.value="issueagain";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function IssueCouponDelete(code,uid) {
	if(confirm(uid+" 회원님에게 발급한 쿠폰을 삭제하시겠습니까?")) {
		document.form1.coupon_code.value=code;
		document.form1.uid.value=uid;
		document.form1.type.value="issuedelete";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function GoPage(block,gotopage) {
	document.form1.type.value = "";
	document.form1.coupon_code.value = "";
	document.form1.uid.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.block2.value="";
	document.form1.gotopage2.value="";
	document.form1.target="";
	document.form1.submit();
}

function GoPage2(block,gotopage) {
	document.form1.type.value = "";
	document.form1.uid.value = "";
	document.form1.block2.value = block;
	document.form1.gotopage2.value = gotopage;
	document.form1.target="";
	document.form1.submit();
}

function GoInit(coupon_code) {
	document.form1.type.value = "";
	document.form1.coupon_code.value = coupon_code;
	document.form1.uid.value = "";
	document.form1.target="";
	document.form1.submit();
}
</script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>발급된 쿠폰내역 관리</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>발급된 쿠폰내역 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 쿠폰코드를 클릭하시면 발급된 쿠폰에 대한 상세정보를 보실 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> [조회] 버튼을 클릭하시면 해당 쿠폰에 대한 발급받은 회원내역을 조회하실 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> [발급중지] 버튼을 클릭하시면 기존 발급된 쿠폰은 사용하되, 더이상 신규발급은 하지 않음으로 설정됩니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> [삭제] 버튼을 클릭하시면 기존 발급된 쿠폰까지 모두 삭제되고, 신규발급도 안됩니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 유효기간이 지난 쿠폰의 경우 [삭제]로 정리를 해주시기 바랍니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">

				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 발급한 쿠폰내역</td>
				</tr>
				<tr><td height=5></td></tr>

				<form name=form1 action="<?=$_SERVER[PHP_SELF]?>" method=post>
				<input type=hidden name=type>
				<input type=hidden name=coupon_code value="<?=$coupon_code?>">
				<input type=hidden name=uid>
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type=hidden name=block2 value="<?=$block2?>">
				<input type=hidden name=gotopage2 value="<?=$gotopage2?>">
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td bgcolor=E7E7E7>
					<table width=100% border=0 cellspacing=1 cellpadding=0 style="table-layout:fixed">
					<col width=35></col>
					<col width=65></col>
					<col width=></col>
					<col width=90></col>
					<col width=110></col>
					<col width=60></col>
					<col width=70></col>
					<col width=45></col>
					<tr height=35 align=center bgcolor=F5F5F5>
						<td align=center><B>번호</B></td>
						<td align=center><B>쿠폰코드</B></td>
						<td align=center><B>쿠폰명</B></td>
						<td align=center><B>금액/할인율</B></td>
						<td align=center><B>유효기간<B></td>
						<td align=center><B>발급내역</B></td>
						<td align=center><B>발급중지</B></td>
						<td align=center><B>삭제</B></td>
					</tr>
<?
					$sql = "SELECT COUNT(*) as t_count FROM tblcouponinfo ";
					$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
					$result = pmysql_query($sql,get_db_conn());
					$row = pmysql_fetch_object($result);
					pmysql_free_result($result);
					$t_count = $row->t_count;
					$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

					$sql = "SELECT * FROM tblcouponinfo WHERE vender='".$_VenderInfo->getVidx()."' ORDER BY date DESC ";
					$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage - 1));
					$result = pmysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
						$cnt++;

						if($coupon_code==$row->coupon_code) {
							$coupon_name=$row->coupon_name;
						}

						if($row->sale_type<=2) $dan="%";
						else $dan="원";
						if($row->sale_type%2==0) $sale = "할인";
						else $sale = "적립";
						if($row->date_start>0) {
							$date = substr($row->date_start,2,2).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,2,2).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
						} else {
							$date = abs($row->date_start)."일동안";
						}

						echo "<tr height=30 bgcolor=#FFFFFF>\n";
						echo "	<td align=center style=\"font-size:8pt\">".$number."</td>\n";
						echo "	<td align=center style=\"font-size:8pt\"><A HREF=\"javascript:CouponView('".$row->coupon_code."');\"><B>".$row->coupon_code."</B></A></td>\n";
						echo "	<td style=\"padding-left:5;color:#003399\"><nobr>".$row->coupon_name."</td>\n";
						echo "	<td align=center><font color=\"".($sale=="할인"?"#FF0000":"#0000FF")."\">".number_format($row->sale_money).$dan." ".$sale."</td>\n";
						echo "	<td align=center style=\"font-size:8pt\">".$date."</td>\n";
						echo "	<td align=center><A HREF=\"javascript:CouponIssue('".$row->coupon_code."')\"><B>[조회]</B></A></td>\n";
						echo "	<td align=center>";
						if($row->issue_type!="D") {
							echo "<A HREF=\"javascript:CouponStop('".$row->coupon_code."')\"><font color=red><B>[발급중지]</B></font></A>";
						} else {
							echo "&nbsp;";
						}
						echo "	</td>\n";
						echo "	<td align=center><A HREF=\"javascript:CouponDelete('".$row->coupon_code."')\"><font color=red><B>[삭제]</B></font></A></td>\n";
						echo "</tr>\n";
					}
					pmysql_free_result($result);
					if ($cnt==0) {
						echo "<tr><td height=30 bgcolor=#FFFFFF colspan=8 align=center>발급한 쿠폰내역이 없습니다.</td></tr>";
					} else {
						$total_block = intval($pagecount / $setup[page_num]);
						if (($pagecount % $setup[page_num]) > 0) {
							$total_block = $total_block + 1;
						}
						$total_block = $total_block - 1;
						if (ceil($t_count/$setup[list_num]) > 0) {
							// 이전	x개 출력하는 부분-시작
							$a_first_block = "";
							if ($nowblock > 0) {
								$a_first_block .= "<a href='javascript:GoPage(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";
								$prev_page_exists = true;
							}
							$a_prev_page = "";
							if ($nowblock > 0) {
								$a_prev_page .= "<a href='javascript:GoPage(".($nowblock-1).",".($setup[page_num]*($block-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

								$a_prev_page = $a_first_block.$a_prev_page;
							}
							if (intval($total_block) <> intval($nowblock)) {
								$print_page = "";
								for ($gopage = 1; $gopage <= $setup[page_num]; $gopage++) {
									if ((intval($nowblock*$setup[page_num]) + $gopage) == intval($gotopage)) {
										$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></font> ";
									} else {
										$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
									}
								}
							} else {
								if (($pagecount % $setup[page_num]) == 0) {
									$lastpage = $setup[page_num];
								} else {
									$lastpage = $pagecount % $setup[page_num];
								}
								for ($gopage = 1; $gopage <= $lastpage; $gopage++) {
									if (intval($nowblock*$setup[page_num]) + $gopage == intval($gotopage)) {
										$print_page .= "<FONT color=red><B>".(intval($nowblock*$setup[page_num]) + $gopage)."</B></FONT> ";
									} else {
										$print_page .= "<a href='javascript:GoPage(".$nowblock.",".(intval($nowblock*$setup[page_num]) + $gopage).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock*$setup[page_num]) + $gopage)."';return true\">[".(intval($nowblock*$setup[page_num]) + $gopage)."]</a> ";
									}
								}
							}
							$a_last_block = "";
							if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
								$last_block = ceil($t_count/($setup[list_num]*$setup[page_num])) - 1;
								$last_gotopage = ceil($t_count/$setup[list_num]);
								$a_last_block .= " <a href='javascript:GoPage(".$last_block.",".$last_gotopage.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";
								$next_page_exists = true;
							}
							$a_next_page = "";
							if ((intval($total_block) > 0) && (intval($nowblock) < intval($total_block))) {
								$a_next_page .= " <a href='javascript:GoPage(".($nowblock+1).",".($setup[page_num]*($nowblock+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";
								$a_next_page = $a_next_page.$a_last_block;
							}
						} else {
							$print_page = "<B>1</B>";
						}
						$pageing=$a_div_prev_page.$a_prev_page.$print_page.$a_next_page.$a_div_next_page;
					}
?>
					</table>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td align=center>

					<?=$pageing?>

					</td>
				</tr>
				<tr><td height=10></td></tr>

				<?if(strlen($coupon_code)>0){?>

				<tr><td height=20></td></tr>
<?
				$sql = "SELECT COUNT(*) as cnt, COUNT(CASE WHEN b.used='Y' THEN 1 ELSE NULL END) as cnt2, ";
				$sql.= "COUNT(CASE WHEN b.id like '%".$search."%' THEN 1 ELSE NULL END) as cnt3 ";
				$sql.= "FROM tblcouponinfo a, tblcouponissue b WHERE a.coupon_code = '".$coupon_code."' AND a.vender='".$_VenderInfo->getVidx()."' ";
				$sql.= "AND a.coupon_code=b.coupon_code ";
				$result=pmysql_query($sql,get_db_conn());
				$row = pmysql_fetch_object($result);
				pmysql_free_result($result);
				$totalnum=$row->cnt;
				$usenum=$row->cnt2;
				$t_count2 = $row->cnt;
				if(strlen($search)>0) $t_count2 = $row->cnt3;
				$pagecount2 = (($t_count2 - 1) / $setup[list_num]) + 1;
?>
				<tr>
					<td>
					<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
					<col width=></col>
					<col width=230></col>
					<tr>
						<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 발급받은 회원내역 <FONT style="font-size:8pt;color:#2A97A7">(쿠폰명 : <B><?=$coupon_name?></B> , &nbsp;&nbsp;&nbsp;발급:<B><?=$totalnum?></B>개, 사용:<B><?=$usenum?></B>개)</FONT></td>
						<td align=right>
						＊아이디 검색 : <input type=text name=search value="<?=$search?>" style="width:70">
						<img src=images/btn_inquery02.gif border=0 align=absmiddle style="cursor:hand" onclick="document.form1.type.value='';document.form1.uid.value='';document.form1.submit();">
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=2></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td bgcolor=E7E7E7>
					<table width=100% border=0 cellspacing=1 cellpadding=0 style="table-layout:fixed">
					<col width=60></col>
					<col width=140></col>
					<col width=140></col>
					<col width=></col>
					<col width=80></col>
					<col width=80></col>
					<tr height=35 align=center bgcolor=F5F5F5>
						<td align=center><B>번호</B></td>
						<td align=center><B>아이디</B></td>
						<td align=center><B>발급일</B></td>
						<td align=center><B>유효기간</B></td>
						<td align=center><B>사용여부<B></td>
						<td align=center><B>비고</B></td>
					</tr>
<?
					if($t_count2>0) {
						$sql = "SELECT b.* FROM tblcouponinfo a, tblcouponissue b ";
						$sql.= "WHERE a.coupon_code='".$coupon_code."' AND a.vender='".$_VenderInfo->getVidx()."' ";
						$sql.= "AND a.coupon_code=b.coupon_code ";
						if(strlen($search)>0) $sql.= "AND b.id LIKE '%".$search."%' ";
						$sql.= "ORDER BY b.date DESC ";
						$sql.= "LIMIT " . $setup[list_num]." OFFSET ".($setup[list_num] * ($gotopage2 - 1));
						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;
						while($row=pmysql_fetch_object($result)) {
							$number = ($t_count2-($setup[list_num] * ($gotopage2-1))-$cnt);
							$cnt++;

							$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)." ~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
							$regdate = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2);
							$used="<FONT COLOR=\"red\">미사용</FONT>";
							if($row->used=="Y") $used="<FONT COLOR=\"blue\">사용함</FONT>";
							echo "<tr height=30 bgcolor=#FFFFFF>\n";
							echo "	<td align=center>".$number."</td>\n";
							echo "	<td align=center>".$row->id."</td>\n";
							echo "	<td align=center>".$regdate."</td>\n";
							echo "	<td align=center>".$date."</td>\n";
							echo "	<td align=center>".$used."</td>\n";
							if($row->used=="Y") {
								echo "	<td align=center><A HREF=\"javascript:IssueCouponAgain('".$row->coupon_code."','".$row->id."')\"><font color=blue><B>[재발급]</B></font></A></td>\n";
							} else {
								echo "	<td align=center><A HREF=\"javascript:IssueCouponDelete('".$row->coupon_code."','".$row->id."')\"><font color=red><B>[삭제]</B></font></A></td>\n";
							}
							echo "</tr>\n";
						}
						pmysql_free_result($result);

						$total_block2 = intval($pagecount2 / $setup[page_num]);
						if (($pagecount2 % $setup[page_num]) > 0) {
							$total_block2 = $total_block2 + 1;
						}
						$total_block2 = $total_block2 - 1;
						if (ceil($t_count2/$setup[list_num]) > 0) {
							// 이전	x개 출력하는 부분-시작
							$a_first_block2 = "";
							if ($nowblock2 > 0) {
								$a_first_block2 .= "<a href='javascript:GoPage2(0,1);' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='첫 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev_end.gif border=0 align=absmiddle></a> ";

								$prev_page_exists2 = true;
							}
							$a_prev_page2 = "";
							if ($nowblock2 > 0) {
								$a_prev_page2 .= "<a href='javascript:GoPage(".($nowblock2-1).",".($setup[page_num]*($block2-1)+$setup[page_num]).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='이전 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_miniprev.gif border=0 align=absmiddle></a> ";

								$a_prev_page2 = $a_first_block2.$a_prev_page2;
							}
							if (intval($total_block2) <> intval($nowblock2)) {
								$print_page2 = "";
								for ($gopage2 = 1; $gopage2 <= $setup[page_num]; $gopage2++) {
									if ((intval($nowblock2*$setup[page_num]) + $gopage2) == intval($gotopage2)) {
										$print_page2 .= "<FONT color=red><B>".(intval($nowblock2*$setup[page_num]) + $gopage2)."</B></font> ";
									} else {
										$print_page2 .= "<a href='javascript:GoPage2(".$nowblock2.",".(intval($nowblock2*$setup[page_num]) + $gopage2).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='페이지 : ".(intval($nowblock2*$setup[page_num]) + $gopage2)."';return true\">[".(intval($nowblock2*$setup[page_num]) + $gopage2)."]</a> ";
									}
								}
							} else {
								if (($pagecount2 % $setup[page_num]) == 0) {
									$lastpage2 = $setup[page_num];
								} else {
									$lastpage2 = $pagecount2 % $setup[page_num];
								}
								for ($gopage2 = 1; $gopage2 <= $lastpage2; $gopage2++) {
									if (intval($nowblock2*$setup[page_num]) + $gopage2 == intval($gotopage2)) {
										$print_page2 .= "<FONT color=red><B>".(intval($nowblock2*$setup[page_num]) + $gopage2)."</B></FONT> ";
									} else {
										$print_page2 .= "<a href='javascript:GoPage2(".$nowblock2.",".(intval($nowblock2*$setup[page_num]) + $gopage2).");' onMouseOver=\"window.status='페이지 : ".(intval($nowblock2*$setup[page_num]) + $gopage2)."';return true\">[".(intval($nowblock2*$setup[page_num]) + $gopage2)."]</a> ";
									}
								}
							}
							$a_last_block2 = "";
							if ((intval($total_block2) > 0) && (intval($nowblock2) < intval($total_block2))) {
								$last_block2 = ceil($t_count2/($setup[list_num]*$setup[page_num])) - 1;
								$last_gotopage2 = ceil($t_count2/$setup[list_num]);

								$a_last_block2 .= " <a href='javascript:GoPage(".$last_block2.",".$last_gotopage2.");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='마지막 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext_end.gif border=0 align=absmiddle></a>";

								$next_page_exists2 = true;
							}
							$a_next_page2 = "";
							if ((intval($total_block2) > 0) && (intval($nowblock2) < intval($total_block2))) {
								$a_next_page2 .= " <a href='javascript:GoPage(".($nowblock2+1).",".($setup[page_num]*($nowblock2+1)+1).");' onMouseOut=\"window.status='';return true\" onMouseOver=\"window.status='다음 ".$setup[page_num]." 페이지';return true\"><img src=".$Dir."images/minishop/btn_mininext.gif border=0 align=absmiddle></a>";

								$a_next_page2 = $a_next_page2.$a_last_block2;
							}
						} else {
							$print_page2 = "<B>1</B>";
						}
						$pageing2=$a_div_prev_page2.$a_prev_page2.$print_page2.$a_next_page2.$a_div_next_page2;
					} else {
						echo "<tr><td height=30 bgcolor=#FFFFFF colspan=6 align=center>조회된 내용이 없습니다.</td></tr>";
					}
?>
					</table>
					</td>
				</tr>
				<tr><td height=10></td></tr>
				<tr>
					<td align=center>

					<?=$pageing2?>

					</td>
				</tr>
				<?}?>

				</form>
				</table>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

	</td>
</tr>

<form name=cform action="coupon_view.php" method=post target=couponview>
<input type=hidden name=coupon_code>
</form>

</table>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

<?=$onload?>

<?php include("copyright.php"); ?>
