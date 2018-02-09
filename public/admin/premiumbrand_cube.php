<?
$pb->cube_list();
$pb->pb_info();
$cube_list = $pb->cube_list;
$pb_info = $pb->pb_info;
?>

<form name="cube_form" method=post action="premiumbrand.proc.php" enctype="multipart/form-data">
<input type=hidden name=mode value="write_cube">
<input type=hidden name="brand_no" value="<?=$brand_no?>">
<div class="contentsBody">
	<h3 class="table-title">큐브 설정<!-- <a  class="btn-line">더보기</a> --></h3>
	<table class="th-left">
		<caption>큐브 설정</caption>
		<colgroup>
			<col style="width:18%"><col style="width:70%">
		</colgroup>
		<tbody>

			<tr>
				<th scope="col">큐브<br><br><input type="checkbox" name="use_cube" value="Y" <?if($pb_info->use_cube =='Y'){echo "checked";}?>>*선택시 노출</th>
				<td scope="col">
					<img src="cube_guide.jpg"><br>
					<span style="font-size:20px;padding-top:20px;">번호에 맞춰 큐브 내용을 설정하세요</span>
				</td>
			</tr>

			<tr>
				<th scope="col">큐브 로고 설정</th>
				<td scope="col">
					<input type="hidden" name="v_logo_file" value="<?=$pb_info->brand_logo ?>" id="v_logo_file" class="vi-image">
					<input type="file" name="logo_file[]" id="logo_file" class="add-image" style='display:none;'>
					<div class="image_preview" id="log_image" style="width:100%;height:100%;">
						<?if( is_file($imagepath.$pb_info->brand_logo) ){?>
							<img src="<?=$imagepath.$pb_info->brand_logo?>" style="width:100px;height:100px;">
						<?}else{?>
							<img src="" style="width:100px;height:100px;display:none;">
						<?}?>
					</div>
					<a  class="btn-line reg_img">이미지등록</a>
					<?if( is_file($imagepath.$pb_info->brand_logo) ){?>
					<img src="images/icon_del1.gif" onclick="javascript:imgDel('log_image','logo_file','v_logo_file');" border="0" style="cursor: hand;vertical-align:middle;" />
					<?}?>
				</td>
			</tr>

			<tr>
				<th scope="col">큐브 배경 설정</th>
				<td scope="col">
					<input type="hidden" name="v_bg_file" value="<?=$pb_info->brand_bg ?>" id="v_bg_file" class="vi-image">
					<input type="file" name="bg_file[]" id="bg_file" class="add-image" style='display:none;'>
					<div class="image_preview" id="bg_image" style="width:100%;height:100%;">
						<?if( is_file($imagepath.$pb_info->brand_bg) ){?>
							<img src="<?=$imagepath.$pb_info->brand_bg?>" style="width:200px;">
						<?}else{?>
							<img src="" style="width:100px;height:100px;display:none;">
						<?}?>
					</div>
					<a  class="btn-line reg_img">이미지등록</a>
					<?if( is_file($imagepath.$pb_info->brand_bg) ){?>
					<img src="images/icon_del1.gif" onclick="javascript:imgDel('bg_image','bg_file','v_bg_file');" border="0" style="cursor: hand;vertical-align:middle;" />
					<?} ?>
				</td>
			</tr>
			
		<?for($i=1; $i<10; $i++){//큐브 9개 사용 합니다?>
			<tr>
				<th scope="col"><?=$i?>번</th>
				<td scope="col">
					<?if( $cube_list[$i]->type2=='i' && is_file($imagepath.$cube_list[$i]->img) || $cube_list[$i]->type2=='m' && $cube_list[$i]->movie_link !='') {?>
						등록됨
					<?}else{?>
						등록안됨
					<?}?>
					<a  class="btn-line view_detail_cube">펼쳐보기</a>
					<div class="detail_cube" style='display:none;'>
						<table style="width:100%;">
						<col style="width:20%"><col style="width:70%">
							<tr>
								<th>타입</th>
								<td>
									<input type=hidden name="index[]" value="<?=$i?>">
									<input type=hidden name='sel_type[]' class="chk_sel_type" value="<?=$cube_list[$i]->type2?>">
									<input type=radio name='sel_type_<?=$i?>' class='sel_type' data-type="img" <?=$cube_list[$i]->chk_sel_type['i']?>>이미지 &nbsp; 
									<input type=radio name='sel_type_<?=$i?>' class='sel_type' data-type="mov" <?=$cube_list[$i]->chk_sel_type['m']?>>동영상
								</td>
							</tr>
							<tr class="img_area" style="display:<?if($cube_list[$i]->type2 != 'i'){echo "none";}?>;">
								<th>이미지  <b>*필수</b></th>
								<td>
									<input type="hidden" name="v_cube_file[]" value="<?=$cube_list[$i]->img ?>" id="v_cube_file<?=$i?>" class="vi-image">
									<input type="file" name="cube_file[]" id="cube_file<?=$i?>" class="add-image" style='display:none;'>
									<div class="image_preview" id="cube_image<?=$i?>" style="width:100%;height:100%;">
										<?if( ($cube_list[$i]->type2=='i') && (is_file($imagepath.$cube_list[$i]->img) ) ){?>
											<img src="<?=$imagepath.$cube_list[$i]->img?>" style="width:100px;height:100px;">
										<?}else{?>
											<img src="" style="width:100px;height:100px;display:none;">
										<?}?>
									</div>
									<a  class="btn-line reg_img">이미지등록</a>
									<?if( ($cube_list[$i]->type2=='i') && (is_file($imagepath.$cube_list[$i]->img) ) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('cube_image<?=$i?>','cube_file<?=$i?>','v_cube_file<?=$i?>');" border="0" style="cursor: hand;vertical-align:middle;" />
									<?}?>
								</td>
							</tr>
							<tr class="hover_img_area" style="display:<?if($cube_list[$i]->type2 != 'i'){echo "none";}?>;">
								<th>호버 이미지 <b>*선택</b></th>
								<td>
									<input type="hidden" name="v_cube_file2[]" value="<?=$cube_list[$i]->img2 ?>" id="v_cube_file2<?=$i?>" class="vi-image">
									<input type="file" name="cube_file2[]" id="cube_file2<?=$i?>" class="add-image" style='display:none;'>
									<div class="image_preview" id="cube_image2<?=$i?>" style="width:100%;height:100%;">
										<?if( ($cube_list[$i]->type2=='i') && (is_file($imagepath.$cube_list[$i]->img2) ) ){?>
											<img src="<?=$imagepath.$cube_list[$i]->img2?>" style="width:100px;height:100px;">
										<?}else{?>
											<img src="" style="width:100px;height:100px;display:none;">
										<?}?>
									</div>
									<a  class="btn-line reg_img">이미지등록</a>
									<?if( ($cube_list[$i]->type2=='i') && (is_file($imagepath.$cube_list[$i]->img2) ) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('cube_image2<?=$i?>','cube_file2<?=$i?>','v_cube_file2<?=$i?>');" border="0" style="cursor: hand;vertical-align:middle;" />
									<?} ?>
							</tr>
							<tr class="link_area" style="display:<?if($cube_list[$i]->type2 != 'i'){echo "none";}?>;">
								<th>링크</th>
								<td>
									<input type="text" name='link[]' value="<?=$cube_list[$i]->link?>">&nbsp;
									<input type=hidden name='link_type[]' value="<?=$cube_list[$i]->link_type?>" class="chk_link_type">
									<input type=radio name="l_type_<?=$i+1?>" class='l_type' data-type="a" <?=$cube_list[$i]->chk_link_type['a']?>>앵커 &nbsp; 
									<input type=radio name="l_type_<?=$i+1?>" class='l_type' data-type="f" <?=$cube_list[$i]->chk_link_type['f']?>>일반링크
								</td>
							</tr>
							<tr class="thumb_area" style="display:<?if($cube_list[$i]->type2 != 'm'){echo "none";}?>;">
								<th>동영상 썸네일</th>
								<td>
									<input type="hidden" name="v_thumb_file[]" value="<?=$cube_list[$i]->img ?>" id="v_thumb_file" class="vi-image">
									<input type="file" name="thumb_file[]" id="thumb_file" class="add-image" style='display:none;'>
									<div class="image_preview" id="thumb_image" style="width:100%;height:100%;">
										<?if( ($cube_list[$i]->type2=='m') && (is_file($imagepath.$cube_list[$i]->img) ) ){?>
											<img src="<?=$imagepath.$cube_list[$i]->img?>" style="width:100px;height:100px;">
										<?}else{?>
											<img src="" style="width:100px;height:100px;display:none;">
										<?}?>
									</div>
									<a  class="btn-line reg_img">이미지등록</a>
									<?if( ($cube_list[$i]->type2=='m') && (is_file($imagepath.$cube_list[$i]->img) ) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('thumb_image','thumb_file','v_thumb_file');" border="0" style="cursor: hand;vertical-align:middle;" />
									<?}?>
								</td>
							</tr>
							<tr class="movie_area" style="display:<?if($cube_list[$i]->type2 != 'm'){echo "none";}?>;">
								<th>동영상 링크</th>
								<td>
								<?if($cube_list[$i]->movie_link){?>
								<div id="movie_link">
									<iframe width="320" height="240" src="https://www.youtube.com/embed/<?=$cube_list[$i]->link?>?rel=0&amp;&amp;controls=0" frameborder="0" allowfullscreen="">
									</iframe>
								</div><br>
								<?}?>
								<input type="text" name="movie_link[]" id="v_movie_link" value="<?=$cube_list[$i]->movie_link?>">
								<?if( ($cube_list[$i]->type2=='m') && ($cube_list[$i]->movie_link ) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('movie_link','','v_movie_link');" border="0" style="cursor: hand;vertical-align:middle;" />
								<?}?>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<?}?>

			<tr>
				<td colspan=2><center><a style="cursor:pointer;" id="submit_cube"><img src="images/botteon_save.gif"></a></center></td>
			</tr>
		</tbody>
	</table>
