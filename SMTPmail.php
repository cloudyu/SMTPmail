<?php
function sendMail($server, $port, $username, $password, $from, $to, $subject, $body, $attachment = array()){
/*
  $server SMTP	服务器地址  
  $port SMTP	服务器端口  
  $username		用户名  
  $password		密码  
  $from			发件人, 不一定要和用户名一样  
  $to			收件人  
  $subject		邮件主题  
  $body			邮件内容  
  $attachment	附件  
  
  注:
  $attachment = array(
    array("name"=>"附件名称", "data"=>"附件数据"),
    array("name"=>"第二个附件", "data"=>"附件数据"),
    array("name"=>"第三个附件", "data"=>"附件数据")
  )
*/
  function buildHeader($headers){
    $ret = '';
    foreach($headers as $k=>$v){
      $ret.=$k . ': ' . $v . "\r\n";
    }
    return $ret;
  } 
  function inlineCode($str){
  	$str = trim($str);
  	return $str ? '=?UTF-8?B?' . base64_encode($str) . '?=' : '';
  }
  $boundary='CLOUD-YU-t='.time().rand(100,999).'-'.chr(rand(0,1)==0?rand(65,90):rand(97,122)).str_replace('=', '',base64_encode("CLOUD-YU".rand().rand()));
	$header = array(
		'Return-path' => '<'. $username .'>', 
		'Date' => date('r'),
		'From' =>  '<'. $from .'>',
		'MIME-Version' => '1.0',
		'Subject' => inlineCode($subject),
		'To' => $to,
		'Content-Type' => 'multipart/mixed; BOUNDARY="'. $boundary . '"'
	); 
	$CRLF = "\r\n";
	$data = buildHeader($header) . $CRLF;
  $header = array(
		'Content-Type' => 'text/html; charset="UTF-8"',
		'Content-Transfer-Encoding' => 'base64'
	); 
  $data .=buildHeader($header) . $CRLF. chunk_split(base64_encode($body));
  $header = array(
		'Content-Type' => 'text/plain; charset="UTF-8"',
		'Content-Transfer-Encoding' => 'base64'
	); 
  $data .='--' . $boundary . $CRLF . buildHeader($header) . $CRLF. chunk_split(base64_encode(strip_tags($body)));
  for($i=0;$i < count($attachment); $i++){
    $header = array(
      'Content-Type' => 'application/octet-stream; name="'.inlineCode($attachment[$i]['name']).'"',
      'Content-Disposition' => 'attachment; filename="'.inlineCode($attachment[$i]['name']).'"',
      'Content-Transfer-Encoding' => 'base64'
    ); 
    $data.= $CRLF . '--' . $boundary . $CRLF . buildHeader($header) . $CRLF . chunk_split(base64_encode($attachment[$i]['data']));
  }
  $data.=  "--" . $boundary . "--";
  $fp = stream_socket_client("tcp://$server:$port", $errno, $errstr, 30);
  if (!$fp) {
      echo "$errstr ($errno)<br />\n";
      return false;
  }
  fgets($fp,100);
  fwrite($fp, "HELO " . $server . $CRLF);fgets($fp,100);
  fwrite($fp, "AUTH LOGIN" . $CRLF);fgets($fp,100);
  fwrite($fp, base64_encode($username) . $CRLF);fgets($fp,100);
  fwrite($fp, base64_encode($password) . $CRLF);fgets($fp,100);
  fwrite($fp, "MAIL FROM:<" . $username . ">" . $CRLF);fgets($fp,100);
  fwrite($fp, "RCPT TO:<" . $to . ">" . $CRLF);fgets($fp,100);
  fwrite($fp, "DATA" . $CRLF);fgets($fp,100);
  fwrite($fp, $data. $CRLF . $CRLF);
  fwrite($fp, "." . $CRLF);
  $result = fgets($fp, 100);
  fwrite($fp, "QUIT" . $CRLF);
  fgets($fp,100);
  if (substr($result, 0, 3) == 250){
    return true;
  }else{
    return false;
  }
}
?>
