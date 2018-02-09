<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$maxcnt=10;
$eventpopup = array("tem_001","001","002","003","004");
/*$eventpopup = array("U","001","002","003","004");*/

$type=$_POST["type"];
$num=$_POST["num"];
$start_date=$_POST["start_date"];
$end_date=$_POST["end_date"];
$design=$_POST["design"];
$x_to=$_POST["x_to"];
$y_to=$_POST["y_to"];
$x_size=$_POST["x_size"]?:0;
$y_size=$_POST["y_size"]?:0;
$scroll_yn=$_POST["scroll_yn"];
$frame_type=$_POST["frame_type"];
$cookietime=$_POST["cookietime"];
$title=$_POST["title"];
$content=$_POST["content"];
$is_mobile =$_POST["is_mobile"];
$mobile_display = $_POST["mobile_display"];

$mobile_type = "";
$mobile_update = "";
$mobile_value = "";
if($is_mobile == "Y"){
	$mobile_type = " is_mobile, ";
	$mobile_update = " is_mobile = '".$is_mobile."', ";
	$mobile_value = " 'Y', ";
}

if($mobile_display == "N"){
	$mobile_display_type = " mobile_display, ";
	$mobile_display_update = " mobile_display = 'N', ";
	$mobile_display_value = " 'N', ";
}else{
	$mobile_display_type = " mobile_display, ";
	$mobile_display_update = " mobile_display = 'Y', ";
	$mobile_display_value = " 'Y', ";
}


$in_start = str_replace("-","",$start_date);
$in_end = str_replace("-","",$end_date);
if($type=="insert") {
	$sql = "SELECT COUNT(*) as cnt, COUNT(CASE WHEN frame_type='2' THEN 1 ELSE NULL END) as cnt2 FROM tbleventpopup ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	pmysql_free_result($result);
	if($row->cnt<$maxcnt) {
//		if($frame_type==2 && $row->cnt2>=1) {
//			$onload="<script>alert('레이어 타입의 팝업창은 1개만 등록 가능합니다.');</script>";
//		} else {
			$sql = "INSERT INTO tbleventpopup(
			start_date	,
			end_date	,
			reg_date	,
			design		,
			x_size		,
			y_size		,
			x_to		,
			y_to		,
			scroll_yn	,
			frame_type	,
			cookietime	,
			title		,
			".$mobile_type."
			".$mobile_display_type."
			content) VALUES (
			'{$in_start}', 
			'{$in_end}', 
			'".date("YmdHis")."', 
			'tem_001', 
			'{$x_size}', 
			'{$y_size}', 
			'{$x_to}', 
			'{$y_to}', 
			'{$scroll_yn}', 
			'{$frame_type}', 
			'{$cookietime}', 
			'{$title}',
			".$mobile_value."
			".$mobile_display_value."
			'{$content}')";
			pmysql_query($sql,get_db_conn());
			$onload="<script>window.onload=function(){ alert('팝업창 등록이 완료되었습니다.'); }</script>";
			$type='';
			$start_date=''; $end_date=''; $design=''; $x_size=''; $y_size=''; $x_to='';
			$y_to=''; $scroll_yn=''; $frame_type=''; $cookietime=''; $title=''; $content='';
//		}
	} else {
		$onload="<script>window.onload=function(){ alert('팝업창 등록은 최대 {$maxcnt}개 까지 등록 가능합니다.'); }</script>";
	}
} else if (($type=="modify_result" || $type=="modify") && ord($num)) {
	$sql = "SELECT * FROM tbleventpopup WHERE num = '{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		pmysql_free_result($result);
		if($type=="modify") {
			$start_date=substr($row->start_date,0,4)."-".substr($row->start_date,4,2)."-".substr($row->start_date,6,2);
			$end_date=substr($row->end_date,0,4)."-".substr($row->end_date,4,2)."-".substr($row->end_date,6,2);
			$design=$row->design;
			$x_size=$row->x_size;
			$y_size=$row->y_size;
			$x_to=$row->x_to;
			$y_to=$row->y_to;
			$scroll_yn=$row->scroll_yn;
			$frame_type=$row->frame_type;
			$cookietime=$row->cookietime;
			$title=$row->title;
			$content=$row->content;
			$is_mobile=$row->is_mobile;
			$mobile_display=$row->mobile_display;
		} else if($type=="modify_result") {
			//$sql = "SELECT COUNT(*) as cnt, COUNT(CASE WHEN frame_type='2' THEN 1 ELSE NULL END) as cnt2 FROM tbleventpopup ";
			//$result = pmysql_query($sql,get_db_conn());
			//$crow=pmysql_fetch_object($result);
			//pmysql_free_result($result);
			//if($row->frame_type!="2" && $frame_type==2 && $crow->cnt2>=1) {
			//	$onload="<script>window.onload=function(){ alert('레이어 타입의 팝업창은 1개만 등록 가능합니다.'); }</script>";
			//} else {
				$sql = "UPDATE tbleventpopup SET ";
				$sql.= "start_date	= '{$in_start}', ";
				$sql.= "end_date	= '{$in_end}', ";
				$sql.= "design		= 'tem_001', ";
				$sql.= "x_size		= '{$x_size}', ";
				$sql.= "y_size		= '{$y_size}', ";
				$sql.= "x_to		= '{$x_to}', ";
				$sql.= "y_to		= '{$y_to}', ";
				$sql.= "scroll_yn	= '{$scroll_yn}', ";
				$sql.= "frame_type	= '{$frame_type}', ";
				$sql.= "cookietime	= '{$cookietime}', ";
				$sql.= "title		= '{$title}', ";
				$sql.= $mobile_update;
				$sql.= $mobile_display_update;
				$sql.= "content		= '{$content}' ";
				$sql.= "WHERE num = '{$num}' ";
				pmysql_query($sql,get_db_conn());
				$onload="<script>window.onload=function(){ alert('팝업창 수정이 완료되었습니다.'); }</script>";
				$type=''; $num='';
				$start_date=''; $end_date=''; $design=''; $x_size=''; $y_size=''; $x_to=''; $is_mobile='';
				$y_to=''; $scroll_yn=''; $frame_type=''; $cookietime=''; $title=''; $content='';
			//}
		}
	} else {
		pmysql_free_result($result);
		$onload="<script>window.onload=function(){ alert('수정하려는 팝업창 정보가 존재하지 않습니다.'); }</script>";
	}
} else if ($type=="delete" && ord($num)) {
	$sql = "SELECT * FROM tbleventpopup WHERE num = '{$num}' ";
	$result = pmysql_query($sql,get_db_conn());
	$rows=pmysql_num_rows($result);
	pmysql_free_result($result);

	if($rows>0) {
		$sql = "DELETE FROM tbleventpopup WHERE num = '{$num}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('해당 팝업창을 삭제하였습니다.'); }</script>";
		$type=''; $num='';
		$start_date=''; $end_date=''; $design=''; $x_size=''; $y_size=''; $x_to=''; $is_mobile='';
		$y_to=''; $scroll_yn=''; $frame_type=''; $cookietime=''; $title=''; $content='';
	}
}

