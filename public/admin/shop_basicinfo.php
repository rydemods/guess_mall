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
$up_info_fax=$_POST["up_info_fax"];
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
	$sql.= "info_fax		= '{$up_info_fax}', ";
	$sql.= "info_addr		= '{$up_info_addr}' ";
	$result = pmysql_query($sql,get_db_conn());

	DeleteCache("tblshopinfo.cache");
	//$onload = "<script> alert('정보 수정이 완료되었습니다.'); </script>";
	$onload="<script>window.onload=function(){alert(\"정보 수정이 완료되었습니다.\");}</script>";

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
	$info_fax  = $row->info_fax;
	$info_addr = $row->info_addr;
	$privercyname = $row->privercyname;
	$privercyemail = $row->privercyemail;
}
pmysql_free_result($result);

?>

<?php include("header.php"); ?>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('up_companypost1').value = data.postcode1;
			document.getElementById('up_companypost2').value = data.postcode2;
			document.getElementById('up_companyaddr').value = data.address;
			document.getElementById('up_companyaddr').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
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
	if(CheckLength(form.up_companyowner)>24) {
		form.up_companyowner.focus();
		alert("대표자 성명은 한글 12글자까지 가능합니다");
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

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 기본정보 설정 &gt;<span>상점 기본정보 관리</span></p></div></div>

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
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상점 기본정보 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">사업자 등록정보 <span>쇼핑몰 회사소개/하단/이용안내/정보보호 등에서 출력됨으로 정확히 입력해야 합니다.</span></div>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				    <div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
                        <th><span>상호 (회사명)</span></th>
						<td class="td_con1"  ><input type="text" name="up_companyname" value="<?=$companyname?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
					</tr>
					<tr>
						<th><span>사업자등록번호</span></th>
						<td class="td_con1"  ><input type="text" name="up_companynum" value="<?=$companynum?>" size="20" maxlength="20" class="input"></td>
					</tr>
					<tr>
						<th><span>대표자 성명</span></th>
						<td class="td_con1"  ><input type="text" name="up_companyowner" value="<?=$companyowner?>" size="40" maxlength="20" onKeyDown="chkFieldMaxLen(20)" class="input"></td>
					</tr>
					<tr>
						<th><span>사업장 주소</span></th>
						<td colspan="3" class="td_con1"  >
                        <div class="table_none">
                        <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="80" nowrap><input type=text name="up_companypost1" id="up_companypost1" value="<?=substr($companypost,0,3)?>" size="3" maxlength="3" class="input"> - <input type=text name="up_companypost2" id="up_companypost2" value="<?=substr($companypost,3,3)?>" size="3" maxlength="3" class="input"></td>
                            <td width="100%"><A href="javascript:openDaumPostcode();" onfocus="this.blur();" style="selector-dummy: true" class="board_list hideFocus"><img src="images/icon_addr.gif" border="0"></A></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type=text name="up_companyaddr" id="up_companyaddr" value="<?=$companyaddr?>" size="60" maxlength="150" onKeyDown="chkFieldMaxLen(150)" class="input"></td>
                        </tr>
                        </table>
                        </div>
						</td>
					</tr>
					<tr>
						<th><span>사업자 업태</span></th>
						<td class="td_con1"  ><input type="text" name="up_companybiz" value="<?=$companybiz?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
					</tr>
					<tr>
						<th><span>사업자 종목</span></th>
						<td class="td_con1"  ><input type=text name="up_companyitem" value="<?=$companyitem?>" size="60" maxlength="30" onKeyDown="chkFieldMaxLen(30)" class="input"></td>
					</tr>
					<tr>
						<th><span>통신판매신고번호</span></th>
						<td class="td_con1"  ><input type=text name="up_reportnum" value="<?=$reportnum?>" size="20" maxlength="20" class="input"></td>
					</tr>
					</table>
                    </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 정보 설정 <span>쇼핑몰 정보를 바탕으로 각종 내용이 표기됩니다. 정확히 입력해 주세요!</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
                        <th><span>상점명</span></th>
						<td class="td_con1"  ><input type=text name="up_shopname" value="<?=$shopname?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
					</tr>
					<tr>
						<th><span>쇼핑몰 운영자 이메일</span></th>
						<td class="td_con1"><input type=text name="up_info_email" value="<?=$info_email?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
					</tr>
					<tr>
						<th><span>고객상담 전화번호</span></th>
						<td class="td_con1"  ><input type=text name="up_info_tel" value="<?=$info_tel?>" size="60" maxlength="100" onKeyDown="chkFieldMaxLen(100)" class="input"> <span class="font_blue">* 여러개 입력시 콤마(,)를 입력하세요.</span></td>
					</tr>
					<tr>
						<th><span>고객상담 팩스번호</span></th>
						<td class="td_con1"  ><input type=text name="up_info_fax" value="<?=$info_fax?>" size="60" maxlength="100" onKeyDown="chkFieldMaxLen(100)" class="input"> <span class="font_blue">* 여러개 입력시 콤마(,)를 입력하세요.</span></td>
					</tr>
					<tr>
						<th><span>주소 및 안내</span></th>
						<td class="td_con1"><input type=text name="up_info_addr" value="<?=$info_addr?>" size="60" maxlength="150" onKeyDown="chkFieldMaxLen(150)" class="input"></td>
					</tr>
					<tr>
						<th><span>개인정보 담당자 이름</span></th>
						<td class="td_con1"><input type="text" name="up_privercyname" value="<?=$privercyname?>" size="20" maxlength="10" onKeyDown="chkFieldMaxLen(10)" class="input"></td>
					</tr>
					<tr>
						<th><span>개인정보 담당자 이메일</span></th>
						<td class="td_con1"><input type="text" name="up_privercyemail" value="<?=$privercyemail?>" size="60" maxlength="50" onKeyDown="chkFieldMaxLen(50)" class="input"></td>
					</tr>
				</table>
                </div>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			</form>
			<!--tr>
				<td>
				
					<div class="title_depth3_sub">사업자 등록정보 <span>쇼핑몰 회사소개/하단/이용안내/정보보호 등에서 출력됨으로 정확히 입력해야 합니다.</span></div>

					<div class="table_style01">
						<table cellpadding=0 cellspacing=0 border=0 width=100%>
							<tr>
								<th><span>상호(회사명)</span></th>
								<td class="td_con1"  ><input type="text" name="" id="" /></td>
							</tr>
							<tr>
								<th><span>상호(회사명)</span></th>
								<td class="td_con1">닝라ㅓㄴ이ㅏ러 <br />sdfsdfdf닝라ㅓㄴ이ㅏ러 <br />sdfsdfdf</td>
							</tr>
							<tr>
								<th><span>개인정보 담당자 이메일</span></th>
								<td class="td_con1">닝라ㅓㄴ이ㅏ러</td>
							</tr>
						</table>
					</div>
					<br />
				
				</td>
			</tr-->
			<tr>
				<td>

					<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<!--<li>사업자 수정 시 [ <b>주문/매출 > 현금영수증 관리 > 현금영수증 환경설정</b> ]에서 최신 사업자정보로 수정해 주시기 바랍니다.</li>-->
							<li>등록/수정하시면 하단에 [적용하기]버튼을 누르셔야 쇼핑몰에 적용됩니다.</li>
							<li>기본정보 입력 시 홈페이지 하단 Footer에 해당 내용이 노출됩니다.</li>
						</ul>
						<div class="ml-20 mb-20"><img src="static/img/common/footer_ex.gif" alt="footer 예시"></div>
						<!-- <dl>
							<dt><span>하단 표기 내용</span></dt>
							<dd>
								상호명:ABC COMPANY &nbsp;대표:000 &nbsp;사업자등록번호:000-00-00000 &nbsp;통신판매번호:0000호<br>
								사업장소재지:000-000 &nbsp;00시 00구 00동 000-0번지 00빌딩 000호 &nbsp;고객센터:00-000-000, 00-000-000<br>E-MAIL:0000@000.000 &nbsp;[개인정보책임자:000] &nbsp;[약관] &nbsp;[개인정보보호정책]<br>Copiright ⓒ ABC COMPANY All Rights Reserved.
							</dd>
						</dl>
						<dl>
							<dt><span>하단 디자인 변경</span></dt>
							<dd>
								<a href="javascript:parent.topframe.GoMenu(2,'design_bottom.php');">디자인관리 > 템플릿 - 메인 및 카테고리 > 쇼핑몰 하단 템플릿</a> 에서 미리 지정된 배치와 타입을 선택할 수 있습니다.<br>
								<a href="javascript:parent.topframe.GoMenu(2,'design_eachbottom.php');">디자인관리 > 개별디자인 - 메인 및 상하단 > 하단화면 꾸미기</a>에서 개별 디자인을 할 수 있습니다.
							</dd>
						</dl> -->
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
