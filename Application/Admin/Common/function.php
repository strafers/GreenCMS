<?php
/**
 * Created by Green Studio.
 * File: function.php
 * User: TianShuo
 * Date: 14-1-14
 * Time: 下午11:09
 */



/**
+----------------------------------------------------------
 * 功能：系统邮件发送函数
+----------------------------------------------------------
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
+----------------------------------------------------------
 * @param string $config
 * @return boolean
+----------------------------------------------------------
 */
function send_mail($to, $name, $subject = '', $body = '', $attachment = null, $config = '')
{
    // $config = is_array($config) ? $config : C('SYSTEM_EMAIL');
    //从数据库读取smtp配置
    $config = array(
        'smtp_host' => C('smtp_host'),
        'smtp_port' => C('smtp_port'),
        'smtp_user' => C('smtp_user'),
        'smtp_pass' => C('smtp_pass'),
        'from_email' => C('from_email'),
        'from_name' => C('title')
    );

    // Log::write(array_to_str($config));
    //TODO 这里类的引用方式不够THINKPHP :)
    include Extend_PATH . 'PHPMailer/phpmailer.class.php'; //从PHPMailer目录导phpmailer.class.php类文件
    $mail = new PHPMailer(); //PHPMailer对象
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
//    $mail->IsHTML(true);
    $mail->SMTPDebug = 1; // 关闭SMTP调试功能 1 = errors and messages2 = messages only
    $mail->SMTPAuth = true; // 启用 SMTP 验证功能
    if ($config['smtp_port'] == 465)
        $mail->SMTPSecure = 'ssl'; // 使用安全协议
    $mail->Host = $config['smtp_host']; // SMTP 服务器
    $mail->Port = $config['smtp_port']; // SMTP服务器的端口号
    $mail->Username = $config['smtp_user']; // SMTP服务器用户名
    $mail->Password = $config['smtp_pass']; // SMTP服务器密码
    $mail->SetFrom($config['from_email'], $config['from_name']);
    $replyEmail = $config['reply_email'] ? $config['reply_email'] : $config['reply_email'];
    $replyName = $config['reply_name'] ? $config['reply_name'] : $config['reply_name'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if (is_array($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            if (is_array($file)) {
                is_file($file['path']) && $mail->AddAttachment($file['path'], $file['name']);
            } else {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
    } else {
        is_file($attachment) && $mail->AddAttachment($attachment);
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}


//TODO 已经移到Dir.class中了 即将删除

function delDirAndFile($path, $delDir = FALSE)
{
    $handle = opendir($path);
    if ($handle) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    } else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}
