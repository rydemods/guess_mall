<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

if(is_array($_layerdata)) {	//상단 이벤트 팝업에서 이미 쿼리를 하였다.
?>
	<script language="javascript">
	var AllList=new Array();

	function LayerList() {
		var argv = LayerList.arguments;   
		var argc = LayerList.arguments.length;
		
		this.classname		= "LayerList";
		this.debug			= false;
		this.id				= new String((argc > 0) ? argv[0] : "");
		this.val			= new String((argc > 1) ? argv[1] : "");
		this.time			= new String((argc > 2) ? argv[2] : "");
	}

	function p_windowclose(pID, bSetCookie) {
		for(i=0;i<AllList.length;i++){
			if(pID==AllList[i].id) {
				document.all[pID].style.visibility="hidden";
				if (bSetCookie=="1"){
					expire = new Date();
					if(parseInt(AllList[i].time)==2) {
						expire.setTime(Date.parse(expire) + 1000*60*60*24*30);
						document.cookie = AllList[i].id + "=" + escape(AllList[i].val) + ";expires=" + expire.toGMTString() + ";path=/<?=RootPath?>;";
					} else if(parseInt(AllList[i].time)==1) {
						expire.setTime(Date.parse(expire) + 1000*60*60*24*parseInt(AllList[i].time));
						document.cookie = AllList[i].id + "=" + escape(AllList[i].val) + ";expires=" + expire.toGMTString() + ";path=/<?=RootPath?>;";
					} else {
						document.cookie = AllList[i].id + "=" + escape(AllList[i].val) + ";path=/<?=RootPath?>;";
					}
				}
				break;
			}			
		}
	}

	function p_windowopen(pID, pVal, pTime){
		if(pVal!=getCookie(pID)) {
			layerlist=new LayerList();
			layerlist.id=pID;
			layerlist.val=pVal;
			layerlist.time=pTime;
			AllList[AllList.length]=layerlist;
			layerlist=null;

			document.all[pID].style.visibility="visible";
		}
	}
	</script>
<?
	$layer_str="";
	for($i=0;$i<count($_layerdata);$i++) {
		if($_layerdata[$i]->frame_type=="2") {
			$cookiename="eventpopup_".$_layerdata[$i]->num;
			if($_layerdata[$i]->end_date!=$_COOKIE[$cookiename]) {
				$row=$_layerdata[$i];
				$layer="Y";
				$one=2;
				$layer_str.= "p_windowopen('".$cookiename."','".$_layerdata[$i]->end_date."','".$_layerdata[$i]->cookietime."');\n";
				$layer_str.="Drag.init($(\"eventlayer-top".$_layerdata[$i]->num."\"),$(\"".$cookiename."\"));\n";
				echo "<div id=\"".$cookiename."\" style=\"border-width:0px;POSITION: absolute;TOP:".$_layerdata[$i]->y_to."px; LEFT:".$_layerdata[$i]->x_to."px; WIDTH:".$_layerdata[$i]->x_size."; HEIGHT:".$_layerdata[$i]->y_size."; z-index:1;visibility:hidden;\">\n";
				echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
				echo "<col width=\"5\"></col>";
				echo "<col width=".($_layerdata[$i]->x_size)."></col>";
				echo "<col width=\"5\"></col>";
				echo "<tr>";
				echo "	<td colspan=\"3\" id=\"eventlayer-top".$_layerdata[$i]->num."\" style=\"cursor:move; float:left;\">";
				echo "	<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">";
				echo "	<TR>";
				echo "		<TD valign=\"top\"><IMG SRC=\"".$Dir."images/common/win_top01.gif\" border=\"0\"></TD>";
				echo "		<TD background=\"".$Dir."images/common/win_topbg.gif\" width=\"100%\"><span style=\"font-size:9pt;\"><b><font color=\"#FFFFFF\">".$_layerdata[$i]->title."</font></b></span></TD>";
				echo "		<TD valign=\"top\"><a href=\"JavaScript:p_windowclose('".$cookiename."',0)\"><IMG SRC=\"".$Dir."images/common/popup_layer_close.gif\" border=\"0\"></a></TD>";
				echo "	</TR>";
				echo "	</TABLE>";
				echo "	</td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td background=\"".$Dir."images/common/win_leftbg.gif\"></td>";
				echo "	<td align=\"center\">";
				include($Dir.TempletDir."event/event".$_layerdata[$i]->design.".php");
				echo "	</td>";
				echo "	<td background=\"".$Dir."images/common/win_rightbg.gif\"></td>";
				echo "</tr>";
				echo "<tr>";
				echo "	<td><IMG SRC=\"".$Dir."images/common/win_leftimg.gif\" border=\"0\"></td>";
				echo "	<td background=\"".$Dir."images/common/win_downbg.gif\"></td>";
				echo "	<td><IMG SRC=\"".$Dir."images/common/win_rightimg.gif\" border=\"0\"></td>";
				echo "</tr>";
				echo "</table>\n";
				echo "</div>\n";
			}
		}
	}
	if(strlen($layer_str)>0) {
		echo "<script>\n";
		echo $layer_str;
		echo "</script>\n";
	}
}
