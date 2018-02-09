<?php
/********************************************************************* 
// 파 일 명		: design_collection4.php
// 설     명		: 데코앤이 COLLECTION 페이지 STAGE4 디자인
// 상세설명	: 데코앤이 COLLECTION 페이지의 STAGE4 배너 및 링크 이미지 관리
// 작 성 자		: 2016.01.15 - 김재수
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
	$PageCode = "de-2";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
	$stage	= "4";
	#배너 기본 세팅
	$display['0'] = '비노출';
	$display['1'] = '노출';
	//exdebug($_POST);

/*
	$sql = "SELECT * FROM tblimgcollectionmain LIMIT 1";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_cdata=pmysql_fetch_object($result)) {		
		alert_go('STAGE1 영역을 먼저 적용해 주세요.',-1);
	} else {		
		$submit_type	= "update";
	}
	pmysql_free_result($result);
*/

	$type=$_POST["type"];
	$sel_cate=$_POST["sel_cate"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/collection/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($type=="insert" || $type=="update") {				// DB를 수정한다.

		$cno	= $_POST["cno"];
		$stage4_bg	= $_POST["stage4_bg"];
		$stage4_text	= $_POST["stage4_text"];
		$stage4_subtext	= $_POST["stage4_subtext"];
		$stage4_selcate1	= $_POST["stage4_selcate1"];
		$stage4_selcate2	= $_POST["stage4_selcate2"];
		$stage4_selcate3	= $_POST["stage4_selcate3"];
		$stage4_selcate4	= $_POST["stage4_selcate4"];
		$v_up_imagefile	= $_POST["v_up_imagefile"];

		$up_imagefile=$imagefile->upFiles();

		if($type=="insert") {
			$sql = "INSERT INTO tblimgcollectionmain(
			stage4_bg		,
			stage4_bg_m		,
			stage4_text		,
			stage4_subtext		,
			stage4_selcate1	,
			stage4_selcate2		,
			stage4_selcate3		,
			stage4_selcate4) VALUES (
			'".$up_imagefile["up_imagefile"][0]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][1]["v_file"]."', 
			'".$stage4_text."', 
			'".$stage4_subtext."', 
			'".$stage4_selcate1."', 
			'".$stage4_selcate2."', 
			'".$stage4_selcate3."', 
			'".$stage4_selcate4."')";
			
		}else if($type=="update") {

			$qry_where="";
			$qry_where[] = "stage4_text='".$stage4_text."'";
			$qry_where[] = "stage4_subtext='".$stage4_subtext."'";
			$qry_where[] = "stage4_selcate1='".$stage4_selcate1."'";
			$qry_where[] = "stage4_selcate2='".$stage4_selcate2."'";
			$qry_where[] = "stage4_selcate3='".$stage4_selcate3."'";
			$qry_where[] = "stage4_selcate4='".$stage4_selcate4."'";

			for($u=0;$u<2;$u++) {
				if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up_imagefile[$u] );
					}
					if ($u == 0) $qry_where[] = "stage4_bg='".$up_imagefile["up_imagefile"][0]["v_file"]."'";
					if ($u == 1) $qry_where[] = "stage4_bg_m='".$up_imagefile["up_imagefile"][1]["v_file"]."'";
				}
			}	

			$sql = "UPDATE tblimgcollectionmain SET ";
			$sql.= implode(", ",$qry_where);
			$sql.= " WHERE no='{$cno}' ";

			//exdebug($sql);
			//exit;

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
	if($type=="delete_pic" || $type=="modify_pic" || $type=="insert_pic") {				// DB를 수정한다.
		
		$cate = $_POST['cate'];
		$stage = $_POST['stage'];
		$cno = $_POST['cno'];
		$link = $_POST['link'];
		$sort = $_POST['sort'];
		$hidden = $_POST['hidden'];
		if( $hidden == '' || is_null( $hidden ) ){
			$hidden = '0';
		}
		$target = $_POST['target'];
		$v_up2_imagefile	= $_POST["v_up2_imagefile"];

		if($type=="delete_pic") {

			$img_no = $_POST['img_no'];
			
			for($u=0;$u<2;$u++) {
				if( strlen( $v_up2_imagefile[$u] ) > 0 && is_file( $imagepath.$v_up2_imagefile[$u] ) ){
					$imagefile->removeFile( $v_up2_imagefile[$u] );
				}
			}

			$sql = "DELETE FROM tblimgcollectionlist WHERE no='".$img_no."' ";
			
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
			} else {
				$error="삭제중 오류가 발생하였습니다.";
			}			

		} else if($type=="modify_pic") {

			$img_no = $_POST['img_no'];
			$up2_imagefile=$imagefile->upFiles();

			//exdebug($_POST);
			//exdebug($up2_imagefile);
			//exit;
			
			$qry_where="";

			for($u=0;$u<2;$u++) {
				if( strlen( $up2_imagefile["up2_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up2_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up2_imagefile[$u] );
					}
					if ($u == 0) $qry_where[] = "img='".$up2_imagefile["up2_imagefile"][0]["v_file"]."'";
					if ($u == 1) $qry_where[] = "img_m='".$up2_imagefile["up2_imagefile"][1]["v_file"]."'";
				}
			}	
		
			$qry_where[]="cate='".$cate."'";
			$qry_where[]="link='".$link."'";
			$qry_where[]="sort='".$sort."'";
			$qry_where[]="hidden='".$hidden."'";
			$qry_where[]="target='".$target."'";
			
			$sql="UPDATE tblimgcollectionlist SET ";
			$sql.=implode(", ",$qry_where);
			$sql.=" WHERE no='".$img_no."' ";	

			//exdebug($sql);
			//exit;
			
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('수정이 완료되었습니다.');parent.goList();\"></body></html>";exit;
			} else {
				$error="수정중 오류가 발생하였습니다.";
			}		
		} else if($type=="insert_pic") {

			$up2_imagefile=$imagefile->upFiles();

			$sql = "INSERT INTO tblimgcollectionlist(
			cno		,
			stage		,
			cate		,
			img		,
			img_m		,
			link	,
			sort	,
			hidden		,
			target	) VALUES (
			'".$cno."', 
			'".$stage."', 
			'".$cate."', 
			'".$up2_imagefile["up2_imagefile"][0]["v_file"]."', 
			'".$up2_imagefile["up2_imagefile"][1]["v_file"]."', 
			'".$link."', 
			'".$sort."', 
			'".$hidden."', 
			'".$target."')";
			
			if(pmysql_query($sql,get_db_conn())) {
				echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
			} else {
				$error="등록중 오류가 발생하였습니다.";
			}		
		}
	}

	include("header.php"); 
	
	//카테고리 불러온다.
	$cateSql = "SELECT * ";
	$cateSql.= "FROM tblimgcollectioncate ";
	$cateSql.= "WHERE cno= '".$_cdata->no."' ORDER BY no";
	$cateResult = pmysql_query($cateSql,get_db_conn());
	while($cateRow = pmysql_fetch_array($cateResult)){
		$thisCateRow[] = $cateRow;
	}
	pmysql_free_result( $cateSqlResult );

	
	# 타입별 불러오기
	if( !is_null($sel_cate) && $sel_cate!='' && strlen( $sel_cate ) > 0 ){
		$picQry = " AND b.no = '".$sel_cate."'";
	}
	
	# 사진 페이징
	$page_sql = "SELECT COUNT(*) FROM tblimgcollectionlist a left join tblimgcollectioncate b on a.cate=b.no WHERE a.stage='{$stage}' {$picQry}";
	$paging = new newPaging($page_sql, 10, 10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	# 사진 리스트 불러오기

	$picSql = "SELECT a.*, b.name FROM tblimgcollectionlist a left join tblimgcollectioncate b on a.cate=b.no WHERE a.stage='{$stage}' {$picQry}";
	$picSql.= "ORDER BY cate, sort ";
	$sql = $paging->getSql( $picSql );
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_array( $result ) ){
		$picList[] = $row;
	}
	pmysql_free_result( $result );

	if( $type == 'modify_picsel' ){
		$img_no = $_POST['img_no'];
		$bSelectSql = "SELECT * FROM tblimgcollectionlist WHERE no ='".trim($img_no)."' ";
		$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
		$bSelectRow = pmysql_fetch_array( $bSelectRes );
		$mSelect = $bSelectRow;
		pmysql_free_result( $bSelectRes );
		//수정
		$qType = '1';
	}

	# 등록 mode 
	if( is_null( $qType ) ){
		$qType = '0';
	}

	# banner target array
	$baerrTargetText = array(
		'_blank'=>'새창',
		'_self'=>'현재위치'
	);
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
    if ( $("#left_title").val().trim() === "" ) {
        alert("좌측 타이틀을 입력해 주세요.");
        $("#left_title").val("").focus();
        return false;
    }

    if ( $("#right_title").val().trim() === "" ) {
        alert("우측 타이틀을 입력해 주세요.");
        $("#right_title").val("").focus();
        return false;
    }

