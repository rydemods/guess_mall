<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$curdate = date("YmdHi",strtotime('-10 min'));//복구 가능 시간 term

$orderby=$_POST["orderby"];
if(ord($orderby)==0) $orderby="ASC";

$type=$_POST["type"];
$ordercode=$_POST["ordercode"];

$CurrentTime = time();

$search_date=$_POST["search_date"];
$search_date=$search_date?$search_date:date("Y-m-d",$CurrentTime);
$search_date2=$search_date?str_replace("-","",$search_date."000000"):date("Ymd",$CurrentTime)."000000";

if($type=="restore" && strlen($ordercode)>=12) {	//복구
	$sql = "SELECT * FROM tblorderinfotemp WHERE ordercode='{$ordercode}' ";
	$result=pmysql_query($sql,get_db_conn());
	$data=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($data && ord($data->del_gbn)==0 && substr($data->ordercode,0,12)<=$curdate) {
		$sql = "SELECT a.productcode,a.productname,a.opt1_name,a.opt2_name,a.quantity, ";
		$sql.= "b.option_quantity,b.option1,b.option2,a.package_idx,a.assemble_idx,a.assemble_info FROM tblorderproducttemp a, tblproduct b ";
		$sql.= "WHERE a.productcode=b.productcode AND a.ordercode='{$ordercode}' ";
		$result=pmysql_query($sql,get_db_conn());
		$message="";
		while ($row=pmysql_fetch_object($result)) {
			$tmpoptq="";
			if(ord($artmpoptq[$row->productcode]))
				$optq=$artmpoptq[$row->productcode];
			else
				$optq=$row->option_quantity;

			if(strlen($optq)>51 && substr($row->opt1_name,0,5)!="[OPTG"){
				$tmpoptname1=explode(" : ",$row->opt1_name);
				$tmpoptname2=explode(" : ",$row->opt2_name);
				$tmpoption1=explode(",",$row->option1);
				$tmpoption2=explode(",",$row->option2);
				$cnt=1;
				$maxoptq = count($tmpoption1);
				while ($tmpoption1[$cnt]!=$tmpoptname1[1] && $cnt<$maxoptq) {
					$cnt++;
				}
				$opt_no1=$cnt;
				$cnt=1;
				$maxoptq2 = count($tmpoption2);
				while ($tmpoption2[$cnt]!=$tmpoptname2[1] && $cnt<$maxoptq2) {
					$cnt++;
				}
				$opt_no2=$cnt;
				$optioncnt = explode(",",ltrim($optq,','));
				if($optioncnt[($opt_no2-1)*10+($opt_no1-1)]!="") $optioncnt[($opt_no2-1)*10+($opt_no1-1)]+=$row->quantity;
				for($j=0;$j<5;$j++){
					for($i=0;$i<10;$i++){
						$tmpoptq.=",".$optioncnt[$j*10+$i];
					}
				}
				if(ord($tmpoptq) && $tmpoptq.","!=$optq){
					$artmpoptq[$row->productcode]=$tmpoptq;
					$tmpoptq=",option_quantity='{$tmpoptq},'";
				}else{
					$tmpoptq="";
					$message .="[{$row->productname} - ".$row->opt1_name.$row->opt2_name."]\\n";
				}
			}
			$sql = "UPDATE tblproduct SET quantity=quantity+".$row->quantity.$tmpoptq." ";
			$sql.= "WHERE productcode='{$row->productcode}'";
			pmysql_query($sql,get_db_conn());

			if(str_replace("","",str_replace(":","",str_replace("=","",$row->assemble_info)))) {
				$assemble_infoall_exp = explode("=",$row->assemble_info);

				if($row->package_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[0])))) {
					$package_info_exp = explode(":",$assemble_infoall_exp[0]);
					if(ord($package_info_exp[0])) {
						$package_productcode_exp = explode("",$package_info_exp[0]);
						for($k=0; $k<count($package_productcode_exp); $k++) {
							$sql2 = "UPDATE tblproduct SET ";
							$sql2.= "quantity		= quantity+{$row->quantity} ";
							$sql2.= "WHERE productcode='{$package_productcode_exp[$k]}' ";
							pmysql_query($sql2,get_db_conn());
						}
					}
				}
				
				if($row->assemble_idx>0 && ord(str_replace("","",str_replace(":","",$assemble_infoall_exp[1])))) {
					$assemble_info_exp = explode(":",$assemble_infoall_exp[1]);
					if(ord($assemble_info_exp[0])) {
						$assemble_productcode_exp = explode("",$assemble_info_exp[0]);
						for($k=0; $k<count($assemble_productcode_exp); $k++) {
							$sql2 = "UPDATE tblproduct SET ";
							$sql2.= "quantity		= quantity+{$row->quantity} ";
							$sql2.= "WHERE productcode='{$assemble_productcode_exp[$k]}' ";
							pmysql_query($sql2,get_db_conn());
						}
					}
				}
			}
		}
		pmysql_free_result($result);

		$sql = "SELECT productcode FROM tblorderproducttemp ";
		$sql.= "WHERE ordercode='{$ordercode}' AND productcode LIKE 'COU%' ";
		$result=pmysql_query($sql,get_db_conn());
		$rowcou=pmysql_fetch_object($result);
		pmysql_free_result($result);
		if($rowcou) {
			$coupon_code=substr($rowcou->productcode,3,-1);
			pmysql_query("UPDATE tblcouponissue SET used='N' WHERE id='{$data->id}' AND coupon_code='{$coupon_code}'",get_db_conn());
		}
		if($data->reserve>0) {
			pmysql_query("UPDATE tblmember SET reserve=reserve+{$data->reserve} WHERE id='{$data->id}'",get_db_conn());
		}
		pmysql_query("UPDATE tblorderinfotemp SET del_gbn='R' WHERE ordercode='{$ordercode}'",get_db_conn());

		$onload="<script>window.onload=function(){ alert(\"해당 주문시도 주문서의 수량/적립금/쿠폰 등을 복구하였습니다.\"); }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert(\"복구할 주문시도 주문서가 존재하지 않습니다.\"); }</script>";
	}
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>

