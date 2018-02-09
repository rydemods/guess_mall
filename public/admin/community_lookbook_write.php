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
$PageCode = "co-6";
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
$imagepath = $Dir.DataDir."shopimages/lookbook/";
// 이미지 파일
$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$no = $_POST["no"];
 
if($mode=="delete") {
	
	$up_imagefile = $imagefile->upFiles();
    $qry = "DELETE FROM tbllookbook WHERE no ='".$no."'";
    pmysql_query( $qry, get_db_conn() );
    
    for($u=0;$u<2;$u++) {
    	if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
    		$imagefile->removeFile( $v_up_imagefile[$u] );
    	}
		if( is_file( $imagepath.$v_up_image[$u] ) > 0 ){
    		$imagefile->removeFile( $v_up_image[$u] );
    	}
    }
    callNaver('lookbook', $no, 'del');
    echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.
    
	$title	            = pg_escape_string($_POST["title"]);
    //$content            = trim($_POST['content']);
    //$content            = str_replace("'", "''", $content);
	$img	        = pg_escape_string($_POST["img"]);
	$img_m	        = pg_escape_string($_POST["img_m"]);
    $content	        = pg_escape_string($_POST["content"]);
    $display            = $_POST["display"];

	$brandcd            = $_POST["brandcd"];
	$season            = $_POST["season"];
	$relationProductArr	 = $_POST["relationProduct"];
	$relationProduct	= implode(",",$relationProductArr);
	


    $tag          = pg_escape_string($_POST["hash_tags"]);
    if (!$display) $display = "N";
    else $display = "Y";

    $v_up_imagefile	    = $_POST["v_up_imagefile"];
    $v_up_image	    = $_POST["v_up_image"];

    $up_imagefile = $imagefile->upFiles();
//     exdebug($up_imagefile);
//     exit;

    $regdt = date("YmdHis");

    if($mode=="insert") {
        $sql = "INSERT INTO tbllookbook (
        title,
        content,
        img_file,
        img_m_file, 
        regdate,
        access, 
        img,
        img_m,
        tag,
        display,
		brandcd,
		season,
		relation_product
        ) VALUES (
        '{$title}', 
        '{$content}', 
        '".$up_imagefile["up_imagefile"][0]["v_file"]."', 
        '".$up_imagefile["up_imagefile"][1]["v_file"]."', 
        '{$regdt}', 
        0,
        '{$img}', 
        '{$img_m}', 
        '{$tag}',
        '{$display}',
        '{$brandcd}',
        '{$season}',
		'{$relationProduct}'
        ) ";
        pmysql_query($sql,get_db_conn());
//         exdebug($sql);
//         exit;

    }else if($mode=="modify") {


	

        $img_where="";
        $img_where[] = "title='{$title}' ";
        $img_where[] = "content ='{$content}' ";
        $img_where[] = "img ='{$img}' ";
        $img_where[] = "img_m ='{$img_m}' ";
        $img_where[] = "display = '{$display}' ";
        $img_where[] = "tag = '{$tag}' ";
		$img_where[] = "brandcd = '{$brandcd}' ";
		$img_where[] = "season = '{$season}' ";
		$img_where[] = "relation_product = '{$relationProduct}' ";
		

        for($u=0;$u<2;$u++) {
            if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
                if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
                    $imagefile->removeFile( $v_up_imagefile[$u] );
                }
                if ($u == 0) $img_where[] = "img_file = '".$up_imagefile["up_imagefile"][0]["v_file"]."'";
                if ($u == 1) $img_where[] = "img_m_file = '".$up_imagefile["up_imagefile"][1]["v_file"]."'";
            }
//             if( strlen( $up_imagefile["up_image"][$u]["v_file"] ) > 0 ){
//             	if( is_file( $imagepath.$v_up_image[$u] ) > 0 ){
//             		$imagefile->removeFile( $v_up_image[$u] );		
//             	}
//             	if ($u == 0) $img_where[] = "img = '".$up_imagefile["up_imagefile"][0]["v_file"]."'";
//             	if ($u == 1) $img_where[] = "img_m = '".$up_imagefile["up_imagefile"][1]["v_file"]."'";
//             }
        }

        $sql = "UPDATE tbllookbook SET ";
        $sql.= implode(", ",$img_where);
        $sql.= "WHERE no = '{$no}' ";	
        //exdebug($sql);
        //exit;
        pmysql_query($sql,get_db_conn());
    }
    if(!pmysql_error()){
	    if($mode=="insert") {
	    	$insetSeq_sql = "SELECT no FROM tbllookbook ORDER BY no DESC LIMIT 1";
	    	$insertSeq_result = pmysql_query($insetSeq_sql);
	    	$insertSeq_row = pmysql_fetch_object( $insertSeq_result );
	    	$insertSeq = $insertSeq_row->no;
	    	callNaver('lookbook', $insertSeq, 'reg');
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
# 브랜드시즌
#---------------------------------------------------------------

$prCateSql = "select brandcd, brandname from tblproductbrand order by brandname  ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );
$ii=0;
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prBrand[$ii] = $prRow;
	$ii++;
}

$prCateSql = "select season,season_eng_name from tblproductseason order by season_eng_name desc  ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );
$ii=0;
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prSeason[$ii] = $prRow;
	$ii++;
}


pmysql_free_result( $prCateRes );



