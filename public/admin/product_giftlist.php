<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-5";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/etc/";

$sort=$_REQUEST["sort"];
$type=$_POST["type"];

//환경설정
$up_gift_type1=$_POST["up_gift_type1"];	//사은품 제도 사용여부
$up_gift_type2='N';	//다중선택 사은품 사용여부
$up_gift_type3='A';	//결제방법
$up_gift_type4='N';	//마이페이지에서의 선택 가능여부
/*
$up_gift_type2=$_POST["up_gift_type2"];	//다중선택 사은품 사용여부
$up_gift_type3=$_POST["up_gift_type3"];	//결제방법
$up_gift_type4=$_POST["up_gift_type4"];	//마이페이지에서의 선택 가능여부
*/
if(ord($up_gift_type1)==0) $up_gift_type1="N";
if(ord($up_gift_type2)==0) $up_gift_type2="N";
if(ord($up_gift_type3)==0) $up_gift_type3="A";
if(ord($up_gift_type4)==0) $up_gift_type4="N";

//사은품 등록
$gift_name=$_POST["gift_name"];
$gift_startprice=$_POST["gift_startprice"];
$gift_endprice=$_POST["gift_endprice"];
$gift_quantity=$_POST["gift_quantity"];
$gift_limit=$_POST["gift_limit"];
$option1_title=$_POST["option1_title"];
$option1_value=$_POST["option1_value"];
$option2_title=$_POST["option2_title"];
$option2_value=$_POST["option2_value"];
$gift_image=$_FILES["gift_image"];

$gift_regdate=$_POST["gift_regdate"];




