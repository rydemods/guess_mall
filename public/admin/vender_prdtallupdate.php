<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "vd-2";
$MenuCode = "vender";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$mode=$_POST["mode"];
if($mode=="update") {
	$aproductcode=(array)$_POST["aproductcode"];

	$aassembleproduct=(array)$_POST["aassembleproduct"];
	$aassembleuse=(array)$_POST["aassembleuse"];

	$aproductname=(array)$_POST["aproductname"];
	$aproductname2=(array)$_POST["aproductname2"];
	$aproduction=(array)$_POST["aproduction"];
	$aproduction2=(array)$_POST["aproduction2"];
	$aconsumerprice=(array)$_POST["aconsumerprice"];
	$aconsumerprice2=(array)$_POST["aconsumerprice2"];
	$abuyprice=(array)$_POST["abuyprice"];
	$abuyprice2=(array)$_POST["abuyprice2"];
	$asellprice=(array)$_POST["asellprice"];
	$asellprice2=(array)$_POST["asellprice2"];
	$areserve=(array)$_POST["areserve"];
	$areserve2=(array)$_POST["areserve2"];
	$areservetype=(array)$_POST["areservetype"];
	$areservetype2=(array)$_POST["areservetype2"];
	$aquantity=(array)$_POST["aquantity"];
	$aquantity2=(array)$_POST["aquantity2"];
	if(count($aproductcode)>0) {
		$movecount=0;
		$update_ymd = date("YmdH");
		$update_ymd2 = date("is");
		for($i=0;$i<count($aproductcode);$i++) {
			if (ord($aproductcode[$i]) && ($aproductname[$i]!=$aproductname2[$i] || $aproduction[$i]!=$aproduction2[$i] || $aconsumerprice[$i]!=$aconsumerprice2[$i] || $abuyprice[$i]!=$abuyprice2[$i] || $asellprice[$i]!=$asellprice2[$i] || $areserve[$i]!=$areserve2[$i] || $areservetype[$i]!=$areservetype2[$i] || $aquantity[$i]!=$aquantity2[$i]) && ord($asellprice[$i]) && ord($areserve[$i]) && ord($aproductname[$i])) {
				if (is_int($asellprice[$i]) && is_numeric($areserve[$i])) {   #숫자인지 검사
					if (ord($aquantity[$i])==0) 
						$quantity="NULL";
					else if (is_int($aquantity[$i]))
						$quantity = $aquantity[$i];
					if (ord($abuyprice[$i])==0) $abuyprice[$i]="0";
					if (ord($areserve[$i])==0) $areserve[$i]=0;
					if($areservetype[$i]!="Y") {
						$areservetype[$i]="N";
					}
					$productname = str_replace("\\\\'","''",$aproductname[$i]);
					$production = str_replace("\\\\'","''",$aproduction[$i]);

					$sql = "UPDATE tblproduct SET ";
					$sql.= "productname			= '{$productname}', ";
					$sql.= "sellprice			= {$asellprice[$i]}, ";
					$sql.= "consumerprice		= {$aconsumerprice[$i]}, ";
					$sql.= "buyprice			= {$abuyprice[$i]}, ";
					$sql.= "reserve				= '{$areserve[$i]}', ";
					$sql.= "reservetype			= '{$areservetype[$i]}', ";
					$sql.= "production			= '{$production}', ";
					$sql.= "quantity			= {$quantity} ";
					$sql.= "WHERE productcode='{$aproductcode[$i]}' ";
					if(pmysql_query($sql,get_db_conn())) {
						if($asellprice[$i]!=$asellprice2[$i] && $aassembleuse[$i]!="Y") {
							if(ord($aassembleproduct[$i])) {
								$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
								$sql.= "WHERE productcode IN ('".str_replace(",","','",$aassembleproduct[$i])."') ";
								$result = pmysql_query($sql,get_db_conn());
								while($row = @pmysql_fetch_object($result)) {
									$sql = "SELECT SUM(sellprice) as sumprice FROM tblproduct ";
									$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
									$sql.= "AND display ='Y' ";
									$sql.= "AND assembleuse!='Y' ";
									$result2 = pmysql_query($sql,get_db_conn());
									if($row2 = @pmysql_fetch_object($result2)) {
										$sql = "UPDATE tblproduct SET sellprice='{$row2->sumprice}' ";
										$sql.= "WHERE productcode = '{$row->productcode}' ";
										$sql.= "AND assembleuse='Y' ";
										pmysql_query($sql,get_db_conn());
									}
									pmysql_free_result($result2);
								}
							}
						}
						$movecount++;

						$update_date = $update_ymd.$update_ymd2;
						$log_content = "## 상품일괄수정 ## - 상품코드: {$aproductcode[$i]} 가격: {$asellprice[$i]} 소비자가 : {$aconsumerprice[$i]}  구입가 : {$abuyprice} 수량: $quantity 적립금 : ".$areserve[$i];
						
						ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content,$update_date);

						$update_ymd2++;
					}
				}
			}
		}
		if ($movecount!=0) {
			echo "<html></head><body onload=\"alert('{$movecount} 건의 상품정보가 수정되었습니다.');parent.pageForm.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('수정된 상품정보가 없습니다.')\"></body></html>";exit;
		}
	}
	exit;
}

