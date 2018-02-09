<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include_once($Dir."lib/shopdata.php");
include("calendar.php");
include("access.php");

//exdebug($_REQUEST);

$search_key     = $_REQUEST["search_key"]?$_REQUEST["search_key"]:"id";
$search_keyword = $_REQUEST["id"]?trim($_REQUEST["id"]):trim($_REQUEST["search_keyword"]);
$menu           = $_REQUEST["menu"]?$_REQUEST["menu"]:"home";

$selected[search_key][$search_key] = "selected";

if($search_key == "id") $where .= "And a.id = '".$search_keyword."' ";
else if($search_key == "name") $where .= "And a.name = '".$search_keyword."' ";

//exdebug($subwhere);
### 회원기본정보
$sql = "Select	a.*, b.group_name
        From	tblmember a 
        Join	tblmembergroup b on a.group_code = b.group_code 
        Where	1=1 
        ".$where."
        ";
//exdebug($sql);
$result = pmysql_query($sql, get_db_conn());
$row = pmysql_fetch_object($result);
	
$mem_join_type	= '1';
if ($row->staff_yn == 'Y') $mem_join_type	= '2';
if ($row->cooper_yn == 'Y') $mem_join_type	= '3';

$mem_id = $row->id;
$mem_name = $row->name;
$mem_email = $row->email;
$mem_hp = $row->mobile;
$mem_logindate = substr($row->logindate, 0, 4)."-".substr($row->logindate, 4, 2)."-".substr($row->logindate, 6, 2);
$mem_date = substr($row->date, 0, 4)."-".substr($row->date, 4, 2)."-".substr($row->date, 6, 2);
$mem_group_code = $row->group_code;
$mem_group_name = $row->group_name;
$erp_mem_reserve	= getErpMeberPoint($row->id);
$mem_reserve	= $erp_mem_reserve[p_err_code]==0?$erp_mem_reserve[p_data]:'0';
$mem_home_post = $row->home_post;
$mem_home_addr = str_replace("↑=↑", ", ", $row->home_addr);
$mobile = explode("-",$mem_hp);
$home_tel = explode("-",$row->home_tel);
$home_addr_temp=explode("↑=↑",$row->home_addr);
$home_addr1=$home_addr_temp[0];
$home_addr2=$home_addr_temp[1];
if($row->news_yn == "Y") {
	$news_mail_yn = "Y";
	$news_sms_yn = "Y";
} else if($row->news_yn == "M") {
	$news_mail_yn = "Y";
	$news_sms_yn = "N";
} else if($row->news_yn == "S") {
	$news_mail_yn = "N";
	$news_sms_yn = "Y";
} else if($row->news_yn == "N") {
	$news_mail_yn = "N";
	$news_sms_yn = "N";
}
$mem_birth = $row->birth;
$news_kko_yn = $row->kko_yn;
$mem_height = $row->height;
$mem_weigh = $row->weigh;
$mem_gender = trim($row->gender);
$married_date = substr($row->married_date,0,4)."-".substr($row->married_date,4,2)."-".substr($row->married_date,6,2);
$married_date = strlen($married_date) == 10 ? $married_date : '';

$mem_gdn_name = $row->gdn_name;							// 보호자 이름
$mem_gdn_birth = $row->gdn_birth;							// 보호자 생년월일
$mem_gdn_gender = $row->gdn_gender;							// 보호자 성별
$mem_gdn_mobile = explode("-",$row->gdn_mobile);							// 보호자 핸드폰
$mem_gdn_email = $row->gdn_email;							// 보호자 이메일

$mem_act_point = $row->act_point;							// 활동 포인트
$mem_emp_id = $row->erp_emp_id;							// ERP 임직원 사번
$office_name = $row->office_name;

$sumprice = $row->sumprice; 
$erp_shopid = $row->erp_shopmem_id;

$selected[group][$row->group_code] = "selected";
$selected[mobile][$mobile[0]] = "selected";
$selected[home_tel][$home_tel[0]] = "selected";
$selected[gdn_mobile][$mem_gdn_mobile[0]] = "selected";
$selected[job_code][$row->job_code]="selected";
// 20170825 수정
$selected[office][$row->company_code]="selected";

$auth_type		= $row->auth_type;			// 인증수단

if($auth_type == 'ipin') {
	$mem_auth_type	= "아이핀 인증";
} else if($auth_type == 'mobile') {
	$mem_auth_type	= "휴대폰 인증";
} else if($auth_type == 'sns') {
	$mem_auth_type	= "SNS 간편인증";
} else if($auth_type == 'adm') {
	$mem_auth_type	= "관리자 인증";
} else {
	$mem_auth_type	= "-";
}

