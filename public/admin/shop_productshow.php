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
$proption_price=$_POST["proption_price"];
$primg_minisize=$_POST["primg_minisize"];
$soldview=$_POST["soldview"];
$codeyes=$_POST["codeyes"];
$quicktools=$_POST["quicktools"];
$bottomtools=$_POST["bottomtools"];

$selfcodev=$_POST["selfcodev"];
$selfcodel=$_POST["selfcodel"];
$selfcoden=$_POST["selfcoden"];

$selfcodefront=$_POST["selfcodefront"];
$selfcodeback=$_POST["selfcodeback"];

$dollarprice=$_POST["dollarprice"];
$prevdollar=$_POST["prevdollar"];
$nextdollar=$_POST["nextdollar"];

$memprice=$_POST["memprice"];
$priceicon=$_POST["priceicon"];

$image=$_FILES["image"];
$pricefile=$_FILES["pricefile"];

$proption_size=$_POST["proption_size"];


$primg_minisize2=250;

$imagepath = $Dir.DataDir."shopimages/etc/";

if ($type=="del" && file_exists($imagepath."soldout.gif")) {
	$img_url=$imagepath."soldout.gif";
	unlink($img_url);
	$onload = "<script> alert('상품 품절 이미지 삭제가 완료되었습니다.'); </script>";
}

if ($type=="icondel" && file_exists($imagepath."priceicon.gif")) {
	$img_url = $imagepath."priceicon.gif";
	unlink($img_url);
	$sqld="SELECT etctype FROM tblshopinfo ";
	$resultd=pmysql_query($sqld,get_db_conn());
	$rowd=pmysql_fetch_object($resultd);
	$rowd->etctype=str_replace("MEMIMG=Y","",$rowd->etctype);
	pmysql_query("UPDATE tblshopinfo SET etctype = '{$rowd->etctype}' ",get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload = "<script> alert('상품가격 아이콘 삭제가 완료되었습니다.'); </script>";
}

if ($type=="up") {
	$ext = strtolower(pathinfo($image['name'],PATHINFO_EXTENSION));
	if (ord($image['tmp_name']) && $ext=="gif" && $image['size']<=153600) {
		$image_name="soldout.gif";
		move_uploaded_file($image['tmp_name'],"$imagepath$image_name");
		chmod("$imagepath$image_name",0664);
	} else {

		if (ord($image['name'])) $msg="올리실 이미지는 150KB 이하의 gif파일만 됩니다.";

	}

	$etctype=$etcvalue;
	if (ord($codeyes)){
		$etctype.= "CODEYES={$codeyes}";
	}
	if ($soldview=="Y") {
		$etctype.="MAINSOLD=Y";
	}
	if ($imgsero=="Y") {
		$etctype.="IMGSERO=Y";
	}

	if($bfont=="Y" || ord($fontcolor)){
		$etctype.="SELL={$bfont},{$fontcolor}";
	}
	if((ord($prevdollar) || ord($nextdollar)) && $dollarprice>0){
		$etctype.="DOLLAR={$prevdollar},{$dollarprice},{$nextdollar}";
	}
	if($memprice=="Y"){
		$etctype.="MEM=Y";
		$ext = strtolower(pathinfo($pricefile['name'],PATHINFO_EXTENSION));

		if (ord($pricefile['tmp_name']) && $ext=="gif" && $pricefile['size'] <= 153600) {

			move_uploaded_file($pricefile['tmp_name'],$imagepath."priceicon.gif");
			chmod($imagepath."priceicon.gif",0664);
			$etctype.="MEMIMG=Y";
		}else if($priceicon=='Y'){
			$etctype.="MEMIMG=Y";
		} else if (ord($priceicon)) {
			$etctype.="MEMIMG={$priceicon}";
		} else if (file_exists($imagepath."priceicon.gif")) {
			$etctype.="MEMIMG=Y";
		}
	}
	
	if ($quicktools=="Y") {
		$etctype.="QUICKTOOLS=Y";
	}

	if ($bottomtools=="Y") {
		$etctype.="BOTTOMTOOLS=Y";
	}

	if ($selfcodev=="Y") {
		if($selfcodel=="Y") {
			if($selfcoden=="Y") {
				$etctype.="SELFCODEVIEW=Y";
			} else {
				$etctype.="SELFCODEVIEW=Z";
			}
		} else {
			if($selfcoden=="Y") {
				$etctype.="SELFCODEVIEW=N";
			} else {
				$etctype.="SELFCODEVIEW=M";
			}
		}
	}
	
	if(ord($selfcodefront)) {
		$etctype.="SELFCODEF={$selfcodefront}";
	}

	if(ord($selfcodeback)) {
		$etctype.="SELFCODEB={$selfcodeback}";
	}

	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "proption_price		= '{$proption_price}', ";
	$sql.= "primg_minisize		= '{$primg_minisize}', ";
	$sql.= "proption_size		= '{$proption_size}', ";
	$sql.= "etctype				= '{$etctype}' ";
	$result = pmysql_query($sql,get_db_conn());

	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('정보 수정이 완료되었습니다. $msg'); }</script>";
}