/*
	if(confirm("정보를 적용하시겠습니까?")) {
		document.form1.type.value="<?=$submit_type?>";
		document.form1.target="processFrame";
		document.form1.submit();
	}
*/
}
function CheckForm2(mode, num) {
	if( mode == '0' ){
		if( document.form2.cate.value == '' || document.form2.cate.value < 0 ){
			alert('카테고리를 선택해야 합니다.');
			return;
		}
		if( document.form2.sort.value == '' || document.form2.sort.value < 0 ){
			alert('노출순서를 입력해야 합니다.');
			return;
		}						
		if( confirm('등록하시겠습니까?') ){
			document.form2.type.value="insert_pic";
			document.form2.target="processFrame";
			document.form2.submit();
		} else {
			return;
		}
	} else if ( mode == '1' ) {
		if( document.form2.cate.value == '' || document.form2.cate.value < 0 ){
			alert('카테고리를 선택해야 합니다.');
			return;
		}
		if( document.form2.sort.value == '' || document.form2.sort.value < 0 ){
			alert('노출순서를 입력해야 합니다.');
			return;
		}						

		if( confirm('수정하시겠습니까?') ){
			document.form2.type.value="modify_pic";
			document.form2.target="processFrame";
			document.form2.submit();
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		document.form2.img_no.value=num;
		document.form2.type.value="modify_picsel";
		document.form2.submit();
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			document.form2.img_no.value=num;
			document.form2.type.value="delete_pic";
			document.form2.target="processFrame";
			document.form2.submit();
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}
}

function goList() {
	document.form2.type.value = "";
	document.form2.img_no.value = "";
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.type.value = "";
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}

function cateinfo(cno) {
	window.open("design_collection_cate.php?cno="+cno,"design_collection_cate","height=400,width=420,scrollbars=no,resizable=no");
}

function changeSelCate(){
	document.form2.type.value = "";
	document.form2.img_no.value = "";
	document.form2.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; 메인 배너관리 &gt;<span>STAGE4 영역관리</span></p></div></div>
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
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">STAGE4 영역관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>STAGE4 영역 정보를 변경 할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">STAGE4 영역정보</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<div class="table_style01">								
				<form id=form1 name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
				<input type=hidden name=type>
				<input type=hidden name=cno value="<?=$_cdata->no?>">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>좌측 타이틀</span></th>
					<TD><INPUT maxLength=80 size=80 id='left_title' name='left_title' value="<?=$_cdata->stage4_text?>"></TD>
				</tr>

                <? 
                    $totalCount = 1; 
                    for ( $i = 1; $i <= 2; $i++ ) { 
                ?>
				<tr>
					<th><span>썸네일 <?=$i?></span></th>
					<td class="td_con1" colspan="3" style="position:relative">
                        링크 : <input type="text" size="50" name="link<?=$totalCount?>" id="link<?=$totalCount?>" /> &nbsp; 
                        노출여부 : <input type="checkbox" name="hidden<?=$totalCount?>" id="hidden<?=$totalCount?>" /> &nbsp; 
						<input type=file name="up_imagefile[<?=$totalCount - 1?>]" style="WIDTH: 400px"><br>
						<!--input type=hidden name="v_up_imagefile[<?=$totalCount - 1?>]" value="<?=$_cdata->stage4_bg?>" -->
<?	/*if( is_file($imagepath.$_cdata->stage4_bg) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_cdata->stage4_bg?>' style='max-height: 200px;' />
						</div>
<?	}*/ ?>
					</td>
				</tr>

                <? 
                        $totalCount++;
                    } 
                ?>
				<tr>
					<th><span>우측 타이틀</span></th>
					<TD><INPUT maxLength=80 size=80 id='right_title' name='right_title' value="" ></TD>
				</tr>
                <?
                    for ( $i = 1; $i <= 5; $i++ ) { 
                ?>
				<tr>
					<th><span>썸네일 <?=$i?></span></th>
					<td class="td_con1" colspan="3" style="position:relative">
                        링크 : <input type="text" size="50" name="link<?=$totalCount?>" id="link<?=$totalCount?>" /> &nbsp; 
                        노출여부 : <input type="checkbox" name="hidden<?=$totalCount?>" id="hidden<?=$totalCount?>" /> &nbsp; 
						<input type=file name="up_imagefile[<?=$totalCount - 1?>]" style="WIDTH: 400px"><br>
						<!--input type=hidden name="v_up_imagefile[<?=$totalCount - 1?>]" value="<?=$_cdata->stage4_bg?>" -->
<?	/*if( is_file($imagepath.$_cdata->stage4_bg) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_cdata->stage4_bg?>' style='max-height: 200px;' />
						</div>
<?	}*/ ?>
					</td>
				</tr>

                <? 
                        $totalCount++;
                    } 
                ?>
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
			</form>

			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>STAGE4 영역관리</span></dt>
							<dd>- STAGE4 영역 정보를 변경 할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
