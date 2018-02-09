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
#
# group = L 개별발송(직접입력) | 나머지는 단체발송 (A 전체회원, B 생일회원, G 검색된회원)
# mode  = SMS, LMS, MMS
# type  = up 문자전송, searchcnt 대상건수 조회(ajax), popuplist 명단확인
#
function msg_paging( $xml, $msg_gotopage, $msg_list_num=10, $msg_page_num=10 ) {
	$returnArr = array();
	$tmp_arr   = array();
	if( empty( $msg_gotopage ) ) $msg_gotopage = 1;
	$t_count = $xml->count();
	$pagecount = ceil( $t_count / $msg_list_num );
	$msg_offset = $msg_list_num * ( $msg_gotopage - 1);

	for( $i = 0; $i < $xml->count(); $i++ ){
		$tmp_arr[$i] = array( 'idx'=>$i, 'content'=>$xml->msg[$i]->content, 'title'=>$xml->msg[$i]->title );
	}
	rsort( $tmp_arr );

	for( $i = $msg_offset; $i < ( $msg_offset + $msg_list_num ); $i++ ){
		if( $i < $t_count ){
			$returnArr[] = $tmp_arr[$i];
		}
	}

	return $returnArr;
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


$sql = "SELECT id, authkey, return_tel, sms_uname, admin_tel FROM tblsmsinfo";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)){
	$return_tel = explode("-",$row->return_tel);
	$sms_id=$row->id;
	$sms_authkey=$row->authkey;
	$sms_uname=$row->sms_uname;
	$admin_tel=$row->admin_tel;
}
pmysql_free_result($result);

$duoSmsData = duo_smsAuthCheck();

$maxcount["SMS"] = floor( $duoSmsData['employ_sms_ea'] / $duoSmsData['sms_cut_count'] );
$maxcount["LMS"] = floor( $duoSmsData['employ_sms_ea'] / $duoSmsData['lms_cut_count'] );
$maxcount["MMS"] = floor( $duoSmsData['employ_sms_ea'] / $duoSmsData['mms_cut_count'] );

$isdisabled="1";

//if(ord($sms_id)==0 || ord($sms_authkey)==0 ) {
if ($duoSmsData[result] == "false"){
	$onload="<script>alert('SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\\n\\nSMS 아이디 및 인증키를 입력하시기 바랍니다.');</script>";
	$isdisabled="0";
} else if ($duoSmsData[result] == "true") {
	/*$smscountdata=getSmscount($sms_id, $sms_authkey);
	if(substr($smscountdata,0,2)=="OK") {
		$totcnt=substr($smscountdata,3);
	} else if(substr($smscountdata,0,2)=="NO") {
		$onload="<script>alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="2";
	} else if(substr($smscountdata,0,2)=="AK") {
		$onload="<script>alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.');</script>";
		$isdisabled="3";
	} else {
		$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
		$isdisabled="4";
	}*/
} else {
	$onload="<script>alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.');</script>";
}

$mode       = $_POST["mode"] ? $_POST["mode"] : "SMS";
$type       = $_POST["type"];
$tel_list   = $_POST["tel_list"];
$subject    = $_POST["subject"];
$msg        = $_POST["msg"];
$from_tel1  = $_POST["from_tel1"];
$from_tel2  = $_POST["from_tel2"];
$from_tel3  = $_POST["from_tel3"];
$clicknum   = $_POST["clicknum"];
$b_month_s  = $_POST["b_month_s"];
$b_day_s    = $_POST["b_day_s"];
$b_month_e  = $_POST["b_month_e"];
$b_day_e    = $_POST["b_day_e"];
$group      = $_REQUEST["group"] ? $_REQUEST["group"] : "L";
$group_code = $_POST["group_code"];
//$mem_type   = $_POST["mem_type"];
//$area       = ($type=="searchcnt") ? iconv("utf-8","euc-kr",$_POST["area"]) : $_POST["area"];
//$recid      = $_POST["recid"];
//$norecid    = $_POST["norecid"];
$buy_min    = $_POST["buy_min"];
$buy_max    = $_POST["buy_max"];
$buy_period = $_POST["buy_period"];
if($buy_period=="") $buy_period = "all";

$selected["group_code"][$group_code] = " selected";
$selected["area"][$area] = " selected";
$checked["buy_period"][$buy_period] = " checked";
$checked["group"][$group] = " checked";
$checked["mode"][$mode]   = " checked";
$MaxBytes = ($mode=="SMS") ? 80 : 2000;

if($group=="L"){
	$page_title = "SMS 개별 발송";
	$page_desc  = "특정 고객에게 문자메세지를 발송할 수 있습니다.";
	$menu_group = "L"; //for left menu
}else{
	$page_title = "SMS 등급/단체 발송";
	$page_desc  = "전체회원/등급회원/생일회원에게 단체 문자메세지를  발송할 수 있습니다.";
	$menu_group = "G"; //for left menu
}
//exdebug($_POST);


