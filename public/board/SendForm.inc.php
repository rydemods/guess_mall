<?php
function GetHeader() {
	$get_result = "
		<HTML>
		<HEAD>
		<TITLE></TITLE>
		</HEAD>
		<style type='text/css'>
		A:link {font-family:굴림,Arial; color: #CC9900; text-decoration: none;}
		A:active {font-family: 굴림,Arial; color: peru; text-decoration: none;}
		A:visited {font-family: 굴림,Arial; color: #CC9900; text-decoration: none;}
		A:hover {font-family: 굴림,Arial; color: #006600; text-decoration:underline;}

		.text {font-family: 굴림,Arial; font-size: 9pt; color:#336666;line-height:15pt;}
		.centertext {font-family: 굴림,Arial; font-size: 9pt; color:#336666;line-height:13pt; text-align:center}
		.mnufont {font-family: 굴림,Arial; font-size: 10pt; color:#336666;line-height:15pt;}


		td {font-family: 굴림,Arial; font-size: 9pt; color:#565656;line-height:12pt}
		</style>
		<body>
	";

	return $get_result;
}

function GetFooter() {
	$get_result = "
		<table border=0 align=center>
		<TR>
			<TD align=middle  height=20 vAlign=center width=630></TD>
		</TR>
		</table>

		</body>
		</HTML>
	";

	return $get_result;
}

function GetContent($b_name, $b_email, $b_subject, $b_memo, $b_date, $b_filename, $b_title) {
	global $_ShopInfo, $board, $view_divider,$view_left_header_color,$view_body_color;
	if ($b_email) {
		$b_name = "<a href='mailto:".$b_email."'>".$b_name."</a>";
	}
	if($b_filename) {
		$b_file="<img src=\"http://".$_ShopInfo->getShopurl().DataDir."shopimages/board/".$board."/".$b_filename."\" border=0><br>";
	}

	$get_result = "
			<TABLE border=0 cellspacing=0 cellpadding=0 width='640' bgcolor=white>
			<TR height=1><TD colspan=2 bgcolor=$view_divider><img width=0 height=1></TD></TR>
			<TR>
				<TD height=23 align=right width=100 bgcolor=$view_left_header_color style='word-break:break-all;'><b>Board Title&nbsp;&nbsp;</b></TD>
				<TD><img width=0 height=3><br>&nbsp;&nbsp; $b_title</TD>
			</TR>

			<TR><TD bgcolor=#ffffff height=1 colspan=2><img width=0 height=1></TD></TR>

			<TR>
				<TD height=23 align=right width=100 bgcolor=$view_left_header_color><img border=0 width=100 height=0><br>
				  <b>Name&nbsp;&nbsp;</b></TD>
				<TD align=left width=100%>
				<TABLE border=0 cellpadding=0 cellspacing=0>
				<TR><TD><img width=0 height=3></TD></TR>
				<TR><TD>&nbsp;&nbsp;</TD><TD>$b_name</TD>
				</TR>
				</TABLE>
				</TD>
			</TR>

			<TR><TD bgcolor=#ffffff height=1 colspan=2><img width=0 height=1></TD></TR>

			<TR>
				<TD height=23 align=right width=100 bgcolor=$view_left_header_color style='word-break:break-all;'><b>Date&nbsp;&nbsp;</b></TD>
				<TD><img width=0 height=3><br>&nbsp;&nbsp; <font style=font-size:8pt;font-family:Tahoma;font-weight:normal>$b_date</font></TD>
			</TR>

			<TR><TD bgcolor=#ffffff height=1 colspan=2><img width=0 height=1></TD></TR>

			<TR>
				<TD height=23 align=right width=100 bgcolor=$view_left_header_color style='word-break:break-all;'><b>Subject&nbsp;&nbsp;</b></TD>
				<TD><img width=0 height=3><br>&nbsp;&nbsp;$b_subject</TD>
			</TR>

			<TR><TD bgcolor=$view_divider height=1 colspan=2><img width=0 height=1></TD></TR>

			</TABLE>

			<TABLE border=0 cellspacing=0 cellpadding=10 width=640>
			<TR>
				<TD style='word-break:break-all;' bgcolor=$view_body_color height=100 valign=top> 
				<span style=line-height:160%>
				$b_file
				$b_memo
				</span>
				</TD>
			</TR>
			</TABLE>

			<TABLE border=0 cellspacing=0 cellpadding=0 width='640' bgcolor=white>
			<TR height=1><TD colspan=2 bgcolor=$view_divider><img width=0 height=1></TD></TR>
			</TABLE>
	";

	return $get_result;
}
