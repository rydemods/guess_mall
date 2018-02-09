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
$PageCode = "co-2";
$MenuCode = "cscenter";
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

//경로
$filepath = $Dir.DataDir."shopimages/cscenter/";
//파일
$noticefile = new FILE($filepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$no = $_POST["no"];
 
if($mode=="delete") {
	
	$qry = "DELETE FROM tblcsnotice WHERE no ='".$no."'";
    pmysql_query( $qry, get_db_conn() );

	$v_up_file	    = $_POST["v_up_file"];

	if( is_file( $filepath.$v_up_file[0] ) > 0 ){
		$noticefile->removeFile( $v_up_file[0] );
	}

    echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;

} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.
    
	$title	            = pg_escape_string($_POST["title"]);
    $content	        = pg_escape_string($_POST["content"]);
    $viewyn            = $_POST["viewyn"];
	$notice_category            = $_POST["notice_category"];
	$notice_type		= $_POST["notice_type"]?$_POST["notice_type"]:"N";
	$v_up_file	    = $_POST["v_up_file"];
	$store_no =$_POST["store_no"];

    $up_noticefile = $noticefile->upFiles();

    $regdt = date("YmdHis");

	if($notice_type=="Y"){
		$notice_date=date("YmdHis");
	}else{
		$notice_date="";
	}

	//exdebug($up_noticefile);
	//exit;

    if($mode=="insert") {
        $sql = "INSERT INTO tblcsnotice (
        title,
        content,
        notice_type,
        notice_date, 
        notice_file,
        notice_file_ori, 
        regdt,
        viewyn,
		notice_category,
		store_no
		) VALUES (
        '{$title}', 
        '{$content}', 
		'{$notice_type}', 
		'{$notice_date}', 
        '".$up_noticefile["up_file"][0]["v_file"]."', 
        '".$up_noticefile["up_file"][0]["r_file"]."', 
        '{$regdt}', 
        '{$viewyn}',
		'{$notice_category}',
		'{$store_no}'
        ) ";
        pmysql_query($sql,get_db_conn());
//         exdebug($sql);
//         exit;

    }else if($mode=="modify") {

        $img_where="";
        $img_where[] = "title='{$title}' ";
        $img_where[] = "content ='{$content}' ";
        $img_where[] = "notice_type ='{$notice_type}' ";
        //$img_where[] = "notice_date ='{$notice_date}' ";
        $img_where[] = "viewyn = '{$viewyn}' ";
		$img_where[] = "notice_category = '{$notice_category}' ";
		$img_where[] = "store_no = '{$store_no}' ";

       
		if( strlen( $up_noticefile["up_file"][0]["v_file"] ) > 0 ){
			if( is_file( $filepath.$v_up_file[$u] ) > 0 ){
				$noticefile->removeFile( $v_up_file[$u] );
			}
			$img_where[] = "notice_file = '".$up_imagefile["up_file"][0]["v_file"]."'";
			$img_where[] = "notice_file_ori = '".$up_imagefile["up_file"][0]["r_file"]."'";
		}
       

        $sql = "UPDATE tblcsnotice SET ";
        $sql.= implode(", ",$img_where);
        $sql.= "WHERE no = '{$no}' ";	
        //exdebug($sql);
        //exit;
        pmysql_query($sql,get_db_conn());
    }
    if(!pmysql_error()){
	    if($mode=="insert") {
	        echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
	    }else if($mode=="modify") {	
	        echo "<html></head><body onload=\"alert('수정이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
	    }
    }else{
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
	$mSelectSql = "SELECT * FROM tblcsnotice WHERE no =".$no."";
	$mSelectRes = pmysql_query( $mSelectSql, get_db_conn() );
	$mSelectRow = pmysql_fetch_array( $mSelectRes );
	$mSelect = $mSelectRow;
	pmysql_free_result( $mSelectRes );

	//수정
	$qType = '1';
	$qType_text = '수정';

	$checked["viewyn"][$mSelect['viewyn']]="checked";
	$checked["notice_type"][$mSelect['notice_type']]="checked";
	$selected["notice_category"][$mSelect['notice_category']]="selected";
	$selected["store_no"][$mSelect['store_no']]="selected";
}

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$qType_text = '등록';

	$checked["viewyn"]['Y']="checked";
}

#매장정보 가져오기
$store_qry="select * from tblstore order by name";
$store_result=pmysql_query($store_qry);

?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(mode, no) {
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
	location.href="cscenter_notice.php";
}


</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : CS관리  &gt; CS관리 &gt; <span>CS공지사항 <?=$qType_text?></span></p></div></div>
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
			<?php include("menu_cscenter.php"); ?>
			</td>
			<td></td>
			<td valign="top">	
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=tb value="lookbook">
			<input type=hidden name=mode>
            <input type=hidden name=no value="<?=$no?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">CS공지사항 <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>CS공지사항 <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">CS공지사항 기본정보</div>
				</td>
			</tr>
			
			<tr><td height=3></td></tr>
			<tr>
				<td>
				
				<div class="table_style01">					
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>

				<tr>
					<th><span>카테고리 분류</span></th>
					<TD>
						<select name="notice_category">
							<option value="">공통</option>

						</select>
					</TD>
				</tr>

				<tr>
					<th><span>매장선택</span></th>
					<TD>
						<select name="store_no">
							<option value=''>전체</option>
							<?while($store_data=pmysql_fetch_array($store_result)){?>
								<option value="<?=$store_data['sno']?>" <?=$selected["store_no"][$store_data['sno']]?>><?=$store_data["name"]?></option>
							<?}?>
						</select>
					</TD>
				</tr>


				<tr>
					<th><span>게시여부</span></th>
					<TD>
						<INPUT type='radio' id='viewyn' name='viewyn' value="Y" <?=$checked["viewyn"]['Y']?>> 게시 
						<INPUT type='radio' id='viewyn' name='viewyn' value="N" <?=$checked["viewyn"]['N']?>> 미게시 
					</TD>
				</tr>

				<tr>
					<th><span>공지글등록</span></th>
					<TD><INPUT type='checkbox' id='notice_type' name='notice_type' value="Y" <?=$checked["notice_type"]['Y']?>> 공지글로 등록됩니다. </TD>
				</tr>
				
				<tr id="ID_trImgPc">
					<th><span id="imgfile_pc">파일첨부</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_file[0]" id="up_file" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_file[0]" value="<?=$mSelect['notice_file']?>" >
<?	if( is_file($filepath.$mSelect['notice_file']) ){ ?>
						<div style='margin-top:5px' >
							<a href="cscenter_filedownload.php?file_name=<?=$mSelect['notice_file']?>&file_name_ori=<?=$mSelect['notice_file_ori']?>"><?=$mSelect['notice_file_ori']?>
						</div>
<?	} ?>
					</td>
				</tr>

				<tr>
					<th><span>제목</span></th>
					<TD><INPUT maxLength=80 size=80 id='title' name='title' value="<?=$mSelect['title']?>"></TD>
				</tr>
				
				
				<tr>
					<th><span>내용</span></th>
					<TD><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name="content"><?=$mSelect['content']?></textarea></TD>
				</tr>
				
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01 lookbooktable" style='padding-bottom:20px'></div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center>
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:CheckForm('<?=$qType?>');"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
<?php
	} else {
?>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['no']?>' );"><img src="images/btn_edit2.gif" alt="수정하기"></a>
					<a href="javascript:CheckForm('2', '<?=$mSelect['no']?>' );"><img src="images/botteon_del.gif" alt="삭제하기"></a>
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
});

</script>
 

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
