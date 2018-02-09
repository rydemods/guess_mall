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
	for($i=0;$i<$size;$i++) {
		if(ord($quantity[$i]) || $array_display[$i]!=$display2[$i]) {
			$prcodes.=$productcode[$i].",";
			$sql = "UPDATE tblproduct SET display = '{$array_display[$i]}' ";
			if(ord($quantity[$i])) {
				$sql.= ", quantity = '{$quantity[$i]}' ";
			}
			$sql.= "WHERE productcode = '{$productcode[$i]}' ";
			pmysql_query($sql,get_db_conn());
			$u_cnt++;
		}
	}
	if(ord($prcodes)) {
		$prcodes=rtrim($prcodes,',');
		$prcodelist=str_replace(',','\',\'',$prcodes);

		$arrvender=array();
		$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('{$prcodelist}') AND vender>0 GROUP BY vender ";
		$p_result=pmysql_query($sql,get_db_conn());
		while($p_row=pmysql_fetch_object($p_result)) {
			$arrvender[]=$p_row->vender;
		}
		pmysql_free_result($p_result);

		for($yy=0;$yy<count($arrvender);$yy++) {
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct 
			WHERE vender='{$arrvender[$yy]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			pmysql_free_result($result);

			$sql ="UPDATE tblvenderstorecount SET prdt_allcnt='{$prdt_allcnt}', prdt_cnt='{$prdt_cnt}' 
			WHERE vender='{$arrvender[$yy]}' ";
			pmysql_query($sql,get_db_conn());
		}
	}
	echo "<html></head><body onload=\"alert('총 {$u_cnt}개의 해당 상품을 변경하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
} else if ($mode=="delete" && ord($allprcode)) {
	$prcodelist = str_replace("|","','",$allprcode);

	$arrvender=array();
	$arrvenderid=array();
	$arrpridx=array();
	$arrassembleuse=array();
	$arrassembleproduct=array();
	$arrproductcode=array();
	$sql = "SELECT vender FROM tblproduct WHERE productcode IN ('{$prcodelist}') ";
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

	$sql = "DELETE FROM tblproduct ";
	$sql.= "WHERE productcode IN ('{$prcodelist}')";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "DELETE FROM tblproductgroupcode ";
		$sql.= "WHERE productcode IN ('{$prcodelist}')";
		$result = pmysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblproducttheme ";
		$sql.= "WHERE productcode IN ('{$prcodelist}')";
		$result = pmysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblproductreview ";
		$sql.= "WHERE productcode IN ('{$prcodelist}')";
		pmysql_query($sql,get_db_conn());

		#태그관련 지우기
		$sql = "DELETE FROM tbltagproduct ";
		$sql.= "WHERE productcode IN ('{$prcodelist}')";
		pmysql_query($sql,get_db_conn());

		$sql = "DELETE FROM tblwishlist ";
		$sql.= "WHERE productcode IN ('{$prcodelist}')";
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
			$sql = "DELETE FROM tblvenderthemeproduct WHERE vender='{$arrvender[$yy]}' ";
			$sql.= "AND productcode IN ('{$prcodelist}') ";
			pmysql_query($sql,get_db_conn());

			//미니샵 상품수 업데이트 (진열된 상품만)
			$sql = "SELECT COUNT(*) as prdt_allcnt, COUNT(CASE WHEN display='Y' THEN 1 ELSE NULL END) as prdt_cnt FROM tblproduct ";
			$sql.= "WHERE vender='{$arrvender[$yy]}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			$prdt_allcnt=(int)$row->prdt_allcnt;
			$prdt_cnt=(int)$row->prdt_cnt;
			pmysql_free_result($result);

			$sql ="UPDATE tblvenderstorecount SET prdt_allcnt='{$prdt_allcnt}', prdt_cnt='{$prdt_cnt}' ";
			$sql.="WHERE vender='{$arrvender[$yy]}' ";
			pmysql_query($sql,get_db_conn());

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
					$sql = "DELETE FROM tblvendercodedesign WHERE vender='{$arrvender[$yy]}' ";
					$sql.= "AND code IN ('{$str_code_a}') AND tgbn='10' ";
					pmysql_query($sql,get_db_conn());
				}
			}
		}

		$log_content = "## 입점업체 품절 상품삭제 ## - 상품코드 ".str_replace("|",",",$allprcode)."";
		ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

		$prcode = explode("|",$allprcode);
		$cnt = count($prcode);

		for($i=0;$i<$cnt;$i++){
			$delshopimage=$Dir.DataDir."shopimages/product/{$prcode[$i]}*";
			proc_matchfiledel($delshopimage);
			delProductMultiImg("prdelete","",$prcode[$i]);
		}
		echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
	} else {
		echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.');\"></body></html>";exit;
	}
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
$qry.= "AND quantity <= 0 ";


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

