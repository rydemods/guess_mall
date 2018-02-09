<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

include_once($Dir."lib/forum.class.php");

$forum = new FORUM('request_write_form');
$cate_A = $forum->cate_A;
$cate_B = $forum->cate_B;
$forum_info = $forum->write_form['forum_info'];
$view_detail = $forum->write_form['view'];
$type = $_REQUEST['type'];
?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<div id="contents">
	<div class="inner forum-wrap">
		<main class="board-list-wrap write">
			<h2>FORUM</h2>
			<div class="tit-search">
				<h3>포럼 신청</h3>
			</div>
			<div class="list-wrap">
			
			<form name="request_form" method=post action="/front/forum_process.php">
				<input type=hidden name="mode" value="request_write">
				<input type=hidden name="forum_index" value="<?=$view_detail->index?>">
				<input type=hidden name="type" value="<?=$type?>">
				<input type=hidden name=code_a value="<?=$view_detail->code_a?>">
				<input type=hidden name=code_b value="<?=$view_detail->code_a?>">
				<input type=hidden name=code_c value="<?=$view_detail->code_c?>">

				<table class="th_left">
					<caption>포럼 신청하기 작성</caption>
					<colgroup>
						<col style="width:160px">
						<col style="width:auto">
						<col style="width:160px">
						<col style="width:160px">
					</colgroup>
					<tbody>
						<tr>
							<th><label for="">카테고리(1차)</label></th>
							<td colspan="3" class="category">
								<div class="my-comp-select" class="width:290px;">
									<select class="required_value select_A" id="" name="" value="" label="카테고리 선택" data-degree="1">
										<option value="no">선택</option>
									<?foreach($cate_A as $key=>$val){?>
										<option value="<?=$val->code_a?>"><?=$val->code_name?></option>
									<?}?>
									</select>
									
								</div>
								<?if($view_detail->code_a){?>
										신청한 카테고리 : <?=$view_detail->code_a?>
								<?}?>
							</td>
						</tr>
						<tr>
							<th><label for="">카테고리(2차)</label></th>
							<td colspan="3" class="category">
								<div class="temp_view">
									1차 카테고리를 선택하세요
								</div>
								<div class="my-comp-select area_B" class="width:290px;" style="display:none;">
								<?foreach($cate_B as $key=>$val1){?>
									<select class="required_value select_B" id="select_B_<?=$key?>" name="" value="" label="카테고리 선택" data-degree="2" style="display:none;">
										<option value ="no">선택</option>
										<option value="custom">직접입력</option>
									<?foreach($val1 as $key2=>$val2){?>
										<option value=""><?=$val2->code_name?></option>
									<?}?>
									</select>
								<?}?>
								</div>
								<div style="display:none;" id="custom_cate">
									<input type="text" class="required_value" id="" name="custom_cate" value="" title="카테고리 입력" label="카테고리" placeholder="카테고리를 입력하세요">
								</div>
								<?if($view_detail->code_b){?>
										&nbsp;&nbsp;&nbsp;신청한 카테고리 : <?=$view_detail->code_b?>
								<?}?>
							</td>
						</tr>
						<tr>
							<th><label for="">포럼명</label></th>
							<td colspan="3">
								<input type="text" class="required_value" id="" name="code_c" value="<?=$view_detail->code_c?>" title="제목 입력자리" label="제목" style="width:590px;">
							</td>
						</tr>
						<tr>
							<th><label for="">제목</label></th>
							<td colspan="3">
								<input type="text" class="required_value" id="" name="title" value="<?=$view_detail->title?>" title="제목 입력자리" label="제목" style="width:100%;">
							</td>
						</tr>
						<tr>
							<th><label for="">내용</label></th>
							<td colspan="3">
								<textarea class="required_value" id="" name="content" cols="30" rows="10" label="문의내용" style="width:100%"><?=$view_detail->content?></textarea>
							</td>
						</tr>
					</tbody>
				</table>

				</form>

				<div class="btn_wrap ta-c mt-30">
					<a href="javascript:;" class="btn-type1" id="">취소</a>
					<a href="javascript:;" class="btn-type1 c1" id="btnSubmit">등록</a>
				</div>
			</div>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->

<script>

function select_cate()
{
	var degree = $(this).data('degree');

	if(degree =="1"){
		if( $(this).val() =='no'){
			$(".select_B").css("display","none");
			$(".area_B").css("display","none");
			$(".temp_view").css("display","block");
		}else{
			var code_a = $("option:selected",this).text();
			document.request_form.code_a.value = code_a;
			var sel_cate = $(this).val();
			$(".temp_view").css("display","none");
			$(".select_B").css("display","none");
			$(".area_B").css("display","block");
			$("#select_B_"+sel_cate).css("display","block");
		}
	}

	if(degree =="2"){
		
		if( $(this).val() =='custom'){
			document.request_form.code_b.value ="custom@#";
			$("#custom_cate").css("display","block");
		}else if( $(this).val() == 'no' ){
			document.request_form.code_b.value ="";
			$("#custom_cate").css("display","none");
		}else{
			var code_b = $("option:selected",this).text();
			document.request_form.code_b.value = code_b;
			$("#custom_cate").css("display","none");
		}

	}

}

function request_submit()
{
	document.request_form.submit();	
}

$(document).on("change",".select_A",select_cate);

$(document).on("change",".select_B",select_cate);

$(document).on("click","#btnSubmit",request_submit);

</script>

<?php
include ($Dir."lib/bottom.php")
?>