if(ord($start_date)==0) $start_date=date("Y-m-d");
if(ord($end_date)==0) $end_date=date("Y-m-d");

if(ord($type)==0) $type="insert";
$type_name="images/botteon_save.gif";
if($type=="modify" || $type=="modify_result") $type_name="images/btn_edit2.gif";
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="Javascript1.2" src="htmlarea/editor.js"></script>
<script language="JavaScript">
_editor_url = "htmlarea/";

var eventpopupcnt = <?=count($eventpopup)?>;
function ChangeEditer(mode,obj){
	if (mode==form1.htmlmode.value) {
		return;
	} else {
		obj.checked=true;
		editor_setmode('content',mode);
	}
	form1.htmlmode.value=mode;
}

function CheckForm(type) {

	frame_type=false;
	for(i=0;i<document.form1.frame_type.length;i++) {
		if(document.form1.frame_type[i].checked) {
			frame_type=true;
			break;
		}
	}

	if(!frame_type) {
		alert("팝업창 종류를 선택하세요.");
		document.form1.frame_type[0].focus();
		return;
	}
	// 모바일 경우 필요없는 값 설정
	if( $("input[name='is_mobile']").prop('checked') ){
		document.form1.x_to.value = 0;
		document.form1.y_to.value = 0;
		document.form1.x_size.value = 0;
		document.form1.y_size.value = 0;
		document.form1.scroll_yn[0].checked = true;
		document.form1.cookietime[0].checked = true;
	} else {
		if(document.form1.x_to.value.length==0 || document.form1.y_to.value.length==0) {
			alert("팝업창 위치 설정을 하세요.");
			document.form1.x_to.focus();
			return;
		}
		if(!IsNumeric(document.form1.x_to.value)) {
			alert("팝업창 위치 설정값은 숫자만 입력 가능합니다.");
			document.form1.x_to.focus();
			return;
		}
		if(!IsNumeric(document.form1.y_to.value)) {
			alert("팝업창 위치 설정값은 숫자만 입력 가능합니다.");
			document.form1.y_to.focus();
			return;
		}
/*
		if(document.form1.x_size.value.length==0 || document.form1.y_size.value.length==0) {
			alert("팝업창 크기 설정을 하세요.");
			document.form1.x_size.focus();
			return;
		}
		if(!IsNumeric(document.form1.x_size.value)) {
			alert("팝업창 크기 설정값은 숫자만 입력 가능합니다.");
			document.form1.x_size.focus();
			return;
		}
		if(!IsNumeric(document.form1.y_size.value)) {
			alert("팝업창 크기 설정값은 숫자만 입력 가능합니다.");
			document.form1.y_size.focus();
			return;
		}
*/
		
		if(document.form1.scroll_yn[0].checked==false && document.form1.scroll_yn[1].checked==false) {
			alert("스크롤바 설정을 하세요.");
			document.form1.scroll_yn[0].focus();
			return;
		}
		if(document.form1.cookietime[0].checked==false && document.form1.cookietime[1].checked==false && document.form1.cookietime[2].checked==false) {
			alert("팝업창 재표시 기간을 설정하세요.");
			document.form1.cookietime[0].focus();
			return;
		}
	}

	
	if(document.form1.title.value.length==0) {
		alert("팝업창 제목을 입력하세요.");
		document.form1.title.focus();
		return;
	}
	/*
	design=false;
	for(i=eventpopupcnt;i<document.form1.design.length;i++) {
		if(document.form1.design[i].checked) {
			design=true;
			break;
		}
	}
	*/
	/*if(!design) {
		alert("팝업창 템플릿을 선택하세요.");
		return;
	}*/
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.content.value=sHTML;
	if(document.form1.content.value.length==0) {
		alert("팝업창 내용을 입력하세요.");
		document.form1.content.focus();
		return;
	}
	if(type=="modify" || type=="modify_result") {
		if(!confirm("해당 팝업창을 수정하시겠습니까?")) {
			return;
		}
		document.form1.type.value="modify_result";
	} else {
		document.form1.type.value="insert";
	}
	document.form1.submit();
}

