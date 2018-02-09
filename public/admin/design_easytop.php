<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-7";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/etc/";

$type=$_POST["type"];
if($type=="update") {
	$menu_list=substr($_POST["menu_list"],1);
	$top_xsize=(int)$_POST["top_xsize"];
	$top_ysize=(int)$_POST["top_ysize"];
	$menu_align=$_POST["menu_align"];
	$logo_loc=$_POST["logo_loc"];

	$isbackground=$_POST["isbackground"];
	if($isbackground!="Y") $isbackground="N";

	$link1=$_POST["menulink16"];
	$link2=$_POST["menulink17"];
	$link3=$_POST["menulink18"];
	$link4=$_POST["menulink19"];
	$link5=$_POST["menulink20"];

	$okdesign=$_POST["okdesign"];

	if(ord($menu_list)==0) $menu_list="1,2,3,4,5,6";
	if($top_xsize==0) $top_xsize=900;
	if($top_ysize==0) $top_ysize=120;
	if($menu_align!="L" && $menu_align!="C" && $menu_align!="R") $menu_align="L";
	if($logo_loc!="T" && $logo_loc!="Y") $logo_loc="T";

	$shopimg=array(1=>&$_FILES["menuimg1"],2=>&$_FILES["menuimg2"],3=>&$_FILES["menuimg3"],4=>&$_FILES["menuimg4"],5=>&$_FILES["menuimg5"],6=>&$_FILES["menuimg6"],7=>&$_FILES["menuimg7"],8=>&$_FILES["menuimg8"],9=>&$_FILES["menuimg9"],10=>&$_FILES["menuimg10"],11=>&$_FILES["menuimg11"],12=>&$_FILES["menuimg12"],13=>&$_FILES["menuimg13"],14=>&$_FILES["menuimg14"],15=>&$_FILES["menuimg15"],16=>&$_FILES["menuimg16"],17=>&$_FILES["menuimg17"],18=>&$_FILES["menuimg18"],19=>&$_FILES["menuimg19"],20=>&$_FILES["menuimg20"],21=>&$_FILES["background"],22=>&$_FILES["logoimg"]);

	$vshopimg=array(1=>&$_POST["vmenuimg1"],2=>&$_POST["vmenuimg2"],3=>&$_POST["vmenuimg3"],4=>&$_POST["vmenuimg4"],5=>&$_POST["vmenuimg5"],6=>&$_POST["vmenuimg6"],7=>&$_POST["vmenuimg7"],8=>&$_POST["vmenuimg8"],9=>&$_POST["vmenuimg9"],10=>&$_POST["vmenuimg10"],11=>&$_POST["vmenuimg11"],12=>&$_POST["vmenuimg12"],13=>&$_POST["vmenuimg13"],14=>&$_POST["vmenuimg14"],15=>&$_POST["vmenuimg15"],16=>&$_POST["vmenuimg16"],17=>&$_POST["vmenuimg17"],18=>&$_POST["vmenuimg18"],19=>&$_POST["vmenuimg19"],20=>&$_POST["vmenuimg20"],21=>&$isbackground,22=>&$_POST["islogoimg"]);

	$display=array(1=>&$_POST["display1"],2=>&$_POST["display2"],3=>&$_POST["display3"],4=>&$_POST["display4"],5=>&$_POST["display5"],6=>&$_POST["display6"],7=>&$_POST["display7"],8=>&$_POST["display8"],9=>&$_POST["display9"],10=>&$_POST["display10"],11=>&$_POST["display11"],12=>&$_POST["display12"],13=>&$_POST["display13"],14=>&$_POST["display14"],15=>&$_POST["display15"],16=>&$_POST["display16"],17=>&$_POST["display17"],18=>&$_POST["display18"],19=>&$_POST["display19"],20=>&$_POST["display20"],21=>&$display21,22=>&$display22);

	if(ord($shopimg[21]['name']) && file_exists($shopimg[21]['tmp_name'])) {
		$display21="21";
		$isbackground="Y";
	}
	if(ord($shopimg[22]['name']) && file_exists($shopimg[22]['tmp_name'])) {
		$display22="22";
	}


	$iserror=false;
	for($i=1;$i<=count($shopimg);$i++) {
		if($display[$i]==$i) {
			if(ord($shopimg[$i]['name']) && file_exists($shopimg[$i]['tmp_name'])) {
				$ext = strtolower(pathinfo($shopimg[$i]['name'],PATHINFO_EXTENSION));
				if($ext=="gif") {
					$shopimg[$i]['name']="easytopmenu{$i}.gif";
					if($i==21) {
						$shopimg[$i]['name']="easytopbg.gif";
					} else if($i==22) {
						$shopimg[$i]['name']="logo.gif";
					}
					move_uploaded_file($shopimg[$i]['tmp_name'],$imagepath.$shopimg[$i]['name']);
					chmod($imagepath.$shopimg[$i]['name'],0664);
				} else {
					$iserror=true;
				}
			} else if(ord($vshopimg[$i])==0) {
				$iserror=true;
			}
		}
	}

	if($iserror) {
		alert_go('이미지 등록이 잘못되었습니다.');
	}

	$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$cnt=(int)$row->cnt;
	pmysql_free_result($result);
	if($cnt==0) {
		$sql = "INSERT INTO tbldesign DEFAULT VALUES";		
		pmysql_query($sql,get_db_conn());
	}
	$sql = "UPDATE tbldesign SET 
	top_set		= 'Y', 
	top_xsize	= '{$top_xsize}', 
	top_ysize	= '{$top_ysize}', 
	menu_align	= '{$menu_align}', 
	background	= '{$isbackground}', 
	logo_loc	= '{$logo_loc}', 
	menu_list	= '{$menu_list}', 
	link1		= '{$link1}', 
	link2		= '{$link2}', 
	link3		= '{$link3}', 
	link4		= '{$link4}', 
	link5		= '{$link5}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tbldesign.cache");

	if($okdesign=="Y") {
		$sql = "UPDATE tblshopinfo SET top_type='tope' ";
		pmysql_query($sql,get_db_conn());
		DeleteCache("tblshopinfo.cache");

		$_shopdata->top_type="tope";
	}
	$onload = "<script>window.onload=function(){ alert('정보 수정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT * FROM tbldesign ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
if($row->top_set=="Y") {
	$top_xsize=$row->top_xsize;
	$top_ysize=$row->top_ysize;
	$menu_align=$row->menu_align;
	$background=$row->background;
	$logo_loc=$row->logo_loc;
	$menu_list=$row->menu_list;
	$link1=$row->link1;
	$link2=$row->link2;
	$link3=$row->link3;
	$link4=$row->link4;
	$link5=$row->link5;
} else {
	$menu_align="L";
	$logo_loc="T";
	$menu_list="1,2,3,4,5,6";
}
pmysql_free_result($result);
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(document.form1.top_xsize.value.length==0) {
		alert("상단메뉴 가로 사이즈를 입력하세요.");
		document.form1.top_xsize.focus();
		return;
	}
	if(!IsNumeric(document.form1.top_xsize.value)) {
		alert("상단메뉴 가로 사이즈는 숫자만 입력 가능합니다.");
		document.form1.top_xsize.focus();
		return;
	}
	if(document.form1.top_ysize.value.length==0) {
		alert("상단메뉴 세로 사이즈를 입력하세요.");
		document.form1.top_ysize.focus();
		return;
	}
	if(!IsNumeric(document.form1.top_ysize.value)) {
		alert("상단메뉴 세로 사이즈는 숫자만 입력 가능합니다.");
		document.form1.top_ysize.focus();
		return;
	}
	if(document.form1.logoimg.value.length==0 && document.form1.islogoimg.value.length==0) {
		alert("쇼핑몰 로고를 등록하세요.");
		return;
	}
	document.form1.menu_list.value="";
	for(i=1;i<=20;i++) {
		if(document.form1["display"+i].checked) {
			if(document.form1["menuimg"+i].value.length==0 && document.form1["vmenuimg"+i].value.length==0) {
				alert("해당 메뉴의 이미지를 등록하셔야 합니다.");
				document.form1["menuimg"+i].focus();
				return;
				break;
			}
			if(document.form1["menulink"+i].value.length==0) {
				alert("해당 메뉴의 링크를 입력하세요.");
				document.form1["menulink"+i].focus();
				return;
				break;
			}
			document.form1.menu_list.value+=","+document.form1["display"+i].value;
		}
	}
	if(document.form1.menu_list.value.length==0) {
		alert("메뉴는 하나 이상 노출하셔야 합니다.");
		return;
	}
	document.form1.type.value="update";
	document.form1.submit();
}

