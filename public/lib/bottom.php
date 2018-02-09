<?php
/*********************************************************************
// 파 일 명		: bottom.php
// 설     명		: 하단 템플릿
// 상세설명	: 하단 ( INFOMATION, CONTACT INFO, HELP DESK) 템플릿
// 작 성 자		: 2016.01.14 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
// 수 정 자		: 2017.01.20 - 위민트
//
*********************************************************************/
?>
<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$companynum="";

/*$bottom_body = "
	<address>
		<p>
			상호 [COMPANYNAME]([NAME]) &nbsp; 전화 : [TEL] &nbsp; 팩스 : [FAX] &nbsp; <a href=\"mailto:[INFOMAIL]\">이메일 : [INFOMAIL]</a> &nbsp; 사업자등록번호 : [BIZNUM] <a href=\"javascript:openBizInfo('[ORIBIZNUM]');\">[사업자정보확인]</a><br>
			통신판매업신고 : [SALENUM] &nbsp; 대표이사 [OWNER], &nbsp; 개인정보책임자 [PRIVERCY] &nbsp; 주소 : [ADDRESS]
		</p>
		<p>COPYRIGHT(C)[NAME]. ALL RIGHTS RESERVED.</p>
	</address>";*/
//기획팀 요청으로 인해 상점명 제거(2016-10-11)
$bottom_body = "
	<address>
		<p>
			상호 [COMPANYNAME] &nbsp; 전화 : [TEL] &nbsp; 팩스 : [FAX] &nbsp; <a href=\"mailto:[INFOMAIL]\">이메일 : [INFOMAIL]</a> &nbsp; 사업자등록번호 : [BIZNUM] <a href=\"javascript:openBizInfo('[ORIBIZNUM]');\">[사업자정보확인]</a><br>
			통신판매업신고 : [SALENUM] &nbsp; 대표이사 [OWNER], &nbsp; 개인정보책임자 [PRIVERCY] &nbsp; 주소 : [ADDRESS]
		</p>
		<p>COPYRIGHT(C)[NAME]. ALL RIGHTS RESERVED.</p>
	</address>";

$arcompa=array("-"," ",".","_",",");
$arcomre=array("", "", "", "", "");
$companynum=str_replace($arcompa,$arcomre,$_data->companynum);

if(strlen($companynum)==13) {
	$companynum=substr($companynum,0,6)."-*******";
} else {
	$companynum=substr($companynum,0,3)."-".substr($companynum,3,2)."-".substr($companynum,5);
}
$bottom_body=str_replace("[DIR]",$Dir,$bottom_body);

