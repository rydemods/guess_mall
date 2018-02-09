<?
include_once('outline/header_m.php');

###################
if(strlen($_MShopInfo->getMemid())==0) {
	Header("Location:".$Dir."m/login.php?chUrl=".getUrl());
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_MShopInfo->getMemid()."' ";
$result=pmysql_query($sql,get_db_conn());
if($row=pmysql_fetch_object($result)) {
	$_mdata=$row;
	if($row->member_out=="Y") {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('회원 아이디가 존재하지 않습니다.',$Dir."m/login.php");
	}

	if($row->authidkey!=$_MShopInfo->getAuthidkey()) {
		$_MShopInfo->SetMemNULL();
		$_MShopInfo->Save();
		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir."m/login.php");
	}
}
pmysql_free_result($result);
######################

//exdebug($_GET);
//exdebug($_POST);

$get_no             = $_GET["no"]?$_GET["no"]:0;
$mode               = $_POST["mode"];
$destination_name   = $_POST["destination_name"];
$get_name           = $_POST["get_name"];
$mobile             = str_replace("-", "", $_POST["mobile"]);
$postcode           = $_POST["postcode"];
$postcode_new       = $_POST["postcode_new"];
$addr1              = $_POST["addr1"];
$addr2              = $_POST["addr2"];
$base_chk           = $_POST["chk"];

if($mode == "modify") {

	#수정
	$no = $_POST['no'];
	
	if($base_chk == "Y" ){
		#기본 배송지로 등록되어 있는 no 조회
		$chkY = "SELECT no FROM tbldestination WHERE mem_id = '".$_MShopInfo->getMemid()."'
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
		alert_go('수정이 완료되었습니다.', "./address_change.php");
	}else{
		alert_go('오류가 발생하였습니다.', "./address_change.php");
	}
} elseif($mode == "insert") {

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
		alert_go('등록이 완료되었습니다.', "./address_change.php");
	}else{
		alert_go('오류가 발생하였습니다.', "./address_change.php");
	}

} elseif($mode == "delete") {

	#삭제
	$no = $_POST['no'];
	$dSql = "DELETE FROM tbldestination WHERE no = '".$no."'"; 
	pmysql_query($dSql, get_db_conn());
	
	if(!pmysql_error()){
		alert_go('삭제가 완료되었습니다.', "./address_change.php");
	}else{
		alert_go('오류가 발생하였습니다.', "./address_change.php");
	}
}

if($get_no) {
    $sql = "Select * from tbldestination where no = {$get_no} and mem_id = '".$_MShopInfo->getMemid()."'";
    $result = pmysql_query($sql);
    $row = pmysql_fetch_object($result);

    $checked[base]['Y'] = "checked";
} else {
}
?>

	<section class="top_title_wrap">
		<h2 class="page_local">
			<a href="javascript:history.back();" class="prev"></a>
			<span>배송지 관리</span>
			<a href="/m/shop.php" class="home"></a>
		</h2>
	</section>

	<div class="mypage_sub">
		<form name='destination_form' id='destination_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
        <input type='hidden' id='mode' name='mode'>
        <input type='hidden' id='chk' name='chk' value="N">
        <input type='hidden' id='no' name='no' value="<?=$get_no?>">
		<div class="order_table">
			<label class="check_def"><input type="checkbox" id="base_chk" class="checkbox_custom" <?=$checked[base][$row->base_chk]?>>기본 배송지 사용</label>
			<table class="my-th-left form_table">
				<colgroup>
					<col style="width:30%;">
					<col style="width:70%;">
				</colgroup>
				<tbody>
					<tr>
						<th>배송지 명</th>
						<td><input type="text" class="required_value" name="destination_name" id="destination_name"  placeholder="배송지 명" title="배송지 명 입력자리" label = "배송지 명" maxlength="20" value="<?=$row->destination_name?>"></td>
					</tr>
					<tr>
						<th>받는 사람</th>
						<td><input type="text" class="required_value" name="get_name" id="get_name" placeholder="이름" title="이름 입력자리" label = "받는사람" maxlength="20" value="<?=$row->get_name?>"></td>
					</tr>
					<tr>
						<th>휴대전화</th>
						<td>
							<div class="tel-input">
								<!-- <div class="tel_select">
									<select name="receiver_tel21" id="receiver_tel21" class="select_def">
										<option value="010" >010</option>
										<option value="011" >011</option>
										<option value="016" >016</option>
										<option value="017" >017</option>
										<option value="018" >018</option>
										<option value="019" >019</option>
									</select>
								</div>
								<div><input type="tel" name="receiver_tel22" id='receiver_tel22' maxlength='4' ></div>
								<div><input type="tel" name="receiver_tel23" id='receiver_tel23' maxlength='4' ></div> -->
                                <input type="text" class="chk_only_number required_value" name="mobile" id="mobile" placeholder="하이픈(-) 없이 입력" title="휴대폰 입력자리" label = "휴대폰" maxlength="20" style="ime-mode:disabled;"  value="<?=$row->mobile?>">
							</div>
						</td>
					</tr>
					<tr>
						<th>주소</th>
						<td>
                            <div class="addr_post">
								<input type="tel" id="postcode_new" name='postcode_new' class="required_value" label = "우편번호" value="<?=$row->postcode_new?>"><input type='hidden' id='postcode' name='postcode' value="<?=$row->postcode?>"><a href="javascript:openDaumPostcode();" class="btn-def">주소찾기</a>
								<div id="addressWrap" style="display:none;position:fixed;overflow:hidden;z-index:9999;-webkit-overflow-scrolling:touch;">
								<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;width:20px;right:0px;top:-1px;z-index:9999" onclick="foldDaumPostcode()" alt="접기 버튼">
								</div>
							</div>
                            <input type="text" class="addr required_value" name = 'addr1' id = 'addr1' label = "주소" value="<?=$row->addr1?>">
                            <input type="text" class="addr" name = 'addr2' id = 'addr2' value="<?=$row->addr2?>">
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- //.order_table -->
        </form>
