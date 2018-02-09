<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "go-3";
$MenuCode = "gong";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}
</script>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 공동구매관리 &gt; <span>가격고정형 공동구매 등록</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_gong.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>				
				<!-- 페이지 타이틀 -->
				<div class="title_depth3">가격변동형 공동구매 등록</div>
				<!-- 소제목 -->
				<div class="title_depth3_sub"><span>가격이 고정된 공동구매 등록 방법에 대해서 안내해드립니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="letter-spacing:-0.5pt;"><img src="images/gong_gongfixreg_icon.gif" border="0" align="absmiddle"><a href="javascript:parent.topframe.GoMenu(4,'product_register.php');"><b><span class="font_blue">상품관리 > 카테고리/상품관리 > 상품 등록/수정/삭제</span></a> 에서 가격고정형&nbsp;공동구매&nbsp;타입으로&nbsp;지정된&nbsp;카테고리를&nbsp;선택합니다.</b></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td style="padding-top:1px;">
						<table cellpadding="0" cellspacing="0">
						<tr>
							<td><img src="images/gong_gongfixreg_img01.gif" border="0"></td>
							<td background="images/gong_gongfixreg_imgbg1.gif" style="letter-spacing:-0.5pt;">※ 가격고정형 공동구매 타입으로 설정된 카테고리는<br><b>&nbsp;&nbsp;</b>&nbsp;&nbsp;관리자 모드에서 <font color="#FF6600"><b>카테고리명 옆에(공구형)이란</b><br><b>&nbsp;&nbsp;</b>&nbsp;&nbsp;<b>문구가 추가 표기</b></font> 됩니다.</span></td>
						</tr>
						</table>
						<td>
						<td><img src="images/gong_gongfixreg_img03.gif" border="0"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height="40"></td></tr>
				<tr>
					<td style="letter-spacing:-0.5pt;"><img src="images/gong_gongfixreg_icon.gif" border="0" align="absmiddle"><b>가격고정형 공동구매 카테고리 선택 후 아래와 같이 공동구매 타입의 추가 항목을 설정하여 상품 등록을 합니다.</b></td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="images/gong_gongfixreg_img04.gif" border="0"></td>
						<td><img src="images/gong_gongfixreg_img05.gif" border="0"></td>
						<td><img src="images/gong_gongfixreg_img06.gif" border="0"></td>
					</tr>
					<tr>
						<td colspan="3" style="padding-left:2px;">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td bgcolor="#F0F0F0" style="padding:10pt;letter-spacing:-0.5pt;">
							① <b><font color="#FF6600">공구가/시작가</font></b> : 최종 판매할 금액(공구가)을 확정하여 입력합니다.(가격고정형 공동구매)<br>
							② <b><font color="#FF6600">수량</font></b> : 상품 재고량이며 [마감]선택시 해당 상품은 품절로 출력됩니다.<br>
							③ <b><font color="#FF6600">공구 판매수량 표시</font></b> : 상품 진열시에 판매수량으로 추가 출력되는 부분입니다. 예) 공구판매수량 100개, 설맞이 이벤트 세일 50개
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td colspan="3"><img src="images/gong_gongfixreg_imgdown.gif" border="0"></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>가격고정형 공구구매 등록</span></dt>
							<dd>
								- 가격고정형 공동구매 상품의 경우 상품 등록 및 진열 방식만 상이하며 해당 상품 구매시, 기존 일반 상품과 동일하게 처리됩니다.
							</dd>
						</dl>
					</div>
				</td>
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