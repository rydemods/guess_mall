<?
include_once('outline/header_m.php');
include ("header.inc.php");
$subTitle = "�������� ����";
include ("sub_header.inc.php");

$location = array("����Ư����","��õ������","��⵵","������","����������","��û��","�뱸������","���","�λ걤����","��걤����","���ֱ�����","����","���ֵ�");
$search_l=$_POST["search_l"];
$search_w=$_POST["search_w"];

$gotopage=$_POST["gotopage"];
if($gotopage==""){
	$gotopage = 0;
}

$qry = " ";
if($search_w!=''){
	$qry = "AND title LIKE '%".$search_w."%'";
}
if($search_l!=''){
	$qry = "AND name LIKE '%".$search_l."%'";
}


$tsql="select num FROM tblboard WHERE board='offlinestore' ".$qry;
$cnt_res = pmysql_query($tsql);
$tcnt=pmysql_num_rows($cnt_res);
$tcnt = round($tcnt/5,0);

$sql="select * FROM tblboard WHERE board='offlinestore' ".$qry;
$sql .= " ORDER BY title LIMIT 5 OFFSET {$gotopage} ";
$sql_off = pmysql_query($sql);
while($res=pmysql_fetch_array($sql_off)){
	$res_list[]=$res;
}
?>
<script>
<!--
function GoPage(gotopage) {
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}
function goSearch(){
	if(document.form1.search_w.value !=''){
		document.form1.search_l.value = '';
	}
	document.form1.submit();
}

function subToggle(eq){
	$("#open_store"+eq).toggle(100);
	$("#store_list"+eq).toggleClass('open');
}

//-->
</script>
<link type="text/css" href="css/nmobile.css" rel="stylesheet">
<article>
	<section class="store_search">
		<form method="POST" name="form1" action="<?=$_SERVER['PHP_SELF']?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">
		<div>
			<!--<p></p>-->
		<select name="search_l">
			<option value="">��������</option>
			<? foreach($location as $lc){
				echo "<option value=\"$lc\"";
				if($lc==$search_l){
					echo " selected=\"selected\">$lc</option>";
				}else{
					echo " >$lc</option>";
				}
			}
			?>
		</select>
		</div>
		�����<input type="text" name="search_w" id=""  value="<?=$search_w?>" /><input type="button" value="�˻�" onclick="javascript:goSearch()" />
		</form>
	</section>

	<section class="store_sorting">
		<ul class="store_list">
			<?
			if($res_list){
				$temp = 0;
				foreach($res_list as $rl){
			?>
			<li id="store_list<?=$temp?>">
				<a href="javascript:subToggle(<?=$temp?>)"><div>
				<p class="store_name">
				<span class="title"><?=$rl[title]?></span>
				<span class="tel"><?=$rl[storetel]?></span>
				</p>
				<!--<p class="star3">���� 5��</p>-->
				<p class="addr"><?=$rl[storeaddress]?></p>
				</div></a>
				<div class="open_store" id="open_store<?=$temp?>" style="display:none;">
				<?if($rl[vfilename]){?>
				<img src="<?=$rl[storefilename]?>" width="100%">
				<?}else{?>
				<img src="../front/image/noimg.jpg" width="100%">
				<?}?>
				</div>
			</li>
			<?	$temp++;
				}
			} else{ ?>
			<li class="">
				<p class="store_name" align="center">����� �����ϴ�.</p>
			</li>
			<?}?>
		</ul>

	<div class="paginate">
		<?if($gotopage!=0){ ?>
			<a href="javascript:GoPage(<?=$gotopage-1?>)" class="pre">����</a>
		<?}?>
		<?=$gotopage+1?><?if($tcnt>1){ echo " / ".$tcnt;}?>
		<?if($gotopage+1<$tcnt and $tcnt>1){ ?>
			<a href="javascript:GoPage(<?=$gotopage+1?>)" class="next">����</a>
		<?}?>
	</div>

	</section>
 </article>
<? include ("footer.inc.php"); ?>