$sql = "SELECT * FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {

	$proption_price = $row->proption_price;
	$primg_minisize = $row->primg_minisize;
	$proption_size = $row->proption_size;
	if($proption_size<=0) $checkopt="F";
	else $checkopt="S";

	if (ord($row->etctype)) {
		$etctemp = explode("",$row->etctype);
		$cnt = count($etctemp);
		$etcvalue="";
		for ($i=0;$i<$cnt;$i++) {
			if (strpos($etctemp[$i],"CODEYES=")===0) $codeyes=substr($etctemp[$i],8);
			else if (strpos($etctemp[$i],"SELL=")===0) $sellprice=substr($etctemp[$i],5);
			else if (strpos($etctemp[$i],"MEM=")===0) $memprice=substr($etctemp[$i],4);
			else if (strpos($etctemp[$i],"DOLLAR=")===0) $ardollar=substr($etctemp[$i],7);
			else if (strpos($etctemp[$i],"MEMIMG=")===0) $memimg=substr($etctemp[$i],7);
			else if (strpos($etctemp[$i],"SELFCODEVIEW=")===0) $selfcode=substr($etctemp[$i],13);
			else if (strpos($etctemp[$i],"SELFCODEF=")===0) $selfcodefront=substr($etctemp[$i],10);
			else if (strpos($etctemp[$i],"SELFCODEB=")===0) $selfcodeback=substr($etctemp[$i],10);
			else if (strpos($etctemp[$i],"MAINSOLD=Y")===0) $soldview="Y";
			else if (strpos($etctemp[$i],"IMGSERO=Y")===0) $imgsero="Y";
			else if (strpos($etctemp[$i],"QUICKTOOLS=Y")===0) $quicktools="Y";
			else if (strpos($etctemp[$i],"BOTTOMTOOLS=Y")===0) $bottomtools="Y";
			else if (strpos($etctemp[$i],"MEMIMG=Y")===0) $memimg="Y";
			else if(ord($etctemp[$i])) $etcvalue.=$etctemp[$i]."";
		}
	}
	$soldview=($soldview!="Y"?"N":"Y");
	$quicktools=($quicktools!="Y"?"N":"Y");
	$bottomtools=($bottomtools!="Y"?"N":"Y");

	if(ord($selfcode) && ($selfcode=="Y" || $selfcode=="Z" || $selfcode=="N" || $selfcode=="M")) { 
		$selfcodev="Y";
		if($selfcode=="Y" || $selfcode=="Z") {
			$selfcodel="Y";
		} else {
			$selfcodel="N";
		}

		if($selfcode=="Y" || $selfcode=="N") {
			$selfcoden="Y";
		} else {
			$selfcoden="N";
		}
	} else {
		$selfcodev="N";
		$selfcodel="N";
		$selfcoden="N";
	}
}
pmysql_free_result($result);

$selltype=explode(",",$sellprice);
if(ord($selltype[0])) $bfont="Y";
if(ord($selltype[1])) $fontcolor=$selltype[1];

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function CheckForm(type) {
	if(type=="icondel"){
		if (!confirm("등록된 상품 아이콘을 삭제하시겠습니까?")) {
			return;
		}
	}
	form1.type.value=type;
	form1.submit();
}

