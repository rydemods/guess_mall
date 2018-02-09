<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$mode=$_POST["mode"];
$destination_name = $_POST["destination_name"];
$get_name = $_POST["get_name"];
$mobile = $_POST["mobile"];
$postcode = $_POST["postcode"];
$postcode_new = $_POST["postcode_new"];
$addr1 = $_POST["addr1"];
$addr2 = $_POST["addr2"];
$base_chk = $_POST["chk"];
$today = date("Y-m-d");

#DB 처리
if($mode == "insert"){
	#등록
	#새로 등록될 배송지가 기본 배송지 일 경우
	if($base_chk == "Y"){
		#기본 배송지로 등록되어 있는 no 조회
		$chkY = "SELECT no FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."'
					AND base_chk = 'Y'";
		$chkRes = pmysql_query( $chkY, get_db_conn());
		$chkRow = pmysql_fetch_object($chkRes);

		if($chkRow->no){
			#기존 기본 배송지로 등록되어 있는 데이터를 N으로 업데이트
			$usql = "UPDATE tbldestination SET  base_chk = 'N' WHERE no = ".$chkRow->no."";
			pmysql_query( $usql, get_db_conn());
		}
	}

	$iSql = "INSERT INTO tbldestination (
				mem_id,
				destination_name,
				get_name,
				mobile,
				postcode,
				postcode_new,
				addr1,
				addr2,
				base_chk,
				reg_date
				)values(
				'{$_ShopInfo->getMemid()}',
				'{$destination_name}',
				'{$get_name}',
				'{$mobile}',
				'{$postcode}',
				'{$postcode_new}',
				'{$addr1}',
				'{$addr2}',
				'{$base_chk}',
				'{$today}'
			)";

	$result = pmysql_query($iSql,get_db_conn());

	if(!pmysql_error()){
		alert_go('등록이 완료되었습니다.', $_SERVER['REQUEST_URI']);
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}

}else if($mode == "modify"){
	#수정
	$no = $_POST['no'];

	if($base_chk == "Y" ){
		#기본 배송지로 등록되어 있는 no 조회
		$chkY = "SELECT no FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."'
					AND base_chk = 'Y'";
		$chkRes = pmysql_query( $chkY, get_db_conn());
		$chkRow = pmysql_fetch_object($chkRes);

		if($chkRow->no){
			#기존 기본 배송지로 등록되어 있는 데이터를 N으로 업데이트
			$usql = "UPDATE tbldestination SET  base_chk = 'N' WHERE no = ".$chkRow->no."";
			pmysql_query( $usql, get_db_conn());
		}
	}

	$where[]="destination_name='".$destination_name."'";
	$where[]="get_name='".$get_name."'";
	$where[]="mobile='".$mobile."'";
	$where[]="postcode='".$postcode."'";
	$where[]="postcode_new='".$postcode_new."'";
	$where[]="addr1='".$addr1."'";
	$where[]="addr2='".$addr2."'";
	$where[]="base_chk='".$base_chk."'";

	$usql = "UPDATE tbldestination SET ";
	$usql.= implode(", ",$where);
	$usql.=" WHERE no = '".$no."'";

	pmysql_query( $usql, get_db_conn() );
	if(!pmysql_error()){
		alert_go('수정이 완료되었습니다.', $_SERVER['REQUEST_URI']);
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}

}else if($mode == "delete"){
	#삭제
	$no = $_POST['no'];
	$dSql = "DELETE FROM tbldestination WHERE no = '".$no."'";
	pmysql_query($dSql, get_db_conn());

	if(!pmysql_error()){
		alert_go('삭제가 완료되었습니다.', $_SERVER['REQUEST_URI']);
	}else{
		alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
	}

}

