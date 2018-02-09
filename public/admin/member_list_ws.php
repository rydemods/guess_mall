<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$SHOP_SCHEMAS="sales.";
$type=$_GET["type"];
$id=$_GET["id"];
$ids=$_GET["ids"];
$email=$_GET["email"];
$member_type1=$_GET["member_type1"];
$member_type2=$_GET["member_type2"];
$member_type3=$_GET["member_type3"];


print_r($member_type);
if ($type=="confirm_ok") {
	$sql = "UPDATE tblmember SET confirm_yn='Y' WHERE id='{$id}' ";
	pmysql_query($sql,get_db_conn());
	if (ord($email)) {
		SendAuthMail($_shopdata->shopname, $shopurl, $_shopdata->mail_type, $_shopdata->info_email, $email, $id);
	}

	$sql2= "SELECT * FROM tblsmsinfo WHERE mem_auth = 'Y' ";
	$result2 = pmysql_query($sql2,get_db_conn());
	if($row2=pmysql_fetch_object($result2)){
		$sql3 = "SELECT mobile, name FROM tblmember WHERE id='{$id}'";
		$result3 = pmysql_query($sql3,get_db_conn());
		$row3 = pmysql_fetch_object($result3);
		pmysql_free_result($result3);
		$sms_id=$row2->id;
		$sms_authkey=$row2->authkey;

		$row3->mobile=str_replace(",","",$row3->mobile);
		$row3->mobile=str_replace("-","",$row3->mobile);
		$toname= $row3->name;
		$totel= $row3->mobile;

		$msg=$row2->msg_mem_auth;
		$patten=array("[NAME]");
		$replace=array($toname);
		$msg=str_replace($patten,$replace,$msg);
		$msg=AddSlashes($msg);
		$fromtel=$row2->return_tel;
		$etcmsg="회원인증 안내메세지";
		$date=0;
		$res=SendSMS($sms_id, $sms_authkey, $totel, "", $fromtel, $date, $msg, $etcmsg);
	}
} else if ($type=="confirm_cancel") {
	$sql = "UPDATE tblmember SET confirm_yn = 'N' WHERE id = '{$id}' ";
	pmysql_query($sql,get_db_conn());
	//echo "<script>history.go(-1);</script>"; exit;
} else if ($type=="member_out" && ord($id)) {	//선택 회원삭제
	$idval=rtrim($id,'|=|');
	$arr_id=explode("|=|",$idval);
	for($i=0;$i<count($arr_id);$i++) {
		$outid=$arr_id[$i];
		$sql = "SELECT COUNT(*) as cnt FROM {$SHOP_SCHEMAS}tblorderinfo WHERE id='{$outid}'";
		$result= pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if ($row->cnt==0) {
			$sql = "DELETE FROM tblmember WHERE id = '{$outid}'";
		} else {
			pmysql_query("BEGIN WORK");			
			$sql = "UPDATE tblmember SET ";
			$sql.= "passwd			= '', ";
			$sql.= "resno			= '', ";
			$sql.= "email			= '', ";
			$sql.= "news_yn			= 'N', ";
			$sql.= "age				= '', ";
			$sql.= "gender			= '', ";
			$sql.= "job				= '', ";
			$sql.= "birth			= '', ";
			$sql.= "home_post		= '', ";
			$sql.= "home_addr		= '', ";
			$sql.= "home_tel		= '', ";
			$sql.= "mobile			= '', ";
			$sql.= "office_post		= '', ";
			$sql.= "office_addr		= '', ";
			$sql.= "office_tel		= '', ";
			$sql.= "memo			= '', ";
			$sql.= "reserve			= 0, ";
			$sql.= "joinip			= '', ";
			$sql.= "ip				= '', ";
			$sql.= "authidkey		= '', ";
			$sql.= "group_code		= '', ";
			$sql.= "member_out		= 'Y', ";
			$sql.= "etcdata			= '' ";
			$sql.= "WHERE id = '{$outid}'";
		}
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblreserve WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblcouponissue WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblmemo WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblrecommendmanager WHERE rec_id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblrecomendlist WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblpersonal WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		if($pmysql_errno==0)
                pmysql_query("COMMIT");
        else
                pmysql_query("ROLLBACK");
	}
	//로그 insert
	$log_content = "## 회원삭제 : ID:".str_replace("|=|",",",$idval)." ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>window.onload=function(){ alert('선택하신 회원 ".count($arr_id)."명을 탈퇴처리 하였습니다.\\n\\n구매내역이 있는 경우에는 회원 기본정보만 삭제됩니다.');}</script>";
}

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));

$sort=(int)$_GET["sort"];
$scheck=(int)$_GET["scheck"];
$group_code=$_GET["group_code"];
$search_start=$_GET["search_start"];
$search_end=$_GET["search_end"];
$vperiod=(int)$_GET["vperiod"];
$search=$_GET["search"];
//$search_start=$search_start?$search_start:$period[0];
//$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_start=$search_start?$search_start:"";
$search_end=$search_end?$search_end:"";

