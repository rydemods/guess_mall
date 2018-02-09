<?php
/********************************************************************* 
// 파 일 명		: design_app_endpopup.php
// 설     명		: APP 종료 팝업 관리
// 상세설명	: APP 종료 팝업 관리
// 작 성 자		: 2016.03.30 - 김재수
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
	include_once($Dir."lib/adminlib.php");
	include("access.php");
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "de-9";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

	include("header.php");
	//exdebug($_POST);
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------

	$banner_type	= 'E'; // F : 전면 팝업, E : 종료 팝업

	$mode			= $_POST['mode'];
	$search_hidden      = $_REQUEST['search_hidden'];
	//배너 이미지
	$v_banner_img = $_POST['v_banner_img'];
	//배너 링크
	$banner_link=$_POST["banner_link"];
	# open 위치
	$banner_target = $_POST['banner_target'];
	//배너 노출
	$banner_hidden=$_POST["banner_hidden"];
	if( $banner_hidden == '' || is_null( $banner_hidden ) ){
		$banner_hidden = '0';
	}

	# 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/app/endpopup/";

	#배너 입력 수정 삭제
	// 이미지 파일
	$banner_file = new FILE($imagepath);

	if($mode=="delete") {
		# 배너 넘버
		$img_no = $_POST['img_no'];

		list($v_banner_img1, $v_banner_img2) = pmysql_fetch("SELECT img1, img2 FROM tblappbannerimg WHERE no ='".trim($img_no)."' ");

		if( strlen( $v_banner_img1 ) > 0 && is_file( $imagepath.$v_banner_img1 ) ){ $banner_file->removeFile( $v_banner_img1 ); }
		if( strlen( $v_banner_img2 ) > 0 && is_file( $imagepath.$v_banner_img2 ) ){ $banner_file->removeFile( $v_banner_img2 ); }
		
		$qry = "DELETE FROM tblappbannerimg WHERE no='".$img_no."' ";
		pmysql_query( $qry, get_db_conn() );

		if( !pmysql_error() ){
			alert_go('삭제가 완료되었습니다.', $_SERVER['PHP_SELF'] . "?search_hidden=" . $search_hidden);
		}else{	
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		} 
		$qry = '';

	} else if($mode=="modify") {
		# 배너 넘버
		$img_no = $_POST['img_no'];
		$banner_img=$banner_file->upFiles();
		$where="";

		for($u=0;$u<2;$u++) {
			if( strlen( $banner_img["banner_img"][$u]["v_file"] ) > 0 ){
				if( is_file( $imagepath.$v_banner_img[$u] ) > 0 ){
					$banner_file->removeFile( $v_banner_img[$u] );
				}
				if ($u == 0) $where[] = "img1='".$banner_img["banner_img"][0]["v_file"]."'";
				if ($u == 1) $where[] = "img2='".$banner_img["banner_img"][1]["v_file"]."'";
			}
		}	
		
		$where[]="link='".$banner_link."'";
		$where[]="target='".$banner_target."'";
		$where[]="hidden='".$banner_hidden."'";
		
		$qry="UPDATE tblappbannerimg SET ";
		$qry.=implode(", ",$where);
		$qry.=" WHERE no='".$img_no."' ";	
		
		pmysql_query($qry,get_db_conn());
		if(!pmysql_error()){		
			alert_go('수정이 완료되었습니다.', $_SERVER['PHP_SELF'] . "?search_hidden=" . $search_hidden);
		}else{	
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		} 
		$qry = '';

	} else if( $mode == 'insert' ){
		$banner_img=$banner_file->upFiles();

		$qry="insert into tblappbannerimg (
			type, 
			img1, 
			img2, 
			link, 
			target,
			hidden,
			regdt
			)values(
			'".$banner_type."',
			'".$banner_img["banner_img"][0]["v_file"]."',
			'".$banner_img["banner_img"][1]["v_file"]."',
			'".$banner_link."',
			'".$banner_target."',
			'".$banner_hidden."',
			'now()'
		) ";
			
		pmysql_query($qry,get_db_conn());
		if(!pmysql_error()){		
			alert_go('등록이 완료되었습니다.', $_SERVER['PHP_SELF'] . "?search_hidden=" . $search_hidden);
		}else{
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		}
		$qry = '';
		pmysql_free_result($result);
	}


	#배너 기본 세팅
	$display['0'] = '비노출';
	$display['1'] = '노출';

	# banner target array
	$bannerTargetText = array(
		'self'=>'App내 이동',
		'blank'=>'새창이동'
	);

	$bannerQry = " AND type='{$banner_type}' ";

	# 메뉴타입별 불러오기

	if( !is_null($search_hidden) && $search_hidden != "" ) {
		$bannerQry .= " AND hidden = {$search_hidden} ";
	}

	# 배너 페이징
	$page_sql = "SELECT COUNT(*) FROM tblappbannerimg WHERE 1=1 {$bannerQry} ";
	//echo $page_sql;
	$paging = new newPaging($page_sql, 10, 10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	# 배너 리스트 불러오기

	$bannerSql = "SELECT * FROM tblappbannerimg WHERE 1=1 ";
	$bannerSql.= $bannerQry;
	$bannerSql.= "ORDER BY no desc ";
	$sql = $paging->getSql( $bannerSql );
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_array( $result ) ){
		$bannerList[] = $row;
	}

	# 수정할 배너 불러오기
	if( $mode == 'modfiy_select' ){
		$img_no = $_POST['img_no'];
		$bSelectSql = "SELECT * FROM tblappbannerimg WHERE no ='".trim($img_no)."' ";
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

	if ($qType == '0') $mode_Text	= "등록";
	if ($qType == '1') $mode_Text	= "수정";

?>
<script type="text/javascript" src="lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">

function GoPage(block,gotopage) {
	document.insertForm.mode.value = "";
	document.insertForm.block.value = block;
	document.insertForm.gotopage.value = gotopage;
	document.insertForm.submit();
}

// 수정 / 삭제 
function changeAction( mode , num ){
	//mode 0 -> insert, 1 -> modify, 2 -> modfiy_select, 3 -> delete
	
	if( mode == '0' ){
		var img_cnt	= 0;
		if ($("input[name='banner_img[0]']").val() == '') img_cnt++;
		if (img_cnt > 0)
		{
			alert('이미지를 하나이상 등록해야 합니다.');
			return;
		}

		if( confirm('등록하시겠습니까?') ){
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
		$('#img_no').val( num );
		$("#mode").val( 'modfiy_select' );
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			$('#img_no').val( num );
			$("#mode").val( 'delete' );
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}

	if( checkForm() ){
		$("#insertForm").submit();
	}
	
}

// submit 하기전 값을 체크한다
function checkForm( mode ){
	var returnVal = true;
	return returnVal;
}


function goList() {
	$('#img_no').val('');
	$("#insertForm").submit();
}
</SCRIPT>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; App 이미지 관리 &gt;<span>종료 팝업 관리</span></p></div></div>

<form name='insertForm' id='insertForm' method='POST' enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<input type='hidden' name='mode' id='mode' value='' >
		<input type='hidden' name='img_no' id='img_no' value='<?=$mSelect['no']?>' >
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">		
        <input type="hidden" name="search_hidden" value="<?=$search_hidden?>" />
        <input type="hidden" name="banner_sort" value="0" />
        <input type="hidden" name="banner_cookietime" value="0" />

		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">종료 팝업 관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>종료 팝업 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배너 <?=$mode_Text?></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					
						<col width=140></col>
						<col width=></col>
						<tr>
							<th><span>이미지1</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="banner_img[0]" style="WIDTH: 400px"><br>
								<span class="font_orange">(권장이미지 : 720px X 1280px)</span>
								<input type=hidden name="v_banner_img[0]" value="<?=$mSelect['img1']?>" >
								<div style='margin-top:5px' >
<?	if( is_file($imagepath.$mSelect['img1']) ){ ?>
									<img src='<?=$imagepath.$mSelect['img1']?>' style='max-width: 125px;' />
<?	} ?>
								</div>
							</td>
						</tr>
						<tr>
							<th><span>이미지2</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="banner_img[1]" style="WIDTH: 400px"><br>
								<span class="font_orange">(권장이미지 : 1600px X 2560px)</span>
								<input type=hidden name="v_banner_img[1]" value="<?=$mSelect['img2']?>" >
								<div style='margin-top:5px' >
<?	if( is_file($imagepath.$mSelect['img2']) ){ ?>
									<img src='<?=$imagepath.$mSelect['img2']?>' style='max-width: 125px;' />
<?	} ?>
								</div>
							</td>
						</tr>
						<tr>
							<th><span>팝업링크</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_link' name='banner_link' value="<?=$mSelect['link']?>" >
									<input type='radio' name='banner_target' value='self' <? if( $mSelect['target'] == '' || $mSelect['target'] == 'self' ) { echo 'CHECKED'; } ?> >App내 이동</option>
									<input type='radio' name='banner_target' value='blank' <? if( $mSelect['target'] == 'blank' ) { echo 'CHECKED'; } ?> >새창이동</option></TD>
						</tr>
						<tr>
							<th><span>노출여부</span></th>
							<TD><INPUT type='checkbox' id='banner_hidden' name='banner_hidden' value="1" <? if( $mSelect['hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center">
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:changeAction('<?=$qType?>', '<?=$mSelect['no']?>' );">
						<img src="images/btn_confirm_com.gif">	
					</a>
<?php
	} else {
?>
					<a href="javascript:javascript:changeAction('<?=$qType?>', '<?=$mSelect['no']?>' );">
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
			<tr>
				<td>
                    <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <col width="10%"></col>
                    <col width=></col>
                    <tr>
                        <td>					
                            <!-- 소제목 -->
        					<div class="title_depth3_sub">검색된 목록</div>
                        </td>
                        <td align="right">
                            <div style="margin:20px 0 5px; align: left;">
                            사용 : 
                            <select name="search_hidden" onChange="javascript:changeSelectHidden(this);">
                                <option value=""  <?php if ($search_hidden == "") echo "selected"; ?>>========전체=======</option>
                                <option value="1" <?php if ($search_hidden == "1") echo "selected"; ?>>노출</option>
                                <option value="0" <?php if ($search_hidden == "0") echo "selected"; ?>>비노출</option>
                            </select>

                            </div>
                        </td>
                    </tr>
                    </table>
				</td>
			</tr>
			
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width='50'>
					<col width='150'>
					<col width='150'>
					<col width='*'>
					<col width='80'>
					<col width='60'>
					<col width='60'>
					<col width='60'>
				</colgroup>
				<TR>
					<th>번호</th>
					<th>이미지1</th>
					<th>이미지2</th>
					<th>링크</th>
					<th>위치</th>
					<th>사용</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $bannerList ) > 0 ) {
		$cnt=0;
		foreach( $bannerList as $bCnt=>$bVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
					<!-- 번호 -->
					<td><?=$number?></td>
					<!-- 이미지1 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file($imagepath.$bVal['img1']) ){
?>
							<img src='<?=$imagepath.$bVal['img1']?>' style='max-width : 70px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<!-- 이미지2 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file($imagepath.$bVal['img2']) ){
?>
							<img src='<?=$imagepath.$bVal['img2']?>' style='max-width : 70px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<td>
						<?=$bVal['link']?>
					</td>
					<!-- 위치 -->
					<td>
						<?=$bannerTargetText[$bVal['target']]?>
					</td>
					<!-- 노출 / 비노출 -->
					<td>
						<?=$display[$bVal['hidden']]?>
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
					<td colspan='8' > 목록이 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $bannerList ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>
			
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>  정보</span></dt>
							<dd>
							- <b>번호</b> : <span style="letter-spacing:-0.5pt;">.</span><br>
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
</form>

<script type="text/javascript">
    function changeSelectHidden(obj) {
        var hiddenVal = $(obj).children("option:selected").val();
        document.insertForm.search_hidden.value = hiddenVal;
        document.insertForm.submit();
    }
</script>

<?=$onload?>
<?php
include("copyright.php");
?>