<script language="JavaScript">

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","ordertempdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function OrderRestore(ordercode) {
	if(confirm("해당 주문시도 주문서의 수량/적립금/쿠폰 등을 복구 하시겠습니까?")) {
		document.idxform.type.value="restore";
		document.idxform.ordercode.value=ordercode;
		document.idxform.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>결제시도 주문서 관리</span></p></div></div>

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
			<?php include("menu_order.php"); ?>
			</td>
			<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">결제시도 주문서 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에서의 결제시도 건에 대한 현황 및 관리를 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
					<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>날짜 선택</span></th>
							<TD class="td_con1">
							<div class="table_none">
							<table cellpadding="0" cellspacing="0" width="200">
							<tr>
								<td width="281"><input type=text name=search_date value="<?=$search_date?>" size=15 onfocus="this.blur();" OnClick="Calendar(event)" class="select_selected"></td>
								<td width="281"><a href="javascript:document.form1.submit();"><img src="images/btn_search2.gif" border="0" hspace="0"></a></td>
							</tr>
							</table>
							</div>
							</TD>
						</TR>
					</TABLE>
					</div>
				</td>
			</tr>
			</form>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<?php
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드",/*"P"=>"신용카드(매매보호)",*/"M"=>"핸드폰");

		$qry.= "WHERE ordercode LIKE '".substr($search_date2,0,8)."%' ";
		$qry.= "AND (pay_data='신용카드 결제중' OR pay_data='실시간 계좌이체 결제중' OR pay_data='') ";

		$sql = "SELECT COUNT(*) as t_count, SUM(price) as t_price FROM tblorderinfotemp ".$qry;
		$result = pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		$t_count = (int)$row->t_count;
		$t_price = (int)$row->t_price;
		pmysql_free_result($result);
		$paging = new Paging($t_count,10,20);
		$gotopage = $paging->gotopage;				
		
		$sql = "SELECT * FROM tblorderinfotemp {$qry} ";
		$sql.= "ORDER BY ordercode {$orderby} ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
?>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><p align="left"><img src="images/icon_8a.gif" border="0"><B>정렬 : 
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</s></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					</B></td>
					<td width=""><p align="right"><img src="images/icon_8a.gif" border="0">현재 <b>1/0</b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
                <col width="40" />
                <col width="140" />
                <col width="100" />
                <col width="" />
                <col width="100" />
                <col width="100" />
                <col width="140" />
				<TR>
					<th>No</th>
					<th>주문시도 일자</th>
					<th>주문자명</th>
					<th>ID/주문시도번호</th>
					<th>결제방법</th>
					<th>가격</th>
					<th>수량/적립금/쿠폰</th>
				</TR>

<?php
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$cnt++;
			$ordercode=$row->ordercode;
			$name=$row->sender_name;
			if(substr($row->ordercode,20)=="X") {	//비회원
				$strid = substr($row->id,1,6);
			} else {	//회원
				$strid = "<A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
			}
			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";

			echo "<tr>\n";
			echo "	<TD><p align=\"center\"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}');\">{$number}</A></td>\n";
			echo "	<TD><p align=\"center\">{$date}</td>\n";
			echo "	<TD><p align=\"center\">{$name}</p></td>\n";
			echo "	<TD><div class=\"ta_l\"><span class=\"font_orange\"><b>{$strid}</b></span></div></td>\n";
			echo "	<TD><p align=\"center\"><b>".$arpm[$row->paymethod[0]]." ";
			if(strstr("B", $row->paymethod[0])) {	//무통장
				if (strlen($row->bank_date)==9 && $row->bank_date[8]=="X") echo "<font color=005000> [환불]</font>";
				else if (ord($row->bank_date)) echo " <font color=004000>[입금완료]</font>";
			} else if(strstr("VM", $row->paymethod[0])) {	//계좌이체/핸드폰
				if ($row->pay_flag=="0000") echo " <font color=0000a0>[결제완료]</font>";
				else echo " <font color=#757575>[결제실패]</font>";
			} else if(strstr("OQ", $row->paymethod[0])) {	//가상계좌
				if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[주문실패]</font>";
				else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [취소완료]</font>";
				else if ($row->pay_flag=="0000" && ord($row->bank_date)==0) echo "<font color=red> [미입금]</font>";
				else if ($row->pay_flag=="0000" && ord($row->bank_date)) echo "<font color=0000a0> [입금완료]</font>";
			} else {
				if (strcmp($row->pay_flag,"0000")!=0) echo " <font color=#757575>[카드실패]</font>";
				else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="N") echo "<font color=red> [카드승인]</font>";
				else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="Y") echo "<font color=0000a0> [결제완료]</font>";
				else if ($row->pay_flag=="0000" && $row->pay_admin_proc=="C") echo "<font color=005000> [취소완료]</font>";
			}
			echo "	</b></td>\n";
			echo "	<TD><p align=\"center\"><b>".number_format($row->price)."&nbsp;</b></p></TD>\n";
			if(ord($row->del_gbn)==0 && substr($row->ordercode,0,12)<=$curdate) {	//복구
				echo "	<TD class=\"table_cell1\"><p align=\"center\"><A HREF=\"javascript:OrderRestore('{$row->ordercode}');\" style=\"color:blue\"><img src=\"images/orderrestore_go.gif\" border=\"0\"></A></td>\n";
			} else if(strlen($row->del_gbn)!=0) {	//복구완료
				echo "	<TD><p align=\"center\"><img src=\"images/orderrestore_ok.gif\" border=\"0\"></td>\n";
			} else {
				echo "	<TD><p align=\"center\">--</td>\n";
			}
			echo "</tr>\n";

		}
		pmysql_free_result($result);

		if ($cnt==0) {
			echo "<tr><td class=\"td_con2\" colspan=\"7\" align=\"center\">검색된 주문내역이 없습니다.</td></tr>";
		}