${"check_sort".$sort} = "checked";
${"check_scheck".$scheck} = "checked";
${"check_vperiod".$vperiod} = "checked";

if ($scheck == "5") {
	$sort_disabled = "disabled";
}

$display_group_code="none";
if($scheck==6) $display_group_code="";
$display_todaylogin="";
if($scheck==7) $display_todaylogin="none";

$ArrSort = array("a.resetday","a.date","a.name","a.id","a.age","a.reserve");
$ArrScheck = array("a.id","a.name","a.email","a.home_tel","a.mobile","a.rec_id","a.group_code","a.logindate");

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">
<!--
function member_baro(type,id,email) {
	var msg = "";
	if (type=="ok") msg="["+id+"] 님을 인증 하시겠습니까?";
	else if (type=="cancel") msg="["+id+"] 님의 인증을 취소하시겠습니까?";
	if (confirm(msg)) {
		document.form1.type.value="confirm_"+type;
		document.form1.id.value=id;
		document.form1.email.value=email;
		document.form1.submit();
	}
}

function check() {
	if (document.form1.search.value.length==0) {
		tmsg="";
		if(document.form1.scheck[6].checked==false) tmsg="검색기간 내에서 ";
		if(document.form1.scheck[5].checked && document.form1.group_code.value.length==0) {
			alert("조회하실 회원 등급을 선택하세요.");
			document.form1.group_code.focus();
			return;
		}
		if (confirm(tmsg+"전체 조회하시겠습니까?")) {
			document.form1.submit();
		}
	} else {
		document.form1.submit();
	}
}

function searchcheck() {
	key=event.keyCode;
	if (key==13) { check(); }
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	if(val=="0"){
		pForm.search_start.value = "";
		pForm.search_end.value = "";
	}else{
		pForm.search_start.value = period[val];
		pForm.search_end.value = period[1];	
	}
	
}
function OnChangeSearchType(val) {
	if (val == 6) {
		for(var i=0;i<document.form1.sort.length;i++) {
			document.form1.sort[i].disabled = true;
		}
	} else {
		for(var i=0;i<document.form1.sort.length;i++) {
			document.form1.sort[i].disabled = false;
		}
	}

	if (val == 6) {
		document.all.div_todaylogin.style.display = "";
		document.all.div_todaylogin2.style.display = "";
		document.all.div_group_code.style.display = "";
		document.all.div_group_code2.style.display = "";
	} else {
		document.all.div_group_code.style.display = "none";
		document.all.div_group_code2.style.display = "none";
		if (val == 7) {
			document.all.div_todaylogin.style.display = "none";
			document.all.div_todaylogin2.style.display = "none";
		} else {
			document.all.div_todaylogin.style.display = "";
			document.all.div_todaylogin2.style.display = "";
		}
	}
}

function CheckAll(){
	chkval=document.form1.allcheck.checked;
	cnt=document.form1.tot.value;
	for(i=1;i<=cnt;i++){
		document.form1.ids_chk[i].checked=chkval;
	}
}

function check_del() {
	document.form1.id.value="";
	for(i=1;i<document.form1.ids_chk.length;i++) {
		if(document.form1.ids_chk[i].checked) {
			document.form1.id.value+=document.form1.ids_chk[i].value+"|=|";
		}
	}
	if(document.form1.id.value.length==0) {
		alert("선택하신 회원아이디가 없습니다.");
		return;
	}
	if(confirm("선택하신 회원아이디를 탈퇴처리 하시겠습니까?")) {
		document.form1.type.value="member_out";
		document.form1.submit();
	}
}

function MemberInfo(id) {
	window.open("about:blank","infopop","width=567,height=600,scrollbars=yes");
	document.form3.target="infopop";
	document.form3.id.value=id;
	document.form3.action="member_infopop.php";
	document.form3.submit();
}

function LostPass(id) {
	window.open("about:blank","lostpasspop","width=350,height=200,scrollbars=no");
	document.form3.target="lostpasspop";
	document.form3.id.value=id;
	document.form3.action="member_lostpasspop.php";
	document.form3.submit();
}

function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=245,height=140,scrollbars=no");
	document.reserveform.target="reserve_set";
	document.reserveform.id.value=id;
	document.reserveform.type.value="reserve";
	document.reserveform.submit();
}


function ReserveInfo(id) {
	window.open("about:blank","reserve_info","height=400,width=400,scrollbars=yes");
	document.form2.id.value=id;
	document.form2.submit();
}

function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=414,height=320,scrollbars=yes");
	document.form3.target="orderinfo";
	document.form3.id.value=id;
	document.form3.action="orderinfopop.php";
	document.form3.submit();
}

function CouponInfo(id) {
	window.open("about:blank","couponinfo","width=600,height=400,scrollbars=yes");
	document.form3.target="couponinfo";
	document.form3.id.value=id;
	document.form3.action="coupon_listpop.php";
	document.form3.submit();
}

