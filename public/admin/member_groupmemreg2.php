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

$regdate=$_shopdata->joindate;
if(ord($regdate)==0) $regdate="20070401010101";

$sort=$_POST["sort"];


$type=$_POST["type"];
$mode=$_POST["mode"];
$ids=$_POST["ids"];
$group_code=$_POST["group_code"];
$searchtype=$_POST["searchtype"];
$sex=$_POST["sex"];
$age=$_POST["age"];
$agemin=$_POST["agemin"];
$agemax=$_POST["agemax"];
$reserve=$_POST["reserve"];
$reservemin=$_POST["reservemin"];
$reservemax=$_POST["reservemax"];
$memregdate=$_POST["memregdate"];
$memregyear1=$_POST["memregyear1"];
$memregmonth1=$_POST["memregmonth1"];
$memregday1=$_POST["memregday1"];
$memregyear2=$_POST["memregyear2"];
$memregmonth2=$_POST["memregmonth2"];
$memregday2=$_POST["memregday2"];
$birth=$_POST["birth"];
$birthmonth=$_POST["birthmonth"];
$birthday=$_POST["birthday"];
$addr=$_POST["addr"];
$seladdr=$_POST["seladdr"];
$groupmember=$_POST["groupmember"];
$buydate=$_POST["buydate"];
$buyyear1=$_POST["buyyear1"];
$buymonth1=$_POST["buymonth1"];
$buyday1=$_POST["buyday1"];
$buyyear2=$_POST["buyyear2"];
$buymonth2=$_POST["buymonth2"];
$buyday2=$_POST["buyday2"];
$price=$_POST["price"];
$pricemin=$_POST["pricemin"];
$pricemax=$_POST["pricemax"];
$ordercnt=$_POST["ordercnt"];
$ordercntmin=$_POST["ordercntmin"];
$ordercntmax=$_POST["ordercntmax"];
$search=$_POST["search"];

$today=date("Ymd");
if(ord($memregmonth1)){
	$memregmonth1 = sprintf("%02d",$memregmonth1);
	$memregday1 = sprintf("%02d",$memregday1);
	$memregmonth2 = sprintf("%02d",$memregmonth2);
	$memregday2 = sprintf("%02d",$memregday2);	
}

if($memregdate!="ALL") $termday = (strtotime("$memregyear2-$memregmonth2-$memregday2")-strtotime("$memregyear1-$memregmonth1-$memregday1"))/86400;
else $termday=0;

if ($termday>92) {
	alert_go("가입일자별 조회시 3개월을 초과할 수 없습니다.\\n날짜를 재조정하신 후 다시 시도하세요.",-1);
}


if($mode=="insert") {
	//멤버 그룹 변경 로고
	$idcut=explode(",",str_replace("|","",$ids));
	foreach($idcut as $k){
		if($k!=""){
			$sel_qry="select * from tblmember a left join tblmembergroup b on(a.group_code=b.group_code) where a.id='{$k}'";
			$sel_result=pmysql_query($sel_qry);
			$sel_data=pmysql_fetch_object($sel_result);
			
			if($sel_data->group_code!=$group_code){
				
				$sum_sql = "SELECT sum(price) as sumprice FROM tblorderinfo ";
				$sum_sql.= "WHERE id = '{$k}' AND deli_gbn = 'Y'";
				$sum_result = pmysql_query($sum_sql,get_db_conn());
				$sum_data=pmysql_fetch_object($sum_result);
				$sumprice="0";
				$sumprice=$sum_data->sumprice+$sel_data->sumprice;
				
				list($after_group)=pmysql_fetch_array(pmysql_query("select group_name from tblmembergroup where group_code='{$group_code}'"));
					
				$qry="insert into tblmemberchange (
				mem_id,
				before_group,
				after_group,
				accrue_price,
				change_date
				) values (
				'".$k."',
				'".$sel_data->group_name."',
				'".$after_group."',
				'".$sumprice."',
				'".date("Y-m-d")."'
				)";
				pmysql_query($qry,get_db_conn());
				
			}
		}
	}
	//로고등록 끝
	

	$ids=rtrim($ids,',');
	$inid=str_replace("|","'",$ids);
	$sql = "UPDATE tblmember SET group_code = '{$group_code}' WHERE id IN ({$inid}) ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('해당 회원의 등급이 변경 되었습니다.');}</script>";
}
$max=10;
$len=21;

