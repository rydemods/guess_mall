<?php
/********************************************************************* 
// 파 일 명		: vender_info_add.php 
// 설     명		: 입점업체 관리자모드 업체 추가정보 관리
// 상세설명	: 입점업체 관리자모드의 업체 추가정보를 관리
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
	include_once($Dir."lib/venderlib.php");
	include("access.php");
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode=$_POST["mode"];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/vender/";
	// 이미지 파일
	$imagefile = new FILE($imagepath);


#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
if($mode=="insert" || $mode=="update") {				// DB를 수정한다.

		
		$up_description	= addslashes($_POST["up_description"]);
		$v_up_imagefile	= $_POST["v_up_imagefile"];

		$up_imagefile=$imagefile->upFiles();

		if($mode=="insert") {
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
			'".$_VenderInfo->getVidx()."', 
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
		}else if($mode=="update") {

			$img_where="";
			$img_where[] = "description='{$up_description}' ";

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
					if ($u == 5) $img_where[] = "b_img4='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 6) $img_where[] = "b_img5='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 7) $img_where[] = "b_img6='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 8) $img_where[] = "b_img7='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 9) $img_where[] = "b_img8='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 10) $img_where[] = "b_img9='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 11) $img_where[] = "b_img10='".$up_imagefile["up_imagefile"][4]["v_file"]."'";
					if ($u == 12) $img_where[] = "s_img_m='".$up_imagefile["up_imagefile"][5]["v_file"]."'";
					if ($u == 13) $img_where[] = "m_img_m='".$up_imagefile["up_imagefile"][6]["v_file"]."'";
					if ($u == 14) $img_where[] = "b_img1_m='".$up_imagefile["up_imagefile"][7]["v_file"]."'";
					if ($u == 15) $img_where[] = "b_img2_m='".$up_imagefile["up_imagefile"][8]["v_file"]."'";
					if ($u == 16) $img_where[] = "b_img3_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 17) $img_where[] = "b_img4_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 18) $img_where[] = "b_img5_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 19) $img_where[] = "b_img6_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 20) $img_where[] = "b_img7_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 21) $img_where[] = "b_img8_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 22) $img_where[] = "b_img9_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
					if ($u == 23) $img_where[] = "b_img10_m='".$up_imagefile["up_imagefile"][9]["v_file"]."' ";
				}
			}	
			
			$sql = "UPDATE tblvenderinfo_add SET ";
			$sql.= implode(", ",$img_where);
			$sql.= "WHERE vender='".$_VenderInfo->getVidx()."' ";
		}

		
		if(pmysql_query($sql,get_db_conn())) {
			
			$log_content = "## 입점업체 추가정보 수정 ## - 벤더 : ".$_VenderInfo->getVidx();
			$_VenderInfo->ShopVenderLog($_VenderInfo->getVidx(),$connect_ip,$log_content);
			echo "<html></head><body onload=\"alert('요청하신 작업이 성공하였습니다.');parent.location.reload()\"></body></html>";exit;
		} else {
			echo "<html></head><body onload=\"alert('요청하신 작업중 오류가 발생하였습니다.')\"></body></html>";exit;
		}		
	}

#---------------------------------------------------------------
# 벤더의 정보를 자른다.
#---------------------------------------------------------------

	include("header.php");  // 상단부분을 불러온다. 

	$sql = "SELECT * FROM tblvenderinfo_add ";
	$sql.= "WHERE vender='".$_VenderInfo->getVidx()."'";
	$result=pmysql_query($sql,get_db_conn());
	if(!$_vadata=pmysql_fetch_object($result)) {
		$submit_type	= "insert";
		//alert_go('해당 업체 정보가 존재하지 않습니다.',-1);
	} else {		
		$submit_type	= "update";
	}
	pmysql_free_result($result);
?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script language="JavaScript">
function formSubmit() {
	var form = document.form1;
	if(confirm("변경하신 내용을 저장하시겠습니까?")) {
		_editor_url = "htmlarea/";
		var sHTML = oEditors.getById["ir1"].getIR();
		form1.up_description.value=sHTML;
		form.mode.value="<?=$submit_type?>";
		form.target="processFrame";
		form.submit();
	}
}
</script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script><!-- 다음 우편번호 api -->
<script>
// 다음 우편번호 팝얻창 불러오기
function openDaumPostcode() {
	new daum.Postcode({
		oncomplete: function(data) {
			// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
			// 우편번호와 주소 정보를 해당 필드에 넣고, 커서를 상세주소 필드로 이동한다.
			document.getElementById('up_com_zonecode').value = data.zonecode;
			document.getElementById('up_com_post1').value = data.postcode1;
			document.getElementById('up_com_post2').value = data.postcode2;
			document.getElementById('up_com_addr').value = data.address;
			document.getElementById('up_com_addr').focus();
			//전체 주소에서 연결 번지 및 ()로 묶여 있는 부가정보를 제거하고자 할 경우,
			//아래와 같은 정규식을 사용해도 된다. 정규식은 개발자의 목적에 맞게 수정해서 사용 가능하다.
			//var addr = data.address.replace(/(\s|^)\(.+\)$|\S+~\S+/g, '');
			//document.getElementById('addr').value = addr;

			
		}
	}).open();
}
</script>

<!-- <table border=0 cellpadding=0 cellspacing=0 width=1000 style="table-layout:fixed"> -->
<table border=0 cellpadding=0 cellspacing=0 width=1480 style="table-layout:fixed">
<col width=175></col>
<col width=5></col>
<!-- <col width=740></col> -->
<col width=1300></col>
<!-- <col width=80></col> -->
<tr>
	<td width=175 valign=top nowrap><? include ("menu.php"); // 해당 메뉴부분을 불러온다. ?></td>
	<td width=5 nowrap></td>
	<td valign=top>

	<table width="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#D0D1D0">
	<tr>
		<td>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0" style="border:3px solid #EEEEEE" bgcolor="#ffffff">
		<tr>
			<td style="padding:10">
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=165></col>
				<col width=></col>
				<tr>
					<td height=29 align=center background="images/tab_menubg.gif">
					<FONT COLOR="#ffffff"><B>업체 추가정보 관리</B></FONT>
					</td>
					<td></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=2 bgcolor=red></td></tr>
			<tr>
				<td bgcolor=#FBF5F7>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=10></col>
				<col width=></col>
				<col width=10></col>
				<tr>
					<td colspan=3 style="padding:15,15,5,15">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td style="padding-bottom:5"><img src="images/icon_boxdot.gif" border=0 align=absmiddle> <B>업체 추가정보 관리</B></td>
					</tr>
					<tr>
						<td style="padding-left:5;color:#7F7F7F"><img src="images/icon_dot02.gif" border=0> 입점사 추가 정보 및 기타 설정 값을 입력합니다.</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td><img src="images/tab_boxleft.gif" border=0></td>
					<td></td>
					<td><img src="images/tab_boxright.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>

			<!-- 처리할 본문 위치 시작 -->
			<tr><td height=0></td></tr>
			<tr>
				<td style="padding:15">
				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>

				<form name=form1 method=post action="<?=$_SERVER[PHP_SELF]?>" enctype="multipart/form-data">
				<input type=hidden name=mode>

				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 업체 소개글</td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td colspan=2>
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td><textarea name=up_description id=ir1 rows=15 wrap=off style="width:100%" class="textarea"><?=$_vadata->description?></textarea></td>
					</tr>
					</table>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 업체 소개 이미지(PC)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>기본 이미지</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[0]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[0]" value="<?=$_vadata->s_img?>" >
<?	if( is_file($imagepath.$_vadata->s_img) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->s_img?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr style='display:none;'><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr style='display:none;'>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>중간이미지</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[1]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[1]" value="<?=$_vadata->m_img?>" >
<?	if( is_file($imagepath.$_vadata->m_img) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->m_img?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지1</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[2]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[2]" value="<?=$_vadata->b_img1?>" >
<?	if( is_file($imagepath.$_vadata->b_img1) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img1?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지2</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[3]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[3]" value="<?=$_vadata->b_img2?>" >
<?	if( is_file($imagepath.$_vadata->b_img2) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img2?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지3</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[4]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[4]" value="<?=$_vadata->b_img3?>" >
<?	if( is_file($imagepath.$_vadata->b_img3) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img3?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지4</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[5]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[5]" value="<?=$_vadata->b_img4?>" >
<?	if( is_file($imagepath.$_vadata->b_img4) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img4?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지5</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[6]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[6]" value="<?=$_vadata->b_img5?>" >
<?	if( is_file($imagepath.$_vadata->b_img5) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img5?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지6</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[7]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[7]" value="<?=$_vadata->b_img6?>" >
<?	if( is_file($imagepath.$_vadata->b_img6) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img6?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지7</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[8" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[8]" value="<?=$_vadata->b_img7?>" >
<?	if( is_file($imagepath.$_vadata->b_img7) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img7?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지8</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[9]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[9]" value="<?=$_vadata->b_img8?>" >
<?	if( is_file($imagepath.$_vadata->b_img8) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img8?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지9</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[10]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[10]" value="<?=$_vadata->b_img9?>" >
<?	if( is_file($imagepath.$_vadata->b_img9) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img9?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지10</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[11]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[11]" value="<?=$_vadata->b_img10?>" >
<?	if( is_file($imagepath.$_vadata->b_img10) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img10?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td><img src="images/icon_dot03.gif" border=0 align=absmiddle> 업체 소개 이미지(MOBILE)</font></td>
				</tr>
				<tr><td height=5></td></tr>
				<tr><td height=1 bgcolor=red></td></tr>
				</table>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width=140></col>
				<col width=></col>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>기본 이미지</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[12]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[12]" value="<?=$_vadata->s_img_m?>" >
<?	if( is_file($imagepath.$_vadata->s_img_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->s_img_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr style='display:none;'><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr style='display:none;'>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>중간이미지</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[13]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[13]" value="<?=$_vadata->m_img_m?>" >
<?	if( is_file($imagepath.$_vadata->m_img_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->m_img_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지1</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[14]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[14]" value="<?=$_vadata->b_img1_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img1_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img1_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지2</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[15]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[15]" value="<?=$_vadata->b_img2_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img2_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img2_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지3</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[16]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[16]" value="<?=$_vadata->b_img3_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img3_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img3_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지4</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[17]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[17]" value="<?=$_vadata->b_img4_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img4_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img4_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지5</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[18]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[18]" value="<?=$_vadata->b_img5_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img5_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img5_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지6</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[19]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[19]" value="<?=$_vadata->b_img6_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img6_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img6_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지7</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[20]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[20]" value="<?=$_vadata->b_img7_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img7_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img7_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지8</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[21]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[21]" value="<?=$_vadata->b_img8_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img8_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img8_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지9</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[22]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[22]" value="<?=$_vadata->b_img9_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img9_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img9_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				<tr>
					<td bgcolor=F5F5F5 background=images/line01.gif style=background-repeat:repeat-y;background-position:right;padding:9><B>상세 이미지10</td>
					<td style="padding:7px 10px;position:relative">
						<input type=file name="up_imagefile[23]" style="WIDTH: 400px"><br>
						<input type=hidden name="v_up_imagefile[23]" value="<?=$_vadata->b_img10_m?>" >
<?	if( is_file($imagepath.$_vadata->b_img10_m) ){ ?>
						<div style='margin-top:5px' >
									<img src='<?=$imagepath.$_vadata->b_img10_m?>' style='max-height: 200px;' />
						</div>
<?	} ?>
					</td>
				</tr>
				<tr><td height=1 colspan=2 bgcolor=E7E7E7></td></tr>
				</table>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<tr><td height=20></td></tr>
				<tr>
					<td align=center>
					<A HREF="javascript:formSubmit()"><img src="images/btn_save01.gif" border=0></A>
					</td>
				</tr>

				</form>

				</table>

				<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>

				</td>
			</tr>
			<!-- 처리할 본문 위치 끝 -->

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
<?=$onload?>
<?php include("copyright.php"); // 하단부분을 불러온다. ?>
