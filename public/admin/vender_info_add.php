<?php
/********************************************************************* 
// 파 일 명		: vender_info_add.php
// 설     명		: 입점업체 추가정보
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 입점업체의 추가정보를 수정
// 작 성 자		: 2016.01.13 - 김재수
// 수 정 자		: 중간 사이즈 이미지 숨기고 큰이미지 10개로 수정 (2016.01.22 - 김재수)
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
	$PageCode = "me-4";
	$MenuCode = "member";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$vender=$_POST["vender"];
	if (!$vender) $vender=$_GET["vender"];

	$sql = "SELECT a.*, b.brand_name FROM tblvenderinfo a, tblvenderstore b ";
	$sql.= "WHERE a.vender='{$vender}' AND a.delflag='N' AND a.vender=b.vender ";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vdata=pmysql_fetch_object($result)) {
		alert_go('해당 업체 정보가 존재하지 않습니다.',-1);
	}
	pmysql_free_result($result);

	$type=$_POST["type"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/vender/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($type=="insert" || $type=="update") {				// DB를 수정한다.

		
		$up_description	= pg_escape_string($_POST["up_description"]);
		$v_up_imagefile	= $_POST["v_up_imagefile"];

		$up_imagefile=$imagefile->upFiles();

		if($type=="insert") {
			$sql = "INSERT INTO tblvenderinfo_add(
			vender		,
			description		,
			s_img	,
			m_img	,
			b_img1		,
			b_img2	,
			b_img3	,
			b_img4	,
			b_img5	,
			b_img6	,
			b_img7	,
			b_img8	,
			b_img9	,
			b_img10	,
			s_img_m	,
			m_img_m	,
			b_img1_m		,
			b_img2_m	,
			b_img3_m	,
			b_img4_m	,
			b_img5_m	,
			b_img6_m	,
			b_img7_m	,
			b_img8_m	,
			b_img9_m	,
			b_img10_m) VALUES (
			'{$vender}', 
			'{$up_description}', 
			'".$up_imagefile["up_imagefile"][0]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][1]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][2]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][3]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][4]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][5]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][6]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][7]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][8]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][9]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][10]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][11]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][12]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][13]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][14]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][15]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][16]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][17]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][18]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][19]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][20]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][21]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][22]["v_file"]."', 
			'".$up_imagefile["up_imagefile"][23]["v_file"]."')";
		}else if($type=="update") {

			$img_where="";
			$img_where[] = "description='".$up_description."' ";

			for($u=0;$u<24;$u++) {
				if( strlen( $up_imagefile["up_imagefile"][$u]["v_file"] ) > 0 ){
					if( is_file( $imagepath.$v_up_imagefile[$u] ) > 0 ){
						$imagefile->removeFile( $v_up_imagefile[$u] );
					}
					if ($u == 0) $img_where[] = "s_img='".$up_imagefile["up_imagefile"][0]["v_file"]."'";
					if ($u == 1) $img_where[] = "m_img='".$up_imagefile["up_imagefile"][1]["v_file"]."'";
					if ($u == 2) $img_where[] = "b_img1='".$up_imagefile["up_imagefile"][2]["v_file"]."'";
					if ($u == 3) $img_where[] = "b_img2='".$up_imagefile["up_imagefile"][3]["v_file"]."'";
					if ($u == 4) $img_where[] = "b_img3='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 5) $img_where[] = "b_img4='".$up_imagefile["up_imagefile"][5]["v_file"]."'";
					if ($u == 6) $img_where[] = "b_img5='".$up_imagefile["up_imagefile"][6]["v_file"]."'";
					if ($u == 7) $img_where[] = "b_img6='".$up_imagefile["up_imagefile"][7]["v_file"]."'";
					if ($u == 8) $img_where[] = "b_img7='".$up_imagefile["up_imagefile"][8]["v_file"]."'";
					if ($u == 9) $img_where[] = "b_img8='".$up_imagefile["up_imagefile"][9]["v_file"]."'";
					if ($u == 10) $img_where[] = "b_img9='".$up_imagefile["up_imagefile"][10]["v_file"]."'";
					if ($u == 11) $img_where[] = "b_img10='".$up_imagefile["up_imagefile"][11]["v_file"]."'";
					if ($u == 12) $img_where[] = "s_img_m='".$up_imagefile["up_imagefile"][12]["v_file"]."'";
					if ($u == 13) $img_where[] = "m_img_m='".$up_imagefile["up_imagefile"][13]["v_file"]."'";
					if ($u == 14) $img_where[] = "b_img1_m='".$up_imagefile["up_imagefile"][14]["v_file"]."'";
					if ($u == 15) $img_where[] = "b_img2_m='".$up_imagefile["up_imagefile"][15]["v_file"]."'";
					if ($u == 16) $img_where[] = "b_img3_m='".$up_imagefile["up_imagefile"][16]["v_file"]."' ";
					if ($u == 17) $img_where[] = "b_img4_m='".$up_imagefile["up_imagefile"][17]["v_file"]."' ";
					if ($u == 18) $img_where[] = "b_img5_m='".$up_imagefile["up_imagefile"][18]["v_file"]."' ";
					if ($u == 19) $img_where[] = "b_img6_m='".$up_imagefile["up_imagefile"][19]["v_file"]."' ";
					if ($u == 20) $img_where[] = "b_img7_m='".$up_imagefile["up_imagefile"][20]["v_file"]."' ";
					if ($u == 21) $img_where[] = "b_img8_m='".$up_imagefile["up_imagefile"][21]["v_file"]."' ";
					if ($u == 22) $img_where[] = "b_img9_m='".$up_imagefile["up_imagefile"][22]["v_file"]."' ";
					if ($u == 23) $img_where[] = "b_img10_m='".$up_imagefile["up_imagefile"][23]["v_file"]."' ";
				}
			}	
			
			$sql = "UPDATE tblvenderinfo_add SET ";
			$sql.= implode(", ",$img_where);
			$sql.= "WHERE vender='{$vender}' ";
		}

		if(pmysql_query($sql,get_db_conn())) {

			$log_content = "## 입점업체 추가정보 수정 ## - 업체ID : ".$_vdata->id;
			ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

			echo "<html></head><body onload=\"alert('업체 추가정보 수정이 완료되었습니다.');parent.document.form3.submit();\"></body></html>";exit;
		} else {
			$error="입점업체 추가정보 등록중 오류가 발생하였습니다.";
		}			
	}

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$disabled=$_POST["disabled"];
	$s_check=$_POST["s_check"];
	$search=$_POST["search"];
	$block=$_POST["block"];
	$gotopage=$_POST["gotopage"];

	$sql = "SELECT * FROM tblvenderinfo_add ";
	$sql.= "WHERE vender='{$vender}'";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vadata=pmysql_fetch_object($result)) {
		$submit_type	= "insert";
		//alert_go('해당 업체 정보가 존재하지 않습니다.',-1);
	} else {		
		$submit_type	= "update";
	}
	pmysql_free_result($result);
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="JavaScript">
function CheckForm() {
	if(confirm("입점업체 추가정보를 수정하시겠습니까?")) {
		_editor_url = "htmlarea/";
		var sHTML = oEditors.getById["ir1"].getIR();
		form1.up_description.value=sHTML;
		document.form1.type.value="<?=$submit_type?>";
		document.form1.target="processFrame";
		document.form1.submit();
	}
}

function goBackList(){
	location.href="vender_management2.php";
}

</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 입점업체 정보관리 &gt;<span>입점업체 추가정보 수정</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">입점업체 추가정보 수정</div>
				</td>
			</tr>
			<tr><td height=15></td></tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>업체 ID</span></th>
					<td><B><?=$_vdata->id?></B></td>
				</tr>
				<tr>
					<th><span>상호 (회사명)</span></th>
					<td><?=$_vdata->com_name?></td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=vender value="<?=$vender?>">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">소개글</div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td><textarea name=up_description id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$_vadata->description?></textarea></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">소개 이미지(PC)</div>
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
					<th><span>브랜드 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$_vadata->s_img?>" >
<?	if( is_file($imagepath.$_vadata->s_img) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->s_img?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr style='display:none;'>
					<th><span>중간 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[1]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[1]" value="<?=$_vadata->m_img?>" >
<?	if( is_file($imagepath.$_vadata->m_img) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->m_img?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지1</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[2]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[2]" value="<?=$_vadata->b_img1?>" >
<?	if( is_file($imagepath.$_vadata->b_img1) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img1?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지2</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[3]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[3]" value="<?=$_vadata->b_img2?>" >
<?	if( is_file($imagepath.$_vadata->b_img2) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img2?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지3</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[4]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[4]" value="<?=$_vadata->b_img3?>" >
<?	if( is_file($imagepath.$_vadata->b_img3) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img3?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지4</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[5]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[5]" value="<?=$_vadata->b_img4?>" >
<?	if( is_file($imagepath.$_vadata->b_img4) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img4?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지5</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[6]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[6]" value="<?=$_vadata->b_img5?>" >
<?	if( is_file($imagepath.$_vadata->b_img5) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img5?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지6</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[7]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[7]" value="<?=$_vadata->b_img6?>" >
<?	if( is_file($imagepath.$_vadata->b_img6) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img6?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지7</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[8]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[8]" value="<?=$_vadata->b_img7?>" >
<?	if( is_file($imagepath.$_vadata->b_img7) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img7?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지8</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[9]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[9]" value="<?=$_vadata->b_img8?>" >
<?	if( is_file($imagepath.$_vadata->b_img8) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img8?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지9</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[10]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[10]" value="<?=$_vadata->b_img9?>" >
<?	if( is_file($imagepath.$_vadata->b_img9) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img9?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지10</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[11]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[11]" value="<?=$_vadata->b_img10?>" >
<?	if( is_file($imagepath.$_vadata->b_img10) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img10?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">소개 이미지(MOBILE)</div>
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
					<th><span>브랜드 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[12]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[12]" value="<?=$_vadata->s_img_m?>" >
<?	if( is_file($imagepath.$_vadata->s_img_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->s_img_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr style='display:none;'>
					<th><span>중간 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[13]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[13]" value="<?=$_vadata->m_img_m?>" >
<?	if( is_file($imagepath.$_vadata->m_img_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->m_img_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지1</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[14]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[14]" value="<?=$_vadata->b_img1_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img1_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img1_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지2</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[15]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[15]" value="<?=$_vadata->b_img2_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img2_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img2_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지3</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[16]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[16]" value="<?=$_vadata->b_img3_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img3_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img3_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지4</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[17]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[17]" value="<?=$_vadata->b_img4_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img4_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img4_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지5</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[18]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[18]" value="<?=$_vadata->b_img5_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img5_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img5_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지6</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[19]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[19]" value="<?=$_vadata->b_img6_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img6_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img6_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지7</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[20]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[20]" value="<?=$_vadata->b_img7_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img7_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img7_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지8</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[21]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[21]" value="<?=$_vadata->b_img8_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img8_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img8_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지9</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[22]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[22]" value="<?=$_vadata->b_img9_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img9_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img9_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr>
					<th><span>롤링 이미지10</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="up_imagefile[23]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[23]" value="<?=$_vadata->b_img10_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img10_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img10_m?>' style='max-height: 200px;' />
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
					<a href="javascript:CheckForm();"><img src="images/btn_edit2.gif" width="113" height="38" border="0"></a>
					&nbsp;
					<a href="javascript:goBackList();"><img src="img/btn/btn_list.gif"></a>
				</td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>입점업체 추가정보 수정</span></dt>
							<dd>- 등록된 입점업체 추가정보를 수정 할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name="form3" method="post" action="vender_management2.php">
			<input type=hidden name='vender' value="<?=$value?>">
			<input type=hidden name='disabled' value='<?=$disabled?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
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
<SCRIPT LANGUAGE="JavaScript">
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
