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

$ssl_type=$_shopdata->ssl_type;
$ssl_domain=$_shopdata->ssl_domain;
$ssl_port=$_shopdata->ssl_port;
$ssl_page=$_shopdata->ssl_page;

$type=$_POST["type"];
if($type=="up") {
	$ssl_type=$_POST["ssl_type"];
	$ssl_port=$_POST["ssl_port"];
	$ssl_domain=$_POST["ssl_domain"];

	$ssl_page_admin=$_POST["ssl_page_admin"];
	$ssl_page_plogn=$_POST["ssl_page_plogn"];
	$ssl_page_vlogn=$_POST["ssl_page_vlogn"];
	$ssl_page_login=$_POST["ssl_page_login"];
	$ssl_page_mjoin=$_POST["ssl_page_mjoin"];
	$ssl_page_medit=$_POST["ssl_page_medit"];
	$ssl_page_mlost=$_POST["ssl_page_mlost"];
	$ssl_page_order=$_POST["ssl_page_order"];
	$ssl_page_adult=$_POST["ssl_page_adult"];

	if($ssl_type=="Y") {
		$ssl_page="";
		if($ssl_page_admin=="Y") $ssl_page.="ADMIN=Y|";
		if($ssl_page_plogn=="Y") $ssl_page.="PLOGN=Y|";
		if($ssl_page_vlogn=="Y") $ssl_page.="VLOGN=Y|";
		if($ssl_page_login=="Y") $ssl_page.="LOGIN=Y|";
		if($ssl_page_mjoin=="Y") $ssl_page.="MJOIN=Y|";
		if($ssl_page_medit=="Y") $ssl_page.="MEDIT=Y|";
		if($ssl_page_mlost=="Y") $ssl_page.="MLOST=Y|";
		if($ssl_page_order=="Y") $ssl_page.="ORDER=Y|";
		if($ssl_page_adult=="Y") $ssl_page.="ADULT=Y|";

		if(ord($ssl_page)) $ssl_page=rtrim($ssl_page,'|');
	} else {
		$ssl_port="";
		$ssl_domain="";
		$ssl_page="";
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "ssl_type	= '{$ssl_type}', ";
	$sql.= "ssl_domain	= '{$ssl_domain}', ";
	$sql.= "ssl_port	= '{$ssl_port}', ";
	$sql.= "ssl_page	= '{$ssl_page}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('SSL(보안서버) 설정이 완료되었습니다.'); }</script>\n";
}


$temp=explode("|",$ssl_page);
$cnt=count($temp);
for ($i=0;$i<$cnt;$i++) {
	if (strpos($temp[$i],"ADMIN=")===0)		$ssl_check_admin=substr($temp[$i],6);
	elseif (strpos($temp[$i],"PLOGN=")===0)	$ssl_check_plogn=substr($temp[$i],6);
	elseif (strpos($temp[$i],"VLOGN=")===0)	$ssl_check_vlogn=substr($temp[$i],6);
	elseif (strpos($temp[$i],"LOGIN=")===0)	$ssl_check_login=substr($temp[$i],6);
	elseif (strpos($temp[$i],"MJOIN=")===0)	$ssl_check_mjoin=substr($temp[$i],6);
	elseif (strpos($temp[$i],"MEDIT=")===0)	$ssl_check_medit=substr($temp[$i],6);
	elseif (strpos($temp[$i],"MLOST=")===0)	$ssl_check_mlost=substr($temp[$i],6);
	elseif (strpos($temp[$i],"ORDER=")===0)	$ssl_check_order=substr($temp[$i],6);
	elseif (strpos($temp[$i],"ADULT=")===0)	$ssl_check_adult=substr($temp[$i],6);
}

?>
<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(){
	if(document.form1.ssl_type[0].checked) {
		if(document.form1.ssl_port.value.length==0) {
			alert("SSL 포트를 입력하세요.");
			document.form1.ssl_port.focus();
			return;
		} else {
			if(!IsNumeric(document.form1.ssl_port.value)) {
				alert("SSL 포트는 숫자만 입력하세요.");
				document.form1.ssl_port.focus();
				return;
			}
		}
		if(document.form1.ssl_domain.value.length==0) {
			alert("보안서버 도메인을 정확히 입력하세요.");
			document.form1.ssl_domain.focus();
			return;
		}
	}
	if(confirm("SSL(보안서버) 설정을 변경하시겠습니까?")) {
		document.form1.type.value="up";
		document.form1.submit();
	}
}

function CheckType(type) {
	if(type=="Y") {
		document.form1.ssl_port.style.background="";
		document.form1.ssl_domain.style.background="";
		document.form1.ssl_port.disabled=false;
		document.form1.ssl_domain.disabled=false;
		for(i=0;i<document.form1.elements.length;i++) {
			temp=document.form1.elements[i];
			if(temp.name.substring(0,9)=="ssl_page_") {
				temp.disabled=false;
			}
		}
	} else {
		document.form1.ssl_port.style.background="#f0f0f0";
		document.form1.ssl_domain.style.background="#f0f0f0";
		document.form1.ssl_port.disabled=true;
		document.form1.ssl_domain.disabled=true;
		for(i=0;i<document.form1.elements.length;i++) {
			temp=document.form1.elements[i];
			if(temp.name.substring(0,9)=="ssl_page_") {
				temp.disabled=true;
			}
		}
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>SSL(보안서버) 기능 설정</span></p></div></div>
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
					<div class="title_depth3">SSL(보안서버)기능 설정</div>
					<br />
					<div class="help_info01_wrap">
						<ul>
							<li>1) SSL(보안서버) 기능 설정시 중요 데이터 암호화를 통해 안전하게 전송시킬 수 있습니다.</li>
							<li>2) 보안접속으로 처리할 경우 암호화/복호화 처리로 인해 일반접속보다 속도는 떨어지지만 보안상 안전함으로 보안접속을 권장합니다.</li>
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
					<th><span>SSL 적용 여부</span></th>
					<TD class="td_con1" >
					<input type=radio name=ssl_type value="Y" onclick="CheckType('Y')" <?=($ssl_type=="Y"?"checked":"")?>>적용함(SSL 포트 : <input type=text name=ssl_port value="<?=$ssl_port?>" size=5 maxlength=5 class="input_selected" onkeyup="strnumkeyup(this)">)
					<img width=20 height=0>
					<input type=radio name=ssl_type value="N" onclick="CheckType('N')" <?=($ssl_type!="Y"?"checked":"")?>>적용안함
					<br>
					<span class=font_orange>※ SSL 보안은 서버에서 도메인에 SSL 보안 셋팅을 해야만 적용이 가능합니다.</span>
					<br>
					<span class=font_orange>※ SSL 서버가 정상 작동 되지 않으면 "적용함"으로 설정하더라도 작동이 되지 않습니다.</span>
					</TD>
				</TR>
				<TR>
					<th><span>SSL 보안 도메인</span></th>
					<TD class="td_con1">
					https://<input type=text name=ssl_domain value="<?=$ssl_domain?>" size=25 class="input_selected">/<?=RootPath.SecureDir?>처리페이지
					<br>
					<span class=font_orange>※ SSL 보안 도메인은 보안업체에서 SSL 인증키 발급시 입력한 도메인을 입력해 주세요.</span>
					<br>
					<span class=font_orange>※ SSL 인증키 발급시 입력한 도메인에 www. 입력 여부를 정확히 확인 한 후 입력해 주세요.</span>
					</TD>
				</TR>
				<TR>
					<th><span>SSL 적용 페이지</span></th>
					<TD class="td_con1">
					<input type=checkbox name=ssl_page_admin value="Y" <?=($ssl_check_admin=="Y"?"checked":"")?>> 관리자 로그인 페이지에 보안접속(SSL) 적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_plogn value="Y" <?=($ssl_check_plogn=="Y"?"checked":"")?>> 파트너사 실적관리 로그인 페이지에 SSL 적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_vlogn value="Y" <?=($ssl_check_vlogn=="Y"?"checked":"")?>> 입점사 미니샵관리 로그인 페이지에 SSL 적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_adult value="Y" <?=($ssl_check_adult=="Y"?"checked":"")?>> 성인인증 페이지에 SSL 적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_login value="Y" <?=($ssl_check_login=="Y"?"checked":"")?>> 회원 로그인 페이지에 SSL 적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_mjoin value="Y" <?=($ssl_check_mjoin=="Y"?"checked":"")?>> 회원가입 페이지에 SSL적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_medit value="Y" <?=($ssl_check_medit=="Y"?"checked":"")?>> 회원정보수정 페이지에 SSL적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_mlost value="Y" <?=($ssl_check_mlost=="Y"?"checked":"")?>> ID/비밀번호 찾기 페이지에 SSL적용하여 전송
					<br>
					<input type=checkbox name=ssl_page_order value="Y" <?=($ssl_check_order=="Y"?"checked":"")?>> 주문서 작성 페이지에 SSL적용하여 전송
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
							<dt><span>SSL(보안서버) 기능 설정 방법</span></dt>
							<dd>
								- 보안업체에서 SSL 키 발급 (현재 운영중인 쇼핑몰 도메인으로 발급)<br>
								- 호스팅 서버 관리자에게 쇼핑몰 도메인에 SSL 보안 셋팅 요청<br>
								- 서버 셋팅 완료 후 셋팅 정보 환경설정에 적용<br>

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
<script>CheckType('<?=$ssl_type?>');</script>
<?=$onload?>
<?php 
include("copyright.php");
