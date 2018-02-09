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

$shopname=$_shopdata->shopname;

$msg_list = array(
					"mem_join"=>strip_tags($shopname)."
dear. [NAME]
welcome to join us★
#스타가되고싶니
#PLAYTHESTAR",
					"mem_orderok"=>strip_tags($shopname)."★ [NAME]님의 주문번호:[ORDERID] 주문완료되었습니다. 감사합니다.",
					"mem_order"=>strip_tags($shopname)." [NAME]님의 주문번호:[ORDERID] 주문접수 완료되었습니다.",
					"mem_bank"=>strip_tags($shopname)."★ 입금계좌안내 [BANK] [ACCOUNT] [DEPOSITOR] [PRICE]원-",
					"mem_bankok"=>strip_tags($shopname)."★ [NAME]님의 [ORDERID] 입금확인되었습니다. 감사합니다.",
					"mem_delivery"=>strip_tags($shopname)."★ [NAME]님 [DELICOM] [DELINUM] 발송하였습니다.",
					"mem_delinum"=>strip_tags($shopname)."★ [NAME]님의 [DELICOM] [DELINUM] 부분발송되었습니다.",
					"mem_cancel"=>strip_tags($shopname)."★ [NAME]님 요청하신 취소/반품/교환처리가 접수완료되었습니다.",
					"mem_refund"=>strip_tags($shopname)."★ [NAME]님 주문번호:[ORDERID] 환불 처리가 완료되었습니다.",
					"mem_passwd"=>strip_tags($shopname)."★ [NAME]님 임시비밀번호 [PW] 입니다.",
					"mem_out"=>strip_tags($shopname)."★ [NAME]님, [DATE] 회원탈퇴가 정상 처리되었습니다.",
					"mem_qna"=>strip_tags($shopname)."★ [NAME]님의 문의에 대한 답변이 등록되었습니다. [MY PAGE] 상품 Q&A에서 확인해주세요.",
					"mem_personal"=>strip_tags($shopname)."★ [NAME]님의 문의에 대한 답변이 등록되었습니다. [MY PAGE] 상품 1:1문의에서 확인해주세요.",
					"mem_birth"=>strip_tags($shopname)."★ [NAME]님 Happy Birthday !
생일쿠폰이 발급되었습니다. ",
					"mem_modify"=>strip_tags($shopname)."★ [NAME]님의 개인정보([PART])가 변경되었습니다."
					);

$type=$_POST["type"];
$sms_id=$_POST["sms_id"];
$sms_authkey=$_POST["sms_authkey"];
$sms_uname=$_POST["sms_uname"];
$return_tel1=$_POST["return_tel1"];
$return_tel2=$_POST["return_tel2"];
$return_tel3=$_POST["return_tel3"];
if(ord($return_tel1) && ord($return_tel2) && ord($return_tel3)) {
	$return_tel=$return_tel1."-{$return_tel2}-".$return_tel3;
}
$admin_tel1=$_POST["admin_tel1"];
$admin_tel2=$_POST["admin_tel2"];
$admin_tel3=$_POST["admin_tel3"];
if(ord($admin_tel1) && ord($admin_tel2) && ord($admin_tel3)) {
	$admin_tel=$admin_tel1."-{$admin_tel2}-".$admin_tel3;
}
$subadmin1_tel1=$_POST["subadmin1_tel1"];
$subadmin1_tel2=$_POST["subadmin1_tel2"];
$subadmin1_tel3=$_POST["subadmin1_tel3"];
if(ord($subadmin1_tel1) && ord($subadmin1_tel2) && ord($subadmin1_tel3)) {
	$subadmin1_tel=$subadmin1_tel1."-{$subadmin1_tel2}-".$subadmin1_tel3;
}
$subadmin2_tel1=$_POST["subadmin2_tel1"];
$subadmin2_tel2=$_POST["subadmin2_tel2"];
$subadmin2_tel3=$_POST["subadmin2_tel3"];
if(ord($subadmin2_tel1) && ord($subadmin2_tel2) && ord($subadmin2_tel3)) {
	$subadmin2_tel=$subadmin2_tel1."-{$subadmin2_tel2}-".$subadmin2_tel3;
}
$subadmin3_tel1=$_POST["subadmin3_tel1"];
$subadmin3_tel2=$_POST["subadmin3_tel2"];
$subadmin3_tel3=$_POST["subadmin3_tel3"];
if(ord($subadmin3_tel1) && ord($subadmin3_tel2) && ord($subadmin3_tel3)) {
	$subadmin3_tel=$subadmin3_tel1."-{$subadmin3_tel2}-".$subadmin3_tel3;
}
$check_sleep_time=$_POST["check_sleep_time"];
$sleep_time1=$_POST["sleep_time1"];
$sleep_time2=$_POST["sleep_time2"];

