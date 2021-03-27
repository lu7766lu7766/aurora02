<?php

class Library_Mailer
{

    public function __construct()
    {
        $this->base = $base;
    }

    public function postal($paddAddress, $pname, $pSubject, $pBody, $pAltBody = 'This is a plain-text message body')
    { //收信人Email, 收信人名稱, 寄件標題, 信件HTML, 非HTML顯示
        require_once dirname(__FILE__) . "/PHPMailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;
        //$mail->SMTPDebug = 3;									// Enable verbose debug output
        $mail->isSMTP();                                        // Set mailer to use SMTP
        $mail->Host = 'smtp.exmail.qq.com';                        // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                    // Enable SMTP authentication
        $mail->Username = 'register@bigwayasia.com';            // SMTP username
        $mail->Password = 'bigWayReg54379276';                    // SMTP password
        $mail->SMTPSecure = 'ssl';                                // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                        // TCP port to connect to
        $mail->From = 'register@bigwayasia.com';


        $mail->FromName = 'Global2buy System';
        $mail->addAddress($paddAddress, $pname);                // Add a recipient
        //$mail->addAddress('ellen@example.com');				// Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
        //$mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name
        $mail->isHTML(true);                                    // Set email format to HTML
        $mail->CharSet = "UTF-8";


        $mail->Subject = $pSubject;
        $mail->Body = $pBody;
        $mail->AltBody = $pAltBody;

        if (!$mail->send())
        {
            $this->mailer_content = '1';
        }
        else
        {
            $this->mailer_content = '0';
        }

        return $this;
    }
}

?>