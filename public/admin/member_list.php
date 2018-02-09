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

$type       = $_POST["type"];
$id         = $_POST["id"];
$ids        = $_POST["ids"];
$email      = $_POST["email"];
$listnum    = $_POST["listnum"] ?: "20";

$search_detail_open	= $_POST['search_detail_open']?$_POST['search_detail_open']:"N";

##################################
$excel_sql = $_POST['excel_sql'];
$_savemoney_start	= $_POST['_savemoney_start'];
$_savemoney_end		= $_POST['_savemoney_end'];
$_act_point_start	= $_POST['_act_point_start'];
$_act_point_end		= $_POST['_act_point_end'];
$_visitcnt_start	= $_POST['_visitcnt_start'];
$_visitcnt_end		= $_POST['_visitcnt_end'];
$_joinus_start		= $_POST['_joinus_start'];
$_joinus_end		= $_POST['_joinus_end'];
$_lastlogin_start	= $_POST['_lastlogin_start'];
$_lastlogin_end		= $_POST['_lastlogin_end'];
$_geder_type		= $_POST['_geder_type'];
$_agefloor			= $_POST['_agefloor'];
$_mailsms_agree		= $_POST['_mailsms_agree'];
$_kakao_agree		= $_POST['_kakao_agree'];
$_mail_agree		= $_POST['_mail_agree'];
$_sms_agree			= $_POST['_sms_agree'] == "" ? "0":$_POST['_sms_agree'];
$_lunar					= $_POST['lunar'];
$_birthday_start	= $_POST['_birthday_start'];
$_birthday_end		= $_POST['_birthday_end'];
$_merryday_start	= $_POST['_merryday_start'];
$_merryday_end		= $_POST['_merryday_end'];
$_sellbuy			= $_POST['_sellbuy'];
$_lastbuy_start		= $_POST['_lastbuy_start'];
$_lastbuy_end		= $_POST['_lastbuy_end'];
$_buy_start				= $_POST['_buy_start'];
$_buy_end				= $_POST['_buy_end'];
$_buypay_start		= $_POST['_buypay_start'];
$_buypay_end		= $_POST['_buypay_end'];
$_buycnt_start		= $_POST['_buycnt_start'];
$_buycnt_end		= $_POST['_buycnt_end'];
$_buyprice_start	= $_POST['_buyprice_start'];
$_buyprice_end		= $_POST['_buyprice_end'];
$_brand				= $_POST['_brand'];
$_brandname         = $_POST['_brandname']; 

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
		/*$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='{$outid}'";
		$result= pmysql_query($sql,get_db_conn());
		$row = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if ($row->cnt==0) {
			$sql = "DELETE FROM tblmember WHERE id = '{$outid}'";
		} else {*/
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
		//}
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		//$sql = "DELETE FROM tblreserve WHERE id='{$outid}'";
		//pmysql_query($sql,get_db_conn());
		//$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblcouponissue WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblmemo WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		//$sql = "DELETE FROM tblrecommendmanager WHERE rec_id='{$outid}'";
		//pmysql_query($sql,get_db_conn());
		//$pmysql_errno += pmysql_errno();
		//$sql = "DELETE FROM tblrecomendlist WHERE id='{$outid}'";
		//pmysql_query($sql,get_db_conn());
		//$pmysql_errno += pmysql_errno();
		$sql = "DELETE FROM tblpersonal WHERE id='{$outid}'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		if($pmysql_errno==0) {
			
			pmysql_query("COMMIT");

			// ERP로 회원정보 전송..2016-12-19
			sendErpMemberInfo($outid,"out");
        } else {
			pmysql_query("ROLLBACK");
		}
	}

	//로그 insert
	$log_content = "## 회원삭제 : ID:".str_replace("|=|",",",$idval)." ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>window.onload=function(){ alert('선택하신 회원 ".count($arr_id)."명을 탈퇴처리 하였습니다.\\n\\n구매내역이 있는 경우에는 회원 기본정보만 삭제됩니다.');}</script>";
} else if ($type=="member_review_auth" && ord($id)) {	//선택 회원 리뷰쓰기 권한 주기
	$idval=rtrim($id,'|=|');
	$arr_id=explode("|=|",$idval);
	for($i=0;$i<count($arr_id);$i++) {
		pmysql_query("BEGIN WORK");
		$sql = "UPDATE tblmember SET staff_type = '1' WHERE id = '".$arr_id[$i]."'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		if($pmysql_errno==0)
                pmysql_query("COMMIT");
        else
                pmysql_query("ROLLBACK");
	}
	//로그 insert
	$log_content = "## 리뷰쓰기권한등록 : ID:".str_replace("|=|",",",$idval)." ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>window.onload=function(){ alert('선택하신 회원 ".count($arr_id)."명에게 리뷰쓰기권한을 주었습니다.');}</script>";
} else if ($type=="member_review_auth_del" && ord($id)) {	//선택 회원 리뷰쓰기 권한 주기
	$idval=rtrim($id,'|=|');
	$arr_id=explode("|=|",$idval);
	for($i=0;$i<count($arr_id);$i++) {
		pmysql_query("BEGIN WORK");
		$sql = "UPDATE tblmember SET staff_type = '0' WHERE id = '".$arr_id[$i]."' and staff_type = '1'";
		pmysql_query($sql,get_db_conn());
		$pmysql_errno += pmysql_errno();
		if($pmysql_errno==0)
                pmysql_query("COMMIT");
        else
                pmysql_query("ROLLBACK");
	}
	//로그 insert
	$log_content = "## 리뷰쓰기권한해제 : ID:".str_replace("|=|",",",$idval)." ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>window.onload=function(){ alert('선택하신 회원 ".count($arr_id)."명에게 리뷰쓰기권한을 해제하였습니다.');}</script>";
}

$listnum    = $_POST["listnum"] ?: "20";

$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = substr($regdate,0,4)."-".substr($regdate,4,2)."-".substr($regdate,6,2);
$period[1] = date("Y-m-d",$CurrentTime);
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));
$sort= $_POST["sort"];
if($sort =='') $sort= 4;
$scheck=(int)$_POST["scheck"];
$staff_type=$_POST["staff_type"];
$group_code=$_POST["group_code"];
$job_cd=$_POST["job_cd"];
$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];
$search=$_POST["search"];
$selected[scheck][$scheck]='selected';
$checked[staff_type][$staff_type]='checked';

$checked[_kakao_agree]['Y']=$_kakao_agree=='Y'?'checked':'';
$_kakao_status = $_kakao_agree;
if(is_array($_mailsms_agree)) $_mailsms_status = implode("','",$_mailsms_agree);
$checked[_mailsms_agree]['Y']='';
$checked[_mailsms_agree]['S']='';
$checked[_mailsms_agree]['M']='';
if (count($_mailsms_agree) > 0) {
	for($i=0;$i<count($_mailsms_agree);$i++) {
		$checked[_mailsms_agree][$_mailsms_agree[$i]] = 'checked';
	}
}

${"check_sort".$sort} = "selected";
${"check_vperiod".$vperiod} = "checked";

$display_group_code="none";
if($scheck==6) $display_group_code="";
$display_todaylogin="";
if($scheck==7) $display_todaylogin="none";

//스테프관련 추가 (2016.05.10 - 김재수)
$mem_type_chk	= $_POST['mem_type_chk'];
$staff_cooper_yn=substr($mem_type_chk,0,1);
if ($staff_cooper_yn == 'Y') {
	$staff_yn		= "Y";
	$cooper_yn	= "N";
} else if ($staff_cooper_yn == 'C') {
	$staff_yn		= "N";
	$cooper_yn	= "Y";
} else {
	$staff_yn		= "N";
	$cooper_yn	= "N";
}
$regmem_yn=substr($mem_type_chk,1,1);
$checked[staff_yn][$staff_yn]='checked';
$checked[mem_type_chk][$mem_type_chk]='checked';
$checked[lunar][$_lunar]='checked';

$ArrSort = array("date","name","id","age","reserve");
$ArrScheck = array("all","id","nickname","name","home_tel","mobile","group_code","logindate","home_addr");

include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
	$("#_agefloor > option[value=<?=$_agefloor?>]").attr("selected", "true");
	$("#_brand > option[value=<?=$_brand?>]").attr("selected", "true");
	$("input:radio[name=_geder_type]:input[value='<?=$_geder_type?>']").attr("checked", "true");
	//$("input:radio[name=_mail_agree]:input[value='<?=$_mail_agree?>']").attr("checked", "true");
	//$("input:radio[name=_sms_agree]:input[value='<?=$_sms_agree?>']").attr("checked", "true");
	$("input:checkbox[name=_sellbuy]:input[value='<?=$_sellbuy?>']").attr("checked", "true");
});

