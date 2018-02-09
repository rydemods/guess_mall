<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$body=$_POST["body"];
$intitle=$_POST["intitle"];

if($type=="update" && ord($body)) {
	if($intitle=="Y") {
		$leftmenu="Y";
	} else {
		$leftmenu="N";
	}
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='basket' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,leftmenu,body) VALUES(
		 'basket', 
		 '장바구니 화면', 
		 '{$leftmenu}', 
		 '{$body}') ";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET ";
		$sql.= "leftmenu	= '{$leftmenu}', ";
		$sql.= "body		= '{$body}' ";
		$sql.= "WHERE type='basket' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$sql = "UPDATE tblshopinfo SET design_basket='U' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"장바구니 화면 디자인 수정이 완료되었습니다.\"); }</script>";
} else if($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='basket' ";
	pmysql_query($sql,get_db_conn());

	$sql = "UPDATE tblshopinfo SET design_basket='001' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"장바구니 화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} else if($type=="clear") {
	$intitle="";
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='basket' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='basket' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$intitle=$row->leftmenu;
	} else {
		$intitle="Y";
	}
	pmysql_free_result($result);
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("장바구니 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("장바구니 화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>장바구니 화면 꾸미기</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">

	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8">
            </td></tr>
			<tr>
				<td>
                    <!-- 페이지 타이틀 -->
					<div class="title_depth3">장바구니 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>장바구니 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">장바구니 화면 개별디자인</div>
                </td>
            </tr>
            <tr>
            	<td>
					<div class="help_info01_wrap">
                    		<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
								<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
					</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=intitle value="Y" <?php if($intitle=="Y")echo"checked";?>> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
                <!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
                        <dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=200></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center bgcolor=#F0F0F0>
							<B>원샷구매 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15"><B>[ONE_START]</B></td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 시작 (원샷구매 사용시 첫부분에 꼭 들어가야함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODEA_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 1차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODEA_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODEB_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 2차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODEB_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODEC_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 3차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODEC_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_CODED_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 4차 카테고리 선택박스 <FONT class=font_blue>(예:[ONE_CODED_width:150px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_PRLIST_선택박스 스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 상품 리스트 선택박스 <FONT class=font_blue>(예:[ONE_PRLIST_width:350px;color:#000000;font-size:11px])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_PRIMG]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 상품이미지
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ONE_BASKET]</td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 장바구니 담기 <FONT class=font_blue>(예:&lt;a href=[ONE_BASKET]>장바구니 담기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15"><B>[ONE_END]</B></td>
							<td class=td_con1 style="padding-left:5;">
							원샷구매 끝 (원샷구매 사용시 마지막 부분에 꼭 들어가야함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center bgcolor=#F0F0F0>
							<B>장바구니 상품 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFBASKET]<br>[IFELSEBASKET]<br>[IFENDBASKET]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니에 상품이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<font class=font_blue>   <B>[IFBASKET]</B>
      장바구니에 상품이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEBASKET]</B>
      장바구니에 상품이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDBASKET]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[FORBASKET]<br>[FORENDBASKET]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							[FORBASKET] 장바구니 상품 한개에 대한 내용 기술[FORENDBASKET]
							<pre style="line-height:15px">