if ($type=="config") {
	$gift_type=$up_gift_type1."|{$up_gift_type2}|{$up_gift_type3}|".$up_gift_type4;
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "gift_type	= '{$gift_type}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('고객 사은품 설정이 완료되었습니다.');}</script>";
} else if ($type=="insert") {
	$maxcnt=100;
	if ($gift_limit<0 || $gift_limit>99) {
		alert_go('사은품 선택제한 수량 입력이 잘못되었습니다.');
	}
	$sql = "SELECT COUNT(*) as cnt FROM tblgiftinfo ";
	$result = pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	if ($row->cnt>=$maxcnt) {
		alert_go('사은품 등록은 최대 100개 까지입니다.');
	} 
	$curdate=date("YmdHis");
	if (ord($gift_endprice)==0) {
		//$gift_endprice=16777215;
		$gift_endprice=10000000;
	}
	$sql_quantity=(ord($gift_quantity)==0)?"NULL":$gift_quantity;
	$sql_limit=(ord($gift_limit)==0)?0:$gift_limit;
	if (ord($gift_image["name"])){
		/*
		if ($gift_image["size"]>153600 || $gift_image["size"]==0) {
			alert_go('등록 가능한 사은품 이미지의 용량은 150KB이하로 제한됩니다.');
		}
		*/
		$getsize=getimageSize($gift_image["tmp_name"]);
		$width=$getsize[0];
		$height=$getsize[1];
		$imgtype=$getsize[2];
		$size_limit=200;
		if ($imgtype==1 || $imgtype==2 || $imgtype==3) {
			if ($imgtype==1) $ext="gif";
			else if ($imgtype==2) $ext="jpg";
			else if ($imgtype==3) $ext="png";
			/*
			if ($width>$size_limit || $height>$size_limit) {
				if($imgtype==1)      $im = ImageCreateFromGif($gift_image["tmp_name"]);
				else if($imgtype==2) $im = ImageCreateFromJpeg($gift_image["tmp_name"]);
				else if($imgtype==3) $im = ImageCreateFromPng($gift_image["tmp_name"]);
				if ($width>=$height) {
					$small_width=$size_limit;
					$small_height=($height*$size_limit)/$width;
				} else if($width<$height) {
					$small_width=($width*$size_limit)/$height;
					$small_height=$size_limit;
				}
				if ($imgtype==1) {
					$im2=ImageCreate($small_width,$small_height); // GIF일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					ImageCopyResized($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imageGIF($im2,$gift_image["tmp_name"]);
				} else if ($imgtype==2) {
					$im2=ImageCreateTrueColor($small_width,$small_height); // JPG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imageJPEG($im2,$gift_image["tmp_name"],$quality);
				} else {
					$im2=ImageCreateTrueColor($small_width,$small_height); // PNG일경우
					$white = ImageColorAllocate($im2, 255,255,255);
					imagefill($im2,1,1,$white);
					imagecopyresampled($im2,$im,0,0,0,0,$small_width,$small_height,$width,$height);
					imagePNG($im2,$gift_image["tmp_name"]);
				}
				ImageDestroy($im);
				ImageDestroy($im2);
			}
			*/
			$filename="gift_{$curdate}.".$ext;

			move_uploaded_file($gift_image["tmp_name"],$imagepath.$filename);
			chmod($imagepath.$filename,0666);
		} else {
			alert_go('사은품 이미지는 JPG, GIF, PNG 파일만 등록 가능합니다.');
		}
	} else {
		$filename="";
	}
	$gift_option1=(ord($option1_title) && ord($option1_value))?str_replace(",","",$option1_title).",".trim($option1_value):"";
	$gift_option2=(ord($option2_title) && ord($option2_value))?str_replace(",","",$option2_title).",".trim($option2_value):"";

	$sql = "INSERT INTO tblgiftinfo(
	gift_regdate	,
	gift_startprice	,
	gift_endprice	,
	gift_quantity	,
	gift_limit	,
	gift_name	,
	gift_image	,
	gift_option1	,
	gift_option2) VALUES (
	'{$curdate}', 
	'{$gift_startprice}', 
	'{$gift_endprice}', 
	{$sql_quantity}, 
	'{$sql_limit}', 
	'{$gift_name}', 
	'{$filename}', 
	'{$gift_option1}', 
	'{$gift_option2}')";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert ('사은품 등록이 완료되었습니다.');}</script>\n";
} else if ($type=="delete") {
	if (ord($gift_regdate)) {
		$sql = "SELECT * FROM tblgiftinfo WHERE gift_regdate = '{$gift_regdate}' ";
		$result = pmysql_query($sql,get_db_conn());
		if ($row = pmysql_fetch_object($result)) {
			if ($row->gift_image) {
				unlink($imagepath.$row->gift_image);
			}
			$sql = "DELETE FROM tblgiftinfo WHERE gift_regdate = '{$gift_regdate}' ";
			pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){alert('해당 사은품 정보 삭제가 완료되었습니다.');}</script>";
		}
		pmysql_free_result($result);
	}
} else if ($type=="quantity") {
	if (ord($gift_quantity)==0) {
		$gift_quantity = "NULL";
	}
	if (ord($gift_startprice)==0) {
		$gift_startprice = "1";
	}
	if (ord($gift_endprice)==0) {
		$gift_endprice = "16777215";
	}
	$sql = "UPDATE tblgiftinfo SET gift_quantity = {$gift_quantity}, gift_endprice='{$gift_endprice}', gift_startprice='{$gift_startprice}' ";
	
	//$sql = "UPDATE tblgiftinfo SET gift_quantity = '{$gift_quantity}' ";
	$sql.= "WHERE gift_regdate = '{$gift_regdate}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert('선택하신 사은품 재고수량 수정이 완료되었습니다.');}</script>";
}

$sql = "SELECT gift_type FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
$row = pmysql_fetch_object($result);
pmysql_free_result($result);
$gift_type = explode("|",$row->gift_type);
if(ord($gift_type[0])==0) $gift_type[0]="N";
if(ord($gift_type[1])==0) $gift_type[1]="N";
if(ord($gift_type[2])==0) $gift_type[2]="A";
if(ord($gift_type[3])==0) $gift_type[3]="N";

${"chk_gift1".$gift_type[0]} = "checked";
${"chk_gift2".$gift_type[1]} = "checked";
${"chk_gift3".$gift_type[2]} = "checked";
${"chk_gift4".$gift_type[3]} = "checked";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm1() {
	document.form1.type.value="config";
	document.form1.submit();
}