//SMS 수신용 기본메시지
$mem_join=(ord($_POST["mem_join"])?$_POST["mem_join"]:"N");
$mem_orderok=(ord($_POST["mem_orderok"])?$_POST["mem_orderok"]:"N");
$mem_order=(ord($_POST["mem_order"])?$_POST["mem_order"]:"N");
$mem_bank=(ord($_POST["mem_bank"])?$_POST["mem_bank"]:"N");
$mem_bankok=(ord($_POST["mem_bankok"])?$_POST["mem_bankok"]:"N");
$mem_delivery=(ord($_POST["mem_delivery"])?$_POST["mem_delivery"]:"N");
$mem_delinum=(ord($_POST["mem_delinum"])?$_POST["mem_delinum"]:"N");
$mem_cancel=(ord($_POST["mem_cancel"])?$_POST["mem_cancel"]:"N");
$mem_refund=(ord($_POST["mem_refund"])?$_POST["mem_refund"]:"N");
$mem_passwd=(ord($_POST["mem_passwd"])?$_POST["mem_passwd"]:"N");
$mem_out=(ord($_POST["mem_out"])?$_POST["mem_out"]:"N");
$mem_qna=(ord($_POST["mem_qna"])?$_POST["mem_qna"]:"N");
$mem_personal=(ord($_POST["mem_personal"])?$_POST["mem_personal"]:"N");
$mem_birth=(ord($_POST["mem_birth"])?$_POST["mem_birth"]:"N");
$mem_modify=(ord($_POST["mem_modify"])?$_POST["mem_modify"]:"N");

$msg_mem_join=$_POST["msg_mem_join"];
$msg_mem_orderok=$_POST["msg_mem_orderok"];
$msg_mem_order=$_POST["msg_mem_order"];
$msg_mem_bank=$_POST["msg_mem_bank"];
$msg_mem_bankok=$_POST["msg_mem_bankok"];
$msg_mem_delivery=$_POST["msg_mem_delivery"];
$msg_mem_delinum=$_POST["msg_mem_delinum"];
$msg_mem_cancel=$_POST["msg_mem_cancel"];
$msg_mem_refund=$_POST["msg_mem_refund"];
$msg_mem_passwd=$_POST["msg_mem_passwd"];
$msg_mem_out=$_POST["msg_mem_out"];
$msg_mem_qna=$_POST["msg_mem_qna"];
$msg_mem_personal=$_POST["msg_mem_personal"];
$msg_mem_birth=$_POST["msg_mem_birth"];
$msg_mem_modify=$_POST["msg_mem_modify"];

$admin_join=(ord($_POST["admin_join"])?$_POST["admin_join"]:"N");
$admin_order=(ord($_POST["admin_order"])?$_POST["admin_order"]:"N");
$admin_orderok=(ord($_POST["admin_orderok"])?$_POST["admin_orderok"]:"N");
$admin_bank=(ord($_POST["admin_bank"])?$_POST["admin_bank"]:"N");
$admin_bankok=(ord($_POST["admin_bankok"])?$_POST["admin_bankok"]:"N");
$admin_delivery=(ord($_POST["admin_delivery"])?$_POST["admin_delivery"]:"N");
$admin_delinum=(ord($_POST["admin_delinum"])?$_POST["admin_delinum"]:"N");
$admin_cancel=(ord($_POST["admin_cancel"])?$_POST["admin_cancel"]:"N");
$admin_refund=(ord($_POST["admin_refund"])?$_POST["admin_refund"]:"N");
$admin_passwd=(ord($_POST["admin_passwd"])?$_POST["admin_passwd"]:"N");
$admin_out=(ord($_POST["admin_out"])?$_POST["admin_out"]:"N");
$admin_qna=(ord($_POST["admin_qna"])?$_POST["admin_qna"]:"N");
$admin_personal=(ord($_POST["admin_personal"])?$_POST["admin_personal"]:"N");
$admin_birth=(ord($_POST["admin_birth"])?$_POST["admin_birth"]:"N");
$admin_modify=(ord($_POST["admin_modify"])?$_POST["admin_modify"]:"N");

$msg_admin_join=$_POST["msg_admin_join"];
$msg_admin_order=$_POST["msg_admin_order"];
$msg_admin_orderok=$_POST["msg_admin_orderok"];
$msg_admin_bank=$_POST["msg_admin_bank"];
$msg_admin_bankok=$_POST["msg_admin_bankok"];
$msg_admin_delivery=$_POST["msg_admin_delivery"];
$msg_admin_delinum=$_POST["msg_admin_delinum"];
$msg_admin_cancel=$_POST["msg_admin_cancel"];
$msg_admin_refund=$_POST["msg_admin_refund"];
$msg_admin_passwd=$_POST["msg_admin_passwd"];
$msg_admin_out=$_POST["msg_admin_out"];
$msg_admin_qna=$_POST["msg_admin_qna"];
$msg_admin_personal=$_POST["msg_admin_personal"];
$msg_admin_birth=$_POST["msg_admin_birth"];
$msg_admin_modify=$_POST["msg_admin_modify"];

