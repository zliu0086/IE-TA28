<?php

// Navigated to by qwizcards.com/admin/register_v2.php > return_via_local_server ().
// Now that we're no longer cross-host, can call opener.

include 'plugin_globals.php';

if ($debug[0]) {
   error_log ('[registration_complete.php] $_REQUEST: ' . print_r ($_REQUEST, true));
}
$qname       = $_REQUEST['qname'];
$i_qwiz      = $_REQUEST['i_qwiz'];
$session_id  = $_REQUEST['session_id'];
$username    = $_REQUEST['username'];

if ($qname != 'qwiz_' && $qname != 'qcard_') {
   exit;
}
if (! is_numeric ($i_qwiz)) {
   exit;
}
$session_id = filter_var (urldecode ($session_id), FILTER_SANITIZE_STRING);
$username   = filter_var (urldecode ($username),   FILTER_SANITIZE_STRING);

$escaped_username = addslashes ($username);
?>
<!DOCTYPE HTML>
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8">
   <meta charset="UTF-8">
   <title>
      Return to quiz/deck page
   </title>

   <script>

      //if (window.opener && window.opener.qwiz_qcards_common) {
      //   window.opener.qwiz_qcards_common.registration_complete (<?php print "'$qname', $i_qwiz, $session_id, '$escaped_username'" ?>);
      //} else {
         alert ('Please return to web page with quizzes/flashcard decks and log in there');
      //}
      window.close ();

</script>
</head>
<body>
</body>
</html>