$(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});

function _sellbuyControl(idx){
	$("input:checkbox[name='_sellbuy']").each(function(index){
		if (idx == index){
			
		} else {
			this.checked = false;
		}
	});
}
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

function resetBrandSearchWord(obj) {
    if ( $(obj).val() == "" ) {
        $("#_brandname").attr("disabled", false).val("").focus();
    } else {
        $("#_brandname").attr("disabled", true);
    }
}

function check() {
	if (document.form1.search.value.length==0) {
		tmsg="";
		//if(document.form1.scheck[6].checked==false) tmsg="검색기간 내에서 ";
		if(document.form1.scheck[5].checked && document.form1.group_code.value.length==0) {
			alert("조회하실 회원 등급을 선택하세요.");
			document.form1.group_code.focus();
			return;
		}
		if (confirm(tmsg+" 조회하시겠습니까?")) {
			document.form1.submit();
		}
	} else {
		document.form1.block.value = ""; 
		document.form1.gotopage.value = "";
		document.form1.submit();
	}
}

function searchcheck() {
	key=event.keyCode;
	if (key==13) { check(); }
}

function ChangeMemberGroup() {
    document.stepform.member_codes.value="";
    for(i=1;i<document.form1.ids_chk.length;i++) {
        if(document.form1.ids_chk[i].checked) {
            document.stepform.member_codes.value += document.form1.ids_chk[i].value+",";
        }
    }

    if(document.stepform.member_codes.value.length==0) {
        alert("선택한 회원이 없습니다.");
        return;
    }

    if (document.form1.sel_member_group.value == '') {
        alert("적용할 회원 등급을 선택해 주시기 바랍니다.");
        return;
    }

    if(confirm("적용 하시겠습니까?")) {
        document.stepform.sel_member_group_code.value = document.form1.sel_member_group.value;
        document.stepform.target = "HiddenFrame";
	    document.stepform.submit();
	}
}

