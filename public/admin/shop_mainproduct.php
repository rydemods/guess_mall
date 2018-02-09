<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$mnew_cols=$_POST["mnew_cols"];
$mnew_type=$_POST["mnew_type"];
$mnew_rows=$_POST["mnew_rows"];

$mbest_cols=$_POST["mbest_cols"];
$mbest_type=$_POST["mbest_type"];
$mbest_rows=$_POST["mbest_rows"];

$mhot_cols=$_POST["mhot_cols"];
$mhot_type=$_POST["mhot_type"];
$mhot_rows=$_POST["mhot_rows"];

$main_notice_num=$_POST["main_notice_num"];
$main_special_num=$_POST["main_special_num"];
$main_special_type=$_POST["main_special_type"];
$main_info_num=$_POST["main_info_num"];

$prlist_num=$_POST["prlist_num"];

if ($type=="up" && ord($mnew_rows)) {
	$mnew_num=$mnew_rows*$mnew_cols;
	$mbest_num=$mbest_rows*$mbest_cols;
	$mhot_num=$mhot_rows*$mhot_cols;
	
	$main_newprdt=$mnew_num."|{$mnew_cols}|".$mnew_type;
	$main_bestprdt=$mbest_num."|{$mbest_cols}|".$mbest_type;
	$main_hotprdt=$mhot_num."|{$mhot_cols}|".$mhot_type;

	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "main_newprdt		= '{$main_newprdt}', ";
	$sql.= "main_bestprdt		= '{$main_bestprdt}', ";
	$sql.= "main_hotprdt		= '{$main_hotprdt}', ";
	$sql.= "main_notice_num		= '{$main_notice_num}', ";
	$sql.= "main_special_num	= '{$main_special_num}', ";
	$sql.= "main_special_type	= '{$main_special_type}', ";
	$sql.= "main_info_num		= '{$main_info_num}', ";
	$sql.= "prlist_num			= '{$prlist_num}' ";
	$result = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('정보 수정이 완료되었습니다. $msg'); }</script>";
}

$sql = "SELECT * FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$main_newprdt = explode("|",$row->main_newprdt);
	$main_bestprdt = explode("|",$row->main_bestprdt);
	$main_hotprdt = explode("|",$row->main_hotprdt);
	
	$main_notice_num = $row->main_notice_num;
	$main_special_num = $row->main_special_num;
	$main_special_type = $row->main_special_type;
	$main_info_num = $row->main_info_num;
	
	$mnew_num=$main_newprdt[0];
	$mnew_cols=$main_newprdt[1];
	$mnew_type=$main_newprdt[2];
	$mbest_num=$main_bestprdt[0];
	$mbest_cols=$main_bestprdt[1];
	$mbest_type=$main_bestprdt[2];
	$mhot_num=$main_hotprdt[0];
	$mhot_cols=$main_hotprdt[1];
	$mhot_type=$main_hotprdt[2];
	
	$mnew_rows = $mnew_num / $mnew_cols;
	$mbest_rows = $mbest_num / $mbest_cols;
	$mhot_rows = $mhot_num / $mhot_cols;

	$prlist_num = $row->prlist_num;
}
pmysql_free_result($result);
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(type) {
	if(document.form1.mnew_cols.value>5) {
		rowsresult = false;
		for(var i=0; i<5; i++) {
			if(document.form1.mnew_rows[i].checked) {
				rowsresult = true;
				break;
			}
		}
		if(rowsresult == false) {
			alert('신규상품 진열 상품의 줄수를 선택해 주세요.');
			document.form1.mnew_rows[0].focus();
			return;
		}
	}

	if(document.form1.mbest_cols.value>5) {
		rowsresult = false;
		for(var i=0; i<5; i++) {
			if(document.form1.mbest_rows[i].checked) {
				rowsresult = true;
				break;
			}
		}
		if(rowsresult == false) {
			alert('인기상품 진열 상품의 줄수를 선택해 주세요.');
			document.form1.mbest_rows[0].focus();
			return;
		}
	}

	if(document.form1.mhot_cols.value>5) {
		rowsresult = false;
		for(var i=0; i<5; i++) {
			if(document.form1.mhot_rows[i].checked) {
				rowsresult = true;
				break;
			}
		}
		if(rowsresult == false) {
			alert('추천상품 진열 상품의 줄수를 선택해 주세요.');
			document.form1.mhot_rows[0].focus();
			return;
		}
	}
	
	if(confirm('현재 설정을 적용하겠습니까?')) {
		form1.type.value=type;
		form1.submit();
	}
}
//best
function changeimg(temp,temp2){
	temp3="";

	if(temp==1){
		if(temp2.options[temp2.selectedIndex].value<=5) document.form1.plusrow.disabled=false;
		else {
			document.form1.plusrow.checked=false;
			document.form1.plusrow.disabled=true;
		}
		if(document.form1.plusrow.checked){
			temp3="A";
		}
		img=document.form1.productimg;
	} else if(temp==2) {
		if(temp2.options[temp2.selectedIndex].value<=5) document.form1.plusnewrow.disabled=false;
		else {
			document.form1.plusnewrow.checked=false;
			document.form1.plusnewrow.disabled=true;
		}
		if(document.form1.plusnewrow.checked){
			temp3="A";
		}
		img=document.form1.newimg;
	} else {
		if(temp2.options[temp2.selectedIndex].value<=5) document.form1.plusbestrow.disabled=false;
		else {
			document.form1.plusbestrow.checked=false;
			document.form1.plusbestrow.disabled=true;
		}
		if(document.form1.plusbestrow.checked){
			temp3="A";
		}
		img=document.form1.bestimg;
	}
	displaydiv(temp);
	img.src="images/product_num"+temp2.options[temp2.selectedIndex].value+temp3+".gif";
}

