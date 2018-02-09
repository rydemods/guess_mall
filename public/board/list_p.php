<?php

if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
include ("head.php");

if($member['grant_list']!="Y") {
	//if(strlen($setup['group_code'])==4 && $setup['group_code']!=$member['group_code']) {}
	if(strlen($setup['group_code'])==4 && $_ShopInfo->memlevel<$setup['group_level']) {
		$errmsg="이용 권한이 없습니다.";
		alert_go($errmsg,-1);
	} else {
		$errmsg="쇼핑몰 회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다.";
		alert_go($errmsg,-1);
	}
}

if($setup['btype']=="B") {
	if($member['grant_view']!="Y") {
		//if(strlen($setup['group_code'])==4 && $setup['group_code']!=$member['group_code']) {}
		if(strlen($setup['group_code'])==4 && $_ShopInfo->memlevel<$setup['group_level']) {
			$errmsg="이용 권한이 없습니다.";
			alert_go($errmsg,-1);
		} else {
			$errmsg="쇼핑몰 회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다.";
			alert_go($errmsg,-1);
		}
	}
}

$prqnaboard="";
if($setup['btype']=="L") {
	$prqnaboard=getEtcfield($_data->etcfield,"PRQNA");
}
switch ($s_check) {
	case "n":
		$check_n = "checked";
		break;
	case "c":
	default:
		$check_c = "checked";
		break;
}
$total_num=0;
$viewtype="0";

// ==========================================================
// 포토게시판용
// ==========================================================
$photo_img = "";

if($_REQUEST[category]){
	$cate_sql = " and category='".$_REQUEST[category]."' ";
}

if($mypageid){
	$w_que='';
	$threadqry="select thread from tblboard WHERE board='{$board}' AND mem_id='".$_ShopInfo->memid."'";
	$threadresult=pmysql_query($threadqry);


	while($threaddata=pmysql_fetch_object($threadresult)){
		$threadnum[]=$threaddata->thread;
	}

	if(count($threadnum)>0){
		$total_num  =pmysql_fetch_object(pmysql_query("SELECT COUNT(*) as totalcon FROM tblboard a WHERE a.board='{$board}' AND thread in (".implode(" ,", $threadnum).")"));
		$w_que= " AND thread in (".implode(" ,", $threadnum).") ";
		$viewtype="1";
	}
} else {

	$total_num  =pmysql_fetch_object(pmysql_query("SELECT COUNT(*) as totalcon FROM tblboard a WHERE a.board='{$board}'".$cate_sql));
	$w_que="";
	$viewtype="1";
}

$t_count = $total_num->totalcon;
//$t_count = $setup['total_article'];

if (strlen($s_check)>0 || $setup['use_reply'] != "Y") {
	$sql2  = "SELECT COUNT(*) FROM tblboard a WHERE a.board='".$board."'AND a.notice = '0' ";

	$orSearch = explode(" ",$search);
	// 검색어가 있는경우 쿼리문에 조건추가...........
	if($_data->icon_type == "tem_001"){
		if($s_check){
				if ($_REQUEST['s_check'] == "all") {
				$w_que.= "AND (";
				for($oo=0;$oo<count($orSearch);$oo++) {
					if ($oo > 0) {
						$w_que .= " OR ";
					}
					$w_que .= "a.name LIKE '%" . $orSearch[$oo] . "%' ";
					$w_que .= "OR a.title LIKE '%" . $orSearch[$oo] . "%' ";
					$w_que .= "OR a.content LIKE '%" . $orSearch[$oo] . "%' ";
				}
				$w_que .= ") ";
			}else{
				$w_que.= "AND (";
				for($oo=0;$oo<count($orSearch);$oo++) {
					$check_array="";
					if ($oo > 0) {
						$w_que .= " OR ";
					}
					/*if($_REQUEST['s_check']['name'] == 'on'){
						$check_array[]= "a.name LIKE '%" . $orSearch[$oo] . "%' ";
					}*/
					if($_REQUEST['s_check'] == "subject"){
						$check_array[]= "a.title LIKE '%" . $orSearch[$oo] . "%' ";
					}
					if($_REQUEST['s_check'] == "content"){
						$check_array[]= "a.content LIKE '%" . $orSearch[$oo] . "%' ";
					}
					$w_que .= implode(" OR ",$check_array);
				}
				$w_que .= ") ";
			}

		}
	}else{
		switch ($s_check) {
			case "c":
				$w_que.= "AND (";
				for($oo=0;$oo<count($orSearch);$oo++) {
					if ($oo > 0) {
						$w_que .= " OR ";
					}
					$w_que .= "a.title LIKE '%" . $orSearch[$oo] . "%' ";
					$w_que .= "OR a.content LIKE '%" . $orSearch[$oo] . "%' ";
				}
				$w_que .= ") ";
				break;
			case "n":
				$w_que = "AND (";
				for($oo=0;$oo<count($orSearch);$oo++) {
					if ($oo > 0) {
						$w_que .= " OR ";
					}
					$w_que .= "a.name LIKE '%" . $orSearch[$oo] . "%' ";
				}
				$w_que .= ") ";
				break;
		}
	}

	if ($setup['use_reply'] != "Y") {
		$w_que.= "AND a.pos = 0 AND a.depth = 0 ";
	}

	$sql2 .= $w_que;
	error_log($sql2,3,'/tmp/error_log');
	$result2 = pmysql_query($sql2,get_db_conn());

	$row2 = pmysql_fetch_row($result2);

	$t_count = $row2[0];
	pmysql_free_result($result2);
}

