<?php
class emails extends db_connect
{
    private $requestFrom = 0;

    public function __construct($dbo = NULL)
    {
        parent::__construct($dbo);

    }

    public function sendVerificationEmail($name, $email, $verifyLink, $appName){

        $subject = $appName . ' | Email Verification';

        $message_html = '<html><body>Hi ' . $name . ',<br><br>Please verify your email by clicking this link:<br><a href="' . $verifyLink . '">' . $verifyLink . '</a><br><br>This link expires in 24 hours.</body></html>';

        $message_text = 'Hi ' . $name . ', Please verify your email by clicking this link: ' . $verifyLink;

        $result = array("error" =>  true, "error_code" => 404, "error_message" => "Invalid smtp settings");

        $configs = new functions($this->db);

        $SMTP_AUTH = $configs->getConfig('SMTP_AUTH');
        $SMTP_HOST = $configs->getConfig('SMTP_HOST');
        $SMTP_USERNAME = $configs->getConfig('SMTP_USERNAME');
        $SMTP_EMAIL = $configs->getConfig('SMTP_EMAIL');
        $SMTP_PASSWORD = $configs->getConfig('SMTP_PASSWORD');
        $SMTP_SECURE = $configs->getConfig('SMTP_SECURE');
        $SMTP_PORT = $configs->getConfig('SMTP_PORT');

        if ($SMTP_SECURE === 'TLS') { $SMTP_SECURE = PHPMailer::ENCRYPTION_STARTTLS; }

        $mail = new PHPMailer(true);

        try {
            if($SMTP_AUTH){ $mail->isSMTP(); }
            $mail->Host = $SMTP_HOST;
            $mail->SMTPAuth = $SMTP_AUTH;
            $mail->Username = $SMTP_USERNAME;
            $mail->Password = $SMTP_PASSWORD;
            $mail->SMTPSecure = $SMTP_SECURE;
            $mail->Port = $SMTP_PORT;

            $mail->setFrom($SMTP_EMAIL, $appName);
            $mail->addAddress($email);
            $mail->addReplyTo($SMTP_EMAIL, $appName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message_html;
            $mail->AltBody = $message_text;

            $mail->send();

            $result['error'] = false;
            $result['error_code'] = 100;
            $result['error_message'] = 'Verification email sent.';

        }catch(Exception $e){
            $result['error'] = true;
            $result['error_code'] = 400;
            $result['error_message'] = 'Error sending verification email: '.$mail->ErrorInfo;
        }

        return $result;
    }

    public function sendPasswordResetEmail($name, $email, $hash){
        
        /*  EMAIL PARAMS WHICH WILL BE REPLACED WHILE SENDING THE EMAIL
        
            {user_name} = Name of the user
            {user_email} = Email of the user
            {app_name} = Application Name of the Script Insatllation (Your App Name from Admin->Settings)
            {reset_link} = Actual Password Reset Link
        
        */
        
        $subject = '{app_name} | Password reset';
        
        $message_html = '<html><body>Hi {user_name},<br><br>This is the link <a href="{reset_link}">{reset_link}</a> to reset your {app_name} account password.</body></html>';
        
        $message_text = 'Hi {user_name}, This is the link to reset your {app_name} account password : {reset_link}';
        
        $result = array("error" =>  true, "error_code" => 404, "error_message" => "Invalid smtp settings");
        
        $configs = new functions($this->db);
        
        $APP_NAME = $configs->getConfig('APP_NAME');
        $APP_URL = $configs->getConfig('WEB_ROOT');
        
        $reset_link = $APP_URL.'admin/restore/?hash='.$hash;
        
        $SMTP_AUTH = $configs->getConfig('SMTP_AUTH');
        $SMTP_HOST = $configs->getConfig('SMTP_HOST');
        $SMTP_USERNAME = $configs->getConfig('SMTP_USERNAME');
        $SMTP_EMAIL = $configs->getConfig('SMTP_EMAIL');
        $SMTP_PASSWORD = $configs->getConfig('SMTP_PASSWORD');
        $SMTP_SECURE = $configs->getConfig('SMTP_SECURE');
        $SMTP_PORT = $configs->getConfig('SMTP_PORT');
        
        if ($SMTP_SECURE === 'TLS') { $SMTP_SECURE = PHPMailer::ENCRYPTION_STARTTLS; }

        
        $message_html = str_replace("{user_name}",$name,$message_html);
        $message_html = str_replace("{reset_link}",$reset_link,$message_html);
        $message_html = str_replace("{app_name}",$APP_NAME,$message_html);
        $message_html = str_replace("{user_email}",$email,$message_html);
        
        $message_text = str_replace("{user_name}",$name,$message_text);
        $message_text = str_replace("{reset_link}",$reset_link,$message_text);
        $message_text = str_replace("{app_name}",$APP_NAME,$message_text);
        $message_text = str_replace("{user_email}",$email,$message_text);
        
        $subject = str_replace("{user_name}",$name,$subject);
        $subject = str_replace("{reset_link}",$reset_link,$subject);
        $subject = str_replace("{app_name}",$APP_NAME,$subject);
        $subject = str_replace("{user_email}",$email,$subject);
        
        
        $mail = new PHPMailer(true);
        
        try {
        
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // FOR DEV USE ONLY
            if($SMTP_AUTH){ $mail->isSMTP(); }
            $mail->Host = $SMTP_HOST;
            $mail->SMTPAuth = $SMTP_AUTH;
            $mail->Username = $SMTP_USERNAME;
            $mail->Password = $SMTP_PASSWORD;
            $mail->SMTPSecure = $SMTP_SECURE;
            $mail->Port = $SMTP_PORT;
        
            //Recipient
            $mail->setFrom($SMTP_EMAIL, $APP_NAME);
            $mail->addAddress($email);
            $mail->addReplyTo($SMTP_EMAIL, $APP_NAME);
            
            // NOT NEEDED - UNCOMMENT THE BELOW LINES IF YOU WISH TO GET YOUR SELF A COPY OF THIS EMAIL
            // $mail->addCC($SMTP_EMAIL); 
            // $mail->addBCC($SMTP_EMAIL);
        
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message_html;
            $mail->AltBody = $message_text;
        
            $mail->send();
            
            $result['error'] = false;
            $result['error_code'] = 100;
            $result['error_message'] = 'Password Reset Link has been sent to the email address. if you didn\'t find our email then please check your spam folder.';
        
        }catch(Exception $e){
            //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            
            $result['error'] = true;
            $result['error_code'] = 400;
            $result['error_message'] = 'Error while sending Password Reset email. Error details : '.$mail->ErrorInfo;
            
        }
        
        return $result;
    }
    
}