</div><!-- //.contentsBody -->
</form>

<script>

function view_detail()
{
	$(this).next().fadeToggle();
}

function preview_img(e)
{
	ext = $(this).val().split('.').pop().toLowerCase();
	if($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
		resetFormElement($(this));
		window.alert('이미지 파일이 아닙니다! (gif, png, jpg, jpeg 만 업로드 가능)');
	} else {
		blobURL = window.URL.createObjectURL(e.target.files[0]);
		//$(".image_preview").find('img').attr('src', blobURL).css("display","block");
		$(this).next().find('img').attr('src', blobURL).css("display","block");
	}
}

function reg_img()
{
	$(this).prev().prev().click();
}

function change_type()
{
	var sel_type = $(this).parent().find('.chk_sel_type');
	var type = $(this).data('type');
	var img = $(this).parent().parent().parent().find(".img_area");
	var mov = $(this).parent().parent().parent().find(".thumb_area");
	if( type=='img'){
		sel_type.val('i');
		mov.fadeOut();
		mov.next().fadeOut();
		img.fadeIn();
		img.next().fadeIn();
		img.next().next().fadeIn();
	}else if(type=='mov'){
		sel_type.val('m');
		mov.fadeIn();
		mov.next().fadeIn();
		img.fadeOut();
		img.next().fadeOut();
		img.next().next().fadeOut();
	}
}

function change_link_type()
{
	var type= $(this).data('type');
	var link_type = $(this).parent().find('.chk_link_type');
	link_type.val(type);
}

function submit_cube()
{
	document.cube_form.submit();
}

$(document).on("change","input[type=file]",preview_img);

$(document).on("click",".view_detail_cube",view_detail);

$(document).on("click",".reg_img",reg_img);

$(document).on("change",".sel_type",change_type);

$(document).on("change",".l_type",change_link_type);

$(document).on("click","#submit_cube",submit_cube);

</script>