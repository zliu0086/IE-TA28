<?php

// Navigated to by qwizcards.com/admin/payment_received.php (which was called by
// PayPal).
//
// Now that we're no longer cross-host, can call opener.
?>
<!DOCTYPE HTML>
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8">
   <meta charset="UTF-8">
   <title>
      Payment return
   </title>

   <script>
      //if (window.opener && window.opener.qwiz_qcards_common) {
      //   window.opener.qwiz_qcards_common.payment_received ();
      //} else {
         alert ('Please return to and/or reload your web page with quizzes/flashcard decks');
      //}
      window.close ();
</script>
</head>
<body>
</body>
</html>
