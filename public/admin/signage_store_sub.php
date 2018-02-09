<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("header.php"); 
?>

<?
#####function#####
function store_info($store_no)
{
	$sql = " select name,no from tblsignage_store where no = {$store_no} ";
	$result = pmysql_query($sql);
	$store_info = pmysql_fetch_object($result)->name;
	return $store_info;
}

function sub_info($store_no)
{
	$list ="";
	$sql = " select * from tblsignage_store_sub where store_no = {$store_no} ";
	$result = pmysql_query($sql);
	while( $row = pmysql_fetch_object($result) ){
		if($row->view=="1"){
			$row->view="비노출";
		}else{
			$row->view="노출";
		}
		$list[$row->type][] = $row;
	}
	return $list;
}

function del_sub_info($sub_no)
{
	$sql = " delete from tblsignage_store_sub where no = {$sub_no} ";
	$result = pmysql_query($sql);
	echo "<script>alert('삭제 되었습니다');</script>";
}

function set_sort()
{
	$sort = $_REQUEST['sort'];
	$sort = explode(",",$sort);
	foreach($sort as $key=>$val){
		$sort2 = explode("/",$val);
		//debug($sort2);
		//$sql = " update tbl";
	}
}

#####on load#####
$mode = $_REQUEST['mode'];
$store_no = $_REQUEST['store_no'];
if($mode=='sort'){
	set_sort();
}else if($mode=='del'){
	$sub_no = $_REQUEST['num'];
	del_sub_info($sub_no);
}

