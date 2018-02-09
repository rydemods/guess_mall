<?php
include_once('outline/header_m.php');

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
$paging = new New_Templet_paging($list_sql, 5,  5, 'GoPage', true);
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

<!-- 내용 -->
<main id="content" class="subpage">

	<form name='destination_form' id='destination_form' action="" method='POST' >
	<input type='hidden' id='mode' name='mode'>
	<input type='hidden' id='chk' name='chk' value="N">
	<input type='hidden' id='no' name='no'>
	<input type='hidden' id='mobile' name='mobile'>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<!-- 배송지 추가 팝업 -->
	<section class="pop_layer layer_add_deli">
		<div class="inner">
			<h3 class="title">배송지 추가 <button type="button" class="btn_close" id="btn_close">닫기</button></h3>

			<div class="board_type_write">
				<dl>
					<dt>
						<span class="required">배송지명</span>
						<label><input type="checkbox" class="check_def" id="base_chk" name="base_chk"> <span>기본 배송지로 설정</span></label>
					</dt>
					<dd><input type="text" class="w100-per required_value" label = "배송지 명" name="destination_name" id="destination_name"></dd>
				</dl>
				<dl>
					<dt><span class="required">받는사람</span></dt>
					<dd><input type="text" class="w100-per required_value" name="get_name" id="get_name" label = "받는사람"></dd>
				</dl>
				<dl>
					<dt><span class="required">휴대폰 번호</span></dt>
					<dd>
						<div class="input_tel">
							<select class="select_line" id="post_add_phone1"> 
								<option value="010" selected="">010</option>
								<option value="011">011</option>
								<option value="016">016</option>
								<option value="017">017</option>
								<option value="018">018</option>
								<option value="019">019</option>
							</select>
							<span class="dash"></span>
							<input type="tel" maxlength="4" id="post_add_phone2" class="required_value" label = "중간번호">
							<span class="dash"></span>
							<input type="tel" maxlength="4" id="post_add_phone3" class="required_value" label = "마지막 번호">
						</div>
					</dd>
				</dl>
				<dl>
					<dt><span class="required">주소</span></dt>
					<dd>
						<div class="input_addr">
							<input type="text" name="postcode_new" id="postcode_new"  class="w100-per required_value" placeholder="우편번호" label = "우편번호">
							<div class="btn_addr"><a href="javascript:search_zip();" class="btn-basic h-input">주소찾기</a></div>
						</div>
						<input type="text" class="w100-per mt-5 required_value" placeholder="기본주소" name="addr1" id="addr1" title="주소" label = "주소">
						<input type="text" class="w100-per mt-5" placeholder="상세주소" name="addr2" id="addr2">
						<input type="hidden" name="postcode" id="postcode" title="우편번호(구)" >
					</dd>
				</dl>
				<div class="btn_area mt-20">
					<ul>
						<li><a href="javascript:;" class="btn-point h-input" id="btnSubmit">저장</a></li>
					</ul>
				</div>
			</div><!-- //.board_type_write -->
		</div>
	</section><!-- //.layer_add_deli -->
	<!-- //배송지 추가 팝업 -->
	</form>

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>배송지 관리</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="my_deli_site sub_bdtop">
		<div class="btn_area">
			<ul>
				<li><button type="button" class="btn_add_deli btn-line h-input">배송지 추가</button></li>
			</ul>
		</div>

		<div class="list_deli">
			<ul>
<?
		if( count($list) > 0 ) {
			foreach( $list as $key=>$val ){
?>
				<li>
					<div class="info">
						<p class="tit"><?=$val['destination_name']?><?php if($val['base_chk'] == 'Y') echo "<span class=\"btn-point h-small\">기본</span>";?></p>
						<p class="tel"><?=addMobile($val['mobile'])?></p>
						<p class="addr"><?=$val['addr1']?> <?=$val['addr2']?></p>
					</div>
					<div class="btns">
						<a href="javascript:modify(<?=$val['no'] ?>);" class="btn_add_deli btn-line">수정</a>
						<a href="javascript:row_delete(<?=$val['no']?>);" class="btn-basic">삭제</a>
					</div>
				</li>
<?
			}
		} else {
?>
			<li>등록된 주소가 없습니다.</li>
<?
		}
?>
			</ul>
		</div><!-- //.list_deli -->

		<div class="list-paginate mt-15">
			<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
		</div><!-- //.list-paginate -->

	</section><!-- //.my_deli_site -->

</main>
<!-- //내용 -->

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
	$("#btn_close").click(function(){
		window.location.reload();
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
		url: "../front/ajax_address_change.php",
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

<?php
include_once('outline/footer_m.php');
?>