if($group!="L" && ($type=="up" or $type=="searchcnt" or $type=="popuplist")){
    /*
	if($area=="기타"){
		$area_exp = "!~ '^(서울|부산|대구|인천|광주|대전|울산|세종|경기|강원|충북|충청북도|충남|충청남도|전북|전라북도|전남|전라남도|경북|경상북도|경남|경상남도|제주)'";
	}else{
		switch($area){
			case "충남": $area_exp = "~ '^(".$area."|충청남도)'"; break;
			case "충북": $area_exp = "~ '^(".$area."|충청북도)'"; break;
			case "경남": $area_exp = "~ '^(".$area."|경상남도)'"; break;
			case "경북": $area_exp = "~ '^(".$area."|경상북도)'"; break;
			case "전남": $area_exp = "~ '^(".$area."|전라남도)'"; break;
			case "전북": $area_exp = "~ '^(".$area."|전라북도)'"; break;
			default : $area_exp = "~ '^".$area."'";
		}
	}
    */
    /*
	if(ord($mem_type[0])) $sql_mem[] = "mem_type=0";
	if(ord($mem_type[1])) $sql_mem[] = "mem_type=1";
	if(ord($mem_type[2])) $sql_mem[] = "mem_type=2 AND mem_type_2=0";
	if(ord($mem_type[3])) $sql_mem[] = "mem_type=2 AND mem_type_2=1";
	if($sql_mem) $sql_mem_type = "((". implode(") OR (", $sql_mem) . "))";
    */
	$where = " WHERE member_out!='Y' AND (news_yn='Y' OR news_yn='S') AND length(replace(mobile,'-',''))>9";
	if($group=="A"){
		$etcmsg = "전체회원 메세지 전송";
	}else if($group=="B"){
		$etcmsg = "생일회원 메세지 전송";
		$where .= " AND substr(birth,6,5)>='".$b_month_s."-".$b_day_s."'";
		$where .= " AND substr(birth,6,5)<='".$b_month_e."-".$b_day_e."'";
	}else if($group=="G"){
		$etcmsg = "검색회원 메세지 전송";
		if(ord($group_code))    $where .= " AND group_code = '{$group_code}'";
		if($_POST[joindate_s])  $where .= " AND date >= '{$_POST[joindate_s]}00000000'";
		if($_POST[joindate_e])  $where .= " AND date <= '{$_POST[joindate_e]}99999999'";
		if($_POST[logincnt_s])  $where .= " AND logincnt >= {$_POST[logincnt_s]}";
		if($_POST[logincnt_e])  $where .= " AND logincnt <= {$_POST[logincnt_e]}";
		if($_POST[logindate_s]) $where .= " AND logindate > '".str_replace("-", "", $_POST[logindate_s])."'";
		if($_POST[logindate_e]) $where .= " AND logindate < '".(str_replace("-", "", $_POST[logindate_e])+1)."'";
		//if(ord($sql_mem_type))  $where .= " AND {$sql_mem_type}";
		//if(ord($area))    $where .= " AND home_addr {$area_exp}"; //정규표현식
		//if(ord($recid))   $where .= " AND rec_id = '{$recid}'";
		//if(ord($norecid)) $where .= " AND rec_id = ''";
		if($buy_min or $buy_max){
			if($buy_min) $sql_buy[] = "SUM( price + deli_price - dc_price::int - reserve )>={$buy_min}";
			if($buy_max) $sql_buy[] = "SUM( price + deli_price - dc_price::int - reserve )<={$buy_max}";
			if($buy_period=="1m") $sql_buy_period = " AND ordercode>='".date("Ymd",strtotime("-1 month"))."'";
			if($buy_period=="6m") $sql_buy_period = " AND ordercode>='".date("Ymd",strtotime("-6 month"))."'";
			if($buy_period=="1y") $sql_buy_period = " AND ordercode>='".date("Ymd",strtotime("-1 year"))."'";
			$where .= " AND id IN (SELECT id FROM tblorderinfo WHERE oi_step1 ='4'{$sql_buy_period} GROUP BY id HAVING ".implode(" AND ", $sql_buy).")";
		}
	}
	$sql = "SELECT COUNT(*) as cnt FROM tblmember".$where;
	//exdebug($sql);
	$result = pmysql_query($sql);
	$row = pmysql_fetch_object($result);
	$search_cnt[$group] = $row->cnt;
	pmysql_free_result($result);
	$error = pmysql_error();
	if($error) $onload = "<script>window.onload=function(){ alert('오류가 발생했습니다. 발송 조건을 확인해 주세요.'); }</script>";

	if($type=="searchcnt"){ // ajax
		echo $error ? "err" : $search_cnt[$group];
		exit;
	}
}

if($type=="popuplist"){ //팝업창으로 SMS대상 명단보기
	if($error)
		echo "<p>오류가 발생했습니다. 발송 조건을 확인해 주세요.</p>";
	else{
		$sql = "SELECT id, name, mobile FROM tblmember".$where." ORDER BY name";
		exdebug($sql);
		$result = pmysql_query($sql);
		$i = 0;
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-KR\">\r\n";
		echo "<style>table{font-size:12px} table{border-collapse: collapse} tr,th,td{padding: 3px 5px 2px; border: 1px solid #999}</style>\r\n";
		echo "<table><tr><th>No.</th><th>ID</th><th>이름</th><th>전화번호</th></tr>\r\n";
		while($row = pmysql_fetch_object($result)) {
			$i++;
			echo "<tr><td>{$i}</td><td>{$row->id}</td><td>{$row->name}</td><td>{$row->mobile}</td></tr>\r\n";
		}
		pmysql_free_result($result);
		echo "</table>\r\n";
	}
	exit;
}

if(!$error && $type=="up" && ($group=="L" || $group=="A" || $group=="B" || $group=="G")) {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$fromtel=$from_tel1."-{$from_tel2}-".$from_tel3;
	$cnt=0;
	if($group=="L"){
		$cnt = count(explode("||",$tel_list));
		$totellist  = $tel_list;
		$tonamelist = "";
		$etcmsg = "개별 메세지 전송";
	}else{
		$sql = "SELECT mobile, name FROM tblmember".$where;
		$result = pmysql_query($sql,get_db_conn());
		$tel_list  = '';
		$name_list = '';
		while($row = pmysql_fetch_object($result)) {
			$row->mobile = str_replace(",","",$row->mobile);
			$row->mobile = str_replace("-","",$row->mobile);
			if(strlen($row->mobile)<10 || strlen($row->mobile)>11){
			} else {
				$tel_list .="||".$row->mobile;
				$name_list.="||".str_replace(",","",$row->name);
				$cnt++;
			}
		}
		pmysql_free_result($result);

		$totellist  = substr($tel_list,2);
		$tonamelist = substr($name_list,2);
	}

	if($mode=="MMS"){
		if($_FILES[goods_img][name]){
			$onload = "";
			$imageKind = array ('image/JPEG', 'image/jpeg', 'image/JPG', 'image/jpg', 'image/PJPEG', 'image/pjpeg');
			if (!in_array($_FILES['goods_img']['type'], $imageKind)) {
				$onload = "<script>window.onload=function(){ alert('JPG 파일만 업로드가 가능합니다.'); location.replace('{$_SERVER["PHP_SELF"]}'); }</script>";
			}
			if ($_FILES["goods_img"]["size"] > (1024*20)){
				$onload = "<script>window.onload=function(){ alert('20kb 이화의 파일만 업로드가 가능합니다.'); location.replace('{$_SERVER["PHP_SELF"]}'); }</script>";
			}
			if($onload){
				echo $onload;
				exit;
			}
		}
	}

	if (isdev() || ($cnt <= $maxcount[$mode] && $cnt>0)) {
		//if(isdev()){
		//	debug($fromtel);
		//	debug($totellist);
		//}else{
			if($mode=="SMS")
				$temp = SendSMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, 0, $msg, $etcmsg);
			else
				$temp = SendMMS($sms_id, $sms_authkey, $totellist, $tonamelist, $fromtel, 0, $msg, $etcmsg, $_FILES, $subject);
		//}
		if($temp['result'] == 'true'){
			$onload = "<script>window.onload=function(){ alert('문자 전송이 성공했습니다.'); location.replace('{$_SERVER["PHP_SELF"]}'); }</script>";
		}else{
			$onload = "<script>window.onload=function(){ alert('문자 전송이 실패했습니다.'); /*location.replace('{$_SERVER["PHP_SELF"]}');*/ }</script>";
			//exdebug($temp);
		}
	} else if ($cnt==0) {
		$onload="<script>window.onload=function(){ alert('발송 대상이 없습니다.'); /*location.replace('{$_SERVER["PHP_SELF"]}');*/ }</script>";
	} else {
		$onload="<script>window.onload=function(){ alert('SMS 머니가 부족합니다. 충전후 이용하세요.'); /*location.replace('{$_SERVER["PHP_SELF"]}');*/ }</script>";
	}

	//$type="";$msg="";$from_tel1="";$from_tel2="";$from_tel3="";$clicknum="";$b_month="";$b_day="";$group="";$group_code="";
}

