<?
header("Content-Type: text/html; charset=utf-8");
require_once(dirname(__FILE__).'/kcaptcha_config.php');
include_once('kcaptcha.php');
while(true){
  $keystring='';
  for($i=0;$i<$length;$i++)
    $keystring.=$allowed_symbols{mt_rand(0,strlen($allowed_symbols)-1)};

  if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp|ww/', $keystring))
    break;
}
session_start();
$_SESSION['captcha_keystring'] = $keystring;
$captcha = new KCAPTCHA();
$captcha->setKeyString($_SESSION['captcha_keystring']);
if($_REQUEST[session_name()]){ $_SESSION['captcha_keystring'] = $captcha->getKeyString(); }
echo md5($captcha->getKeyString());
?>