<!--<li>
	<a href=""><div>
		<p class="store_name">
		<span class="title">�����ȭ�� �ϻ�Ų�ؽ���</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">���� 5��</p>
	<p class="addr">��⵵ ���� �ϻ꼭�� ��ȭ�� 2602 ���� ��ȭ�� Ų�ٽ��� 3��</p>
	</div></a>
	</li>
	<li class="open"><a href=""><div>
		<p class="store_name">
		<span class="title">�����ȭ�� ����</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star3">���� 5��</p>
	<p class="addr">��⵵ ���� �ϻ꼭�� ��ȭ�� 2602 ���� ��ȭ�� Ų�ٽ��� 3��</p>
	</div></a>

	<div class="open_store">
       <img src="img/store.jpg" alt="" width="100%" />
	<ul class="sns">
	<li class="sns_t"><a href="">Ʈ����</a></li>
	<li class="sns_f"><a href="">���̽���</a></li>
	<li class="sns_m"><a href="">��������</a></li>
	<li class="sns_k"><a href="">īī����</a></li>
	<li class="sns_c"><a href="">���̿���</a></li>
 	</ul>

		 <div class="goods_review store_rev">
		 <h3>���� ���� ���ϱ�</h3>
		<form method="post" action="" class="form_wrap">
			<div class="radio_star">
			<p><input type="radio" id="star1" /> <label for="star1"  class="no01">����5</label><input type="radio" id="star2" /> <label for="star2"  class="no02">����4</label>
			<input type="radio" id="star3" /> <label for="star3"  class="no03">����3</label></p>
			<p><input type="radio" id="star4" /> <label for="star4"  class="no04">����2</label><input type="radio" id="star5" /> <label for="star5"  class="no05">����1</label></p>
		</div>
		<div class="form">
		<textarea id="" rows="" cols="" class="contents">������ �Է����ּ���.</textarea>
		<input type="button" value="���� ���ϱ�" onclick="" />
		</div>
		</form>
		<table class="store_tb">
			<tr>
				<td><p class="star4">���� 5��</p>  <span class="date">2013.10.25</span>
				<span class="name">�ڿ��ϴ�</span>
				�Ŵ����� ���񽺰� �ʹ� ���ƿ�. ���忡 ���� ���Կ�.
				</td>
			</tr>
			<tr>
				<td><p class="star2">���� 5��</p>  <span class="date">2013.10.25</span>
				<span class="name">�ڿ��ϴ�</span>
				�Ŵ����� ���񽺰� �ʹ� ���ƿ�. ���忡 ���� ���Կ�.
				</td>
			</tr>
			<tr>
				<td><p class="star2">���� 5��</p>  <span class="date">2013.10.25</span>
				<span class="name">�ڿ��ϴ�</span>
				�Ŵ����� ���񽺰� �ʹ� ���ƿ�. ���忡 ���� ���Կ�.
				</td>
			</tr>
		</table>
		</div>
<a href="" class="attention">���� �̺�Ʈ ���� �߱޹ޱ�&nbsp;&nbsp;(�� ���񽺴� �α��� �� �̿밡��)</a>
	</div>
	</li>
	<li><a href=""><div>
		<p class="store_name">
		<span class="title">�����ȭ�� �ϻ�Ų�ؽ���</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">���� 5��</p>
	<p class="addr">��⵵ ���� �ϻ꼭�� ��ȭ�� 2602 ���� ��ȭ�� Ų�ٽ��� 3��</p>
	</div></a></li>
	<li><a href=""><div>
		<p class="store_name">
		<span class="title">�����ȭ�� �ϻ�Ų�ؽ���</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">���� 5��</p>
	<p class="addr">��⵵ ���� �ϻ꼭�� ��ȭ�� 2602 ���� ��ȭ�� Ų�ٽ��� 3��</p>
	</div></a></li>
	<li><a href=""><div>
		<p class="store_name">
		<span class="title">�����ȭ�� �ϻ�Ų�ؽ���</span>
	    <span class="tel">031-822-3378</span>
	</p>
	<p class="star5">���� 5��</p>
	<p class="addr">��⵵ ���� �ϻ꼭�� ��ȭ�� 2602 ���� ��ȭ�� Ų�ٽ��� 3��</p>
	</div></a></li>-->