<?php
$Dir = '../';
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

function folder_mkdir( $folder ) {

    if( !is_dir( $folder ) ){ // 폴더 유무 확인
        mkdir( $folder, 0777 );
        chmod( $folder, 0777 );
    } else if( substr( sprintf( '%o', fileperms( $folder ) ), -4 ) != '0777' ) { // 폴더 퍼미션 확인
        chmod( $folder, 0777 );
    }

}

function append_Node( $dom, $xml, $title, $content ) {

    $currentMSG = $dom->createElement( "msg" );
    $currentMSG = $xml->appendChild( $currentMSG );
    $currentMSG->appendChild( $dom->createElement( 'title', strip_tags( $title ) ) ); // 메세지 명칭
    $currentMSG->appendChild( $dom->createElement( 'content', strip_tags( $content ) ) ); // 메세지 내용

}

function create_xml_file( $file_path, $file, $write_html ){

    if( file_exists( $file_path.$file ) ){ // 파일이 존재할경우 덮어쓰기
        $write_file = fopen( $file_path.$file, 'wb' );
        $write_succes = fwrite( $write_file, $write_html );
        fclose( $write_file );
        chmod( $file_path.$file, 0777 );
    } else { // 파일이 없을경우 생성
        $write_file = fopen( $file_path.$file, 'wb+' );
        $write_succes = fwrite( $write_file, $write_html );
        fclose( $write_file );
        chmod( $file_path.$file, 0777 );
    }

    return $write_succes;

}

$msg_mode  = $_POST['msg_mode'];
$mem_msg   = $_POST['mem_msg'];
$mem_title = $_POST['mem_title'];

$folder    = './sms_msg';
$file_path = $folder.'/';
$file      = 'mem_msg.xml';
$write_msg = '';

$write_succes = false; // 파일 쓰기 성공유무
$write_err    = false; // 파일 에러처리 flag
$write_html   = '';    // xml 내용

if( $msg_mode == 'msg_all_insert' && count( $mem_msg ) > 0 ){ // sms 메세지 일괄 입력

    folder_mkdir( $folder );

    $domtree = new DOMDocument('1.0', 'UTF-8'); // xml type

    $xmlRoot = $domtree->createElement( "massages" ); // xml root 명칭
    $xmlRoot = $domtree->appendChild( $xmlRoot );

    foreach( $mem_msg as $msgKey=>$msgVal ){ // 메세지 내용 작성
        append_Node( $domtree, $xmlRoot, $mem_title[$msgKey], $msgVal );
    }
    $write_html = $domtree->saveXML(); // xml 저장

    $write_succes = create_xml_file( $file_path, $file, $write_html );

    if( $write_succes === false ){ // 파일이 들어갔는지 확인
        $write_err = true;
    }

} else if( $msg_mode == 'msg_append' ){ // sms메세지 하나씩 입력

    folder_mkdir( $folder );

    if( file_exists( $file_path.$file ) ){
        $xml = simplexml_load_file( $file_path.$file );
        $ele = dom_import_simplexml( $xml );
        $domtree = new DomDocument( '1.0', 'UTF-8' );
        $element = $domtree->importNode( $ele, true );
        $domtree->appendChild( $element );
        $xmlRoot = $domtree->firstChild;
    } else {
        $domtree = new DOMDocument('1.0', 'UTF-8'); // xml type
        $xmlRoot = $domtree->createElement( "massages" ); // xml root 명칭
        $xmlRoot = $domtree->appendChild( $xmlRoot );
    }

    foreach( $mem_msg as $msgKey=>$msgVal ){ // 메세지 내용 작성
        append_Node( $domtree, $xmlRoot, $mem_title[$msgKey], $msgVal );
    }

    $write_html = $domtree->saveXML(); // xml 저장

    $write_succes = create_xml_file( $file_path, $file, $write_html );

    if( $write_succes === false ){ // 파일이 들어갔는지 확인
        $write_err = true;
    }


} else if( $msg_mode == 'msg_modify' ){ //  sms 메세지 수정
    $msg_idx = $_POST['msg_idx'];

    $xml = simplexml_load_file( $file_path.$file );
    $ele = dom_import_simplexml( $xml );
    $domtree = new DomDocument( '1.0', 'UTF-8' );
    $element = $domtree->importNode( $ele, true );
    $domtree->appendChild( $element );
    
    $xpath = new DOMXpath( $domtree );
    $list = $xpath->query('/massages/msg');
    $oldnode = $list->item( (int)$msg_idx );

    $replace_dom = new DomDocument;
    $replace_dom_node = $replace_dom ->createElement('msg');
    $replace_dom_node->appendChild( $replace_dom->createElement( 'title', strip_tags( $mem_title[0] ) ) );
    $replace_dom_node->appendChild( $replace_dom->createElement( 'content', strip_tags( $mem_msg[0] ) ) );
    $replace_dom->appendChild( $replace_dom_node );

    $newnode = $domtree->importNode( $replace_dom->documentElement, true );
    $oldnode->parentNode->replaceChild( $newnode, $oldnode );

    $write_html = $domtree->saveXML(); // xml 저장

    $write_succes = create_xml_file( $file_path, $file, $write_html );

    if( $write_succes === false ){ // 파일이 들어갔는지 확인
        $write_err = true;
    }
} else if( $msg_mode == 'msg_delete' ) { //sms 메세지 삭제
    $msg_idx = $_POST['delete_idx'];

    $xml = simplexml_load_file( $file_path.$file );
    $ele = dom_import_simplexml( $xml );
    $domtree = new DomDocument( '1.0', 'UTF-8' );
    $element = $domtree->importNode( $ele, true );
    $domtree->appendChild( $element );

    $xpath = new DOMXpath( $domtree );
    $list = $xpath->query('/massages/msg');
    $oldnode = $list->item( (int)$msg_idx );

    $oldnode->parentNode->removeChild( $oldnode );

    $write_html = $domtree->saveXML(); // xml 저장

    $write_succes = create_xml_file( $file_path, $file, $write_html );

    if( $write_succes === false ){ // 파일이 들어갔는지 확인
        $write_err = true;
    }
}

if( $write_succes !== false ){
    $write_msg = '등록 되었습니다.';
} else {
    $write_msg = "등록에 실패했습니다.";
}

echo '<script>';
if( $msg_mode != 'msg_delete' ) {
    echo "  alert('".$write_msg."');";
}
if( $msg_mode == 'msg_all_insert' || $msg_mode == 'msg_delete' ){
    echo '  window.location.replace("market_smssend.php");';
} else if( $msg_mode == 'msg_append' || $msg_mode == 'msg_modify' ) {
    echo '  opener.location.reload();';
    echo '  window.close();';
}
echo '</script>';

?>