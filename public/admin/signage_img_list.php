<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
include("header.php");
#########################################################

$mode = $_REQUEST['mode'];
$imagepath = $Dir.DataDir."shopimages/signage/"; //이미지 경로

// 이미지 파일
$banner_file = new FILE($imagepath);

$no = $_POST['no'];
$idx=$_POST['idx'];

$checked["center_type"]["0"]="checked";
$center_img_hide="";
$center_url_hide="style='display:none'";

if ($idx) {
    //$whereIdx = "'" . implode("','", $idx) . "'";
    
    $sql = "";
    if ( $mode == "visible_set" ) {
		$a_sql  = "UPDATE tblsignage_img SET viewyn = 0 ";
		pmysql_query($a_sql);
        $sql  = "UPDATE tblsignage_img SET viewyn = 1 WHERE no in ({$idx}) ";
    } else if ( $mode == "visible_unset" ) {
        $sql  = "UPDATE tblsignage_img SET viewyn = 0 WHERE no in ({$idx}) ";
    }

    if ( !empty($sql) ) { $result = pmysql_query($sql); }
}

if($mode=="ins"){
	$banner_img=$banner_file->upFiles();
	$title=$_POST["title"];
	$center_type=$_POST["center_type"];
	$center_url=$_POST["center_url"];
	$viewyn=$_POST["viewyn"]?$_POST["viewyn"]:"0";
	$top_img=$banner_img["top_img"][0]["v_file"];
	$bottom_img=$banner_img["bottom_img"][0]["v_file"];
	//$center_img=$banner_img["center_img"][0]["v_file"];
	$logo_img=$banner_img["logo_img"][0]["v_file"];


	if($viewyn) {
		$a_sql  = "UPDATE tblsignage_img SET viewyn = 0 ";
		pmysql_query($a_sql);
	}

	$ins_qry="insert into tblsignage_img(top_img, bottom_img, center_type, center_img, title, viewyn, regdt, center_url, panel_no, logo_img) values ('".$top_img."', '".$bottom_img."', '".$center_type."', '', '".$title."', '".$viewyn."', now(), '".$center_url."', '1', '".$logo_img."') RETURNING no ";

	$return_no = pmysql_fetch_object( pmysql_query($ins_qry) );//등록후 2차 패널 등록을 위한 no를 받습니다. 

	#####2패널 중단 타입 이미지 다른 테이블에 저장합니다. #####
	if(!$center_type){
		$center_imgs= $banner_img["center_img"];
		if($center_imgs ){
			$sub_sql = " insert into tblsignage_img_panel ( img_no, img, panel_no, reg_date ) values ";
			$sub_where="";
			foreach($center_imgs as $sort=>$i_val){
				if( strlen($i_val['v_file']) >0 ) {
					$sub_where[] = "(".$return_no->no." ,'".$i_val[v_file]."', ".$sort.",now() )";
				}
			}
			$sub_sql .= implode("," , $sub_where);
			pmysql_query($sub_sql);
		}
	}
	###################################################
	echo "<script>alert('등록되었습니다');</script>";

}else if($no && $mode=="mod"){
	$where="";
	$banner_img=$banner_file->upFiles();
	$title=$_POST["title"];
	$center_type=$_POST["center_type"];
	$center_url=$_POST["center_url"];
	$viewyn=$_POST["viewyn"]?$_POST["viewyn"]:"0";
	$top_img=$banner_img["top_img"][0]["v_file"];
	$bottom_img=$banner_img["bottom_img"][0]["v_file"];
	
	$logo_img=$banner_img["logo_img"][0]["v_file"];
	$v_top_img=$_POST["v_top_img"];
	$v_bottom_img=$_POST["v_bottom_img"];

	$v_logo_img=$_POST["v_logo_img"];

	$where[]="title='".$title."'";
	$where[]="viewyn='".$viewyn."'";
	$where[]="center_type='".$center_type."'";
	if( strlen( $top_img ) > 0 ){
		if( is_file( $imagepath.$v_top_img ) > 0 ){
			$banner_file->removeFile( $v_top_img );
		}
		$where[]="top_img='".$top_img."'";
	}
	if( strlen( $bottom_img ) > 0 ){
		if( is_file( $imagepath.$v_bottom_img ) > 0 ){
			$banner_file->removeFile( $v_bottom_img );
		}
		$where[]="bottom_img='".$bottom_img."'";
	}
	if( strlen( $logo_img ) > 0 ){
		if( is_file( $imagepath.$v_logo_img ) > 0 ){
			$banner_file->removeFile( $v_logo_img );
		}
		$where[]="logo_img='".$logo_img."'";
	}

	if($viewyn) {
		$a_sql  = "UPDATE tblsignage_img SET viewyn = 0 ";
		pmysql_query($a_sql);
	}

	##########2패널 중단 이미지 처리 분리합니다.############
	$v_center_imgs= $_POST["v_center_img"];
	$center_imgs= $banner_img["center_img"];

	if(!$center_type){//2패널 중단 타입 이미지 
	
		if($center_imgs ){
			foreach($center_imgs as $index=>$i_val){
				if( ( strlen( $i_val['v_file'] ) >0 ) && ( is_file( $imagepath.$v_center_imgs[$index] ) > 0 ) ){ //업로드 파일이 존재하면서, 기존 파일이 존재하면 기존 파일 삭제합니다.
					$banner_file->removeFile($v_center_imgs[$index]);
				}
				if( strlen( $i_val['v_file'] ) >0 ){
					list($chk_data)=pmysql_fetch("select index from tblsignage_img_panel where img_no = {$no} AND panel_no = {$index} ");
					if($chk_data){//기존 이미지가 있다면 업데이트
						$sub_sql = " update tblsignage_img_panel set ";
						$sub_sql .= " img = '{$i_val[v_file]}' ";
						$sub_sql .= " where img_no = {$no} AND panel_no = {$index} ";
					}else{//기존 이미지가 없다면 삽입
						$sub_sql = " insert into tblsignage_img_panel ( img_no, img, panel_no, reg_date ) values ";
						$sub_sql .=  "(".$no." ,'".$i_val[v_file]."', ".$index.",now() )";
					}
					pmysql_query($sub_sql);
				}
			}
			/*
			if( is_file( $imagepath.$v_center_img ) > 0 ){
				$banner_file->removeFile( $v_center_img );
			}
			*/
			$where[]="center_img='".$center_img."'";
		}
		$where[]="center_url=''";
	}else{//2패널 중단 타입 동영상
		$v_center_img= $_POST["v_center_img"];
		$center_img = $banner_img["center_img"][0]["v_file"];
		foreach($v_center_imgs as $index=>$v_val){
			if( is_file( $imagepath.$v_val ) > 0 ){//동영상을 사용할 경우 기존에 중단 이미지는 모두 제거해 줍니다
				$banner_file->removeFile( $v_val );
			}
		}
		$del_qry2 ="delete from tblsignage_img_panel where img_no = {$no} ";
		pmysql_query($del_qry2);

		$where[]="center_img=''";
		$where[]="center_url='".$center_url."'";
	}
	##################################################
	$up_qry="update tblsignage_img set ".implode(", ", $where)." where no='".$no."'";
	pmysql_query($up_qry);
	echo "<script>alert('수정되었습니다');</script>";

}else if ($no && $mode=="del"){
		
		$v_top_img=$_POST["v_top_img"];
		$v_bottom_img=$_POST["v_bottom_img"];
		$v_center_img=$_POST["v_center_img"];
		$v_logo_img=$_POST["v_logo_img"];
	
		if( is_file( $imagepath.$v_top_img ) > 0 ) $banner_file->removeFile( $v_top_img );
		if( is_file( $imagepath.$v_bottom_img ) > 0 ) $banner_file->removeFile( $v_bottom_img );	
		if( is_file( $imagepath.$v_center_img ) > 0 ) $banner_file->removeFile( $v_center_img );
		if( is_file( $imagepath.$v_logo_img ) > 0 ) $banner_file->removeFile( $v_logo_img );

		$del_qry="delete from tblsignage_img where no='".$no."'";
		pmysql_query($del_qry);

		$del_qry2 ="delete from tblsignage_img_panel where img_no = {$no} ";
		pmysql_query($del_qry2);
		echo "<script>alert('삭제되었습니다');</script>";
}