function ModeSend(type,num) {
	if(type=="delete") {
		if(!confirm("해당 팝업창을 삭제하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.num.value=num;
	document.form1.submit();
}

function ChangeDesign(tmp) {
	tmp=tmp + eventpopupcnt;
	document.form1["design"][tmp].checked=true;
}
// 모바일 경우 필요없는 매뉴 감추기
$(document).on( 'click', "input[name='is_mobile']", function(){
	if( $(this).prop('checked') ){
		$('.CLS_PcPopUp').hide();
	} else {
		$('.CLS_PcPopUp').show();
	}
});
// 모바일 경우 필요없는 매뉴 감추기
$(document).ready(function(){
	if( $("input[name='is_mobile']").prop('checked') ){
		$('.CLS_PcPopUp').hide();
	}
});

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>팝업 이벤트 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<input type=hidden name=type>
			<input type=hidden name=num value="<?=$num?>">
			<input type=hidden name=htmlmode value='wysiwyg'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">팝업 이벤트 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>이벤트, 긴급공지시 메인페이지 팝업창을 통해 고객에게 이벤트 내용을 알릴 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">팝업창 목록</div>
                </td>
            </tr>
            <tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) &quot;초기화&quot; 버튼 클릭시 제휴사를 통한 방문 접속자가 &quot;0&quot;으로 초기화 됩니다.</li>
                            <li>2) &quot;주문조회&quot; 버튼 클릭시 제휴사를 통하여 방문한 고객의 주문조회를 하실 수 있습니다.</li>
                        </ul>
                    </div>                    
            	</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=50><col width=><col width=75><col width=75><col width=70><col width=70><col width="60"><col width=60><col width=60>
				<TR align=center>
					<th>No</th>
					<th>이벤트 공지창 상단 제목</th>
					<th>시작일</th>
					<th>마감일</th>
					<th>팝업창타입</th>
					<th>등록일</th>
					<th>모바일</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=8;
				$sql = "SELECT num, start_date, end_date, reg_date, frame_type, is_mobile, title FROM tbleventpopup ";
				$sql.= "ORDER BY num DESC ";
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$cnt++;
					$date1 = substr($row->start_date,0,4).".".substr($row->start_date,4,2).".".substr($row->start_date,6,2);
					$date2 = substr($row->end_date,0,4).".".substr($row->end_date,4,2).".".substr($row->end_date,6,2);
					$reg_date = substr($row->reg_date,0,4).".".substr($row->reg_date,4,2).".".substr($row->reg_date,6,2);
					$on_mobile = $row->is_mobile;
					if($row->frame_type==0) $frame_type_name = "<img src=\"images/icon_type3.gif\" border=\"0\">";
					else if($row->frame_type==1)	$frame_type_name = "<img src=\"images/icon_type2.gif\" border=\"0\">";
					else if($row->frame_type==2)	$frame_type_name = "<img src=\"images/icon_type1.gif\" border=\"0\">";
					else if($row->frame_type==3)	$frame_type_name = "<img src=\"images/icon_type4.gif\" border=\"0\">";
					echo "<TR>\n";
					echo "	<TD>{$cnt}</TD>\n";
					echo "	<TD><div class=\"ta_l\">{$row->title}</div></TD>\n";
					echo "	<TD>{$date1}</TD>\n";
					echo "	<TD>{$date2}</TD>\n";
					echo "	<TD>{$frame_type_name}</TD>\n";
					echo "	<TD>{$reg_date}</TD>\n";
					echo "	<TD>{$on_mobile}</TD>\n";
					echo "	<TD><a href=\"javascript:ModeSend('modify','{$row->num}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:ModeSend('delete','{$row->num}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD colspan={$colspan} align=center>등록된 팝업창이 없습니다.</TD></TR>";
				}
