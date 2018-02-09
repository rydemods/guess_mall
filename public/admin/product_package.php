<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-7";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 320;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 카테고리/상품관리 &gt;<span>패키지 등록 관리</span></p></div></div>
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
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">패키지 등록 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품에 등록/수정시 지정할 패키지를 등록할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%" height="1090">
				<tr>
					<td width="100%" valign="top" height="100%"><DIV style="position:relative;z-index:1;width:100%;height:100%;bgcolor:#FFFFFF;"><IFRAME name="ListFrame" id="ListFrame" src="product_package.list.php" width=100% height=1000 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></div></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>패키지 그룹 등록/수정/삭제 주의사항</span></dt>
							<dd>
							- 패키지로 구성된 상품중 코디/조립상품이 있을 경우 해당 상품은 자동 제외 됩니다.<br>
							- 패키지 구성시 본사의 상품만 등록이 가능 합니다.<br>
							- 패키지 판매가격은 해당 패키지를 구성한 상품들의 현재 판매가격의 합계에 대한 할인/할증을 지정할 수 있습니다.<br>
													<b>&nbsp;</b>&nbsp;※ 패키지를 구성하는 상품들의 판매가격 변동될 경우 변동된 판매가격에 대한 할인/할증이 적용됩니다.<br>
							- 패키지 선택시 필수 또는 미필수로 지정할 수 있습니다.<br>
							- 패키지로 구성된 상품에서 참조하는 값은 상품명, 판매가격, 재고량, 상품 소(小) 이미지, 상품진열여부만 참조합니다.
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
<?php 
include("copyright.php");
