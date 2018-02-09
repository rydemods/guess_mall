<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-1";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];

if($type=="modify") {
	$sel_topleft=$_POST["sel_topleft"];
	$sel_mainetc=$_POST["sel_mainetc"];

	$sql = "SELECT * FROM tbltempletinfo WHERE icon_type='{$_shopdata->icon_type}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$templet_icon_type=$row->icon_type;
		$templet_top_type=$row->top_type;
		$templet_main_type=$row->main_type;
		$templet_menu_type=$row->menu_type;
	} else {
		$templet_icon_type="001";
		$templet_top_type="top001";
		$templet_main_type="main001";
		$templet_menu_type="menu001";
	}
	pmysql_free_result($result);
	if($sel_topleft!="NO" || $sel_mainetc!="NO") {
		$qry="";
		switch($sel_topleft){
			case "ALL":
				$qry.="top_type='topp', menu_type='menup',";
				break;
			case "TOP":
				$qry.="top_type='topp', menu_type='{$templet_menu_type}',";
				break;
			case "LEFT":
				$qry.="top_type='{$templet_top_type}', menu_type='menup',";
				break;
			case "NO":
				$qry.="top_type='{$templet_top_type}', menu_type='{$templet_menu_type}',";
				break;			
		}
		switch($sel_mainetc) {
			case "M":
				$qry.="title_type='Y', main_type='mainm',";
				break;
			case "N":
				$qry.="title_type='Y', main_type='mainn',";
				break;
			case "P":
				$qry.="title_type='Y', main_type='{$templet_main_type}',";
				break;
			case "NO":
				$qry.="title_type='N', main_type='{$templet_main_type}',";
				break;
		}

		$qry=rtrim($qry,',');
		if(ord($qry)) {
			$sql = "UPDATE tblshopinfo SET {$qry} ";
			$update=pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){alert(\"개별디자인 적용선택이 완료되었습니다.\");}</script>";
		}
	} else {
		$sql = "UPDATE tblshopinfo SET 
		top_type	= '{$templet_top_type}', 
		menu_type	= '{$templet_menu_type}', 
		main_type	= '{$templet_main_type}', 
		title_type	= 'N', 
		icon_type	= '{$templet_icon_type}' ";
		$update=pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){alert(\"디자인 템플릿으로 변경되었습니다.\");}</script>";
	}
	DeleteCache("tblshopinfo.cache");
}

$sql = "SELECT top_type, menu_type, main_type, title_type, icon_type FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);

$design_top_type=$row->top_type;
$design_menu_type=$row->menu_type;
$design_main_type=$row->main_type;
$design_title_type=$row->title_type;
$design_icon_type=$row->icon_type;
pmysql_free_result($result);
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("선택된 항목의 개별디자인을 하셨습니까?\n\n개별디자인을 하셨다면 \"확인\"버튼을 누르시기 바랍니다.")) {
		document.form1.type.value="modify";
		document.form1.submit();
	}
}
</script>