function OnChangePeriod(val,pos) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";

	if(val!='0'){
		$("#"+pos+"start").val(period[val]);
		$("#"+pos+"end").val(period[1]);
	}else{
		$("#"+pos+"start").val('');
		$("#"+pos+"end").val('');
	}
}
function OnChangeSearchType(val) {
	return;
	/*
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
	*/
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

// 리뷰쓰기 권한(2016-03-02 - 김재수 추가)
function check_review_auth(mode) {
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
	if (mode =='in')
	{
		if(confirm("선택하신 회원아이디에 리뷰쓰기권한을 주시겠습니까?")) {
			document.form1.type.value="member_review_auth";
			document.form1.submit();
		}
	} else if (mode =='out')
	{
		if(confirm("선택하신 회원아이디에 리뷰쓰기권한을 해제하시겠습니까?")) {
			document.form1.type.value="member_review_auth_del";
			document.form1.submit();
		}
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
	window.open("about:blank","lostpasspop","width=350,height=350,scrollbars=no");
	document.form3.target="lostpasspop";
	document.form3.id.value=id;
	document.form3.action="member_lostpasspop_new.php";
	document.form3.submit();
}

function ReserveInOut(id){
	window.open("about:blank","reserve_set","width=445,height=750,scrollbars=no");
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

function actPointInOut(id){
	window.open("about:blank","actpoint_set","width=445,height=750,scrollbars=no");
	document.actpointform2.target="actpoint_set";
	document.actpointform2.id.value=id;
	document.actpointform2.type.value="actpoint";
	document.actpointform2.submit();
}

function actPointInfo(id) {
	window.open("about:blank","actpoint_info","height=400,width=400,scrollbars=yes");
	document.actpointform1.target="actpoint_info";
	document.actpointform1.id.value=id;
	document.actpointform1.submit();
}

function OrderInfo(id) {
	window.open("about:blank","orderinfo","width=414,height=320,scrollbars=yes");
	document.form3.target="orderinfo";
	document.form3.id.value=id;
	document.form3.action="orderinfopop_new.php";
	document.form3.submit();
}

function CouponInfo(id) {
	window.open("about:blank","couponinfo","width=800,height=400,scrollbars=yes");
	document.form3.target="couponinfo";
	document.form3.id.value=id;
	document.form3.action="coupon_listpop_new.php";
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
	document.form3.action="member_etcpop_new.php";
	document.form3.submit();
}

function GoPage(block,gotopage) {
	/*document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();*/
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function member_write(id){
	document.form1.action="member_write.php?id="+id;
	document.form1.submit();
}

function member_add(){
	document.form1.action="member_write.php";
	document.form1.submit();
}

function MemberExcel() {
	document.excel_query.ids.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.excel_query.target="excelselpop";
	document.excel_query.submit();
}

function MemberCheckExcel() {
	document.excel_query.ids.value="";
	for(i=1;i<document.form1.ids_chk.length;i++) {
		if(document.form1.ids_chk[i].checked) {
			if(document.excel_query.ids.value!='') document.excel_query.ids.value +=",";
			document.excel_query.ids.value+=document.form1.ids_chk[i].value;
		}
	}
	if(document.excel_query.ids.value.length==0) {
		alert("선택하신 회원아이디가 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.excel_query.target="excelselpop";
	document.excel_query.submit();
}

function search_detail_open() {
	if ($("input[name=search_detail_open]").val() == 'Y') {
		$(".search_tr").hide();
		$("input[name=search_detail_open]").val('N');
	} else {
		$(".search_tr").show();
		$("input[name=search_detail_open]").val('Y');
	}
}
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			<input type=hidden name=email>
			<input type=hidden name=block value="">
			<input type=hidden name=gotopage value="">
			<input type=hidden name=search_detail_open value="<?=$search_detail_open?>">

			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=1 >
				<colgroup>
				<col style="width:100px">
				<col style="width:470px">
				<col style="width:100px">
				<col style="width:auto">
				</colgroup>
				<TR style="display:none;">
					<th class="sleep_search2" style="height:30px;"><span>휴면일</span></th>
					<TD colspan=3 class="sleep_search2">
						<input class="input_bd_st01_01" type="text"  OnClick="Calendar(event)" name="sleep_date_start" id="sleep_date_start" value='<?=$sleep_date_start?>'> ~ 
						<input class="input_bd_st01_01" type="text"  OnClick="Calendar(event)" name="sleep_date_end" id="sleep_date_end" value='<?=$sleep_date_end?>'>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'sleep_date_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'sleep_date_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'sleep_date_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'sleep_date_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'sleep_date_')">
					</TD>
				</TR>

				<TR>
					<th><span>검색어 입력</span></th>
					<TD colspan="3"><input name=search size=40 value="<?=$search?>" onKeyDown="searchcheck()" class="input"></TD>
				</TR>

				<TR>
					<th><span>검색조건 선택</span></th>
					<TD colspan="3">
					<div class="table_none" >
					<select name="scheck" onchange="OnChangeSearchType(this.value);">
						<option value='0' <?=$selected[scheck][0]?>>통합검색</option>
						<option value='1' <?=$selected[scheck][1]?>>아이디</option>
						<option value='3' <?=$selected[scheck][3]?>>회원명</option>
						<option value='5' <?=$selected[scheck][5]?>>핸드폰번호</option>
						<option value='7' <?=$selected[scheck][7]?>>오늘 로그인 회원</option>
						<option value='8' <?=$selected[scheck][8]?>>주소</option>
					</select>
					<?/*?>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="0" id=idx_scheck0 onClick="OnChangeSearchType(this.value);" <?=$check_scheck0?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck0>회원 아이디로 검색</label></TD>
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="1" id=idx_scheck1 onClick="OnChangeSearchType(this.value);" <?=$check_scheck1?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck1>회원명로 검색</label></TD>
						<TD width="40%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="2" id=idx_scheck2 onClick="OnChangeSearchType(this.value);" <?=$check_scheck2?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck2>이메일로 검색</label></TD>
					</TR>
					<TR>
						<!--TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="3" id=idx_scheck3 onClick="OnChangeSearchType(this.value);" <?=$check_scheck3?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck3>주민등록번호로 검색</label></TD-->
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="3" id=idx_scheck3 onClick="OnChangeSearchType(this.value);" <?=$check_scheck3?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck3>전화번호로 검색</label></TD>
						<TD width="40%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="4" id=idx_scheck4 onClick="OnChangeSearchType(this.value);" <?=$check_scheck4?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck4>핸드폰 번호로 검색</label></TD>
						<!--TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="5" id=idx_scheck5 onClick="OnChangeSearchType(this.value);" <?=$check_scheck5?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck5>추천인 검색</label></TD-->
						<TD width="30%"><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="6" id=idx_scheck6 onClick="OnChangeSearchType(this.value);" <?=$check_scheck6?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck6>등급회원 검색</label></TD>
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
						<TD width="*%" colspan = '6'><input  style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;" type=radio name=scheck value="7" id=idx_scheck7 onClick="OnChangeSearchType(this.value);" <?=$check_scheck7?>> <label style='cursor:hand; TEXT-DECORATION: none;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_scheck7>오늘 로그인 회원 검색 <B>(총 <?=$todaylogin?>명)</B></label></TD>
					</TR>
					</TABLE>
					<?*/?>
					</div>
					</TD>
				</TR>
				<TR id="div_group_code" style="">
					<th><span>회원등급 선택</span></th>
					<TD><select name=group_code style="width:300px" class="">
					<option value="">전체</option>
<?php 
					$sql = "SELECT group_code,group_name FROM tblmembergroup order by group_code";
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
					</TD>
					<th><span>회원직업 선택</span></th>
					<TD><select name=job_cd style="width:300px" class="">
					<option value="">전체</option>
<?php 
					foreach ($erp_job_cd_arr as $k => $v) {
						$job_cd_selected	= $k==$job_cd?" selected":"";
						echo "<option value='{$k}'{$job_cd_selected}>{$v}</option>";
					}
?>
					</select>
					</TD>
				</TR>
				<TR id="div_group_code2" style="display:<?=$display_group_code?>;">
					<TD colspan="2" background="images/table_con_line.gif"></TD>
				</TR>
				<TR id="div_todaylogin" style="display:none;">
					<th><span>회원 구분</span></th>
					<td colspan="3">
						<input type ='radio' name = 'staff_yn' value = '' <?=$checked['staff_yn']['']?>>전체
						<input type ='radio' name = 'staff_yn' value = 'N' <?=$checked['staff_yn']['N']?>>일반회원
						<input type ='radio' name = 'staff_yn' value = 'Y' <?=$checked['staff_yn']['Y']?>>임직원
					</td>
				</TR>
				<TR id="div_todaylogin">
					<th><span>회원 구분</span></th>
					<td colspan="3">
						<input type ='radio' name = 'mem_type_chk' value = '' <?=$checked['mem_type_chk']['']?>>전체
						<!-- <input type ='radio' name = 'mem_type_chk' value = 'NN' <?=$checked['mem_type_chk']['NN']?>>준회원 -->
						<input type ='radio' name = 'mem_type_chk' value = 'NY' <?=$checked['mem_type_chk']['NY']?>>일반회원
						<input type ='radio' name = 'mem_type_chk' value = 'YY' <?=$checked['mem_type_chk']['YY']?>>임직원
<!-- 20170825 제휴사 추가 -->
						<input type ='radio' name = 'mem_type_chk' value = 'CY' <?=$checked['mem_type_chk']['CY']?>>제휴업체
<!------------------------>
					</td>
				</TR>
				<TR id="div_todaylogin">
					<th><span>가입일</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="search_start" id="search_start" OnClick="Calendar(event)" readonly value="<?=$search_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="search_end" id="search_end" OnClick="Calendar(event)" readonly value="<?=$search_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'search_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'search_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'search_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'search_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'search_')">
					</td>
					<th><span>최종로그인</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_lastlogin_start" id="_lastlogin_start" OnClick="Calendar(event)" readonly value="<?=$_lastlogin_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="_lastlogin_end" id="_lastlogin_end" OnClick="Calendar(event)" readonly value="<?=$_lastlogin_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'_lastlogin_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'_lastlogin_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'_lastlogin_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'_lastlogin_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'_lastlogin_')">
					</td>
				</TR>
				<TR id="div_todaylogin" style="display:none">
					<th><span>성별</span></th>
					<td>
						<input type ='radio' name = '_geder_type' value = '' >전체
						<input type ='radio' name = '_geder_type' value = '1' >남자
						<input type ='radio' name = '_geder_type' value = '2' >여자
					</td>
					<th><span>연령층</span></th>
					<td>
						<select name="_agefloor" id="_agefloor">
							<option value="">전체</option>
							<option value="10">10대</option>
							<option value="20">20대</option>
							<option value="30">30대</option>
							<option value="40">40대</option>
							<option value="50">50대</option>
							<option value="60">60대</option>
						</select>
					</td>
				</TR>
				<TR id="div_todaylogin">
					<th><span>메일/SMS수신여부</span></th>
					<td colspan="3">
						<input type ='checkbox' name = '_mailsms_agree[]' value='Y' <?=$checked['_mailsms_agree']['Y']?>>SMS/MAIL/카카오톡 모두수신
						<input type ='checkbox' name = '_mailsms_agree[]' value='S' <?=$checked['_mailsms_agree']['S']?>>SMS만 수신
						<input type ='checkbox' name = '_mailsms_agree[]' value='M' <?=$checked['_mailsms_agree']['M']?>>MAIL만 수신
						<input type ='checkbox' name = '_kakao_agree' value='Y' <?=$checked['_kakao_agree']['Y']?>>카카오톡만 수신
					</td>
					
				</TR>
				<!--
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>회원 권한</span></th>
					<td colspan="3">
						<input type ='radio' name = 'staff_type' value = '' <?=$checked['staff_type']['']?>>전체
						<input type ='radio' name = 'staff_type' value = '1' <?=$checked['staff_type']['1']?>>리뷰쓰기 가능
					</td>
				</TR>
				<TR id="div_todaylogin" style="display:<?=$display_todaylogin?>;">
					<th><span>검색기간 선택</span></th>
					<td colspan="3">
						<input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ 
						<input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
					</td>
				</TR>
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>활동 포인트</span></th>
					<td colspan=3>
						<input class="input_bd_st01" type="text" name="_act_point_start" numberonly='true' id="_act_point_start" value="<?=$_act_point_start?>"/>포인트 ~ 
						<input class="input_bd_st01" type="text" name="_act_point_end" numberonly='true' id="_act_point_end" value="<?=$_act_point_end?>"/>포인트
					</td>
				-->
					<!--th><span>적립금</span></th>
					<td>
						<input class="input_bd_st01" type="text" name="_savemoney_start" numberonly='true' id="_savemoney_start" value="<?=$_savemoney_start?>"/>원 ~ 
						<input class="input_bd_st01" type="text" name="_savemoney_end" numberonly='true' id="_savemoney_end" value="<?=$_savemoney_end?>"/>원
					</td-->
				</TR>
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>방문횟수</span></th>
					<td colspan="3">
						<input class="input_bd_st01" type="text" name="_visitcnt_start" numberonly='true' id="_visitcnt_start" value="<?=$_visitcnt_start?>"/>회 ~ 
						<input class="input_bd_st01" type="text" name="_visitcnt_end" numberonly='true' id="_visitcnt_end" value="<?=$_visitcnt_end?>"/>회
					</td>
				</TR>
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>생년월일</span></th>
					<td colspan=3>
						<input type ='radio' name = 'lunar' value = '' <?=$checked['lunar']['']?>>전체
						<input type ='radio' name = 'lunar' value = '1' <?=$checked['lunar']['1']?>>양력
						<input type ='radio' name = 'lunar' value = '0' <?=$checked['lunar']['0']?>>음력
						<input class="input_bd_st01_01" type="text" name="_birthday_start" id="_birthday_start" numberonly='true' OnClick="Calendar(event)" readonly value="<?=$_birthday_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="_birthday_end" id="_birthday_end" numberonly='true' OnClick="Calendar(event)" readonly value="<?=$_birthday_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'_birthday_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'_birthday_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'_birthday_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'_birthday_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'_birthday_')">
					</td>
					<!-- <th><span>기념일</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_merryday_start" numberonly='true' id="_merryday_start" OnClick="Calendar(event)" readonly value="<?=$_merryday_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="_merryday_end" numberonly='true' id="_merryday_end" OnClick="Calendar(event)" readonly value="<?=$_merryday_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'_merryday_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'_merryday_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'_merryday_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'_merryday_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'_merryday_')">
					</td> -->
				</TR>
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>구매기간설정</span></th>
					<td>
						<input type ='checkbox' id="_sellbuy_0" name="_sellbuy" onclick="_sellbuyControl(0);" value = '0'>전체
						<input type ='checkbox' id="_sellbuy_1" name="_sellbuy" onclick="_sellbuyControl(1);" value = '1'>최근 1년
					</td>
					<th><span>최종구매일</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_lastbuy_start" id="_lastbuy_start" OnClick="Calendar(event)" readonly value="<?=$_lastbuy_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="_lastbuy_end" id="_lastbuy_end" OnClick="Calendar(event)" readonly value="<?=$_lastbuy_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'_lastbuy_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'_lastbuy_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'_lastbuy_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'_lastbuy_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'_lastbuy_')">
					</td>
				</TR>
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>주문일</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_buy_start" id="_buy_start" OnClick="Calendar(event)" readonly value="<?=$_buy_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="_buy_end" id="_buy_end" OnClick="Calendar(event)" readonly value="<?=$_buy_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'_buy_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'_buy_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'_buy_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'_buy_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'_buy_')">
					</td>
					<th><span>결제완료일</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_buypay_start" id="_buypay_start" OnClick="Calendar(event)" readonly value="<?=$_buypay_start?>"/> ~ 
						<input class="input_bd_st01_01" type="text" name="_buypay_end" id="_buypay_end" OnClick="Calendar(event)" readonly value="<?=$_buypay_end?>"/>
						<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0,'_buypay_')">
						<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1,'_buypay_')">
						<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2,'_buypay_')">
						<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3,'_buypay_')">
						<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4,'_buypay_')">
					</td>
				</TR>
				<TR id="div_todaylogin"<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>주문횟수</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_buycnt_start" numberonly='true' id="_buycnt_start" value="<?=$_buycnt_start?>"/>회 ~ 
						<input class="input_bd_st01_01" type="text" name="_buycnt_end" numberonly='true' id="_buycnt_end" value="<?=$_buycnt_end?>"/>회
					</td>
					<th><span>주문금액</span></th>
					<td>
						<input class="input_bd_st01_01" type="text" name="_buyprice_start" numberonly='true' id="_buyprice_start"  value="<?=$_buyprice_start?>"/>원 ~ 
						<input class="input_bd_st01_01" type="text" name="_buyprice_end" numberonly='true' id="_buyprice_end"  value="<?=$_buyprice_end?>"/>원
					</td>
				</TR>
				<TR<?if ($search_detail_open=='N'){?> style="display:none;"<?}?> class='search_tr'>
					<th><span>주문브랜드</span></th>
					<TD colspan="3">
					<select name="_brand" id="_brand" onChange="javascript:resetBrandSearchWord(this);">
					<option value="">전체 브랜드</option>