function displaydiv(temp){
	var layername2 = new Array ('display1','display2','display3','display4','display5','display6');

	if(temp==1){ 
		start=0; end=2;
		if(document.form1.plusrow.checked) shop="display2";
		else  shop="display1";
	} else if(temp==2) { 
		start=2; end=4;
		if(document.form1.plusnewrow.checked) shop="display4";
		else  shop="display3";
	} else {
		start=4; end=6;
		if(document.form1.plusbestrow.checked) shop="display6";
		else  shop="display5";
	}
	if(document.all){
		for(i=start;i<end;i++) document.all(layername2[i]).style.display="none";
		document.all(shop).style.display="block";
	} else if(document.getElementById){
		for(i=start;i<end;i++) document.getElementById(layername2[i]).style.display="none";
		document.getElementById(shop).style.display="block";
	} else if(document.layers){
		for(i=start;i<end;i++) document.layers(layername2[i]).display="none";
		document.layers[shop].display="block";
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>상품 진열수/화면설정</span></p></div></div>
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
					<div class="title_depth3">상품 진열수/화면설정</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 메인 상품 및 카테고리 상품의 진열수와 메인 상품 진열 타입을 설정할 수 있습니다.</span></div>
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=etcvalue value="<?=$etcvalue?>">
			<tr>
				<td>
					<div class="title_depth3_sub">신규상품 진열 개수</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 메인페이지 진열상품의 라인당 상품진열 숫자와 진열줄수, 진열타입을 설정할 수 있습니다.</li>
                            <li>2) 메인에 진열할 상품을 등록하더라도 진열개수를 설정한 숫자만큼만 진열됩니다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>			
            <tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9; border-right:1px solid #b9b9b9;">
				<TR style="background-color:#f8f8f8;">
					<td>
                   		<div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td>&nbsp;<b>라인(가로)별 상품수 : <select name=mnew_cols onchange=changeimg(2,this) style="width:42px" class="select">
                            <?php
                            for($i=1;$i<=8;$i++){
                                echo "<option value=\"{$i}\"";
                                if($i==$mnew_cols) echo " selected";
                                echo ">{$i}개";
                            }
                            if($mnew_rows>5) $plusnewrow="Y";
                            ?>
                            </select> <input type=checkbox id="idx_plusnewrow1" name=plusnewrow value="Y" <?=$plusnewrow=="Y"?"checked":""?> onclick=changeimg(2,document.form1.mnew_cols)> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_plusnewrow1>5줄(세로)이상 추가.</label> 라인별 5개 사용까지만 가능</b></td>
                        </tr>
                        <tr>
                            <td nowrap>
                            <input type=radio id="idx_mnew_type0" name=mnew_type value="I" <?php if ($mnew_type=="I") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mnew_type0><span class="font_orange">이미지A형 타입 진열</span> <b>(권장)</b> </label>
                            <input type=radio id="idx_mnew_type1" name=mnew_type value="D" <?php if ($mnew_type=="D") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mnew_type1>이미지B형 타입 진열</label>
                            <input type=radio id="idx_mnew_type2" name=mnew_type value="L" <?php if ($mnew_type=="L") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mnew_type2>리스트형 타입(줄수만 적용) 진열</label>
                            </td>
                        </tr>
                        </table>
                        </div>
					</td>
				</TR>
				<TR>
					<TD class=linebottomleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="745" bgColor=#ffffff colspan="2">
					<div class="table_none">
                    <table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="150"><img src="images/shop_mainproduct_img1.gif" border="0"></td>
						<td width="560">
						<table cellpadding="2" cellspacing="0" width="487">
						<TR>
							<TD>
							<div id=display3 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: <?=(strlen($plusnewrow)=="0"?"block":"none")?>; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<?php for($i=1;$i<=5;$i++){?>
									<TD align=middle><p align="center"><input type=radio id="idx_mnew_rows<?=$i?>" name=mnew_rows value="<?=$i?>" <?php if ($mnew_rows==$i) echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mnew_rows<?=$i?>><?=($i)?>줄</label></td>
								<?php }?>
							</TR>
							</TABLE>
							</DIV>
							<div id=display4 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: <?=($plusnewrow=="Y"?"block":"none")?>; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD align=middle><p align="center">&nbsp;</td>
								<?php for($i=6;$i<=8;$i++){ ?>
									<TD align=middle><p align="center"><input type=radio id="idx_mnew_rows<?=$i?>" name=mnew_rows value="<?=$i?>" <?php if ($mnew_rows==$i) echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mnew_rows<?=$i?>><?=($i)?>줄</label></td>
								<?php }?>
								<TD align=middle><p align="center">&nbsp;</td>
							</TR>
							</TABLE>
							</DIV>
							</td>
						</tr>
						<tr>
							<td width="483"><img src="images/product_num<?=$mnew_cols.($plusnewrow=="Y"?"A":"")?>.gif" align=absmiddle border="0" name=newimg></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
                    </div>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">인기상품 진열 개수</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9; border-right:1px solid #b9b9b9;">
				<TR style="background-color:#f8f8f8;">
					<td>
                   		<div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td>&nbsp;<b>라인(가로)별 상품수 : <select name=mbest_cols onchange=changeimg(3,this) style="width:40px" class="select">
						<?php
						for($i=1;$i<=8;$i++){
							echo "<option value=\"{$i}\"";
							if($i==$mbest_cols) echo " selected";
							echo ">{$i}개";
						}
						if($mbest_rows>5) $plusbestrow="Y";
						?>
						</select> <input type=checkbox id="idx_plusbestrow1" name=plusbestrow value="Y" <?=$plusbestrow=="Y"?"checked":""?> onclick=changeimg(3,document.form1.mbest_cols)> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_plusbestrow1>5줄(세로)이상 추가.</label> 라인별 5개 사용까지만 가능</td>
						</tr>
						<tr>
							<td nowrap>
						<input type=radio id="idx_mbest_type0" name=mbest_type value="I" <?php if ($mbest_type=="I") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mbest_type0><span class="font_orange">이미지A형 타입 진열<b>(권장)</b></span></label>
						<input type=radio id="idx_mbest_type1" name=mbest_type value="D" <?php if ($mbest_type=="D") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mbest_type1>이미지B형 타입 진열</label>
						<input type=radio id="idx_mbest_type2" name=mbest_type value="L" <?php if ($mbest_type=="L") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mbest_type2>리스트형 타입 진열(줄수만 적용)</label>
							</td>
						</tr>
						</table>
                        </div>
					</TD>
				</TR>
				<TR>
					<TD class=linebottomleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="745" bgColor=#ffffff colspan="2">
                    <div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="150"><img src="images/shop_mainproduct_img2.gif" border="0"></td>
						<td width="560">
						<table cellpadding="2" cellspacing="0" width="487">
						<TR>
							<TD>
							<div id=display5 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: <?=(strlen($plusbestrow)=="0"?"block":"none")?>; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<?php for($i=1;$i<=5;$i++){?>
									<TD align=middle><p align="center"><input type=radio id="idx_mbest_rows<?=$i?>" name=mbest_rows value="<?=$i?>" <?php if ($mbest_rows==$i) echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mbest_rows<?=$i?>><?=($i)?>줄</label></td>
								<?php }?>
							</TR>
							</TABLE>
							</DIV>
							<div id=display6 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: <?=($plusbestrow=="Y"?"block":"none")?>; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD align=middle><p align="center">&nbsp;</td>
								<?php for($i=6;$i<=8;$i++){ ?>
									<TD align=middle><p align="center"><input type=radio id="idx_mbest_rows<?=$i?>" name=mbest_rows value="<?=$i?>" <?php if ($mbest_rows==$i) echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mbest_rows<?=$i?>><?=($i)?>줄</label></td>
								<?php }?>
								<TD align=middle><p align="center">&nbsp;</td>
							</TR>
							</TABLE>
							</DIV>
							</td>
						</tr>
						<tr>
							<td width="483"><img src="images/product_num<?=$mbest_cols.($plusbestrow=="Y"?"A":"")?>.gif" align=absmiddle border=0 name=bestimg></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
                    </div>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">추천상품 진열 개수</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border-left:1px solid #b9b9b9; border-right:1px solid #b9b9b9;">
				<TR style="background-color:#f8f8f8;">
					<td>
                   		<div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td>&nbsp;<b>라인(가로)별 상품수 : <select name=mhot_cols onchange=changeimg(1,this) style="width:40px" class="select">
						<?php
						for($i=1;$i<=8;$i++){
							echo "<option value=\"{$i}\"";
							if($i==$mhot_cols) echo " selected";
							echo ">{$i}개";
						}
						if($mhot_rows>5) $plusrow="Y";
						?>
						</select> <input type=checkbox id="idx_plusrow1" name=plusrow value="Y" <?=$plusrow=="Y"?"checked":""?> onclick=changeimg(1,document.form1.mhot_cols)> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_plusrow1>5줄(세로)이상 추가.</label> 라인별 5개 사용까지만 가능.</td>
						</tr>
						<tr>
							<td nowrap>
						<input type=radio id="idx_mhot_type0" name=mhot_type value="I" <?php if ($mhot_type=="I") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mhot_type0><span class="font_orange">이미지A형 타입 진열<b>(권장)</b></span></label>
						<input type=radio id="idx_mhot_type1" name=mhot_type value="D" <?php if ($mhot_type=="D") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mhot_type1>이미지B형 타입 진열</label>
						<input type=radio id="idx_mhot_type2" name=mhot_type value="L" <?php if ($mhot_type=="L") echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mhot_type2>리스트형 타입 진열(줄수만 적용)</label>
							</td>
						</tr>
						</table>
                        </div>
					</TD>
				</TR>
				<TR>
					<TD class=linebottomleft style="PADDING-RIGHT: 5px; PADDING-LEFT: 10px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px" align=left width="745" bgColor=#ffffff colspan="2">
                    <div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="150"><img src="images/shop_mainproduct_img3.gif" border="0"></td>
						<td width="560">
						<table cellpadding="2" cellspacing="0" width="487">
						<TR>
							<TD>
							<DIV id=display1 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: <?=(strlen($plusrow)=="0"?"block":"none")?>; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<?php for($i=1;$i<=5;$i++){?>
									<TD align=middle><p align="center"><input type=radio id="idx_mhot_rows<?=$i?>" name=mhot_rows  value="<?=$i?>" <?php if ($mhot_rows==$i) echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mhot_rows<?=$i?>><?=($i)?>줄</label></td>
								<?php }?>
							</TR>
							</TABLE>
							</DIV>
							<DIV id=display2 style="BORDER-RIGHT: black 0px solid; PADDING-RIGHT: 0px; BORDER-TOP: black 0px solid; DISPLAY: <?=($plusrow=="Y"?"block":"none")?>; PADDING-LEFT: 0px; BACKGROUND: #ffffff; PADDING-BOTTOM: 0px; MARGIN-LEFT: 0px; BORDER-LEFT: black 0px solid; PADDING-TOP: 0px; BORDER-BOTTOM: black 0px solid">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD align=middle><p align="center">&nbsp;</td>
								<?php for($i=6;$i<=8;$i++){ ?>
									<TD align=middle><p align="center"><input type=radio id="idx_mhot_rows<?=$i?>" name=mhot_rows  value="<?=$i?>" <?php if ($mhot_rows==$i) echo "checked"; ?>><label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_mhot_rows<?=$i?>><?=($i)?>줄</label></td>
								<?php }?>
								<TD align=middle><p align="center">&nbsp;</td>
							</TR>
							</TABLE>
							</DIV>
							</td>
						</tr>
						<tr>
							<td width="483"><img src="images/product_num<?=$mhot_cols?><?=($plusrow=="Y"?"A":"")?>.gif" align=absmiddle border=0 name=productimg></td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
                    </div>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li><img src="images/mainproduct_imageA.gif" border="0"> <b>이미지A형 타입</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/mainproduct_imageB.gif" border="0"> <b>이미지B형 타입</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/mainproduct_list.gif" border="0"> <b>리스트형 타입</b></li>
                            <li>1) 신규, 인기, 추천, 특별상품별 각각의 독립적인 페이지가 제공됩니다.</li>
                            <li>2) <a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');"><span class="font_blue">디자인관리 > 개별디자인 - 메인 및 상하단 > 메인본문 꾸미기</span></a> 에서 직접 디자인 변경도 가능합니다.</li>
                            <li>3) 개별디자인의 메인본문 꾸미기에서 [특별상품]의 배치를 자유롭게 이동 할 수 있습니다.</li>
                        </ul>
                    </div>
                    
            	</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">특별상품 및 우측 디스플레이 설정 <span>특별 상품과 우측 메뉴들의 표시개수 설정을 합니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><center><img src="images/shop_mainproduct_img4.gif" border="0"></center></th>
                    <td class="td_con1"  >
               		<div class="table_none">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td width="207">①공지사항 표시개수</td>
							<td width="50"><select name=main_notice_num style="width:40px" class="select">
