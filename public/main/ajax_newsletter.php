<?
Header('Content-Type: text/html; charset=euc-kr');
//include("../lib/init.php");
//include("../lib/lib.php");
//$sql = "SELECT * FROM tblnewsletter order by date DESC , no DESC";
//$result = pmysql_query($sql,get_db_conn());
//$qq ;
//	if( $row=pmysql_fetch_object($result) ) {
//			$qq= $row->html;
//	}
//pmysql_free_result($result);

$host = "localhost"; 
$user = "digiatom"; 
$pass = "digiatom0314"; 
$db = "digiatom"; 
$con = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die ("Could not connect to server\n"); 
$query = "SELECT html FROM tblnewsletter order by date DESC , no DESC limit 1";
$rs = pg_query($con, $query) or die("Cannot execute query: $query\n");
$subject_sub;
while ($row = pg_fetch_row($rs)) {
  $subject_sub = $row[0] ;
}
pg_close($con); 



$toto      = $_GET['idx'];   // 받는 사람의 이메일 주소
$to      = 'qkdrmrghks@naver.com';
//$subject = 'the subject';
$subject = $subject_sub;

//$handle=fopen("http://digiatom.ajashop.co.kr/templet/mail/news_letter.php","w");
$handle=fopen("../templet/mail/news_letter.php","w");
fwrite($handle, $subject_sub );
fclose($handle);

//$handle=fopen("http://digiatom.ajashop.co.kr/templet/mail/news_letter.php","r");
//$file_size = filesize("http://digiatom.ajashop.co.kr/templet/mail/news_letter.php");
$handle=fopen("../templet/mail/news_letter.php","r");
$file_size = filesize("../templet/mail/news_letter.php");
$content = fread($handle, $file_size);
fclose($handle);

$message = $content;
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=euc-kr' . "\r\n";
$headers .= 'From: qkdrmrghks@naver.com' . "\r\n" .
    'Reply-To: qkdrmrghks@naver.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

	$email = 	Mail($to, $subject, $message, $headers);          // 메일 전송

  if($email == ""){
   echo "
       <script>
        alert('메일전송 실패');
       </script>
      ";
  }else{
   echo "
       <script>
        alert('메일전송 성공');
       </script>
       ";
       
  }

?>
