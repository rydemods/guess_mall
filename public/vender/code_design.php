<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");
include("access.php");


$maxRow=12;


$mode=$_POST["mode"];
$cbm_tgbn=$_POST["cbm_tgbn"];
$cbm_sectcode=$_POST["cbm_sectcode"];
$cbm_themesectcode=$_POST["cbm_themesectcode"];

if($mode=="update") {
	$select_code=$_POST["select_code"];
	$select_tgbn=$_POST["select_tgbn"];

	$hot_used_flag=(int)$_POST["hot_used_flag"];		//hot사용여부
	$hot_prdlinktype=(int)$_POST["hot_prdlinktype"];	//hot 링크방법
	$hot_templt_dispseq=(int)$_POST["hot_templt_dispseq"];
	$new_used_flag=(int)$_POST["new_used_flag"];
	$new_templt_dispseq=(int)$_POST["new_templt_dispseq"];
	$page_disptype=$_POST["page_disptype"];
	$page_disp_num=(int)$_POST["page_disp_num"];

	$sql = "SELECT * FROM tblvendersectdisplist WHERE seq IN ('".$hot_templt_dispseq."','".$new_templt_dispseq."') ";
	$result=pmysql_query($sql,get_db_conn());
	$check_dispseq=array();
	while($row=pmysql_fetch_object($result)) {
		$check_dispseq[$row->seq]=$row;
	}
	pmysql_free_result($result);

	if(!strstr("01",$hot_used_flag)) {
		$hot_used_flag=0;
	}
	if(!strstr("12",$hot_prdlinktype)) {
		$hot_prdlinktype=1;
	}
	if(!is_object($check_dispseq[$hot_templt_dispseq])) {
		$hot_templt_dispseq=118;
	}
	if(!strstr("01",$new_used_flag)) {
		$new_used_flag=0;
	}

	//insert OR update
	$sql = "SELECT f_merge_tblvendercodedesign('".$_VenderInfo->getVidx()."','".$select_code."','".$select_tgbn."','".$hot_used_flag."','".$hot_templt_dispseq."','".$hot_prdlinktype."',NULL,NULL)";
	if(pmysql_query($sql,get_db_conn())) {
		$isupdate=true;
	}

	if($isupdate) {
		if($hot_prdlinktype=="1") {	//HOT 판매량순
			$sql = "DELETE FROM tblvenderspecialcode ";
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
			$sql.= "AND code='".$select_code."' AND tgbn='".$select_tgbn."' AND special='3' ";
			pmysql_query($sql,get_db_conn());
		} else {					//HOT 상품별도지정
			$special_list="";
			$in_prlist="";
			for($i=0;$i<$maxRow;$i++) {
				${"hot_prdcode".$i}=$_POST["hot_prdcode".$i];
				if(strlen(${"hot_prdcode".$i})==18) {
					$special_list.=${"hot_prdcode".$i}.",";
				}
			}
			if(strlen($special_list)>0) {
				$special_list=str_replace(',','\',\'',$special_list);
				$sql = "SELECT productcode FROM tblproduct WHERE productcode IN ('".$special_list."') ";
				$sql.= "AND vender='".$_VenderInfo->getVidx()."' ";
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$in_prlist.=$row->productcode.",";
				}
				pmysql_free_result($result);
				$in_prlist=rtrim($in_prlist,',');
			}
			$sql = "SELECT f_merge_tblvenderspecialcode('".$_VenderInfo->getVidx()."','".$select_code."','".$select_tgbn."','3','".$in_prlist."') ";
		}
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
	}
	echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
}

if($cbm_tgbn!="10" && $cbm_tgbn!="20") {
	$cbm_tgbn="10";
	$cbm_sectcode="";
	$cbm_themesectcode="";
}

$sql = "SELECT * FROM tblvendersectdisplist ";
$result=pmysql_query($sql,get_db_conn());
$SectArr=array();
while($row=pmysql_fetch_object($result)) {
	$SectArr[$row->seq]=$row;
	if($row->dispcnt>$maxRow) $maxRow=$row->dispcnt;
}
pmysql_free_result($result);

//기본 카테고리 조회
$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
$sql.= "AND display='Y' GROUP BY code_a ";
$result=pmysql_query($sql,get_db_conn());
$codelist="";
while($row=pmysql_fetch_object($result)) {
	$codelist.=$row->code_a.",";
}
pmysql_free_result($result);
$codelist=str_replace(',','\',\'',$codelist);
$CodeArr=array();
if(strlen($codelist)>0) {
	$sql = "SELECT code_a, code_name FROM tblproductcode WHERE code_a IN ('".$codelist."') AND code_b='000' AND code_c='000' AND code_d='000' ";
	$sql.= "ORDER BY sequence DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$CodeArr[$row->code_a]=$row;
		if(strlen($cbm_sectcode)==0) {
			$cbm_sectcode=$row->code_a;
		}
	}
	pmysql_free_result($result);
}

