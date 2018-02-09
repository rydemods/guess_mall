<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-4";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$addr_group=$_POST["addr_group"];
$mobile=$_POST["mobile"];

if($type=="group_delete" && ord($addr_group)) {
	$sql = "DELETE FROM tblsmsaddress WHERE addr_group='{$addr_group}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('삭제하였습니다.'); }</script>";
} else if($type=="delete" && ord($mobile)) {
	$telval=rtrim($mobile,'|=|');
	$telval=str_replace("|=|","','",$telval);

	$sql = "DELETE FROM tblsmsaddress WHERE mobile IN ('{$telval}') ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('삭제하였습니다.'); }</script>";	
}

include("header.php"); 

$qry = "WHERE 1=1 ";
if(ord($addr_group)) $qry.= "AND addr_group='{$addr_group}' ";

$sql = "SELECT COUNT(*) as t_count FROM tblsmsaddress ".$qry;
$paging = new Paging($sql,10,20);
$t_count = $paging->t_count;	
$gotopage = $paging->gotopage;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function AddressAdd(mobile) {
	if(mobile.length>0) {
		document.form2.type.value="update";
		document.form2.mobile.value=mobile;
	} else {
		document.form2.type.value="insert";
	}
	window.open("about:blank","addbookpop","width=350,height=3,scrollbars=no");
	document.form2.target="addbookpop";
	document.form2.action="market_smsaddbookpop.php";
	document.form2.submit();
}

function GroupDelete() {
	if(document.form1.addr_group.value.length==0) {
		alert("삭제할 그룹을 선택하세요.");
		document.form1.addr_group.focus();
		return;
	}
	if(confirm("해당 그룹에 속한 모든 번호를 삭제하시겠습니까?")) {
		document.form1.type.value="group_delete";
		document.form1.submit();
	}
}

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.tels_chk[i].checked=chkval;
	}
}

function check_del() {
	document.form1.mobile.value="";
	for(i=1;i<document.form1.tels_chk.length;i++) {
		if(document.form1.tels_chk[i].checked) {
			document.form1.mobile.value+=document.form1.tels_chk[i].value+"|=|";
		}
	}
	if(document.form1.mobile.value.length==0) {
		alert("선택하신 SMS번호가 없습니다.");
		return;
	}
	if(confirm("선택하신 SMS번호를 삭제하시겠습니까?")) {
		document.form1.type.value="delete";
		document.form1.submit();
	}
}

function SearchGroup(group) {
	document.form1.addr_group.value=group;
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.mobile.value="";
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 주소록 관리</span></p></div></div>

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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mobile>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS 주소록 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>휴대폰 번호로 SMS 주소록을 만들어 회원관리를 할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="padding-bottom:3pt;">
					그룹선택
                    <SELECT onchange="this.form.type.value='';this.form.submit();" name=addr_group class="select">
					<option value="">전체</option>
<?php
					$sql = "SELECT addr_group FROM tblsmsaddress GROUP BY addr_group ";
					$result=pmysql_query($sql,get_db_conn());
					while($row=pmysql_fetch_object($result)) {
						echo "<option value=\"{$row->addr_group}\"";
						if($addr_group==$row->addr_group) echo " selected";
						echo ">{$row->addr_group}</option>\n";
					}
					pmysql_free_result($result);
?>
					</SELECT>
					<a href="javascript:GroupDelete();"><img src="images/btn_groupdel.gif" border="0" align=absmiddle></a>
					</td>
					<td height=3 style="padding-bottom:3pt;" align=right><img src="images/icon_8a.gif" border="0">총 등록 건수 : <B><?= $t_count ?></B>건 <img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b></td>
				</tr>
				<tr>
					<td width="100%" colspan="2">
					<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><INPUT onclick=CheckAll() type=checkbox name=allcheck></th>
						<th>No</th>
						<th>이름</th>
						<th>그룹명</th>
						<th>휴대폰번호</th>
						<th>기타메모</th>
					</TR>
					<input type=hidden name=tels_chk>
<?php
					$colspan=6;
										
					$sql = "SELECT * FROM tblsmsaddress {$qry} ";
					$sql.= "ORDER BY date DESC ";
					$sql = $paging->getSql($sql);
					$result = pmysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
						echo "<TR>\n";
						echo "	<TD><input type=checkbox name=tels_chk value=\"{$row->mobile}\"></TD>\n";
						echo "	<TD>{$number}</TD>\n";
						echo "	<TD><A HREF=\"javascript:AddressAdd('{$row->mobile}')\"><b><span class=\"font_orange\">{$row->name}</span></a></A></TD>\n";
						echo "	<TD><A HREF=\"javascript:SearchGroup('{$row->addr_group}')\">{$row->addr_group}</A></TD>\n";
						echo "	<TD>{$row->mobile}&nbsp;</TD>\n";
						echo "	<TD width=\"50%\" align=\"center\">&nbsp;{$row->memo}</TD>\n";
						echo "</TR>\n";
						$cnt++;
					}
					pmysql_free_result($result);

					if ($cnt==0) {
						echo "<tr><td colspan={$colspan} align=center>조건에 맞는 내역이 존재하지 않습니다.</td></tr>";
					}
?>
					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<td width="100%" colspan="2">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" class="font_size" align=left><a href="javascript:check_del();"><img src="images/btn_del2.gif" border="0"></a></td>
					</tr>
					<tr>
						<td width="100%" class="font_size" align="center">
						<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<input type=hidden name=tot value="<?=$cnt?>">
				</table>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:AddressAdd('');"><img src="images/btn_smsupload.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>						
						<dl>
							<dt><span>SMS 주소록 관리</span></dt>
							<dd>- 신규그룹 생성은 [SMS 주소 신규등록 > 그룹선택 > 신규그룹]에 그룹명을 입력하시면 됩니다.<BR>
							- 이름 클릭시 해당 주소록의 정보를 변경하실 수 있습니다.<br>
							- 그룹명 클릭시 해당 그룹의 전체 주소록을 보실 수 있습니다.<br>
							- 그룹 삭제시, 해당 그룹에 속한 주소록도 같이 삭제됩니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</form>

			<form name=form2 method=post>
			<input type=hidden name=type>
			<input type=hidden name=mobile>
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