?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">팝업창 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<th><span>모바일용 팝업창</span></th>
					<TD class="td_con1">
						<INPUT type="checkbox" name=is_mobile value="Y" <?if($is_mobile=="Y"){echo "checked";}?>>
						<span class="font_orange">＊모바일 에만 팝업창이 뜹니다.</span><br>
						<span class="font_orange">＊모바일의 재표시 기간은 하루동안 열리지 않음입니다.</span><br>
						<span class="font_orange">＊모바일 은 팝업창 위치 및 크기설정이 안됩니다.</span>
					</TD>
				</tr>
				<tr>
					<th><span>모바일용 팝업 사용중지</span></th>
					<TD class="td_con1">
						<INPUT type="checkbox" name=mobile_display value="N" <?if($mobile_display=="N"){echo "checked";}?>>
						<span class="font_orange">＊모바일 에만 팝업창에만 적용됩니다.</span><br>
					</TD>
				</tr>
				<TR>
					<th><span>공지 기간</span></th>
					<TD class="td_con1"><INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=start_date value="<?=$start_date?>" class="input_bd_st01">부터  <INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=end_date value="<?=$end_date?>" class="input_bd_st01">까지&nbsp;&nbsp;<span class="font_orange">＊해당 기간 내에만 팝업창이 뜹니다.</span></TD>
				</TR>
				<TR class='CLS_PcPopUp' >
					<th><span>팝업창 위치 설정</span></th>
					<TD class="td_con1">왼쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_to value="<?=$x_to?>" class="input">픽셀 이동 후, 위쪽에서 <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_to value="<?=$y_to?>" class="input">픽셀 아래로 이동합니다.</TD>
				</TR>
				<TR class='CLS_PcPopUp' style="display:none;" >
					<th><span>팝업창 크기 설정</span></th>
					<TD class="td_con1">
					가로: <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=x_size value="<?=$x_size?>" class="input">픽셀,  &nbsp;
					세로: <INPUT onkeyup="return strnumkeyup(this);" style="PADDING-LEFT: 5px" size=5 name=y_size value="<?=$y_size?>" class="input">픽셀</TD>
				</TR>
				<TR class='CLS_PcPopUp' >
					<th><span>팝업창 종류 선택</span></th>
					<TD class="td_con1">
					<INPUT id=idx_frame_type3 type=radio value=2 name=frame_type <?if($frame_type == 2 || $frame_type == '') echo "checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_frame_type3><B><span class="font_orange">레이어 타입</B></LABEL></span>
					<INPUT id=idx_frame_type4 type=radio value=3 name=frame_type style='display:none' <?if($frame_type == 3) echo "checked";?>><LABEL style='display:none' onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_frame_type4><B><span class="font_orange">플로팅 타입</B></LABEL></span>
					</TD>
				</TR>
				<tr class='CLS_PcPopUp' >
					<th><span>스크롤바 설정</span></th>
					<TD class="td_con1">
					<INPUT id=idx_scroll_yn1 type=radio value=Y name=scroll_yn <?php if($scroll_yn=="Y")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_yn1>스크롤을 허용함</LABEL> &nbsp;&nbsp;
					<INPUT id=idx_scroll_yn2 type=radio value=N name=scroll_yn <?php if($scroll_yn=="N")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_scroll_yn2>스크롤바 허용하지 않음</LABEL><BR><span class="font_orange">＊스크롤을 허용하지 않는 경우, 팝업창 크기보다 내용이 많으면 고객이 보지 못할 수 있습니다.</span>
					</TD>
				</tr>
				<tr class='CLS_PcPopUp' >
					<th><span>팝업창 재표시 기간</span></th>
					<TD class="td_con1">
					<INPUT id=idx_cookietime1 type=radio value=1 name=cookietime <?php if($cookietime=="1")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_cookietime1>하루동안 열리지 않음</LABEL>&nbsp;&nbsp;
					<INPUT id=idx_cookietime2 type=radio value=2 name=cookietime <?php if($cookietime=="2")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_cookietime2>다시 열지 않음</LABEL>&nbsp;&nbsp;
					<INPUT id=idx_cookietime3 type=radio value=0 name=cookietime <?php if($cookietime=="0")echo"checked";?>><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand" onmouseout="style.textDecoration='none'" for=idx_cookietime3>팝업창 브라우저 종료시</LABEL>
					</TD>
				</tr>
				<tr>
					<th><span>팝업창 제목</span></th>
					<TD class="td_con1"><INPUT style="WIDTH: 100%" name=title value="<?=$title?>" class="input"></TD>
				</tr>
				<tr>
					<th colspan="2">
					<div class="table_none" style='display:none;'>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="100%" >
						<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
						<tr>
							<td width="100%">
							<div class="table_none">
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
								<TR>
									<TD><div class="point_title">템플릿 선택</div></TD>
								</TR>
								<TR>
									<TD width="100%" style="padding:0pt;">
									<TABLE cellSpacing=0 cellPadding="5" width="100%" border=0>
									<TR>
										<TD width="44" height="160" align=right valign="middle"><img src="images/btn_back.gif" border="0" onMouseover='moveright()' onMouseout='clearTimeout(righttime)' style="cursor:hand;"></TD>
										<TD width="100%" height="160">					
										<table width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr height=230>
											<td id=temp style="visibility:hidden;position:absolute;top:0;left:0">