function selcolor(){
	fontcolor = document.form1.fontcolor.value.substring(1);
	var newcolor = showModalDialog("color.php?color="+fontcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		document.form1.fontcolor.value='#'+newcolor;
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>상품 진열 기타 설정</span></p></div></div>
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
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 상품 진열 관련 설정을 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=etcvalue value="<?=$etcvalue?>">
			<tr>
				<td>

				<div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<th><span>하단 폴로메뉴 출력 설정</span></th>
							<td>
								<input type=radio name="bottomtools" value="N" id="idx_bottomtools2" <?=($bottomtools=="N")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bottomtools2">하단 폴로메뉴 출력함<font color="#000000">(권장)</font></label>
								<input type=radio name="bottomtools" value="Y" id="idx_bottomtools1" <?=($bottomtools=="Y")?"checked":""?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bottomtools1">하단 폴로메뉴 출력안함</label>
							</td>
						</tr>
					</table>
				</div>

				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1. 하단 폴로메뉴의 출력 여부를 설정할 수 있습니다 .</li>
						<li>2. "<b>하단 폴로메뉴 출력함(권장)</b>" 설정시 출력 형태는 아래와 같습니다.</li>
						<li class="ptb_5"><img src="images/bottom_tools_col.gif" border="0" style="border:1px #C4C4C4 solid;"></li>
					</ul>
				</div>

				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <br />
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<th><span>상품 퀵툴스 출력 설정</span></th>
							<td>
								<input type=radio name="quicktools" value="N" id="idx_quicktools2" <?=($quicktools=="N")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_quicktools2">퀵툴스 출력함<font color="#000000">(권장)</font></label>
								<input type=radio name="quicktools" value="Y" id="idx_quicktools1" <?=($quicktools=="Y")?"checked":""?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_quicktools1">퀵툴스 출력안함</label>
							</td>
						</tr>
					</table>
				</div>

				<!-- 도움말 -->
				<div class="help_info01_wrap">
					<ul>
						<li>1. 상품들의 목록 출력시에 퀵툴스 출력 여부를 설정합니다.</li>
						<li>2. "<b>퀵툴스 출력함(권장)</b>" 설정시 출력 형태는 아래와 같습니다.</li>
						<li class="ptb_5">
							<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>
								<tr>
									<td height="5" colspan=3></td>
								</tr>
								<tr align="center">
									<td><b>이미지A형 타입</b></td>
									<td style="padding-left:20px;"></td>
									<td><b>이미지B형, 리스트형 타입</b></td>
								</tr>
								<tr>
									<td height="5" colspan=3></td>
								</tr>
								<tr>
									<td><img src="images/quick_tools_col.gif" border="0"></td>
									<td style="padding-left:20px;"></td>
									<td valign=top><img src="images/quick_tools_row.gif" border="0"></td>
								</tr>
							</table>
						</li>
					</ul>
				</div>				
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<div class="title_depth3_sub">품절 표시 아이콘</div>
					</td>
			</tr>
            <tr>
				<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 메인과 카테고리 상품리스트의 품절표시 설정입니다. 이미지가 없는 경우에는 (품절)로 표시 됩니다.</li>
                            <li>2) 상품리스트와 상품상세 설명에 품절표시 설정</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>	
				<tr>
					<td>
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>품절 표시 설정</span></th>
						<TD class="td_con1"  ><input type=radio id="idx_soldview1" name=soldview value="Y" <?=($soldview=="Y")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_soldview1>품절 표시함(O)</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_soldview2" name="soldview" value="N" <?=($soldview=="N")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_soldview2>품절 표시 안함(X)</label></TD>
					</TR>
					<TR>
						<th><span>품절 아이콘 업로드</span></th>
						<TD class="td_con1"  ><input type=file name=image <?php if (file_exists($imagepath."soldout.gif")) { ?> > <a href="javascript:CheckForm('del');"><img src="images/icon_del1.gif" border="0" align=absmiddle></a> <br><img src="<?=$imagepath?>soldout.gif" border=0 align=absmiddle><?php }else {?> ><?php }?><br><span class="font_orange">* 등록 가능 이미지는 150KB 이하의 GIF(gif)파일만 가능합니다.</span></TD>
					</TR>
					</TABLE>
                    </div>
					</td>
				</tr>
				<!--
				<tr>
					<td height="30">&nbsp;</td>
				</tr>
				<tr>
					<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/shop_mainproduct_stitle5.gif" WIDTH="192" HEIGHT=31 ALT=""></TD>
						<TD width="100%" background="images/shop_basicinfo_stitle_bg.gif">&nbsp;</TD>
						<TD><IMG SRC="images/shop_basicinfo_stitle_end.gif" WIDTH=10 HEIGHT=31 ALT=""></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td height=3></td>
				</tr>
				<tr>
					<td>
					<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
					<TR>
						<TD><IMG SRC="images/distribute_01.gif"></TD>
						<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
						<TD><IMG SRC="images/distribute_03.gif"></TD>
					</TR>
					<TR>
						<TD background="images/distribute_04.gif"></TD>
						<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
						<TD width="100%" class="notice_blue">1) 메인상품의 판매가격 색상을 설정할 수 있습니다.<br>
						2) <a href="javascript:parent.topframe.GoMenu(2,'design_easycss.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 텍스트 속성 관리</font></a> 에서 메인, 상품리스트, 상품상세 등의 폰트 스타일을 추가로 설정할 수 있습니다.<br>
						3) Easy 디자인을 사용할 경우 Easy디자인 설정값으로 적용됩니다.
						</TD>
						<TD background="images/distribute_07.gif"></TD>
					</TR>
					<TR>
						<TD><IMG SRC="images/distribute_08.gif"></TD>
						<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
						<TD><IMG SRC="images/distribute_10.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td height=3></td>
				</tr>
				<tr>
					<td>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					<TR>
						<TD width="146" class="table_cell"><img src="images/icon_point2.gif" width="8" height="11" border="0">상품가격색상 설정</TD>
						<TD class="td_con1"  >색상 : <input type=text name=fontcolor size=10 maxlength=7 value="<?=$fontcolor?>" class="input"> <a href="JavaScript:selcolor()"><img src="images/btn_color.gif" width="111" height="16" border="0" hspace="1"></a>  &nbsp;&nbsp;&nbsp;&nbsp;두께 : <input type=checkbox id="idx_bfont1" name=bfont value="Y" <?=($bfont=="Y"?"checked":"")?>> <label style='cursor:hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_bfont1>굵게</label></TD>
					</TR>
					<TR>
						<TD colspan=2 background="images/table_top_line.gif"></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				-->
				<tr>
					<td>
						<!-- 소제목 -->
						<div class="title_depth3_sub">진열코드 출력 설정<span>상품 등록/수정시 입력한 진열코드의 출력 및 위치를 설정할 수 있습니다.</span></div>
					</td>
				</tr>
				<tr>
					<td>
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>진열코드 출력 여부</span></th>
						<TD class="td_con1"  ><input type=radio id="idx_selfcodev1" name=selfcodev value="Y" <?=($selfcodev=="Y")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_selfcodev1>진열코드 출력함(O)</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_selfcodev2" name="selfcodev" value="N" <?=($selfcodev!="Y")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_selfcodev2>진열코드 출력 안함(X)</label><br>
						<span class="font_orange">* 진열코드 출력함 사용시 상품 목록에서 출력됩니다.</span></TD>
					</TR>
					<TR>
						<th><span>진열코드 출력 위치</span></th>
						<TD class="td_con1"  ><input type=radio id="idx_selfcodel1" name=selfcodel value="Y" <?=($selfcodel=="Y")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_selfcodel1>상품명 앞</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_selfcodel2" name="selfcodel" value="N" <?=($selfcodel!="Y")?"checked":""?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_selfcodel2>상품명 뒤</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type=checkbox id="idx_selfcoden1" name=selfcoden value="Y" <?=$selfcoden=="Y"?checked:""?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_selfcoden1>줄바뀜</label>
						</TD>
					</TR>
					<TR>
						<th><span>진열코드 전후 문자</span></th>
						<TD class="td_con1"  >진열코드 전 문자 : <input type=text name=selfcodefront value="<?=$selfcodefront?>" size=10 maxlength=12 onKeyDown="chkFieldMaxLen(12)" class="input">&nbsp;&nbsp;<span class="font_orange">* Html은 사용 불가이며 입력 최대는 12Byte(영문12자, 한글6자)</span><br>
						진열코드 후 문자 : <input type=text name=selfcodeback value="<?=$selfcodeback?>" size=10 maxlength=8 onKeyDown="chkFieldMaxLen(8)" class="input">&nbsp;&nbsp;<span class="font_orange">* Html은 사용 불가이며 입력 최대는 8Byte(영문8자, 한글4자)</span><br>
						<span class="font_orange">* 진열코드 전후 문자는 진열코드 출력시 추가로 앞뒤에 문자를 출력을 원할 경우 사용하세요.</span></TD>
					</TR>
					</TABLE>
					</div>
					</td>
				</tr>
				<tr>
					<td>
						<!-- 소제목 -->
						<div class="title_depth3_sub">상품 상세 정보 해외화폐 가격 설정<span>상품 상세페이지에 통화표시를 하시려면 환율 및 기호를 설정하시고 [상품 상세조건 노출관리]에서 추가하시면 됩니다.</span></div>
					</td>
				</tr>