$store_name = store_info($store_no);
$list = sub_info($store_no);
?>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; 매장관리 &gt; <span>매장관리</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
				<?php include("menu_signage.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="80%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">[<?=$store_name?>] 의 주변정보</div>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>			


			<tr>
                <td>
                    <div id="tabs-container">
                        <ul class="tabs-menu">
                            <li class="on"><a href="#tab-1">엔터테인먼트</a></li>
                            <li><a href="#tab-2">Night Life</a></li>
                            <li><a href="#tab-3">맛집</a></li>
                        </ul>
                        <div class="tab-content-wrap">
                            <div id="tab-1" class="tab-content">
								<div class="title_depth3" style="margin-top:30px;">등록된 주변정보 리스트[엔터테인먼트]</div>

								<div class="table_style02">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<TR >
											<th>분류</th>
											<th>매장명</th>
											<th>주소</th>
											<th>전화번호</th>
											<th>위치정보</th>
											<th>매장 정보</th>
											<th>노출여부</th>
											<th>노출순서</th>
											<th>수정</th>
											<th>삭제</th>
										</TR>
									<?if($list[1]){?>
										<?foreach($list[1] as $val_1){?>
										<tr>
											<td><?=$val_1->store_tag?></td>
											<td><?=$val_1->name?></td>
											<td><?=$val_1->address?></td>
											<td><?=$val_1->phone?></td>
											<td>
												<a style="cursor:pointer;" class="view_map" data-x="<?=$val_1->map_x?>" data-y="<?=$val_1->map_y?>">[지도보기]</a>
											</td>
											<td><?=$val_1->comment?></td>
											<td><?=$val_1->view?></td>
											<td><input type="text" value="<?=$val_1->sort?>" name="sort_1[]" size=2  data-num="<?=$val_1->no?>"></td>
											<td>
												<a style="cursor:pointer;" class="modi_store" data-mode="modi" data-num="<?=$val_1->no?>" data-type="1">
													<img src="img/btn/btn_cate_modify.gif" alt="수정" />
												</a>
											</td>
											<td>
												<a style="cursor:pointer;" class="del_store" data-mode="del" data-num="<?=$val_1->no?>">
													<img src="img/btn/btn_cate_del01.gif" alt="삭제" />
												</a>
											</td>
										</tr>
										<?}?>
									<?}else{?>
										<tr>
											<td colspan=10>등록된 리스트가 없습니다</td>
										</tr>
									<?}?>
									</table>
									<center>
										<a style="cursor:pointer;" class="add_store" data-mode="add" data-type="1">
											<img src="images/btn_badd2.gif" border="0" style="margin-top:50px">
										</a>
										<a style="cursor:pointer;" class="modi_sort" data-type="1">
											<img src="images/botteon_save.gif">
										</a>
									</center>
								</div>
							</div>

                            <div id="tab-2" class="tab-content">
								<div class="title_depth3" style="margin-top:30px;">등록된 주변정보 리스트[Night Life]</div>

								<div class="table_style02">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<TR >
											<th>분류</th>
											<th>매장명</th>
											<th>주소</th>
											<th>전화번호</th>
											<th>위치정보</th>
											<th>매장 정보</th>
											<th>노출여부</th>
											<th>노출순서</th>
											<th>수정</th>
											<th>삭제</th>
										</TR>
									<?if($list[2]){?>
										<?foreach($list[2] as $val_2){?>
										<tr>
											<td><?=$val_2->store_tag?></td>
											<td><?=$val_2->name?></td>
											<td><?=$val_2->address?></td>
											<td><?=$val_2->phone?></td>
											<td>
												<a style="cursor:pointer;" class="view_map" data-x="<?=$val_2->map_x?>" data-y="<?=$val_2->map_y?>">[지도보기]</a>
											</td>
											<td><?=$val_2->comment?></td>
											<td><?=$val_2->view?></td>
											<td><input type="text" value="<?=$val_2->sort?>" name="sort_2[]" size=2></td>
											<td>
												<a style="cursor:pointer;" class="modi_store" data-mode="modi" data-num="<?=$val_2->no?>" data-type="2">
													<img src="img/btn/btn_cate_modify.gif" alt="수정" />
												</a>
											</td>
											<td>
												<a style="cursor:pointer;" class="del_store" data-mode="del" data-num="<?=$val_2->no?>">
													<img src="img/btn/btn_cate_del01.gif" alt="삭제" />
												</a>
											</td>
										</tr>
										<?}?>
									<?}else{?>
										<tr>
											<td colspan=10>등록된 리스트가 없습니다</td>
										</tr>
									<?}?>
									</table>
									<center>
										<a style="cursor:pointer;" class="add_store" data-mode="add" data-type="2">
											<img src="images/btn_badd2.gif" border="0" style="margin-top:50px">
										</a>
										<a style="cursor:pointer;" data-type="2">
											<img src="images/botteon_save.gif">
										</a>
									</center>
								</div>
							</div>

                            <div id="tab-3" class="tab-content">
								<div class="title_depth3" style="margin-top:30px;">등록된 주변정보 리스트[맛집]</div>
								<div class="table_style02">
									<table border=0 cellpadding=0 cellspacing=0 width=100%>
										<TR >
											<th>분류</th>
											<th>매장명</th>
											<th>주소</th>
											<th>전화번호</th>
											<th>위치정보</th>
											<th>매장 정보</th>
											<th>노출여부</th>
											<th>노출순서</th>
											<th>수정</th>
											<th>삭제</th>
										</TR>
									<?if($list[3]){?>
										<?foreach($list[3] as $val_3){?>
										<tr>
											<td><?=$val_3->store_tag?></td>
											<td><?=$val_3->name?></td>
											<td><?=$val_3->address?></td>
											<td><?=$val_3->phone?></td>
											<td>
												<a style="cursor:pointer;" class="view_map" data-x="<?=$val_3->map_x?>" data-y="<?=$val_3->map_y?>">[지도보기]</a>
											</td>
											<td><?=$val_3->comment?></td>
											<td><?=$val_3->view?></td>
											<td><input type="text" value="<?=$val_3->sort?>" name="sort_3[]" size=2></td>
											<td>
												<a style="cursor:pointer;" class="modi_store" data-mode="modi" data-num="<?=$val_3->no?>" data-type="3">
													<img src="img/btn/btn_cate_modify.gif" alt="수정" />
												</a>
											</td>
											<td>
												<a style="cursor:pointer;" class="del_store" data-mode="del" data-num="<?=$val_3->no?>">
													<img src="img/btn/btn_cate_del01.gif" alt="삭제" />
												</a>
											</td>
										</tr>
										<?}?>
									<?}else{?>
										<tr>
											<td colspan=10>등록된 리스트가 없습니다</td>
										</tr>
									<?}?>
									</table>
									<center>
										<a style="cursor:pointer;" class="add_store" data-mode="add" data-type="3">
											<img src="images/btn_badd2.gif" border="0" style="margin-top:50px">
										</a>
										<a  style="cursor:pointer;" data-type="3">
											<img src="images/botteon_save.gif">
										</a>
									</center>
								</div>
							
							</div>
                        </div>
                    </div>
                </td>
            </tr>
			
			<tr>
				<td height="150"></td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<form id="sub_form" name="sub_form">
	<input type="hidden" name="mode" id="form_mode">
	<input type="hidden" name="sort" id="form_sort">
	<input type="hidden" name="no" id="form_no">
	<input type="hidden" name="num">
	<input type="hidden" name="store_no" value="<?=$store_no?>">
</form>

<script src="../js/jquery.js"></script>

<script type="text/javascript">

function change_tab()
{
	$(this).parent().addClass("on");
	$(this).parent().siblings().removeClass("on");
	var tab = $(this).attr("href");
	$(".tab-content").not(tab).css("display", "none");
	$(tab).fadeIn();
}

function set_store()
{
	var mode = $(this).data('mode');
	var store_no = "<?=$store_no?>";
	var type = $(this).data('type');
	if(mode == 'add'){
		window.open("signage_store_sub_reg.php?store_no="+store_no+"&type="+type,"_blank","width=900,height=400,scrollbars=no");
		return;
	}else if(mode =='modi'){
		var num = $(this).data('num');
		window.open("signage_store_sub_reg.php?store_no="+store_no+"&no="+num+"&type="+type, "_blank","width=900,height=400,scrollbars=no");
		return;
	}else{//매장 삭쩨
		if(confirm('매장 정보를 삭제하시겠습니까?')){
			var num = $(this).data('num');
			var form = document.sub_form;
			form.mode.value = mode;
			form.num.value = num;
			form.submit();
		}
	}
}

function modi_sort()
{
	var sort_info="";
	var sort = $("input[name='sort_1[]']");
	sort.each(function(){
		var no = $(this).data('num');
		var sort = $(this).val();
		if(sort_info){
			sort_info +=",";
		}
		sort_info += no+"/"+sort;
	});
	$("#form_mode").val('sort');
	$("#form_sort").val(sort_info);
	$("#sub_form").submit();
}

$(document).on("click",".tabs-menu a",change_tab);

$(document).on("click",".add_store",set_store);

$(document).on("click",".modi_store",set_store);

$(document).on("click",".del_store",set_store);

$(document).on("click",".modi_sort",modi_sort);

</script>

<style type="text/css">
.tabs-menu {}
	.tabs-menu:after {display:block; clear:both; content:"";}
	.tabs-menu li {float:left; position:relative; width:33%; height: 31px;line-height: 31px;float: left;background-color: #f0f0f0; box-sizing:border-box; border:1px solid #d3d3d3; border-bottom:1px solid #4b4b4b;}
	.tabs-menu li.on {position: relative;background-color: #fff; z-index: 5; border:1px solid #4b4b4b; border-bottom:1px solid #fff; }
	.tabs-menu li.on:after {display:block; position:absolute; top:0; right:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:last-child::after {display:none;}
	.tabs-menu li.on:before {display:block; position:absolute; top:0; left:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:first-child::before {display:none;}
	.tabs-menu li a {display:block; font-size:0.8rem; font-weight:bold; color:#aaa; text-align:center;}
	.tabs-menu .on a {color: #4b4b4b;}

.tab-content-wrap {background-color: #fff; }
	.tab-content {display: none;}
	.tab-content-wrap > div:first-child { display: block;}
</style>

<?php 
include("copyright.php");
?>
