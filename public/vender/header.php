<?
	//header("Location: http://" . $_SERVER['HTTP_HOST'] . "/admin/" );
?>
<html>
<head>
<title>관리자 페이지</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="stylesheet" href="style.css">
</head>
<body topmargin=0 leftmargin=1 rightmargin=0 marginheight=0 marginwidth=0>
<table border=0 cellpadding=0 cellspacing=0 width=100% align=center style="table-layout:fixed">
<tr><td bgcolor=red height=1></td></tr>
<tr><td bgcolor=#FEC5C0 height=2></td></tr>
<tr>
	<td align="center">
	<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
	<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
	<!-- <col width=920></col>
	<col width=80></col> -->
	<tr id="loginfo">
		<td align=right>
		<A HREF="logout.php">로그아웃</A> | <A HREF="vender_info.php">정보변경</A>
		</td>
	</tr>
	</table
	</td>
</tr>
<tr><td height=10></td></tr>
<tr>
	<td align="center">
	<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
	<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
	<col width=></col>
	<tr>
		<td style="padding:0,10,0,10;background-repeat:no-repeat;">
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td height=100 align=center style="font-size:30px;"><A HREF="main.php"><B><?=$_venderdata->brand_name?></B></A></td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td height=5></td>
</tr>
<tr>
	<td valign="top" align="center">
