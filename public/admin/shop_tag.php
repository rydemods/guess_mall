<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$sql = "SELECT etctype FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$tagtype="";
if($row=pmysql_fetch_object($result)) {
	if (ord($row->etctype)) {
		$etctemp = explode("TAGTYPE=",$row->etctype);
		
		if (ord($etctemp[1])) {
			if(ord($etctemp[1][0]) && $etctemp[1][1] == "") {
				$tagtype=$etctemp[1][0];
				$etctempvalue = substr($etctemp[1],2);
			} else {
				$etctempvalue = $etctemp[1];
			}
		}

		$etctype = $etctemp[0].$etctempvalue;
	}
}
pmysql_free_result($result);

$type=$_POST["type"];
$up_tag=$_POST["up_tag"];
$up_listtag=$_POST["up_listtag"];

if($type=="up") {
	if(ord($up_tag)==0) $up_tag="Y";

	if($up_tag == "Y" && $up_listtag == "N") {
		$up_tag = "L";
	}

	$tag_info.="TAGTYPE={$up_tag}";
	$tagtype = $up_tag;

	$sql="UPDATE tblshopinfo SET etctype='".$etctype.$tag_info."' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('상품태그 관련 기능 설정이 완료되었습니다.'); }</script>";
}

if(ord($tagtype)==0) {
	$tagtype = "Y";
}

if($tagtype == "Y") {
	$check_tagY="checked";
	$check_listtagY="checked";
	$listdisabled="";
} else if($tagtype == "L") {
	$check_tagY="checked";
	$check_listtagN="checked";
	$listdisabled="";
} else {
	$check_tagN="checked";
	$check_listtagY="checked";
	$listdisabled="disabled";
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<!--
function CheckForm() {
	if (!confirm("태그 정보를 저장하겠습니까?")) {
		return;
	}
	form1.type.value="up";
	form1.submit();
}

function tag_change(form) {
	if(form.up_tag[0].checked) {
		form.up_listtag[0].disabled=false;
		form.up_listtag[1].disabled=false;
	} else {
		form.up_listtag[0].disabled=true;
		form.up_listtag[1].disabled=true;
	}
}
//-->
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>상품태그 관련 기능설정</span></p></div></div>
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
					<div class="title_depth3">상품태그 관련 기능설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품의 태그(Tag)관련 기능을 설정하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품태그 기능 설정</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>상품태그 사용여부 선택</span></th>
					<TD class="td_con1" >
                        <div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <tr>
                            <td><input type=radio id="idx_tag1" name=up_tag value="Y" <?=$check_tagY?> onclick="tag_change(this.form);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tag1>상품태그 기능 사용</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_tag2" name=up_tag value="N" <?=$check_tagN?> onclick="tag_change(this.form);"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_tag2>상품태그 기능 미사용</label></td>
                        </tr>
                        <tr>
                            <td height="4"></td>
                        </tr>
                        <tr>
                            <td>&nbsp;<span class=font_blue>* 상품태그(Tag)는 상품에 고객들이 직접 Tag를 입력 및 공유하는 Web2.0 기반의 기능입니다.</span></TD>
                        </tr>
                        </table>
                        </div>
					</td>
				</TR>
				<TR>
					<th><span>상품 목록 상품태그 출력여부</span></th>
					<TD class="td_con1">
                        <div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                        <tr>
                            <td><input type=radio id="idx_listtag1" name=up_listtag value="Y" <?=$check_listtagY?> <?=$listdisabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_listtag1>상품태그 출력함</label>&nbsp;&nbsp;&nbsp;<input type=radio id="idx_listtag2" name=up_listtag value="N" <?=$check_listtagN?> <?=$listdisabled?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_listtag2>상품태그 미출력</label></td>
                        </tr>
                        <tr>
                            <td height="4"></td>
                        </tr>
                        <tr>
                            <td>&nbsp;<span class=font_blue>* 상품목록 출력페이지에서 최근 등록된 태그를 출력할 수 있습니다.</span></TD>
                        </tr>
                        </table>
                        </div>
					</td>
				</tr>
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
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>상품태그(Tag)란?</span></dt>
							<dd>참여, 개방, 공유의 web2.0 기능으로 고객이 직접 상품에 대한 느낌이나 특징을 단어 꼬리표(tag)를 입력하면 동일한 tag를 가진 모든 상품을 검색해줍니다.
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
