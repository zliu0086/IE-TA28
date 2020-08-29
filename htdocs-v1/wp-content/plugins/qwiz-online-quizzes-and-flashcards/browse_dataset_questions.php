<?php
// Called from qwizard.js.

include 'plugin_globals.php';

// Agnostic server loc.
$server_loc = preg_replace ('/https*:/', '', $server_loc);

// Retrieve current user email address, if available.
//DKTMP
//$maker_email = '';
//if (isset ($_COOKIE['maker_session_id'])) {
//   $no_classes_f = true;
//   include "$server_loc/maker_classes_from_session_id.php";
//}
if ($debug[0]) {
   error_log ('[browse_dataset_questions.php] $_REQUEST: ' . print_r ($_REQUEST, true));
}
$records_per_query = 25;

// -----------------------------------------------------------------------------
function selected ($option, $value) {
   $selected = '';
   if ($option == $value) {
      $selected = 'selected';
   }

   return $selected;
}


// -----------------------------------------------------------------------------
// For qjax.
$server_name = $_SERVER['SERVER_NAME'];
$qjax_bypass = $server_name == 'sciencemusicvideos.com' || $server_name == 'qwizcards.com' || $server_name == 'sciencemusicvideos.qwizcards.com' || $server_name == 'biology180.com' || $server_name == 'localhost';

$plugin_loc  = '.';
if (isset ($_REQUEST['maker_email'])) {
   $maker_email = $_REQUEST['maker_email'];
} else {
   $maker_email = '';
}
if (isset ($_REQUEST['qwiz_deck'])) {
   $qwiz_deck = $_REQUEST['qwiz_deck'];
} else {
   $qwiz_deck = 'qwiz';
}
if (isset ($_REQUEST['qwizard_page'])) {
   $qwizard_page = $_REQUEST['qwizard_page'];
   $qwizard_page = filter_var (urldecode ($qwizard_page), FILTER_SANITIZE_STRING);
} else {
   $qwizard_page = '';
}

if (isset ($_REQUEST['qwizard_php_f'])) {
   $qwizard_php_f = $_REQUEST['qwizard_php_f'];
   $qwizard_php_f = $qwizard_php_f ? 1 : 0;
} else {
   $qwizard_php_f = 0;
}
if (isset ($_REQUEST['browse_records_shown'])) {
   $browse_records_shown = $_REQUEST['browse_records_shown'];
} else {
   $browse_records_shown = $records_per_query;
}
if ($qwiz_deck == 'qwiz') {
   $q_f = 'Q';
   $question_card = 'question';
} else {
   $q_f = 'F';
   $question_card = 'card';
}
$search_words_array_json = '""';
$search_words_string     = '';
if (isset ($_REQUEST['browse_search_words_array'])) {
   $search_words_array_json = $_REQUEST['browse_search_words_array'];
   $search_words_array  = json_decode ($search_words_array_json, true);
   if (gettype ($search_words_array) == 'array') {
      $search_words_string = implode (' ', $search_words_array);
   }
   if ($debug[0]) {
      error_log ('[browse_dataset_questions.php] $search_words_array_json: ' . print_r ($search_words_array_json, true));
      error_log ('[browse_dataset_questions.php] gettype ($search_words_array):' .  gettype ($search_words_array));
      error_log ('[browse_dataset_questions.php] $search_words_string: ' . $search_words_string);
   }
}
$search_question_card_type = '';
if (isset ($_REQUEST['browse_search_question_card_type'])) {
   $search_question_card_type = $_REQUEST['browse_search_question_card_type'];
}
$reinit_scrollY = 0;
if (isset ($_REQUEST['browse_scrollY'])) {
   $reinit_scrollY = $_REQUEST['browse_scrollY'];
}
$question_types_display = array ('Multiple choice', 'Hangman', 'Free-form input',
                                 'One-letter answer', 'Labeled diagram',
                                 'Show me the answer', 'Information only');
$card_types_display     = array ('Simple card', 'Hangman', 'Free-form input',
                                 'One-letter answer');
$question_card_type_display = array (
   'multiple_choice'    => 'Multiple choice',
   'hangman'            => 'Hangman',
   'textentry'          => 'Free-form input',
   'one_letter_answer'  => 'One-letter answer',
   'labeled_diagram'    => 'Labeled diagram',
   'show_me'            => 'Show me the answer',
   'information_only'   => 'Information only',
   'simple_card'        => 'Simple card',
   'optional_textentry' => 'Optional text input',
   ''                   => '');