function CheckForm2() {
	form=document.form2;
	if (form.gift_name.value.length==0) {
		alert ("사은품의 이름을 입력하세요.");
		form.gift_name.focus();
		return;
	}
	if (isNaN(form.gift_startprice.value) || form.gift_startprice.value.length==0) {
		alert ("구매가격범위를 숫자로 입력하세요.");
		form.gift_startprice.focus();
		return;
	}
	if (form.between.selectedIndex==0 && (isNaN(form.gift_endprice.value) || form.gift_endprice.value.length==0)) {
		alert ("구매가격범위를 숫자로 입력하세요.");
		form.gift_endprice.focus();
		return;
	}
	if (parseInt(form.gift_startprice.value)<=0) {
		alert ("구매범위 시작가는 1원이상 입력하셔야 합니다..");
		form.gift_startprice.focus();
		return;
	}
	if (form.between.selectedIndex==0 && (parseInt(form.gift_startprice.value) >= parseInt(form.gift_endprice.value))) {
		alert ("구매범위 시작가가 구매범위 종료가 보다 크거나 같을 수 없습니다.");
		form.gift_startprice.focus();
		return;
	}
	if (form.quantity_choice[1].checked && (isNaN(form.gift_quantity.value) || form.gift_quantity.value.length==0)) {
		alert ("재고수량을 숫자로 입력하세요.");
		form.gift_quantity.focus();
		return;
	}
	if (form.quantity_choice[1].checked && parseInt(form.gift_quantity.value) > 60000) {
		alert ("재고수량이 60000개 이상인 경우는 무제한으로 선택하세요");
		form.gift_quantity.focus();
		return;
	}
	/*
	if (form.limit_choice[1].checked && (isNaN(form.gift_limit.value) || parseInt(form.gift_limit.value)<1)) {
		alert ("선택제한은 1부터 99까지의 숫자로만 입력을 하셔야 합니다.");
		form.gift_limit.focus();
		return;
	}
	
	if (form.option1_title.value.length>0 && form.option2_value.length==0) {
		alert ("옵션제목을 입력하신 경우는 반드시 속성도 입력하셔야 합니다.");
		form.option2_value.focus();
		return;
	}
	if (form.option2_title.value.length>0 && form.option2_value.length==0) {
		alert ("옵션제목을 입력하신 경우는 반드시 속성도 입력하셔야 합니다.");
		form.option2_value.focus();
		return;
	}
	*/
	form.type.value="insert";
	form.submit();
}

function between_check() {
	form=document.form2;
	if (form.between.selectedIndex==1) {
		form.gift_endprice.style.background="#F0F0F0";
		form.gift_endprice.disabled=true;
	} else if (form.between.selectedIndex==0) {
		form.gift_endprice.style.background="white";
		form.gift_endprice.disabled=false;
	}
}

function quantity_change(tmp) {
	form=document.form2;
	if (tmp=="flag") {
		form.gift_quantity.style.background="#F0F0F0";
		form.gift_quantity.disabled=true;
	} else if (tmp=="nonflag") {
		form.gift_quantity.style.background="white";
		form.gift_quantity.disabled=false;
	}
}

function limit_change(tmp) {
	form=document.form2;
	if (tmp=="flag") {
		form.gift_limit.style.background="#F0F0F0";
		form.gift_limit.disabled=true;
	} else if (tmp=="nonflag") {
		form.gift_limit.style.background="white";
		form.gift_limit.disabled=false;
	}
}

function delgift(code) {
	if (confirm("선택하신 사은품을 삭제하시겠습니까?")) {
		document.form3.type.value="delete";
		document.form3.gift_regdate.value=code;
		document.form3.submit();
	}
}

