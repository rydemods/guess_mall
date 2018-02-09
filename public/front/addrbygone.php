<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

if(ord($_ShopInfo->getMemid())==0) {
	exit;
}
?>
<html>
<head>
<title>과거 배송지 검색</title>
<meta http-equiv="CONTENT-TYPE" content="text/html;charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<style>
td	{font-family:"굴림,돋움";color:#4B4B4B;font-size:12px;line-height:17px;}
BODY,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:Tahoma;color:000000;font-size:9pt;}

A:link    {color:#635C5A;text-decoration:none;}
A:visited {color:#545454;text-decoration:none;}
A:active  {color:#5A595A;text-decoration:none;}
A:hover  {color:#545454;text-decoration:underline;}
.input{font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}
.select{color:#444444;font-size:12px;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}
</style>
<link rel="stylesheet" href="../css/sub.css" />
<link rel="stylesheet" href="../css/common.css" />
<SCRIPT LANGUAGE="JavaScript">
<!--
window.resizeTo(476,350);

function choice_addr(post,addr1,addr2) {
	opener.document.form1.rpost1.value=post.substring(0,3);
	opener.document.form1.rpost2.value=post.substring(4,7);
	opener.document.form1.raddr1.value=addr1;
	opener.document.form1.raddr2.value=addr2;
	window.close();
}
//-->
</SCRIPT>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" marginheight="0" marginwidth="0" oncontextmenu="return false;" onload="window.resizeTo(612,590);">

<div class="popup_def_wrap" style="width:100%">
	<div class="title_wrap">
		<p class="title">과거 배송지 목록</p>
		<a href="javascript:window.close();" class="btn_close"></a>
	</div>

	<div style="padding:0px 10px">
		
		<table width="100%" class="th_top">
			<caption style="text-align:left; font-size:12px; color:#333; padding:15px 0px 5px">* 과거 배송지내역 입니다</caption>
			<col width=60></col><col width=></col><col width=50></col>
			<tr>
				<th>우편번호</th>
				<th>배송지 주소</th>	
				<th>선택</th>
			</tr>
	<?php 
			$sql = "SELECT receiver_addr FROM tblorderinfo WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "GROUP BY receiver_addr ";
			$result=pmysql_query($sql,get_db_conn());
			$cnt=0;
			while($row=pmysql_fetch_object($result)) {
				$cnt++;
				$receiver_addr = str_replace(array("\n","\r")," ",trim($row->receiver_addr));
				$pos=strpos($receiver_addr,"주소");
				if($pos>0) {
					$post = trim(substr($receiver_addr,0,$pos));
					$receiver_addr = substr($receiver_addr,$pos+7);
					$arrayaddr = explode("  ",$receiver_addr);
					$count=count($arrayaddr);
					if($count>1) {
						$addr1=$arrayaddr[0];
						$addr2=" ";
						for($i=1;$i<$count;$i++) $addr2.=$arrayaddr[$i]." ";
						if(strlen($addr2)>1) $addr2=rtrim($addr2);
					} else {
						$arrayaddr = explode(" ",$receiver_addr);
						$count=count($arrayaddr);
						if($count>3) {
							$addr1=$arrayaddr[0]." {$arrayaddr[1]} ".$arrayaddr[2];
							$addr2=" ";
							for($i=3;$i<$count;$i++) $addr2.=$arrayaddr[$i]." ";
							if(strlen($addr2)>1) $addr2=rtrim($addr2);
						} else {
							$addr1=$receiver_addr;
							$addr2=" ";
						}
					}
				}
				$post = str_replace("우편번호 : ","",$post);
				$addr1=trim(str_replace(":", "", $addr1));
				$addr2=trim($addr2);

				echo "<tr align=\"center\" height=\"25\">\n";
				echo "	<td><A HREF=\"javascript:choice_addr('{$post}','{$addr1}','{$addr2}')\"><U><font color=\"#333333\">{$post}</font></U></A></td>\n";
				echo "	<td class=\"ta_l\"><A HREF=\"javascript:choice_addr('{$post}','{$addr1}','{$addr2}')\"><font color=\"#333333\">{$addr1} {$addr2}</font></a></td>\n";
				echo "	<td><a href=\"javascript:choice_addr('{$post}','{$addr1}','{$addr2}');\" class=\"btn_util\">선택</a></td>\n";
				echo "</tr>\n";
			}
			if($cnt==0) {
				echo "<tr height=\"30\">\n";
				echo "	<td colspan=\"3\" align=\"center\">검색된 과거 배송지가 없습니다.</td>\n";
				echo "</tr>\n";
				echo "<tr><td height=\"1\" colspan=\"3\" bgcolor=\"#DDDDDD\"></td></tr>\n";
			}
	?>
			</table>
		</div>

	<div class="btn_area"><a href="javascript:window.close();" class="gray">창닫기</a></div>

</div><!-- //div.popup_def_wrap -->
</body>
</html>