#배송지 관리 리스트
$list_sql ="SELECT * FROM tbldestination WHERE mem_id = '".$_ShopInfo->getMemid()."' ORDER BY NO DESC";
# 페이징
$paging = new New_Templet_paging($list_sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql( $list_sql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$list[] = $row;
}

#기본 배송지
$base['Y'] = '(기본)';

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">배송지 관리</h2>
		<div class="inner-align page-frm clear">

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->
			<article class="my-content">
				
				<div class="ta-r"><button class="btn-line w100 btn-postList add" type="button" id="address_add"><span class="fz-14">배송지 추가</span></button></div>
				<table class="th-top mt-10">
					<colgroup>
						<col style="width:115px">
						<col style="width:115px">
						<col style="width:auto">
						<col style="width:160px">
						<col style="width:170px">
					</colgroup>
					<thead>
						<tr>
							<th scope="col">배송지명</th>
							<th scope="col">받는사람</th>
							<th scope="col">주소</th>
							<th scope="col">연락처</th>
							<th scope="col">수정/삭제</th>
						</tr>
					</thead>
					<tbody>
<?
				if( count($list) > 0 ) {
					$cnt=0;
					foreach( $list as $key=>$val ){
						$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt++ );
?>
						<tr>
							<td class="txt-toneB"><?=$val['destination_name']?></td>
							<td class="txt-toneA"><?=$val['get_name']?></td>
							<td class="subject"><?=$val['addr1']?> <?=$val['addr2']?></td>
							<td class="txt-toneB"><?=addMobile($val['mobile'])?></td>
							<td>
								<div class="refund-btnGroup">
									<button class="btn-basic btn-postList add" type="button" onclick="modify(<?=$val['no'] ?>);" ><span>수정</span></button>
									<button class="btn-line ml-5" type="button" onclick="row_delete(<?=$val['no']?>);"><span>삭제</span></button>
								</div>
							</td>
						</tr>
<?
					}
				} else {
?>
						<tr>
							<td colspan="5">등록된 배송지가 없습니다.</td>
						</tr>
<?
				}
?>
					</tbody>
				</table>
				<div class="list-paginate mt-20">
					<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
				</div>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- 배송지  시작  -->
<div class="layer-dimm-wrap pop-post add">
	<div class="layer-inner">
		<h2 class="layer-title">배송지 추가</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			<form name='destination_form' id='destination_form' action="" method='POST' >
			<input type='hidden' id='mode' name='mode'>
			<input type='hidden' id='chk' name='chk' value="N">
			<input type='hidden' id='no' name='no'>
			<input type='hidden' id='mobile' name='mobile'>
			<input type=hidden name=block>
			<input type=hidden name=gotopage>
			<table class="th-left mt-10">
				<caption>배송지 추가</caption>
				<colgroup>
					<col style="width:138px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="post_add_name1" class="essential">배송지명</label></th>
						<td>
							<div class="input-cover">
								<input type="text" title="배송지명 입력자리" style="width:190px" placeholder="배송지명 입력" class="required_value" label = "배송지 명" name="destination_name" id="destination_name">
								<div class="checkbox ml-20">
									<input type="checkbox"  id="base_chk" name="base_chk">
									<label for="base_chk">기본 배송지로 저장</label>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="post_add_name2" class="essential">받는사람</label></th>
						<td>
							<div class="input-cover">
								<input type="text" title="받는사람 입력자리" style="width:190px" placeholder="이름 입력" class="required_value" name="get_name" id="get_name" label = "받는사람">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="post_add_phone1" class="essential">휴대전화</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select id="post_add_phone1" style="width:110px" title="전화 앞자리 선택">
										<option value="010" selected="">010</option>
										<option value="011">011</option>
										<option value="016">016</option>
										<option value="017">017</option>
										<option value="018">018</option>
										<option value="019">019</option>
									</select>
								</div>
								<span class="txt">-</span>
								<input type="text" title="필수 휴대폰 번호 가운데 입력자리" style="width:110px" id="post_add_phone2" class="required_value" label = "중간번호">
								<span class="txt">-</span>
								<input type="text" title="필수 휴대폰 번호 마지막 입력자리" style="width:110px" id="post_add_phone3" class="required_value" label = "마지막 번호">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label class="">주소</label></th>
						<td>
							<ul class="input-multi input-cover">
								<li><input type="text" title="우편번호 입력자리" name="postcode_new" id="postcode_new" class="required_value" label = "우편번호"><button class="btn-basic btn-postList" type="button" onclick="javascript:search_zip();"><span>주소찾기</span></button></li>
								<li><input type="hidden" name="postcode" id="postcode" title="우편번호(구)" style="width:130px;"></li>
								<li><input type="text" class="w100-per required_value" name="addr1" id="addr1" title="주소" label = "주소"></li>
								<li><input type="text" title="상세주소 입력" class="w100-per" name="addr2" id="addr2"></li>
							</ul>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			<div class="btnPlace mt-40">
				<button class="btn-line h-large" type="button"><span>취소</span></button>
				<button class="btn-point h-large" type="submit" id="btnSubmit"><span>적용</span></button>
			</div>

		</div><!-- //.layer-content -->
	</div>