<?php
				$ardollar=explode(",",$ardollar);
				$dollar1=$ardollar[0];
				$dollarprice=$ardollar[1];
				$dollar2=$ardollar[2];
?>
				<tr>
					<td>
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>환율 기본값</span></th>
						<TD class="td_con1"  ><input type=text name=dollarprice value="<?=$dollarprice?>" size=10 maxlength=5 class="input"> 원</TD>
					</TR>
					<TR>
						<th><span>통화 기호</span></th>
						<TD class="td_con1"  >
<?php
			$prevdollar=array("","$","￡","￥","EUR","USD","US $","Can$");
			$nextdollar=array("","달러","프랑","엔","유로","EUR","USD","Can$");
			$dollarcnt = count($prevdollar);
			echo "<select name=prevdollar class=\"select\">";
			for($i=0;$i<$dollarcnt;$i++){
				echo "<option value=\"{$prevdollar[$i]}\"";
				if($dollar1 ==$prevdollar[$i]) echo " selected";
				if($i==0) $prevdollar[$i]="표시하지 않음";
				echo ">".$prevdollar[$i];
			}
			echo "</select> <!--1,234.56--> ";
			echo "<select name=nextdollar class=\"select\">";
			for($i=0;$i<$dollarcnt;$i++){
				echo "<option value=\"{$nextdollar[$i]}\"";
				if($dollar2 ==$nextdollar[$i]) echo " selected";
				if($i==0) $nextdollar[$i]="표시하지 않음";
				echo ">".$nextdollar[$i];
			}
			echo "</select>";
