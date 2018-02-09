<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

if(ord($_ShopInfo->getId())==0){
	echo "<script>alert('정상적인 경로로 접근하시기 바랍니다.');window.close();</script>";
	exit;
}

$CurrentTime = time();

extract($_REQUEST);


if ($mode == "add") {
	$subject = addslashes($subject);
	$comment = addslashes($comment);
	$duedate = $year.$month.$day;
	$duetime = $time;
	if ($loop) {
		if ($loop == "day") {
			for($i=0;$i<$loopnum;$i++) {
				if ($i > 0) {
					$timestamp = strtotime("$year-$month-$day +{$i} day");
					$duedate = date("Ymd",$timestamp);
				}
				$sql = "INSERT INTO tblschedule(import,rest,subject,comment,duedate,duetime,date) VALUES ('{$import}','{$rest}', 
				'{$subject}','{$comment}','{$duedate}','{$duetime}','{$CurrentTime}')";
				$insert = pmysql_query($sql,get_db_conn());
			}
		} else if ($loop == "week") {
			for($i=0;$i<$loopnum;$i++) {
				if ($i > 0) {
					$timestamp = strtotime("$year-$month-$day +{$i} week");
					$duedate = date("Ymd",$timestamp);
				}
				$sql = "INSERT INTO tblschedule(import,rest,subject,comment,duedate,duetime,date) VALUES ('{$import}','{$rest}','{$subject}', 
				'{$comment}','{$duedate}','{$duetime}','{$CurrentTime}')";
				$insert = pmysql_query($sql,get_db_conn());
			}
		} else if ($loop == "month") {
			$tmpYear = $year;
			$tmpMonth = $month;
			$tmpDay = $day;
			for($i=0;$i<$loopnum;$i++) {
				if ($i > 0) {
					$tmpNum = get_totaldays($tmpYear,$tmpMonth);
					$timestamp = strtotime("$tmpYear-$tmpMonth-$tmpDay +{$tmpNum} day");
					$duedate = date("Ymd",$timestamp);
					$tmpYear = date("Y",$timestamp);
					$tmpMonth = date("m",$timestamp);
					$tmpDay = date("d",$timestamp);
				}
				$sql = "INSERT INTO tblschedule(import,rest,subject,comment,duedate,duetime,date) VALUES ('{$import}','{$rest}','{$subject}', 
				'{$comment}','{$duedate}','{$duetime}','{$CurrentTime}')";
				$insert = pmysql_query($sql,get_db_conn());
			}
		}
		echo "<script>alert('일정이 추가되었습니다.');opener.location.reload();top.close();</script>";
		exit;
	} else {
		$sql = "INSERT INTO tblschedule(import,rest,subject,comment,duedate,duetime,date) VALUES('{$import}','{$rest}','{$subject}', 
		'{$comment}','{$duedate}','{$duetime}','{$CurrentTime}')";
		$insert = pmysql_query($sql,get_db_conn());

		if ($insert) {
			echo "<script>alert('일정이 추가되었습니다.');opener.location.reload();top.close();</script>";
			exit;
		} else {
			alert_go('일정 추가중 오류가 발생하였습니다.',-1);
		}
	}
}

if (!$year) $year = date("Y");
if (!$month) $month = date("m");
if (!$day) $day = date("d");

$month = sprintf("%02d",$month);
$day = sprintf("%02d",$day);

$inputY = $year;
$inputM = $month;

$totaldays = get_totaldays($inputY,$inputM);

if ($totaldays <= 0) {
	echo "<script>alert('날짜 선택이 잘못되었습니다.');top.close();</script>";
	exit;
}
?>
<html>
<head>
<title>스케쥴러</title>
<meta http-equiv='content-type' content='text/html; charset=utf-8'>
<LINK rel="stylesheet" type="text/css" href="style.css">
<SCRIPT LANGUAGE="JavaScript">
<!--
function PageResize() {
	var oWidth = 410;
	var oHeight = 370;

	window.resizeTo(oWidth,oHeight);
}