function menu_sort() {
	window.open("design_easytoppopup.php","easytop","height=100,width=200,toolbar=no,menubar=no,scrollbars=no,status=no");
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; Easy디자인 관리 &gt;<span>Easy 상단 메뉴 관리</span></p></div></div>
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
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">Easy 상단 메뉴 관리</div>
					<div class="title_depth3_sub"><span>쇼핑몰 상단 디자인을 직접 제작하신 이미지파일을 이용하여, 간단하게 디자인을 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) HTML을 몰라도 이미지만 등록하여 상단메뉴의 디자인이 변경 가능합니다.</li>
								<li>2) 기본메뉴의 링크수정 불가, 추가 메뉴의 링크는 등록/수정 가능합니다.</li>
								<li>3) Easy 디자인 해제 : 메인 템플릿 선택 또는 개별디자인 선택하면 됩니다.</li>
                                <li><IMG SRC="images/design_eachtop_img.gif" ALT="" align="baseline"></li>
							</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 상단디자인 미리보기</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=menu_list>
			<tr>
				<td style="PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 10px; PADDING-TOP: 10px" bgcolor="#EBEBEB"><div style="background-color:#fff"><iframe name=contents src="/<?=RootPath.MainDir?>tope.php" frameborder=no scrolling=auto style="width:100%; height: 100%;"></iframe></div></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 상단디자인 설정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>상단 사이즈 설정</span></th>
					<TD class="td_con1"><img src="images/icon_width.gif" border="0"> <input type=text name=top_xsize value="<?=$top_xsize?>" size=10 maxlength=3 onkeyup="strnumkeyup(this)" class="input">픽셀 &nbsp;&nbsp;<img src="images/icon_height.gif" border="0"> <input type=text name=top_ysize value="<?=$top_ysize?>" size=10 maxlength=3 onkeyup="strnumkeyup(this)" class="input">픽셀</TD>
				</TR>
				<TR>
					<th><span>상단메뉴 정렬 위치</span></th>
					<TD class="td_con1" width="600"><input type=radio name=menu_align value="L" <?php if($menu_align=="L")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">왼쪽 &nbsp; <input type=radio name=menu_align value="C" <?php if($menu_align=="C")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">가운데 &nbsp; <input type=radio name=menu_align value="R" <?php if($menu_align=="R")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;">오른쪽</TD>
				</TR>
				<TR>
					<th><span>상단 배경이미지</span></th>
					<TD width="596" class="td_con1">
						<input type=file name=background size=50><br>
					<!--
							<input type="text" id="fileName1" class="file_input_textbox w400" readonly="readonly">
							<div class="file_input_div">
							<input type="button" value="찾아보기" class="file_input_button" /> 
							<input type=file name=background size=45 class="file_input_hidden" onchange="javascript: document.getElementById('fileName1').value = this.value" >
							</div></div>
					-->
							<span class="font_orange">* 미등록시 흰색 배경, 배경이미지 사이즈가 작을 경우 반복되는 현상 발생됩니다.</span>
						<input type=hidden name=isbackground<?php if(file_exists($imagepath."easytopbg.gif"))echo" value=Y";?>>
					</TD>
				</TR>
				<TR>
					<th><span>상단쇼핑몰 로고 이미지 설정</span></th>
					<TD width="596" class="td_con1">
					<div class="table_none">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<TR>
						<TD style="PADDING-BOTTOM: 5px" width="60">로고등록 :<input type=hidden name=islogoimg<?php if(file_exists($imagepath."logo.gif"))echo" value=Y";?>></TD>
						<TD style="PADDING-BOTTOM: 5px" width="532" class="td_con1">
						<input type=file name=logoimg size=50><br>
						<!--
						<input type="text" id="fileName2" class="file_input_textbox w400" readonly="readonly"> 
						<div class="file_input_div">
						<input type="button" value="찾아보기" class="file_input_button" /> 
						<input type=file name=logoimg size=45 style=width:100% class="file_input_hidden" onchange="javascript: document.getElementById('fileName2').value = this.value" ><br />
						</div>
						-->
						</TD>
					</TR>
					<TR>
						<TD style="PADDING-TOP: 5px" width="60">노출위치 : </TD>
						<TD style="PADDING-TOP: 5px" width="532"><input type=radio name=logo_loc value="T" <?php if($logo_loc=="T")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"><img src="images/design_easytop_logoimg1.gif" border=0 align=absmiddle>&nbsp;<input type=radio name=logo_loc value="Y" <?php if($logo_loc=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"><img src="images/design_easytop_logoimg2.gif" border=0 align=absmiddle></TD>
					</TR>
					</TABLE>
					</div>
					</TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td><img src="images/design_easytop_imga21.gif" border="0">
				<img src="images/design_easytop_imga22.gif" border="0">
				<img src="images/design_easytop_imga23.gif" border="0"></td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 상단디자인 관리</div>
				</td>
			</tr>
			<tr>
				<td><div><A HREF="javascript:menu_sort()"><img src="images/btn_array.gif" border="0" vspace="5"></a></div></td>
			</tr>
			<tr>
				<td align="center">
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR align=center>
					<th>No</th>
					<th>상단 이미지명</th>
					<th>해당 이미지 등록(GIF 파일 - 150K 이하)</th>
					<th>링크 주소 (URL)</th>
					<th>노출여부</th>
				</TR>