<?php
			for ($i=3;$i<=10;$i++) {
				if ($i==$main_notice_num) {
?>
				<option value="<?php echo $i ?>" selected><?php echo $i ?>
<?php
				} else {
?>
				<option value="<?php echo $i ?>"><?php echo $i ?>
<?php
				}
			}
?>
							</select>개</td>
							<td width="340" valign=bottom>&nbsp;&nbsp;<span class="font_blue"><a href="javascript:parent.topframe.GoMenu(7,'market_notice.php');">마케팅지원 > 마케팅지원 > 공지사항 관리</a></span>에 출력됨.</td>
						</tr>
						<tr>
							<td width="207">②특별상품 표시개수</td>
							<td width="50"><select name=main_special_num style="width:40px" class="select">
<?php
				for ($i=1;$i<=10;$i++) {
					if ($i==$main_special_num) {
?>
					<option value="<?php echo $i ?>" selected><?php echo $i ?>
<?php
					} else {
?>
						<option value="<?php echo $i ?>"><?php echo $i ?>
<?php
					}
				}
?>
								</select>개</td>
							<td width="340" valign=bottom><p align="left">&nbsp;<input type=radio id="idx_main_special_type1" name=main_special_type value="N" <?php if($main_special_type == "N") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_main_special_type1>고정된 상품리스트</label> <input type=radio id="idx_main_special_type2" name=main_special_type value="Y" <?php if($main_special_type == "Y") echo "checked ";?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_main_special_type2>흐르는 상품리스트</label></td>
						</tr>
						<tr>
							<td width="207">③정보(information)관리 표시개수</td>
							<td width="50"><select name=main_info_num style="width:40px" class="select">