function MemberMail(mail,news_yn){
	if(news_yn!="Y" && news_yn!="M" && !confirm("해당 회원은 메일수신을 거부하였습니다.\n\n메일을 발송하시려면 확인 버튼을 클릭하시기 바랍니다.")) {
		return;
	}
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}

function MemberSMS(news_yn,tel1,tel2) {
	if(news_yn!="Y" && news_yn!="S") {
		if(!confirm("SMS수신거부 회원입니다.\n\nSMS를 발송하시려면 \"확인\"을 눌러주세요.")) {
			return;
		}
	}
	number=tel1+"|"+tel2;
	document.smsform.number.value=number;
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.submit();
}

function MemberMemo2(id) {
	window.open("about:blank","memopop","width=350,height=350,scrollbars=no");
	document.form3.target="memopop";
	document.form3.id.value=id;
	document.form3.action="member_memopop.php";
	document.form3.submit();
}
function MemberEtcView(id) {
	window.open("about:blank","etcpop","width=350,height=350,scrollbars=no");
	document.form3.target="etcpop";
	document.form3.id.value=id;
	document.form3.action="member_etcpop.php";
	document.form3.submit();
}

function excel_download() {
	if(confirm("검색된 모든 회원정보를 다운로드 하시겠습니까?")) {
		document.excelform.submit();
	}
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function member_write(id){
	document.form_member.action="member_write.php?id="+id;
	document.form_member.submit();
}
function up_ws(id,group_level,wsmoney){
	var result = confirm("연장 하시겠습니까?");
	if(result){
		$("#pgtId").val(id);
		$("#pgLevel").val(group_level);
		$("#pgtMoney").val(wsmoney);
		$("form[name='pgtform']").submit();
	}
}

function MemberListWs(id){
	window.open("about:blank","infopop","width=480,height=580,scrollbars=yes");
	document.form3.target="infopop";
	document.form3.id.value=id;
	document.form3.action="./member_list_ws_money.php?id="+id;
	document.form3.submit();
}
//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원정보관리 &gt;<span>회원정보관리</span></p></div></div>

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
					<div class="title_depth3">회원정보 관리</div>

					<!-- 달력 임시로 만듬 s -->
					<!--
					<div class="calendar_pop_wrap">
						<div class="calendar_con">
							<div class="month_select">
								<a href="#"><img src="img/btn/btn_month_pre.gif" alt="이전달"  align=absmiddle /></a>
								<select name="" id="">
									<option value="">2013</option>
									<option value="">2012</option>
									<option value="">2011</option>
								</select>년
								<select name="" id="">
									<option value="">12</option>
									<option value="">11</option>
									<option value="">10</option>
								</select>월
								<a href="#"><img src="img/btn/btn_month_next.gif" alt="다음달"  align=absmiddle /></a>
							</div>
							<div class="day">
								<table border=0 cellpadding=0 cellspacing=0>
									<tr>
										<th class="sun">일</th>
										<th>월</th>
										<th>화</th>
										<th>수</th>
										<th>목</th>
										<th>금</th>
										<th>토</th>
									</tr>
									<tr>
										<td class="pre_month"><a href="#">28</a></td>
										<td class="pre_month"><a href="#">29</a></td>
										<td class="pre_month"><a href="#">30</a></td>
										<td class="pre_month"><a href="#">31</a></td>
										<td><a href="#">1</a></td>
										<td><a href="#">2</a></td>
										<td><a href="#">3</a></td>
									</tr>
									<tr>
										<td><a href="#">4</a></td>
										<td><a href="#">5</a></td>
										<td><a href="#">6</a></td>
										<td><a href="#">7</a></td>
										<td><a href="#">8</a></td>
										<td><a href="#">9</a></td>
										<td><a href="#">10</a></td>
									</tr>
									<tr>
										<td><a href="#">11</a></td>
										<td class="today"><a href="#">12</a></td>
										<td><a href="#">13</a></td>
										<td><a href="#">14</a></td>
										<td><a href="#">15</a></td>
										<td><a href="#">16</a></td>
										<td><a href="#">17</a></td>
									</tr>
									<tr>
										<td><a href="#">18</a></td>
										<td><a href="#">19</a></td>
										<td><a href="#">20</a></td>
										<td><a href="#">21</a></td>
										<td><a href="#">22</a></td>
										<td><a href="#">23</a></td>
										<td><a href="#">24</a></td>
									</tr>
									<tr>
										<td><a href="#">25</a></td>
										<td><a href="#">26</a></td>
										<td><a href="#">27</a></td>
										<td><a href="#">28</a></td>
										<td><a href="#">29</a></td>
										<td><a href="#">30</a></td>
										<td class="pre_month"><a href="#">31</a></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					-->
					<!-- 달력 임시로 만듬 e-->

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>회원을 검색하거나 회원 상세내용을 조회/수정/탈퇴/암호변경/기타 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>
            <tr>
            	<td><div class="title_depth3_sub">회원정보 검색</div>
                </td>
            </tr>
            <tr>
            	<td style="padding-top:3; padding-bottom:3">
					<div class="help_info01_wrap">
							<ul>
								<li>1) 회원정보 검색시 반드시 검색조건을 선택하세요.</li>
								<li>2) 검색어 입력시 공백 또는 특수문자(- / ~)는 정상적으로 검색되지 않습니다.</li>
							</ul>
					</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=get>
			<input type=hidden name=type>
			<input type=hidden name=id>
			<input type=hidden name=email>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=139></col>
				<col width=></col>
				<TR>
					<th><span>정렬방식 선택</span></th>
					<td>
					<div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="30%"><input style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="0" id=idx_sort0 <?=$check_sort0?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort0>도매 만료 임박일 기준으로 정렬</label></td>
						<TD width="30%"><input style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="7" id=idx_sort7 <?=$check_sort7?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort7>도매 만료일 기준으로 정렬</label></td>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="6" id=idx_sort6 <?=$check_sort6?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort6>승인일 미확인 기준 정렬</label></td>
						
					</TR>
					<TR>
						<TD width="40%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="3" id=idx_sort3 <?=$check_sort3?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort3>아이디 기준으로 정렬</label></td>

						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="4" id=idx_sort4 <?=$check_sort4?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort4>나이순 기준으로 정렬</label></td>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="5" id=idx_sort5 <?=$check_sort5?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort5>적립금 기준으로 정렬</label></td>
					</tr>
					<tr>
						<TD width="30%"><input style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="1" id=idx_sort1 <?=$check_sort1?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort1>등록일 기준으로 정렬</label></td>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=sort value="2" id=idx_sort2 <?=$check_sort2?> <?=$sort_disabled?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_sort2>회원명 기준으로 정렬</label></td>
					</TR>
					</TABLE>
					</div>
					</td>
				</TR>
				<TR>
					<th><span>검색조건 선택</span></th>
					<td>
					<div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="0" id=idx_scheck0 onClick="OnChangeSearchType(this.value);" <?=$check_scheck0?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck0>회원 아이디로 검색</label></td>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="1" id=idx_scheck1 onClick="OnChangeSearchType(this.value);" <?=$check_scheck1?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck1>회원명로 검색</label></td>
						<TD width="40%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="2" id=idx_scheck2 onClick="OnChangeSearchType(this.value);" <?=$check_scheck2?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck2>이메일로 검색</label></td>
					</TR>
					<TR>
						<!--TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="3" id=idx_scheck3 onClick="OnChangeSearchType(this.value);" <?=$check_scheck3?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck3>주민등록번호로 검색</label></TD-->
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="3" id=idx_scheck3 onClick="OnChangeSearchType(this.value);" <?=$check_scheck3?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck3>전화번호로 검색</label></td>
						<TD width="40%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="4" id=idx_scheck4 onClick="OnChangeSearchType(this.value);" <?=$check_scheck4?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck4>핸드폰 번호로 검색</label></td>
						<!--TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="5" id=idx_scheck5 onClick="OnChangeSearchType(this.value);" <?=$check_scheck5?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck5>추천인 검색</label></TD-->
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="6" id=idx_scheck6 onClick="OnChangeSearchType(this.value);" <?=$check_scheck6?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck6>등급회원 검색</label></td>
					</TR>
					<TR>
