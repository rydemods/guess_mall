<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

include("access.php");
?>

<?php include("header.php"); ?>
<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 마케팅지원 &gt; <span class="2depth_select">마케팅지원 메인</span></td>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/market_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "market_mainstitle1.gif";
				$shop_main_title[] = "market_mainstitle2.gif";
				$shop_main_title[] = "market_mainstitle3.gif";
				$shop_main_title[] = "market_mainstitle4.gif";

				$shop_main_stext[0][] = "market_mains0text01.gif";
				$shop_main_stext[0][] = "market_mains0text02.gif";
				$shop_main_stext[0][] = "market_mains0text03.gif";
				$shop_main_stext[0][] = "market_mains0text04.gif";
				$shop_main_stext[0][] = "market_mains0text05.gif";
				$shop_main_stext[0][] = "market_mains0text06.gif";

				$shop_main_stext[1][] = "market_mains1text01.gif";
				$shop_main_stext[1][] = "market_mains1text02.gif";
				$shop_main_stext[1][] = "market_mains1text03.gif";
				$shop_main_stext[1][] = "market_mains1text04.gif";
				$shop_main_stext[1][] = "market_mains1text07.gif";
				$shop_main_stext[1][] = "market_mains1text05.gif";
				$shop_main_stext[1][] = "market_mains1text06.gif";
				
				$shop_main_stext[2][] = "market_mains2text01.gif";
				$shop_main_stext[2][] = "market_mains2text02.gif";
				$shop_main_stext[2][] = "market_mains2text03.gif";

				$shop_main_stext[3][] = "market_mains3text01.gif";
				$shop_main_stext[3][] = "market_mains3text02.gif";
				$shop_main_stext[3][] = "market_mains3text03.gif";
				$shop_main_stext[3][] = "market_mains3text04.gif";
				$shop_main_stext[3][] = "market_mains3text05.gif";
				$shop_main_stext[3][] = "market_mains3text06.gif";

				$shop_main_slink[0][] = "market_notice.php";
				$shop_main_slink[0][] = "market_contentinfo.php";
				$shop_main_slink[0][] = "market_survey.php";
				$shop_main_slink[0][] = "market_partner.php";
				$shop_main_slink[0][] = "market_affiliatebanner.php";
				$shop_main_slink[0][] = "market_enginepage.php";
				
				$shop_main_slink[1][] = "market_eventpopup.php";
				$shop_main_slink[1][] = "market_quickmenu.php";
				$shop_main_slink[1][] = "market_newproductview.php";
				$shop_main_slink[1][] = "market_eventcode.php";
				$shop_main_slink[1][] = "market_eventbrand.php";
				$shop_main_slink[1][] = "market_eventprdetail.php";
				$shop_main_slink[1][] = "product_giftlist.php";
				
				$shop_main_slink[2][] = "market_couponnew.php";
				$shop_main_slink[2][] = "market_couponsupply.php";
				$shop_main_slink[2][] = "market_couponlist.php";

				$shop_main_slink[3][] = "market_smsconfig.php";
				$shop_main_slink[3][] = "market_smssendlist.php";
				$shop_main_slink[3][] = "market_smssinglesend.php";
				$shop_main_slink[3][] = "market_smsgroupsend.php";
				$shop_main_slink[3][] = "market_smsaddressbook.php";
				$shop_main_slink[3][] = "market_smsfill.php";

				$shop_main_sinfo[0][] = "공지사항을 등록/수정/삭제 하실 수 있습니다.";
				$shop_main_sinfo[0][] = "정보(information)를 등록/수정/삭제 하실 수 있습니다.";
				$shop_main_sinfo[0][] = "온라인투표를 등록/수정/삭제 하실 수 있습니다.";
				$shop_main_sinfo[0][] = "제휴사 관리 및 제휴배너를 통한 접속자,주문통계를 확인하실 수 있습니다.";
				$shop_main_sinfo[0][] = "로그인 페이지 우측에 등록될 배너를 관리하실 수 있습니다.";
				$shop_main_sinfo[0][] = "가격비교 서비스 업체에 제공할 상품 정보 페이지를 관리합니다.";
				
				$shop_main_sinfo[1][] = "이벤트, 긴급공지시 메인페이지 팝업창을 통해 고객에게 이벤트 내용을 알릴 수 있습니다.";
				$shop_main_sinfo[1][] = "쇼핑몰 전체페이지에서 항상 따라다니는 우측의 Quick메뉴를 관리할 수 있습니다. 쇼핑 편의 및 이벤트 홍보를 위해 활용하세요.";
				$shop_main_sinfo[1][] = "선택한 상품이 쇼핑몰 우측에 배너형식으로 따라다니면서 보여주는 기능 입니다.";
				$shop_main_sinfo[1][] = "각 카테고리별 페이지 상단에 이미지 또는 Html 편집을 통해 이벤트를 관리 하실 수 있습니다.";
				$shop_main_sinfo[1][] = "각 브랜드별 페이지 상단에 이미지 또는 Html 편집을 통해 이벤트를 관리 하실 수 있습니다.";
				$shop_main_sinfo[1][] = "상품 상세페이지 정보란에  진행중인 이벤트를 표기할 수 있습니다.";
				$shop_main_sinfo[1][] = "상품 주문시 가격대별로 선택이 가능한 무료 사은품을 관리합니다.";

				$shop_main_sinfo[2][] = "회원들에게 자유롭게 쿠폰발행 서비스를 진행할 수 있습니다.";
				$shop_main_sinfo[2][] = "생성된 쿠폰은 (전체 회원 발급, 회원 등급별 발급, 회원 개별 발급) 구분해서 발급 할 수 있습니다.";
				$shop_main_sinfo[2][] = "현재 진행중인 쿠폰내역과 정보를 확인할 수 있는 메뉴 입니다.";

				$shop_main_sinfo[3][] = "SMS 문자서비스 기본환경과  설정메뉴를 관리할 수 있습니다.";
				$shop_main_sinfo[3][] = "SMS 문자서비스 발송에 대한 상세내역을 관리할 수 있습니다.";
				$shop_main_sinfo[3][] = "개별사용 또는 특정 고객에게 SMS를 발송할 수 있습니다.";
				$shop_main_sinfo[3][] = "전체회원/등급회원/생일회원에게 단체 SMS 발송을 할 수 있습니다.";
				$shop_main_sinfo[3][] = "휴대폰 번호로 SMS 주소록을 만들어 회원관리를 할 수 있습니다.";
				$shop_main_sinfo[3][] = "SMS 발송시 필요한 사용료를 충전합니다.";

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
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"market_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"market_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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
			<tr><td height="30"></td></tr>
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