if ($type=="update") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################
	
	if(ord($sms_id) && ord($sms_authkey)) {
		$smscountdata=getSmscount($sms_id,$sms_authkey);
		if(substr($smscountdata,0,2)=="OK") {
			$sql = "UPDATE tblsmsinfo SET ";
			$sql.= "id				= '{$sms_id}', ";
			$sql.= "authkey			= '{$sms_authkey}' ";
			pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){ alert('SMS 기본환경 설정이 완료되었습니다.'); }</script>";
		} else {
			if(substr($smscountdata,0,2)=="NO") {
				$onload="<script>window.onload=function(){ alert('SMS 회원 아이디가 존재하지 않습니다.\\n\\nSMS 회원 아이디를 정확히 입력하시기 바랍니다.'); }</script>";
			} else if(substr($smscountdata,0,2)=="AK") {
				$onload="<script>window.onload=function(){ alert('SMS 회원 인증키가 일치하지 않습니다.\\n\\n인증키를 정확히 입력하시기 바랍니다.'); }</script>";
			} else {
				$onload="<script>window.onload=function(){ alert('SMS 서버와 통신이 불가능합니다.\\n\\n잠시 후 이용하시기 바랍니다.'); }</script>";
			}
		}
		$sql = "UPDATE tblsmsinfo SET ";
	} else {
		$sql = "UPDATE tblsmsinfo SET ";
		if(ord($sms_id)) {
			$sql.= "id				= '{$sms_id}', ";
			$sql.= "authkey			= '', ";
		} else {
			$sql.= "id				= '', ";
			$sql.= "authkey			= '{$sms_authkey}', ";
		}
	}

	if ($check_sleep_time=="Y" || ($sleep_time1==$sleep_time2)) {
		$check_sleep_time1=$check_sleep_time2=0;
	} else {
		$check_sleep_time1=$sleep_time2;
		if($sleep_time1==0) $check_sleep_time2=23;
		else $check_sleep_time2=$sleep_time1-1;
	}
	
	$sql.= "sms_uname		= '{$sms_uname}', ";

	$sql.= "mem_join		= '{$mem_join}', ";
	$sql.= "mem_order		= '{$mem_order}', ";
	$sql.= "mem_orderok	= '{$mem_orderok}', ";
	$sql.= "mem_bank		= '{$mem_bank}', ";
	$sql.= "mem_bankok	= '{$mem_bankok}', ";
	$sql.= "mem_delivery	= '{$mem_delivery}', ";
	$sql.= "mem_delinum	= '{$mem_delinum}', ";
	$sql.= "mem_cancel	= '{$mem_cancel}', ";
	$sql.= "mem_refund		= '{$mem_refund}', ";
	$sql.= "mem_passwd	= '{$mem_passwd}', ";
	$sql.= "mem_out			= '{$mem_out}', ";
	$sql.= "mem_qna		= '{$mem_qna}', ";
	$sql.= "mem_personal	= '{$mem_personal}', ";
	$sql.= "mem_birth		= '{$mem_birth}', ";
	$sql.= "mem_modify	= '{$mem_modify}', ";

	$sql.= "msg_mem_join	= '{$msg_mem_join}', ";
	$sql.= "msg_mem_order	= '{$msg_mem_order}', ";
	$sql.= "msg_mem_orderok	= '{$msg_mem_orderok}', ";
	$sql.= "msg_mem_bank	= '{$msg_mem_bank}', ";
	$sql.= "msg_mem_bankok	= '{$msg_mem_bankok}', ";
	$sql.= "msg_mem_delivery= '{$msg_mem_delivery}', ";
	$sql.= "msg_mem_delinum	= '{$msg_mem_delinum}', ";
	$sql.= "msg_mem_cancel	= '{$msg_mem_cancel}', ";
	$sql.= "msg_mem_refund	= '{$msg_mem_refund}', ";
	$sql.= "msg_mem_passwd	= '{$msg_mem_passwd}', ";
	$sql.= "msg_mem_out	= '{$msg_mem_out}', ";
	$sql.= "msg_mem_qna	= '{$msg_mem_qna}', ";
	$sql.= "msg_mem_personal	= '{$msg_mem_personal}', ";
	$sql.= "msg_mem_birth	= '{$msg_mem_birth}', ";
	$sql.= "msg_mem_modify	= '{$msg_mem_modify}', ";

	$sql.= "admin_join		= '{$admin_join}', ";
	$sql.= "admin_order		= '{$admin_order}', ";
	$sql.= "admin_orderok	= '{$admin_orderok}', ";
	$sql.= "admin_bank		= '{$admin_bank}', ";
	$sql.= "admin_bankok	= '{$admin_bankok}', ";
	$sql.= "admin_delivery	= '{$admin_delivery}', ";
	$sql.= "admin_delinum	= '{$admin_delinum}', ";
	$sql.= "admin_cancel	= '{$admin_cancel}', ";
	$sql.= "admin_refund		= '{$admin_refund}', ";
	$sql.= "admin_passwd	= '{$admin_passwd}', ";
	$sql.= "admin_out			= '{$admin_out}', ";
	$sql.= "admin_qna		= '{$admin_qna}', ";
	$sql.= "admin_personal	= '{$admin_personal}', ";
	$sql.= "admin_birth		= '{$admin_birth}', ";
	$sql.= "admin_modify	= '{$admin_modify}', ";

	$sql.= "msg_admin_join	= '{$msg_admin_join}', ";
	$sql.= "msg_admin_order	= '{$msg_admin_order}', ";
	$sql.= "msg_admin_orderok	= '{$msg_admin_orderok}', ";
	$sql.= "msg_admin_bank	= '{$msg_admin_bank}', ";
	$sql.= "msg_admin_bankok	= '{$msg_admin_bankok}', ";
	$sql.= "msg_admin_delivery= '{$msg_admin_delivery}', ";
	$sql.= "msg_admin_delinum	= '{$msg_admin_delinum}', ";
	$sql.= "msg_admin_cancel	= '{$msg_admin_cancel}', ";
	$sql.= "msg_admin_refund	= '{$msg_admin_refund}', ";
	$sql.= "msg_admin_passwd	= '{$msg_admin_passwd}', ";
	$sql.= "msg_admin_out	= '{$msg_admin_out}', ";
	$sql.= "msg_admin_qna	= '{$msg_admin_qna}', ";
	$sql.= "msg_admin_personal	= '{$msg_admin_personal}', ";
	$sql.= "msg_admin_birth	= '{$msg_admin_birth}', ";
	$sql.= "msg_admin_modify	= '{$msg_admin_modify}', ";


	$sql.= "admin_tel		= '{$admin_tel}', ";
	$sql.= "subadmin1_tel	= '{$subadmin1_tel}', ";
	$sql.= "subadmin2_tel	= '{$subadmin2_tel}', ";
	$sql.= "subadmin3_tel	= '{$subadmin3_tel}', ";
	$sql.= "sleep_time1		= '{$check_sleep_time1}', ";
	$sql.= "sleep_time2		= '{$check_sleep_time2}', ";
	$sql.= "return_tel		= '{$return_tel}' ";
	//echo $sql;
	pmysql_query($sql,get_db_conn());

	$onload="<script>window.onload=function(){ alert('SMS 기본환경 설정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT * FROM tblsmsinfo ";
