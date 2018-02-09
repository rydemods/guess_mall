<?php
/********************************************************************* 
// 파 일 명		: community_store_story_write.php
// 설     명	: STORE STORY 관리 생성, 수정, 삭제
// 상세설명	    : STORE STORY 관리 생성, 수정, 삭제
// 작 성 자		: 2016.09.08 - 김재수
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
$PageCode = "co-7";
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
$imagepath = $Dir.DataDir."shopimages/store_story/";
// 이미지 파일
$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$sno = $_POST["sno"];

if($mode=="delete_exe") {

    $qry = "DELETE FROM tblstorestory WHERE sno ='".trim($sno)."'";
    pmysql_query( $qry, get_db_conn() );

    $qry = "DELETE FROM tblstorestory_comment WHERE sno ='".trim($sno)."'";
    pmysql_query( $qry, get_db_conn() );

    echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

} else if($mode=="insert_exe" || $mode=="modify_exe") {				// DB를 수정한다.
    
    $store_code	        = $_POST["store_code"];
    $title				        = $_POST["title"];
    $content					= $_POST["content"];
    $v_up_filename	    = $_POST["v_up_filename"];

    $up_imagefile = $imagefile->upFiles();
    //exdebug($up_imagefile);
    //exit;

    $regdt = date("YmdHis");

    if($mode=="insert_exe") {
        $sql = "INSERT INTO tblstorestory (
        mem_id,
        store_code,
        filename,
        vfilename,
        title,
        content,
        regdt
        ) VALUES (
        '{$mem_id}', 
        '{$store_code}', 
        '".$up_imagefile["up_imagefile"][0]["v_file"]."', 
        '".$up_imagefile["up_imagefile"][0]["r_file"]."', 
        '{$title}', 
        '{$content}', 
        '{$regdt}'
        ) RETURNING idx";
        $row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
        $idx = $row2[0];

//         exdebug($idx);
//         exit;

    }else if($mode=="modify_exe") {

        $img_where="";
        $img_where[] = "store_code='{$store_code}' ";
        $img_where[] = "title ='{$title}' ";
        $img_where[] = "content ='{$content}' ";

        for($u=0;$u<1;$u++) {
            if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
                if( is_file( $imagepath.$v_up_filename[$u] ) > 0 ){
                    $imagefile->removeFile( $v_up_filename[$u] );
                }
                if ($u == 0) $img_where[] = "filename = '".$up_imagefile["up_imagefile"][0]["v_file"]."'";
                if ($u == 0) $img_where[] = "vfilename = '".$up_imagefile["up_imagefile"][0]["r_file"]."'";
            }
        }

        $sql = "UPDATE tblstorestory SET ";
        $sql.= implode(", ",$img_where);
        $sql.= "WHERE sno = '{$sno}' ";	
        //exdebug($sql);
        //exit;
        pmysql_query($sql,get_db_conn());
    }

    if($mode=="insert_exe") {
        echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
    }else if($mode=="modify_exe") {	
        echo "<html></head><body onload=\"alert('수정이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
    }
}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
# 수정할 배너 불러오기
if( $mode == 'modify' ){
	$sno = $_POST['sno'];
	if(!$sno) $sno = $_GET['sno'];
	$storySelectSql = "SELECT * FROM tblstorestory WHERE sno ='".trim($sno)."' ";
	$storySelectRes = pmysql_query( $storySelectSql, get_db_conn() );
	$storySelectRow = pmysql_fetch_array( $storySelectRes );
	$s_row = $storySelectRow;
	pmysql_free_result( $storySelectRes );

    //exdebug($imagepath.$s_row['img_rfile']);
    //$arrProductCodes = explode("||", $s_row['productcodes']);

	//수정
	$qType = '1';
	$qType_text = '수정';
}

// 전체매장 가져오기
$arrStoreList = array();
$sql  = "SELECT * FROM tblstore WHERE view = '1' ORDER BY sort asc, sno desc ";
$result = pmysql_query($sql);
while ($row = pmysql_fetch_object($result)) {
	$arrStoreList[] = $row;
}
pmysql_free_result($result);

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
function CheckForm(mode, sno) {
		
	var sHTML = oEditors.getById["ir1"].getIR();
	document.form1.content.value=sHTML;

	if( mode == '0' ){

		if( document.form1.title.value == '' ){
			alert('제목을 입력해야 합니다.');
			return;
		}			
		if( confirm('등록하시겠습니까?') ){
			document.form1.mode.value="insert_exe";
			document.form1.target="processFrame";
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
			document.form1.mode.value="modify_exe";
			document.form1.target="processFrame";
			document.form1.submit();
		} else {
			return;
		}
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			document.form1.sno.value=sno;
			document.form1.mode.value="delete_exe";
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
	location.href="community_store_story_list.php";
}


</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; STORE STORY 관리 &gt; <span>STORE STORY <?=$qType_text?></span></p></div></div>
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
			<input type=hidden name=mode>
            <input type=hidden name=sno value="<?=$sno?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<?include("layer_prlistPop.php");?>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">STORE STORY <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>STORE STORY을 <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">STORE STORY 기본정보</div>
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
					<th><span>회원 ID</span></th>
					<TD><?=$s_row['mem_id']?></TD>
				</tr>
				<tr>
					<th><span>지점명</span></th>
					<TD>
						<select id="store_code" name="store_code">
							<option value="">지점명</option>
						<?php
						foreach($arrStoreList as $storeKey => $storeVal) {
						?>
							<option value="<?=$storeVal->store_code?>"<?=$s_row['store_code']==$storeVal->store_code?' selected':''?>><?=$storeVal->name?></option>							
						<?
						}
						?>
						</select>
					</TD>
				</tr>
				<tr>
					<th><span>제목</span></th>
					<TD><input name="title" style="WIDTH: 100%" value="<?=$s_row['title']?>"></TD>
				</tr>
				<tr>
					<th><span>이미지</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_filename[0]" value="<?=$s_row['filename']?>" >
<?	if( is_file($imagepath.$s_row['filename']) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$s_row['filename']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>내용</span></th>
					<TD><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name="content"><?=$s_row['content']?></textarea></TD>
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
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$s_row['idx']?>' );"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
<?php
	} else {
?>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$s_row['idx']?>' );"><img src="images/btn_edit2.gif" alt="수정하기"></a>
					<a href="javascript:CheckForm('3', '<?=$s_row['idx']?>' );"><img src="images/botteon_del.gif" alt="삭제하기"></a>
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
							<dt><span>STORE STORY <?=$qType_text?></span></dt>
							<dd>- STORE STORY을 <?=$qType_text?>할 수 있습니다.
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
	var oEditors = [];

	nhn.husky.EZCreator.createInIFrame({
		oAppRef: oEditors,
		elPlaceHolder: "ir1",
		sSkinURI: "../SE2/SmartEditor2Skin.html",
		htParams : {
			bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
			bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
			//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
			fOnBeforeUnload : function(){
			}
		},
		fOnAppLoad : function(){
		},
		fCreator: "createSEditor2"
	});
</script>
 
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
