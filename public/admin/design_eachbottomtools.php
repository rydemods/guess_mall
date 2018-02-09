<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$body=$_POST["body"];
$code=$_POST["code"];

if(ord($code)==0) {
	$code="1";
}

$imagepath = $Dir.DataDir."shopimages/etc/";
$btimage_name="btbackground.gif";

if($type=="update" && (ord($body) || $code=="1") && strstr("123456", $code)) {
	if($code=="1") {
		$ptype="bttoolsetc";
		$pmsg="기본메인설정";

		$up_bottomtools_width=(int)$_POST["up_bottomtools_width"];
		$up_bottomtools_widthmain=(int)$_POST["up_bottomtools_widthmain"];
		$up_bottomtools_height=(int)$_POST["up_bottomtools_height"];
		$up_bottomtools_heightclose=(int)$_POST["up_bottomtools_heightclose"];

		if($up_bottomtools_width>0 || $up_bottomtools_height>0) {
			$up_bottomtools_width_type=($_POST["up_bottomtools_width_type"]=="%"?$_POST["up_bottomtools_width_type"]:"");

			$up_bottomtoolsbgtype=$_POST["up_bottomtoolsbgtype"];
			$up_bgcolor=$_POST["up_bgcolor"];
			$up_bgclear=$_POST["up_bgclear"];

			$up_bgimage = $_FILES['up_bgimage']['tmp_name'];
			$up_bgimage_type = $_FILES['up_bgimage']['type'];
			$up_bgimage_name = $_FILES['up_bgimage']['name'];
			$up_bgimage_size = $_FILES['up_bgimage']['size'];
			$up_bgimage_old = $_POST["up_bgimage_old"];

			$up_bgimagelocat=$_POST["up_bgimagelocat"];
			$up_bgimagerepet=$_POST["up_bgimagerepet"];
			
			if($up_bottomtoolsbgtype == "I") {
				if(ord($up_bgimage)) {
					$ext = strtolower(pathinfo($up_bgimage_name,PATHINFO_EXTENSION));
					if (ord($up_bgimage_name) && $ext=="gif" && $up_bgimage_size<=153600) {			
						move_uploaded_file($up_bgimage,$imagepath.$btimage_name);
						chmod($imagepath.$btimage_name,0664);
					} else {
						if (ord($up_bgimage_name)) {
							$msg="올리실 이미지는 150KB 이하의 gif파일만 됩니다.";
						}
					}
				} else {
					if(ord($up_bgimage_old)==0) {
						$msg="배경 이미지 파일이 선택되지 않았습니다.";
					}
				}
			} else if($up_bottomtoolsbgtype == "B" && ord($up_bgcolor)==0){
				$msg="배경 색상이 선택되지 않았습니다.";
				@unlink($imagepath.$btimage_name);
			} else {
				@unlink($imagepath.$btimage_name);
			}

			$followetc_str="";
			if ($up_bottomtools_width>0){
				$followetc_str[] = "BTWIDTH=".$up_bottomtools_width.$up_bottomtools_width_type;
			}
			if ($up_bottomtools_widthmain>0){
				$followetc_str[] = "BTWIDTHM=".$up_bottomtools_widthmain;
			}
			if ($up_bottomtools_height>0){
				$followetc_str[] = "BTHEIGHT=".$up_bottomtools_height;
			}
			if ($up_bottomtools_heightclose>0){
				$followetc_str[] = "BTHEIGHTC=".$up_bottomtools_heightclose;
			}
			if(strstr("NBI", $up_bottomtoolsbgtype)) {
				if($up_bottomtoolsbgtype == "B" && ord($up_bgcolor)) {
					$followetc_str[]= "BTBGTYPE=".$up_bottomtoolsbgtype;
					$followetc_str[]= "BTBGCLEAR=".$up_bgclear;
					$followetc_str[]= "BTBGCOLOR=#".$up_bgcolor;
				} else if($up_bottomtoolsbgtype == "I" && ord($msg)==0) {
					$followetc_str[]= "BTBGTYPE=".$up_bottomtoolsbgtype;
					$followetc_str[]= "BTBGIMAGELOCAT=".$up_bgimagelocat;
					$followetc_str[]= "BTBGIMAGEREPET=".$up_bgimagerepet;
				} else {
					$followetc_str[]= "BTBGTYPE=N";
				}
			}
			
			if(count($followetc_str)>0) {
				$body=implode("",$followetc_str);
			} else {
				$body="";
			}
		}
	} else if($code=="2") {
		$ptype="bttools";
		$pmsg="기본메인화면";
	} else if($code=="3") {
		$ptype="bttoolstdy";
		$pmsg="최근 본 상품 본문";
	} else if($code=="4") {
		$ptype="bttoolswlt";
		$pmsg="WishList 본문";
	} else if($code=="5") {
		$ptype="bttoolsbkt";
		$pmsg="장바구니 본문";
	} else if($code=="6") {
		$ptype="bttoolsmbr";
		$pmsg="회원정보 본문";
	}
	
	if(ord($body)) {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='{$ptype}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT INTO tbldesignnewpage(type,subject,body) VALUES(
			'{$ptype}', 
			'{$pmsg}', 
			'{$body}')";
			pmysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesignnewpage SET 
			body		= '{$body}' 
			WHERE type='{$ptype}' ";
			pmysql_query($sql,get_db_conn());
		}
		pmysql_free_result($result);
		$onload="<script type=\"text/javascript\">window.onload=function(){ alert(\"{$pmsg} 디자인 수정이 완료되었습니다. {$msg}\"); }</script>";
	}
} else if($type=="delete" && strstr("123456", $code)) {
	if($code=="1") {
		$ptype="bttoolsetc";
		$pmsg="기본메인화면";
		@unlink($imagepath.$btimage_name);
	} else if($code=="2") {
		$ptype="bttools";
		$pmsg="기본메인화면";
	} else if($code=="3") {
		$ptype="bttoolstdy";
		$pmsg="최근 본 상품 본문";
	} else if($code=="4") {
		$ptype="bttoolswlt";
		$pmsg="WishList 본문";
	} else if($code=="5") {
		$ptype="bttoolsbkt";
		$pmsg="장바구니 본문";
	} else if($code=="6") {
		$ptype="bttoolsmbr";
		$pmsg="회원정보 본문";
	}

	$sql = "DELETE FROM tbldesignnewpage WHERE type='{$ptype}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script type=\"text/javascript\">window.onload=function(){ alert(\"{$pmsg} 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear" && strstr("23456", $code)) {
	$body="";
	if($code=="1") {
		$ptype="bttoolsetc";
	} elseif($code=="2") {
		$ptype="bttools";
	} elseif($code=="3") {
		$ptype="bttoolstdy";
	} elseif($code=="4") {
		$ptype="bttoolswlt";
	} elseif($code=="5") {
		$ptype="bttoolsbkt";
	} elseif($code=="6") {
		$ptype="bttoolsmbr";
	}
	if(ord($ptype)) {
		$sql = "SELECT body FROM tbldesigndefault WHERE type='{$ptype}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$body=$row->body;
		}
		pmysql_free_result($result);
	}
}

