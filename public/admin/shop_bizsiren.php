<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

########################### TEST 쇼핑몰 확인 ##########################
DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", "history.go(-1)");
#######################################################################

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$adultused=$_POST["adultused"];
$adultauthid=$_POST["adultauthid"];
$adultauthpw=$_POST["adultauthpw"];
if($type=="up") {
	if(!strstr("YN",$adultused)) {
		$adultused="N";
	}
	$adultauth=$adultused."={$adultauthid}=".$adultauthpw;
	$_shopdata->adultauth=$adultauth;

	$sql = "UPDATE tblshopinfo SET adultauth='{$adultauth}' ";
	pmysql_query($sql,get_db_conn());

	DeleteCache("tblshopinfo.cache");
	$onload = "<script>window.onload=function(){ alert('정보 수정이 완료되었습니다.'); }</script>";
}

$adultused='';
$adultauthid='';
$adultauthpw='';
if(ord($_shopdata->adultauth)) {
	$tempadult=explode("=",$_shopdata->adultauth);
	$adultused=$tempadult[0];
	$adultauthid=$tempadult[1];
	$adultauthpw=$tempadult[2];
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm() {
	if(confirm("실명인증 정보를 설정하시겠습니까?")) {
		document.form1.type.value="up";
		document.form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>실명인증 정보 설정</span></p></div></div>

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
					<div class="title_depth3">실명인증 정보 설정</div>
					<br />
					<div class="help_info01_wrap">
						<ul>
							<li>1) 실명인증은 <font class=font_orange>유료실명인증 서비스</font>에 가입하셔야 사용 가능합니다.</li>
							<li>2) 실명인증 서비스 가입은 <font class=font_orange><A HREF="http://www.siren24.com" target="_blank">서울신용평가정보(siren24.com)</a></font>에서 가입 가능합니다.</li>
							<li>3) 자세한 등록 및 설정 방법은 아래 메뉴얼을 꼭 참조하세요.</li>
						</ul>
					</div>
				</td>
			</tr>
			
			<tr>
				<td height=3></td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>실명인증 사용여부</span></th>
					<TD class="td_con1" >
					<input type=radio name=adultused value="Y" <?=($adultused=="Y"?"checked":"")?>>사용함
					<img width=20 height=0>
					<input type=radio name=adultused value="N" <?=($adultused!="Y"?"checked":"")?>>사용안함
					<br>
					<span class=font_orange>※ 실명인증 서비스를 사용하거나 사용하지 않도록 설정합니다.<br><img width=17 height=0>단, 사용할 경우 실명인증 서비스에 가입되어 있어야 합니다.</span>
					</TD>
				</TR>
				<TR>
					<th><span>실명인증 상점ID</span></th>
					<TD class="td_con1">
					<input type=text name=adultauthid value="<?=$adultauthid?>" size=10 class="input_selected">
					<span class=font_orange>※ 서울신용평가정보(주)에서 발급 받은 ID를 등록하세요.</span>
					</TD>
				</TR>
				<TR>
					<th><span>상점 비밀번호</span></th>
					<TD class="td_con1">
					<input type=text name=adultauthpw value="<?=$adultauthpw?>" size=10 class="input_selected">
					<span class=font_orange>※ 서울신용평가정보(주)에서 발급 받은 비밀번호를 등록하세요.</span>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>실명인증 서비스 가입 및 설정 방법</span></dt>
							<dd>
								- 실명인증 서비스 제공사(<B>siren24.com</B>)의 관리자 화면에 아래와 같은 정보를 등록해야합니다.<br>
								- 실명확인 도메인 등록 신청에서 아래의 도메인(주소) 추가<br>
								<span class=font_orange>&nbsp;&nbsp;&nbsp;<b>http://<?=$_ShopInfo->getShopurl().FrontDir?>getnamecheck.php</b></span>

								<br><br>
								<span class=font_orange>※실명인증은 유료 서비스 입니다. 자세한 내용은 <A HREF="" target="_blank"><B>[실명인증 안내]</B></A> 페이지를 참고하세요.</span><br>
							</dd>
						</dl>
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
<?=$onload?>
<?php 
include("copyright.php");
