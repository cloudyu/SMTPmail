# SMTPmail
SMTP发送邮件

使用 `SMTP` 发送邮件

sendMail($server, $port, $username, $password, $from, $to, $subject, $body, $attachment = array())
$server SMTP服务器地址  
$port SMTP服务器端口  
$username 用户名  
$password 密码  
$from 发件人, 不一定要和用户名一样  
$to 收件人  
$subject 邮件主题  
$body 邮件内容  
$attachment 附件  

注:
```
$attachment = array(
  array("name"=>"附件名称", "data"=>"附件数据"),
  array("name"=>"第二个附件", "data"=>"附件数据"),
  array("name"=>"第三个附件", "data"=>"附件数据")
)
```