//if($maxcount>0 && ord($onload)==0 && $type!="changegroup" && $type!="birthsearch") $onload="<script>window.onload=function(){ alert('현재 {$maxcount}건의 SMS를 발송하실 수 있습니다.'); }</script>";

if(ord($msg)==0) $msg="";



# 개별 발송용 저장 메세지를 불러온다

$msg_path = './sms_msg/';
$msg_file = 'mem_msg.xml';
if( file_exists( $msg_path.$msg_file ) ){
	$msg_obj = simplexml_load_file( $msg_path.$msg_file );
}

?>

<?php include("header.php"); ?>
<?php
if( file_exists( $msg_path.$msg_file ) ){
	$paging = new newPaging( $msg_obj->count(), 10, 10 );
	$msg_arr = msg_paging( $msg_obj, $paging->gotopage );
}
?>

<style type="text/css">
<!--
TEXTAREA {  clip:   rect(   ); overflow: hidden; background-image:url('');font-family:굴림;}
.phone {  font-family:굴림; height: 80px; width: 173px;color: #191919;  FONT-SIZE: 9pt; font-style: normal; background-color: #A8E4ED;; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px}

input.border-none {
	border:none; border-right:0px; border-top:0px; boder-left:0px; boder-bottom:0px;
}

div.div_hide {
	padding-left: 12pt;
	padding-right: 12pt;
	overflow: hidden;
	font-family: 굴림;
	color: #191919;
	FONT-SIZE: 9pt;
	font-style: normal;
	text-align: left;
}
-->
</style>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";

	pForm.logindate_s.value = period[val];
	pForm.logindate_e.value = period[0];
}

function CheckForm() {
<?php if($isdisabled=="1"){?>
	var mode  = $(":radio[name='mode']:checked").val();
	var group = $(":radio[name='group']:checked").val();
	var mem_cnt;

	if(document.form1.msg.value.length==0) {
		alert("전송할 메세지를 입력하세요.");
		document.form1.msg.focus();
		return;
	}
	cal_pre2();

	for(i=1;i<=3;i++) {
		if(document.form1["from_tel"+i].value.length==0) {
			alert("보내는 사람 전화번호를 입력하세요.");
			document.form1["from_tel"+i].focus();
			return;
		}
		if(!IsNumeric(document.form1["from_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["from_tel"+i].focus();
			break; return;
		}
	}
	from_tel=document.form1.from_tel1.value+document.form1.from_tel2.value+document.form1.from_tel3.value;
	if(from_tel.length<8) {
		alert("보내는 사람 전화번호 입력이 잘못되었습니다.");
		document.form1.from_tel1.focus();
		return;
	}
<? if($group=="L"){ ?>
	cnt=document.form1.to_list.options.length - 1;
	if(cnt==0) {
		alert("받는 사람 추가가 안되었습니다.");
		document.form1.to_list.focus();
		return;
	}
	if (cnt > <?=$maxcount?>) {
		alert("SMS 머니가 부족합니다.\n\n<?=$maxcount?>명 까지 발송 가능합니다.");
		document.form1.to_list.focus();
		return;
	}
	document.form1.tel_list.value="";
	for(i=1;i<=cnt;i++) {
		if(i==1) {
			document.form1.tel_list.value+=document.form1.to_list.options[i].value;
		} else {
			document.form1.tel_list.value+="||"+document.form1.to_list.options[i].value;
		}
	}
<? }else{ ?>
	if(group=="" || group==undefined) {
		alert("받는 등급을 선택하세요.");
		return;
	}
	if(group=="A") mem_cnt = $("#ResultCnt_1").val();
	if(group=="B") mem_cnt = $("#ResultCnt_2").val();
	if(group=="G") mem_cnt = $("#ResultCnt_3").val();

	if(mem_cnt==0){
		alert("발송대상자가 없습니다.");
		return;
	}
<?if(!isdev()){?>
	if((mode=="SMS" && <?=$maxcount["SMS"]?> < mem_cnt) ||
	   (mode=="LMS" && <?=$maxcount["LMS"]?> < mem_cnt) ||
	   (mode=="MMS" && <?=$maxcount["MMS"]?> < mem_cnt)){
		alert("SMS 머니가 부족합니다. 충전후 이용하세요.");
		return;
	}
<?}?>
<? } ?>
	if(confirm("해당 문자를 발송하시겠습니까?")){
		$("#btnSend").attr("href","javascript:alert('발송중입니다.')");
		document.form1.type.value="up";
		document.form1.submit();
	}
<?php }else if($isdisabled=="0"){?>
	alert("SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\n\nSMS 아이디 및 인증키를 입력하시기 바랍니다.");
<?php }else if($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?php }?>
}

function DefaultFrom(checked,ch_type) {
	if(ch_type) {
		if(document.form1.clicknum.checked==false) {
			document.form1.from_tel1.value="<?=$return_tel[0]?>";
			document.form1.from_tel2.value="<?=$return_tel[1]?>";
			document.form1.from_tel3.value="<?=$return_tel[2]?>";
			document.form1.clicknum.checked = true;
		} else {
			document.form1.from_tel1.value="";
			document.form1.from_tel2.value="";
			document.form1.from_tel3.value="";
			document.form1.clicknum.checked = false;
		}
	} else {
		if(checked) {
			document.form1.from_tel1.value="<?=$return_tel[0]?>";
			document.form1.from_tel2.value="<?=$return_tel[1]?>";
			document.form1.from_tel3.value="<?=$return_tel[2]?>";
		} else {
			document.form1.from_tel1.value="";
			document.form1.from_tel2.value="";
			document.form1.from_tel3.value="";
		}
	}
}

function addChar(aspchar) {
<?php if($isdisabled=="1"){?>
	document.form1.msg.value += aspchar;
	cal_pre2();
<?php }else if($isdisabled=="0"){?>
	alert("SMS 회원가입 및 충전 후 SMS 기본환경 설정에서\n\nSMS 아이디 및 인증키를 입력하시기 바랍니다.");
<?php }else if($isdisabled=="2"){?>
	alert("SMS 회원 아이디가 존재하지 않습니다.\n\nSMS 기본환경 설정에서 SMS 아이디 및 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="3"){?>
	alert("SMS 회원 인증키가 일치하지 않습니다.\n\nSMS 기본환경 설정에서 인증키를 정확히 입력하시기 바랍니다.");
<?php }else if($isdisabled=="4"){?>
	alert("SMS 서버와 통신이 불가능합니다.\n\n잠시 후 이용하시기 바랍니다.");
<?php }?>
}
var MAX_BYTES = <?=$MaxBytes?>;

function cal_pre2() {
	obj_msg = document.form1.msg;
	obj_len = document.form1.len_msg;

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > MAX_BYTES){
		if(MAX_BYTES==80){
			alert('메시지 내용이 '+MAX_BYTES+' byte를 넘어 LMS로 전환됩니다.');
			chgMode('LMS');
			document.form1.mode[1].checked = true;
			cal_pre2();
			return;
		}
		reserve = strcnt - MAX_BYTES;
		alert('메시지 내용은 '+MAX_BYTES+' byte를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+reserve+' byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
		obj_msg.value = nets_check2(obj_msg.value);
		strcnt = cal_byte2(obj_msg.value);
		obj_len.value=strcnt;
		return;
	}
	obj_len.value=strcnt;
}

function cal_byte2(aquery) {
	var tmpStr;
	var temp = 0;
	var onechar;
	var tcount = 0;
	var reserve = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0; k<temp; k++) {
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount ++;
		}
	}
	return tcount;
}

function nets_check2(aquery) {
	var temStr;
	var temp = 0;
	var onechar;
	var tcount;
	tcount = 0;

	tmpStr = new String(aquery);
	temp = tmpStr.length;

	for(k=0;k<temp;k++){
		onechar = tmpStr.charAt(k);
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount++;
		}
		if(tcount > MAX_BYTES) {
			tmpStr = tmpStr.substring(0,k);
			break;
		}
	}
	return tmpStr;
}

