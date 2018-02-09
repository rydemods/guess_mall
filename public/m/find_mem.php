<?
$subTitle = "비밀번호 찾기";
include_once('outline/header_m.php');
include_once('sub_header.inc.php');
?>


<script type="text/javascript">
	$(document).ready(function(){
		$("#searchId").click(function(){
			var searchName = $('#srch_id_name').val();
			var searchMail = $('#srch_id_mail').val();
			$.ajax({
				type:"post",
				url:"./ajax.find.php",
				data: "name="+searchName+"&mail="+searchMail+"&mode=findId",
				success:function(result){
					if(result){
						alert("회원님의 아이디는 "+result+"입니다.");
					}else{
						alert("일치하는 회원정보를 찾을 수 없습니다.");
					}
				},
				error:function(result){
					alert('일시적인 에러가 발생하였습니다.\n다시 시도하여주시기 바랍니다.');
				}
			});
		})


		$("#searchPw").click(function(){
			var searchName = $('#srch_pw_name').val();
			var searchId = $('#srch_pw_id').val();
			var searchMail = $('#srch_pw_mail').val();

			$.ajax({
				type:"post",
				url:"./ajax.find.php",
				data: "name="+searchName+"&id="+searchId+"&mail="+searchMail+"&mode=findPw",
				success:function(result){
					if(result){
						//alert(result);
						alert("이메일이 발송되었습니다.");
						location.href = "./login.php";
					}else{
						alert("이메일이 발송이 실패했습니다. 다시 시도해주세요.");
					}
				},
				error:function(){
					alert('일시적인 에러가 발생하였습니다.\n다시 시도하여주시기 바랍니다.');
				}
			});
		})
	})
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">

<main id="content" class="subpage">
<section class="find_idpw">
	<article>
		<h1>
			회원 아이디를 잊어버리셨나요?
			<span>아래 정보를 입력해주시면 회원님의 아이디를 찾아드립니다.</span>
		</h1>
		<div class="find">
		<table>
			<caption class="hide">아이디찾기 테이블</caption>
			<colgroup>
				<col width="*" /><col width="*" />
			</colgroup>
			<tr>
				<th scope="row">이름(실명)</th>
				<td><input class="inp" type="text" id="srch_id_name" tabindex="1" required label="이름(실명)"/></td>
			</tr>
			<tr>
				<th scope="row">이메일</th>
				<td><input class="inp" type="text" id="srch_id_mail" tabindex="2" required label="이메일"></td>
			</tr>
			<tr>
				<td colspan=2><a href = "javascript:;" id = "searchId" class="btn_find"  tabindex="3">아이디 찾기</a></td>
			</tr>
		</table>
		</div>
	</article>

	<article>
		<h1>
			회원 비밀번호를 잊어버리셨나요?
			<span>가입하실 때 정보와 일치할 경우 회원님의 이메일로 비밀번호를 보내드립니다.</span>
		</h1>
		<div class="find">
		<table>
			<caption class="hide">비밀번호찾기 테이블</caption>
			<colgroup>
				<col width="*" /><col width="*" />
			</colgroup>
			<tr>
				<th scope="row">이름(실명)</th>
				<td><input class="inp" type="text" id="srch_pw_name" tabindex="4" required label="이름(실명)"/></td>
			</tr>
			<tr>
				<th scope="row">아이디</th>
				<td><input class="inp" type="text" id="srch_pw_id" tabindex="5" required label="아이디" ></td>
			</tr>
			<tr>
				<th scope="row">이메일</th>
				<td><input class="inp" type="text" id="srch_pw_mail" tabindex="6" required label="이메일"></td>
			</tr>
			<tr>
				<td colspan=2><a href = "javascript:;" id = "searchPw" class="btn_find"  tabindex="7">비밀번호 찾기</a></td>
			</tr>
		</table>
		</div>
	</article>

	<article>
		<h1>아직 회원이 아닌가요?
		<span>회원가입을 하시면 다양한 혜택이 준비되어 있습니다.</span>
		</h1>
		<div class="find pt_30">
			<a class="btn_main" href = "./member_agree.php?jointype=0" style="color: #FFFFFF;">회원가입</a>
		</div>
	</article>



</section>
</main>
<? include_once('outline/footer_m.php'); ?>