$pattern=array("[SHOPTITLE]","[URL]","[NAME]","[TEL]","[FAX]","[INFOMAIL]","[COMPANYNAME]","[ORIBIZNUM]","[BIZNUM]","[SALENUM]","[OWNER]","[PRIVERCY]","[ADDRESS]","[HOME]","[USEINFO]","[BASKET]","[COMPANY]","[ESTIMATE]","[BOARD]","[AUCTION]","[GONGGU]","[EMAIL]","[RESERVEVIEW]","[LOGIN]","[LOGOUT]","[PRIVERCYVIEW]","[CONTRACT]","[MEMBER]","[MYPAGE]","[ORDER]","[RSS]","[PRODUCTNEW]","[PRODUCTBEST]","[PRODUCTHOT]","[PRODUCTSPECIAL]","[REGDATE]");
$replacelogin=array($_data->shoptitle,"http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,str_replace(","," / ", $_data->info_fax),$_data->info_email,$_data->companyname,$_data->companynum,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php","\"JavaScript:alert('로그인중입니다.');\"",$Dir.MainDir."top.php?type=logout","\"/front/privacy.php\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."mypage_usermodify.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php", substr($_data->regdate,0,4));
$replacelogout=array($_data->shoptitle,"http://".$_ShopInfo->getShopurl()." target=_top",$_data->shopname,$_data->info_tel,str_replace(","," / ", $_data->info_fax),$_data->info_email,$_data->companyname,$_data->companynum,$companynum,$_data->reportnum,$_data->companyowner,"<a href=\"mailto:".$_data->privercyemail."\">".$_data->privercyname."</a>",$_data->info_addr,$Dir.MainDir."main.php",$Dir.FrontDir."useinfo.php",$Dir.FrontDir."basket.php",$Dir.FrontDir."company.php","\"JavaScript:estimate()\"",$Dir.BoardDir."board.php?board=qna",$Dir.AuctionDir."auction.php",$Dir.GongguDir."gonggu.php","\"JavaScript:sendmail()\"",$Dir.FrontDir."mypage_reserve.php",$Dir.FrontDir."login.php?chUrl=".(isset($_REQUEST["chUrl"])?$_REQUEST["chUrl"]:""),"\"JavaScript:alert('먼저 로그인하세요.');\"","\"JavaScript:privercy()\"",$Dir.FrontDir."agreement.php",$Dir.FrontDir."member_agree.php",$Dir.FrontDir."mypage.php",$Dir.FrontDir."mypage_orderlist.php",$Dir.FrontDir."rssinfo.php",$Dir.FrontDir."productnew.php",$Dir.FrontDir."productbest.php",$Dir.FrontDir."producthot.php",$Dir.FrontDir."productspecial.php", substr($_data->regdate,0,4));

if (strlen($_ShopInfo->getMemid())>0) {
	$bottom_body = str_replace($pattern,$replacelogin,$bottom_body);
} else {
	$bottom_body = str_replace($pattern,$replacelogout,$bottom_body);
}
?>



<!-- [D] 스토어스토리_상세보기 팝업 -->
<div class="layer-dimm-wrap pop-view-detail store"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner">
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content store_story_detail_content">
		</div>
	</div>
</div>

<!-- 공통 적용 스크립트 , 모든 페이지에 노출되도록 설치. 단 전환페이지 설정값보다 항상 하단에 위치해야함 --> 
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"> </script> 
<script type="text/javascript"> 
if (!wcs_add) var wcs_add={};
wcs_add["wa"] = "s_4c8895fe304e";
if (!_nasa) var _nasa={};
wcs.inflow();
wcs_do(_nasa);
</script>


<!-- // [D] 스토어스토리_상세보기 팝업 -->
<script type="text/javascript">
var store_code = '';
var start_sno = '';
var next_page = 1;
var total_sno = [];
var prev_sno = '';
var now_sno = '';
var next_sno = '';
var gBlock		= 0;
var gGotopage = 0;
var store_search_val	='';
function storyList(page, type){
	$("#store_content"+store_code).find(".btn_list_more").hide();
	//return;
	$.ajax({
		type: "POST",
		url: "../front/ajax_store_story_list.php",
		data: { store_code : store_code, search : store_search_val, page : page, start_sno : start_sno, view_type : '' },
		dataType : "json",
		async: false,
		cache: false,
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(data){
		if( !jQuery.isEmptyObject( data ) ){
			start_sno	= data.story_start_sno;
			var dsta	= data.story_total_sno;
			var dsta_exp	= dsta.split(",");
			var cnt	= 0;
			var type_next_sno	= 0;
			$.each(dsta_exp,function(number){
				if (type == 'next' && cnt == 0) type_next_sno = dsta_exp[number];
				total_sno.push(dsta_exp[number]);
				cnt++;
			});

// 			console.log(total_sno);
// 			console.log(store_code);

			if(page == '1')
			{
				$("#store_content"+store_code).find(".comp-posting").html("");
				$("#store_content"+store_code).find(".comp-posting").append(data.story_html);
				if($("#store_content"+store_code).find('.comp-posting').children().length > 0)
				{
					$("#store_content"+store_code).find('.comp-posting').masonry();
					$("#store_content"+store_code).find('.comp-posting').masonry('reloadItems');
					$("#store_content"+store_code).find('.comp-posting').masonry('layout');
					$("#store_content"+store_code).find('.comp-posting').css("height","422");
				}
			}else{
				$("#store_content"+store_code).find(".comp-posting").append(data.story_html);
				$("#store_content"+store_code).find('.comp-posting').masonry('reloadItems');

				var listLen = 0;

				for(var i=0;i<$('.comp-posting>li>figure>a>img').length;i++)
				{
					$('.comp-posting>li>figure>a>img').eq(i).attr("src", $('.comp-posting>li>figure>a>img').eq(i).attr("src"));
				}

				$('.comp-posting>li>figure>a>img').on('load', function(){
					listLen++;
					if(listLen == $('.comp-posting>li').length)
					{
						$("#store_content"+store_code).find('.comp-posting').masonry('layout');
					}
				});



			}

			next_page	= data.story_next_page;
			if(data.story_next_page == 'E')
			{
				$("#store_content"+store_code).find(".btn_list_more").hide();
			} else {
				$("#store_content"+store_code).find(".btn_list_more").html('<a href="javascript:storyList(\''+data.story_next_page+'\',\'\')">더보기</a>');
				$("#store_content"+store_code).find(".btn_list_more").show();
			}
			if (type == 'next') stsDetailView(type_next_sno, type);
		}

	});
}

function new_open (url){
	window.open(url);
}

<?if (strpos($_SERVER["REQUEST_URI"],'store_story.php') !== false) {?>
storyList('1','');
<?}?>
function sel_store(sel_code) {
	var bf_store_code = store_code;
	store_code	= sel_code;
	start_sno = '';
	next_page = 1;
	total_sno = [];
	$("#store_content"+bf_store_code).removeClass("on");
	$("#store_content"+store_code).addClass("on");
	storyList('1','');
}

function storyListSearch() {
	store_search_val	= $("form[name=storeSearchForm]").find("input[name=searchVal]").val();
	if (store_search_val == '') {
		alert('검색어를 입력해 주세요.');
		$("form[name=storeSearchForm]").find("input[name=searchVal]").focus();
		return;
	} else {
		start_sno = '';
		next_page = 1;
		total_sno = [];
		storyList('1','');
	}
}

function stsDetailView(sno, type){
	$.ajax({
		type: "POST",
		url: "../front/ajax_store_story_detail.php",
		data: { detail_type : '', sno : sno, view_type : '' },
		dataType : "html",
		async: false,
		cache: false,
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(data){
		$(".store_story_detail_content").html(data);
		now_sno	= sno;
		prev_sno	= total_sno[total_sno.indexOf(sno) - 1];
		next_sno	= total_sno[total_sno.indexOf(sno) + 1];
		if (type!='open_only') {
			if (prev_sno === undefined) $(".store_story_detail_content").find(".view-prev").hide();
			if (next_sno === undefined && next_page =='E') $(".store_story_detail_content").find(".view-next").hide();
		} else {
			$(".store_story_detail_content").find(".view-prev").hide();
			$(".store_story_detail_content").find(".view-next").hide();
		}
		gBlock		= 0;
		gGotopage = 1;

		//console.log(prev_sno+"/"+next_sno);
		if (type=='open' || type=='open_only')
			$('.pop-view-detail.store').fadeIn();
	});
}

function move_detail(type) {
	if (type == 'prev') {
		stsDetailView(prev_sno, type);
	} else if (type == 'next') {
		if (type=='next' && next_sno === undefined && next_page !='E')
		{
			storyList(next_page, type);
		} else {
			stsDetailView(next_sno, type);
		}
	}
}

function commentSubmit(sno) {
<?if (strlen($_ShopInfo->getMemid()) == 0) {?>
	document.location.href="<?=$Dir.FrontDir?>login.php?chUrl=<?=getUrl()?>";
<?} else {?>
	var comment	= $("form[name=commentForm]").find("input[name=comment]").val();
	if (comment == '') {
		alert('댓글을 입력해 주세요.');
		$("form[name=commentForm]").find("input[name=comment]").focus();
		return;
	}
	$.ajax({
		url : 'store_story_comment_proc.php',
		type: "POST",
		data: {
			mode : 'write', sno : sno, comment : comment
		},
		async: false,
		cache: false,
	}).success(function(data){
		if( data === "SUCCESS" ) {
			$("form[name=commentForm]").find("input[name=comment]").val("");
			GoPageAjax(0, 1);
			alert("등록되었습니다.");
		} else {
			var arrTmp = data.split("||");
			if ( arrTmp[0] === "FAIL" ) {
				alert(arrTmp[1]);
			} else {
				alert("등록에 실패하였습니다.");
			}
		}
	}).error(function(){
		alert("다시 시도해 주십시오.");
	});
<?}?>
}
function commentDel(cno) {
	$.ajax({
		url : 'store_story_comment_proc.php',
		type: "POST",
		data: {
			mode : 'delete', cno : cno
		},
		async: false,
		cache: false,
	}).success(function(data){
		if( data === "SUCCESS" ) {
			GoPageAjax(gBlock, gGotopage);
			alert("삭제되었습니다.");
		} else {
			var arrTmp = data.split("||");
			if ( arrTmp[0] === "FAIL" ) {
				alert(arrTmp[1]);
			} else {
				alert("삭제에 실패하였습니다.");
			}
		}
	}).error(function(){
		alert("다시 시도해 주십시오.");
	});
}

function GoPageAjax(block, gotopage) {
	gBlock = block;
	gGotopage = gotopage;
	var sno	= now_sno;
	$.ajax({
		type: "POST",
		url: "../front/ajax_store_story_detail.php",
		data: { detail_type : 'comment', sno : sno, block : block, gotopage : gotopage },
		dataType : "html",
		async: false,
		cache: false,
		error:function(request,status,error){
			//alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		}
	}).done(function(data){
		$(".store_story_detail_content").find(".reply-list").html(data);
	});
}
function go_check() { 
//	window.open("http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L");
	 var status  = "width=500 height=450 menubar=no,scrollbars=no,resizable=no,status=no"; 
	 var obj     = window.open('', 'kcp_pop', status); 
	 document.shop_check.method = "post"; 
	 document.shop_check.target = "kcp_pop"; 
	 document.shop_check.action = "http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp"; 
	 document.shop_check.submit();  
}
</script>


<div id="footer">
	<div class="menu-wrap">
		<div class="inner-align clear">
			<a href="../index.htm" class="footer-logo"><?=$_data->shoptitle?></a>
			<nav class="footer-menu">
				<!-- <a href="../front/company.php">회사소개</a> -->
				<a onclick="new_open('http://www.sw.co.kr');">회사소개</a>
				<!-- <a href="../front/store_story.php">매장안내</a> -->
				<a href="../front/storeList.php">매장안내</a>
				<!-- <a href="javascript:void(0);">개인정보취급방침</a>
				<a href="javascript:void(0);">이용약관</a> -->
				<a href="../front/etc_privacy.php">개인정보취급방침</a>
				<a href="../front/etc_agreement.php">이용약관</a>
				<a href="../front/customer_notice.php">고객센터</a>
			</nav>
			<div class="footer-share">
				<span class="txt">share your style</span>
				<a onclick="new_open('https://www.facebook.com/mall.shinwon.9');" target="_blank"><i class="icon-facebook">페이스북</i></a>
				<a onclick="new_open('https://www.instagram.com/shinwonmall/');" target="_blank"><i class="icon-instagram">인스타그램</i></a>
				<a onclick="new_open('https://www.youtube.com/channel/UCITkKbSvb3hjm8rTeLW1Jcw/feed');"><i class="icon-youtube">유튜브</i></a>
			</div>
		</div>
	</div><!-- //.menu-wrap -->
	<div class="body-footer inner-align">
		<address>
			<p><?=$_data->companyname?><span class="pl-10"></span> 대표자(성명) : <?=$_data->companyowner?> <span class="pl-10"></span> 사업장 소재지 : <?=$_data->info_addr?> <span class="pl-10"></span> 대표번호 : <?=$_data->info_tel?></p>
			<p>사업자 등록번호 안내 : <?=$companynum?> <span class="pl-10"></span> 개인정보관리책임자 : <?=$_data->privercyname?> <span class="pl-10"></span> 통신판매업 신고 : <?=$_data->reportnum?> <!-- <a href="javascript:openBizInfo('<?=$_data->companynum?>');">[사업자정보확인]</a></p> --> <a href="#;" onclick="javascript:window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?=$_data->companynum?>', 'communicationViewPopup', 'width=750, height=600');">[사업자정보확인]</a></p>
		</address>
		<div class="copyright">
			<span class="ds-ib">COPYRIGHT &copy; 2016 <?=$_data->shopname?>. ALL RIGHTS RESERVED.</span>
			<!-- 
			<a onclick="go_check();" style="cursor:pointer" class="ml-5">에스크로 서비스 가입 확인</a>Z
			 -->
			<a href="#;" onclick="javascript:window.open('https://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L','KCPHelp','width=500,height=450,scrollbars=auto,resizable=yes');" style="cursor:pointer" class="ml-5">에스크로 서비스 가입 확인</a>
			<!--  
			<a onclick="javascript:window.open('https://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp?site_cd=A7J0L','KCPHelp','width=500,height=450,scrollbars=auto,resizable=yes');" style="cursor:pointer" class="ml-5">에스크로 서비스 가입 확인</a>
			-->
		</div>
		<!-- 20170426 QR코드, APP 링크 임시숨김처리 -->
		<!-- 
		<div class="apps clear">
			<div class="qrcode"><img src="/sinwon/web/static/img/common/qrcode.jpg" alt="QR CODE"></div>
			<div class="btn">
				<a href="javascript:void(0);"><img src="/sinwon/web/static/img/btn/btn_app_ios.jpg" alt="App Store"></a>
				<a href="javascript:void(0);" class="mt-5"><img src="/sinwon/web/static/img/btn/btn_app_android.jpg" alt="Google Play"></a>
			</div>
		</div>
		 -->
	</div>
</div><!-- //#footer -->


<!-- 입점문의 팝업 -->
<div class="layer-dimm-wrap pop-standing-point layer-contact-us"> <!-- .layer-class 이부분에 클래스 추가하여 사용합니다. -->
	<div class="dimm-bg"></div>
	<div class="layer-inner">
        <form id="storeLocWriteForm" name="storeLocWriteForm" method="post" action="/board/board.php" enctype="multipart/form-data">
        <input type=hidden name=mode value=''>
        <input type=hidden name=pagetype value='write'>
        <input type=hidden name=exec value='write'>
        <input type=hidden name=num value=>
        <input type=hidden name=board value='storeloc'>
        <input type=hidden name=s_check value=>
        <input type=hidden name=search value=>
        <input type=hidden name=block value=>
        <input type=hidden name=gotopage value=>
        <input type=hidden name=pridx value=>
        <input type=hidden name=recipe_no value=>
        <input type=hidden name=pos value="">
        <input type=hidden name=up_is_secret value="">
        <input type=hidden name=up_passwd value="1234">

		<h3 class="layer-title">HOT<span class="type_txt1">-T</span> 입점문의</h3>
		<button type="button" class="btn-close">창 닫기 버튼</button>
		<div class="layer-content">
			<p class="mb-10">입점문의 관련하여 궁금하신 점을 문의하시면 신속하고 정확한 답변을 받으실 수 있습니다.</p>
			<table class="th_left">
			<caption></caption>
			<colgroup>
				<col style="width:100px">
				<col style="width:auto">
			</colgroup>
			<tbody>
				<tr>
					<th scope="row">이름 <span class="required">*</span></th>
					<td>
						<input type="text" id="up_name" name="up_name" title="이름 입력자리" maxlength="20" style="width:239px;">
					</td>
				</tr>
				<tr>
					<th scope="row">제목 <span class="required">*</span></th>
					<td>
						<input type="text" id="up_subject" name="up_subject" title="제목 입력자리" maxlength="20" style="width:100%;">
					</td>
				</tr>
				<tr>
					<th scope="row">내용 <span class="required">*</span></th>
					<td>
						<textarea id="up_memo" name="up_memo" cols="30" rows="10" style="width:100%"></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">답변 이메일 <span class="required">*</span></th>
					<td><input type="text"  name="up_email" id="up_email" title="이름 입력자리" style="width:239px;"></td>
				</tr>
				<tr>
					<th scope="row">휴대폰 번호</th>
					<td>
						<input type="text" name="up_storetel" id="up_storetel" placeholder="하이픈(-) 없이 입력" title="휴대폰 번호 입력자리" style="width:239px;">
						<!-- <input id="base_chk" name="sms-send" type="checkbox" class="chk_agree checkbox-def ml-5" checked><label for="base_chk"> 담당자의 답신여부를 SMS로 받겠습니다.</label> -->
                        담당자의 답신여부를 SMS로 받으시겠습니까?
                        <input type="radio" name="sms-send" id="sms-ok" class="radio-def" value="1">
                        <label for="sms-ok">예</label>
                        <input type="radio" name="sms-send" id="sms-no" class="radio-def" value="0" checked>
                        <label for="sms-no">아니오</label>
					</td>
				</tr>
				<tr>
					<th scope="row">이미지</th>
					<td class="imageAdd">
						<input type="file" id="up_filename" name="up_filename[]">
						<div class="txt-box"></div> <!-- // 첨부파일명 노출 영역 -->
						<label for="up_filename">찾기</label>
					</td>
				</tr>
			</tbody>
			</table>

			<!-- A Square|Site Analyst WebLog for Emission Script v1.1 -->
			<!--  엔서치스크립트 2017.09.12 -->
			<script type="text/javascript">
			var _nSA=(function(_g,_c,_s,_p,_d,_i,_h){ 
				if(_i.wgc!=_g){var _ck=(new Image(0,0)).src=_p+'//'+_c+'/?cookie';_i.wgc=_g;_i.wrd=(new Date().getTime());
				var _sc=_d.createElement('script');_sc.src=_p+'//sas.nsm-corp.com/'+_s+'gc='+_g+'&dn='+escape(_h)+'&rd='+_i.wrd;
				var _sm=_d.getElementsByTagName('script')[0];_sm.parentNode.insertBefore(_sc, _sm);_i.wgd=_c;} return _i;
			})('CS6B41796118755','ngc16.nsm-corp.com','sa-w.js?',location.protocol,document,window._nSA||{},location.hostname);
			</script><noscript><img src="//ngc16.nsm-corp.com/?uid=CS6B41796118755&je=n&" border=0 width=0 height=0></noscript>

            <SCRIPT LANGUAGE="JavaScript">
            <!--
            field = "";
            var sms_send_element_cnt = 0;
            for(i=0;i<document.storeLocWriteForm.elements.length;i++) {
                if(document.storeLocWriteForm.elements[i].name.length>0) {
                    if ( document.storeLocWriteForm.elements[i].name == "sms-send" ) {
                        if ( sms_send_element_cnt == 0 ) {
                            field += "<input type=hidden name=ins4eField["+document.storeLocWriteForm.elements[i].name+"]>\n";
                        }
                        sms_send_element_cnt++;
                    } else {
                        field += "<input type=hidden name=ins4eField["+document.storeLocWriteForm.elements[i].name+"]>\n";
                    }
                }
            }
            document.write(field);
            //-->
            </SCRIPT>

			<p class="mt-10"><span class="required">*</span> 필수입력</p>
			<div class="btn_wrap mt-40"><a href="javascript:;" class="btn-type1" onClick="chk_storeLocWriteForm(document.storeLocWriteForm); return false;">문의하기</a></div>
		</div>
        </form>
	</div>
</div>
<!-- // 입점문의 팝업 -->
<form name="shop_check" target="kcp_pop"> 
	<input type="hidden" name="site_cd" id="site_cd" value="A7J0L"> 
</form>
<?
// 이용약관
$agreement = "";
if(file_exists($Dir.AdminDir."agreement.txt")) {
    $agreement = file_get_contents($Dir.AdminDir."agreement.txt");

    $pattern=array("[SHOP]","[COMPANY]");
    $replace=array($_data->shopname, $_data->companyname);
    $agreement = str_replace($pattern,$replace,$agreement);
    $agreement = preg_replace('/[\\\\\\\]/',"",$agreement);
}

// 개인정보취급방침
$privercy = "";
if(file_exists($Dir.AdminDir."privercy.txt")) {
    $privercy = file_get_contents($Dir.AdminDir."privercy.txt");

    $pattern=array("[SHOP]","[NAME]","[EMAIL]","[TEL]");
    $replace=array($_data->shopname,$_data->privercyname,"<a href=\"mailto:{$_data->privercyemail}\">{$_data->privercyemail}</a>",$_data->info_tel);
    $privercy = str_replace($pattern,$replace,$privercy);
}
?>

<script type="text/javascript">
<!--
	function openBizInfo(bizNo){
		var url="http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no="+bizNo;
		window.open(url,"communicationViewPopup","width=750, height=700;");
	}
//-->
</script>

<script type="text/javascript">
<!--
	function mobRf(){
  		var rf = new EN();
		rf.setSSL(true);
  		rf.sendRf();
	}
  //-->
</script>
<script async="true" src="https://cdn.megadata.co.kr/js/enliple_min2.js" onload="mobRf()"></script>

<script type="text/javascript">
<!--
function chk_storeLocWriteForm(form) {
    if (typeof(form.tmp_is_secret) == "object") {
        form.up_is_secret.value = form.tmp_is_secret.options[form.tmp_is_secret.selectedIndex].value;
    }

    if (!form.up_name.value) {
        alert('이름을 입력하십시오.');
        form.up_name.focus();
        return false;
    }

    if (!form.up_subject.value) {
        alert('제목을 입력하십시오.');
        form.up_subject.focus();
        return false;
    }

    if (!form.up_memo.value) {
        alert('내용을 입력하십시오.');
        form.up_memo.focus();
        return false;
    }

    if (form.up_email.value == "" ) {
        alert('이메일을 입력하십시요.');
        form.up_email.value = "";
        form.up_email.focus();
        return false;
    }
    /*
    if (form.up_storetel.value == "" ) {
        alert('전화번호를 입력하십시요.');
        form.up_storetel.value = "";
        return false;
    }
    */
    form.mode.value = "up_result";
    reWriteName(form);

    var fd = new FormData($("#storeLocWriteForm")[0]);

    $.ajax({
        type: "POST",
        url: "../board/board.php",
        data: fd,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
    }).success( function( data ) {
        alert('등록이 성공했습니다.');
        $('div.layer-contact-us').fadeOut('fast');
    }).fail(function() {
        alert('등록이 실패했습니다.');
    });
}

// 파일 업로드 이벤트
$('input[type=file]').on('change', function (e) {
    var fileName = $(this).val().split('\\').pop();

    $(this).parent().find(".txt-box").html(fileName);
    $("#file_exist").val("Y");
});

//베스트 포토 리뷰
function bestPhotoReview(){
	$.ajax({
		type: "POST",
		url: "../main/ajax_review_photo.php",
		data: "type=pc",
		dataType:"HTML",
	    error:function(request,status,error){
	       //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
	    }
	}).done(function(html){
		$(".best-phto-review").html(html);
	});
}

//-->
</script>

<!-- WIDERPLANET  SCRIPT START 2017.9.18 -->
<div id="wp_tg_cts" style="display:none;"></div>
<script type="text/javascript">
var wptg_tagscript_vars = wptg_tagscript_vars || [];
wptg_tagscript_vars.push(
(function() {
	return {
		wp_hcuid:"",   /*Cross device targeting을 원하는 광고주는 로그인한 사용자의 Unique ID (ex. 로그인 ID, 고객넘버 등)를 암호화하여 대입.
				*주의: 로그인 하지 않은 사용자는 어떠한 값도 대입하지 않습니다.*/
		ti:"37370",	/*광고주 코드*/
		ty:"Home",	/*트래킹태그 타입*/
		device:"web"	/*디바이스 종류 (web 또는 mobile)*/
		
	};
}));
</script>
<script type="text/javascript" async src="//cdn-aitg.widerplanet.com/js/wp_astg_4.0.js"></script>
<!-- // WIDERPLANET  SCRIPT END 2017.9.18 -->