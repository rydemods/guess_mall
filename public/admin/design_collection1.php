<?php
/********************************************************************* 
// 파 일 명		: design_collection1.php
// 설     명		: 데코앤이 COLLECTION 페이지 STAGE1 디자인
// 상세설명	: 데코앤이 COLLECTION 페이지의 STAGE1 배너 및 링크 이미지 관리
// 작 성 자		: 2016.01.14 - 김재수
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
	$PageCode = "de-6";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$sql = "SELECT * FROM tblimgcollectionmain LIMIT 1";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_cdata=pmysql_fetch_object($result)) {
		$submit_type	= "insert";
	} else {		
		$submit_type	= "update";
	}
	pmysql_free_result($result);

	$type=$_POST["type"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/collection/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($type=="insert" || $type=="update") {				// DB를 수정한다.

		$cno	= $_POST["cno"];
		$v_up_imagefile	= $_POST["v_up_imagefile"];

		$up_imagefile=$imagefile->upFiles();

		if($type=="insert") {
			$sql = "INSERT INTO tblimgcollectionmain(
			stage1_img		,
			stage1_img_m) VALUES (
			'".$up_imagefile["up_imagefile"][0]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][1]["v_file"]."')";
			
		}else if($type=="update") {

			$img_where="";

			for($u=0;$u<2;$u++) {
				if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up_imagefile[$u] );
					}
					if ($u == 0) $img_where[] = "stage1_img='".$up_imagefile["up_imagefile"][0]["v_file"]."'";
					if ($u == 1) $img_where[] = "stage1_img_m='".$up_imagefile["up_imagefile"][1]["v_file"]."'";
				}
			}	
			if ($img_where) {
				$sql = "UPDATE tblimgcollectionmain SET ";
				$sql.= implode(", ",$img_where);
				$sql.= "WHERE no='{$cno}' ";
			}
		}

		if ($sql) {
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('적용이 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
			} else {
				$error="적용중 오류가 발생하였습니다.";
			}			
		} else {
			echo "<html></head><body onload=\"alert('적용이 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
		}
	}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("정보를 적용하시겠습니까?")) {
		document.form1.type.value="<?=$submit_type?>";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; 메인 배너관리 &gt;<span>STAGE1 영역관리</span></p></div></div>
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
			<input type=hidden name=type>
			<input type=hidden name=cno value="<?=$_cdata->no?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">STAGE1 영역관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>STAGE1 영역 정보를 변경 할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">STAGE1 영역정보</div>
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
					<th><span>이미지(PC)</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$_cdata->stage1_img?>" >
<?	if( is_file($imagepath.$_cdata->stage1_img) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_cdata->stage1_img?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>이미지(MOBILE)</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[1]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[1]" value="<?=$_cdata->stage1_img_m?>" >
<?	if( is_file($imagepath.$_cdata->stage1_img_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_cdata->stage1_img_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center>
					<a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>STAGE1 영역관리</span></dt>
							<dd>- STAGE1 영역 정보를 변경 할 수 있습니다.
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
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
