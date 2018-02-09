<?php 
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include($Dir."admin/calendar_join.php");

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
	exit;
}
// 기존 ========
// $sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
// $result=pmysql_query($sql,get_db_conn());
// if($row=pmysql_fetch_object($result)) {
// 	if($row->member_out=="Y") {
// 		$_ShopInfo->SetMemNULL();
// 		$_ShopInfo->Save();
// 		alert_go('회원 아이디가 존재하지 않습니다.',$Dir.FrontDir."login.php");
// 	}

//     /*
// 	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
// 		$_ShopInfo->SetMemNULL();
// 		$_ShopInfo->Save();
// 		alert_go('처음부터 다시 시작하시기 바랍니다.',$Dir.FrontDir."login.php");
// 	}
//     */
// }
// pmysql_free_result($result);

#####날짜 셋팅 부분
$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

// 상단 top 메뉴 추가
// $type_menu=$_POST["type_menu"];
// $idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];

// $mode = $_POST["mode"];
// $day_division = $_POST['day_division'];
// $limitpage = $_POST['limitpage'];

$type_menu=$_POST["type_menu"];

$mode = $_POST["mode"];
$idx = $_POST["idx"] ? $_POST["idx"] : $_GET["idx"];
$email = $_POST["email"];
$subjectl = pg_escape_string($_POST["pop_subject"]);
$content = pg_escape_string($_POST["pop_content"]);
$content = str_replace("\r\n","<br/>",$content); //줄바꿈 처리
$hp = $_POST["hp"];

$openyn = $_POST["openyn"];

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

// 상품문의 내역


if($mode == "modify"){
	$where[]="title='".$subjectl."'";
	$where[]="hp='".$hp."'";
	$where[]="content='".$content."'";
	$where[]="email='".$email."'";
	$where[]="is_secret='".$openyn."'";

	#첨부파일이 변경되면 기존에 있는 파일 삭제 & 새로운 파일 업데이트
// 	if ($data->ori_filename != $ori_filename) {
// 		$where[]="up_filename='".$up_filename."'";
// 		$where[]="ori_filename='".$ori_filename."'";
// 		$up_file->removeFile($data->up_filename);
// 	}

	$usql = "UPDATE tblboard SET ";
	$usql.= implode(", ",$where);
	$usql.=" WHERE num = '".$idx."'";
	
	//echo $usql;
	//exit();

	
	pmysql_query( $usql, get_db_conn() );

	if(!pmysql_error()){
		echo  "<script>alert('수정이 완료되었습니다.'); location.href=\"/front/mypage_qna.php\"</script>";
	}else{
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/front/mypage_qna.php\"</script>";
	}
} else if ($mode == "delete"){

	#데이터삭제
	$dSql = "DELETE FROM tblboard WHERE num = '".$idx."'";

	pmysql_query($dSql, get_db_conn());

	if(!pmysql_error()){
		echo  "<script>alert('삭제가 완료되었습니다..'); location.href=\"/front/mypage_qna.php\"</script>";
	}else{
		//alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		echo  "<script>alert('오류가 발생하였습니다.'); location.href=\"/front/mypage_qna.php\"</script>";
	}
}

// $where 재설정
$where = "";
if($type_menu == '0' || $type_menu ==''){
	$where = " AND a.total_comment = 0 ";
} else {
	$where .= " AND a.total_comment = 1 ";
}
/*
$list_sql = "SELECT
				T1.idx,
				T1.id,
				T1.name,
				T1.email,
				T1.\"HP\",
				T1.subject,
				T1.date,
				T1.content,
				T1.re_date,
				T1.re_content,
				T1.re_id,
				T1.head_title,
				T1.re_subject,
				T1.productcode,
				T1.up_filename,
				T1.ori_filename,
				T1.open_yn,
				T2.pridx,
				T2.productname,
				T2.sellprice,
				T2.consumerprice,
				T2.brand,
				T2.tinyimage,
				T2.vender,
				T2.production
			FROM tblpersonal T1 JOIN tblproduct T2 ON T1.productcode = T2.productcode AND id = '".$_ShopInfo->getMemid()."'
				AND T1.date >= '".$strDate1."' AND T1.date <= '".$strDate2."' ".$where." ORDER BY idx desc";
*/

