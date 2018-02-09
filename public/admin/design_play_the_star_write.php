<?php
/********************************************************************* 
// 파 일 명		: design_play_the_star_write.php
// 설     명	: '스타가 되고 싶니' 생성, 수정, 삭제
// 상세설명	    : '스타가 되고 싶니' 생성, 수정, 삭제
// 작 성 자		: 2016.02.03 - 최문성
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
	$PageCode = "de-5";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

	include("header.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------

    $tb = "playthestar";
	$mode=$_POST["mode"];
	if(!$mode) $mode=$_GET["mode"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/{$tb}/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	$lbno	            = $_POST["lbno"];

	if($mode=="delete") {
		$qry = "DELETE FROM tbl{$tb} WHERE no ='".trim($lbno)."'";
		pmysql_query( $qry, get_db_conn() );
		echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

	} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.
		
		$title	            = pg_escape_string($_POST["title"]);
		$subtitle	        = $_POST["subtitle"];
		$hidden	            = $_POST["hidden"];
		$is_gnb             = $_POST["is_gnb"];
		if (!$hidden) $hidden = 0;
		$v_up_imagefile	    = $_POST["v_up_imagefile"];
		
		$sort	            = $_POST["sort"];
		$lbcno	            = $_POST["lbcno"];
		$v_up_imagefile2	= $_POST["v_up_imagefile2"];
		$places	            = $_POST["places"];
        $content            = trim($_POST['content']);
        $content            = str_replace("'", "''", $content);
        $content_m            = trim($_POST['content_m']);
        $content_m            = str_replace("'", "''", $content_m);

		$up_imagefile=$imagefile->upFiles();

// 		exdebug((count($_POST[productcodes])/5));
// 		exdebug($_POST);
// 		exdebug($content);
// 		exit;

		$s_cnt	= 0;
		foreach($v_up_imagefile2 as $key=>$value){ 
			if (($key%2) == 0 && $key != 0) $s_cnt++;
			$s_v_up_imagefile2[$s_cnt][] = $value;
			$s_up_imagefile2[$s_cnt][] = $up_imagefile["up_imagefile2"][$key]["v_file"];
			//exdebug($up_imagefile["up_imagefile2"][$key]["v_file"]);
		}

		//exdebug($places);
		//exdebug($s_lbcno);
		//exit;
		//exdebug($productcodes);

		$regdate = date("YmdHis");

		if($mode=="insert") {
			$sql = "INSERT INTO tbl{$tb} (
			title		,
			img	,
			img_m	,
			hidden		,
			is_gnb      ,
			regdate,
			content,
            content_m ) VALUES (
			'{$title}', 
			'".$up_imagefile["up_imagefile"][0]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][1]["v_file"]."', 
			'{$hidden}', 
			'{$is_gnb}', 
			'{$regdate}',
			'{$content}',
            '{$content_m}') RETURNING no";
			$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
			$lbno = $row2[0];

//            $sql = str_replace(array("\r", "\n", "\t"), "", $sql);
//            trigger_error($sql, E_USER_ERROR);

		}else if($mode=="modify") {

			$img_where="";
			$img_where[] = "title='{$title}' ";
			$img_where[] = "hidden='{$hidden}' ";
			$img_where[] = "is_gnb='{$is_gnb}' ";
			$img_where[] = "content ='{$content}' ";
			$img_where[] = "content_m ='{$content_m}' ";

			for($u=0;$u<2;$u++) {
				if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up_imagefile[$u] );
					}
					if ($u == 0) $img_where[] = "img='".$up_imagefile["up_imagefile"][0]["v_file"]."'";
					if ($u == 1) $img_where[] = "img_m='".$up_imagefile["up_imagefile"][1]["v_file"]."'";
				}
			}				

			$sql = "UPDATE tbl{$tb} SET ";
			$sql.= implode(", ",$img_where);
			$sql.= "WHERE no='{$lbno}' ";	

			pmysql_query($sql,get_db_conn());
		}

		list($pts_title, $pts_content, $pts_img, $pts_img_m)=pmysql_fetch_array(pmysql_query("select  title, content, img, img_m from tbl{$tb} WHERE no='{$lbno}'"));

		$pts_imgLink	= "";
		if( is_file($imagepath.$pts_img_m) ){
			$pts_imgLink	= "http://".$_SERVER[HTTP_HOST]."/data/shopimages/{$tb}/".$pts_img_m;
		}
		if( is_file($imagepath.$pts_img) ){
			$pts_imgLink	= "http://".$_SERVER[HTTP_HOST]."/data/shopimages/{$tb}/".$pts_img;
		}

		$html_Dir = $_SERVER[DOCUMENT_ROOT].'/front/playthestar_sns/';
		$outHtml.= "<html>
<head>
<!-- fb -->
<meta property='og:site_name' content='".$_shopdata->shoptitle."'/>
<meta property=\"og:type\" content=\"website\" />
<meta property=\"og:title\" content=\"".$_shopdata->shoptitle."\" />
<meta property=\"og:url\" content=\"http://".$_SERVER[HTTP_HOST]."/front/playthestar_sns/".$lbno.".html\" />
<meta property=\"og:description\" content=\"PLAY THE STAR - ".addslashes($pts_title)."\" />
<meta property=\"og:image\" content=\"".$pts_imgLink."\" />

<!-- tw -->
<meta name=\"twitter:card\" content=\"summary_large_image\">
<meta name=\"twitter:site\" content=\"@".$_shopdata->shoptitle."\">
<meta name=\"twitter:title\" content=\"".$_shopdata->shoptitle."\">
<meta name=\"twitter:description\" content=\"PLAY THE STAR - ".addslashes($pts_title)."\">
<meta name=\"twitter:image\" content=\"".$pts_imgLink."\">
</head>
<body onload='javascript:location.replace(\"http://".$_SERVER[HTTP_HOST]."/front/play_the_star_detail.php?id=".$lbno."\");'>
</body>	
</html>
		";

		$upQrt_f = fopen($html_Dir.$lbno.'.html','w');
		fwrite($upQrt_f, $outHtml );
		fclose($upQrt_f);
		chmod($html_Dir.$lbno.'.html',0777);

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
	$lbSelectSql = "SELECT * FROM tbl{$tb} WHERE no ='".trim($lbno)."' ";
	$lbSelectRes = pmysql_query( $lbSelectSql, get_db_conn() );
	$lbSelectRow = pmysql_fetch_array( $lbSelectRes );
	$mSelect = $lbSelectRow;
	pmysql_free_result( $lbSelectRes );

//    $arrProductCodes = explode("||", $mSelect['productcodes']);

	//수정
	$qType = '1';
	$qType_text = '수정';
}

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$qType_text = '등록';
}

#노출 기본 세팅
$display['0'] = '비노출';
$display['1'] = '노출';
?>

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
            alert(sHTML);
            document.form1.content.value=sHTML;

            var sHTML_m = oEditors_m.getById["ir2"].getIR();
            document.form1.content_m.value=sHTML_m;

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

            var sHTML_m = oEditors_m.getById["ir2"].getIR();
            document.form1.content_m.value=sHTML_m;

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
	location.href="design_play_the_star_list.php";
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; <?=strtoupper($tb)?>정보관리 &gt;<span><?=strtoupper($tb)?> <?=$qType_text?></span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=tb value="<?=$tb?>">
			<input type=hidden name=mode>
			<input type="hidden" name="itemCount">
			<input type=hidden name=lbno value="<?=$lbno?>">		
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=strtoupper($tb)?> <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=strtoupper($tb)?>을 <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><?=strtoupper($tb)?> 기본정보</div>
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
					<th><span>제목</span></th>
					<TD><INPUT maxLength=80 size=80 id='title' name='title' value="<?=$mSelect['title']?>"></TD>
				</tr>
				<tr>
					<th><span>썸네일이미지(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$mSelect['img']?>" >
<?	if( is_file($imagepath.$mSelect['img']) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$mSelect['img']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>썸네일이미지(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[1]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[1]" value="<?=$mSelect['img_m']?>" >
<?	if( is_file($imagepath.$mSelect['img_m']) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$mSelect['img_m']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>내용(PC)</span></th>
					<TD><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name="content"><?=$mSelect['content']?></textarea></TD>
				</tr>
				<tr>
					<th><span>내용(MOBILE)</span></th>
					<TD><textarea wrap=off  id="ir2" style="WIDTH: 100%; HEIGHT: 300px" name="content_m"><?=$mSelect['content_m']?></textarea></TD>
				</tr>
				<tr>
					<th><span>GNB 노출</span></th>
					<TD><INPUT type='checkbox' id='is_gnb' name='is_gnb' value="1" <? if( $mSelect['is_gnb'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
				</tr>
				<tr>
					<th><span>노출</span></th>
					<TD><INPUT type='checkbox' id='hidden' name='hidden' value="1" <? if( $mSelect['hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
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
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['no']?>' );"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
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
							<dt><span><?=strtoupper($tb)?> <?=$qType_text?></span></dt>
							<dd>- <?=strtoupper($tb)?>을 <?=$qType_text?>할 수 있습니다.
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
$(document).on('click', '#tr_del',function(e){
	if(confirm('삭제하시겠습니까?')){
		$(this).parent().parent().parent().parent().remove();
	}
});

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

var oEditors_m = [];
nhn.husky.EZCreator.createInIFrame({
    oAppRef: oEditors_m,
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
</script>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