//테마 카테고리 조회
$sql = "SELECT SUBSTR(a.themecode,1,3) as code_a FROM tblvenderthemeproduct a, tblproduct b ";
$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
$sql.= "AND a.vender=b.vender AND a.productcode=b.productcode ";
$sql.= "AND b.display='Y' GROUP BY code_a ";
$result=pmysql_query($sql,get_db_conn());
$themecodelist="";
while($row=pmysql_fetch_object($result)) {
	$themecodelist.=$row->code_a.",";
}
pmysql_free_result($result);
$themecodelist=str_replace(',','\',\'',$themecodelist);
$ThemeCodeArr=array();
if(strlen($themecodelist)>0) {
	$sql = "SELECT code_a, code_name FROM tblvenderthemecode WHERE vender='".$_VenderInfo->getVidx()."' AND code_a IN ('".$themecodelist."') AND code_b='000' ";
	$sql.= "ORDER BY sequence DESC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$ThemeCodeArr[$row->code_a]=$row;
		if(strlen($cbm_themesectcode)==0) {
			$cbm_themesectcode=$row->code_a;
		}
	}
	pmysql_free_result($result);
}

if($cbm_tgbn=="10" && strlen($cbm_sectcode)>0) {
	$sql = "SELECT hot_used,hot_dispseq,hot_linktype FROM tblvendercodedesign ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND code='".$cbm_sectcode."' AND tgbn='10' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$hot_used=$row->hot_used;
		$hot_dispseq=$row->hot_dispseq;
		$hot_linktype=$row->hot_linktype;
	} else {
		$hot_used="0";
		$hot_dispseq="118";
		$hot_linktype="1";
	}
	pmysql_free_result($result);
	$select_code=$cbm_sectcode;
} else if($cbm_tgbn=="20" && strlen($cbm_themesectcode)>0) {
	$sql = "SELECT hot_used,hot_dispseq,hot_linktype FROM tblvendercodedesign ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND code='".$cbm_themesectcode."' AND tgbn='20' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$hot_used=$row->hot_used;
		$hot_dispseq=$row->hot_dispseq;
		$hot_linktype=$row->hot_linktype;
	} else {
		$hot_used="0";
		$hot_dispseq="118";
		$hot_linktype="1";
	}
	pmysql_free_result($result);
	$select_code=$cbm_themesectcode;
}

if(strlen($select_code)>0) {
	$sql = "SELECT special_list FROM tblvenderspecialcode ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
	$sql.= "AND code='".$select_code."' AND tgbn='".$cbm_tgbn."' AND special='3' ";
	$result=pmysql_query($sql,get_db_conn());
	$sp_prcode="";
	if($row=pmysql_fetch_object($result)) {
		$sp_prcode=str_replace(',','\',\'',$row->special_list);
		$codes = explode(',',$row->special_list);
	}
	pmysql_free_result($result);

	$hotprobj20=array();
	$hotprobj10=array();
	if(strlen($sp_prcode)>0) {
		$sql = "SELECT productcode,productname,sellprice,display FROM tblproduct ";
		$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND productcode IN ('".$sp_prcode."') ";
		$sql.= "ORDER BY FIELD(productcode,'".$sp_prcode."') LIMIT ".$maxRow." ";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$hotprobj20[]=$row;
		}
		pmysql_free_result($result);
	}

	if($cbm_tgbn=="10") {
		$sql = "SELECT productcode,productname,sellprice FROM tblproduct ";
		$sql.= "WHERE productcode LIKE '".$select_code."%' ";
		$sql.= "AND vender='".$_VenderInfo->getVidx()."' AND display='Y' ";
		$sql.= "ORDER BY sellcount DESC LIMIT ".$maxRow." ";
	} else if($cbm_tgbn=="20") {
		$sql = "SELECT b.productcode,b.productname,b.sellprice FROM tblvenderthemeproduct a, tblproduct b ";
		$sql.= "WHERE a.vender='".$_VenderInfo->getVidx()."' ";
		$sql.= "AND a.vender=b.vender AND a.productcode=b.productcode ";
		$sql.= "AND a.themecode LIKE '".$select_code."%' ";
		$sql.= "AND b.display='Y' ORDER BY b.sellcount DESC LIMIT ".$maxRow." ";
	}
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$hotprobj10[]=$row;
	}
	pmysql_free_result($result);
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language=javascript src="PrdtDispInfoFucn.js.php" type="text/javascript"></script>

