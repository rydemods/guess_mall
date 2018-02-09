<?php
/********************************************************************* 
// 파 일 명		: shop_etc_agreement.php 
// 설     명		: 쇼핑몰 기타약관
// 상세설명	: 쇼핑몰 기타약관 설정
// 작 성 자		: 2016.07.28 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-1";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type = $_POST["type"];
$up_etc_agreement1 = addslashes($_POST["up_etc_agreement1"]);
$up_etc_agreement2 = addslashes($_POST["up_etc_agreement2"]);
$up_etc_agreement3 = addslashes($_POST["up_etc_agreement3"]);

if ($type == "up") {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	$flag = $row->cnt;
	pmysql_free_result($result);

	if ($flag) {
		$onload = "<script>alert('정보 수정이 완료되었습니다.'); </script>";
		$sql = "UPDATE tbldesign SET etc_agreement1 = '{$up_etc_agreement1}', etc_agreement2 = '{$up_etc_agreement2}', etc_agreement3 = '{$up_etc_agreement3}' ";
	} else {
		$onload = "<script>alert('정보 등록이 완료되었습니다.');</script>";
		$sql = "INSERT INTO tbldesign(etc_agreement1, etc_agreement2, etc_agreement3) VALUES('{$up_etc_agreement1}', '{$up_etc_agreement2}', '{$up_etc_agreement3}')";
	}
	pmysql_query($sql,get_db_conn());
}

$sql = "SELECT etc_agreement1, etc_agreement2, etc_agreement3 FROM tbldesign ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$flag = true;
	$etc_agreement1 = ($row->etc_agreement1=="<P>&nbsp;</P>"?"":$row->etc_agreement1);
	$etc_agreement1 = str_replace('\\','',$etc_agreement1);

	$etc_agreement2 = ($row->etc_agreement2=="<P>&nbsp;</P>"?"":$row->etc_agreement2);
	$etc_agreement2 = str_replace('\\','',$etc_agreement2);

	$etc_agreement3 = ($row->etc_agreement3=="<P>&nbsp;</P>"?"":$row->etc_agreement3);
	$etc_agreement3 = str_replace('\\','',$etc_agreement3);
}
pmysql_free_result($result);


include("header.php"); 
echo $onload;
?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script>
_editor_url = "htmlarea/";
function CheckForm(){
	var sHTML = oEditors.getById["ir1"].getIR();
	form1.up_etc_agreement1.value=sHTML;
	var sHTML2 = oEditors2.getById["ir2"].getIR();
	form1.up_etc_agreement2.value=sHTML2;
	var sHTML3 = oEditors3.getById["ir3"].getIR();
	form1.up_etc_agreement3.value=sHTML3;
	
	form1.type.value="up";
	form1.submit();
}

function BasicTerms(obj){
	var str = '';
	if(obj == 3){
		str += '<dl style="margin: 0px 0px 25px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-stretch: inherit; font-size: 13px; line-height: 22px; font-family: OpenSans, &quot;Malgun Gothic&quot;, &quot;맑은 고딕&quot;, sans-serif; vertical-align: baseline; color: rgb(85, 85, 85);"><dt style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: 700; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px; color: rgb(0, 0, 0);">제1조(목적)</dt><dd style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px;">이 약관은 신원 회사(전자상거래 사업자)가 운영하는 신원 사이버 몰(이하 “몰”이라 한다)에서 제공하는 인터넷 관련 서비스(이하 “서비스”라 한다)를 이용함에 있어 사이버 몰과 이용자의 권리 · 의무 및 책임사항을 규정함을 목적으로 합니다. ※「PC통신, 무선 등을 이용하는 전자상거래에 대해서도 그 성질에 반하지 않는 한 이 약관을 준용합니다.」</dd></dl><dl style="margin: 0px 0px 25px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-stretch: inherit; font-size: 13px; line-height: 22px; font-family: OpenSans, &quot;Malgun Gothic&quot;, &quot;맑은 고딕&quot;, sans-serif; vertical-align: baseline; color: rgb(85, 85, 85);"><dt style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: 700; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px; color: rgb(0, 0, 0);">제2조(정의)</dt><dd style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px;">① “몰”이란 신원 회사가 재화 또는 용역(이하 “재화 등”이라 함)을 이용자에게 제공하기 위하여 컴퓨터 등 정보통신설비를 이용하여 재화 등을 거래할 수 있도록 설정한 가상의 영업장을 말하며, 아울러 사이버몰을 운영하는 사업자의 의미로도 사용합니다.</dd><dd style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px;">② “이용자”란 “몰”에 접속하여 이 약관에 따라 “몰”이 제공하는 서비스를 받는 회원 및 비회원을 말합니다.</dd><dd style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px;">③ ‘회원’이라 함은 “몰”에 회원등록을 한 자로서, 계속적으로 “몰”이 제공하는 서비스를 이용할 수 있는 자를 말합니다.</dd><dd style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px;">④ ‘비회원’이라 함은 회원에 가입하지 않고 “몰”이 제공하는 서비스를 이용하는 자를 말합니다.</dd></dl><dl style="margin: 0px 0px 25px; padding: 0px; border: 0px; font-variant-numeric: inherit; font-stretch: inherit; font-size: 13px; line-height: 22px; font-family: OpenSans, &quot;Malgun Gothic&quot;, &quot;맑은 고딕&quot;, sans-serif; vertical-align: baseline; color: rgb(85, 85, 85);"><dt style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: 700; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px; color: rgb(0, 0, 0);">제3조 (약관 등의 명시와 설명 및 개정)</dt><dd style="margin: 0px; padding: 0px; border: 0px; font-style: inherit; font-variant: inherit; font-weight: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; vertical-align: baseline; letter-spacing: 0px;">① “몰”은 이 약관의 내용과 상호 및 대표자 성명, 영업소 소재지 주소(소비자의 불만을 처리할 수 있는 곳의 주소를 포함), 전화번호 · 모사전송번호 · 전자우편주소, 사업자등록번호, 통신판매업 신고번호, 개인정보관리책임자 등을 이용자가 쉽게 알 수 있도록 신원 사이버몰의 초기 서비스화면(전면)에 게시합니다. 다만, 약관의 내용은 이용자가 연결화면을 통하여 볼 수 있도록 할 수 있습니다.</dd></dl>';
		oEditors3.getById["ir3"].setIR(str);
		str = '';
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 기본정보 설정 &gt;<span>쇼핑몰 기타약관</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">쇼핑몰 기타약관</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 기타약관 <span>쇼핑몰 기타약관을 설정합니다.</span></div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
					<div class="tab_style1" data-ui="TabMenu">
						<div class="tab-menu clear">
							<a data-content="menu" class="active" title="선택됨">개인정보 제3자 제공</a>
							<a data-content="menu">마케팅 정보 수신</a>
							<a data-content="menu">멤버쉽 약관</a>
						</div>
						<!-- 개인정보 제3자 제공 -->
						<div class="tab-content active" data-content="content">
							<div>
								<textarea name=up_etc_agreement1 id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$etc_agreement1?></textarea>
							</div>
						</div>

						<!-- 마케팅 정보 수신 -->
						<div class="tab-content" data-content="content">
							<div>
								<textarea name=up_etc_agreement2 id=ir2 rows=15 wrap=off style="width:100%" class="textarea"><?=$etc_agreement2?></textarea>
							</div>
						</div>

						<!-- 멤버쉽 약관 -->
						<div class="tab-content" data-content="content">
							<div>
								<textarea name=up_etc_agreement3 id=ir3 rows=15 wrap=off style="width:100%" class="textarea"><?=$etc_agreement3?></textarea>
							</div>
						</div>
					</div>
				</td>
			</tr>

			<!-- <tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height="10"></td>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" height="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">개인정보 제3자 제공</TD>
				</TR>
				<TR>
					<TD width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><textarea name=up_etc_agreement1 id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$etc_agreement1?></textarea></td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height="10"></td>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" height="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">마케팅 정보 수신</TD>
				</TR>
				<TR>
					<TD width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><textarea name=up_etc_agreement2 id=ir2 rows=15 wrap=off style="width:100%" class="textarea"><?=$etc_agreement2?></textarea></td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td height="10"></td>
				</tr>
				<TR>
					<TD background="images/table_top_line.gif" height="3"></TD>
				</TR>
				<TR>
					<TD class="table_cell" align="center">개인정보 취급 위탁관련</TD>
				</TR>
				<TR>
					<TD width="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><textarea name=up_etc_agreement3 id=ir3 rows=15 wrap=off style="width:100%" class="textarea"><?=$etc_agreement3?></textarea></td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr> -->
			<!-- <tr>
				<td height=3></td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">
			                    
			                    도움말
			                    <div class="help_info01_wrap">
			                        <ul>
			                            <li>1) <B>[COMPANY]</B>, <B>[SHOP]</B>은 회사명과 상점명이 자동 입력됩니다.</li>
			                            <li>2) 공정거래위원회 표준약관 준수를 권합니다.</li>
			                        </ul>
			                    </div>
			                    
			            	</td>
			</tr> -->
			<tr><td height=10></td></tr>
			<tr>
				<td align="center">
					<a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a>
					<a href="javascript:BasicTerms(3);"><span class="btn-basic">샘플약관 적용</span></a>
				</td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>[COMPANY]는 회사명, [SHOP]은 쇼핑몰명이 자동 입력됩니다.</li>
							<li><b>쇼핑몰에 적용하시기 전, 쇼핑몰 운영사항을 확인하시고 내용 수정 후 반영하여 사용하시기 바랍니다.</b></li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
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
<SCRIPT LANGUAGE="JavaScript">
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
<SCRIPT LANGUAGE="JavaScript">
	var oEditors2 = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors2,
		elPlaceHolder: "ir2",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
<SCRIPT LANGUAGE="JavaScript">
	var oEditors3 = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors3,
		elPlaceHolder: "ir3",
		sSkinURI: "../SE2/SmartEditor2Skin.html",	
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		}, 
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>

<?php 
include("copyright.php");
