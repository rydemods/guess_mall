<?php
/********************************************************************* 
// 파 일 명		: community_magazine_write.php
// 설     명		: 매거진 관리 생성, 수정
// 상세설명	    : 매거진 관리 생성, 수정
// 작 성 자		: 2016.09.20 - 김대엽
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

##################### 페이지 접근권한 check #####################
$PageCode = "co-5";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#################################################################

include("header.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
//exdebug($_POST);
//exdebug($_GET);
//exdebug($_FILES);
//exit;

$mode = $_POST["mode"];
if(!$mode) $mode = $_GET["mode"];

// 이미지 경로
$imagepath = $Dir.DataDir."shopimages/magazine/";
// 이미지 파일
$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$no = $_POST["no"];
 
if($mode=="delete") {

    $qry = "DELETE FROM tblmagazine WHERE no ='".$no."'";
    pmysql_query( $qry, get_db_conn() );
    for($u=0;$u<1;$u++) {
    	if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
    		$imagefile->removeFile( $v_up_imagefile[$u] );
    	}
    }
    callNaver('magazine', $no, 'del');
    echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.
    
	$category_nm = $_POST["magazine_category"] ? $_POST["magazine_category"] : $_POST["magazine_category2"] ;
    $type = $_POST["magazine_type"];
	$title	            = pg_escape_string($_POST["title"]);
    //$content            = trim($_POST['content']);
    //$content            = str_replace("'", "''", $content);
    $content	        = pg_escape_string($_POST["content"]);
    $link_url	        = $_POST["link_url"];
    $link_m_url         = $_POST["link_m_url"];
    $display            = $_POST["display"];
    $writer             =$_POST['writer'];
    $tag          = pg_escape_string($_POST["hash_tags"]);
    if (!$display) $display = "N";
    else $display = "Y";

    $v_up_imagefile	    = $_POST["v_up_imagefile"];

    $up_imagefile = $imagefile->upFiles();
//     exdebug($up_imagefile);
//     exit;

    $regdt = date("YmdHis");

    if($mode=="insert") {
        $sql = "INSERT INTO tblmagazine (
        category_nm,
        title,
        content,
        type,
        link_url,
        link_m_url,
        img_file,
        img_rfile, 
        img_m_file, 
        img_m_rfile, 
        access, 
        regdt,
        display,
        writer,
        tag
        ) VALUES (
        '{$category_nm}',
        '{$title}', 
        '{$content}', 
         '{$type}',
        '{$link_url}', 
        '{$link_m_url}', 
        '".$up_imagefile["up_imagefile"][0]["v_file"]."', 
        '".$up_imagefile["up_imagefile"][0]["r_file"]."', 
        '".$up_imagefile["up_imagefile"][1]["v_file"]."', 
        '".$up_imagefile["up_imagefile"][1]["r_file"]."', 
        0, 
        '{$regdt}', 
       '{$display}',
       '{$writer}',
       '{$tag}'
        ) ";
        pmysql_query($sql,get_db_conn());
//         exdebug($sql);
//         exit;

    }else if($mode=="modify") {

        $img_where="";
        $img_where[] = "category_nm='{$category_nm}' ";
        $img_where[] = "title='{$title}' ";
        $img_where[] = "content ='{$content}' ";
        $img_where[] = "type ='{$type}' ";
        $img_where[] = "link_url ='{$link_url}' ";
        $img_where[] = "link_m_url ='{$link_m_url}' ";
        $img_where[] = "display = '{$display}' ";
        $img_where[] = "tag = '{$tag}' ";

        for($u=0;$u<2;$u++) {
            if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
                if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
                    $imagefile->removeFile( $v_up_imagefile[$u] );
                }
                if ($u == 0) $img_where[] = "img_file = '".$up_imagefile["up_imagefile"][0]["v_file"]."'";
                if ($u == 0) $img_where[] = "img_rfile = '".$up_imagefile["up_imagefile"][0]["r_file"]."'";
                if ($u == 1) $img_where[] = "img_m_file = '".$up_imagefile["up_imagefile"][1]["v_file"]."'";
                if ($u == 1) $img_where[] = "img_m_rfile = '".$up_imagefile["up_imagefile"][1]["r_file"]."'";
            }
        }

        $sql = "UPDATE tblmagazine SET ";
        $sql.= implode(", ",$img_where);
        $sql.= "WHERE no = '{$no}' ";	
        //exdebug($sql);
        //exit;
        pmysql_query($sql,get_db_conn());
    }
    if(!pmysql_error()){
	    if($mode=="insert") {
	    	$insetSeq_sql = "SELECT no FROM tblmagazine ORDER BY no DESC LIMIT 1";
	    	$insertSeq_result = pmysql_query($insetSeq_sql);
	    	$insertSeq_row = pmysql_fetch_object( $insertSeq_result );
	    	$insertSeq = $insertSeq_row->no;
	    	callNaver('magazine', $insertSeq, 'reg');
	        echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
	    }else if($mode=="modify") {	
	    	callNaver('magazine', $no, 'reg');
	        echo "<html></head><body onload=\"alert('수정이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
	    }
    }else{
    	exdebug($sql);
    	alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
    }
}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
# 수정할 배너 불러오기
if( $mode == 'modfiy_select' ){
	$no = $_POST['no'];
	if(!$no) $no = $_GET['no'];
	$mSelectSql = "SELECT * FROM tblmagazine WHERE no =".$no."";
	$mSelectRes = pmysql_query( $mSelectSql, get_db_conn() );
	$mSelectRow = pmysql_fetch_array( $mSelectRes );
	$mSelect = $mSelectRow;
	pmysql_free_result( $mSelectRes );

    //exdebug($imagepath.$mSelect['img_rfile']);
    //$arrProductCodes = explode("||", $mSelect['productcodes']);

	//수정
	$qType = '1';
	$qType_text = '수정';
}

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$qType_text = '등록';
}

// 전체카테고리 가져오기
$arrCategoryList = array();
$sql  = "SELECT DISTINCT category_nm FROM tblmagazine WHERE category_nm != '' ORDER BY category_nm ASC ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrCategoryList[] = $row;
}
pmysql_free_result($result);

?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(mode, no) {

	$(".procode").val("");
	$(".prList").each(function(){
		var num	= $(this).attr("alt");
		$(this).find(".relationProduct"+num).each(function(){
			$(".productcodes" + num).val($(this).val()); 
		});
	});

	if( mode == '0' ){

		if( document.form1.title.value == '' ){
			alert('제목을 입력해야 합니다.');
			return;
		}	
		if($("#magazine_type").val() == "0" || $("#magazine_type").val() == "1"){
			if( document.form1.magazine_category2.value == '' && document.form1.magazine_category.value == ''){
				alert('카테고리명을 입력해야 합니다.');
				return;
			}
		}		
		if( document.form1.magazine_type.value == ''){
			alert('타입을 선택하세요.');
			return;
		}					
		if( confirm('등록하시겠습니까?') ){
			document.form1.mode.value="insert";
			document.form1.target="processFrame";

            var sHTML = oEditors.getById["ir1"].getIR();
            document.form1.content.value=sHTML;
			document.form1.submit();
		} else {
			return;
		}
	} else if ( mode == '1' ) {
		if( document.form1.title.value == '' ){
			alert('제목을 입력해야 합니다.');
			return;
		}

		if( confirm('수정하시겠습니까?') ){
			document.form1.mode.value="modify";
			document.form1.target="processFrame";

            var sHTML = oEditors.getById["ir1"].getIR();
            document.form1.content.value=sHTML;
			document.form1.submit();
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		document.form1.no.value=no;
		document.form1.mode.value="modfiy_select";
		document.form1.submit();
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			document.form1.no.value=no;
			document.form1.mode.value="delete";
			document.form1.target="processFrame";
			document.form1.submit();
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}
}

function goBackList(){
	location.href="community_magazine_list.php";
}


</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; MAGAZINE 관리 &gt; <span>MAGAZINE <?=$qType_text?></span></p></div></div>
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
			<?php include("menu_community.php"); ?>
			</td>
			<td></td>
			<td valign="top">	
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=tb value="magazine">
			<input type=hidden name=mode>
            <input type=hidden name=no value="<?=$no?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<?include("layer_prlistPop.php");?>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">MAGAZINE <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>MAGAZINE <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">MAGAZINE 기본정보</div>
				</td>
			</tr>
			
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<?#include("layer_prlistPop.php");?>
				<div class="table_style01">					
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>제목</span></th>
					<TD><INPUT maxLength=80 size=80 id='title' name='title' value="<?=$mSelect['title']?>"></TD>
				</tr>
				<tr>
					<th><span>작성자(Editor)</span></th>
					<TD><INPUT maxLength=50 size=50 id='writer' name='writer' value="<?=$mSelect['writer']?>" <?=$mSelect['writer'] ? 'readonly' : ''?>></TD>
				</tr>
				<tr id="ID_trCateNm">
					<th><span>카테고리명</span></th>
					<TD>
						<select id="magazine_category" name="magazine_category">
							<option value="">=====직접입력=====</option>
						<?php
						foreach($arrCategoryList as $Key => $Val) {
							if($no == ""){
						?>
							<option value="<?=$Val->category_nm ?>"><?=$Val->category_nm ?></option>			
						<?}else{ ?>
							<option value="<?=$mSelect['category_nm']?>"<?=$mSelect['category_nm'] ?' selected':''?>><?=$mSelect['category_nm']?></option>
						<?} ?>
					<?}?>
						
						</select>
						<input type="text" id="magazine_category2" name="magazine_category2" <?=$mSelect['category_nm'] ?'disabled' : ''?>/>
					</TD>
				</tr>
				<tr>
					<th><span>타입</span></th>
					<TD>
						<select id="magazine_type" name="magazine_type">
							<option value=0  <?=$mSelect['type']== "0"?' selected':''?>>이미지</option>
							<option value=1 <?=$mSelect['type']== "1"?' selected':''?>>동영상</option>
<!--  							<option value=2 <?=$mSelect['type']=="2"?' selected':''?>>상단 배너</option>-->
						</select>
					</TD>
				</tr>
				<tr id="ID_trImgPc">
					<th><span id="imgfile_pc">썸네일이미지(PC)</span><div style="text-align: center;">(이미지 사이즈: 283 x 210)</div></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[0]" id="up_imagefile" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$mSelect['img_file']?>" >
<?	if( is_file($imagepath.$mSelect['img_file']) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$mSelect['img_file']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr id="ID_trImgMobile">
					<th><span id="imgfile_mobile">>썸네일이미지(MOBILE)</span><div style="text-align: center;">(이미지 사이즈: 296 x 220)</div></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[1]" id="up_imagefile2" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[1]" value="<?=$mSelect['img_m_file']?>" >
<?	if( is_file($imagepath.$mSelect['img_m_file']) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$mSelect['img_m_file']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>내용</span></th>
					<TD><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name="content"><?=$mSelect['content']?></textarea></TD>
				</tr>
				<tr id="ID_trUrlPc" style="display: none;">
					<th><span id="urlLinkPc">링크 URL(PC)</span></th>
					<TD><INPUT maxLength=80 size=80 id='link_url' name='link_url' value="<?=$mSelect['link_url']?>"> (내부일 경우는 /front/.... 외부일 경우는 http://hot-t.co.kr/ 의 형식입니다.) </TD>
				</tr>
				<tr id="ID_trUrlMobile" style="display: none;">
					<th><span id="urlLinkMobile">링크 URL(MOBILE)</span></th>
					<TD><INPUT maxLength=80 size=80 id='link_m_url' name='link_m_url' value="<?=$mSelect['link_m_url']?>"> (내부일 경우는 /m/.... 외부일 경우는 http://hot-t.co.kr/ 의 형식입니다.) </TD>
				</tr>
				<tr>
					<th><span>태그</span></th>
					<TD><INPUT maxLength=80 size=80 id='hash_tags' name='hash_tags' value="<?=$mSelect['tag']?>"> (# 없이 ,(콤마)로 구분하여 작성하여 주십시오.) </TD>
				</tr>
				<tr>
					<th><span>노출</span></th>
					<TD><INPUT type='checkbox' id='display' name='display' value="1" <? if( $mSelect['display'] == 'Y' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01 lookbooktable" style='padding-bottom:0px'></div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center>
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['idx']?>' );"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
<?php
	} else {
?>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['no']?>' );"><img src="images/btn_edit2.gif" alt="수정하기"></a>
					<a href="javascript:CheckForm('3', '<?=$mSelect['no']?>' );"><img src="images/botteon_del.gif" alt="삭제하기"></a>
<?php
	}
?>
					<a href="javascript:goBackList();"><img src="img/btn/btn_list.gif" alt="목록보기"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>MAGAZINE <?=$qType_text?></span></dt>
							<dd>- MAGAZINE을 <?=$qType_text?>할 수 있습니다.
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

<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>

<script language="javascript">
var oEditors = [];
$(document).ready( function() {

	if($('#magazine_type').val() == '1'){
		$("#ID_trCateNm").show();
		$("#imgfile_pc").text("썸네일이미지(PC)");
		$("#imgfile_mobile").text("썸네일이미지(MOBILE)");
	}else if($('#magazine_type').val() == '2'){
		$("#ID_trCateNm").hide();
		$("#imgfile_pc").text("배너이미지(PC)");
		$("#imgfile_mobile").text("배너이미지(MOBILE)");
	}else{
		$("#ID_trCateNm").show();
		$("#imgfile_pc").text("썸네일이미지(PC)");
		$("#imgfile_mobile").text("썸네일이미지(MOBILE)");
	}
	
	//카테고리명 선택
	$('#magazine_category').change(function(){
	   $("#magazine_category option:selected").each(function () {
	        
	        if($(this).val()== ''){ //직접입력일 경우
	             $("#magazine_category2").val('');                        //값 초기화
	             $("#magazine_category2").attr("disabled",false); //활성화
	        }else{ //직접입력이 아닐경우
	             $("#magazine_category2").attr("disabled",true); //비활성화
	        }
	   });
	});
	
	//타입 선택
	$('#magazine_type').change(function(){
		if($(this).val()== '1'){ 
			$("#ID_trCateNm").show();
			$("#imgfile_pc").text("썸네일이미지(PC)");
			$("#imgfile_mobile").text("썸네일이미지(MOBILE)");
		}else if($(this).val()== '2'){
			$("#ID_trCateNm").hide();
			$("#imgfile_pc").text("배너이미지(PC)");
			$("#imgfile_mobile").text("배너이미지(MOBILE)");
		}else{
			$("#ID_trCateNm").show();
			$("#imgfile_pc").text("썸네일이미지(PC)");
			$("#imgfile_mobile").text("썸네일이미지(MOBILE)");
		}		
	});
	
	nhn.husky.EZCreator.createInIFrame({
	    oAppRef: oEditors,
	    elPlaceHolder: "ir1",
	    sSkinURI: "../SE2/SmartEditor2Skin.html",
	    htParams : {
	        bUseToolbar : true,             // 툴바 사용 여부 (true:사용/ false:사용하지 않음)
	        bUseVerticalResizer : true,     // 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
	        bUseModeChanger : true,         // 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
	        //aAdditionalFontList : aAdditionalFontSet,     // 추가 글꼴 목록
	        fOnBeforeUnload : function(){
	        }
	    },
	    fOnAppLoad : function(){
	    },
	    fCreator: "createSEditor2"
	        
	});

});

</script>
 
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
