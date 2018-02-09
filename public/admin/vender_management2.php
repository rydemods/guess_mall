<?php
/********************************************************************* 
// 파 일 명		: vender_management2.php 
// 설     명		: 입점업체 정보관리
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 입점업체 리스트 관리
// 작 성 자		: 2015.11.16 - 김재수 (vender_management.php 복사)
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "br-1";
	$MenuCode = "brand";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################
//exdebug($_POST);
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST["mode"];
	$vender=$_POST["vender"];
	$sort_mode=$_POST["sort_mode"];
	$brand_sort=$_POST["brand_sort"];

	$disabled=$_POST["disabled"];
	$s_check=$_POST["s_check"];
	$search= strtolower(trim($_POST["search"]));
#---------------------------------------------------------------
# 노출순서를 변경한다.
#---------------------------------------------------------------

	if($sort_mode=="sort_change"){
		foreach($brand_sort as $bs=>$bsv){
			$sql="update tblproductbrand set brand_sort='".$bsv."' where bridx='".$bs."'";
			pmysql_query($sql);
		}
		echo "<html></head><body onload=\"alert('순서가 변경되었습니다.'); window.location.replace('vender_management2.php')\"></body></html>";exit;
	}
#---------------------------------------------------------------
# 벤더 승인 상태를 변경한다.
#---------------------------------------------------------------
	if($mode=="disabled" && ord($vender) && ($disabled=="0" || $disabled=="1")) {
		$sql = "UPDATE tblvenderinfo SET disabled='{$disabled}' ";
		$sql.= "WHERE vender='{$vender}' AND delflag='N' ";
		if(pmysql_query($sql,get_db_conn())) {
			$log_content = "## Vender 승인상태 변경 ## - 벤더 : {$vender} , 승인여부 : ".($disabled==0?"승인":"보류")."";
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.pageForm.submit();\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}
	}
#---------------------------------------------------------------
# 벤더정보를 삭제한다.
#---------------------------------------------------------------
	if($mode=="vender_del" && ($_POST["delete_gbn"]=="Y" || $_POST["delete_gbn"]=="N")) {				// DB를 삭제한다.
		$delete_gbn=$_POST["delete_gbn"];
		$venders	= explode(",", $vender);
		for($k=0;$k < count($venders);$k++) {
			$vender	= $venders[$k];
			$sql = "SELECT COUNT(*) as cnt FROM tblorderproduct WHERE vender='{$vender}' ";
			$result=pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			pmysql_free_result($result);
			$cnt=$row->cnt;

			$sql="UPDATE tblshopcount SET vendercnt=vendercnt-1 ";
			pmysql_query($sql,get_db_conn());

			if($cnt<=0) {
				pmysql_query("DELETE FROM tblvenderinfo WHERE vender='{$vender}'",get_db_conn());
			} else {
				$sql = "UPDATE tblvenderinfo SET delflag='Y' ";
				$sql.= "WHERE vender='{$vender}' ";
				pmysql_query($sql,get_db_conn());
			}
			pmysql_query("DELETE FROM  tblvenderinfo_add WHERE vender='{$vender}'",get_db_conn());
			pmysql_query("DELETE FROM  tblproductbrand WHERE vender='{$vender}'",get_db_conn());
			pmysql_query("DELETE FROM tblvenderstore WHERE vender='{$vender}'",get_db_conn());
			pmysql_query("DELETE FROM tblvenderstorecount WHERE vender='{$vender}'",get_db_conn());
			pmysql_query("DELETE FROM tblvenderlog WHERE vender='{$vender}'",get_db_conn());
			pmysql_query("optimize table tblvenderstorevisit");

			/**************************** 필요없는 쿼리 삭제 (김재수 - 2015.10.23) ****************************/
			//pmysql_query("optimize table tblvenderlog");
			//pmysql_query("optimize table tblregiststore");
			/***********************************************************************************************/

			//이미지 파일 삭제
			$storeimagepath=$Dir.DataDir."shopimages/vender/";
			proc_matchfiledel($storeimagepath."MAIN_{$vender}.*");
			proc_matchfiledel($storeimagepath."logo_{$vender}.*");
			proc_matchfiledel($storeimagepath.$vender."*");
			proc_matchfiledel($storeimagepath."aboutdeliinfo_{$vender}*");

			if($delete_gbn=="Y") {			//업체 상품 완전 삭제
				$sql = "SELECT productcode FROM tblproduct WHERE vender='{$vender}' ";
				$result=pmysql_query($sql,get_db_conn());
				while($row=pmysql_fetch_object($result)) {
					$prcode=$row->productcode;
					#태그관련 지우기
					$sql1 = "DELETE FROM tbltagproduct WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					#리뷰 지우기
					$sql1 = "DELETE FROM tblproductreview WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					#위시리스트 지우기
					$sql1 = "DELETE FROM tblwishlist WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					#관련상품 지우기
					$sql1 = "DELETE FROM tblcollection WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					$sql1 = "DELETE FROM tblproducttheme WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					$sql1 = "DELETE FROM tblproduct WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					$sql1 = "DELETE FROM tblproduct_option WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					$sql1 = "DELETE FROM tblproductgroupcode WHERE productcode = '{$prcode}'";
					pmysql_query($sql1,get_db_conn());

					$delshopimage = $Dir.DataDir."shopimages/product/{$prcode}*";
					proc_matchfiledel($delshopimage);

					delProductMultiImg("prdelete","",$prcode);
				}
				pmysql_free_result($result);

				$log_content = "## 입점업체 삭제 ## - 업체ID : {$_vdata->id} , [업체상품 삭제]";
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
			} else if($delete_gbn=="N") {	//업체 상품 쇼핑몰 본사 상품으로 변경
				$sql = "UPDATE tblproduct SET vender=0 ";
				$sql.= "WHERE vender='{$vender}' ";
				pmysql_query($sql,get_db_conn());

				$log_content = "## 입점업체 삭제 ## - 업체ID : {$_vdata->id} , [업체상품 본사상품으로 변경]";
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
			}
		}

		echo "<html></head><body onload=\"alert('선택하신 입점업체 정보가 완전히 삭제되었습니다.');parent.pageForm.submit();\"></body></html>";exit;
	}