$code=$_POST["code"];
$vender=$_POST["vender"];
$disptype=$_POST["disptype"];
$s_check=$_POST["s_check"];
if(ord($s_check)==0) $s_check="name";
$search=ltrim($_POST["search"]);
$sort=$_POST["sort"];
if($sort!="order by productname asc" && $sort!="order by productname desc" && $sort!="order by vender asc" && $sort!="order by vender desc" && $sort!="order by sellprice asc" && $sort!="order by sellprice desc" && $sort!="order by production asc" && $sort!="order by production desc") {
	$sort="order by regdate desc";
}
$qry = "WHERE 1=1 ";
if(strlen($code)>=3) {
	$qry.= "AND productcode LIKE '{$code}%' ";
}
if(ord($vender)) {
	$qry.= "AND vender='{$vender}' ";
} else {
	$qry.= "AND vender>0 ";
}
if($disptype=="Y") $qry.= "AND display='Y' ";
else if($disptype=="N") $qry.= "AND display='N' ";
if(ord($search)) {
	if($s_check=="name") $qry.= "AND productname LIKE '%{$search}%' ";
	else if($s_check=="code") $qry.= "AND productcode='{$search}' ";
}

$sql = "SELECT COUNT(*) as t_count FROM tblproduct {$qry} ";
$paging = new Paging($sql,10,10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$venderlist=array();
$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$venderlist[$row->vender]=$row;
}
pmysql_free_result($result);

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function ACodeSendIt(code) {
	document.sForm.code.value=code;
	murl = "vender_prdtlist.ctgr.php?code="+code+"&depth=2";
	surl = "vender_prdtlist.ctgr.php?depth=3";
	durl = "vender_prdtlist.ctgr.php?depth=4";
	BCodeCtgr.location.href = murl;
	CCodeCtgr.location.href = surl;
	DCodeCtgr.location.href = durl;
}