if (empty($searchtype)) $searchtype="M";
if (empty($page)) $page="1";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.group_code.value.length==0){
		alert('회원등급을 먼저 선택하셔야 검색이 가능합니다.');
		document.form1.group_code.focus();
		return;
	}
	//if(document.form1.searchtype[0].checked){
		if(document.form1.sex[0].checked==false && document.form1.sex[1].checked==false && document.form1.sex[2].checked==false){
			alert('성별을 선택하세요. ');
			document.form1.sex[0].focus();
			return;
		}
		if(document.form1.age.checked==false && document.form1.agemin.value.length==0 && document.form1.agemax.value.length==0){
			alert('나이를 선택하세요. ');
			document.form1.age.focus();
			return;
		}
		if((document.form1.agemin.value.length!=0 && isNaN(document.form1.agemin.value)) || (document.form1.agemax.value.length!=0 && isNaN(document.form1.agemax.value))){
			alert('나이는 숫자만 입력 가능 합니다.');
			document.form1.agemin.focus();
			return;
		}
		if(document.form1.reserve.checked==false && document.form1.reservemin.value.length==0 && document.form1.reservemax.value.length==0){
			alert('적립금 선택을 하세요. ');
			document.form1.reserve.focus();
			return;
		}
		if((document.form1.reservemin.value.length!=0 && isNaN(document.form1.reservemin.value)) || (document.form1.reservemax.value.length!=0 && isNaN(document.form1.reservemax.value))){
			alert('적립금은 숫자만 입력 가능합니다.');
			document.form1.reservemin.focus();
			return;
		}
	//}else if(document.form1.searchtype[1].checked){
		if(document.form1.price.checked==false && document.form1.pricemin.value.length==0 && document.form1.pricemax.value.length==0){
			alert('구매금액을 선택하세요. ');
			document.form1.price.focus();
			return;
		}
		if((document.form1.pricemin.value.length!=0 && isNaN(document.form1.pricemin.value)) || (document.form1.pricemax.value.length!=0 && isNaN(document.form1.pricemax.value))){
			alert('구매금액은 숫자만 입력 가능합니다.');
			document.form1.pricemin.focus();
			return;
		}
		if(document.form1.ordercnt.checked==false && document.form1.ordercntmin.value.length==0 && document.form1.ordercntmax.value.length==0){
			alert('구매건수를 선택하세요. ');
			document.form1.ordercnt.focus();
			return;
		}
		if((document.form1.ordercntmin.value.length!=0 && isNaN(document.form1.ordercntmin.value)) || (document.form1.ordercntmax.value.length!=0 && isNaN(document.form1.ordercntmax.value))){
			alert('구매건수는 숫자만 입력 가능합니다.');
			document.form1.ordercntmin.focus();
			return;
		}
	/*
	}else if(document.form1.searchtype[2].checked){
		if(document.form1.search.value.length<2){
			alert('특정회원 검색어는 2자 이상 입력하셔야 합니다. ');
			document.form1.search.focus();
			return;
		}
	}
	*/
	document.form1.type.value="search";
	document.form1.mode.value="";
	document.form1.block.value=document.form3.block.value;
	document.form1.gotopage.value=document.form3.gotopage.value;
	document.form1.submit();
}

function ChangeSex(no){
	for(i=0;i<3;i++){
		if(no==i) document.form1.sex[i].checked=true;
		else document.form1.sex[i].checked=false;
	}
}

function ChangeGroup(no){
	for(i=0;i<3;i++){
		if(no==i) document.form1.groupmember[i].checked=true;
		else document.form1.groupmember[i].checked=false;
	}
}

function ChangeCheck(no){
	if(no==1) document.form1.age.checked=false;
	else if(no==2) document.form1.reserve.checked=false;
	else if(no==3) document.form1.price.checked=false;
	else if(no==4) document.form1.ordercnt.checked=false;
	else if(no==5) document.form1.buydate.checked=false;
	else if(no==6) document.form1.memregdate.checked=false;
	else if(no==7) document.form1.birth.checked=false;
	else if(no==8) document.form1.addr.checked=false;
}

var shop="layer1";
var ArrLayer = new Array ("layer1","layer2","layer3");
function ViewLayer(gbn){
	if(document.all){
		for(i=0;i<ArrLayer.length;i++) {
			if (ArrLayer[i] == gbn)
				document.all[ArrLayer[i]].style.display="";
			else
				document.all[ArrLayer[i]].style.display="none";
		}
	} else if(document.getElementById){
		for(i=0;i<=2;i++) {
			if (ArrLayer[i] == gbn)
				document.getElementById(ArrLayer[i]).style.display="";
			else
				document.getElementById(ArrLayer[i]).style.display="none";			
		}
	} else if(document.layers){
		for(i=0;i<2;i++) {
			if (ArrLayer[i] == gbn)
				document.layers[ArrLayer[i]].display="";
			else
				document.layers[ArrLayer[i]].display="none";
		}
	}
	shop=gbn;
}