function CheckUpdate(cnt) {
	if(cnt==0) return;
	if (cnt>1) {
		for(i=0;i<cnt;i++){
			if(isNaN(document.form2["quantity[]"][i].value)){
				alert('수량은 숫자만 입력이 가능합니다.');
				document.form2["quantity[]"][i].focus();
				return;
			}
		   if(document.form2["display[]"][i].checked) document.form2.alldisplay.value+="N|";
		   else document.form2.alldisplay.value+="Y|";
		}
	} else {
		if(isNaN(document.form2["quantity[]"].value)){
			alert('수량은 숫자만 입력이 가능합니다.');
			document.form2["quantity[]"].focus();
			return;
		}
	   if(document.form2["display[]"].checked) document.form2.alldisplay.value+="N|";
	   else document.form2.alldisplay.value+="Y|";
	}
	if(confirm("수정 하시겠습니까?")) {
		document.form2.mode.value="update";
		document.form2.target="processFrame";
		document.form2.submit();
	}
}

function CheckDelete(cnt) {
	if(cnt==0) return;
	ischeck=false;
	prcode="";
	if (cnt>1) {
		for (i=0;i<cnt;i++) {
			if (document.form2["delcheck[]"][i].checked) {
				ischeck=true;
			   if(prcode=="") prcode=document.form2["delcheck[]"][i].value;
			   else prcode=prcode+"|"+document.form2["delcheck[]"][i].value;
			}
		}
	} else {
		if (document.form2["delcheck[]"].checked) {
			ischeck=true;
		   if(prcode=="") prcode=document.form2["delcheck[]"].value;
		   else prcode=prcode+"|"+document.form2["delcheck[]"].value;
		}
	}
	if (ischeck && confirm("선택하신 상품을 정말로 삭제하시겠습니까?")) {
		document.form2.mode.value="delete";
		document.form2.allprcode.value=prcode;
		document.form2.target="processFrame";
		document.form2.submit();
	} else if (ischeck==false) {
		alert('삭제하시려는 상품을 선택하세요.');
		return;
	}
}