if($no){
	
	$sql="select * from tblsignage_img where no='".$no."'";
	$result=pmysql_query($sql);
	$data=pmysql_fetch_object($result);
	
	if($data->center_type == "0"){
		$panel_2 = "";
		$sub_sql = " select img, panel_no from tblsignage_img_panel where img_no = {$no} order by panel_no asc";
		$sub_result = pmysql_query($sub_sql);
		while($sub_row = pmysql_fetch_object($sub_result) ){
			$panel_2[] = $sub_row;
		}
	}

	$checked["center_type"][$data->center_type]="checked";
	if($data->center_type=="1"){
		$center_img_hide="style='display:none'";
		$center_url_hide="";
	}else{
		$center_img_hide="";
		$center_url_hide="style='display:none'";
		
	}
}

$page_sql = "SELECT COUNT(*) FROM tblsignage_img ";

//echo $page_sql;
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$s_sql="select * from tblsignage_img order by regdt desc";
$s_sql = $paging->getSql($s_sql);
$s_result=pmysql_query($s_sql);

list($idx_check)=pmysql_fetch("select no from tblsignage_img where viewyn='1' limit 1");
$checked[idx][$idx_check]="checked";

?>

<div class="admin_linemap"><div class="line"><p>현재위치 : 디지털사이니즈 이미지관리 </span></p></div></div>

