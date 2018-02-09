<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$page_num       = $_POST[page_num];
$search_name       = $_POST['search_name'];

$sql = "SELECT  *
        FROM    tblboard a
        WHERE   1=1
        AND     a.board = 'notice'
        AND     a.notice='0'
        AND     a.deleted='0'
        AND     a.pos = 0
        AND     a.depth = 0";
if($search_name == null){
	$sql .= "ORDER BY a.thread, a.pos";
} else {
	$sql .= "AND		a.title like '%{$search_name}%'
        	ORDER BY a.thread, a.pos";
}

$paging = new New_Templet_paging($sql, 10,  10, 'GoPage', true);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = $paging->getSql($sql);
$ret = pmysql_query($sql,get_db_conn());
//exdebug($sql);

#####좌측 메뉴 class='on' 을 위한 페이지코드
//$page_code='csfaq';
$board = "notice";
$class_on['notice'] = " class='active'";
?>
<?php include ($Dir.MainDir.$_data->menu_type.".php") ?>

<SCRIPT LANGUAGE="JavaScript">
<!--
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
function ViewNotice(num) {
	location.href="customer_notice_view.php?num="+num;
}

//-->
</SCRIPT>

<!-- 20170328 신규퍼블리싱 추가 -->
<div id="contents">
	<div class="cs-page">

		<h2 class="page-title">공지사항</h2>

		<div class="inner-align page-frm clear">
			<!-- LEFT 메뉴 설정 -->			
			<?php 
				$lnb_flag = 5;
				include ($Dir.MainDir."lnb.php");
				//echo ($Dir.MainDir."lnb.php");
			?>
			
			<article class="cs-content">
				
				<section>
					<header class="my-title">
						<h3 class="fz-0">공지사항</h3>
						<div class="count">전체 <strong><?=$t_count ?></strong></div>
						<div class="align-input">
							<fieldset>
								<legend>공지사항 검색</legend>
								<!-- 20170329 검색 from 추가  -->
								<form action="customer_notice.php" method="POST">
									<!--  
									<div class="select">
										<select style="width:120px">
											<option value="">전체</option>
										</select>
									</div>
									-->
									<input type="text" name="search_name" title="검색어 입력자리" placeholder="검색어를 입력해주세요" class="ml-5 w250" value="<?=$search_name ?>">
									<button class="btn-point ml-5 w60 va-t" type="submit"><span>검색</span></button>
								</form>
							</fieldset>
						</div>
					</header>
					<table class="th-top">
						<caption>공지사항</caption>
						<colgroup>
							<col style="width:70px">
							<col style="width:auto">
							<col style="width:106px">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">No.</th>
								<th scope="col">상세내역</th>
								<th scope="col">등록일</th>
							</tr>
						</thead>
						<tbody>
<?
		$cnt=0;
		if ($t_count > 0) {

			while($row = pmysql_fetch_object($ret)) {

				$number = ($t_count-($setup[list_num] * ($gotopage-1))-$cnt);
				$reg_date = date("Y-m-d", $row->writetime);

                if($row->filename) {
                    $file_icon = '<span><img src="../static/img/icon/icon_file.png" alt="첨부파일"></span>';
                } else {
                    $file_icon = '';
                }
?>
							<tr>
								<td class="txt-toneB"><?=$number?></td>
								<td class="txt-toneA subject">
									<a href="javascript:ViewNotice('<?=$row->num?>')">
										<?=strip_tags($row->title)?>
										<?=$file_icon?>
									</a>
								</td>
								<td class="txt-toneB"><?=$reg_date?></td>
							</tr>
							
<?
		        $cnt++;
		    }
	    } else {
?>
                            <tr>
                                <td class="txt-toneA pd-30" colspan="3">내역이 없습니다.</td>
                            </tr>
<?
	    }
?>
							<!-- <tr>
								<td class="txt-toneA pd-30" colspan="3">검색결과가 없습니다.</td> 
							</tr> --><!-- [D] 검색 결과 없음 -->
							<!-- <tr>
								<td class="txt-toneA pd-30" colspan="3">등록된 게시물이 없습니다.</td> 
							</tr> --><!-- [D] 최초 게시판 내용 없을경우 -->
						</tbody>
					</table>
					<!-- 페이징 -->
					<div class="list-paginate mt-20"><?=$paging->a_prev_page.$paging->print_page.$paging->a_next_page?></div>
					<!-- // 페이징 -->
				</section>

			</article><!-- //.my-content -->
		</div><!-- //.page-frm -->

	</div>
</div><!-- //#contents -->

<!-- 페이징 처리 -->
<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>

<!-- footer 시작 -->
<?php  include ($Dir."lib/bottom.php") ?>
</BODY>
</HTML>
