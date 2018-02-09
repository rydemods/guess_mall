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

$type=$_POST["type"];
$up_val=$_POST["up_val"];
$up_product_filter=$_POST["up_product_filter"];
$up_review_type=$_POST["up_review_type"];
$up_reviewlist=$_POST["up_reviewlist"];
$up_leftreview=$_POST["up_leftreview"];
$up_review_memtype=$_POST["up_review_memtype"];
$up_review_date=$_POST["up_review_date"];
$up_review_filter=$_POST["up_review_filter"];
$up_reviewrow=$_POST["up_reviewrow"];

if ($type=="up") {
	// ##### 리뷰필터링 및 rowsu 등록
	if (strpos($up_review_filter,"#")!==false) {
		alert_go('필터링단어에 『#』를 입력하실 수 없습니다.');
	}
	$filter=$up_product_filter."#{$up_review_filter}REVIEWROW".$up_reviewrow;

	$etctype=$up_val;
	if ($up_reviewlist!="0"){
		$etctype.= "REVIEWLIST={$up_reviewlist}"; // 리뷰디스플레이 방식
	}
	if ($up_leftreview!="N"){
		$etctype.= "REVIEW={$up_leftreview}"; // 사용후기메뉴
	}
	if ($up_review_date=="N"){
		$etctype.= "REVIEWDATE={$up_review_date}"; // 리뷰등록날짜 표시안함 
	}
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "review_type		= '{$up_review_type}', ";
	$sql.= "review_memtype	= '{$up_review_memtype}', ";
	$sql.= "etctype			= '{$etctype}', ";
	$sql.= "filter			= '{$filter}' ";
	$update = pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert('고객 상품리뷰 설정이 완료되었습니다.'); }</script>";
}

$sql = "SELECT review_type,review_memtype,etctype,filter FROM tblshopinfo ";
$result = pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);

$review_type=$row->review_type;
$review_memtype=$row->review_memtype;
if (ord($row->etctype)) {
	$etctemp = explode("",$row->etctype);
	$cnt = count($etctemp);
	$etcvalue="";
	for ($i=0;$i<$cnt;$i++) {
		if (substr($etctemp[$i],0,11)=="REVIEWLIST=") $reviewlist=substr($etctemp[$i],11);	#상품리뷰 디스플레이방식
		else if (substr($etctemp[$i],0,7)=="REVIEW=") $leftreview=substr($etctemp[$i],7);	#상품리뷰 왼쪽메뉴 
		else if (substr($etctemp[$i],0,11)=="REVIEWDATE=") $review_date=substr($etctemp[$i],11);	#상품리뷰 등록날짜 표시여부
		else if(ord($etctemp[$i])) $etcvalue.=$etctemp[$i]."";
	}
}
if(ord($reviewlist)==0) $reviewlist="N";
if(ord($leftreview)==0) $leftreview="N";
if(ord($review_date)==0) $review_date="Y";
$tmp_filter=explode("#",$row->filter);
$product_filter=$tmp_filter[0];
$filter_array=explode("REVIEWROW",$tmp_filter[1]);
$review_filter=$filter_array[0];
$reviewrow=$filter_array[1];