<script>
	function center_change(num){
		if(num=="1"){
			$(".ID_centerImg").hide();
			$("#ID_centerLink").show();
		}else{
			$(".ID_centerImg").show();
			$("#ID_centerLink").hide();
		}
	}

	function changeStatus(mode) {


		if ( $('input:radio[name=idx]').is(':checked')==false ) {
			alert('하나 이상을 선택해 주세요.');
			return;
		}
		document.form1.mode.value = "visible_set";
		msg = "노출 설정을 하시겠습니까?";
/*
		switch(mode) {
			case 1:
				// 노출 설정
				document.form1.mode.value = "visible_set";
				msg = "노출 설정을 하시겠습니까?";
				break;
			case 2:
				// 비노출 설정
				document.form1.mode.value = "visible_unset";
				msg = "비노출 설정을 하시겠습니까?";
				break;
		}
*/
		if ( confirm(msg) ) {
			document.form1.submit();
		}
	}

	function check_indb(type){
		
		if(!$("#title").val()){
			alert("타이틀을 입력해주세요.");
			$("#title").focus();
			return
		}

		$("#mode").val(type);
		$("#form1").submit();	

	}

	function list_indb(type, no){
		if(type=="del"){
			if(confirm("삭제하시겠습니까?")){
				$("#no").val(no);
				$("#mode").val(type);
				$("#form1").submit();	
			}
		}else{
			$("#no").val(no);
			$("#mode").val(type);
			$("#form1").submit();	
		}

	}