<?php
			$menu_all=",1 ,2 ,3 ,4 ,5 ,6 ,7 ,8 ,9 ,10 ,11 ,12 ,13 ,14 ,15 ,16 ,17 ,18 ,19 ,20 ";
			$menu_all_name=array(1=>"메인페이지",2=>"회사소개",3=>"이용안내",4=>"회원가입/수정",5=>"장바구니",6=>"주문조회",7=>"로그인",8=>"로그아웃",9=>"회원탈퇴",10=>"마이페이지",11=>"고객센터",12=>"신규상품",13=>"인기상품",14=>"추천상품",15=>"특별상품",16=>"추가이미지1",17=>"추가이미지2",18=>"추가이미지3",19=>"추가이미지4",20=>"추가이미지5");
			$menu_all_url=array(1=>"[HOME]",2=>"[COMPANY]",3=>"[USEINFO]",4=>"[MEMBER]",5=>"[BASKET]",6=>"[ORDER]",7=>"[LOGIN]",8=>"[LOGOUT]",9=>"[MEMBEROUT]",10=>"[MYPAGE]",11=>"[EMAIL]",12=>"[PRODUCTNEW]",13=>"[PRODUCTBEST]",14=>"[PRODUCTHOT]",15=>"[PRODUCTSPECIAL]",16=>&$link1,17=>&$link2,18=>&$link3,19=>&$link4,20=>&$link5);

			$arr_menu_list=explode(",",$menu_list);
			$j=0;
			for($i=0;$i<count($arr_menu_list);$i++) {
				$j++;
				$menu_all=str_replace(",{$arr_menu_list[$i]} ","",$menu_all);
				echo "<tr>\n";
				echo "	<td><p align=center>{$j}</p></td>\n";
				echo "	<TD>{$menu_all_name[$arr_menu_list[$i]]}</td>\n";
				echo "	<TD class=\"td_con1\">";
				
				echo "<input type=file name=\"menuimg{$arr_menu_list[$i]}\" size=30>";
				/*
				<input type=\"text\" id=\"fileName[$i]\" class=\"file_input_textbox w400\" readonly=\"readonly\"> 
				<div class=\"file_input_div\">
				<input type=\"button\" value=\"찾아보기\" class=\"file_input_button\" /> 
				<input type=file name=\"menuimg{$arr_menu_list[$i]}\" size=30 style=\"width:99%\" class=\"file_input_hidden\" onchange=\"javascript: document.getElementById('fileName[$i]').value = this.value\">
				</div>
				</td>\n";
				*/
				echo "</td>\n";
				echo "	<TD><input type=text name=\"menulink{$arr_menu_list[$i]}\" value=\"{$menu_all_url[$arr_menu_list[$i]]}\" size=40";
				if($arr_menu_list[$i]<=15) echo " readonly style=\"BACKGROUND: #f4f4f4; COLOR: #555555\"";
				echo " style=\"width:99%\" class=\"input\"></td>\n";
				echo "	<TD align=center><input type=checkbox name=\"display{$j}\" value=\"{$arr_menu_list[$i]}\" checked style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\"></td>\n";
				echo "</tr>\n";
				echo "<input type=hidden name=vmenuimg{$arr_menu_list[$i]}";
				if(file_exists($imagepath."easytopmenu{$arr_menu_list[$i]}.gif")) echo " value=\"easytopmenu{$arr_menu_list[$i]}.gif\"";
				echo ">\n";
			}
			$menu_all=str_replace(" ","",$menu_all);
			$menu_all=substr($menu_all,1);
			$arr_menu_all=explode(",",$menu_all);
			for($i=0;$i<count($arr_menu_all);$i++) {
				$j++;
				echo "<tr>\n";
				echo "	<td><p align=center>{$j}</p></td>\n";
				echo "	<TD>{$menu_all_name[$arr_menu_all[$i]]}</td>\n";
				echo "	<TD class=\"td_con1\">";
				echo "<input type=file name=\"menuimg{$arr_menu_all[$i]}\" size=30>";
				/*
				<input type=\"text\" id=\"fileName[$i]\" class=\"file_input_textbox w400\" readonly=\"readonly\"> 
				<div class=\"file_input_div\">
				<input type=\"button\" value=\"찾아보기\" class=\"file_input_button\" />
				<input type=file name=\"menuimg{$arr_menu_all[$i]}\" size=30 style=\"width:99%\" class=\"file_input_hidden\" onchange=\"javascript: document.getElementById('fileName[$i]').value = this.value\">
				</div>*/
				echo "</td>\n";
				echo "	<TD><input type=text name=\"menulink{$arr_menu_all[$i]}\" value=\"{$menu_all_url[$arr_menu_all[$i]]}\" size=40";
				if($arr_menu_all[$i]<=15) echo " readonly style=\"BACKGROUND: #f4f4f4; COLOR: #555555\"";
				echo " style=\"width:99%\" class=\"input\"></td>\n";
				echo "	<TD align=center><input type=checkbox name=\"display{$j}\" value=\"{$arr_menu_all[$i]}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none\"></td>\n";
				echo "</tr>\n";
				echo "<input type=hidden name=vmenuimg{$arr_menu_all[$i]}";
				if(file_exists($imagepath."easytopmenu{$arr_menu_all[$i]}.gif")) echo " value=\"easytopmenu{$arr_menu_list[$i]}.gif\"";
				echo ">\n";
			}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center" height="25">
				<table cellpadding="0" cellspacing="0" width="100%" style="padding-top:4pt; padding-bottom:4pt;">
				<tr>
					<td style="letter-spacing:-0.5pt;">&nbsp;<?php if($_shopdata->top_type!="tope") {?><input type=checkbox name=okdesign value="Y" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <span class="font_orange"><b>Easy 디자인을 쇼핑몰에 곧바로 반영합니다.(템플릿, 개별디자인 모두 해제됨)</b> 이미 적용중일 때는 체크박스 생성되지 않습니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 사용중일 때 디자인 수정 - [적용하기] 클릭하면 쇼핑몰에 곧바로 반영됩니다.<br>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* 미사용일 때 디자인 수정 - 체크박스를 체크하지 않은 상태에서 [적용하기] 클릭하면 저장만 됩니다.</span><?php }?></TD>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0" vspace="7"></a></td>
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
					  <dt><span>Easy 디자인 해제 방법</span></dt>
                      <dd>- 템플릿으로 변경 : 메인템플릿 선택(Easy 디자인, 개별디자인이 모두 해제되고 선택된 템플릿의 상단으로 변경)<br />- 개별디자인으로 변경 : <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');">디자인관리 > 웹FTP 및  개별적용 선택 > 개별디자인 적용선택</a> 메뉴에서<br>
						<span style="padding:0px 67px;"></span>[상단+왼쪽 동시 적용][상단만 적용] 선택 - 개별디자인 내용으로 변경됩니다.<br>
						<span style="padding:0px 128;"></span>[적용안함] 선택 - 사용중인 템플릿으로 변경됩니다.
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