if(!$setup[list_num]) $setup[list_num] = '10';
if(!$setup[list_num]) $setup[page_num] = '20';

if($_data->icon_type=="tem_001"){
	$setup[page_num] = '7';
	$paging = new Tem001_saveheels_Paging((int)$t_count,$setup[page_num],$setup[list_num]);
}else{
	$paging = new Tem001_saveheels_Paging((int)$t_count,$setup[page_num],$setup[list_num]);
}

$gotopage = $paging->gotopage;

if($viewtype=="1"){
    if($prqnaboard==$board) {
        $sql = "SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage ";
        $sql.= "FROM tblboard a LEFT OUTER JOIN tblproduct b ";
        $sql.= "ON a.pridx=b.pridx ";
        $sql.= "WHERE a.board='".$board."'  AND a.notice='0' AND deleted='0' ";
        $sql.= $w_que.$cate_sql."ORDER BY thread , pos ";
        $sql = $paging->getSql($sql);
    } else {
        $sql = "SELECT a.* FROM tblboard a WHERE a.board='".$board."' AND a.notice='0' AND deleted='0'  ";
        $sql .= $w_que.$cate_sql."ORDER BY a.thread , a.pos ";
        $sql = $paging->getSql($sql);
    }
}

$res = pmysql_query($sql,get_db_conn());
$total = pmysql_num_rows($res);

if($_GET['board']!='support'){
    $page_code = $_GET['board'];
}

include ("top_p.php");

if($_data->icon_type == 'tem_001'){
	$bname = pmysql_fetch(pmysql_query("SELECT board_name FROM tblboardadmin WHERE board = '{$_GET['board']}' "));
?>

<div id="contents">
        <div class="containerBody sub-page">
            
            <div class="breadcrumb">
                <ul>
                    <li><a href="#">HOME</a></li>
                    <li class="on"><a href="#">PROMOTION</a></li>
                </ul>
            </div><!-- //.breadcrumb -->

            <div class="promotion-wrap">

                <div class="board-view">
                    <p class="title">open! cash 50% sale! 다양한 혜택을 드립니다! <span class="date">2016.01.01~2016.01.10</span></p>
                    <div class="view-content">
                        <img src="../static/img/test/@promotion_img.jpg" alt="">
                    </div>
                </div><!-- //.board-view -->

			<div class="photo-list-wrap">
                <p class="ea">게시된 포토 (50)</p>
<?
}

