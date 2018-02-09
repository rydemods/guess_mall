<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-4";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$mode=$_POST["mode"];
$mode1=$_POST["mode_i"];
$mode2=$_POST["mode_i2"];
$mode3=$_POST["mode_i3"];
$idx=$_POST["idx"];
$gotopage = $_POST["gotopage"];
$keyword=$_POST["keyword"];
$s_check=$_POST["s_check"];
$display_best=$_POST["display_best"];
$display_new=$_POST["display_new"];
$display_only=$_POST["display_only"];
$display_sale=$_POST["display_sale"];
$display=$_POST["display"];
$vperiod=$_POST["vperiod"];
$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];
$search_end=$_POST["search_end"];
$search_start=$_POST["search_start"];
$sellprice_min=$_POST["sellprice_min"];
$sellprice_max=$_POST["sellprice_max"];
$code_type          = $_REQUEST["code_type"];
$sel_season         = $_REQUEST["sel_season"];
$sel_vender = $_POST['sel_vender'];             // 브랜드 지정
$s_brand_keyword = $_POST['s_brand_keyword'];   // 브랜드명으로 검색

$listnum=(int)$_REQUEST["listnum"];
if(!$listnum){
	$listnum = 20;
}

if($keyword=="상품명 상품코드")$keyword="";

$likecode="";
if($code_a!="000") $likecode.=$code_a;
if($code_b!="000") $likecode.=$code_b;
if($code_c!="000") $likecode.=$code_c;
if($code_d!="000") $likecode.=$code_d;


$regdate = $_shopdata->regdate;
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$checked["display"][$display] = "checked";
$checked["display_best"][$display_best] = "checked";
$checked["display_new"][$display_new] = "checked";
$checked["display_only"][$display_only] = "checked";
$checked["display_sale"][$display_sale] = "checked";
$checked["s_check"][$s_check] = "checked";
$checked["check_vperiod"][$vperiod] = "checked";

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
$adisplay_not=$_POST["adisplay_not"];
$adisplay_best=$_POST["adisplay_best"];
$adisplay_new=$_POST["adisplay_new"];
$adisplay_only=$_POST["adisplay_only"];
$adisplay_sale=$_POST["adisplay_sale"];

$icon = $adisplay_best.$adisplay_new.$adisplay_only.$adisplay_sale;

if ($mode1=="update" && count($idx)>0) {

	$movecount=count($idx);
	$update_ymd = date("YmdH");
	$update_ymd2 = date("is");
	$displist=array();

	for($i=0;$i<count($idx);$i++) {

        		$productname = str_replace("'","''",$aproductname[$i]);
				$production = str_replace("'","''",$aproduction[$i]);

				$sql = "UPDATE tblproduct SET ";

				if($mode2){
					$sql.= "icon			= '', ";
					$sql.= "etctype			= 'ICON=' ";
				}else if($mode3){
					$sql.= "icon			= '02', ";
					$sql.= "etctype			= 'ICON=02', ";
					$sql.= "newicon_date			= now() ";
				}else{
					$sql.= "icon			= '{$icon}', ";
					$sql.= "etctype			= 'ICON=".$icon."' ";
				}

				$sql.= "WHERE productcode='{$idx[$i]}' ";
				pmysql_query($sql,get_db_conn());
		}

	if ($movecount!=0) {
		$onload="<script>window.onload=function(){alert('{$movecount} 건의 상품정보가 수정되었습니다.');}</script>";
	}
}

// 즉시 업데이트시, 기간 설정된 내역과 중복되는 상품이 있는지 체크
function CheckDupProcut($productcode) {
    
    $sql = "select  count(*)  
            from    tblbatchapplylog 
            where   productcode = '".$productcode."'  
            and	    pidx > 0 
            and     (start_date <= '".date("Ymd")."' and end_date >= '".date("Ymd")."') 
            ";
    list($cnt) = pmysql_fetch($sql);
    if($cnt > 0) return false;
    else return true;
}

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY lower(b.brandname) ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

$imagepath=$Dir.DataDir."shopimages/product/";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function listnumSet(listnum){
	document.form1.listnum.value=listnum.value;
	document.form1.submit();
}

