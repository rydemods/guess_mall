<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$sql = "SELECT * FROM tblsurveymain WHERE display='Y' ";
$sql.= "ORDER BY survey_code DESC LIMIT 1 ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);
$choice=array(1=>&$row->survey_select1,&$row->survey_select2,&$row->survey_select3,&$row->survey_select4,&$row->survey_select5);
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function poll_result(type,code) {
	if(type=="result") {
		k=0;
		for (i=0;i<document.poll_form.poll_sel.length;i++) {
			if(document.poll_form.poll_sel[i].checked) {
				url="<?=$Dir.FrontDir?>survey.php?type=result&survey_code="+code+"&val="+document.poll_form.poll_sel[i].value;
				k=1;
			}
		}
		if (k==1) {
			window.open(url,"survey","width=450,height=400,scrollbars=yes");
		} else {
			alert ("투표하실 항목을 선택해 주세요");return;
		}
	} else {
		window.open ("<?=$Dir.FrontDir?>survey.php?type=view&survey_code="+code,"survey","width=450,height=400,scrollbars=yes"); 
	}
}

//-->
</SCRIPT>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr>
	<td class="mainpoll" style="padding-left:3;padding-right:3"><B><?=$row->survey_content?></B></td>
</tr>
<form name=poll_form method=post>
<tr>
	<td align=center style="padding:5">
	<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<col width=10></col>
	<col width=></col>
<?
	for($i=1;$i<=count($choice);$i++) {
		if(strlen($choice[$i])>0) {
			echo "<tr>\n";
			echo "	<td><input type=radio id=\"idx_poll_sel".$i."\" name=poll_sel value=\"".$i."\"></td>\n";
			echo "	<td class=\"mainpoll\"><label style='cursor:hand;' onmouseover=\"style.textDecoration='underline'\" onmouseout=\"style.textDecoration='none'\" for=idx_poll_sel".$i.">".$choice[$i]."</label></td>\n";
			echo "</tr>\n";
		}
	}
?>
	</table>
	</td>
</tr>
<tr>
	<td align=center style="padding-top:5">
	<A HREF="javascript:poll_result('result','<?=$row->survey_code?>')"><img src="<?=$Dir?>images/survey/poll_bt01.gif" border=0></A>
	&nbsp;
	<A HREF="javascript:poll_result('view','<?=$row->survey_code?>')"><img src="<?=$Dir?>images/survey/poll_bt02.gif" border=0></A>
	</td>
</tr>
</form>
</table>