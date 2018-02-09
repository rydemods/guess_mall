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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상품관리 &gt; <span class="2depth_select">상품관리 메인</span></td>
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
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/product_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "product_mainstitle1.gif";
				$shop_main_title[] = "product_mainstitle2.gif";
				$shop_main_title[] = "product_mainstitle3.gif";
				$shop_main_title[] = "product_mainstitle4.gif";
				$shop_main_title[] = "product_mainstitle5.gif";
				$shop_main_title[] = "product_mainstitle6.gif";
				$shop_main_title[] = "product_mainstitle7.gif";

				$shop_main_stext[0][] = "product_mains0text01.gif";
				$shop_main_stext[0][] = "product_mains0text02.gif";
				$shop_main_stext[0][] = "product_mains0text12.gif";
				$shop_main_stext[0][] = "product_mains0text03.gif";
				$shop_main_stext[0][] = "product_mains0text04.gif";
				$shop_main_stext[0][] = "product_mains0text05.gif";
				$shop_main_stext[0][] = "product_mains0text06.gif";
				$shop_main_stext[0][] = "product_mains0text07.gif";
				$shop_main_stext[0][] = "product_mains0text08.gif";
				$shop_main_stext[0][] = "product_mains0text09.gif";
				$shop_main_stext[0][] = "product_mains0text10.gif";
				$shop_main_stext[0][] = "product_mains0text11.gif";

				$shop_main_stext[1][] = "product_mains1text01.gif";
				$shop_main_stext[1][] = "product_mains1text02.gif";
				
				$shop_main_stext[2][] = "product_mains2text01.gif";
				$shop_main_stext[2][] = "product_mains2text02.gif";
				
				$shop_main_stext[3][] = "product_mains3text01.gif";
				$shop_main_stext[3][] = "product_mains3text02.gif";
				$shop_main_stext[3][] = "product_mains3text03.gif";
				$shop_main_stext[3][] = "product_mains3text04.gif";
				$shop_main_stext[3][] = "product_mains3text05.gif";
				$shop_main_stext[3][] = "product_mains3text06.gif";
				$shop_main_stext[3][] = "product_mains3text07.gif";

				$shop_main_stext[4][] = "product_mains4text01.gif";
				$shop_main_stext[4][] = "product_mains4text02.gif";
				$shop_main_stext[4][] = "product_mains4text03.gif";
				$shop_main_stext[4][] = "product_mains4text04.gif";
				$shop_main_stext[4][] = "product_mains4text05.gif";
				$shop_main_stext[4][] = "product_mains4text06.gif";

				$shop_main_stext[5][] = "product_mains5text01.gif";

				$shop_main_stext[6][] = "product_mains6text01.gif";

				$shop_main_slink[0][] = "product_code.php";
				$shop_main_slink[0][] = "product_register.php";
				$shop_main_slink[0][] = "product_assemble.php";
				$shop_main_slink[0][] = "product_mainlist.php";
				$shop_main_slink[0][] = "product_codelist.php";
				$shop_main_slink[0][] = "product_sort.php";
				$shop_main_slink[0][] = "product_copy.php";
				$shop_main_slink[0][] = "product_theme.php";
				$shop_main_slink[0][] = "product_detaillist.php";
				$shop_main_slink[0][] = "product_deliinfo.php";
				$shop_main_slink[0][] = "product_brand.php";
				$shop_main_slink[0][] = "product_business.php";

				$shop_main_slink[1][] = "product_imgmulticonfig.php";
				$shop_main_slink[1][] = "product_imgmultiset.php";

				$shop_main_slink[2][] = "product_collectionconfig.php";
				$shop_main_slink[2][] = "product_collectionlist.php";
				
				$shop_main_slink[3][] = "product_allupdate.php";
				$shop_main_slink[3][] = "product_reserve.php";
				$shop_main_slink[3][] = "product_price.php";
				$shop_main_slink[3][] = "product_allsoldout.php";
				$shop_main_slink[3][] = "product_allquantity.php";
				$shop_main_slink[3][] = "product_excelupload.php";
				$shop_main_slink[3][] = "product_exceldownload.php";

				$shop_main_slink[4][] = "product_giftlist.php";
				$shop_main_slink[4][] = "product_estimate.php";
				$shop_main_slink[4][] = "product_review.php";
				$shop_main_slink[4][] = "product_wishlist.php";
				$shop_main_slink[4][] = "product_keywordsearch.php";
				$shop_main_slink[4][] = "product_detailfilter.php";

				$shop_main_slink[5][] = "product_option.php";

				$shop_main_slink[6][] = "product_package.php";

				$shop_main_sinfo[0][] = "카테고리 추가, 수정, 삭제가 가능하며 카테고리별 템플릿을 선택할 수 있습니다.";
				$shop_main_sinfo[0][] = "상품 등록/수정/삭제를 관리할 수 있습니다.";
				$shop_main_sinfo[0][] = "코디/조립 상품으로 등록된 상품의 구성 상품 등록/수정/삭제 할 수 있습니다.";
				$shop_main_sinfo[0][] = "쇼핑몰 메인페이지에 신상품, 인기상품, 추천상품, 특별상품에 진열할 상품을 등록할 수 있습니다.";
				$shop_main_sinfo[0][] = "쇼핑몰 카테고리페이지에 신상품, 인기상품, 추천상품에 진열할 상품을 등록할 수 있습니다.";
				$shop_main_sinfo[0][] = "각각의 카테고리에 등록된 상품의 진열 순서를 변경할 수 있습니다.";
				$shop_main_sinfo[0][] = "상품을 카테고리에서 다른 카테고리로 이동/복사 할수 있으며 또한 삭제도 가능합니다.";
				$shop_main_sinfo[0][] = "가상카테고리의 상품을 등록/관리할 수 있습니다.";
				$shop_main_sinfo[0][] = "상품 상세페이지에서 노출되는 각상품의  상세항목 순서를 변경할 수 있습니다.";
				$shop_main_sinfo[0][] = "배송/교환/환불정보 관련된 내용을 상품상세화면 하단에 공통적으로 노출할 수 있도록 설정하실 수 있습니다.";
				$shop_main_sinfo[0][] = "브랜드 추가, 수정, 삭제가 가능하며 브랜드 관련 페이지의 출력 설정을 할 수 있습니다.";
				$shop_main_sinfo[0][] = "상품 거래처의 등록/수정/삭제를 관리할 수 있습니다.";
				
				$shop_main_sinfo[1][] = "쇼핑몰 상품의 다중이미지의 디스플레이 위치를 설정할 수 있습니다.";
				$shop_main_sinfo[1][] = "상품등록시 대/중/소 이미지 외 10여개의 이미지를 다중으로 더 보여줄 수 있습니다.";

				$shop_main_sinfo[2][] = "상품 상세페이지에서 관련상품의 진열여부 및 진열상품수, 진열위치을 설정할 수 있습니다.";
				$shop_main_sinfo[2][] = "상품 상세페이지에 보여질 관련상품 등록/삭제할 수 있습니다.";
				
				$shop_main_sinfo[3][] = "등록된 상품의 가격을 포함한 적립금,수량 등을 일괄 수정할 수 있습니다.";
				$shop_main_sinfo[3][] = "쇼핑몰에 등록된 모든 상품의 적립금을 일괄 수정할 수 있습니다.";
				$shop_main_sinfo[3][] = "쇼핑몰에 등록된 모든 상품의 가격을 일괄 수정할 수 있습니다.";
				$shop_main_sinfo[3][] = "품절된 상품을 전체적으로 삭제/등록 등 관리를 하실 수 있습니다.";
				$shop_main_sinfo[3][] = "모든 상품의 재고를 확인할 수 있습니다.";
				$shop_main_sinfo[3][] = "다수 상품정보를 엑셀파일로 작성하여 일괄 등록하실 수 있습니다.";
				$shop_main_sinfo[3][] = "상품정보 Excel(.csv) 형식으로 다운로드할 수 있습니다.";

				$shop_main_sinfo[4][] = "상품 주문시 가격대별로 선택이 가능한 무료 사은품을 관리합니다.";
				$shop_main_sinfo[4][] = "쇼핑몰 카테고리별 견적서 관리할 수 있습니다.";
				$shop_main_sinfo[4][] = "쇼핑몰 전체 상품들의 리뷰를 관리할 수 입니다.";
				$shop_main_sinfo[4][] = "Wishlist에 보관한 상품을 확인하실 수 있습니다.";
				$shop_main_sinfo[4][] = "쇼핑몰의 모든 상품을 상품명 및 키워드로 검색 하실 수 있습니다";
				$shop_main_sinfo[4][] = "상품상세정보란에 상품상세내역을 단어 필터링을 통해 출력해 주는 기능입니다.";
				
				$shop_main_sinfo[5][] = "상품에 등록/수정시 지정할 옵션그룹을 등록할 수 있습니다.";

				$shop_main_sinfo[6][] = "상품에 등록/수정시 지정할 패키지를 등록할 수 있습니다.";

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
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"product_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"product_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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
