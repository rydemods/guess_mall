<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//http://test-hott.ajashop.co.kr/partner/call_make_syndi.php?menu=forum&bbsno=44&type=reg

$shopurl = "http://test-hott.ajashop.co.kr";

$menu = $_GET["menu"];
$bbsno = $_GET["bbsno"];
$type = $_GET["type"];  // reg or del

if($menu == "forum") {
    $syndi_id = $shopurl."/front/forum_view.php?index=".$bbsno;  // 실제 상세 페이지 주소
    $syndi_link = $shopurl."/front/forum_main.php";    // 한단계 위 리스트 주소 or 해당 메뉴 메인 주소

    $sql = "select id, summary||title as subject, content from tblforumlist where index = ".$bbsno;
    list($name, $subject, $contents) = pmysql_fetch($sql);
    //$subject = "10월에 가족들과 일본온천여행 가려고 합니다.";
    //$name = "ikazeus@naver.com";
    //$contents = "일본 온천 여행 정보 추천해주세요~~";

} else if($menu == "magazine") {
    $syndi_id = $shopurl."/front/magazine_detail.php?no=".$bbsno;  // 실제 상세 페이지 주소
    $syndi_link = $shopurl."/front/magazine_list.php";    // 한단계 위 리스트 주소 or 해당 메뉴 메인 주소

    $sql = "select writer, title, content from tblmagazine where no = ".$bbsno;
    list($name, $subject, $contents) = pmysql_fetch($sql);

} else if($menu == "lookbook") {
    $syndi_id = $shopurl."/front/lookbook_view.php?no=".$bbsno;  // 실제 상세 페이지 주소
    $syndi_link = $shopurl."/front/lookbook_list.php";    // 한단계 위 리스트 주소 or 해당 메뉴 메인 주소

    $sql = "select 'webmaster' as id, title, content from tbllookbook where no = ".$bbsno;
    list($name, $subject, $contents) = pmysql_fetch($sql);

} else if($menu == "product") {
    $syndi_id = $shopurl."/front/productdetail.php?productcode=".$bbsno;  // 실제 상세 페이지 주소
    $syndi_link = $shopurl;    // 한단계 위 리스트 주소 or 해당 메뉴 메인 주소

    $sql = "select 'webmaster' as id, productname, content from tblproduct where productcode = '".$bbsno."'";
    list($name, $subject, $contents) = pmysql_fetch($sql);
}

if($type == "reg") {
    // 등록, 수정
    $data = array(
                    'id' => $shopurl,
                    'title' => 'Naver Syndication Sample Document',
                    'author' => 'webmaster',
                    'updated' => time(),
                    'updated_entry' => array(
                        array(  'id'=>$syndi_id,
                                'title'=>$subject,
                                'author'=>$name,
                                'updated'=>time(),
                                'published'=>time(),
                                'link'=>$syndi_link,
                                'content'=>$contents, 
                            ),
                    ),
                );
} else {

    // 삭제
    $data = array(
                    'id' => $shopurl,
                    'title' => 'Naver Syndication Sample Document',
                    'author' => 'webmaster',
                    'updated' => time(),
                    'deleted_entry' => array(
                        array('ref'=>$syndi_id,'when'=>time()),
                    ),
                );
}

$doc = new DomDocument('1.0', 'UTF-8');

$feed = $doc->createElementNS('http://webmastertool.naver.com','feed');
$doc->appendChild($feed);

$id = $doc->createElement('id',$data['id']);
$title = $doc->createElement('title',$data['title']);
$author = $doc->createElement('author');
$name = $doc->createElement('name',$data['author']);
$author->appendChild($name);

$updated = $doc->createElement('updated',date( DateTime::RFC3339, $data['updated'] ));


$feed->appendChild($id);
$feed->appendChild($title);
$feed->appendChild($author);
$feed->appendChild($updated);


foreach($data['updated_entry'] as $element) {
	
	$entry = $doc->createElement('entry');
	$id = $doc->createElement('id',htmlentities($element['id']));
	$cdata = $doc->createCDATASection($element['title']);
	$title = $doc->createElement('title');
	$title->appendChild($cdata);

	$author = $doc->createElement('author');
	$name = $doc->createElement('name',$element['author']);
	$author->appendChild($name);

	$updated = $doc->createElement('updated',date( DateTime::RFC3339, $element['updated'] ));
	$published = $doc->createElement('published',date( DateTime::RFC3339, $element['published'] ));

	$link = $doc->createElement('link');
	$link->setAttribute('rel','via');
	$link->setAttribute('href',$element['link']);

	$cdata = $doc->createCDATASection($element['content']);
	$content = $doc->createElement('content');
	$content->appendChild($cdata);
	$content->setAttribute('type','html');

	$entry->appendChild($id);
	$entry->appendChild($title);
	$entry->appendChild($author);
	$entry->appendChild($updated);
	$entry->appendChild($published);
	$entry->appendChild($link);
	$entry->appendChild($content);

	$feed->appendChild($entry);

}

foreach($data['deleted_entry'] as $element) {
	$deleted = $doc->createElement('deleted-entry');
	$deleted->setAttribute('ref',$element['ref']);
	$deleted->setAttribute('when',date( DateTime::RFC3339, $element['when'] ));

	$feed->appendChild($deleted);
}

$xml = $doc->saveXML();
header('Content-type: text/xml');
echo $xml;

?>