${"check_review_type".$review_type} = "checked";
${"check_reviewlist".$reviewlist} = "checked";
${"check_review_memtype".$review_memtype} = "checked";
${"check_review_date".$review_date} = "checked";
${"check_leftreview".$leftreview} = "checked";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	document.form1.type.value="up";
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 운영 설정 &gt;<span>상품리뷰(후기) 설정</span></p></div></div>

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
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품리뷰(후기) 설정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>상품사용후기의 사용여부, 게시방식, 작성권한을 설정할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품리뷰 적용여부</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 관리자 인증후 등록 : <a href="javascript:parent.topframe.GoMenu(4,'product_review.php');"><span class="font_blue">상품관리 > 사은품/견적/기타관리 > 상품 리뷰 관리</span></a>에서 인증 할 수 있습니다.</li>
                            <li>2) 리뷰에 대한 답변/수정/삭제 관리도 할수 있습니다.</li>
                        </ul>
                    </div>
                </td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=up_val value="<?=$etcvalue?>">
			<input type=hidden name=up_product_filter value="<?=$product_filter?>">
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>상품리뷰 사용여부</span></th>
					<TD class="td_con1">
						<input type=radio id="idx_review_type2" name=up_review_type value="Y" <?=$check_review_typeY?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_type2>사용</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type=radio id="idx_review_type1" name=up_review_type value="N" <?=$check_review_typeN?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_type1>사용안함</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type=radio id="idx_review_type3" name=up_review_type value="A" <?=$check_review_typeA?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_type3>관리자 인증 후 등록</label>
					</TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">리뷰 디스플레이 방식</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 관리자 인증후 등록 : <a href="javascript:parent.topframe.GoMenu(4,'product_review.php');"><span class="font_blue">상품관리 > 사은품/견적/기타관리 > 상품 리뷰 관리</span></a>에서 인증 할 수 있습니다.</li>
                            <li>2) <a href="javascript:parent.topframe.GoMenu(2,'design_eachreviewpopup.php');"><span class="font_blue">디자인관리 > 개별디자인-페이지 본문 > 상품리뷰 보기창 꾸미기</span></a>에서 팝업형식 개별디자인 가능합니다.</li>
                        </ul>
                    </div>
                </td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="border:#b9b9b9 1px solid;">
						<TR>
							<TD width="50%"><div class="point_title"><input type=radio id="idx_reviewlist1" name=up_reviewlist value="Y" <?=$check_reviewlistY?> /><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reviewlist1><b>상품상세페이지 본문에 출력(권장)</label></div></TD>
							<TD width="50%"><p align="center"><div class="point_title"><input type=radio id="idx_reviewlist2" name=up_reviewlist value="N" <?=$check_reviewlistN?> />  <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_reviewlist2><b>팝업으로 출력</b></label></div></TD>
						</TR>
						<TR>
							<TD width="50%" align=center style="padding-top:10pt; padding-bottom:10pt;"><img src="images/review_img001.gif" border="0" class="imgline"></TD>
							<TD width="50%" align=center class="td_con1"><img src="images/review_img002.gif" border="0" class="imgline"></TD>
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
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<span class="font_orange"><b>* 회원전용 선택시 회원만 리뷰를 작성할 수 있습니다.</b></span><br>
                <div class="table_style01">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<th><span>회원제 리뷰 적용여부</span></th>
					<td class="td_con1"><input type="radio" id="idx_review_memtype2" name="up_review_memtype" value="Y" <?=$check_review_memtypeY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_memtype2>회원 전용</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_review_memtype1" name=up_review_memtype value="N" <?=$check_review_memtypeN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_memtype1>누구나 작성(회원+비회원)</label></TD>
				</TR>
                <TR>
					<th><span>리뷰 등록날짜 표시여부</span></th>
					<td class="td_con1"><input type=radio id="idx_review_date2" name=up_review_date value="Y" <?=$check_review_dateY?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_date2>리뷰 등록 날짜 표시</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_review_date1" name=up_review_date value="N" <?=$check_review_dateN?>><label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_review_date1>리뷰등록날짜 미표시</label></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">리뷰 필터링 단어 입력 <span>리뷰 작성시 사용할 수 없는 단어(콤마","로 구분하여 입력)</span></div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>필터링 단어 입력</span></th>
					<TD class="td_con1"><input type=text name=up_review_filter value="<?=$review_filter?>" size=100% class="input"></TD>
				</TR>
				</TABLE>
                </div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">리뷰 모음게시판 적용</div>
				</td>
			</tr>
			<tr>
                 <td style="padding-top:3pt; padding-bottom:3pt;">
                 <!-- 도움말 -->
                 <div class="help_info01_wrap">
                 	<ul>
                    	<li>1) 전체보기 사용 : 쇼핑몰왼쪽메뉴에 <b>[사용후기 모음]</b>, 상품상세페이지 본문에 <b>[전체보기]</b> 메뉴가 생성됩니다.</li>
                        <li>2) 전체보기에서는 최근 100개의 사용후기를 보여줍니다.</li>
                    </ul>
                </div>
                </td>
			</tr>
			<tr>
				<td>
                <div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>사용여부 선택</span></th>
					<TD class="td_con1">
                        <div class="table_none">
                        <TABLE cellSpacing=0 cellPadding=0 border=0>
                        <TR>
                            <TD><input type=radio id="idx_leftreview1" name=up_leftreview value="Y" <?=$check_leftreviewY?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_leftreview1><b>[사용후기 모음]</b>, <b>[전체보기]</b> 사용</label>(한 페이지당 표시 수 : <select name=up_reviewrow class="select" style=width:100px>
    <?php
        for ($i=8 ; $i <= 20 ; $i++ ) {
            echo "<option value='{$i}' ";
            if ($i==$reviewrow) echo " selected";
            echo ">{$i}</option>\n";
        }
    ?>
                                </select>)</TD>
                        </TR>
                        <TR>
                            <TD><input type=radio id="idx_leftreview2" name=up_leftreview value="N" <?=$check_leftreviewN?>> <label style='cursor:hand;' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_leftreview2>미사용</label></TD>
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
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>등록된 리뷰관리 메뉴</span></dt>
							<dd><a href="javascript:parent.topframe.GoMenu(4,'product_review.php');"><span class="font_blue">상품관리 > 사은품/견적/기타관리 > 상품 리뷰 관리</span></a></dd>
							
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