function GoSort(sort){
	document.form1.type.value="search";
	document.form1.mode.value="";
	document.form1.sort.value=sort;
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.type.value="search";
	document.form1.mode.value="";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoGroupCode(){
	document.form1.searchtype[0].checked=true;
	document.form1.type.value="";
	document.form1.mode.value="";
	document.form1.submit();
}

function CheckAll(cnt){
	chkval=document.form1.allcheck.checked;
	for(i=1;i<=cnt;i++){
		document.form1.chkid[i].checked=chkval;
	}
}

function InsertGroup(cnt){
	chkval=false;
	document.form1.ids.value="";  
	for(i=1;i<=cnt;i++){
		if(document.form1.chkid[i].checked){ 
			chkval=true;
			document.form1.ids.value+="|"+document.form1.chkid[i].value+"|,";  
		}
	}
	if(chkval==false){
		alert('변경할 회원을 선택하세요');
		document.form1.chkid[1].focus();
		return;
	}
	document.form1.type.value="search";
	document.form1.mode.value="insert";
	document.form1.submit();
}

function ReserveInfo(id) {
	window.open("about:blank","reserve_info","height=400,width=400,scrollbars=yes");
	document.form2.id.value=id;
	document.form2.submit();
}

function UserMemoView(obj,type) {
	try {
		obj.style.visibility = type;
	} catch (e) {}
}
function MemberInfo(id) {
	window.open("about:blank","infopop","width=567,height=600,scrollbars=yes");
	document.form_member.target="infopop";
	document.form_member.id.value=id;
	document.form_member.action="member_infopop.php";
	document.form_member.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원등급설정 &gt;<span>등급별 회원변경/관리</span></p></div></div>
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
					<div class="title_depth3">회원등급 변경 관리</div>
					<div class="title_depth3_sub"><span>회원검색을 통해 회원특성에 맞는 등급으로 변경 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=ids>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="point_title">변경 등급 선택</div>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD>
							<div class="table_style01">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<col width=138></col>
							<col width=></col>
							<col width=108></col>
							<col width=206></col>
							<TR>
								<th><span>회원등급 선택</span></th>
								<TD>
									<select name=group_code onchange="GoGroupCode();" style="width:90%" class="select">
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
									</select>
								</TD>
								<th><span>등급 회원수</span></th>
								<TD style="padding-top:10pt;">
<?php
								if (strlen($group_code)==4) {
									$sql = "SELECT COUNT(*) as cnt FROM tblmember 
									WHERE group_code = '{$group_code}'";
									$result=pmysql_query($sql,get_db_conn());
									$row=pmysql_fetch_object($result);
									$membercnt=$row->cnt;
									pmysql_free_result($result);
								}
?>
									<?=number_format($membercnt)?>명
								</TD>
							</TR>
							<TR>
								<th><span>회원등급 설명</span></th>
								<TD colspan="3">&nbsp;<?=$group_description?></TD>
							</TR>
							</TABLE>
							</div>
							</TD>
							<td></td>
							<td></td>
							<td></td>
						</TR>
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
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="point_title">회원검색하기</div>
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD>
							
								<div class="table_style01">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
									<col width=138></col>
									<col width=></col>
									<col width=108></col>
									<col width=206></col>
									<TR>
										<th><span>검색기준</span></th>
										<TD class="td_con1" colspan="3">
											<input type=radio id="idx_searchtype1" name=searchtype value="M" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($searchtype=="M") echo "checked";?> onclick="ViewLayer('layer1')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype1>회원 속성</label> &nbsp;&nbsp;
											<input type=radio id="idx_searchtype2" name=searchtype value="O" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($searchtype=="O") echo "checked";?> onclick="ViewLayer('layer2')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype2>구매 내역</label> &nbsp;&nbsp;
											<input type=radio id="idx_searchtype3" name=searchtype value="U" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($searchtype=="U") echo "checked";?> onclick="ViewLayer('layer3')"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_searchtype3>특정 회원</label>
										</TD>
									</TR>
									</table>
								</div>
								<div id=layer1 style="margin-left:0;display:hide; display:<?=($searchtype=="M"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
								<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<col width=138></col>
								<col width=></col>
								<col width=108></col>
								<col width=206></col>
								<TR>
									<th><span>성별</span></th>
									<TD class="td_con1">
										<input type=checkbox id="idx_sex1" name=sex value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="ChangeSex(0)" <?php if($sex=="ALL" || !$sex) echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sex1>전체</label> 
										<input type=checkbox id="idx_sex2" name=sex value="M" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="ChangeSex(1)" <?php if($sex=="M") echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sex2>남자</label> 
										<input type=checkbox id="idx_sex3" name=sex value="F" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="ChangeSex(2)" <?php if($sex=="F") echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sex3>여자</label>
									</TD>
									<th><span>연령별</span></th>
									<TD class="td_con1">
										<input type=checkbox id="idx_age1" name=age value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($age=="ALL" || !$agemin) echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_age1>전체</label> <br />
										<input type=text name=agemin value="<?=$agemin?>" size=3 maxlength=3 onfocus="ChangeCheck(1)" class="input">세부터 <input type=text name=agemax value="<?=$agemax?>" size=3 maxlength=3 onfocus="ChangeCheck(1)" class="input">세까지
									</TD>
								</TR>
								<TR>
									<th><span>적립금액별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_reserve1" name=reserve value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($reserve=="ALL" || $reservemin=='') echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reserve1>전체</label> <input type=text name=reservemin value="<?=$reservemin?>" size=8 maxlength=8 onfocus="ChangeCheck(2)" class="input">원부터  <input type=text name=reservemax value="<?=$reservemax?>" size=8 maxlength=8 onfocus="ChangeCheck(2)" class="input">원까지</TD>
								</TR>
								<TR>
									<th><span>가입자별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_memregdate1" name=memregdate value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($memregdate=="ALL" || !$memregyear1) echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memregdate1>전체</label>
	<?php
									if (empty($memregdate1)) $memregdate1 = $today;
									if (empty($memregdate2)) $memregdate2 = $today;
									// 초기값 설정 :오늘날짜
									if(empty($memregyear1))	$memregyear1 = substr($memregdate1,0,4);
									if(empty($memregmonth1))	$memregmonth1 = substr($memregdate1,4,2);
									if(empty($memregday1))		$memregday1 = substr($memregdate1,6,2);

									if(empty($memregyear2))	$memregyear2 = substr($memregdate2,0,4);
									if(empty($memregmonth2))	$memregmonth2 = substr($memregdate2,4,2);
									if(empty($memregday2))		$memregday2 = substr($memregdate2,6,2);

									echo "<select size=1 name=memregyear1 class=\"select\" onchange=\"ChangeCheck(6)\">\n";
									for ($i = substr($regdate,0,4);$i <=substr($today,0,4) ; $i++) {
										if($i == $memregyear1)  echo "<option selected value=\"$i\">$i</option>\n";
										else echo "<option value=\"$i\">$i</option>\n";
									}
									echo "</select>년";
									echo "<select size=1 name=memregmonth1 class=\"select\" onchange=\"ChangeCheck(6)\">\n";
									for ($i = 1;$i <= 12; $i++) {
										if($i == $memregmonth1)  echo "<option selected value=\"$i\">$i</option>\n";
										else echo "<option value=\"$i\">$i</option>\n";
									}
									echo "</select>월";
									echo "<select size=1 name=memregday1 class=\"select\" onchange=\"ChangeCheck(6)\">\n";
									for ($i = 1;$i <= 31; $i++) {
										if ($i == $memregday1)  echo "<option selected value=\"$i\">$i</option>\n";
										else echo "<option value=\"$i\">$i</option>\n";
									}
									echo "</select>일 ~ ";

									echo "<select size=1 name=memregyear2 class=\"select\" onchange=\"ChangeCheck(6)\">\n";
									for ($i = substr($regdate,0,4);$i <= substr($today,0,4); $i++) {
										if ($i == $memregyear2)  echo "<option selected value=\"$i\">$i</option>\n";
										else echo "<option value=\"$i\">$i</option>\n";
									}
									echo "</select>년";
									echo "<select size=1 name=memregmonth2 class=\"select\" onchange=\"ChangeCheck(6)\">\n";
									for ($i = 1;$i <= 12; $i++) {
										if ($i == $memregmonth2)  echo "<option selected value=\"$i\">$i</option>\n";
										else echo "<option value=\"$i\">$i</option>\n";
									}
									echo "</select>월";
									echo "<select size=1 name=memregday2 class=\"select\" onchange=\"ChangeCheck(6)\">\n";

									for ($i = 1;$i <= 31; $i++) {
										if($i == $memregday2)   echo "<option selected value=\"$i\">$i</option>\n";
										else  echo "<option value=\"$i\">$i</option>\n";
									}
									echo "</select>일";
	?>
									</TD>
								</TR>
								<TR>
									<th><span>생년월일별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_birth1" name=birth value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($birth=="ALL" || !$birthmonth) echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_birth1>전체</label>
	<?php
									if(ord($birthmonth)==0) $birthmonth = date("m");
									echo "<select size=1 name=birthmonth class=\"select\" onchange=\"ChangeCheck(7)\">\n";
									for ($i = 1;$i <= 12; $i++) {
										if ($i<10) $i2 = "0$i";
										else $i2=$i;
										if($i2 == $birthmonth)  echo "<option selected value=\"$i2\">$i</option>\n";
										else echo "<option value=\"$i2\">$i</option>\n";
									}
									echo "</select>월";
									if(ord($birthday)==0) $birthday = date("d");
									echo "<select size=1 name=birthday class=\"select\" onchange=\"ChangeCheck(7)\">\n";
									echo "<option value=\"ALL\"";
									if($birthday=="ALL") echo " selected";
									echo ">전체";
									for ($i = 1;$i <= 31; $i++) {
										if ($i<10) $i2 = "0$i";
										else $i2=$i;
										if ($i2 == $birthday)  echo "<option selected value=\"$i2\">$i</option>\n";
										else echo "<option value=\"$i2\">$i</option>\n";
									}
									echo "</select>일";
	?>
									</TD>
								</TR>
								<TR>
									<th><span>지역별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_addr1" name=addr value="NO" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($addr=="NO" || !$seladdr) echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_addr1>전체</label> <select name=seladdr class="select" onchange="ChangeCheck(8)">
	<?php 
									$area= array ("서울","인천","부산","대전","광주","대구","울산","경기","강원","충북","충남","경북","경남","전북","전남","제주");
									$arnum = count($area);
									for($i=0;$i<$arnum;$i++){
										echo "<option value=\"{$area[$i]}\"";
										if($seladdr==$area[$i]) echo " selected";
										echo ">".$area[$i];
									}
	?>
										</select> <span class="font_orange">*해당 검색은 집주소만 검색합니다.</span>
									</TD>
								</TR>
								<tr>
									<th><span>기변경 회원</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_groupmember1" name=groupmember value="NO" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($groupmember=="NO" || !$groupmember) echo "checked"?> onclick="ChangeGroup(0)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_groupmember1>전체</label> &nbsp;&nbsp; 
									<input type=checkbox id="idx_groupmember2" name=groupmember value="YES" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($groupmember=="YES") echo "checked"?> onclick="ChangeGroup(1)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_groupmember2>전체 기변경 회원 제외</label> &nbsp;&nbsp; 
									<input type=checkbox id="idx_groupmember3" name=groupmember value="ONE" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($groupmember=="ONE") echo "checked"?> onclick="ChangeGroup(2)"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_groupmember3>선택 등급 회원 제외</label></TD>
								</tr>
								</table>
								</div>
								</div>
								<div id=layer2 style="margin-left:0;display:hide; display:<?=($searchtype=="O"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
								<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<col width=138></col>
								<col width=></col>
								<col width=108></col>
								<col width=206></col>
								<tr>
									<th><span>구매날짜별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_buydate1" name=buydate value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($buydate=="ALL" || !$buyyear1) echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_buydate1>전체</label> <select name=buyyear1 onchange="ChangeCheck(5)" class="select">
	<?php
									if(ord($buyyear1)==0) $temp = date("Y");
									else $temp=$buyyear1;
									for ($i=substr($regdate,0,4);$i<=date("Y");$i++) {
										if ($i==$temp)
											echo "<option value=\"$i\" selected>$i\n";
										else
											echo "<option value=\"$i\">$i\n";
									}
	?>
									</select>년
									<select name=buymonth1 onchange="ChangeCheck(5)" class="select">
	<?php
									if(ord($buymonth1)==0) $curmonth = date("m");
									else $curmonth=$buymonth1;
									for ($i=1;$i<=12;$i++) {
										if ($i<10) $i2 = "0$i";
										else $i2 = $i;
										if ($i2 == $curmonth) echo "<option value=\"$i2\" selected>$i2\n";
										else echo "<option value=\"$i2\">$i2\n";
									}
	?>
									</select>월
									<select name=buyday1 onchange="ChangeCheck(5)" class="select">
	<?php
									if(ord($buyday1)==0) $curday = date("d");
									else $curday=$buyday1;
									for ($i=1;$i<=31;$i++) {
										if ($i<10) $i2 = "0$i";
										else $i2 = $i;
										if ($i2 == $curday) echo "<option value=\"$i2\" selected>$i2\n";
										else echo "<option value=\"$i2\">$i2\n";
									}
	?>
									</select>일 ~ 
									<select name=buyyear2 onchange="ChangeCheck(5)" class="select">
	<?php
									if(ord($buyyear2)==0) $temp = date("Y");
									else $temp=$buyyear2;
									for ($i=substr($regdate,0,4);$i<=date("Y");$i++) {
										if ($i==$temp)
											echo "<option value=\"$i\" selected>$i\n";
										else
											echo "<option value=\"$i\">$i\n";
									}
	?>
									</select>년
									<select name=buymonth2 onchange="ChangeCheck(5)" class="select">
	<?php
									if(ord($buymonth2)==0) $curmonth = date("m");
									else $curmonth=$buymonth2;
									for ($i=1;$i<=12;$i++) {
										if ($i<10) $i2 = "0$i";
										else $i2 = $i;
										if ($i2 == $curmonth) echo "<option value=\"$i2\" selected>$i2\n";
										else echo "<option value=\"$i2\">$i2\n";
									}
	?>
									</select>월
									<select name=buyday2 onchange="ChangeCheck(5)" class="select">
	<?php
									if(ord($buyday2)==0) $curday = date("d");
									else $curday=$buyday2;
									for ($i=1;$i<=31;$i++) {
										if ($i<10) $i2 = "0$i";
										else $i2 = $i;
										if ($i2 == $curday) echo "<option value=\"$i2\" selected>$i2\n";
										else echo "<option value=\"$i2\">$i2\n";
									}
	?>
									</select>일
									</TD>
								</tr>
								<tr>
									<th><span>구매금액별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_price1" name=price value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($price=="ALL" || $pricemin=='') echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_price1>전체</label> <input type=text name=pricemin value="<?=$pricemin?>" size=8 maxlength=8 onclick="ChangeCheck(3)" class="input">원 부터 <input type=text name=pricemax value="<?=$pricemax?>" size=8 maxlength=8 onclick="ChangeCheck(3)" class="input">원 까지</TD>
								</tr>
								<TR>
									<th><span>구매건수별</span></th>
									<TD class="td_con1" colspan="3"><input type=checkbox id="idx_ordercnt1" name=ordercnt value="ALL" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" <?php if($ordercnt=="ALL" || $ordercntmin=='') echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_ordercnt1>전체</label> <input type=text name=ordercntmin value="<?=$ordercntmin?>" size=8 maxlength=8 onclick="ChangeCheck(4)" class="input">건 부터 &nbsp; <input type=text name=ordercntmax value="<?=$ordercntmax?>" size=8 maxlength=8 onclick="ChangeCheck(4)" class="input">건 까지</TD>
								</TR>
								</table>
								</div>
								</div>
								<div class="table_style01">
								<div id=layer3 style="margin-left:0;display:hide; display:<?=($searchtype=="U"?"block":"none")?> ;border-style:solid; border-width:0; border-color:black;background:#FFFFFF;padding:0;">
									<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
									<col width=138></col>
									<col width=></col>
									<col width=108></col>
									<col width=206></col>
										<TR>
											<th><span>특정회원 검색</span></th>
											<TD class="td_con1" colspan="3" style="padding-bottom:10pt;"><input type=text name=search value="<?=$search?>" class="input">&nbsp;<span class="font_orange">*특정회원의 이름 또는 아이디를 입력하세요!</span></TD>
										</TR>
									</TABLE>
								</div>
								</div>

							</TD>
							<td></td>
							<td></td>
							<td></td>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td><p align="center"><a href="javascript:CheckForm();"><img src="images/botteon_search1.gif" border="0" vspace="3"></a></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색된 회원</div>
				</td>
			</tr>
			<tr>
				<td><p align="right">&nbsp;<FONT color=red><B>정렬방법 :</B></FONT> 적립금 <A href="javascript:GoSort('reserve_desc');"><B>▲</B></A> <A href="javascript:GoSort('reserve_asc');"><B>▼</B></A> &nbsp;|&nbsp; 이름 <A href="javascript:GoSort('name_asc');"><B>▲</B></A> <A href="javascript:GoSort('name_desc');"><B>▼</B></A></td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<input type=hidden name=chkid>
				<TR>
					<th>선택</th>
					<th>아이디</th>
					<th>메모</th>
					<th>등급명</th>
					<th>지역</th>
					<th>성별</th>
					<th>나이</th>
					<th>구매금액</th>
					<th>구매건수</th>
					<th>적립금</th>
				</TR>
<?php
				if ($type=="search") {
//					if ($searchtype=="M" || $searchtype=="U") {
						$qry.= "WHERE member_out = 'N' ";
						if ($groupmember=="YES")
							$qry.= "AND group_code = '' ";
						else if ($groupmember=="ONE")
							$qry.= "AND (group_code = '' OR group_code != '{$group_code}') ";
						if ($search!='') {
							$qry.= "AND (name LIKE '%{$search}%' OR id LIKE '%{$search}%' ) ";
						}
						if($sex!="ALL" && $sex=="M") $qry.= "AND  gender='1' ";
						else if($sex!="ALL" && $sex=="F") $qry.= "AND  gender='2' ";
						if ($age!="ALL"){
							$start_year = (int)date("Y") - (int)$agemax;
							$end_year = (int)date("Y") - (int)$agemin;
							$s_year = substr((string)$start_year,2,2);
							$e_year = substr((string)$end_year,2,2);
							if ($start_year < 2000 && $end_year < 2000) {
								$qry.= "AND (SUBSTR(birth,3,2) BETWEEN '{$s_year}' AND '{$e_year}') ";
								//$qry.= "AND SUBSTR(resno,7,1) < '3' ";
							} else if ($start_year < 2000 && $end_year > 1999) {
								$qry.= "AND (SUBSTR(birth,3,2) BETWEEN '{$s_year}' AND '99') ";
								//$qry.= "AND SUBSTR(resno,7,1) < '3') OR ((LEFT(resno,2) BETWEEN '00' AND '{$e_year}') ";
								//$qry.= "AND SUBSTR(resno,7,1) > '2')) ";
							} else if ($start_year > 1999 && $end_year > 1999) {
								$qry.= "AND (SUBSTR(birth,3,2) BETWEEN '{$s_year}' AND '{$e_year}') ";
								//$qry.= "AND SUBSTR(resno,7,1) > '2') ";
							}
						}
						if($birth!="ALL"){
							if($birthday!="ALL") $qry.= "AND SUBSTR(birth,6,5) = '".$birthmonth."-".$birthday."' ";
							else $qry.= "AND SUBSTR(birth,6,2) = '{$birthmonth}' ";
						}
						if($memregdate!="ALL"){
							$memregdate1 = substr($memregyear1,0,4).substr($memregmonth1,0,2).substr($memregday1,0,2)."000000";
							$memregdate2 = substr($memregyear2,0,4).substr($memregmonth2,0,2).substr($memregday2,0,2)."999999";
							$qry.= "AND date >= '{$memregdate1}' AND date <= '{$memregdate2}' ";
						}
						if($reserve!="ALL"){
							if(strlen($reservemin)!=0) $reserveminvalue=$reservemin;
							else $reserveminvalue=0;
							if(strlen($reservemax)!=0) $reservemaxvalue=$reservemax;
							else $reservemaxvalue=10000000000;
							$qry.= "AND reserve >= '{$reserveminvalue}' AND reserve <= '{$reservemaxvalue}' ";
						}
						if($addr!="NO"){
							$qry.= "AND home_addr LIKE '{$seladdr}%' ";
						}
						
						
						if($buydate!="ALL" || $price!="ALL" || $ordercnt!="ALL"){
							$sql="select COUNT(cnt) as cnt, SUM(totalprice)+(select sumprice from tblmember where id=a.id) as totalprice, id from (";
							$sql.= "SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM tblorderinfo ";
							$sql.= "WHERE 1=1 ";
							if($buydate!="ALL") {
								$sql.= "AND (ordercode > '".$buyyear1.$buymonth1.$buyday1."000000' ";
								$sql.= "AND ordercode < '".$buyyear2.$buymonth2.$buyday2."999999') ";
							}
							$sql.= "AND deli_gbn = 'Y' AND (SUBSTR(ordercode,15,2) != 'X' AND SUBSTR(ordercode,15,1) != '|') ";
							$sql.= "GROUP BY id ";
							/*
							if($price!="ALL" || $ordercnt!="ALL") $sql.= "HAVING ";
							if($price!="ALL"){
								if(strlen($pricemin)!=0) $priceminvalue=$pricemin;
								else $priceminvalue=0;
								if(strlen($pricemax)!=0) $pricemaxvalue=$pricemax;
								else $pricemaxvalue=10000000000;
								$sql.= "SUM(price) >= '{$priceminvalue}' AND SUM(price) <= '{$pricemaxvalue}' ";
							}
							if($price!="ALL" && $ordercnt!="ALL") $sql.= "AND ";
							if($ordercnt!="ALL"){
								if(strlen($ordercntmin)!=0) $ordercntminvalue=$ordercntmin;
								else $ordercntminvalue=0;
								if(strlen($ordercntmax)!=0) $ordercntmaxvalue=$ordercntmax;
								else $ordercntmaxvalue=10000000000;
								$sql.= "COUNT(price) >= '{$ordercntminvalue}' AND COUNT(price) <= '{$ordercntmaxvalue}' ";
							}
							
							*/
							
							
							$sql.= "union SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM sales.tblorderinfo ";
							$sql.= "WHERE 1=1 ";
							if($buydate!="ALL") {
								$sql.= "AND (ordercode > '".$buyyear1.$buymonth1.$buyday1."000000' ";
								$sql.= "AND ordercode < '".$buyyear2.$buymonth2.$buyday2."999999') ";
							}
							$sql.= "AND deli_gbn = 'Y' AND (SUBSTR(ordercode,15,2) != 'X' AND SUBSTR(ordercode,15,1) != '|') ";
							$sql.= "GROUP BY id ";
							$g_sql= "GROUP BY id ";
							
							if($price!="ALL" || $ordercnt!="ALL") $g_sql.= "HAVING ";
							if($price!="ALL"){
								if(strlen($pricemin)!=0) $priceminvalue=$pricemin;
								else $priceminvalue=0;
								if(strlen($pricemax)!=0) $pricemaxvalue=$pricemax;
								else $pricemaxvalue=10000000000;
								$g_sql.= "SUM(totalprice)+(select sumprice from tblmember where id=a.id) >= '{$priceminvalue}' AND SUM(totalprice)+(select sumprice from tblmember where id=a.id) <= '{$pricemaxvalue}' ";
							}
							if($price!="ALL" && $ordercnt!="ALL") $g_sql.= "AND ";
							if($ordercnt!="ALL"){
								if(strlen($ordercntmin)!=0) $ordercntminvalue=$ordercntmin;
								else $ordercntminvalue=0;
								if(strlen($ordercntmax)!=0) $ordercntmaxvalue=$ordercntmax;
								else $ordercntmaxvalue=10000000000;
								$g_sql.= "COUNT(totalprice)+(select sumprice from tblmember where id=a.id) >= '{$ordercntminvalue}' AND COUNT(totalprice)+(select sumprice from tblmember where id=a.id) <= '{$ordercntmaxvalue}' ";
							}
							$sql.=") a ".$g_sql;
							
							$result = pmysql_query($sql,get_db_conn());
							
							while($row=pmysql_fetch_object($result)){
								$mulid.="'$row->id',";
								
							}
							$mulid=rtrim($mulid,',');
							
							$qry.= "and id IN ({$mulid}) ";
						}
						
						
						$sql = "SELECT COUNT(*) as t_count FROM tblmember ";
						$sql.= $qry;
						$paging = new Paging($sql,10,20);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;

						$sql = "SELECT id, reserve, name, SUBSTR(birth,3,2) as age, gender as sex, ";
						$sql.= "group_code, SUBSTR(home_addr,1,2) as addr, memo FROM tblmember ";
						$sql.= $qry." ";
						if($sort=="reserve_desc") $sql.= "ORDER BY reserve DESC ";
						elseif($sort=="reserve_asc") $sql.= "ORDER BY reserve ";
						elseif($sort=="name_asc") $sql.= "ORDER BY name ";
						else $sql.= "ORDER BY name DESC ";
						$sql = $paging->getSql($sql);
						$result=pmysql_query($sql,get_db_conn());
						$count=0;
						
						
						
						while($row=pmysql_fetch_object($result)){
							//$row->id=strtolower($row->id);
							$arcount[$count]=$row->id;
							$arreserve[$row->id]=$row->reserve;
							$arname[$row->id]=$row->name;
							$arsex[$row->id]=$row->sex;
							$arage[$row->id]=$row->age;
							if(!$row->group_code) $artrue[$row->id]="Y";
							else $artrue[$row->id]="N";
							$groupcode[$row->id]=$row->group_code;
							$address[$row->id]=$row->addr;
							$memo[$row->id]=$row->memo;
							if($buydate=="ALL" && $price=="ALL" && $ordercnt=="ALL"){
							$mulid.="'$row->id',";
							}
							$count++;
						}
						$maxcnt=$count;
						if($maxcnt>20) $count--;
						if($buydate=="ALL" && $price=="ALL" && $ordercnt=="ALL"){
							$mulid=rtrim($mulid,',');
						}
						pmysql_free_result($result);
						if($count!=0){
							$sql="select COUNT(cnt) as cnt, SUM(totalprice)+(select sumprice from tblmember where id=a.id) as totalprice, id FROM (";
							$sql.= "SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM tblorderinfo ";
							$sql.= "WHERE deli_gbn = 'Y' AND id IN ({$mulid}) GROUP BY id";
							$sql.= " union SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM sales.tblorderinfo ";
							$sql.= "WHERE deli_gbn = 'Y' AND id IN ({$mulid}) GROUP BY id";
							$sql.=" ) a  GROUP BY id";
							$result=pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)){
								//$row->id=strtolower($row->id);
								$arprice[$row->id]=$row->totalprice;
								$arcnt[$row->id]=$row->cnt;
							}
							pmysql_free_result($result);
						}
						
/*
					} else if ($searchtype=="O") {
						$sql = "SELECT COUNT(price) as cnt, SUM(price) as totalprice, id FROM tblorderinfo ";
						$sql.= "WHERE 1=1 ";
						if($buydate!="ALL") {
							$sql.= "AND (ordercode > '".$buyyear1.$buymonth1.$buyday1."000000' ";
							$sql.= "AND ordercode < '".$buyyear2.$buymonth2.$buyday2."999999') ";
						}
						$sql.= "AND deli_gbn = 'Y' AND (SUBSTR(ordercode,15,2) != 'X' AND SUBSTR(ordercode,15,1) != '|') ";
						$sql.= "GROUP BY id ";
						if($price!="ALL" || $ordercnt!="ALL") $sql.= "HAVING ";
						if($price!="ALL"){
							if(strlen($pricemin)!=0) $priceminvalue=$pricemin;
							else $priceminvalue=0;
							if(strlen($pricemax)!=0) $pricemaxvalue=$pricemax;
							else $pricemaxvalue=10000000000;
							$sql.= "totalprice >= '{$priceminvalue}' AND totalprice <= '{$pricemaxvalue}' ";
						}
						if($price!="ALL" && $ordercnt!="ALL") $sql.= "AND ";
						if($ordercnt!="ALL"){
							if(strlen($ordercntmin)!=0) $ordercntminvalue=$ordercntmin;
							else $ordercntminvalue=0;
							if(strlen($ordercntmax)!=0) $ordercntmaxvalue=$ordercntmax;
							else $ordercntmaxvalue=10000000000;
							$sql.= "cnt >= '{$ordercntminvalue}' AND cnt <= '{$ordercntmaxvalue}' ";
						}
						$result = pmysql_query($sql,get_db_conn());
						$t_count = pmysql_num_rows($result);
						pmysql_free_result($result);
						$paging = new Paging($t_count,10,20);
						$gotopage = $paging->gotopage;
			
						if($sort=="topcnt") $sql.= "ORDER BY cnt DESC ";
						elseif($sort=="bottomcnt") $sql.= "ORDER BY cnt ";
						elseif($sort=="bottomprice") $sql.= "ORDER BY totalprice ";
						else $sql.= "ORDER BY totalprice DESC ";
						$sql = $paging->getSql($sql);
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)){
							//$row->id=strtolower($row->id);
							$arcount[$count]=$row->id;
							$arprice[$row->id]=$row->totalprice;
							$arcnt[$row->id]=$row->cnt;
							$mulid.="'$row->id',";
							$count++;
						}				$maxcnt=$count;
						if($maxcnt>20) $count--;
						$mulid=rtrim($mulid,',');
						pmysql_free_result($result);
						if ($count!=0) {
							$sql = "SELECT id, SUBSTR(birth,3,2) as age, gender as sex, reserve, name, ";
							$sql.= "group_code, SUBSTR(home_addr,1,2) as addr, memo FROM tblmember ";
							$sql.= "WHERE id IN ({$mulid}) ";
							$result=pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)){
								//$row->id=strtolower($row->id);
								if(!$row->group_code) $artrue[$row->id]="Y";
								else $artrue[$row->id]="N";
								$arreserve[$row->id]=$row->reserve;
								$arname[$row->id]=$row->name;
								$arsex[$row->id]=$row->sex;
								$arage[$row->id]=$row->age;
								$groupcode[$row->id]=$row->group_code;
								$address[$row->id]=$row->addr;
								$memo[$row->id]=$row->memo;
							}
							pmysql_free_result($result);
						}
					}
*/
					$lineage=100+date("y");
					$totalcheck=0;
					for($i=0;$i<$count;$i++) {
						$bgcolor="#FFFFFF";
						if($searchtype!="O" && ord($arprice[$arcount[$i]])) {
							$bgcolor="#FEFAAB";
						}
						echo "<tr>\n";
						echo "	<TD>";
						if($artrue[$arcount[$i]]!=""){
							$totalcheck++;
							echo "<input type=checkbox name=chkid value=\"{$arcount[$i]}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\">";
						}
						echo "	</td>\n";
						echo "	<TD><span class=\"font_orange\"><A HREF=\"javascript:MemberInfo('{$arcount[$i]}')\"><b>{$arcount[$i]}</b></a></span></TD>\n";
						echo "	<TD><NOBR>";
						if (ord(trim($memo[$arcount[$i]]))) {
							echo "<img src=\"images/btn_memo.gif\" border=\"0\" onMouseOver=\"UserMemoView(divmemo_{$i},'visible')\" onMouseOut=\"UserMemoView(divmemo_{$i},'hidden')\">";
						} else {
							echo "<img src=\"images/btn_memor.gif\" border=\"0\">";
						}
						echo "	<div id=\"divmemo_{$i}\" style=\"position:absolute; z-index:5; width:250; filter:revealTrans(duration=0.3); visibility:hidden;\">\n";
						echo "	<table border=0 cellspacing=0 cellpadding=1 bgcolor=#7F7F65>\n";
						echo "	<tr>\n";
						echo "		<td><font color=#ffffff>&nbsp;{$memo[$arcount[$i]]}&nbsp;</td>\n";
						echo "	</tr>\n";
						echo "	</table>\n";
						echo "	</div>\n";
						echo "	</td>\n";
						echo "	<TD>";
						if(($searchtype!="O" && $groupcode[$arcount[$i]]=="") || ($searchtype=="O" && $artrue[$arcount[$i]]=="Y")) { 
							echo $arname[$arcount[$i]];
						} else if($artrue[$arcount[$i]]=="N" || $groupcode[$arcount[$i]]!="") {
							echo "<font color=#AA0000 title='이름 : {$arname[$arcount[$i]]}\n해당 등급명 : {$grouptitle[$groupcode[$arcount[$i]]]}'><u>".titleCut(15,$grouptitle[$groupcode[$arcount[$i]]])."</font>\n";
						} else {
							echo "삭제 회원";
						}
						echo "	</td>\n";
						echo "	<TD>".($address[$arcount[$i]])."&nbsp;</td>\n";
						echo "	<TD>".($arsex[$arcount[$i]]%2==0?"여자":"남자")."</td>\n";
						echo "	<TD>".(ord($arage[$arcount[$i]])==0?"&nbsp;":$lineage-$arage[$arcount[$i]])."</td>\n";
						echo "	<TD><span class=\"font_orange\"><b>".number_format($arprice[$arcount[$i]])."원</b></span></td>\n";
						echo "	<TD>".number_format($arcnt[$arcount[$i]])."건</td>\n";
						echo "	<TD>".number_format($arreserve[$arcount[$i]])."원&nbsp;<a href=\"javascript:ReserveInfo('{$arcount[$i]}');\"><img src=\"images/btn_detail.gif\" border=\"0\" align=absMiddle></a></td>\n";
						echo "</tr>\n";
					}
				}
				if($count==0) {
					echo "<tr><td class=\"td_con2\" colspan=10 align=center>검색된 회원이 없습니다.</td></tr>\n";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<input type=checkbox name=allcheck value="<?=$totalcheck?>" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" onclick="CheckAll('<?=$totalcheck?>')">&nbsp;<font color=#0054A6>page 전체 회원 선택</font>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td width="100%" class="font_size" align=center>
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
				</td>
			</tr>
			<tr>
				<td><p align="center"><a href="javascript:InsertGroup('<?=$totalcheck?>');"><img src="images/botteon_register.gif" border="0" vspace="3"></a></td>
			</tr>
			</form>
			<form name=form2 action="member_reservelist.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>
			<form name=form3 method=post>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>회원등급 변경/검색 방법</span></dt>
							<dd>
							- 이동될 등급을 선택 후 검색기준별로 검색조건들을 선택해야만 검색이 가능합니다.<br>
							- 검색된 후 해당 회원들의 등급을 이동할 수 있습니다.
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
<form name=form_member method=post>
<input type=hidden name=id>
</form>
<?=$onload?>
<?php 
include("copyright.php");