function amount_up(code,no) {
	//if (isNaN(document.form5["gift_quantity"+no].value) || document.form5["gift_quantity"+no].value.length==0) {
	//	document.form5["gift_quantity"+no].focus();
	//	alert("재고수량은 숫자만 입력 가능합니다.");
	//	return;
	//}
	if (confirm("사은품 재고수량을 수정하시겠습니까?")) {
		document.form3.type.value="quantity";
		document.form3.gift_regdate.value=code;
		document.form3.gift_quantity.value=document.form5["gift_quantity"+no].value;
		document.form3.gift_startprice.value=document.form5["gift_startprice"+no].value;
		document.form3.gift_endprice.value=document.form5["gift_endprice"+no].value;
		document.form3.submit();
	}
}

function overTip(boxObj) {
	try {
		boxObj.style.visibility = "visible";
	} catch (e) {}
}
function outTip(boxObj) {
	try {
		boxObj.style.visibility = "hidden";
	} catch (e) {}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>사은품 제도 관리</span></p></div></div>

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
					<div class="title_depth3">사은품 제도 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품 주문시 가격대별로 선택이 가능한 무료 사은품을 관리합니다.</span></div>
				</td>
			</tr>
            <tr>
            	<td>
                	<div class="title_depth3_sub">사은품 행사 설정</div>
                </td>
            </tr>
            <tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) [사은품 복수선택]</b>은 고객이 주문한 최종구매금액을 포인트로 환산(1:1)하여 포인트 내에서 사은품을 복수로 선택할 수 있습니다.</li>
                            <li>2) 주문조회페이지에서 선택 가능여부는 결제완료 페이지에서 선택을 못했을 경우 주문조회페이지에서 선택가능하게 할것인지를 선택합니다.</li>
                        </ul>
                    </div>                    
            	</td>
            </tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden name=type>
				<input type=hidden name=sort value="<?=$sort?>">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<TR>
					<th><span>사은품 제도 사용여부</span></th>
					<TD>
					<INPUT id=idx_gift_type11 type=radio name=up_gift_type1 value=N <?=$chk_gift1N?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_gift_type11>사용안함</LABEL>&nbsp;&nbsp;
					<INPUT id=idx_gift_type12 type=radio name=up_gift_type1 value=M <?=$chk_gift1M?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_gift_type12>회원 구매자만 사용가능</LABEL>&nbsp;&nbsp;&nbsp;
					<INPUT id=idx_gift_type13 type=radio name=up_gift_type1 value=C <?=$chk_gift1C?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gift_type13>모든 구매자 사용가능</LABEL></TD>
				</TR>
				<!--TR>
					<th><span>사은품 복수선택 사용여부</span></th>
					<TD>
					<INPUT id=idx_gift_type22 type=radio name=up_gift_type2 value=Y <?=$chk_gift2Y?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_gift_type22>사용함</LABEL>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<INPUT id=idx_gift_type21 type=radio name=up_gift_type2 value=N <?=$chk_gift2N?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_gift_type21>사용안함</LABEL>
					</TD>
				</TR>
				<TR>
					<th><span>결제방법에 따른<br>&nbsp;&nbsp; 사은품 선택</span></th>
					<TD>
					<INPUT id=idx_gift_type31 type=radio value=A name=up_gift_type3 <?=$chk_gift3A?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gift_type31>모든 결제 가능</LABEL>&nbsp;&nbsp;
					<INPUT id=idx_gift_type32 type=radio value=B name=up_gift_type3 <?=$chk_gift3B?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gift_type32>현금 결제만 가능</LABEL>
					</TD>
				</TR>
				<TR>
					<th><span>주문조회페이지에서<br>&nbsp;&nbsp;&nbsp;선택 가능여부</span></th>
					<TD>
					<INPUT id=idx_gift_type42 type=radio value=Y name=up_gift_type4 <?=$chk_gift4Y?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gift_type42>선택가능</LABEL>&nbsp;&nbsp;
					<INPUT id=idx_gift_type41 type=radio value=N name=up_gift_type4 <?=$chk_gift4N?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_gift_type41>선택불가</LABEL>
					</TD>
				</TR-->
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm1();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">사은품 등록 관리</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=190></col>
				<col width=></col>
				<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
				<input type=hidden name=type>
				<input type=hidden name=sort value="<?=$sort?>">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">

				<TR>
					<th><span>사은품명</span></th>
					<TD><INPUT style=width:100% onkeydown=chkFieldMaxLen(200) maxLength=20  name=gift_name class="input"></TD>
				</TR>
				<TR>
					<th><span>사은품 혜택 가능 구매가격</span></th>
					<TD>
					<INPUT maxLength=7 size=7 name=gift_startprice class="input">원 
					<SELECT onchange=between_check() name=between class="select">
					<OPTION value=1>부터(이상)</OPTION>
					<OPTION value=2>이상 모든가격</OPTION>
					</SELECT>
					 ~&nbsp; 
					 <INPUT maxLength=7 size=7 name=gift_endprice class="input"> 원 까지(미만) 
					 <span class="font_orange">* 콤마(,) 제외한 숫자만 입력해 주세요.</span>
					</TD>
				</TR>
				<TR>
					<th><span>사은품 재고수량</span></th>
					<TD>
					<INPUT id=idx_quantity_choice1 onclick="quantity_change('flag')" type=radio CHECKED value=endless name=quantity_choice><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_quantity_choice1>제한없음</LABEL>&nbsp;
					<INPUT id=idx_quantity_choice2 onclick="quantity_change('nonflag')" type=radio value=end name=quantity_choice><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_quantity_choice2>개수</LABEL> 
					<INPUT  disabled maxLength="10" size="10" name=gift_quantity class="input_disabled"> 개
					</TD>
				</TR>
				<!--TR>
					<th><span>사은품 선택제한</span></th>
					<TD>
					<INPUT id=idx_limit_choice1 onclick="limit_change('flag')" type=radio CHECKED value=endless name=limit_choice><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_limit_choice1>제한없음</LABEL>&nbsp;
					<INPUT id=idx_limit_choice2 onclick="limit_change('nonflag')" type=radio value=end name=limit_choice><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_limit_choice2>개수</LABEL> 
					<INPUT  disabled maxLength="10" size="10" name=gift_limit class="input_disabled"> 개 <span class="font_orange">* 한 사은품당 선택가능한 최대 개수</span>
					</TD>
				</TR>
				<tr>
					<th><span>사은품 옵션1</span></th>
					<TD>
					속성명 : <INPUT size=15 name=option1_title class="input"> &nbsp;&nbsp;
					속성 : <INPUT size=53 name=option1_value class="input">
					</TD>
				</tr>
				<tr>
					<th><span>사은품 옵션2</span></th>
					<TD>
					속성명 : <INPUT size=15 name=option2_title class="input"> &nbsp;&nbsp;
					속성 : <INPUT size=53 name=option2_value class="input">
					</TD>
				</tr-->
				<tr>
					<th><span>사은품 이미지</span></th>
					<TD class="td_con1">
					<INPUT type=file size=50 name=gift_image><br>
					<!--
					<input type="text" id="fileName" class="file_input_textbox w400" readonly="readonly"> 
					<div class="file_input_div">
					<input type="button" value="찾아보기" class="file_input_button" /> 
					<INPUT type=file size=71 name=gift_image class="file_input_hidden" onchange="javascript: document.getElementById('fileName').value = this.value" ><br />
					</div>
					-->
					<span class="font_orange">* 이미지는 최대사이즈 200 X 200, 최대용량 150KB 이하의 GIF, JPG, PNG 만 가능</span></TD>
				</tr>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm2();"><img src="images/botteon_save.gif"  border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">등록된 사은품 목록</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=120>
                <col width=>
                <col width=100 />
                <col width=100>
                <col width=80>
                <col width=80>
                <col width=80>
				<form name=form5 action="<?=$_SERVER['PHP_SELF']?>">
				<TR align=center>
					<th>등록일자</th>
					<th>사은품명</th>
					<th>구매시작가</th>
					<th>구매종료가</th>
					<th>재고수량</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$sql = "SELECT COUNT(*) as t_count FROM tblgiftinfo ";
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;
				
				$sql = "SELECT * FROM tblgiftinfo ";
				$sql.= "ORDER BY gift_regdate DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
					echo "<TR>\n";
					echo "	<TD>".substr($row->gift_regdate,0,4)."/".substr($row->gift_regdate,4,2)."/".substr($row->gift_regdate,6,2)."</TD>\n";
					echo "	<TD style=\"word-break:break-all;\">";
					if (ord($row->gift_image) && file_exists($imagepath.$row->gift_image)) {
						echo "  <div class=\"ta_l\"><span onMouseOver='overTip(bigimg{$i})' onMouseOut='outTip(bigimg{$i})'>$row->gift_name</span></div>\n";
						echo "	<div id=\"bigimg{$i}\" style=\"position:absolute; z-index:100; visibility:hidden;\">";
						echo "	<img name=bigimgs src=\"".$imagepath.$row->gift_image."\"></div>\n";
					} else {
						echo " <div class=\"ta_l\"> $row->gift_name </div>";
					}
					echo "	<TD><b><span class=\"font_orange\"><input type=text name=gift_startprice{$i} value=\"{$row->gift_startprice}\" size=10 class=\"input\" maxLength=7 style=\"ime-mode:disabled;\" onKeypress=\"if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;\">원</span></b></TD>\n";
					echo "	<TD><b><span class=\"font_blue\"><input type=text name=gift_endprice{$i} value=\"{$row->gift_endprice}\" size=10 class=\"input\" maxLength=7 style=\"ime-mode:disabled;\" onKeypress=\"if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;\">원</span></b></TD>\n";
					echo "	<TD><input type=text name=gift_quantity{$i} value=\"{$row->gift_quantity}\" size=5 maxlength=5 class=\"input\" style=\"ime-mode:disabled;\" onKeypress=\"if(event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;\"><b>개</b> </TD>\n";
					echo "<td><A HREF=\"javascript:amount_up('{$row->gift_regdate}','{$i}');\"><img src='images/btn_edit.gif' border=0 align=absmiddle></A></td>";
					echo "	<TD><A HREF=\"javascript:delgift('{$row->gift_regdate}')\"><img src=\"images/btn_del.gif\" border=\"0\"></A></TD>\n";
					//echo "	<TD><b><span class=\"font_orange\">".number_format($row->gift_startprice)."원</span></b></TD>\n";
					//echo "	<TD><b><span class=\"font_blue\">".number_format($row->gift_endprice)."원</span></b></TD>\n";
					//echo "	<TD><input type=text name=gift_quantity{$i} value=\"{$row->gift_quantity}\" size=5 maxlength=5 class=\"input\"> <A HREF=\"javascript:amount_up('{$row->gift_regdate}','{$i}');\"><img src='images/icon_edit2.gif' border=0 align=absmiddle></A></TD>\n";
					//echo "	<TD><A HREF=\"javascript:delgift('{$row->gift_regdate}')\"><img src=\"images/btn_del.gif\" border=\"0\"></A></TD>\n";
					echo "</TR>\n";
					$i++;
				}
				pmysql_free_result($result);

				if ($i==0) {
					echo "<tr><td colspan=6 align=center>등록된 사은품 정보가 없습니다.</td></tr>\n";
				}
?>
				</form>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
<?php
			echo "<tr>\n";
			echo "	<td colspan=\"6\" align=center style='font-size:11px;'>\n";
			echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
?>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>사은품 제도 관리</span></dt>
							<dd>
								- 등록한 사은품은 재고수량 수정외에는 수정이 불가능하므로 삭제 후 다시 등록해 주세요.<br>
- 사은품 재고수량은 주문이 취소되도 복원되지 않습니다.<br>
- 삭제된 사은품은 복원되지 않으므로 신중히 처리하시기 바랍니다.<br>
- <span class="font_orange">사은품 등록은 최대 100개</span> 까지 등록이 가능합니다.<br>
- 고객이 선택한 사은품 내역은 주문조회페이지에서 확인 가능합니다.
							</dd>
							
						</dl>
						</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			<form name=form3 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=gift_regdate>
			<input type=hidden name=gift_quantity>
			<input type=hidden name=gift_startprice>
			<input type=hidden name=gift_endprice>
			</form>
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