function ChangeGroupCode() {
	val=document.form1.group_code.options[document.form1.group_code.selectedIndex].value;
	if(val!="") {
		document.form1.type.value="changegroup";
		document.form1.submit();
	}
}

function SearchCnt(g_idx) {
	var f = document.form1;
	var min = f.buy_min.value;
	var max = f.buy_max.value;
	if(g_idx==3 && min!="" && max!="" && min>max){
		alert("구매금액 최소값이 최대값보다 큽니다.");
		f.buy_min.focus();
		return;
	}
	f.group[g_idx-1].checked=true;
	f.type.value="searchcnt";
	//f.submit();
	$("#ResultCnt_1").val("");
	$("#ResultCnt_2").val("");
	$("#ResultCnt_3").val("");
	$("#ResultView_1").hide();
	$("#ResultView_2").hide();
	$("#ResultView_3").hide();

	$.post( "<?=$_SERVER["PHP_SELF"]?>", $( f ).serialize() ).done(function( data ) {
		if(data=="err")
			alert("오류가 발생했습니다. 발송 조건을 확인해 주세요.");
		else{
			$("#ResultCnt_"+g_idx).val(data);
			$("#ResultView_"+g_idx).show();
		}
	});
}
/*
function BirthSearch() {
	document.form1.group[1].checked=true;
	document.form1.type.value="birthsearch";
	document.form1.submit();
}
function GroupSearch(){
	document.form1.group[2].checked=true;
	document.form1.type.value="groupsearch";
	document.form1.submit();
}
*/
function PopupList(){
	window.open("about:blank","popupsmslist","width=420,height=600,scrollbars=yes");
	document.form1.method = "post";
	document.form1.target = "popupsmslist";
	document.form1.type.value = "popuplist";
	document.form1.action = "<?=$_SERVER["PHP_SELF"]?>";
	document.form1.submit();
	document.form1.target = "_self";
}

function chgMode(mode){
	if(mode=="SMS"){
		MAX_BYTES = 80;
		$("#mms_file").hide();
		$("#box_subject").hide();
	}else if(mode=="LMS"){
		MAX_BYTES = 2000;
		$("#mms_file").hide();
		$("#box_subject").show();
	}else if(mode=="MMS"){
		MAX_BYTES = 2000;
		$("#mms_file").show();
		$("#box_subject").show();
	}
	$("#max_byte").html(MAX_BYTES);
	cal_pre2();
}

