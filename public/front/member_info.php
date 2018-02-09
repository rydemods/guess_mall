<?php
/********************************************************************* 
// 파 일 명		: member_info.php 
// 설     명		: 회원가입전 정보
// 상세설명	: 회원가입전 정보(이메일로인증, 학교 리스트)를 보여줌
// 작 성 자		: 2015.11.03 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
session_start();

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

<SCRIPT LANGUAGE="JavaScript">
<!--

$(document).ready(function(){	
	$("#btnCheckEmail").click(function(){
		var email_id = document.form1.email_id.value;
		var email_addr = document.form1.email_addr.value;
		if(email_id.length==0){
			alert("이메일 아이디를 입력하세요."); document.form1.email_id.focus(); return;
		}

		if(email_addr.length==0){
			alert("이메일 주소를 입력하세요."); document.form1.email_addr.focus(); return;
		}
		
		var email = email_id+"@"+email_addr;
		$.ajax({ 
			type: "GET", 
			url: "<?=$Dir.FrontDir?>member_join_emailsend.php", 
			data: "email=" + email,
			dataType:"json", 
			success: function(data) {
				alert(data.msg);
			},
			error: function(result) {
				alert("에러가 발생하였습니다."); 
			}
		}); 
	})

	$("input[name='email']").keyup(function(){
		$("#email_check").val('0');
	})
})

//-->
</SCRIPT>

	 <div class="containerBody sub_skin">

			<h3 class="title mt_20">
				회원가입
				<p class="line_map"><a>홈</a> &gt; <a class="on">약관/본인인증</a> &gt; <a>회원정보입력</a> &gt; <a>가입완료</a></p>
			</h3>

			<!-- 이용약관 -->
			<div class="join_agree">
				<div class="title">
					<h3>1. 학교 이메일 인증을 통한 가입</h3>
				</div>
				
				<div>
					<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					<div>
						<div >
							
							<table class="th_left" summary="회원가입 정보를 입력합니다.">
								<colgroup>
									<col style="width:121px" />
									<col style="width:auto" />
								</colgroup>
								<tbody>
									<tr>
										<th scope="row">*이메일</th>
										<td>
											<input type="text" class="w140" name='email_id' value="" size=30 title="이메일을 입력하세요" required /> @ <input type="text" class="w140" name='email_addr' title="이메일을 입력하세요" />
											<select class="w140 defult" name="email_select" title="이메일 도메인을 선택하세요." onchange="email_set(this.value);">
												<option value="">직접입력</option>
												<option value="naver.com"  >naver.com</option>
												<option value="hanmail.net"  >hanmail.net</option>
												<option value="yahoo.co.kr" >yahoo.co.kr</option>
												<option value="yahoo.com"  >yahoo.com</option>
												<option value="gmail.com"  >gmail.com</option>
												<option value="korea.com"  >korea.com</option>
												<option value="nate.com"  >nate.com</option>
												<option value="paran.com"  >paran.com</option>
												<option value="hanmir.com"  >hanmir.com</option>
												<option value="hitel.net"  >hitel.net</option>
												<option value="hotmail.com"  >hotmail.com</option>
												<option value="dreamwiz.com"  >dreamwiz.com</option>
												<option value="freechal.com"  >freechal.com</option>
												<option value="chol.com"  >chol.com</option>
												<option value="empal.com"  >empal.com</option>
												<option value="lycos"  >lycos.co.kr</option>
												<option value="hanafos.com"  >hanafos.com</option>
												<option value="netian.com"  >netian.com</option>
											</select>
											<a href="javascript:;" id="btnCheckEmail" class="btn_util">중복확인</a>
											<input type="hidden" name="email" />
											<input type="hidden" id="email_check" value="0" />
										</td>
									</tr>			
								</tbody>
							</table>
						</div>
					</div>
					</form>
				</div>

			</div><!-- //이용약관 -->

			<!-- 개인정보취급방침 -->
			<div class="join_agree">
				<div class="title">
					<h3>2.제휴대학 홈페이지를 통한 가입</h3>
				</div>
				<div class="join-guide-school">
					<dl class="seoul">
						<dt>서울/경기</dt>
						<dd>
							<ul>
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '서울/경기'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 8 == 0))
								echo "</ul><ul>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
							</ul>
						</dd>
					</dl>
					<dl>
						<dt>경북/경남</dt>
						<dd>
							<ul>
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '경북/경남'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 6 == 0))
								echo "</ul><ul>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
							</ul>
						</dd>
					</dl>
					<dl>
						<dt>전북/전남</dt>
						<dd>
							<ul>
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '전북/전남'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 6 == 0))
								echo "</ul><ul>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
							</ul>
						</dd>
					</dl>
					<dl>
						<dt>충북/충남</dt>
						<dd>
							<ul>
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '충북/충남'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 6 == 0))
								echo "</ul><ul>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
							</ul>
						</dd>
					</dl>
					<dl>
						<dt>강원도</dt>
						<dd>
							<ul>
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '강원도'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 6 == 0))
								echo "</ul><ul>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
							</ul>
						</dd>
					</dl>
					<dl>
						<dt>제주</dt>
						<dd>
							<ul>
						<?php
						// 테이블의 전체 레코드수만 얻음
						$sql = " select * from tblaffiliatesinfo where type=1 and output = '1' and area = '제주'";
						$result = pmysql_query($sql,get_db_conn());
						$i = 0;
						while($row = pmysql_fetch_object($result)){
							if($i>0 && ($i % 6 == 0))
								echo "</ul><ul>";
							$rf_referrername = $row->name;
						?>
								<li><a href="http://<?php echo ($row->url)?$row->url:$row->referrer_url;?>"><?php echo $rf_referrername;?></a></li>
						<?php 
							$i++;
						}
						pmysql_free_result($result);
						?>
							</ul>
						</dd>
					</dl>

				</div>
			</div>
	 </div>

<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
