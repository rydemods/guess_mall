<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
extract($_REQUEST);

$mode=$_POST["mode"];
$code=$_POST["code"];
$money=$_POST["money"];
$gbn=$_POST["gbn"];
$reserve=$_POST["reserve"];
$danwi=$_POST["danwi"];
$cut=$_POST["cut"];

if(strlen($code)==12) {
	$sql = "SELECT type FROM tblproductcode WHERE code_a='".substr($code,0,3)."' ";
	$sql.= "AND code_b='".substr($code,3,3)."' ";
	$sql.= "AND code_c='".substr($code,6,3)."' AND code_d='".substr($code,9,3)."' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if(!$row) {
		$code="";
	}
	$type = $row->type;
} else {
	$code="";
}

if ($mode=="update" && ord(trim($percent)) && strlen($code)==12) {
	if(strpos($type,'X')!==FALSE) {
		$likecode=$code;
	} else {
		$likecode=substr($code,0,3);
		if(substr($code,3,3)!="000") {
			$likecode.=substr($code,3,3);
			if(substr($code,6,3)!="000") {
				$likecode.=substr($code,6,3);
				if(substr($code,9,3)!="000") {
					$likecode.=substr($code,9,3);
				}
			}
		}
	}

	$won=array(1=>0.1,10=>0.01,100=>0.001,1000=>0.0001);
	$pricevalue=array("sellprice"=>"판매가격","consumerprice"=>"시중가격","buyprice"=>"구입가격");
	$plus=array("up"=>"+","down"=>"-");

	if ($pricetype=="%") {
		$sql = "UPDATE tblproduct SET ";
		$sql.= $tgprice."={$cut}((".$price.$plus[$updown]."({$price}/100)*$percent)*$won[$danwi])*($danwi*10) ";
	} else {
		$sql = "UPDATE tblproduct SET $tgprice=$price{$plus[$updown]}$percent ";
	}
//	$sql.= "WHERE productcode LIKE '{$likecode}%' ";
	$sql.= "WHERE productcode in (select c_productcode from tblproductlink where c_category like '{$likecode}%') ";
	$sql.= "AND assembleuse != 'Y' ";
	
	$update = pmysql_query($sql,get_db_conn());
	if($update && $tgprice=="sellprice") {
		$sql = "SELECT assembleproduct FROM tblproduct a left join tblproductlink b on(a.productcode=b.c_productcode)";
		$sql.= "WHERE b.c_category LIKE '{$likecode}%' ";
		$sql.= "AND assembleuse != 'Y' ";
		$apresult=pmysql_query($sql,get_db_conn());
		while($aprow=pmysql_fetch_object($apresult)) {
			if(ord($aprow->assembleproduct)) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$aprow->assembleproduct)."') ";
				$result = pmysql_query($sql,get_db_conn());
				while($row = @pmysql_fetch_object($result)) {
					$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND display ='Y' ";
					$sql.= "AND assembleuse!='Y' ";
					$result2 = pmysql_query($sql,get_db_conn());
					if($row2 = @pmysql_fetch_object($result2)) {
						$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
						$sql.= "WHERE productcode = '{$row->productcode}' ";
						$sql.= "AND assembleuse='Y' ";
						pmysql_query($sql,get_db_conn());
					}
					pmysql_free_result($result2);
				}
			}
		}
		pmysql_free_result($apresult);
	}
	if($updown=="up") $updown="할증";
	else $updown="할인";
	$onload="<script>window.onload=function(){alert('{$pricevalue[$tgprice]}을 {$pricevalue[$price]}의 ".$percent.$pricetype.$updown."한 가격으로 변경하였습니다.');}</script>";

	$log_content = "## 상품일괄 $pricetype로수정 ## - 카테고리코드: $likecode {$pricevalue[$price]} $pricetype: $percent 단위:$danwi $updown";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script language="JavaScript">