function formSubmit() {
	try {
		if (typeof(document.form2["aproductcode[]"])!="object") {
			alert("수정할 상품이 존재하지 않습니다.");
			return;
		}

		var i=0;
		while(true) {
			if(document.getElementById("areserve"+i) && document.getElementById("areservetype"+i)) {
				if (document.getElementById("areserve"+i).value.length>0) {
					if(document.getElementById("areservetype"+i).value=="Y") {
						if(isDigitSpecial(document.getElementById("areserve"+i).value,".")) {
							alert("적립률은 숫자와 특수문자 소수점\(.\)으로만 입력하세요.");
							document.getElementById("areserve"+i).focus();
							return;
						}
						
						if(getSplitCount(document.getElementById("areserve"+i).value,".")>2) {
							alert("적립률 소수점\(.\)은 한번만 사용가능합니다.");
							document.getElementById("areserve"+i).focus();
							return;
						}

						if(getPointCount(document.getElementById("areserve"+i).value,".",2)) {
							alert("적립률은 소수점 이하 둘째자리까지만 입력 가능합니다.");
							document.getElementById("areserve"+i).focus();
							return;
						}

						if(Number(document.getElementById("areserve"+i).value)>100 || Number(document.getElementById("areserve"+i).value)<0) {
							alert("적립률은 0 보다 크고 100 보다 작은 수를 입력해 주세요.");
							document.getElementById("areserve"+i).focus();
							return;
						}
					} else {
						if(isDigitSpecial(document.getElementById("areserve"+i).value,"")) {
							alert("적립금은 숫자로만 입력하세요.");
							document.getElementById("areserve"+i).focus();
							return;
						}
					}
				}
				i++;
			} else {
				break;
			}
		}
	} catch (e) {
		return;
	}
	if(confirm("상품정보를 수정 하시겠습니까?")) {
		document.form2.mode.value="update";
		document.form2.target="processFrame";
		document.form2.submit();
	}
}

function SearchPrd() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function OrderSort(sort) {
	document.pageForm.block.value="";
	document.pageForm.gotopage.value="";
	document.pageForm.sort.value=sort;
	document.pageForm.submit();
}

function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}

function chkFieldMaxLenFunc(thisId,reserveTypeID) {
	if(document.getElementById(reserveTypeID)) {
		if (document.getElementById(reserveTypeID).value=="Y") { max=5; addtext="/특수문자(소수점)";} else { max=6; }

		if(document.getElementById(thisId)) {
			if (document.getElementById(thisId).value.bytes() > max) {
				alert("입력할 수 있는 허용 범위가 초과되었습니다.\n\n" + "숫자"+addtext+" " + max + "자 이내로 입력이 가능합니다.");
				document.getElementById(thisId).value = document.getElementById(thisId).value.cut(max);
				document.getElementById(thisId).focus();
			}
		}
	}
}

function getSplitCount(objValue,splitStr)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	return split_array.length;
}

function getPointCount(objValue,splitStr,falsecount)
{
	var split_array = new Array();
	split_array = objValue.split(splitStr);
	
	if(split_array.length!=2) {
		if(split_array.length==1) {
			return false;
		} else {
			return true;
		}
	} else {
		if(split_array[1].length>falsecount) {
			return true;
		} else {
			return false;
		}
	}
}

function isDigitSpecial(objValue,specialStr)
{
	if(specialStr.length>0) {
		var specialStr_code = parseInt(specialStr.charCodeAt(i));

		for(var i=0; i<objValue.length; i++) {
			var code = parseInt(objValue.charCodeAt(i));
			var ch = objValue.substr(i,1).toUpperCase();
			
			if((ch<"0" || ch>"9") && code!=specialStr_code) {
				return true;
				break;
			}
		}
	} else {
		for(var i=0; i<objValue.length; i++) {
			var ch = objValue.substr(i,1).toUpperCase();
			if(ch<"0" || ch>"9") {
				return true;
				break;
			}
		}
	}
}
</script>
<table cellpadding="0" cellspacing="0" width="980" style="table-layout:fixed">
<tr>
	<td width=10></td>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td height="29">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점상품 관리 &gt; <span class="2depth_select">상품 일괄 간편수정</span></td>
		</tr>
		<tr>
			<td><img src="images/top_link_line.gif" width="100%" height="1" border="0"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=190></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top" background="images/left_bg.gif" style="padding-top:15">
			<?php include("menu_vender.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/vender_prdtallupdate_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/distribute_01.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_02.gif"></TD>
					<TD><IMG SRC="images/distribute_03.gif"></TD>
				</TR>
				<TR>
					<TD background="images/distribute_04.gif"></TD>
					<TD class="notice_blue"><IMG SRC="images/distribute_img.gif" ></TD>
					<TD width="100%" class="notice_blue">해당 입점업체 상품의 가격/적립금/수량 등을 일괄 관리 할 수 있습니다.</TD>
					<TD background="images/distribute_07.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/distribute_08.gif"></TD>
					<TD COLSPAN=2 background="images/distribute_09.gif"></TD>
					<TD><IMG SRC="images/distribute_10.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="sForm" method="post">
			<input type="hidden" name="code" value="<?=$code?>">
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#0099CC" style="padding:6pt;">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="FFFFFF">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD height="1" background="images/table_con_line.gif"></TD>
						</TR>
						<TR>
							<TD height="35" align="center" background="images/blueline_bg.gif"><b><font color="#0099CC">입점업체 일괄 간편수정 검색 선택</font></b></TD>
						</TR>
						<TR>
							<TD>
							<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
							<TR>
								<TD height="1" background="images/table_con_line.gif"></TD>
							</TR>
							<TR>
								<TD class="td_con1" style="padding-top:10pt;padding-left:10px;" align="center">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=></col>
								<tr>
									<td>
									<select name="code1" style=width:155 onchange="ACodeSendIt(this.options[this.selectedIndex].value)">
									<option value="">------ 대 분 류 ------</option>
<?php
						$sql = "SELECT code_a,code_b,code_c,code_d,code_name FROM tblproductcode ";
						$sql.= "WHERE code_b='000' AND code_c='000' ";
						$sql.= "AND code_d='000' AND type LIKE 'L%' ORDER BY sequence DESC ";
						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							echo "<option value=\"{$row->code_a}\"";
							if($row->code_a==substr($code,0,3)) echo " selected";
							echo ">{$row->code_name}</option>\n";
						}
						pmysql_free_result($result);