<?
if($get_no) {
?>
		<div class="btnwrap">
			<ul class="ea2">
				<li><a href="#" class="btn-def" onclick="row_delete(<?=$get_no?>);">삭제</a></li>
				<li><button type="button" class="btn-point" id="btnSubmit2">저장</button></li>
			</ul>
		</div><!-- [D] 수정인 경우 -->
<?
} else {
?>
		<div class="btnwrap">
			<ul class="ea1">
				<li><button type="button" class="btn-point" id="btnSubmit2">저장</button></li>
			</ul>
		</div><!-- [D] 추가인 경우 -->
<?
}
?>
	</div><!-- //.mypage_sub -->

<script Language="JavaScript">
$(document).ready(function (){
	$("#btnSubmit2").click(function(){
    
        var no = <?=$get_no?>;
		if(check_form()){
			if(no > 0){
				$("#mode").val("modify");
			} else {
                $("#mode").val("insert");
            }
			$("#destination_form").submit();
		}
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

	if(procSubmit){
		return true;
	}else{
		return false;
	}
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
</script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script>
    // 우편번호 찾기 찾기 화면을 넣을 element
    var element_layer = document.getElementById('addressWrap');

    function foldDaumPostcode() {
        // iframe을 넣은 element를 안보이게 한다.
        element_layer.style.display = 'none';
    }

    function openDaumPostcode() {
        // 현재 scroll 위치를 저장해놓는다.
        var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
        new daum.Postcode({
            oncomplete: function(data) {
                // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullAddr = data.address; // 최종 주소 변수
                var extraAddr = ''; // 조합형 주소 변수

                // 기본 주소가 도로명 타입일때 조합한다.
                if(data.addressType === 'R'){
                    //법정동명이 있을 경우 추가한다.
                    if(data.bname !== ''){
                        extraAddr += data.bname;
                    }
                    // 건물명이 있을 경우 추가한다.
                    if(data.buildingName !== ''){
                        extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                    fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('postcode_new').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('addr1').value = fullAddr;
                document.getElementById('addr2').value = "";
	 			document.getElementById('addr2').focus();

                // iframe을 넣은 element를 안보이게 한다.
                // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
                element_layer.style.display = 'none';

                // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
                document.body.scrollTop = currentScroll;
            },
            // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
            onresize : function(size) {
            		//console.log("Size:", size, element_layer)
                //element_layer.style.height = size.height+'px';
            },
            width : '100%',
            height : '100%'
        }).embed(element_layer);

        // iframe을 넣은 element를 보이게 한다.
        element_layer.style.display = 'block';

        // iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
        initLayerPosition();
    }

    // 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
    // resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
    // 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
    function initLayerPosition(){
        var width = (window.innerWidth || document.documentElement.clientWidth)-20; //우편번호서비스가 들어갈 element의 width
        var height = (window.innerHeight || document.documentElement.clientHeight)-200; //우편번호서비스가 들어갈 element의 height
        var borderWidth = 1; //샘플에서 사용하는 border의 두께

        // 위에서 선언한 값들을 실제 element에 넣는다.
        element_layer.style.width = width + 'px';
        element_layer.style.height = height + 'px';
        element_layer.style.border = borderWidth + 'px solid';
        // 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
        element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
        element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
    }
</script>

<? include_once('outline/footer_m.php'); ?>