//스테프관련 추가 (2016.05.10 - 김재수)
$staff_yn			= $row->staff_yn;			// 임직원유무
$cooper_yn		= $row->cooper_yn;			// 제휴업체유무
$mem_staff_yn	= "일반회원";
if($staff_yn == 'Y') $mem_staff_yn				= "임직원";
if($cooper_yn == 'Y') $mem_staff_yn				= "제휴사";  //20170825 수정
$staff_reserve	= $row->staff_reserve;		// 임직원적립금
$cooper_reserve	= $row->cooper_reserve;		// 제휴사적립금 //20170901

pmysql_free_result($result);


## 총주문금액 tot_ord_price, 누적주문건수 tot_ord_cnt (전체 기준)
$sql = "select count(*) tot_cnt, sum(z.price) tot_price, sum(z.deli_price) tot_deli_price, sum(z.dc_price::int) tot_dc_price, sum(z.reserve) tot_reserve 
        from 
        (
            SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
            min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
            min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
            FROM tblorderinfo a 
            join tblorderproduct b on a.ordercode = b.ordercode 
            join tblproductbrand v on b.vender = v.vender 
            WHERE 1=1 
            AND a.id = '".$mem_id."' 
            GROUP BY a.ordercode 
        ) z 
        ";
list($tot_ord_cnt, $tot_ord_price, $tot_ord_deli_price, $tot_ord_dc_price, $tot_ord_reserve) = pmysql_fetch($sql);

## 총실결제 금액(입금완료기준) tot_pay_price
$sql = "select count(*) tot_cnt, sum(z.price) tot_price, sum(z.deli_price) tot_deli_price, sum(z.dc_price::int) tot_dc_price, sum(z.reserve) tot_reserve 
        from 
        (
            SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
            min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
            min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
            FROM tblorderinfo a 
            join tblorderproduct b on a.ordercode = b.ordercode 
            join tblproductbrand v on b.vender = v.vender 
            WHERE 1=1 
            AND a.id = '".$mem_id."' 
            AND a.oi_step1 > 0
            GROUP BY a.ordercode 
        ) z 
        ";
list($tot_pay_cnt, $tot_pay_price, $tot_pay_deli_price, $tot_pay_dc_price, $tot_pay_reserve) = pmysql_fetch($sql);

## 취소 주문건수(입금전 취소완료 + 환불완료 주문건수) tot_cancel_cnt
$sql = "select count(*) tot_cancel_cnt, sum(z.price) tot_price, sum(z.deli_price) tot_deli_price, sum(z.dc_price::int) tot_dc_price, sum(z.reserve) tot_reserve 
        from 
        (
            SELECT a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
            min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
            min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
            FROM tblorderinfo a 
            join tblorderproduct b on a.ordercode = b.ordercode 
            join tblproductbrand v on b.vender = v.vender 
            WHERE 1=1 
            AND a.id = '".$mem_id."' 
            AND ( (a.oi_step1 = 0 And a.oi_step2 = 44) OR (a.oi_step1 > 0 And b.op_step = 44 And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = ''))) )
            GROUP BY a.ordercode 
        ) z
        ";
list($tot_cancel_cnt, $tot_cancel_price, $tot_cancel_deli_price, $tot_cancel_dc_price, $tot_cancel_reserve) = pmysql_fetch($sql);

## 쿠폰수 (유효한 쿠폰수)
$sql = "select count(*) from tblcouponissue where id = '".$mem_id."' and used = 'N' and (date_start >= '".date("Ymd")."' or date_end <= '".date("Ymd")."')";
list($cnt_coupon) = pmysql_fetch($sql);

## 게시글수 (1:1, 상품Q&A, 상품리뷰)
$sql = "select count(a.*) 
        from 
        (
            select  bd.board, bd.title, bd.mem_id, to_char(to_timestamp(bd.writetime), 'YYYYMMDDHH24MISS') as regdt, 0 as marks, p.productcode  
            from    tblboard bd 
            join    tblproduct p on bd.pridx = p.pridx 
            where   bd.board = 'qna' and bd.mem_id = '".$mem_id."' 
            union all
            select  '1:1' as board, subject as title, id as mem_id, date as regdt, 0 as marks, productcode from tblpersonal where id = '".$mem_id."' 
            union all
            select  'review' as board, subject as title, id as mem_id, date as regdt, marks, productcode from tblproductreview where id = '".$mem_id."' 
        ) a 
        left join tblproduct b on a.productcode = b.productcode 
        ";