?>
									</select>
									</td>
									<td></td>
									<td>
									<iframe name="BCodeCtgr" src="vender_prdtlist.ctgr.php?code=<?=substr($code,0,3)?>&select_code=<?=$code?>&depth=2" width="155" height="21" scrolling=no frameborder=no></iframe>
									</td>
									<td></td>
									<td><iframe name="CCodeCtgr" src="vender_prdtlist.ctgr.php?code=<?=substr($code,0,6)?>&select_code=<?=$code?>&depth=3" width="155" height="21" scrolling=no frameborder=no></iframe></td>
									<td></td>
									<td><iframe name="DCodeCtgr" src="vender_prdtlist.ctgr.php?code=<?=substr($code,0,9)?>&select_code=<?=$code?>&depth=4" width="155" height="21" scrolling=no frameborder=no></iframe></td>
								</tr>
								</table>
								</TD>
							</TR>
							<TR>
								<TD class="td_con1" style="padding-top:3px;padding-left:10px;" align="center">
								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=155></col>
								<col width=5></col>
								<col width=></col>
								<tr>
									<td>
									<select name=vender style="font-size:8pt;width:155">
									<option value="">모든 입점업체</option>
<?php
						$tmplist=$venderlist;
						while(list($key,$val)=each($tmplist)) {
							if($val->delflag=="N") {
								echo "<option value=\"{$val->vender}\"";
								if($vender==$val->vender) echo " selected";
								echo ">{$val->id} - {$val->com_name}</option>\n";
							}
						}
