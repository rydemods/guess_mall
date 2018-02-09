<?php
/********************************************************************* 
// 파 일 명		: design_collection5.php
// 설     명		: 데코앤이 COLLECTION 페이지 STAGE5 디자인
// 상세설명	: 데코앤이 COLLECTION 페이지의 STAGE5 배너 및 링크 이미지 관리
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
	
	$stage	= "5";
	#배너 기본 세팅
	$display['0'] = '비노출';
	$display['1'] = '노출';
	//exdebug($_POST);

	$sql = "SELECT * FROM tblimgcollectionmain LIMIT 1";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_cdata=pmysql_fetch_object($result)) {		
		alert_go('STAGE1 영역을 먼저 적용해 주세요.',-1);
	}
	pmysql_free_result($result);

	$type           = $_POST["type"];
    $search_hidden  = $_POST['search_hidden'];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/collection/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($type=="delete_pic" || $type=="modify_pic" || $type=="insert_pic") {				// DB를 수정한다.
		
		$stage = $_POST['stage'];
		$cno = $_POST['cno'];
		$brname = pg_escape_string($_POST['brname']);
		$bridx = $_POST['bridx'];
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
		
			$qry_where[]="brname='".$brname."'";
			$qry_where[]="bridx='".$bridx."'";
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
			img		,
			img_m		,
			brname	,
			bridx	,
			sort	,
			hidden		,
			target	) VALUES (
			'".$cno."', 
			'".$stage."', 
			'".$up2_imagefile["up2_imagefile"][0]["v_file"]."', 
			'".$up2_imagefile["up2_imagefile"][1]["v_file"]."', 
			'".$brname."', 
			'".$bridx."', 
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

    $idx            = $_POST['idx'];
    $visible_mode   = $_POST['visible_mode'];

    if ( count($idx) >= 1 ) {
        $whereIdx = implode(",", $idx);
        $sql  = "UPDATE tblimgcollectionlist SET hidden = {$visible_mode} WHERE no in ( " . $whereIdx . " ) ";
        pmysql_query($sql, get_db_conn());
    }

    $whereQry = "";
    if( !is_null($search_hidden) && $search_hidden != "" ) {
        $whereQry .= " AND hidden = ${search_hidden} ";
    }
	
	# 사진 페이징
	$page_sql = "SELECT COUNT(*) FROM tblimgcollectionlist a left join tblproductbrand b on a.bridx=b.bridx WHERE a.stage='{$stage}' {$whereQry}";
	$paging = new newPaging($page_sql, 10, 10);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	# 사진 리스트 불러오기

	$picSql = "SELECT a.*, b,brandname FROM tblimgcollectionlist a left join tblproductbrand b on a.bridx=b.bridx WHERE a.stage='{$stage}' {$whereQry}";
	$picSql.= "ORDER BY sort ";
	$sql = $paging->getSql( $picSql );
	$result = pmysql_query( $sql, get_db_conn() );
	while( $row = pmysql_fetch_array( $result ) ){
		$picList[] = $row;
	}
	pmysql_free_result( $result );

	if( $type == 'modify_picsel' ){
		$img_no = $_POST['img_no'];
		$bSelectSql = "SELECT a,*, b,brandname FROM tblimgcollectionlist a left join tblproductbrand b on a.bridx=b.bridx WHERE a.no ='".trim($img_no)."' ";
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

function BrandSelect() {
	window.open("design_collection_brand.php","brandselect","height=400,width=420,scrollbars=no,resizable=no");
}

function CheckForm2(mode, num) {
	if( mode == '0' ){
		if( confirm('등록하시겠습니까?') ){
			if( document.form2.sort.value == '' || document.form2.sort.value < 0 ){
				alert('노출순서를 입력해야 합니다.');
				return;
			}			
			document.form2.type.value="insert_pic";
			document.form2.target="processFrame";
			document.form2.submit();
		} else {
			return;
		}
	} else if ( mode == '1' ) {

		if( confirm('수정하시겠습니까?') ){
			if( document.form2.sort.value == '' ||  document.form2.sort.value < 0 ){
				alert('노출순서를 입력해야 합니다.');
				return;
			}
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
	location.href='design_collection5.php';
}

function GoPage(block,gotopage) {
	document.form2.type.value = "";
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; 메인 배너관리 &gt;<span>STAGE5 영역관리</span></p></div></div>
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
					<div class="title_depth3">STAGE5 영역관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>STAGE5 영역 정보를 변경 할 수 있습니다.</span></div>
				</td>
            </tr>			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">STAGE5 사진정보</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<div class="table_style01">								
				<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
				<input type=hidden name=type>
				<input type=hidden name=cno value="<?=$_cdata->no?>">
				<input type=hidden name=stage value="<?=$stage?>">
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type=hidden name=img_no value='<?=$mSelect['no']?>' >
				<input type=hidden name=bridx value='<?=$mSelect['bridx']?>' >
				<input type=hidden name=search_hidden value='<?=$search_hidden?>' >
                <input type=hidden name='visible_mode' value='<?=$visible_mode?>'>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>이미지(PC)</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up2_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up2_imagefile[0]" value="<?=$mSelect['img']?>" >
<?	if( is_file($imagepath.$mSelect['img']) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$mSelect['img']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>이미지(MOBILE)</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up2_imagefile[1]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up2_imagefile[1]" value="<?=$mSelect['img_m']?>" >
<?	if( is_file($imagepath.$mSelect['img_m']) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$mSelect['img_m']?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>브랜드명</span></th>
					<TD><INPUT maxLength=80 size=80 id='brname' name='brname' value="<?=$mSelect['brname']?>" ></TD>
				</tr>
				<tr>
					<th><span>브랜드선택</span></th>
					<TD><INPUT maxLength=50 size=50 id='brandname' name='brandname' value="<?=$mSelect['brandname']?>" readonly><a href="javascript:BrandSelect();"><img src="images/btn_select.gif" border="0" hspace="5" align="absmiddle"></a></TD>
				</tr>
				<tr>
					<th><span>위치</span></th>
					<td>
						<select name='target' >
							<option value='_blank' <? if( $mSelect['target'] == '_blank' ) { echo 'SELECTED'; } ?> > 새창 </option>
							<option value='_self' <? if( $mSelect['target'] == '_self' ) { echo 'SELECTED'; } ?> >현재위치</option>
						</select>
					</td>
				</tr>
				<tr>
					<th><span>노출순서</span></th>
					<TD><INPUT maxLength=10 size=10 id='sort' name='sort' value="<?=$mSelect['sort']?>" ></TD>
				</tr>
				<tr>
					<th><span>노출</span></th>
					<TD><INPUT type='checkbox' id='hidden' name='hidden' value="1" <? if( $mSelect['hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center>
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:CheckForm2('<?=$qType?>', '<?=$mSelect['no']?>' );">
						<img src="images/btn_confirm_com.gif">	
					</a>
<?php
	} else {
?>
					<a href="javascript:CheckForm2('<?=$qType?>', '<?=$mSelect['no']?>' );">
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
        					<div class="title_depth3_sub">STAGE5 사진목록</div>
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
					<col width='50'>
					<col width='200'>
					<col width='200'>
					<col width='*'>
					<col width='250'>
					<col width='55'>
					<col width='60'>
					<col width='60'>
					<col width='60'>
					<col width='60'>
				</colgroup>
				<TR>
					<th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
					<th>번호</th>
					<th>이미지(PC)</th>
					<th>이미지(MOBILE)</th>
					<th>브랜드명</th>
					<th>브랜드</th>
					<th>위치</th>
					<th>노출순서</th>
					<th>사용</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $picList ) > 0 ) {
		$cnt=0;
		foreach( $picList as $bCnt=>$bVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
                    <td align=center><input type='checkbox' name='idx[]' value="<?=$bVal['no']?>" /></td>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
					<!-- 이미지 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file($imagepath.$bVal['img']) ){
?>
							<img src='<?=$imagepath.$bVal['img']?>' style='max-height : 100px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<!-- 이미지 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file($imagepath.$bVal['img_m']) ){
?>
							<img src='<?=$imagepath.$bVal['img_m']?>' style='max-height : 100px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<!-- 브랜드명 -->
					<td>
						<? if ($bVal['brname']) echo $bVal['brname'];?>
					</td>
					<!-- 브랜드명 -->
					<td>
						<? if ($bVal['brandname']) echo $bVal['brandname'];?>
					</td>
					<!-- 위치 -->
					<td>
						<?=$baerrTargetText[$bVal['target']]?>
					</td>
					<!-- 노출 순서 -->
					<td>
						<?=$bVal['sort']?>
					</td>
					<!-- 노출 / 비노출 -->
					<td>
						<?=$display[$bVal['hidden']]?>
					</td>
					<!-- 수정 -->
					<td>
						<a href="javascript:CheckForm2( '2' ,'<?=$bVal["no"]?>' );"><img src="images/btn_edit.gif"></a>
					</td>
					<!-- 삭제 -->
					<td>
						<a href="javascript:CheckForm2('3', '<?=$bVal["no"]?>' );"><img src="images/btn_del.gif"></a>
					</td>
				</TR>
<?php
			$cnt++;
		}
	} else {
?>
				<TR>
					<td colspan='10' > 사진이 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $picList ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>
			</form>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
                <td colspan="8" align="center">
                    <a href="javascript:changeVisible('1');"><img src="images/btn_visible_set.png" border="0"></a>
                    <a href="javascript:changeVisible('0');"><img src="images/btn_visible_unset.png" border="0"></a>
                </td>
			</tr>
			<tr>
				<td height="30">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>STAGE5 영역관리</span></dt>
							<dd>- STAGE5 영역 정보를 변경 할 수 있습니다.
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
<script type="text/javascript">
    function changeSelectHidden(obj) {
        var hiddenVal = $(obj).children("option:selected").val();
        document.form2.search_hidden.value = hiddenVal;
        document.form2.submit();
    }

    function allCheck(obj) {
        if ( $(obj).is(":checked") ) {
            $("input:checkbox[name='idx[]']").attr("checked", true);
        } else {
            $("input:checkbox[name='idx[]']").attr("checked", false);
        }
    }

    function changeVisible(val) {
        // val : 1 => 노출, 0 => 비노출

        if ( $("input[name='idx[]']:checked").length == 0 ) {
            alert('하나 이상을 선택해 주세요.');
        } else {
            if ( val == "1" ) {
                msg = "노출 설정 하시겠습니까?";
            } else {
                msg = "비노출 설정 하시겠습니까?";
            }

            if ( confirm(msg) ) {
                document.form2.visible_mode.value = val;
                document.form2.submit();
            }
        }
    }
</script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