<table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<col width=740></col>
<col width=80></col>
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>대분류 화면 관리</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>대분류 화면 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 미니샵 분류화면 상단에 진열되는 상품을 판매자 의도대로 자유롭게 배치하실 수 있습니다.</td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 별도로 상품 진열 지정을 하지 않을 경우 판매량 순으로 진열됩니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=10></td></tr>
			<tr>
				<td style="padding:15">

<script language=Javascript>
maxRow = <?=$maxRow?>;

<?
$tmpsectval=$SectArr;
while(list($key,$val)=each($tmpsectval)) {
	echo "hot_templt_img[".$key."]=\"images/sample/display/section/".$val->disptype.".gif\";\n";
	echo "hot_prd_count[".$key."]=\"".$val->dispcnt."\";\n";
}
?>
</script>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=iForm action="" method=post>
				<input type=hidden name=mode>
				<input type=hidden name=hot_prddispcnt value="<?=$SectArr[$hot_dispseq]->dispcnt?>">
				<input type=hidden name=hot_used_flag>
				<input type=hidden name=hot_prdlinktype>
				<input type=hidden name=select_code value="<?=$select_code?>">
				<input type=hidden name=select_tgbn value="<?=$cbm_tgbn?>">

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> HOT 추천상품 관리</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				<tr>
					<td align=center bgcolor=F8F8F8 style=padding:25>
					<img name=hot_templt_img id=hot_templt_img src=images/sample/display/section/<?=$SectArr[$hot_dispseq]->disptype?>.gif border=0>
					</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr>
					<td valign=top>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>대분류</B></td>
						<td width=83% style=padding:7,10>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td>
							<input type=radio value="10" name="cbm_tgbn" onClick='changeSect(10)' <?if($cbm_tgbn=="10")echo"checked";?>>기본카테고리 
							<select name="cbm_sectcode" style=width:150 onChange='changeSect(10)'>
<?
							$CodeArrVal=$CodeArr;
							while(list($key,$val)=each($CodeArrVal)) {
								echo "<option value=\"".$key."\"";
								if($key==$cbm_sectcode) echo " selected";
								echo ">".$val->code_name."</option>\n";
							}

							$ThemeCodeArrVal=$ThemeCodeArr;
?>
							</select>
							</td>
							<td width=20> </td>
							<td <?=(count($ThemeCodeArrVal)==0?"style=display:none":"")?>>
							<input type=radio value="20" name="cbm_tgbn" onClick='changeSect(20)' <?if($cbm_tgbn=="20")echo"checked";?>> 테마 카테고리
							<select name="cbm_themesectcode" style=width:150 onChange='changeSect(20)'>
<?
							while(list($key,$val)=each($ThemeCodeArrVal)) {
								echo "<option value=\"".$key."\"";
								if($key==$cbm_themesectcode) echo " selected";
								echo ">".$val->code_name."</option>\n";
							}
?>
							</select>
							</td>
						</tr>                      
						</table>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td width=17% bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>사용여부</B></td>
						<td width=83% style=padding:7,10><input type=radio name="hot_used_flag_radio" <?if($hot_used=="1")echo"checked";?>>예 &nbsp; &nbsp; <input type=radio name="hot_used_flag_radio" <?if($hot_used=="0")echo"checked";?>>아니오</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상품진열 선택</B></td>
						<td valign=top style=padding:7,10>
						<select name="hot_templt_dispseq" style=width:200 onChange="changeHotTempltIdx();">
<?
						$tmpsectval=$SectArr;
						while(list($key,$val)=each($tmpsectval)) {
							echo "<option value=\"".$key."\"";
							if($key==$hot_dispseq) echo " selected";
							echo ">".$val->dispname."</option>\n";
						}
