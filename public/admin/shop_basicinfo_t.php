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
$up_companyname=$_POST["up_companyname"];
$up_companynum=$_POST["up_companynum"];
$up_companyowner=$_POST["up_companyowner"];
$up_companypost1=$_POST["up_companypost1"];
$up_companypost2=$_POST["up_companypost2"];
$up_companyaddr=$_POST["up_companyaddr"];
$up_companybiz=$_POST["up_companybiz"];
$up_companyitem=$_POST["up_companyitem"];
$up_reportnum=$_POST["up_reportnum"];

$up_shopname=$_POST["up_shopname"];
$up_info_email=$_POST["up_info_email"];
$up_info_tel=$_POST["up_info_tel"];
$up_info_addr=$_POST["up_info_addr"];
$up_privercyname=$_POST["up_privercyname"];
$up_privercyemail=$_POST["up_privercyemail"];

$up_companypost = $up_companypost1.$up_companypost2;

if ($type == "up") {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "shopname		= '{$up_shopname}', ";
	$sql.= "companyname		= '{$up_companyname}', ";
	$sql.= "companynum		= '{$up_companynum}', ";
	$sql.= "companypost		= '{$up_companypost}', ";
	$sql.= "companyaddr		= '{$up_companyaddr}', ";
	$sql.= "companybiz		= '{$up_companybiz}', ";
	$sql.= "companyitem		= '{$up_companyitem}', ";
	$sql.= "companyowner	= '{$up_companyowner}', ";
	$sql.= "reportnum		= '{$up_reportnum}', ";
	$sql.= "privercyname	= '{$up_privercyname}', ";
	$sql.= "privercyemail	= '{$up_privercyemail}', ";
	$sql.= "info_email		= '{$up_info_email}', ";
	$sql.= "info_tel		= '{$up_info_tel}', ";
	$sql.= "info_addr		= '{$up_info_addr}' ";
	$result = pmysql_query($sql,get_db_conn());

	DeleteCache("tblshopinfo.cache");
	$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
}

$sql = "SELECT * FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if ($row=pmysql_fetch_object($result)) {
	$shopname = $row->shopname;
	$companyname = $row->companyname;
	$companynum = $row->companynum;
	$companyowner = $row->companyowner;
	$companypost = $row->companypost;
	$companyaddr = $row->companyaddr;
	$companybiz = $row->companybiz;
	$companyitem = $row->companyitem;
	$reportnum = $row->reportnum;
	$info_email = $row->info_email;
	$info_tel  = $row->info_tel;
	$info_addr = $row->info_addr;
	$privercyname = $row->privercyname;
	$privercyemail = $row->privercyemail;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function f_addr_search(form,post,addr,gbn) {
	window.open("<?=$Dir.FrontDir?>addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");		
}