list($cnt_bbs) = pmysql_fetch($sql);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>CRM 보기</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="stylesheet" href="static/css/crm.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script src="../js/jquery-1.10.1.min.js" type="text/javascript"></script>
<script src="static/js/crm_ui.js" type="text/javascript"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<SCRIPT LANGUAGE="javascript">
<!--
function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 16;
	var oHeight = document.all.table_body.clientHeight + 66;
	
	window.resizeTo(oWidth,oHeight);
}

function go_menu(menu) {

    document.frm1.menu.value = menu;
    document.frm1.action = "crm_view.php";
    document.frm1.submit();
    
}

function MemberMail(mail,news_yn){
	if(news_yn!="Y" && news_yn!="M" && !confirm("해당 회원은 메일수신을 거부하였습니다.\n\n메일을 발송하시려면 확인 버튼을 클릭하시기 바랍니다.")) {
		return;
	}
	document.mailform.rmail.value=mail;
	document.mailform.submit();
}

function SendSMS(tel1,tel2,tel3) {
    //alert(tel1);
	//number=tel1+"|"+tel2+"|"+tel3;
	number=tel1;
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

function OrderDetailView(ordercode) {
    document.detailform.ordercode.value = ordercode;
    window.open("","orderdetail","scrollbars=yes,width=700,height=600,resizable=yes");
    document.detailform.submit();
}

function LostPass(id) {
	window.open("about:blank","lostpasspop","width=350,height=200,scrollbars=no");
	document.form3.target="lostpasspop";
	document.form3.id.value=id;
	document.form3.action="member_lostpasspop_new.php";
	document.form3.submit();
}

function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
            document.getElementById('home_zonecode').value = data.zonecode;
			document.getElementById('home_addr1').value = data.address;
			document.getElementById('home_addr2').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
}
//-->
</SCRIPT>
<style type="text/css">
pre {display:none;}
</style>
</head>
<!--body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();"-->
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow:hidden;" onLoad="PageResize();">