<?php 
					$sql = "select bridx,brandname from tblproductbrand ORDER BY lower(brandname) asc ";
					$result = pmysql_query($sql,get_db_conn());
					while ($row=pmysql_fetch_object($result)) {
						echo "<option value='{$row->bridx}'>{$row->brandname}</option>";
					}
					pmysql_free_result($result);
?>
                    </select>
                    <input class="w200" type="text" id="_brandname" name="_brandname" value="<?=$_brandname?>" <?php if($_brand) echo "disabled";?>>
					</TD>
				</TR>
				</TABLE>
				<div><a href="javascript:search_detail_open();" style='display:block;background:#f0f0f0; border-left:1px solid #aeaeae; border-right:1px solid #aeaeae; border-bottom:1px solid #aeaeae; padding:8px 0px;text-align:center;text-decoration: none;'>상세검색▲▼</a></div>
				</div>
				<p class="ta_r"><a href="javascript:check();"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a>&nbsp;<a href="#;" onclick="MemberExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></p>
				</td>
			</tr>
<?php
			// 회원가입 기간 - 사용 안함
			$date_start = str_replace("-","",$search_start)."000000";
			$date_end = str_replace("-","",$search_end)."235959";
			if($search_start && $search_end) $searchsql = "AND date >= '{$date_start}' AND date <= '{$date_end}' ";

			// 회원등급
			if ($group_code) {
				$searchsql.= "AND a.group_code = '{$group_code}' ";	//해당 등급회원
			}// else {
				//$searchsql.= "AND a.group_code IS NOT NULL ";				//모든 등급회원
			//}
			
			// 회원직업
			if ($job_cd !='') {
				$searchsql.= "AND a.job_code = '{$job_cd}' ";
			}

            /*
			//회원임직원구분
			if ($staff_yn) {
				$searchsql .= "AND staff_yn = '{$staff_yn}' ";
			}

			//회원 준회원, 정회원, 임직원 구분
			if ($regmem_yn) {
				if ($regmem_yn == 'Y')
					$searchsql .= "AND (dupinfo !='' OR mb_type ='adm') ";
				else 
					$searchsql .= "AND sns_type = 'sns' AND dupinfo ='' ";
			}
            */

            //회원임직원구분 2016-12-01
			if ($mem_type_chk) {
				if ($staff_yn == "Y") {
					$searchsql .= "AND staff_yn = '{$staff_yn}' ";
				} else {
					//회원협력업체구분
					//20170826 제휴사 구분 추가
					if ($cooper_yn == "Y") {
						$searchsql .= "AND cooper_yn = '{$cooper_yn}' ";
					} else {

					//회원 일반 회원
						$searchsql .= "AND staff_yn = 'N' AND cooper_yn = 'N' ";
					}
				}
			}

			// 회원권한구분 - 1일경우 리뷰쓰기 가능회원
			if($staff_type == '1') $searchsql .= "AND staff_type = '1' ";

			//적립금
			if ($_savemoney_start != "" || $_savemoney_end != "") { 
				$_savemoney_start_tem = $_savemoney_start == "" ? "0" : $_savemoney_start;
				$_savemoney_end_tem = $_savemoney_end == "" ? "1000000000" : $_savemoney_end;
				$searchsql.= " AND reserve BETWEEN {$_savemoney_start_tem} AND {$_savemoney_end_tem} ";
			}

			//활동 포인트
			if ($_act_point_start != "" || $_act_point_end != "") { 
				$_act_point_start_tem = $_act_point_start == "" ? "0" : $_act_point_start;
				$_act_point_end_tem = $_act_point_end == "" ? "1000000000" : $_act_point_end;
				$searchsql.= " AND act_point BETWEEN {$_act_point_start_tem} AND {$_act_point_end_tem} ";
			}
			
			//방문횟수
			if ($_visitcnt_start != "" || $_visitcnt_end != "") { 
				$_visitcnt_start_tem = $_visitcnt_start == "" ? "0" : $_visitcnt_start;
				$_visitcnt_end_tem = $_visitcnt_end == "" ? "1000000000" : $_visitcnt_end;
				$searchsql.= " AND logincnt BETWEEN {$_visitcnt_start_tem} AND {$_visitcnt_end_tem} ";
			} 
			
			//최종로그인
			if ($_lastlogin_start != "" || $_lastlogin_end != "") { 
				$_lastlogin_start_tem = $_lastlogin_start == "" ? "1900-01-01" : $_lastlogin_start;
				$_lastlogin_end_tem = $_lastlogin_end == "" ? date("Y-m-d") : $_lastlogin_end;
				$_lastlogin_start_tem = str_replace("-","",$_lastlogin_start_tem)."000000";
				$_lastlogin_end_tem = str_replace("-","",$_lastlogin_end_tem)."235959";
				$searchsql.= " AND logindate BETWEEN '{$_lastlogin_start_tem}' AND '{$_lastlogin_end_tem}' ";
			}
			
			$mem_date_search_cnt	= 0;

			//성별
			if ($_geder_type != "") {
				$searchsql.= " AND gender = '{$_geder_type}' ";
			}
			
			//연령층
			if ($_agefloor != ""){ 
				$nowYear = date("Y");
				$searchsql.= " AND LEFT(birth,4) BETWEEN '".((($nowYear-$_agefloor)+2)-10)."' AND '".(($nowYear-$_agefloor)+1)."' "; 
				$mem_date_search_cnt++;
				//exdebug($searchsql);
			} 

			//메일수신 여부
			/*if ($_mail_agree != "") {
				$searchsql.= " AND news_yn = '{$_mail_agree}' ";
				$mem_date_search_cnt++;
			}*/

			//메일/SMS수신 여부
			$_receive_searchsql	= array();
			if ($_mailsms_status != "") {
				//$receive_searchsql[]	= "news_yn IN ('{$_mailsms_status}')";

				for($i=0;$i<count($_mailsms_agree);$i++) {
					if ($_mailsms_agree[$i]=='Y') {
						$_receive_searchsql[]	= "(news_yn = 'Y' AND kko_yn = 'Y')";
					} else if ($_mailsms_agree[$i]=='S') {
						$_receive_searchsql[]	= "(news_yn = 'S' AND kko_yn = 'N')";
					} else if ($_mailsms_agree[$i]=='M') {
						$_receive_searchsql[]	= "(news_yn = 'M' AND kko_yn = 'N')";
					}
				}
				$mem_date_search_cnt++;
			}

			//카카오 수신 여부
			if ($_kakao_status != "") {
				$_receive_searchsql[]	= "(kko_yn = '{$_kakao_status}' AND news_yn = 'N')";
				$mem_date_search_cnt++;
			}
			if ($_receive_searchsql) $searchsql.= " AND ( ".implode(" OR ", $_receive_searchsql)." ) ";

			//음력양력구분
			if($_lunar !='') {
				$searchsql.= " AND lunar = '{$_lunar}' ";
			}

			//생년월일
			if ($_birthday_start != "" || $_birthday_end != "") { 
				$_birthday_start_tem = $_birthday_start == "" ? "1900-01-01" : $_birthday_start;
				$_birthday_end_tem = $_birthday_end == "" ? date("Y-m-d") : $_birthday_end;
				$_birthday_start_tem = str_replace("-","", $_birthday_start_tem);
				$_birthday_end_tem = str_replace("-","", $_birthday_end_tem);
				$_birthday_start_tem = strlen($_birthday_start_tem) == 4 ? '1900'.$_birthday_start_tem : $_birthday_start_tem;
				$_birthday_end_tem = strlen($_birthday_end_tem) == 4 ? date("Y").$_birthday_end_tem : $_birthday_end_tem;
				$searchsql.= " AND replace(birth,'-','') BETWEEN '{$_birthday_start_tem}' AND '{$_birthday_end_tem}' ";
				$mem_date_search_cnt++;
			}

			//기념일 - married_date를 기념일로 사용
			if ($_merryday_start !='' || $_merryday_end) { 
				$_merryday_start_tem = $_merryday_start == "" ? "1900-01-01" : $_merryday_start;
				$_merryday_end_tem = $_merryday_end == "" ? date("Y-m-d") : $_merryday_end;
				$_merryday_start_tem = str_replace("-","", $_merryday_start_tem);
				$_merryday_end_tem = str_replace("-","", $_merryday_end_tem);
				$_merryday_start_tem = strlen($_merryday_start_tem) == 4 ? '1900'.$_merryday_start_tem : $_merryday_start_tem;
				$_merryday_end_tem = strlen($_merryday_end_tem) == 4 ? date("Y").$_merryday_end_tem : $_merryday_end_tem;
				$searchsql.= " AND replace(married_date,'-','') BETWEEN '{$_merryday_start_tem}' AND '{$_merryday_end_tem}' ";
				$mem_date_search_cnt++;
			}
			//if ($mem_date_search_cnt > 0) $searchsql.= " AND member_out != 'Y' ";
			$searchsql.= " AND member_out != 'Y' ";


			$outerJoinControl = "left outer";
			$queryOuterW = "";
			if ($_sellbuy != "" ) {
				if ($_sellbuy == "0") {
					$outerJoinControl = "";
					$queryOuterW .= "";
				} else if ($_sellbuy == "1") { 
					$outerJoinControl = "";
					$p_Y= date("Ymd",mktime(0,0,0,date("m"),date("d"),date("Y")-1))."000000";
					$queryOuterW = " and order_conf_date >= '{$p_Y}' ";
				}
			}

			// 구매기간
			if ($_lastbuy_start !='' || $_lastbuy_end) { 
				$outerJoinControl = "";
				$_lastbuy_start_tem = $_lastbuy_start == "" ? "1900-01-01" : $_lastbuy_start;
				$_lastbuy_end_tem = $_lastbuy_end == "" ? date("Y-m-d") : $_lastbuy_end;
				$_lastbuy_start_tem = str_replace("-","",$_lastbuy_start_tem)."000000";
				$_lastbuy_end_tem = str_replace("-","",$_lastbuy_end_tem)."235959";
				$queryOuterW = " AND order_conf_date BETWEEN '{$_lastbuy_start_tem}' AND '{$_lastbuy_end_tem}' ";
			}

			// 주문일
			if ($_buy_start !='' || $_buy_end) { 
				$outerJoinControl = "";
				$_buy_start_tem = $_buy_start == "" ? "1900-01-01" : $_buy_start;
				$_buy_end_tem = $_buy_end == "" ? date("Y-m-d") : $_buy_end;
				$_buy_start_tem = str_replace("-","",$_buy_start_tem)."000000";
				$_buy_end_tem = str_replace("-","",$_buy_end_tem)."235959";
				$queryOuterW .= " AND regdt BETWEEN '{$_buy_start_tem}' AND '{$_buy_end_tem}' ";
			}

			// 결제완료일
			if ($_buypay_start !='' || $_buypay_end) { 
				$outerJoinControl = "";
				$_buypay_start_tem = $_buypay_start == "" ? "1900-01-01" : $_buypay_start;
				$_buypay_end_tem = $_buypay_end == "" ? date("Y-m-d") : $_buypay_end;
				$_buypay_start_tem = str_replace("-","",$_buypay_start_tem)."000000";
				$_buypay_end_tem = str_replace("-","",$_buypay_end_tem)."235959";
				$queryOuterW .= " AND ( (oi_step1 in (1) And oi_step2 = 0) ) AND ((bank_date BETWEEN '{$_buypay_start_tem}' AND '{$_buypay_end_tem}') OR (paymethod != 'B' AND (regdt BETWEEN '{$_buypay_start_tem}' AND '{$_buypay_end_tem}'))) ";
			}

			//주문횟수
			if ($_buycnt_start != "" || $_buycnt_end != "") { 
				$outerJoinControl = "";
				$_buycnt_start_tem = $_buycnt_start == "" ? "0" : $_buycnt_start;
				$_buycnt_end_tem = $_buycnt_end == "" ? "1000000000" : $_buycnt_end;
				$searchsql.= " AND coalesce(ex_ordercnt,0) BETWEEN {$_buycnt_start_tem} AND {$_buycnt_end_tem} ";
			}

			//구매금액
			if ($_buyprice_start != "" || $_buyprice_end != "") { 
				$outerJoinControl = "";
				$_buyprice_start_tem = $_buyprice_start == "" ? "0" : $_buyprice_start;
				$_buyprice_end_tem = $_buyprice_end == "" ? "1000000000" : $_buyprice_end;
				$searchsql.= " AND coalesce(ex_ordertotprice,0) BETWEEN {$_buyprice_start_tem} AND {$_buyprice_end_tem} ";
			}

			//주문브랜드
			if ($_brand != "") { 
				$outerJoinControl = "";
				$queryBrandW = " AND topb.bridx = {$_brand} ";
				$queryOuterW .= " AND _ord.vender_cnt > 0 ";
			} elseif ( $_brandname ) {
				$outerJoinControl = "";
                $arrBrandIdx = array();

                $tmp_search_keyword = strtolower($_brandname);
                $subsql  = "SELECT bridx FROM tblproductbrand WHERE lower(brandname) like '%{$tmp_search_keyword}%' OR lower(brandname2) like '%{$tmp_search_keyword}%' ";
                $subresult = pmysql_query($subsql);
                while ( $subrow = pmysql_fetch_object($subresult) ) {
                    if ( $subrow->bridx != "" ) {
                        array_push($arrBrandIdx, $subrow->bridx);
                    }
                }
                pmysql_free_result($subresult);

                if ( count($arrBrandIdx) > 0 ) { 
                    $queryBrandW = " AND topb.bridx in ( " . implode(",", $arrBrandIdx) . " )  ";
                    $queryOuterW .= " AND _ord.vender_cnt > 0 ";
                }
            }

			if($scheck=="0"){  //통합검색

				if ($search) {
					$searchsql.=" and id||name||email||home_tel||mobile||home_addr like '%{$search}%'";
				}

				$sql0 = "SELECT 
					COUNT(*) as cnt 
					FROM tblmember a {$outerJoinControl} join (select 
																_ord.id as _orderid,count(_ord.id) as ex_ordercnt,sum(_ord.totprice) as ex_ordertotprice
																from (select id,
																		SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) totprice,
																		min(oi.deli_date) as deli_date,
																		min(oi.regdt) as regdt,
																		min(oi.oi_step1) as oi_step1,
																		min(oi.oi_step2) as oi_step2,
																		min(oi.bank_date) as bank_date,
																		min(oi.paymethod) as paymethod,
																		min(oi.order_conf_date) as order_conf_date,
																		(select count(*) from (select top.*, tpb.bridx from tblorderproduct top left join tblproductbrand tpb on top.vender=tpb.vender) topb where min(oi.ordercode) = topb.ordercode and topb.op_step IN ('1', '2', '3', '4', '40', '41', '42') {$queryBrandW} group by vender limit 1) vender_cnt
																		from tblorderproduct op 
																		left join tblorderinfo oi on op.ordercode=oi.ordercode
																		where oi.id != '' 
																		and op.op_step IN ('1', '2', '3', '4', '40', '41', '42')
																		and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0', '40', '41', '42')																
																		group by op.ordercode, oi.id
																	)_ord
																	where 1=1
																	{$queryOuterW}
																	group by _ord.id) ord on a.id = ord._orderid
					WHERE 1=1 ".$searchsql;
				$sql = "SELECT *, coalesce(ex_ordercnt,0) as _ordercnt, coalesce(ex_ordertotprice,0) as _ordertotprice FROM (SELECT a.*,ord.*,mg.group_name,(SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count 
				FROM tblmember a left join tblmembergroup mg on a.group_code = mg.group_code
									 {$outerJoinControl} join (select 
																_ord.id as _orderid,count(_ord.id) as ex_ordercnt,sum(_ord.totprice) as ex_ordertotprice
																from (select id,
																		SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) totprice,
																		min(oi.deli_date) as deli_date,
																		min(oi.regdt) as regdt,
																		min(oi.oi_step1) as oi_step1,
																		min(oi.oi_step2) as oi_step2,
																		min(oi.bank_date) as bank_date,
																		min(oi.paymethod) as paymethod,
																		min(oi.order_conf_date) as order_conf_date,
																		(select count(*) from (select top.*, tpb.bridx from tblorderproduct top left join tblproductbrand tpb on top.vender=tpb.vender) topb where min(oi.ordercode) = topb.ordercode and topb.op_step IN ('1', '2', '3', '4', '40', '41', '42') {$queryBrandW} group by vender limit 1) vender_cnt
																		from tblorderproduct op 
																		left join tblorderinfo oi on op.ordercode=oi.ordercode
																		where oi.id != '' 
																		and op.op_step IN ('1', '2', '3', '4', '40', '41', '42')
																		and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0', '40', '41', '42')																		
																		group by op.ordercode, oi.id
																	)_ord
																	where 1=1
																	{$queryOuterW}
																	group by _ord.id) ord on a.id = ord._orderid
				) a 
				WHERE 1=1 {$searchsql} ";
			}else if ($scheck=="6") {	//등급회원 검색 - 사용안함
				
			} else if ($scheck=="7") {	//오늘 로그인 회원 검색
				//$searchsql = "";
				if ($search) {
					$searchsql = "AND id LIKE '%{$search}%' ";
				}
				$sql0 = "SELECT COUNT(*) as cnt FROM tblmember a {$outerJoinControl} join (select 
																_ord.id as _orderid,count(_ord.id) as ex_ordercnt,sum(_ord.price) as ex_ordertotprice
																from (select id,
																		SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) totprice,
																		min(oi.deli_date) as deli_date,
																		min(oi.regdt) as regdt,
																		min(oi.oi_step1) as oi_step1,
																		min(oi.oi_step2) as oi_step2,
																		min(oi.bank_date) as bank_date,
																		min(oi.paymethod) as paymethod,
																		min(oi.order_conf_date) as order_conf_date,
																		(select count(*) from (select top.*, tpb.bridx from tblorderproduct top left join tblproductbrand tpb on top.vender=tpb.vender) topb where min(oi.ordercode) = topb.ordercode and topb.op_step IN ('1', '2', '3', '4', '40', '41', '42') {$queryBrandW} group by vender limit 1) vender_cnt
																		from tblorderproduct op 
																		left join tblorderinfo oi on op.ordercode=oi.ordercode
																		where oi.id != '' 
																		and op.op_step IN ('1', '2', '3', '4', '40', '41', '42')
																		and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0', '40', '41', '42')																	
																		group by op.ordercode, oi.id
																	)_ord
																	where 1=1
																	{$queryOuterW}
																	group by _ord.id) ord on a.id = ord._orderid ";
				$sql0.= "WHERE logindate >= '".date("Ymd")."000000' ".$searchsql;
				$sql = "SELECT *, coalesce(ex_ordercnt,0) as _ordercnt, coalesce(ex_ordertotprice,0) as _ordertotprice FROM (SELECT a.*,ord.*,getgroupcodename(a.group_code) as group_name,(SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count 
				FROM tblmember a {$outerJoinControl} join (select 
																_ord.id as _orderid,count(_ord.id) as ex_ordercnt,sum(_ord.totprice) as ex_ordertotprice
																from (select id,
																		SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) totprice,
																		min(oi.deli_date) as deli_date,
																		min(oi.regdt) as regdt,
																		min(oi.oi_step1) as oi_step1,
																		min(oi.oi_step2) as oi_step2,
																		min(oi.bank_date) as bank_date,
																		min(oi.paymethod) as paymethod,
																		min(oi.order_conf_date) as order_conf_date,
																		(select count(*) from (select top.*, tpb.bridx from tblorderproduct top left join tblproductbrand tpb on top.vender=tpb.vender) topb where min(oi.ordercode) = topb.ordercode and topb.op_step IN ('1', '2', '3', '4', '40', '41', '42') {$queryBrandW} group by vender limit 1) vender_cnt
																		from tblorderproduct op 
																		left join tblorderinfo oi on op.ordercode=oi.ordercode
																		where oi.id != '' 
																		and op.op_step IN ('1', '2', '3', '4', '40', '41', '42')
																		and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0', '40', '41', '42')															
																		group by op.ordercode, oi.id
																	)_ord
																	where 1=1
																	{$queryOuterW}
																	group by _ord.id) ord on a.id = ord._orderid
				) a WHERE logindate >= '".date("Ymd")."000000' {$searchsql} ";
			} else {

				if ($search) {
					$searchsql.= "AND {$ArrScheck[$scheck]} LIKE '%{$search}%' ";
				}
				$sql0 = "SELECT 
					COUNT(*) as cnt 
					FROM tblmember a {$outerJoinControl} join (select 
																_ord.id as _orderid,count(_ord.id) as ex_ordercnt,sum(_ord.totprice) as ex_ordertotprice
																from (select id,
																		SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) totprice,
																		min(oi.deli_date) as deli_date,
																		min(oi.regdt) as regdt,
																		min(oi.oi_step1) as oi_step1,
																		min(oi.oi_step2) as oi_step2,
																		min(oi.bank_date) as bank_date,
																		min(oi.paymethod) as paymethod,
																		min(oi.order_conf_date) as order_conf_date,
																		(select count(*) from (select top.*, tpb.bridx from tblorderproduct top left join tblproductbrand tpb on top.vender=tpb.vender) topb where min(oi.ordercode) = topb.ordercode and topb.op_step IN ('1', '2', '3', '4', '40', '41', '42') {$queryBrandW} group by vender limit 1) vender_cnt
																		from tblorderproduct op 
																		left join tblorderinfo oi on op.ordercode=oi.ordercode
																		where oi.id != '' 
																		and op.op_step IN ('1', '2', '3', '4', '40', '41', '42')
																		and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0', '40', '41', '42')																
																		group by op.ordercode, oi.id
																	)_ord
																	where 1=1
																	{$queryOuterW}
																	group by _ord.id) ord on a.id = ord._orderid
					WHERE 1=1 ".$searchsql;
				$sql = "SELECT *, coalesce(ex_ordercnt,0) as _ordercnt, coalesce(ex_ordertotprice,0) as _ordertotprice FROM (SELECT a.*,ord.*,mg.group_name,(SELECT count(id) FROM tblmember_question WHERE id = a.id) q_count 
				FROM tblmember a left join tblmembergroup mg on a.group_code = mg.group_code
								 {$outerJoinControl} join (select 
																_ord.id as _orderid,count(_ord.id) as ex_ordercnt,sum(_ord.totprice) as ex_ordertotprice
																from (select id,
																		SUM(((op.price+op.option_price)*op.option_quantity) - op.coupon_price - op.use_point + op.deli_price) totprice,
																		min(oi.deli_date) as deli_date,
																		min(oi.regdt) as regdt,
																		min(oi.oi_step1) as oi_step1,
																		min(oi.oi_step2) as oi_step2,
																		min(oi.bank_date) as bank_date,
																		min(oi.paymethod) as paymethod,
																		min(oi.order_conf_date) as order_conf_date,
																		(select count(*) from (select top.*, tpb.bridx from tblorderproduct top left join tblproductbrand tpb on top.vender=tpb.vender) topb where min(oi.ordercode) = topb.ordercode and topb.op_step IN ('1', '2', '3', '4', '40', '41', '42') {$queryBrandW} group by vender limit 1) vender_cnt
																		from tblorderproduct op 
																		left join tblorderinfo oi on op.ordercode=oi.ordercode
																		where oi.id != '' 
																		and op.op_step IN ('1', '2', '3', '4', '40', '41', '42')
																		and oi.oi_step1 IN ('1', '2', '3', '4') and oi.oi_step2 IN ('0', '40', '41', '42')																	
																		group by op.ordercode, oi.id
																	)_ord
																	where 1=1
																	{$queryOuterW}
																	group by _ord.id) ord on a.id = ord._orderid
				) a 
				WHERE 1=1 {$searchsql} ";
			}

			$mem_excel_sql		= $sql;

			switch ($sort) {
				case "0":	//아이디 내림차순
					$add_sql = "ORDER BY id DESC ";
					break;
				case "1":	//아이디 오름차순
					$add_sql = "ORDER BY id ASC ";
					break;
				case "2":	//이름 내림차순
					$add_sql = "ORDER BY name DESC, id DESC ";
					break;
				case "3":	//이름 오름차순
					$add_sql = "ORDER BY name ASC, id ASC ";
					break;
				case "4":	//가입일 내림차순
					$add_sql = "ORDER BY date DESC, id DESC ";
					break;
				case "5":	//가입일 오름차순
					$add_sql = "ORDER BY date ASC, id ASC ";
					break;
				case "6":	//적립금 내림차순
					$add_sql = "ORDER BY reserve DESC, id DESC ";
					break;
				case "7":	//적립금 오름차순
					$add_sql = "ORDER BY reserve ASC, id ASC ";
					break;
				case "8":	//누적금액 내림차순
					$add_sql = "ORDER BY _ordertotprice DESC, id DESC ";
					break;
				case "9":	//누적금액 오름차순
					$add_sql = "ORDER BY _ordertotprice ASC, id ASC ";
					break;
				case "10":	//주문횟수 내림차순
					$add_sql = "ORDER BY _ordercnt DESC, id DESC ";
					break;
				case "11":	//주문횟수 오름차순
					$add_sql = "ORDER BY _ordercnt ASC, id ASC ";
					break;
				case "12":	//최근로그인 내림차순
					$add_sql = "ORDER BY logindate DESC, id DESC ";
					break;
				case "13":	//최근로그인 오름차순
					$add_sql = "ORDER BY logindate ASC, id ASC ";
					break;
				default :	//아이디 내림차순
					$add_sql = "ORDER BY id DESC ";
					break;
			}

			$sql	.= $add_sql;
			$mem_excel_sql_orderby	= $add_sql;
			if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){
//				exdebug($sql);
			}
			$paging = new Paging($sql0,10,$listnum);
			$t_count = $paging->t_count;
			$gotopage = $paging->gotopage;