function CheckForm() {
	var form = document.form1;
	if (!form.up_companyname.value) {
		form.up_companyname.focus();
		alert("상호(회사명)을 입력하세요.");
		return;
	}
	if(CheckLength(form.up_companyname)>30) {
		form.company_name.focus();
		alert("상호(회사명)은 한글15자 영문30자 까지 입력 가능합니다");
		return;
	}
	if (!form.up_companynum.value) {
		form.up_companynum.focus();
		alert("사업자등록번호를 입력하세요.");
		return;
	}

	var bizno;
	var bb;
	bizno = form.up_companynum.value;
	bizno = bizno.replace("-","");
	bb = chkBizNo(bizno);
	if (!bb) {
		alert("인증되지 않은 사업자등록번호 입니다.\n사업자등록번호를 다시 입력하세요.");
		form.up_companynum.value = "";
		form.up_companynum.focus();
		return;
	}

	if (!form.up_companyowner.value) {
		form.up_companyowner.focus();
		alert("대표자 성명을 입력하세요.");
		return;
	}
	if(CheckLength(form.up_companyowner)>12) {
		form.up_companyowner.focus();
		alert("대표자 성명은 한글 6글자까지 가능합니다");
		return;
	}
	if (!form.up_companypost1.value || !form.up_companypost2.value) {
		form.up_companypost1.focus();
		alert("우편번호를 입력하세요.");
		return;
	}
	if (!form.up_companyaddr.value) {
		form.up_companyaddr.focus();
		alert("사업장 주소를 입력하세요.");
		return;
	}
	if(CheckLength(form.up_companybiz)>30) {
		form.up_companybiz.focus();
		alert("사업자 업태는 한글 15자까지 입력 가능합니다");
		return;
	}
	if(CheckLength(form.up_companyitem)>30) {
		form.up_companyitem.focus();
		alert("사업자 종목은 한글 15자까지 입력 가능합니다");
		return;
	}

	form.type.value="up";
	form.submit();
}
</script>
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link pl_15" align="left"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 상점관리 &gt; 상점 기본정보 설정 &gt; <span class="2depth_select">상점 기본정보 관리</span></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240></col>
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
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_basicinfo.gif" border="0"></td>
					<td width="100%" background="images/title_bg.gif"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_basicinfo_stitle1.gif" border="0"></td>
					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
					<td><img src="images/shop_basicinfo_stitle_end.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/distribute_01.gif"></td>
					<td COLSPAN="2" background="images/distribute_02.gif"></td>
					<td><img src="images/distribute_03.gif"></td>
				</tr>
				<tr>
					<td background="images/distribute_04.gif"></td>
					<td class="notice_blue"><img src="images/distribute_img.gif" border="0"></td>
					<td width="100%" class="notice_blue">쇼핑몰 <b>회사소개/하단/이용안내/정보보호</b> 등에서 출력됨으로 정확히 입력해야 합니다.</span></b></td>
					<td background="images/distribute_07.gif"></td>
				</tr>
				<tr>
					<td><img src="images/distribute_08.gif"></td>
					<td COLSPAN="2" background="images/distribute_09.gif"></td>
					<td><img src="images/distribute_10.gif"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<col width="140"></col>
				<col></col>
				<tr>
					<td height="1" colspan="2" bgcolor="#B9B9B9"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">상호 (회사명)</td>
					<td class="td_con1"><input type="text" name="up_companyname" value="<?=$companyname?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">사업자등록번호</td>
					<td class="td_con1"><input type="text" name="up_companynum" value="<?=$companynum?>" size="20" maxlength="20" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">대표자 성명</td>
					<td class="td_con1"><input type="text" name="up_companyowner" value="<?=$companyowner?>" size="20" maxlength="12" onKeyDown="chkFieldMaxLen(12)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">사업장 주소</td>
					<td colspan="3" bgcolor="#FFFFFF" class="td_con1">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td width="80" nowrap><input type=text name="up_companypost1" value="<?=substr($companypost,0,3)?>" size="3" maxlength="3" class="input"> - <input type=text name="up_companypost2" value="<?=substr($companypost,3,3)?>" size="3" maxlength="3" class="input"></td>
						<td width="100%"><A href="javascript:f_addr_search('form1','up_companypost','up_companyaddr',2);" onfocus="this.blur();" style="selector-dummy: true" class="board_list hideFocus"><img src="images/icon_addr.gif" border="0"></A></td>
					</tr>
					<tr>
						<td colspan="2"><input type=text name="up_companyaddr" value="<?=$companyaddr?>" size="60" maxlength="150" onKeyDown="chkFieldMaxLen(150)" class="input"></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">사업자 업태</td>
					<td class="td_con1"><input type="text" name="up_companybiz" value="<?=$companybiz?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">사업자 종목</td>
					<td class="td_con1"><input type=text name="up_companyitem" value="<?=$companyitem?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">통신판매신고번호</td>
					<td class="td_con1"><input type=text name="up_reportnum" value="<?=$reportnum?>" size="20" maxlength="20" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#B9B9B9"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/shop_basicinfo_stitle2.gif" border="0"></td>
					<td width="100%" background="images/shop_basicinfo_stitle_bg.gif"></td>
					<td><img src="images/shop_basicinfo_stitle_end.gif" border="0"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/distribute_01.gif"></td>
					<td COLSPAN="2" background="images/distribute_02.gif"></td>
					<td><img src="images/distribute_03.gif"></td>
				</tr>
				<tr>
					<td background="images/distribute_04.gif"></td>
					<td class="notice_blue"><img src="images/distribute_img.gif" ></td>
					<td width="100%" class="notice_blue">쇼핑몰 정보를 바탕으로 각종 내용이 표기됩니다. <b>정확히 입력해 주세요!</b></td>
					<td background="images/distribute_07.gif"></td>
				</tr>
				<tr>
					<td><img src="images/distribute_08.gif"></td>
					<td COLSPAN="2" background="images/distribute_09.gif"></td>
					<td><img src="images/distribute_10.gif"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<col width="140"></col>
				<col></col>
				<tr>
					<td height="1" colspan="2" bgcolor="#B9B9B9"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">상점명</td>
					<td class="td_con1"><input type=text name="up_shopname" value="<?=$shopname?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">쇼핑몰 운영자 이메일</td>
					<td class="td_con1"><input type=text name="up_info_email" value="<?=$info_email?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">고객상담 전화번호</td>
					<td class="td_con1"><input type=text name="up_info_tel" value="<?=$info_tel?>" size="60" maxlength="100" onKeyDown="chkFieldMaxLen(100)" class="input"> <span class="font_blue">* 여러개 입력시 콤마(,)를 입력하세요.</span></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">주소 및 안내</td>
					<td class="td_con1"><input type=text name="up_info_addr" value="<?=$info_addr?>" size="60" maxlength="150" onKeyDown="chkFieldMaxLen(150)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">개인정보 담당자 이름</td>
					<td class="td_con1"><input type="text" name="up_privercyname" value="<?=$privercyname?>" size="20" maxlength="10" onKeyDown="chkFieldMaxLen(10)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#EDEDED"></td>
				</tr>
				<tr>
					<td class="table_cell"><img src="images/icon_point2.gif" border="0">개인정보 담당자 이메일</td>
					<td class="td_con1"><input type="text" name="up_privercyemail" value="<?=$privercyemail?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
				</tr>
				<tr>
					<td height="1" colspan="2" bgcolor="#B9B9B9"></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			</form>
			<tr>
				<td>
				<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td><img src="images/manual_top1.gif" border="0"></td>
					<td width="100%" background="images/manual_bg.gif"><img src="images/manual_title.gif" border="0"></td>
					<td><img src="images/manual_top2.gif" border="0"></td>
				</tr>
				<tr>
					<td background="images/manual_left1.gif"></td>
					<td style="padding-top:5px;">
					<table width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">하단 표기 내용</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="space_top">
						상호명:ABC COMPANY &nbsp;대표:000 &nbsp;사업자등록번호:000-00-00000 &nbsp;통신판매번호:0000호<br>
						사업장소재지:000-000 &nbsp;00시 00구 00동 000-0번지 00빌딩 000호 &nbsp;고객센터:00-000-000, 00-000-000<br>E-MAIL:0000@000.000 &nbsp;[개인정보책임자:000] &nbsp;[약관] &nbsp;[개인정보보호정책]<br>Copiright ⓒ ABC COMPANY All Rights Reserved.
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td><img src="images/icon_8.gif" border="0" align="absmiddle"><span class="font_dotline">하단 디자인 변경</span></td>
					</tr>
					<tr>
						<td style="padding-left:13px;" class="space_top">
						<span class="font_blue"><a href="javascript:parent.topframe.GoMenu(2,'design_bottom.php');">디자인관리 > 템플릿 - 메인 및 카테고리 > 쇼핑몰 하단 템플릿</a></span> 에서 미리 지정된 배치와 타입을 선택할 수 있습니다.<br>
						<span class="font_blue"><a href="javascript:parent.topframe.GoMenu(2,'design_eachbottom.php');">디자인관리 > 개별디자인 - 메인 및 상하단 > 하단화면 꾸미기</a></span> 에서 개별 디자인을 할 수 있습니다.
						</td>
					</tr>
					</table>
					</td>
					<td background="images/manual_right1.gif"></td>
				</tr>
				<tr>
					<td><img src="images/manual_left2.gif" border="0"></td>
					<td background="images/manual_down.gif"></td>
					<td><img src="images/manual_right2.gif" border="0"></td>
				</tr>
				</table>
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