?>
						</TD>
					</TR>
					</TABLE>
                    </div>
					</td>
				</tr>
				<tr>
					<td>
					<div class="title_depth3_sub">상품가격 노출여부</div>
					</td>
				</tr>
                <tr>
                    <td style="padding-top:3pt; padding-bottom:3pt;">                    
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 회원에게만 가격 노출시에 회원공개 문자 또는 아이콘으로 표시 할 수 있습니다.</li>
                                <li>2) 등록된 이미지의 삭제는 이미지를 클릭하시면 삭제가 됩니다.</li>
                            </ul>
                        </div>                    
                    </td>
                </tr>				
				<tr>
					<td>
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>상품가격 노출여부</span></th>
						<TD class="td_con1"  ><input type=radio id="idx_memprice1" name=memprice value="N" <?=($memprice!="Y"?"checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memprice1>회원/비회원 모두 노출</label> &nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_memprice2" name=memprice value="Y" <?=($memprice=="Y"?"checked":"")?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_memprice2>회원에게만 상품 가격 노출</label>(아이콘 미사용시 <span class="font_blue">회원전용</span> 으로 표시)</TD>
					</TR>
					<TR>
						<th><span>기본 아이콘 사용</span></th>
						<TD class="td_con1"  >
                        <div class="table_none">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR height=5>
							<TD colSpan=7></TD>
						</TR>
						<TR>
<?php
			for ($i=1 ; $i <= 9 ; $i++) { 
				if ($i%5==1) echo "<tr>";
?>
				<td>&nbsp;<input type=radio id="idx_priceicon<?=$i?>" name=priceicon value="<?=$i?>" <?php if ($memimg=="$i") echo "checked"?>> <img src="<?=$Dir?>images/common/priceicon<?=$i?>.gif" align=absmiddle></td>
<?php
				if ($i%5==0) echo "</tr>\n";
			}
?>
							<td>&nbsp;<input type=radio id="idx_priceiconN" name=priceicon value="N" <?php if ($memimg=="N") echo "checked"?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_priceiconN>표시안함</label></td>
						</TR>
						</TABLE>
                        </div>
						</TD>
					</TR>												
					<TR>
						<th><span>직접 등록한 아이콘사용</span></th>
						<TD class="td_con1"  ><input type=file name=pricefile >
<?php
			if (file_exists($imagepath."priceicon.gif")){
			echo "<a href=\"javascript:CheckForm('icondel');\">
			<img src=\"images/icon_del1.gif\" border=\"0\" align=absmiddle>
			</a>
			<br><img src='{$imagepath}priceicon.gif' border=0 align=absmiddle>
			\n";
			
?>
			<input type="radio" name="priceicon" value="Y" <?if($memimg=='Y'){ echo "checked"; }?>/>사용
<?
			}
?>
						
						<br><span class="font_orange">* 등록 가능 이미지는 150KB 이하의 GIF(gif)파일만 가능합니다.</span>
						</TD>
					</TR>
					</TABLE>
                    </div>
					</td>
				</tr>
				<tr>
					<td>
						<!-- 소제목 -->
						<div class="title_depth3_sub">카테고리 표시 설정</div>
					</td>
				</tr>
				<tr>
					<td>
					<table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #b9b9b9">
					<tr>
						<td width="100%" bgcolor="#0099CC">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td>
							<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
                                <TR>
                                    <TD width="50%"><div class="point_title"><input type=radio id="idx_codeyes1" name=codeyes value="Y" class="radio" <?php if ($codeyes!="N") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_codeyes1>항상 따라다님</label></div></TD>
                                    <TD width="50%"><div class="point_title"><input type=radio id="idx_codeyes2" name=codeyes value="N" class="radio" <?php if ($codeyes=="N") echo "checked"; ?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_codeyes2>표시하지 않음</label></div></TD>
                                </TR>
							<TR>
								<TD style="padding-top:10pt; padding-bottom:10pt;"><p align="center"><img src="images/shop_saleout_img1.gif" border="0" class="imgline"></TD>
								<TD class="td_con1"><p align="center"><img src="images/shop_saleout_img2.gif" border="0" class="imgline"></TD>
							</TR>
							</TABLE>
							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
						<div class="title_depth3_sub">작은 이미지 사이즈 설정</div>
					</td>
				</tr>
                <tr>
                    <td style="padding-top:3pt; padding-bottom:3pt;">                    
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 메인/카테고리의 상품리스트 이미지 사이즈를 설정 할 수 있습니다.</li>
                                <li>2) 직사각형의 이미지를 사용할 경우 세로사이즈에 맞춰 가로사이즈는 자동비율로 변경됩니다.</li>
                            </ul>
                        </div>                    
                    </td>
                </tr>	
				<tr>
					<td>
                    <div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>상품 이미지 최소 사이즈</span></th>
						<TD class="td_con1"  ><select name=primg_minisize class="select">
