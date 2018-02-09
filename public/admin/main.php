<?php // hspark
header("Location: order_list_all.php");   // 전체주문조회(주문별)로 이동

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if($bannertype == "left" || $bannertype == "right") {
	echo "
	<html>
	<head>
	<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
	<title></title>
	<script type=\"text/javascript\" src=\"lib.js.php\"></script>
	<script>var LH = new LH_create();</script>
	<script for=window event=onload>LH.exec();</script>
	<script>LH.add(\"parent_resizeIframe('Banner{$bannertype}Frame')\");</script>
	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>
	<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"http://www.incomuad.com/gadbanner/gadbanner{$bannertype}.php?sellerid=".$_ShopInfo->getSellerid()."\"></SCRIPT>
	</body>
	</html>
	";
	exit;
}

include("access.php");
$curdate = date("Ymd");
include("header.php");

?>
<script>try {parent.topframe.ChangeMenuImg(0);}catch(e){}</script>
<style>td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--

//######################################################################################################
//공지사항
function shop_noticeview(type,code) {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

//전체흐름도
function shop_process() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

//메뉴얼
function shop_menual() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

//쇼핑몰 TIP&양식
function shop_tip() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

//제안 및 불편사항 신고
function shop_report() {
	alert("죄송합니다. 잠시 후 이용하시기 바랍니다.");
}

//벤더제한
function not_vender_alert() {
	alert("입점기능 및 미니샵은 몰인몰(E-market) 버전에서만 사용하실 수 있습니다.");
}
//######################################################################################################


function sms_fill() {
	parent.topframe.GoMenu(7,"market_smsfill.php");
}

function ViewPersonal(idx) {
	window.open("about:blank","personal_pop","width=600,height=550,scrollbars=yes");
	document.perform.idx.value=idx;
	document.perform.submit();
}

function ReviewReply(date,prcode) {
	window.open("about:blank","reply","width=400,height=500,scrollbars=no");
	document.reviewform.target="reply";
	document.reviewform.date.value=date;
	document.reviewform.productcode.value=prcode;
	document.reviewform.submit();
}

function ProductInfo(code,prcode,popup) {
	document.prform.code.value=code;
	document.prform.prcode.value=prcode;
	document.prform.popup.value=popup;
	if (popup=="YES") {
		document.prform.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
		document.prform.action="product_register.set.php";
	} else {
		document.prform.target="_parent";
		document.prform.action="product_register.php";
	}
	document.prform.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
	document.detailform.submit();
}

function NoticeSend(type,date) {
	if(type=="delete") {
		if(!confirm("해당 공지사항을 삭제하시겠습니까?")) return;
	}
	document.noticeform.type.value=type;
	document.noticeform.date.value=date;
	document.noticeform.submit();
}

function div_change(type){
	document.getElementById(type+'_class').className='this';
	document.getElementById(type+'_div').style.display="block";
	if(type=='qna'){ document.getElementById('personal_div').style.display="none";document.getElementById('personal_class').className='';document.getElementById('over_href').href='/admin/community_article.php';}
	else{ document.getElementById('qna_div').style.display="none";document.getElementById('qna_class').className='';document.getElementById('over_href').href='/admin/community_personal.php';}

}

//-->
</SCRIPT>

<script type="text/javascript" src="http://<?=_SellerUrl?>/incomushop/global.js"></script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">

<!-- main wrap -->
<div class="admin_main_wrap">

<table cellpadding="0" cellspacing="0" width="983" style="table-layout:fixed">
<col width=3></col>
<col width=></col>
<tr>
	<td valign="top"><img src="images/space01.gif" height="1" border="0" width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" border=0 width=100%>
	<tr><td height=10></td></tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=203></col>
		<col width=4></col>
		<col width=577></col>
		<col width=3></col>
		<col width=193></col>
		<tr>
			<td valign="top">
			<!--######################## 왼쪽 시작 ########################-->

			<table cellpadding="0" cellspacing="0" width="203">
			<tr>
				<td >

				<!-- 어드민 기본 정보 -->
				<div class="admin_left_info_wrap">

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<div class="version_font">
							<img src="img/common/admin_left_tit_admin.gif" alt="" /><br />
							<span>Version <?=_IncomuShopVersionNo?>(<?=_IncomuShopVersionDate?>)</span>
						</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/admin_left_con_top.gif" alt="" /></td>
				</tr>
				<tr>
					<td background="img/common/admin_left_con_midd.gif">
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD ROWSPAN=2 ></TD>
						<TD valign="top" style="padding-bottom:4pt;">
						<table border=0 cellpadding="0" cellspacing="0" width="203">
						<tr>
							<td align=center valign=top >

<?php
							################# 등록 상품수 #########################
							$sql = "SELECT COUNT(*) as totproduct FROM tblproduct ";
							$result=pmysql_query($sql,get_db_conn());
							$row=pmysql_fetch_object($result);
							$totproduct=(int)$row->totproduct;
							pmysql_free_result($result);

							################# 입점기능 #################
							$vender_used = setVenderUsed();

							################# PG셋팅 ###################
							$pg_used="";
							if($f=@file(DirPath.AuthkeyDir."pg")) {
								$pg_used="<a style=\"cursor:hand\" onclick=\"alert('인증키 디렉토리에 PG셋팅 키가 존재합니다.')\"><font class=\"font_orange4\">셋팅완료</font></a>";
							} else {
								$pg_used="<a style=\"cursor:hand\" onclick=\"alert('인증키 디렉토리에 PG셋팅 키가 존재하지 않습니다.')\"><font color=red>미셋팅</font></a>";
							}
?>

							<table border=0 cellpadding=0 cellspacing=0 width=88% class="pl_10">
							<col width=50></col>
							<col width=10></col>
							<col width=></col>

							<?php if(strlen(_ExpireDate)==8){?>
							<!--tr>
								<td class="font_size">사용만료</td>
								<td class="font_size">:</td>
								<td class="font_size"><span class="font_orange4"><?=substr(_ExpireDate,0,4)."/".substr(_ExpireDate,4,2)."/".substr(_ExpireDate,6,2)?></span></td>
							</tr-->
							<?php }?>

							<tr>
								<td class="font_size" colspan=2>FTP 용량</td>
								<td class="font_size" align=right><b>57</b>MB / 200MB</td>
							</tr>
							<tr>
								<td colspan=3>
									<div class="ftp_gauge"><?for($i=0;$i<100;$i++){?><img src="img/common/ftp_gauge.gif" alt="" /><?}?></div>
								</td>
							</tr>
							<tr>
								<td colspan=3 class="font_size">기간 : 2013.04.24~2015.04.20</td>
							</tr>
							<tr>
								<td class="font_size">등록상품</td>
								<td class="font_size">:</td>
								<td class="font_size"><b><span class="font_orange4"><?=$totproduct?></span></b>개 등록</td>
							</tr>
							<tr>
								<td class="font_size">입점기능</td>
								<td class="font_size">:</td>
								<td class="font_size"><?=$vender_used?></td>
							</tr>
							<tr>
								<td class="font_size">PG 셋팅</td>
								<td class="font_size">:</td>
								<td class="font_size"><?=$pg_used?></td>
							</tr>
							<!--tr>
								<td class="font_size">SMS잔여</td>
								<td class="font_size">:</td>
								<td id="idx_sms" class="font_size">읽는중...</td>
							</tr-->

							</table>

							</td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/admin_left_con_bottom.gif" alt="" /></td>
				</tr>
				</table>

				</div>
				<!-- 어드민 기본 정보 -->

				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<?php /*?>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><IMG SRC="images/main_left_start_title.gif" WIDTH="190" HEIGHT=28 ALT=""></td>
				</tr>
				<tr>
					<td align=center background="images/main_left_start_bg.gif" style="padding-top:8pt; padding-bottom:3pt;"><a href="javascript:void(0)" onclick="shop_process()"><IMG SRC="images/main_left_start_btn1.gif" WIDTH=81 HEIGHT=23 ALT="" border="0"></a><a href="javascript:void(0)" onclick="shop_menual()"><IMG SRC="images/main_left_start_btn2.gif" WIDTH=79 HEIGHT=23 ALT="" hspace="3" border="0"></a></td>
				</tr>
				<tr>
					<td><IMG SRC="images/main_left_start_downimg.gif" WIDTH="190" HEIGHT=5 ALT=""></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height=12></td>
			</tr>
			<?php */?>
			<tr>
				<td background="img/common/left_tit_function_midd.gif">
				<!--@ left @-->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="img/common/left_tit_function01.gif"></td>
				</tr>
				<tr>
					<td>
						<div class="left_function_table">
						<table width="183" border="0" cellspacing="0" cellpadding="0" align="center" >
						<!--tr>
							<td><a href="product_assemble.php">&bull; 코디/조립상품</a></td>
							<td><a href="product_option.php">&bull; 옵션그룹 기능</a></td>
						</tr>
						<tr>
							<td><a href="product_package.php">&bull; 패키지 상품</a></td>
							<td><a href="product_business.php">&bull; 거래처 관리</a></td>
						</tr>
						<tr>
							<td><a href="product_brand.php">&bull; 브랜드 기능</a></td>
							<td><a href="product_excelupload.php">&bull; 상품 일괄등록</a></td>
						</tr>
						<tr>
							<td><a href="product_estimate.php">&bull; 견적서 기능</a></td>
							<td><a href="product_allupdate.php">&bull; 상품 간편수정</a></td>
						</tr-->
						<tr>
							<td><a href="product_brandlist.php">&bull; 브랜드 기능</a></td>
							<td><a href="product_excelupload.php">&bull; 상품 일괄등록</a></td>
						</tr>
						</table>
						</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/left_tit_function02.gif"></td>
				</tr>
				<tr>
					<td>
						<div class="left_function_table">
						<table width="183" border="0" cellspacing="0" cellpadding="0" align="center">
						<tr>
							<td><?=(setUseVender()?"<a href=\"vender_new2.php\">":"<a href=\"javascript:not_vender_alert();\">")?>&bull; 입점업체 등록</a></td>
						</tr>
						<tr>
							<td><?=(setUseVender()?"<a href=\"vender_management2.php\">":"<a href=\"javascript:not_vender_alert();\">")?>&bull; 입점업체 관리</a></td>
						</tr>
						<!-- tr>
							<td><?=(setUseVender()?"<a href=\"vender_orderlist.php\">":"<a href=\"javascript:not_vender_alert();\">")?>&bull; 입점업체 주문조회</a></td>
						</tr>
						<tr>
							<td><?=(setUseVender()?"<a href=\"vender_orderadjust.php\">":"<a href=\"javascript:not_vender_alert();\">")?>&bull; 입점업체 정산관리</a></td>
						</tr -->
						</table>
						</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/left_tit_function03.gif"></td>
				</tr>
				<tr>
					<td>
						<div class="left_function_table">
						<table width="183" border="0" cellspacing="0" cellpadding="0" align="center">
						<tr>
							<td><a href="order_basket.php">&bull; 장바구니 상품분석</a></td>
						</tr>
						<tr>
							<td><a href="order_allsale.php">&bull; 전체상품 매출분석</a></td>
						</tr>
						<tr>
							<td><a href="counter_prsearchprefer.php">&bull; 상품 검색 선호도</a></td>
						</tr>
						<tr>
							<td><a href="counter_searchkeywordrank.php">&bull; 검색엔진 검색어 순위</a></td>
						</tr>
						<!-- tr>
							<td><a href="market_partner.php">&bull; 제휴마케팅 관리</a></td>
						</tr -->
						</table>
						</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/left_tit_function04.gif"></td>
				</tr>
				<tr>
					<td>
						<div class="left_function_table">
						<table width="183" border="0" cellspacing="0" cellpadding="0" align="center">
						<tr>
							<td><a href="shop_search_tag.php">&bull; 상품태그 관련 기능설정</a></td>
						</tr>
						<!--tr>
							<td><a href="shop_tag.php">&bull; tag 기능</a></td>
						</tr>
						<tr>
							<td><a href="shop_productshow.php">&bull; 퀵툴스 (새창 미리보기 메뉴)</a></td>
						</tr>
						<tr>
							<td><a href="shop_reserve.php">&bull; 적립금/쿠폰 설정</a></td>
						</tr -->
						<tr>
							<td><a href="market_couponnew.php">&bull; 쿠폰기능</a></td>
						</tr>
						<!-- tr>
							<td><a href="product_giftlist.php">&bull; 사은품 기능</a></td>
						</tr -->
						</table>
						</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/left_tit_function_bottom.gif"></td>
				</tr>
				</table>

				<!--@ left @-->
				</td>
			</tr>
			</table>

			<!-- left 배너 추가 -->

			<!--######################## 왼쪽 끝 ########################-->
			</td>

			<!--######################## 공백 6px ########################-->
			<td width="6" valign="top"><img src="images/space01.gif" width="6" height="1" border="0"></td>
			<!--##########################################################-->

			<td valign="top">
			<!--######################## 가운데 시작 ########################-->

			<!-- 현황 -->


			<table cellpadding="0" cellspacing="0" width="100%">
<?php
			$curdate_1 = date("Ymd",strtotime('-1 day'));
			$curdate_2 = date("Ymd",strtotime('-2 day'));
			$curdate_3 = date("Ymd",strtotime('-3 day'));
			$curdate_4 = date("Ymd",strtotime('-4 day'));

			$sql = "SELECT
			COUNT(CASE WHEN date LIKE '{$curdate}%' THEN 1 ELSE NULL END) as totmemcnt,
			COUNT(CASE WHEN date LIKE '{$curdate_1}%' THEN 1 ELSE NULL END) as totmemcnt1,
			COUNT(CASE WHEN date LIKE '{$curdate_2}%' THEN 1 ELSE NULL END) as totmemcnt2,
			COUNT(CASE WHEN date LIKE '{$curdate_3}%' THEN 1 ELSE NULL END) as totmemcnt3,
			COUNT(CASE WHEN date LIKE '{$curdate_4}%' THEN 1 ELSE NULL END) as totmemcnt4,
			COUNT(CASE WHEN date LIKE '".substr($curdate,0,6)."%' THEN 1 ELSE NULL END) as totmonmemcnt
			FROM tblmember WHERE 1=1 ";
			if(substr($curdate,0,6)!=substr($curdate_4,0,6)) {
				$sql.="AND (date LIKE '".substr($curdate,0,6)."%' OR date LIKE '{$curdate_1}%' OR date LIKE '{$curdate_2}%' OR date LIKE '{$curdate_3}%' OR date LIKE '{$curdate_4}%') ";
			} else {
				$sql.="AND date LIKE '".substr($curdate,0,6)."%' ";
			}

			$filename="admin.main.member.cache";
			//get_db_cache($sql, $resval, $filename, 60);
			//$row=$resval[0];
			$res=pmysql_query($sql);
			$row=pmysql_fetch_object($res);

			$totmemcnt=(int)$row->totmemcnt;		//오늘 회원가입수
			$totmemcnt1=(int)$row->totmemcnt1;		//1일전 회원가입수
			$totmemcnt2=(int)$row->totmemcnt2;		//2일전 회원가입수
			$totmemcnt3=(int)$row->totmemcnt3;		//3일전 회원가입수
			$totmemcnt4=(int)$row->totmemcnt4;		//4일전 회원가입수
			$totmonmemcnt=(int)$row->totmonmemcnt;	//이달 회원가입수

			$sql = "SELECT
			COUNT(CASE WHEN to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate}' THEN 1 ELSE NULL END) as totbrdcnt,
			COUNT(CASE WHEN to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_1}' THEN 1 ELSE NULL END) as totbrdcnt1,
			COUNT(CASE WHEN to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_2}' THEN 1 ELSE NULL END) as totbrdcnt2,
			COUNT(CASE WHEN to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_3}' THEN 1 ELSE NULL END) as totbrdcnt3,
			COUNT(CASE WHEN to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_4}' THEN 1 ELSE NULL END) as totbrdcnt4,
			COUNT(CASE WHEN to_char(to_timestamp(writetime),'YYYYMM')='".substr($curdate,0,6)."' THEN 1 ELSE NULL END) as totmonbrdcnt
			FROM tblboard WHERE 1=1 ";
			if(substr($curdate,0,6)!=substr($curdate_4,0,6)) {
				$sql.="AND (to_char(to_timestamp(writetime),'YYYYMM')='".substr($curdate,0,6)."' OR to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_1}' OR to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_2}' OR to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_3}' OR to_char(to_timestamp(writetime),'YYYYMMDD')='{$curdate_4}') ";
			} else {
				$sql.="AND to_char(to_timestamp(writetime),'YYYYMM')='".substr($curdate,0,6)."' ";
			}
			$filename="admin.main.board.cache";

			//get_db_cache($sql, $resval, $filename, 60);
			//$row=$resval[0];
			$res=pmysql_query($sql);
			$row=pmysql_fetch_object($res);

			$totbrdcnt=(int)$row->totbrdcnt;		//오늘 등록된 게시물수
			$totbrdcnt1=(int)$row->totbrdcnt1;		//1일전 등록된 게시물수
			$totbrdcnt2=(int)$row->totbrdcnt2;		//2일전 등록된 게시물수
			$totbrdcnt3=(int)$row->totbrdcnt3;		//3일전 등록된 게시물수
			$totbrdcnt4=(int)$row->totbrdcnt4;		//4일전 등록된 게시물수
			$totmonbrdcnt=(int)$row->totmonbrdcnt;	//이달 등록된 게시물수



/*
			$sql = "SELECT
			SUM(CASE WHEN date LIKE '{$curdate}%' THEN count ELSE NULL END) as totvstcnt,
			SUM(CASE WHEN date LIKE '{$curdate_1}%' THEN count ELSE NULL END) as totvstcnt1,
			SUM(CASE WHEN date LIKE '{$curdate_2}%' THEN count ELSE NULL END) as totvstcnt2,
			SUM(CASE WHEN date LIKE '{$curdate_3}%' THEN count ELSE NULL END) as totvstcnt3,
			SUM(CASE WHEN date LIKE '{$curdate_4}%' THEN count ELSE NULL END) as totvstcnt4,
			SUM(CASE WHEN date LIKE '".substr($curdate,0,6)."%' THEN count ELSE NULL END) as totmonvstcnt
			FROM tblshopcountday WHERE 1=1 ";
			if(substr($curdate,0,6)!=substr($curdate_4,0,6)) {
				$sql.="AND (date LIKE '".substr($curdate,0,6)."%' OR date LIKE '{$curdate_1}%' OR date LIKE '{$curdate_2}%' OR date LIKE '{$curdate_3}%' OR date LIKE '{$curdate_4}%') ";
			} else {
				$sql.="AND date LIKE '".substr($curdate,0,6)."%' ";
			}
			$filename="admin.main.count.cache";
			get_db_cache($sql, $resval, $filename, 60);
			$row=$resval[0];

			$totvstcnt=(int)$row->totvstcnt;		//오늘 방문자수
			$totvstcnt1=(int)$row->totvstcnt1;		//1일전 방문자수
			$totvstcnt2=(int)$row->totvstcnt2;		//2일전 방문자수
			$totvstcnt3=(int)$row->totvstcnt3;		//3일전 방문자수
			$totvstcnt4=(int)$row->totvstcnt4;		//4일전 방문자수
			$totmonvstcnt=(int)$row->totmonvstcnt;	//이달 방문자수
*/

			$sql="SELECT SUBSTR(date,7,2) as day,sum(cnt) as cnt FROM tblcounter
			WHERE date LIKE '".substr($curdate,0,6)."%' GROUP BY day order by day desc limit 5";
			$res=pmysql_query($sql);
			$totvstcnt_arr=array();
			$totvstcnt_tot=0;
			while($row=pmysql_fetch_array($res)){
				$totvstcnt_arr[]=$row['cnt'];
			}

			$totvstcnt=(int)$totvstcnt_arr[0];		//오늘 방문자수
			$totvstcnt1=(int)$totvstcnt_arr[1];		//1일전 방문자수
			$totvstcnt2=(int)$totvstcnt_arr[2];		//2일전 방문자수
			$totvstcnt3=(int)$totvstcnt_arr[3];		//3일전 방문자수
			$totvstcnt4=(int)$totvstcnt_arr[4];		//4일전 방문자수

			list($target_date,$totmonvstcnt)=pmysql_fetch("SELECT SUBSTR(date,1,6) as day,sum(cnt) as cnt FROM tblcounter
			WHERE date LIKE '".substr($curdate,0,6)."%' GROUP BY day");//이달 방문자수


			$sql = "SELECT ";
			//오늘 주문건수 및 주문금액
			$sql.= "COUNT(CASE WHEN ordercode LIKE '{$curdate}%' THEN 1 ELSE NULL END) as totordcnt, ";
			$sql.= "SUM(CASE WHEN ordercode LIKE '{$curdate}%' THEN price ELSE 0 END) as totordprice, ";
			//오늘 미배송 건수 및 미배송건 금액
			$sql.= "COUNT(CASE WHEN (ordercode LIKE '{$curdate}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE NULL END) as totdelaycnt, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '{$curdate}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE 0 END) as totdelayprice, ";
			//1일전 주문건수 및 주문금액
			$sql.= "COUNT(CASE WHEN ordercode LIKE '{$curdate_1}%' THEN 1 ELSE NULL END) as totordcnt1, ";
			$sql.= "SUM(CASE WHEN ordercode LIKE '{$curdate_1}%' THEN price ELSE 0 END) as totordprice1, ";
			//1일전 미배송 건수 및 미배송건 금액
			$sql.= "COUNT(CASE WHEN (ordercode LIKE '{$curdate_1}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE NULL END) as totdelaycnt1, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '{$curdate_1}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE 0 END) as totdelayprice1, ";
			//2일전 주문건수 및 주문금액
			$sql.= "COUNT(CASE WHEN ordercode LIKE '{$curdate_2}%' THEN 1 ELSE NULL END) as totordcnt2, ";
			$sql.= "SUM(CASE WHEN ordercode LIKE '{$curdate_2}%' THEN price ELSE 0 END) as totordprice2, ";
			//2일전 미배송 건수 및 미배송건 금액
			$sql.= "COUNT(CASE WHEN (ordercode LIKE '{$curdate_2}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE NULL END) as totdelaycnt2, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '{$curdate_2}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE 0 END) as totdelayprice2, ";
			//3일전 주문건수 및 주문금액
			$sql.= "COUNT(CASE WHEN ordercode LIKE '{$curdate_3}%' THEN 1 ELSE NULL END) as totordcnt3, ";
			$sql.= "SUM(CASE WHEN ordercode LIKE '{$curdate_3}%' THEN price ELSE 0 END) as totordprice3, ";
			//3일전 미배송 건수 및 미배송건 금액
			$sql.= "COUNT(CASE WHEN (ordercode LIKE '{$curdate_3}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE NULL END) as totdelaycnt3, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '{$curdate_3}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE 0 END) as totdelayprice3, ";
			//4일전 주문건수 및 주문금액
			$sql.= "COUNT(CASE WHEN ordercode LIKE '{$curdate_4}%' THEN 1 ELSE NULL END) as totordcnt4, ";
			$sql.= "SUM(CASE WHEN ordercode LIKE '{$curdate_4}%' THEN price ELSE 0 END) as totordprice4, ";
			//4일전 미배송 건수 및 미배송건 금액
			$sql.= "COUNT(CASE WHEN (ordercode LIKE '{$curdate_4}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE NULL END) as totdelaycnt4, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '{$curdate_4}%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE 0 END) as totdelayprice4, ";
			//이달 주문건수 및 매출
			$sql.= "COUNT(CASE WHEN ordercode LIKE '".substr($curdate,0,6)."%' THEN 1 ELSE NULL END) as totmonordcnt, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '".substr($curdate,0,6)."%' AND deli_gbn='Y') THEN price ELSE 0 END) as totmonordprice, ";
			//이달 미배송 건수 및 미배송건 금액
			$sql.= "COUNT(CASE WHEN (ordercode LIKE '".substr($curdate,0,6)."%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE NULL END) as totmondelaycnt, ";
			$sql.= "SUM(CASE WHEN (ordercode LIKE '".substr($curdate,0,6)."%') AND (deli_gbn IN('N','X','S')) THEN 1 ELSE 0 END) as totmondelayprice ";
			$sql.= "FROM tblorderinfo WHERE 1=1 ";
			if(substr($curdate,0,6)!=substr($curdate_4,0,6)) {
				$sql.="AND (ordercode LIKE '".substr($curdate,0,6)."%' OR ordercode LIKE '{$curdate_1}%' OR ordercode LIKE '{$curdate_2}%' OR ordercode LIKE '{$curdate_3}%' OR ordercode LIKE '{$curdate_4}%') ";
			} else {
				$sql.="AND ordercode LIKE '".substr($curdate,0,6)."%' ";
			}
			$filename="admin.main.order.cache";


			//get_db_cache($sql, $resval, $filename, 60);
			//$row=$resval[0];
			$res=pmysql_query($sql);
			$row=pmysql_fetch_object($res);

			$totordcnt=(int)$row->totordcnt;			//오늘 주문건수
			$totordprice=(int)$row->totordprice;		//오늘 주문금액
			$totdelaycnt=(int)$row->totdelaycnt;		//오늘 미배송건수
			$totdelayprice=(int)$row->totdelayprice;	//오늘 미배송금액

			$totordcnt1=(int)$row->totordcnt1;			//1일전 주문건수
			$totordprice1=(int)$row->totordprice1;		//1일전 주문금액
			$totdelaycnt1=(int)$row->totdelaycnt1;		//1일전 미배송건수
			$totdelayprice1=(int)$row->totdelayprice1;	//1일전 미배송금액

			$totordcnt2=(int)$row->totordcnt2;			//2일전 주문건수
			$totordprice2=(int)$row->totordprice2;		//2일전 주문금액
			$totdelaycnt2=(int)$row->totdelaycnt2;		//2일전 미배송건수
			$totdelayprice2=(int)$row->totdelayprice2;	//2일전 미배송금액

			$totordcnt3=(int)$row->totordcnt3;			//3일전 주문건수
			$totordprice3=(int)$row->totordprice3;		//3일전 주문금액
			$totdelaycnt3=(int)$row->totdelaycnt3;		//3일전 미배송건수
			$totdelayprice3=(int)$row->totdelayprice3;	//3일전 미배송금액

			$totordcnt4=(int)$row->totordcnt4;			//4일전 주문건수
			$totordprice4=(int)$row->totordprice4;		//4일전 주문금액
			$totdelaycnt4=(int)$row->totdelaycnt4;		//4일전 미배송건수
			$totdelayprice4=(int)$row->totdelayprice4;	//4일전 미배송금액

			$totmonordcnt=(int)$row->totmonordcnt;		//이달의 주문건수
			$totmonordprice=(int)$row->totmonordprice;	//이달의 매출
			$totmondelaycnt=(int)$row->totmondelaycnt;	//이달 미배송건수
			$totmondelayprice=(int)$row->totmondelayprice;//이달 미배송금액

			//미승인 사업자
			$confirm_qry="select count(*) as cnt from tblmember where mem_type='1' and confirm_yn!='Y'";
			$confirm_res=pmysql_query($confirm_qry);
			$confirm_row=pmysql_fetch_array($confirm_res);
			pmysql_free_result($confirm_res);


?>

			<tr>
				<td>

				<!-- 현황시작 -->
				<div class="center_view_wrap">



				<!-- 현황 테이블 -->
				<div class="lately_table_wrap">

				<p><img src="img/common/lately_tit_total.gif" alt="최근 쇼핑몰 현황" /></p>
				<div class="lately_table">
				<div class="today_icon"><img src="img/icon/icon_today.gif" alt="TODAY" /></div>
				<table cellpadding="0" cellspacing="0" border=0 width="556" style="table-layout:fixed">
				<colgroup>
					<col width="56" /><col width="100" /><col width="100" /><col width="100" /><col width="100" /><col width="100" />
				</colgroup>
					<tr>
						<th>구분</th>
						<th><?=substr($curdate_4,4,2)."월".substr($curdate_4,6,2)."일"?></th>
						<th><?=substr($curdate_3,4,2)."월".substr($curdate_3,6,2)."일"?></th>
						<th><?=substr($curdate_2,4,2)."월".substr($curdate_2,6,2)."일"?></th>
						<th><?=substr($curdate_1,4,2)."월".substr($curdate_1,6,2)."일"?></th>
						<th class="today"><?=substr($curdate,4,2)."월".substr($curdate,6,2)."일"?></th>
					</tr>
					<tr>
						<td class="list">주문</td>
						<td><p><b><?=$totordcnt4?></b> (<?=number_format($totordprice4)?>)</p></td>
						<td><p><b><?=$totordcnt3?></b> (<?=number_format($totordprice3)?>)</p></td>
						<td><p><b><?=$totordcnt2?></b> (<?=number_format($totordprice2)?>)</p></td>
						<td><p><b><?=$totordcnt1?></b> (<?=number_format($totordprice1)?>)</p></td>
						<td><p><span><?=$totordcnt?></span> (<?=number_format($totordprice)?>)</p></td>
					</tr>
					<tr>
						<td class="list">미배송</td>
						<td><p><b><?=$totdelayprice4?></b> (<?=number_format($totdelayprice4)?>)</p></td>
						<td><p><b><?=$totdelayprice3?></b> (<?=number_format($totdelayprice3)?>)</p></td>
						<td><p><b><?=$totdelayprice2?></b> (<?=number_format($totdelayprice2)?>)</p></td>
						<td><p><b><?=$totdelayprice1?></b> (<?=number_format($totdelayprice1)?>)</p></td>
						<td><p><span><?=$totdelaycnt?></span> (<?=number_format($totdelayprice)?>)</p></td>
					</tr>
					<tr>
						<td class="list">게시글</td>
						<td><p><b><?=$totbrdcnt4?></b></p></td>
						<td><p><b><?=$totbrdcnt3?></b> </p></td>
						<td><p><b><?=$totbrdcnt2?></b> </p></td>
						<td><p><b><?=$totbrdcnt1?></b> </p></td>
						<td><p><span><?=$totbrdcnt?></span></p></td>
					</tr>
					<tr>
						<td class="list">신규회원</td>
						<td><p><b><?=$totmemcnt4?></b></p></td>
						<td><p><b><?=$totmemcnt3?></b> </p></td>
						<td><p><b><?=$totmemcnt2?></b> </p></td>
						<td><p><b><?=$totmemcnt1?></b> </p></td>
						<td><p><span><?=$totmemcnt?></span></p></td>
					</tr>
					<tr>
						<td class="list">방문자</td>
						<td><p><b><?=$totvstcnt4?></b></p></td>
						<td><p><b><?=$totvstcnt3?></b> </p></td>
						<td><p><b><?=$totvstcnt2?></b> </p></td>
						<td><p><b><?=$totvstcnt1?></b> </p></td>
						<td><p><span><?=$totvstcnt?></span></p></td>
					</tr>
				</table>
				</div>
				</div>
				<!-- 테이블 끝 -->

				<!-- div class="lately_table" style="padding-left:8px;cursor:hand" onclick="javascript:document.location.href='member_list.php?confirm_chk=1'">
					<table cellpadding="0" cellspacing="0" border=0 style="table-layout:fixed;width:30%;border-top:1px solid #CCCCCC;border-right:1px solid #CCCCCC;">
						<tr><td class="list" style="width:70%" ><strong>미승인 사업자 회원</strong></td><td><?=number_format($confirm_row['cnt'])?>명&nbsp; </td></tr>
					</table>
				</div -->

				<!-- 최근주문내역 + 쇼핑메모장 -->
				<div class="lately_orderlist_wrap">


				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="288" valign="top" align=center>
					<table cellpadding="0" cellspacing="0" width="228">
					<tr>
						<td>
						<div class="orderlist_tit"><p><a href="#"><img src="img/icon/icon_reload.gif" alt="새로고침" /></a> <a href="order_list_new.php" target="_blink"><img src="img/icon/icon_more.gif" alt="더보기" /></a><div class="icon"></p></div></div>
						</td>
					</tr>
					<tr>
						<td height=10></td>
					</tr>
					<tr>
						<td style="padding-left:2pt;">
						<div class="bbs_orderlist_list">
						<table cellpadding="0" cellspacing="0" width="100%">
<?php
						$sql = "SELECT * FROM tblorderinfo
						ORDER BY ordercode DESC LIMIT 6 ";
						$result=pmysql_query($sql,get_db_conn());
						$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"매매보호","C"=>"신용카드","P"=>"매매보호","M"=>"핸드폰");
						$i=0;
						while($row = pmysql_fetch_object($result)) {
							$name=$row->sender_name;
							$date = substr($row->ordercode,4,2).substr($row->ordercode,6,2).substr($row->ordercode,8,2).substr($row->ordercode,10,2);
							echo "<tr>\n";
							echo "	<td style=\"padding-left:8pt; \"><A HREF=\"javascript:OrderDetailView('{$row->ordercode}');\">· [{$name}]&nbsp;";
							switch($row->deli_gbn) {
								case 'S': echo "발송준비";  break;
								case 'X': echo "배송요청";  break;
								case 'Y': echo "배송";  break;
								case 'D': echo "<font color=blue>취소요청</font>";  break;
								case 'N': echo "미처리";  break;
								case 'E': echo "<font color=red>환불대기</font>";  break;
								case 'C': echo "<font color=red>주문취소</font>";  break;
								case 'R': echo "반송";  break;
								case 'H': echo "배송(<font color=red>정산보류</font>)";  break;
							}
							echo "(".$arpm[$row->paymethod[0]].":".number_format($row->price).")</A></td>\n";
							echo "</tr>\n";
							$i++;
						}
						pmysql_free_result($result);
						if($i==0) {
							echo "<tr><td align=center>등록된 데이터가 없습니다.</td></tr>";
						}
?>
						</table>
						</div>
						</td>
					</tr>
					</table>
					</td>
					<td width="3" valign="top"></td>
					<td width="" valign="top">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="">
						<div class="memo_tit">
							<p class="btn01"><a href="javascript:OpenWindow('community_schedule_add.php?year=<?=date("Y")?>&month=<?=date("m")?>&day=<?=date("d")?>',350,130,'no','schedule')"></a></p>
							<p class="btn02"><a href="community_schedule_year.php"></a></p>
						</div>
						</td>
					</tr>
					<tr>
						<td valign="top" >

						<div class="meme_date_wrap">
						<div class="calendar">
<?php
	$y_date=date("Y");
	$m_date=date("m");

?>						<form name="calendar_form" action="<?=$_SERVER['PHP_SELF']?>" method=post>
						<input type='hidden' name="y_date" value="<?=$y_date?>">
						<input type='hidden' name="m_date" value="<?=$m_date?>">
						</form>
						<div class="month_select"><a href="#">◀</a> <?=$y_date?>.<?=$m_date?> <a href="#">▶</a></div>
						<table cellpadding="0" cellspacing="0" width="">
						<tr>
							<td width="15" valign="top">&nbsp;</td>
							<td width="230" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" border=0>
							<tr bgcolor=666666>
								<th ><img src="img/icon/icon_date_sun.gif" border="0"></th>
								<th ><img src="img/icon/icon_date_mon.gif" border="0"></th>
								<th ><img src="img/icon/icon_date_tue.gif" border="0"></th>
								<th ><img src="img/icon/icon_date_wed.gif" border="0"></th>
								<th ><img src="img/icon/icon_date_thu.gif" border="0"></th>
								<th ><img src="img/icon/icon_date_fri.gif" border="0"></th>
								<th ><img src="img/icon/icon_date_sat.gif" border="0"></th>
							</tr>
<?php
							$total_days=get_totaldays($y_date,$m_date);
							echo "<tr>\n";
							$first_day = date('w', strtotime($y_date."-".$m_date));
							$valueStr='';
							$col = 0;
							for($i=0;$i<$first_day;$i++) {
								$valueStr .= "<td></td>";
								$col++;
							}

							$sql = "SELECT idx,import,rest,subject,duedate,duetime FROM tblschedule
							WHERE duedate LIKE '".date("Ym")."%' AND rest='Y'
							ORDER BY duetime ASC ";
							$result = pmysql_query($sql,get_db_conn());
							$restDate=array();
							while($row = pmysql_fetch_object($result)) {
								$restDate[$row->duedate] = "Y";
							}
							pmysql_free_result($result);

							for($j=1;$j<=$total_days;$j++) {
								$dayname = $j;
								$enum = sprintf("%02d",$j);

								$fontclass="";
								if ($col == 0) {
									$fontclass="calender_sun";
								} else if ($col == 6) {
									$fontclass = "calender_sat";
									if ($restDate[date("Ym").$enum] == "Y") {
										$fontclass = "calender_sun";
									}
								} else {
									$fontclass = "calender";
									if ($restDate[date("Ym").$enum] == "Y") {
										$fontclass = "calender_sun";
									}
								}
								if($enum==date("d")) $fontclass="calender_select";
								$dayname = "<font class={$fontclass} style=\"line-height:15px\">{$j}</font>";
								$valueStr.="<td align=center><a href=\"community_schedule_day.php?year=".date("Y")."&month=".date("m")."&day={$j}\">{$dayname}</a></td>\n";
								$col++;

								if ($col == 7) {
									$valueStr .= "</tr>";
									if ($j != $total_days) {
										$valueStr .= "<tr>";
									}
									$col = 0;
								}
							}

							while($col > 0 && $col < 7) {
								$valueStr .= "<td></td>";
								$col++;
							}
							$valueStr .= "</tr>";

							echo $valueStr;

?>
							</table>
							</td>
						</tr>
						<tr>
							<td width="15" valign="top"></td>
							<td width="" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%">
<?php
							$sql = "SELECT subject,duedate,rest FROM tblschedule
							WHERE duedate >= '".date("Ymd")."'
							ORDER BY duedate, duetime ASC LIMIT 3 ";
							$result = pmysql_query($sql,get_db_conn());
							while($row=pmysql_fetch_object($result)) {
								list($y,$m,$d) = sscanf($row->duedate,'%4s%2s%2s');
								$weekday=date("w", strtotime("$y-$m-$d"));

								$fontclass="";
								if ($weekday == 0) {
									$fontclass="calender_sun";
								} else if ($weekday == 6) {
									$fontclass = "calender_sat";
									if ($row->rest == "Y") {
										$fontclass = "calender_sun";
									}
								} else {
									$fontclass = "calender";
									if ($row->rest == "Y") {
										$fontclass = "calender_sun";
									}
								}
								if($row->duedate==date("Ymd")) $fontclass="calender_select";

								echo "<tr>\n";
								echo "	<td width=8><img src=\"images/main_center_point1.gif\" border=0></td>\n";
								echo "	<td align=left style = 'text-align:left;'><A HREF=\"community_schedule_day.php?year=".$y."&month=".$m."&day=".$d."\"><font class={$fontclass}>[".$m."-".$d."]</font> <font class={$fontclass}>".titleCut(30,$row->subject)."</font></A></td>\n";
								echo "</tr>\n";
							}
							pmysql_free_result($result);
?>
							</table>
							</td>
						</tr>
						</table>

</div>
</div>


						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>



				</div><!-- 최근주문내역 + 쇼핑메모장 -->
				</div><!-- 현황 끝 -->

				</td>
			</tr>
			<tr>
				<td width="547" height=10></td>
			</tr>
			<!--tr>
				<td-->
					<!-- 센터 퀵메뉴 -->
					<!--div class="center_function_wrap">
						<ul>
							<li class="a1"><a href="order_list.php"></a></li>
							<li class="a2"><a href="order_delay.php"></a></li>
							<li class="a3"><a href="order_delisearch.php"></a></li>
							<li class="a4"><a href="order_monthsearch.php"></a></li>
							<li class="b1"><a href="member_list.php"></a></li>
							<li class="b2"><a href="member_outlist.php"></a></li>
							<li class="b3"><a href="member_mailsend.php"></a></li>
							<li class="b4"><a href="market_smssinglesend.php"></a></li>
							<li class="c1"><a href="product_register.php"></a></li>
							<li class="c2"><a href="product_price.php"></a></li>
							<li class="c3"><a href="product_allsoldout.php"></a></li>
							<li class="c4"><a href="product_allquantity.php"></a></li>
							<li class="d1"><a href="order_list.php"></a></li>
							<li class="d2"><a href="member_list.php"></a></li>
							<li class="d3"><a href="product_exceldownload.php"></a></li>
							<li class="d4"><a href="#"></a></li>
						</ul>
					</div-->
					<!-- 센터 퀵메뉴 -->
				<!--/td>
			</tr>

			<tr>
				<td width="547" height="10"></td>
			</tr-->

			<!-- 게시판 -->
			<tr>
				<td>

					<div class="center_bbs_wrap">
						<table>
							<colgroup>
								<col width="288" /><col width="289" />
							</colgroup>
							<tr valign=top>
								<td align=center height="141">
									<div class="tap_wrap">
										<ul>
											<li class="this" id="personal_class"><a href="community_personal.php"><img src="img/common/bbs_tit_tap01.gif" alt="" onmouseover="javascript:div_change('personal')" /></a></li>
											<li id="qna_class"><a href="community_article.php"><img src="img/common/bbs_tit_tap02.gif" alt=""  onmouseover="javascript:div_change('qna')"/></a></li>
										</ul>
										<div class="more"><a href="community_personal.php" id="over_href"><img src="img/common/bbs_tit_tap_more.gif" alt="" /></a></div>
									</div>

									<div style="display:block" id="personal_div">
									<table cellpadding="0" cellspacing="0" width="267" >

									<form name=perform action="community_personal_pop.php" method=post target="personal_pop">
									<input type=hidden name=idx>
									</form>
									<?php
									$sql = "SELECT idx, subject, re_date FROM tblpersonal where (length(re_date) != 14  or re_date IS NULL)
									ORDER BY date DESC LIMIT 6 ";
									$result=pmysql_query($sql,get_db_conn());
									$i=0;
									while($row=pmysql_fetch_object($result)) {
										echo "<tr>\n";
										echo "	<td width=8><img src=\"images/main_center_point.gif\" border=0></td>\n";
										echo "	<td><A HREF=\"javascript:ViewPersonal('{$row->idx}');\">".titleCut(24,strip_tags($row->subject))."</A>";

										if(strlen($row->re_date)==14) {
											echo " <img src=\"img/icon/icon_handling.gif\" border=\"0\">";
										} else {
											echo " <img src=\"img/icon/icon_nohandling.gif\" border=\"0\">";
										}
										echo "</td>\n";
										echo "</tr>\n";
										$i++;
									}
									pmysql_free_result($result);
									if($i==0) {
										echo "<tr><td align=center>등록된 데이터가 없습니다.</td></tr>";
									}
									?>
									</table>
									</div>

									<div style="display:none" id="qna_div">
									<table cellpadding="0" cellspacing="0" width="267" >

									<?php
									$sql	 = "SELECT a.title as title, a.num as num, b.num as a_num ";
									$sql	.= "FROM tblboard a left join tblboardcomment b on a.board=b.board and a.num=b.parent WHERE a.board='qna' and b.num is NULL ";
									$sql	.= "ORDER BY a.num DESC LIMIT 6  ";

									$result=pmysql_query($sql,get_db_conn());
									$i=0;
									while($row=pmysql_fetch_object($result)) {
										echo "<tr>\n";
										echo "	<td width=8><img src=\"images/main_center_point.gif\" border=0></td>\n";
										echo "	<td><a href=\"community_article.php?exec=view&num={$row->num}&board=qna&block=&gotopage=&search=&s_check=\">".titleCut(24,strip_tags($row->title))."</A>";

										if($row->a_num != "") {
											echo " <img src=\"img/icon/icon_handling.gif\" border=\"0\">";
										} else {
											echo " <img src=\"img/icon/icon_nohandling.gif\" border=\"0\">";
										}
										echo "</td>\n";
										echo "</tr>\n";
										$i++;
									}
									pmysql_free_result($result);
									if($i==0) {
										echo "<tr><td align=center>등록된 데이터가 없습니다.</td></tr>";
									}
									?>
									</table>
									</div>

								</td>
								<td align=center>
									<div class="title"><a href="../admin/market_notice.php"><img src="img/common/bbs_tit_notice.gif" alt="공지사항" /></a></div>
									<table cellpadding="0" cellspacing="0" width="268">
<!--
									<form name=reviewform action="product_reviewreply.php" method=post>
									<input type=hidden name=date>
									<input type=hidden name=productcode>
									</form>
									<?php
									$sql = "SELECT a.productcode,a.date,a.content FROM tblproductreview a, tblproduct b
									WHERE a.productcode = b.productcode ORDER BY a.date DESC LIMIT 6 ";
									$result=pmysql_query($sql,get_db_conn());
									$i=0;
									while($row=pmysql_fetch_object($result)) {
										$rowcontent = explode("=",$row->content);

										echo "<tr>\n";
										echo "	<td width=8><img src=\"images/main_center_point.gif\" border=0></td>\n";
										echo "	<td><A HREF=\"javascript:ReviewReply('{$row->date}','{$row->productcode}')\">".titleCut(24,strip_tags($rowcontent[0]))."</A></td>\n";
										echo "</tr>\n";
										$i++;
									}
									pmysql_free_result($result);
									if($i==0) {
										echo "<tr><td align=center>등록된 데이터가 없습니다.</td></tr>";
									}
									?>
-->
									<form name=noticeform action="market_notice.php" method=post>
									<input type=hidden name=type>
									<input type=hidden name=date>
									</form>
									<?php
									$sql = "SELECT date,subject FROM tblnotice ORDER BY date DESC LIMIT 6 ";
									$result=pmysql_query($sql,get_db_conn());
									$i=0;
									while($row=pmysql_fetch_object($result)) {
										echo "<tr>\n";
										echo "	<td width=8><img src=\"images/main_center_point.gif\" border=0></td>\n";
										echo "	<td><A HREF=\"javascript:NoticeSend('modify','{$row->date}')\">".titleCut(24,strip_tags($row->subject))."</A></td>\n";
										echo "</tr>\n";
										$i++;
									}
									pmysql_free_result($result);
									if($i==0) {
										echo "<tr><td align=center>등록된 데이터가 없습니다.</td></tr>";
									}
									?>
									</table>
								</td>
							</tr>
						</table>
					</div>

				</td>
			</tr>
			<!-- 게시판 -->

			<tr>
				<td width="547" height="10"></td>
			</tr>
			<tr>
				<td>

				<!-- 최근 관련상품 -->
				<div class="lately_contents_wrap">
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<tr>
					<td width=50% align=center valign=top>


					<!-- 최근등록상품 시작 -->
					<table border=0 cellpadding=3 cellspacing=0 width=99% height=434 >
					<tr height=27>
						<td><div class="reg_tit"><p><a href="#"><img src="img/icon/icon_reload.gif" alt="새로고침" /></a> <a href="#"><img src="img/icon/icon_more.gif" alt="더보기" /></a></p></div></td>
					</tr>
					<tr>
						<td align=center>
						<table border=0 cellpadding=0 cellspacing=0 width=98%>
						<col width=50></col>
						<col width=></col>
<?php
						$sql = "SELECT productcode,productname,tinyimage,regdate FROM tblproduct
						ORDER BY regdate DESC LIMIT 6 ";
						$result=pmysql_query($sql,get_db_conn());
						$line_bottom=0;
						while($row=pmysql_fetch_object($result)) {
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td colspan=2 height=5>\n";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr height=60>\n";
							echo "	<td valign=top >";
							if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
								echo "<img src=\"".$Dir.DataDir."shopimages/product/".$row->tinyimage."\" border=0 width=50 height=50 style=\"border:1px #efefef solid\">";
							} else {
								echo "<img src=\"{$Dir}images/no_img.gif\" border=0 width=50 height=50 style=\"border:1px #efefef solid\">";
							}
							echo "	</td>\n";
							echo "	<td valign=top style=\"padding-left:10\">";
							echo "	<A HREF=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\">{$row->productname}</A>";
							echo "	<br><FONT COLOR=#9d9d9d>".str_replace("-","/",substr($row->regdate,0,10))."</FONT>\n";
							echo "	</td>\n";
							echo "</tr>\n";
							if($line_bottom!='5'){
							echo "<tr>\n";
							echo "<td colspan=2 height=1 bgcolor=#dddddd>\n";
							echo "</td>\n";
							echo "</tr>\n";
							}
							$line_bottom++;

						}
						pmysql_free_result($result);
?>
						</table>
						</td>
					</tr>
					</table>
					<!-- 최근등록상품 끝 -->
					</td>

					<td width=50% align=center valign=top>
					<!-- 최근판매상품 시작 -->
					<table border=0 cellpadding=3 cellspacing=0 width=99%  height=434 >
					<tr height=27>
						<td><div class="sell_tit"><p><a href="#"><img src="img/icon/icon_reload.gif" alt="새로고침" /></a> <a href="#"><img src="img/icon/icon_more.gif" alt="더보기" /></a></p></div></td>
					</tr>
					<tr>
						<td valign=top align=center>
						<table border=0 cellpadding=0 cellspacing=0 width=98%>
<?php
						$sql = "SELECT productcode,productname,tinyimage,selldate FROM tblproduct
						WHERE selldate!='1970-01-01 00:00:00' ORDER BY selldate DESC LIMIT 6 ";
						$result=pmysql_query($sql,get_db_conn());
						$line_bottom=0;
						while($row=pmysql_fetch_object($result)) {
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td colspan=2 height=5>\n";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr height=60>\n";
							echo "	<td valign=top>";
							if (ord($row->tinyimage) && file_exists($Dir.DataDir."shopimages/product/".$row->tinyimage)) {
								echo "<img src=\"".$Dir.DataDir."shopimages/product/".urlencode($row->tinyimage)."\" border=0 width=50 height=50 style=\"border:1px #efefef solid\">";
							} else {
								echo "<img src=\"{$Dir}images/no_img.gif\" border=0 width=50 height=50 style=\"border:1px #efefef solid\">";
							}
							echo "	</td>\n";
							echo "	<td valign=top style=\"padding-left:10\">";
							echo "	<A HREF=\"JavaScript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\">{$row->productname}</A>";
							echo "	<br><FONT COLOR=#9d9d9d>".str_replace("-","/",substr($row->selldate,0,10))."</FONT>\n";
							echo "	</td>\n";
							echo "</tr>\n";
							echo "</tr>\n";
							if($line_bottom!='5'){
							echo "<tr>\n";
							echo "<td colspan=2 height=1 bgcolor=#dddddd>\n";
							echo "</td>\n";
							echo "</tr>\n";
							}
							$line_bottom++;
						}
						pmysql_free_result($result);
?>
						</table>
						</td>
					</tr>
					</table>
					<!-- 최근판매상품 끝 -->
					</td>
				</tr>
				</table>
				</div><!-- 최근 관련상품 -->

				</td>
			</tr>
			</table>

			<!--######################## 가운데 끝 ########################-->
			</td>

			<!--######################## 공백 6px ########################-->
			<td width="6" valign="top"><img src="images/space01.gif" width="6" height="1" border="0"></td>
			<!--##########################################################-->

			<td valign="top">
			<!--######################## 오른쪽 시작 ########################-->
			<table cellpadding="0" cellspacing="0" width="203">
			<tr>
				<td>

				<!-- month's 현황 -->
				<div class="right_month_info">

				<TABLE  BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><img src="img/common/right_month_tit.gif" ALT=""></TD>
				</TR>
				<TR>
					<TD width="100%" background="img/common/right_month_midd.gif">
					<table cellpadding="0" cellspacing="0" width="100%" >
					<col width=18></col>
					<col width=></col>
					<tr>
						<td></td>
						<td>
						<table cellpadding="0" cellspacing="0" >
						<tr><td height=5></td></tr>
						<tr>
							<td>
							<div class="right_month_info_table">
							<table border=0 cellpadding=0 cellspacing=0>
							<col width=60></col>
							<col width=></col>
							<tr>
								<td>주문 </td>
								<td>: <span><?=$totmonordcnt?></span>건</td>
							</tr>
							</table>
							</div>
							</td>
						</tr>
						<tr>
							<td>
							<div class="right_month_info_table">
							<table border=0 cellpadding=0 cellspacing=0>
							<col width=60></col>
							<col width=></col>
							<tr>
								<td>매출 </td>
								<td>: <span><?=number_format($totmonordprice)?></span>원</td>
							</tr>
							</table>
							</td>
							</div>
						</tr>
						<tr>
							<td>
							<div class="right_month_info_table">
							<table border=0 cellpadding=0 cellspacing=0>
							<col width=60></col>
							<col width=></col>
							<tr>
								<td>게시글 </td>
								<td>: <span><?=$totmonbrdcnt?></span>건</td>
							</tr>
							</table>
							</td>
							</div>
						</tr>
						<tr>
							<td>
							<div class="right_month_info_table">
							<table border=0 cellpadding=0 cellspacing=0>
							<col width=60></col>
							<col width=></col>
							<tr>
								<td>회원가입 </td>
								<td>: <span><?=$totmonmemcnt?></span>건</td>
							</tr>
							</table>
							</div>
							</td>
						</tr>
						<tr>
							<td>
							<div class="right_month_info_table">
							<table border=0 cellpadding=0 cellspacing=0>
							<col width=60></col>
							<col width=></col>
							<tr>
								<td>방문자 </td>
								<td>: <span><?=$totmonvstcnt?></span>건</td>
							</tr>
							</table>
							</div>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					<table class="month_btn" border=0 cellpadding=0 cellspacing=0 align=center>
						<tr>
							<td><span class="btn"><a class="w90" href="http://<?=$shopurl?>" name="shopurl" target="_blank">마이샵 바로가기</a></span></td>
<!--							<td> <span class="btn"><a class="w70"  href="#">내정보확인</a></span></td>-->
						</tr>
					</table>

					</TD>
				</TR>
				<TR>
					<TD><IMG SRC="img/common/right_month_bottom.gif"  ALT=""></TD>
				</TR>
				</TABLE>
				</div><!-- month's 현황 -->

				</td>
			</tr>
			<tr>
				<td width="5" height=10></td>
			</tr>
			<tr>
				<td>
				<!--@ right @-->
				<div class="right_design_wrap">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="img/common/right_tit_design01.gif"></td>
				</tr>
				<tr>
					<td background="img/common/right_tit_design_midd.gif">
					<div class="design_table">
					<table width="169" border="0" cellspacing="0" cellpadding="0" align="center">
					<!--tr>
						<td><a href="design_eachmain.php"><span>- 메인 html로 디자인</span></a></td>
					</tr>
					<tr>
						<td><a href="design_main.php"><span>- 메인 템플릿</span></a></td>
					</tr>
					<tr>
						<td><a href="shop_mainintro.php">- 메인 타이틀디자인</a></td>
					</tr>
					<tr>
						<td><a href="shop_layout.php">- 쇼핑몰 레이아웃 설정</a></td>
					</tr-->
					<tr>
						<td><a href="product_mainlist.php">- 메인상품 진열관리</a></td>
					</tr>
					<!--tr>
						<td><a href="shop_mainproduct.php">- 상품 진열수/화면 설정</a></td>
					</tr>
					<tr>
						<td><a href="shop_logobanner.php">- 로고/배너 관리</a></td>
					</tr-->
					</table>
					</div>
					</td>
				</tr>
				<!--tr>
					<td><img src="img/common/right_tit_design02.gif"></td>
				</tr>
				<tr>
					<td background="img/common/right_tit_design_midd.gif">
					<div class="design_table">
					<table width="169" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td><a href="design_plist.php"><span>- 카테고리 디자인 템플릿</span></a></td>
					</tr>
					<tr>
						<td><a href="design_easytop.php"><span>- easy 상단 메뉴</span></a></td>
					</tr>
					<tr>
						<td><a href="market_eventcode.php">- 카테고리별 상단 디자인</a></td>
					</tr>
					<tr>
						<td><a href="market_eventprdetail.php">- 상품상세 이벤트 디자인</a></td>
					</tr>
					<tr>
						<td><a href="design_easyleft.php">- easy 왼쪽메뉴</a></td>
					</tr>
					<tr>
						<td><a href="design_easycss.php">- easy 텍스트 속성 변경</a></td>
					</tr>
					<tr>
						<td><a href="design_eachtitleimage.php">- 타이틀 이미지 관리</a></td>
					</tr>
					</table>
					</div>
					</td>
				</tr-->
				<tr>
					<td><img src="img/common/right_tit_design03.gif"></td>
				</tr>
				<tr>
					<td background="img/common/right_tit_design_midd.gif">
					<div class="design_table">
					<table width="169" border="0" cellspacing="0" cellpadding="0" align="center">
					<!-- tr>
						<td><a href="shop_escrow.php"><span>- 에스크로 결제관련 설정</span></a></td>
					</tr -->
					<tr>
						<td><a href="shop_payment.php">- 상품 결제관련 기능설정</a></td>
					</tr>
					<tr>
						<td><a href="order_taxsaveconfig.php">- 현금영수증 환경설정</a></td>
					</tr>
					<!-- tr>
						<td><a href="shop_ssl.php">- SSL(보안서버) 기능 설정</a></td>
					</tr -->
					</table>
					</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/right_tit_design04.gif"></td>
				</tr>
				<tr>
					<td background="img/common/right_tit_design_midd.gif">
					<div class="design_table">
					<table width="169" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td><a href="member_excelupload.php"><span>- 회원정보 일괄등록</span></a></td>
					</tr>
					<tr>
						<td><a href="member_groupnew.php">- 회원등급 기능</a></td>
					</tr>
					<!-- tr>
						<td><a href="member_mailallsend.php">- 단체 메일 발송</a></td>
					</tr>
					<tr>
						<td><a href="market_smsgroupsend.php">- 단체 sms 발송</a></td>
					</tr -->
					</table>
					</div>
					</td>
				</tr>
				<tr>
					<td><img src="img/common/right_tit_design_bottom.gif"></td>
				</tr>
				</table>
				</div>
				<!--@ right @-->
				</td>
			</tr>
			</table>

			<div class="right_banner_wrap">
				<table border="0" cellspacing="0" cellpadding="0" width=203>
					<tr><td><img src="img/common/right_banner_tit.gif" alt="파워서비스" /></td></tr>
					<!--<tr><td><a href="#"><img src="img/common/right_bn01.gif" alt="" /></a></td></tr>
					<tr><td><a href="#"><img src="img/common/right_bn02.gif" alt="" /></a></td></tr>-->
					<tr><td><img src="img/common/right_bn03.gif" alt="" /></td></tr>
					<tr><td><img src="img/common/right_bn04.gif" alt="" /></td></tr>
					<tr><td><img src="img/common/right_bn05.gif" alt="" /></td></tr>
					<tr><td><img src="img/common/right_bn06.gif" alt="" /></td></tr>
				</table>
			</div>

			<!--######################## 오른쪽 끝 ########################-->
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td width="100%" style="display:none;"><IFRAME name="BannerrightFrame" src="main.php?bannertype=right" width=100% height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
	</tr>
	<tr>
		<td width="100%" style="display:none;"><IFRAME name="BannerleftFrame" src="main.php?bannertype=left" width=100% height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
	</tr>
	</table>
	</td>
</tr>
<form name=prform method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>

<form name=detailform method="post" action="order_detail.php" target="orderdetail">
<input type=hidden name=ordercode>
</form>

<IFRAME name="tempiframe" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

</table>

</div><!-- main wrap -->

<script language="JavaScript" Event="onLoad" For="window">
//document.tempiframe.location="main_socketdata.php";
</script>
<?php
include("copyright.php");
