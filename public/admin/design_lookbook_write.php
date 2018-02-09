<?php
/********************************************************************* 
// 파 일 명		: design_lookbook_write.php
// 설     명		: LOOKBOOK 생성, 수정, 삭제
// 상세설명	: LOOKBOOK 생성, 수정, 삭제
// 작 성 자		: 2016.01.22 - 김재수
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

	$mode=$_POST["mode"];
	if(!$mode) $mode=$_GET["mode"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/lookbook/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($mode=="delete") {
		
		$lbno	= $_POST["lbno"];
		$v_up_imagefile	= $_POST["v_up_imagefile"];

		$lbConSelectSql = "SELECT * FROM tbllookbook_content WHERE lbno ='".trim($lbno)."'";
		$lbConSelectRes = pmysql_query( $lbConSelectSql, get_db_conn() );
		while($lbConSelectRow = pmysql_fetch_array( $lbConSelectRes )){
			if( strlen( $lbConSelectRow['img'] ) > 0 && is_file( $imagepath.$lbConSelectRow['img'] ) ){
				$imagefile->removeFile(  $lbConSelectRow['img'] );
				//exdebug($lbConSelectRow['img']);
			}
			if( strlen( $lbConSelectRow['img_m'] ) > 0 && is_file( $imagepath.$lbConSelectRow['img_m'] ) ){
				$imagefile->removeFile( $lbConSelectRow['img_m'] );
				//exdebug($lbConSelectRow['img_m']);
			}				
			$lbCon_del = "DELETE FROM tbllookbook_content WHERE no = '".$lbConSelectRow['no']."' ";
			pmysql_query($lbCon_del,get_db_conn());
			//exdebug($lbCon_del);
		}
		pmysql_free_result( $lbConSelectRes );


		for($u=0;$u<2;$u++) {
			if( strlen( $v_up_imagefile[$u] ) > 0 && is_file( $imagepath.$v_up_imagefile[$u] ) ){
				$imagefile->removeFile( $v_up_imagefile[$u] );
			}
		}

		$qry = "DELETE FROM tbllookbook WHERE no ='".trim($lbno)."'";
		pmysql_query( $qry, get_db_conn() );
		echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.goBackList();\"></body></html>";exit;


	} else if($mode=="insert" || $mode=="modify") {				// DB를 수정한다.

		
		$lbno	= $_POST["lbno"];
		$title	= pg_escape_string($_POST["title"]);
		$subtitle	= pg_escape_string($_POST["subtitle"]);
		$hidden	= $_POST["hidden"];
		if (!$hidden) $hidden = 0;
		$v_up_imagefile	= $_POST["v_up_imagefile"];
		
		$sort	= $_POST["sort"];
		$lbcno	= $_POST["lbcno"];
		$v_up_imagefile2	= $_POST["v_up_imagefile2"];
		$places	= $_POST["places"];
		$productcodes	= $_POST["productcodes"];

		$up_imagefile=$imagefile->upFiles();

		//exdebug((count($_POST[productcodes])/5));
		//exdebug($_POST);
		//exit;

		foreach($sort as $key=>$value){ 
			$s_sort[] = $value;
		}

		foreach($lbcno as $key=>$value){ 
			$s_lbcno[] = $value;
		}

		$s_cnt	= 0;
		foreach($v_up_imagefile2 as $key=>$value){ 
			if (($key%2) == 0 && $key != 0) $s_cnt++;
			$s_v_up_imagefile2[$s_cnt][] = $value;
			$s_up_imagefile2[$s_cnt][] = $up_imagefile["up_imagefile2"][$key]["v_file"];
			//exdebug($up_imagefile["up_imagefile2"][$key]["v_file"]);
		}

		$s_cnt	= 0;
		foreach($places as $key=>$value){ 
			if (($key%5) == 0 && $key != 0) $s_cnt++;
			$s_places[$s_cnt][] = $value;
		}

		$s_cnt	= 0;
		foreach($productcodes as $key=>$value){ 
			if (($key%5) == 0 && $key != 0) $s_cnt++;
			$s_productcodes[$s_cnt][] = $value;
		}

		//exdebug($places);
		//exdebug($s_lbcno);
		//exit;
		//exdebug($productcodes);

		//exdebug(count($s_productcodes));
		for($j=0;$j<count($s_productcodes);$j++) {
			$s_productcodes[$j] = implode("|",$s_productcodes[$j]);
		}
		for($j=0;$j<count($s_places);$j++) {
			$s_places[$j] = implode("|",$s_places[$j]);
		}
		//exdebug($sort);
		//exit;

		$regdate = date("YmdHis");

		if($mode=="insert") {
			$sql = "INSERT INTO tbllookbook(
			title		,
			subtitle		,
			img	,
			img_m	,
			hidden		,
			regdate) VALUES (
			'{$title}', 
			'{$subtitle}', 
			'".$up_imagefile["up_imagefile"][0]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][1]["v_file"]."', 
			'{$hidden}', 
			'{$regdate}') RETURNING no";
			$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
			$lbno = $row2[0];
			if($lbno>0) {
				for($j=0;$j<count($s_sort);$j++) {
					$sql = "INSERT INTO tbllookbook_content(
					lbno		,
					img	,
					img_m	,
					places		,
					places_m		,
					productcodes		,
					sort) VALUES (
					'{$lbno}', 
					'".$s_up_imagefile2[$j][0]."', 
					'".$s_up_imagefile2[$j][1]."', 
					'".$s_places[$j]."', 
					'',
					'".$s_productcodes[$j]."', 
					'".$s_sort[$j]."')";		
					pmysql_query($sql,get_db_conn());		
					//exdebug($sql);
				}
					//exit;
			}
			echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackView('".$lbno."');\"></body></html>";exit;

		}else if($mode=="modify") {

			$img_where="";
			$img_where[] = "title='{$title}' ";
			$img_where[] = "subtitle='{$subtitle}' ";
			$img_where[] = "hidden='{$hidden}' ";

			for($u=0;$u<2;$u++) {
				if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up_imagefile[$u] );
					}
					if ($u == 0) $img_where[] = "img='".$up_imagefile["up_imagefile"][0]["v_file"]."'";
					if ($u == 1) $img_where[] = "img_m='".$up_imagefile["up_imagefile"][1]["v_file"]."'";
				}
			}				

			$sql = "UPDATE tbllookbook SET ";
			$sql.= implode(", ",$img_where);
			$sql.= "WHERE no='{$lbno}' ";	
			//exdebug($sql);

			pmysql_query($sql,get_db_conn());

			$sel_lbcno	= "'".implode("','",$s_lbcno)."'";
			$sel_lbcno	= str_replace(",''","",$sel_lbcno);
			$lbConSelectSql = "SELECT * FROM tbllookbook_content WHERE lbno ='".trim($lbno)."' AND no NOT IN (".$sel_lbcno.")";
			$lbConSelectRes = pmysql_query( $lbConSelectSql, get_db_conn() );
			while($lbConSelectRow = pmysql_fetch_array( $lbConSelectRes )){
				if( strlen( $lbConSelectRow['img'] ) > 0 && is_file( $imagepath.$lbConSelectRow['img'] ) ){
					$imagefile->removeFile(  $lbConSelectRow['img'] );
					//exdebug($lbConSelectRow['img']);
				}
				if( strlen( $lbConSelectRow['img_m'] ) > 0 && is_file( $imagepath.$lbConSelectRow['img_m'] ) ){
					$imagefile->removeFile( $lbConSelectRow['img_m'] );
					//exdebug($lbConSelectRow['img_m']);
				}				
				$lbCon_del = "DELETE FROM tbllookbook_content WHERE no = '".$lbConSelectRow['no']."' ";
				pmysql_query($lbCon_del,get_db_conn());
				//exdebug($lbCon_del);
			}
			pmysql_free_result( $lbConSelectRes );
			//exit;
			
			for($j=0;$j<count($s_sort);$j++) {
				if ($s_lbcno[$j]) {		
					$qry_where="";
					$qry_where[] = "places='".$s_places[$j]."' ";
					//$qry_where[] = "productcodes='".$s_productcodes[$j]."' ";
					$qry_where[] = "sort='".$s_sort[$j]."' ";

					for($k=0;$k<2;$k++) {
						//exdebug($s_up_imagefile2[$j][$k]);
						if( strlen( $s_up_imagefile2[$j][$k] ) > 0 ){
							if( is_file( $imagepath.$s_v_up_imagefile2[$j][$k] ) > 0 ){
								$imagefile->removeFile( $s_v_up_imagefile2[$j][$k] );
							}
							if ($k == 0) $qry_where[] = "img='".$s_up_imagefile2[$j][$k]."'";
							if ($k == 1) $qry_where[] = "img_m='".$s_up_imagefile2[$j][$k]."'";
						}
					}				

					$sql = "UPDATE tbllookbook_content SET ";
					$sql.= implode(", ",$qry_where);
					$sql.= "WHERE no='".$s_lbcno[$j]."' ";
				} else {
					/*
					$sql = "INSERT INTO tbllookbook_content(
					lbno		,
					img	,
					img_m	,
					places		,
					places_m		,
					productcodes		,
					sort) VALUES (
					'{$lbno}', 
					'".$s_up_imagefile2[$j][0]."', 
					'".$s_up_imagefile2[$j][1]."', 
					'".$s_places[$j]."', 
					'',
					'".$s_productcodes[$j]."', 
					'".$s_sort[$j]."')";		*/
					$sql = "INSERT INTO tbllookbook_content(
					lbno		,
					img	,
					img_m	,
					places		,
					places_m		,
					sort) VALUES (
					'{$lbno}', 
					'".$s_up_imagefile2[$j][0]."', 
					'".$s_up_imagefile2[$j][1]."', 
					'".$s_places[$j]."', 
					'',
					'".$s_sort[$j]."')";		
				}
				pmysql_query($sql,get_db_conn());		
				//exdebug($sql);
			}
			//exit;
			
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
	$lbSelectSql = "SELECT * FROM tbllookbook WHERE no ='".trim($lbno)."' ";
	$lbSelectRes = pmysql_query( $lbSelectSql, get_db_conn() );
	$lbSelectRow = pmysql_fetch_array( $lbSelectRes );
	$mSelect = $lbSelectRow;
	pmysql_free_result( $lbSelectRes );

	$lbConSelectSql = "SELECT * FROM tbllookbook_content WHERE lbno ='".trim($lbno)."' order by sort asc";
	$lbConSelectRes = pmysql_query( $lbConSelectSql, get_db_conn() );
	while($lbConSelectRow = pmysql_fetch_array( $lbConSelectRes )){
		$mConSelect[] = $lbConSelectRow;
		/*$mConSelect['places'] = explode('|',$mConSelect['places']);
		$mConSelect['productcodes'] = explode('|',$mConSelect['productcodes']);*/
	}
	pmysql_free_result( $lbConSelectRes );
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
	location.href="design_lookbooklist.php";
}
function goBackView(lbno){
	location.href="design_lookbook_write.php?mode=modfiy_select&lbno="+lbno;
}
function img_view(num){
	window.open('design_lookbook_imgview.php?num='+num ,'_blank',"scrollbars=yes; top=10; left=5");
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; LOOKBOOK 정보관리 &gt;<span>LOOKBOOK <?=$qType_text?></span></p></div></div>
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
			<input type=hidden name=mode>
			<input type="hidden" name="itemCount">
			<input type=hidden name=lbno value="<?=$lbno?>">		
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">LOOKBOOK <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>LOOKBOOK을 <?=$qType_text?>할 수 있습니다.</span></div>
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
				<?include("layer_prlistPop.php");?>
				<div class="table_style01">					
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>제목</span></th>
					<TD><INPUT maxLength=27 size=80 id='title' name='title' value="<?=$mSelect['title']?>"></TD>
				</tr>
				<tr>
					<th><span>설명 텍스트</span></th>
					<TD><INPUT maxLength=80 size=80 id='subtitle' name='subtitle' value="<?=$mSelect['subtitle']?>" ></TD>
				</tr>
				<tr>
					<th><span>이미지(PC)</span></th>
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
					<th><span>이미지(MOBILE)</span></th>
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
					<th><span>노출</span></th>
					<TD><INPUT type='checkbox' id='hidden' name='hidden' value="1" <? if( $mSelect['hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상세 정보</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<div class="table_style01 lookbooktable" style='padding-bottom:0px'>	
<?php
	if( $qType == '0' ){
?>
				<table name="lookbooktable" cellpadding=0 cellspacing=0 border=0 width=100% class="item1">
				<col width=140></col>
				<col width=400></col>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>영역 우선순위</span></th>
					<td colspan=3><INPUT maxLength=20 size=20 name='sort[0]' value="1"><INPUT type='hidden' name='lbcno[0]' value=""></td>
				</tr> 
				<tr>
					<th><span>이미지(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile2[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile2[]" value="" >
					</td>
					<th><span>이미지(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile2[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile2[]" value="" >
					</td>
				</tr>
	
				</table>
<?php
	} else {
?>			
<?php
		//exdebug($mConSelect);
		$sj	= 0;
		foreach( $mConSelect as $mKey=>$mVal ){
			//echo $mKey."=>".$mVal."<br>";
			//echo $mVal['sort'];
			$mVal_place			= explode("|",$mVal['places']);
			$mVal_productcode	= explode("|",$mVal['productcodes']);
?>	
				<table name="lookbooktable" cellpadding=0 cellspacing=0 border=0 width=100% class="item<?=($mKey+1)?>">
				<col width=140></col>
				<col width=400></col>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>영역 우선순위</span></th>
					<td colspan="<?if ($mKey>0){echo '2';} else {echo '3';} ?>"><INPUT maxLength=20 size=20 name='sort[<?=$mKey?>]' value="<?=$mVal['sort']?>"><INPUT type='hidden' name='lbcno[<?=$mKey?>]' value="<?=$mVal['no']?>"></td>
					<?if ($mKey>0){?><td style='border-left:1px solid #fff;text-align:right;'><img src="images/btn_del6.gif"  id="tr_del" border="0" style="cursor:pointer;"></td><?}?>
				</tr> 
				<tr>
					<th><span>이미지(PC)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile2[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile2[]" value="<?=$mVal['img']?>" >
						<div style='margin-top:5px' >
						<?	if( is_file($imagepath.$mVal['img']) ){ ?>
							<a href="javascript:img_view(<?=$mVal['no']?>)"><img src='<?=$imagepath.$mVal['img']?>' style='max-width: 125px;' /></a>
						<?	} ?>
						</div>
					</td>
					<th><span>이미지(MOBILE)</span></th>
					<td class="td_con1" style="position:relative">
						<input type=file name="up_imagefile2[]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile2[]" value="<?=$mVal['img_m']?>" >
						<div style='margin-top:5px' >
						<?	if( is_file($imagepath.$mVal['img_m']) ){ ?>
							<img src='<?=$imagepath.$mVal['img_m']?>' style='max-width: 125px;' />
						<?	} ?>
						</div>
					</td>
				</tr>
		
				</table>
<?php
		}
	}
?>
				</div>
				</td>
			</tr>
			<tr>
				<TD style='text-align:right;padding-top:10px;' colspan="4"><img src="images/btn_add.gif"  id="tr_add" border="0" style="cursor:pointer;"></TD>
			</tr>
			<tr><td height=20></td></tr>	
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
							<dt><span>LOOKBOOK <?=$qType_text?></span></dt>
							<dd>- LOOKBOOK을 <?=$qType_text?>할 수 있습니다.
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
<script language="javascript">
$(document).ready(function(){
	$("#tr_add").click(function(){	
		var lastItemNo = $(".lookbooktable [name=lookbooktable]:last").attr("class").replace("item", "");
		document.form1.itemCount.value = lastItemNo;
		if(lastItemNo <=20){
			//var newItem = $(".lookbooktable [name=lookbooktable]:last").clone();
					
			var newItem = "<table name=\"lookbooktable\" cellpadding=0 cellspacing=0 border=0 width=100% class=\""+"item"+(parseInt(lastItemNo)+1)+"\"><col width=140></col><col width=400></col><col width=140></col><col width=></col><tr><th><span>영역 우선순위</span></th><td colspan=2><INPUT maxLength=20 size=20 name='sort["+lastItemNo+"]' value=\""+(parseInt(lastItemNo)+1)+"\"><INPUT type='hidden' name='lbcno["+lastItemNo+"]' value=''></td><td style='border-left:1px solid #fff;text-align:right;'><img src=\"images/btn_del6.gif\"  id=\"tr_del\" border=\"0\" style=\"cursor:pointer;\"></td></tr> <tr><th><span>이미지1(PC)</span></th><td class=\"td_con1\" style=\"position:relative\"><input type=file name=\"up_imagefile2[]\" style=\"WIDTH: 400px\"><br><input type=hidden name=\"v_up_imagefile2[]\" value=\"\" ></td><th><span>이미지1(MOBILE)</span></th><td class=\"td_con1\" style=\"position:relative\"><input type=file name=\"up_imagefile2[]\" style=\"WIDTH: 400px\"><br><input type=hidden name=\"v_up_imagefile2[]\" value=\"\" ></td></tr>";
			/*
			var sj= parseInt(lastItemNo) * 5;
			for(var j=sj;j<(sj+5);j++){
				newItem += "<TR>";
				newItem += "	<th><span>좌표(PC)</span></th>";
				newItem += "	<TD class=\"td_con1\"><INPUT maxLength=60 size=60 name='places["+j+"]' value=\"\"><input type=\"hidden\" name=\"productcodes["+j+"]\" class='procode productcodes"+j+"'></TD>";
				newItem += "	<th><span>관련상품</span>&nbsp;&nbsp;<a href=\"javascript:T_layer_open('layer_product_sel','relationProduct"+j+"');\"><img src=\"./images/btn_search2.gif\" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>";
				newItem += "	<td align=\"left\">";
				newItem += "			<div style=\"margin-top:0px; margin-bottom: 0px;height:50px\">		";					
				newItem += "				<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name=\"prList\" class=\"prList\" id=\"check_relationProduct"+j+"\" alt=\""+j+"\">	";
				newItem += "				<input type=\"hidden\" name=\"limit_relationProduct"+j+"\" id=\"limit_relationProduct"+j+"\" value=\"1\"/>		";						
				newItem += "					<colgroup>";
				newItem += "						<col width=50></col>";
				newItem += "						<col width=></col>";
				newItem += "					</colgroup>";
				newItem += "				</table>";
				newItem += "			</div>";
				newItem += "	</td>";
				newItem += "</TR>";
			}
			*/
			$('.lookbooktable').append(newItem); 					
			
		}else{ 
			alert("20개까지 등록할 수 있습니다.");
			return;   
		}
	}); 
});
$(document).on('click', '#tr_del',function(e){
	if(confirm('삭제하시겠습니까?')){
		$(this).parent().parent().parent().parent().remove();
	}
});
</script>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