?>
<!DOCTYPE HTML>
<html>
<head>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8">
   <meta charset="UTF-8">
   <title>
      <?php print ucfirst ($question_card) ?> from database
   </title>

   <script src="<?php print $server_loc ?>/jquery.min.js"></script>
   <script src="<?php print $server_loc ?>/jquery-ui.js"></script>

   <!-- For jjax ().  Need to pass it server loc (qwiz_params not otherwise
        set). -->
   <script>
      var qwiz_params = {server_loc:        '<?php print $server_loc ?>',
                         secure_server_loc: '<?php print $secure_server_loc ?>',
                         qjax_bypass:       '<?php print $qjax_bypass ?>'
                        };
   </script>
   <script src="<?php print $plugin_loc ?>/qwiz_qcards_common.js"></script>

   <script src="<?php print $server_loc ?>/featherlight.js" type="text/javascript" charset="utf-8"></script>

   <link rel="stylesheet" href="<?php print $server_loc ?>/jquery-ui.css">
   <link rel="stylesheet" href="<?php print $server_loc ?>/jquery-ui.smoothness.css">

   <link href="<?php print $server_loc ?>/featherlight.min.css" type="text/css" rel="stylesheet" />

   <script>

      // Globals.
      var $ = jQuery;
      var qqc;
      var debug         = '<?php print $debug[0] ?>';
      var qwizard_page  = '<?php print $qwizard_page ?>';
      var q_f           = '<?php print $q_f ?>';
      var question_card = '<?php print $question_card ?>';
      var qwizard_php_f = <?php print $qwizard_php_f ?>;

      var question_card_type_display = <?php print json_encode ($question_card_type_display) ?>;

      var dataset_question_ids    = [];
      var question_card_page_urls = [];
      var skip_records;
      var records_per_query    = <?php print $records_per_query ?>;
      var more_records_available_f;
      var browse_records_shown = <?php print $browse_records_shown ?>;
      var ii_row = 0;

      var input_search_words        = '';
      var search_words_array        = <?php print ($search_words_array_json) ?>;
      var search_question_card_type = '<?php print $search_question_card_type ?>';
      var reinit_scrollY            = <?php print $reinit_scrollY ?>;

      var $dialog_request_permission;
      var $dialog_dataset_question_page_link;
      var requested_dataset;
      var requested_question_card_page_url;
      var request_permission_email_msg_body;

      // -----------------------------------------------------------------------
      $ (document).ready (function () {
         qqc = qwiz_qcards_common;

         // Set up questions/cards table.
         create_questions_cards_table ();

         // Get initial set of questions/cards from database and display.
         // If previously browsed more than base set, recover that state.
         skip_records = 0;
         get_data (browse_records_shown, search_words_array, search_question_card_type);

         // Dialog box for requesting permission to use dataset.
         $dialog_request_permission = $ ('#dialog_request_permission').dialog ({
            autoOpen:      false,
            width:         700,
            height:        500,
            modal:         true,
            draggable:     false,
            resizable:     false
         });

         // Dialog box for link to dataset question web page.
         $dialog_dataset_question_page_link = $ ('#dialog_dataset_question_page_link').dialog ({
            autoOpen:      false,
            width:         500,
            height:        350,
            modal:         true,
            draggable:     false,
            resizable:     false,
            buttons:       {'Close':        function () {
                                               $dialog_dataset_question_page_link.dialog ('close');
                                            }
                           }
         });
      });


      // -----------------------------------------------------------------------
      function get_data (local_browse_records_shown, search_words_array, search_question_card_type) {
         var data = {qwizard_page:         qwizard_page,
                     q_f:                  q_f,
                     skip_records:         skip_records,
                     records_per_query:    local_browse_records_shown
                    };
         if (search_words_array.length) {
            data.search_words_array = JSON.stringify (search_words_array);
         }
         if (search_question_card_type) {
            data.search_question_card_type = search_question_card_type;
         }
         if (debug) {
            console.log ('[browse_dataset_questions.php > get_data ()] data:', data);
         }
         qqc.jjax ('', 0, 0, 'browse_dataset_questions_sql', data);
      }


      // -----------------------------------------------------------------------
      function full_text_search (input_el) {

         // Global.
         input_search_words = trim (input_el.value);
         if (debug) {
            console.log ('[full_text_search] input_search_words:', input_search_words);
         }
         search_words_array = '';
         if (input_search_words.length) {

            // Condense whitespace.
            input_search_words = input_search_words.replace (/[-\s]+/g, ' ').toUpperCase ();

            // Keep only alpha and blank.
            input_search_words = input_search_words.replace (/[^A-Z ]/g, '');
            search_words_array = input_search_words.split (/\s+/);
            if (debug) {
               console.log ('[full_text_search] search_words_array:', search_words_array);
            }

            // Must be at least one word > 3 characters.
            var ok_f = false;
            var n_words = search_words_array.length;
            for (var i=0; i<n_words; i++) { if (search_words_array[i].length > 3) {
                  ok_f = true;
                  break;
               }
            }
            if (! ok_f) {
               alert ('Word must be at least four letters or longer');
               return;
            }
         }
         new_search ();
      }


      // -----------------------------------------------------------------------
      function new_search () {

         // New search: reset record counts.  If no search words, will redo
         // table without full-text search.  If no question/card type, get any.
         skip_records            = 0;
         ii_row                  = 0;
         browse_records_shown    = records_per_query;
         dataset_question_ids    = [];
         question_card_page_urls = [];

         // Cancel scroll event listener.
         window.removeEventListener ('scroll', detect_scroll_near_bottom);

         // Erase rows, add new.
         $ ('#questions_cards_tbody').html ('');
         get_data (records_per_query, search_words_array, search_question_card_type);
      }


      // -----------------------------------------------------------------------
      function question_card_type_search (select_el) {
         var display_type = select_el.value;
         search_question_card_type = findKey (question_card_type_display, display_type);
         if (debug) {
            console.log ('[question_card_type_search] display_type:', display_type, 'search_question_card_type:', search_question_card_type);
         }
         new_search ();
      }


      // -----------------------------------------------------------------------
      // Alert in caller to redirect focus.  Caller will try to close this
      // window.
      function cancel_browse () {
         if (window.opener && window.opener.qwizard) {
            window.opener.qwizard.cancel_browse_alert (question_card);
         }

         // In case nothing above works!
         window.close ();
      }


      // -----------------------------------------------------------------------
      function create_questions_cards_table () {
         if (debug) {
            console.log ('[create_questions_cards_table]');
         }
         var Question_Card;
         if (q_f == 'Q') {
            Question_Card       = 'Question';
         } else {
            Question_Card       = 'Card';
         }

         var htm = [];
         htm.push ('<table id="questions_cards" class="questions_cards">');
         htm.push (   '<thead>');
         htm.push (      '<tr>');
         htm.push (         '<th>');
         htm.push (            'Type');
         htm.push (         '</th>');
         htm.push (         '<th>');
         //                     add icon
         htm.push (         '</th>');
         htm.push (         '<th>');
         if (q_f == 'Q') {
            htm.push (         Question_Card + '&nbsp;text');
         } else {
            htm.push (         '<table class="card_front_back">');
            htm.push (            '<thead>');
            htm.push (               '<tr>');
            htm.push (                  '<th>');
            htm.push (                     'Card front');
            htm.push (                  '</th>');
            htm.push (                  '<th>');
            htm.push (                     'Card back');
            htm.push (                  '</th>');
            htm.push (               '</tr>');
            htm.push (            '</thead>');
            htm.push (         '</table>');
         }
         htm.push (         '</th>');
         htm.push (         '<th class="center">');
         htm.push (            Question_Card + '&nbsp;image');
         htm.push (            '<br />');
         htm.push (            '<span class="normal smaller">');
         htm.push (               '(hover to enlarge)');
         htm.push (            '</span>');
         htm.push (          '</th>');
         htm.push (      '</tr>');
         htm.push (   '</thead>');

         htm.push (   '<tbody id="questions_cards_tbody">');
         htm.push (    '</tbody>');
         htm.push ('</table>');

         htm = htm.join ('\n');
         $ ('div#questions_cards_table').html (htm);
      }


      // -----------------------------------------------------------------------
      function add_to_questions_cards_table (data) {
         if (debug) {
            console.log ('[add_to_questions_cards_table] data:', data);
         }
         var quiz_flashcard_deck;
         if (q_f == 'Q') {
            quiz_flashcard_deck = 'quiz';
         } else {
            quiz_flashcard_deck = 'flashcard_deck';
         }

         var htmls                = data.htmls;
         var n_additional_questions_cards = htmls.length;
         if (n_additional_questions_cards > 0) {

            // Update feedback, then fade.
            $ ('#getting_more').html ('Added ' + n_additional_questions_cards + ' ' + question_card + 's').fadeOut (4000, 'easeInCubic');
         } else {
            $ ('#getting_more').hide ();
            var msg =  '<tr>'
                     +    '<td colspan="4">'
                     +       '<br />'
                     +       '<span class="red">Did not find any ' + question_card + 's matching ';
               if (search_words_array) {
                  msg +=        '&ldquo;' + input_search_words.toLowerCase () + '&rdquo;';
                  if (search_question_card_type) {
                     msg +=     ' and ';
                  }
               }
               if (search_question_card_type) {
                  msg +=        '&ldquo;' + question_card_type_display[search_question_card_type] + '&rdquo;';
               }
               msg +=        '</span>';
                     +    '</td>';
                     + '</tr>';
               $ ('#questions_cards_tbody').append (msg);
            return;
         }

         var browse_image_urls          = data.browse_image_urls;
         var thumbnail_urls             = data.thumbnail_urls;
         var question_card_types        = data.question_card_types;
         var permission_requested_fs    = data.permission_requested_fs;
         var permission_requested_dates = data.permission_requested_dates;
         var permission_ok_fs           = data.permission_ok_fs;
         var records_queried            = data.records_queried;
         more_records_available_f       = data.more_records_available_f;

         var add_question_card_title = 'Add ' + question_card + ' to ' + quiz_flashcard_deck;
         var request_permission_title = 'Click to open form to request permission to use ' + question_card + 's from this dataset on your site/page';
         var htm = [];
         for (var i_row=0; i_row<n_additional_questions_cards; i_row++) {

            // Create a table row.
            htm.push ('<tr class="i_row' + ii_row + '">');

            var question_card_type = question_card_types[i_row];
            htm.push (   '<td class="pad_top">');
            htm.push (      question_card_type_display[question_card_type]);
            htm.push (   '</td>');

            htm.push (   '<td class="pad_top center">');
            if (permission_ok_fs[i_row]) {
               htm.push (   '<img src="images/add_icon.png" class="pointer" onclick="add_question_card (' + ii_row + ')" title="' + add_question_card_title + '" />');
            } else if (permission_requested_fs[i_row]) {
               var rerequest_permission_title = 'Permission requested ' + permission_requested_dates[i_row] + '.  Click to open form to resend request';
               htm.push (   '<img src="images/icon_question_checked.png" class="pointer" onclick="request_permission (' + ii_row + ', \'' + permission_requested_dates[i_row] + '\')" title="' + rerequest_permission_title + '" />');
            } else if (qwizard_php_f) {
               var save_first_title = 'To use this ' + question_card + ' you need to save your new ' + quiz_flashcard_deck + '.  Please close this page and click &ldquo;Publish/update&rdquo;';
               htm.push (   '<img src="images/warning_icon.png" + title="' + save_first_title + '" />');
            } else {
               var dataset_question_id = data.dataset_question_ids[i_row];
               var i_pos = dataset_question_id.indexOf ('|');
               var dataset = dataset_question_id.substr (0, i_pos);
               var b64_dataset = btoa (dataset).replace (/=/g, '');
               htm.push (   '<img src="images/icon_question.png" class="pointer ' + b64_dataset + '" onclick="request_permission (' + ii_row + ')" title="' + request_permission_title + '" />');
            }
            htm.push (      '<br />' + (ii_row + 1));
            htm.push (   '</td>');

            var html = htmls[i_row];
            htm.push (   '<td>');
            htm.push (      '<div class="question_card">');
            htm.push (         html);
            htm.push (      '</div>');
            htm.push (   '</td>');

            var browse_image_url = browse_image_urls[i_row];
            var thumbnail_url    = thumbnail_urls[i_row];
            htm.push (   '<td>');
            if (browse_image_url) {
               htm.push ('<a href="#" class="qwiz_thumbnail" data-featherlight="' + browse_image_url + '">');
               htm.push (   '<img class="qwiz_thumbnail" src="' + thumbnail_url + '"></a>');
            }
            htm.push (   '</td>');

            htm.push ('</tr>');

            // Global.
            dataset_question_ids.push (data.dataset_question_ids[i_row]);
            question_card_page_urls.push (data.question_card_page_urls[i_row]);
            ii_row++;
         }
         htm = htm.join ('\n');
         $ ('#questions_cards_tbody').append (htm);

         if (reinit_scrollY) {
            window.scrollTo (0, reinit_scrollY);
            reinit_scrollY = 0;
         }

         // (Re-) initialize thumbnail-->image popups with "hover" trigger.
         $ ('a.qwiz_thumbnail').featherlight (null, {openTrigger: 'mouseenter', closeTrigger: 'mousemove', loading: 'Getting image...'});

         // If more rows available, detect when scroll near bottom of page --
         // load then.
         if (debug) {
            console.log ('[add_to_questions_cards_table] more_records_available_f:', more_records_available_f);
         }
         if (more_records_available_f) {
            skip_records += records_queried;

            // Delay a little.
            setTimeout (near_bottom_add_data, 500);
         }
      }


      // -----------------------------------------------------------------------
      function near_bottom_add_data () {
         if (debug) {
            console.log ('[near_bottom_add_data] skip_records:', skip_records);
         }
         window.addEventListener ('scroll', detect_scroll_near_bottom);
      }


      // -----------------------------------------------------------------------
      function detect_scroll_near_bottom () {
         var doc_height_trigger = parseInt ($ (document).height ()) - 200;;

         // scrollY measures pixels off top of screen.
         var scroll =   parseInt (window.innerHeight)
                      + parseInt (window.scrollY);
         if (debug) {
            //console.log ('[detect_scroll_near_bottom] scroll:', scroll, ', doc_height_trigger:', doc_height_trigger);
         }
         if (scroll > doc_height_trigger) {

            // Don't do again.  Will create new listener if more data
            // available after load.
            window.removeEventListener ('scroll', detect_scroll_near_bottom);

            // Get more data.  Show feedback.
            $ ('#getting_more').html ('Getting more ' + question_card + 's...').show ();
            get_data (records_per_query, search_words_array, search_question_card_type);
         }
      }


      // -----------------------------------------------------------------------
      // Return dataset_question_id to qwizard.js.
      function add_question_card (i_row) {
         var dataset_question_id = dataset_question_ids[i_row];
         if (debug) {
            console.log ('[add_question_card] i_row:', i_row, ', dataset_question_ids.length:', dataset_question_ids.length);
         }

         // Get full html for dataset question/card.  Also, pass through info
         // to be returned to qwizard.js (via callback
         // return_question_card_html).
         var data = {dataset_question_id:  dataset_question_id,
                     page_url:             qwizard_page,
                     skip_records:         skip_records
                    };
         qqc.jjax ('', 0, 0, 'browse_dataset_questions_get', data);
      }


      // -----------------------------------------------------------------------
      function request_permission (i_row, permission_requested_date) {
         var dataset_question_id    = dataset_question_ids[i_row];
         var question_card_page_url = question_card_page_urls[i_row];
         if (debug) {
            console.log ('[request_permission] i_row:', i_row, ', dataset_question_ids.length:', dataset_question_ids.length);
            console.log ('[request_permission] dataset_question_id:', dataset_question_id, ', question_card_page_url:', question_card_page_url);
         }
         var rerequest_msg = '';
         if (permission_requested_date) {
            rerequest_msg = 'You previously requested permission to use ' + question_card + 's from this dataset on ' + permission_requested_date + '<br /><br />';
         }
         $ ('#rerequest_msg').html (rerequest_msg);

         // See if dataset-maker's email is available.  Callback will open
         // dialog.
         var i_pos = dataset_question_id.indexOf ('|');
         var dataset = dataset_question_id.substr (0, i_pos);
         var data = {dataset:                 dataset,
                     question_card_page_url:  question_card_page_url
                    };
         qqc.jjax ('', 0, 0, 'maker_email_from_dataset', data);
      }


      // -----------------------------------------------------------------------
      function open_dialog_request_permission (data) {
         if (debug) {
            console.log ('[open_dialog_request_permission] data:', data);
         }
         // Indicator if email address available.
         var maker_email_f = data.maker_email_f;

         // Globals.
         requested_question_card_page_url = data.question_card_page_url;
         requested_dataset                = data.dataset;

         $ ('a.dialog_request_permission_page_url').html (requested_question_card_page_url);
         $ ('a.dialog_request_permission_page_url').attr ('href', requested_question_card_page_url);
         if (maker_email_f == 1) {

            // Create email message, display in modal dialog window (with option
            // to add user's message).  Also display link to dataset question/
            // card's web page.
            var msg = [];
            msg.push ('Hello:');
            msg.push ('This is a request to use questions from Qwizcards dataset "' + requested_dataset + '"');
            msg.push ('I would like to use them on this site/page: ' + qwizard_page);
            msg.push ('(Permissions can be set on the Qwizcards "Manage datasets" page.)');
            msg.push ('');

            // Global.
            request_permission_email_msg_body = msg.join ('\n');

            $ ('div.request_permission_email_msg_body').html (request_permission_email_msg_body);
            hide_request_permission_feedback ();
            $ ('#preface_message_to_send').show ();

            // Re-initialize buttons, in case new request.
            $ ('button.send_permission_request').show ();
            $ ('button.cancel_permission_request').html ('Cancel');
            $dialog_request_permission.dialog ('open');
         } else {

            // No email.  Dialog to display link to dataset question's web page.
            $dialog_dataset_question_page_link.dialog ('open');
         }
      }


      // -----------------------------------------------------------------------
      function send_permission_request () {
         var errmsg = '';
         var msg_addition        = $ ('#request_permission_email_msg_body_addition').val ();
         var current_maker_email = $ ('#current_maker_email').val ();
         var cc_sender_f         = $ ('#cc_sender')[0].checked ? 1 : 0;
         if (trim (current_maker_email) == '') {
            errmsg = 'Please enter your email address';
         } else {
            if (! validate_email (current_maker_email)) {
               errmsg = 'Email address is not valid';
            }
         }

         if (errmsg) {
            $ ('#dialog_request_permission_feedback').addClass ('red').html (errmsg).show ();
         } else {
            var request_permission_msg =   request_permission_email_msg_body
                                         + msg_addition
                                         + '\n\n'
                                         + 'Thanks.  I look forward to hearing from you'
                                         + '\n';
            var data = {dataset:                requested_dataset,
                        request_for_page_url:   qwizard_page,
                        current_maker_email:    current_maker_email,
                        cc_sender_f:            cc_sender_f,
                        request_permission_msg: request_permission_msg
                       };
            if (debug) {
               console.log ('[send_permission_request] data:', data);
            }
            qqc.jjax ('', 0, 0, 'send_request_permission_email', data);
         }
      }


      // -----------------------------------------------------------------------
      function sent_permission_request (data) {
         if (debug) {
            console.log ('[sent_permission_request]: data:', data);
         }
         if (data.errmsg == 'ok') {

            // Change icons for this dataset.
            var b64_dataset = btoa (requested_dataset).replace (/=/g, '');
            $ ('img.' + b64_dataset).attr ('src', 'images/icon_question_checked.png')
                                    .attr ('title', 'Permission requested')
                                    .attr ('disabled', true);

            // Provide feedback, hide send-email button, re-label cancel button.
            $ ('button.send_permission_request').hide ();
            $ ('button.cancel_permission_request').html ('Close');
            $ ('#preface_message_to_send').hide ();
            var msg = 'Email sent to dataset owner';
            $ ('#dialog_request_permission_feedback').addClass ('green').html (msg).show ();
         } else {
            var msg;
            if (data.errmsg == 'same_email') {
               msg = 'Sorry, cannot send to recipient email address same as sender&rsquo;s (that is, yours)';
            } else {
               msg = 'Sorry, could not send email to dataset owner';
            }
            $ ('#dialog_request_permission_feedback').addClass ('red').html (msg).show ();
         }
      }


      // -----------------------------------------------------------------------
      function hide_request_permission_feedback () {
         $ ('#dialog_request_permission_feedback').hide ();
      }


      // -----------------------------------------------------------------------
      function return_question_card_html (data) {
         if (debug) {
            console.log ('[return_question_card_html] data:', data);
         }
         if (data.error) {
            alert ('Permission problem - question/card not available');
         } else {

            // Callback from browse_dataset_questions_get.php.  Data includes
            // html and dataset_question_id.  Add info on current state.  Return to
            // qwizard.js.
            data.browse_search_words_array        = search_words_array;
            data.browse_search_question_card_type = search_question_card_type;
            data.browse_scrollY                   = window.scrollY;
            if (window.opener.qwizard) {
               window.opener.qwizard.new_dataset_question_card (data);
            }
            window.close ();
         }
      }


      // -----------------------------------------------------------------------
      function findKey (obj, value) {
         for (var key in obj) {
            if (obj.hasOwnProperty (key)) {
               if (obj[key] == value) {
                  return key;
               }
            }
         }
         return '';
      }


      // -----------------------------------------------------------------------
      function validate_email (email) {
         var ok_f = true;
         var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
         if (! re.test (email)) {
            ok_f = false;
         }

         return ok_f;
      }


      // -----------------------------------------------------------------------
      // IE 8 does not have trim () method for strings.
      function trim (s) {
         if (s) {
            if ('a'.trim) {
               s = s.trim ();
            } else {
               s = s.replace (/^\s+|\s+$/g, '');
            }
         }

         return s;
      }


   </script>

   <style type="text/css">

      body {
         margin:              0;
         font-family:         arial, verdana, sans-serif;
         font-size:           10pt;
      }

      h2 {
         margin-top:          5px;
         margin-bottom:       10px;
      }

      p {
         margin-top:          0;
      }

      div#fixed_header {
         position:            fixed;
         top:                 0;
         width:               100%;
         height:              65px;
         padding:             5px;
         background:          white;
         border-bottom:       1px solid black;
      }

      input.search_words {
         width:               16em;
      }

      button.cancel {
         float:               right;
         margin-right:        10px;
      }

      div#questions_cards_table {
         margin-left:         5px;
         margin-top:          75px;
      }

      table.questions_cards {
         border:              0;
         border-spacing:      0;
      }

      table.questions_cards th {
         text-align:          left;
         vertical-align:      bottom;
         padding:             3px;
      }

      table.questions_cards th.center {
         text-align:          center;
      }

      table.questions_cards span.normal.smaller {
         font-size:           80%;
         font-weight:         normal;
      }

      table.questions_cards td {
         text-align:          left;
         vertical-align:      top;
         margin:              0;
         padding:             3px;
         border-top:          1px solid lightgray;
      }
      table.questions_cards td.pad_top {
         padding-top:         8px;
      }

      table.questions_cards td div.question_card {
         padding:             5px;
         max-height:          150px;
         overflow:            auto;
      }

      div.clear {
         clear:               both;
      }

      table.card_front_back {
                           /* Hor. Ver. */
         border:              0;
         border-spacing:      15px 0;
         width:               100%;
      }

      table.card_front_back th,
      table.card_front_back td {
         width:               50%;
         min-width:           300px;
         padding:             2px;
         margin-right:        1em;
         text-align:          center;
         vertical-align:      middle;
         overflow:            auto;
         box-sizing:          border-box;
      }

      table.card_front_back td {
         border:              1px solid black;
         height:              122px;
      }

      table.card_front_back td div.card_front_back,
      table.card_front_back td div.card_front_back {
         display:             inline-block;
         min-height:          50px;
         max-height:          120px;
         padding:             2px;
         overflow:            auto;
         min-width:           300px;
      }

      span.hangman {
         border:              1px solid gray;
         padding:             2px;
         font-family:         courier new;
         color:               blue;
         font-weight:         bold;
      }

      span.textentry {
         border:              1px solid gray;
      }

      ul.radio_bullet {
         margin-top:          0;
         list-style:          url('images/radio.png');
      }

      ul.radio_bullet span.radio_li {
         vertical-align:      30%;
      }

      ul.inline_bullet {
         margin-top:          0;
      }

      ul.inline_bullet li {
         float:               left;
         padding:             0px;
         margin:              0 0 0.5rem 2rem;
         line-height:         1.2rem;
      }

      img.qwiz_thumbnail {
         border:              1px dotted gray;
      }

      br.bigger {
         line-height:         150%;
      }

      div.message_to_send {
         font-size:           10pt;
         border:              1px solid black;
         padding:             3px;
         margin-left:         15px;
         margin-bottom:       5px;
      }

      div.request_permission_email_msg_body {
         white-space:         pre;
      }

      input#current_maker_email {
         width:               17em;
      }

      textarea#request_permission_email_msg_body_addition {
         width:               550px;
         height:               60px;
      }

      div#getting_more {
         position:            fixed;
         bottom:              20px;
         left:                50%;
         transform:           translateX(-50%);
         -webkit-transform:   translateX(-50%);
         width:               200px;
         height:              20px;
         display:             none;
         text-align:          center;
         background:          white;
         border:              1px solid black;
         border-radius:       5px;
         -webkit-border-radius: 5px;
         -moz-border-radius:  5px;
         box-shadow:          3px 3px 2px gray;
         padding:             15px 5px 5px 5px;
      }

      .qwiz_button {
         margin-bottom: 2px;
         border-top: 1px solid #96d1f8;
         background: #65a9d7;
         background: -webkit-gradient(linear, left top, left bottom, from(#3e779d), to(#65a9d7));
         background: -webkit-linear-gradient(top, #3e779d, #65a9d7);
         background: -moz-linear-gradient(top, #3e779d, #65a9d7);
         background: -ms-linear-gradient(top, #3e779d, #65a9d7);
         background: -o-linear-gradient(top, #3e779d, #65a9d7);
         padding: 3px 3px;
         -webkit-border-radius: 8px;
         -moz-border-radius: 8px;
         border-radius: 8px;
         -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
         -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
         box-shadow: rgba(0,0,0,1) 0 1px 0;
         text-shadow: rgba(0,0,0,.4) 0 1px 0;
         color: white;
         font-size: 14px !important;
         font-weight: bold;
         font-family: arial, verdana, sans-serif;
         text-decoration: none;
         vertical-align: middle;
      }

      .indent {
         margin-left:         15px;
      }

      .center {
         text-align:          center !important;
      }

      .green {
         color:               green;
         font-weight:         bold;
      }

      .red {
         color:               red;
         font-weight:         bold;
      }

      .gray {
         color:               gray;
      }

      .highlight {
         background:          #ffffa0;
      }

      .normal {
         font-size:           10pt;
         font-weight:         normal;
      }

      .smaller {
         font-size:           80%;
      }

      .textentry_border {
         border:              1px solid gray;
         padding:             2px;
      }

      img[src$="add_icon.png"] {
         width:           16px;
         height:          16px;
      }

      .shift_icon {
         transform:           translate(0px, 3px);
         -webkit-transform:   translate(0px, 3px);
      }

      .pointer {
         cursor:              pointer;
      }

      .help {
         cursor:              help;
      }


   </style>

</head>
<body>
   <div id="fixed_header">
      <h2>
         Select <?php print $question_card ?> from database
      </h2>
      <?php print ucfirst ($question_card) ?> type
      <select id="question_card_type_select" onchange="question_card_type_search (this)">
      <?php
      if ($q_f == 'Q') {
         $types_display = $question_types_display;
      } else {
         $types_display = $card_types_display;
      }
      print "<option>\n";
      print "</option>\n";
      $search_question_card_type_display = $question_card_type_display[$search_question_card_type];
      foreach ($types_display as $type_display) {
         $selected = selected ($type_display, $search_question_card_type_display);
         print "<option $selected>\n";
         print    "$type_display\n";
         print "</option>\n";
      }
      ?>
      </select>
      &emsp;
      <input type="text" class="search_words" onchange="full_text_search (this)" placeholder="Search words" value="<?php print strtolower ($search_words_string) ?>" />
      &nbsp;
      <button>
         Go
      </button>
      <button class="cancel qwiz_button" onclick="cancel_browse ()">
         Cancel
      </button>
   </div>

   <div id="questions_cards_table">
   </div>

   <div id="dialog_request_permission" title="Request permission to use <?php print $question_card ?> from dataset">
      <div id="rerequest_msg" class="normal">
      </div>
      <p id="preface_message_to_send" style="margin-bottom: 4px;">
         Message to send:
      </p>
      <div class="message_to_send">
         <p>

            <!-- maker_email from maker_classes_from_session_id.php, included
                 above. -->
            From: <input type="text" id="current_maker_email" onfocus="hide_request_permission_feedback ()" value="<?php print $maker_email ?>" placeholder="Enter your email address" />
            &emsp;
            <span class="smaller">
               <input type="checkbox" id="cc_sender" checked />
               Send copy to this address
            </span>
         </p>
         <div class="request_permission_email_msg_body">
         </div>
         <p>
            <textarea id="request_permission_email_msg_body_addition" placeholder="Add your own message here"></textarea>
         </p>
         <p>
            Thanks!  I look forward to hearing from you.
         </p>
      </div>
      <div id="dialog_request_permission_feedback">
      </div>
      <div style="text-align: right; margin-bottom: 10px;">
         <button class="send_permission_request" onclick="send_permission_request ()">
            Send message
         </button>
         &ensp;
         <button class="cancel_permission_request" onclick="$dialog_request_permission.dialog ('close')">
            Cancel
         </button>
      </div>
      <p style="margin-bottom: 0;">
         <b>View dataset page in new window:</b>
      </p>
      <p class="indent">
         <a href="#" class="dialog_request_permission_page_url" target="_blank">
         </a>
      </p>


   </div>

   <div id="dialog_dataset_question_page_link" title="View dataset <?php print $question_card ?> page in new window">
      <p>
         Sorry, we don&rsquo;t have an email address for this <?php print $question_card ?>/dataset&rsquo;s
         owner.&nbsp;
         You may be able to view the dataset and find contact info here:
      </p>
      <p class="indent">
         <a href="#" class="dialog_request_permission_page_url" target="_blank">
         </a>
      </p>

   </div>

   <div id="getting_more">
   </div>

</body>
</html>