<?php
			for ($i=3;$i<=10;$i++) {
				if ($i==$main_info_num) {
?>
				<option value="<?php echo $i ?>" selected><?php echo $i ?>
<?php
				} else {
?>
				<option value="<?php echo $i ?>"><?php echo $i ?>
<?php
				}
			}
?>
								</select>개</td>
							<td width="340" valign=bottom>&nbsp;&nbsp;<span class="font_blue"><a href="javascript:parent.topframe.GoMenu(7,'market_contentinfo.php');">마케팅지원 > 마케팅지원 > 컨텐츠 관리</a></span>에 출력됨.</td>
						</tr>
						<tr>
							<td width="558" colspan="3" height=10></td>
						</tr>
						<tr>
							<td width="558" colspan="3">* <span class="font_blue"><a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');">디자인관리 > 개별디자인 - 메인 및 상하단 > 메인본문 꾸미기</a></span> 에서 개별 디자인이 가능합니다.<br>* 공지사항과 컨텐츠, 설문 내용이 없으면 사용자화면에서 자동으로 출력되지 않습니다.</td>
						</tr>
						</table>
                    </div>
					</td>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">카테고리 일반상품 진열수 <span>카테고리 페이지에 보여질 상품 리스트의 표시개수를 설정하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><center><img src="images/shop_saleout_img3.gif" border="0"></center></th>
                    <td class="td_con1"  >
                    	<div class="table_none">
                        <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="560" class="td_con1">카테고리 일반상품 진열수 일괄 지정 : <select name=prlist_num class="select" style="width:40px">
    <?php
                for ($i=8;$i<=50;$i++) {
                    if ($i==$prlist_num) {
    ?>
                    <option value="<?php echo $i ?>" selected><?php echo $i ?>
    <?php
                    } else {
    ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?>
    <?php
                    }
                }
    ?>
                            </select>개
                            * 등록된 상품수에 비해 진열수를 적게 입력한 경우 자동으로 페이지가 추가됩니다. 1[2][3][4]<br>
                            * 모든 카테고리에 일괄 적용됩니다.<br>
                            * 카테고리의 신규, 인기, 추천 상품의 진열수는 카테고리마다 개별 지정할 수 있습니다.</td>
                        </tr>
                        </table>
                        </div>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td width="100%">
               	<!-- 매뉴얼 -->
                <div class="sub_manual_wrap">
	                <div class="title"><p>매뉴얼</p></div>
	                    <dl>
    	                    <dt><span>배치순서 변경</span></dt>
	                        <dd><img src="images/shop_mainproduct_img5.gif" border="0" align="left">
                                - <a href="javascript:parent.topframe.GoMenu(2,'design_eachmain.php');"><span class="font_blue">디자인관리 > 개별디자인 - 메인 및 상하단 > 메인본문 꾸미기</span></a> 에서 직접 디자인 변경 및 배치도 변경할 수 있습니다. <br /><br /><br /><br /><br /><br /><br /><br /><br />
                            </dd>
                        </dl>
                     </div>
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
