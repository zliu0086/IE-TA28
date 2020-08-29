<?php
include 'plugin_globals.php';

if ($debug[0]) {
   error_log ('[qwiz_send_follow_ups.php] $_REQUEST: ' . print_r ($_REQUEST, true));
}
// -----------------------------------------------------------------------------
// Mail function.
function send_email ($email_address, $email_from, $reply_to, $email_copy_to,
                     $email_subject, $email_html) {
   global $debug;

   $headers = array ();
   $headers[] = "MIME-Version: 1.0";
   $headers[] = "Content-type: text/html; charset=utf-8";
   $headers[] = "From: $email_from";
   $headers[] = "Reply-To: $reply_to";
   $headers[] = "Return-Path: $reply_to";
   if ($email_copy_to) {
      $headers[] = "Bcc: $email_copy_to";
   }
   $headers[] = "X-Mailer: PHP/" . phpversion () . "";
   $headers = implode ("\r\n", $headers) . "\r\n";
   $parameters = "-f $email_from";

   $email_html = preg_replace ('/\\\\n/', "\r\n", $email_html);
   $mail_accepted_b = mail ($email_address, $email_subject, $email_html, $headers, $parameters);
   if ($debug[0]) {
      error_log ('[qwiz_send_follow_ups.php > send_email ()] $email_address:   ' . $email_address);
      error_log ('[qwiz_send_follow_ups.php > send_email ()] $headers:         ' . $headers);
      error_log ('[qwiz_send_follow_ups.php > send_email ()] $email_subject:   ' . $email_subject);
      error_log ('[qwiz_send_follow_ups.php > send_email ()] $email_html:      ' . $email_html);
      error_log ('[qwiz_send_follow_ups.php > send_email ()] $mail_accepted_b: ' . $mail_accepted_b);
   }
   if ($mail_accepted_b) {
      $errmsg = 'ok';
   } else {

      //error_log ('[qwiz_send_follow_ups.php > send_email ()] mail error: ' . $php_errormsg);
      $errmsg = 'xx';
   }

   return $errmsg;
}
// -----------------------------------------------------------------------------


if (isset ($_REQUEST['e'])) {
   print 'ok';
} else if (isset ($_REQUEST['id'])) {

   $id = $_REQUEST['id'];
   $js = file_get_contents ("$secure_server_loc/get_email_from_maker_url.php?id=$id");
   $data = json_decode ($js, true);
   $email_address = $data['email_address'];
   $email_from    = $data['email_from'];
   $reply_to      = $data['reply_to'];
   $email_subject = $data['email_subject'];
   $email_html    = $data['email_html'];
   $email_copy_to = '';
   $errmsg = send_email ($email_address, $email_from, $reply_to, $email_copy_to,
                         $email_subject, $email_html);
   print $errmsg;

} else {
   //...
}