?>
									</select>
									</td>
									<td></td>
									<td>
									<select name=disptype style="width:100%">
									<option value="">진열/대기상품 전체</option>
									<option value="Y" <?php if($disptype=="Y")echo"selected";?>>진열상품만 검색</option>
									<option value="N" <?php if($disptype=="N")echo"selected";?>>대기상품만 검색</option>
									</select>
									</td>
									<td></td>
									<td>
									<select name="s_check" style="width:100%">
									<option value="name" <?php if($s_check=="name")echo"selected";?>>상품명으로 검색</option>
									<option value="code" <?php if($s_check=="code")echo"selected";?>>상품코드로 검색</option>
									</select>
									</td>
									<td></td>
									<td>
									<input type=text name=search value="<?=$search?>" style="width:155">
									<A HREF="javascript:SearchPrd()"><img src=images/btn_inquery03.gif border=0 align=absmiddle></A>
									</td>
								</tr>
								</table>
								</td>
							</tr>
							</TABLE>
							</TD>
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
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 method=post>
			<input type=hidden name=mode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				<col width=40></col>
				<col width=70></col>
				<col width=></col>
				<col width=100></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<col width=90></col>
				<col width=40></col>
				<TR>
					<TD height=1 background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				<TR align="center">
					<TD class="table_cell"><B>번호</B></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by vender asc"?"order by vender desc":"order by vender asc")?>')"; onMouseover="self.status=''; return true; "><B>입점업체</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by productname asc"?"order by productname desc":"order by productname asc")?>')"; onMouseover="self.status=''; return true; "><B>상품명</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by production asc"?"order by production desc":"order by production asc")?>')"; onMouseover="self.status=''; return true; "><B>제조사</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by sellprice asc"?"order by sellprice desc":"order by sellprice asc")?>')"; onMouseover="self.status=''; return true; "><B>판매가</B></a></TD>
					<TD class="table_cell1"><B>소비자가</B></TD>
					<TD class="table_cell1"><B>구입가</B></TD>
					<TD class="table_cell1"><B>적립금(률)</B></TD>
					<TD class="table_cell1"><B>수량</B></TD>
				</TR>
				<TR>
					<TD height=1 background="images/table_con_line.gif" colspan="9"></TD>
				</TR>

<?php
			$colspan=9;
			if($t_count>0) {
				$sql = "SELECT productcode,productname,sellprice,consumerprice,buyprice,reserve,reservetype, ";
				$sql.= "production,quantity,vender,assembleproduct,assembleuse FROM tblproduct {$qry} {$sort} ";
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				$i=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				echo "<tr align=center bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<td class=\"td_con2\">{$number}</td>\n";
				echo "	<td class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
				echo "	<td class=\"td_con1\"><input type=text name=\"aproductname[]\" maxlength=250 value=\"".str_replace("\"","&quot",$row->productname)."\" style=\"font-size:8pt;width:100%\" onKeyDown=\"chkFieldMaxLen(250)\"></td>\n";
				echo "	<td class=\"td_con1\"><input type=text name=\"aproduction[]\" maxlength=20 value=\"".str_replace("\"","&quot",$row->production)."\" style=\"font-size:8pt;width:100%\"></td>\n";
				if($row->assembleuse=="Y") { 
					echo "	<td class=\"td_con1\" align=\"right\" style=\"font-size:8pt;\"><input type=hidden name=\"asellprice[]\" value=\"{$row->sellprice}\">{$row->sellprice}</td>\n";
				} else {
					echo "	<td class=\"td_con1\"><input type=text name=\"asellprice[]\" maxlength=8 value=\"{$row->sellprice}\" style=\"font-size:8pt;width:100%;text-align:right\"></td>\n";
				}
				echo "	<td class=\"td_con1\"><input type=text name=\"aconsumerprice[]\" maxlength=8 value=\"{$row->consumerprice}\" style=\"font-size:8pt;width:100%;text-align:right\"></td>\n";
				echo "	<td class=\"td_con1\"><input type=text name=\"abuyprice[]\" maxlength=8 value=\"{$row->buyprice}\" style=\"font-size:8pt;width:100%;text-align:right\"></td>\n";
				echo "	<td class=\"td_con1\"><input type=text name=\"areserve[]\" size=6 maxlength=6 value=\"{$row->reserve}\"  style=\"font-size:8pt;text-align:right\" id=\"areserve{$i}\" onKeyUP=\"chkFieldMaxLenFunc(this.id,'areservetype{$i}');\"><select name=\"areservetype[]\" style=\"width:36px;font-size:8pt;margin-left:1px;\" id=\"areservetype{$i}\" onchange=\"chkFieldMaxLenFunc('areserve{$i}',this.id);\"><option value=\"N\"".($row->reservetype!="Y"?" selected":"").">￦<option value=\"Y\"".($row->reservetype!="Y"?"":" selected").">%</select></td>\n";
				echo "	<td class=\"td_con1\"><input type=text name=\"aquantity[]\" maxlength=3 value=\"{$row->quantity}\"  style=\"font-size:8pt;width:100%;text-align:right\"></td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"9\"></TD>\n";
				echo "</tr>\n";
				echo "<input type=hidden name=\"aproductcode[]\" value=\"{$row->productcode}\">\n";
				echo "<input type=hidden name=\"aassembleproduct[]\" value=\"{$row->assembleproduct}\">\n";
				echo "<input type=hidden name=\"aassembleuse[]\" value=\"{$row->assembleuse}\">\n";
				echo "<input type=hidden name=\"aproductname2[]\" value=\"".str_replace("\"","&quot",$row->productname)."\">\n";
				echo "<input type=hidden name=\"aproduction2[]\" value=\"".str_replace("\"","&quot",$row->production)."\">\n";
				echo "<input type=hidden name=\"aconsumerprice2[]\" value=\"{$row->consumerprice}\">\n";
				echo "<input type=hidden name=\"abuyprice2[]\" value=\"{$row->buyprice}\">\n";
				echo "<input type=hidden name=\"asellprice2[]\" value=\"{$row->sellprice}\">\n";
				echo "<input type=hidden name=\"areserve2[]\" value=\"{$row->reserve}\">\n";
				echo "<input type=hidden name=\"areservetype2[]\" value=\"".($row->reservetype!="Y"?"N":"Y")."\">\n";
				echo "<input type=hidden name=\"aquantity2[]\" value=\"{$row->quantity}\">\n";
				$i++;
			}
			pmysql_free_result($result);

			if($i>0) {
					$pageing=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				}
			} else {
				echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
			}
