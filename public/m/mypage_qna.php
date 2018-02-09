<?php
include_once('outline/header_m.php');

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.MDir."login.php?chUrl=".getUrl());
	exit;
}

#####날짜 셋팅 부분
$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

$type_menu=$_POST["type_menu"];

$mode = $_POST["mode"];
$idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];
$email = $_POST["email"];
$subjectl = pg_escape_string($_POST["pop_subject"]);
$content = pg_escape_string($_POST["pop_content"]);
$content = str_replace("\r\n","<br/>",$content); //줄바꿈 처리
$hp = $_POST["hp"];

$openyn = $_POST["openyn"];
$day_division = $_POST["day_division"];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$etime=strtotime("$e_year-$e_month-$e_day");

$stime=strtotime("$e_year-$e_month-$e_day -1 month");
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$formatDate1 = date("Y-m-d",strtotime("$s_year-$s_month-$s_day"));
$formatDate2 = date("Y-m-d",$etime);

$strDate1 = date("YmdHis",strtotime("$s_year-$s_month-$s_day"));
$strDate2 = date("Ymd235959",$etime);
$today = time();

if($mode == "modify"){
	$where[]="subject='".$subjectl."'";
	$where[]="\"HP\"='".$hp."'";
	$where[]="content='".$content."'";
	$where[]="email='".$email."'";
	$where[]="open_yn='".$openyn."'";

	$usql = "UPDATE tblboard SET ";
	$usql.= implode(", ",$where);
	$usql.=" WHERE num = '".$idx."'";

	pmysql_query( $usql, get_db_conn() );

	if(!pmysql_error()){
		echo  "<script>alert('수정이 완료되었습니다.'); location.href=\"/m/mypage_qna.php\"</script>";
	}else{
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/m/mypage_qna.php\"</script>";
	}
} else if ($mode == "delete"){

	#데이터삭제
	$dSql = "DELETE FROM tblboard WHERE num = '".$idx."'";

	pmysql_query($dSql, get_db_conn());

	if(!pmysql_error()){
		echo  "<script>alert('삭제가 완료되었습니다..'); location.href=\"/m/mypage_qna.php\"</script>";
	}else{
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/m/mypage_qna.php\"</script>";
	}
}

// $where 재설정
$where = "";
if($type_menu == '0' || $type_menu ==''){
	$where = " AND a.total_comment = 0 ";
} else {
	$where = " AND a.total_comment = 1 ";
}

$list_sql = " SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage, b.date, bc.writetime as bc_write,bc.comment
        FROM tblboard a LEFT OUTER JOIN tblproduct b ON a.pridx=b.pridx
        LEFT OUTER JOIN (select parent, MIN(writetime) as writetime , comment from tblboardcomment group by parent,comment) bc ON ( a.num = bc.parent )
        WHERE 1=1 AND a.board='qna' AND a.mem_id = '".$_ShopInfo->getMemid()."' AND b.date >= '".$strDate1."' AND b.date <= '".$strDate2."'
        ".$where."
        ORDER BY a.thread ,a.pos
		";

