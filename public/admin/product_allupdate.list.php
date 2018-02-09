<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "nomenu";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$sort=$_REQUEST["sort"];
$mode=$_POST["mode"];
$code=$_POST["code"];
$keyword=$_POST["keyword"];
$searchtype=$_POST["searchtype"];
if(ord($searchtype)==0) $searchtype=0;

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
$adisplay=(array)$_POST["adisplay"];
$adisplay2=(array)$_POST["adisplay2"];

if ($mode=="update" && count($aproductcode)>0) {
	$movecount=0;
	$update_ymd = date("YmdH");
	$update_ymd2 = date("is");
	$displist=array();
	for($i=0;$i<count($aproductcode);$i++) {
		if (ord($aproductcode[$i]) && ($aproductname[$i]!=$aproductname2[$i] || $aproduction[$i]!=$aproduction2[$i] || $aconsumerprice[$i]!=$aconsumerprice2[$i] || $abuyprice[$i]!=$abuyprice2[$i] || $asellprice[$i]!=$asellprice2[$i] || $areserve[$i]!=$areserve2[$i] || $areservetype[$i]!=$areservetype2[$i] || $aquantity[$i]!=$aquantity2[$i] || $adisplay[$i]!=$adisplay2[$i]) && ord($asellprice[$i]) && ord($areserve[$i]) && ord($aproductname[$i])) {
			if (is_numeric($asellprice[$i]) && is_numeric($areserve[$i])) {   #숫자인지 검사
			    $aquantity[$i]=trim($aquantity[$i]);
				if (ord($aquantity[$i])==0) 
					$quantity="NULL";
				else if (is_numeric($aquantity[$i]))
					$quantity = $aquantity[$i]+0;
				if (ord($abuyprice[$i])==0) 
					$abuyprice[$i]="0";
				if (ord($areserve[$i])==0) 
					$areserve[$i]=0;
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
				$sql.= "quantity			= {$quantity}, ";
				$sql.= "display				= '{$adisplay[$i]}' ";
				$sql.= "WHERE productcode='{$aproductcode[$i]}' ";
				pmysql_query($sql,get_db_conn());

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

				if($adisplay[$i]!=$adisplay2[$i]) {
					$displist[]=$aproductcode[$i];
				}

				$movecount++;

				$update_date = $update_ymd.$update_ymd2;
				$log_content = "## 상품일괄수정 ## - 상품코드: {$aproductcode[$i]} 가격: {$asellprice[$i]} 소비자가 : {$aconsumerprice[$i]}  구입가 : {$abuyprice} 진열: {$adisplay[$i]} 수량: $quantity 적립금 : ".$areserve[$i];
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content,$update_date);
				$update_ymd2++;
			}
		}
	}

	//진열 업데이트 배열 확인 후 입점업체 상품수 업데이트
	$prcodelist = implode(',',$displist);
	if(ord($prcodelist)) {
		$prcodelist = str_replace(",","','",$prcodelist);

		$arrvender=array();
		$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('{$prcodelist}') AND vender>0 ";
		$sql.= "GROUP BY vender ";
		$p_result=pmysql_query($sql,get_db_conn());
		while($p_row=pmysql_fetch_object($p_result)) {
			$arrvender[]=$p_row->vender;
		}
		pmysql_free_result($p_result);

		for($yy=0;$yy<count($arrvender);$yy++) {
			//미니샵 상품수 업데이트 (진열된 상품만)
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='{$arrvender[$yy]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			pmysql_free_result($result);

			setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);
		}
	}

	if ($movecount!=0) {
		$onload="<script>alert('{$movecount} 건의 상품정보가 수정되었습니다.');</script>";
	}
}

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY id ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";
include("header.php"); 
?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>