?>
			<tr>
				<td>				
				<div class="table_none">
				<table cellspacing=0 cellpadding=0 width="100%" border=0 >
				<tr>
					<td align=left width="50%">
					<div class="title_depth3_sub">검색된 회원목록</div>
					</td>
					<td align=right>
					<div style="margin:20px 0px 5px">전체 <span class="font_orange"><B><?=$t_count?></B></span>건 조회, 현재 <span class="font_orange"><B><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></B></span> 페이지				
					<select name=sort onchange="javascript:document.form1.submit();">
					<option value="0" <?=$check_sort0?>>아이디 내림차순</option>
					<option value="1" <?=$check_sort1?>>아이디 오름차순</option>
					<option value="2" <?=$check_sort2?>>이름 내림차순</option>
					<option value="3" <?=$check_sort3?>>이름 오름차순</option>
					<option value="4" <?=$check_sort4?>>가입일 내림차순</option>
					<option value="5" <?=$check_sort5?>>가입일 오름차순</option>
					<option value="6" <?=$check_sort6?>>적립금 내림차순</option>
					<option value="7" <?=$check_sort7?>>적립금 오름차순</option>
					<option value="8" <?=$check_sort8?>>누적금액 내림차순</option>
					<option value="9" <?=$check_sort9?>>누적금액 오름차순</option>
					<option value="10" <?=$check_sort10?>>주문횟수 내림차순</option>
					<option value="11" <?=$check_sort11?>>주문횟수 오름차순</option>
					<option value="12" <?=$check_sort12?>>최근로그인 내림차순</option>
					<option value="13" <?=$check_sort13?>>최근로그인 오름차순</option>
					</select>
					<select name="listnum" onchange="javascript:document.form1.submit();">
						<option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
						<option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
						<option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
						<option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
						<option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
						<option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
						<option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
						<option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
						<option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
					</select></div>			
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<?$_shopdata->member_baro=="Y"?$member_list_colspan="15":$member_list_colspan="14";?>
				<TR>
					<th><input type=checkbox name=allcheck onclick="CheckAll()" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"></th>
					<th>번호</th>
					<th>아이디</th>
					<th>이름</th>
					<th>비번</th>
					<th>가입일</th>
					<th>메일</th>
					<th>메모</th>
					<!--th>주소</th-->
					<th>휴대폰</th>
					<th colspan=2>통합포인트 / 상세</th>
					<th colspan=2>E포인트 / 적립&차감 / 상세</th>
					<th>누적금액</th>
					<th>내역</th>
					<th>주문횟수</th>
					<th>최근로그인</th>
					<?php if ($_shopdata->member_baro=="Y") {?>
					<th>인증</th>
					<?php }?>
					<th>수정</th>
				</TR>
				<input type=hidden name=ids_chk>