# 페이징
$paging = new New_Templet_paging($list_sql, 5,  5, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql( $list_sql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$list[] = $row;
}

?>

<!-- 내용 -->
<main id="content" class="subpage">
	<!-- Q&A작성 팝업 -->
	<section class="pop_layer layer_qna_write">
		<div class="inner">
			<h3 class="title">Q&amp;A작성<button type="button" class="btn_close">닫기</button></h3>
			<form name='write_form' id='write_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
			<input type='hidden' id='mode' name='mode' value="<?=$_GET["mode"] ?>">
			<input type='hidden' id='idx' name='idx' value="<?=$_GET["idx"] ?>">
			<input type='hidden' id="ph" name='hp' value="">
			<input type='hidden' id="email" name='email' value="">
			<!-- <input type='hidden' id="openyn" name="openyn" value=""> -->
			
			<div class="board_type_write">
				<dl>
					<dt>제목</dt>
					<dd>
						<input type="text" class="w100-per required_value" placeholder="제목 입력(필수)" label="문의제목" title="제목 입력" name="pop_subject" id="pop_subject">
					</dd>
				</dl>
				<dl>
					<dt>내용</dt>
					<dd>
						<textarea class="w100-per required_value" rows="6" placeholder="내용 입력(필수)" name="pop_content" label="문의내용" id="my_qna_textarea"></textarea>
					</dd>
				</dl>
				<dl>
					<dt>답변받을 이메일</dt>
					<dd>
						<div class="input_mail">
							<input type="text" class="" id="contact_email">
							<span class="at">&#64;</span>
							<select class="select_line" onchange="EmailChange()" id="email_select">
								<option value="@naver.com">naver.com</option>
								<option value="@daum.net">daum.net</option>
								<option value="@gmail.com">gmail.com</option>
								<option value="@nate.com">nate.com</option>
								<option value="@yahoo.co.kr">yahoo.co.kr</option>
								<option value="@lycos.co.kr">lycos.co.kr</option>
								<option value="@empas.com">empas.com</option>
								<option value="@hotmail.com">hotmail.com</option>
								<option value="@msn.com">msn.com</option>
								<option value="@hanmir.com">hanmir.com</option>
								<option value="@chol.net">chol.net</option>
								<option value="@korea.com">korea.com</option>
								<option value="@netsgo.com">netsgo.com</option>
								<option value="@dreamwiz.com">dreamwiz.com</option>
								<option value="@hanafos.com">hanafos.com</option>
								<option value="@freechal.com">freechal.com</option>
								<option value="@hitel.net">hitel.net</option>
								<option value="">직접입력</option>
							</select>
						</div>
						<input type="text" class="w100-per mt-5" placeholder="직접입력" id="email2" readonly>
					</dd>
				</dl>
				<dl>
					<dt>휴대폰 번호</dt>
					<dd>
						<div class="input_tel">
							<select class="select_line" id="phone_num1">
								<option value="010">010</option>
								<option value="011">011</option>
								<option value="016">016</option>
								<option value="017">017</option>
								<option value="018">018</option>
								<option value="019">019</option>
							</select>
							<span class="dash"></span>
							<input type="tel" maxlength="4" id="phone_num2">
							<span class="dash"></span>
							<input type="tel" maxlength="4" id="phone_num3">
						</div>
					</dd>
				</dl>
				<dl>
					<dt>공개여부</dt>
					<dd>
						<label>
							<input type="radio" class="radio_def" name="openyn" value="Y" checked>
							<span>공개</span>
						</label>
						<label class="ml-25">
							<input type="radio" class="radio_def" name="openyn" value="N">
							<span>비공개</span>
						</label>
					</dd>
				</dl>
				
				<div class="btn_area">
					<ul class="ea2">
						<li><a href="javascript:;" class="btn-line h-large" id="btnCancel">취소</a></li>
						<li><a href="javascript:;" class="btn-point h-large" id="btnSubmit">등록</a></li>
					</ul>
				</div>
			</div>
			</form>
		</div>
	</section>
	<!-- //Q&A작성 팝업 -->

	<section class="page_local">
		<h2 class="page_title">
			<a href="javascript:history.back();" class="prev">이전페이지</a>
			<span>상품문의</span>
		</h2>
	</section><!-- //.page_local -->

	<section class="mypage_qna sub_bdtop">

		<div class="tab_type1 mt-15" data-ui="TabMenu">
		<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
			<input type="hidden" name="date1">
			<input type="hidden" name="date2">
			<div class="tab-menu clear mb-20">
			<?
				if($type_menu == '0' || $type_menu ==''){
			?>
				<a data-content="menu" class="active" title="선택됨" onClick="javascript:CheckTopMenuForm('0');">답변대기</a>
				<a data-content="menu" onClick="javascript:CheckTopMenuForm('1');">답변완료</a>
			<?
				} else {
			?>
				<a data-content="menu" onClick="javascript:CheckTopMenuForm('0');">답변대기</a>
				<a data-content="menu" class="active" title="선택됨" onClick="javascript:CheckTopMenuForm('1');">답변완료</a>
			<?
				}
			?>
			</div>

			<!-- 답변대기 -->
			<div class="tab-content active" data-content="content">
				<div class="check_period">
					<ul>
						<!-- <li ><a href="javascript:;">1개월</a></li>
						<li class="on"><a href="javascript:;">3개월</a></li>
						<li><a href="javascript:;">6개월</a></li>
						<li><a href="javascript:;">12개월</a></li> -->
				<?php 
					if(!$day_division) $day_division = '1MONTH';
					foreach($arrSearchDate as $kk => $vv){
						$dayClassName = "";
						if($day_division != $kk){
							$dayClassName = '';
						}else{
							$dayClassName = 'class="on"';
						}
						//echo "<li ".$dayClassName."><a href=\"javascript:GoSearch2('".$kk."', this);\" >".$vv."</a></li>\n";
				?>
						<li <?=$dayClassName?>><a href="javascript:GoSearch2('<?=$kk?>', this);" ><?=$vv?></a></li>
						<!-- <button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button> -->
				<?php
					}
				?>
					</ul>
				</div><!-- //.check_period -->
			</form>
				<div class="review_list qna_list">
					<ul class="accordion_list">
				<?
					if( count($list) > 0 ) {
						$cnt=0;
						foreach( $list as $key=>$val ){
							$number = ( $t_count - ( 5 * ( $gotopage - 1 ) ) - $cnt++ );
							$ord_date	= substr($val['date'],0,4)."-".substr($val['date'],4,2)."-".substr($val['date'],6,2);
							
							$temp_btns = "";
							$temp_answer = "";
							if($type_menu == '0' || $type_menu ==''){
								$temp_btns .= "<div class=\"btns\">\n";
								$temp_btns .= "<a href=\"javascript:editForm('".$val['num']."','tblboard');\" class=\"btn_qna_write btn-line\" >수정</a>\n";
								$temp_btns .= "<a href=\"javascript:deleteForm('".$val['num']."');\" class=\"btn-basic\">삭제</a>\n";
								$temp_btns .= "</div>\n";
							} else {
								$temp_answer .= "<div class=\"answer accordion_con\">";
								$temp_answer .= "<p class=\"writer\"><span>관리자</span><span class=\"a_date\">".date("Y.m.d", strtotime( $val['date']) )."</span></p>";
								$temp_answer .= "<p class=\"txt\">".$val['comment']."</p>";
								$temp_answer .= "</div>";
							}
							
				?>	
						<li>
							<p class="date">작성일 <?=$ord_date?><span class="private"><?php if($val['open_yn'] == 'Y'){?>공개<?php }else{?>비공개<?php }?></span></p><!-- <p class="date">작성일 2017-01-18<span class="private">비공개</span></p> -->
							<div class="cart_wrap">
								<div class="goods_area">
									<div class="img"><a href="productdetail.php?productcode=<?=$val['productcode']?>"><img src="<?=$val['tinyimage']?>" alt="상품 이미지"></a></div>
									<div class="info">
										<p class="brand"><?=$val['production']?></p>
										<p class="name"><?=$val['productname']?></p>
									</div>
									<?php echo $temp_btns;?>
									<!-- <div class="btns">
										<a href="javascript:;" class="btn_qna_write btn-line">수정</a>
										<a href="javascript:;" class="btn-basic">삭제</a>
									</div> -->
								</div>
							</div>
							<div class="qna_con">
								<div class="question accordion_btn">
									<p class="tit"><?=$val['title']?></p>
									<p class="txt accordion_con"><?=$val['content']?> </p>
								</div>
								<?php echo $temp_answer;?>
							</div>
						</li>
						<?} ?>
				<?}else{ ?>	
						<li>내용이 없습니다.</li>
				<?} ?>
					</ul>
				</div><!-- //.review_list -->
				
				<div class="list-paginate mt-15">
				<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
				</div><!-- //.list-paginate -->
			</div>

	</section><!-- //.mypage_point -->

</main>
<!-- //내용 -->

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=s_year value="<?=$s_year?>">
<input type=hidden name=s_month value="<?=$s_month?>">
<input type=hidden name=s_day value="<?=$s_day?>">
<input type=hidden name=e_year value="<?=$e_year?>">
<input type=hidden name=e_month value="<?=$e_month?>">
<input type=hidden name=e_day value="<?=$e_day?>">
<input type=hidden name=day_division value="<?=$day_division?>">
<input type=hidden name=type_menu value="<?=$type_menu ?>">
</form>

<script Language="JavaScript">
var NowYear=parseInt(<?=date('Y')?>);
var NowMonth=parseInt(<?=date('m')?>);
var NowDay=parseInt(<?=date('d')?>);
var NowTime=parseInt(<?=time()?>);

$(document).ready(function(){

	var up_email = '';
	var up_storetel = '';
	
	$("#subject").click(function(){
		var url = "../front/myqna_view.php";
		$(location).attr('href',url);
	});

	$("input[name='date1'], input[name='date2']").click(function(){
		Calendar(event);
	});
	
	$("#btnSubmit").click(function(){
		if(check_form()){
			if($("#mode").val() == ""){
				$("#mode").val("insert");
			}
			EmailChange ();
			document.getElementById("ph").value = document.getElementById("phone_num1").value +"-"+document.getElementById("phone_num2").value +"-"+document.getElementById("phone_num3").value;
			$("#write_form").submit();
		}
	});
	$("#btnCancel").click(function(){
		window.location.reload();
	});

	//파일첨부 파일명 설정
    $("#up_filename").change(function(){
        var filename = $("#up_filename").val();
		$(".txt-box").text(filename);
		$("input[name=ori_filename]").val(getFilename(filename));
    });
	// 20170404 1:1 벨리데이션 체크 끝   
	 
});

//상세페이지
function go_view(idx){
	var url = "../m/mypage_personalview.php?idx="+idx;
	$(location).attr('href',url);
}

function getMonthDays(sYear,sMonth) {
	var Months_day = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
	var intThisYear = new Number(), intThisMonth = new Number();
	datToday = new Date();													// 현재 날자 설정
	
	intThisYear = parseInt(sYear);
	intThisMonth = parseInt(sMonth);


	
	if (intThisYear == 0) intThisYear = datToday.getFullYear();				// 값이 없을 경우
	if (intThisMonth == 0) intThisMonth = parseInt(datToday.getMonth())+1;	// 월 값은 실제값 보다 -1 한 값이 돼돌려 진다.
	

	if ((intThisYear % 4)==0) {													// 4년마다 1번이면 (사로나누어 떨어지면)
		if ((intThisYear % 100) == 0) {
			if ((intThisYear % 400) == 0) {
				Months_day[2] = 29;
			}
		} else {
			Months_day[2] = 29;
		}
	}
	intLastDay = Months_day[intThisMonth];										// 마지막 일자 구함
	return intLastDay;
}

function ChangeDate(gbn) {
	year=document.form1[gbn+"_year"].value;
	month=document.form1[gbn+"_month"].value;
	totdays=getMonthDays(year,month);

	MakeDaySelect(gbn,1,totdays);
}

function MakeDaySelect(gbn,intday,totdays) {
	document.form1[gbn+"_day"].options.length=totdays;
	for(i=1;i<=totdays;i++) {
		var d = new Option(i);
		document.form1[gbn+"_day"].options[i] = d;
		document.form1[gbn+"_day"].options[i].value = i;
	}
	document.form1[gbn+"_day"].selectedIndex=intday;
}

function GoSearch2(gbn, obj) {
	var s_date = new Date(NowTime*1000);
	switch(gbn) {
		case "TODAY":
			break;
		case "1WEEK":
			s_date.setDate(s_date.getDate()-7);
			break;
		case "15DAY":
			s_date.setDate(s_date.getDate()-15);
			break;
		case "1MONTH":
			s_date.setMonth(s_date.getMonth()-1);
			break;
		case "3MONTH":
			s_date.setMonth(s_date.getMonth()-3);
			break;
		case "6MONTH":
			s_date.setMonth(s_date.getMonth()-6);
			break;
		case "9MONTH":
			s_date.setMonth(s_date.getMonth()-9);
			break;
		case "12MONTH":
			s_date.setFullYear(s_date.getFullYear()-1);
			break;
		default :
			break;
	}
	e_date = new Date(NowTime*1000);

	//======== 시작 날짜 셋팅 =========//
	var s_month_str = str_pad_right(parseInt(s_date.getMonth())+1);
	var s_date_str = str_pad_right(parseInt(s_date.getDate()));
	
	// 폼에 셋팅
	document.form2.s_year.value = s_date.getFullYear();
	document.form2.s_month.value = s_month_str;
	document.form2.s_day.value = s_date_str;
	//날짜 칸에 셋팅
	var s_date_full = s_date.getFullYear()+"-"+s_month_str+"-"+s_date_str;
	document.form1.date1.value=s_date_full;
	//======== //시작 날짜 셋팅 =========//
	
	//======== 끝 날짜 셋팅 =========//
	var e_month_str = str_pad_right(parseInt(e_date.getMonth())+1);
	var e_date_str = str_pad_right(parseInt(e_date.getDate()));

	// 폼에 셋팅
	document.form2.e_year.value = e_date.getFullYear();
	document.form2.e_month.value = e_month_str;
	document.form2.e_day.value = e_date_str;

	document.form2.day_division.value = gbn;
	
	//날짜 칸에 셋팅
	var e_date_full = e_date.getFullYear()+"-"+e_month_str+"-"+e_date_str;
	document.form1.date2.value=e_date_full;
	//======== //끝 날짜 셋팅 =========//
	
	/*
	document.form1.s_year.value=parseInt(s_date.getFullYear());
	document.form1.s_month.value=parseInt(s_date.getMonth());
	document.form1.e_year.value=NowYear;
	document.form1.e_month.value=NowMonth;
	totdays=getMonthDays(parseInt(s_date.getFullYear()),parseInt(s_date.getMonth()));
	MakeDaySelect("s",parseInt(s_date.getDate()),totdays);
	totdays=getMonthDays(NowYear,NowMonth);
	MakeDaySelect("e",NowDay,totdays);
	document.form1.submit();
	*/
	CheckForm();
}

function str_pad_right(num){
	
	var str = "";
	if(num<10){
		str = "0"+num;
	}else{
		str = num;
	}
	return str;

}

function isNull(obj){
	return (typeof obj !="undefined" && obj != "")?false:true;
}

// top 메뉴 추가 
function CheckTopMenuForm(obj) {
	document.form2.type_menu.value = obj; 
	document.form2.gotopage.value = 1; 
	CheckForm();
}


function CheckForm() {

	//##### 시작날짜 셋팅
	var sdatearr = "";
	var str_sdate = document.form1.date1.value;
	if(!isNull(document.form1.date1.value)){
		sdatearr = str_sdate.split("-");
		if(sdatearr.length==3){
		// 폼에 셋팅
			document.form2.s_year.value = sdatearr[0];
			document.form2.s_month.value = sdatearr[1];
			document.form2.s_day.value = sdatearr[2];
		}
	}
	var s_date = new Date(parseInt(sdatearr[0]),parseInt(sdatearr[1]),parseInt(sdatearr[2]));
	
	//##### 끝 날짜 셋팅
	var edatearr = "";
	var str_edate = document.form1.date2.value;
	if(!isNull(document.form1.date2.value)){
		edatearr = str_edate.split("-");
		if(edatearr.length==3){
		// 폼에 셋팅
			document.form2.e_year.value = edatearr[0];
			document.form2.e_month.value = edatearr[1];
			document.form2.e_day.value = edatearr[2];
		}
	}
	var e_date = new Date(parseInt(edatearr[0]),parseInt(edatearr[1]),parseInt(edatearr[2]));

	if(s_date>e_date) {
		alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
		return;
	}
	
	document.form2.submit();
}

function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}