</div>
<!-- 배송지 끝 -->

<script type="text/javascript" src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script Language="JavaScript">
$(document).ready(function (){
	$("#btnSubmit").click(function(){
		if(check_form()){
			if($("#mode").val() != "modify"){
				$("#mode").val("insert");
			}
			$("#destination_form").submit();
		}
	});

	$("#address_add").click(function(){
		$("#btnSubmit").text("추가");
		//$("#submit_type").text("추가");
		$("#base_chk").attr("checked", false);
	});

	//form 리셋
	$(".btn-close").click(function(){
		document.forms['destination_form'].reset();
	});

	//기본 배송지 체크값 설정
	$("#base_chk").change(function(){
        if($("#base_chk").is(":checked")){
        	$("#chk").val("Y");
        }else{
        	$("#chk").val("N");
        }
    });

});

function check_form() {
	var procSubmit = true;

	$(".required_value").each(function(){
		if(!$(this).val()){
			alert($(this).attr('label')+"를 정확히 입력해 주세요");
			$(this).focus();
			procSubmit = false;
			return false;
		}
	})
	
	$("#mobile").val($("#post_add_phone1").val() + "-" + $("#post_add_phone2").val() + "-" + $("#post_add_phone3").val());

	if(procSubmit){
		return true;
	}else{
		return false;
	}

}

function search_zip(text){
	daum.postcode.load(function(){
		new daum.Postcode({
			oncomplete: function(data) {
				var postcode = data.zonecode; //2015-08-01 시행 새 우편번호
				var zipCode1 = data.postcode1; //구 우편번호1
				var zipCode2 = data.postcode2; //구 우편번호2

				if(data.userSelectedType == 'R'){ //도로명
					var address = data.roadAddress;
				}else{//지번
					var address = data.jibunAddress;
				}

				$("#postcode_new").val(postcode);
				$("#postcode").val(zipCode1+"-"+zipCode2);
				$("#addr1").val(address);

			}
		}).open();
	});
	
}

function row_delete(no){
	if( confirm('삭제하시겠습니까?') ){
		$("#mode").val("delete");
		$("#no").val(no);
		$("#destination_form").submit();
	}else{
		return;
	}
}

function GoPage(block,gotopage) {
	document.destination_form.block.value=block;
	document.destination_form.gotopage.value=gotopage;
	document.destination_form.submit();
}

// 기존 수정
function modify(no){

	$('.layer-title').text('배송지 수정');
	$.ajax({
		type: "POST",
		url: "ajax_address_change.php",
		data: "no="+no,
		dataType:"JSON"
	}).done(function(data){
		$("#mode").val("modify");
		$("#no").val(no);
		$("#destination_name").val(data[0]['destination_name']);
		$("#get_name").val(data[0]['get_name']);
		// $("#mobile").val(data[0]['mobile']);
		$("#postcode").val(data[0]['postcode']);
		$("#postcode_new").val(data[0]['postcode_new']);
		$("#addr1").val(data[0]['addr1']);
		$("#addr2").val(data[0]['addr2']);
		$("#btnSubmit").text("수정");
		//$("#submit_type").text("수정");
		
		if(data[0]['base_chk'] == "Y"){
			$("#base_chk").attr("checked", true);
		}else{
			$("#base_chk").attr("checked", false);
		}

		var mobile = data[0]['mobile'];
	 	var phones = mobile.split("-");
	 	if(phones[0] != null){
	 		$("#post_add_phone1").val(phones[0]).attr("selected", "selected");
	 	}
	 	if(phones[1] != null){
	 		$("#post_add_phone2").val(phones[1]);
	 	}
	 	if(phones[2] != null){
	 		$("#post_add_phone3").val(phones[2]);
	 	}
	});
}

</script>
<?php  include ($Dir."lib/bottom.php") ?>