?>
						</select>
						</td>
					</tr>
					<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
					<tr valign=top>
						<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>링크설정</B></td>
						<td style=padding:7,10>
						<table width=100% border=0 cellspacing=0 cellpadding=0>
						<tr>
							<td><input type=radio name="hot_sect_prdlink" value="10" onClick="resetHotPrdList();" <?if($hot_linktype=="1")echo"checked";?>>판매량순 &nbsp; &nbsp; <input type=radio name="hot_sect_prdlink" value="20" onClick="resetHotPrdList();" <?if($hot_linktype=="2")echo"checked";?>>상품별도지정 : 상품선택 &nbsp; <a href="javascript:openPrdList();"><img src=images/btn_select02.gif border=0 align=absmiddle></a> <a href="javascript:deletePrdList();"><img src=images/btn_delete02.gif border=0 align=absmiddle></a></td>
						</tr>
						<tr>
							<td height=6></td>
						</tr>
						<tr>
							<td style=color:2A97A7>&nbsp;* 판매량순으로 선택하실 경우 HOT 추천상품에 진열되는 상품은 판매량 순으로 진열됩니다.</td>
						</tr>
						<tr>
							<td height=6></td>
						</tr>
						<tr>
							<td valign=top bgcolor=E7E7E7>
<?
							$hotprlist10="";
							$hotprlist20="";
							for($i=0;$i<$maxRow;$i++) {
								$display="";
								if($SectArr[$hot_dispseq]->dispcnt>=$i) $display="style='display:none'";
								if($hot_linktype=="1") $display="style='display:none'";
								$icon_num=sprintf("%02d",$i+1);

								$hotprlist20.="<tr height=28 align=center bgcolor=FFFFFF id=hotPrd".$i." ".$display.">\n";
								$hotprlist20.="	<td><input type=checkbox name='selectBoxPrd'></td>\n";
								$hotprlist20.="	<td style=padding-bottom:4><img src=images/icon_num".$icon_num.".gif border=0><input type=hidden name='hot_prdcode".$i."' value='".$hotprobj20[$i]->productcode."'></td>\n";
								$hotprlist20.="	<td id='hot_prdname".$i."' >".$hotprobj20[$i]->productname."</td>\n";
								$hotprlist20.="	<td style='padding-right:5'><input type=text readonly name='hot_prdprice".$i."' value='".(strlen($hotprobj20[$i]->sellprice)>0?number_format($hotprobj20[$i]->sellprice):"")."' style='text-align:right;border:0' size=10></td>\n";
								$hotprlist20.="	<td><input type=text readonly name='hot_prddpflag".$i."' value='".$hotprobj20[$i]->display."' style='border:0' size=1></td>\n";
								$hotprlist20.="	<td><a href='javascript:goDown(".$i.")'><img src=images/btn_down02.gif border=0 align=absmiddle></a> <a href='javascript:goUp(".$i.")'><img src=images/btn_up01.gif border=0 align=absmiddle></a></td>\n";
								$hotprlist20.="</tr>\n";

								$display="";
								if($SectArr[$hot_dispseq]->dispcnt>=$i) $display="style='display:none'";
								if($hot_linktype=="2") $display="style='display:none'";
								$icon_num=sprintf("%02d",$i+1);

								$hotprlist10.="<tr height=28 align=center bgcolor=FFFFFF id=autoPrd".$i." ".$display.">\n";
								$hotprlist10.="	<td style=padding-bottom:4><img src=images/icon_num".$icon_num.".gif border=0></td>\n";
								$hotprlist10.="	<td><input type=text readonly name='auto_prdname".$i."' value='".$hotprobj10[$i]->productname."' size=60 style='text-align:left;border:0'><input type=hidden name='auto_prdcode".$i."' value='".$hotprobj10[$i]->productcode."'></td>\n";
								$hotprlist10.="	<td style='padding-right:5'><input type=text readonly name='auto_prdprice".$i."' value='".(strlen($hotprobj10[$i]->sellprice)>0?number_format($hotprobj10[$i]->sellprice):"")."' style='text-align:right;border:0' size=13></td>\n";
								$hotprlist10.="</tr>\n";
							}
?>
							<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr height=28 align=center bgcolor=FEFCE2 id=hotHeader>
								<td width=15% colspan=2><B>위치</B></td>
								<td width=58%><B>상품명</B></td>
								<td width=10%><B>가격</B></td>
								<td width=7%><B>진열</B></td>
								<td width=10%><B>순서</B></td>
							</tr>
							<?=$hotprlist20?>
							</table>

							<table width=100% border=0 cellspacing=1 cellpadding=0>
							<tr height=28 align=center bgcolor=FEFCE2 id=autoHeader>
								<td width=15%><B>위치</B></td>
								<td width=75%><B>상품명</B></td>
								<td width=10%><B>가격</B></td>
							</tr>
							<?=$hotprlist10?>
							</table>

							</td>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 bgcolor=#CDCDCD></td></tr>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formGSubmit()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>
				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<script>resetHotPrdList();</script>
<?=$onload?>
<?php include("copyright.php"); ?>