// 20170404 1:1 입력 팝업창 
//파일명 추출
function getFilename(filename) {

	var fileValue = filename.split("\\");
	var fileName = fileValue[fileValue.length-1]; // 파일명

	return fileName;
}

function check_form() {
	var procSubmit = true;

	$(".required_value").each(function(){
		if(!$(this).val()){
			if($(this).attr('label') == ""){
				alert($(this).attr('label')+"를 정확히 입력해 주세요");
			}	
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
// 20170404 팝업창 끝

// 벨리데이션 체크 [직접입력] 시 입력가능
function EmailChange (){
	var email_select = document.getElementById("email_select").value;
	if(email_select == ''){
		document.getElementById("email").value = document.getElementById("contact_email").value +"@"+document.getElementById("email2").value;
		document.getElementById("email2").readOnly = false;
	} else {
		document.getElementById("email").value = document.getElementById("contact_email").value +document.getElementById("email_select").value;
		document.getElementById("email2").readOnly = true;
	}
}

// 20170404 삭제폼
function deleteForm(obj){
	var con_test = confirm("삭제 하시겠습니까?");
	if(con_test == true){ 
		$("#mode").val("delete");
		$("#idx").val(obj);
		$("#write_form").submit();
	}
}

// 수정폼
function editForm(idx,table) {

	$("#mode").val("modify");
	$("#idx").val(idx);

	$.ajax({
		type: "POST",
		url: "../front/ajax_common_change.php",
		data: "idx="+idx+"&table="+table,
		dataType:"JSON"
	}).done(function(data){
		$("#pop_subject").val(data[0]['subject']);
		var content = data[0]['content'].replace(/<br[^>]*>/gi,"\n");	// 줄바꿈처리
		$("#my_qna_textarea").val(content);
		var emails = data[0]['email'].split("@");

		if(emails[0] != null){
	 		$("#contact_email").val(emails[0]);
	 	}
		
		if(emails[1] != null){
			var temp_mail = '';
			var arr_mail = new Array( 'naver.com', 'daum.net', 'gmail.com','nate.com', 'yahoo.co.kr', 'lycos.co.kr','empas.com', 'hotmail.com', 'gmail.com','hanmir.com','chol.net','korea.com','netsgo.com','dreamwiz.com','hanafos.com','freechal.com' );
			for ( var i = 0; i < arr_mail.length; i++ ) {
		        if(arr_mail[i] === emails[1]){
			        temp_mail = emails[1];
		        } 
	      	}
			
	      	if(temp_mail == ''){
	      		$("#email_select").val(temp_mail).attr("selected", "selected");
	      		$("#email2").val(temp_mail);
	      		document.getElementById("email2").readOnly = false;
	      	} else {
				$("#email_select").val("@"+temp_mail).attr("selected", "selected");
	      	}
		}

		var phones = data[0]['phone'].split("-");
	 	if(phones[0] != null){
	 		var res = phones[0].substr(0, 3);
	 		$("#phone_num1").val(res).attr("selected", "selected");
	 	}
	 	if(phones[1] != null){
	 		$("#phone_num2").val(phones[1]+'');
	 	}
	 	if(phones[2] != null){
	 		$("#phone_num3").val(phones[2]+'');
	 	}
		var openyn = document.getElementsByName("openyn");
		if(data[0]['is_secret'] == "0"){
			openyn[0].checked = true;
			openyn[1].checked = false;
		} else {
			openyn[0].checked = false;
			openyn[1].checked = true;
		}
	 	//$("#openyn").val(data[0]['open_yn']);
	});
	$('#btnSubmit').text('수정');
 	$('.layer_qna_write').show();

}

$("#open_yn").change(function(){
    if($("#open_yn").is(":checked")){
    	$("#openyn").val("1");
    }else{
    	$("#openyn").val("0");
    }
});

</script>

<?php
include_once('outline/footer_m.php');
?>