<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$member['id']=$_ShopInfo->getMemid();
$member['name']=$_ShopInfo->getMemname();
//var_dump($member);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<HEAD>
<TITLE><?=$_data->shoptitle?> - 이용약관</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<META name="description" content="<?=(ord($_data->shopdescription)?$_data->shopdescription:$_data->shoptitle)?>">
<META name="keywords" content="<?=$_data->shopkeyword?>">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<?php include($Dir."lib/style.php")?>
</HEAD>

<!--php끝-->
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<body<?=($_data->layoutdata["MOUSEKEY"][0]=="Y"?" oncontextmenu=\"return false;\"":"")?><?=($_data->layoutdata["MOUSEKEY"][1]=="Y"?" ondragstart=\"return false;\" onselectstart=\"return false;\"":"")?> leftmargin="0" marginwidth="0" topmargin="0" marginheight="0"><?=($_data->layoutdata["MOUSEKEY"][2]=="Y"?"<meta http-equiv=\"ImageToolbar\" content=\"No\">":"")?>
<script type="text/javascript" src="<?=$Dir?>js/instagramAPI.js"></script>


<!-- [D] 스토어_리스트 퍼블 추가 -->
<div id="contents" class="bg">
	<div class="inner">
		<main class="store_story_wrap write">
			<h3>STORE STORY</h3>
			<h4>스토어 스토리 등록</h4>
			<section class="store_story_list">
				<table class="th_left">
				<caption></caption>
				<colgroup>
					<col style="width:160px">
					<col style="width:325px">
					<col style="width:160px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row">지점명</th>
						<td>
							<div class="my-comp-select">
								<select class="required_value" id="" name="" value="" label="지점선택">
									<option value="">지점명</option>
									<option value="">명동점</option>
									<option value="">여의도점</option>
									<option value="">안양점</option>
									<option value="">인천구월점</option>
									<option value="">대전은행동점</option>
									<option value="">대구동성로점</option>
									<option value="">울산</option>
									<option value="">광주충장로</option>
									<option value="">부산대학로</option>
									<option value="">홍대점</option>
								</select>
							</div>
						</td>
						<th scope="row">비공개</th>
						<td>
							<div>
								<input id="" name="" type="checkbox" class="chk_agree checkbox-def" value="" checked="">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">이미지첨부</th>
						<td colspan="3" class="imageAdd">
							<input type="file" name="up_filename[]" id="up_filename">
							<div class="txt-box">핫티.jpg</div> <!-- 파일 업로드시 파일 주소 출력 -->
							<label for="up_filename">찾기</label>
						</td>
					</tr>
					<tr>
						<th scope="row">내용</th>
						<td colspan="3">
							<textarea class="required_value" id="content" name="content" cols="30" rows="22" label="문의내용" style="width:100%">
								A－LINE의 혁신적이며 세련된 디자인은 공간활용도가 뛰어나며 어떤 장소에서도 잘 어울립니다. 또한 고광택 하이글로시 표면 처리된 고강도의 ABS 제질로
								재질로 제작되어 외관이 고급스러우면서도 내구성이 매우 뛰어납니다. 정해진 위치에만 물건을 수납하는 복잡하고 규격화된 오거나이저와 달리 심플한 디자
								인의 A－BOARD는 자신만의 스타일로 자유스럽고 세련되게 정리하고 연출할 수 있습니다. 4포트 USB 2.0허브가 내장되어 스마트폰 충전은 물론 USB메모
								리와 외장하드 등을 편리하게 연결하여 사용할 수 있습니다. 재입고 기념 35％할인 놓치지마세요.
							</textarea>
						</td>
					</tr>
				</tbody>
				</table>
				<div class="btn_wrap mt-30">
					<a href="../front/mypage_personal.php" class="btn-type1 c1">등록</a>
					<a href="../front/mypage_personal.php" class="btn-type1">취소</a>
				</div>
			</section>
		</main>
	</div>
</div>
<!-- // [D] 스토어_리스트 퍼블 추가 -->





<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