if($type!="clear" && strstr("123456", $code)) {
	if($code=="1") {
		$ptype="bttoolsetc";
	} elseif($code=="2") {
		$ptype="bttools";
	} elseif($code=="3") {
		$ptype="bttoolstdy";
	} elseif($code=="4") {
		$ptype="bttoolswlt";
	} elseif($code=="5") {
		$ptype="bttoolsbkt";
	} elseif($code=="6") {
		$ptype="bttoolsmbr";
	}

	if(ord($ptype)) {
		$body="";
		$sql = "SELECT body FROM tbldesignnewpage WHERE type='{$ptype}' ";
		$result = pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$body=$row->body;
		}
		pmysql_free_result($result);
	}
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript">
<!--
function CheckForm(type) {
	form = document.form1;
<?php if($code=="1") { ?>
	if(form.up_bottomtools_width.value.length==0 || !isDigit(form.up_bottomtools_width.value) || Number(form.up_bottomtools_width.value)<=0) {
		alert("하단 폴로메뉴 전체 가로(Width)를 0보다 큰 숫자로만 입력해 주세요.");
		form.up_bottomtools_width.focus();
		return;
	}
	if(Number(form.up_bottomtools_width.value)>100 && form.up_bottomtools_width_type.value.length>0) {
		alert("하단 폴로메뉴 전체 가로(Width) 퍼센트(%)는 100보다 작은 숫자로만 입력해 주세요.");
		form.up_bottomtools_width.focus();
		return;
	}
	if(form.up_bottomtools_height.value.length==0 || !isDigit(form.up_bottomtools_height.value) || Number(form.up_bottomtools_height.value)<=0) {
		alert("하단 폴로메뉴 전체 세로(Height)-열림은 0보다 큰 숫자로만 입력해 주세요.");
		form.up_bottomtools_height.focus();
		return;
	}
	if(form.up_bottomtools_heightclose.value.length>0 && (!isDigit(form.up_bottomtools_heightclose.value) || Number(form.up_bottomtools_heightclose.value)<=0)) {
		alert("하단 폴로메뉴 전체 세로(Height)-닫힘은 0보다 큰 숫자로만 입력해 주세요.");
		form.up_bottomtools_heightclose.focus();
		return;
	}
	if(form.up_bottomtools_widthmain.value.length==0 || !isDigit(form.up_bottomtools_widthmain.value) || Number(form.up_bottomtools_widthmain.value)<=0) {
		alert("하단 폴로메뉴 전체 가로(Width)를 0보다 큰 숫자로만 입력해 주세요.");
		form.up_bottomtools_widthmain.focus();
		return;
	}
	if(form.up_bottomtoolsbgtype[1].checked && form.up_bgcolor.value.length==0) {
		alert("하단 폴로메뉴 전체 배경 색상을 선택해 주세요.");
		form.up_bgcolor.focus();
		return;
	}
	if(form.up_bgimage_old.value.length==0 && form.up_bottomtoolsbgtype[2].checked && form.up_bgimage.value.length==0) {
		alert("하단 폴로메뉴 전체 배경 이미지를 입력해 주세요.");
		form.up_bgimage.focus();
		return;
	}
<?php } ?>
	if(type=="update") {
		<?php if($code!="1") { ?>
		if(form.body.value.length==0) {
			alert("디자인 내용을 입력하세요.");
			form.body.focus();
			return;
		}
		<?php } ?>
		form.type.value=type;
		form.submit();
	} else if(type=="delete") {
		if(confirm("디자인을 삭제하시겠습니까?")) {
			form.type.value=type;
			form.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		form.type.value=type;
		form.submit();
	}
}

function change_page(val) {
	document.form2.type.value="change";
	document.form2.submit();
}

function isDigit(str) { 
	for(i=0; i<str.length; i++) { 
		var ch = str.substr(i,1).toUpperCase();
		if((ch < "0") || (ch > "9")) {
			return false;
		}
	}
	return true;
}

function selcolor(obj){
	fontcolor = obj.value.substring(1);
	var newcolor = showModalDialog("color.php?color="+fontcolor, "oldcolor", "resizable: no; help: no; status: no; scroll: no;");
	if(newcolor){
		obj.value=newcolor;
	}
}

function ResetClear() {
	form = document.form1;
	form.up_bottomtools_width.value="100";
	form.up_bottomtools_width_type.value="%";
	form.up_bottomtools_height.value="238";
	form.up_bottomtools_heightclose.value="29";
	form.up_bottomtools_widthmain.value="900";
	form.up_bottomtoolsbgtype[2].checked=true;
	bottomtoolsbgtype_change(form,"I");
	form.up_bgimagelocat.value="A";
	form.up_bgimagerepet[0].checked=true;
	if(form.up_bgimage_old.value.length==0 && form.up_bgimage.value.length==0) {
		alert("하단 폴로메뉴 전체 배경 이미지는 직접 입력해 주세요.");
		form.up_bgimage.focus();
	}
}

function bottomtoolsbgtype_change(thisForm,thisValue) {
	if(document.getElementById("idx_bgcolor")) {
		bgcolor_obj = document.getElementById("idx_bgcolor");
	}
	if(document.getElementById("idx_bgimage")) {
		bgimage_obj = document.getElementById("idx_bgimage");
	}

	if(thisValue == "N") {
		thisForm.up_bgcolor.disabled=true;
		thisForm.up_bgclear[0].disabled=true;
		thisForm.up_bgclear[1].disabled=true;
		thisForm.up_bgimage.disabled=true;
		thisForm.up_bgimagelocat.disabled=true;
		thisForm.up_bgimagerepet[0].disabled=true;
		thisForm.up_bgimagerepet[1].disabled=true;
		thisForm.up_bgimagerepet[2].disabled=true;
		thisForm.up_bgimagerepet[3].disabled=true;
		bgcolor_obj.style.backgroundColor="#EAE9E4";
		bgimage_obj.style.backgroundColor="#EAE9E4";
	} else {
		if(thisValue == "B") {
			thisForm.up_bgcolor.disabled=false;
			thisForm.up_bgclear[0].disabled=false;
			thisForm.up_bgclear[1].disabled=false;
			thisForm.up_bgimage.disabled=true;
			thisForm.up_bgimagelocat.disabled=true;
			thisForm.up_bgimagerepet[0].disabled=true;
			thisForm.up_bgimagerepet[1].disabled=true;
			thisForm.up_bgimagerepet[2].disabled=true;
			thisForm.up_bgimagerepet[3].disabled=true;
			bgcolor_obj.style.backgroundColor="#35353f";
			bgimage_obj.style.backgroundColor="#EAE9E4";
		} else {
			thisForm.up_bgcolor.disabled=true;
			thisForm.up_bgclear[0].disabled=true;
			thisForm.up_bgclear[1].disabled=true;
			thisForm.up_bgimage.disabled=false;
			thisForm.up_bgimagelocat.disabled=false;
			thisForm.up_bgimagerepet[0].disabled=false;
			thisForm.up_bgimagerepet[1].disabled=false;
			thisForm.up_bgimagerepet[2].disabled=false;
			thisForm.up_bgimagerepet[3].disabled=false;
			bgcolor_obj.style.backgroundColor="#EAE9E4";
			bgimage_obj.style.backgroundColor="35353f";
		}
	}
}
//-->
</SCRIPT>
<!-- 라인맵 -->
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>하단 폴로메뉴 화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">하단 폴로메뉴 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>하단 폴로메뉴 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
				<div class="table_style01">
					<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
					<th><span>폴로메뉴 화면 선택</span></th>
					<TD>
					<select name=code onchange="change_page(options.value)" style="width:330" class="input">
					<option value="1" <?php if($code=="1")echo"selected";?>>기본메인설정</option>
					<option value="2" <?php if($code=="2")echo"selected";?>>폴로Bar 디자인</option>
					<option value="3" <?php if($code=="3")echo"selected";?>>최근 본 상품 본문</option>
					<option value="4" <?php if($code=="4")echo"selected";?>>WishList 본문</option>
					<option value="5" <?php if($code=="5")echo"selected";?>>장바구니 본문</option>
					<option value="6" <?php if($code=="6")echo"selected";?>>회원정보 본문</option>
					</select>
					</td>
					</TABLE>
				</div>
				</td>
			</tr>
			</form>
			<tr>
				<td>
				<!-- 소제목 -->
				<div class="title_depth3_sub">폴로메뉴 사이즈 설정<p><span>하단 폴로메뉴 전체 가로(Width), 세로(Height) 사이즈를 설정 하실 수 있습니다.</span></p></div>
                </td>
			</tr>	
<?php if($code=="1") { ?>

<?php } else { ?>

			<tr>
				<td style="padding-bottom:3pt;">
				<div class="help_info01_wrap">
					<ul>
						<li>1) 매뉴얼의 <b>매크로명령어</b>를 참조하여 디자인 하세요.</li>
						<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
						<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
					</ul>
				</div>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
            
<?php } ?>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=code value="<?=$code?>">
			<tr>
				<td style="padding-top:3pt;">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				