<?php
						$sql = "SELECT login_cnt FROM tblshopcountday WHERE date = '".date("Ymd")."'";
						$result = pmysql_query($sql,get_db_conn());
						if ($row = pmysql_fetch_object($result)) {
							$todaylogin = $row->login_cnt;
						} else {
							$todaylogin = 0;
						}
						pmysql_free_result($result);
?>
						<TD width="*%" colspan = '6'><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="7" id=idx_scheck7 onClick="OnChangeSearchType(this.value);" <?=$check_scheck7?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck7>오늘 로그인 회원 검색 <B>(총 <?=$todaylogin?>명)</B></label></td>
					</TR>
					</TABLE>
					</div>
					</td>
				</TR>
				<TR id="div_group_code" style="display:<?=$display_group_code?>;">
					<th><span>회원등급 선택</span></th>
					<td><select name=group_code style="width:300px" class="input_selected">
					<option value="">회원 등급을 선택하세요.</option>
<?php
					$sql = "SELECT group_code,group_name FROM tblmembergroup order by group_level";
					$result = pmysql_query($sql,get_db_conn());
					$count = 0;
					while ($row=pmysql_fetch_object($result)) {
						echo "<option value='{$row->group_code}'";
						$group_name[$row->group_code]=$row->group_name;
						if($group_code==$row->group_code){
							//$subject=$row->subject;
							echo " selected";
						}
						echo ">{$row->group_name}</option>";
					}
					pmysql_free_result($result);