<?php
											echo "<script>";
											$jj=0;
											$menucontents = "";
											$menucontents .= "<table border=0 cellpadding=0 cellspacing=0><tr>";
											for($i=0;$i<1;$i++) {
												echo "thisSel = 'dotted #FFFFFF';";
												$menucontents .= "<td width=173 align=center><input type=radio name='design' value='{$eventpopup[$i]}'  checked";
												$menucontents .= "><br><img src='images/sample/event{$eventpopup[$i]}.gif' border=0 style='border-width:1pt; border-color:#FFFFFF; border-style:solid;' hspace=5 onMouseOver='changeMouseOver(this);' onMouseOut='changeMouseOut(this,thisSel);' style='cursor:hand;' onclick='ChangeDesign({$i});'></td>";
												$jj++;
											}
											$menucontents .= "</tr></table>";
											echo "</script>";
?>  

											<script language="JavaScript1.2">
											<!--
											function changeMouseOver(img) {
												 img.style.border='1 solid #0c71c6';
											}
											function changeMouseOut(img,dot) {
												 img.style.border="1 "+dot;
											}

											var menuwidth=1000
											var menuheight=230
											var scrollspeed=10
											var menucontents="<nobr><?=$menucontents?></nobr>";
											
											var iedom=document.all||document.getElementById
											if (iedom)
												document.write(menucontents)
											var actualwidth=''
											var cross_scroll, ns_scroll
											var loadedyes=0
											function fillup(){
												if (iedom){
													cross_scroll=document.getElementById? document.getElementById("test2") : document.all.test2
													cross_scroll.innerHTML=menucontents
													actualwidth=document.all? cross_scroll.offsetWidth : document.getElementById("temp").offsetWidth
												}
												else if (document.layers){
													ns_scroll=document.ns_scrollmenu.document.ns_scrollmenu2
													ns_scroll.document.write(menucontents)
													ns_scroll.document.close()
													actualwidth=ns_scroll.document.width
												}
												loadedyes=1
											}
											window.onload=fillup
											
											function moveleft(){
												if (loadedyes){
													if (iedom&&parseInt(cross_scroll.style.left)>(menuwidth-actualwidth)){
														cross_scroll.style.left=parseInt(cross_scroll.style.left)-scrollspeed
													}
													else if (document.layers&&ns_scroll.left>(menuwidth-actualwidth))
														ns_scroll.left-=scrollspeed
												}
												lefttime=setTimeout("moveleft()",50)
											}
											
											function moveright(){
												if (loadedyes){
													if (iedom&&parseInt(cross_scroll.style.left)<0)
														cross_scroll.style.left=parseInt(cross_scroll.style.left)+scrollspeed
													else if (document.layers&&ns_scroll.left<0)
														ns_scroll.left+=scrollspeed
												}
												righttime=setTimeout("moveright()",50)
											}
											
											if (iedom||document.layers){
												with (document){
													write('<td valign=top>')
													if (iedom){
														write('<div style="position:relative;width:'+menuwidth+';">');
														write('<div style="position:absolute;width:'+menuwidth+';height:'+menuheight+';overflow:hidden;">');
														write('<div id="test2" style="position:absolute;left:0">');
														write('</div></div></div>');
													}
													else if (document.layers){
														write('<ilayer width='+menuwidth+' height='+menuheight+' name="ns_scrollmenu">')
														write('<layer name="ns_scrollmenu2" left=0 top=0></layer></ilayer>')
													}
													write('</td>')
												}
											}
											//-->
											</script>
											</td>
										</tr>
										</table>
										</td>
										<TD width="27" height="160"><img src="images/btn_next.gif" border="0" onMouseover='moveleft()' onMouseout='clearTimeout(lefttime)' style="cursor:hand;"></TD>
									</TR>
									</TABLE>
									</TD>
								</TR>
							</TABLE>
							</div>
							</td>
						</tr>
						</table>
						</div>
						</th>
					</tr>
					</table>
					</div>

					</TD>
				</tr>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td class="bd_editer">
					<table cellpadding="0" cellspacing="0" width="100%">
					
					<tr>
						<td width="100%"><TEXTAREA style="DISPLAY: yes; WIDTH: 100%" name=content rows="17" id="ir1" wrap=off><?=$content?></TEXTAREA></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="pt_20" align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="<?=$type_name?>" border="0"></a></td>
			</tr>
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>팝업창 사용가이드</span></dt>
							<dd>
								- 팝업창은 최대 10개 까지 등록 가능합니다. 
                            <br>- 팝업창 종류중 &quot;레이어 	타입&quot; 팝업창은 1개만 등록 가능합니다.
                            <br>- 팝업창 크기는 340*400을 권장합니다. 이보다 크거나 작을 경우 디자인 템플릿과 정확히 맞지 않을 수 있습니다.
                            <br>- 웹편집기 (드림위버, 나모웹에디터 등)로 작성 후 붙혀넣기로 할때는 이미지 경로에 유의하시기 바랍니다.
                            <br>- 제목에는 가급적 HTML코드를 사용하지 마세요.
                            <br>- 팝업창 위치는 다중 팝업창을 띄우는 경우 창 위치가 겹치지 않도록 위치를 각각 조절하시기 바랍니다.<br>
							</dd>
							
						</dl>
						<dl>
							<dt><span>팝업창 하단 닫기 부분 입력폼</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[CHECK]</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-top-color:silver; border-top-style:solid;" width="100%">체크박스를 표시하는 태그입니다.</TD>
								</TR>
								<TR>
									<TD class="table_cell" style="padding-right:15px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" noWrap align=right width=150 bgColor=#f0f0f0 height="27">[CLOSE]</TD>
									<TD class="td_con1" style="padding-left:5px; border-top-width:1pt; border-bottom-width:1pt; border-top-color:rgb(222,222,222); border-bottom-color:silver; border-top-style:solid; border-bottom-style:solid;" width="100%">팝업창을 닫는 태그입니다. 예) 창 닫기 &lt;a href=[CLOSE]&gt;[닫기]&lt;/a&gt;</TD>
								</TR>
								</TABLE>
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	}, 
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

</script>

<?=$onload?>
<?php 
include("copyright.php");
