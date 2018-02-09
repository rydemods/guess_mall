<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

include("access.php");
include("header.php"); 
?><table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 커뮤니티 &gt; <span class="2depth_select">커뮤니티 메인</span></td>
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
			<?php include("menu_community.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/community_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "community_mainstitle1.gif";

				$shop_main_stext[0][] = "community_mains0text01.gif";
				$shop_main_stext[0][] = "community_mains0text02.gif";
				$shop_main_stext[0][] = "community_mains0text03.gif";
				$shop_main_stext[0][] = "community_mains0text04.gif";
				$shop_main_stext[0][] = "community_mains0text05.gif";
				$shop_main_stext[0][] = "community_mains0text06.gif";

				$shop_main_slink[0][] = "community_list.php";
				$shop_main_slink[0][] = "community_register.php";
				$shop_main_slink[0][] = "community_article.php";
				$shop_main_slink[0][] = "community_notice.php";
				$shop_main_slink[0][] = "community_personal.php";
				$shop_main_slink[0][] = "community_schedule_year.php";

				$shop_main_sinfo[0][] = "등록된 게시판의 기능/디자인 변경 및 삭제처리를 할 수 있습니다.";
				$shop_main_sinfo[0][] = "커뮤니티 성격에 맞는 다양한 게시판을 생성할 수 있습니다.";
				$shop_main_sinfo[0][] = "등록된 게시판의 모든 게시물을 관리할 수 있습니다.";
				$shop_main_sinfo[0][] = "게시판 상단에 운영자 공지사항을 관리할 수 있습니다.";
				$shop_main_sinfo[0][] = "1:1 고객문의 게시판 설정 및 문의에 대한 답변 관리를 하실 수 있습니다.";
				$shop_main_sinfo[0][] = "쇼핑몰의 주요 일정을 관리하실 수 있습니다.";

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
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"community_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"community_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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