$result=pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$sms_id = $row->id;
	$sms_authkey = $row->authkey;
	$admin_tel = explode("-", $row->admin_tel);
	$subadmin1_tel = explode("-", $row->subadmin1_tel);
	$subadmin2_tel = explode("-", $row->subadmin2_tel);
	$subadmin3_tel = explode("-", $row->subadmin3_tel);
	$return_tel = explode("-",$row->return_tel);
	if(ord($row->msg_mem_join)==0) $row->msg_mem_join=$msg_list['mem_join'];
	if(ord($row->msg_mem_order)==0) $row->msg_mem_order=$msg_list['mem_order'];
	if(ord($row->msg_mem_orderok)==0) $row->msg_mem_orderok=$msg_list['mem_orderok'];
	if(ord($row->msg_mem_bank)==0) $row->msg_mem_bank=$msg_list['mem_bank'];
	if(ord($row->msg_mem_bankok)==0) $row->msg_mem_bankok=$msg_list['mem_bankok'];
	if(ord($row->msg_mem_delivery)==0) $row->msg_mem_delivery=$msg_list['mem_delivery'];
	if(ord($row->msg_mem_delinum)==0) $row->msg_mem_delinum=$msg_list['mem_delinum'];
	if(ord($row->msg_mem_cancel)==0) $row->msg_mem_cancel=$msg_list['mem_cancel'];
	if(ord($row->msg_mem_refund)==0) $row->msg_mem_refund=$msg_list['mem_refund'];
	if(ord($row->msg_mem_passwd)==0) $row->msg_mem_passwd=$msg_list['mem_passwd'];
	if(ord($row->msg_mem_out)==0) $row->msg_mem_out=$msg_list['mem_out'];
	if(ord($row->msg_mem_qna)==0) $row->msg_mem_qna=$msg_list['mem_qna'];
	if(ord($row->msg_mem_personal)==0) $row->msg_mem_personal=$msg_list['mem_personal'];
	if(ord($row->msg_mem_birth)==0) $row->msg_mem_birth=$msg_list['mem_birth'];
	if(ord($row->msg_mem_modify)==0) $row->msg_mem_modify=$msg_list['mem_modify'];
	$sleep_time1=$row->sleep_time2;
	$sleep_time2=$row->sleep_time1;
} else {
	$sql = "INSERT INTO tblsmsinfo (sms_uname) VALUES ('{$_shopdata->shopname}')";
	$result=pmysql_query($sql,get_db_conn());
}

if ($sleep_time1==0 && $sleep_time2==0) {
	$check_sleep_time="Y";
	$sleep_time1=$sleep_time2=0;
} else {
	$check_sleep_time="N";
	if($sleep_time1==23) $sleep_time1=0;
	else $sleep_time1=$sleep_time1+1;
}

?>

<?php include("header.php"); ?>

