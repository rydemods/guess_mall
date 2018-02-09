<?php
/********************************************************************* 
// 파 일 명		: sns_instagram_write.php
// 설     명	: 인스타그램 관리 생성, 수정, 삭제
// 상세설명	    : 인스타그램 관리 생성, 수정, 삭제
// 작 성 자		: 2016.08.01 - 정정호
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
$PageCode = "co-4";
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
$imagepath = $Dir.DataDir."shopimages/instagram/";
// 이미지 파일
$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$lbno = $_POST["lbno"];

if($mode=="delete") {

    $qry = "DELETE FROM tblinstagram WHERE idx ='".trim($lbno)."'";
    pmysql_query( $qry, get_db_conn() );
    echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.
    
    $title	            = pg_escape_string($_POST["title"]);
    //$content            = trim($_POST['content']);
    //$content            = str_replace("'", "''", $content);
    $content	        = pg_escape_string($_POST["content"]);
    $link_url	        = $_POST["link_url"];
    $link_m_url         = $_POST["link_m_url"];
    $hash_tags          = pg_escape_string($_POST["hash_tags"]);
    $display            = $_POST["display"];
    //관련상품
    $relationProduct = $_POST['relationProduct'];
    foreach ($relationProduct as $i => $v){
    	if($i == 0){
    		$relationProduct = $v;
    	}else{
    		$relationProduct .= ",".$v;
    	}
    }
    
   	$relationProduct;
    if (!$display) $display = "N";
    else $display = "Y";
    $v_up_imagefile	    = $_POST["v_up_imagefile"];

    $up_imagefile = $imagefile->upFiles();
    //exdebug($up_imagefile);
    //exit;

    $regdt = date("YmdHis");

    if($mode=="insert") {
        $sql = "INSERT INTO tblinstagram (
        title,
        content,
        link_url,
        link_m_url,
        img_file,
        img_rfile, 
        img_m_file, 
        img_m_rfile, 
        display, 
        regdt, 
        hash_tags,
        relation_product
        ) VALUES (
        '{$title}', 
        '{$content}', 
        '{$link_url}', 
        '{$link_m_url}', 
        '".$up_imagefile["up_imagefile"][0]["v_file"]."', 
        '".$up_imagefile["up_imagefile"][0]["r_file"]."', 
        '".$up_imagefile["up_imagefile"][1]["v_file"]."', 
        '".$up_imagefile["up_imagefile"][1]["r_file"]."', 
        '{$display}', 
        '{$regdt}', 
        '{$hash_tags}',
        '{$relationProduct}'
        ) RETURNING idx";
        $row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
        $idx = $row2[0];

//         exdebug($idx);
//         exit;

    }else if($mode=="modify") {

        $img_where="";
        $img_where[] = "title='{$title}' ";
        $img_where[] = "content ='{$content}' ";
        $img_where[] = "link_url ='{$link_url}' ";
        $img_where[] = "link_m_url ='{$link_m_url}' ";
        $img_where[] = "display = '{$display}' ";
        $img_where[] = "hash_tags = '{$hash_tags}' ";
        $img_where[] = "relation_product = '{$relationProduct}' ";

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

        $sql = "UPDATE tblinstagram SET ";
        $sql.= implode(", ",$img_where);
        $sql.= "WHERE idx = '{$lbno}' ";	
        //exdebug($sql);
        //exit;
        pmysql_query($sql,get_db_conn());
    }

    if($mode=="insert") {
        echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
    }else if($mode=="modify") {	
        echo "<html></head><body onload=\"alert('수정이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
    }
}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
# 수정할 배너 불러오기
if( $mode == 'modfiy_select' ){
	$lbno = $_POST['lbno'];
	if(!$lbno) $lbno = $_GET['lbno'];
	$lbSelectSql = "SELECT * FROM tblinstagram WHERE idx ='".trim($lbno)."' ";
	$lbSelectRes = pmysql_query( $lbSelectSql, get_db_conn() );
	$lbSelectRow = pmysql_fetch_array( $lbSelectRes );
	$mSelect = $lbSelectRow;
	pmysql_free_result( $lbSelectRes );

    //exdebug($imagepath.$mSelect['img_rfile']);
    //$arrProductCodes = explode("||", $mSelect['productcodes']);

	//수정
	$qType = '1';
	$qType_text = '수정';
}

#---------------------------------------------------------------
# 관련상품
#---------------------------------------------------------------
$bProductSql = "SELECT a.productcode,a.productname,a.sellprice,a.tinyimage ";
$bProductSql.= "FROM tblproduct a ";
$arrRealtionCode = explode(",",$mSelect['relation_product']);
foreach($arrRealtionCode as $i => $v){
	if($i == 0){
		$bProductSql.= " WHERE (a.productcode = '".$v."'";
	}else{
		$bProductSql.= " OR a.productcode = '".$v."'";
	}
}
$bProductSql.=")";

$bProductResult = pmysql_query($bProductSql,get_db_conn());
while($bProductRow = pmysql_fetch_array($bProductResult)){
	$thisBannerProduct[] = $bProductRow;
}
pmysql_free_result( $bProductResult );

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$qType_text = '등록';
}

#노출 기본 세팅
$display['N'] = '비노출';
$display['Y'] = '노출';
?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(mode, lbno) {

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
		document.form1.lbno.value=lbno;
		document.form1.mode.value="modfiy_select";
		document.form1.submit();
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			document.form1.lbno.value=lbno;
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
	location.href="sns_instagram_list.php";
}


</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; SNS 관리 &gt; <span>인스타그램 <?=$qType_text?></span></p></div></div>
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
			<input type=hidden name=tb value="instagram">
			<input type=hidden name=mode>
            <input type=hidden name=lbno value="<?=$lbno?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<?include("layer_prlistPop.php");?>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">인스타그램 <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>인스타그램을 <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">인스타그램 기본정보</div>
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
					<th><span>썸네일이미지(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$mSelect['img_file']?>" >
<?	if( is_file($imagepath.$mSelect['img_file']) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$mSelect['img_file']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>썸네일이미지(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[1]" style="WIDTH: 400px"><br>
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
				<tr>
					<th><span>링크 URL(PC)</span></th>
					<TD><INPUT maxLength=80 size=80 id='link_url' name='link_url' value="<?=$mSelect['link_url']?>"> (내부일 경우는 /front/.... 외부일 경우는 http://hot-t.co.kr/ 의 형식입니다.) </TD>
				</tr>
				<tr>
					<th><span>링크 URL(MOBILE)</span></th>
					<TD><INPUT maxLength=80 size=80 id='link_m_url' name='link_m_url' value="<?=$mSelect['link_m_url']?>"> (내부일 경우는 /m/.... 외부일 경우는 http://hot-t.co.kr/ 의 형식입니다.) </TD>
				</tr>
				<TR id='ID_RelationProduct' >
					<th><span>관련상품</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">	
								<input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value=""/>								
									<colgroup>
										<col width=20></col>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?foreach($thisBannerProduct as $bannerProductKey=>$bannerProduct){?>	
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
											<!-- <img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/> -->
                                                    <img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $bannerProduct['tinyimage'] );?>" border="1"/>
											<input type='hidden' name='relationProduct[]' value='<?=$bannerProduct[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bannerProduct[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bannerProduct[productcode]?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?}?>
								</table>
							</div>
					</td>
				</TR>
				<tr>
					<th><span>태그</span></th>
					<TD><INPUT maxLength=80 size=80 id='hash_tags' name='hash_tags' value="<?=$mSelect['hash_tags']?>"> (# 없이 ,(콤마)로 구분하여 작성하여 주십시오.) </TD>
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
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['idx']?>' );"><img src="images/btn_edit2.gif" alt="수정하기"></a>
					<a href="javascript:CheckForm('3', '<?=$mSelect['idx']?>' );"><img src="images/botteon_del.gif" alt="삭제하기"></a>
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
							<dt><span>인스타그램 <?=$qType_text?></span></dt>
							<dd>- 인스타그램을 <?=$qType_text?>할 수 있습니다.
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
$(document).ready(function(){});
/*
$(document).on('click', '#tr_del',function(e){
	if(confirm('삭제하시겠습니까?')){
		$(this).parent().parent().parent().parent().remove();
	}
});
*/
var oEditors = [];
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



</script>
 
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