<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 웹FTP, 개별적용 선택 &gt;<span>개별디자인 적용선택</span></p></div></div>

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
			<td valign="top" >
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 타이틀 -->
					<div class="title_depth3">개별디자인 적용선택</div>
				</td>
			</tr>
            <tr><td height="20"></td></tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 개별 메인, 상단, 왼쪽 디자인, 각종 타이틀을 선택적으로 적용을 할 수 있습니다.</li>
                            <li>2) 적용선택을 하면 곧바로 쇼핑몰에 반영됩니다.(템플릿, Easy 디자인 자동해제)</li>
                        </ul>
                    </div>
                </td>
            </tr>    
            <tr><td>
            	<table width="100%">
				<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden name=type>
				<tr>
					<TD  valign=top>
					<div class="point_title">상단과 왼쪽 개별적용 선택</div>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><img src="images/design_option_img01.gif" border="0"></td>
						<td width="100%" valign="middle"><input type=radio id="idx_topleft0" name=sel_topleft value="ALL" <?php if ($design_top_type=="topp" && $design_menu_type=="menup") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_topleft0><span style="letter-spacing:-0.5pt;">상단+왼쪽 동시 적용</span></label><BR>
							<input type=radio id="idx_topleft1" name=sel_topleft value="TOP" <?php if ($design_top_type=="topp" && $design_menu_type!="menup") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_topleft1><span style="letter-spacing:-0.5pt;">상단만 적용</span></label><BR>
							<input type=radio id="idx_topleft2" name=sel_topleft value="LEFT" <?php if ($design_top_type!="topp" && $design_menu_type=="menup") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_topleft2><span style="letter-spacing:-0.5pt;">왼쪽만 적용</span></label><BR>
							<input type=radio id="idx_topleft3" name=sel_topleft value="NO" <?php if ($design_top_type!="topp" && $design_menu_type!="menup") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_topleft3><span style="letter-spacing:-0.5pt;">상단+왼쪽 모두 적용 안함</span></label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_blue">(템플릿으로 변경)</span>
						</td>
					</tr>
					</table>
					<?php if ($design_top_type=="tope" && $design_menu_type!="menue") {  ?>
					<div class="point_title">상단과 왼쪽 개별적용 선택</div>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><img src="images/design_option_img02.gif" border="0"></td>
						<td width="100%" valign="middle"><input type="radio" name="sel_topleft" value="TOPE" checked><span class=font_orange style="letter-spacing:-0.5pt;">Easy 상단 디자인이 적용 중...<?php if($design_menu_type=="menup") echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"font_blue\">(왼쪽 메뉴 개별디자인 상태)</span>"; ?></span></td>
					</tr>
					</table>
					<?php } else if ($design_top_type!="tope" && $design_menu_type=="menue") { ?>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><img src="images/design_option_img02.gif" border="0"></td>
						<td width="100%" valign="top"><input type="radio" name="sel_topleft" value="LEFTE" checked><span class=font_orange style="letter-spacing:-0.5pt;">Easy 왼쪽 디자인이 적용 중...<br><?php if($design_menu_type=="menup") echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"font_blue\">(상단 메뉴 개별디자인 상태)</span>"; ?></span></td>
					</tr>
					</table>
					<?php } else if ($design_top_type=="tope" && $design_menu_type=="menue") { ?>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td><img src="images/design_option_img02.gif" border="0"></td>
						<td width="100%" valign="top"><input type="radio" name="sel_topleft" value="ALLE" checked><span class=font_orange style="letter-spacing:-0.5pt;">Easy 상단/왼쪽 디자인이 적용 중...</span></td>
					</tr>
					</table>
					<?php }?>
					</td>
					<td  valign="middle">
					<div class="point_title">메인본문과  타이틀 개별적용 선택</div>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<tr>
						<td valign="middle"><img src="images/design_option_img03.gif" border="0"></td>
						<td width="100%" valign="top"><input type=radio id="idx_mainetc0" name=sel_mainetc value="M" <?php if ($design_main_type=="mainm") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mainetc0><span style="letter-spacing:-0.5pt;">메인본문 적용+<span class="font_orange">메인 왼쪽메뉴 출력</span></span></label><br>
							<input type=radio id="idx_mainetc1" name=sel_mainetc value="N" <?php if ($design_main_type=="mainn") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mainetc1><span style="letter-spacing:-0.5pt;">메인본문 적용+<span class="font_orange">메인 왼쪽메뉴 미출력</span></span></label><br>
							<input type=radio id="idx_mainetc2" name=sel_mainetc value="P" <?php if ($design_title_type=="Y" && strlen($design_main_type)==7) echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mainetc2><span style="letter-spacing:-0.5pt;">각종 타이틀 개별디자인 적용</label></span><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_blue">(전체 타이틀 중 변경한 이미지만 변경)</span><br>
							<input type=radio id="idx_mainetc3" name=sel_mainetc value="NO" <?php if($design_title_type!="Y") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mainetc3><span style="letter-spacing:-0.5pt;">메인본문+각종 타이틀 모두 적용 안함</span></label><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font_blue">(템플릿으로 변경)</span>
						</td>
					</tr>
					</table>
					</TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
						<dt><span>개별 디자인 적용</span></dt>
						<dd>- 적용선택을 하면 곧바로 쇼핑몰에 반영됩니다.(템플릿, Easy 디자인 자동해제)<br>
						<b>&nbsp;&nbsp;</b>반대로 템플릿, Easy 디자인을 재 선택하면 개별디자인이 자동 해제됩니다.<br>
						- 개별 디자인 -> [개별디자인 적용선택] -> 적용을 원하는 부분을 체크박스 선택(반드시 적용부분을 체크해야만 쇼핑몰에 반영)<br>
						- 기본에 개별 디자인한 내용이 없어도(내용이 공란이라도) 적용 체크한 경우<br>
						<b>&nbsp;&nbsp;</b>곧바로 쇼핑몰에 [디자인 준비중입니다.]라는 안내와 함께 쇼핑몰이 빈내용으로 변경됩니다.</dd>
                    </dl>
                    <dl>
                    	<dt><span>적용안함</span></dt>
                        <dd>- 개별디자인 적용 해제되고 사용중이던 템플릿으로 변경됩니다.(개별디자인 한 내용은 보관됨)</dd>
                    </dl>
                    <dl>
                    	<dt><span>각종 타이틀 개별디자인 적용의 특성</span></dt>
                        <dd>- 전체 타이틀이미지 중에서 변경된 이미지만 변경되고 나머지 이미지들은 사용하던 템플릿의 타이틀로 유지됩니다.</dd>
                    </dl>
                    <dl>
                    	<dt><span>플래시 출력</span></dt>
                        <dd><a href="https://www.microsoft.com/korea/windows/ie/ie6/activex/default.mspx" target=_blank><span class="font_orange">[IE 설계 변경 관련 안내]</span></a><br>
						<a href="https://www.microsoft.com/korea/windows/ie/ie6/activex/activate/default.mspx" target=_blank><span class="font_orange">[ActiveX 컨트롤 활성화 가이드]</span></a><br />위 링크의 내용과 같이 2006-04-12 부터 IE(Internet Explorer)의 중요 업데이트로 적용됐습니다.<br>
						정상적인 플래시 출력을 위해서 아래의 내용을 참고하셔서 사용하시기 바랍니다.</dd>
                    </dl>
                    <dl>
                    	<dd>
                        <table>
                        <tr>
							<td width="20" align="right">&nbsp;</td>
							<td width="796" class="space_top">
						- <b>간단 출력 방법</b><br><span class="font_blue">
						&nbsp;&nbsp;&nbsp;&lt;script&gt;<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;flash_show("플래시파일경로","가로크기","세로크기");<br>
						&nbsp;&nbsp;&nbsp;&lt;/script&gt;</span>
							</td>
						</tr>
                        <tr>
                            <td height="10" colspan="2"></td>
                        </tr>
                        <tr>
                            <td width="20" align="right">&nbsp;</td>
                            <td width="796" class="space_top">
						- <b>상세 출력 방법(파라미터 추가)</b><br><span class="font_blue">
						&nbsp;&nbsp;&nbsp;&lt;script&gt;<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj=new embedcls();<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.init("플래시파일경로","가로크기","세로크기");<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.setparam("파라미터명","파라미터값");<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.setparam("파라미터명","파라미터값");<br>
						<span style="line-height:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br></span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.show();<br>
						&nbsp;&nbsp;&nbsp;&lt;/script&gt;</span>
							</td>
						</tr>
						</table>
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
<?=$onload?>
<?php 
include("copyright.php");
