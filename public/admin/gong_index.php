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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 공구/경매 &gt; <span class="2depth_select">공구/경매 메인</span></td>
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
			<?php include("menu_gong.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/gong_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "gong_mainstitle1.gif";
				$shop_main_title[] = "gong_mainstitle2.gif";
				$shop_main_title[] = "gong_mainstitle3.gif";

				$shop_main_stext[0][] = "gong_mains0text01.gif";
				
				$shop_main_stext[1][] = "gong_mains1text01.gif";
				$shop_main_stext[1][] = "gong_mains1text02.gif";
				
				$shop_main_stext[2][] = "gong_mains2text01.gif";
				$shop_main_stext[2][] = "gong_mains2text02.gif";
				$shop_main_stext[2][] = "gong_mains2text03.gif";
				$shop_main_stext[2][] = "gong_mains2text04.gif";

				$shop_main_slink[0][] = "gong_displayset.php";
				
				$shop_main_slink[1][] = "gong_auctionreg.php";
				$shop_main_slink[1][] = "gong_auctionlist.php";
				
				$shop_main_slink[2][] = "gong_gongchangereg.php";
				$shop_main_slink[2][] = "gong_gongchangelist.php";
				$shop_main_slink[2][] = "gong_gongfixset.php";
				$shop_main_slink[2][] = "gong_gongfixreg.php";

				$shop_main_sinfo[0][] = "경매 및 공동구매 페이지의 상품 디스플레이 설정을 하실 수 있습니다.";
				
				$shop_main_sinfo[1][] = "경매 상품의 등록 및 수정을 하실 수 있습니다.";
				$shop_main_sinfo[1][] = "등록된 경매를 관리할 수 있습니다.";
				
				$shop_main_sinfo[2][] = "공동구매 상품을 등록/수정하실 수 있습니다.";
				$shop_main_sinfo[2][] = "등록된 공동구매를 관리할 수 있습니다.";
				$shop_main_sinfo[2][] = "가격이 고정된 공동구매 설정 방법에 대해서 안내해드립니다.";
				$shop_main_sinfo[2][] = "가격이 고정된 공동구매 등록 방법에 대해서 안내해드립니다.";

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
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"gong_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"gong_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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