?>
				<TR>
					<TD colspan="<?=$colspan?>" background="images/table_top_line.gif"></TD>
				</TR>

				<tr><td colspan=<?=$colspan?> height=5></td></tr>
				<tr><td align=right colspan=<?=$colspan?>><A HREF="javascript:formSubmit()"><img src=images/btn_save01.gif border=0></A></td></tr>
				<tr><td colspan=<?=$colspan?> height=10></td></tr>
				<tr><td colspan=<?=$colspan?> align=center class="font_size"><?=$pageing?></td></tr>
				<tr><td colspan=<?=$colspan?> height=10></td></tr>
				</form>
				</table>
				</td>
			</tr>
			<form name="pageForm" method="post">
			<input type=hidden name='code' value='<?=$code?>'>
			<input type=hidden name='disptype' value='<?=$disptype?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='sort' value='<?=$sort?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
				<TR>
					<TD><IMG SRC="images/manual_top1.gif" WIDTH=15 height="45" ALT=""></TD>
					<TD><IMG SRC="images/manual_title.gif" WIDTH=113 height="45" ALT=""></TD>
					<TD width="100%" background="images/manual_bg.gif" height="35"></TD>
					<TD background="images/manual_bg.gif"></TD>
					<td background="images/manual_bg.gif"><IMG SRC="images/manual_top2.gif" WIDTH=18 height="45" ALT=""></td>
				</TR>
				<TR>
					<TD background="images/manual_left1.gif"></TD>
					<TD COLSPAN=3 width="100%" valign="top" bgcolor="white" style="padding-top:8pt; padding-bottom:8pt; padding-left:4pt;">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="20" align="right" valign="top"><img src="images/icon_8.gif" width="13" height="18" border="0"></td>
						<td width="701"><span class="font_dotline">상품일괄 간편수정</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 입점업체가 등록한 상품기본정보를 일괄수정할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 검색은 분류별 검색 및 입점사 아이디로 검색할 수 있습니다.</td>
					</tr>
					</table>
					</TD>
					<TD background="images/manual_right1.gif"></TD>
				</TR>
				<TR>
					<TD><IMG SRC="images/manual_left2.gif" WIDTH=15 HEIGHT=8 ALT=""></TD>
					<TD COLSPAN=3 background="images/manual_down.gif"></TD>
					<TD><IMG SRC="images/manual_right2.gif" WIDTH=18 HEIGHT=8 ALT=""></TD>
				</TR>
				</TABLE>
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