?>
					</select>
					</td>
				</TR>
				<TR id="div_group_code2" style="display:<?=$display_group_code?>;">
					<TD colspan="2" background="images/table_con_line.gif"></td>
				</TR>
				<TR id="div_todaylogin" style="display:<?=$display_todaylogin?>;">
					<th><span>검색기간 선택</span></th>
					<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
				</TR>
				<TR id="div_todaylogin2" style="display:<?=$display_todaylogin?>;">
				</TR>
				<TR>
					<th><span>검색어 입력</span></th>
					<td><input name=search size=40 value="<?=$search?>" onKeyDown="searchcheck()" class="input"> <a href="javascript:check();"><img src="images/btn_search3.gif" border="0" align=absmiddle></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="member_type1" value="1" <?if($member_type1=="1"){echo "checked";}?>>도매회원 검색 <input type="checkbox" name="member_type2" value="2" <?if($member_type2=="2"){echo "checked";}?>>소매회원 검색 <input type="checkbox" name="member_type3" value="3" <?if($member_type3=="3"){echo "checked";}?>>도매 연장회원 검색</td>
					
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색된 회원목록</div>
				</td>
			</tr>
<?php
			$date_start = str_replace("-","",$search_start)."000000";
			$date_end = str_replace("-","",$search_end)."235959";
			
			$datam_s=date('Y-m-d H:i:s',strtotime("-3 months",strtotime($date_start)));
			$datam_e=date('Y-m-d H:i:s',strtotime("-3 months",strtotime($date_end)));
				
				
			if ($scheck=="6") {	//등급회원 검색
				//$searchsql = "AND a.date >= '{$date_start}' AND a.date <= '{$date_end}' ";
				if($search_start && $search_end){
					$searchsql = "AND a.resetday between '{$datam_s}' AND '{$datam_e}' ";	
				}
				
				if($member_type1 && !$member_type2){
					$searchsql .= "AND b.group_wsmember = 'Y' ";
				}else if(!$member_type1 && $member_type2){
					$searchsql .= "AND b.group_wsmember = 'N' ";
				}
				
				if($member_type3){
					$searchsql .= "AND a.resetday != '1970-01-01 00:00:00' ";
				}
				
				$searchsql .= "AND b.group_wsmember = 'Y' ";
				if ($group_code) {
					$searchsql.= "AND a.group_code = '{$group_code}' ";	//해당 등급회원
				} else {
					$searchsql.= "AND a.group_code != '' ";				//모든 등급회원
				}
				if ($search) {
					$searchsql.= "AND a.id LIKE '%{$search}%' ";
				}
				$sql0 = "SELECT COUNT(a.id) as cnt FROM tblmember a JOIN tblmembergroup b on a.group_code = b.group_code WHERE 1=1 ";
				$sql0.= $searchsql;
				$sql = "SELECT a.*, b.group_name, b.group_level, b.group_wsmember, b.group_wsmoney, (SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count FROM tblmember a JOIN tblmembergroup b on a.group_code = b.group_code WHERE 1=1 ";
				$sql.= $searchsql." ";
			} else if ($scheck=="7") {	//오늘 로그인 회원 검색
				if ($search) {
					$searchsql = "AND a.id LIKE '%{$search}%' ";
				}
				if($member_type1 && !$member_type2){
					$searchsql .= "AND b.group_wsmember = 'Y' ";
				}else if(!$member_type1 && $member_type2){
					$searchsql .= "AND b.group_wsmember = 'N' ";
				}
				if($member_type3){
					$searchsql .= "AND a.resetday != '1970-01-01 00:00:00' ";
				}
				
				$searchsql .= "AND b.group_wsmember = 'Y' ";
				$sql0 = "SELECT COUNT(a.id) as cnt FROM tblmember a JOIN tblmembergroup b on a.group_code = b.group_code ";
				$sql0.= "WHERE a.logindate >= '".date("Ymd")."000000' ".$searchsql;
				$sql = "SELECT a.*, b.group_name, b.group_level, b.group_wsmember, b.group_wsmoney, (SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count FROM tblmember a JOIN tblmembergroup b on a.group_code = b.group_code WHERE logindate >= '".date("Ymd")."000000' {$searchsql} ";
			} else {
				
					
				//$searchsql = "AND a.date >= '{$date_start}' AND a.date <= '{$date_end}' ";
				if($search_start && $search_end){
					$searchsql = "AND a.resetday between '{$datam_s}' AND '{$datam_e}' ";
				}
				
				if ($search) {
					$searchsql.= "AND {$ArrScheck[$scheck]} LIKE '%{$search}%' ";
				}
				if($member_type1 && !$member_type2){
					$searchsql .= "AND b.group_wsmember = 'Y' ";
				}else if(!$member_type1 && $member_type2){
					$searchsql .= "AND b.group_wsmember = 'N' ";
				}
				if($member_type3){
					$searchsql .= "AND a.resetday != '1970-01-01 00:00:00' ";
				}
			//	$searchsql .= "AND b.group_wsmember = 'Y' ";
				$sql0 = "SELECT COUNT(a.id) as cnt FROM tblmember a JOIN tblmembergroup b on a.group_code = b.group_code WHERE 1=1 ".$searchsql;
				$sql = "SELECT a.*, b.group_name, b.group_level, b.group_wsmember, b.group_wsmoney, (SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count FROM tblmember a JOIN tblmembergroup b on a.group_code = b.group_code WHERE 1=1 {$searchsql} ";
			}
			if ($scheck!="5") {
				switch ($sort) {

					case "0":
//						$sql.= "ORDER BY case when (a.resetday + '3 month'::interval)::date - now()::date > 0 then 1 else 0 end desc, a.resetday + '3 month'::interval - now() asc ";
						$sql.= "ORDER BY resetday asc ";
						//$sql.= "ORDER BY (resetday + '3 month'::interval)::date - now()::date<'0' asc, resetday ";
						break;
					case "1":	//등록일
						$sql.= "ORDER BY a.date DESC ";
						break;
					case "2":	//회원명
						$sql.= "ORDER BY a.name ASC ";
						break;
					case "3":	//아이디
						$sql.= "ORDER BY a.id ASC ";
						break;
					case "4":	//나이순
						$sql.= "ORDER BY a.birth ASC ";
						break;
					case "5":	//적립금
						$sql.= "ORDER BY a.reserve DESC ";
						break;
					case "6":	//승인일 미확인 기준
						$sql.= "ORDER BY a.resetday asc ";
						break;
					case "7":	//도매만료일 기준
						$sql.= "ORDER BY a.resetday desc ";
						break;
					default :	//등록일
//						$sql.= "ORDER BY case when (a.resetday + '3 month'::interval)::date - now()::date > 0 then 1 else 0 end desc, a.resetday + '3 month'::interval - now() asc ";
						$sql.= "ORDER BY resetday asc ";
						break;
				}
			}
			
			
			$paging = new Paging($sql0,10,20);
			$t_count = $paging->t_count;
			$gotopage = $paging->gotopage;