if($_GET['board']!='support'){			//board에 맞는 db값을 가져오는 구문
	if($_GET['board']=='qnabbs'){
		$nSql = "SELECT num, title, writetime, access, name FROM tblboard WHERE board='".$board."' ";
		$nSql.= "AND notice='1' AND deleted ='0' ORDER BY thread ASC ";
		$nResult = pmysql_query($nSql,get_db_conn());
		}
	else if($_GET['board']=='reviewbbs'){
		$nSql = "SELECT num, title, writetime, access, name FROM tblboard WHERE board='".$board."' ";
		$nSql.= "AND notice='1' AND deleted ='0' ORDER BY thread ASC ";
		$nResult = pmysql_query($nSql,get_db_conn());
		}
	else {
		$nSql = "SELECT num, title, writetime, access, name FROM tblboard WHERE board='".$board."' ";
		$nSql.= "AND notice='1' AND deleted ='0' and notice_secret='1' ORDER BY thread ASC ";
		$nResult = pmysql_query($nSql,get_db_conn());
	}
}

if ($total == 0) {
	if ($s_check == "") {
		//echo "</table>";
		$nosearch = "등록된 게시물이 없습니다.";
		include($dir."/list_no_main.php");
	} else {
		//echo "</table>";
		$nosearch = "검색된 게시물이 없습니다.";
		include($dir."/list_no_main.php");
	}
} else {
    echo "<ul class=\"photo-list\">";

	$i = 0;
	while($row = pmysql_fetch_array($res)) {
		$row['title'] = stripslashes($row['title']);
		if($setup['use_html']!="Y") {
			$row['title'] = strip_tags($row['title']);
			$row['content'] = strip_tags($row['content']);
		}
		$row['title'] = getTitle($row['title']);
		$row['title'] = getStripHide($row['title']);

		$row['content']=getStripHide(stripslashes($row['content']));
		if($row['use_html']=="0") {
			//$row['content']=nl2br($row['content']);
			$row['content']=nl2br(htmlspecialchars($row['content']));
		}

		$row['name'] = stripslashes(strip_tags($row['name']));		
		if ($setup['grant_write'] == "A") {
            // 쓰기가 관리자 전용이면 이름을 몰이름으로 변경한다.(2015.12.02 - 김재수)
            $row['name'] = $_data->shopname; 
        }

		$deleted = $row['deleted'];
		$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);

		$prview_img='';
		if($prqnaboard==$board) {
			if(strlen($row['pridx'])>0 && $row['pridx']>0 && strlen($row['productcode'])>0) {
				$prview_img="<A HREF=\"".$Dir.FrontDir."productdetail.php?productcode=".$row['productcode']."\" onmouseover=\"window.status='상품상세조회';return true;\" onmouseout=\"window.status='';return true;\"><img src=".$imgdir."/btn_prview.gif border=0 align=absmiddle class=\"img_ib\"></A>";
			}
		}

		$subject='';
        $row['link_url'] = "";

		if ($deleted != "1" && $setup['btype']!="B") {
			if($mypageid){
				$subject = "<a href='board.php?pagetype=view&view=1&num=".$row['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."&mypageid=".$mypageid."'>";

                $row['link_url'] = "board_p.php?pagetype=view_p&view=1&num=".$row['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."&mypageid=".$mypageid;
			}else{	// 이리로 들어옴
				$subject = "<a href='board.php?pagetype=view&view=1&num=".$row['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."'>";

                $row['link_url'] = "board_p.php?pagetype=view_p&view=1&num=".$row['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check;
			}
		}

		$depth = $row['depth'];
		if($setup['title_length']>0) {
			$len_title = $setup['title_length'];
		}
		$wid = 1;
		if ($depth > 0) {
			if ($depth == 1) {
				$wid = 2;
			} else {
				$wid = (2 * $depth) + (12 * ($depth-1));
			}
			$subject .= "<img src=\"".$imgdir."/x.gif\" width=".$wid."\" height=\"2\" border=\"0\" align=\"top\" class=\"img_ib\">";
			$subject .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\" align=\"top\" class=\"img_ib\">";
			if ($len_title) {
				$len_title = $len_title - (3 * $depth);
			}
		}
		$title = $row['title'];
		if ($len_title) {
			$title = len_title($title, $len_title);
		}
		$subject .=  $title;
		if ($deleted != "1" && $setup['btype']!="B") {
			$subject .= "</a>";
		}
		$new_img='';
		if (getNewImage($row['writetime'])) {
			$subject .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\" class=\"img_ib\">";
			$new_img .= "<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\" class=\"img_ib\">&nbsp;";
		}
		$secret_img='';

        if ($setup['use_lock']=="A" || $setup['use_lock']=="Y") {
            if ($row['is_secret'] == "1") {
                $secret_img = "<img src=\"".$imgdir."/lock.gif\" border=\"0\" align=\"absmiddle\" class=\"img_ib\">";
            } else {
                $secret_img = "";
            }
        }

		if ($setup['use_comment']=="Y" && $row['total_comment'] > 0) {
			if($_data->icon_type != 'tem_001'){
				$subject .= "&nbsp;<img src=\"".$imgdir."/icon_memo.gif\" border=\"0\" align=\"absmiddle\">&nbsp;<font style=\"font-size:8pt;\">(<font color=\"#FF0000\">".$row['total_comment']."</font>)</font>";
			}
		}

		if($setup["first_subject_check"]=="Y" && $setup["first_subject"]!=""){
			$subject = $row[category]?"[<b>".$row[category]."</b>] &nbsp;".$subject:$subject;
		}else{
			$subject=$subject;
		}

		$comment_tot = $row['total_comment'];

		//사용자 이름표시
		//$name_qry="select ";
		$user_name = $row['name'];
		$str_name = $user_name;

		$reg_date = getTimeFormat($row['writetime']);
		$hit = $row['access'];

		if($row['filename'] && ($deleted != "1")) {
			$file_name = strtolower(pathinfo($row["filename"],PATHINFO_EXTENSION));
			if($file_name == 'zip' || $file_name == 'arj' || $file_name == 'gz' || $file_name == 'tar') {
				$file_icon = "compressed.gif";
			} elseif ($file_name == 'rar') {
				$file_icon = "ra.gif";
			} elseif ($file_name == 'exe') {
				$file_icon = "exe.gif";
			} elseif($file_name == 'gif') {
				$file_icon = "gif.gif";
			} elseif($file_name == 'jpg' || $file_name == 'jpeg') {
				$file_icon = "jpg.gif";
			} elseif($file_name == 'mpeg' || $file_name == 'mpg' || $file_name == 'asf' || $file_name == 'avi' || $file_name == 'swf') {
				$file_icon = "movie.gif";
			} elseif($file_name == 'mp3' || $file_name == 'rm' || $file_name == 'ram') {
				$file_icon = "sound.gif";
			}elseif($file_name == 'pdf') {
				$file_icon = "pdf.gif";
			} elseif($file_name == 'ppt') {
				$file_icon = "ppt.gif";
			} elseif($file_name == 'doc') {
				$file_icon = "doc.gif";
			} elseif($file_name == 'hwp') {
				$file_icon = "hwp.gif";
			} else {
				$file_icon = "txt.gif";
			}
			$file_icon = "<IMG SRC=\"".$file_icon_path."/".$file_icon."\" border=0 class=\"img_ib\" align=\"absmiddle\">";
		} else {
			$file_icon = "";
		}

        //썸네일 이미지 링크
        if($_data->icon_type == 'tem_001'){
            if($setup['btype']=="W"){
            $widthTemplate = "100";
            $heightTemplate = "100";
            $styleTemplate = "";
            }else{

            $widthTemplate = "150";
            $heightTemplate = "150";
            $styleTemplate = "";

            }
        }else{
            $widthTemplate = "100";
            $heightTemplate = "100";
            $styleTemplate = "style='border-width:1pt; border-color:rgb(235,235,235); border-style:solid;'";
        }

        if($mypageid){
            $mini_file1="<a href='board.php?pagetype=view&view=1&num=".$row['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."&mypageid=".$mypageid."'>";
        }else{
            if($setup['btype']=="W" && !$row['vfilename']){
                $mini_file1="";
            }else{
                $mini_file1="<a href='board.php?pagetype=view&view=1&num=".$row['num']."&board=".$board."&block=".$nowblock."&gotopage=".$gotopage. "&search=".$search."&s_check=".$s_check."'>";
            }
        }

        if($row['vfilename']){
            $div = explode("|", $row['vfilename']);
        }
        $attachfileurl=$filepath."/".$div[0];
        if(file_exists($attachfileurl)) {
            if($div[0]) {
                $mini_file1.="<img src='".ImageMiniUrl($board,$div[0])."' border='0' width='".$widthTemplate."' height='".$heightTemplate."' ".$styleTemplate.">";

                $photo_img = "<img src=\"" . ImageMiniUrl($board,$div[0]) . "\" alt=\"\">";
            } else {
                if($setup['btype']=="I") {
                    $mini_file1.="<img src='images/no_img.gif' border='0' width='".$widthTemplate."' height='".$heightTemplate."' ".$styleTemplate.">";
                    $photo_img = "<img src=\"images/no_img.gif\" alt=\"\">";
                }
            }
        } else {
            if($setup['btype']=="I") {
                $mini_file1.="<img src='images/no_img.gif' border='0' width='".$widthTemplate."' height='".$heightTemplate."' ".$styleTemplate.">";
                $photo_img = "<img src=\"images/no_img.gif\" alt=\"\">";
            }
        }

        if($setup['btype']=="W" && $row['vfilename']==''){
            $mini_file1.='';
        }else{
            $mini_file1.='</a>';
        }

		include($dir."/list_main_p.php");

		$i++;
	}

	if($_data->icon_type == 'tem_001'){}
	pmysql_free_result($res);
}
?>
</ul>
</div><!-- //.photo-list-wrap -->

<div class="list-paginate">
                    <a href="#" class="prev-all">처음으로</a>
                    <a href="#" class="prev">이전</a>
                    <a href="#" class="on">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#">4</a>
                    <a href="#">5</a>
                    <a href="#">6</a>
                    <a href="#">7</a>
                    <a href="#">8</a>
                    <a href="#">9</a>
                    <a href="#">10</a>
                    <a href="#" class="next">다음</a>
                    <a href="#" class="next-all">끝으로</a>
                </div>

                <div class="btn-place-view">
                    <button class="btn-dib-function photo-event-write" type="button"><span>WRITE</span></button>
                    <button class="btn-dib-function" type="button"><span>LIST</span></button>
                </div>

                <ul class="view-move">
                    <li><span>이전글</span><a href="#">공지내용 글입니다.</a></li>
                    <li><span>다음글</span><a href="#">다음 공지글입니다.</a></li>
                </ul>

            </div><!-- //.promotion-wrap -->

        </div><!-- //공통 container -->
    </div><!-- //contents -->














<script>
function GoPage(block,gotopage) {
	document.form2.block.value=block;
	document.form2.gotopage.value=gotopage;
	document.form2.submit();
}
</script>

<?
if($_data->icon_type == 'tem_001'){
?>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name='board' value="<?=$board?>">
	<?if($checkedData['s_check']['all']['on']){?> <input type=hidden name='s_check[all]' value="<?=$checkedData['s_check']['all']['on']?>"><?}?>
	<?if($checkedData['s_check']['name']['on']){?> <input type=hidden name='s_check[name]' value="<?=$checkedData['s_check']['name']['on']?>"><?}?>
	<?if($checkedData['s_check']['subject']['on']){?> <input type=hidden name='s_check[subject]' value="<?=$checkedData['s_check']['subject']['on']?>"><?}?>
	<?if($checkedData['s_check']['contents']['on']){?> <input type=hidden name='s_check[contents]' value="<?=$checkedData['s_check']['contents']['on']?>"><?}?>
	<?if($search){?> <input type=hidden name='search' value="<?=$search?>"><?}?>
	<?if($mypageid){?><input type=hidden name=mypageid value="<?=$mypageid?>"><?}?>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=category value="<?=$category?>">
</form>
<?}else{?>
<form name=form2 method=get action="<?=$_SERVER['PHP_SELF']?>">
	<input type=hidden name=board value="<?=$board?>">
	<?if($mypageid){?><input type=hidden name=mypageid value="<?=$mypageid?>"><?}?>
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
</form>
<?}?>
<?
//include ($dir."/list_foot.php");

include ("bottom.php");
?>
