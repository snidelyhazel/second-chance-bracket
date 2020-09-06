<?php

  require '../vendor/autoload.php';
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  if (is_string(getenv("AMAZON_SES_SMTP_USERNAME")))
  {
    $amazon_ses_smtp_username = getenv("AMAZON_SES_SMTP_USERNAME");
    $amazon_ses_smtp_password = getenv("AMAZON_SES_SMTP_PASSWORD");
  }
  else
  {
    include("../includes/SCB_admininfo.php");
  }

  // if (is_string(getenv("SENDGRID_API_KEY")))
  // {
  //   require 'vendor/autoload.php';
  //   $sendgrid_api_key = getenv("SENDGRID_API_KEY");
  // }
  // else
  // {
  //   require_once '../sendgrid/sendgrid-php.php';
  //   include("../includes/SCB_admininfo.php");
  // }

  include("../includes/SCB_connect.php");

  $username_or_email = $_POST["username_or_email"];
  $query_result = mysqli_query($db, "SELECT `id`, `username`, `email` FROM users WHERE `username` = '" . mysqli_real_escape_string($db, $username_or_email) . "' OR `email` = '" . mysqli_real_escape_string($db, $username_or_email) . "';");
  if (mysqli_num_rows($query_result) != 0)
  {
    $row = $query_result->fetch_assoc();
    $email_address = $row["email"];
    $username = $row["username"];

    $table = "password_reset_temp";
    $query_result = mysqli_query($db, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($query_result) == 0)
    {
      $SQLstring = "CREATE TABLE IF NOT EXISTS $table (`key` varchar(250) NOT NULL PRIMARY KEY, `email` varchar(250) NOT NULL, `expDate` datetime NOT NULL);";
      $query_result = mysqli_query($db, $SQLstring);
      if ($query_result === FALSE)
      {
        error_log("<p>Unable to create the table.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
      }
    }

    $expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
    $expDate = date("Y-m-d H:i:s", $expFormat);
    $key = md5(2418 . $email_address) . substr(md5(uniqid(rand(),1)),3,10);
    $query_result = mysqli_query($db, "INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`) VALUES ('" . mysqli_real_escape_string($db, $email_address) . "', '" . $key . "', '" . $expDate . "');");
    if ($query_result === FALSE)
    {
      error_log("<p>Unable to create password reset token.</p>" . "<p>Error code " . mysqli_errno($db) . ": " . mysqli_error($db) . "</p>");
    }
    else
    {
      $reset_url = "http://secondchancebracket.net/reset/?key=" . $key . "&email=" . $email_address;

      $message_body = '<p>Hi ' . $username . ',</p>';
      $message_body .= '<p>Please click on the following link to reset your password.</p>';
      $message_body .= '<p><a href="' . $reset_url . '" target="_blank">' . $reset_url . '</a></p>';
      $message_body .= '<p>If you are unable to click the link, please copy the entire URL into your browser. The link will expire after 1 day for security reasons.</p>';
      $message_body .= '<p>If you did not request this password reset email--or if you requested a password reset in error--no action is needed and your password will not be reset. However, you may want to log in to your account and change your password anyway, as someone may have accessed your account.</p>';
      $message_body .= '<p>Cheers,<br/>Ashley</p>';

      $message_plaintext = "Hi " . $username . ",\r\n\r\n";
      $message_plaintext .= "Please click on the following link to reset your password.\r\n\r\n";
      $message_plaintext .= $reset_url . "\r\n\r\n";
      $message_plaintext .= "If you are unable to click the link, please copy the entire URL into your browser. The link will expire after 1 day for security reasons.\r\n\r\n";
      $message_plaintext .= "If you did not request this password reset email--or if you requested a password reset in error--no action is needed and your password will not be reset. However, you may want to log in to your account and change your password anyway, as someone may have accessed your account.\r\n\r\n";
      $message_plaintext .= "Cheers,\r\nAshley";

      // $email = new \SendGrid\Mail\Mail();
      // $email->setFrom("ashley@hockeypostgamebar.com", "Ashley");
      // $email->setSubject("Password Reset for secondchancebracket.net");
      // $email->addTo($email_address, $username);
      // $email->addContent("text/plain", $message_plaintext);
      // $email->addContent("text/html", $message_body);
      // $sendgrid = new \SendGrid($sendgrid_api_key);
      // try {
      //     $response = $sendgrid->send($email);
      //     print $response->statusCode() . "\n";
      //     print_r($response->headers());
      //     print $response->body() . "\n";
      // } catch (Exception $e) {
      //     echo 'Caught exception: '. $e->getMessage() ."\n";
      // }


      // Replace sender@example.com with your "From" address.
      // This address must be verified with Amazon SES.
      $sender = 'ashley@hockeypostgamebar.com';
      $senderName = 'Ashley';

      // Replace recipient@example.com with a "To" address. If your account
      // is still in the sandbox, this address must be verified.
      $recipient = $email_address;

      // Replace smtp_username with your Amazon SES SMTP user name.
      $usernameSmtp = $amazon_ses_smtp_username;

      // Replace smtp_password with your Amazon SES SMTP password.
      $passwordSmtp = $amazon_ses_smtp_password;

      // Specify a configuration set. If you do not want to use a configuration
      // set, comment or remove the next line.
      // $configurationSet = 'ConfigSet';

      // If you're using Amazon SES in a region other than US West (Oregon),
      // replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
      // endpoint in the appropriate region.
      $host = 'email-smtp.us-west-2.amazonaws.com';
      $port = 587;

      // The subject line of the email
      $subject = 'Password Reset for secondchancebracket.net';

      // The plain-text body of the email
      $bodyText =  $message_plaintext;

      // The HTML-formatted body of the email
      $bodyHtml = $message_body;

      $mail = new PHPMailer(true);

      try {
          // Specify the SMTP settings.
          // $mail->SMTPDebug = 3;
          $mail->isSMTP();
          $mail->setFrom($sender, $senderName);
          $mail->Username   = $usernameSmtp;
          $mail->Password   = $passwordSmtp;
          $mail->Host       = $host;
          $mail->Port       = $port;
          $mail->SMTPAuth   = true;
          $mail->SMTPSecure = 'tls';
          // $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);

          // Specify the message recipients.
          $mail->addAddress($email_address, $username);
          // You can also add CC, BCC, and additional To recipients here.

          // Specify the content of the message.
          $mail->isHTML(true);
          $mail->Subject    = $subject;
          $mail->Body       = $bodyHtml;
          $mail->AltBody    = $bodyText;
          $mail->Send();
          echo "Email sent!" , PHP_EOL;
      } catch (phpmailerException $e) {
          echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
      } catch (Exception $e) {
          echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
      }
    }
  }
?>
