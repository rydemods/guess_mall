<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-3";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$storeimagepath=DocumentRoot."/gonggu/upfile/";

$mode=$_POST["mode"];
$gong_seq=$_POST["gong_seq"];
$allid=$_POST["allid"];

if($mode=="delete" && ord($gong_seq)) {
	$sql = "SELECT COUNT(*) as cnt FROM tblgongresult WHERE gong_seq='{$gong_seq}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($row->cnt>0) {
		$onload="<script>window.onload=function(){alert(\"해당 공동구매에 참여자가 있어 삭제가 불가능합니다.\\n\\n참여자를 먼저 삭제 후 삭제하시기 바랍니다.\"); }</script>";
	} else {
		$sql = "SELECT image1,image2,image3 FROM tblgonginfo WHERE gong_seq='{$gong_seq}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		pmysql_free_result($result);
		$oldfiles=array(&$row->image1,&$row->image2,&$row->image3);

		$sql = "DELETE FROM tblgonginfo WHERE gong_seq='{$gong_seq}' ";
		$delete=pmysql_query($sql,get_db_conn());
		if($delete) {
			$sql = "DELETE FROM tblgongresult WHERE gong_seq='{$gong_seq}' ";
			pmysql_query($sql,get_db_conn());
			for($i=0;$i<3;$i++) {
				if(ord($oldfiles[$i]) && file_exists($storeimagepath.$oldfiles[$i])) {
					unlink($storeimagepath.$oldfiles[$i]);
				}
			}
			$onload="<script>window.onload=function(){ alert(\"해당 공동구매를 삭제하였습니다.\"); }</script>";
		} else {
			$onload="<script>window.onload=function(){ alert(\"해당 공동구매중 오류가 발생하였습니다.\"); }</script>";
		}
	}
	$gong_seq="";
} else if($mode=="process_receipt" && ord($gong_seq) && ord($allid)) {
	$allid=str_replace("\\\\","",$allid);
	$sql = "UPDATE tblgongresult SET process_gbn='B' 
	WHERE gong_seq='{$gong_seq}' AND id IN ({$allid})";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"선택하신 공동구매 참여자를 입금확인 하였습니다.\"); }</script>";
} else if($mode=="process_deli" && ord($gong_seq) && ord($allid)) {
	$allid=str_replace("\\\\","",$allid);
	$sql = "UPDATE tblgongresult SET process_gbn='E' 
	WHERE gong_seq='{$gong_seq}' AND id IN ({$allid})";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"선택하신 공동구매 참여자를 배송확인 하였습니다.\"); }</script>";
} else if($mode=="process_del" && ord($gong_seq) && ord($allid)) {
	$allid=str_replace("\\\\","",$allid);
	$sql = "DELETE FROM tblgongresult WHERE gong_seq='{$gong_seq}' AND id IN ({$allid})";
	pmysql_query($sql,get_db_conn());

	$sql = "SELECT SUM(buy_cnt) as buy_cnt FROM tblgongresult WHERE gong_seq='{$gong_seq}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($row->buy_cnt==NULL || ord($row->buy_cnt)==0) $buy_cnt=0;
	else $buy_cnt=$row->buy_cnt;

	$sql = "UPDATE tblgonginfo SET bid_cnt='{$buy_cnt}' WHERE gong_seq='{$gong_seq}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"선택하신 공동구매 참여자를 삭제 하였습니다.\"); }</script>";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function GongModify(gong_seq) {
	document.modifyform.gong_seq.value=gong_seq;
	document.modifyform.submit();
}

function GongDelete(gong_seq) {
	if(confirm("해당 공동구매를 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.gong_seq.value=gong_seq;
		document.form1.submit();
	}
}

function GongMail(gong_seq) {
	window.open('gong_gongmail_pop.php?gong_seq='+gong_seq,"gong_mail","width=430,height=430");
}

function BidsView(gong_seq) {
	document.form1.gong_seq.value=gong_seq;
	document.form1.submit();
}

function AllExcel(gong_seq) {
	document.excelform.submit();
}

function MemberView(id){
	document.memberform.search.value=id;
	document.memberform.submit();
}

function MemberMail(mail){
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}

function GoPage(block,gotopage) {
	document.form1.gong_seq.value="";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function CheckAllReceipt(checked) {
	try {
		for(i=1;i<document.form2.ckreceipt.length;i++) {
			document.form2.ckreceipt[i].checked=checked;
		}
	} catch(e) {}
}
function CheckAllDeli(checked) {
	try {
		for(i=1;i<document.form2.ckdeli.length;i++) {
			document.form2.ckdeli[i].checked=checked;
		}
	} catch(e) {}
}
function CheckAllDel(checked) {
	try {
		for(i=1;i<document.form2.ckdel.length;i++) {
			document.form2.ckdel[i].checked=checked;
		}
	} catch(e) {}
}

function ResultReceipt() {
	allid="";
	try {
		for(i=1;i<document.form2.ckreceipt.length;i++) {
			if(document.form2.ckreceipt[i].checked) allid+=",'"+document.form2.ckreceipt[i].value+"'";
		}
		if(allid.length==0) {
			alert("입금확인할 공동구매 참여자를 선택하세요.");
			return;
		} else {
			if(!confirm("선택하신 공구 참여자를 입금확인 하시겠습니까?")) return;
			allid=allid.substring(1,allid.length);
			document.prform.mode.value="process_receipt";
			document.prform.allid.value=allid;
			document.prform.submit();
		}
	} catch(e) {}
}
function ResultDeli() {
	allid="";
	try {
		for(i=1;i<document.form2.ckdeli.length;i++) {
			if(document.form2.ckdeli[i].checked) allid+=",'"+document.form2.ckdeli[i].value+"'";
		}
		if(allid.length==0) {
			alert("배송확인할 공동구매 참여자를 선택하세요.");
			return;
		} else {
			if(!confirm("선택하신 공구 참여자를 배송확인 하시겠습니까?")) return;
			allid=allid.substring(1,allid.length);
			document.prform.mode.value="process_deli";
			document.prform.allid.value=allid;
			document.prform.submit();
		}
	} catch(e) {}
}
function ResultDel() {
	allid="";
	try {
		for(i=1;i<document.form2.ckdel.length;i++) {
			if(document.form2.ckdel[i].checked) allid+=",'"+document.form2.ckdel[i].value+"'";
		}
		if(allid.length==0) {
			alert("삭제할 공동구매 참여자를 선택하세요.");
			return;
		} else {
			if(!confirm("선택하신 공구 참여자를 삭제 하시겠습니까?")) return;
			allid=allid.substring(1,allid.length);
			document.prform.mode.value="process_del";
			document.prform.allid.value=allid;
			document.prform.submit();
		}
	} catch(e) {}
}


function MemoMouseOver(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	obj._tid = setTimeout("MemoView(WinObj)",200);
}
function MemoView(WinObj) {
	WinObj.style.visibility = "visible";
}
function MemoMouseOut(cnt) {
	obj = event.srcElement;
	WinObj=eval("document.all.memo"+cnt);
	WinObj.style.visibility = "hidden";
	clearTimeout(obj._tid);
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 공동구매관리 &gt;<span>가격변동형 등록공구 관리</span></p></div></div>
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
			<?php include("menu_gong.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=gong_seq>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">

			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">가격변동형 등록공구 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 공동구매를 관리할 수 있습니다.</span></div>
					<!-- 소제목 -->
					<div class="title_depth3_sub">공동구매 조회 및 관리</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=90></col>
				<col width=></col>
				<col width=75></col>
				<col width=60></col>
				<col width=50></col>
				<col width=50></col>
				<TR>
					<th>공구 마감일</th>
					<th>공구 상품명</th>
					<th>참여자</th>
					<th>현재가</th>
					<th>참여수</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=6;
				$sql = "SELECT COUNT(*) as t_count FROM tblgonginfo "; 
				$result = pmysql_query($sql,get_db_conn());
				$paging = new Paging($sql,10,15);
				$t_count = $paging->t_count;	
				$gotopage = $paging->gotopage;						

				$sql = "SELECT * FROM tblgonginfo ORDER BY end_date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$end_date=substr($row->end_date,0,4)."/".substr($row->end_date,4,2)."/".substr($row->end_date,6,2)."(".substr($row->end_date,8,2).":".substr($row->end_date,10,2).")";
					
					if($row->count){
						$gong_no=intval($row->bid_cnt/$row->count);
					}else{
						$gong_no=0;
					}
					$now_price=$row->start_price-($gong_no*$row->down_price);
					if($now_price<$row->mini_price) $now_price=$row->mini_price;

					echo "<TR>\n";
					echo "	<TD align=center class=\"td_con2\">";
					if($row->end_date<date("YmdHis") || $row->quantity<=$row->bid_cnt) {
						echo "<img src=\"images/gong_auctionlist_endicon2.gif\" border=\"0\">";
					} else {
						echo $end_date;
					}
					echo "	</TD>\n";
					if($row->bid_cnt==0) {	//참여자 없음
						echo "	<TD><div class=\"ta_l\"><A HREF=\"javascript:GongModify('{$row->gong_seq}');\">{$row->gong_name}</A></div></TD>\n";
						echo "	<TD>참여자 없음</td>\n";
					} else {	//입찰자 있음
						echo "	<TD><div class=\"ta_l\">{$row->gong_name}</div></TD>\n";
						echo "	<TD><a href=\"javascript:BidsView('{$row->gong_seq}')\"><img src=\"images/gong_gongchangelist_cham.gif\" border=\"0\"></a></TD>\n";
					}
					echo "	<TD><b><font color=\"#220F03\">".number_format($now_price)."원</font></b></TD>\n";
					echo "	<TD>".(int)$row->bid_cnt."</TD>\n";
					if($row->bid_cnt==0 || $row->gbn=="E") {
						echo "	<TD><a href=\"javascript:GongDelete('{$row->gong_seq}')\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					} else if($row->end_date<date("YmdHis") || $row->quantity==$row->bid_cnt) {
						echo "	<TD><a href=\"javascript:GongMail('{$row->gong_seq}')\"><img src=\"images/btn_mail.gif\" border=\"0\"></a></TD>\n";
					} else {
						echo "	<TD>&nbsp;</TD>\n";
					}
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>검색된 리뷰 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" align=center class="font_size">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
<?php
			$row=null;
			if(ord($gong_seq)) {
				$sql = "SELECT * FROM tblgonginfo WHERE gong_seq='{$gong_seq}' ";
				$result=pmysql_query($sql,get_db_conn());
				$row=pmysql_fetch_object($result);
				pmysql_free_result($result);
			}
			if($row){
?>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td><span class="font_blue" style="font-size:9pt;"><b><img align=absmiddle src="images/btn_sound01.gif" border="0">[</span><span class="font_blue1" style="font-size:9pt;"><?=$row->gong_name?></span><span class="font_blue" style="font-size:9pt;">]</b><B>공구 참여자 관리</B><b> </span></b></td>
			</tr>
			<tr>
				<td height=3></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<tr>
				<td align="center">
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=60></col>
				<col width=60></col>
				<col width=></col>
				<col width=60></col>
				<col width=35></col>
				<col width=35></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<TR>
					<th>아이디</th>
					<th>이름</th>
					<th>이메일</th>
					<th>전화/주소</th>
					<th>메모</th>
					<th>수량</th>
					<th><INPUT id=idx_receipt onclick=CheckAllReceipt(this.checked) type=checkbox><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_receipt>입금확인</LABEL></th>
					<th><INPUT id=idx_deli onclick=CheckAllDeli(this.checked) type=checkbox><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_deli>배송확인</LABEL></th>
					<th><INPUT id=idx_del onclick=CheckAllDel(this.checked) type=checkbox><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_del>삭제하기</LABEL></th>
				</TR>
				<input type=hidden name=ckreceipt>
				<input type=hidden name=ckdeli>
				<input type=hidden name=ckdel>
<?php
				$sql = "SELECT * FROM tblgongresult WHERE gong_seq='{$gong_seq}' ";
				$result=pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$i++;
					echo "<TR>\n";
					echo "	<TD><A HREF=\"javascript:MemberView('{$row->id}')\">{$row->id}</A></TD>\n";
					echo "	<TD>{$row->name}</TD>\n";
					echo "	<TD><A HREF=\"javascript:MemberMail('{$row->email}');\">{$row->email}</A></TD>\n";
					$tel_disabled="";
					$addr_disabled="";
					if(ord($row->tel)==0) $tel_disabled="disabled";
					if(ord($row->address)==0) $addr_disabled="disabled";
					echo "	<TD><a href=\"javascript:alert('{$row->tel}');\" {$tel_disabled}><IMG SRC=\"images/member_tel.gif\" border=\"0\"></a>&nbsp;<a href=\"javascript:alert('{$row->address}');\" {$addr_disabled}><IMG SRC=\"images/addr_home.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD>";
					if(ord($row->memo)) {
						echo "	<a href=\"javascript:alert('".str_replace("\r\n","",$row->memo)."');\" onMouseOver='MemoMouseOver($i)' onMouseOut=\"MemoMouseOut($i);\"><IMG SRC=\"images/btn_memo.gif\" border=\"0\"></a>";
						echo "	<div id=memo{$i} style=\"position:absolute; z-index:100; visibility:hidden;\">\n";
						echo "	<table width=250 border=0 cellspacing=0 cellpadding=5 bgcolor=#A47917>\n";
						echo "	<tr><td style=\"color:#ffffff\">{$row->memo}</td></tr>\n";
						echo "	</table>\n";
						echo "	</div>\n";
					} else {
						echo "	<IMG SRC=\"images/btn_memor.gif\" border=\"0\">";
					}
					echo "	</TD>\n";
					echo "	<TD>{$row->buy_cnt}</TD>\n";
					echo "	<TD>";
					if($row->process_gbn=="I") {
						echo "<input type=checkbox name=ckreceipt value=\"{$row->id}\">";
					} else {
						echo "완료";
					}
					echo "	</TD>\n";
					echo "	<TD>";
					if($row->process_gbn=="B") {
						echo "<input type=checkbox name=ckdeli value=\"{$row->id}\">";
					} else if($row->process_gbn=="I") {
						echo "&nbsp;";
					} else {
						echo "완료";
					}
					echo "	</TD>\n";
					echo "	<TD><input type=checkbox name=ckdel value=\"{$row->id}\"></TD>\n";
					echo "</TR>\n";
				}
				pmysql_free_result($result);
?>
				<TR>
					<TD background="images/grayline_bg.gif" colspan="6" align=right style="padding-right:10"><a href="javascript:AllExcel();"><IMG src="images/icon_exceldown.gif" vspace=3 border=0></a></TD>
					<TD background="images/grayline_bg.gif" align=center><a href="javascript:ResultReceipt();"><IMG src="images/icon_ip.gif" vspace=3 border=0></a></TD>
					<TD background="images/grayline_bg.gif" align=center><a href="javascript:ResultDeli();"><IMG src="images/icon_trans1.gif" vspace=3 border=0></a></TD>
					<TD background="images/grayline_bg.gif" align=center><a href="javascript:ResultDel();"><IMG src="images/icon_del.gif" vspace=3 border=0></a></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			</form>
			<?php }?>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>가격변동형 등록공구 관리</span></dt>
							<dd>
								- 참여자보기를 클릭하면 해당 공동구매에 참여한 구매자 리스트가 출력됩니다.<br>
								- 메일 버튼을 이용하여 참여한 구매자에게 전체 메일 발송이 가능하며, 참여자 리스트에서 입금, 배송, 삭제가 가능합니다.<br>
								- 참여자가 없는 공동구매건에 한해 상품명 클릭후 수정이 가능합니다.<br>
								- 공동구매 목록 누적시 마감된 공동구매는 삭제 처리해 주시면 됩니다.
							</dd>
						</dl>
						
					</div>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			<form name=prform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=allid>
			<input type=hidden name=gong_seq value="<?=$gong_seq?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			</form>

			<form name=modifyform action="gong_gongchangereg.php" method=post>
			<input type=hidden name=gong_seq>
			</form>

			<form name=excelform action="gong_gongexcel.php" method=post>
			<input type=hidden name=gong_seq value="<?=$gong_seq?>">
			</form>

			<form name=memberform action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>
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