?>

			<tr>
				<td align=right><p>전체 <span class="font_orange"><B><?=$t_count?></B></span>건 조회, 현재 <span class="font_orange"><B><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></B></span> 페이지</p></td>
			</tr>
			<tr>
				<td>
				<!--     그룹	적립금	구매금액	도매승인일	도매만료일	유지조건금액	누적금	누적 차액	연장	  -->
				<div class="table_style02">
					<input type=hidden name=ids_chk>
					<?$_shopdata->member_baro=="Y"?$member_list_colspan="15":$member_list_colspan="14";?>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
<!--							<th><input type=checkbox name=allcheck onclick="CheckAll()" style="border:0px;"></th>-->
							<th>번호</th>
							<th>아이디</th>
							<th>성명</th>
							<th>그룹</th>
							<th>총누적금</th>
							<th>도매승인일</th>
							<th>도매만료일</th>
							<th>유지조건금액</th>
							<th>누적금</th>
							<th>누적 차액</th>
							<th>연장</th>
							<th>수정</th>
						</tr>
<?php
				$cnt=0;
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());

				list($year,$month,$day) = explode(' ',date('Y n j')); // 현재 년,월,일 
				$beforeDate = date('Ymd',mktime(0,0,0,$month-3,$day,$year)); 
				$nowDate = $year.$month.$day;
				
				while($row=pmysql_fetch_object($result)) {
					/*
					$sumSql = "	SELECT 
											coalesce(sum(price),0) tot_price
										FROM 
											{$SHOP_SCHEMAS}tblorderinfo
										WHERE 
											(
												(paymethod = 'B' AND coalesce(length(bank_date), 0) > 0)
												OR (paymethod = 'V' AND pay_flag = '0000' AND (pay_admin_proc != 'C' AND length(pay_admin_proc)>0))
												OR (paymethod = 'M' AND pay_flag = '0000' AND (pay_admin_proc != 'C' AND length(pay_admin_proc)>0))
												OR (paymethod = 'OQ' AND pay_flag = '0000' AND coalesce(length(bank_date), 0) > 0)
												OR ((paymethod != 'M' AND paymethod = 'V' AND paymethod = 'M' AND paymethod = 'OQ') AND pay_flag = '0000' AND (pay_admin_proc != 'C' AND length(pay_admin_proc)>0))
											) 
											AND (ordercode between '".$beforeDate."' AND '".$nowDate."')
											AND id = '".$row->id."'
					";
					*/
					
					
					$resetday_replace= str_replace("-","",reset(explode(" ",$row->resetday)));
					
					/*
					$sumSql = "	SELECT 
											coalesce(sum(price),0) tot_price
										FROM 
											{$SHOP_SCHEMAS}tblorderinfo
										WHERE 
											deli_gbn = 'Y'
											AND ordercode between '".$resetday_replace."' AND '".date("Ymd",time())."'
											AND id = '".$row->id."'
					";
					*/
					//$sumSql="SELECT sum(tot_price) tot_price FROM(";
					
					
					
					//기업회원일때 
					if($row->group_wsmember=="Y"){
						
						$price_sum="";
						$sumSql= "SELECT coalesce(sum(price),0) tot_price FROM {$SHOP_SCHEMAS}tblorderinfo ";
						$sumSql.= "WHERE id = '{$row->id}' AND deli_gbn = 'Y' AND ordercode between '".$resetday_replace."' AND '".date("Ymd",time())."' ";
						$sumSql.= " union SELECT coalesce(sum(price),0) tot_price FROM tblorderinfo ";
						$sumSql.= "WHERE id = '{$row->id}' AND deli_gbn = 'Y' AND ordercode between '".$resetday_replace."' AND '".date("Ymd",time())."' ";
					
					}else{
					//일반회원일때 마지막 도매 3개월 금액
						$price_sum="";
						$sumSql= "SELECT coalesce(sum(price),0) tot_price FROM {$SHOP_SCHEMAS}tblorderinfo ";
						$sumSql.= "WHERE id = '{$row->id}' AND deli_gbn = 'Y' AND ordercode between '".$resetday_replace."' AND '".date("Ymd",strtotime("+3 month",strtotime($resetday_replace)))."' ";
						$sumSql.= " union SELECT coalesce(sum(price),0) tot_price FROM tblorderinfo ";
						$sumSql.= "WHERE id = '{$row->id}' AND deli_gbn = 'Y' AND ordercode between '".$resetday_replace."' AND '".date("Ymd",strtotime("+3 month",strtotime($resetday_replace)))."' ";
					}
					
					//$sumSql.= ")";
					
					$sumResult= pmysql_query($sumSql);
					while($sumData = pmysql_fetch_array($sumResult)){
						
						$price_sum+=$sumData[tot_price];
					}
					
					$sumData[tot_price]=$price_sum;
					
					
					################## 총 구매금액 쿼리(도매) ################
					$sum_sql = "SELECT sum(price) as sumprice FROM {$SHOP_SCHEMAS}tblorderinfo ";
					$sum_sql.= "WHERE id = '{$row->id}' AND deli_gbn = 'Y'";
					$sum_result = pmysql_query($sum_sql,get_db_conn());
					$sum_data=pmysql_fetch_object($sum_result);
					###################################################
					
					################## 총 구매금액 쿼리(소매) ################
					$sum_sql2 = "SELECT sum(price) as sumprice FROM tblorderinfo ";
					$sum_sql2.= "WHERE id = '{$row->id}' AND deli_gbn = 'Y'";
					$sum_result2 = pmysql_query($sum_sql2,get_db_conn());
					$sum_data2=pmysql_fetch_object($sum_result2);
					###################################################
					
					$total_sum=$sum_data->sumprice+$sum_data2->sumprice+$row->sumprice+$row->random_price;
					
					
					if($sumData[tot_price]<$row->group_wsmoney){
						$minus="<font color=red>  ".number_format($sumData[tot_price]-$row->group_wsmoney)."원</font>";
					}else{
						$minus="<font color=blue> ".number_format($sumData[tot_price]-$row->group_wsmoney)."원</font>";
					}

					if($row->resetday < '1971'){
						$resetday='승인일 미확인';
						$wsPrice='승인일 미확인';
						$afterResetDay="승인일 미확인";
						$minus="승인일 미확인";
						$wsMoney="승인일 미확인";
					}else{
						$resetday=substr($row->resetday, 0, 10);
						$wsMoney=number_format($row->group_wsmoney)."원";
						$wsPrice="<b>".number_format($sumData[tot_price])."</b>원";
						$resetdayToTimeStamp = toTimeStamp(substr($row->resetday, 0, 19));
						$afterResetDay=date("Y-m-d",strtotime("+3 months", $resetdayToTimeStamp));
					}

					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					if($row->black){
						$blackImage = "<img src = './img/btn/black_icon.gif' align = 'absmiddle'>";
					}else{
						$blackImage = "";
					}
						
?>
						<tr>
						<!--
							<td>
								<p align="center">
									<input type=checkbox name=ids_chk value="<?=$row->id?>" style="border:0px;">
								</p>
							</td>
							-->
							<td>
								<? if($row->member_out!="Y"){ ?>
									<b><span class="font_orange"><A HREF="javascript:MemberEtcView('<?=$row->id?>')"><?=$number?></A></span></b>
								<? }else{ ?>
									<?=$number?>
								<? } ?>
							</td>
							<td>
								<?  if($row->member_out!="Y") { ?>
									<b align = 'absmiddle'><span class="font_orange"><A HREF="javascript:MemberInfo('<?=$row->id?>')"><?=$row->id?></A></span></b>
								<? } else { ?>
									<?=$row->id?>
								<? } ?>&nbsp;<?=$blackImage?>
							</td>
							<td>
								<?=$row->name?>(<?=number_format($row->q_count)?>)
							</td>
							<td>
								<?=$row->group_name?>
							</td>
							<td>
								<?=number_format($total_sum)?>원
							</td>
							<td>
								<?=$resetday?>
							</td>
							<td>
								<?=$afterResetDay?>
							</td>
							<td>
								<?=$wsMoney?>
							</td>
							<td>
								<span style="cursor:pointer"  onclick="javascript:MemberListWs('<?=$row->id?>');"><?=$wsPrice?><br />[내역]</span></font>
							</td>
							<td>
								<?=$minus?>
							</td>
							<td>
								<font class="ver81" color="#616161"><span onclick="up_ws('<?=$row->id?>','<?=$row->group_level?>','<?=$sumData[tot_price]?>')" style="cursor:pointer">[연장]</span></font>
							</td>
							<td>
								<? if($row->member_out!="Y"){ ?>
									<a href="javascript:member_write('<?=$row->id?>')"><img src="/admin/images/btn_edit.gif"></a>
								<? }else{ ?>
									&nbsp;
								<? } ?>
							</td>
						</tr>
<?
					$cnt++;
				}
				pmysql_free_result($result);