#---------------------------------------------------------------
# 검색부분을 정리한다.
#---------------------------------------------------------------
	//var_dump($_POST);
	$listnum    = $_POST["listnum"] ?: "20";
	$sort= $_POST["sort"];
	if($sort =='') $sort= 8;
	//echo $sort;
	${"check_sort".$sort} = "selected";

	$qry = "WHERE a.delflag='N' ";
	if($disabled=="Y") $qry.= "AND a.disabled='0' ";
	else if($disabled=="N") $qry.= "AND a.disabled='1' ";
	if(ord($search)) {
		if($s_check=="id") $qry.= "AND lower(a.id) LIKE '%{$search}%' ";
		else if($s_check=="com_name") $qry.= "AND lower(a.com_name) LIKE '%{$search}%' ";
		else if($s_check=="brand_name") $qry.= "AND ( lower(b.brandname) LIKE '%{$search}%' OR lower(b.brandname2) LIKE '%{$search}%' ) ";
	}

	include("header.php");  // 상단부분을 불러온다.

#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$sql = "SELECT COUNT(a.*) as t_count FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender {$qry} ";

//echo $sql . "<br/>";
	$paging = new Paging($sql,10,$listnum);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;			
    //echo $sql."<br>";
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SearchVender() {
	//document.sForm.submit();
    document.form1.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function VenderModify(vender) {
	document.form3.vender.value=vender;
	document.form3.action="vender_infomodify2.php";
	document.form3.submit();
}

function VenderAddModify(vender) {
	document.form3.vender.value=vender;
	document.form3.action="vender_info_add.php";
	document.form3.submit();
}

function VenderDeliveryModify(vender) {
	document.form3.vender.value=vender;
	document.form3.action="vender_delivery.php";
	document.form3.submit();
}

function VenderDeliinfoModify(vender) {
	document.form3.vender.value=vender;
	document.form3.action="vender_product_deliinfo.php";
	document.form3.submit();
}

function VenderDetail(vender) {
	window.open("about:blank","venderdetail_pop","height=100,width=100,toolbar=no,menubar=no,scrollbars=yes,status=no");

	document.form2.vender.value=vender;
	document.form2.action="vender_detailpop.php";
	document.form2.target="venderdetail_pop";
	document.form2.submit();
}

function setVenderDisabled(vender,disabled) {
	if(disabled!="0" && disabled!="1") {
		alert("승인상태 설정이 잘못되었습니다.");
		return;
	}
	document.etcform.vender.value=vender;
	if(confirm("해당 Vender의 승인상태를 ["+(disabled=="0"?"ON":"OFF")+"] 하시겠습니까?")) {
		document.etcform.mode.value="disabled";
		document.etcform.disabled.value=disabled;
		document.etcform.action="<?=$_SERVER['PHP_SELF']?>";
		document.etcform.target="processFrame";
		document.etcform.submit();
	}
}

function excel_download() {
	if(confirm("검색된 모든 정보를 다운로드 하시겠습니까?")) {
		document.excelform.submit();
	}
}

function CheckAll() {
	if($("#allCheck").prop("checked")) {
		$("input[name='vender_chk']").prop("checked",true);
	} else {
		$("input[name='vender_chk']").prop("checked",false);
	}
}

function CheckDelete() {
	document.exeform.vender.value="";
	var k = 0;
	for(i=0;i<document.form1.vender_chk.length;i++) {
		if(document.form1.vender_chk[i].checked) {
			if (k == 0)
			{
				document.exeform.vender.value+=document.form1.vender_chk[i].value;
			} else {
				document.exeform.vender.value+=","+document.form1.vender_chk[i].value;
			}
			k++;
		}
	}
	if(document.exeform.vender.value.length==0) {
		alert("선택하신 Vender가 없습니다.");
		return;
	}

	if(confirm("선택하신 Vender를 정말 삭제하시겠습니까?")) {
		if(confirm("해당 Vender의 상품도 같이 삭제하시겠습니까?\n\nVender 상품을 같이 삭제할 경우 [확인]\n\nVender 상품을 쇼핑몰 본사 상품으로 변경하시려면 [취소] 버튼을 누르세요.")) {
			if(confirm("정말 해당 업체와 상품을 모두 삭제하시겠습니까?")) {
				document.exeform.delete_gbn.value="Y";
				document.exeform.mode.value="vender_del";
				document.exeform.target="processFrame";
				document.exeform.action="<?=$_SERVER['PHP_SELF']?>";
				document.exeform.submit();
			}
		} else {
			if(confirm("정말 해당 업체 삭제 후 업체 상품을 쇼핑몰 본사 상품으로 변경하시겠습니까?")) {
				document.exeform.delete_gbn.value="N";
				document.exeform.mode.value="vender_del";
				document.exeform.target="processFrame";
				document.exeform.action="<?=$_SERVER['PHP_SELF']?>";
				document.exeform.submit();
			}
		}
	}
}

function CheckAllUdate() {
	document.form2.vender.value="";
	var k = 0;
	for(i=0;i<document.form1.vender_chk.length;i++) {
		if(document.form1.vender_chk[i].checked) {
			if (k == 0)
			{
				document.form2.vender.value+=document.form1.vender_chk[i].value;
			} else {
				document.form2.vender.value+=","+document.form1.vender_chk[i].value;
			}
			k++;
		}
	}
	if(document.form2.vender.value.length==0) {
		alert("선택하신 Vender가 없습니다.");
		return;
	}

	window.open("about:blank","allupdate_pop","height=100,width=100,toolbar=no,menubar=no,scrollbars=yes,status=no");

	document.form2.action="vender_allupdate_popup.php";
	document.form2.target="allupdate_pop";
	document.form2.submit();
}
function sort_change(){
	if(confirm("노출순서를 변경하시겠습니까?")) {
		document.form1.sort_mode.value="sort_change";
		document.form1.submit();
	}
}
</script>
<style>
a.btn_blue {display:block;color:#507291;background-color:#FFFFFF;font-size:8pt;border:1px solid #507291;padding:0px 0px;text-align:center;text-decoration:none;}
a.btn_blue:hover {color:#FFFFFF;background-color:#507291;text-decoration:none;}
</style>
<div class="admin_linemap"><div class="line"><p>현재위치 : 브랜드 관리 &gt; 브랜드 관리 &gt;<span>브랜드 정보관리</span></p></div></div>
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
			<?php include("menu_brand.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">브랜드 정보관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>브랜드의 정보를 수정/삭제 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<!-- <form name="sForm" method="post">	 -->
            <form name="form1" method="post">
			<input type="hidden" name="sort_mode" id="sort_mode">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">브랜드 검색 선택</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>업체 검색</span></th>
					<td>
					<select name=disabled class="select">
					<option value="">===== 전체 =====</option>
					<option value="Y" <?php if($disabled=="Y")echo"selected";?>>승인 업체만 검색</option>
					<option value="N" <?php if($disabled=="N")echo"selected";?>>대기 업체만 검색</option>
					</select>
					<select name="s_check" class="select">
					<option value="id" <?php if($s_check=="id")echo"selected";?>>업체 아이디로 검색</option>
					<option value="com_name" <?php if($s_check=="com_name")echo"selected";?>>업체명으로 검색</option>
					<option value="brand_name" <?php if($s_check=="brand_name")echo"selected";?>>브랜드명으로 검색</option>
					</select>
					<input type=text name=search value="<?=$search?>" class="input">
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center><a href="javascript:SearchVender();"><img src="images/btn_search01.gif"></a></td>
			</tr>
			<!-- </form>
			<form name="form1" method="post"> -->
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 소제목 -->
                    <div class="btn_right" style="margin-bottom:5px">			
						<select name=sort onchange="javascript:document.form1.submit();">
						<option value="0" <?=$check_sort0?>>아이디 내림차순</option>
						<option value="1" <?=$check_sort1?>>아이디 오름차순</option>
						<option value="2" <?=$check_sort2?>>브랜드명 내림차순</option>
						<option value="3" <?=$check_sort3?>>브랜드명 오름차순</option>
						<option value="4" <?=$check_sort4?>>대카테고리명 내림차순</option>
						<option value="5" <?=$check_sort5?>>대카테고리명 오름차순</option>
						<option value="6" <?=$check_sort6?>>회사명 내림차순</option>
						<option value="7" <?=$check_sort7?>>회사명 오름차순</option>
						<option value="8" <?=$check_sort8?>>등록일 내림차순</option>
						<option value="9" <?=$check_sort9?>>등록일 오름차순</option>
						</select>
                        <select name="listnum" onchange="javascript:document.form1.submit();">
                            <option value="20" <?if($listnum==20)echo "selected";?>>20개씩 보기</option>
                            <option value="40" <?if($listnum==40)echo "selected";?>>40개씩 보기</option>
                            <option value="60" <?if($listnum==60)echo "selected";?>>60개씩 보기</option>
                            <option value="80" <?if($listnum==80)echo "selected";?>>80개씩 보기</option>
                            <option value="100" <?if($listnum==100)echo "selected";?>>100개씩 보기</option>
                            <option value="200" <?if($listnum==200)echo "selected";?>>200개씩 보기</option>
                            <option value="300" <?if($listnum==300)echo "selected";?>>300개씩 보기</option>
                            <option value="400" <?if($listnum==400)echo "selected";?>>400개씩 보기</option>
                            <option value="500" <?if($listnum==500)echo "selected";?>>500개씩 보기</option>
                            <option value="100000" <?if($listnum==100000)echo "selected";?>>전체</option>
                        </select>
                    </div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="40"></col>
				<col width="45"></col>
				<col width="180"></col>
				<col width="150"></col>
				<col width="120"></col>
                <col width=""></col>
				<col width="120"></col>
				<col width="120"></col>
				<col width="120"></col>
				<col width="60"></col>
				<col width="45"></col>
				<col width="45"></col>
				<col width="45"></col>
				<col width="45"></col>
				<col width="60"></col>
				<col width="60"></col>
				<col width="60"></col>
				<col width="45"></col>
				<TR align=center>
					<th rowspan=2><input type=checkbox name=allCheck id=allCheck onclick="CheckAll()" style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"></th>
					<th rowspan=2>번호</th>
					<th rowspan=2>업체ID</th>
					<th rowspan=2>브랜드명</th>
                    <th rowspan=2>대카테고리명</th>
                    <th rowspan=2>회사명</th>
					<th rowspan=2>회사전화</th>
					<th rowspan=2>담당자명</th>
					<th rowspan=2>휴대전화</th>
					<th rowspan=2>임직원<br>할인율</th>
					<th colspan=4>상품권한</th>
					<th rowspan=2>노출순서</th>
					<th rowspan=2>정보</th>
					
					<th rowspan=2>상세</th>
					<th rowspan=2>승인</th>					
				</TR>
				<TR align=center>
					<td>등록</td>
					<td>수정</td>
					<td>삭제</td>
					<td>인증</td>
				</tr>

<?php
#---------------------------------------------------------------
# 벤더 정보 리스트를 불러온다.
#---------------------------------------------------------------

		if($t_count>0) {
            // 대카테고리 정보
            $sql = "SELECT code_a, code_name FROM tblproductcode WHERE code_b = '000' AND group_code!='NO' AND (type LIKE 'L%')";
            $result = pmysql_query($sql);
            $arrCategory = array();
            while ( $row = pmysql_fetch_object($result) ) {
                $arrCategory[$row->code_a] = $row->code_name;
            }
            pmysql_free_result($result);

			$sql = "SELECT a.*, b.brandname, b.productcode_a, b.bridx, b.staff_rate, b.brand_sort FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender {$qry} ";

			switch ($sort) {
				case "0":	//아이디 내림차순
					$sql.= "ORDER BY a.disabled ASC, a.id DESC ";
					break;
				case "1":	//아이디 오름차순
					$sql.= "ORDER BY a.disabled ASC, a.id ASC ";
					break;
				case "2":	//브랜드명 내림차순
                    $sql.= "ORDER BY a.disabled ASC, lower(b.brandname) DESC, a.vender DESC ";
					break;
				case "3":	//브랜드명 오름차순
                    $sql.= "ORDER BY a.disabled ASC, lower(b.brandname) ASC, a.vender ASC ";
					break;
				case "4":	//대카테고리 내림차순
					$sql.= "ORDER BY a.disabled ASC, b.productcode_a DESC, lower(b.brandname) DESC, a.vender DESC ";
					break;
				case "5":	//대카테고리 오름차순
					$sql.= "ORDER BY a.disabled ASC, b.productcode_a ASC, lower(b.brandname) ASC, a.vender ASC ";
					break;
				case "6":	//회사명 내림차순
					$sql.= "ORDER BY a.disabled ASC, a.com_name DESC, lower(b.brandname) DESC, a.vender DESC ";
					break;
				case "7":	//회사명 오름차순
					$sql.= "ORDER BY a.disabled ASC, a.com_name ASC, lower(b.brandname) ASC, a.vender ASC ";
					break;
				case "8":	//등록일 내림차순
					$sql.= "ORDER BY a.disabled ASC, a.vender DESC, lower(b.brandname) DESC ";
					break;
				case "9":	//등록일 오름차순
					$sql.= "ORDER BY a.disabled ASC, a.vender ASC, lower(b.brandname) ASC ";
					break;
				default :	//등록일 내림차순
					$sql.= "ORDER BY a.disabled ASC, a.vender DESC, lower(b.brandname) DESC ";
					break;
			}

			$sql = $paging->getSql($sql);

			$result=pmysql_query($sql,get_db_conn());
            //echo $sql;
			$i=0;
			while($row=pmysql_fetch_object($result)) {
                // 대카테고리명 
                $cate_code = trim($row->productcode_a);
                $categoryName = "-";
                if ( isset($arrCategory[$cate_code]) && !empty($arrCategory[$cate_code]) ) {
                    $categoryName = $arrCategory[$cate_code];
                }

				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<TD><p align=\"center\"><input type=checkbox name=vender_chk value=\"{$row->vender}\" style=\"BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;\"></td>\n";
				echo "	<td align=center>{$number}</td>\n";
				echo "	<td align=center>{$row->id}</td>\n";
				echo "	<td style='text-align:left'>&nbsp;{$row->brandname}</td>\n";
				echo "	<td style='text-align:center'>&nbsp;{$categoryName}</td>\n";
				echo "	<td style='text-align:left'>&nbsp;{$row->com_name}&nbsp;</td>\n";
				echo "	<td align=center>&nbsp;{$row->com_tel}&nbsp;</td>\n";
				echo "	<td align=center>&nbsp;{$row->p_name}&nbsp;</td>\n";
				echo "	<td align=center>&nbsp;{$row->p_mobile}&nbsp;</td>\n";
				echo "	<td align=center>&nbsp;{$row->staff_rate} %&nbsp;</td>\n";
				echo "	<td align=center><B>".($row->grant_product[0]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "	<td align=center><B>".($row->grant_product[1]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "	<td align=center><B>".($row->grant_product[2]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";
				echo "	<td align=center><B>".($row->grant_product[3]=="Y"?"<span class=font_blue>Y</span>":"<span class=font_orange>N</span>")."</B></td>\n";				
				echo "	<td align=center><input type='text' value='{$row->brand_sort}' style='width:50px;text-align:center' name='brand_sort[{$row->bridx}]'></td>\n";
				echo "	<td align=center>\n";				
				echo "	<A HREF=\"javascript:VenderModify({$row->vender})\" class='btn_blue' style='margin:0px 2px'>수정</A>\n";				
				//echo "	<A HREF=\"javascript:VenderAddModify({$row->vender})\" class='btn_blue' style='margin:5px 10px 0px;'>추가정보관리</A>\n";				
				//echo "	<A HREF=\"javascript:VenderDeliveryModify({$row->vender})\" class='btn_blue'  style='margin:5px 10px 0px;'>배송관련 기능설정</A>\n";				
				//echo "	<A HREF=\"javascript:VenderDeliinfoModify({$row->vender})\" class='btn_blue'  style='margin:5px 10px 0px;'>배송/교환/환불정보 노출</A>\n";				
				//echo "	<A HREF=\"../front/brand_detail.php?bridx={$row->bridx}\" class='btn_blue'  style='margin:5px 10px 0px;' target='_blink'>브랜드 상세가기</A>\n";				
				echo "	</td>\n";

				echo "	<td align=center><A HREF=\"javascript:VenderDetail({$row->vender})\" class='btn_blue' style='margin:0px 2px'>보기</A></td>\n";
				echo "	<td align=center>";
				if($row->disabled=="0") {
					echo "<img src=images/icon_on.gif border=0 align=absmiddle style=\"cursor:hand\" onclick=\"setVenderDisabled('{$row->vender}','1')\">";
				} else {
					echo "<img src=images/icon_off.gif border=0 align=absmiddle style=\"cursor:hand\" onclick=\"setVenderDisabled('{$row->vender}','0')\">";
				}
				echo "	</td>\n";
				echo "</tr>\n";
				$i++;
			}
			pmysql_free_result($result);
		} else {
			echo "<tr><td colspan=14 align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
		}
?>
				</TABLE>
				</form>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=250></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td align='left'>
					<a href="javascript:CheckDelete();"><img src="images/btn_infodelete.gif" width="113" height="38" border="0"></a>
					<!-- <a href="javascript:CheckAllUdate();"><img src="images/btn_product_reg_all.gif" border="0" alt="일괄적용"></a> -->
					<a class="btn-point" href="javascript:sort_change();">순서변경하기</a>
						
					
					</td>

					<td align='center'>
					<table cellpadding="0" cellspacing="0" width="100%">
<?php				
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
					</table></td>
					<td align='right'><a href="javascript:excel_download()"><img src="images/btn_excel1.gif" border="0"></a></td>
				<tr>
				</table>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>브랜드 정보관리</span></dt>
							<dd>- 등록된 브랜드 리스트와 기본적인 정보사항을 확인할 수 있습니다.<br>
							- 입점사 정보변경은 [관리]를 이용하여 변경할 수 있습니다.<br>
							- 입점사 관리자 URL은 <B><font class=font_orange><A HREF="http://<?=$_ShopInfo->getShopurl()?>vender/" target="_blank">http://<?=$_ShopInfo->getShopurl()?>vender/</A></font></B> 입니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			<form name=form2 method=post>
			<input type=hidden name=vender>
			</form>

			<form name="form3" method="post">
			<input type=hidden name='vender'>
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			</form>

			<form name="pageForm" method="post">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			<input type=hidden name='listnum' value='<?=$listnum?>'>
			<input type=hidden name='sort' value='<?=$sort?>'>
			</form>

			<form name=etcform method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=mode>
			<input type=hidden name=vender>
			<input type=hidden name=disabled>
			</form>

            <form name=excelform action="vender_excel.php" method=post>
			<input type=hidden name=disabled value="<?=$disabled?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			</form>

			<form name=exeform method=post action="<?=$_SERVER['PHP_SELF']?>">
			<input type=hidden name=mode>
			<input type=hidden name=vender>
			<input type=hidden name=delete_gbn>
			</form>
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
include("copyright.php"); // 하단부분을 불러온다. 