<?php
	if($code=="1") {
		$body_exp = explode("",$body);
		if(strlen($body)<1) {
			$body="BTWIDTH=BTHEIGHT=BTBGTYPE=NBTBGCLEAR=NBTBGCOLOR=#FFFFFFBTBGIMAGELOCAT=ABTBGIMAGEREPET=A";
		}
		$followetcdata=array();
		if(ord($body)) {
			$followetctemp=explode("",$body);
			$followetccnt=count($followetctemp);
			for ($followetci=0;$followetci<$followetccnt;$followetci++) {
				$followetctemp2=explode("=",$followetctemp[$followetci]);
				if(isset($followetctemp2[1])) {
					$followetcdata[$followetctemp2[0]]=$followetctemp2[1];
				} else {
					$followetcdata[$followetctemp2[0]]="";
				}
			}
		}
		
		if(substr($followetcdata["BTWIDTH"],-1)=="%") {
			$bottomtools_width=rtrim($followetcdata["BTWIDTH"],'%');
			$bottomtools_width_type='%';
		} else {
			$bottomtools_width=$followetcdata["BTWIDTH"];
		}
		$bottomtools_widthmain=$followetcdata["BTWIDTHM"];

		$bottomtools_height=$followetcdata["BTHEIGHT"];
		$bottomtools_heightclose=$followetcdata["BTHEIGHTC"];

		$bottomtoolsbgtype = $followetcdata["BTBGTYPE"];
		$bottomtoolsbgtype_checked[$bottomtoolsbgtype] = "checked";

		if(ord($followetcdata["BTBGCLEAR"])==0) {
			$followetcdata["BTBGCLEAR"]="N";
		}
		if(ord($followetcdata["BTBGCOLOR"])==0) {
			$followetcdata["BTBGCOLOR"]="#FFFFFF";
		}
		if(ord($followetcdata["BTBGIMAGELOCAT"])==0) {
			$followetcdata["BTBGIMAGELOCAT"] = "A";
		}
		if(ord($followetcdata["BTBGIMAGEREPET"])==0) {
			$followetcdata["BTBGIMAGEREPET"] = "A";
		}

		$bgclear_checked[$followetcdata["BTBGCLEAR"]] = "checked";
		$bgcolor = substr($followetcdata["BTBGCOLOR"],1);

		$bgimagelocat_seleced[$followetcdata["BTBGIMAGELOCAT"]] = "selected";
		$bgimagerepet_checked[$followetcdata["BTBGIMAGEREPET"]] = "checked";
?>
				<tr>
					<td>
					<TABLE cellSpacing="0" cellPadding="0" width="100%" border="0">
					<TR>
						<TD>
                        <div class="table_style01">
						<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th><img src="images/design_follow_img1.gif" border="0" style="border:1px #C4C4C4 solid;"></th>
							<td class="td_con1" width="100%" style="padding:10px;"><b><span class="font_orange"><font color="#000000">하단 폴로메뉴 전체</font> 가로(Width)&nbsp;<b>&nbsp;</b>: <input type=text name="up_bottomtools_width" value="<?=$bottomtools_width?>" size="6" maxlength="4" class="input"> <select name="up_bottomtools_width_type" class="input"><option value="">픽셀(px)</option><option value="%" <?php if($bottomtools_width_type=="%")echo"selected";?>>페센트(%)</option></select>
							<br><font color="#000000">하단 폴로메뉴 전체</font> 세로(Height) - <font color="#0000FF">열림</font> : <input type=text name="up_bottomtools_height" value="<?=$bottomtools_height?>" size="6" maxlength="3" class="input"> 픽셀
							<br><font color="#000000">하단 폴로메뉴 전체</font> 세로(Height) - <font color="#005500">닫힘</font> : <input type=text name="up_bottomtools_heightclose" value="<?=$bottomtools_heightclose?>" size="6" maxlength="3" class="input"> 픽셀<br><br>
							
							<font color="#0000FF">하단 폴로메뉴 본문</font> 가로(Width) : <input type=text name="up_bottomtools_widthmain" value="<?=$bottomtools_widthmain?>" size="6" maxlength="4" class="input"> 픽셀</span></span></b><br><br>
							<span class="space_top">* 사이즈는 1보다 큰 숫자만 입력가능합니다.<br>
							* 퍼센트 입력시 최대값은 100 입니다.</span></td>
						</tr>
						</table>
						</TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td>
					<div class="table_style01">
						<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
						<tr>
							<th><span>폴로메뉴 배경 설정</span></th>
							<TD>
							<input type=radio name="up_bottomtoolsbgtype" value="N" id="idx_bottomtoolsbgtype1" onclick="bottomtoolsbgtype_change(this.form,this.value);" <?=$bottomtoolsbgtype_checked["N"]?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bottomtoolsbgtype1">배경 사용 안함</label>
							<input type=radio name="up_bottomtoolsbgtype" value="B" id="idx_bottomtoolsbgtype2" onclick="bottomtoolsbgtype_change(this.form,this.value);" <?=$bottomtoolsbgtype_checked["B"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bottomtoolsbgtype2">배경 색상으로 설정</label>
							<input type=radio name="up_bottomtoolsbgtype" value="I" id="idx_bottomtoolsbgtype3" onclick="bottomtoolsbgtype_change(this.form,this.value);" <?=$bottomtoolsbgtype_checked["I"]?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bottomtoolsbgtype3">배경 이미지로 설정</label>
						</tr>
						</table>
					</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="table_style01">
							<table cellpadding="0" cellspacing="0" width="100%">
							<col width="150"></col>
							<col width=""></col>
							<TR>
								<tr>
									<th colspan=2><div class="point_title"  id="idx_bgcolor">배경 색상으로 설정</div></th>
								</tr>
								<th><span>색상 선택</span></th>
								<TD class="td_con1" valign="bottom">
								<div class="table_none">
								<table cellpadding="0" cellspacing="0">
								<tr>
									<td style="padding-left:5px;">#</td>
									<td style="padding-left:3px;"><input type=text name="up_bgcolor" value="<?=$bgcolor?>" size="8" maxlength="6" class="input" <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="I"?"disabled":"")?>></td>
									<td style="padding-left:5px;"><font color="<?=$bgcolor?>"><span style="font-size:20pt;">■</span></font></td>
									<td style="padding-left:5px;"><a href="javascript:selcolor(document.form1.up_bgcolor)"><IMG src="images/icon_color.gif" border="0" align="absmiddle"></a></td>
								</tr>
								</table>
								</div>
								</td>
							</TR>
							<TR>
								<th><span>투명색 사용여부</span></th>
								<TD class="td_con1" valign="bottom"><input type=radio name="up_bgclear" value="N" id="idx_bgclear1" <?=$bgclear_checked["N"]?> <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="I"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgclear1">투명색 사용안함</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="up_bgclear" value="Y" id="idx_bgclear2" <?=$bgclear_checked["Y"]?> <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="I"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgclear2">투명색 사용함</label></td>
							</TR>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="table_style01">
							<table cellpadding="0" cellspacing="0" width="100%">
							<col width="150"></col>
							<col width=""></col>
							<input type=hidden name="up_bgimage_old" value="<?=(file_exists($imagepath.$btimage_name)?"1":"")?>">
							<tr>
								<th colspan=2><div class="point_title" id="idx_bgimage" style="background-Color:<?=($bottomtoolsbgtype=="I"?"#35353f":"#EAE9E4")?>;">배경 이미지로 설정</div></th>
							</tr>
							<TR>
								<th><span>배경 이미지</span></th>
								<TD class="td_con1" style="padding-left:8px;">
									<input type=file name="up_bgimage" size=50><br>
								<!--
									<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly">
									<div class="file_input_div">
									<input type="button" value="찾아보기" class="file_input_button" />
									<input type=file name="up_bgimage" style="WIDTH: 98%" class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="B"?"disabled":"")?>><br>
									</div>
									-->
								* 등록 가능한 이미지는 파일 확장자 <span class="font_orange">GIF(gif)</span> 만 가능하며 용량은 <span class="font_orange">최대 150KB</span> 까지 가능합니다.
								<?php if(file_exists($imagepath.$btimage_name)) { ?>
								<div class="table_none">
								<table cellpadding="0" cellspacing="0" width="98%" border="0" style="table-layout:fixed">
								<tr>
									<td height="5"></td>
								</tr>
								<tr>
									<td height="100" style="border:#00A0D5 1px solid;"><img src="<?=$imagepath.$btimage_name?>" border="0"></td>
								</tr>
								<tr>
									<td height="5"></td>
								</tr>
								</table>
								</div>
								<?php } ?>
								</TD>
							</TR>
							<TR>
								<th><span>배경 출력 시작 위치</span></th>
								<TD class="td_con1" style="padding-left:8px;"><select name="up_bgimagelocat" class="select" <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="B"?"disabled":"")?>>
									<option value="A" <?=$bgimagelocat_seleced["A"]?>>맨위 - 좌측 </option>
									<option value="B" <?=$bgimagelocat_seleced["B"]?>>맨위 - 중앙</option>
									<option value="C" <?=$bgimagelocat_seleced["C"]?>>맨위 - 우측</option>
									<option value="D" <?=$bgimagelocat_seleced["D"]?>>가운데 - 좌측</option>
									<option value="E" <?=$bgimagelocat_seleced["E"]?>>가운데 - 중앙</option>
									<option value="F" <?=$bgimagelocat_seleced["F"]?>>가운데 - 우측</option>
									<option value="G" <?=$bgimagelocat_seleced["G"]?>>맨아래 - 좌측</option>
									<option value="H" <?=$bgimagelocat_seleced["H"]?>>맨아래 - 중앙</option>
									<option value="I" <?=$bgimagelocat_seleced["I"]?>>맨아래 - 우측</option>
									</select></TD>
							</TR>
							<TR>
								<th><span>배경 반복 설정</span></th>
								<TD class="td_con1"><input type=radio name="up_bgimagerepet" value="A" id="idx_bgimagerepet1" <?=$bgimagerepet_checked["A"]?> <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet1">전체반복</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="up_bgimagerepet" value="B" id="idx_bgimagerepet2" <?=$bgimagerepet_checked["B"]?> <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet2">수평반복</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="up_bgimagerepet" value="C" id="idx_bgimagerepet3" <?=$bgimagerepet_checked["C"]?> <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet3">수직반복</label>&nbsp;&nbsp;&nbsp;&nbsp;
								<input type=radio name="up_bgimagerepet" value="D" id="idx_bgimagerepet4" <?=$bgimagerepet_checked["D"]?> <?=($bottomtoolsbgtype=="N" || $bottomtoolsbgtype=="B"?"disabled":"")?>><label style="cursor:hand;" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for="idx_bgimagerepet4">반복안함</label>
								</TD>
							</TR>
							</table>
						</div>
					</td>
				</tr>
