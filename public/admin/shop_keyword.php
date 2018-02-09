<?php // hspark
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

$type=$_POST["type"];
$up_shoptitle=$_POST["up_shoptitle"];
$up_shopkeyword=$_POST["up_shopkeyword"];
$up_shopdescription=$_POST["up_shopdescription"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "shoptitle		= '{$up_shoptitle}', ";
	$sql.= "shopkeyword		= '{$up_shopkeyword}', ";
	$sql.= "shopdescription	= '{$up_shopdescription}' ";
	$result = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload = "<script>window.onload=function(){ alert('정보 수정이 완료되었습니다.');} </script>";
}

$sql = "SELECT shoptitle, shopkeyword, shopdescription ";
$sql.= "FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$shoptitle = $row->shoptitle;
	$shopkeyword = $row->shopkeyword;
	$shopdescription = $row->shopdescription;
}
pmysql_free_result($result);
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(){
	var form = document.form1;
	if(CheckLength(form.up_shoptitle)>100){
		alert("타이틀명은 한글50자, 영문100자 까지 입력 가능합니다.\n\n다시 확인하시기 바랍니다.");
		form.up_shoptitle.focus();
		return;
	}
	if(CheckLength(form.up_shopkeyword)>200){
		alert("키워드는 한글100자, 영문200자 까지 입력 가능합니다.\n\n다시 확인하시기 바랍니다.");
		form.up_shopkeyword.focus();
		return;
	}
	if(CheckLength(form.up_shopdescription)>100){
		alert("설명은 한글50자, 영문100자 까지 입력 가능합니다.\n\n다시 확인하시기 바랍니다.");
		form.up_shopdescription.focus();
		return;
	}
	form.type.value="up";
	form.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 기본정보 설정 &gt;<span>브라우저 타이틀/키워드</span></p></div></div>
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
			<table width="100%" cellpadding="0" cellspacing="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<div class="title_depth3">브라우저 타이틀/키워드</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub">타이틀바 설정 <span>쇼핑몰 상단에 표시되는 타이틀바 설정입니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<div class="table_style01">
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<th>
						<span>타이틀명 입력</span><br>
						<a href="javascript:;" class="btn-function ml-15 mt-5">노출위치보기</a>
					</th>
					<td class="td_con1"  ><input name="up_shoptitle" value="<?=$shoptitle?>" size="80" maxlength="100" onKeyDown="chkFieldMaxLen(100)" class="input_selected"><br><span ><!-- * 웹브라우져 윈도우 좌측 상단 타이틀바에 표시할 문장을 입력합니다.<br>* 위 내용은 즐겨찾기 등록시의 타이틀이 됩니다.<br> -->* 최대 <b><span >100자(한글50자)</span></b> 가능(공백포함) </span>.&nbsp;<br><img src="images/shop_keyword_img1.gif" border="0"></td>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub">검색어 설정 <span>검색용 메타테그를 설정합니다. / 검색엔진 사이트의 운영정책(유료정책)에 따라 쇼핑몰의 키워드나 설명이 등록되지 않을 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<table width="100%" cellpadding="0" cellspacing="0" border=0>
				<tr>
					<th><span>키워드 입력</span></th>
					<td class="td_con1"  ><input type=text name="up_shopkeyword" value="<?=$shopkeyword?>" size="80" maxlength="100" onKeyDown="chkFieldMaxLen(100)" class="input"><br><span class="font_orange">* 각종 검색엔진 사이트에서 참조하는 Keyword 메타태그에 들어갈 검색어를 입력하세요.<br>* 쇼핑몰에 가장 적절한 검색어를 콤마(,)를 구분자로 입력하세요.</span></td>
				</tr>
				<tr>
					<th><span>설명 입력</span></th>
					<td class="td_con1"  ><input type=text name="up_shopdescription" value="<?=$shopdescription?>" size="80" maxlength="100" onKeyDown="chkFieldMaxLen(100)" class="input"><br><span class="font_orange">* Description 메타태그에 들어갈 설명을 입력하세요.<br>* 각종 검색엔진 사이트에서 페이지 설명을 위해 사용됩니다. 쇼핑몰 설명을 간략히 입력하세요.</span></td>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>

					<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>타이틀바 설정은 웹 브라우저 윈도우 좌측 상단 타이틀바에 표시할 문장을 입력해주세요. 즐겨찾기 등록 시 타이틀이 됩니다.</li>
							<li>[<b>키워드</b>]는 각종 검색엔진 사이트에서 참조하는 Keyword 메타태그에 들어갈 검색어를 입력하세요.</li>
							<li>[<b>설명</b>]은 각종 검색엔진 사이트에서 쇼핑몰 설명에 노출할 내용을 입력해주세요.</li>
							<li>검색엔진 사이트의 운영정책(유료정책)에 따라 쇼핑몰의 키워드나 설명이 등록되지 않을 수 있습니다.</li>
							<li>
								<dl>
									<dt>타이틀과 메타태그 출력 예</dt>
									<dd>&lt;HEAD&gt;<br>&lt;TITLE&gt;웹브라우져 타이틀바&lt;/TITLE&gt;<br>&lt;meta http-equiv=&quot;CONTENT-TYPE&quot; content=&quot;text/html; charset=utf-8&quot;&gt;<br>&lt;meta name=&quot;description&quot; content=“meta Description(쇼핑몰 설명문구) 출력되는 곳&quot;&gt;<br>&lt;meta name=&quot;keywords&quot; content=“meta Keyword(검색 키워드) 출력되는 곳&quot;&gt;<br>&lt;/HEAD&gt;</dd>
								</dl>
							</li>
							<li>
								<dl>
									<dt><span>웹페이지 소스 보는 방법</span></dt>
									<dd><b>① 브라우저 타이틀 메뉴 &gt; 보기 &gt; 소스<br>② 웹페이지 본문 &gt; 공백부분에 오른쪽 마우스 &gt;소스보기(상단메뉴 고정타입의 프레임을 사용할 경우)</b></dd>
								</dl>
							</li>
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
						</ul>
					</div>

				</td>
			</tr>

			<tr><td height="50"></td></tr>
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
<?= $onload ?>
<?php
include("copyright.php");
