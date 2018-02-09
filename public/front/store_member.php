<?php
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	if(strlen($_ShopInfo->getMemid())>0) {
		header("Location:../index.php");
		exit;
	}

?>

<?php  include ($Dir.MainDir.$_data->menu_type.".php") ?>

<script type="text/javascript">
	
	$(function(){
		$('.layer-dimm-wrap').hide();

		var email_confirm = $('#type-email .btn-email');
		var layerPop_close = $('.layer-dimm-wrap .btn-close');

		$('#btn-type-email').click(function(){
			$('#type-univ , #btn-type-univ').removeClass('on');
			$('#type-email , #btn-type-email').addClass('on');
			
		});

		$('#btn-type-univ').click(function(){
			alert('서비스 준비중입니다.');
			//2015 12 29 요청에 의한 임시 주석 - 유동혁
			//$('#type-email , #btn-type-email').removeClass('on');
			//$('#type-univ , #btn-type-univ').addClass('on');
		});

		email_confirm.click(function(){
			$('.layer-dimm-wrap').show();
		});
		layerPop_close.click(function(){
			$('.layer-dimm-wrap').hide();
		});
	})

	$(document).ready(function(){	
		$("#btnCheckEmail").click(function(){
			var email1 = document.form1.email1.value;
			var email2 = document.form1.email2.value;
			if(email1.length==0){
				alert("이메일을 입력하세요."); document.form1.email1.focus(); return;
			}

			$.ajax({ 
				type: "GET", 
				url: "<?=$Dir.FrontDir?>member_join_emailsend.php", 
				data: "email1=" + email1+"&email2=" + email2,
				dataType:"json", 
				success: function(data) {
					alert(data.msg);
					if (data.code == 1)
					{
						document.form1.email.value	= "";
						$('.layer-dimm-wrap').hide();
					}
				},
				error: function(result) {
					alert("에러가 발생하였습니다."); 
				}
			}); 
		})
	})

</script>

<!-- dimm 레이어팝업 layer-inner 클래스에 사용하실 팝업 클래스 추가 -->
<div class="layer-dimm-wrap">
	<div class="dimm-bg"></div>
	<div class="layer-inner email-confirm">
		<h3 class="layer-title">인증메일 보내기</h3>
		<button type="button" class="btn-close">닫기</button>
		<!-- 내용은 이 부분에 추가 - 클래스 주의할것 -->
		<div class="email-confirm-content">
			<p class="ment">교육할인스토어 메일 내 링크 클릭 -> 사이트 회원가입 클릭</p>
			<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post">
				<fieldset>
					<legend>인증메일을 보내기위한 대학교 메일주소 입력</legend>
					<p class="info">입력 칸에 <strong>대학교 이메일 정보 입력 후 전송하기 버튼</strong>을 눌러주세요</p>
					<div class="input-wrap">
						<input class="mail-id" type="text" name="email1" title="대학교 메일주소 입력자리" placeholder="대학교 메일아이디">
						<span class="txt-lh">@</span>
						<select name="email2">
<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and use = '1' and output = '1' and referrer_email_url != 'ac.kr' and referrer_email_url != '' order by name asc";
						$result = pmysql_query($sql,get_db_conn());
				
						while($row = pmysql_fetch_object($result)){
						?>
								<option value="E<?=$row->idx?>|<?=$row->referrer_email_url?>"><?=$row->name?></option>
						<?php 
						}
						pmysql_free_result($result);
					
?>
						</select>
					</div>
					<div class="btn-align"><input type="button" id="btnCheckEmail" value="전송"></div>
				</fieldset>
			</form>
		</div><!-- 내용은 이 부분에 추가 -->
	</div>
</div>