<?php } else { ?>
				<TR>
					<TD><TEXTAREA style="WIDTH: 100%; HEIGHT: 300px" name=body class="textarea"><?=htmlspecialchars($body)?></TEXTAREA></TD>
				</TR>
<?php
}
?>
				</TABLE>											
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a><?=($code=="1"?"&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:ResetClear('clear');\"><img src=\"images/botteon_bok.gif\" border=\"0\" hspace=\"2\"></a>":"&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:CheckForm('clear');\"><img src=\"images/botteon_bok.gif\" border=\"0\" hspace=\"2\"></a>")?>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif"border="0" hspace="0"></a></td>
			</tr>
			</form>
<?php if($code=="1") { ?>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>하단 폴러메뉴 설정</span></dt>
                      <dd>
						- 하단 폴러메뉴 전체 가로, 세로(열림,닫힘) 및 본문 가로 사이즈를 조절할 수 있습니다.<br>
						- 하단 폴러메뉴 배경 설정은 색상코드 및 이미지 구분하여 사용할 수 있습니다.<br>
						<b>&nbsp;&nbsp;</b>이미지 용량은 최대 150KByte까지 가능하며, 확장자는 gif만 가능합니다.</dd>
                    </dl>
                </div>				
                </td>
			</tr>
			
<?php } else { ?>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>하단 폴로메뉴 화면 매크로명령어 (해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
                      <dd>
						<table border=0 cellpadding=0 cellspacing=0 width=95%>
						<col width=150></col>
						<col width=></col>

						<?php if($code=="2"){?>

						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[TODAYCHANGE_선택 전 폰트_선택 후 폰트]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 '최근 본 상품' 폰트
								<br><br><img width=10 height=0>
								<FONT class=font_orange>앞??|???????|?|? : <b>선택 전 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br>
								<br><img width=10 height=0>
								<FONT class=font_orange>뒤??|???????|?|? : <b>선택 후 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br><br>

								<FONT class=font_blue>예) [TODAYCHANGE_12px|0000000|N|N_12px|FF0000|N|N]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[WISHLISTCHANGE_선택 전 폰트_선택 후 폰트]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 'Wishlist' 폰트
								<br><br><img width=10 height=0>
								<FONT class=font_orange>앞??|???????|?|? : <b>선택 전 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br>
								<br><img width=10 height=0>
								<FONT class=font_orange>뒤??|???????|?|? : <b>선택 후 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br><br>

								<FONT class=font_blue>예) [WISHLISTCHANGE_12px|0000000|N|N_12px|FF0000|N|N]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[BASKETCHANGE_선택 전 폰트_선택 후 폰트]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 '장바구니' 폰트
								<br><br><img width=10 height=0>
								<FONT class=font_orange>앞??|???????|?|? : <b>선택 전 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br>
								<br><img width=10 height=0>
								<FONT class=font_orange>뒤??|???????|?|? : <b>선택 후 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br><br>

								<FONT class=font_blue>예) [BASKETCHANGE_12px|0000000|N|N_12px|FF0000|N|N]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[MEMBERCHANGE_선택 전 폰트_선택 후 폰트]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 '회원정보' 폰트
								<br><br><img width=10 height=0>
								<FONT class=font_orange>앞??|???????|?|? : <b>선택 전 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br>
								<br><img width=10 height=0>
								<FONT class=font_orange>뒤??|???????|?|? : <b>선택 후 폰트</b></FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>?? : 글자 크기(예:12px)</FONT> - <FONT COLOR="red">단위 제외시 기본 px로 지정 됩니다.</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>??????? : 글자 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 두껍게(Y/N)</FONT>
								<br><img width=23 height=0>
								<FONT class=font_orange>? : 글자 밑선(Y/N)</FONT><br><br>

								<FONT class=font_blue>예) [MEMBERCHANGE_12px|0000000|N|N_12px|FF0000|N|N]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[TODAYCNT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 '최근 본 상품' 개수 출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[WISHLISTCNT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 'Wishlist' 상품 개수 출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[BASKETCNT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 '장바구니 상품' 개수 출력
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[OPENCLOSEIMG_열기이미지경로_닫기이미지경로]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 열기/닫기 이미지 지정<br>
							<FONT class=font_blue>예) &lt;img src=[OPENCLOSEIMG_../images/common/btopen.gif_../images/common/btclose.gif] border="0"></FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[OPENCLOSECHANGE]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							폴로바 열기/닫기 <FONT class=font_blue>(예:&lt;a href=[OPENCLOSECHANGE]>폴로바 열기/닫기 이미지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>

						<?php } else if($code=="3") {?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFTODAY]<br>[IFELSETODAY]<br>[IFENDTODAY]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							최근 본 상품이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFTODAY]</B>
      최근 본 상품이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSETODAY]</B>
      최근 본 상품이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDTODAY]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[TODAYPROLIST_?????]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							최근 본 상품 목록
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 적립금 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 아이콘 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 진열코드 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 퀵툴스 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 시중가격 출력여부 (Y/N)</FONT>
								<br><FONT class=font_blue>예) [TODAYPROLIST_YYYYY]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E>상품 정보 스타일 정의</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
							<img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xtprname - 상품명 글자 스타일 정의(폰트 사이즈 및 컬러)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xtprname { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xtprsellprice - 상품 판매가격 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xtprsellprice { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xtprreserve - 상품 적립금 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xtprreserve { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xtprconsumerprice - 상품 시중가격 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xtprconsumerprice { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xtprimage - 상품 이미지 스타일 정의</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xtprimage { border:1px #CCCCCC solid; }</FONT>
				<pre style="line-height:15px">
<B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.

<FONT class=font_blue>&lt;style type="text/css">
  #xtprname {color:#666666;font-size:12px;}
  #xtprsellprice {color:#666666;font-size:12px;}
  #xtprreserve {color:#666666;font-size:12px;}
  #xtprconsumerprice {color:#666666;font-size:12px;}
  #xtprimage {border:1px #CCCCCC solid;}
&lt;/style></FONT></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLSELECT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 전체선택 <FONT class=font_blue>(예:&lt;a href=[ALLSELECT]>전체선택&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLSELECTOUT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 전체해제 <FONT class=font_blue>(예:&lt;a href=[ALLSELECTOUT]>전체해제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLOUT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 일괄삭제 <FONT class=font_blue>(예:&lt;a href=[ALLOUT]>일괄삭제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[BASKETLINK]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							장바구니 바로가기 <FONT class=font_blue>(예:&lt;a href=[BASKETLINK]>장바구니 바로가기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<?php } else if($code=="4") {?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFMEMBER]<br>[IFELSEMEMBER]<br>[IFENDMEMBER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							회원 로그인/로그아웃 상태일 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFMEMBER]</B>
      회원 <FONT COLOR="red"><B>로그인</B></FONT> 상태일 경우의 내용
   <B>[IFELSEMEMBER]</B>
      회원 <FONT COLOR="red"><B>로그아웃</B></FONT> 상태일 경우의 내용
   <B>[IFENDMEMBER]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFWISHLIST]<br>[IFELSEWISHLIST]<br>[IFENDWISHLIST]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							Wishlist 상품이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFWISHLIST]</B>
      Wishlist 상품이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEWISHLIST]</B>
      Wishlist 상품이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDWISHLIST]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[WISHLISTPROLIST_?????]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							Wishlist 상품 목록
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 적립금 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 아이콘 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 진열코드 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 퀵툴스 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 옵션상품 아이콘 출력여부 (Y/N)</FONT>
								<br><FONT class=font_blue>예) [WISHLISTPROLIST_YYYYY]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E>상품 정보 스타일 정의</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;word-break:break-all;">
							<img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xwprname - 상품명 글자 스타일 정의(폰트 사이즈 및 컬러)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xwprname { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xwprsellprice - 상품 판매가격 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xwprsellprice { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xwprreserve - 상품 적립금 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xwprreserve { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xwprconsumerprice - 상품 시중가격 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xwprconsumerprice { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xwprimage - 상품 이미지 스타일 정의</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xwprimage { border:1px #CCCCCC solid; }</FONT>
				<pre style="line-height:15px">
<B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.

<FONT class=font_blue>&lt;style type="text/css">
  #xwprname {color:#666666;font-size:12px;}
  #xwprsellprice {color:#666666;font-size:12px;}
  #xwprreserve {color:#666666;font-size:12px;}
  #xwprconsumerprice {color:#666666;font-size:12px;}
  #xwprimage {border:1px #CCCCCC solid;}
&lt;/style></FONT></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLSELECT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 전체선택 <FONT class=font_blue>(예:&lt;a href=[ALLSELECT]>전체선택&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLSELECTOUT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 전체해제 <FONT class=font_blue>(예:&lt;a href=[ALLSELECTOUT]>전체해제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLOUT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 일괄삭제 <FONT class=font_blue>(예:&lt;a href=[ALLOUT]>일괄삭제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[WISHLISTLINK]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							Wishlist 바로가기 <FONT class=font_blue>(예:&lt;a href=[WISHLISTLINK]>Wishlist 바로가기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<?php } else if($code=="5") {?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFBASKET]<br>[IFELSEBASKET]<br>[IFENDBASKET]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							장바구니 상품이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFBASKET]</B>
      장바구니 상품이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEBASKET]</B>
      장바구니 상품이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDBASKET]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[BASKETPROLIST_??????]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							장바구니 상품 목록
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 적립금 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 아이콘 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 진열코드 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 퀵툴스 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : 상품 수량 출력여부 (Y/N)</FONT>
								<br><img width=10 height=0>
								<FONT class=font_orange>? : (옵션,패키지,코디/조립)상품 아이콘 출력여부 (Y/N)</FONT>
								<br><FONT class=font_blue>예) [BASKETPROLIST_YYYYYY]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E>상품 정보 스타일 정의</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;word-break:break-all;">
							<img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xbprname - 상품명 글자 스타일 정의(폰트 사이즈 및 컬러)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xbprname { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xbprsellprice - 상품 판매가격 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xbprsellprice { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xbprreserve - 상품 적립금 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xbprreserve { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xbprquantity - 상품 구입수량 TD 스타일 정의 (폰트 및 셀 스타일)</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xbprquantity { color:#666666;font-size:12px; }</FONT>
							<br><img width=0 height=7 style="visibility:hidden;"><br><img width=10 height=0 style="visibility:hidden;">
							<FONT class=font_orange>#xbprimage - 상품 이미지 스타일 정의</FONT>
							<br><img width=100 height=0 style="visibility:hidden;">
							<FONT class=font_blue>예) #xbprimage { border:1px #CCCCCC solid; }</FONT>
				<pre style="line-height:15px">
<B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.

<FONT class=font_blue>&lt;style type="text/css">
  #xbprname {color:#666666;font-size:12px;}
  #xbprsellprice {color:#666666;font-size:12px;}
  #xbprreserve {color:#666666;font-size:12px;}
  #xbprquantity {color:#666666;font-size:12px;}
  #xbprimage {border:1px #CCCCCC solid;}
&lt;/style></FONT></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLSELECT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 전체선택 <FONT class=font_blue>(예:&lt;a href=[ALLSELECT]>전체선택&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLSELECTOUT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 전체해제 <FONT class=font_blue>(예:&lt;a href=[ALLSELECTOUT]>전체해제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ALLOUT]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							상품 일괄삭제 <FONT class=font_blue>(예:&lt;a href=[ALLOUT]>일괄삭제&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[BASKETLINK]</td>
							<td class=td_con1 style="padding-left:5;word-break:break-all;">
							장바구니 바로가기 <FONT class=font_blue>(예:&lt;a href=[BASKETLINK]>장바구니 바로가기&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<?php } else if($code=="6") {?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFMEMBER]<br>[IFELSEMEMBER]<br>[IFENDMEMBER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							회원 로그인/로그아웃 상태일 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFMEMBER]</B>
      회원 <FONT COLOR="red"><B>로그인</B></FONT> 상태일 경우의 내용
   <B>[IFELSEMEMBER]</B>
      회원 <FONT COLOR="red"><B>로그아웃</B></FONT> 상태일 경우의 내용
   <B>[IFENDMEMBER]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFMEMNON]<br>[IFELSEMEMNON]<br>[IFENDMEMNON]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							회원 정보가 있을 경우와 없을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFMEMNON]</B>
      회원 정보가 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEMEMNON]</B>
      회원 정보가 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDMEMNON]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 아이디
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[GNAME]</td>
							<td class=td_con1 style="padding-left:5;">
							회원등급
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFGNAME]<br>[IFENDGNAME]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							회원등급이 있을 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFGNAME]</B>
      회원등급이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFENDGNAME]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[TEL]</td>
							<td class=td_con1 style="padding-left:5;">
							전화번호
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							이메일 주소
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ADDR]</td>
							<td class=td_con1 style="padding-left:5;">
							집주소
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFNEWSMAIL]<br>[IFELSENEWSMAIL]<br>[IFENDNEWSMAIL]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							이메일 수신일 경우와 아닐 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFNEWSMAIL]</B>
      이메일 <FONT COLOR="red"><B>수신</B></FONT>일 경우의 내용
   <B>[IFELSENEWSMAIL]</B>
      이메일 <FONT COLOR="red"><B>미수신</B></FONT>일 경우의 내용
   <B>[IFENDNEWSMAIL]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right nowrap>[IFNEWSSMS]<br>[IFELSENEWSSMS]<br>[IFENDNEWSSMS]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							SMS 수신일 경우와 아닐 경우
							<pre style="line-height:15px">
<FONT class=font_blue>   <B>[IFNEWSSMS]</B>
      SMS <FONT COLOR="red"><B>수신</B></FONT>일 경우의 내용
   <B>[IFELSENEWSSMS]</B>
      SMS <FONT COLOR="red"><B>미수신</B></FONT>일 경우의 내용
   <B>[IFENDNEWSSMS]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ORDERCNT]</td>
							<td class=td_con1 style="padding-left:5;">
							최근 주문내역 건수
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[ORDERLINK]</td>
							<td class=td_con1 style="padding-left:5;">
							주문내역 바로가기
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[RESERVECNT]</td>
							<td class=td_con1 style="padding-left:5;">
							현재 적립금액
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[RESERVELINK]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금내역 바로가기
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[COUPONCNT]</td>
							<td class=td_con1 style="padding-left:5;">
							현재 보유 쿠폰수
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[COUPONLINK]</td>
							<td class=td_con1 style="padding-left:5;">
							쿠폰내역 바로가기
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[PERSONALCNT]</td>
							<td class=td_con1 style="padding-left:5;">
							1:1 문의 건수
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right>[PERSONALLINK]</td>
							<td class=td_con1 style="padding-left:5;">
							1:1문의 바로가기
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<?php } ?>
						</table>
					  </dd>
					  <dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
                    </dl>

                </div>		

						

				
				</td>
			</tr>
<?php
}
?>
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
