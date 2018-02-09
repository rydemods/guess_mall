<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
$mode=$_POST["mode"];
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>엑셀파일 업로드</title>
<link rel="stylesheet" href="style.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;
function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}

	if(ekey==13) {
		excel_submit();
		return false;
	}
}

function PageResize() {
	var oWidth = 450;
	var oHeight = document.all.table_body.clientHeight + 120;
	//var oHeight = 300;

	window.resizeTo(oWidth,oHeight);
}

function excel_submit() {
	if(document.form1.cvs_file.value=='') {
		alert("엑셀파일(CSV) 선택하세요.");
		document.form1.search.focus();
		return;
	}
	document.form1.submit();
}
//-->
</SCRIPT>
</head>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>엑셀파일 업로드</title>
<link rel="stylesheet" href="style.css" type="text/css">

<div class="pop_top_title"><p>엑셀파일 업로드</p></div>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">

<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="98%">
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
	<input type=hidden name=mode value="result">
	<input type=hidden name=formname value="<?=$formname?>">
	<?php if ($mode != 'result') {?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">엑셀파일(CSV) 업로드후 확인을 클릭하세요.</span> <a href='./sample/issue_member.sample.csv'>[엑셀샘플 다운로드 ]</a></td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr align=center>
			<td><INPUT type=file size="24" name=cvs_file style="WIDTH:368px;height:22px"></td>
			<td width="40" align=right><a href="javascript:excel_submit();"><img src="images/btn_ok3.gif" border="0" valign=top></a></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;"><hr size="1" align="center" color="#EBEBEB"></td>
	</tr>
	<?} else {?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:5pt;"><b><font color="black">회원내역</b>(회원내역을 확인하실수 있습니다.)</font></td>
	</tr>
	<tr>
		<td>
        <div class="table_style02">
		<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
		<tr>
			<th>번호</th>
			<th>아이디</th>
			<th>그룹코드</th>
			<th>제한시작일</th>
			<th>제한종료일</th>
		</tr>
<?php

	// 등급별 정보
	$g_sql = "SELECT  group_code, group_name FROM tblmembergroup  ORDER BY group_code";
	$g_ret = pmysql_query($g_sql);
	$grade = array();
	while($g_row = pmysql_fetch_object($g_ret)) {
		$grade[$g_row->group_code] = $g_row;
	}
	pmysql_free_result($g_ret);

	$lineNumber	= 1;
	if($_FILES['cvs_file'][tmp_name]){
		$fp = fopen( $_FILES['cvs_file'][tmp_name], 'r' );
		while ( $record = fgetcsv( $fp, 135000, ',' ) ){
			//if ($lineNumber > 0) {
				$kp_id				= $record[0];
				$kp_af_group		= $record[1];
				$kp_s_date			= $record[2];
				$kp_e_date			= $record[3];
				echo "<tr>\n";
				echo "	<td width=100 style='text-align:center;'>{$lineNumber}</td>\n";
				echo "	<td style='text-align:left;padding-left:5px'><span class=\"font_blue\"><B>{$kp_id}</B></span></td>\n";
				echo "	<td style='text-align:left;padding-left:5px'>{$kp_af_group}</td>\n";
				echo "	<td style='text-align:left;padding-left:5px'>{$kp_s_date}</td>\n";
				echo "	<td style='text-align:left;padding-left:5px'>{$kp_e_date}</td>\n";
				echo "</tr>\n";
			//}
				echo "<tr>\n";
				echo "	<td colspan=5 width=100 style='text-align:left;'>";
				$k_query = "INSERT INTO tblmembergroup_keep ( id, group_code, s_date, e_date ) VALUES ( '".$kp_id."', '".$kp_af_group."', '".$kp_s_date."', '".$kp_e_date."' )";		
				echo $k_query."<br>";
				//pmysql_query($k_query,get_db_conn());

				list($kp_bf_group)=pmysql_fetch_array(pmysql_query("select group_code from tblmember where id='".$kp_id."'"));
				
				if($kp_bf_group && ($kp_bf_group != $kp_af_group)) {
					// =========================================================================
					// 등급 갱신 및 히스토리 저장
					// =========================================================================
					$u_query = "update tblmember set group_code = '".$kp_af_group."' where id='".$kp_id."'";
					echo $u_query."<br>";
					//pmysql_query( $u_query, get_db_conn() );

					$h_query = "insert into tblmemberchange 
								(mem_id, before_group, after_group, accrue_price, change_date) 
								values 
								('".$kp_id."', '".$grade[$kp_bf_group]->group_name."', '".$grade[$kp_af_group]->group_name."', '0', '".date("Y-m-d")."')
								"; 
					echo $h_query."<br>";
					//pmysql_query( $h_query, get_db_conn() );
				}

			$lineNumber++;
			echo "</td>\n";
			echo "</tr>\n";
		}
		fclose( $handle );
	}
?>								
		</table>
        </div>
		</td>
	</tr>
	<?php }?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center>
		<?if ($lineNumber > 0) {?><a href="javascript:selectid();"><img src="images/btn_input.gif"border="0" vspace="2" border=0></a>&nbsp;<?}?><a href="javascript:window.close()"><img src="images/btn_close.gif"border="0" vspace="2" border=0></a>
	</TD>
</TR>
</form>
</TABLE>
</body>
</html>