function ProductInfo(code,prcode,popup) {
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

function CheckAll(){	//숨김
	try {
		checkvalue=document.form2.allcheck.checked;
		if (typeof(document.form2["display[]"].length)=="number") {
			cnt=document.form2["display[]"].length;
			for(i=0;i<cnt;i++){
				document.form2["display[]"][i].checked=checkvalue;
			}
		} else {
			document.form2["display[]"].checked=checkvalue;
		}
	} catch(e) {}
}

function CheckAll2(){	//삭제
	try {
		checkvalue=document.form2.allcheck2.checked;
		if (typeof(document.form2["delcheck[]"].length)=="number") {
			cnt=document.form2["delcheck[]"].length;
			for(i=0;i<cnt;i++){
				document.form2["delcheck[]"][i].checked=checkvalue;
			}
		} else {
			document.form2["delcheck[]"].checked=checkvalue;
		}
	} catch(e) {}
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
			<td height="28" class="link" align="right"><img src="images/top_link_house.gif" border="0" valign="absmiddle">현재위치 : 입점관리 &gt; 입점상품 관리 &gt; <span class="2depth_select">품절상품 일괄 삭제/관리</span></td>
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
					<TD><IMG SRC="images/vender_prdtallsoldout_title.gif" WIDTH="208" HEIGHT=32 ALT=""></TD>
					<TD width="100%" background="images/title_bg.gif"></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="3"></td>
			</tr>
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
					<TD width="100%" class="notice_blue">해당 입점업체의 품절된 상품을 전체적으로 삭제/등록 등 관리를 할 수 있습니다.</TD>
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
			<tr>
				<td height="20"></td>
			</tr>
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
			<input type=hidden name=alldisplay>
			<input type=hidden name=allprcode>
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=40></col>
				<col width=120></col>
				<col width=70></col>
				<col width=></col>
				<col width=60></col>
				<col width=70></col>
				<col width=45></col>
				<col width=35></col>
				<col width=35></col>
				<TR>
					<TD height=1 background="images/table_top_line.gif" colspan="9"></TD>
				</TR>
				<TR align="center">
					<TD class="table_cell"><B>번호</B></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by productcode asc"?"order by productcode desc":"order by productcode asc")?>')"; onMouseover="self.status=''; return true; "><B>상품코드</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by vender asc"?"order by vender desc":"order by vender asc")?>')"; onMouseover="self.status=''; return true; "><B>입점업체</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by productname asc"?"order by productname desc":"order by productname asc")?>')"; onMouseover="self.status=''; return true; "><B>품절 상품명</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by sellprice asc"?"order by sellprice desc":"order by sellprice asc")?>')"; onMouseover="self.status=''; return true; "><B>가격</B></a></TD>
					<TD class="table_cell1"><a href="javascript:OrderSort('<?=($sort=="order by regdate asc"?"order by regdate desc":"order by regdate asc")?>')"; onMouseover="self.status=''; return true; "><B>등록일</B></a></TD>
					<TD class="table_cell1"><B>수량</B></TD>
					<TD class="table_cell1"><B>숨김</B></TD>
					<TD class="table_cell1"><B>삭제</B></TD>
				</TR>
				<TR>
					<TD height=1 background="images/table_con_line.gif" colspan="9"></TD>
				</TR>

<?php
			$colspan=9;
			$cnt=0;
			if($t_count>0) {
				$sql = "SELECT productcode,productname,sellprice,quantity,display,regdate,vender ";
				$sql.= "FROM tblproduct {$qry} {$sort} ";
				$sql = $paging->getSql($sql);
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					echo "<tr align=center bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
					echo "	<td class=\"td_con2\">{$number}</td>\n";
					echo "	<td class=\"td_con1\">{$row->productcode}</td>\n";
					echo "	<td class=\"td_con1\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->id}</a>":"-")."</B></td>\n";
					echo "	<td class=\"td_con1\" style=\"word-break:break-all;\"><!--A HREF=\"javascript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','')\"-->".titleCut(48,$row->productname)."</A> <A HREF=\"javascript:ProductInfo('".substr($row->productcode,0,12)."','{$row->productcode}','YES')\"><img src=images/newwindow.gif border=0 align=absmiddle></A></td>\n";
					echo "	<td class=\"td_con1\">".number_format($row->sellprice)."</td>\n";
					echo "	<td class=\"td_con1\">".substr($row->regdate,0,10)."</td>\n";
					echo "	<td class=\"td_con1\"><input type=text name=quantity[] size=3 maxlength=8></td>\n";
					echo "	<td class=\"td_con1\"><input type=checkbox name=display[] value=\"Y\"";
						if($row->display=="N") echo " checked";
						echo "></td>\n";
					echo "	<td class=\"td_con1\"><input type=checkbox name=delcheck[] value=\"{$row->productcode}\"></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<TD height=1 background=\"images/table_con_line.gif\" colspan=\"9\"></TD>\n";
					echo "</tr>\n";
					echo "<input type=hidden name=productcode[] value=\"{$row->productcode}\">";
					echo "<input type=hidden name=display2[] value=\"{$row->display}\">";
					$cnt++;
				}
				pmysql_free_result($result);

				if($cnt>0) {
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
				<tr><td align=right colspan=<?=$colspan?>>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<tr>
					<td>상품 전체 선택/해제 (<input type=checkbox name=allcheck onclick="CheckAll(<?=$cnt?>);"><B>숨김</B> / <input type=checkbox name=allcheck2 onclick="CheckAll2(<?=$cnt?>);"><B>삭제</B>)</td>
					<td align=right><input type="image" src="images/btn_modify06.gif" border="0" style="cursor:hand" onclick="CheckUpdate('<?=$cnt?>');">
					&nbsp;
					<input type="image" src="images/btn_delete01.gif" border="0" style="cursor:hand" onclick="CheckDelete('<?=$cnt?>');">
					</td>
				</tr>
				</table>
				</td></tr>
				<tr><td colspan=<?=$colspan?> height=10></td></tr>
				<tr><td colspan=<?=$colspan?> align=center class="font_size"><?=$pageing?></td></tr>
				<tr><td colspan=<?=$colspan?> height=10></td></tr>
				</form>
				</table>
				</td>
			</tr>
			<form name="pageForm" method="post">
			<input type=hidden name='code' value='<?=$code?>'>
			<input type=hidden name='vender' value='<?=$vender?>'>
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

			<form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
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
						<td width="701"><span class="font_dotline">품절상품 일괄 삭제/관리</span></td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 입점업체가 등록한 상품중 품절된 상품을 확인할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 본사 관리자는 품절된 상품의 수량조절 및 숨김/삭제할 수 있습니다.</td>
					</tr>
					<tr>
						<td width="20" align="right">&nbsp;</td>
						<td width="701" class="space_top">- 삭제된 상품은 입점사 상품리스트에서도 삭제됩니다.</td>
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