?>
					</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
<!--						<td><a href="javascript:check_del()"><img src="images/icon_tal.gif" border="0"></a></td>-->
						<td align=right><a href="javascript:excel_download()"><img src="images/btn_excel1.gif" border="0"></a></td>
					</tr>
					<tr>
						<td colspan=2 height=30 align=center><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>
			<form name=form2 action="member_reservelist.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>
			<form name=form3 method=post>
			<input type=hidden name=id>
			</form>
			<form name=form_member method=post>
			<input type=hidden name=id>
			</form>
			
			<form name=reserveform action="reserve_money.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>
			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>
			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=number>
			</form>
			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=get>
			<input type=hidden name=block value="">
			<input type=hidden name=gotopage value="">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=scheck value="<?=$scheck?>">
			<input type=hidden name=group_code value="<?=$group_code?>">
			<input type=hidden name=vperiod value="<?=$vperiod?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=member_type1 value="<?=$member_type1?>">
			<input type=hidden name=member_type2 value="<?=$member_type2?>">
			<input type=hidden name=member_type3 value="<?=$member_type3?>">
			</form>
			
			<form name=excelform action="member_excel.php" method=post>
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=scheck value="<?=$scheck?>">
			<input type=hidden name=group_code value="<?=$group_code?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=search value="<?=$search?>">
			</form>
			
			<form name=pgtform action="member_update_ws.php" method=post >
				<input type = 'hidden' name = 'id' id = 'pgtId' value = ''>
				<input type = 'hidden' name = 'group_level' id = 'pgLevel' value = ''>
				<input type = 'hidden' name = 'wsmoney' id = 'pgtMoney' value = ''>
				<input type = "hidden" name='returnUrl' value="<?=$_SERVER[HTTP_REFERER]?>">
			</form>
			 
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>검색된 회원목록 클릭시 정보</span></dt>
							<dd>
							- <b>번호</b> : <span style="letter-spacing:-0.5pt;">회원접속 정보 및 회원추가 입력폼 정보를 확인할 수 있습니다.</span><br>
							- <b>아이디</b> : <span style="letter-spacing:-0.5pt;">주민번호, 이메일, 주소 등 회원기본정보를 확인할 수 있습니다.</span><br>
							- <b>비번</b> : <span style="letter-spacing:-0.5pt;">운영자라고 하여도 회원의 비밀번호 자체는 변경하지 못하며 대신 임시비밀번호는 발급가능합니다.</span><br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="letter-spacing:-0.5pt;">(임시비밀번호는 회원가입시 등록한 이메일로 발송됩니다. 이메일 수신여부를 확인하세요.)</span><br>
							- <b>메일</b> : <span style="letter-spacing:-0.5pt;">회원에게 메일을 발송할 수 있습니다.</span><br>
							- <b>메모</b> : <span style="letter-spacing:-0.5pt;">회원에 대한 메모를 할 수 있습니다.(아이디 입력후 메모가능)</span><br>
							- <b>주소</b> : <span style="letter-spacing:-0.5pt;">집주소 또는 회사주소를 확인 할 수 있습니다.</span><br>
							- <b>전화</b> : <span style="letter-spacing:-0.5pt;">자택전화와 휴대전화 번호를 확인 할수 있으며 SMS 발송도 가능합니다.(SMS 발송은 SMS머니를 충전 후 이용이 가능합니다.)</span><br>
							- <b>적립금</b> : <span style="letter-spacing:-0.5pt;">운영자 임의로 적립금을 조절할 수 있으며 또한 적립금 내역을 확인할 수 있습니다.</span><br>
							- <b>내역</b> : <span style="letter-spacing:-0.5pt;">구매내역 및 쿠폰 보유내역을 확인 할수 있습니다.(구매내역 정보는 배송처리 완료된 주문건만 출력됩니다.)</span><br>
							- <b>인증</b> : <span style="letter-spacing:-0.5pt;">업종별 운영방식 설정에서 관리자 인증후 구매로 설정시에만 출력됩니다.</span><br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:parent.topframe.GoMenu(1,'shop_openmethod.php');"><span class="font_blue" style="letter-spacing:-0.5pt;">상점관리 > 쇼핑몰 환경 설정 > 업종별 운영방식 설정</span></a>
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