<style type="text/css">
<!--
TEXTAREA {  clip:   rect(   ); overflow: hidden; background-image:url('');font-family:굴림;}
.phone {  font-family:굴림; height: 80px; width: 173px;color: #191919;  FONT-SIZE: 9pt; font-style: normal; background-color: #A8E4ED;; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px}
-->
</style>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["return_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["return_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["admin_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["admin_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["subadmin1_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["subadmin1_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["subadmin2_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["subadmin2_tel"+i].focus();
			break; return;
		}
	}
	for(i=1;i<=3;i++) {
		if(!IsNumeric(document.form1["subadmin3_tel"+i].value)) {
			alert("숫자만 입력하세요.");
			document.form1["subadmin3_tel"+i].focus();
			break; return;
		}
	}
	document.form1.type.value="update";
	document.form1.submit();
}
function CheckSleepTime(disabled) {
	document.form1.sleep_time1.disabled=disabled;
	document.form1.sleep_time2.disabled=disabled;
}
function cal_pre2(field,ismsg) {
	var strcnt,obj_msg,obj_len;
	var reserve=0;

	obj_msg = document.form1["msg_"+field];
	obj_len = document.form1["len_"+field];

	strcnt = cal_byte2(obj_msg.value);

	if(strcnt > 80)	{
		/*
		reserve = strcnt - 80;
		if(ismsg) {
			alert('메시지 내용은 80바이트를 넘을수 없습니다.\n\n작성하신 메세지 내용은 '+ reserve +'byte가 초과되었습니다.\n\n초과된 부분은 자동으로 삭제됩니다.');
		}
		obj_msg.value = nets_check2(obj_msg.value);
		strcnt = cal_byte2(obj_msg.value);
		obj_len.value=strcnt;
		return;
		*/
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
	
	for(k=0;k<temp;k++)	{
		onechar = tmpStr.charAt(k);
	
		if(escape(onechar).length > 4) {
			tcount += 2;
		} else {
			tcount++;
		}
	
		if(tcount > 80) {
			tmpStr = tmpStr.substring(0,k);
			break;
		}
	}
	return tmpStr;
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; SMS 발송/관리 &gt;<span>SMS 기본환경 설정</span></p></div></div>
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

			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">SMS기본환경 설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>SMS 문자서비스 기본환경과 설정메뉴를 관리할 수 있습니다.</span></div>
				</td>
			</tr>
            <tr>
            	<td><div class="title_depth3_sub">SMS 기본정보 설정</div></td>
            </tr>
            <tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) <b><span class="font_orange">SMS 서비스는 유료 서비스로서 머니를 충전하셔야만 이용이 가능합니다.</span></b></li>
                            <li>2) 회신 전화번호는 SMS 발송시 회신전화번호로 찍히는 번호이니 관리자 휴대폰 번호를 입력하시기를 권장합니다.</li>
                            <li>3) 관리자 휴대폰 번호는 관리자에게 SMS 발송시 필요함으로 입력해 주세요.</li>
                            <li>4) <b>부운영자 휴대폰 번호를 입력하시면 관리자에게 SMS 발송시 동시에 발송이 됩니다.</b></li>
                            <li>5) SMS 임시중단 적용시 해당 시간동안 SMS는 발송이 안되며, 발송이 안되었던 메세지들은 임시중단이 종료된 후 일괄 발송됩니다.</li>
                        </ul>
                    </div>                    
            	</td>
            </tr>			
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>SMS 아이디</span></th>
					<TD><INPUT maxLength=20 size=40 name=sms_id value="<?=$row->id?>" class="input" style=width:30%>&nbsp;<span class="font_orange">＊SMS 가입시 신청하신 아이디를 입력하세요.</span></TD>
				</TR>
				<TR>
					<th><span>SMS 인증키</span></th>
					<TD><INPUT maxLength=33 size=40 name=sms_authkey value="<?=$row->authkey?>" class="input" style=width:30%>&nbsp;<span class="font_orange">＊SMS 회원 인증키를 정확히 입력하세요.</span></TD>
				</TR>
				<TR>
					<th><span>쇼핑몰 상점명</span></th>
					<TD><INPUT maxLength=20 size=40 name=sms_uname value="<?=$row->sms_uname?>" class="input" style=width:30%></TD>
				</TR>
				<TR>
					<th><span>회신 전화번호</span></th>
					<TD>
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=return_tel1 value="<?=$return_tel[0]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=return_tel2 value="<?=$return_tel[1]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=return_tel3 value="<?=$return_tel[2]?>" class="input">&nbsp;<span class="font_orange">＊SMS 발송시 <B>기본 회신번호</B>로 지정됩니다.</span>
					</TD>
				</TR>
				<TR>
					<th><span>관리자 휴대폰 번호</span></th>
					<TD>
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=admin_tel1 value="<?=$admin_tel[0]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=admin_tel2 value="<?=$admin_tel[1]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=admin_tel3 value="<?=$admin_tel[2]?>" class="input">
					</TD>
				</TR>
				<tr>
					<th><span>부운영자1 휴대폰 번호</span></th>
					<TD>
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=subadmin1_tel1 value="<?=$subadmin1_tel[0]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin1_tel2 value="<?=$subadmin1_tel[1]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin1_tel3 value="<?=$subadmin1_tel[2]?>" class="input">
					</TD>
				</tr>
				<tr>
					<th><span>부운영자2 휴대폰 번호</span></th>
					<TD>
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=subadmin2_tel1 value="<?=$subadmin2_tel[0]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin2_tel2 value="<?=$subadmin2_tel[1]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin2_tel3 value="<?=$subadmin2_tel[2]?>" class="input">
					</TD>
				</tr>
				<tr>
					<th><span>부운영자3 휴대폰 번호</span></th>
					<TD>
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=3 size=5 name=subadmin3_tel1 value="<?=$subadmin3_tel[0]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin3_tel2 value="<?=$subadmin3_tel[1]?>" class="input"> - 
					<INPUT onkeyup="return strnumkeyup(this);" maxLength=4 size=5 name=subadmin3_tel3 value="<?=$subadmin3_tel[2]?>" class="input">
					</TD>
				</tr>
				<tr>
					<th><span>SMS 임시중단</span></th>
					<TD>
					<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" onclick=CheckSleepTime(true) type=radio value=Y name=check_sleep_time <?=($check_sleep_time=="Y"?"checked":"")?>>적용안함  &nbsp;&nbsp;
					<INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" onclick=CheckSleepTime(false) type=radio value=N name=check_sleep_time <?=($check_sleep_time=="N"?"checked":"")?>>적용
					<SELECT name=sleep_time1 class="select" style=width:70px>
<?php
					for($i=0;$i<24;$i++){
						echo "<option value='{$i}'";
						if($i==$sleep_time1) echo " selected";
						echo ">".($i>12?"pm":"am")." ".sprintf("%02d",$i)."</option>";
					}
?>
					</SELECT>
					시 부터  
					<SELECT name=sleep_time2 class="select"  style=width:70px>
<?php
					for($i=0;$i<24;$i++){
						echo "<option value='{$i}'";
						if($i==$sleep_time2) echo " selected";
						echo ">".($i>12?"pm":"am")." ".sprintf("%02d",$i)."</option>";
					}