var code="<?=$code?>";
function CodeProcessFun(_code) {
	if(_code=="out" || _code.length==0 || _code=="000000000000") {
		document.all["code_top"].style.background="#dddddd";
		selcode="";
		seltype="";

		if(_code!="out") {
			BodyInit('');
		} else {
			_code="";
		}
	} else {
		document.all["code_top"].style.background="#ffffff";
		BodyInit(_code);
	}
}


function CheckForm() {
	if(selcode.length!=12 || selcode=="000000000000") {
		alert ("변경하실 카테고리를 선택하세요.");
		return;
	}
	if(document.form1.percent.value.length==0){
		alert('일괄 상품가격의 금액을 입력하세요');
		document.form1.percent.focus();
		return;
	}
	if(document.form1.percent.value.length!=0 && isNaN(document.form1.percent.value)){
		alert('금액은 숫자만 입력하세요.');
		document.form1.percent.focus();
		return;
	}
	if (!confirm("해당 카테고리의 상품가격을 변경하시겠습니까?")) return;
	document.form1.mode.value="update";
	document.form1.code.value=selcode;
	document.form1.submit();
}

function price_type(){
	if(document.form1.pricetype.selectedIndex==0){
		document.form1.cut.disabled=false;
		document.form1.danwi.disabled=false;
	}else{
		document.form1.cut.disabled=true;
		document.form1.danwi.disabled=true;
	}
}