$list_sql = " SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage, b.date, bc.writetime as bc_write,bc.comment
        FROM tblboard a LEFT OUTER JOIN tblproduct b ON a.pridx=b.pridx
        LEFT OUTER JOIN (select parent, MIN(writetime) as writetime , comment from tblboardcomment group by parent,comment) bc ON ( a.num = bc.parent )
        WHERE 1=1 AND a.board='qna' AND a.mem_id = '".$_ShopInfo->getMemid()."' AND b.date >= '".$strDate1."' AND b.date <= '".$strDate2."' 
        ".$where."
        ORDER BY a.thread ,a.pos
		";

# 페이징
$paging = new New_Templet_paging($list_sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql( $list_sql );
//exdebug($sql);
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$list[] = $row;
}

?>
<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<style>
/** 달력 팝업 **/
.calendar_pop_wrap {position:relative; background-color:#FFF;}
.calendar_pop_wrap .calendar_con {position:absolute; top:0px; left:0px;width:247px; padding:10px; border:1px solid #b8b8b8; background-color:#FFF;}
.calendar_pop_wrap .calendar_con .month_select { text-align:center; background-color:#FFF; padding-bottom:10px;}
.calendar_pop_wrap .calendar_con .day {clear:both;border-left:1px solid #e4e4e4;}
.calendar_pop_wrap .calendar_con .day th {background:url('../admin/img/common/calendar_top_bg.gif') repeat-x; width:34px; font-size:11px; border-top:1px solid #9d9d9d;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; padding:6px 0px 4px;}
.calendar_pop_wrap .calendar_con .day th.sun {color:#ff0012;}
.calendar_pop_wrap .calendar_con .day td {border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4; background-color:#FFF; width:34px;  font-size:11px; text-align:center; font-family:tahoma;}
.calendar_pop_wrap .calendar_con .day td a {color:#35353f; display:block; padding:2px 0px;}
.calendar_pop_wrap .calendar_con .day td a:hover {font-weight:bold; color:#ff6000; text-decoration:none;}
.calendar_pop_wrap .calendar_con .day td.pre_month a {color:#fff; display:block; padding:3px 0px;}
.calendar_pop_wrap .calendar_con .day td.pre_month a:hover {text-decoration:none; color:#fff;}
.calendar_pop_wrap .calendar_con .day td.today {background-color:#52a3e7; }
.calendar_pop_wrap .calendar_con .day td.today a {color:#fff;}
.calendar_pop_wrap .calendar_con .close_btn {text-align:center; padding-top:10px;}
</style>

<!-- 20170405 상품문의 -->
<div id="contents">
	<div class="mypage-page">

		<h2 class="page-title">상품문의</h2>

		<div class="inner-align page-frm clear">

			<!-- LNB -->
			<? include  "mypage_TEM01_left.php";  ?>
			<!-- //LNB -->
			<article class="my-content">
				
				<section data-ui="TabMenu">
					<form name="form1" action="<?=$_SERVER['PHP_SELF']?>">
					<div class="tabs top"> 
						<!-- <button type="button" data-content="menu" class="active"><span>답변대기</span></button>
						<button type="button" data-content="menu"><span>답변완료</span></button> -->
					<?
						if($type_menu == '0' || $type_menu ==''){
					?>
						<button type="button" data-content="menu" class="active" onClick="javascript:CheckTopMenuForm('0');"><span>답변대기</span></button>
						<button type="button" data-content="menu" onClick="javascript:CheckTopMenuForm('1');"><span>답변완료</span></button>
					<?
						} else {
					?>
						<button type="button" data-content="menu" onClick="javascript:CheckTopMenuForm('0');"><span>답변대기</span></button>
						<button type="button" data-content="menu" class="active" onClick="javascript:CheckTopMenuForm('1');"><span>답변완료</span></button>
					<?
						}
					?>
					</div>
					<header class="my-title mt-40">
						<h3 class="fz-0">상품문의</h3>
						<div class="count">전체 <strong><?=$t_count?></strong></div>
						<div class="date-sort clear">
							<div class="type month">
								<p class="title">기간별 조회</p>
								<!-- <button type="button" class="on"><span>1개월</span></button>
								<button type="button"><span>3개월</span></button>
								<button type="button"><span>6개월</span></button>
								<button type="button"><span>12개월</span></button> -->
								<?
									if(!$day_division) $day_division = '1MONTH';
								?>
								<?foreach($arrSearchDate as $kk => $vv){?>
									<?
										$dayClassName = "";
										if($day_division != $kk){
											$dayClassName = '';
										}else{
											$dayClassName = 'on';
										}
									?>
									<button type="button" class="<?=$dayClassName?>" onClick = "GoSearch2('<?=$kk?>', this)"><span><?=$vv?></span></button>
								<?}?>
							</div>
							<div class="type calendar">
									<p class="title">일자별 조회</p>
									<div class="box">
										<div><input type="text" title="일자별 시작날짜" name="date1" id="" value="<?=$formatDate1?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
									</div>
									<span class="dash"></span>
									<div class="box">
										<div><input type="text" title="일자별 시작날짜" name="date2" id="" value="<?=$formatDate2?>" readonly></div>
										<button type="button" class="btn_calen CLS_cal_btn">달력 열기</button>
									</div>
								</div>
							<button type="button" class="btn-point" onClick="javascript:CheckForm();"><span>검색</span></button>
						</div>
					</header>
					</form>
					<div data-content="content" class="active">
						<table class="th-top table-toggle">
							<caption>답변대기 목록</caption>
							<colgroup>
								<col style="width:100px">
								<col style="width:auto">
								<col style="width:250px">
								<col style="width:90px">
								<col style="width:105px">
							</colgroup>
							<thead>
								<tr>
									<th scope="col">작성일</th>
									<th scope="col">상품정보</th>
									<th scope="col">제목</th>
									<th scope="col">공개여부</th>
									<th scope="col">상태</th>
								</tr>
							</thead>
							<tbody class="tbody_subject">
				<?
					if( count($list) > 0 ) {
						$cnt=0;
						//exdebug($list);
						foreach( $list as $key=>$val ){
							$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt++ );
							$ord_date	= substr($val['date'],0,4)."-".substr($val['date'],4,2)."-".substr($val['date'],6,2);
				?>
								<tr>
									<td class="txt-toneB"><?=$ord_date?></td>
									<td class="pl-25">
										<div class="goods-in-td">
											<div class="thumb-img"><a href="../front/productdetail.php?productcode=<?=$val['productcode']?>"><img src="<?=$val['tinyimage']?>" alt=""></a></div>
											<div class="info">
												<p class="brand-nm"><?=$val['production']?></p>
												<p class="goods-nm"><?=$val['productname']?></p>
											</div>
										</div>
									</td>
									<td class="subject"><a href="javascript:;" class="menu ellipsis w250 fw-bold"><?=$val['title']?></a></td>
									<?
										if($val['is_secret'] == '0'){
									?>
										<td class="txt-toneB">공개</td>
									<?
										} else {
									?>
										<td class="txt-toneB">비공개</td>
									<?
										}
									?>
									<?
										if($type_menu == '0' || $type_menu ==''){
									?>
										<td class="txt-toneB">답변대기</td>
									<?
										} else {
									?>
										<td class="txt-toneB">답변완료</td>
									<?
										}
									?>
								</tr>
								
								<tr class="hide">
									<td class="reset" colspan="5">
										<div class="answer-box">
								<?
									if($type_menu == '0' || $type_menu ==''){
								?>
											<div class="question editor-output">
												<div class="btn">
													<!-- <button class="btn-basic" type="button"><span>수정</span></button>
													<button class="btn-line" type="button"><span>삭제</span></button> -->
													<button class="btn-basic btn-my-qnaWrite" type="button" id="btn-my-qnaWrite" onclick="editForm('<?=$val['num'] ?>','tblboard')"><span>수정</span></button>
													<button class="btn-line" type="button" onclick="deleteForm('<?=$val['num'] ?>')"><span>삭제</span></button>
												</div>
												<?=$val['content']?><br>
											</div>
								<?
									} else {
								?>
										<div class="question editor-output">
										<?=$val['content']?><br>
									<?
										if($val['up_filename'] != ''){
									?>
										<p><img src="<?=$filepath.$val['up_filename'] ?>" alt=""></p>
									<?
										}
									?>
										</div>
										<div class="answer editor-output">
											<div class="answer-user"><span>관리자 <em>| </em><?=date("Y.m.d", strtotime( $val['date']) )?></span></div>
											<!-- 
											<p><?=$val['re_subject']?></p>
											 -->
											<p><?=$val['comment']?></p>
										</div>
								<?
									}
								?>
										</div>
									</td>
								</tr>
								<?} ?>
							<?}else{ ?>	
								<tr>
									<td colspan="5">등록된 문의가 없습니다.</td>
								</tr>
							<?} ?>
							</tbody>
						</table>
						<div class="list-paginate mt-20">
							<?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?>
						</div>
					</div>
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- //상세 > Q&A 작성 -->
<!-- <div class="layer-dimm-wrap goodsQna-write"> -->
<div class="layer-dimm-wrap myQna-write">
	<div class="layer-inner">
		<h2 class="layer-title">Q&amp;A 작성</h2>
		<button class="btn-close" type="button"><span>닫기</span></button>
		<div class="layer-content">
			<form name='write_form' id='write_form' action="<?=$_SERVER['PHP_SELF']?>" method='POST' enctype="multipart/form-data">
			<input type='hidden' id='mode' name='mode' value="<?=$_GET["mode"] ?>">
			<input type='hidden' id='idx' name='idx' value="<?=$_GET["idx"] ?>">
			<input type='hidden' id="ph" name='hp' value="">
			<input type='hidden' id="email" name='email' value="">
			<input type='hidden' id="openyn" name="openyn" value="">
			<table class="th-left">
				<caption>Q&amp;A 작성하기</caption>
				<colgroup>
					<col style="width:144px">
					<col style="width:auto">
				</colgroup>
				<tbody>
					<tr>
						<th scope="row"><label for="qna_title" class="essential">제목</label></th>
						<td><div class="input-cover"><input type="text" class="w100-per required_value" label="문의제목" title="제목 입력" name="pop_subject" id="pop_subject"></div></td>
					</tr>
					<tr>
						<th scope="row"><label for="my_qna_textarea" class="essential">내용</label></th>
						<td><textarea id="my_qna_textarea" class="w100-per required_value" label="문의내용" style="height:272px" name="pop_content"></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="qna_email">이메일</label></th>
						<td>
							<div class="input-cover">
								<input type="text" id="email1" name="email1" value="" style="width:150px" title="이메일 입력" tabindex="14">
								<span class="txt">@</span>
								<input type="text" id="email2" name="email2" value="" title="도메인 직접 입력" class="ml-10" style="width:150px; display: none;" >
								&nbsp;
								<div class="select">
									<select style="width:170px" title="이메일 도메인 선택" onchange="customChk(this.value);" id="email_select">
										<option value="">선택</option>
										<option value="custom">직접입력</option>
										<option value="naver.com">naver.com</option>
										<option value="daum.net">daum.net</option>
										<option value="gmail.com">gmail.com</option>
										<option value="nate.com">nate.com</option>
										<option value="yahoo.co.kr">yahoo.co.kr</option>
										<option value="lycos.co.kr">lycos.co.kr</option>
										<option value="empas.com">empas.com</option>
										<option value="hotmail.com">hotmail.com</option>
										<option value="msn.com">msn.com</option>
										<option value="hanmir.com">hanmir.com</option>
										<option value="chol.net">chol.net</option>
										<option value="korea.com">korea.com</option>
										<option value="netsgo.com">netsgo.com</option>
										<option value="dreamwiz.com">dreamwiz.com</option>
										<option value="hanafos.com">hanafos.com</option>
										<option value="freechal.com">freechal.com</option>
										<option value="hitel.net">hitel.net</option>
									</select>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>휴대폰 번호</label></th>
						<td>
							<div class="input-cover">
								<div class="select">
									<select style="width:110px" title="휴대폰 앞자리 선택" id="phone_num1">
										<option value="010">010</option>
										<option value="011">011</option>
										<option value="016">016</option>
										<option value="017">017</option>
										<option value="018">018</option>
										<option value="019">019</option>
									</select>
								</div>
								<span class="txt">-</span>
								<input type="text" title="휴대폰 가운데 번호 입력" style="width:110px" id="phone_num2">
								<span class="txt">-</span>
								<input type="text" title="휴대폰 마지막 번호 입력" style="width:110px" id="phone_num3">
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row"><label>공개여부</label></th>
						<td>
							<div class="checkbox">
								<input type="checkbox" id="open_yn">
								<label for="open_yn">비공개</label>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="att pt-10"><span class="point-color">*</span> 표시는 필수항목입니다.</p>
			<div class="btnPlace mt-20">
				<button class="btn-line  h-large" type="button" id="btnCancel"><span>취소</span></button>
				<button class="btn-point h-large" type="submit" id="btnSubmit"><span>등록</span></button>
			</div>
			</form>
		</div><!-- //.layer-content -->
	</div>
</div><!-- //상세 > Q&A 작성 -->

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
	$(".CLS_cal_btn").click(function(){
		$(this).prev().find("input[type='text']").focus();
		$(this).prev().find("input[type='text']").trigger('click');
	});
	
	$("#btnSubmit").click(function(){
		if(check_form()){
			if($("#mode").val() == ""){
				$("#mode").val("insert");
			}
			document.getElementById("email").value = $('#email1').val() +'@'+ $('#email2').val();
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
	var url = "../front/mypage_personalview.php?idx="+idx;
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
	// idx,subject,content,email,hp,open_yn
	//console.log('idx=['+idx+'],headtitle=['+head_title+'],subject=['+subject+'],content=['+content+'],email=['+email+'],hp=['+hp+'],up_filename=['+up_filename+']');
	
	$("#mode").val("modify");
	$("#idx").val(idx);

	$.ajax({
		type: "POST",
		url: "ajax_common_change.php",
		data: "idx="+idx+"&table="+table,
		dataType:"JSON"
	}).done(function(data){
		//console.log(data[0]);
		$("#pop_subject").val(data[0]['subject']);
		var content = data[0]['content'].replace(/<br[^>]*>/gi,"\n");	// 줄바꿈처리
		$("#my_qna_textarea").val(content);
		var emails = data[0]['email'].split("@");

		if(emails[0] != null){
			$("#email1").val(emails[0]);
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
	      		$('#email2').val(emails[1]);
	    		$('#email2').show();
	      		$("#email_select").val("custom").attr("selected", "selected");
	      	} else {
				$("#email_select").val(temp_mail).attr("selected", "selected");
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

	 	if(data[0]['is_secret'] == "0"){
			$("#open_yn").attr("checked", false);
		}else{
			$("#open_yn").attr("checked", true);
		}
	 	$("#openyn").val(data[0]['open_yn']);
	});	

	$('#btnSubmit').text('수정');
 	$('.myQna-write').show();

// 	$("#pop_subject").val(subject);
// 	content = content.replace(/<br[^>]*>/gi,"\n");	// 줄바꿈처리
// 	$("#my_qna_textarea").val(content);

// 	var emails = email.split("@");
// 	if(emails[0] != null){
// 		$("#contact_email").val(emails[0]);
// 	}
// 	if(emails[1] != null){
// 		$("#email2").val(emails[1]);
// 	}

// 	var phones = hp.split("-");
// 	if(phones[0] != null){
// 		//$("#phone_num1 option:eq('"+016+"')").attr("selected", "selected");
// 		$("#phone_num1").val(phones[0]).attr("selected", "selected");
// 	}
// 	if(phones[1] != null){
// 		$("#phone_num2").val(phones[1]);
// 	}
// 	if(phones[2] != null){
// 		$("#phone_num3").val(phones[2]);
// 	}

// 	if(open_yn == "Y"){
// 		$("#open_yn").attr("checked", false);
// 	}else{
// 		$("#open_yn").attr("checked", true);
// 	}
// 	$("#openyn").val(open_yn);

}

$("#open_yn").change(function(){
    if($("#open_yn").is(":checked")){
    	$("#openyn").val("1");
    }else{
    	$("#openyn").val("0");
    }
});

function customChk(str){
	
	$('#email2').val(str);
	
	if(str=='custom'){
		$('#email2').val('');
		$('#email2').show();
	}
}

</script>

<?php  include ($Dir."lib/bottom.php") ?>
<?=$onload?>
</BODY>
</HTML>