?>
					</SELECT>
					시 까지
					<?php if($check_sleep_time=="Y")echo"<script>CheckSleepTime(true);</script>\n"; ?>
					</TD>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">SMS 회원 수신용 기본메세지 설정</div>
				</td>
			</tr>
			<tr>
				<td height='30'></td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" style='border-left: solid #000000; border-top: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="">
							<tr>
								<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_join <?php if($row->mem_join=="Y") echo "checked"?>>회원가입 축하메세지</td>
							</tr>
							<tr>
								<td>
								<TABLE WIDTH= BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
								<TR>
									<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
								</TR>
								<TR>
									<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_join',true);" name=msg_mem_join rows=5 cols="26" onchange="cal_pre2('mem_join',true);"><?=$row->msg_mem_join?></TEXTAREA></TD>
								</TR>
								<TR>
									<TD align=center height="26" background="images/sms_down_01.gif">
									<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_join size="3" class="input_hide">bytes (최대2000 bytes)
									<SCRIPT>cal_pre2('mem_join',false);</SCRIPT>
									</TD>
								</TR>
								</TABLE>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000; border-top: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
							<tr>
								<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name='admin_join' <?php if($row->admin_join=="Y") echo "checked"?>>회원가입 (관리자)</td>
							</tr>
							<tr>
								<td>
								<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
								<TR>
									<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
								</TR>
								<TR>
									<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_join',true);" name='msg_admin_join' rows=5 cols="26" onchange="cal_pre2('admin_join',true);"><?=$row->msg_admin_join?></TEXTAREA></TD>
								</TR>
								<TR>
									<TD align=center height="26" background="images/sms_down_01.gif">
									<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_join size="3" class="input_hide">bytes (최대2000 bytes)
									<SCRIPT>cal_pre2('admin_join',false);</SCRIPT>
									</TD>
								</TR>
								</TABLE>
								</td>
							</tr>
						</table>
					</td>

					<td valign="top" style='border-top: solid #000000; ' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_orderok <?php if($row->mem_orderok=="Y") echo "checked"?>>상품주문완료 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_orderok',true);" name=msg_mem_orderok rows=5 cols="26" onchange="cal_pre2('mem_orderok',true);"><?=$row->msg_mem_orderok?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_orderok size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_orderok',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000; border-top: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_orderok <?php if($row->admin_orderok=="Y") echo "checked"?>>상품주문완료 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_orderok',true);" name=msg_admin_orderok rows=5 cols="26" onchange="cal_pre2('admin_orderok',true);"><?=$row->msg_admin_orderok?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_orderok size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_orderok',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

					<td valign="top" style='border-top: solid #000000; ' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_order <?php if($row->mem_order=="Y") echo "checked"?>>상품주문접수 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_order',true);" name=msg_mem_order rows=5 cols="26" onchange="cal_pre2('mem_order',true);"><?=$row->msg_mem_order?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_order size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_order',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-top: solid #000000; border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_order <?php if($row->admin_order=="Y") echo "checked"?>>상품주문접수 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_order',true);" name=msg_admin_order rows=5 cols="26" onchange="cal_pre2('admin_order',true);"><?=$row->msg_admin_order?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_order size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_order',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

				</tr>
				<tr>
					<td valign="top"  style='border-bottom: solid #000000; border-left: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" style='border-left: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_bank <?php if($row->mem_bank=="Y") echo "checked"?>>입금계좌 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_bank',true);" name=msg_mem_bank rows=5 cols="26" onchange="cal_pre2('mem_bank',true);"><?=$row->msg_mem_bank?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_bank size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_bank',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td class="font_blue1">* 계좌번호는 상품주문시, 고객이 선택한<br>&nbsp; 계좌번호 안내</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_bank <?php if($row->admin_bank=="Y") echo "checked"?>>입금계좌 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_bank',true);" name=msg_admin_bank rows=5 cols="26" onchange="cal_pre2('admin_bank',true);"><?=$row->msg_admin_bank?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_bank size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_bank',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						<tr>
							<td class="font_blue1">* 계좌번호는 상품주문시, 고객이 선택한<br>&nbsp; 계좌번호 안내</td>
						</tr>
						</table>
					</td>

					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_bankok <?php if($row->mem_bankok=="Y") echo "checked"?>>입금및주문완료 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_bankok',true);" name=msg_mem_bankok rows=5 cols="26" onchange="cal_pre2('mem_bankok',true);"><?=$row->msg_mem_bankok?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_bankok size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_bankok',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_bankok <?php if($row->admin_bankok=="Y") echo "checked"?>>입금및주문완료 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_bankok',true);" name=msg_admin_bankok rows=5 cols="26" onchange="cal_pre2('admin_bankok',true);"><?=$row->msg_admin_bankok?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_bankok size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_bankok',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>


					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_delivery <?php if($row->mem_delivery=="Y") echo "checked"?>>운송장(완전배송) 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_delivery',true);" name=msg_mem_delivery rows=5 cols="26" onchange="cal_pre2('mem_delivery',true);"><?=$row->msg_mem_delivery?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_delivery size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_delivery',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_delivery <?php if($row->admin_delivery=="Y") echo "checked"?>>운송장(완전배송) (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_delivery',true);" name=msg_admin_delivery rows=5 cols="26" onchange="cal_pre2('admin_delivery',true);"><?=$row->msg_admin_delivery?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_delivery size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_delivery',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

				</tr>
				<tr>
					<td valign="top"  style='border-bottom: solid #000000; border-left: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" style='border-left: solid #000000;'>
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_delinum <?php if($row->mem_delinum=="Y") echo "checked"?>>운송장(부분배송) 안내메세지</td>
						</tr>
						<tr>
						<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_delinum',true);" name=msg_mem_delinum rows=5 cols="26" onchange="cal_pre2('mem_delinum',true);"><?=$row->msg_mem_delinum?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_delinum size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_delinum',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
						</td>
						</tr>
						<tr>
						<td class="font_blue1">* 택배회사/송장번호는 상품발송시 내용을<br>&nbsp;&nbsp;자동 안내</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_delinum <?php if($row->admin_delinum=="Y") echo "checked"?>>운송장(부분배송) (관리자)</td>
						</tr>
						<tr>
						<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_delinum',true);" name=msg_admin_delinum rows=5 cols="26" onchange="cal_pre2('admin_delinum',true);"><?=$row->msg_admin_delinum?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_delinum size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_delinum',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
						</td>
						</tr>
						<tr>
						<td class="font_blue1">* 택배회사/송장번호는 상품발송시 내용을<br>&nbsp;&nbsp;자동 안내</td>
						</tr>
						</table>
					</td>

					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_cancel <?php if($row->mem_cancel=="Y") echo "checked"?>>취소/반품/교환 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_cancel',true);" name=msg_mem_cancel rows=5 cols="26" onchange="cal_pre2('mem_cancel',true);"><?=$row->msg_mem_cancel?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_cancel size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_cancel',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_cancel <?php if($row->admin_cancel=="Y") echo "checked"?>>취소/반품/교환 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_cancel',true);" name=msg_admin_cancel rows=5 cols="26" onchange="cal_pre2('admin_cancel',true);"><?=$row->msg_admin_cancel?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_cancel size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_cancel',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_refund <?php if($row->mem_refund=="Y") echo "checked"?>>환불 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_refund',true);" name=msg_mem_refund rows=5 cols="26" onchange="cal_pre2('mem_refund',true);"><?=$row->msg_mem_refund?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_refund size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_refund',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;'>
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_refund <?php if($row->admin_refund=="Y") echo "checked"?>>환불 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_refund',true);" name=msg_admin_refund rows=5 cols="26" onchange="cal_pre2('admin_refund',true);"><?=$row->msg_admin_refund?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_refund size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_refund',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

				</tr>
				<tr>
					<td valign="top"  style='border-bottom: solid #000000; border-left: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" style='border-left: solid #000000;'>
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_passwd <?php if($row->mem_passwd=="Y") echo "checked"?>>임시비밀번호 안내메세지</td>
						</tr>
						<tr>
						<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_passwd',true);" name=msg_mem_passwd rows=5 cols="26" onchange="cal_pre2('mem_passwd',true);"><?=$row->msg_mem_passwd?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_passwd size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_passwd',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
						</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
						<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_passwd <?php if($row->admin_passwd=="Y") echo "checked"?>>임시비밀번호 (관리자)</td>
						</tr>
						<tr>
						<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_passwd',true);" name=msg_admin_passwd rows=5 cols="26" onchange="cal_pre2('admin_passwd',true);"><?=$row->msg_admin_passwd?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_passwd size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_passwd',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
						</td>
						</tr>
						</table>
					</td>

					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_out <?php if($row->mem_out=="Y") echo "checked"?>>회원탈퇴 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_out',true);" name=msg_mem_out rows=5 cols="26" onchange="cal_pre2('mem_out',true);"><?=$row->msg_mem_out?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_out size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_out',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_out <?php if($row->admin_out=="Y") echo "checked"?>>회원탈퇴 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_out',true);" name=msg_admin_out rows=5 cols="26" onchange="cal_pre2('admin_out',true);"><?=$row->msg_admin_out?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_out size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_out',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_qna <?php if($row->mem_qna=="Y") echo "checked"?>>문의(상품문의) 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_qna',true);" name=msg_mem_qna rows=5 cols="26" onchange="cal_pre2('mem_qna',true);"><?=$row->msg_mem_qna?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_qna size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_qna',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;'>
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_qna <?php if($row->admin_qna=="Y") echo "checked"?>>문의(상품문의) (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif"ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_qna',true);" name=msg_admin_qna rows=5 cols="26" onchange="cal_pre2('admin_qna',true);"><?=$row->msg_admin_qna?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_qna size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_qna',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>

				</tr>
				<tr>
					<td valign="top"  style='border-bottom: solid #000000; border-left: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
				</tr>
				<tr>
					<td valign="top" style='border-left: solid #000000;'>
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_personal <?php if($row->mem_personal=="Y") echo "checked"?>>문의(1:1) 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_personal',true);" name=msg_mem_personal rows=5 cols="26" onchange="cal_pre2('mem_personal',true);"><?=$row->msg_mem_personal?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_personal size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_personal',false);</SCRIPT></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_personal <?php if($row->admin_personal=="Y") echo "checked"?>>문의(1:1) (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_personal',true);" name=msg_admin_personal rows=5 cols="26" onchange="cal_pre2('admin_personal',true);"><?=$row->msg_admin_personal?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_personal size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_personal',false);</SCRIPT></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>


					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_birth <?php if($row->mem_birth=="Y") echo "checked"?>>생일 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_birth',true);" name=msg_mem_birth rows=5 cols="26" onchange="cal_pre2('mem_birth',true);"><?=$row->msg_mem_birth?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_birth size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_birth',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;' >
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_birth <?php if($row->admin_birth=="Y") echo "checked"?>>생일 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_birth',true);" name=msg_admin_birth rows=5 cols="26" onchange="cal_pre2('admin_birth',true);"><?=$row->msg_admin_birth?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_birth size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_birth',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>


					<td valign="top">
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=mem_modify <?php if($row->mem_modify=="Y") echo "checked"?>>정보변경 안내메세지</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('mem_modify',true);" name=msg_mem_modify rows=5 cols="26" onchange="cal_pre2('mem_modify',true);"><?=$msg_list['mem_modify']?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_mem_modify size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('mem_modify',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					<td valign="top" style='border-right: solid #000000;'>
						<table align="center" cellpadding="0" cellspacing="0" width="200">
						<tr>
							<td height="23"><INPUT style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none" type=checkbox value=Y name=admin_modify <?php if($row->admin_modify=="Y") echo "checked"?>>정보변경 (관리자)</td>
						</tr>
						<tr>
							<td>
							<TABLE WIDTH=200 BORDER=0 CELLPADDING=0 CELLSPACING=0 align="center">
							<TR>
								<TD><IMG SRC="images/sms_top_01.gif" ALT=""></TD>
							</TR>
							<TR>
								<TD align=center height="90" background="images/sms_bg.gif" valign="top"><TEXTAREA class="textarea_hide" onkeyup="cal_pre2('admin_modify',true);" name=msg_admin_modify rows=5 cols="26" onchange="cal_pre2('admin_modify',true);"><?=$row->msg_admin_modify?></TEXTAREA></TD>
							</TR>
							<TR>
								<TD align=center height="26" background="images/sms_down_01.gif">
								<INPUT style="PADDING-RIGHT:5px; WIDTH:20px; TEXT-ALIGN:right" onfocus=this.blur(); value=0 name=len_admin_modify size="3" class="input_hide">bytes (최대2000 bytes)
								<SCRIPT>cal_pre2('admin_modify',false);</SCRIPT>
								</TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
					</td>
					
				</tr>
				<tr>
					<td valign="top"  style='border-bottom: solid #000000; border-left: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;' >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000;'  >&nbsp;</td>
					<td valign="top"  style='border-bottom: solid #000000; border-right: solid #000000;'  >&nbsp;</td>
				</tr>
				</table>
				</td>
			</tr>
            <tr><td height="30"></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
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
							<dt><span>SMS 기본 환경 설정</span></dt>
							<dd>- <b><span class="font_orange">SMS는 유료서비스로서 이용전 반드시 충전을 하셔야만 사용이 가능합니다. </b></span><br>
								- SMS 회원 수신용 기본메세지에 체크를 하시면 회원에게 메세지가 자동 발송됩니다.<br>
								- SMS 관리자 수신용 기본메세지에 체크를 하시면 관리자 및 부운영자에게 메세지가 자동 발송됩니다.<br>
								- 메세지는 80byte까지 입력 가능하오니 넘지않도록 주의하시기 바랍니다.<br>
								- 메세지 내용중 매타태크는 메세지 발송시 자동으로 해당 값으로 변경됨으로 충분히 고려하시고 메세지 작성을 하시기 바랍니다.
							</dd>	
						</dl>
						<!-- dl>
							<dt><span>회원가입 축하메세지</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[ID]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 ID로 변경되어 메세지 전송이 됩니다. (예:hong27)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[NAME]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
									</TR>
								</TABLE>
							</dd>
						</dl>
						<dl>
							<dt><span>상품주문 안내메세지</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[PRODUCT]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">주문 상품명으로 변경되어 메세지 전송이 됩니다. (예:구찌스타일 가방LT-3)</TD>							
									</TR>
								</TABLE>
							</dd>
						</dl>
						<dl>
							<dt><span>무통장입금 안내메세지</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[PRICE]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">상품구매금액으로 변경되어 메세지 전송 (예:50,000)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[ACCOUNT]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">상품구매시 회원이 선택한 입금계좌번호로 변경되어 메세지 전송<br>(예:123456-78-901234 예금주:아무개)</TD>
									</TR>
								</TABLE>
							</dd>
						</dl>
						<dl>
							<dt><span>입금확인/상품발송 안내메세지</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DATE]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">해당 월/일로 	변경되어 메세지 전송 (예:04월 25일)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[PRICE]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">상품구매금액으로 변경되어 메세지 전송 (예:50,000)</TD>
									</TR>
								</TABLE>
							</dd>
						</dl>

						<dl>
							<dt><span>송장번호 안내메세지</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DATE]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">해당 월/일로 변경되어 메세지 전송 (예:04월 25일)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[PRICE]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">상품구매금액으로 변경되어 메세지 전송 (예:50,000)</TD>
									</TR>
									<tr>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[DELICOM]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:rgb(222,222,222); border-top-style:solid;" width="100%">택배회사명으로 변경되어 메세지 전송 (예:KGB택배)</TD>
									</tr>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[DELINUM]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">송장번호로 변경되어 메세지 전송 (예:1234-5678-9012)</TD>
									</TR>
								</TABLE>
							</dd>
						</dl>

						<dl>
							<dt><span>생일회원 자동메세지</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[NAME]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1px; border-top-color:silver; border-top-style:solid;" width="100%" height="27">회원 이름으로 변경되어 메세지 전송이 됩니다. (예:홍길동)</TD>
									</TR>
									<TR>
										<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor="#F0F0F0" height="27">[DATE]</TD>
										<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%" height="27">해당 월/일로 변경되어 메세지 전송(예:04월 25일)-주민등록번호의 생일기준으로 데이터 추출</TD>
									</TR>
								</TABLE>
							</dd>
						</dl -->

						
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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
