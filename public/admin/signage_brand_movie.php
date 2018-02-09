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

##########################function###############################
function set_brand($mode)
{
	$imagepath = "../".DataDir."shopimages/signage/"; //이미지 경로
	if($mode =='reg' || $mode=='modi'){//등록 또는 수정
		
		$v_brand_img = $_POST['v_brand_img'];//기존 등록되어 있던 이미지
		$name = $_POST['brand_name'];
		$link ="";
		$full_link = $_POST['full_link'];
		if($full_link){
			$link = explode("/", str_replace(array('http://','https://'), '', $full_link) );
			$link = $link[1];
		}
		$sort = $_POST['sort'] ? $_POST['sort']: 0;
		$display = $_POST['hidden'];
		$brand_text = $_POST['brand_text'];

		$brand_file = new FILE($imagepath);
		$brand_img=$brand_file->upFiles();
		if( strlen( $brand_img["brand_img"][0]["v_file"] ) > 0 ){//신규로 등록한 이미지가 존재할 때
			if( is_file( $imagepath.$v_brand_img ) > 0 ){//기존 이미지가 이미지 폴더에 존재하면은 삭쩨
				$brand_file->removeFile( $v_brand_img );
			}
		}
	
		if($mode =='reg'){//등록쿼리
			$sql = " insert into tblsignage_brand ";
			$sql .= "(
				name,
				img,
				link,
				full_link,
				sort,
				display,
				brand_text
			)";
			$sql .= " values (
				'{$name}',
				'{$brand_img[brand_img][0][v_file]}',
				'{$link}',
				'{$full_link}',
				{$sort},
				'{$display}',
				'{$brand_text}'
			)";
		}

		if($mode=='modi'){//수정쿼리
			$num = $_POST['num'];
			if($brand_img[brand_img][0][v_file]){
				$img = $brand_img[brand_img][0][v_file];
			}else{
				$img = $v_brand_img;
			}
			$sql = " update tblsignage_brand ";
			$sql .="set
				name = '{$name}',
				img = '{$img}',
				link = '{$link}',
				full_link = '{$full_link}',
				sort = {$sort},
				display = '{$display}',
				brand_text = '{$brand_text}'
			";
			$sql .= " where num = {$num} ";
		}
	}

	if($mode=='del'){//삭제
		$num = $_POST['num'];
		$sql = " delete from tblsignage_brand where num = {$num} ";
	}

	$result = pmysql_query($sql); //쿼리 수행
	if(!pmysql_error()){//쿼리 성공시 동작
		alert_go('적용되었습니다','signage_brand_movie.php');
	}
	
}

function brand_list() //리스트 가져오기
{
	$list ="";
	$sql = " select * from tblsignage_brand ";
	$result = pmysql_query($sql);
	while( $row = pmysql_fetch_object($result) ){
		if($row->display == '1'){
			$row->display="사용";
		}else{
			$row->display="노출안함";
		}
		$list[] = $row;
	}

	return $list;
}

function view_detail($num) //브랜드 수정을 위한 상세 보기
{
	$sql = "select * from tblsignage_brand where num = {$num} ";
	$result = pmysql_query($sql);
	return $view_detail = pmysql_fetch_object($result);
}

##########################//function###############################


#############################page load############################
$mode = $_REQUEST['mode'];
$imagepath = $Dir.DataDir."shopimages/signage/"; //이미지 경로
//$link = "https://youtu.be/JRQbVNzmCK0";//테스트 링크
//$link ="Z-moluUhncQ"; //테스트 링크 
//$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );//페이징 번호 줄 용도인대 걍 안쓸거임

$mode = $_POST['mode'];
$num = $_POST['num'];

if($mode=='view'){
	$view_detail = view_detail($num);
}else{
	if($mode){
		set_brand($mode);
	}
}

$brand_list = brand_list();//리스트

#############################//page load###########################
?>

<div class="admin_linemap"><div class="line"><p>현재위치 : 브랜드 영상 &gt;</span></p></div></div>