function GoAllUpdate(){
	if(selcode.length!=12 || selcode=="000000000000") {
		alert('먼저 카테고리를 선택하세요');
		return;
	}
	document.form2.code.value=selcode;
	document.form2.code_a.value=selcode.substring(0,3);
	document.form2.code_b.value=selcode.substring(3,6);
	document.form2.code_c.value=selcode.substring(6,9);
	document.form2.code_d.value=selcode.substring(9,12);
	document.form2.submit();
}
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 315;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 상품 일괄관리 &gt;<span>판매상품 가격 일괄수정</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">판매상품 가격 일괄수정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에 등록된 모든 상품의 가격을 일괄 수정할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="242" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="232" height="100%" valign="top" >
						<table cellpadding="0" cellspacing="0" width="242">
						<tr>
							<td bgcolor="white">
								<!-- 소제목 -->
								<div class="title_depth3_sub">전체 카테고리</div>
							</td>
						</tr>

						<tr>
							<td width="100%" height="100%" align=center valign=top style="padding-left:5px;padding-right:5px;" class="bd_editer">
								<DIV class=MsgrScroller id=contentDiv style="width=99%;height:394px;OVERFLOW-x: auto; OVERFLOW-y: auto;" oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false">
									<DIV id=bodyList>
										<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" bgcolor=FFFFFF>
										<tr>
											<td height=18><IMG SRC="images/directory_root.gif" border=0 align=absmiddle> <span id="code_top" style="cursor:default;" onmouseover="this.className='link_over'" onmouseout="this.className='link_out'" onclick="ChangeSelect('out');">최상위 카테고리</span></td>
										</tr>
										<tr>
											<!-- 상품카테고리 목록 -->
											<td id="code_list" nowrap valign=top></td>
											<!-- 상품카테고리 목록 끝 -->
										</tr>
										</table>
									</DIV>
								</DIV>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
					<td width="15"><img src="images/btn_next1.gif" border="0" hspace="5"><br></td>
					<td width="100%" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td>
						<table cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tr>
							<td bgcolor="white">
								<!-- 소제목 -->
								<div class="title_depth3_sub">가격 일괄수정</div>
							</td>
						</tr>
						<tr>
							<td width="100%" height="100%" valign="top" style="BORDER-bottom:#0F8FCB 2px solid;BORDER-top:#0F8FCB 2px solid;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="100%" style="padding-top:4pt; padding-bottom:2pt;"><select name=price class="input">
										<option value='sellprice'>판매가격
										<option value='consumerprice'>시중가격
										<option value='buyprice'>공급가격
									</select>의 <input type=text size=7 name=percent maxlength=7 style="text-align:right" class="input"> <select name=pricetype onchange="price_type()" class="input">
										<option value='%'>%
										<option value='원'>원
									</select>를 <select name=updown class="input">
										<option value='down'>할인
										<option value='up'>할증
									</select>된 가격으로 <select name=tgprice class="input">
										<option value='sellprice'>판매가격
										<option value='consumerprice'>시중가격
									</select>을 일괄 수정<BR> 
								</td>
							</tr>
							<tr>
								<td width="100%" style="padding-top:2pt; padding-bottom:2pt;">상품 절사 단위는 <select name=danwi class="input">
									<option value="1">1
									<option value="10">10
									<option value="100">100
									<option value="1000">1000
								</select>원 단위로 <select name=cut class="input">
									<option value="floor">내림
									<option value="round">반올림
									<option value="ceiling">올림
								</select>하여 절사합니다.</td>
							</tr>
							<tr>
								<td width="100%">&nbsp;</td>
							</tr>
							<tr>
								<td width="100%" style="padding-top:5pt; padding-bottom:5pt;">
									<table cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td width="100%" class="font_orange" style="padding-top:5pt; padding-bottom:5pt; border-width:1pt; border-color:rgb(0,153,204); border-style:solid;">
												&nbsp;&nbsp;* 코디/조립 판매가격은 일괄 변경이 불가능합니다.<br>
												&nbsp;&nbsp;* 상품가격 직접(원) 입력시 콤마(,)를 제외한 숫자만 입력하세요.<br>
												&nbsp;&nbsp;* 상품가격 비율(％) 입력시 소수점 첫째자리까지만 정상 처리됩니다.<br>
												&nbsp;&nbsp;* 선택된 카테고리내 모든 하위 카테고리의 상품 가격이 변경됩니다.<br>
												&nbsp;&nbsp;* 변경된 상품가격은 복원되지 않으므로 신중히 처리하시기 바랍니다.<br>
												&nbsp;&nbsp;* <b>[일괄 상품가격 보기]</b> 버튼을 누르면 해당 카테고리의 상품정보를 보실 수 있습니다.
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td width="100%"></td>
							</tr>
							</table>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height=10></td>
					</tr>
					<tr>
						<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0" hspace="0" vspace="4"></a>&nbsp;&nbsp;<a href="javascript:GoAllUpdate();"><img src="images/btn_allprice.gif" border="0" hspace="2" vspace="4"></a></td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</form>
			<form name=form2 action="product_allupdate.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=code_a>
			<input type=hidden name=code_b>
			<input type=hidden name=code_c>
			<input type=hidden name=code_d>
			</form>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>판매상품 가격 일괄수정</span></dt>
							<dd>
								- 코디/조립 판매가격은 일괄 변경이 불가능합니다.<Br>
								- 상품가격 변경은 등록된 상품가격 기준으로 계산됩니다.<br>
														<b>&nbsp;&nbsp;</b>예) 판매가격 10,000원인 상품<br>
														<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>판매가격의 10% <span class="font_blue">할인</span>된 가격으로 일괄 변경할 경우<b>&nbsp;&nbsp;</b>9,000원으로 변경됩니다.<br>
														<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>판매가격의 10% <span class="font_orange">할증</span>된 가격으로 일괄 변경할 경우 11,000원으로 변경됩니다.<br>
								- 개별 상품 가격 수정은 [상품 등록/수정/삭제] 메뉴에서 처리하시기 바랍니다.<br>
														<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(4,'product_register.php');"><span class="font_blue">상품관리 > 카테고리/상품관리 > 상품 등록/수정/삭제</span></a><br>
								- 선택된 카테고리내 모든 하위카테고리에 등록된 모든 상품의 가격이 일괄변경되므로 카테고리 선택에 주의하세요.

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
$sql = "SELECT * FROM tblproductcode WHERE type!='T' AND type!='TX' AND type!='TM' AND type!='TMX' ";
$sql.= "ORDER BY sequence DESC ";
include("codeinit.php");
include("copyright.php");