#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
# 수정할 배너 불러오기
if( $mode == 'modfiy_select' ){
	$no = $_POST['no'];
	if(!$no) $no = $_GET['no'];
	$mSelectSql = "SELECT * FROM tbllookbook WHERE no =".$no."";
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
				
		if( confirm('등록하시겠습니까?') ){
			document.form1.mode.value="insert";
			document.form1.target="processFrame";

            var sHTML = oEditors.getById["ir1"].getIR();
            document.form1.img.value=sHTML;
            var sHTML = oEditors2.getById["ir2"].getIR();
            document.form1.img_m.value=sHTML;
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
            document.form1.img.value=sHTML;
            var sHTML = oEditors2.getById["ir2"].getIR();
            document.form1.img_m.value=sHTML;
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
	location.href="community_lookbook_list.php";
}


</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; LOOKBOOK 관리 &gt; <span>LOOKBOOK <?=$qType_text?></span></p></div></div>
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
			<input type=hidden name=tb value="lookbook">
			<input type=hidden name=mode>
            <input type=hidden name=no value="<?=$no?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<?include("layer_prlistPop.php");?>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">LOOKBOOK <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>LOOKBOOK <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">LOOKBOOK 기본정보</div>
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
					<th><span>브랜드/시즌</span></th>
					<TD>
						브랜드:
						<select name="brandcd">
							<option value="">==선택==</option>
							<?for($ii=0; $ii<count($prBrand); $ii++){ ?>
							<option value="<?=$prBrand[$ii][0]?>" <?if($mSelect['brandcd']==$prBrand[$ii][0]){echo "selected";}?>><?=$prBrand[$ii][1]?></option>
							<?}?>
						</select>
						/시즌:
						<select name="season">
							<option value="">==선택==</option>
							<?for($ii=0; $ii<count($prSeason); $ii++){ ?>
							<option value="<?=$prSeason[$ii][0]?>" <?if($mSelect['season']==$prSeason[$ii][0]){echo "selected";}?>><?=$prSeason[$ii][1]?></option>
							<?}?>
							
						</select>
					</TD>
				</tr>
				
				<tr>
					<th>
					<span>관련상품</span>&nbsp;&nbsp;
					<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a>
					</th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">	
								<input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value=""/>								
									<colgroup>
										<col width=20></col>
										<col width=50></col>
										<col width=></col>
									</colgroup>

								<?


								$relationProductArr = explode(",",$mSelect['relation_product']);
								for($ii=0; $ii < count($relationProductArr); $ii++){
									if($relationProductArr[$ii]!=""){
									$relationProductArr_serialze .= "'".$relationProductArr[$ii]."',";

									$insetSeq_sql = "select * from tblproduct where productcode = '".$relationProductArr[$ii]."'";
									$insertSeq_result = pmysql_query($insetSeq_sql);
									$insertSeq_row = pmysql_fetch_object( $insertSeq_result );
									$tinyimage = $insertSeq_row->tinyimage;
									$productname = $insertSeq_row->productname;
									$productcode = $insertSeq_row->productcode;
								?>
									<tr align="center">
										<td style='border:0px'>
											<a name="pro_upChange" style="cursor: hand;">
												<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
											</a>
											<br>
											<a name="pro_downChange" style="cursor: hand;">
												<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
											</a>
										</td>
										<td style='border:0px'>
											
											<img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $tinyimage );?>" border="1"/>
											<input type='hidden' name='relationProduct[]' value='<?=$productcode?>'>
										</td>
										<td style='border:0px' align="left"><?=$productname?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$productcode?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								
								<?
									}
								}
								pmysql_free_result( $mSelectRes );
								?>
								</table>
							</div>
					</td>
				</tr>

				

				<tr id="ID_trImgPc">
					<th><span id="imgfile_pc">썸네일이미지(PC)</span></th>
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
					<th><span id="imgfile_mobile">썸네일이미지(MOBILE)</span></th>
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
<!--  				
				<tr>
					<th><span id="img_pc">상세 이미지(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_image[0]" id="up_image" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_image[0]" value="<?=$mSelect['img']?>" >
<?	if( is_file($imagepath.$mSelect['img']) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$mSelect['img']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span id="img_mobile">상세 이미지(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_image[1]" id="up_image2" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_image[1]" value="<?=$mSelect['img_m']?>" >
<?	if( is_file($imagepath.$mSelect['img_m']) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$mSelect['img_m']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
-->				
				<tr>
					<th><span>LOOKBOOK 이미지(PC)<br></span><div style="text-align: center;">(이미지 사이즈: 1160 x 738)</div></th>
					<TD><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name="img"><?=$mSelect['img']?></textarea></TD>
				</tr>
				<tr>
					<th><span>LOOKBOOK 이미지(mobile)<br></span></th>
					<TD><textarea wrap=off  id="ir2" style="WIDTH: 100%; HEIGHT: 300px" name="img_m"><?=$mSelect['img_m']?></textarea></TD>
				</tr>
				<tr>
					<th><span>내용</span></th>
					<TD><textarea wrap=off  id="" style="WIDTH: 100%; HEIGHT: 300px" name="content"><?=$mSelect['content']?></textarea></TD>
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
var oEditors2 = [];
$(document).ready( function() {

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

	nhn.husky.EZCreator.createInIFrame({
	    oAppRef: oEditors2,
	    elPlaceHolder: "ir2",
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