<?php
				$cnt=0;
				#############엑셀다운로드시 데이터 처리#############
				/*기존 소스가 페이지 로드시 무조건 엑셀 관련 쿼리를 수행하기 때문에 페이지 로딩이 무척 느려졌음. 이것때문에 새로이 조건을 주어서 엑셀다운로드 버튼 클릭시에만 이하 쿼리를 수행하고 다운로드 폼을 submit하도록 변경함 150813원재*/
				/*---------------------------------*/

				//$excel_sql0 = $sql; // 검색결과 모두를 엑셀 다운로드에 반영

				$sql = $paging->getSql($sql);
				//exdebug($sql);

				$result = pmysql_query($sql,get_db_conn());
                //echo "sql = ".$sql."<br>";

				// ERP 접속 (김재수)
				$oci_conn = GetErpDBConn();

				while($row=pmysql_fetch_object($result)) {

					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					if($row->black){
						$blackImage = "<img src = './img/btn/black_icon.gif' align = 'absmiddle'>";
					}else{
						$blackImage = "";
					}
					$reg_date=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)." (".substr($row->date,8,2).":".substr($row->date,10,2).")";
					//if ($row->home_zonecode) {
					//	$home_zonecode	= $row->home_zonecode;
					//} else {
						if (strlen($row->home_post) > 5) {
							$home_zonecode	= substr($row->home_post,0,3)."-".substr($row->home_post,3,3);
						} else {
							$home_zonecode	= $row->home_post;
						}
					//}
					$haddress="[".$home_zonecode."] ".str_replace("↑=↑"," ",$row->home_addr);		
					
					$erp_mem_reserve	= getErpMeberPoint($row->id, $oci_conn);
					$mem_reserve	= $erp_mem_reserve[p_err_code]==0?$erp_mem_reserve[p_data]:'0';


					echo "<tr>\n";
					echo "	<TD><p align=\"center\"><input type=checkbox name=ids_chk value=\"{$row->id}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"></td>\n";
					echo "	<TD>";
					if($row->member_out!="Y") {
						echo "	<b><span>{$number}</span></b>";
					} else echo $number;
					echo "	</td>\n";
					echo "	<TD>";

					if($row->member_out!="Y") {
//						echo "<b align = 'absmiddle'><span class=\"font_orange\"><A HREF=\"javascript:MemberInfo('{$row->id}')\">{$row->id} ({$row->group_name})</A></span></b>";
						echo "<b align = 'absmiddle'><span class=\"font_orange\">{$row->id} ({$row->group_name})</span></b>";
					} else {
						echo $row->id;
					}
					echo "&nbsp;".$blackImage ."</td>\n";

                    if ( $row->id ) {
                        echo "	<TD ><a href=\"javascript:CrmView('{$row->id}');\"><font color=\"blue\">{$row->name}</font></a></td>\n";
                    } else {
                        echo "	<TD >{$row->name}</td>\n";
                    }
					
					echo "	<TD>";
					if($row->member_out!="Y") {
						echo "	<a href=\"javascript:LostPass('{$row->id}');\"><img src=\"images/btn_edit4.gif\" border=\"0\"></a>";
					} else {
						echo "&nbsp;";
						//echo "	<button class=button2 disabled>변경</button>";
					}
					echo "	</td>\n";
					echo "	<TD title='{$reg_date}'>".substr($reg_date,0,10)."</td>\n";
					echo "	<TD >";
					echo "	<a href=\"javascript:MemberMail('{$row->email}','{$row->news_yn}');\"><img src=\"images/btn_send.gif\" border=\"0\"></a>";
					echo "	</td>\n";
					echo "	<TD>";
					echo "	<a href=\"javascript:MemberMemo2('{$row->id}');\">".(ord($row->memo)?"<img src=\"images/btn_memo.gif\" border=\"0\">":"<img src=\"images/btn_memor.gif\" border=\"0\">")."</a>";
					echo "	</td>\n";
					echo "	<!--TD >\n";
					echo "	<div class=\"table_none\"> \n";
					echo "		<A HREF=\"javascript:alert('{$haddress}')\"><IMG src=\"images/addr_home.gif\" align=absMiddle border=0></A>";
					echo "	</div>\n";
					echo "	</TD-->\n";
					echo "	<TD >\n";
					echo "	<div class=\"table_none\"> \n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					if(ord($row->mobile)) {
						$mem_tel ="휴대전화 : {$row->mobile}\\n";
					echo "		<A HREF=\"javascript:alert('{$mem_tel}')\"><IMG src=\"images/member_tel.gif\" align=absMiddle border=0 ></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:MemberSMS('{$row->news_yn}','{$row->home_tel}','{$row->mobile}')\"><IMG src=\"images/member_mobile.gif\" align=absMiddle border=0 ></A>";

						//news_yn : Y/S
					} else {
						echo "- -";
					}
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</div>\n";
					echo "	</TD>\n";
					echo "	<TD >".number_format($mem_reserve)."</td>\n";
					echo "	<TD >\n";
					echo "	<div class=\"table_none\"> \n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:ReserveInfo('{$row->id}');\"><img src=\"images/btn_detail.gif\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</div>\n";
					echo "	</TD>\n";
					echo "	<TD >".number_format($row->act_point)."</td>\n";
					echo "	<TD >\n";
					echo "	<div class=\"table_none\"> \n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					echo "		<A HREF=\"javascript:actPointInOut('{$row->id}');\"><img src=\"images/btn_pm.gif\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:actPointInfo('{$row->id}');\"><img src=\"images/btn_detail.gif\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</div>\n";
					echo "	</TD>\n";
					echo "	<TD >".number_format($row->_ordertotprice)."</td>\n";
					echo "	<TD >\n";
					echo "	<div class=\"table_none\"> \n";
					echo "	<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
					echo "	<tr>\n";
					echo "		<td>\n";
					echo "		<A HREF=\"javascript:OrderInfo('{$row->id}');\"><img src=\"images/btn_purchus.gif\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "		<td style=\"padding-left:1pt;\">\n";
					echo "		<A HREF=\"javascript:CouponInfo('{$row->id}');\"><img src=\"images/btn_coupon.gif\" border=\"0\"></A>";
					echo "		</td>\n";
					echo "	</tr>\n";
					echo "	</table>\n";
					echo "	</div>\n";
					echo "	</TD>\n";
					/*if ($scheck!="5") {
						echo "	<TD>".($row->rec_id?$row->rec_id:"&nbsp;")."</td>\n";
					} else {
						echo "	<TD style=\"padding-right:1pt;\">{$row->rec_cnt}명</td>\n";
					}*/
					$row->_ordercnt = $row->_ordercnt == '' ? '0' : $row->_ordercnt;
					echo "	<TD>".$row->_ordercnt."</td>\n";
					echo "	<TD >";
					if ($row->logindate) {
						echo substr($row->logindate,0,4)."-".substr($row->logindate,4,2)."-".substr($row->logindate,6,2)." ".substr($row->logindate,8,2).":".substr($row->logindate,10,2).":".substr($row->logindate,12,2);
					} else {
						echo "-";
					}
					echo "</td>\n";
					if ($_shopdata->member_baro=="Y") {
						if ($row->confirm_yn == "Y") {
							echo "<TD><a href=\"javascript:member_baro('cancel','{$row->id}','{$row->email}');\"><b><span class=\"font_orange\">OK</span></b></td>\n";
						} else {
							echo "<TD ><a href=\"javascript:member_baro('ok','{$row->id}','{$row->email}');\"><img src=\"images/btn_ok2.gif\" border=\"0\"></td>\n";
						}
					}
					echo "<TD>";
					if($row->member_out!="Y"){
						echo "<a href=\"javascript:member_write('{$row->id}')\"><img src=\"/admin/images/btn_edit.gif\"></a>";
					}else{
						echo "&nbsp;";
					}
					echo "</TD>";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);

				// ERP 접속종료 (김재수)
				GetErpDBClose($oci_conn);
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
				<col width="250">
				<col width="">
				<col width="250">
				</colgroup>
					<tr>
						<td><a href="javascript:check_del()"><img src="images/icon_tal.gif" border="0"></a> <!-- <a href="javascript:check_review_auth('in')"><img src="images/icon_rw_type_in.gif" border="0"></a> <a href="javascript:check_review_auth('out')"><img src="images/icon_rw_type_out.gif" border="0"></a> --></td>
						<td align="center"><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></td>
						<td align=right>
                            <a href="javascript:member_add()"><img src="images/btn_badd2.gif" border="0"></a>
							<a href="javascript:MemberCheckExcel()"><img src="images/btn_excel1.gif" border="0"></a>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">

            <!-- <tr>
				<td background="images/counter_blackline_bg.gif"  class="font_white" align="center" height='40'>
				선택한 회원의 등급을 &nbsp;
				<select name="sel_member_group" class="select">
    				<option value="">=======회원등급변경=======</option>