<!-- start contents -->
<div class="store-member-info">
	<div class="inner">
		<h2><img src="../img/common/h2_edu_member.gif" alt="대학생교육할인스토어의 회원이 되고 싶나요?"></h2>
		<p class="title-ment">대학생교육할인스토어는<span>대학생만을 위한 특별한가격(교육할인가)으로 운영</span>이 되고 있습니다. <br>아래의 방법을 참조하여 가입하시면 됩니다.</p>
		
		<ul class="tab-button">
			<li><button type="button" id="btn-type-email" class="on">학교 이메일 인증을 통한 가입</button></li>
			<li><button type="button" id="btn-type-univ">제휴대학 홈페이지를 통한 가입</button></li>
		</ul>
		<div id="type-email" class="tab-contents on">
			<dt>인증메일 회원가입</dt>
			<dd><img src="../img/common/join_progress.gif" alt="이메일 회원가입 방법"></dd>
			<dd class="c"><button type="button" class="btn-email mt_30">이메일 인증받기</button></dd>
			<dd>
				<ul class="join-bg-attention mt_50">
					<li><span>'이메일 인증받기'</span>를 클릭하시면 쉽게 이해하실 수 있습니다.</li>
					<li><span>가끔 인증메일이 스팸으로 차단될 수 있습니다. 스팸처리가 된 경우 <strong>연락처(02-398-8188)</strong>로 문의해주시기 바랍니다.</span></li>
				</ul>
			</dd>
		</div>
		<div id="type-univ" class="tab-contents">
			<dl>
				<dt>1. 아래 현재 다니고 있는 대학을 선택 합니다.</dt>
				<dd>
					아래 리스트 중 현재 다니고 있는 대학교를 링크로 타고 들어갑니다. <br>
					혹 아래 리스트에 현재 다니고 있는 대학이 없을 경우 고객센터를 통해 <span class="td-u">총학 담당자 분을 연결</span>해주시기 바랍니다.
				</dd>
				<dd class="univ-list-wrap">
					<div class="univ-list">
						<ul>
							<li class="do">서울/경기</li>						
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '서울/경기'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;						
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul><li class='do'></li>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
						</ul>
					</div>
					<div class="univ-list sec-line">
						<ul>
							<li class="do">경북/경남</li>						
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '경북/경남'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;						
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul><li class='do'></li>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
						</ul>
						<ul>
							<li class="do">전북/전남</li>						
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '전북/전남'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;						
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul><li class='do'></li>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
						</ul>
						<ul>
							<li class="do">충북/충남</li>						
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '충북/충남'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;						
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul><li class='do'></li>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
						</ul>
						<ul>
							<li class="do">강원도</li>						
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '강원도'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;						
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul><li class='do'></li>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
						</ul>
						<ul>
							<li class="do">제주</li>						
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '제주'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;						
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul><li class='do'></li>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
						</ul>
					</div>
				</dd>
			</dl>
			<dl class="margin">
				<dt>2. 교육할인 스토어 배너를 찾아, 클릭 후 다시 대학생교육할인스토어로 들어옵니다.</dt>
				<dd>
					교육할인스토어 배너가 있는 홈페이지(각 학교 총학생회 및 대학 커뮤니티)링크를 타고 방문하지 않으면 가입을 할 수 없습니다. <br>
					올바른 경로를 통해 가입한 것이 확인 된 경우 서비스 이용에 제한을 받을 수 있습니다.
				</dd>
				<dd class="mt_20"><img src="../img/common/banner_store_join.gif" alt="대학생교육할인스토어 배너"></dd>
				<dd class="info-ment">위와 같은 교육할인스토어 로고를 찾으시면 됩니다.</dd>
			</dl>
			<dl class="margin">
				<dt>다시 접속한 대학생교육할인스토어에서 회원가입을 누르시면 됩니다.</dt>
				<dd class="mt_20"><img src="../img/common/join_info_ex01.jpg" alt="회원가입 예제 이미지"></dd>
			</dl>
		</div>
		<ul class="join-attention">
			<li>※ 대학생들에게만 판매하는 가격정찰제로 운영이 되는 곳으로 일반 사람들은 가입이 제한 됩니다.</li>
			<li>※ 대학생이지만, 아래의 대학에 포함이 되지 않은 경우 따로 이메일(csmaster@ytn.co.kr)을 통해 문의해주시기 바랍니다.</li>
		</ul>

	</div>
</div>



<?php
include ($Dir."lib/bottom.php")
?>
</BODY>
</HTML>
