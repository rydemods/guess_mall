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
<div class="admin_linemap"><div class="line"><p>현재위치 : 공구/경매 &gt; 공동구매관리 &gt;<span>가격고정형 공동구매 설정</span></p></div></div>

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
				<div class="title_depth3">가격변동형 공동구매 설정</div>
				<!-- 소제목 -->
				<div class="title_depth3_sub"><span>가격이 고정된 공동구매 설정 방법에 대해서 안내해드립니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<!-- 소제목 -->
				<div class="title_depth3_sub">가격고정형 공동구매?
                	<span>가격 고정형 공동구매란 일반적인 가격변동형 공동구매(참여자수에 따라 가격변동)와는 달리 공구가가 확정되어진 공동구매로, 상품 진열시 일반상품 진열방식 대신, <br />공동구매 타입으로 상품 진열 방식을 바꾸어 출력하는 방법입니다.</span>
                </div>
                </td>
            </tr>
			<tr>
				<td><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle"><b>가격고정형 공동구매 상품</b>
                </td>
			</tr>
			<tr>
				<td style="padding-left:14px;">
               		<div class="table_style01">
					<table cellpadding="0" cellspacing="0" width="100%" style="border-left:1px solid #b9b9b9;">
					<tr>
						<td align="center" width="50%"><img src="images/gong_gongfixset_img01_01.gif" border="0"></th>
						<td align="center" width="50%"><img src="images/gong_gongfixset_img02_01.gif" border="0"></th>
					</tr>
					<tr>
						<td align="center"><b>[카테고리 리스트 진열 방식]</b></td>
						<td align="center"><b>[상품 상세설명 페이지 진열 방식]</b></td>
					</tr>
					</table>
                    </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">가격고정형 공동구매 설정방법</div>
				</td>
			</tr>
			<tr>
			<tr>
				<td>
                <img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle"><a href="javascript:parent.topframe.GoMenu(4,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span></a> 에서 상품진열 템플릿을 <b>가격고정형 공동구매 디자인(공구형)</b>으로 선택한다.
                </td>
			</tr>
			<tr>
				<td style="padding-left:8px;">
                	<div class="table_style01">
						<table cellpadding="0" cellspacing="0" width="100%" style="border-left:1px solid #b9b9b9;">
						<tr>
							<td align="center"><img src="images/gong_gongfixset_img03.gif" border="0"></td>
						</tr>
						</table>
                    </div>
				</td>
			</tr>
			<tr>
				<td><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle"><a href="javascript:parent.topframe.GoMenu(4,'product_code.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 카테고리 관리</span></a> 에서 상품진열 템플릿을 가격고정형 공동구매 디자인(공구형)으로 선택한다.
                </td>
			</tr>
			<tr>
				<td style="padding-left:8px;">
                	<div class="table_style01">
						<table cellpadding="0" cellspacing="0" width="100%" style="border-left:1px solid #b9b9b9;">
						<tr>
							<td align="center"><img src="images/gong_gongfixset_img05.gif" border="0"></td>
						</tr>
						</table>
					</div>
                </td>
			</tr>
			<tr>
				<td><img src="images/gong_gongfixset_icon.gif" border="0" align="absmiddle">가격고정형 공동구매 타입으로 설정된 카테고리에 상품을 등록합니다.(<a href="javascript:parent.topframe.GoMenu(4,'gong_gongfixreg.php');"><span class="font_blue">공구/경매 > 공동구매관리 > 가격고정형 공동구매 등록</a></span>)
                </td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>가격고정형 공동구매 설정</span></dt>
							<dd>
								- 가격고정형 공동구매 타입으로 설정된 카테고리는 관리자 모드에서 "카테고리명(공구형)" 형식으로 문구가 추가 표기됩니다.
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