function ToAdd() {
	for(i=2;i<=3;i++) {
		if(!IsNumeric(document.form1["to_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["to_tel"+i].focus();
			break; return;
		}
	}
	tel_txt=document.form1.to_tel1.value+"-"+document.form1.to_tel2.value+"-"+document.form1.to_tel3.value;
	tel_val=document.form1.to_tel1.value+""+document.form1.to_tel2.value+""+document.form1.to_tel3.value;
	if(tel_txt.length<12 || tel_txt.length>13) {
		alert("전화번호 입력이 잘못되었습니다.");
		return;
	}
	to_list=document.form1.to_list;
	if(to_list.options.length>50) {
		alert("받는 사람은 1회 50명 까지 가능합니다.");
		return;
	}
	for(i=1;i<to_list.options.length;i++) {
		if(tel_val==to_list.options[i].value) {
			alert("이미 추가된 번호입니다.\n\n다시 확인하시기 바랍니다.");
			document.form1.to_tel1.selectedIndex=0;
			document.form1.to_tel2.value="";
			document.form1.to_tel3.value="";
			return;
		}
	}
	new_option = document.createElement("OPTION");
	new_option.text=tel_txt;
	new_option.value=tel_val;
	to_list.add(new_option);
	cnt=to_list.options.length - 1;
	to_list.options[0].text = "------------------- 수신목록("+cnt+") ----------------------";
	document.form1.to_tel1.selectedIndex=0;
	document.form1.to_tel2.value="";
	document.form1.to_tel3.value="";
}

function ToDelete() {
	to_list=document.form1.to_list;
	for(i=1;i<to_list.options.length;i++) {
		if(to_list.options[i].selected){
			to_list.options[i]=null;
			cnt=to_list.options.length - 1;
			to_list.options[0].text = "------------------- 수신목록("+cnt+") ----------------------";
			return;
		}
	}
	alert("삭제할 번호를 선택하세요.");
	to_list.focus();
}

function sms_addressbook() {
	window.open("market_smsaddresspop.php","smsaddresspop","width=400,height=350,scrollbars=no");
}
/*
function noRecId(o){
	if(o.checked){
		document.form1.recid.value = "";
		document.form1.recid.disabled = true;
	}else
		document.form1.recid.disabled = false;
}
*/
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span><?=$page_title?></span></p></div></div>
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
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=$page_title?></div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=$page_desc?></span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="961"><!--761-->
				<tr>
					<td width="224" valign="top">
					<table align="center" cellpadding="0" cellspacing="0" width="200">
					<tr>
						<td>
						<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
						<TR>
							<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
						</TR>
						<TR>
							<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" name=msg rows=5 cols=26 bgcolor="#A8E4ED" onkeyup="cal_pre2();" onchange="cal_pre2();" <?php if($isdisabled!="1") echo "disabled";?>><?=$msg?></TEXTAREA></TD>
						</TR>
						<TR>
							<TD align=center height="26" background="images/sms_down_01.gif"><input type="text" name="len_msg" value="0" style="PADDING-RIGHT:5px; WIDTH:35px; TEXT-ALIGN:right" onfocus="this.blur();" class="input_hide">bytes (최대 <span id="max_byte"><?=$MaxBytes?></span>bytes)<script>cal_pre2();</script></TD>
						</TR>
						<TR>
							<TD HEIGHT=6></TD>
						</TR>
						<TR>
							<TD>
							<TABLE cellSpacing=1 cellPadding=0 width="100%" bgColor="#EEEEEE" border=0>
							<TR align=middle bgColor=#ffffff>
<?php
	$specialchar = array("☆","★","○","●","◎","◇","◆","□","■","△","▲","◁","◀","♤","♠","♡","♥","♧","♣","⊙","◈","▣","◐","▩","▨","▒","♨","☏","☎","℡","☜","☞","♩","♪","♬","▽","▼","∞","∴","∽","※","㉿","㈜","™","￣","…","?","》","♂","♀","∬","‡","￠","￥","⊃","∪","∧","⇒","∀","∃","→","←","↑","↓","↔","『","』","【","】","(",")","①","②","③","④","⑤","⑥","⑦","⑧","⑨");

	for($i=0;$i<count($specialchar);$i++) {
		if ($i>0 && $i%10==0) {
			echo "</tr>\n";
			echo "<TR align=middle bgColor=#ffffff>\n";
		}
		echo "<td width=10% style=\"CURSOR: hand; LINE-HEIGHT: 14pt; FONT-FAMILY: 굴림\" onmouseover=\"this.style.background='#DFF6FF'\" onmouseout=\"this.style.background='#FFFFFF'\" onclick=\"addChar('{$specialchar[$i]}');\">{$specialchar[$i]}</td>\n";
	}
?>
							</TABLE>
							</TD>
						</TR>
						</TABLE>
						</td>
					</tr>
					</table>
					</td>
					<td width="11" valign="top">&nbsp;</td>
					<td width="726" valign="top"><!--526-->
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="616"><!--516-->
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="750">
							<div class="point_title">휴대폰 문자메세지 발송정보 입력 <input style='float:right; margin-right:5px;' type='button' value='SMS 메세지 저장' onclick='msg_insert()' ></div>
							<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
							<tr>
								<td width="100%">
								<div class="table_style01">
								<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<th><span>구분</span></th>
									<TD width="596" class="td_con1" height="40"><p class="LIPoint">
										<input type="radio" name="mode" value="SMS" onclick="chgMode('SMS')"<?=$checked["mode"]["SMS"]?> /><label><b>SMS</b> (<?=number_format($maxcount["SMS"])?>건)</label> &nbsp;
										<input type="radio" name="mode" value="LMS" onclick="chgMode('LMS')"<?=$checked["mode"]["LMS"]?> /><label><b>LMS</b> (<?=number_format($maxcount["LMS"])?>건)</label> &nbsp;
										<input type="radio" name="mode" value="MMS" onclick="chgMode('MMS')"<?=$checked["mode"]["MMS"]?> /><label><b>MMS</b> (<?=number_format($maxcount["MMS"])?>건)</label>
									</TD>
								</TR>
								<TR>
									<th><span>보내는 사람</span></th>
									<TD width="596" class="td_con1" height="40"><p class="LIPoint">
										<input type=text name=from_tel1 value="<?=$from_tel1?>" size=5 maxlength=3 onKeyUp="return strnumkeyup(this);" class="input"> - 
										<input type=text name=from_tel2 value="<?=$from_tel2?>" size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input"> - 
										<input type=text name=from_tel3 value="<?=$from_tel3?>" size=5 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input">
										<input type=checkbox id="idx_clicknum" name=clicknum value="Y" <?php if($clicknum=="Y") echo "checked";?>  onclick="DefaultFrom(this.checked,'')">
										<a href="javascript:DefaultFrom('','1');"><img src="images/btn_tel.gif" border="0"></a>
									</TD>
								</TR>
								<TR id="box_subject"<?if($mode=="SMS"){?> style="display:none"<?}?>>
									<th><span>제목</span></th>
									<TD width="596" class="td_con1" height="40"><p class="LIPoint">
										<input type=text name=subject value="<?=$subject?>" size=80 class="input_bd_st01">
									</TD>
								</TR>
<? if($group=="L"){ //-----개별발송----- ?>
							<TR>
								<th><span>받는 사람</span></th>
								<TD>
								<div class="table_none">
								<input type=hidden name=tel_list />
								<input type=radio id="idx_group0" name=group value="L"<?=$checked["group"]["L"]?> style="display:none" />
								<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%">
										<table cellpadding="0" cellspacing="0">
										<tr>
											<td><p class="LIPoint">
												<select name=to_tel1 style="width:50" class="select">
													<option value="010">010</option>
													<option value="011">011</option>
													<option value="016">016</option>
													<option value="017">017</option>
													<option value="018">018</option>
													<option value="019">019</option>
												</select> - 
												<input type=text name=to_tel2 size=4 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input" /> -
												<input type=text name=to_tel3 size=4 maxlength=4 onKeyUp="return strnumkeyup(this);" class="input" />&nbsp;
											</td>
											<td><a href="javascript:ToAdd();"><img src="images/btn_add1.gif" border="0" hspace="2"></a></td>
											<td><a href="javascript:ToDelete();"><img src="images/btn_del.gif" border="0"></a></td>
											<td><a href="javascript:sms_addressbook();"><img src="images/btn_addresssearch.gif" border="0" hspace="2"></a></td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td width="100%" style="padding-top:2pt;">
										<select name=to_list size=10 style="WIDTH:80%" class="select">
											<option value="" style="BACKGROUND-COLOR: #ffff00">------------------- 수신목록(0) ----------------------</option>
										</select>
									</td>
								</tr>
								</table>
								</div>
								</TD>
							</TR>
<? }else{ //-----등급/단체발송----- ?>
								<TR>
									<th colspan=2 class="ptb_10"><span>받는 등급/단체 선택</span></th>
								</TR>
								<TR>
									<TD width="592" valign="top" colspan="2" style="padding:10pt; border-left:1px solid #b9b9b9"><!--492-->

									<div class="table_none">
									<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td width="190" style="padding:3pt 0;">
											<input type=radio id="idx_group1" name=group value="A"<?=$checked["group"]["A"]?> />
											<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group1>
												<span class="font_orange"><B>전체회원에게 발송하기</B></span>
											</label>
										</td>
										<td width="*" style="padding:3pt 0;">
											<input type=button value="검색" onclick="SearchCnt(1)" class="submit1">
											<input type=text name="ResultCnt_1" id="ResultCnt_1" size="8" value="<?=$search_cnt["A"]?>" onfocus="this.blur();" style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" class="input"> 명
											<span id="ResultView_1" style="display:none">→ <a href="javascript:PopupList()">명단보기</a></span>
										</td><!--317-->
									</tr>
									<tr>
										<td width="190" style="padding:3pt 0; border-top:1px dotted #b9b9b9">
											<input type=radio id="idx_group2" name=group value="B"<?=$checked["group"]["B"]?> />
											<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group2>
												<B>생일회원에게 발송하기</B>
											</label>
										</td>
										<td align=left width="*" style="padding-top:2pt;  border-top:1px dotted #b9b9b9">
											<select name=b_month_s class="select">
<?php
					if(ord($b_month_s)==0) $b_month_s=date("m");
					for($i=1;$i<=12;$i++) {
						$select='';
						if($b_month_s==sprintf("%02d",$i)) $select="selected";
						echo "<option value=\"".sprintf("%02d",$i)."\" {$select}>".sprintf("%02d",$i)."</option>\n";
					}
?>
											</select> 월
											<select name=b_day_s class="select">
<?php
					if(ord($b_day_s)==0) $b_day_s=date("d");
					for($i=1;$i<=31;$i++) {
						$select='';
						if($b_day_s==sprintf("%02d",$i)) $select="selected";
						echo "<option value=\"".sprintf("%02d",$i)."\" {$select}>".sprintf("%02d",$i)."</option>\n";
					}
?>
											</select> 일 ~
											<select name=b_month_e class="select">
<?php
					if(ord($b_month_e)==0) $b_month_e=date("m");
					for($i=1;$i<=12;$i++) {
						$select='';
						if($b_month_e==sprintf("%02d",$i)) $select="selected";
						echo "<option value=\"".sprintf("%02d",$i)."\" {$select}>".sprintf("%02d",$i)."</option>\n";
					}
?>
											</select> 월
											<select name=b_day_e class="select">
<?php
					if(ord($b_day_e)==0) $b_day_e=date("d");
					for($i=1;$i<=31;$i++) {
						$select='';
						if($b_day_e==sprintf("%02d",$i)) $select="selected";
						echo "<option value=\"".sprintf("%02d",$i)."\" {$select}>".sprintf("%02d",$i)."</option>\n";
					}
?>
											</select> 일 &nbsp;
											<input type=button value="검색" onclick="SearchCnt(2)" class="submit1">
											<input type=text name="ResultCnt_2" id="ResultCnt_2" size="8" value="<?=$search_cnt["B"]?>" onfocus="this.blur();" style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" class="input"> 명
											<span id="ResultView_2" style="display:none">→ <a href="javascript:PopupList()">명단보기</a></span>
<?php
/*
					if($group=="B" &&$type=="birthsearch") {
						//$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE SUBSTR(resno,3,4)='".$b_month.$b_day."' AND (news_yn='Y' OR news_yn='S') AND mobile<>''";
						$sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE substr(birth,6,5)='".$b_month."-".$b_day."' AND (news_yn='Y' OR news_yn='S') AND mobile<>''";
						$result = pmysql_query($sql,get_db_conn());
						$row = pmysql_fetch_object($result);
						$bircnt = $row->cnt;
						pmysql_free_result($result);
						echo "<input type=text name=birth_mem size=\"6\" value=\"{$bircnt}\" onfocus=\"this.blur();\" style=\"PADDING-RIGHT: 5px; TEXT-ALIGN: right\" class=\"input\">명";
					}
*/
?>
										</td>
									</tr>
									<tr>
										<td width="190" style="padding:3pt 0; border-top:1px dotted #b9b9b9">
											<input type=radio id="idx_group3" name=group value="G"<?=$checked["group"]["G"]?> />
											<label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_group3>
												<B>검색된 회원에게 발송하기</B>
											</label>
										</td>
										<td align=left width="*" style="padding-top:2pt; border-top:1px dotted #b9b9b9">
										<table style="width:100%">
											<tr>
												<td width="70"><b>회원등급</b></td>
												<td>
										<select name=group_code style="width:200" class="select">
											<option value="">해당 등급을 선택하세요.</option>
<? //onchange="ChangeGroupCode();"
	$sql = "SELECT group_code, group_name FROM tblmembergroup WHERE group_name!='' order by group_code ";
	$result = pmysql_query($sql,get_db_conn());
	while ($row=pmysql_fetch_object($result)) {
?>
												<option value="<?=$row->group_code?>"<?=$selected["group_code"][$row->group_code]?>><?=$row->group_name?></option>
<?
	}
	pmysql_free_result($result);
?>
											</select>
												</td>
											</tr>
											<tr>
												<td><b>가입일</b></td>
												<td>
													<input class="input_bd_st01" type="text" name="joindate_s" size="8" value="<?=$_REQUEST[joindate_s]?>"> ~ 
													<input class="input_bd_st01" type="text" name="joindate_e" size="8" value="<?=$_REQUEST[joindate_e]?>">
													(YYYYMMDD 예:20150101)
												</td>
											</tr>
											<tr>
												<td><b>방문횟수</b></td>
												<td>
													<input class="input_bd_st01" type="text" name="logincnt_s" size="5" value="<?=$_REQUEST[logincnt_s]?>"> ~ 
													<input class="input_bd_st01" type="text" name="logincnt_e" size="5" value="<?=$_REQUEST[logincnt_e]?>">
												</td>
											</tr>
											<tr>
												<td><b>방문일자</b></td>
												<td>
													<input class="input_bd_st01" type="text" size="10" name="logindate_s" value="<?=$_REQUEST[logindate_s]?>"/> ~ <input class="input_bd_st01" type="text" name="logindate_e"  size="10" value="<?=$_REQUEST[logindate_e]?>"/>
													<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
													<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
													<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
													<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
												</td>
											</tr>
                                            <!--
											<tr>
												<td><b>회원구분</b></td>
												<td>
													<input type="checkbox" name="mem_type[0]" value=" checked"<?=$mem_type[0]?> />일반회원
													<input type="checkbox" name="mem_type[1]" value=" checked"<?=$mem_type[1]?> />지사
													<input type="checkbox" name="mem_type[2]" value=" checked"<?=$mem_type[2]?> />직영가맹점
													<input type="checkbox" name="mem_type[3]" value=" checked"<?=$mem_type[3]?> />일반가맹점
												</td>
											</tr>
                                            -->
                                            <!--
											<tr>
												<td><b>주거지역</b></td>
												<td>
													<select name="area">
														<option value="">선택</option>
<?
	//지역
	$sql = "select code_name from common_code where code_group='tel_list' and code_id!='070'";
	$result = pmysql_query($sql);
	while ($row=pmysql_fetch_object($result)) {
?>
														<option value="<?=$row->code_name?>"<?=$selected["area"][$row->code_name]?>><?=$row->code_name?></option>
<?
	}
	pmysql_free_result($result);
?>
														<option value="기타"<?=$selected["area"]["기타"]?>>기타</option>
													</select> ※'기타'에는 주소가 없는 회원도 검색됩니다.
												</td>
											</tr>
											<tr>
												<td><b>교육이수</b></td>
												<td>(준비중)</td>
											</tr>
											<tr>
												<td><b>추천인ID</b></td>
												<td>
													<input class="input_bd_st01" type="text" size="20" name="recid" value="<?=$recid?>" /> &nbsp;
													<input type="checkbox" name="norecid" value="1"<?=$checked["norecid"]?> onclick="noRecId(this)" />추천인ID 없음
												</td>
											</tr>
                                            -->
											<tr>
												<td><b>구매금액</b></td>
												<td>
													<input class="input_bd_st01" type="text" size="10" name="buy_min" value="<?=$buy_min?>" style="text-align:right" /> ~
													<input class="input_bd_st01" type="text" size="10" name="buy_max" value="<?=$buy_max?>" style="text-align:right" /> 원<br />
													<input type="radio" name="buy_period"<?=$checked["buy_period"]["all"]?> value="all" />누적
													<input type="radio" name="buy_period"<?=$checked["buy_period"]["1m"] ?> value="1m" />최근1개월
													<input type="radio" name="buy_period"<?=$checked["buy_period"]["6m"] ?> value="6m" />최근6개월
													<input type="radio" name="buy_period"<?=$checked["buy_period"]["1y"] ?> value="1y" />최근1년
												</td>
											</tr>
										</table>

											<input type=button value="검색" onclick="SearchCnt(3)" class="submit1">
											<input type=text name="ResultCnt_3" id="ResultCnt_3" size="8" value="<?=$search_cnt["G"]?>" onfocus="this.blur();" style="PADDING-RIGHT: 5px; TEXT-ALIGN: right" class="input"> 명
											<span id="ResultView_3" style="display:none">→ <a href="javascript:PopupList()">명단보기</a></span>
										</td>
									</tr>
									</table>
									</div>
									</TD>
								</TR>
<? } ?>
								<TR id="mms_file"<?=($mode=="MMS")?"":" style=\"display:none\""?>>
									<th><span>이미지 첨부</span></th>
									<TD>
										<input type="file" name="goods_img" size="40" />
										<br>20kb 이하의 jpg파일만 가능합니다.
									</TD>
								</TR>
								</TABLE>
								</div>
								</td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td width="516" height="50" align=center>
							<a href="javascript:CheckForm();" id="btnSend"><img src="images/btn_sms3.gif" border="0" <?php if($isdisabled!="1") echo "style=\"filter:Alpha(Opacity=60) Gray\"";?>></a>&nbsp;&nbsp;
							<a href="market_smsfill.php"><img src="images/btn_sms4.gif" border="0" hspace="2"></a>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3">저장 메세지</div>
					<div class="title_depth3_sub"><span> 해당 메세지를 클릭하면 메세지창에 바로 입력됩니다.</span></div>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<form id='msg_frm' name='msg_frm' method='POST' action='<?=$_SERVER["PHP_SELF"]?>' >
					<input type='hidden' name='msg_mode' id='msg_mode' value='' >
					<input type='hidden' name='block' id='block' value='' >
					<input type='hidden' name='gotopage' id='gotopage' value='' >
				<td>
<?php
if( count( $msg_arr ) > 0 ){
?>
					<div class='massage_type' >
					<table BORDER='0' CELLPADDING='0' CELLSPACING='0' align="left" >
<?php
	$msg_cnt = 1;
	foreach( $msg_arr as $msgKey=>$msgVal ){
		if( $msgKey == 0 ) echo '<tr>';
		if( ( $msgVal % 5 ) == 0 ) echo '<tr>';
?>
							<td valign="top">
								<table name='msg_table' >
									<tr>
										<td height="23">
											<!-- 개별 메세지 <?=$msg_i?> -->
											<?=$msgVal['title']?>
											<div style='float:right;'>
												<input type='button' value='수정' onclick='javascript:msg_modify(<?=$msgVal['idx']?>)' >
												<input type='button' value='삭제' onclick='javascript:msg_delete(<?=$msgVal['idx']?>)' >
											</div>
										</td>
									</tr>
									<tr>
										<td>
										<TABLE WIDTH='200' BORDER='0' CELLPADDING='0' CELLSPACING='0' align="center">
											<TR>
												<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
											</TR>
											<TR>
												<TD align='center' height="90" background="images/sms_bg.gif" valign="top">
													<input type='hidden' name='mem_msg[<?=$msg_i?>]' value='<?=$msgVal['content']?>' >
													<div class='div_hide' >
														<?=$msgVal['content']?>
													<div>
												</TD>
											</TR>
											<TR>
												<TD align='center' height="26" background="images/sms_down_01.gif">
													<INPUT style="PADDING-RIGHT:5px; WIDTH:40px; TEXT-ALIGN:right" onfocus='this.blur();' value='0' name='len_mem_msg[<?=$msg_i?>]' size="3" class="input_hide"> bytes (최대2000 bytes)
												</TD>
											</TR>
										</TABLE>
									</tr>
								</table>
							</td>
<?php
		if( $msgKey == count( $msg_arr ) - 1 ) echo '</tr>';
		else if( ( $msg_cnt % 5 ) == 0 ) echo '</tr>';
		$msg_cnt++;
	}
?>
						<tr>
							<td height="20" colspan="5"></td>
						</tr>
						<tr>
							<td colspan='5'>
								<div id='page_navi01' >
									<div class="page_navi">
										<ul>
											<?=$paging->a_preve_page.$paging->print_page.$paging->a_next_page?>
										</ul>
									</div>
								</div>
							</td>
						</tr>
					</table>
					</div>
<?php
}
?>
				</td>
				</form>
			</tr>

			<tr>
				<td height="20"></td>
			</tr>

			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>SMS 발송</span></dt>
							<dd>
								- SMS 문자메세지 보내기는 유료서비스 입니다. SMS머니를 먼저 충전 후 사용 가능합니다.<br>
								- SMS 문자메세지는 1회 최대 80Byte, LMS/MMS는 최대 2000Byte 발송 가능합니다.<br>
								- 휴대폰 번호를 입력한 회원에게만 발송이 됩니다.<br>
								- 네트워크 지연, 통신사 사정에 의해 발송시간이 다소 지연될 수 있으니 시간을 고려하여 발송하시기 바랍니다.(1초당 5건 발송)<br>
								- &quot;SMS 발송&quot; 버튼을 누르시고 발송완료 되었다는 메세지가 나올때까지 기다려주시기 바랍니다.
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
<form name='modify_frm' id='modify_frm' method='POST' >
	<input type='hidden' name='modify_idx' id='modify_idx' value='' >
	<input type='hidden' name='modify_mode' id='modify_mode' value='' >
</form>
<form name='delete_frm' id='delete_frm' method='POST' action='smssend_msg_indb.php'>
	<input type='hidden' name='delete_idx' id='delete_idx' value='' >
	<input type='hidden' name='msg_mode' id='delete_mode' value='' >
</form>
<script>

function GoPage(block,gotopage) {
	document.msg_frm.block.value = block;
	document.msg_frm.gotopage.value = gotopage;
	document.msg_frm.submit();
}

function msg_insert(){
	var msg_pop_url = 'sms_msg_insert.php';
	var msg_pop_target = 'msg_insert_pop';
	var msg_pop_option = 'width=500,height=500,fullscreen=no';
	msg_insert_pop = window.open( msg_pop_url, msg_pop_target, msg_pop_option );
}

$(document).ready( function () {
	//cal_pre2('massage_1',false);
	$('input[name^="mem_msg"]').each( function ( msg_idx, msg_obj ){
		$('input[name^="len_mem_msg"]').eq( msg_idx ).val( cal_byte2( $(this).val() ) );
	});
});
/*
$(document).on( 'keyup', 'textarea[name^="mem_msg"]', function( event ) {
	var msg_idx = $('textarea[name^="mem_msg"]').index( $(this) );
	$('input[name^="len_mem_msg"]').eq( msg_idx ).val( cal_byte2( $(this).val() ) );
});
*/

$(document).on( 'click', 'table[name="msg_table"]', function( event ){
	var msg_idx = $('table[name="msg_table"]').index( $(this) );
	var msg     = $('input[name^="mem_msg"]').eq( msg_idx ).val();
	$('textarea[name="msg"]').val( msg );
	$('textarea[name="msg"]').trigger('keyup');
});

function msg_modify( idx ){
	var msg_pop_url = 'sms_msg_insert.php';
	var msg_pop_target = 'msg_modify_pop';
	var msg_pop_option = 'width=500,height=500,fullscreen=no';
	$('#modify_idx').val( idx );
	$('#modify_mode').val('modify');
	$('#modify_frm').attr( 'target', msg_pop_target );
	$('#modify_frm').attr( 'action', msg_pop_url );
	msg_insert_pop = window.open( '', msg_pop_target, msg_pop_option );
	$('#modify_frm').submit();
	
}

function msg_delete( idx ){
	if( confirm('메세지를 삭제 하시겠습니까?') ){
		$('#delete_idx').val( idx );
		$('#delete_mode').val( 'msg_delete' );
		$('#delete_frm').submit();
	}
}

</script>
<?=$onload?>
<?php 
include("copyright.php");
