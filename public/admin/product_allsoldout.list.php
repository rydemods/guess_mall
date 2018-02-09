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

$mode=$_POST["mode"];
$code=$_POST["code"];
$search=$_POST["search"];
$s_check=(int)$_POST["s_check"];

$quantity=(array)$_POST["quantity"];
$alldisplay=$_POST["alldisplay"];
$allprcode=$_POST["allprcode"];

$productcode=(array)$_POST["productcode"];
$display2=(array)$_POST["display2"];

if ($mode=="update" && count($productcode)>0) {
	$prcodes="";
	$array_display=explode("|",$alldisplay);
	$size=count($productcode);
	$u_cnt=0;
	$_=array();	
	for($i=0;$i<$size;$i++) {
		if(ord($quantity[$i]) || $array_display[$i]!=$display2[$i]) {
			$_[] = $productcode[$i];
			$sql = "UPDATE tblproduct SET display = '{$array_display[$i]}' ";
			if(ord($quantity[$i])) {
				$sql.= ", quantity = '{$quantity[$i]}' ";
			}
			$sql.= "WHERE productcode = '{$productcode[$i]}' ";
			pmysql_query($sql,get_db_conn());
			$u_cnt++;
		}
	}
	$prcodes = implode(',',$_);
	if(ord($prcodes)) {
		$prcodelist=str_replace(',','\',\'',$prcodes);

		$arrvender=array();
		$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('{$prcodelist}') AND vender>0 ";
		$sql.= "GROUP BY vender ";
		$p_result=pmysql_query($sql,get_db_conn());
		while($p_row=pmysql_fetch_object($p_result)) {
			$arrvender[]=$p_row->vender;
		}
		pmysql_free_result($p_result);

		for($yy=0;$yy<count($arrvender);$yy++) {
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
	$onload="<script>alert('총 {$u_cnt}개의 해당 상품을 변경하였습니다.');</script>";
} elseif ($mode=="delete" && ord($allprcode)) {
	$prcodelist = str_replace("|","','",$allprcode);

	$arrvender=array();
	$arrvenderid=array();
	$arrpridx=array();
	$arrassembleuse=array();
	$arrassembleproduct=array();
	$arrproductcode=array();
	$sql = "SELECT vender,pridx,assembleuse,assembleproduct,productcode FROM tblproduct ";
	$sql.= "WHERE productcode IN ('{$prcodelist}') ";
	$p_result=pmysql_query($sql,get_db_conn());
	while($p_row=pmysql_fetch_object($p_result)) {
		if($p_row->vender>0 && ord($arrvenderid[$p_row->vender])==0) {
			$arrvender[]=$p_row->vender;
			$arrvenderid[$p_row->vender]=$p_row->vender;
		}
		$arrpridx[]=$p_row->pridx;
		$arrassembleuse[]=$p_row->assembleuse;
		$arrassembleproduct[]=$p_row->assembleproduct;
		$arrproductcode[]=$p_row->productcode;
	}
	pmysql_free_result($p_result);

	$sql = "DELETE FROM tblproduct WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproducttheme WHERE productcode IN ('{$prcodelist}')";
	$result = pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductreview WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblproductgroupcode WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	#태그관련 지우기
	$sql = "DELETE FROM tbltagproduct WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblwishlist WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	$sql = "DELETE FROM tblcollection WHERE productcode IN ('{$prcodelist}')";
	pmysql_query($sql,get_db_conn());

	for($vz=0; $vz<count($arrpridx); $vz++) { // 코디/조립 기본구성상품의 가격 처리		
		if($arrassembleuse[$vz]=="Y") {
			$sql = "SELECT assemble_pridx FROM tblassembleproduct ";
			$sql.= "WHERE productcode = '{$arrproductcode[$vz]}' ";
			$result = pmysql_query($sql,get_db_conn());
			if($row = @pmysql_fetch_object($result)) {
				$sql = "DELETE FROM tblassembleproduct WHERE productcode = '{$arrproductcode[$vz]}' ";
				pmysql_query($sql,get_db_conn());
				
				if(ord(str_replace("","",$row->assemble_pridx))) {
					$sql = "UPDATE tblproduct SET ";
					$sql.= "assembleproduct = REPLACE(assembleproduct,',{$arrproductcode[$vz]}','') ";
					$sql.= "WHERE pridx IN ('".str_replace("","','",$row->assemble_pridx)."') ";
					$sql.= "AND assembleuse != 'Y' ";
					pmysql_query($sql,get_db_conn());
				}
			}
			pmysql_free_result($result);
		} else {
			if(ord($arrassembleproduct[$vz])) {
				$sql = "SELECT productcode, assemble_pridx FROM tblassembleproduct ";
				$sql.= "WHERE productcode IN ('".str_replace(",","','",$arrassembleproduct[$vz])."') ";
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

			$sql = "UPDATE tblassembleproduct SET ";
			$sql.= "assemble_pridx=REPLACE(assemble_pridx,'{$arrpridx[$vz]}',''), ";
			$sql.= "assemble_list=REPLACE(assemble_list,',{$arrpridx[$vz]}','') ";
			pmysql_query($sql,get_db_conn());
		}
	}

	for($yy=0;$yy<count($arrvender);$yy++) {
		//미니샵 테마코드에 등록된 상품 삭제
		setVenderThemeDelete($prcodelist, $arrvender[$yy]);

		//미니샵 상품수 업데이트 (진열된 상품만)
		$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
		$sql.= "WHERE vender='{$arrvender[$yy]}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		$prdt_allcnt=(int)$row->prdt_allcnt;
		$prdt_cnt=(int)$row->prdt_cnt;
		pmysql_free_result($result);

		setVenderCountUpdate($prdt_allcnt, $prdt_cnt, $arrvender[$yy]);

		//tblvendercodedesign => 해당 대분류 상품 확인 후 없으면 대분류 화면 삭제
		$tmpcode_a=array();
		$arrprcode=explode("|",$allprcode);
		for($j=0;$j<count($arrprcode);$j++) {
			$tmpcode_a[substr($arrprcode[$j],0,3)]=true;
		}

		if(count($tmpcode_a)>0) {
			$sql = "SELECT SUBSTR(productcode,1,3) as code_a FROM tblproduct ";
			$sql.= "WHERE ( ";
			$arr_code_a=$tmpcode_a;
			$_=array();			
			while(list($key,$val)=each($arr_code_a)) {
				if(strlen($key)==3) {
					$_[] = "productcode LIKE '{$key}%' ";
				}
			}
			$sql.= implode("OR ",$_);
			$sql.= ") ";
			$sql.= "AND vender='{$arrvender[$yy]}' ";
			$sql.= "GROUP BY code_a ";
			$result=pmysql_query($sql,get_db_conn());
			while($row=pmysql_fetch_object($result)) {
				unset($tmpcode_a[$row->code_a]);
			}
			pmysql_free_result($result);

			if(count($tmpcode_a)>0) {
				while(list($key,$val)=each($tmpcode_a)) {					
					$imagename = $Dir.DataDir."shopimages/vender/{$arrvender[$yy]}_CODE10_{$key}.gif";
					@unlink($imagename);
				}
				$str_code_a = implode(',',array_keys($tmpcode_a));
				$str_code_a=str_replace(',','\',\'',$str_code_a);
				setVenderDesignDelete($str_code_a, $arrvender[$yy]);
			}
		}
	}

	$log_content = "## 품절 상품삭제 ## - 상품코드 ".str_replace("|",",",$allprcode)."";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$prcode = explode("|",$allprcode);
	$cnt = count($prcode);

	for($i=0;$i<$cnt;$i++){
		$delshopimage=$Dir.DataDir."shopimages/product/{$prcode[$i]}*";
		proc_matchfiledel($delshopimage);
		delProductMultiImg("prdelete","",$prcode[$i]);
	}
	$onload="<script>alert('해당 상품을 삭제하였습니다.');</script>";
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

function CheckUpdate(cnt) {
	if(cnt==0) return;
	if (cnt>1) {
		for(i=0;i<cnt;i++){
			if(isNaN(document.form1["quantity[]"][i].value)){
				alert('수량은 숫자만 입력이 가능합니다.');
				document.form1["quantity[]"][i].focus();
				return;
			}
			if(document.form1["display[]"][i].checked) document.form1.alldisplay.value+="N|";
			else document.form1.alldisplay.value+="Y|";
		}
	} else {
		if(isNaN(document.form1["quantity[]"].value)){
			alert('수량은 숫자만 입력이 가능합니다.');
			document.form1["quantity[]"].focus();
			return;
		}
		if(document.form1["display[]"].checked) document.form1.alldisplay.value+="N|";
		else document.form1.alldisplay.value+="Y|";
	}
	document.form1.mode.value="update";
	document.form1.search.value="OK";
	document.form1.block.value="";
	document.form1.gotopage.value="";
	document.form1.submit();
}

function CheckDelete(cnt) {
	if(cnt==0) return;
	ischeck=false;
	prcode="";
	if (cnt>1) {
		for (i=0;i<cnt;i++) {
			if (document.form1["delcheck[]"][i].checked) {
				ischeck=true;
				if(prcode=="") prcode=document.form1["delcheck[]"][i].value;
				else prcode=prcode+"|"+document.form1["delcheck[]"][i].value;
			}
		}
	} else {
		if (document.form1["delcheck[]"].checked) {
			ischeck=true;
			if(prcode=="") prcode=document.form1["delcheck[]"].value;
			else prcode=prcode+"|"+document.form1["delcheck[]"].value;
		}
	}
	if (ischeck && confirm("선택하신 상품을 정말로 삭제하시겠습니까?")) {
		document.form1.mode.value="delete";
		document.form1.allprcode.value=prcode;
		document.form1.search.value="OK";
		document.form1.block.value="";
		document.form1.gotopage.value="";
		document.form1.submit();
	} else if (ischeck==false) {
		alert('삭제하시려는 상품을 선택하세요.');
		return;
	}
}

function ProductInfo(prcode) {
	code=prcode.substring(0,12);
	popup="YES";
	document.form_reg.code.value=code;
	document.form_reg.prcode.value=prcode;
	document.form_reg.popup.value=popup;
	if (popup=="YES") {
		document.form_reg.action="product_register.add.php";
		document.form_reg.target="register";
		window.open("about:blank","register","width=820,height=700,scrollbars=yes,status=no");
	} else {
		document.form_reg.action="product_register.php";
		document.form_reg.target="";
	}
	document.form_reg.submit();
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
	document.form1.search.value = "OK";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.sort.value = sort;
	document.form1.submit();
}

function GoSort(sort) {
	document.form1.search.value = "OK";
	document.form1.sort.value = sort;
	document.form1.block.value = "";
	document.form1.gotopage.value = "";
	document.form1.submit();
}

function CheckAll(){	//숨김
	try {
		checkvalue=document.form1.allcheck.checked;
		if (typeof(document.form1["display[]"].length)=="number") {
			cnt=document.form1["display[]"].length;
			for(i=0;i<cnt;i++){
				document.form1["display[]"][i].checked=checkvalue;
			}
		} else {
			document.form1["display[]"].checked=checkvalue;
		}
	} catch(e) {}
}

function CheckAll2(){	//삭제
	try {
		checkvalue=document.form1.allcheck2.checked;
		if (typeof(document.form1["delcheck[]"].length)=="number") {
			cnt=document.form1["delcheck[]"].length;
			for(i=0;i<cnt;i++){
				document.form1["delcheck[]"][i].checked=checkvalue;
			}
		} else {
			document.form1["delcheck[]"].checked=checkvalue;
		}
	} catch(e) {}
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
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=code value="<?=$code?>">
<input type=hidden name=block>
<input type=hidden name=gotopage>
<input type=hidden name=alldisplay>
<input type=hidden name=allprcode>
<input type=hidden name=sort value="<?=$sort?>">
<tr>
	<td width="100%" bgcolor="#FFFFFF"><IMG SRC="images/product_mainlist_text.gif" border="0"></td>
</tr>
<tr>
	<td width="100%" height="100%" valign="top" style="BORDER:#FF8730 2px solid;padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%" style="padding-top:2pt; padding-bottom:2pt;" height="30"><B><span class="font_orange">* 정렬방법 :</span></B> <A HREF="javascript:GoSort('date');"><B>진열순</B></a> | <A HREF="javascript:GoSort('price');"><B>가격순</B></a> | <A HREF="javascript:GoSort('productname');"><B>상품명순</B></a></td>
	</tr>
	<tr>
		<td width="100%" valign="top">
		<DIV style="width:100%;height:100%;overflow:hidden;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
<?php
			$colspan=8;
			if($vendercnt>0) $colspan++;
?>
			<col width=40></col>
			<?php if($vendercnt>0){?>
			<col width=70></col>
			<?php }?>
			<col width=50></col>
			<col width=></col>
			<col width=70></col>
			<col width=40></col>
			<col width=40></col>
			<col width=40></col>
			<col width=50></col>
			<TR>
				<TD background="images/table_top_line.gif" colspan="<?=$colspan?>"></TD>
			</TR>
			<TR align=center>
				<TD class="table_cell3" style="font-size:11px;"><b>No</b></TD>
				<?php if($vendercnt>0){?>
				<TD class="table_cell1" style="font-size:11px;">입점업체</TD>
				<?php }?>
				<TD class="table_cell1" style="font-size:11px;" colspan="2">품절된 상품명/진열코드/특이사항</TD>
				<TD class="table_cell1" style="font-size:11px;">판매가격</TD>
				<TD class="table_cell1" style="font-size:11px;">수량</TD>
				<TD class="table_cell1" style="font-size:11px;">진열<br>안함</TD>
				<TD class="table_cell1" style="font-size:11px;">삭제</TD>
				<TD class="table_cell1" style="font-size:11px;">수정</TD>
			</TR>
<?php
			if ($search=="OK" && strlen($code)==12) {
				$page_numberic_type=1;
				$sql = "SELECT code_a||code_b||code_c||code_d as code, type,code_name FROM tblproductcode ";
				$result = pmysql_query($sql,get_db_conn());
				while ($row=pmysql_fetch_object($result)) {
					$code_name[$row->code] = $row->code_name;
				}
				pmysql_free_result($result);

				list($code_a,$code_b,$code_c,$code_d) = sscanf($code,'%3s%3s%3s%3s');
				$likecode=$code_a;
				if($code_b!="000") {
					$likecode.=$code_b;
					if($code_c!="000") {
						$likecode.=$code_c;
						if($code_d!="000") {
							$likecode.=$code_d;
						}
					}
				}

				$qry ="WHERE productcode LIKE '{$likecode}%' ";
				$qry.= "AND quantity <= 0 ";
				if($s_check==1)		$qry.="AND display = 'N' ";
				else if($s_check==2)$qry.="AND display = 'Y' ";

				$sql = "SELECT COUNT(*) as t_count FROM tblproduct ".$qry;
				$paging = new Paging($sql,10,10);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT productcode,display,addcode,productname,quantity,tinyimage,vender,sellprice,reserve,reservetype,selfcode,assembleuse FROM tblproduct ";
				$sql.= $qry;
				if ($sort=="price")				$sql.= "ORDER BY sellprice ";
				else if ($sort=="productname")	$sql.= "ORDER BY productname ";
				else							$sql.= "ORDER BY date DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

					if (ord($row->quantity) == 0) $quantity = "무제한";
					elseif ($row->quantity > 0) $quantity = $row->quantity;
					elseif ($row->quantity < 1) $quantity = "<font color=red>품절</font>";

					$codename="";
					list($code_a,$code_b,$code_c,$code_d) = sscanf($row->productcode,'%3s%3s%3s%3s');
					if($code_b=="000") $code_b="";
					if($code_c=="000") $code_c="";
					if($code_d=="000") $code_d="";
					$codename.=$code_name[$code_a."000000000"];
					if(ord($code_name[$code_a.$code_b."000000"])) {
						$codename.=" > ".$code_name[$code_a.$code_b."000000"];
					}
					if(ord($code_name[$code_a.$code_b.$code_c."000"])) {
						$codename.=" > ".$code_name[$code_a.$code_b.$code_c."000"];
					}

					if(ord($code_name[$code_a.$code_b.$code_c.$code_d])) {
						$codename.=" > ".$code_name[$code_a.$code_b.$code_c.$code_d];
					}
					
					echo "<tr>\n";
					echo "	<TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD>\n";
					echo "</tr>\n";
					echo "<tr align=center>";
					echo "	<TD class=\"td_con2\">{$number}</td>\n";
					if($vendercnt>0) {
						echo "	<TD class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
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
					echo "	<TD align=left class=\"td_con1\" style=\"word-break:break-all;\"><span style=\"font-size:8pt; letter-spacing:-0.5pt;\"><span class=\"font_orange\"><b>카테고리 : </b></span>{$codename}</span>";
					echo "		<br><img src=\"images/producttype".($row->assembleuse=="Y"?"y":"n").".gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><font color=#3D3D3D>".$row->productname.($row->selfcode?"-".$row->selfcode:"").($row->addcode?"-".$row->addcode:"")."</font>";
					echo "	</td>\n";
					echo "	<TD align=right class=\"td_con1\"><img src=\"images/won_icon.gif\" border=\"0\" style=\"margin-right:2px;\"><span class=\"font_orange\">".number_format($row->sellprice)."</span><br><img src=\"images/reserve_icon.gif\" border=\"0\" style=\"margin-right:2px;\">".($row->reservetype!="Y"?number_format($row->reserve):$row->reserve."%")."</TD>\n";
					echo "	<TD class=\"td_con1\"><input type=text name=quantity[] size=3 maxlength=8 class=\"input\"></td>";
					echo "	<TD class=\"td_con1\"><input type=checkbox name=display[] value=\"Y\"";
					if($row->display=="N") echo " checked";
					echo "	style=\"BORDER:none\"></td>\n";
					echo "	<TD class=\"td_con1\"><input type=checkbox name=delcheck[] value=\"{$row->productcode}\" style=\"BORDER:none\"></td>\n";
					echo "	<TD class=\"td_con1\"><a href=\"javascript:ProductInfo('{$row->productcode}');\"><img src=\"images/icon_newwin1.gif\" border=\"0\"></a></td>\n";
					echo "<input type=hidden name=productcode[] value=\"{$row->productcode}\">";
					echo "<input type=hidden name=display2[] value=\"{$row->display}\">";
					echo "</tr>\n";
					$cnt++;
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					$page_numberic_type="";
					echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
				}
			} else {
				$page_numberic_type="";
				echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 없습니다.</td></tr>";
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
			echo "	<td width=\"100%\" height=\"30\" background=\"images/blueline_bg.gif\" align=right>\n";
			echo "	상품일괄 선택/해제 (<input type=checkbox name=allcheck onclick=\"CheckAll('{$cnt}');\"><B>진열안함</B> / <input type=checkbox name=allcheck2 onclick=\"CheckAll2('{$cnt}');\"><B>삭제</B>)";
			echo "	</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "	<td width=\"100%\" height=\"30\" background=\"images/blueline_bg.gif\" align=center>\n";
			echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "	</td>\n";
			echo "</tr>\n";
		}
?>
		<tr>
			<td style="padding:10px;BORDER-top:#0099CC 2px solid;" align="center"><a href="javascript:CheckUpdate('<?=$cnt?>');"><img src="images/btn_edit2.gif" border="0"></a>&nbsp;&nbsp;<a href="javascript:CheckDelete('<?=$cnt?>');"><img src="images/btn_del3.gif" border="0"></a></td>
		</tr>
		</table>
		</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
</form>
<form name=form_reg action="product_register.php" method=post>
<input type=hidden name=code>
<input type=hidden name=prcode>
<input type=hidden name=popup>
</form>
<?php if($vendercnt>0){?>
<form name=vForm action="vender_infopop.php" method=post>
<input type=hidden name=vender>
</form>
<?php }?>
</table>
<?=$onload?>
</body>
</html>