</script>
<form name='form1' id='form1' method='POST' enctype="multipart/form-data">
	<input type="hidden" id="mode" name="mode">
	<input type="hidden" id="no" name="no" value="<?=$no?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">		

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
			<?php include("menu_signage.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">디지털사이니즈 이미지관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span> 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					
						<col width=140></col>
						<col width=></col>
						
						<tr id='ID_trName' >
							<th><span>타이틀</span></th>
							<TD><INPUT maxLength=80 size=80 id='title' name='title' value="<?=$data->title?>">
							</TD>
						</tr>

						<tr id='ID_trImg'>
							<th>
								<span>1패널 상단 로고</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="logo_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_logo_img" value="<?=$data->logo_img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$data->logo_img) ){ ?>
									<img src='<?=$imagepath.$data->logo_img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr id='ID_trImg'>
							<th>
								<span>2패널 상단 이미지</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="top_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_top_img" value="<?=$data->top_img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$data->top_img) ){ ?>
									<img src='<?=$imagepath.$data->top_img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr id='ID_trImg'>
							<th>
								<span>2패널 하단 이미지</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="bottom_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_bottom_img" value="<?=$data->bottom_img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$data->bottom_img) ){ ?>
									<img src='<?=$imagepath.$data->bottom_img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr id='ID_trImg'>
							<th>
								<span>2패널 중단 타입</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type="radio" name="center_type" value="0" <?=$checked["center_type"]["0"]?> onclick="center_change('0')">이미지 <input type="radio" name="center_type" value="1" <?=$checked["center_type"]["1"]?> onclick="center_change('1')">동영상
							</td>
						</tr>

						<?/*
						<tr class='ID_centerImg' <?=$center_img_hide?>>
							<th>
								<span>2패널 중단 이미지 1</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="center_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_center_img[]" value="<?=$data->center_img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$data->center_img) ){ ?>
									<img src='<?=$imagepath.$data->center_img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>
						*/?>

						<tr class='ID_centerImg' <?=$center_img_hide?>>
							<th>
								<span>2패널 중단 이미지 1</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="center_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_center_img[]" value="<?=$panel_2[0]->img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$panel_2[0]->img) ){ ?>
									<img src='<?=$imagepath.$panel_2[0]->img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr class='ID_centerImg' <?=$center_img_hide?>>
							<th>
								<span>2패널 중단 이미지 2</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="center_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_center_img[]" value="<?=$panel_2[1]->img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$panel_2[1]->img) ){ ?>
									<img src='<?=$imagepath.$panel_2[1]->img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr class='ID_centerImg' <?=$center_img_hide?>>
							<th>
								<span>2패널 중단 이미지 3</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="center_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_center_img[]" value="<?=$panel_2[2]->img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$panel_2[2]->img) ){ ?>
									<img src='<?=$imagepath.$panel_2[2]->img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr class='ID_centerImg' <?=$center_img_hide?>>
							<th>
								<span>2패널 중단 이미지 4</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="center_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_center_img[]" value="<?=$panel_2[3]->img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$panel_2[3]->img) ){ ?>
									<img src='<?=$imagepath.$panel_2[3]->img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						<tr class='ID_centerImg' <?=$center_img_hide?>>
							<th>
								<span>2패널 중단 이미지 5</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="center_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_center_img[]" value="<?=$panel_2[4]->img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$panel_2[4]->img) ){ ?>
									<img src='<?=$imagepath.$panel_2[4]->img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>


						<tr id='ID_centerLink' <?=$center_url_hide?>>
							<th><span>2패널 동영상 링크</span></th>
							<TD>
								<INPUT size="150" id='center_url' name='center_url' value="<?=$data->center_url?>" >
								<div style='margin-top:5px' >
								<?if($data->center_url){?>
									<iframe width="320" height="240" src="<?=$data->center_url?>" frameborder="0" allowfullscreen="">
									</iframe>
								<?}?>
								</div>
							</TD>
						</tr>

						<tr>
							<th><span>노출</span></th>
							<TD><INPUT type='checkbox' id='viewyn' name='viewyn' value="1" <?if($data->viewyn==1){echo "checked";}?>> * 체크시 노출됩니다. </TD>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="8" align="center">
				<?if($mode=='mod_list' || $mode=='mod'){?>
					<a id="modi_brand" href="javascript:check_indb('mod')"><img src="images/btn_edit2.gif"></a>
				<?}else{?>	
					<a id="reg_brand" href="javascript:check_indb('ins')"><img src="images/btn_confirm_com.gif"></a>
				<?}?>
					<a href="signage_img_list.php"><img src="img/btn/btn_list.gif" ></a>
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
                        
                    </tr>
                    </table>
				</td>
			</tr>
			
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width="30"></col>
					<col width="200"></col>
					<col width="130"></col>
					<col width="130"></col>
					<col width="130"></col>
					<col width="100"></col>
					<col width=""></col>
					<col width="80"></col>
					<col width="80"></col>
					<col width="60"></col>
					<col width="60"></col>
				</colgroup>

				<TR>
					<th>&nbsp;</th>
					<th>타이틀</th>
					<th>1패널 로고</th>
					<th>2패널 상단이미지</th>
					<th>2패널하단이미지</th>
					<th>2패널중단타입</th>
					<th>2패널중단 동영상URL</th>
					<th>노출여부</th>
					<th>등록일</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
			<?while($s_data=pmysql_fetch_array($s_result)){
				$center_title[0]="이미지";
				$center_title[1]="동영상";
				$viewyn_title[0]="비노출";
				$viewyn_title[1]="노출";
				?>

				<TR>
					<td><input type="radio" name="idx" value="<?=$s_data['no']?>" <?=$checked[idx][$s_data['no']]?>></td><!-- 번호 -->

					<td><?=$s_data["title"]?></td><!--이름-->
					<td>
						<div id='img_display' >
						<?if( is_file($imagepath.$s_data["logo_img"]) ){?>
							<img src='<?=$imagepath.$s_data["logo_img"]?>' style='max-width : 70px;' >
						<?} else {?>
							-
						<?}?>	
						</div>
					</td><!-- 이미지 -->

					<td>
						<div id='img_display' >
						<?if( is_file($imagepath.$s_data["top_img"]) ){?>
							<img src='<?=$imagepath.$s_data["top_img"]?>' style='max-width : 70px;' >
						<?} else {?>
							-
						<?}?>	
						</div>
					</td><!-- 이미지 -->

					<td>
						<div id='img_display' >
						<?if( is_file($imagepath.$s_data["bottom_img"]) ){?>
							<img src='<?=$imagepath.$s_data["bottom_img"]?>' style='max-width : 70px;' >
						<?} else {?>
							-
						<?}?>	
						</div>
					</td><!-- 이미지 -->

					<td><?=$center_title[$s_data['center_type']]?></td>
					<?if($s_data['center_type']=="1"){?>
						<td><?=$s_data['center_url']?></td><!--노출순서-->
					<?}else{?>
						<td> - </td>
					<!--
						<td>
							<div id='img_display' >
							<?if( is_file($imagepath.$s_data["center_img"]) ){?>
								<img src='<?=$imagepath.$s_data["center_img"]?>' style='max-width : 70px;' >
							<?} else {?>
								-
							<?}?>	
							</div>
						</td>--><!-- 이미지 -->
					<?}?>
					

					<td><?=$viewyn_title[$s_data['viewyn']]?></td><!-- 노출 / 비노출 -->

					<td><?=$s_data['regdt']?></td><!-- 노출 / 비노출 -->
					
					<td><a class="list_btn_modi" href="javascript:list_indb('mod_list', '<?=$s_data["no"]?>')"><img src="images/btn_edit.gif"></a></td><!-- 수정 -->
					
					<td><a class="list_btn_del" href="javascript:list_indb('del', '<?=$s_data["no"]?>')"><img src="images/btn_del.gif"></a></td><!-- 삭제 -->
				</TR>
			<?}?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
					</div>
				</div>

				</td>
			</tr>
			
			<tr>
				<td height="20">&nbsp;</td>
			</tr>

            <tr >
                <td colspan="8" align="center">
                    <a href="javascript:changeStatus('1');">
                        <img src="images/btn_visible_set.png">
                    </a>
					<!---
                    <a href="javascript:changeStatus('2');">
                        <img src="images/btn_visible_unset.png">
                    </a>-->
                </td>
            </tr>

			<tr>
				<td height="30">&nbsp;</td>
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

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}


</script>

<?=$onload?>
<?php
include("copyright.php");