<?php
			for ($i=80;$i<=240;$i+=10) {
				echo "<option value=\"$i\"";
				if ($i==$primg_minisize) echo " selected";
				echo ">$i";
			}
			if($primg_minisize>200) {
				echo "<option value=\"$primg_minisize\" selected>$primg_minisize";
			}
?>
						</select>픽셀 <input type=checkbox id="idx_imgsero1" name=imgsero value="Y" <?=$imgsero=="Y"?checked:""?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_imgsero1>세로사이즈 <b><?=$primg_minisize2?>픽셀</b>까지 허용</label></TD>
					</TR>
					</TABLE>
                    </div>
					</td>
				</tr>
				<tr>
					<td>
						<!-- 소제목 -->
						<div class="title_depth3_sub">상품옵션 출력폼 사이즈 조정</div>
					</td>
				</tr>
				<tr>
					<td>
                    <table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #b9b9b9;">
					<tr>
						<td width="100%" bgcolor="#0099CC">
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td>
							<TABLE cellSpacing=0 cellPadding=0 border=0 width="100%">
                                
                                <TR>
                                    <TD width="50%" ><div class="point_title"><input type=radio id="idx_checkopt1" name=checkopt value="F" <?php if($checkopt=="F") echo "checked"?> onClick="document.form1.proption_size.disabled=true;document.form1.proption_size.style.background='#EFEFEF';document.form1.proption_size.value='';"><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkopt1>자동으로 조절합니다.</label></div></TD>
                                    <TD width="50%"><div class="point_title">		
									<input type=radio id="idx_checkopt2" name=checkopt value="S" <?php if($checkopt=="S") echo "checked"?> onClick="document.form1.proption_size.disabled=false;document.form1.proption_size.style.background='white';">
									<input name=proption_size size=3 maxlength=3 value="<?=$proption_size?>"> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_checkopt2>픽셀로 고정 시킵니다. (권장 230픽셀)</label></div>
									</TD>
                                </TR>
                                <TR>
                                    <TD style="padding-top:10pt; padding-bottom:10pt;"><p align="center"><IMG src="images/mainproduct_img8_1.gif" align=absMiddle></TD>
                                    <TD class="td_con1"><p align="center"><IMG src="images/mainproduct_img8_2.gif" align=absMiddle></TD>
                                </TR>
							</TABLE>
							</td>
						</tr>	
						</table>
						</td>
					</tr>
					</table>
                    
					</td>
				</tr>
				<tr>
					<td>	
						<div class="title_depth3_sub">상품 옵션가격 표시 방법</div>
					</td>
				</tr>
                <tr>
                    <td style="padding-top:3pt; padding-bottom:3pt;">                    
                        <!-- 도움말 -->
                        <div class="help_info01_wrap">
                            <ul>
                                <li>1) 상품 가격이 옵션별로 틀릴경우 나오는 메세지를 변경할 수 있습니다.</li>
                                <li>2) 상품 가격을 표현하고 싶을경우 <B>[PRICE]</B>로 입력하시기 바랍니다. >3) 입력이 안되었을 경우 기본으로 셋팅됩니다.</li>
                            </ul>
                        </div>                    
                    </td>
                </tr>					
                <tr>
					<td>
					<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<th><span>대체 표시 문구 입력</span></th>
						<TD class="td_con1"  ><input type="text" name="proption_price" size=50 maxlength=50 value="<?=$proption_price?>" onKeyDown="chkFieldMaxLen('100')" class="input" style="width:100%"></TD>
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
                                <li>1)상품가격 출력부분을 변경할 수 있습니다.</li>
                                <li>2)<span class="font_orange"><b>상품가격 표시 + 문구 삽입 = [PRICE]입력 + 문구입력</b><br></span></li>
                                <li>[상품 옵션가 표시 설정 입력예제]</li>
                                <li>① 미입력(기본-가격만표시) <INPUT class="input" style="width:100% readonly"> → 판매가격 89,000원</li>
                                <li>② [PRICE]원+문구입력 <INPUT class="input" style="width:100% readonly" value="[PRICE]원 초저가 행사"> → 판매가격 89,000 초저가 행사</li>
                                <li>③ 문구입력만 입력(가격미표시) <INPUT class="input" style="width:100% readonly" value="초저가 행사 "> → 판매가격 초저가 행사</li>
                            </ul>
                        </div>
                        
                    </td>
				</tr>
				<tr>
					<td height=10></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('up');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
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