<table border=0 cellpadding=0 cellspacing=0 style="table-layout:fixed;" id=table_body>
<tr>
    <td width=100%>


		<div id="crm-wrap">

			<div class="crm-header">
				<a href="#" class="logo"><span><img src="img/common/admin_logo.png" border="0"></span></a>
				<div class="header-find">
				<form name=frm1>
                <input type=hidden name="menu">
					<fieldset>
						<legend>회원검색</legend>
						<select name="search_key">
							<option value="id" <?=$selected[search_key]["id"]?>>아이디</option>
							<option value="name" <?=$selected[search_key]["name"]?>>이름</option>
						</select>
						<input type="text" name="search_keyword" value="<?=$search_keyword?>">
						<button class="btn-function search" type="submit"><span>검색</span></button>
					</fieldset>
				</form>
				</div>
			</div>

		</div><!-- //#crm-wrap -->

		<div id="crm-container">
		
			<div class="lnb-wrap">
				<p class="name"><?=$mem_name?>(<?=$mem_id?>)<!-- <a href="#">M</a> --></p>
				<div class="inner">
					<div class="quick-message">
						<p class="grade">등급 : <strong><?=$mem_group_name?></strong></p>
						<ul class="mrk-icon">
							<li><a href="javascript:SendSMS('<?=str_replace("-", "", $mem_hp)?>')"><img src="static/img/icon/icon_marketing_phone.gif" alt=""></a></li>
							<li><a href="javascript:MemberMail('<?=$mem_email?>','<?=$news_mail_yn?>');"><img src="static/img/icon/icon_marketing_mail.gif" alt=""></a></li>
							<li><a href="javascript:MemberMemo2('<?=$mem_id?>')"><img src="static/img/icon/icon_marketing_memo.gif" alt=""></a></li>
						</ul>
					</div>
					<ul class="visit-date">
						<li>최종방문일 : <?=$mem_logindate?></li>
						<li>가입일 : <?=$mem_date?></li>
					</ul>
					<ul class="nav-menu">
						<li <?if($menu=="home") {?>class="on"<?}?>><a href="javascript:go_menu('home');">CRM홈</a></li>
						<li <?if($menu=="mem_list") {?>class="on"<?}?>><a href="javascript:go_menu('mem_list');">회원상세정보</a></li>
						<li <?if($menu=="order") {?>class="on"<?}?>><a href="javascript:go_menu('order');">주문내역</a></li>
						<li <?if($menu=="board") {?>class="on"<?}?>><a href="javascript:go_menu('board');">게시글 정보</a></li>
						<li <?if($menu=="mileage") {?>class="on"<?}?>><a href="javascript:go_menu('mileage');">통합포인트 정보</a></li>
						<?if($staff_yn=="Y") {?><li <?if($menu=="mileage_staff") {?>class="on"<?}?>><a href="javascript:go_menu('mileage_staff');">임직원적립금 정보</a></li><?}?>
						<li <?if($menu=="mileage_act") {?>class="on"<?}?>><a href="javascript:go_menu('mileage_act');">E포인트 정보</a></li>
						<li <?if($menu=="coupon") {?>class="on"<?}?>><a href="javascript:go_menu('coupon');">쿠폰 정보</a></li>
						<li <?if($menu=="basket") {?>class="on"<?}?>><a href="javascript:go_menu('basket');">장바구니 정보</a></li>
						<!-- <li <?if($menu=="wish") {?>class="on"<?}?>><a href="javascript:go_menu('wish');">관심상품 정보</a></li> -->
						<li <?if($menu=="sms") {?>class="on"<?}?>><a href="javascript:go_menu('sms');">SMS 발송내역</a></li>
						<!-- <li <?if($menu=="loginlog") {?>class="on"<?}?>><a href="javascript:go_menu('loginlog');">로그인 로그</a></li> -->
						<li <?if($menu=="mem_memo") {?>class="on"<?}?>><a href="javascript:go_menu('mem_memo');">회원 메모</a></li>
						<li <?if($menu=="call_memo") {?>class="on"<?}?>><a href="javascript:go_menu('call_memo');">전화상담 메모</a></li>
					</ul>
                    <!-- 
                    총주문금액 : 전체 주문금액
                    총 실결제 금액 : 실제 입금완료 한 주문금액
                    누적 주문건수 : 총 주문금액의 주문건수
                    취소 주문건수 : 입금전 취소완료 + 환불완료 주문건수
                     -->
					<dl class="quick-info-box">
						<dt>주문정보</dt>
						<dd>총 주문금액<strong><?=number_format($tot_ord_price)?></strong></dd>
						<dd>총 실결제금액<strong><?=number_format($tot_pay_price)?></strong></dd>
						<dd>누적 주문건수<strong><?=number_format($tot_ord_cnt)?></strong></dd>
						<dd>취소 주문건수<strong><?=number_format($tot_cancel_cnt)?></strong></dd>
					</dl>
					<dl class="quick-info-box">
						<dt>상담</dt>
						<dd>게시물<strong><?=number_format($cnt_bbs)?></strong></dd>
					</dl>
					<dl class="quick-info-box">
						<!--dt>적립금/쿠폰</dt-->
						<dt>포인트/쿠폰</dt>
						<dd>통합포인트<strong><?=number_format($mem_reserve)?></strong></dd>
						<?if($staff_yn=="Y") {?>
						<dd>임직원적립금<strong><?=number_format($staff_reserve)?></strong></dd>
						<?}else if($cooper_yn=="Y"){?>
<!--						<dd>제휴사적립금<strong><?=number_format($cooper_reserve)?></strong></dd>-->
						<?}?>
						<dd>E포인트<strong><?=number_format($mem_act_point)?></strong></dd>
						<dd>쿠폰<strong><?=number_format($cnt_coupon)?></strong></dd>
					</dl>
				</div>
			</div><!-- //.lnb-wrap -->

<?
if($menu == "home") include "./crm_view_home.php";
else if($menu == "mem_list") include "./crm_view_mem_list.php";
else if($menu == "order") include "./crm_view_order_list.php";
else if($menu == "board") include "./crm_view_board_list.php";
else if($menu == "mileage_staff") include "./crm_view_mileage_staff_list.php";
else if($menu == "mileage") include "./crm_view_mileage_list.php";
else if($menu == "mileage_act") include "./crm_view_mileage_act_list.php";
else if($menu == "coupon") include "./crm_view_coupon_list.php";
else if($menu == "basket") include "./crm_view_basket_list.php";
else if($menu == "wish") include "./crm_view_wish_list.php";
else if($menu == "sms") include "./crm_view_sms_list.php";
else if($menu == "loginlog") include "./crm_view_loginlog_list.php";
else if($menu == "mem_memo") include "./crm_view_mem_memo_list.php";
else if($menu == "call_memo") include "./crm_view_call_memo_list.php";
?>

		</div><!-- //#crm-container -->


    </td>
</tr>
</table>

<form name=mailform action="member_mailsend.php" method=post target="_blank">
<input type=hidden name=rmail>
</form>

<form name=smsform action="sendsms.php" method=post target="sendsmspop">
<input type=hidden name=number>
</form>

<form name=form3 method=post>
<input type=hidden name=id>
</form>

<form name=detailform method="post" action="order_detail.php" target="orderdetail">
<input type=hidden name=ordercode>
</form>

<?=$onload?>
</body>
</html>