<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function CheckForm() {
	try {
		if (typeof(document.form1["aproductcode[]"])!="object") {
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
		document.form1.mode.value="update";
		document.form1.submit();
	}
}

function ProductMouseOver(Obj) {
	obj = event.srcElement;
	WinObj=document.getElementById(Obj);
	obj._tid = setTimeout("ProductViewImage(WinObj)",200);
}
function ProductViewImage(WinObj) {
	WinObj.style.display = "";
	
	if(!WinObj.height)
		WinObj.height = WinObj.offsetTop;

	WinObjPY = WinObj.offsetParent.offsetHeight;
	WinObjST = WinObj.height-WinObj.offsetParent.scrollTop;
	WinObjSY = WinObjST+WinObj.offsetHeight;

	if(WinObjPY < WinObjSY)
		WinObj.style.top = WinObj.offsetParent.scrollTop-WinObj.offsetHeight+WinObjPY;
	else if(WinObjST < 0)
		WinObj.style.top = WinObj.offsetParent.scrollTop;
	else
		WinObj.style.top = WinObj.height;
}
function ProductMouseOut(Obj) {
	obj = event.srcElement;
	WinObj = document.getElementById(Obj);
	WinObj.style.display = "none";
	clearTimeout(obj._tid);
}

function GoPage(block,gotopage,sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.mode.value = "";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
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

function DivDefaultReset()
{
	if(!self.id)
	{
		self.id = self.name;
		parent.document.getElementById(self.id).style.height = parent.document.getElementById(self.id).height;
	}
}
DivDefaultReset();
</script>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="table-layout:fixed">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=searchtype value="<?=$searchtype?>">
<input type=hidden name=keyword value="<?=$keyword?>">
<input type=hidden name=sort value="<?=$sort?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');"><B>진열순</B></a> | <A HREF="javascript:GoSort('price');"><B>가격순</B></a> | <A HREF="javascript:GoSort('productname');"><B>상품명순</B></a> | <A HREF="javascript:GoSort('production');"><B>제조사순</B></a></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
<?php
			$colspan=10;
			if($vendercnt>0) $colspan++;
?>
			<col width=40></col>
			<?php if($vendercnt>0){?>
			<col width=60></col>
			<?php }?>
			<col width=50></col>
			<col width=></col>
			<col width=80></col>
			<col width=55></col>
			<col width=55></col>
			<col width=55></col>
			<col width=90></col>
			<col width=40></col>
			<col width=43></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			<TR align="center">
				<TD class="table_cell3" style="font-size:11px;"><b>No</b></TD>
				<?php if($vendercnt>0){?>
				<TD class="table_cell1" style="font-size:11px;">입점업체</TD>
				<?php }?>
				<TD class="table_cell1" style="font-size:11px;" colspan="2">상품명</TD>
				<TD class="table_cell1" style="font-size:11px;">제조사</TD>
				<TD class="table_cell1" style="font-size:11px;">시중가</TD>
				<TD class="table_cell1" style="font-size:11px;">구입가</TD>
				<TD class="table_cell1" style="font-size:11px;">판매가</TD>
				<TD class="table_cell1" style="font-size:11px;">적립금(률)</TD>
				<TD class="table_cell1" style="font-size:11px;">수량</TD>
				<TD class="table_cell1" style="font-size:11px;">진열</TD>
			</TR>
<?php
	if (($searchtype=="0" && strlen($code)==12) || ($searchtype=="1" && strlen($keyword)>2)) {
		$page_numberic_type=1;
		if ($searchtype=="0" && strlen($code)==12) {
			$qry = "AND productcode LIKE '{$code}%' ";
		} else {
			$qry = "AND productname LIKE '%{$keyword}%' ";
		}
		$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct WHERE 1=1 ";
		$sql0.= $qry;
		$paging = new Paging($sql0,10,10);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT option_price,productcode,productname,production,sellprice,consumerprice, ";
		$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct ";
		$sql.= "FROM tblproduct WHERE 1=1 ";
		$sql.= $qry." ";
		if ($sort=="price")				$sql.= "ORDER BY sellprice ";
		elseif ($sort=="production")	$sql.= "ORDER BY production ";
		elseif ($sort=="productname")	$sql.= "ORDER BY productname ";
		else							$sql.= "ORDER BY date DESC ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
?>
				<input type="hidden" name="aproductcode[]" value="<?=$row->productcode?>">
				<input type="hidden" name="aassembleproduct[]" value="<?=$row->assembleproduct?>">
				<input type="hidden" name="aassembleuse[]" value="<?=$row->assembleuse?>">
				<tr>
					<TD colspan="<?=$colspan?>" background="images/table_con_line.gif"></TD>
				</tr>
				<tr>
					<td align="center" style="font-size:8pt;padding:2"><?=$number?></td>
<?php
				if($vendercnt>0) {
					echo "	<td class=\"td_con1\" align=\"center\" style=\"font-size:8pt\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
				}
				echo "	<TD class=\"td_con1\">";
				if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
					echo "<img src='".$imagepath.$row->tinyimage."' height=40 width=40 border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
				} else {
					echo "<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
				}
				echo "<div id=\"primage{$cnt}\" style=\"position:absolute; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
				echo "		<tr bgcolor=\"#FFFFFF\">\n";
				if (ord($row->tinyimage) && file_exists($imagepath.$row->tinyimage)){
					echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"".$imagepath.$row->tinyimage."\" border=\"0\"></td>\n";
				} else {
					echo "		<td align=\"center\" width=\"100%\" height=\"150\" style=\"border:#000000 solid 1px;\"><img src=\"{$Dir}images/product_noimg.gif\" border=\"0\"></td>\n";
				}
				echo "		</tr>\n";
				echo "		</table>\n";
				echo "		</div>\n";
				echo "	</td>\n";
?>
					<td class="td_con1"><input type=text name="aproductname[]" maxlength=250 value="<?=str_replace("\"","&quot",$row->productname) ?>" style="font-size:8pt;width:100%;" onKeyDown="chkFieldMaxLen(250)" class="input"></td>
					<td class="td_con1"><input type=text name="aproduction[]" maxlength=20 value="<?=str_replace("\"","&quot",$row->production) ?>" style="font-size:8pt;width:100%;" class="input"></td>
					<td class="td_con1"><input type=text name="aconsumerprice[]" maxlength=8 value="<?=$row->consumerprice?>" style="font-size:8pt;width:100%;text-align:right" class="input"></td>
					<td class="td_con1"><input type=text name="abuyprice[]" maxlength=8 value="<?=$row->buyprice?>" style="font-size:8pt;width:100%;text-align:right" class="input"></td>

					<?php if($row->assembleuse=="Y") { ?>
					<td class="td_con1" align="right" style="font-size:8pt;"><input type=hidden name="asellprice[]" value="<?=$row->sellprice?>"><?=$row->sellprice?></td>
					<?php } else { ?>
					<td class="td_con1"><input type=text name="asellprice[]" maxlength=8 value="<?=$row->sellprice?>" style="font-size:8pt;width:100%;text-align:right" class="input"></td>
					<?php } ?>

					<td class="td_con1"><input type=text name="areserve[]" size=6 maxlength=6 value="<?=$row->reserve?>" style="font-size:8pt;text-align:right" class="input" id="areserve<?=$cnt?>" onKeyUP="chkFieldMaxLenFunc(this.id,'areservetype<?=$cnt?>');"><select name="areservetype[]" style="width:36px;font-size:8pt;margin-left:1px;" id="areservetype<?=$cnt?>" onchange="chkFieldMaxLenFunc('areserve<?=$cnt?>',this.id);"><option value="N"<?=($row->reservetype!="Y"?" selected":"")?>>￦<option value="Y"<?=($row->reservetype!="Y"?"":" selected")?>>%</select></td>
					<td class="td_con1"><input type=text name="aquantity[]" maxlength=3 value="<?=$row->quantity?>" style="font-size:8pt;width:100%;text-align:right" class="input"></td>
					<td class="td_con1"><select name="adisplay[]" style="font-size:8pt;width:100%;"><option value="Y" <?php if ($row->display=="Y") echo "selected" ?>>Y<option value="N" <?php if ($row->display=="N") echo "selected" ?>>N</select></td>
				</tr>
				<input type="hidden" name="aproductname2[]" value="<?=str_replace("\"","&quot",$row->productname)?>">
				<input type="hidden" name="aproduction2[]" value="<?=str_replace("\"","&quot",$row->production)?>">
				<input type="hidden" name="aconsumerprice2[]" value="<?=$row->consumerprice?>">
				<input type="hidden" name="abuyprice2[]" value="<?=$row->buyprice?>">
				<input type="hidden" name="asellprice2[]" value="<?=$row->sellprice?>">
				<input type="hidden" name="areserve2[]" value="<?=$row->reserve?>">
				<input type="hidden" name="areservetype2[]" value="<?=($row->reservetype!="Y"?"N":"Y")?>">
				<input type="hidden" name="aquantity2[]" value="<?=$row->quantity?>">
				<input type="hidden" name="adisplay2[]" value="<?=$row->display?>">
<?php
			$cnt++;
		}
		pmysql_free_result($result);
		if ($cnt==0) {
			$page_numberic_type="";
			echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=lineleft colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
		}
	} else {
		echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=linebottomleft colspan={$colspan} align=center>상품카테고리를 선택하거나 검색을 해주세요.</td></tr>";
	}
?>
			<TR>
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			</TABLE>
			</td>
		</tr>
<?php
	if($page_numberic_type) {
		echo "<tr>\n";
		echo "	<td width=\"100%\" height=\"50\" background=\"images/blueline_bg.gif\" align=\"center\">\n";
		echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
	}
?>
		<tr>
			<td style="padding:10px;BORDER-top:#0099CC 2px solid;" align="center"><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
		</tr>
		</table>
		</td>
	</tr>
	</form>

	<?php if($vendercnt>0){?>
	<form name=vForm action="vender_infopop.php" method=post>
	<input type=hidden name=vender>
	</form>
	<?php }?>

	</table>
	</td>
</tr>
</table>
<?=$onload?>
</body>
</html>