function form_submit(thisform) {

	if (thisform.subject.value=='') {
		alert('제목을 입력하세요');
		thisform.subject.focus();
		return false;
	}

	if (thisform.comment.value=='') {
		alert('내용을 입력하세요');
		thisform.comment.focus();
		return false;
	}

	return true;

}
//-->
</SCRIPT>
</head >
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="pop_top_title"><p>스케줄 입력하기</p></div>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 style="overflow-x:hidden;" onLoad="PageResize();">
<form action='<?=$_SERVER['PHP_SELF']?>' method='post' onSubmit="return form_submit(this)">
<input type='hidden' name='mode' value='add'>
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
<tr>
<TD style="padding:10pt;">
	<div class="table_style01">
	<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
	<TR>
		<th width="20"><span>날짜</span></th>
		<td width="390" class="td_con1">
		<SELECT name=year size="1" class="select">
<?php
		for($y=2000;$y<=date("Y")+5;$y++) {
			$select='';
			if ($y == $year) $select = "selected";
			echo "<option value='{$y}' {$select}>{$y} 년</option>";
		}
?>
		</SELECT>
		<SELECT name=month class="select">
<?php
		for($y=1;$y<=12;$y++) {
			$select='';
			$yn = sprintf("%02d",$y);
			if ($yn == $month) $select = "selected";
			echo "<option value='{$yn}' {$select}>{$yn} 월</option>";
		}
?>
		</SELECT>
		<SELECT name=day class="select">
<?php
		for($y=1;$y<=$totaldays;$y++) {
			$select='';
			$yn = sprintf("%02d",$y);
			if ($yn == $day) $select = "selected";
			echo "<option value='{$yn}' {$select}>{$yn} 일</option>";
		}
?>
		</SELECT>
		<SELECT name=time class="select">
		<option value='25'>시간미지정</option>
		<option value='6'>6 시 AM</option>
		<option value='7'>7 시 AM</option>
		<option value='8'>8 시 AM</option>
		<option value='9'>9 시 AM</option>
		<option value='10'>10 시 AM</option>
		<option value='11'>11 시 AM</option>
		<option value='12'>12 시 AM</option>
		<option value='13'>1 시 PM</option>
		<option value='14'>2 시 PM</option>
		<option value='15'>3 시 PM</option>
		<option value='16'>4 시 PM</option>
		<option value='17'>5 시 PM</option>
		<option value='18'>6 시 PM</option>
		<option value='19'>7 시 PM</option>
		<option value='20'>8 시 PM</option>
		<option value='21'>9 시 PM</option>
		<option value='22'>10 시 PM</option>
		</SELECT>			 
		</TD>
	</TR>
	<TR>
		<th><span>제목</span></span>
		<td class="td_con1"><INPUT class="input" maxLength=12 size=15 name="subject" style="width:100%"> </TD>
	</TR>
	<TR>
		<th><span>내용</span></th>
		<td class="td_con1"><textarea rows="3" class="textarea" style=width:100% name="comment"></textarea></TD>
	</TR>
	<TR>
		<td class="td_con1" colspan="2" align="center" style="border-left:1px solid #b9b9b9;">
		<SELECT name=import class="select">
		<option selected value='N'>일반일정</option>
		<option value='Y'>중요일정</option>
		</SELECT>			 
		<SELECT name=rest class="select">
		<option selected value='N'>비공휴일</option>
		<option value='Y'>공휴일지정</option>
		</SELECT>
		<SELECT name=loop class="select">
		<option selected value=''>반복없음</option>
		<option value='day'>일단위</option>
		<option value='week'>주단위</option>
		<option value='month'>월단위</option>
		</SELECT>			 
		<SELECT name=loopnum class="select">
		<option value='1'>1 번</option>
		<option value='2'>2 번</option>
		<option value='3'>3 번</option>
		<option value='4'>4 번</option>
		<option value='5'>5 번</option>
		<option value='6'>6 번</option>
		<option value='7'>7 번</option>
		<option value='8'>8 번</option>
		<option value='9'>9 번</option>
		<option value='10'>10 번</option>
		</SELECT>
		</td>
	</TR>
	</TABLE>	
    </th>			
	</TD>
</tr>
<TR>
	<TD align="center"><input type="image" src="images/btn_ok1.gif" border="0" vspace="0" border="0"><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="0" border="0" hspace="2"></a></TD>
</TR>
</TABLE>

</form>
</body>
</html>
