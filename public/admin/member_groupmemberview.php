<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-2";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$id=$_POST["id"];
$sort=$_POST["sort"];
$group_code=$_POST["group_code"];
$search=$_POST["search"];

if($type=="delete") {
	$sql = "UPDATE tblmember SET group_code = '' WHERE id='{$id}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('해당 등급에서 회원을 삭제하였습니다.');}</script>";
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
function GoGroupCode(){
	document.form1.type.value="";
	document.form1.submit();
}

function CheckSearch() {
	if (document.form1.search.value.length<2) {
		alert('특정회원 검색어는 2자 이상 입력하야 합니다. ');
		document.form1.search.focus();
		return;
	} else {
		document.form1.type.value="";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	}
}

function CheckAll(){
	chkval=document.reserveform.allcheck.checked;
	cnt=document.reserveform.tot.value;
	for(i=1;i<=cnt;i++){
		document.reserveform.chkid[i].checked=chkval;
	}
}

function GroupMemberDelete(id) {
	if(!confirm('선택하신 회원을 해당 등급에서 삭제하시겠습니까?')) return;
	document.form1.id.value=id;
	document.form1.type.value="delete";
	document.form1.submit();
}

function ReserveInfo(id) {
	window.open("about:blank","reserve_info","height=500,width=600,scrollbars=yes");
	document.form2.id.value=id;
	document.form2.submit();
}

function actPointInfo(id) {
	window.open("about:blank","actpoint_info","height=400,width=400,scrollbars=yes");
	document.actpointform1.target="actpoint_info";
	document.actpointform1.id.value=id;
	document.actpointform1.submit();
}

function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=414,height=320,scrollbars=yes");
	document.orderform.target="orderinfo";
	document.orderform.id.value=id;
	document.orderform.submit();
}

function MemberMail(mail){
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}