<form name='brand_form' id='brand_form' method='POST' enctype="multipart/form-data">
	<input type="hidden" id="mode" name="mode">
	<input type="hidden" id="num" name="num" value="<?=$num?>">

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
					<div class="title_depth3">브랜드 영상 관리</div>

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
							<th><span>이름</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_name' name='brand_name' value="<?=$view_detail->name?>">
							</TD>
						</tr>

						<tr id='ID_trImg'>
							<th>
								<span>썸네일 이미지</span>
							</th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="brand_img[]" style="WIDTH: 400px"><br>
								<input type=hidden name="v_brand_img" value="<?=$view_detail->img?>" >
								<div style='margin-top:5px' >
								<?	if( is_file($imagepath.$view_detail->img) ){ ?>
									<img src='<?=$imagepath.$view_detail->img?>' style='max-width: 125px;' />
								<?	} ?>
								</div>
							</td>
						</tr>

						
						<tr id='ID_trLink'>
							<th><span>동영상 링크</span></th>
							<TD>
								<INPUT maxLength=80 size=80 id='banner_link' name='full_link' value="<?=$view_detail->full_link?>" >
								<div style='margin-top:5px' >
								<?if($view_detail->link){?>
									<iframe width="320" height="240" src="https://www.youtube.com/embed/<?=$view_detail->link?>?rel=0&amp;&amp;controls=0" frameborder="0" allowfullscreen="">
									</iframe>
								<?}?>
								</div>
							</TD>
						</tr>

						<tr>
							<th><span>텍스트</span></th>
							<td>
								<textarea style="width:490px;" name="brand_text"><?=$view_detail->brand_text?></textarea>*내용이 길면 중간에 br 태그로 줄바꿈 가능
							</td>
						</tr>
					
						<tr>
							<th><span>노출순서</span></th>
							<TD><INPUT maxLength=10 size=10 id='sort' name='sort' value="<?=$view_detail->sort?>" ></TD>
						</tr>

						<tr>
							<th><span>노출</span></th>
							<TD><INPUT type='checkbox' id='hidden' name='hidden' value="1" <?if($view_detail==1){echo "checked";}?>> * 체크시 노출됩니다. </TD>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>

			<tr>
				<td colspan="8" align="center">
				<?if($mode=='view'){?>
					<a id="modi_brand" data-mode="modi"><img src="images/btn_edit2.gif"></a>
				<?}else{?>	
					<a id="reg_brand" data-mode="reg"><img src="images/btn_confirm_com.gif">	</a>
				<?}?>
					<a href="signage_brand_movie.php"><img src="img/btn/btn_list.gif" ></a>
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
                        <td align="right" style="display:none;">
                            <div style="margin:20px 0 5px; align: left;">
                          사용 : 
                            <select id="search_hidden">
                                <option value="">========전체=======</option>
                                <option value="1" >노출</option>
                                <option value="0">비노출</option>
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
					
				</colgroup>

				<TR>
					<th><input type="checkbox" id="allCheck" onClick="CheckAll()";></th>
					<!-- <th>번호</th> -->
					<th>이름</th>
					<th>썸네일이미지</th>
					<th>동영상 링크</th>
					<th>노출순서</th>
	
					<th>사용여부</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
			<?if($brand_list){?>
				<?foreach($brand_list as $list){?>
				<TR>
					<td><input type="checkbox" name="idx[]" value="<?=$bVal['no']?>"></td><!-- 번호 -->

					<td><?=$list->name?></td><!--이름-->
				
					<td>
						<div id='img_display' >
						<?if( is_file($imagepath.$list->img) ){?>
							<img src='<?=$imagepath.$list->img?>' style='max-width : 70px;' >
						<?} else {?>
							-
						<?}?>	
						</div>
					</td><!-- 이미지 -->

					<td><a href="<?=$list->full_link?>"><?=$list->full_link?></a></td>

					<td><?=$list->sort?></td><!--노출순서-->

					<td><?=$list->display?></td><!-- 노출 / 비노출 -->
					
					<td><a class="list_btn_modi" data-num="<?=$list->num?>"><img src="images/btn_edit.gif"></a></td><!-- 수정 -->
					
					<td><a class="list_btn_del" data-num="<?=$list->num?>" data-mode="del"><img src="images/btn_del.gif"></a></td><!-- 삭제 -->
				</TR>
				<?}?>
			<?}else{?>
				<TR>
					<td colspan='8' > 목록이 존재하지 않습니다.</td>
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

            <tr style="display:none;">
                <td colspan="8" align="center">
                    <a href="javascript:changeVisible('1');">
                        <img src="images/btn_visible_set.png">
                    </a>
                    <a href="javascript:changeVisible('0');">
                        <img src="images/btn_visible_unset.png">
                    </a>
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
	document.insertForm.mode.value = "";
	document.insertForm.block.value = block;
	document.insertForm.gotopage.value = gotopage;
	document.insertForm.submit();
}

function CheckAll() {
	if($("#allCheck").prop("checked")) {
		$("input[name='idx[]']").prop("checked",true);
	} else {
		$("input[name='idx[]']").prop("checked",false);
	}
}

function set_brand()
{
	var mode = $(this).data('mode');
	if(mode =='del'){
		$("#num").val($(this).data('num'));
	}
	$("#mode").val(mode);
	$("#brand_form").submit();
}

function view_detail()
{
	var num = $(this).data('num');
	$("#mode").val('view');
	$("#num").val(num);
	$("#brand_form").submit();
}

$(document).on("click","#reg_brand",set_brand);

$(document).on("click","#modi_brand",set_brand);

$(document).on("click",".list_btn_del",set_brand);

$(document).on("click",".list_btn_modi",view_detail);
</script>

<?=$onload?>
<?php
include("copyright.php");
