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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 주문/매출 &gt; <span class="2depth_select">주문/매출 메인</span></td>
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
			<?php include("menu_order.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/order_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "order_mainstitle1.gif";
				$shop_main_title[] = "order_mainstitle2.gif";
				$shop_main_title[] = "order_mainstitle3.gif";

				$shop_main_stext[0][] = "order_mains0text01.gif";
				$shop_main_stext[0][] = "order_mains0text02.gif";
				$shop_main_stext[0][] = "order_mains0text03.gif";
				$shop_main_stext[0][] = "order_mains0text04.gif";
				$shop_main_stext[0][] = "order_mains0text05.gif";
				$shop_main_stext[0][] = "order_mains0text06.gif";
				$shop_main_stext[0][] = "order_mains0text07.gif";
				$shop_main_stext[0][] = "order_mains0text08.gif";

				$shop_main_stext[1][] = "order_mains1text01.gif";
				$shop_main_stext[1][] = "order_mains1text02.gif";
				$shop_main_stext[1][] = "order_mains1text03.gif";
				
				$shop_main_stext[2][] = "order_mains2text01.gif";
				$shop_main_stext[2][] = "order_mains2text02.gif";
				$shop_main_stext[2][] = "order_mains2text03.gif";
				$shop_main_stext[2][] = "order_mains2text04.gif";

				$shop_main_slink[0][] = "order_list.php";
				$shop_main_slink[0][] = "order_delay.php";
				$shop_main_slink[0][] = "order_delisearch.php";
				$shop_main_slink[0][] = "order_namesearch.php";
				$shop_main_slink[0][] = "order_monthsearch.php";
				$shop_main_slink[0][] = "order_tempinfo.php";
				$shop_main_slink[0][] = "order_excelinfo.php";
				$shop_main_slink[0][] = "order_csvdelivery.php";

				$shop_main_slink[1][] = "order_basket.php";
				$shop_main_slink[1][] = "order_allsale.php";
				$shop_main_slink[1][] = "order_eachsale.php";
				
				$shop_main_slink[2][] = "order_taxsaveabout.php";
				$shop_main_slink[2][] = "order_taxsaveconfig.php";
				$shop_main_slink[2][] = "order_taxsavelist.php";
				$shop_main_slink[2][] = "order_taxsaveissue.php";

				$shop_main_sinfo[0][] = "일자별 쇼핑몰의 모든 주문현황 및 주문내역을 확인/처리하실 수 있습니다.";
				$shop_main_sinfo[0][] = "미배송/미입금 처리된 주문 관리를 하실 수 있습니다.";
				$shop_main_sinfo[0][] = "입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.";
				$shop_main_sinfo[0][] = "주문자 이름 및 주문가격 등으로 주문현황 및 주문내역을 확인하실 수 있습니다.";
				$shop_main_sinfo[0][] = "해당 상품을 주문한 주문서를 확인하실 수 있습니다.";
				$shop_main_sinfo[0][] = "쇼핑몰에서의 결제시도 건에 대한 현황 및 관리를 하실 수 있습니다.";
				$shop_main_sinfo[0][] = "주문리스트를 엑셀파일로 다운로드할 경우, 주문리스트의 각 항목 및 배열순서를 설정할 수 있습니다.";
				$shop_main_sinfo[0][] = "다수 주문건의 배송정보를 엑셀파일로 만들어 주문리스트에 일괄 반영하는 기능입니다.";
				
				$shop_main_sinfo[1][] = "고객이 장바구니에 담은 상품을 확인할 수 있으며, 그에 따른 분석이 가능합니다.";
				$shop_main_sinfo[1][] = "전체 상품의 매출정보를 확인하실 수 있습니다.";
				$shop_main_sinfo[1][] = "개별상품의 매출정보를 확인하실 수 있습니다.";

				$shop_main_sinfo[2][] = "현금영수증 제도에 대한 소개와 현금영수증 서비스를 위한 쇼핑몰의 서비스 신청 절차 안내입니다.";
				$shop_main_sinfo[2][] = "현금영수증 발급을 위한 사업자정보를 관리하실 수 있습니다.";
				$shop_main_sinfo[2][] = "현금영수증 발급신청 조회 및 발급내역 확인이 가능합니다.";
				$shop_main_sinfo[2][] = "현금영수증을 개별적으로 발급요청이 가능합니다.";

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
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"order_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"order_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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