function reservein(){
	temp =document.reserveform.tot.value;
	allreserve="";
	for(i=1;i<=temp;i++){
		//if(document.reserveform.chkid[i].checked) allreserve+="'"+document.reserveform.chkid[i].value+"',";
        if(document.reserveform.chkid[i].checked) allreserve+=document.reserveform.chkid[i].value+",";
	}
	if(allreserve.length==0){
		alert('적립금을 적립할 회원을 선택하세요');
		if(temp!=0) document.reserveform.chkid[1].focus();
		return;
	}
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.allid.value=allreserve;
	document.reserveform.type.value="inreserve";
	document.reserveform.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원등급설정 &gt;<span>등급별 회원 관리</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">등급별 회원관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등급별 등록된 회원정보를 조회/관리가 가능합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" >
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="point_title">회원검색하기</div>						
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
						<input type=hidden name=type>
						<input type=hidden name=id>
						<input type=hidden name=sort>
						<input type=hidden name=block>
						<input type=hidden name=gotopage>
						<TR>
							<TD>
							<div class="table_style01">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<col width=138></col>
							<col width=></col>
							<TR>
								<th><span>회원등급 선택</span></th>
								<TD class="td_con1"><select name=group_code onchange="GoGroupCode();" style="width:350" class="select">
									<option value="">해당 등급을 선택하세요.
<?php 
			$sql = "SELECT * FROM tblmembergroup order by group_level";
			$result = pmysql_query($sql,get_db_conn());
			$count = 0;
			while ($row=pmysql_fetch_object($result)) {
				$grouptitle[$row->group_code]=$row->group_name;
				echo "<option value=\"{$row->group_code}\"";
				if ($group_code==$row->group_code) {
					$group_description=$row->group_description;
					echo " selected";
				}
				echo ">{$row->group_name}</option>\n";
			}
			pmysql_free_result($result);
?>
									</select></TD>
							</TR>
							<tr>
								<th><span>회원등급 설명</span></th>
								<TD class="td_con1">&nbsp;<?=$group_description?></TD>
							</tr>
							<TR>
								<th><span>특정회원 검색</span></th>
								<TD class="td_con1"><input type=text name=search value="<?=$search?>" class="input" size="28"> <a href="javascript:CheckSearch();"><img src="images/btn_search3.gif" border="0" align=absmiddle></a>&nbsp;<span class="font_orange">*특정회원의 이름 또는 아이디를 입력하세요!</span></TD>
							</TR>
							</TABLE>
							</div>
							</TD>
						</TR>
						</form>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색된 회원</div>
				</td>
			</tr>
			<form name=reserveform action="reserve_money_new.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=allid>
			<tr>
				<td>
<?php
		if (ord($group_code)) {
			$sql = "SELECT COUNT(*) as t_count FROM tblmember ";
			$sql.= "WHERE group_code = '{$group_code}' ";
			if(strlen($search)!=0) $sql.= "AND (name LIKE '%{$search}%' OR id LIKE '%{$search}%')";
			$paging = new Paging($sql,10,20);
			$t_count = $paging->t_count;
			$gotopage = $paging->gotopage;
		}
?>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                <col width="40" />
                <col width="100" />
                <col width="100" />
                <col width="80" />
                <col width="80" />
                <col width="120" />
                <col width="" />
                <col width="120" />
                <col width="80" />
				<input type=hidden name=chkid>
				<TR align=center>
					<th><input type=checkbox name=allcheck onclick="CheckAll()" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"></th>
					<th>아이디</th>
					<th>E-mail</th>
					<th>성명</th>
					<th>닉네임</th>
					<th>가입일</th>
					<th>활동 포인트</th>
					<th>이메일</th>
					<th>삭제</th>
				</TR>
<?php
		if (ord($group_code)) {
			$sql = "SELECT id,email,reserve,name, nickname,date, act_point FROM tblmember ";
			$sql.= "WHERE group_code = '{$group_code}' ";
			if(ord($search)) $sql.= "AND (name LIKE '%{$search}%' OR id LIKE '%{$search}%') ";
			if($sort=="reserve") $sql.= "ORDER BY reserve DESC ";
			elseif($sort=="id") $sql.= "ORDER BY id ";
			elseif($sort=="name") $sql.= "ORDER BY name DESC ";
			else $sql.= "ORDER BY date DESC ";
			$sql = $paging->getSql($sql);
			$result = pmysql_query($sql,get_db_conn());
            //echo "sql = ".$sql."<br>";
			$cnt=0;
			//$lineage=100+date("y");
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
				$cnt++;

				//if($row->gender>2) $row->age+=99;
				echo "<tr>\n";
				echo "	<TD><input type=checkbox name=chkid value=\"{$row->id}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"></td>\n";
				echo "	<TD><span class=\"font_orange\"><b>{$row->id}</b></span></TD>\n";
				echo "	<TD><NOBR>{$row->email}</td>\n";
                echo "	<TD><NOBR>{$row->name}</td>\n";
                echo "	<TD><NOBR>{$row->nickname}</td>\n";
				echo "	<TD><NOBR>{$row->date}</td>\n";
				//echo "	<TD><a href=\"javascript:OrderInfo('{$row->id}');\"><img src=\"images/icon_expenditure.gif\"  border=\"0\"></a></td>\n";
				echo "	<TD><b><span class=\"font_orange\">".number_format($row->act_point)."원</span></b> &nbsp;<a href=\"javascript:actPointInfo('{$row->id}');\"><img src=\"images/icon_expenditure.gif\"  border=\"0\" vspace=\"1\"></a></td>\n";
				echo "	<TD><a href=\"javascript:MemberMail('{$row->email}');\"><img src=\"images/icon_mail.gif\" border=\"0\"></a></td>\n";
				echo "	<TD><a href=\"javascript:GroupMemberDelete('{$row->id}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></td>\n";
				echo "</tr>\n";
			}
			pmysql_free_result($result);

			if ($cnt==0) {
				echo "<tr><td colspan=9 align=center>등급내 검색된 회원이 없습니다.</td></tr>";
			}
		} else {
			echo "<tr><td colspan=9 align=center>등급내 검색된 회원이 없습니다.</td></tr>";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<!--tr>
				<td><a href="javascript:reservein();"><img src="images/btn_point.gif" border="0" vspace="5"></a></td>
			</tr-->
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" class="font_size" align="center">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>
			<form name=form2 action="member_reservelist_new.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>

			<form name=actpointform1 action="member_actpointlist.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>

			<form name=orderform action="orderinfopop.php" method=post>
			<input type=hidden name=id>
			</form>

			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>등급별 회원정보</span></dt>
							<dd>
							- 회원등급 선택시 해당 등급 회원들의 기본정보 및 구매내역, 적립금내역, 이메일 등을 확인할 수 있습니다.
							</dd>
							
						</dl>
						<dl>
							<dt><span>등급회원 메일발송</span></dt>
							<dd>
							- 메일발송 선택시 선택한 회원에게만 개별발송됩니다.<br>
													<b>&nbsp;&nbsp;</b>등급별 또는 전체회원에게 메일을 발송할 경우에는 <a href="javascript:parent.topframe.GoMenu(3,'member_mailallsend.php');"><span class="font_blue">회원관리 > 회원관리 부가기능 > 단체메일 발송</span></a> 에서 발송가능합니다.</p>
							</dd>

						</dl>
						<dl>
							<dt><span>등급회원 삭제</span></dt>
							<dd>
							- 등급별 검색회원 목록에서의 삭제는 회원탈퇴가 아닌 해당 회원의 등급만 삭제됩니다.
												
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
