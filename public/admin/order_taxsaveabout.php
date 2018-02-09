<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
?>
<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
</script>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출 &gt; 현금영수증 관리 &gt; <span>현금영수증 제도란?</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">

	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_order.php"); ?>
			</td>

			<td width="20" valign="top"><img src="images/space01.gif" height="1" border="0" width="20"></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td height="8">
				</td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">현금영수증 제도란?</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>현금영수증 제도에 대한 소개와 현금영수증 서비스를 위한 쇼핑몰의 서비스 신청 절차 안내입니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><img src="images/order_taxsaveabout_st01.gif" border="0"></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><img src="images/order_taxsaveabout_img01.gif" border="0"></td>
						<td width="100%" background="images/order_taxsaveabout_imgbg.gif"></td>
						<td><img src="images/order_taxsaveabout_img02.gif" border="0"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="30"><hr size="1" noshade color="#F0F0F0"></td>
				</tr>
				<tr>
					<td><img src="images/order_taxsaveabout_st02.gif" border="0"></td>
				</tr>
				<tr>
					<td style="padding-top:4px;padding-left:20px;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><img src="images/order_taxsaveabout_t01.gif" border="0" align="absmiddle"><span style="font-size:8pt;"><font color="#0099CC">(<a href="http://taxsave.kcp.co.kr/Service03.html" target="_blank"><font color="#0099CC">http://taxsave.kcp.co.kr/Service03.html</font></a>)</span></font></td>
					</tr>
					<tr>
						<td style="letter-spacing:-0.5pt;">&nbsp;&nbsp;&nbsp;- KCP 전자결제 서비스 미사용 가맹점만 해당됩니다.<br>
						&nbsp;&nbsp;&nbsp;- 기존 KCP 전자결제 서비스 이용업체는 신청서를 따로 작성하실 필요가 없습니다.</td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td><img src="images/order_taxsaveabout_t02.gif" border="0"></td>
					</tr>
					<tr>
						<td style="letter-spacing:-0.5pt;">&nbsp;&nbsp;&nbsp;- 아래 양식으로 내용을 입력하신 후 본사에 현금영수증 사용을 요청하세요.<br>
						&nbsp;&nbsp;&nbsp;- 신청후 설정까지 1~2일 정도의 기간이 소요됩니다.<br>
						&nbsp;&nbsp;&nbsp;- 쇼핑몰 URL, 상점 ID, 사업자 등록번호, 가맹점 상호, 대표자명, 사업장 주소, 사업장 전화, MID, TID<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#0099CC"><span style="font-size:8pt; letter-spacing:-0.5pt;">(MID와 TID 정보는 KCP 전자 결제 서비스 미이용 가맹점만 보내주시면 됩니다.)</span></font></td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td><img src="images/order_taxsaveabout_t03.gif" border="0"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="30"><hr size="1" noshade color="#F0F0F0"></td>
				</tr>
				<tr>
					<td><img src="images/order_taxsaveabout_st03.gif" border="0"></td>
				</tr>
				<tr>
					<td style="padding-top:4px;padding-left:20px;letter-spacing:-0.5pt;">① 주문후 주문조회 상세 페이지에 '현금영수증 발급 신청' 버튼이 생성됩니다.<br>
					② 현금영수증 발급 버튼을 클릭하면 현금영수증 발급을 쇼핑몰에 신청하게 됩니다.</td>
				</tr>
				<tr>
					<td height="30"><hr size="1" noshade color="#F0F0F0"></td>
				</tr>
				<tr>
					<td><img src="images/order_taxsaveabout_st04.gif" border="0"></td>
				</tr>
				<tr>
					<td style="padding-left:20px;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><img src="images/order_taxsaveabout_t04.gif" border="0"></td>
					</tr>
					<tr>
						<td style="letter-spacing:-0.5pt;">&nbsp;&nbsp;&nbsp;- 쇼핑몰 운영자의 입금확인/취소단계에서 자동으로 현금영수증을 신청하신 고객에게 현금영수증이 발급/취소됩니다.<br>
						&nbsp;&nbsp;&nbsp;- 쇼핑몰 운영자의 입금완료 확인 후 익일 반영 및 조회 됩니다.</td>
					</tr>
					<tr>
						<td height="5"></td>
					</tr>
					<tr>
						<td><img src="images/order_taxsaveabout_t05.gif" border="0"></td>
					</tr>
					<tr>
						<td style="letter-spacing:-0.5pt;">&nbsp;&nbsp;&nbsp;- 쇼핑몰 운영자가 '현금영수증 발급/조회 화면'에서 현금영수증을 신청한 고객리스트를 확인하신 후 수동으로 발급/최소 처리해야합니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>현금영수증 제도</span></dt>
							<dd>
								- 현금영수증은 거래일로부터 48시간 안에 발행을 해야 합니다.<br>
								- 현금영수증 발행 취소의 경우는 시간 제한이 없습니다.
							</dd>
						</dl>
					</div>

				</td>
			</tr>
			<tr>
				<td height="50"></td>
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
<?=$onload?>
<?php 
include("copyright.php");
