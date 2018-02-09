<?php
/********************************************************************* 
// 파 일 명		: design_depth1_tw.php
// 설     명		: 데코앤이 대카테고리 페이지 TODAY/WEEKLY 디자인
// 상세설명	: 데코앤이 대카테고리 페이지의 TODAY/WEEKLY 상품 리스트 관리
// 작 성 자		: 2016.01.18 - 김재수
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
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

    // 총 아이콘 갯수
    $icon_count     = 7;

    // 기존에 업로드한 내용
    $v_up_img_icon  = $_POST["v_up_img_icon"];

    // 이미지 경로
    $imagepath = $Dir.DataDir."shopimages/best_weekly/";

    // 이미지 파일              
    $imagefile = new FILE($imagepath);

##################### 페이지 접근권한 check #####################
	$PageCode = "de-2";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

include("header.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
	$menu_title	= "WEEKLY 숫자 아이콘 설정";

	$mode   = $_POST["mode"];
	$twno	= $_POST["twno"];

	# 카테고리 추가
	$code_a = $_POST['code_a'];
	$code_b = $_POST['code_b'];
	$code_c = $_POST['code_c'];
	$code_d = $_POST['code_d'];
	if( $code_a ){
		if( $code_b=="" || is_null( $code_b ) ) $code_b = '000';
		if( $code_c=="" || is_null( $code_c ) ) $code_c = '000';
		if( $code_d=="" || is_null( $code_d ) ) $code_d = '000';
	}
	$cate_number = $code_a.$code_b.$code_c.$code_d;

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

    if ( $mode == "insert" || $mode == "modify" ) {
		$up_imagefile=$imagefile->upFiles();
    }
		
	if($mode=="delete") {
		
		$qry = "DELETE FROM tblproduct_weekly_icon WHERE no = '".$twno."' ";
		pmysql_query($qry,get_db_conn());

		if( !pmysql_error() ){
			alert_go('삭제가 완료되었습니다.', $_SERVER['REQUEST_URI']);
		}
		$qry = '';

	} else if($mode=="insert") {

		$qry = "INSERT INTO tblproduct_weekly_icon(
		cate		,";

        for ( $i = 1; $i <= $icon_count; $i++ ) {
            if ( $i == $icon_count ) {
                $qry .= "icon{$i} ";
            } else {
                $qry .= "icon{$i}, ";
            }
        }
        
        $qry .= ") VALUES ( 
		'{$cate_number}', ";

        for ( $i = 1; $i <= $icon_count; $i++ ) {
            if ( $i == $icon_count ) {
                $qry .= "'" . $up_imagefile["up_img_icon"][$i - 1]["v_file"] . "' ";
            } else {
                $qry .= "'" . $up_imagefile["up_img_icon"][$i - 1]["v_file"] . "',";
            }
        }

        $qry .= ")";		

		pmysql_query($qry,get_db_conn());
		if(!pmysql_error()){
			pmysql_query($titleUp_sql,get_db_conn());	
			alert_go('등록이 완료되었습니다.', $_SERVER['REQUEST_URI']);
		}else{	
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		} 
		$qry = '';
		
	}else if($mode=="modify") {

		$qry = "UPDATE tblproduct_weekly_icon SET ";

        $arrUpdateQuery = array();
        array_push($arrUpdateQuery, "cate = '{$cate_number}'");

        for ( $i = 1; $i <= $icon_count; $i++ ) {

            // 파일 업로드가 된경우
            if( strlen( $up_imagefile["up_img_icon"][$i - 1]["v_file"] ) > 0 ){
                if( is_file( $imagepath.$v_up_img_icon[$i - 1] ) > 0 ) { // 기존 파일이 있는 경우
                    $imagefile->removeFile( $v_up_img_icon[$i - 1] );  // 파일 삭제
                }

                // 새로 업로드한 파일로 업데이트
                array_push($arrUpdateQuery, "icon{$i} = '" . $up_imagefile["up_img_icon"][$i - 1]["v_file"] . "'");
            }
        }

        $qry .= implode(",", $arrUpdateQuery);
		$qry.= " WHERE no='{$twno}' ";

		pmysql_query($qry,get_db_conn());
		if(!pmysql_error()){
			alert_go('수정이 완료되었습니다.', $_SERVER['REQUEST_URI']);
		}else{	
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		} 
		$qry = '';
	}


#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
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
$display_type = "display:none;";
//$display_type = '';
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다 display:none;
$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px; ".$display_type."\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다display:none;
$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px; ".$display_type."\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다display:none;
$codeD_list = "<select name=code_d id=code_d style=\"width:150px; height:150px; display:none;\" {$disabled} Multiple>\n";
$codeD_list.= "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
$codeD_list.= "</select>\n";

$codeSelect = "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" style=\"height : 20px;\" onclick=\"javascript:exec_add()\"></span>";


if ($mode=='modify_select') {	
	//수정
	$qType = '1';
	$sql = "SELECT * FROM tblproduct_weekly_icon where no='{$twno}' LIMIT 1";
	$result=pmysql_query($sql,get_db_conn());
	$_cdata=pmysql_fetch_object($result);
	if( $_cdata->cate != '' || is_null( $_cdata->cate ) ){
		list($code_a,$code_b,$code_c,$code_d) = sscanf($_cdata->cate,'%3s%3s%3s%3s');
		if(strlen($code_a)!=3) $code_a="000";
		if(strlen($code_b)!=3) $code_b="000";
		if(strlen($code_c)!=3) $code_c="000";
		if(strlen($code_d)!=3) $code_d="000";
	}
	pmysql_free_result($result);
}
# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$bSelectSql = "SELECT * FROM tblproduct_weekly_icon limit 1";
	$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
	$bSelectRow = pmysql_fetch_object( $bSelectRes );
	$_cdata->title	= $bSelectRow->title;
	pmysql_free_result( $bSelectRes );
}

if ($qType == '0') $mode_Text	= "등록";
if ($qType == '1') $mode_Text	= "수정";

$prCateSql = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d AS cate_code, code_name FROM tblproductcode ORDER BY cate_code ASC ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );

$prCate = array();
$prFirstCate = array();
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prCate[ $prRow['cate_code'] ] = $prRow;

    if ( $prRow['code_b'] == "000" ) {
        $prFirstCate[$prRow['cate_code']] = $prRow['code_name'];
    }
}
pmysql_free_result( $prCateRes );

# 페이징
$page_sql = "SELECT COUNT(*) FROM tblproduct_weekly_icon ";
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

# 리스트 불러오기

$twSql = "SELECT * FROM tblproduct_weekly_icon ";
$twSql.= "ORDER BY no ";
//echo $twSql;
$sql = $paging->getSql( $twSql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
    if ( isset($prFirstCate[$row['cate']]) ) {
        unset($prFirstCate[$row['cate']]);
    }

	$twList[] = $row;
}
pmysql_free_result($result);
?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
// 수정 / 삭제 
function changeAction( mode , num ){
	//mode 0 -> insert, 1 -> modify, 2 -> modfiy_select, 3 -> delete

    var cateFreeCount = <?=count($prFirstCate)?>;

	if( mode == '0' ){
        if ( cateFreeCount == 0 ) {
            alert('모든 카테고리가 등록되었습니다. 등록이 불가합니다.');
            return;
        }
	
		if( confirm('등록하시겠습니까?') ){
            // 카테고리 선택이 되었는지 체크
            if ( $("#code_a option:selected").text() === "" ) {
                alert("카테고리를 선택해 주세요.");
                $("#code_a").focus();
                return false;
            }

			$("#mode").val( 'insert' );
		} else {
			return;
		}
	} else if ( mode == '1' ) {

		if( confirm('수정하시겠습니까?') ){
			$("#mode").val( 'modify' );
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		$('#twno').val( num );
		$("#mode").val( 'modify_select' );
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			$('#twno').val( num );
			$("#mode").val( 'delete' );
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}

	if( checkForm() ){
		document.form1.submit();
	}
	
}

// submit 하기전 값을 체크한다
function checkForm( mode ){
	var returnVal = true;
	return returnVal;
}

//배너 카테고리
function exec_add()
{

	var ret;
	var str = new Array();
	var code_a=document.form1.code_a.value;
	var code_b=document.form1.code_b.value;
	var code_c=document.form1.code_c.value;
	var code_d=document.form1.code_d.value;

	if(!code_a) code_a="000";
	if(!code_b) code_b="000";
	if(!code_c) code_c="000";
	if(!code_d) code_d="000";
	sumcode=code_a+code_b+code_c+code_d;
	$.ajax({
		type: "POST",
		url: "product_register.ajax.php",
		data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
	}).done(function(msg) {
		if(msg=='nocate'){
			alert("상품카테고리 선택이 잘못되었습니다.");

		}else if(msg=='nolowcate'){
			alert("하위카테고리가 존재합니다.");
		}else{
			document.form1.code.value=sumcode;
			var code_a=document.getElementById("code_a");
			var code_b=document.getElementById("code_b");
			var code_c=document.getElementById("code_c");
			var code_d=document.getElementById("code_d");
		}

	});
}

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function goList() {
	document.form1.twno.value = "";
	document.form1.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; 메인 배너관리 &gt;<span><?=$menu_title?>관리</span></p></div></div>
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
			<?php include("menu_design.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<form name=form1 id=form1 method=post enctype="multipart/form-data">
			<input type=hidden name=mode id=mode value='' >
			<input type=hidden name=twno id=twno value="<?=$_cdata->no?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">		
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=$menu_title?>관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=$menu_title?> 정보를 변경 할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색된 목록</div>
				</td>
				
			</tr>
			
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width='50'>
					<col width='*'>
					<col width='60'>
					<col width='60'>
				</colgroup>
				<TR>
					<th>번호</th>
					<th>카테고리</th>				
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $twList ) > 0 ) {
		$cnt=0;
		foreach( $twList as $bCnt=>$bVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
					<!-- 카테고리 -->
					<td>
<?php
			if( $bVal['cate'] ) echo $prCate[$bVal['cate']]['code_name'];
			else echo '-';
?>
					</td>
					<!-- 수정 -->
					<td>
						<a href="javascript:changeAction( '2' ,'<?=$bVal["no"]?>' );"><img src="images/btn_edit.gif"></a>
					</td>
					<!-- 삭제 -->
					<td>
						<a href="javascript:changeAction('3', '<?=$bVal["no"]?>' );"><img src="images/btn_del.gif"></a>
					</td>
				</TR>
<?php
			$cnt++;
		}
	} else {
?>
				<TR>
					<td colspan='5' > 목록이 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $twList ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><?=$menu_title?>정보 <?=$mode_Text?></div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<?include("layer_prlistPop.php");?>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>카테고리</span></th>
					<td>
<?php
	//카테고리 SELECT BOX를 불러온다
	echo $codeA_list;
	echo $codeB_list;
	echo $codeC_list;
	echo $codeD_list;
	//카테고리 SELECT 버튼을 불러온다
	//echo $codeSelect;
	//카테고리 스크립트 실행
	echo $strcodelist;
	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
?>
					</td>
				</tr>

                <? 
                    for ( $i = 1; $i <= 7; $i++ ) { 

                        $varName = "icon{$i}";
                        $imgFileName = $_cdata->$varName;
                ?>
				<tr>
					<th><span>BEST<?=$i?> 숫자 아이콘</span></th>
					<td align="left">
                        <input type=file name="up_img_icon[]" style="WIDTH: 400px" ><br>
                        <input type=hidden name="v_up_img_icon[]" value="<?=$imgFileName?>" >
<?	if( is_file($imagepath.$imgFileName) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$imgFileName?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>		
                <? } ?>

				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center>
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:changeAction('<?=$qType?>', '<?=$mSelect['banner_no']?>' );">
						<img src="images/btn_confirm_com.gif">	
					</a>
<?php
	} else {
?>
					<a href="javascript:javascript:changeAction('<?=$qType?>', '<?=$mSelect['banner_no']?>' );">
						<img src="images/btn_edit2.gif">
					</a>
					<a href="javascript:javascript:goList();">
						<img src="img/btn/btn_list.gif" >
					</a>
<?php
	}
?>
				</td>
			</tr>
			
			<tr><td height=30></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span><?=$menu_title?>관리</span></dt>
							<dd>- <?=$menu_title?> 정보를 변경 할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php 
include("copyright.php");