?>
				
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			</form>
			<form name=detailform method="post" action="order_tempdetail.php" target="ordertempdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercode>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=search_date value="<?=$search_date?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>개월별 상품명 주문조회</span></dt>
						<dd>
							- 결제시도 목록이란 구매자가 주문서를 작성하고 최종 결제단계로 넘어가기 전<br />
							&nbsp;&nbsp;&nbsp;고객의 변심, 네트워크 장애, 구매자 PC 장애, 기타 예기치 못한 문제로 인해 최종 결제완료되지 못한 주문서들의 현황입니다.<br />
							- 결제시도 목록에 등록된 주문건은 적용된 <span class="point_c1">1시간 후</span>에 <span class="point_c1">[자동]</span>으로 해당상품으로 적용되었던 수량/적립금/쿠폰이 원상복구가 됩니다.<span class="point_c1">(권장)</span><br />
							- 결제시도 목록에 등록된 주문건은 <span class="point_c1">10분 후 [수동]</span>으로 복구할 수 있지만, 현재 결제중인 주문일 수 있으므로 권장하지 않습니다.<br />
							- 수동 복구시 해당 주문에 적용되었던 수량/적립금/쿠폰이 원상 복구됩니다.
						</dd>
					</dl>
					<dl>
						<dt><span>결제시도 목록에 등록되는 경우</span></dt>
						<dd>
							- ① 현재 결제중(ISP결제나 인증서 입력 등으로 시간이 다소 지연될 수 있음) : 최종 결제 완료시 결제시도 목록에서 자동 삭제됩니다.<br />
							- ② 구매고객이 주문서를 작성하고, 최종 결제완료 전에 고객의 변심으로 결제를 종료하는 경우<br />
							- ③ 구매고객의 사정 또는 PC의 브라우저 문제, 네트워크의 장애, 기타 다른 여러가지 이유로 인해 최종 결제완료 못한 경우
						</dd>
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
