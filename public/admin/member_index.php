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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 회원관리 &gt; <span class="2depth_select">회원관리 메인</span></td>
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
			<?php include("menu_member.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td><img src="images/member_maintitle.gif" border="0"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="99%" style="table-layout:fixed">
				<col width="50%"></col>
				<col width="50%"></col>
<?php
				$shop_main_title[] = "member_mainstitle1.gif";
				$shop_main_title[] = "member_mainstitle2.gif";
				$shop_main_title[] = "member_mainstitle3.gif";

				$shop_main_stext[0][] = "member_mains0text01.gif";
				$shop_main_stext[0][] = "member_mains0text02.gif";
				$shop_main_stext[0][] = "member_mains0text03.gif";

				$shop_main_stext[1][] = "member_mains1text01.gif";
				$shop_main_stext[1][] = "member_mains1text02.gif";
				$shop_main_stext[1][] = "member_mains1text03.gif";
				
				$shop_main_stext[2][] = "member_mains2text01.gif";
				$shop_main_stext[2][] = "member_mains2text02.gif";
				$shop_main_stext[2][] = "member_mains2text03.gif";
				$shop_main_stext[2][] = "member_mains2text04.gif";
				$shop_main_stext[2][] = "member_mains2text05.gif";

				$shop_main_slink[0][] = "member_list.php";
				$shop_main_slink[0][] = "member_outlist.php";
				$shop_main_slink[0][] = "member_excelupload.php";

				$shop_main_slink[1][] = "member_groupnew.php";
				$shop_main_slink[1][] = "member_groupmemreg.php";
				$shop_main_slink[1][] = "member_groupmemberview.php";
				
				$shop_main_slink[2][] = "member_mailsend.php";
				$shop_main_slink[2][] = "member_mailallsend.php";
				$shop_main_slink[2][] = "member_mailallsendinfo.php";
				$shop_main_slink[2][] = "javascript:parent.topframe.GoMenu(7,'market_smssinglesend.php');";
				$shop_main_slink[2][] = "javascript:parent.topframe.GoMenu(7,'market_smsgroupsend.php');";

				$shop_main_sinfo[0][] = "회원을 검색하거나 회원 상세내용을 조회/수정/탈퇴/암호변경/기타 처리를 할 수 있습니다.";
				$shop_main_sinfo[0][] = "쇼핑몰에서 등록된 회원 조회 및 탈퇴관리할 수 있는 페이지 입니다.";
				$shop_main_sinfo[0][] = "다수의 회원정보를 엑셀파일로 만들어 일괄 등록하실 수 있습니다.";
				
				$shop_main_sinfo[1][] = "회원등급 신규등록/수정/삭제를 하실 수 있으며 등급별 권한설정이 가능합니다.";
				$shop_main_sinfo[1][] = "회원검색을 통해 회원특성에 맞는 등급으로 변경 관리할 수 있습니다.";
				$shop_main_sinfo[1][] = "등급별 등록된 회원정보를 조회/관리가 가능합니다.";

				$shop_main_sinfo[2][] = "쇼핑몰 회원중 특정회원 한명에게 메일을 발송할 수 있습니다.";
				$shop_main_sinfo[2][] = "쇼핑몰 전체회원 또는 등급별 회원에게 메일을 발송할 수 있습니다.";
				$shop_main_sinfo[2][] = "쇼핑몰 전체회원 또는 등급회원에게 발송한 내역 및 발송여부를 확인할 수 있습니다.";
				$shop_main_sinfo[2][] = "특정 고객에게 SMS를 발송할 수 있습니다.";
				$shop_main_sinfo[2][] = "전체회원/등급회원/생일회원에게 단체 SMS 발송을 할 수 있습니다.";

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
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"member_fontcolor\">{$shop_main_sinfo[$i][$j]}</td>\n";
					echo "	<td style=\"padding-left:21px\" valign=\"top\" class=\"member_fontcolor\">{$shop_main_sinfo[$i][$k]}</td>\n";
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
