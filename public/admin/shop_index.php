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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; <span class="2depth_select">상점관리 메인</span></td>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/shop_main_title.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "shop_main_stitle1.gif";
				$shop_main_title[] = "shop_main_stitle2.gif";
				$shop_main_title[] = "shop_main_stitle3.gif";
				$shop_main_title[] = "shop_main_stitle4.gif";

				$shop_main_stext[0][] = "shop_main_s0text01.gif";
				$shop_main_stext[0][] = "shop_main_s0text02.gif";
				$shop_main_stext[0][] = "shop_main_s0text03.gif";
				$shop_main_stext[0][] = "shop_main_s0text04.gif";
				$shop_main_stext[0][] = "shop_main_s0text05.gif";
				$shop_main_stext[0][] = "shop_main_s0text06.gif";

				$shop_main_stext[1][] = "shop_main_s1text01.gif";
				$shop_main_stext[1][] = "shop_main_s1text09.gif";
				$shop_main_stext[1][] = "shop_main_s1text02.gif";
				$shop_main_stext[1][] = "shop_main_s1text03.gif";
				$shop_main_stext[1][] = "shop_main_s1text10.gif";
				$shop_main_stext[1][] = "shop_main_s1text04.gif";
				$shop_main_stext[1][] = "shop_main_s1text05.gif";
				$shop_main_stext[1][] = "shop_main_s1text06.gif";
				$shop_main_stext[1][] = "shop_main_s1text07.gif";
				$shop_main_stext[1][] = "shop_main_s1text08.gif";
				
				$shop_main_stext[2][] = "shop_main_s2text12.gif";
				$shop_main_stext[2][] = "shop_main_s2text11.gif";
				$shop_main_stext[2][] = "shop_main_s2text01.gif";
				$shop_main_stext[2][] = "shop_main_s2text02.gif";
				$shop_main_stext[2][] = "shop_main_s2text03.gif";
				$shop_main_stext[2][] = "shop_main_s2text04.gif";
				$shop_main_stext[2][] = "shop_main_s2text05.gif";
				$shop_main_stext[2][] = "shop_main_s2text06.gif";
				$shop_main_stext[2][] = "shop_main_s2text07.gif";
				$shop_main_stext[2][] = "shop_main_s2text08.gif";
				$shop_main_stext[2][] = "shop_main_s2text09.gif";
				$shop_main_stext[2][] = "shop_main_s2text10.gif";
				
				$shop_main_stext[3][] = "shop_main_s3text02.gif";
				$shop_main_stext[3][] = "shop_main_s3text01.gif";
				$shop_main_stext[3][] = "shop_main_s3text03.gif";
				$shop_main_stext[3][] = "shop_main_s3text04.gif";

				$shop_main_slink[0][] = "shop_basicinfo.php";
				$shop_main_slink[0][] = "shop_keyword.php";
				$shop_main_slink[0][] = "shop_mainintro.php";
				$shop_main_slink[0][] = "shop_companyintro.php";
				$shop_main_slink[0][] = "shop_agreement.php";
				$shop_main_slink[0][] = "shop_privercyinfo.php";

				$shop_main_slink[1][] = "shop_openmethod.php";
				$shop_main_slink[1][] = "shop_layout.php";
				$shop_main_slink[1][] = "shop_displaytype.php";
				$shop_main_slink[1][] = "shop_mainproduct.php";
				$shop_main_slink[1][] = "shop_productshow.php";
				$shop_main_slink[1][] = "shop_mainleftinform.php";
				$shop_main_slink[1][] = "shop_logobanner.php";
				$shop_main_slink[1][] = "shop_orderform.php";
				$shop_main_slink[1][] = "shop_ssl.php";
				$shop_main_slink[1][] = "shop_bizsiren.php";
				
				$shop_main_slink[2][] = "shop_tag.php";
				$shop_main_slink[2][] = "shop_search.php";
				$shop_main_slink[2][] = "shop_reserve.php";
				$shop_main_slink[2][] = "shop_recommand.php";
				$shop_main_slink[2][] = "shop_member.php";
				$shop_main_slink[2][] = "shop_deli.php";
				$shop_main_slink[2][] = "shop_payment.php";
				$shop_main_slink[2][] = "shop_escrow.php";
				$shop_main_slink[2][] = "shop_return.php";
				$shop_main_slink[2][] = "shop_basket.php";
				$shop_main_slink[2][] = "shop_review.php";
				$shop_main_slink[2][] = "shop_estimate.php";
				
				$shop_main_slink[3][] = "shop_rolelist.php";
				$shop_main_slink[3][] = "shop_adminlist.php";
				$shop_main_slink[3][] = "shop_iplist.php";
				$shop_main_slink[3][] = "shop_changeadminpasswd.php";

				$shop_main_sinfo[0][] = "사업자등록정보, 상점정보 등등 기본적인 내용을 관리합니다.";
				$shop_main_sinfo[0][] = "쇼핑몰 상단에 표시되는 타이틀바와 검색용 메타테그를 설정합니다.";
				$shop_main_sinfo[0][] = "쇼핑몰 메인 중앙의 타이틀 이미지 디자인 영역을 관리합니다.";
				$shop_main_sinfo[0][] = "회사소개와 연혁, 약도등를 설정합니다.";
				$shop_main_sinfo[0][] = "쇼핑몰 이용약관을 설정합니다.";
				$shop_main_sinfo[0][] = "개인정보취급방침, 정보책임자 정보를 설정합니다.";

				$shop_main_sinfo[1][] = "특수 쇼핑몰을 위한 회원/비회원, 성인몰인증 여부를 설정할 수 있습니다.";
				$shop_main_sinfo[1][] = "쇼핑몰 전체사이즈 조절 및 복사방지 기능을 관리할 수 있습니다.";
				$shop_main_sinfo[1][] = "프레임, 페이지 정렬설정, 상품상세입력의 웹편집기 사용을 일괄 적용할 수 있습니다.";
				$shop_main_sinfo[1][] = "쇼핑몰의 메인 상품 및 카테고리 상품의 진열수와 메인 상품 진열 타입을 설정할 수 있습니다.";
				$shop_main_sinfo[1][] = "쇼핑몰의 상품 진열 관련 설정을 할 수 있습니다.";
				$shop_main_sinfo[1][] = "쇼핑몰 메인 왼쪽 하단 공간에 고객에게 알리는 계좌번호, 필수 공지사항 등을 등록할 수 있습니다.";
				$shop_main_sinfo[1][] = "쇼핑몰의 로고 및 배너를 등록/관리하실 수 있습니다.";
				$shop_main_sinfo[1][] = "회원가입 및 주문시 쇼핑몰 운영자의 메세지를 등록할 수 있습니다.";
				$shop_main_sinfo[1][] = "안전한 데이터 전송을 위한 SSL(보안서버) 기능 설정을 할 수 있습니다.";
				$shop_main_sinfo[1][] = "실명인증 정보를 등록할 경우 성인몰의 성인인증 및 본인확인 처리가 가능합니다.";
				
				$shop_main_sinfo[2][] = "상품의 태그(Tag) 관련 기능을 설정하실 수 있습니다.";
				$shop_main_sinfo[2][] = "상품검색의 인기검색어 관련 기능을 설정하실 수 있습니다.";
				$shop_main_sinfo[2][] = "구매자에 대한 적립금/쿠폰 지급 조건과 사용가능 조건, 기본 지급비율을 설정할 수 있습니다.";
				$shop_main_sinfo[2][] = "정회원 가입을 추천한 추천인에게 각종 혜택을 부여할 수 있습니다.";
				$shop_main_sinfo[2][] = "기본 회원가입 입력폼 + 추가 입력폼 , 주민번호 사용 및 탈퇴설정을 할 수 있습니다.";
				$shop_main_sinfo[2][] = "상품 배송관련 조건을 쇼핑몰 성격에 맞게 설정하실 수 있습니다.";
				$shop_main_sinfo[2][] = "현금/신용카드 결제시 할인율, 최소구매액, 무이자할부, 신용카드 수수료등을 설정할 수 있습니다.";
				$shop_main_sinfo[2][] = "쇼핑몰의 결제대금 예치제(에스크로)의 조건 설정을 하실 수 있습니다.";
				$shop_main_sinfo[2][] = "교환/반품/환불에 대한 설정을 하실 수 있습니다.";
				$shop_main_sinfo[2][] = "장바구니에 관련된 기능을 설정할 수 있습니다.";
				$shop_main_sinfo[2][] = "상품사용후기의 사용여부, 게시방식, 작성권한을 설정할 수 있습니다.";
				$shop_main_sinfo[2][] = "쇼핑몰의 상품견적서 기능을 설정할 수 있습니다.";
				
				$shop_main_sinfo[3][] = "관리페이지 메뉴별 접근권한 그룹을 관리합니다.";
				$shop_main_sinfo[3][] = "운영자/부운영자 정보 관리 및 생성, 부운영자 별 메뉴 사용권한/접속제한 등을 설정할 수 있습니다.";
				$shop_main_sinfo[3][] = "관리페이지에 접근할 수 있는 운영자/부운영자 IP를 관리합니다.";
				$shop_main_sinfo[3][] = "운영자/부운영자별 본인의 패스워드를 변경할 수 있습니다.";

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
						echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"shop_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
						echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"shop_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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