function resetBrandSearchWord(obj) {
    if ( $(obj).val() == "" ) {
        $("#s_brand_keyword").attr("disabled", false).val("").focus();
    } else {
        $("#s_brand_keyword").attr("disabled", true);
    }
}

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function ProductInfo(prcode) {
	code=prcode.substring(0,12);
	popup="YES";
	document.form_register.code.value=code;
	document.form_register.prcode.value=prcode;
	document.form_register.popup.value=popup;
	if (popup=="YES") {
		document.form_register.action="product_register.set.php";
		document.form_register.target="register";
		window.open("about:blank","register","width=1000,height=700,scrollbars=yes,status=no");
	} else {
		document.form_register.action="product_register.set.php";
		document.form_register.target="";
	}
	document.form_register.submit();
}

function allCheck(obj) {
    if ( $(obj).is(":checked") ) {
        $("input:checkbox[name='idx[]']").attr("checked", true);
    } else {
        $("input:checkbox[name='idx[]']").attr("checked", false);
    }
}

function CheckForm() {
    var frm = document.form1;
    var chk_count = 0;

    for (var i=0; i<frm.length; i++) {
        if (frm.elements[i].name == "idx[]" && frm.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert("수정할 상품을 하나 이상 선택하세요.");
        return false;
    }else{
		if(confirm("선택상품에 아이콘을 적용 하시겠습니까?")) {
			document.form1.mode_i.value="update";
			document.form1.submit();
		}
	}
    //return true;
}

function CheckresetForm() {
    var frm = document.form1;
    var chk_count = 0;

    for (var i=0; i<frm.length; i++) {
        if (frm.elements[i].name == "idx[]" && frm.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert("수정할 상품을 하나 이상 선택하세요.");
        return false;
    }else{
		if(confirm("선택 상품의 아이콘 적용상태가 초기화됩니다. 적용 하시겠습니까?")) {
			document.form1.mode_i.value="update";
			document.form1.mode_i2.value="reset";
			document.form1.submit();
		}
	}
    //return true;
}

function ChecknewForm() {
    var frm = document.form1;
    var chk_count = 0;

    for (var i=0; i<frm.length; i++) {
        if (frm.elements[i].name == "idx[]" && frm.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert("수정할 상품을 하나 이상 선택하세요.");
        return false;
    }else{
		if(confirm("선택상품에 'NEW' 아이콘을 적용 하시겠습니까?")) {
			document.form1.mode_i.value="update";
			document.form1.mode_i3.value="new";
			document.form1.submit();
		}
	}
    //return true;
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt;상품 일괄관리 &gt; <span>아이콘 일괄수정</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">아이콘 일괄수정</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에 등록된 상품의 아이콘을 일괄 수정할 수 있습니다.</span></div>
				</td>
			</tr>
			
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<input type=hidden name=mode>
			<input type=hidden name=code value="<?=$code?>">
			<input type=hidden name=searchtype value="<?=$searchtype?>">
			<input type=hidden name=keyword value="<?=$keyword?>">
			<input type=hidden name=sort value="<?=$sort?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=listnum value="<?=$listnum?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<tr>
				<td>
				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20">
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품코드 검색</span></th>
							<td>
								<select name="code_type" style="width:80px;height:32px;vertical-align:middle;">
								<?php 
									if($code_type=="name" || $code_type == ""){
										echo "<option value=\"name\" selected>상품명</option>\n";
										echo "<option value=\"code\" >상품코드</option>\n";
										echo "<option value=\"erpcode\" >ERP코드</option>\n";
									} else if ($code_type=="code"){
										echo "<option value=\"name\" >상품명</option>\n";
										echo "<option value=\"code\" selected>상품코드</option>\n";
										echo "<option value=\"erpcode\" >ERP코드</option>\n";
									} else if ($code_type=="erpcode"){
										echo "<option value=\"name\" >상품명</option>\n";
										echo "<option value=\"code\" >상품코드</option>\n";
										echo "<option value=\"erpcode\" selected>ERP코드</option>\n";
									}
								?>
								</select> 
								<textarea rows="2" cols="10" class="w200" name="keyword"   style="resize:none;vertical-align:middle;"><?=$keyword?></textarea><!-- onfocus="this.value=''" -->
							</td>
						</tr>
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
								$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
								$i=0;
								$ii=0;
								$iii=0;
								$iiii=0;
								$strcodelist = "";
								$strcodelist.= "<script>\n";
								$result = pmysql_query($sql,get_db_conn());
								$selcode_name="";

								while($row=pmysql_fetch_object($result)) {
									$strcodelist.= "var clist=new CodeList();\n";
									$strcodelist.= "clist.code_a='{$row->code_a}';\n";
									$strcodelist.= "clist.code_b='{$row->code_b}';\n";
									$strcodelist.= "clist.code_c='{$row->code_c}';\n";
									$strcodelist.= "clist.code_d='{$row->code_d}';\n";
									$strcodelist.= "clist.type='{$row->type}';\n";
									$strcodelist.= "clist.code_name='{$row->code_name}';\n";
									if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
										$strcodelist.= "lista[{$i}]=clist;\n";
										$i++;
									}
									if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
										if ($row->code_c=="000" && $row->code_d=="000") {
											$strcodelist.= "listb[{$ii}]=clist;\n";
											$ii++;
										} else if ($row->code_d=="000") {
											$strcodelist.= "listc[{$iii}]=clist;\n";
											$iii++;
										} else if ($row->code_d!="000") {
											$strcodelist.= "listd[{$iiii}]=clist;\n";
											$iiii++;
										}
									}
									$strcodelist.= "clist=null;\n\n";
								}
								pmysql_free_result($result);
								$strcodelist.= "CodeInit();\n";
								$strcodelist.= "</script>\n";

								echo $strcodelist;


								echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
								echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
								echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
								echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_d style=\"width:170px;\">\n";
								echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
				?>
							</td>
						</tr>
						<tr>
							<th><span>품절 유무</span></th>
							<td><input type="radio" name="s_check" value="" <?=$checked["s_check"]['']?>/>전체 <input type="radio" name="s_check" value="1" <?=$checked["s_check"]['1']?>/>판매중 <input type="radio" name="s_check" value="2" <?=$checked["s_check"]['2']?>/>품절</td>
						</tr>
						<tr>
							<th><span>진열 유무</span></th>
							<td><input type="radio" name="display" value="" <?=$checked["display"]['']?>/>전체 <input type="radio" name="display" value="1" <?=$checked["display"]['1']?>/> 진열&nbsp;&nbsp; <input type="radio" name="display" value="2" <?=$checked["display"]['2']?>/> 미진열&nbsp;&nbsp; <input type="radio" name="display" value="3" <?=$checked["display"]['3']?>/> 가등록</td>
						</tr>
						<tr>
							<th><span>아이콘 유무</span></th>
							<td>
                                <input type="checkbox" name="display_best" value="01" <?=$checked["display_best"]['01']?>/> BEST&nbsp;&nbsp;
								<input type="checkbox" name="display_new" value="02" <?=$checked["display_new"]['02']?>/> NEW&nbsp;&nbsp;
                                <input type="checkbox" name="display_only" value="03" <?=$checked["display_only"]['03']?>/> ONLY&nbsp;&nbsp;
                                <input type="checkbox" name="display_sale" value="04" <?=$checked["display_sale"]['04']?>/> SALE
                            </td>
						</tr>
                         <TR>
						<th><span>시즌 검색</span></th>
						<td><select name=sel_season class="select">
							<option value="">==== 전체 ====</option>
	<?php
							// 20170410 시즌검색 추가
							$sql = "SELECT SEASON_YEAR,SEASON,SEASON_KOR_NAME,SEASON_ENG_NAME FROM tblproductseason ORDER BY NO DESC";
							$result = pmysql_query($sql,get_db_conn());
							while($row = pmysql_fetch_object($result)){
								echo "<option value=\"{$row->season_year},{$row->season}\"";
								if($sel_season=="{$row->season_year},{$row->season}") echo " selected";
								echo ">{$row->season_eng_name}</option>\n";
							}
	?>
							</select>
						</td>
					</TR>
					</table>
					<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색" /></a></p>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
					<div class="table_style02">

                    <div class="btn_right">
                        <select name="listnum_select" onchange="javascript:listnumSet(this)">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
							<!-- <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
							<option value="1000" <?if($listnum==1000)echo "selected";?>>1000개씩 보기</option>  -->
                        </select>
                    </div>


					<table width=100% cellpadding=0 cellspacing=0>
						<colgroup>
							<?php
							$colspan=14;
							if($vendercnt>0) $colspan++;
							?>
							<col width=40></col>
							<col width=40></col>
							<?php if($vendercnt>0){?>
							<col width=60></col>
							<?php }?>
							<col width=330></col>
							<col width=50></col>
							<col width=></col>
							<col width=100></col>
							<col width=100></col>
							<col width=100></col>
							<col width=70></col>
							<col width=70></col>
							<col width=70></col>
							<col width=70></col>
							<!-- <col width=43></col> -->
						</colgroup>
						<tr>
							<th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
							<th>번호</th>
							<?php if($vendercnt>0){?>
							<th>브랜드</th>
							<?php }?>
							<th>상품코드</th>
							<th colspan=2>상품명</th>
							<th>제조사</th>
							<th>정가</th>
							<th>판매가</th>
							<th>BEST</th>
							<th>NEW</th>
							<th>ONLY</th>
							<th>SALE</th>
							<th>NEW 적용일</th>
						</tr>
		<?php
						$page_numberic_type=1;
						
						if ($likecode){
							$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
							$link_result=pmysql_query($link_qry);
							while($link_data=pmysql_fetch_object($link_result)){
								$linkcode[]=$link_data->c_productcode;
							}

							$qry.= "AND a.productcode in ('".implode("','",$linkcode)."') ";
							
							// $qry= "AND b.c_category LIKE '{$likecode}%' ";
						
						}
						// 20170510 복수검색 적용
						if ($keyword) {
							
							$keyword = trim($keyword);
							$temp_search = explode("\r\n", $keyword);
							$cnt = count($temp_search);
							
							$search_arr = array();
							for($i = 0 ; $i < $cnt ; $i++){
								array_push($search_arr, "'%".$temp_search[$i]."%'");
							}

							if($code_type=="name"){
								$qry.= "AND productname LIKE any ( array[".implode(",", $search_arr)."] ) ";
							} else if ($code_type=="code") {
								$qry.= "AND productcode LIKE any ( array[".implode(",", $search_arr)."] ) ";
							} else if($code_type=="erpcode") {
								$qry.= "AND prodcode || colorcode LIKE any ( array[".implode(",", $search_arr)."] ) ";
							}
							
						}

						if($s_check==1)	$qry.="AND (quantity is NULL OR quantity > 0) ";
						elseif($s_check==2)$qry.="AND quantity <= 0 ";
						if($display_best)$qry.="AND icon like '%".$display_best."%'";
						if($display_new)$qry.="AND icon like '%".$display_new."%'";
						if($display_only)$qry.="AND icon like '%".$display_only."%'";
						if($display_sale)$qry.="AND icon like '%".$display_sale."%'";

						/*if($display_best || $display_new || $display_only || $display_sale)	$qry.="AND (icon like '%".$display_best."%' or icon like '%".$display_new."%' or icon like '%".$display_only."%' or icon like '%".$display_sale."%') ";*/

						if($display==1)	$qry.="AND display='Y' ";
						elseif($display==2)	$qry.="AND display='N'";
						elseif($display==3)	$qry.="AND display='R'";
						//if($search_start && $search_end) $qry.="AND SUBSTRING(date from 1 for 8) between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if($search_start && $search_end) $qry.="AND to_char(modifydate,'YYYYMMDD') between replace('{$search_start}','-','') AND replace('{$search_end}','-','')";
						if(!isnull($sellprice_min) && !isnull($sellprice_max)) $qry.="AND sellprice between '{$sellprice_min}' and '{$sellprice_max}'";

                        // ============================================================================================
                        // 브랜드 지정 또는 검색
                        // ============================================================================================
                        if ( $sel_vender ) {
                            $qry.="AND brand = {$sel_vender} ";
                        } elseif ( $s_brand_keyword ) {
                            $arrBrandIdx = array();

                            $tmp_search_keyword = strtolower($s_brand_keyword);
                            $subsql  = "SELECT bridx FROM tblproductbrand WHERE lower(brandname) like '%{$tmp_search_keyword}%' OR lower(brandname2) like '%{$tmp_search_keyword}%' ";
                            $subresult = pmysql_query($subsql);
                            while ( $subrow = pmysql_fetch_object($subresult) ) {
                                if ( $subrow->bridx != "" ) {
                                    array_push($arrBrandIdx, $subrow->bridx);
                                }
                            }
                            pmysql_free_result($subresult);

                            if ( count($arrBrandIdx) > 0 ) { 
                                $qry.="AND brand in ( " . implode(",", $arrBrandIdx) . " ) ";
                            }
                        }
						
                        if ($sel_season){
                        	$temp = explode (",", $sel_season);
                        	$season_year = $temp[0];
                        	$season = $temp[1];
                        	$qry.="AND a.season_year = '{$season_year}' AND season = '{$season}'";
                        }
                        
						$sql0 = "SELECT COUNT(*) as t_count FROM tblproduct a  WHERE 1=1 ";
						$sql0.= $qry;
						$paging = new newPaging($sql0,10,$listnum);
						$t_count = $paging->t_count;
						$gotopage = $paging->gotopage;

						$sql = "SELECT icon,etctype,option_price,productcode,prodcode,colorcode,productname,production,sellprice,consumerprice, self_goods_code, ";
						$sql.= "buyprice,quantity,reserve,reservetype,addcode,display,vender,tinyimage,assembleuse,assembleproduct, date,newicon_date ";
						$sql.= "FROM tblproduct a  WHERE 1=1 ";
						$sql.= $qry." ";
						$sql.= "ORDER BY a.pridx desc ";
						$sql = $paging->getSql($sql);
						

						if($_SERVER[REMOTE_ADDR]=="218.234.32.103"){   
							//exdebug($sql);
						} 
		
						$result = pmysql_query($sql,get_db_conn());
						$cnt=0;

						while($row=pmysql_fetch_object($result)) {

							$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
		?>
						<tr>
							<input type="hidden" name="aproductcode[]" value="<?=$row->productcode?>">
							<input type="hidden" name="aassembleproduct[]" value="<?=$row->assembleproduct?>">
							<input type="hidden" name="aassembleuse[]" value="<?=$row->assembleuse?>">
							
							<td align="center" style="font-size:8pt;padding:2"><input type="checkbox" name="idx[]" value="<?=$row->productcode?>"></td>
							<td align="center" style="font-size:8pt;padding:2"><?=$number?></td>
		<?php
							if($vendercnt>0) {
								echo "	<td align=\"center\" style=\"font-size:8pt\"><B>".(ord($venderlist[$row->vender]->vender)?"<a href=\"javascript:viewVenderInfo({$row->vender})\">{$venderlist[$row->vender]->brandname}</a>":"-")."</B></td>\n";
							}
							?>
							<td align="center" style="font-size:8pt;padding:2">
							<p><a href="javascript:ProductInfo('<?=$row->productcode?>','YES', '<?=$likecodeExchange?>');"> <!--YES를 NO로 바꾸면 팝업안됨-->
                            <?=$row->productname.($row->selfcode?"-".$row->selfcode:"")?><br><?=$row->productcode?> / <?=$row->prodcode?>-<?=$row->colorcode?></a></p>
							</td>
							<?
							echo "	<TD style='position:relative'>";

							$file = getProductImage($Dir.DataDir.'shopimages/product/',$row->tinyimage);

							if (ord($row->tinyimage)){
								echo "<img src='".$file."' style=\"width:30px\" border=1 onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							} else {
								echo "$row->tinyimage<img src=images/space01.gif onMouseOver=\"ProductMouseOver('primage{$cnt}')\" onMouseOut=\"ProductMouseOut('primage{$cnt}');\">";
							}
							echo "<div id=\"primage{$cnt}\" style=\"position:absolute;left:40px; z-index:100; display:none;\"><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"170\">\n";
							echo "		<tr bgcolor=\"#FFFFFF\">\n";
							if (ord($row->tinyimage)){
								echo "		<td align=\"center\" width=\"100%\" height=\"100%\" style=\"border:#000000 solid 1px;padding:5px;\"><img src=\"".$file."\" border=\"0\" style='max-width:300px'></td>\n";
							}

							echo "		</tr>\n";
							echo "		</table>\n";
							echo "		</div>\n";
							echo "	</td>\n";

		?>							
							<td><?=str_replace("\"","&quot",$row->productname) ?></td>
							<td><?=str_replace("\"","&quot",$row->production) ?></td>
							<td><?=str_replace("\"","&quot",$row->consumerprice) ?></td>
							<td><?=$row->sellprice?></td>
							<td><?=strpos($row->icon,'01')!==false ? "Y"  :"N"?></td>
							<td><?=strpos($row->icon,'02')!==false ? "Y"  :"N"?></td>
							<td><?=strpos($row->icon,'03')!==false ? "Y"  :"N"?></td>
							<td><?=strpos($row->icon,'04')!==false ? "Y"  :"N"?></td>
							<td><?=substr($row->newicon_date,0,16) ?></td>
						</tr>
						
		<?
						$cnt++;	
						}
						if ($cnt==0) {
							$page_numberic_type="";
							echo "<tr><TD colspan=\"{$colspan}\" background=\"images/table_con_line.gif\"></TD></tr><tr><td class=\"td_con2\" colspan={$colspan} align=center>검색된 상품이 존재하지 않습니다.</td></tr>";
						}
		?>

						<tr>
                        <?if($page_numberic_type) {?>
							
                            <td style="text-align:left;" colspan=14>
							<div style="padding:4px;margin:0 10px 0 0;float:left;vertical-align: middle">
								<a href="javascript:CheckresetForm();"><img src="images/botteon_reset.gif" border="0" style="vertical-align: middle;"></a>&nbsp;&nbsp;
								<a href="javascript:ChecknewForm();"><img src="images/botteon_new.gif" border="0" style="vertical-align: middle;"></a>&nbsp;&nbsp;
							</div>
							<div style="border:1px solid #cbcbcb;padding:4px;margin:0;float:left;vertical-align: middle">
								<input type="checkbox" name="adisplay_best" value="01" style="text-align:right;vertical-align: middle;"><img src="../images/common/icon01.gif" border="0" style="vertical-align: middle;">&nbsp;&nbsp;
								<input type="checkbox" name="adisplay_only" value="03" style="text-align:right;vertical-align: middle;"><img src="../images/common/icon03.gif" border="0" style="vertical-align: middle;">&nbsp;&nbsp;
								<input type="checkbox" name="adisplay_sale" value="04" style="text-align:right;vertical-align: middle;"><img src="../images/common/icon04.gif" border="0" style="vertical-align: middle;">&nbsp;&nbsp;
								<input type="hidden" name="mode_i" value="">
								<input type="hidden" name="mode_i2" value="">
								<input type="hidden" name="mode_i3" value="">
                                <a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0" style="text-align:right;vertical-align: middle;"></a>
							</div>
							</td>
                        <?}?>
                        </tr>
					</table>
					</div>
					
		<?
					if($page_numberic_type) {
							echo "<div id=\"page_navi01\">";
							echo "<div class=\"page_navi\">";
							echo "<ul>";
							echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
							echo "</ul>";
							echo "</div>";
							echo "</div>";
					}
		?>		
					</td>
				</tr>
				<tr>
					<td height="20"></td>
				</tr>

			</form>

			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
			</form>
			
			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>
			
			<form name=form_register action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form>

            <!-- 일괄적용용 form -->
            <form name="form_batch_apply" id="form_batch_apply" method="post">
            </form>
			<form name="product_excel" id="product_excel" method="post">
				<input type="hidden" name=product_code_all>
			</form>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>일괄 수정시 주의사항</span></dt>
							<dd>
							- 아이콘은 마지막에 적용한 아이콘만 적용되도록 되어 있습니다.<br>
							- 아이콘을 여러 개 동시에 등록할 경우, 쇼핑몰에서는 BEST > NEW > ONLY > SALE 의 우선 순위로 하나씩만 노출됩니다.<br>
							&nbsp;&nbsp;예) BEST + 기타 아이콘 동시 등록 ==> BEST 만 노출 / NEW + ONLY , SALE 등록 ==> NEW 노출 <br>
							- 상품 관리 상세에서 아이콘 변경 적용시 동일 기준으로 적용됩니다.
							</dd>
								
						</dl>
						<dl>
							<dt><span>상품 일괄수정 방법</span></dt>
							<dd>
							① 상품보기 선택에 따라 카테고리 선택 또는 상품명으로 검색합니다.<br>
							② 출력된 상품들 중 수정을 원하는 상품만 입력내용을 수정합니다.<Br>
							③ 수정이 완료 됐으며 [적용하기] 버튼을 클릭합니다.
							</dd>

						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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

<?php
include("copyright.php");
?>
<?=$onload?>
