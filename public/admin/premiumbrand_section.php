<?
$pb->section_list();
$pb->section_slide_list();
$section_list = $pb->section_list;
$slide_list = $pb->section_slide_list;
?>
<form name="section_form" method=post action="premiumbrand.proc.php" enctype="multipart/form-data">
<input type=hidden name=mode value="write_section">
<input type=hidden name="brand_no" value="<?=$brand_no?>">
<div class="contentsBody">
	<h3 class="table-title">섹션 설정<!-- <a  class="btn-line">더보기</a> --></h3>
	<table class="th-left">
		<caption>섹션 설정</caption>
		<colgroup>
			<col style="width:18%"><col style="width:60%"><col style="width:20%">
		</colgroup>
		<tbody>
		<?for($i=1; $i<9; $i++){//일반 섹션은 8개?>
			<tr>
				<th scope="col">SECTION <?=$i?></th>
				<td scope="col" colspan=2>
					<?if(is_file($imagepath.$section_list[$i]->img)){?>
						등록됨
					<?}else{?>
						등록안됨
					<?}?>
					<a  class="btn-line view_detail">펼쳐보기</a>
					<div style="display:none">
						<table style="width:100%;">
						<col style="width:18%"><col style="width:70%">
							<tr>
								<th>이미지</th>
								<td>
									<input type="hidden" name="v_section_file[]" id="v_section_file<?=$i?>" value="<?=$section_list[$i]->img ?>" class="vi-image">
									<input type="file" name="section_file[]" id="section_file<?=$i?>" class="add-image" style='display:none;'>
									<div class="image_preview" id="section_image<?=$i?>" style="width:100%;height:100%;">
										<?if( is_file($imagepath.$section_list[$i]->img) ){?>
											<img src="<?=$imagepath.$section_list[$i]->img?>" style="width:200px;">
										<?}else{?>
											<img src="" style="width:200px;display:none;">
										<?}?>
									</div>
									<a  class="btn-line reg_img">이미지등록</a>
									<input type=hidden name='display1[]' value="<?=$section_list[$i]->display?>">
									<?if( is_file($imagepath.$section_list[$i]->img) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('section_image<?=$i?>','section_file<?=$i?>','v_section_file<?=$i?>');" border="0" style="cursor: hand;vertical-align:middle;" />
									<?} ?>
									<input type="checkbox" class="display_section" <?if($section_list[$i]->display=='Y'){echo "checked";}?>>*선택시 노출
								</td>
							</tr>
							<tr>
								<th>이미지<br>(모바일)</th>
								<td>
									<input type="hidden" name="v_section_file_m[]" id="v_section_file_m<?=$i?>" value="<?=$section_list[$i]->img_m ?>" class="vi-image">
									<input type="file" name="section_file_m[]" id="section_m_file<?=$i?>"  class="add-image" style='display:none;'>
									<div class="image_preview" id="section_m_image<?=$i?>" style="width:100%;height:100%;">
										<?if( is_file($imagepath.$section_list[$i]->img_m) ){?>
											<img src="<?=$imagepath.$section_list[$i]->img_m?>" style="width:200px;">
										<?}else{?>
											<img src="" style="width:200px;display:none;">
										<?}?>
									</div>
									<a  class="btn-line reg_img">이미지등록</a>
									<input type=hidden name='display_m[]' value="<?=$section_list[$i]->display_m?>">
									<?if( is_file($imagepath.$section_list[$i]->img_m) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('section_m_image<?=$i?>','section_m_file<?=$i?>','v_section_file_m<?=$i?>');" border="0" style="cursor: hand;vertical-align:middle;" />
									<?}?>
									<input type="checkbox" class="display_section_m" <?if($section_list[$i]->display_m=='Y'){echo "checked";}?>>*선택시 노출
								</td>
							</tr>
							<tr>
								<th>링크</th>
								<td><input type=text name='section_link[]' value="<?=$section_list[$i]->link?>"></td>
							</tr>
						</table>
					</div>
				</td>
				<!--
				<td scope="col">
					<input type=hidden name='display1[]' value="<?=$section_list[$i]->display?>">
					<input type="checkbox" class="display_section" <?if($section_list[$i]->display=='Y'){echo "checked";}?>>*선택시 노출 
				</td>
				-->
			</tr>
		<?}?>
			<tr>
				<th scope="col">SECTION SLIDE</th>
				<td scope="col">12개까지 이미지를 등록하여 슬라이드를 구성합니다.<br>
				등록된 섹션들중 마지막 전 영역에 노출됩니다.<br>
					<a  class="btn-line view_detail">펼쳐보기</a>
					<div style="display:none;">
						<table style="width:100%;">
						<col style="width:20%"><col style="width:40%"><col style="width:30%">
						<?for($i=1; $i<13; $i++){//슬라이드는 12개 까지 사용합니다.?>
							<tr>
								<th>슬라이드<?=$i?></th>
								<td>
									<input type="hidden" name="v_slide_file[]" id="v_slide_file" value="<?=$slide_list[$i]->img ?>" class="vi-image">
									<input type="file" name="slide_file[]" id="slide_file" class="add-image" style='display:none;'>
									<div class="image_preview" id="slide_image" style="width:100%;height:100%;">
										<?if( is_file($imagepath.$slide_list[$i]->img) ){?>
											<img src="<?=$imagepath.$slide_list[$i]->img?>" style="width:200px;">
										<?}else{?>
											<img src="" style="width:200px;display:none;">
										<?}?>
									</div>
									<a  class="btn-line reg_img">이미지등록</a>
									<?if( is_file($imagepath.$slide_list[$i]->img) ){?>
									<img src="images/icon_del1.gif" onclick="javascript:imgDel('slide_image','slide_file','v_slide_file');" border="0" style="cursor: hand;vertical-align:middle;" />
									<?} ?>
								</td>
								<td><input type="text" name='slide_link[]' value="<?=$slide_list[$i]->link?>">링크</td>
							</tr>
						<?}?>
						</table>
					</div>
				</td>
				<td scope="col"><input type="checkbox" name="display2" value="Y" <?if($slide_list[1]->display=='Y'){echo "checked";}?>>*선택시 노출</td>
			</tr>

		</tbody>
	</table>
	<center style="padding-top:50px;"><a style="cursor:pointer;" id="submit_section"><img src="images/botteon_save.gif"></a></center>
</div><!-- //.contentsBody -->
</form>


<script>

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

function view_detail()
{
	$(this).next().fadeToggle();
}

function submit_section()
{
	document.section_form.submit();
}

function chk_section_display()
{
	if( $(this).is(":checked") ){
		$(this).prev().val('Y');
	}else{
		$(this).prev().val('N');	
	}
}

function chk_section_display_m()
{
	if( $(this).is(":checked") ){
		$(this).prev().val('Y');
	}else{
		$(this).prev().val('N');	
	}
}

$(document).on("click",".display_section",chk_section_display);

$(document).on("click",".display_section_m",chk_section_display_m);

$(document).on("change","input[type=file]",preview_img);

$(document).on("click",".reg_img",reg_img);

$(document).on("click",".view_detail",view_detail);

$(document).on("click","#submit_section",submit_section);


</script>