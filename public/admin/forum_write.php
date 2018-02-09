
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
$PageCode = "co-8";
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
$mode = $_REQUEST["mode"];
$Type = "0";

// 이미지 경로
$imagepath = $Dir.DataDir."shopimages/forum/";

if($mode == "write" || $mode == "modify"){
	$imagefile = new FILE($imagepath);
	$v_up_imagefile	    = $_POST["v_up_imagefile"];
	$up_imagefile = $imagefile->upFiles();
	//$id = $_POST['id'];
	//$id = '관리자';
    $id = $_ShopInfo->getId();
	$summary = $_POST["summary"];
	$title	            = pg_escape_string($_POST["title"]);
	$content	        = pg_escape_string($_POST["content"]);
	$hash_tags          = pg_escape_string($_POST["hash_tags"]);
	$forum_code = $_POST['forum_code'];

    // 관리자가 수기로 작성자 및 날짜 변경 입력했을 경우..2016-10-24
    if($_POST[modi_id]) $id = trim($_POST[modi_id]);
    if($_POST[modi_date]) $modi_date = date("Y-m-d H:i:s", strtotime($_POST[modi_date]));
    else $modi_date = "";

	if($mode == "modify"){

		//새로 등록된 파일 있으면 기존 파일 삭제
		if( strlen( $up_imagefile["up_imagefile"][0]["v_file"] ) > 0 ){
			if( is_file( $imagepath.$v_up_imagefile[0] ) > 0 ){
				$imagefile->removeFile( $v_up_imagefile[0] );
			}
			$qry[] = " img = '{$up_imagefile['up_imagefile'][0]['v_file']}' " ;
		}
	}

	if($mode == "write"){
		$w_sql = " insert into tblforumlist (code, id, name, tag, summary, title, img, content, writetime) values ( ";
		$w_sql .= " '{$forum_code}', ";
		$w_sql .= " '{$id}', ";
		$w_sql .= " '{$name}',";
		$w_sql .= " '{$hash_tags}', ";
		$w_sql .= " '{$summary}', ";
		$w_sql .= " '{$title}', ";
		$w_sql .= " '{$up_imagefile['up_imagefile'][0]['v_file']}', ";
		$w_sql .= " '{$content}', ";
		//$w_sql .= " now() ";
        if($modi_date) {
            $w_sql .= " '{$modi_date}' ";
        } else {
            $w_sql .= " now() ";
        }
		$w_sql .= " ) ";
		$result = pmysql_query($w_sql);
		echo "<script>alert('등록되었습니다');location.href='forum_list.php';</script>";
		exit;
	}

}


if($mode == 'view'){
	$index = "5";
	$v_sql = " select * from tblforumlist where index = {$index} ";
	$v_result = pmysql_query($v_sql);
	$forum_view = pmysql_fetch_object($v_result);
	$code_a = substr($forum_view->code,0,3);
	$code_b = substr($forum_view->code,3,3);
	$code_c = substr($forum_view->code,6,3);
	$code_d = "000";
	debug($forum_view);
}
?>

<?
$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblforumcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
$i=0;
$ii=0;
$iii=0;

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
		} 
	}
	$strcodelist.= "clist=null;\n\n";
}
pmysql_free_result($result);
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";

$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";

$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";
// 스크립트 작성완료
?>

<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script> 
<script language="JavaScript">

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; 포럼관리 &gt; <span>포럼글<?=$qType_text?></span></p></div></div>
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
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name="forum_code">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">포럼 게시글 <?=$Type_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>포럼 게시글을 <?=$Type_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">게시글 내용</div>
				</td>
			</tr>
			
			<tr><td height=3></td></tr>

			<!-- 포럼 카테고리 선택하기-->
			<tr>
				<td>
					<div class="table_style01">
					<table cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<tr>
							<th><span>카테고리 선택</span> <font color='#FF0000' > *필수 </font> </th>
							<td colspan="3">
							<?php
								//카테고리 SELECT BOX를 불러온다
								echo $codeA_list;
								echo $codeB_list;
								echo $codeC_list;
								//카테고리 SELECT 버튼을 불러온다
								//echo $codeSelect;
								//카테고리 스크립트 실행
								echo $strcodelist;
								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
							?>
							</td>
						</tr>
						</table>
						</div>
				</td>
			</tr>
			<!-- //포럼 카테고리 선택하기-->

			<tr>
				<td>
				<div class="table_style01">					
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>

				<tr>
					<th><span>글쓴이ID</span></th>
					<TD><INPUT class="input" maxLength=50 size=50 id='modi_id' name='modi_id' value="<?=$_ShopInfo->getId()?>"></TD>
				</tr>

				<tr>
					<th><span>작성일</span></th>
					<TD><INPUT class="input" maxlength=14 id='modi_date' name='modi_date' value="<?=$forum_view->modi_date?>"> (수기변경시 20161021183947 과 같이 입력해주세요.)</TD>
				</tr>

				<tr>
					<th><span>말머리</span></th>
					<TD><INPUT class="input" maxLength=80 size=80 id='summary' name='summary' value="<?=$forum_view->summary?>"></TD>
				</tr>

				<tr>
					<th><span>제목</span></th>
					<TD><INPUT class="input" maxLength=80 size=80 id='title' name='title' value="<?=$forum_view->title?>"></TD>
				</tr>

				<tr>
					<th><span>썸네일이미지</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$forum_view->img?>" >
					<?	if( is_file($imagepath.$forum_view->img) ){ ?>
						<div style='margin-top:5px' >
							<img src='<?=$imagepath.$forum_view->img?>' style='max-height: 200px;' />
						</div>
					<?}?>
					</td>
				</tr>

				<tr>
					<th><span>내용</span></th>
					<TD><textarea wrap=off  id="ir1" style="WIDTH: 100%; HEIGHT: 300px" name="content"><?=$forum_view->content?></textarea></TD>
				</tr>
				
				<tr>
					<th><span>태그</span></th>
					<TD><INPUT class="input" maxLength=80 size=80 id='hash_tags' name='hash_tags' value="<?=$forum_view->tag?>"> (# 없이 ,(콤마)로 구분하여 작성하여 주십시오.) </TD>
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
				<?if( $Type == '0' ){?>
					<a href="#" id="forum_submit"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
				<?}else{?>
					<a href="#" id="forum_submit"><img src="images/btn_edit2.gif" alt="수정하기"></a>
				<?}?>
					<a href="#"><img src="img/btn/btn_list.gif" alt="목록보기"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span> <?=$Type_text?></span></dt>
							<dd>- <?=$Type_text?>할 수 있습니다.
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

function forum_submit()
{
	var sHTML = oEditors.getById["ir1"].getIR();
	var code_a = $("#code_a").val();
	var code_b = $("#code_b").val();
	var code_c = $("#code_c").val();
	if(!code_c){
		alert('3차 카테고리 까지 선택 하셔야 합니다, 3차카테고리가 없는경우 등록이 필요합니다');
		return false;
	}

    var modi_date = document.form1.modi_date.value;
    //alert(modi_date.length);
    //alert(modi_date);
    if(modi_date.length > 0 && modi_date.length < 14) {
        alert("수정날짜는 년4자리,월2자리, 일2자리, 시2자리, 분2자리, 초2자리로 총 14자리로 입력해주십시오.");
        return false;
    }
    document.form1.content.value=sHTML;
	document.form1.mode.value = "write";
	document.form1.forum_code.value = code_a + code_b + code_c;
	document.form1.submit();	
}

$(document).on("click","#forum_submit",forum_submit);
</script>

 <?=$onload?>

<?php 
include("copyright.php");
