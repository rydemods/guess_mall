<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("header.php"); 
?>
<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 디자인관리 &gt; <span class="2depth_select">디자인관리 메인</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/design_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
	$shop_main_title[] = "design_mainstitle1.gif";
	$shop_main_title[] = "design_mainstitle2.gif";
	$shop_main_title[] = "design_mainstitle4.gif";
	$shop_main_title[] = "design_mainstitle3.gif";
	$shop_main_title[] = "design_mainstitle5.gif";
	$shop_main_title[] = "design_mainstitle6.gif";
	$shop_main_title[] = "design_mainstitle7.gif";

	$shop_main_stext[0][] = "design_mains0text01.gif";
	$shop_main_stext[0][] = "design_mains0text02.gif";

	$shop_main_stext[1][] = "design_mains1text01.gif";
	$shop_main_stext[1][] = "design_mains1text02.gif";
	$shop_main_stext[1][] = "design_mains1text03.gif";
	$shop_main_stext[1][] = "design_mains1text04.gif";
	$shop_main_stext[1][] = "design_mains1text05.gif";
	
	$shop_main_stext[2][] = "design_mains3text01.gif";
	$shop_main_stext[2][] = "design_mains3text02.gif";
	$shop_main_stext[2][] = "design_mains3text03.gif";
	$shop_main_stext[2][] = "design_mains3text04.gif";
	$shop_main_stext[2][] = "design_mains3text05.gif";
	$shop_main_stext[2][] = "design_mains3text06.gif";
	$shop_main_stext[2][] = "design_mains3text07.gif";
	$shop_main_stext[2][] = "design_mains4text01.gif";
	$shop_main_stext[2][] = "design_mains4text02.gif";

	$shop_main_stext[3][] = "design_mains2text20.gif";
	$shop_main_stext[3][] = "design_mains2text19.gif";
	$shop_main_stext[3][] = "design_mains2text01.gif";
	$shop_main_stext[3][] = "design_mains2text02.gif";
	$shop_main_stext[3][] = "design_mains2text03.gif";
	$shop_main_stext[3][] = "design_mains2text04.gif";
	$shop_main_stext[3][] = "design_mains2text05.gif";
	$shop_main_stext[3][] = "design_mains2text06.gif";
	$shop_main_stext[3][] = "design_mains2text07.gif";
	$shop_main_stext[3][] = "design_mains2text08.gif";
	$shop_main_stext[3][] = "design_mains2text09.gif";
	$shop_main_stext[3][] = "design_mains2text10.gif";
	$shop_main_stext[3][] = "design_mains2text11.gif";
	$shop_main_stext[3][] = "design_mains2text12.gif";
	$shop_main_stext[3][] = "design_mains2text13.gif";
	if(getVenderUsed()) { $shop_main_stext[3][] = "design_mains2text21.gif"; }
	$shop_main_stext[3][] = "design_mains2text14.gif";
	$shop_main_stext[3][] = "design_mains2text15.gif";
	$shop_main_stext[3][] = "design_mains2text16.gif";
	$shop_main_stext[3][] = "design_mains2text17.gif";
	$shop_main_stext[3][] = "design_mains2text18.gif";
	$shop_main_stext[3][] = "design_mains2text22.gif";
	$shop_main_stext[3][] = "design_mains2text23.gif";

	$shop_main_stext[4][] = "design_mains4text36.gif";
	$shop_main_stext[4][] = "design_mains4text31.gif";
	$shop_main_stext[4][] = "design_mains4text30.gif";
	$shop_main_stext[4][] = "design_mains4text03.gif";
	$shop_main_stext[4][] = "design_mains4text04.gif";
	$shop_main_stext[4][] = "design_mains4text05.gif";
	$shop_main_stext[4][] = "design_mains4text06.gif";
	$shop_main_stext[4][] = "design_mains4text07.gif";
	$shop_main_stext[4][] = "design_mains4text08.gif";
	$shop_main_stext[4][] = "design_mains4text09.gif";
	$shop_main_stext[4][] = "design_mains4text10.gif";
	$shop_main_stext[4][] = "design_mains4text11.gif";
	$shop_main_stext[4][] = "design_mains4text12.gif";
	$shop_main_stext[4][] = "design_mains4text13.gif";
	$shop_main_stext[4][] = "design_mains4text14.gif";
	$shop_main_stext[4][] = "design_mains4text15.gif";
	$shop_main_stext[4][] = "design_mains4text16.gif";
	$shop_main_stext[4][] = "design_mains4text17.gif";
	$shop_main_stext[4][] = "design_mains4text18.gif";
	$shop_main_stext[4][] = "design_mains4text19.gif";
	$shop_main_stext[4][] = "design_mains4text20.gif";
	$shop_main_stext[4][] = "design_mains4text21.gif";
	$shop_main_stext[4][] = "design_mains4text22.gif";
	$shop_main_stext[4][] = "design_mains4text23.gif";
	$shop_main_stext[4][] = "design_mains4text24.gif";
	$shop_main_stext[4][] = "design_mains4text25.gif";
	if(getVenderUsed()) { $shop_main_stext[4][] = "design_mains4text33.gif"; }
	$shop_main_stext[4][] = "design_mains4text26.gif";
	$shop_main_stext[4][] = "design_mains4text27.gif";
	$shop_main_stext[4][] = "design_mains4text28.gif";
	$shop_main_stext[4][] = "design_mains4text29.gif";
	$shop_main_stext[4][] = "design_mains4text32.gif";
	$shop_main_stext[4][] = "design_mains4text34.gif";
	$shop_main_stext[4][] = "design_mains4text35.gif";

	$shop_main_stext[5][] = "design_mains5text01.gif";
	$shop_main_stext[5][] = "design_mains5text02.gif";

	$shop_main_stext[6][] = "design_mains6text01.gif";
	$shop_main_stext[6][] = "design_mains6text02.gif";
	$shop_main_stext[6][] = "design_mains6text03.gif";

	$shop_main_slink[0][] = "design_webftp.php";
	$shop_main_slink[0][] = "design_option.php";

	$shop_main_slink[1][] = "design_adultintro.php";
	$shop_main_slink[1][] = "design_main.php";
	$shop_main_slink[1][] = "design_bottom.php";
	$shop_main_slink[1][] = "design_plist.php";
	$shop_main_slink[1][] = "design_pdetail.php";
	
	$shop_main_slink[2][] = "design_eachintropage.php";
	$shop_main_slink[2][] = "design_eachtitleimage.php";
	$shop_main_slink[2][] = "design_eachtopmenu.php";
	$shop_main_slink[2][] = "design_eachleftmenu.php";
	$shop_main_slink[2][] = "design_eachmain.php";
	$shop_main_slink[2][] = "design_eachbottom.php";
	$shop_main_slink[2][] = "design_eachloginform.php";
	$shop_main_slink[2][] = "design_eachplist.php";
	$shop_main_slink[2][] = "design_eachpdetail.php";

	$shop_main_slink[3][] = "design_tag.php";
	$shop_main_slink[3][] = "design_section.php";
	$shop_main_slink[3][] = "design_search.php";
	$shop_main_slink[3][] = "design_useinfo.php";
	$shop_main_slink[3][] = "design_memberjoin.php";
	$shop_main_slink[3][] = "design_usermodify.php";
	$shop_main_slink[3][] = "design_login.php";
	$shop_main_slink[3][] = "design_basket.php";
	$shop_main_slink[3][] = "design_order.php";
	$shop_main_slink[3][] = "design_mypage.php";
	$shop_main_slink[3][] = "design_orderlist.php";
	$shop_main_slink[3][] = "design_wishlist.php";
	$shop_main_slink[3][] = "design_mycoupon.php";
	$shop_main_slink[3][] = "design_myreserve.php";
	$shop_main_slink[3][] = "design_mypersonal.php";
	if(getVenderUsed()) { $shop_main_slink[3][] = "design_mycustsect.php"; }
	$shop_main_slink[3][] = "design_sendmail.php";
	$shop_main_slink[3][] = "design_popupnotice.php";
	$shop_main_slink[3][] = "design_popupinfo.php";
	$shop_main_slink[3][] = "design_formmail.php";
	$shop_main_slink[3][] = "design_cardtopimg.php";
	$shop_main_slink[3][] = "design_blist.php";
	$shop_main_slink[3][] = "design_bmap.php";

	$shop_main_slink[4][] = "design_eachbottomtools.php";
	$shop_main_slink[4][] = "design_eachtag.php";
	$shop_main_slink[4][] = "design_eachsection.php";
	$shop_main_slink[4][] = "design_eachsearch.php";
	$shop_main_slink[4][] = "design_eachbasket.php";
	$shop_main_slink[4][] = "design_eachprimageview.php";
	$shop_main_slink[4][] = "design_eachpopupnotice.php";
	$shop_main_slink[4][] = "design_eachpopupinfo.php";
	$shop_main_slink[4][] = "design_eachsendmail.php";
	$shop_main_slink[4][] = "design_eachformmail.php";
	$shop_main_slink[4][] = "design_eachboardtop.php";
	$shop_main_slink[4][] = "design_eachuseinfo.php";
	$shop_main_slink[4][] = "design_eachagreement.php";
	$shop_main_slink[4][] = "design_eachjoinagree.php";
	$shop_main_slink[4][] = "design_eachmemberjoin.php";
	$shop_main_slink[4][] = "design_eachusermodify.php";
	$shop_main_slink[4][] = "design_eachiddup.php";
	$shop_main_slink[4][] = "design_eachfindpwd.php";
	$shop_main_slink[4][] = "design_eachlogin.php";
	$shop_main_slink[4][] = "design_eachmemberout.php";
	$shop_main_slink[4][] = "design_eachmypage.php";
	$shop_main_slink[4][] = "design_eachorderlist.php";
	$shop_main_slink[4][] = "design_eachwishlist.php";
	$shop_main_slink[4][] = "design_eachmycoupon.php";
	$shop_main_slink[4][] = "design_eachmyreserve.php";
	$shop_main_slink[4][] = "design_eachmypersonal.php";
	if(getVenderUsed()) { $shop_main_slink[4][] = "design_eachmycustsect.php"; }
	$shop_main_slink[4][] = "design_eachsurveylist.php";
	$shop_main_slink[4][] = "design_eachsurveyview.php";
	$shop_main_slink[4][] = "design_eachreviewpopup.php";
	$shop_main_slink[4][] = "design_eachreviewall.php";
	$shop_main_slink[4][] = "design_eachrssinfo.php";
	$shop_main_slink[4][] = "design_eachblist.php";
	$shop_main_slink[4][] = "design_eachbmap.php";

	$shop_main_slink[5][] = "design_newpage.php";
	$shop_main_slink[5][] = "design_community.php";

	$shop_main_slink[6][] = "design_easytop.php";
	$shop_main_slink[6][] = "design_easyleft.php";
	$shop_main_slink[6][] = "design_easycss.php";

	$shop_main_sinfo[0][] = "쇼핑몰에 사용될 파일들을 웹상에서 쉽게 관리하실 수 있습니다.";
	$shop_main_sinfo[0][] = "개별 메인, 상단, 왼쪽 디자인, 각종 타이틀을 선택적으로 적용을 할 수 있습니다.";
	
	$shop_main_sinfo[1][] = "개별 메인디자인 및 왼쪽메뉴, 각종 타이틀을 변경하실 수 있습니다.";
	$shop_main_sinfo[1][] = "쇼핑몰 메인화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[1][] = "쇼핑몰 하단 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[1][] = "쇼핑몰 카테고리 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[1][] = "쇼핑몰 상품 상세화면 디자인을 선택하여 사용하실 수 있습니다.";
	
	$shop_main_sinfo[2][] = "인트로 페이지를 관리하실 수 있습니다.";
	$shop_main_sinfo[2][] = "쇼핑몰의 각종 타이틀 이미지 및 메인페이지 오른쪽 타이틀 배경색을 지정하실 수 있습니다.";
	$shop_main_sinfo[2][] = "상단메뉴를 전체페이지(default), 또는 카테고리별, 메뉴별 자유롭게 디자인이 가능합니다.";
	$shop_main_sinfo[2][] = "왼쪽메뉴를 전체페이지(default), 또는 카테고리별, 메뉴별 자유롭게 디자인이 가능합니다.";
	$shop_main_sinfo[2][] = "쇼핑몰 메인본문(메인중앙+우측메뉴를 모두 포함)을 자유롭게 디자인이 가능합니다.";
	$shop_main_sinfo[2][] = "하단메뉴 디자인을 자유롭게 관리하실 수 있습니다.";
	$shop_main_sinfo[2][] = "로그인 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[2][] = "상품카테고리 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[2][] = "상품상세 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";

	$shop_main_sinfo[3][] = "인기태그 및 태그검색 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "메인 섹션별 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "상품검색 결과화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 이용안내 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 회원가입 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 회원정보수정 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 로그인 및 비밀번호 분실 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 장바구니 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "상품 주문서 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 마이페이지 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 마이페이지 주문리스트 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 WishList 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 마이페이지 쿠폰 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 마이페이지 적립금 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 마이페이지 1:1고객문의 화면 디자인을 선택하여 사용하실 수 있습니다.";
	if(getVenderUsed()) { $shop_main_sinfo[3][] = "쇼핑몰 마이페이지 단골매장 화면 디자인을 선택하여 사용하실 수 있습니다."; }
	$shop_main_sinfo[3][] = "쇼핑몰 메일 관련 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 공지사항 팝업창 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 컨텐츠(정보) 팝업창 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "쇼핑몰 폼메일 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "카드결제창의 상단이미지를 쇼핑몰에 맞게 변경/관리하실 수 있습니다.";
	$shop_main_sinfo[3][] = "상품 브랜드별 화면 디자인을 선택하여 사용하실 수 있습니다.";
	$shop_main_sinfo[3][] = "브랜드맵 화면 디자인을 선택하여 사용하실 수 있습니다.";
	
	$shop_main_sinfo[4][] = "하단 폴로메뉴 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "인기태그 및 태그검색 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "메인 섹션별 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "상품검색 결과화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "장바구니 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "상품이미지 확대창 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "공지사항 팝업창 디자인을 자유롭게 관리하실 수 있습니다.";
	$shop_main_sinfo[4][] = "컨텐츠정보 팝업창 디자인을 자유롭게 관리하실 수 있습니다.";
	$shop_main_sinfo[4][] = "메일 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "폼메일 팝업 페이지의 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "게시판 상단 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "쇼핑몰 이용안내 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "쇼핑몰 이용약관 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "쇼핑몰 회원가입 약관 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "쇼핑몰 회원가입 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "회원정보수정 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "회원ID 중복체크 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "패스워드 분실화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "로그인 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "회원탈퇴 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "마이페이지 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "마이페이지 주문리스트 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "WishList 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "마이페이지 쿠폰 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "마이페이지 적립금 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "마이페이지 1:1고객문의 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	if(getVenderUsed()) { $shop_main_sinfo[4][] = "마이페이지 단골매장 화면 디자인을 자유롭게 디자인 하실 수 있습니다."; }
	$shop_main_sinfo[4][] = "투표리스트 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "투표결과 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "각 상품의 리뷰에 대한 상세보기 페이지의 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "리뷰모음 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "RSS 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "상품 브랜드별 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	$shop_main_sinfo[4][] = "브랜드맵 화면 디자인을 자유롭게 디자인 하실 수 있습니다.";
	
	$shop_main_sinfo[5][] = "개별 일반페이지를 등록 및 관리하실 수 있습니다.";
	$shop_main_sinfo[5][] = "커뮤니티 페이지를 등록 및 관리하실 수 있습니다.";

	$shop_main_sinfo[6][] = "쇼핑몰 상단 디자인을 직접 제작하신 이미지파일을 이용하여, 간단하게 디자인을 하실 수 있습니다.";
	$shop_main_sinfo[6][] = "쇼핑몰 왼쪽메뉴 디자인을 직접 제작하신 이미지파일을 이용하여, 간단하게 디자인을 하실 수 있습니다.";
	$shop_main_sinfo[6][] = "메인페이지, 상품카테고리, 검색화면에서 보여지는 텍스트들의 속성을 간단하게 변경하실 수 있습니다.";

	for($i=0; $i<count($shop_main_title); $i++) {
		echo "<tr>\n";
		echo "	<td colspan=\"3\" background=\"images/mainstitle_bg.gif\"><img src=\"images/{$shop_main_title[$i]}\" border=\"0\"></td>\n";
		echo "</tr>\n";
		
		$shop_main_stext_round = @round(count($shop_main_stext[$i])/2);
		$k = $shop_main_stext_round;
		for($j=0; $j<$shop_main_stext_round; $j++) {
		echo "<tr>\n";
		echo "	<td style=\"padding-left:15px\"><a href=\"{$shop_main_slink[$i][$j]}\"><img src=\"images/{$shop_main_stext[$i][$j]}\" border=\"0\"><img src=\"images/cmn_main_go.gif\" border=\"0\"></a></td>\n";
			if($shop_main_stext[$i][$k]) {
			echo "	<td style=\"padding-left:15px\"><a href=\"{$shop_main_slink[$i][$k]}\"><img src=\"images/{$shop_main_stext[$i][$k]}\" border=\"0\"><img src=\"images/cmn_main_go.gif\" border=\"0\"></a></td>\n";
			} else {
			echo "	<td style=\"padding-left:15px\"></td>\n";
			}
		echo "</tr>\n";
		echo "<tr>\n";
		echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"design_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
		echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"design_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
		echo "</tr>\n";
			$k++;
		}

		echo "<tr>\n";
		echo "	<td height=\"20\" colspan=\"3\"></td>\n";
		echo "</tr>\n";
	}
?>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
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
<?php 
include("copyright.php");