<?php
    $sql = "select * from tblmembergroup order by group_level asc ";
    $result = pmysql_query($sql);
    while ( $row = pmysql_fetch_object($result) ) {
        echo "<option value=\"{$row->group_code}\">{$row->group_name}</option>";
    }
    pmysql_free_result($result);
?>
				</select> 로 
				&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:ChangeMemberGroup();" style='font-weight:bold;color: #FFDB1A;'>[ 적용하기 ]</a></td>
			</tr> -->

			</form>
			<form name=form2 action="member_reservelist_new.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>
			<form name=form3 method=post>
			<input type=hidden name=id>
			</form>
			<form name=reserveform action="reserve_money_new.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>
			<form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form>
			<form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=number>
			</form>
			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=block value="">
			<input type=hidden name=gotopage value="">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=scheck value="<?=$scheck?>">
			<input type=hidden name=group_code value="<?=$group_code?>">
			<input type=hidden name=vperiod value="<?=$vperiod?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=search value="<?=$search?>">
			</form>
			<form name=actpointform1 action="member_actpointlist.php" method=post target=reserve_info>
			<input type=hidden name=id>
			<input type=hidden name=type>
			</form>

			<form name=actpointform2 action="actpoint_inout.php" method=post>
			<input type=hidden name=type>
			<input type=hidden name=id>
			</form>

			<form name=excel_query action="member_excel_sel_popup.php" method="post">
				<input type=hidden name="excel_sql" value="<?=$mem_excel_sql?>">
				<input type=hidden name="excel_sql_orderby" value="<?=$mem_excel_sql_orderby?>">
				<input type=hidden name=ids>
			</form>

            <form name=crmview method="post" action="crm_view.php">
            <input type=hidden name=id>
            </form>

            <form name=stepform action="member_group_code_state_indb.php" method=post>
			<input type=hidden name=member_codes>
			<input type=hidden name=sel_member_group_code>
			</form>

            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
			
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>검색된 회원목록 클릭시 정보</span></dt>
							<dd>
							- <b>아이디</b> : <span style="letter-spacing:-0.5pt;">이메일, 휴대폰, 주소 등 회원기본정보를 확인할 수 있습니다.</span><br>
							- <b>비번</b> : <span style="letter-spacing:-0.5pt;">운영자라고 하여도 회원의 비밀번호 자체는 변경하지 못하며 대신 임시비밀번호는 발급가능합니다.</span><br>
													&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="letter-spacing:-0.5pt;">(임시비밀번호는 회원가입시 등록한 이메일로 발송됩니다. 이메일이 없을경우 발송되지 않습니다.)</span><br>
							- <b>메일</b> : <span style="letter-spacing:-0.5pt;">회원에게 메일을 발송할 수 있습니다.</span><br>
							- <b>메모</b> : <span style="letter-spacing:-0.5pt;">회원에 대한 메모를 할 수 있습니다.(아이디 입력후 메모가능)</span><br>
							- <b>주소</b> : <span style="letter-spacing:-0.5pt;">집주소를 확인 할 수 있습니다.</span><br>
							- <b>전화</b> : <span style="letter-spacing:-0.5pt;">휴대전화 번호를 확인 할수 있으며 SMS 발송도 가능합니다.(SMS 발송은 SMS머니를 충전 후 이용이 가능합니다.)</span><br>
							- <b>포인트 관리</b> : <span style="letter-spacing:-0.5pt;">운영자 임의로 포인트를 조절할 수 있으며 또한 포인트 내역을 확인할 수 있습니다.</span><br>
							- <b>내역</b> : <span style="letter-spacing:-0.5pt;">구매내역 및 쿠폰 보유내역을 확인 할수 있습니다.(구매내역 정보는 배송처리 완료된 주문건만 출력됩니다.)</span><br>
							- <b>수정</b> : <span style="letter-spacing:-0.5pt;">회원기본정보를 수정할 수 있습니다.</span><br>
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