<font class=font_blue>   [IFBASKET]
       <B>[FORBASKET]</B>상품 하나에 대한 내용 기술<B>[FORENDBASKET]</B>
   [IFELSEBASKET]
       장바구니에 담긴 상품이 없습니다.
   [IFENDBASKET]</font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRIMG]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품이미지
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRNAME]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품명
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ADDCODE1]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품 특수값 ("-"포함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ADDCODE2]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품 특수값 ("-"비포함)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_RESERVE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							적립금 <FONT class=font_blue>(예:[BASKET_RESERVE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_SELLPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품가격 <FONT class=font_blue>(예:[BASKET_SELLPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QUANTITY]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량 입력박스
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QUP]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량증가 함수 <FONT class=font_blue>(예:&lt;a href=[BASKET_QUP]>수량증가&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QDN]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량감소 함수 <FONT class=font_blue>(예:&lt;a href=[BASKET_QDN]>수량감소&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_QUPDATE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							수량적용(수정) <FONT class=font_blue>(예:&lt;a href=[BASKET_QUPDATE]>수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							주문금액 <FONT class=font_blue>(예:[BASKET_PRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_WISHLIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							위시리스트 담기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_WISHLIST]>위시리스트 담기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_DEL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니에서 삭제 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_DEL]>삭제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_DEL_BIZ]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							비즈스프링 장바구니 삭제 이벤트 <br><FONT class=font_blue>(예:&lt;img src = '장바구니 삭제 이미지' [BASKET_DEL_BIZ]>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFOPTION]<br>[IFENDOPTION]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품옵션 처리 (옵션이 있을 경우에만 옵션내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFOPTION]</B>
      상품옵션 내용 예) [BASKET_OPTION]
   <B>[IFENDOPTION]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_OPTION]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품옵션내용 <FONT class=font_blue>(예:옵션 : [BASKET_OPTION])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRODUCTPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							상품 합계금액 <FONT class=font_blue>(예:상품 합계금액 : [BASKET_PRODUCTPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFPACKAGE]<br>[IFENDPACKAGE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품 패키지 처리 (패키지가 있을 경우에만 패키지 내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFPACKAGE]</B>
      상품패키지 내용 예) [BASKET_PACKAGE]
   <B>[IFENDPACKAGE]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PACKAGE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							패키지 정보 <FONT class=font_blue>(예:[BASKET_PACKAGE])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFPACKAGELIST]<br>[IFENDPACKAGELIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품 패키지 구성 정보 처리 (패키지 구성 상품이 있을 경우에만 내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFPACKAGELIST]</B>
      상품패키지 내용 예) [BASKET_PACKAGELIST]
   <B>[IFENDPACKAGELIST]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PACKAGELIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							패키지 구성 상품 정보 <FONT class=font_blue>(예:[BASKET_PACKAGELIST])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFASSEMBLE]<br>[IFENDASSEMBLE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 코디/조립상품의 구성 (코디/조립상품이 있을 경우에만 구성내용 출력)
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFASSEMBLE]</B>
      코디/조립상품 내용 예) [BASKET_ASSEMBLE]
   <B>[IFENDASSEMBLE]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ASSEMBLE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							코디/조립 구성 상품 정보 <FONT class=font_blue>(예:[BASKET_ASSEMBLE])</font>
							</td>
						</tr>
						<?php if($_shopdata->ETCTYPE["VATUSE"]=="Y") { ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_PRODUCTVAT]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							VAT 합계금액 <FONT class=font_blue>(예:VAT 합계금액 : [BASKET_PRODUCTVAT]원)</font>
							</td>
						</tr>
						<?php } ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_GROUPSTART]<br>[BASKET_GROUPEND]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							업체별 배송비/합계금액 내용
							<pre style="line-height:15px">
<font class=font_blue>   <B>[BASKET_GROUPSTART]</B>
      사용 예) 배송비 : [GROUP_DELIPRICE]원, 합계금액 : [GROUP_TOTPRICE]원
   <B>[BASKET_GROUPEND]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GROUP_DELIPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							업체별 배송비 <FONT class=font_blue>(예:상품 합계금액 : [GROUP_DELIPRICE]원)</font><br>[BASKET_GROUPSTART] [BASKET_GROUPEND]에 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GROUP_TOTPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							업체별 합계금액 <FONT class=font_blue>(예:업체별 합계금액 : [GROUP_TOTPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_TOTPRICE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							총 결제금액 <FONT class=font_blue>(예:총 결제금액 : [BASKET_TOTPRICE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_TOTRESERVE]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							총 적립금 <FONT class=font_blue>(예:총 적립금 : [BASKET_TOTRESERVE]원)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_ORDER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							주문하기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_ORDER]>주문하기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_SHOPPING]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							계속쇼핑 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_SHOPPING]>계속쇼핑&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET_CLEAR]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 비우기 버튼 <FONT class=font_blue>(예:&lt;a href=[BASKET_CLEAR]>장바구니 비우기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center bgcolor=#F0F0F0>
							<B>특별회원 관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15">[IFROYAL]<br>[IFENDROYAL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원에 대한 내용 기술 (특별회월일 경우에만 내용 출력)
							<pre style="line-height:15px">
<font class=font_blue>   <B>[IFROYAL]</B>
      내용
   <B>[IFENDROYAL]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_IMG]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 이미지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_MSG1]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 관련 메세지1 - 자동출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ROYAL_MSG2]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							특별회원 관련 메세지2 - 자동출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						</table>
						</dd>
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
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
