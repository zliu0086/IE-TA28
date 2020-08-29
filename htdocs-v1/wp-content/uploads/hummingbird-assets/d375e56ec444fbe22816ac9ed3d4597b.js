/**handles:qwiz_handle**/
// Do-nothing function for old IE.
if (! window.console) {
   window.console = {log: function(){} };
}

// =============================================================================
// Isolate namespace.
qwiz_ = {};
var qwizf = function () {
// =============================================================================
//
var qname = 'qwiz_';

// Debug settings.
var debug = [];
debug.push (false);    // 0 - general.
debug.push (false);    // 1 - radio/choices html.
debug.push (false);    // 2 - feedback html.
debug.push (false);    // 3 - old/new html dump.
debug.push (false);    // 4 - question tags/topics.
debug.push (false);    // 5 - unused.
debug.push (false);    // 6 - [textentry] / autocomplete.
debug.push (false);    // 7 - Enter -> click.
debug.push (false);    // 8 - Drag and drop.
debug.push (false);    // 9 - [hangman].
debug.push (false);    // 10 - unused.
debug.push (false);    // 11 - use_dataset.
debug.push (false);    // 12 - QWizard.

var $ = jQuery;

// Publicly available.
this.no_intro_b = [];
this.qwizard_b = false;
this.processing_complete_b = false;
this.qrecord_b = false;
this.dataset_b = false;
this.preview = false;
this.any_pay_quiz_f = false;

// Private data, but global to this qwiz instance.
var q = this;
var qqc;

var content;
var default_use_dict;
var default_use_terms;
var hint_timeout_sec;
var default_hangman_max_hints;
var post_id;
var correct;
var incorrect;
var errmsgs = [];

var n_qwizzes = 0;
var qwizzled_b;

var qwizcards_page_f = false;

var qwizdata = [];

var header_html;

var drag_and_drop_initialized_b = false;
var try_again_obj = '';
var $label_clicked = [];
var $prev_label_clicked = [];
var ignore_label_click_b = false;

var next_button_active_b = false;

var textentry_b = false;
var loaded_metaphone_js_b = false;

// Object (singular and plural) of arrays of term-metaphone pairs.
// Constant across quizzes.
var default_textentry_terms_metaphones;

// (qwizdata[i_qwiz].textentry_terms_metaphones are quiz-specific terms given
// with [terms]...[/terms].)

// These vary with quiz, and are set up anew for each [textentry] question.
var current_question_textentry_terms_metaphones = {};

var textentry_answers = {};
var textentry_answer_metaphones = {};

var textentry_matches = {};
var lc_textentry_matches = {};
var textentry_i_qwiz;

var suppress_hangman_hint_b = false;

var Tcheck_answer_message;
var show_hint_timeout = {};

var panel_exit_mobile_open_b = false;
var panel_exit_mobile_just_closed_b;

var non_mobile_scrollLeft;
var non_mobile_scrollTop;

var qw;
var set_qwizard_data_b = false;

// -----------------------------------------------------------------------------
$ (document).ready (function () {

   qqc = qwiz_qcards_common;
   if (debug[0]) {
      console.log ('[qwiz.js > document ready] typeof (document_qwizard_user_page):', typeof (document_qwizard_user_page));
   }
   correct = [T ('Good!'), T ('Correct!'), T ('Excellent!'), T ('Great!')];
   incorrect = [T ('No.'), T ('No, that\'s not correct.'), T ('Sorry, that\'s not correct.')];

   // Are we on qwizcards page (either user page or qwizard.php)?
   var page_url = document.location.href;
   qwizcards_page_f =    page_url.indexOf ('qwizcards.com/u/') != -1
                      || page_url.indexOf ('qwizcards.com/admin/') != -1
                      || page_url.indexOf ('localhost/u/') != -1
                      || page_url.indexOf ('localhost/admin/') != -1;

   // Do only if we're not on WordPress editing page and we're not on qwizard
   // user/editing page (qwiz_init will be called in that case once the user/
   // editing page has had a chance to grab the source shortcodes).
   if (typeof (document_qwizard_user_page) == 'undefined'
               && window.location.href.indexOf ('action=edit') == -1
                          && window.location.href.indexOf ('post-new') == -1) {
      q.qwiz_init ();
   }
});


// -----------------------------------------------------------------------------
this.qwiz_init = function (skip_error_check_f, only_get_qwiz_param_f) {

   // The identifier -- including qualifiers like "#" -- of the page content
   // (that perhaps contains inline quizzes) on WordPress.  Multiple-entries
   // default set in qwiz-online-quizzes-wp-plugin.php: span.entry-content,
   // div.post-entry, div.container.  Apparently themes can change this; these
   // have come up so far.  Body default for stand-alone use.
   content                   = qqc.get_qwiz_param ('content', 'body');
   default_use_dict          = qqc.get_qwiz_param ('use_dict', 'true');
   default_use_terms         = qqc.get_qwiz_param ('use_terms', 'true');
   default_hangman_max_hints = parseInt (qqc.get_qwiz_param ('hangman_hints', 2), 10);
   hint_timeout_sec          = qqc.get_qwiz_param ('hint_timeout_sec', 20);
   post_id                   = qqc.get_qwiz_param ('post_id', 0);
   q.qwizcards_version       = qqc.get_qwiz_param ('qwizcards_version', '');
   document_qwiz_mobile_enabled = qqc.get_qwiz_param ('mobile_enabled', 'Enabled') == 'Enabled';
   Tcheck_answer_message = T ('Need help?  Try the "hint" button');
   if (only_get_qwiz_param_f) {
      return;
   }

   // If we're in an iframe and the frame page is qwizcards wizard-based
   // (presumably Google sites or similar) and flag for alternate behavior not
   // set, set document global flag to use mobile logic to fit to iframe (as
   // would fit to mobile device).
   qqc.set_force_mobile ();

   process_html ();

   // Error messages, if any.
   if (errmsgs.length && ! skip_error_check_f) {
      if (! q.qwizard_b) {
         alert (Tplural ('Error found', 'Errors found', errmsgs.length) + ':\n\n' + errmsgs.join ('\n'));
      } else {
         if (debug[12]) {
            console.log (errmsgs.join ('\n'));
         }
      }
   }
   if (document_qwiz_force_mobile_f) {
      q.go_mobile (0);
   }

   if (n_qwizzes) {

      // If on small screen, show expand-to-full-screen icon and clickable
      // target on all quizzes.
      if (qqc.is_mobile () && ! document_qwiz_force_mobile_f) {
         $ ('.go-mobile-qwiz').show ();
      }
      for (var i_qwiz=0; i_qwiz<n_qwizzes; i_qwiz++) {

         // If quiz subject to recording, set flag to record start times on
         // first interaction of logged-in user with quiz.
         if (qwizdata[i_qwiz].qrecord_id) {
            qwizdata[i_qwiz].record_start_b = true;
         }

         // If no intro for a quiz or single-question quiz, move immediately to
         // first question.  Otherwise, if not display_pay_screen special, show
         // intro.
         if (q.no_intro_b[i_qwiz] || qwizdata[i_qwiz].n_questions == 1) {
            q.next_question (i_qwiz);
         } else {
            if (! qwizdata[i_qwiz].display_pay_screen) {
               $ ('div.intro-qwiz' + i_qwiz).show ();
               $ ('div#next_button-qwiz' + i_qwiz).show ();
            }
         }
      }

      // Not-enrolled-in-class-for-quiz message, with option to register with
      // code.  $usernames_is_are and $dialog_no_credit used in
      // record_response_v3.php.
      if (! q.$dialog_no_credit) {
         $ ('body').append (dialog_no_credit_html ());
         q.$usernames_is_are = $ ('#qwiz_usernames_is_are');
         q.$dialog_no_credit = $ ('#qwiz_dialog_no_credit').dialog ({
            height:        425,
            width:         550,
            modal:         true,
            autoOpen:      false,
            buttons:       {'Close':   function () {
                                          q.$dialog_no_credit.dialog ('close');
                                       }
                           }
         });
      }

      if (q.preview && ! q.qwizard_b) {
         init_preview (0);
      }
   }
}


// -----------------------------------------------------------------------------
function process_html () {

   // Delete paragraphs and headers that contain only [!] ... [/!] comments
   // and whitespace/tags outside.
   $ ('p:contains("[!]"), :header:contains("[!]")').each (function () {

      // See if only whitespace outside [!] ... [/!].
      var comment_htm = $ (this).html ();
      if (comment_htm.search (/\s*(<.+?>)*\s*\[!+\][^]*?\[\/!+\]\s*(<.+?>)*\s*$/m) == 0) {
         $ (this).remove ();
      }
   });

   // Look for [qwiz] and [/qwiz] that are only thing inside parents (e.g.,
   // <p>[qwiz]</p>).  Replace with "unwrapped" content if so.
   $ ('p:contains("qwiz"), :header:contains("qwiz")').each (function () {
      var tag_htm = $ (this).html ();
      if (tag_htm.search (/\s*\[\/{0,1}qwiz[^\]]*\]\s*/m) == 0) {
         $ (this).replaceWith (tag_htm);
      }
   });

   // We're either going to deal with HTML (stand-alone version) or divs (added
   // by WordPress content filter).  The divs allow us to replace content
   // specific to qwiz/qdeck -- avoid clobbering any events bound to remaining
   // html by other plugins.  See if there are such divs.  WP content filter
   // always adds at least one empty div, so don't have to do HTML branch.
   var div_html_selector = '';
   var $qwiz_divs= $ ('div.qwiz_wrapper');
   var $fallback_wrappers = $ ('div.qwiz_wrapper_fallback');
   if ($qwiz_divs.length) {
      div_html_selector = 'div.qwiz_wrapper';

      // Hide fallback wrappers.
      $fallback_wrappers.css ({display: 'none'});
   } else {

      // If there are no fallback wrappers, need to add style so they'll show
      // if they're inserted into the page later.
      if ($fallback_wrappers.length == 0) {
         var style =   '<style type="text/css">\n'
                     +    '.qwiz_wrapper_fallback_visible {\n'
                     +       'visibility: visible;\n'
                     +    '}\n'
                     + '</style>\n';
         $ ('head').append (style);
      }
      div_html_selector = content;
   }

   // Read appropriate divs, look for qwiz shortcodes, loop over shortcode
   // pairs.  n_qwizzes default; reset in process_html2 ().
   n_qwizzes = 0;
   var i_qwiz = 0;
   $ (div_html_selector).each (function () {
      var htm = $ (this).html ();
      if (! htm) {

         //errmsgs.push ('Did not find page content (looking for div "' + content + '")');
      } else {

         // See if there is a qwiz or qwizzes.
         var qwiz_pos = htm.indexOf ('[qwiz');
         if (qwiz_pos != -1) {


            var r = q.process_html2 (htm, i_qwiz);
            htm = r.htm;


            // Restore examples, but without [qwizdemo] ... [/qwizdemo] tags.
            //                               0----+----1    ----+----1-
            if (q.qwizdemos) {
               var n_qwizdemos = q.qwizdemos.length;
               for (var i_qwizdemo=0; i_qwizdemo< n_qwizdemos; i_qwizdemo++) {
                  var qwizdemo_i = q.qwizdemos[i_qwizdemo];
                  var len = qwizdemo_i.length;
                  qwizdemo_i = qwizdemo_i.substring (10, len - 11);
                  htm = htm.replace ('<qwizdemo></qwizdemo>', qwizdemo_i);
               }
            }

            // Replace content html.
            $ (this).html (htm);

            // If i_qwiz wasn't incremented, means was an excerpt.
            if (i_qwiz != r.i_qwiz) {
               i_qwiz = r.i_qwiz;

               // Mouseenter for this quiz records it as the active qwiz.
               $ (this).find ('div.qwiz')
                  .on ('mouseenter',
                       function (e) {

                          // Gets off after usermenu open/close.  Make sure get
                          // container div.
                          if (e.target.tagName.toLowerCase () == 'div'
                                                 && e.target.className == 'qwiz') {
                             document_active_qwiz_qdeck = e.target;
                          } else {
                             var $qwizdiv = $ (e.target).parents ('div.qwiz');
                             if ($qwizdiv.length) {
                                document_active_qwiz_qdeck = $qwizdiv[0];
                             }
                          }
                          if (debug[7]) {
                             console.log ('[qwiz mouseenter] e.target:', e.target);
                             console.log ('[qwiz mouseenter] document_active_qwiz_qdeck:', document_active_qwiz_qdeck);
                          }
                      });

               // For each quiz question with a background image, set the height of
               // the question to accommodate the background. To find out the size
               // of the background image, need separate retrieval.
               var ii_qwiz = i_qwiz - 1;
               var n_questions = qwizdata[ii_qwiz].n_questions;
               for (var i_question=0; i_question<n_questions; i_question++) {
                  if (qwizdata[ii_qwiz].bg_img[i_question]) {
                     var bg_img = qwizdata[ii_qwiz].bg_img[i_question];
                     var img = new Image ();
                     img.src = bg_img.src;
                     img.i_qwiz = ii_qwiz;
                     img.i_question = i_question;
                     img.onload = function () {
                        var w = this.width;
                        var h = this.height;
                        var $qwizq = $ ('#qwiz' + img.i_qwiz + '-q' + img.i_question);
                        if (debug[0]) {
                           console.log ('[process_html] w:', w, ', h:', h, ', $qwizq:', $qwizq);
                        }

                        // Default sizing (auto auto) keeps the original image
                        // size.  We'll work only in pixels.  If height given,
                        // accommodate that; if width given but not height,
                        // calculate scaled height.
                        var min_height;
                        if (bg_img.height) {
                           min_height = bg_img.height;
                        } else if (bg_img.width) {
                           min_height = Math.floor (bg_img.width/w * h);
                        } else {

                           // No scaling.  Accommodate native image height.
                           min_height = h;
                        }
                        min_height = '' + min_height + 'px';
                        if (bg_img.top) {
                           min_height = 'calc(' + bg_img.top + 'px + ' + min_height + ')';
                        }
                        $qwizq.css ({'min-height': min_height});
                     }
                  }
               }
            }
         }

         // If wrapper divs, unwrap.
         if ($qwiz_divs.length) {
            $ (this).contents ().unwrap ();
         }
      }
   });
   n_qwizzes = i_qwiz;

   // Tooltips on hangman hints -- show up faster.
   /*
   $ ('button.hangman_hint').tooltip ({tooltipClass:  'qwiz_hint_tooltip',
                                       show:          {delay: 500}
                                      });
                                      */

   // If hangman-labeled-diagram fields, increase their width (so can
   // accommodate extra width of bold characters as they're typed in).
   $ ('div.hangman_label').each (function () {
                                    var $this = $ (this);
                                    var width = $this.outerWidth ();
                                    $this.outerWidth (1.2 * width);
                                 });

   // Waited to do check_registered so can update sharing href of qwiz icon.
   // Also, record/update number of questions.
   for (var i_qwiz=0; i_qwiz<n_qwizzes; i_qwiz++) {
      if (qwizdata[i_qwiz].qrecord_id) {
         var n_questions = qwizdata[i_qwiz].n_questions;
         var data = {qwiz_qdeck: 'qwiz', n_questions_cards: n_questions};
         qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'check_registered', data);
      }
   };

   // Initial setting of lock/unlock status for pay quizzes.  (Will be
   // re-queried if not-yet-logged-in user logs in.)  Callback also initializes
   // pay-unlock menus.
   // NOW DONE BY check_registered.php CALLBACK.
   //if (q.any_pay_quiz_f) {
   //   q.pay_lock_settings ();
   //}


   // Set up Enter-key intercept -- trigger appropriate button press
   // (Next question, Check answer).
   if (n_qwizzes) {
      qqc.init_enter_intercept ();
   }

   // If any quizzes subject to recording, set user menus -- if this comes after
   // check_session_id () callback, it will properly set the menus (while the
   // callback may not have worked if the html hadn't been set at that time).
   if (q.qrecord_b) {
      qqc.set_user_menus_and_icons ();
   }

   // If any [textentry] free-form input, set up autocomplete.
   if (textentry_b) {

      // If this is standalone version, and qcard_ present, wait until it's
      // done (since re-writes body html).
      if (content == 'body' && typeof (qcard_) != 'undefined') {
         var n_tries = 0;

         // ................................................
         var run_init_textentry_autocomplete = function () {
            var ok_b = false;
            if (debug[6]) {
               console.log ('[run_init_textentry_autocomplete]', n_tries);
            }
            if (qcard_.processing_complete_b || n_tries > 30) {
               if (debug[6]) {
                  console.log ('[run_init_textentry_autocomplete] OK');
               }
               q.init_textentry_autocomplete ($ ('body'));
               ok_b = true;
            }

            // Do every 10th of a second until success.
            if (! ok_b) {
               setTimeout (run_init_textentry_autocomplete, 100);
               n_tries++;
            }
         }
         // ................................................

         run_init_textentry_autocomplete ();
      } else {
         q.init_textentry_autocomplete ($ ('body'));
      }

   }

   // Set flag just in case.
   q.processing_complete_b = true;
}


// -----------------------------------------------------------------------------
// Called from process_html () and from qwiz_admin_footer_post_func ().
this.process_html2 = function (htm, i_qwiz, create_qwizard_json_f) {

   // Remove and save text inside [qwizdemo] ... [/qwizdemo] pairs.
   // Replace with <qwizdemo></qwizdemo> pairs as placeholder.
   var qwizdemo_re = new RegExp ('\\[qwizdemo\\][\\s\\S]*?\\[\\/qwizdemo\\]', 'gm');
   q.qwizdemos = htm.match (qwizdemo_re);
   if (q.qwizdemos) {
      htm = htm.replace (qwizdemo_re, '<qwizdemo></qwizdemo>');
      if (debug[0]) {
         console.log ('[process_html2] q.qwizdemos.length: ', q.qwizdemos.length);
      }
   }

   // Delete comments -- don't want to process [qwiz][/qwiz] pairs or any other
   // qwiz-related tags that are in comments.
   htm = htm.replace (/<!--[^]*?-->/gm, '');

   // Take out any remaining [!]...[\!] comments (those that were not
   // inside paragraph or header elements).
   htm = htm.replace (/\[!+\][^]*?\[\/!+\]/gm, '');

   // Check that there are pairs.
   var local_n_qwizzes = 0;
   var do_not_process_htm = check_qwiz_tag_pairs (htm);
   if (do_not_process_htm) {
      htm = do_not_process_htm;
   } else {

      // Get text, including beginning and ending tags.  "." does not
      // match line-ends (!), so use the whitespace/not-whitespace
      // construct.  Non-greedy search, global, multiline.
      qwizzled_b = false;
      var qwiz_matches = htm.match (/\[qwiz[^]*?\[\/qwiz\]/gm);
      if (qwiz_matches) {
         local_n_qwizzes = qwiz_matches.length;
         if (debug[0]) {
            console.log ('[process_html2] local_n_qwizzes: ', local_n_qwizzes);
            console.log ('                qwiz_matches[0]: ', qwiz_matches[0]);
         }

         // Loop over qwiz-tag pairs.
         q.quizzes_questions = [];
         for (var ii_qwiz=0; ii_qwiz<local_n_qwizzes; ii_qwiz++) {
            var new_qwiz_html = q.process_qwiz_pair (qwiz_matches[ii_qwiz], i_qwiz, create_qwizard_json_f, create_qwizard_json_f);
            if (create_qwizard_json_f) {

               // Returning data to qwiz_admin_footer_post_func ().  Save
               // qwizard structure by quiz in case multiple quizzes on page.
               // JSON stringify > parse: deep copy.
               if (qwizard.questions_cards && qwizard.questions_cards.length) {
                  qwizard.questions_cards[0].dataset_b = qwizard.questions_cards_dataset_b;
                  q.quizzes_questions[i_qwiz] = JSON.parse (JSON.stringify (qwizard.questions_cards));
               }
               if (debug[0]) {
                  console.log ('[process_html2] i_qwiz:', i_qwiz, ', qwizard.questions_cards:', qwizard.questions_cards);
                  if (qwizard.questions_cards) {
                     console.log ('[process_html2] JSON.stringify (qwizard.questions_cards):', JSON.stringify (qwizard.questions_cards));
                     console.log ('[process_html2] qwizard.questions_cards.length:', qwizard.questions_cards.length);

                     //DKTMP.  Any empty questions?
                     var ll = qwizard.questions_cards.length;
                     for (var ii=0; ii<ll; ii++) {
                        if (qwizard.questions_cards[ii] == '') {
                           console.log ('[process_html2] NULL JSON ii:', ii);
                        }
                     }
                  }
               }
            }

            // Let's take out <p...> and <h...> from before [qwiz].  Use a
            // function to return replacement value - avoids $100 interpreted
            // as sub-expression replacement!
            htm = htm.replace (/(<[ph][^>]*>\s*)*?\[qwiz[^]*?\[\/qwiz\]/m, function () {return new_qwiz_html});
            i_qwiz++;
         }
         if (debug[0] && q.quizzes_questions.length) {
            var n = q.quizzes_questions.length;
            for (var i=0; i<n; i++) {
               console.log ('[process_html2] q.quizzes_questions[' + i + ']:', q.quizzes_questions[i]);
            }
         }
         if (debug[3]) {
            console.log ('[process_html2] htm:', htm);
         }
      }
   }

   return {'htm': htm, 'i_qwiz': i_qwiz};
}


// -----------------------------------------------------------------------------
function dialog_no_credit_html () {
   htm = [];
   htm.push ('<div id="qwiz_dialog_no_credit" title="Register for class?">');
   htm.push (   '<p>');
   htm.push (      '<span id="qwiz_usernames_is_are"></span> ');
   htm.push (      'not currently registered in a class to which this quiz has been assigned.');
   htm.push (      'We&rsquo;ll record your progress, but if you&rsquo;re here for a class assignment, you won&rsquo;t get credit.');
   htm.push (   '</p>');
   htm.push (   '<p>');
   htm.push (      'If your teacher gave you a registration code, please enter it here');
   htm.push (      '<br />');
   htm.push (      '<br />');
   htm.push (      '<label>');
   htm.push (         'Registration code &nbsp;');
   htm.push (         '<input type="text" style="display: inline-block; padding: 2px; width: 15em;" onchange="' + qname + '.process_reg_code (this.value)" onfocus="' + qname + '.hide_reg_code_error (this);" />');
   htm.push (      '</label>');

   // Just to trigger onchange.
   htm.push (      '<button>');
   htm.push (         'Go');
   htm.push (      '</button>');

   htm.push (      '<div id="reg_code_errmsg" class="qwiz_reg_code_errmsg"></div>');
   htm.push (   '</p>');
   htm.push ('</div>');

   return htm.join ('\n');
}


// -----------------------------------------------------------------------------
this.process_reg_code = function (reg_code) {
   if (debug[0]) {
      console.log ('[process_reg_code] reg_code:', reg_code);
   }
   if (reg_code != '') {
      var data = {reg_code: reg_code};

      // Just sets div.qwiz_reg_code_errmsg in div#qwiz_dialog_no_credit.
      qqc.jjax (qname, 0, 0, 'reg_code_add_to_class', data);
   }
}


// -----------------------------------------------------------------------------
this.hide_reg_code_error = function () {
   $ ('div.qwiz_reg_code_errmsg').html ('').hide ();
}


// -----------------------------------------------------------------------------
// Show all questions at once.
function init_preview (i_qwiz) {

   // Each question needs border, sizing.  So apply whole-quiz class.  Also add
   // class to do some spacing.  When mouseover a question, set i_question.
   // Show all.
   $ ('#qwiz' + i_qwiz + ' div.qwizq').addClass ('qwiz qwizq_preview')
                                      .on ('mouseenter', q.set_i_qwiz_i_question)
                                      .show ();

   // Hide overall quiz border.
   $ ('#qwiz' + i_qwiz).css ({border: 'none'});

   // Show intro if set, and give it qwiz class.
   if (! q.no_intro_b[i_qwiz]) {
      $ ('div.intro-qwiz' + i_qwiz).addClass ('qwiz qwizq_preview').show ();
   }

   // Set summary, summary/exit, and add classes.
   q.display_summary_and_exit (i_qwiz);
   $ ('#summary-qwiz' + i_qwiz).addClass ('qwiz qwizq_preview').show ();

   // Don't show "Next question" or "Start quiz" ever.
   $ ('#next_button-qwiz' + i_qwiz).remove ();

   // Initialize each question.
   var n_questions = qwizdata[i_qwiz].n_questions;
   for (var i_question=0; i_question<n_questions; i_question++) {
      q.display_question (i_qwiz, i_question, false);
   }
}


// -----------------------------------------------------------------------------
this.set_i_qwiz_i_question = function () {
   if (debug[0]) {
      console.log ('[set_i_qwiz_i_question] this:', this);
   }

   // Looks like qwiz0-q0.
   var id = this.id;
   var i_qwiz = id.match (/qwiz([^-]+)/)[1];
   var i_question = id.match (/-q(.+)/)[1];
   qwizdata[i_qwiz].i_question = i_question;
}


// -----------------------------------------------------------------------------
this.init_qwizzled = function ($content, i_qwiz, i_question) {
   if (debug[0]) {
      console.log ('[init_qwizzled] i_qwiz:', i_qwiz);
   }

   // Targets no longer draggable (from qwizzled create/edit step).
   // Also reset borders.  Give each target a unique ID (so can see if correctly
   // labeled on first try) -- but give text-target siblings the same ID.
   sibs = {};
   var t_id;
   var ii = 0;
   $content.find ('div.qwizzled_canvas .qwizzled_target').each (function () {
      var $this = $ (this);
      $this.removeClass ('ui-draggable ui-draggable-handle');
      $this.css ({'border-style': 'dotted', 'border-color': 'gray'});
      var classes = $this.attr ('class');
      var m = classes.match (/qtarget_sib-([0-9]+)/);
      if (m) {
         var sib = m[1];
         if (sibs[sib]) {

            // Have already seen sibling of this target.  Use that ID.
            t_id = sibs[sib];
         } else {

            // This is new.  Create new ID and save.
            t_id = 't' + ii;
            sibs[sib] = t_id;
            ii++;
         }
      } else {
         t_id = 't' + ii;
         ii++;
      }
      $this.attr ('id', t_id);

      // Also, make targets clickable (along with labels; provides alternative
      // to drag and drop).  Click is about same as label-drop.  Act on click
      // only if target still droppable.
      $this.on ('click', function (event) {
                               if (debug[8]) {
                                  console.log ('[target clicked] $ (event.target):', $ (event.target));
                               }
                               var $target = $ (event.target);

                               // Click counts as a drop only if target still
                               // droppable.
                               if ($target.hasClass ('qwizzled_target')) {
                                  if (! $target.droppable ('option', 'disabled')) {
                                     q.label_dropped ($target);
                                  }
                               }
                            });
   });

   // Labels clickable also (alternative to drag).
   $content.find ('td.qwizzled_labels div.qwizzled_label').each (function () {
      $ (this).on ('click', function (event) {
                               if (debug[8]) {
                                  console.log ('[label clicked] $ (event.target).html ():', $ (event.target).html ());
                               }
                               if (ignore_label_click_b) {
                                  ignore_label_click_b = false;
                               } else {
                                  var $label;
                                  if (event.target.tagName.toLowerCase () == 'div') {
                                     $label = $ (event.target);
                                  } else {
                                     $label = $ (event.target).parents ('div.qwizzled_label');
                                  }

                                  // Record label clicked, unhighlight others
                                  // (all), highlight this one,
                                  var ii_qwiz = $label[0].id.match (/qwiz([^-]+)/)[1];
                                  $label_clicked[ii_qwiz] = $label;
                                  var $td_qwizzled_labels = $label.parents ('td.qwizzled_labels');
                                  $td_qwizzled_labels.find ('.qwizzled_highlight_label').removeClass ('label_click_highlight');
                                  $td_qwizzled_labels.find ('.qwizzled_label_head').hide ();
                                  $td_qwizzled_labels.find ('.qwizzled_label_head_label_clicked').show ();
                                  $label.find ('.qwizzled_highlight_label').addClass ('label_click_highlight');
                                  q.label_dragstart ($label, true);
                               }
                            });
   });

   // Remove resizing handle divs.
   $content.find ('div.qwizzled_canvas div.ui-resizable-handle').remove ();

   // Image-linked targets need border-width.
   $content.find ('div.qwizzled_image div.qwizzled_target').css ('border-width', '2px');

   // Eliminate label borders.
   $content.find ('.qwizzled_highlight_label').css ('border', 'none');

   // Everything within label (such as <strong> or <sup>) needs to override
   // WordPress word-wrap: break-word setting.
   $content.find ('.qwizzled_highlight_label *').css ('word-wrap', 'normal');

   // (Setting up drag-and-drop here doesn't stick -- perhaps WordPress cancels
   // events.  Done by init_drag_and_drop () when first mouseover a qwizzled
   // question div.)

   // Don't let WordPress resize images on small screens.  Get size from image
   // wrapper, assign to image (and eliminate sizes= and srcset= attributes).
   // Do for each wrapper.
   $content.find ('div.qwizzled_image').each (function () {
      var wrapper_width  = $ (this).width ();
      var wrapper_height = $ (this).height ();

      // Assign width and height to image, and remove unwanted attributes.
      $ (this).find ('img').attr ('width', wrapper_width).attr ('height', wrapper_height)
                           .removeAttr ('sizes').removeAttr ('srcset');
   });

   q.init_qwizzled2 ($content, i_qwiz, i_question);
}


// -----------------------------------------------------------------------------
this.init_qwizzled2 = function ($content, i_qwiz, i_question) {

   // Save deep copy of qwizzled question -- in case restart quiz.
   // Also see if quiz has non-default width.
   var initial_width = $ ('#xqwiz' + i_qwiz).outerWidth ();
   if (debug[0]) {
      console.log ('[init_qwizzled2] i_qwiz:', i_qwiz, ', i_question:', i_question, ', initial_width:', initial_width);
   }
   if (initial_width) {
      qwizdata[i_qwiz].initial_width = initial_width;
   }
   if (! qwizdata[i_qwiz].$qwizzled) {
      qwizdata[i_qwiz].$qwizzled = {};
   }
   var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_question;
   qwizdata[i_qwiz].$qwizzled[qwizq_id] = $content.clone (true);
}


// -----------------------------------------------------------------------------
this.init_textentry_autocomplete = function ($scope) {
   if (debug[6]) {
      console.log ('[init_textentry_autocomplete]');
   }

   $scope.find ('input.qwiz_textentry').autocomplete ({
      source:        find_matching_terms,
      close:         menu_closed,
      open:          menu_shown,
      select:        q.item_selected
   });

   $scope.find ('input.qwiz_textentry').keyup (menu_closed);

   // Single-char textentry.
   $scope.find ('input.qwiz_single_char_entry').keyup (single_char_textentry_keyup);
}


// -----------------------------------------------------------------------------
this.label_dragstart = function ($label, label_clicked_b) {

   var i_qwiz = $label[0].id.match (/qwiz([^-]+)/)[1];
   if (label_clicked_b) {
      if (debug[8]) {
         console.log ('[label_dragstart] $label_clicked[i_qwiz].html():', $label_clicked[i_qwiz].html());
      }
   } else {

      // Drag -- remove highlight, reset message, turn off click.
      if ($label_clicked[i_qwiz]) {
         $label_clicked[i_qwiz].find ('.qwizzled_highlight_label').removeClass ('label_click_highlight');
         var $td_qwizzled_labels = $label.parents ('td.qwizzled_labels');
         $td_qwizzled_labels.find ('.qwizzled_label_head').hide ();
         $td_qwizzled_labels.find ('.qwizzled_label_head_standard').show ();
         $label_clicked[i_qwiz] = '';
      }
      $prev_label_clicked[i_qwiz] = 0;
   }

   // Reset things only if flag is set indicating a label was incorrectly
   // placed.
   if (try_again_obj && $prev_label_clicked[i_qwiz] !== $label_clicked[i_qwiz]) {
      var local_try_again_obj = try_again_obj;
      try_again_obj = '';

      if (debug[8]) {
         console.log ('[label_dragstart] $label.html():', $label.html());
         console.log ('[label_dragstart] local_try_again_obj:', local_try_again_obj);
         console.log ('[label_dragstart] local_try_again_obj.$label.attr (\'id\'):', local_try_again_obj.$label.attr ('id'));
      }

      // Reset background of incorrectly-placed label.
      local_try_again_obj.$label.find ('.qwizzled_highlight_label').css ({background: ''});
      local_try_again_obj.$label.find ('.qwizzled_highlight_label img').css ({outline: ''});

      // If dragging a label other than the one that was incorrectly placed,
      // move the incorrectly-placed label back to list.
      if (local_try_again_obj.$label.attr ('id') != $label.attr ('id')) {
         local_try_again_obj.$label.animate ({left: '0px', top: '0px'}, {duration: 750})
         local_try_again_obj.$label.find ('.qwizzled_highlight_label').removeClass ('label_click_highlight');
      }

      // Reset feedback.
      local_try_again_obj.$feedback.hide ();

      // Make target droppable again.
      local_try_again_obj.$target.droppable ('enable');
   }
   $prev_label_clicked[i_qwiz] = '';
}


// -----------------------------------------------------------------------------
this.label_dropped = function ($target, $label) {

   if (debug[8]) {
      console.log ('[label_dropped]: $target:', $target, ', $label:', $label);
   }
   if ($label) {

      // Drop triggers click -- flag to ignore.
      ignore_label_click_b = true;

      // If qwizard, and tooltip exists, re-enable.
      /* DKTMP DEDRAG
      if (q.qwizard_b) {
         $qwizzled_highlight_label = $label.children ();
         if ($qwizzled_highlight_label.tooltip ('instance')) {
            if (debug[8]) {
               console.log ('[label_dropped] $qwizzled_highlight_label:', $qwizzled_highlight_label);
            }
            $qwizzled_highlight_label.tooltip ('enable');
         }
      }
      */

   } else {

      // No label -- must have been click rather than drop.  See if a label
      // was clicked.
      var $qwizq = $target.parents ('div.qwizq');
      var i_qwiz = $qwizq[0].id.match (/qwiz([^-]+)/)[1];
      if ($label_clicked[i_qwiz]) {

         // Yes - use that label.  Reset message.
         if (debug[8]) {
            console.log ('[label_dropped]: $label_clicked[i_qwiz]:', $label_clicked[i_qwiz]);
         }
         $label = $label_clicked[i_qwiz];
         var $td_qwizzled_labels = $label.parents ('td.qwizzled_labels');
         $td_qwizzled_labels.find ('.qwizzled_label_head').hide ();
         var standard_mobile = document_qwiz_mobile ? 'mobile' : 'standard';
         $td_qwizzled_labels.find ('.qwizzled_label_head_' + standard_mobile).show ();
      } else {

         // Target click, but no label click.  Ignore.
         return false;
      }
   }

   // Remove highlighting if there are NOT multiple targets for this label.
   var classes = $label.attr ('class');
   m = classes.match (/qwizzled_n_targets([0-9]*)/);
   if (! m) {
      $label.find ('.qwizzled_highlight_label').removeClass ('label_click_highlight');
   }

   // Is this the right target?  Get the association id from the label class.
   // If no matching class, use data () (backwards compatibility).
   var classes = $label.attr ('class');
   var m = classes.match (/qtarget_assoc([0-9]+)/);
   var assoc_id;
   if (m) {
      assoc_id = m[1];
   } else {
      assoc_id = $label.data ('label_target_id');
   }
   if (debug[8]) {
      console.log ('[label_dropped] $target:', $target, ', assoc_id:', assoc_id);
   }

   // Get label id (so know which feedback to show).  Looks like
   // label-qwiz0-q0-a0.  Feedback id looks like qwiz0-q0-a0x.
   var label_id = $label.attr ('id');
   var feedback_selector = '#' + label_id.substr (6);
   var fields = feedback_selector.split ('-');
   var question_selector = fields[0] + '-' + fields[1];
   var i_qwiz = fields[0].substr (5);
   var i_question = fields[1].substr (1);
   if (debug[8]) {
      console.log ('[label_dropped] question_selector:', question_selector);
   }

   // If recording and this is first interaction (no-intro, single-question
   // quiz) record as start time.
   if (qwizdata[i_qwiz].record_start_b && document_qwiz_user_logged_in_b) {
      qwizdata[i_qwiz].record_start_b = false;
      var data = {qrecord_id_ok: qwizdata[i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
      qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
   }

   // If no separate intro page, and this is first question, hide qwiz icon.
   if (i_question == 0 && (q.no_intro_b[i_qwiz]
                                     || qwizdata[i_qwiz].n_questions == 1)) {
      $ ('div#icon_qwiz' + i_qwiz).hide ();
   }

   // Increment number of tries.
   qwizdata[i_qwiz].n_label_attempts++;
   // Hide previous feedback, if any.
   $ ('[id^=qwiz' + i_qwiz + '-q' + i_question + '-a]').hide ();

   var qwizq_id = '#qwiz' + i_qwiz + '-q' + i_question;
   var correct_b = false;

   // If recording, and haven't gotten text yet, collect image source and
   // label texts.
   if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {
      if (qqc.isInteger (qwizdata[i_qwiz].dataset_id[i_question])) {
         if (! qwizdata[i_qwiz].q_and_a_text[i_question]) {
            var img_src = $ (qwizq_id + ' div.qwizzled_image img').attr ('src');
            var q_and_a_text;
            if (img_src) {
               q_and_a_text = img_src;
            } else {
               img_src = '';
            }
            $ (qwizq_id + ' div.qwizzled_label').each (function () {
                                                          var label_text = $ (this).html ();
                                                          if (label_text) {
                                                             q_and_a_text += '\t' + label_text;
                                                          }
                                                       });
            q_and_a_text = qqc.remove_tags_eols (q_and_a_text);
            qwizdata[i_qwiz].q_and_a_text[i_question]  = qqc.q_and_a_hash (q_and_a_text);
            qwizdata[i_qwiz].q_and_a_crc32[i_question] = qwiz_crc32 ($ (qwizq_id).html ());
            if (debug[0]) {
               console.log ('[label_dropped] qwizdata[i_qwiz].q_and_a_crc32[i_question]:', qwizdata[i_qwiz].q_and_a_crc32[i_question]);
            }
         }
      } else {

         // Dataset questions: use dataset ID.
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qwizdata[i_qwiz].dataset_id[i_question];
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = 'dataset';
      }
   }

   // Label correctly placed?  That is, does the target have this id as a class?
   // (Note: not using id= because WordPress eats ids).
   var finished_diagram_b = false;
   if ($target.hasClass ('qwizzled_target-' + assoc_id)) {
      if (debug[8]) {
         console.log ('[label_dropped] feedback_selector:', feedback_selector + 'c');
         console.log ('[label_dropped] qwizdata[i_qwiz].n_questions:', qwizdata[i_qwiz].n_questions);
      }

      // Yes.  Show positive feedback for this label.
      $ (feedback_selector + 'c').show ();

      place_label ($target, $label);

      // Increment number of labels correctly placed.  See if done with
      // diagram.
      qwizdata[i_qwiz].n_labels_correct++;
      if (qwizdata[i_qwiz].n_labels_correct == qwizdata[i_qwiz].n_label_targets) {

         // Done with labeled diagram.  Show summary.
         finished_diagram_b = true;
         var n_tries = qwizdata[i_qwiz].n_label_attempts;
         var n_label_targets = qwizdata[i_qwiz].n_label_targets;
         correct_b = n_tries == n_label_targets;
         var qwizzled_summary;
         if (correct_b) {
            qwizzled_summary = 'You placed all of the items correctly on the first try!';
         } else {
            qwizzled_summary = Tplural ('It took you one try', 'It took you %s tries', n_tries) + ' ' + Tplural ('to place this label correctly', 'to place these labels correctly', n_label_targets) + '.';
            qwizzled_summary = qwizzled_summary.replace ('%s', qqc.number_to_word (n_tries));

            // If single-question quiz, and "learn" mode, allow re-do of
            // labeled diagram.
            if (qwizdata[i_qwiz].n_questions == 1
                                       && qwizdata[i_qwiz].repeat_incorrect_b) {
               qwizzled_summary += '<br />Re-do those you did not label correctly ';
               if (qwizdata[i_qwiz].qrecord_id
                                            && document_qwiz_user_logged_in_b) {
                  qwizzled_summary += 'to get this question marked &ldquo;correct&rdquo; '
               }
               qwizzled_summary +=  '<button class="qwiz_button" onclick="qwiz_.next_question (' + i_qwiz + ', true)">'
                                  +    'Re-do'
                                  + '</button>';
               if (! q.qwizard_b) {
                  qwizdata[i_qwiz].answered_correctly[i_question] = false;
               }
            }
         }
         $ (qwizq_id + '-ff').html (qwizzled_summary).show ();
         if (qwizdata[i_qwiz].n_questions > 1 || qwizdata[i_qwiz].use_dataset) {

            // Consider correct only if all labels properly placed on first
            // try.  Don't do if qwizard.
            if (! q.qwizard_b) {
               qwizdata[i_qwiz].answered_correctly[i_question] = correct_b;
               if (correct_b) {
                  qwizdata[i_qwiz].n_correct++;
               } else {

                  // Record number of incorrect responses.
                  qwizdata[i_qwiz].n_incorrect++;
               }
               update_topic_statistics (i_qwiz, i_question, correct_b);
            }

            update_progress_show_next (i_qwiz);
         }

         // Show next-button (or do-question-again in single-question quiz).
         update_progress_show_next (i_qwiz);
      } else {

         // Was this the first try for this target?  If so, record that was
         // correctly-placed on first try.  (Last-filled target doesn't count,
         // since it's the only choice left!)
         var target_id = $target.attr ('id');
         if (typeof (qwizdata[i_qwiz].correct_on_try1[i_question]) == 'undefined') {
            qwizdata[i_qwiz].correct_on_try1[i_question] = {};
         }
         if (! qwizdata[i_qwiz].correct_on_try1[i_question][target_id]) {
            qwizdata[i_qwiz].correct_on_try1[i_question][target_id] = 1;
         }

         // Update progress bar.
         display_qwizzled_progress (i_qwiz);
      }
   } else {
      if (debug[8]) {
         console.log ('[label_dropped] feedback_selector:', feedback_selector + 'x');
         console.log ('[label_dropped] qwizdata[i_qwiz].n_questions:', qwizdata[i_qwiz].n_questions);
      }

      // Incorrectly placed (or incorrect target clicked -- if so, place label
      // on clicked target).
      if ($label_clicked[i_qwiz]) {

         // Offset gives coords relative to document.
         var target_offset = $target.offset ();
         var target_x = target_offset.left;
         var target_y = target_offset.top;

         // Get original position of label.
         var label_x = $label_clicked[i_qwiz].data ('label_x');
         var label_y = $label_clicked[i_qwiz].data ('label_y');
         if (debug[8]) {
            console.log ('[label_dropped] target_x:', target_x, ', target_y:', target_y);
            console.log ('[label_dropped] label_x:', label_x, ', label_y:', label_y);
         }

         $label.css ({left: (target_x - label_x) + 'px',
                      top:  (target_y - label_y) + 'px'});

         // Reset for next click.
         $label_clicked[i_qwiz] = '';
      }

      // Set label background red.
      $label.find ('.qwizzled_highlight_label').css ({background: '#FF8080'});
      $label.find ('.qwizzled_highlight_label img').css ({outline: '2px solid #FF8080'});

      // Show feedback.
      var $feedback = $ (feedback_selector + 'x');
      $feedback.show ();

      // Set indicator to reset things if drag or click another label; record
      // which label dropped, etc.
      try_again_obj = { $label: $label, $feedback:  $feedback, $target: $target};

      // Make target no longer droppable -- starting drag while over the
      // target seems to count as a "drop".  Will re-enable droppability in
      // label_dragstart ().
      $target.droppable ('disable');

      // Record that label was not correctly placed on first try.
      var target_id = $target.attr ('id');
      if (typeof (qwizdata[i_qwiz].correct_on_try1[i_question]) == 'undefined') {
         qwizdata[i_qwiz].correct_on_try1[i_question] = {};
      }
      qwizdata[i_qwiz].correct_on_try1[i_question][target_id] = -1;
   }

   // If recording, record all label placements -- correct and incorrect.
   // Want to record: label, dropped-in-target-for-label.
   if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {
      var label = $label.find ('span.qwizzled_highlight_label').html ();
      label = qqc.remove_tags_eols (label);

      var classes = $target.attr ('class');
      var target_assoc_id = classes.match (/qwizzled_target-([0-9]*)/)[1];
      var target_label = $ (qwizq_id).find ('div.qtarget_assoc' + target_assoc_id).find ('span.qwizzled_highlight_label').html ();

      // Or older style...
      if (! target_label) {
         target_label = $ (qwizq_id).find ('div.qwizzled_label[data-label_target_id="' + target_assoc_id + '"]').find ('span.qwizzled_highlight_label').html ();
      }

      target_label = qqc.remove_tags_eols (target_label);
      var data = {q_and_a_text:  btoa (encodeURIComponent (qwizdata[i_qwiz].q_and_a_text[i_question])),
                  q_and_a_crc32: qwizdata[i_qwiz].q_and_a_crc32[i_question],
                  i_question:    qwizdata[i_qwiz].dataset_id[i_question],
                  unit:          qwizdata[i_qwiz].unit[i_question],
                  type:          'labeled_diagram',
                  response:      label + '\t' + target_label,
                  correct_b:     '',
                  confirm:       'js'};
      qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);

      // Also, if finished diagram, record that as separate entry.  Wait a bit
      // to make it less confusing in debugging record_response_v3.php.
      if (finished_diagram_b) {
         var data = {q_and_a_text:  btoa (encodeURIComponent (qwizdata[i_qwiz].q_and_a_text[i_question])),
                     q_and_a_crc32: qwizdata[i_qwiz].q_and_a_crc32[i_question],
                     i_question:    qwizdata[i_qwiz].dataset_id[i_question],
                     unit:          qwizdata[i_qwiz].unit[i_question],
                     type:          'labeled_diagram',
                     response:      'done',
                     correct_b:     correct_b ? 1 : '',
                     confirm:       'js'};
         var delay_jjax = function () {
            qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
         }
         setTimeout (delay_jjax, 500);
      }
   }
}


// -----------------------------------------------------------------------------
function place_labels (i_qwiz, i_question, qwizq_id) {
   if (debug[0]) {
      console.log ('[place_labels] i_qwiz:', i_qwiz, ', i_question:', i_question, ', qwizq_id:, ', qwizq_id);
   }

   // On this time around, put in place those labels that were correctly
   // placed on a target on the first try in the previous go-around.
   for (var target_id in qwizdata[i_qwiz].correct_on_try1[i_question]) {
      if (qwizdata[i_qwiz].correct_on_try1[i_question][target_id] == 1) {
         var $target = $ ('div#' + qwizq_id + ' div#' + target_id);
         if ($target.length == 0) {
            $target = $ ('div#' + qwizq_id + ' span#' + target_id).first ();
         }

         // Get associated label.
         var classes = $target.attr ('class');
         var m = classes.match (/qwizzled_target-([0-9]+)/);
         var assoc_id;
         if (m) {
            assoc_id = m[1];
         }
         if (debug[8]) {
            console.log ('[place_labels] $target:', $target, ', assoc_id:', assoc_id);
         }
         var $label = $ ('td.qwizzled_labels div.qtarget_assoc' + assoc_id);

         // Or older style...
         if (! $label.length) {
            $label = $ ('div#' + qwizq_id).find ('td.qwizzled_labels div.qwizzled_label[data-label_target_id="' + assoc_id + '"]');
         }

         place_label ($target, $label);

         // And record "placed".
         qwizdata[i_qwiz].n_labels_correct++;
         qwizdata[i_qwiz].n_label_attempts++;
      } else {

         // New chance at correct-on-first-try for others.
         qwizdata[i_qwiz].correct_on_try1[i_question][target_id] = 0;
      }
   }

   // Update progress.
   display_qwizzled_progress (i_qwiz);
}


// -----------------------------------------------------------------------------
function place_label ($target, $label) {

   // Do-it-myself snap to target.  Make copy of label into child of the
   // target.  Clone false arg says do not copy events (namely, dragging
   // effect).
   var $label_copy = $label.clone (false);
   if (q.qwizard_b) {

      // Remove editor from placed copy; get rid of id (which is editor-
      // specific).  Add class to indicate needs update of content if
      // original label changed.
      $editable = $label_copy.find ('.qwiz_editable');
      $editable.removeAttr ('id')
               .removeAttr ('contenteditable')
               .removeClass ('qwiz_editable')
               .addClass ('qwizzled_label_placed');
      if (debug[12]) {
         console.log ('[place_label] $editable:', $editable);
      }
   }
   $label_copy.appendTo ($target);
   $label_copy.css ({position: 'absolute', left: '4px', top: '50%', height: 'auto', width: '100%', transform: 'translateY(-50%)'});
   $label_copy.removeClass ('qwizzled_label_unplaced');
   $label_copy.find ('.qwizzled_highlight_label').css ('cursor', 'default').removeClass ('label_click_highlight');

   // Move original label back to original position.
   $label.css ({left: '0px', top: '0px'});

   // See if multiple targets for this label.
   var multiple_targets_b = false;
   var classes = $label.attr ('class');
   m = classes.match (/qwizzled_n_targets([0-9]*)/);
   if (m) {
      multiple_targets_b = true;

      // Either decrement targets remaining, or, if only one left, remove
      // class.
      var current_n_targets = m[0];
      var n_targets = parseInt (m[1], 10);
      if (n_targets == 2) {

         // Will be only one left.  Can treat as "normal".  Remove class.
         $label.removeClass (current_n_targets);
      } else {

         // Decrement.  Set flag, remove existing class, add decremented
         // class.
         var new_class = 'qwizzled_n_targets' + (--n_targets);
         $label.removeClass (current_n_targets).addClass (new_class);
      }
   }

   // If not multiple targets, disable drag of original label, and remove
   // class to signal no re-enable.  Also remove cursor css.
   // Gray-out (apply to children, too, in case need to overcome default),
   // move to original position.
   if (! q.qwizard_b && ! multiple_targets_b) {
      if (debug[8]) {
         console.log ('[place_label] (draggable disable) $label[0]:', $label[0]);
      }
      $label.css ({color: 'lightgray', left: '0px', top: '0px'});
      $label.find ('*').css ({color: 'lightgray'});
      $label.find ('.qwizzled_highlight_label').css ('cursor', 'default');
      $label.removeClass ('qwizzled_label_unplaced');

      // This if-error-do-nothing "try" shouldn't be necessary, but won't hurt.
      if (! q.qwizard_b) {
         try {
            $label.draggable ('disable');
         } catch (e) {}
      }

      // Also turn off click (alternative to drag).
      $label.off ('click');
   }

   // This target no longer droppable.  If div, just this.  If span (text
   // target, possibly with multiple spans) find relevant siblings.
   if (! q.qwizard_b) {
      if ($target[0].tagName.toLowerCase () == 'div') {
         $target.droppable ('disable');
      } else {
         var classes = $target.attr ('class');
         var m = classes.match (/qtarget_sib-[0-9]+/);
         if (m) {

            // Restrict to this quiz in case copy on same page.
            var $span = $target.parents ('qwizq').find ('span.' + m[0]);
            $span.droppable ('disable');
         } else {

            // Backwards compatibility -- assume they're in a wrapper span.
            var $siblings = $target.siblings ('span').andSelf ();
            $siblings.droppable ('disable');
         }
      }
   }
}


// -----------------------------------------------------------------------------
this.process_qwiz_pair = function (htm, i_qwiz, existing_quiz_to_qwizard_f,
                                   qwizard_process_dataset_questions_f) {
   if (existing_quiz_to_qwizard_f) {

      // Called from qwizard.js or qwiz_admin_footer_post_func () >
      // process_html2 () to process/parse quiz shortcodes into qwizard
      // structure (questions_cards[]).
      qw = qwizard;
      n_qwizzes = 1;
      set_qwizard_data_b = true;
      q.qwizard_b = true;
      q.no_intro_b = [];
      if (debug[0]) {
         console.log ('[process_qwiz_pair] htm.substr (0, 2000):', htm.substr (0, 2000));
      }
   }

   // Data object for this qwiz.
   qwizdata[i_qwiz] = {};
   qwizdata[i_qwiz].questions            = [];
   qwizdata[i_qwiz].answered_correctly   = [];
   qwizdata[i_qwiz].n_correct            = 0;
   qwizdata[i_qwiz].n_incorrect          = 0;
   qwizdata[i_qwiz].i_question           = -1;
   qwizdata[i_qwiz].i_user_question      = -1;
   qwizdata[i_qwiz].user_question_number = 0;
   qwizdata[i_qwiz].initial_width        = 500;

   qwizdata[i_qwiz].hangman = {};

   qwizdata[i_qwiz].use_dataset = '';
   qwizdata[i_qwiz].dataset_id = {};
   qwizdata[i_qwiz].use_dataset_question_ids = {};
   qwizdata[i_qwiz].bg_img = {};
   qwizdata[i_qwiz].align = '';
   qwizdata[i_qwiz].qrecord_id = '';
   qwizdata[i_qwiz].qrecord_id_ok = 'check credit';
   qwizdata[i_qwiz].information_question_b = {};
   qwizdata[i_qwiz].unit = [];
   qwizdata[i_qwiz].parts_htm = {};

   var m = htm.match (/\[qwiz([^\]]*)\]/m);
   var qwiz_tag   = m[0];
   var attributes = m[1];
   qwiz_tag   = qqc.replace_smart_quotes (qwiz_tag);
   attributes = qqc.replace_smart_quotes (attributes);

   if (debug[0]) {
      console.log ('[process_qwiz_pair] qwiz_tag: ', qwiz_tag);
      console.log ('[process_qwiz_pair] attributes: ', attributes);
   }
   if (set_qwizard_data_b) {
      qw.set_qwizard_data ('qwiz_deck_attributes', attributes);
   }
   var use_dataset_questions_b = false;

   // See if no summary at end (default is yes).
   qwizdata[i_qwiz].summary_b = get_attr (qwiz_tag, 'summary') != 'false';

   // See if use_dataset quiz.
   var use_dataset = get_attr (qwiz_tag, 'use_dataset', true);
   if (use_dataset) {
      qwizdata[i_qwiz].use_dataset = use_dataset;

      // See if no dataset intro.
      var dataset_intro_f = get_attr (qwiz_tag, 'dataset_intro') != 'false';
      qwizdata[i_qwiz].dataset_intro_f = dataset_intro_f;

      // See if not spaced repetition (applies when no dataset intro).
      var spaced_repetition_f = get_attr (qwiz_tag, 'spaced_repetition') != 'false';
      qwizdata[i_qwiz].dataset_questions_to_do = spaced_repetition_f ? 'spaced_repetition' : 'all';

      // See if random question order (default is not).
      qwizdata[i_qwiz].random_b = get_attr (qwiz_tag, 'random') == 'true';

      // See if a display name is given.  First, may include style info.
      // Encode that so can parse out.
      var m = qwiz_tag.match (/\sstyle\s*=\s*"[^"]+"/gm);
      if (m) {
         var len = m.length;
         for (var i=0; i<len; i++) {
            var encoded_style = encodeURIComponent (m[i]);
            qwiz_tag = qwiz_tag.replace (m[i], encoded_style);
         }
      }
      var display_name = get_attr (qwiz_tag, 'display_name');
      if (display_name) {
         qwizdata[i_qwiz].use_dataset_options_display_name = decodeURIComponent (display_name);

         // Delete display_name="..." from qwiz_tag -- so won't contaminate
         // final html created in create_qwiz_divs ().
         var qwiz_tag = qwiz_tag.replace (/\sdisplay_name\s*=\s*"[^"]*?"/, '');
      }

      // Restore any remaining style info to plain HTML.
      qwiz_tag = decodeURIComponent (qwiz_tag);
   }

   // Alignment.  Default = left.  If center, right, or tiled, set.
   var align = get_attr (attributes, 'align');
   if (align == 'center' || align == 'right' || align == 'tiled') {
      qwizdata[i_qwiz].align = align;
      if (align == 'tiled') {

         // Set default spacing.
         qwizdata[i_qwiz].spacing = 20;
      }
   }
   var spacing = parseInt (get_attr (attributes, 'spacing'));
   if (qqc.isInteger (spacing)) {
      qwizdata[i_qwiz].spacing = spacing;
   }

   // See if showing forward-back controls.
   qwizdata[i_qwiz].hide_forward_back_b = get_attr (qwiz_tag, 'hide_forward_back') == 'true';

   // See if showing progress.
   qwizdata[i_qwiz].hide_progress_b = get_attr (qwiz_tag, 'hide_progress') == 'true';

   // Hide Q icon this quiz.
   qwizdata[i_qwiz].hide_qwizcards_icon_b = get_attr (qwiz_tag, 'hide_qwizcards_icon') == 'true';

   // If "qrecord_id=..." present, parse out database ID.
   var qrecord_id = get_attr (attributes, 'qrecord_id');
   if (qrecord_id) {

      // Set flag indicating this quiz subject to recording.  (Will get unset
      // by check_registered returned JavaScript if not registered.)
      qwizdata[i_qwiz].qrecord_id = qrecord_id;

      // Set up arrays to save question text and crc32 of (more complete)
      // question text.
      qwizdata[i_qwiz].q_and_a_text  = {};
      qwizdata[i_qwiz].q_and_a_crc32 = {};

      // On first recorded quiz, set flag that there is at least one quiz
      // subject to recording.  See if user already logged in (get session ID
      // in cookie, see if still valid).
      if (! q.qrecord_b) {
         q.qrecord_b = true;
         if (typeof (document_qwiz_user_logged_in_b) == 'undefined'
                              || document_qwiz_user_logged_in_b == 'not ready') {
            qqc.check_session_id (i_qwiz);
         }
      }

      // Check for "display_pay_screen" attribute -- special to show only login/
      // pay options.
      var display_pay_screen = get_attr (attributes, 'display_pay_screen');
      if (display_pay_screen) {
         if (display_pay_screen == 'true' || display_pay_screen == 'login') {
            display_pay_screen = 'login';
         } else if (display_pay_screen == 'register') {
            display_pay_screen = 'register';
         }
         qwizdata[i_qwiz].display_pay_screen = display_pay_screen;
      }
   }

   // Flag for qwizard.js -- if deck is a dataset.  Also flag passed through to
   // qwiz_admin_footer_post_func ().
   if (q.qwizard_b) {
      if (get_attr (attributes, 'dataset')) {
         q.dataset_b = true;
         if (set_qwizard_data_b) {
            qw.questions_cards_dataset_b = true;
         }
      }
   }

   // If "unit=..." present, set as default for questions in this quiz.
   var unit = get_attr (attributes, 'unit');
   if (unit) {
      qwizdata[i_qwiz].default_unit = unit.replace (/\s/g, '_');
      if (set_qwizard_data_b) {
         qw.set_qwizard_data ('default_unit', unit);
      }
   } else {
      qwizdata[i_qwiz].default_unit = 'null';
   }

   var new_htm = '';
   var no_intro_i_b = false;

   // Is qwiz encoded?  Decode if necessary.  Turns tag into plain '[qwiz]'.
   //htm = decode_qwiz (htm, qwiz_tag);

   // Capture any initial closing tags after [qwiz ...] -- will put them in
   // front of <div> that replaces [qwiz ...].
   var m = htm.match (/\[qwiz[^\]]*\]((<\/[^>]+>\s*)*)/m, '');
   if (m) {
      var initial_closing_tags = m[1];
      //new_htm += initial_closing_tags;
      if (debug[0]) {
         console.log ('[process_qwiz_pair] initial_closing_tags: ', initial_closing_tags);
      }
   }

   // Delete [qwiz], any initial closing tags.
   htm = htm.replace (/\[qwiz[^\]]*\]((<\/[^>]+>\s*)*)/m, '');

   // Take off closing "[/qwiz]".
   var len = htm.length;
   htm = htm.substring (0, len-7);

   // Delete any initial whitespace.
   htm = qqc.trim (htm);

   // See how many questions (needed if re-writing quiz for qwizard.js).
   // Make sure there's at least one question (unless use_dataset or qwizard
   // set up).
   m = htm.match (/\[(q|<code><\/code>q)([^\]]*)\]/gm);
   var n_questions = m ? m.length : 0;
   if (! use_dataset && ! q.qwizard_b && n_questions == 0) {
      errmsgs.push (T ('Did not find question tags ("[q]")') + '.  qwiz: ' + (i_qwiz + 1));
      header_html = '';
   } else {

      // Look for [terms]...[/terms] and/or [add_terms]...[/add_terms] pairs.
      // Parse, and delete.  Include opening tags in front and closing tags
      // after.
      htm = qqc.process_inline_textentry_terms (htm, 'terms', qwizdata, i_qwiz);
      errmsgs = errmsgs.concat (qwizdata.additional_errmsgs);
      htm = qqc.process_inline_textentry_terms (htm, 'add_terms', qwizdata, i_qwiz);
      errmsgs = errmsgs.concat (qwizdata.additional_errmsgs);

      // See if html up to first shortcode is just whitespace, including empty
      // paragraphs.  Limit to first 2000 characters.
      var whitespace = qqc.parse_html_block (htm.substr (0, 2000), ['^'], ['[h]', '[i]', '[q]', '[q '], '[<code></code>q', 'return whitespace');
      if (whitespace) {

         // Yes, delete it.
         htm = htm.replace (whitespace, '');
      }

      // See if header.  Sets header_html global variable.
      htm = process_header (htm, i_qwiz, 0, true);
      if (set_qwizard_data_b && header_html != 'NA') {
         qw.set_qwizard_data ('header_text', header_html);
      }

      // See if intro.  Limit search to first 5000 characters.  Include [x] as
      // a "next" tag in case use_dataset (no questions) or qwizard with no
      // questions.
      var intro_html = qqc.parse_html_block (htm.substr (0, 5000), ['[i]'], ['[q]', '[q ', '[<code></code>q', '<div class="qwizzled_question', '[x]']);

      // See if no [i].
      if (intro_html == 'NA') {

         // No [i] -- intro may be text before [q].  See if there is.  Add flag
         // to ignore &nbsp; (empty paragraph).
         intro_html = qqc.parse_html_block (htm.substr (0, 5000), ['^'], ['[q]', '[q ', '[<code></code>q', '<div class="qwizzled_question', '[x]'], true);

         // If just tags and whitespace, then no intro, unless this is a
         // use_dataset= quiz, which always has at least a "Start quiz" button.
         if (intro_html == '') {
            if (use_dataset) {

               // Create intro with a few line-breaks -- move start button down
               // a bit.
               intro_html = '<br /><br /><br />';
            } else {
               no_intro_i_b = true;
            }
         }
      } else {

         // There is an [i].  Error if text before [i].  Exclude <br...> or
         // <img...> or <span class="qwizard_placeholder">
         var htmx = htm.substr (0, 200);
         htmx = qqc.trim (htmx);
         var i_pos = qqc.opening_tag_shortcode_pos ('[i]', htmx);
         htmx = htmx.substr (i_pos, 5);
         var intro_htmlx = intro_html.replace (/<br[^>]*>/g, '');
         intro_htmlx = qqc.trim (intro_htmlx).substr (0, 5);
         if (htmx != intro_htmlx) {
            errmsgs.push (T ('Text before intro') + ' [i].  qwiz: ' + (i_qwiz + 1));
         }

         // Delete [i] from intro.
         intro_html = intro_html.replace ('[i]', '');

         // If there's an unmatched </p> or the like near the beginning, add a
         // matching opening tag.
         intro_html = qqc.balance_closing_tag (intro_html);
      }

      // If there's an intro, or an intro may be added interactively via the
      // wizard, create intro div.
      if (q.qwizard_b) {
         intro_html = qqc.shortcodes_to_video_elements (intro_html);
      }
      if (! no_intro_i_b || q.qwizard_b) {
         if (debug[0]) {
            console.log ('[process_qwiz_pair] intro_html:', intro_html);
         }
         new_htm += '<div class="intro-qwiz' + i_qwiz + ' qwiz-intro qwiz_editable">'
                  +    qqc.decode_image_tags (intro_html)
                  + '</div>\n';
      }
      if (set_qwizard_data_b) {
         qw.set_qwizard_data ('intro_text', intro_html);
      }

      // If there's exit text, capture (with opening tags) for summary div, and
      // delete.
      var exit_html = qqc.parse_html_block (htm, ['[x]'], []);
      if (exit_html != 'NA') {
         exit_html = exit_html.replace (/\[x\]/, '');

         // Error if a [q] tag inside exit text.
         if (exit_html.search (/\[q[ \]]|<div class="qwizzled_question/) != -1) {
            errmsgs.push ('[x] ' + T ('(exit text) must be last') + '.  qwiz: ' + (i_qwiz + 1));
         } else {

            // Delete exit text from htm.
            var i_pos_exit_opening = qqc. opening_tag_shortcode_pos ('[x]', htm);
            htm = htm.substr (0, i_pos_exit_opening);
         }
      } else {
         exit_html = '';
      }
      if (set_qwizard_data_b) {
         var qwizard_exit_html = qqc.shortcodes_to_video_elements (exit_html);
         qw.set_qwizard_data ('exit_text', qwizard_exit_html);
      }

      // If not using a dataset, may be specific questions from various
      // datasets.  If so, save questions html.
      if (! use_dataset) {
         if (htm.search (/use_dataset_question\s*=\s*/) != -1) {
            use_dataset_questions_b = true;
            qwizdata[i_qwiz].use_dataset_questions_htm = htm;
         }
      }

      if (! use_dataset && (! use_dataset_questions_b || qwizard_process_dataset_questions_f)) {
         if (n_questions == 0) {

            // Qwizard, initially.  Create placeholder (question "-1") -- first
            // question will be added after this.
            qwizdata[i_qwiz].n_questions = 0;
            new_htm += '<div id="qwiz' + i_qwiz + '-q-1" class="qwizq">'
                       + '</div>';
         } else {
            qwizdata[i_qwiz].n_questions = n_questions;
            new_htm = q.process_questions (htm, new_htm, i_qwiz);
         }
      } else {
         if (qwizard_process_dataset_questions_f) {
            qwizard.questions_cards_dataset_b = false;
         }

         // Set up div within which to place questions when downloaded.
         new_htm +=   '<div id="dataset_questions-qwiz' + i_qwiz + '">'
                    + '</div>';
      }
   }
   q.no_intro_b.push (no_intro_i_b);

   // First qwiz tag becomes opening div for box around qwiz.  Add additional
   // div elements (progress, login div, summary div, "next" button).
   new_htm = create_qwiz_divs (i_qwiz, qwiz_tag, new_htm, exit_html);

   //if (debug[3]) {
   //   console.log ('[process_qwiz_pair] new_htm: ', new_htm);
   //}

   if (q.qwizard_b) {

      // Called from qwizard.js - return error messages to qwizard (won't
      // always be used).
      qwizard.errmsgs = errmsgs;
   }
   set_qwizard_data_b = false;

   return new_htm;
}


// -----------------------------------------------------------------------------
this.process_questions = function (htm, new_htm, i_qwiz, i_qwizard_question,
                                                               set_qwizard_f) {
   if (set_qwizard_f) {
      qw = qwizard;
      n_qwizzes = 1;
      set_qwizard_data_b = true;
      q.qwizard_b = true;
   }

   // i_qwizard_question is set if called from qwizard.js.
   if (typeof (i_qwizard_question) != 'undefined') {
      number_first_question = i_qwizard_question;
   } else {
      number_first_question = 0;
   }

   // May be from use_dataset= retrieval.  Delete qwizcard comments, if any.
   // DKTMP -- may leave empty paragraphs.
   // Leave comments in place if from qwizard.js.
   if (! set_qwizard_data_b) {
      if (htm.indexOf ('[!') != -1) {
         htm = htm.replace (/\[!+\][^]*?\[\/!+\]/gm, '');
      }
   }

   // question_html -- everything from first [q] on.
   var question_html = htm.match (/(\[q [^\]]*\]|<div class="qwizzled_question|\[q\])[^]*/m)[0];

   // Find attributes, if any, for each question (topic=, unit=).  First get
   // array of shortcodes.
   var question_shortcodes = question_html.match (/\[(<code><\/code>)*q([^\]]*)\]/gm);
   if (debug[4] || debug[11]) {
      console.log ('[process_questions] question_shortcodes: ', question_shortcodes);
   }

   // Set up list of topic/topics each question.
   n_questions = question_shortcodes.length;
   qwizdata[i_qwiz].question_topics = new Array (n_questions);

   // If wizard, need to know which questions are multiple-choice (because may
   // be in-progress, with only one choice so far).
   if (q.qwizard_b) {
      qwizdata[i_qwiz].qwizard_multiple_choice_b = [];
   }

   // Set up lists of all units and topics.
   qwizdata[i_qwiz].units  = [];
   qwizdata[i_qwiz].topics = [];

   // Capture any opening tags before each "[q...] tag.
   var matches = htm.match (/(<[^\/][^>]*>\s*)*?(\[q[ \]]|\[<code><\/code>q)/gm);
   var q_opening_tags = [];
   var n_q_opening_tags = matches.length;
   for (var i_tag=0; i_tag<n_q_opening_tags; i_tag++) {
      var q_opening_tag = matches[i_tag];
      //q_opening_tag = q_opening_tag.replace (/\[q[ \]]|\[<code><\/code>q|<div class="qwizzled_question|<div class="qwizzled_canvas/gm, '');
      q_opening_tag = q_opening_tag.replace (/\[q[ \]]|\[<code><\/code>q/gm, '');

      // Don't include <img ... /> and <input ... /> tags and anything in front.
      q_opening_tag = q_opening_tag.replace (/[^]*<(img|input)[^>]+>/, '');
      q_opening_tags.push (q_opening_tag);
   }
   if (debug[0] || debug[11]) {
      console.log ('[process_questions] q_opening_tags: ', q_opening_tags);
      console.log ('[process_questions] question_html: ', question_html);
   }

   // Take off initial [q] or [q topic=...] or <div class="qwizzled_question">
   // or <div class="qwizzled_question qwiz_editable">
   // Save flag for which.
   var first_q_qwizzled_b = question_html.substr (0, 2) != '[q';
   if (first_q_qwizzled_b) {
      question_html = question_html.replace (/<div class="qwizzled_question[^>]*>/, '');
   } else {
      var start = question_html.indexOf (']') + 1;
      question_html = question_html.substr (start);
   }

   // Split into individual items.  First split on qwizzled_question divs.
   // Find class="qwizzled_question" and "qwizzled_question qwiz_editable" but not
   // "qwizzled_question_bottom_border_title".
   var qwizzled_pieces = question_html.split (/<div class="qwizzled_question[^_][^>]*>/);
   if (debug[0] || debug[11]) {
      console.log ('[process_questions] qwizzled_pieces.length:', qwizzled_pieces.length);
   }
   var questions_html = [];
   if (qwizzled_pieces.length == 1) {

      // No splits.  Split on [q].
      var q_split = question_html.split (/\[q [^\]]*\]|\[<code><\/code>q [^\]]*\]|\[q\]|\[<code><\/code>q\]/);
      var i_qbeg = 0;
      if (first_q_qwizzled_b) {

         // Put first and second qsplit pieces back together (with the q).
         questions_html.push (q_split[0] + '[q]' + q_split[1]);
         i_qbeg = 2;
      }

      // Regular questions, if any.
      for (var i_q=i_qbeg; i_q<q_split.length; i_q++) {
         questions_html.push (q_split[i_q]);
      }
   } else if (qwizzled_pieces.length > 1) {
      if (first_q_qwizzled_b) {

         // Split each piece on regular [q]s, including old-style (with
         // <code></code>).  Put first and second pieces back together in
         // each case.
         for (var i_qwizzled=0; i_qwizzled<qwizzled_pieces.length; i_qwizzled++) {

            // Note: regular expression /\[(<code><\/code)*q... didn't work --
            // caused extra splits.
            var q_split = qwizzled_pieces[i_qwizzled].split (/\[q [^\]]*\]|\[<code><\/code>q [^\]]*\]|\[q\]|\[<code><\/code>q\]/);
            questions_html.push (q_split[0] + '[q]' + q_split[1]);

            // Additional, if any.
            for (var i_q=2; i_q<q_split.length; i_q++) {
               questions_html.push (q_split[i_q]);
            }
         }
      } else {

         // First piece before a qwizzled question is all regular questions.
         var q_split =  qwizzled_pieces[0].split (/\[q [^\]]*\]|\[q\]/);
         for (var i_q=0; i_q<q_split.length; i_q++) {
            questions_html.push (q_split[i_q]);
         }

         // Split remaining pieces on regular [q]s.  Put first and second
         // pieces back together in each case.
         for (var i_qwizzled=1; i_qwizzled<qwizzled_pieces.length; i_qwizzled++) {
            var q_split = qwizzled_pieces[i_qwizzled].split (/\[q [^\]]*\]|\[<code><\/code>q [^\]]*\]|\[q\]|\[<code><\/code>q\]/);
            questions_html.push (q_split[0] + '[q]' + q_split[1]);

            // Additional, if any.
            for (var i_q=2; i_q<q_split.length; i_q++) {
               questions_html.push (q_split[i_q]);
            }
         }
      }
   }

   if (q.qwizard_b && set_qwizard_data_b
                                && typeof (i_qwizard_question) == 'undefined') {
      qw.set_qwizard_data ('n_questions', n_questions);
   }
   if (! q.qwizard_b || set_qwizard_f) {
      qwizdata[i_qwiz].n_questions = n_questions;
   }
   if (debug[0] || debug[11]) {
      console.log ('[process_questions] n_questions:', n_questions);
      console.log ('[process_questions] questions_html:', questions_html.join ('\n================================================\n'));
   }

   // Create a div for each.
   var question_divs = [];
   var question_div;
   var questions_w_topics_b = false;
   for (var ii=0; ii<n_questions; ii++) {
      var i_question = ii + number_first_question;

      // Look at the attributes for this question.
      var question_shortcode;
      if (typeof (i_qwizard_question) != 'undefined') {
         question_shortcode = question_shortcodes[0];
      } else {
         question_shortcode = question_shortcodes[i_question];
      }
      question_topic = process_question_attributes (i_qwiz, i_question, question_shortcode, i_qwizard_question);
      if (question_topic) {
         questions_w_topics_b = true;
      }

      // See if hangman-style entry.
      if (questions_html[ii].indexOf ('[hangman') != -1
                  || questions_html[ii].indexOf ('hangman_img_wrapper') != -1) {
         question_div = process_hangman (i_qwiz, i_question,
                                         questions_html[ii],
                                         q_opening_tags[ii]);

      // See if free-form entry.
      } else if (questions_html[ii].indexOf ('[textentry') != -1) {
         question_div = process_textentry (i_qwiz, i_question,
                                           questions_html[ii],
                                           q_opening_tags[ii]);

      // See if multiple-choice question.
      } else if (questions_html[ii].search (/\[c\]|\[c\*\]/m) != -1) {

         // Regular multiple-choice question.
         question_div = process_multiple_choice (i_qwiz, i_question,
                                                 questions_html[ii],
                                                 q_opening_tags[ii]);

      } else if (questions_html[ii].search (/<div[^>]+class=".*?qwizzled_label/m) != -1) {

         // Labels.
         qwizzled_b = true;
         qwizdata[i_qwiz].qwizzled_b = true;
         question_div = process_qwizzled (i_qwiz, i_question,
                                          questions_html[ii],
                                          q_opening_tags[ii],
                                          question_shortcodes[ii]);
         if (qwizdata[i_qwiz].correct_on_try1) {
            qwizdata[i_qwiz].correct_on_try1[i_question] = {};
         } else {
            qwizdata[i_qwiz].correct_on_try1 = [];
         }
      } else {

         // "Question" has neither choices or labels.  We'll treat it as an
         // "information-only question".  Set flag to show "Next question"
         // button right away.  Error if no text at all -- unless we're doing
         // qwiz_admin_footer_post_func () processing and this is a specific
         // question from a dataset.  Check no text by deleting tags, "&nbsp;",
         // and whitespace, but have to check for images.
         var question_htm = questions_html[ii];
         var question_html_wo_tags_whitespace = question_htm.replace (/<[^>]+>|&nbsp;|\s/gm, '');
         if (! question_html_wo_tags_whitespace) {
            if (question_htm.indexOf ('img') != -1) {
               question_html_wo_tags_whitespace = true;
            }
         }
         if (! question_html_wo_tags_whitespace) {
            if (! qwizdata[i_qwiz].use_dataset_question_ids[i_question]) {
               errmsgs.push (T ('Question is completely blank') + '.  qwiz: ' + (i_qwiz + 1) + ', ' + T ('question') + ' ' + (i_question + 1));
            }
         } else {
            qwizdata[i_qwiz].information_question_b[i_question] = true;
            if (qwizdata[i_qwiz].qrecord_id) {
               var q_and_a_text;
               if (qqc.isInteger (qwizdata[i_qwiz].dataset_id[i_question])) {
                  q_and_a_text = qqc.remove_tags_eols (question_htm);
                  qwizdata[i_qwiz].q_and_a_text[i_question]  = qqc.q_and_a_hash (q_and_a_text);
                  qwizdata[i_qwiz].q_and_a_crc32[i_question] = qwiz_crc32 (questions_html[ii]);
               } else {
                  qwizdata[i_qwiz].q_and_a_text[i_question]  = qwizdata[i_qwiz].dataset_id[i_question];
                  qwizdata[i_qwiz].q_and_a_crc32[i_question] = 'dataset';
               }
            }
         }

         if (set_qwizard_data_b) {
            qw.questions_cards[i_question].type = 'information_only';

            // Include opening tags -- formatting -- with question text, so
            // qwizard will save (and allow changes to) formatting.
            question_htm = qqc.shortcodes_to_video_elements (question_htm);
            qw.questions_cards[i_question].question_text = q_opening_tags[ii] + question_htm;
         }

         // Wrap in div for this qwiz and question.
         var bg_img_style = create_bg_img_style (i_qwiz, i_question);
         question_div = '<div id="qwiz' + i_qwiz + '-q' + i_question + '" class="qwizq"' + bg_img_style + '>\n'
                    +      '<div class="qwiz-question qwiz_editable">'
                    +          q_opening_tags[ii] + question_htm
                    +      '</div>'
                    +   '</div>';
      }
      question_divs.push (question_div);
   }
   new_htm += question_divs.join ('\n');

   if (questions_w_topics_b) {

      // If any topics given, and any questions without topics, set their
      // topic to "Other".  Do so in check_questions_have_topics () -- after
      // we know whether some questions are information-only.
      if (debug[4]) {
         console.log ('[process_questions] topics: ' + qwizdata[i_qwiz].topics.join ('; '));
      }

      // Set up statistics by topic.  Object of objects (list of lists).  Done
      // in check_questions_have_topics ().
   }
   if (set_qwizard_data_b) {
      qw.unit_names = qwizdata[i_qwiz].units;
   }

   // Check that if any questions have topics, all do (except information-
   // only questions).
   if (qwizdata[i_qwiz].topics.length) {
      check_questions_have_topics (i_qwiz);
      if (set_qwizard_data_b) {
         qw.topic_names = qwizdata[i_qwiz].topics;
      }
   }

   if (debug[3] || debug[11]) {
      console.log ('[process_questions] new_htm: ', new_htm);
   }
   if (debug[12]) {
      console.log ('[process_questions] errmsgs: ', errmsgs.join ('\n'));
   }

   return new_htm;
}


// -----------------------------------------------------------------------------
/*
function decode_qwiz (htm, qwiz_tag) {

   // Get html after [qwiz] tag and before [/qwiz] tag.
   htm = htm.substring (qwiz_tag.length, htm.length-7);
   while (true) {

      // See if non-base64 character (blank, for now) in html.
      var cpos = htm.indexOf (' ');
      if (cpos != -1) {
         break;
      } else {
         htm = atob (htm);
      }
   }

   // Add back [qwiz] [/qwiz] tags.
   htm = '[qwiz]' + htm + '[/qwiz]';

   return htm;
}
*/


// -----------------------------------------------------------------------------
// Divs at top of question (qwiz itself, progress), and at bottom "next".
function create_qwiz_divs (i_qwiz, qwiz_tag, htm, exit_html) {

   // Capture any style info or other attributes provided.
   var m = qwiz_tag.match (/\[qwiz([^\]]*)\]/m);
   var attributes = m[1];
   if (debug[0]) {
      console.log ('[create_qwiz_divs] attributes: ', attributes);
   }

   // If non-default width set, set flag.  qrecord_id attribute handled
   // earlier, in process_qwiz_pair ().
   attributes = qqc.replace_smart_quotes (attributes);
   var non_default_width_b = attributes.search (/[\s;"]width/m) != -1;


   // If "repeat_incorrect=..." present, parse out true/false.
   var repeat_incorrect_value = get_attr (attributes, 'repeat_incorrect');
   qwizdata[i_qwiz].repeat_incorrect_b = repeat_incorrect_value != 'false';
   if (debug[0]) {
      console.log ('[create_qwiz_divs] repeat_incorrect_value:', repeat_incorrect_value, ', repeat_incorrect_b:', qwizdata[i_qwiz].repeat_incorrect_b);
   }

   // If "random=..." present, parse out true/false.
   var random = get_attr (attributes, 'random');
   qwizdata[i_qwiz].random_b = random == 'true';
   if (debug[0]) {
      console.log ('[create_qwiz_divs] random:', random, ', random_b:', qwizdata[i_qwiz].random_b);
   }

   // Undisplayed version of qwiz div, so can measure default width if need to.
   // Keep out of flow.  (Don't let margins, padding take up room.)
   var top_html = [];
   if (non_default_width_b) {
      var xattributes = attributes.replace (/(style\s*=\s*"[^"]*)/, '$1; position: absolute;');

      // Correct double ";;" if we've done that.
      xattributes = xattributes.replace (/;\s*;/g, ';');
      top_html.push ('<div id="xqwiz' + i_qwiz + '" class="xqwiz" ' + xattributes + '></div>\n');
   }

   // This qwiz opening div.  If align=, add appropriate margin css to style.
   // Add style= if not there.  Then delete align=... from attributes.
   if (qwizdata[i_qwiz].align) {
      var align = qwizdata[i_qwiz].align;
      var style = '';
      if (align == 'center') {
         style = 'margin: auto;';
      } else if (align == 'right') {
         style = 'margin-left: auto;';
      } else if (align == 'tiled') {
         style = 'float: left;';
         if (qwizdata[i_qwiz].spacing) {
            var spacing = qwizdata[i_qwiz].spacing + 'px';
            style += ' margin-left: ' + spacing + '; margin-bottom: ' + spacing + ';';
         }
      }
      m = attributes.match (/style\s*=\s*"[^"]*/m);
      if (m) {
         attributes = attributes.replace (/(style\s*=\s*"[^"]*)/m, '$1' + '; ' + style);

         // Correct double ";;" if we've done that.
         attributes = attributes.replace (/;\s*;/g, ';');
      } else {

         // No style=.  Add.
         attributes += ' style="' + style + '"';
      }
      attributes = attributes.replace (/align\s*=\s*"[^"]*"/, '');
      if (debug[0]) {
         console.log ('[create_qwiz_divs] attributes: ', attributes);
      }
   }
   top_html.push ('<div id="qwiz' + i_qwiz + '" class="qwiz" ' + attributes + '>');

   // Exit mobile mode panel and slide-in icon.
   top_html.push (   '<div id="overlay-exit-mobile-qwiz' + i_qwiz + '" class="overlay-exit-mobile-qwiz" onclick="' + qname + '.close_panel_exit_mobile(this)">');
   top_html.push (      '<div id="panel-exit-mobile-qwiz' + i_qwiz + '" class="panel-exit-mobile-qwiz">');
   top_html.push (         '<button onclick="' + qname + '.exit_mobile (' + i_qwiz + ')">');
   top_html.push (            'Back to page view');
   top_html.push (         '</button>');
   top_html.push (         '<br />');
   top_html.push (         '<span>');
   top_html.push (            '(To return to this full-screen view, tap ');
   top_html.push (            '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAIAAADZF8uwAAAAk0lEQVR4nI3QMQ6EIBAF0BG2YLiGtjRcgt7EcBfDhShtbLwBHIgCJrPFbrGJqPvrl/k/MzAzPOUFAMYYRCSiaZpijGckAAARSynM3BVf1FpTSkkpQwiXaBzHnLNzbtu2Lhr+GS4exSUyxqzrCgDLssDnBefM87zv+3EcRHS3yVpba0XElFK/znsvhNBal1LuLv3mDbu1OYLB67+mAAAAAElFTkSuQmCC" />');
   top_html.push (            ')');
   top_html.push (         '</span>');
   top_html.push (         '<div class="panel-icon-exit-mobile-qwiz"></div>');
   top_html.push (      '</div>');
   top_html.push (   '</div>');
   top_html.push (   '<div id="icon-exit-mobile-qwiz' + i_qwiz + '" class="icon-exit-mobile-qwiz" onclick="' + qname + '.open_panel_exit_mobile (' + i_qwiz + ')"></div>');


   // Header div.  If no header, hide.
   var style = '';
   if (header_html == '' || header_html == 'NA' || header_html.indexOf ('Enter header text') != -1) {
      style = ' style="display: none;"';
   }
   top_html.push ('<div class="header-qwiz' + i_qwiz + ' qwiz-header qwiz_editable"' + style + '>');
   top_html.push (    header_html);
   top_html.push ('</div>');
   top_html = top_html.join ('\n');

   // ..........................................................................
   // Mode and progress divs.  (Set up in any case, in case single-question
   // qwiz consisting of a labeled diagram.)
   /*
   var learn_mode_title = T ('Learn mode: questions repeat until answered correctly.');
   var test_mode_title  = T ('Test mode: incorrectly-answered questions do not repeat.');
   var mode;
   var title;
   if (qwizdata[i_qwiz].repeat_incorrect_b) {
      mode = T ('Learn');
      title = learn_mode_title + ' ' + test_mode_title;
   } else {
      mode = T ('Test');
      title = test_mode_title + ' ' + learn_mode_title;
   }
   */
   var progress_div_html = [];
   progress_div_html.push ('<div class="qwiz-progress-container qwiz' + i_qwiz + '">');

   // "Go-mobile" icon.  First, large clickable target positioned absolutely,
   // centered on go-mobile icon.
   progress_div_html.push (   '<div class="go-mobile-qwiz go-mobile-qwiz' + i_qwiz + '" onclick="' + qname + '.go_mobile (' + i_qwiz + ')" title="Full-screen view">');
   progress_div_html.push (   '</div>');

   // Image is in the regular flow (float left, though).
   progress_div_html.push (   '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAKwmlDQ1BJQ0MgUHJvZmlsZQAASImVlwdUU9kWhs+96Y2WEAEpofcuXUroAZReRSUkgYQSY0JQsSviCCqKiAiWoQxVwUEpMhbEgm1QbNgnyKCiPgcLNlTeBYYw89567633r3XW/e6+++y9z1nnrLUvABQ8WyTKgJUAyBRmiSMCvBlx8QkM3O8AAligChSAHpsjETHDwkIAounn3/XhDuKN6KblRKx///5fpczlSTgAQGEIJ3MlnEyEjyHjNUckzgIAVY3Y9ZdliSb4IsI0MVIgwg8nOHWKRyY4eZLR6EmfqAgfhNUAwJPZbHEqAGQDxM7I5qQicci+CNsIuQIhwsg78ODw2VyEkbzAIjNzyQTLEDZJ/kuc1L/FTJbHZLNT5Ty1lknhfQUSUQZ7xf+5Hf9bmRnS6RxGyCDzxYERyJOO7Nnd9CXBchYmzw+dZgF30n+S+dLA6GnmSHwSppnL9g2Wz82YHzLNKQJ/ljxOFitqmnkSv8hpFi+JkOdKEfswp5ktnskrTY+W2/k8ljx+Dj8qdpqzBTHzp1mSHhk84+Mjt4ulEfL6ecIA75m8/vK1Z0r+sl4BSz43ix8VKF87e6Z+npA5E1MSJ6+Ny/P1m/GJlvuLsrzluUQZYXJ/XkaA3C7JjpTPzUIO5MzcMPkeprGDwqYZhIAAwADRIANkATFgIxwIkJOaxVs+cUaBzxLRCrEglZ/FYCK3jMdgCTlWFgw7GzsbACbu7NSReHd38i5CdPyMTYJ4uG5DjKIZ20IaAMf4AChwZmxGRch1JAFwNoEjFWdP2SauE8AAIlAENKAOtIE+MAGWwA44AjfgBfxAEAgFUSAeLAIcwAeZSOXLwCqwHuSBArAD7AZl4CCoAnXgMGgB7eAEOAMugCvgOrgNHgAZGAIvwQj4AMYgCMJBFIgKqUM6kCFkDtlBzpAH5AeFQBFQPJQEpUJCSAqtgjZCBVARVAZVQPXQz9Bx6Ax0CeqD7kED0DD0FvoCo2AyTIO1YCPYGnaGmXAwHAUvhFPhpXAOnAtvh0vhSvgQ3Aafga/At2EZ/BIeRQEUCUVH6aIsUc4oH1QoKgGVghKj1qDyUSWoSlQTqhPVg7qJkqFeoT6jsWgqmoG2RLuhA9HRaA56KXoNeiu6DF2HbkOfQ99ED6BH0N8xFIwmxhzjimFh4jCpmGWYPEwJpgbTijmPuY0ZwnzAYrF0rDHWCRuIjcemYVdit2L3Y5uxXdg+7CB2FIfDqePMce64UBwbl4XLw+3FHcKdxt3ADeE+4Ul4Hbwd3h+fgBfiN+BL8A34U/gb+Gf4MYISwZDgSgglcAkrCIWEakIn4RphiDBGVCYaE92JUcQ04npiKbGJeJ74kPiORCLpkVxI4SQBaR2plHSEdJE0QPpMViGbkX3IiWQpeTu5ltxFvkd+R6FQjChelARKFmU7pZ5ylvKY8kmBqmClwFLgKqxVKFdoU7ih8FqRoGioyFRcpJijWKJ4VPGa4islgpKRko8SW2mNUrnScaV+pVFlqrKtcqhypvJW5QblS8rPVXAqRip+KlyVXJUqlbMqg1QUVZ/qQ+VQN1KrqeepQzQszZjGoqXRCmiHab20EVUV1TmqMarLVctVT6rK6Ci6EZ1Fz6AX0lvod+hfZmnNYs7izdoyq2nWjVkf1Wareanx1PLVmtVuq31RZ6j7qaer71RvV3+kgdYw0wjXWKZxQOO8xqvZtNluszmz82e3zL6vCWuaaUZortSs0ryqOaqlrRWgJdLaq3VW65U2XdtLO027WPuU9rAOVcdDR6BTrHNa5wVDlcFkZDBKGecYI7qauoG6Ut0K3V7dMT1jvWi9DXrNeo/0ifrO+in6xfrd+iMGOgbzDFYZNBrcNyQYOhvyDfcY9hh+NDI2ijXabNRu9NxYzZhlnGPcaPzQhGLiabLUpNLklinW1Nk03XS/6XUz2MzBjG9WbnbNHDZ3NBeY7zfvs8BYuFgILSot+i3JlkzLbMtGywErulWI1QardqvX1gbWCdY7rXusv9s42GTYVNs8sFWxDbLdYNtp+9bOzI5jV253y55i72+/1r7D/s0c8zm8OQfm3HWgOsxz2OzQ7fDN0clR7NjkOOxk4JTktM+p35nmHOa81fmiC8bF22WtywmXz66OrlmuLa5/uFm6pbs1uD2fazyXN7d67qC7njvbvcJd5sHwSPL40UPmqevJ9qz0fOKl78X1qvF6xjRlpjEPMV9723iLvVu9P/q4+qz26fJF+Qb45vv2+qn4RfuV+T321/NP9W/0HwlwCFgZ0BWICQwO3BnYz9JicVj1rJEgp6DVQeeCycGRwWXBT0LMQsQhnfPgeUHzds17ON9wvnB+eygIZYXuCn0UZhy2NOyXcGx4WHh5+NMI24hVET2R1MjFkQ2RH6K8owqjHkSbREuju2MUYxJj6mM+xvrGFsXK4qzjVsddideIF8R3JOASYhJqEkYX+C3YvWAo0SExL/HOQuOFyxdeWqSxKGPRycWKi9mLjyZhkmKTGpK+skPZlezRZFbyvuQRjg9nD+cl14tbzB3mufOKeM9S3FOKUp6nuqfuSh3me/JL+K8EPoIywZu0wLSDaR/TQ9Nr08czYjOaM/GZSZnHhSrCdOG5JdpLli/pE5mL8kSypa5Ldy8dEQeLaySQZKGkI4uGNEdXpSbSTdKBbI/s8uxPy2KWHV2uvFy4/OoKsxVbVjzL8c/5aSV6JWdl9yrdVetXDaxmrq5YA61JXtO9Vn9t7tqhdQHr6tYT16ev/3WDzYaiDe83xm7szNXKXZc7uClgU2OeQp44r3+z2+aDP6B/EPzQu8V+y94t3/O5+ZcLbApKCr5u5Wy9vM12W+m28e0p23sLHQsP7MDuEO64s9NzZ12RclFO0eCuebvaihnF+cXvdy/efalkTsnBPcQ90j2y0pDSjr0Ge3fs/VrGL7td7l3evE9z35Z9H/dz99844HWg6aDWwYKDX34U/Hi3IqCirdKosqQKW5Vd9bQ6prrnJ+ef6ms0agpqvtUKa2V1EXXn6p3q6xs0Gwob4UZp4/ChxEPXD/se7miybKpopjcXHAFHpEde/Jz0852W4Jbuo85Hm44ZHtvXSm3Nb4PaVrSNtPPbZR3xHX3Hg453d7p1tv5i9UvtCd0T5SdVTxaeIp7KPTV+Ouf0aJeo69WZ1DOD3Yu7H5yNO3vrXPi53vPB5y9e8L9wtofZc/qi+8UTl1wvHb/sfLn9iuOVtqsOV1t/dfi1tdext+2a07WO6y7XO/vm9p264XnjzE3fmxdusW5duT3/dt+d6Dt3+xP7ZXe5d5/fy7j35n72/bEH6x5iHuY/UnpU8ljzceVvpr81yxxlJwd8B64+iXzyYJAz+PJ3ye9fh3KfUp6WPNN5Vv/c7vmJYf/h6y8WvBh6KXo59irvH8r/2Pfa5PWxP7z+uDoSNzL0Rvxm/O3Wd+rvat/Ped89Gjb6+EPmh7GP+Z/UP9V9dv7c8yX2y7OxZV9xX0u/mX7r/B78/eF45vi4iC1mT7YCKGTAKSkAvK0FgBIPAPU6AMQFUz31pKCp/4BJAv+Jp/ruSTkCgIQC0V0ATLRoVX+2tIrIe5gXAFFeALa3l48/JUmxt5uKRWpHWpOS8fF3SP+IMwXgW//4+Fj7+Pi3GqTY+wB0fZjq5SekjfxXLMADqHzTrZYB8K/6J1bAD27htQDfAAAACXBIWXMAABYlAAAWJQFJUiTwAAACBGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+NDY4PC9leGlmOlBpeGVsWURpbWVuc2lvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxYRGltZW5zaW9uPjQ2NjwvZXhpZjpQaXhlbFhEaW1lbnNpb24+CiAgICAgICAgIDx0aWZmOk9yaWVudGF0aW9uPjE8L3RpZmY6T3JpZW50YXRpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgrdRrnkAAADWklEQVRIDZ1VW08TQRT+uvRqCBoI5ckCD3JRjEALCfFNMJHfIH9B0V+BkIj6omgiEilPBiFyT+TRAAX1wQj0JpBIFYQaa6Db7WWcc8rWBKl2nWR2Z3bOfOf2nbMmfzAk1tfWYLfbkclkABN4KIqCpJZEs7sZ5eVOpFIpmExHh1mRwp53+/uFlMw7P3xcFRkhxGEiIVQtaXiaHQ4HW+LxtCAS2UZxcTHS6TS2IxG8np/HhfP1SEhPFJNSmMXHpJRMhowHYrEfUkEEaiKBcDgMr3cEl9vaEFcTx64Y2yp6zK1WG5wVFUglk4wwNPQMgVAYp+y2bG6M4eakFY6+3MbVOHZ3dqCqKmpqazE5MQGPTHAw/ImVUNj+ZyhWq5XvlZWVoae3F9FoFAG/Hw0NF/EzFkODzIHuicy1cR29fX3MIN/yirwvxIvRUd47nRVCesJrm80mAqGQSMvzQ9UYm/D23Xux6FtmcKIhUXJ0bIyBJf/FpcZGXkvTxe7evkhLgYO4WjBdQVYRaDyh5awjbS/Hxhn4rMvF74ePBvg8kUyxbKE1gd/AGltFIaBvNMZfTTD4wOMnIiWt0FLpHDjJHar/9sRElhzPHLWMoqIiULsIh0JwVVbCYrEgKSlM36ltyLzI4oOsG43ljmPo+xMV0KF0gGXsVgtkWHhPCgncIWvj294+Dg8OUFXpoijkVZK3/qmx0ZShYHBaUy1Q4UWj39F98waqqyqxHgjCYbPyWbalZY2jKLCRhSaLYk55iHzdEde7ujg35CjN1XU/pYzZRXKESSMpKYdCFRA1aSwsLjHouZoa0djUlFOkKyEiEOb07Kz4HPlSuAK6ROwiWk9OzzBwSUmJqD0qRt0TovzTwUE+f7OwaFyB7v7k1DSDyPYu6urreX2lvV303OnlNSn0rawYU6B7odfJ1JEnBFZXV5cDdrvdvF5c8om8LJKXThzEJhkFyh06O69hZm6O5YLBIFwuF6qqq+W/JZa9K+vEsAK6SfVANKR/VUfHVXTfus001TQNmxsboLeuwXy0MvQiD8xmM4N6h5/jwf17aGltRTwex+kzZ1BaWoqtra0sJsXV6KQcEFuGvSO5uEu0P9bEoryt4m8ukQfUkwKBALY2N7kv8R9Pxlwf0gK4PR78Ak4cQYObEn/YAAAAAElFTkSuQmCC" class="go-mobile-qwiz go-mobile-qwiz' + i_qwiz + '" />');

   // "Exit-mobile" icon and image, too.
   progress_div_html.push (   '<div class="exit-mobile-qwiz exit-mobile-qwiz' + i_qwiz + '" onclick="' + qname + '.exit_mobile (' + i_qwiz + ')" title="Exit full-screen view">');
   progress_div_html.push (   '</div>');
   progress_div_html.push (   '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAAlwSFlzAAALEwAACxMBAJqcGAAAAVlpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KTMInWQAAAoVJREFUSA2lVj2PEkEY3plZV5I9Q3KBqzFHQkFJaCx0sTCxo6EnsfMPWPjBctqoueqMNf/CwtODxo7ySCxMaAjhgiRGiXjsh8+zzl5WKoZ7k+GdnZ33ed6PeWdRtVrtxnQ6jWxbvrUs8SCO449cq1Qqst1uWxBVr9fFaDSKW62WouaikTiO0xVCxBy2bXdpXC6Xb5ZKpZzv+xKkIp/PH3JuBMzN9FyD/4Fea5IjvmMk1FLKExAfc24sMH6tCQgeYgSa5BXB4MAz/ewbg6cG8M4niAZPotDPZ1ozdZ10v5GuVqsODTIkJGIkkQZn6q5qYwSOzbJYLEYsaBAEvlLqpQYQWq+h07leMlPS87wI4IIFhX4B8zMNGkAnRTaD/H+37Pf7PO+Xw+FwzYLidQPjUm9jBCSiRP+U4S+bJ44twaOoc570w+YcNUIjmkuSXzbRcrl8DPOfGOzUbEPR8z0QfFitVp8xNxNGsG2Hpo1nxmC4e1sSOo0DZO90BHO53H2cuIfw7RfGZjqJeWt/331/cfHjG+bmwoJvHoLss1LyHS9Ipl+ZwyeX3x0AeLD9re15EEIM9KrqhGH4BIQSV7vMhqf3bqVSOxu702bkldNH6o5Yp16v57CJd42ggQjuAZCec5CIchvpsyeTyWmhUFCLxcK6LgHTklyW0OwfAeK74JDz+fzUdd2UGK8MBACdTFGvrnSs8apPvyddQl4nAg8FfR5F0SOmhZ4DL4kCOsRzA9fPHkmMBXjHOIonNEwbD2tdHRWjSL4hIHhjDM4OPTjIH8JDwTn/GPB7QiCQpF9GfqB8ru2SInV+/vX7YDCwx+OxaDabAU8LCzqbzT7BaxcRfEEvPGV0fwEIA/zW345reQAAAABJRU5ErkJggg==" class="exit-mobile-qwiz exit-mobile-qwiz' + i_qwiz + '" />');

   /*
   progress_div_html.push (   '<div id="mode-qwiz' + i_qwiz + '" class="qwiz-mode" title="' + title + '">');
   progress_div_html.push (      'Mode: ' + mode);
   progress_div_html.push (   '</div>');
   */

   if ((   qwizdata[i_qwiz].n_questions > 1
        || qwizdata[i_qwiz].use_dataset
        || qwizdata[i_qwiz].use_dataset_questions_htm) && ! q.preview) {
      var style = '';
      if (qwizdata[i_qwiz].hide_forward_back_b) {
         style = ' style="visibility: hidden;"';
      }
      var title;
      if (qwizdata[i_qwiz].use_dataset && qwizdata[i_qwiz].dataset_intro_f) {
         title = 'Go to &ldquo;Choose questions&rdquo;';
      } else {
         title = 'Go to first question';
      }
      progress_div_html.push ('<span class="bbfe bbfe-qwiz' + i_qwiz + ' bck-question-qwiz' + i_qwiz + '"' + style + ' onclick="' + qname + '.bck_question (' + i_qwiz + ', true )" title="' + title + '">');
      progress_div_html.push (   '<span class="bar-up">|</span>&#x25c0;&#xFE0E;');
      progress_div_html.push ('</span>');

      progress_div_html.push ('<span class="bbfe bbfe-qwiz' + i_qwiz + ' bck-question-qwiz' + i_qwiz + '"' + style + ' onclick="' + qname + '.bck_question (' + i_qwiz + ', false)" title="Go to previous question">');
      progress_div_html.push (   '&#x25c0;&#xFE0E;');
      progress_div_html.push ('</span>');

      progress_div_html.push ('<span class="question-number-qwiz question-number-qwiz' + i_qwiz + '"' + style + '>');
      progress_div_html.push ('</span>');

      progress_div_html.push ('<span class="bbfe bbfe-qwiz' + i_qwiz + ' fwd-question-qwiz' + i_qwiz + '"' + style + ' onclick="' + qname + '.fwd_question (' + i_qwiz + ', false)" title="Go to next question">');
      progress_div_html.push (   '&#x25b6;&#xFE0E;');
      progress_div_html.push ('</span>');

      progress_div_html.push ('<span class="bbfe bbfe-qwiz' + i_qwiz + ' fwd-question-qwiz' + i_qwiz + '"' + style + ' onclick="' + qname + '.fwd_question (' + i_qwiz + ', true )" title="Go to most-recent question">');
      progress_div_html.push (   '&#x25b6;&#xFE0E;<span class="bar-up">|</span>');
      progress_div_html.push ('</span>');
   }


   if (qwizdata[i_qwiz].qrecord_id) {

      // Lock/unlock.
      var plugin_url = qqc.get_qwiz_param ('url', './');
      progress_div_html.push ('<div class="qwiz_icon_and_menu_container  lock_unlock qwiz' + i_qwiz + '">');
      progress_div_html.push (   '<div id="locked-qwiz' + i_qwiz + '" class="qwiz-locked qwiz_menu_icon">');
      progress_div_html.push (      '<img src="' + plugin_url + '/images/icon_locked.png" />');
      progress_div_html.push (   '</div>');

      progress_div_html.push (   '<div id="unlocked-qwiz' + i_qwiz + '" class="qwiz-unlocked qwiz_menu_icon">');
      progress_div_html.push (      '<img src="' + plugin_url + '/images/icon_unlocked.png" />');
      progress_div_html.push (   '</div>');

      progress_div_html.push (   '<div class="qwiz_icon_trigger_and_menu qwiz-hover">');
      progress_div_html.push (      '<div class="qwiz_icon_trigger">');
      progress_div_html.push (      '</div>');

      // Add pay/unlock menu div.  Populate after start/login.
      progress_div_html.push (      '<div id="pay_unlock_menu-qwiz' + i_qwiz + '" class="qwiz-pay_unlock_menu qwiz_menu">');
      progress_div_html.push (      '</div>');
      progress_div_html.push (   '</div>');
      progress_div_html.push ('</div>');

      // Set up pull-down arrow for user menu login/logout, etc.  Right end of
      // progress bar.  Add class if this quiz doesn't have an intro/start
      // button or is single-question quiz.
      var addclass = '';
      if (q.no_intro_b[i_qwiz] || qwizdata[i_qwiz].n_questions == 1) {
         addclass = ' qwiz-usermenu_icon_no_intro';
      }
      progress_div_html.push ('<div class="qwiz_icon_and_menu_container qwiz' + i_qwiz + '">');
      progress_div_html.push (   '<div class="qwiz-usermenu_icon qwiz_menu_icon' + addclass + '">');
      progress_div_html.push (      '&#x25bc;');
      progress_div_html.push (   '</div>');

      progress_div_html.push (   '<div class="qwiz_icon_trigger_and_menu qwiz-hover">');
      progress_div_html.push (      '<div class="qwiz_icon_trigger" style="left: -12px; top: -4px;">');
      progress_div_html.push (      '</div>');

      // User menu div.  Populate after start/login.
      progress_div_html.push (      '<div id="usermenu-qwiz' + i_qwiz + '" class="qwiz-usermenu qwiz_menu">');
      progress_div_html.push (      '</div>');
      progress_div_html.push (   '</div>');
      progress_div_html.push ('</div>');
   }

   // Div for progress numbers.
   style = '';
   if (qwizdata[i_qwiz].hide_progress_b) {
      style = ' style="display: none;"';
   }
   progress_div_html.push (   '<div id="progress-qwiz' + i_qwiz + '" class="qwiz-progress"' + style + '>');
   progress_div_html.push (   '</div>');

   // Close container.
   progress_div_html.push (   '<div style="clear: both;"></div>');
   progress_div_html.push ('</div>');

   progress_div_html = progress_div_html.join ('\n');

   // ..........................................................................
   // Login div, if quiz may be recorded, or if use_dataset.
   var login_div = '';
   if (qwizdata[i_qwiz].qrecord_id || qwizdata[i_qwiz].use_dataset) {
      login_div =  '<div id="qwiz_login-qwiz' + i_qwiz + '" class="qwiz-login">\n'
                 + '</div>';
   }


   // ..........................................................................
   var bottom_html = [];
   if (   qwizdata[i_qwiz].n_questions > 1
       || qwizdata[i_qwiz].use_dataset
       || qwizdata[i_qwiz].use_dataset_questions_htm || q.qwizard_b) {

      // Summary div.  If exit text, see if "[unpaid]...[/unpaid] shortcode
      // pair -- put into undisplayed span.  Displayed if pay quiz but user
      // hasn't paid.
      if (exit_html) {
         if (exit_html.indexOf ('[unpaid') != -1 && exit_html.indexOf ('[/unpaid]') != -1) {
            exit_html = exit_html.replace ('[unpaid]', '<span class="unpaid_msg">');
            exit_html = exit_html.replace ('[/unpaid]', '</span>');
         } else {

            // Add span for message from payment type..
            exit_html += '<span class="unpaid_msg_payment_type unpaid_msg"></span>';
         }
      } else {

         // Add span for message from payment type..
         exit_html += '<span class="unpaid_msg_payment_type unpaid_msg"></span>';
      }

      // Replace "[restart]", if there, with restart button html plus
      // (initially-non-displayed) "exit-mobile-mode" button.
      // If no exit text, just add non-displayed "exit-mobile-mode" button.
      // If use_dataset= quiz, add [restart] if not there.
      if (qwizdata[i_qwiz].use_dataset) {
         if (exit_html.indexOf ('[restart') == -1) {
            exit_html += '<br />[restart]';
         }
      }
      if (exit_html) {
         exit_html = create_restart_button (i_qwiz, exit_html);
      }
      if (q.qwizard_b) {
         exit_html = qqc.shortcodes_to_video_elements (exit_html);
      }
      if (qwizdata[i_qwiz].summary_b) {
         bottom_html.push (create_summary_report_div (i_qwiz, exit_html));
      }
   } else {

      // For single-question quiz, exit text shown rather than next-
      // button.
      if (n_questions == 1 && exit_html) {
         exit_html = create_restart_button (i_qwiz, exit_html);
         bottom_html.push ('<div class="single-question_exit">');
         bottom_html.push (   exit_html);
         bottom_html.push ('</div>');
      }

      // Single-question quiz.  If not labeled diagram, don't take any space
      // with progress bar.  Discard progress div if quiz won't be recorded
      // (which requires menu icon) and not mobile device (which requires
      // "expand-to-mobile" icon).
      if (! qwizdata[i_qwiz].qwizzled_b && ! qwizdata[i_qwiz].qrecord_id && ! qqc.is_mobile () && ! q.qwizard_b) {
         progress_div_html = '';
      }
   }

   // "Next" button.  Begins as "Start quiz" button.  If quiz may be recorded,
   // and user not logged in, go to login rather than first question (if hasn't
   // declined).
   bottom_html.push ('<div class="next_button" id="next_button-qwiz' + i_qwiz + '">\n');
   bottom_html.push (   '<button class="qwiz_button" onclick="' + qname + '.next_question (' + i_qwiz + ')">');
   bottom_html.push (       '<span id="next_button_text-qwiz' + i_qwiz + '">');
   bottom_html.push (          T ('Start quiz'));
   bottom_html.push (       '</span>');
   bottom_html.push (   '</button>\n');
   bottom_html.push ('</div>\n');
   if (! qwizdata[i_qwiz].summary_b) {
      if (n_questions > 1 && exit_html) {
         bottom_html.push (create_summary_report_div (i_qwiz, exit_html));
      }
   }

   style = '';
   if (qqc.get_qwiz_param ('beta')) {
      style = 'style = "background: red;"';
   }
   if (! qwizdata[i_qwiz].hide_qwizcards_icon_b) {
      bottom_html.push ('<div class="icon_qwiz" id="icon_qwiz' + i_qwiz + '" ' + style + '>');
      var icon_qwiz = qqc.get_qwiz_param ('icon_qwiz');
      if (icon_qwiz != 'Not displayed') {
         var title = 'Want to use this quiz in your own class?';
         if (icon_qwiz != 'Icon only') {

            // If qrecord_id, sharing, href will be reset by jjax call to
            // check_registered.php.
            var share_loc = qqc.get_qwiz_param ('server_loc', 'http://qwizcards.com/admin').replace ('admin', 'share');
            bottom_html.push ('<a href="' + share_loc + '" style="border: none; box-shadow: none;" target="_blank">');
         } else {
            title += '  See qwizcards.com/share';
         }
         bottom_html.push ('<img class="icon_qwiz" style="border: none;" title="' + title + '" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAUCAIAAAALACogAAAABnRSTlMA/wD/AP83WBt9AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABP0lEQVR4nGP8//8/AymAiSTV5GhgwSZ4rcRrxRooW3futlBnJDlGND/cXzXVccFLVP0oepiwqtZJyH2wrenBtogQBgYGhsv9q15j9cO1qTDVW8JEGRgYGBi0PJ0YGBgYrjzCpuH+qv1rGBgYGHQLoaoZGBgYlOTEGRgYGB68uY+h4fXuQy8ZGBgYnLSRvXjv0UsGBgYGBRFFdA1Prm+6x8DAwBBio4XsyO37GBgYGHTkEHaixYO4mszrWTl1CjmH7iMcKe5nhdAAi4cnL6/A3HbrHgMDw56pJ0QYIOHr5JgmgzASZoOFdggDAwPDy03HRCEhs6YJEne6c0uQHYkUcXt76pL3oTqQQbxqVjay8Sh+cC5pmuuEpkFMWQZNBCNpwMDrWTmT2+5hCCu54EqtomkVLjqYwgoiuGzACWifgQDhK2rq5bcX2gAAAABJRU5ErkJggg==" />');
         if (icon_qwiz != 'Icon only') {
            bottom_html.push ('</a>');
         }
      }
   }
   bottom_html.push ('</div>');


   // Add opening and closing html.
   htm = top_html + progress_div_html + login_div
         + htm + bottom_html.join ('')
         + '</div>\n';  // This qwiz closing div.

   return htm;
}


// -----------------------------------------------------------------------------
function create_summary_report_div (i_qwiz, exit_html) {
   bottom_html = [];
   bottom_html.push ('<div id="summary-qwiz' + i_qwiz + '" class="qwiz-summary">\n');
   bottom_html.push (   '<div id="summary_report-qwiz' + i_qwiz + '">');
   bottom_html.push (   '</div>\n');
   bottom_html.push (   '<div id="qwiz_exit-qwiz' + i_qwiz + '" class="qwiz-exit qwiz_editable">');
   bottom_html.push (       exit_html);
   bottom_html.push (   '</div>\n');

   // Initially-non-displayed "exit-mobile-mode" button.
   bottom_html.push (   '<button class="summary_exit_mobile_qwiz" onclick="' + qname + '.exit_mobile (' + i_qwiz + ')">\n');
   bottom_html.push (      'Return to page view');
   bottom_html.push (   '</button>\n');
   bottom_html.push ('</div>\n');

   return bottom_html.join ('');
}


// -----------------------------------------------------------------------------
function get_login_html (i_qwiz, add_team_member_f, msg, proceed_to_pay_f) {

   add_team_member_f = add_team_member_f ? 1 : 0;
   proceed_to_pay_f  = proceed_to_pay_f  ? 1 : 0;
   var onfocus = 'onfocus="jQuery (\'#qwiz_login-qwiz' + i_qwiz + ' p.login_error\').hide ()"';

   var login_div_html = '<p>';
   if (msg) {
      login_div_html += '<strong>' + msg + '</strong>';
   } else if (add_team_member_f) {
      login_div_html += '<strong>' + T ('Add team member') + '</strong>';
   } else {
      login_div_html += '<strong>' + T ('Record score/credit?') + '</strong>';
   }
   login_div_html += '</p>';

   login_div_html +=
      '<form action="nada" onSubmit="return qwiz_.login (' + i_qwiz + ', ' + add_team_member_f + ', ' + proceed_to_pay_f + ')">\n'
     +   '<table border="0" align="center" width="100%">'
     +      '<tr>'
     +         '<td>'
     +            '<label for="qwiz_username-qwiz' + i_qwiz + '">'+ T ('User name') + '</label>'
     +         '</td>'
     +         '<td>'
     +            '<input type="text" id="qwiz_username-qwiz' + i_qwiz + '" ' + onfocus + ' />'
     +         '</td>'
     +      '</tr>'
     +      '<tr>'
     +         '<td>'
     +            '<label for="qwiz_password-qwiz' + i_qwiz + '">'+ T ('Password') + '</label>'
     +         '</td>'
     +         '<td>'
     +            '<input type="password" id="qwiz_password-qwiz' + i_qwiz + '" />'
     +         '</td>'
     +      '</tr>'
     +      '<tr>'
     +         '<td style="text-align: right;">'
     +            '<span class="qwiz-remember" title="' + T ('Save preference (do not use on shared computer)') + '"><label><span><input type="checkbox" /></span>&nbsp;' + T ('Remember') + '</label></span>'
     +         '</td>'
     +         '<td>'
     +            '<button type="submit" class="qwiz_button">'
     +               T ('Student login')
     +            '</button>'
     +            '&ensp;';
   if (! add_team_member_f) {
      login_div_html +=
                  '<span class="qwiz_button" onclick="qwiz_qcards_common.create_register_taker_screen (\'' + qname + '\', ' + i_qwiz + ', ' + proceed_to_pay_f + ')">'
     +               T ('New student - register')
     +            '</span>'
     +            '&ensp;';
   }
   login_div_html +=
                  '<span class="qwiz_login_cancel_no_thanks qwiz_button" onclick="' + qname + '.no_login (' + i_qwiz + ',' + add_team_member_f + ')">';
   if (add_team_member_f) {
      login_div_html +=
                     T ('Cancel');
   } else {
      login_div_html +=
                    T ('No thanks');
   }
   login_div_html +=
                 '</span>'
     +         '</td>'
     +      '</tr>';

   if (! add_team_member_f) {
      login_div_html +=
            '<tr>'
     +         '<td>'
     +         '</td>'
     +         '<td class="qwiz-smaller">'
     +            '<a href="' + qqc.get_qwiz_param ('server_loc', 'http://qwizcards.com/admin') + '/password_reset_request" target="_blank">'
     +               T ('Forgot password?') + '</a>'
     +         '</td>'
     +      '</tr>'
   }
   var register_page = 'new_account';
   if (window.location.href.indexOf ('sciencemusicvideos.com') != -1) {
      register_page = 'new_account_smv';
   }
   login_div_html +=
             '<tr>'
     +          '<td colspan="2">'
     +             '<hr>'
     +          '</td>'
     +       '</tr>'
     +       '<tr>'
     +          '<td colspan="2" class="qwiz-center">'
     +             '<b>Teachers: track your students&rsquo; progress on quizzes and flashcards.&nbsp; '
     +                '<a href="' + qqc.get_qwiz_param ('secure_server_loc', 'https://qwizcards.com/admin') + '/' + register_page + '" target="_blank">'
     +                'Create&nbsp;teacher&nbsp;administrative&nbsp;account</a></b>'
     +          '</td>'
     +       '</tr>'
     +    '</table>\n'
     + '</form>'
     + '<p class="login_error">'
     +     T ('Login incorrect. Please try again')
     + '</p>\n';

   return login_div_html;
}


// -----------------------------------------------------------------------------
this.qwiz_password_focus = function (el, i_qwiz) {
   el.qwiz_pw = '';
   el.value = '';
   $ ('#qwiz_login-qwiz' + i_qwiz + ' p.login_error').hide ();
}


// -----------------------------------------------------------------------------
function create_restart_button (i_qwiz, htm, feedback_f) {
   var restart = htm.match (/\[restart[^\]]*\]/);
   if (restart) {

      // Default label.
      var label;
      if (feedback_f || qwizdata[i_qwiz].n_questions == 1) {
         label = T ('Do this question again');
      } else {
         if (qwizdata[i_qwiz].use_dataset && qwizdata[i_qwiz].dataset_intro_f) {
            label = T ('Practice more questions');
         } else {
            label = T ('Take this quiz again');
         }
      }

      // Change button text if label="..." given.
      var attr = qqc.replace_smart_quotes (restart[0]);
      var custom_label = get_attr (attr, 'label');
      if (custom_label) {
         label = custom_label;
      }
      var restart_redo = feedback_f ? 'redo_question' : 'restart_quiz' ;
      var restart_button_html =
                       '<button class="qwiz_button qwiz_restart" onclick="' + qname + '.' + restart_redo + ' (' + i_qwiz + ')">'
                     +    label
                     + '</button>';
      htm = htm.replace (restart, restart_button_html);
   }

   return htm;
}


// -----------------------------------------------------------------------------
function create_bg_img_style (i_qwiz, i_question) {
   var style = '';
   var bg_img = qwizdata[i_qwiz].bg_img[i_question];
   if (bg_img) {

      // Examples:
      //   background:    no-repeat 10em 5em / 547px 267px url(...);
      //   background:    no-repeat 10em 5em / auto  267px url(...);
      //                            left top   width height
      var top    = bg_img.top    ? bg_img.top    + 'px' : '0';
      var left   = bg_img.left   ? bg_img.left   + 'px' : '0';
      var width  = bg_img.width  ? bg_img.width  + 'px' : 'auto';
      var height = bg_img.height ? bg_img.height + 'px' : 'auto';

      var style = ' style="background: no-repeat ' + left + ' ' + top
                                       + ' / ' + width + ' ' + height
                                       + ' url(' + bg_img.src + ')"';
      if (debug[0]) {
         console.log ('[create_bg_img_style] style:', style);
      }

      // Also need to measure image and set question div minimum height to
      // accommodate (plus top offset).  Do after page htm set in
      // process_html ().
   }

   return style;
}


// -----------------------------------------------------------------------------
function process_question_attributes (i_qwiz, i_question, question_shortcode, i_qwizard_question) {

   if (set_qwizard_data_b) {
      if (typeof (i_qwizard_question) == 'undefined') {
         i_qwizard_question = i_question;
      }
      qw.questions_cards[i_qwizard_question] = {};
   }

   // Set to defaults first.
   qwizdata[i_qwiz].dataset_id[i_question] = i_question;
   qwizdata[i_qwiz].unit[i_question] = qwizdata[i_qwiz].default_unit;

   // Find attributes, if any.
   var m = question_shortcode.match (/\[(<code><\/code>)*q\s*([^\]]*)\]/m);
   var attributes = m[2];
   if (attributes) {
      attributes = qqc.replace_smart_quotes (attributes);
      if (set_qwizard_data_b) {
         qw.questions_cards[i_qwizard_question].question_attributes = attributes;
      }

      // See if marked as multiple-choice question in qwizard.js -- may only
      // have one choice -- a question in progress.
      if (q.qwizard_b) {
         qwizdata[i_qwiz].qwizard_multiple_choice_b[i_question] = get_attr (attributes, 'multiple_choice') == 'true';
      }

      // Look for "topic=" or "topics=" attribute.
      var question_topics = get_attr (attributes, 'topic', true);
      if (question_topics) {
         if (debug[4]) {
            console.log ('[process_question_attributes] question_topics: ', question_topics);
         }
         if (set_qwizard_data_b) {
            qw.questions_cards[i_qwizard_question].topic = question_topics;
         }

         // Multiple topics for a question - separated by semicolon (and
         // optional space).  Split into array.
         question_topics = question_topics.split (/; */);

         // Replace whitespace with underscores; add topics to list of topics if not
         // already in list.
         for (var i=0; i<question_topics.length; i++) {
            question_topics[i] = question_topics[i].replace (/\s/g, '_');
            var topic = question_topics[i];
            if (qwizdata[i_qwiz].topics.indexOf (topic) == -1) {
               qwizdata[i_qwiz].topics.push (topic);
            }
         }
         qwizdata[i_qwiz].question_topics[i_question] = question_topics;
      }

      // Look for dataset_id= attribute.  Default: i_question.
      var dataset_id = get_attr (attributes, 'dataset_id');
      if (dataset_id) {
         qwizdata[i_qwiz].dataset_id[i_question] = dataset_id;
      }

      // Look for unit= attribute.
      var unit = get_attr (attributes, 'unit');
      if (unit) {
         qwizdata[i_qwiz].unit[i_question] = unit;
         if (set_qwizard_data_b) {
            qw.questions_cards[i_qwizard_question].unit = unit;
         }

         // Add unit to list of units if not already in list.
         if (qwizdata[i_qwiz].units.indexOf (unit) == -1) {
            qwizdata[i_qwiz].units.push (unit);
         }
      }

      // Is this a specific question from a dataset?
      var use_dataset_question_id = get_attr (attributes, 'use_dataset_question');
      if (use_dataset_question_id) {
         qwizdata[i_qwiz].use_dataset_question_ids[i_question] = use_dataset_question_id;
         qwizdata[i_qwiz].dataset_id[i_question]               = use_dataset_question_id;
         if (set_qwizard_data_b) {
            qw.questions_cards[i_qwizard_question].from_dataset_b = true;
         }
      }

      // Background image?
      var bg_img_src = get_attr (attributes, 'bg_img_src');
      if (bg_img_src) {
         var bg_img = {};
         bg_img.src    = bg_img_src;
         bg_img.left   = get_attr (attributes, 'bg_img_left');
         bg_img.top    = get_attr (attributes, 'bg_img_top');
         bg_img.width  = get_attr (attributes, 'bg_img_width');
         bg_img.height = get_attr (attributes, 'bg_img_height');

         qwizdata[i_qwiz].bg_img[i_question] = bg_img;
         if (set_qwizard_data_b) {
            qw.questions_cards[i_qwizard_question].bg_img = bg_img;
         }
      }
   }

   return question_topics;
}


// -----------------------------------------------------------------------------
function check_questions_have_topics (i_qwiz) {

   // Look for non-information questions -- if such a question does not have any
   // topics, add topic "Other".
   var add_other_b = false;
   for (var i_question=0; i_question<qwizdata[i_qwiz].n_questions; i_question++) {
      if (! qwizdata[i_qwiz].information_question_b[i_question]) {
         if (! qwizdata[i_qwiz].question_topics[i_question]) {
            qwizdata[i_qwiz].question_topics[i_question] = ['Other'];
            add_other_b = true;
         }
      }
   }
   if (add_other_b) {
      if (qwizdata[i_qwiz].topics.indexOf ('Other') == -1) {
         qwizdata[i_qwiz].topics.push ('Other');
      }
   }

   if (debug[4]) {
      console.log ('[check_questions_have_topics] qwizdata[i_qwiz].question_topics:', qwizdata[i_qwiz].question_topics);
   }
   // Set up statistics by topic.  Object of objects (list of lists).
   qwizdata[i_qwiz].topic_statistics = {};
   var n_topics = qwizdata[i_qwiz].topics.length;
   for (var i_topic=0; i_topic<n_topics; i_topic++) {
      var topic = qwizdata[i_qwiz].topics[i_topic];
      qwizdata[i_qwiz].topic_statistics[topic] = {};
      qwizdata[i_qwiz].topic_statistics[topic].n_correct = 0;
      qwizdata[i_qwiz].topic_statistics[topic].n_incorrect = 0;
   }
}


// -----------------------------------------------------------------------------
this.restart_quiz = function (i_qwiz) {

   // Hide summary report and exit-mobile-mode button, if showing.
   var $summary = $ ('#summary-qwiz' + i_qwiz);
   $summary.hide ();
   $summary.find ('button.summary_exit_mobile_qwiz').hide ();

   // Hide "Got it" and "Need more practice" buttons, if any.
   $ ('#qwiz' + i_qwiz + ' div.show_answer_got_it_or_not').hide ();

   // Hide single-question quiz exit text, if any.
   if (qwizdata[i_qwiz].n_questions == 1) {
      $( '#qwiz' + i_qwiz + ' div.single-question_exit').hide ();
   }

   qwizdata[i_qwiz].n_correct = 0;
   qwizdata[i_qwiz].n_incorrect = 0;
   if (qwizdata[i_qwiz].use_dataset) {
      qwizdata[i_qwiz].information_question_b = {};
      qwizdata[i_qwiz].hangman = {};
      qwizdata[i_qwiz].textentry = '';
   }
   q.display_progress (i_qwiz);

   // Reset qwizzled divs to original state (cloned in init_qwizzled2 ()).
   for (var qwizzled_div_id in qwizdata[i_qwiz].$qwizzled) {
      $ ('div#' + qwizzled_div_id).replaceWith (qwizdata[i_qwiz].$qwizzled[qwizzled_div_id]);

      // For reasons beyond me, it's necessary to re-initialize the cloned
      // object.
      qwizdata[i_qwiz].$qwizzled[qwizzled_div_id] = $ ('div#' + qwizzled_div_id).clone (true);
   }
   if (qwizdata[i_qwiz].qwizzled_b) {
      qwizdata[i_qwiz].correct_on_try1 = [];
   }

   // For good measure (especially restart of use_dataset= quiz), make sure all
   // questions hidden.
   $ ('#qwiz' + i_qwiz).find ('div.qwizq').hide ();

   var n_questions = qwizdata[i_qwiz].n_questions;
   for (var i_question=0; i_question<n_questions; i_question++) {
      qwizdata[i_qwiz].answered_correctly[i_question] = '';

      // For back-forward.
      qwizdata[i_qwiz].questions[i_question] = {};
   }

   // Disable back-forward buttons.
   if (! qwizdata[i_qwiz].hide_forward_back_b) {
      $ ('.bbfe-qwiz' + i_qwiz).css ({visibility: 'visible', color: 'lightgray'}).removeClass ('hover');
      $ ('span.question-number-qwiz' + i_qwiz).css ({visibility: 'visible'}).html (1);
   }

   var n_topics = qwizdata[i_qwiz].topics.length;
   for (var i_topic=0; i_topic<n_topics; i_topic++) {
      var topic = qwizdata[i_qwiz].topics[i_topic];
      qwizdata[i_qwiz].topic_statistics[topic].n_correct = 0;
      qwizdata[i_qwiz].topic_statistics[topic].n_incorrect = 0;
   }
   qwizdata[i_qwiz].i_question           = -1;
   qwizdata[i_qwiz].i_user_question      = -1;
   qwizdata[i_qwiz].user_question_number = 0;

   if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {
      qwizdata[i_qwiz].record_start_b = false;
      var data = {qrecord_id_ok: qwizdata[i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
      qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
   }
   q.next_question (i_qwiz, true);
};


// -----------------------------------------------------------------------------
this.redo_question = function (i_qwiz) {

   // Hide single-question quiz exit text, if any.
   if (qwizdata[i_qwiz].n_questions == 1) {
      $( '#qwiz' + i_qwiz + ' div.single-question_exit').hide ();
   }
   qwizdata[i_qwiz].i_question = qwizdata[i_qwiz].i_question - 1;
   qwizdata[i_qwiz].answered_correctly[0] = '';
   qwizdata[i_qwiz].n_correct = 0;

   q.next_question (i_qwiz, true);
}


// -----------------------------------------------------------------------------
this.next_question = function (i_qwiz, no_login_b, simple_go_f) {
   if (debug[0]) {
      console.log ('[next_question] qwizdata[i_qwiz].i_question:', qwizdata[i_qwiz].i_question);
   }

   // If had gone back to previous question, just display next question in list.
   if (qwizdata[i_qwiz].bck_f) {
      q.fwd_question (i_qwiz, false);
      return;
   }
   var i_question = qwizdata[i_qwiz].i_question;

   // If starting quiz (and not going to dataset intro), show back-forward\
   // buttons.  Save first question shown for to-beginning button.
   if (i_question == -1) {
      if (! qwizdata[i_qwiz].use_dataset || ! qwizdata[i_qwiz].dataset_intro_f) {
         $ ('.bbfe-qwiz' + i_qwiz).css ({visibility: 'visible', color: 'lightgray'}).removeClass ('hover');
      }
   }
   var qwiz_id = 'qwiz' + i_qwiz;

   var n_questions = qwizdata[i_qwiz].n_questions;
   if (debug[0]) {
      console.log ('[next_question] i_question: ', i_question, ', n_questions: ', n_questions);
   }

   // If width was explicitly set (to accommodate large labeled diagram), set
   // back to non-default width or unset.  Unset width if mobile view.
   if (document_qwiz_mobile) {
      $ ('#' + qwiz_id).css ('width', '');
   } else {
      if (qwizdata[i_qwiz].width_reset) {
         if ($ ('#xqwiz' + i_qwiz).length) {
            $ ('#' + qwiz_id).css ('width', qwizdata[i_qwiz].initial_width + 'px');
         } else {
            $ ('#' + qwiz_id).css ('width', '');
         }
         qwizdata[i_qwiz].width_reset = false;
      }
   }

   // If was displaying intro and more than one question or use_dataset= quiz --
   // came from "Start quiz" button.  Flag to do switch to mobile.
   var start_quiz_b = false;
   if (i_question == -1 && ! simple_go_f) {
      if (qwizdata[i_qwiz].use_dataset || qwizdata[i_qwiz].use_dataset_questions_htm || n_questions > 1 || q.qwizard_b) {
         if (! q.no_intro_b[i_qwiz] || qwizdata[i_qwiz].use_dataset_questions_htm) {
            start_quiz_b = true;

            // See if already checked login.
            if (! no_login_b && ! q.qwizard_b) {

               // Not already checked.  If quiz may be recorded, and user not
               // logged in, go to login rather than first question (if user
               // hasn't declined).
               if (qwizdata[i_qwiz].qrecord_id) {
                  var user_logged_in_b
                     =    typeof (document_qwiz_user_logged_in_b) != 'undefined'
                                               && document_qwiz_user_logged_in_b
                       && typeof (document_qwiz_username) != 'undefined';
                  if (   user_logged_in_b
                      || (   typeof (document_qwiz_declined_login_b) != 'undefined'
                          && document_qwiz_declined_login_b)) {
                     if (user_logged_in_b) {

                        // If cookie for current login not there -- expired
                        // (default) 40 minutes after last login -- confirm
                        // continue.
                        var check_team_b = true;
                        if (! $.cookie ('qwiz_current_login_lt_nmin_ago')) {
                           check_team_b = false;
                           var a_team = '';
                           if (document_qwiz_team_b) {
                              a_team = ' ' + T ('a team') + ':';
                           }
                           if (confirm (T ('You are logged in as') + a_team + ' ' + document_qwiz_username + '.\n' + T ('Do you want to continue?  (Click "Cancel" to sign out)'))) {

                              // Continue.  Update cookie.
                              var login_timeout_min = qqc.get_qwiz_param ('login_timeout_min', 40);
                              var options = {path:    '/',
                                             expires: login_timeout_min/(24.0*60.0)};
                              $.cookie ('qwiz_current_login_lt_nmin_ago', 1, options);
                           } else {
                              qqc.sign_out ();
                              document_qwiz_user_logged_in_b = false;
                           }
                        }

                        // If logged in as team, check if want to continue as
                        // team.
                        if (check_team_b && document_qwiz_team_b) {

                           if (! confirm (T ('You are logged in as team') + ': ' + document_qwiz_username + '.\n' + T ('Do you want to continue as this team?'))) {

                              // No.  Reset document global flags and user menu.
                              document_qwiz_session_id = document_qwiz_session_id.split (';')[0];
                              document_qwiz_username   = document_qwiz_username.split ('; ')[0];
                              document_qwiz_team_b     = false;
                              qqc.set_user_menus_and_icons ();
                              var msg = T ('OK.  Only %s is logged in now');
                              msg = msg.replace ('%s', document_qwiz_username);
                              alert (msg);
                           }
                        }
                        qwizdata[i_qwiz].record_start_b = false;
                        if (document_qwiz_user_logged_in_b) {
                           var data = {qrecord_id_ok: qwizdata[i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
                           qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
                        }
                     }
                  } else {
                     q.display_login (i_qwiz);
                     return false;
                  }
               }
            }

            // If use_dataset= quiz, show units/topics options screen.  If
            // dataset_intro="false", go straight to loading dataset questions.
            if (qwizdata[i_qwiz].use_dataset || qwizdata[i_qwiz].use_dataset_questions_htm) {
               if (qwizdata[i_qwiz].dataset_intro_f) {
                  q.display_login (i_qwiz, false, 'use_dataset_options');
               } else {

                  // Hide intro -- if any.
                  $ ('.intro-qwiz' + i_qwiz).hide ();
                  qqc.get_dataset_questions (qwizdata[i_qwiz].use_dataset, qname,
                                             i_qwiz, qwizdata[i_qwiz].qrecord_id,
                                             [], [], 10000,
                                             qwizdata[i_qwiz].dataset_questions_to_do,
                                             qwizdata[i_qwiz].random_b,
                                             qwizdata[i_qwiz].use_dataset_questions_htm);
               }
            }
         }
         q.next_question_from_intro (i_qwiz);

      } else {

         // Don't show mode.
         $ ('#mode-' + qwiz_id).css ('visibility', 'hidden');

         // If qwizard, need to hide intro if there.
         /* DKTMP
         if (q.qwizard_b && ! q.no_intro_b[i_qwiz]) {
            $ ('#intro-' + qwiz_id).hide ();
         }
         */
      }

   } else {

      // Hide previous question.
      var qwizq_id = qwiz_id + '-q' + i_question;
      $ ('#' + qwizq_id).hide ();
      if (document_qwiz_mobile) {
         $ ('#mobile_' + qwizq_id).hide ();
      }
      if (qwizdata[i_qwiz].pay_quiz_deck_id
            && (   qwizdata[i_qwiz].pay_quiz_ok == 'preview_questions'
                || qwizdata[i_qwiz].pay_quiz_ok == 'preview_period_expired'
                || qwizdata[i_qwiz].pay_quiz_ok == 'no_free_trial')) {
         if (qqc.preview_limit ('qwiz', qwizdata, i_qwiz)) {
            return;
         }
      }
   }

   // If qwizard, initially (no questions), nothing more.
   if (n_questions == 0) {
      if (debug[0]) {
         console.log ('[next_question] n_questions:', n_questions);
      }
      return;
   }

   // Hide "next" button until user makes a choice.
   // question"
   if (! next_button_active_b) {
      $ ('#next_button-' + qwiz_id).hide ();

      // Record state.
      qwizdata[i_qwiz].next_button_show_b = false;
   }

   if (i_question != -1 || simple_go_f) {

      // If had been displaying "information question," set as "correct" so
      // don't return to it.  Reset text of next-button.
      if (qwizdata[i_qwiz].information_question_b[i_question]) {
         $ ('#next_button_text-qwiz' + i_qwiz).html (T ('Next question'));
         if (! q.qwizard_b) {
            qwizdata[i_qwiz].answered_correctly[i_question] = true;
            qwizdata[i_qwiz].n_correct++;
            q.display_progress (i_qwiz);

            // If no separate intro page, and this is first question, hide qwiz
            // icon.
            if (i_question == 0 && (q.no_intro_b[i_qwiz]
                                        || qwizdata[i_qwiz].n_questions == 1)) {
               $ ('div#icon_qwiz' + i_qwiz).hide ();
            }

            // If recording, jjax call.
            if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {
               var data = {q_and_a_text:  btoa (encodeURIComponent (qwizdata[i_qwiz].q_and_a_text[i_question])),
                           q_and_a_crc32: qwizdata[i_qwiz].q_and_a_crc32[i_question],
                           i_question:    qwizdata[i_qwiz].dataset_id[i_question],
                           unit:          qwizdata[i_qwiz].unit[i_question],
                           type:          'information_only',
                           response:      'continue',
                           correct_b:     1,
                           confirm:       'js'};
               qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
            }
         }
      }
   }

   if (! (qwizdata[i_qwiz].use_dataset && i_question == -1)) {

      // Next question -- if repeating incorrect, keep running through
      // questions until all answered correctly.  If done, show summary/exit
      // text.
      var n_done = qwizdata[i_qwiz].n_correct;
      if (! qwizdata[i_qwiz].repeat_incorrect_b) {
         n_done += qwizdata[i_qwiz].n_incorrect;
      }
      if (debug[0]) {
         console.log ('[next_question] n_done:', n_done, ', n_questions:', n_questions);
      }
      if (n_done == n_questions) {

         // Set extra "question" for sake of bck_question ();
         var i_user_question = qwizdata[i_qwiz].i_user_question;
         qwizdata[i_qwiz].questions[n_questions] = {i_user_prev_question: i_user_question, user_question_number: '--'};
         qwizdata[i_qwiz].questions[i_user_question].i_user_next_question = n_questions;
         qwizdata[i_qwiz].i_user_question  = n_questions;
         qwizdata[i_qwiz].saved_i_question = n_questions;
         $ ('span.question-number-qwiz' + i_qwiz).html ('--');

         q.display_summary_and_exit (i_qwiz);
      } else {

         // If random order, start at random place to look for next not-yet-
         // answered-correctly question.
         if (! q.qwizard_b && qwizdata[i_qwiz].random_b) {
            i_question = Math.floor (Math.random () * n_questions);
         }
         while (true) {
            i_question++;
            if (i_question >= n_questions) {
               i_question = 0;
            }
            if (! qwizdata[i_qwiz].answered_correctly[i_question]) {
               break;
            }
         }
         var i_prev_question = qwizdata[i_qwiz].i_user_question;
         qwizdata[i_qwiz].i_question = i_question;

         // Previous and current question.
         if (typeof (qwizdata[i_qwiz].questions[i_question]) == 'undefined') {
            qwizdata[i_qwiz].questions[i_question] = {};
         }
         var question = qwizdata[i_qwiz].questions[i_question];
         question.i_user_prev_question = i_prev_question;
         if (i_prev_question == -1) {

            // In case came from dataset intro or intro, special.
            qwizdata[i_qwiz].i_first_user_question = i_question;
            if (debug[0]) {
               console.log ('[next_question] i_first_user_question:', i_question);
            }
         } else {
            var prev_question = qwizdata[i_qwiz].questions[i_prev_question];
            prev_question.i_user_next_question = i_question;

            // Enable to-beginning and back "buttons."
            if (! q.qwizard_b) {
               $ ('.bck-question-qwiz' + i_qwiz).css ({color: 'gray'}).addClass ('hover');
            }
         }
         qwizdata[i_qwiz].i_user_question = i_question;
         if (typeof question.user_question_number == 'undefined') {
            qwizdata[i_qwiz].user_question_number++;
            question.user_question_number = qwizdata[i_qwiz].user_question_number;
         }
         if (! q.qwizard_b) {
            $ ('span.question-number-qwiz' + i_qwiz).html (question.user_question_number);
         }

         q.display_question (i_qwiz, i_question, start_quiz_b);

         // If qwizard, use go_to_question2 () to hide any previous menus, show
         // relevant menus for this question, highlight in accordion panel
         // question list.  Update progress now.  Don't do yet if processing
         // specific questions from various datasets.
         if (q.qwizard_b && n_questions) {
            qwizard.set_qwizard_data ('i_question', i_question);
            qwizard.go_to_question2 ();
            q.display_progress (i_qwiz);
         }
      }
   }
};


// -----------------------------------------------------------------------------
this.next_question_from_intro = function (i_qwiz) {

   // If no questions (qwizard, initially), do nothing.
   if (! qwizdata[i_qwiz].n_questions) {
      return;
   }

   // Hide intro (intro -- if any -- shows with the question of a single-
   // question quiz).
   $ ('.intro-qwiz' + i_qwiz).hide ();

   // If intro was showing, can hide qwiz icon now.
   if (! q.no_intro_b[i_qwiz]) {
      $ ('#icon_qwiz' + i_qwiz).hide ();
   }

   // Show back-forward buttons, set question number.
   $ ('.bbfe-qwiz' + i_qwiz).css ({visibility: 'visible', color: 'lightgray'}).removeClass ('hover');
   qwizdata[i_qwiz].i_user_question       = -1;

   $ ('span.question-number-qwiz' + i_qwiz).html (1);

   // After "Start quiz", button is left-aligned.
   $ ('#next_button-qwiz' + i_qwiz).css ('text-align', 'left');

   // Also, show progress and change button text.  Only if more than one
   // question in quiz.  For use_dataset= quizzes, need to wait for
   // questions to load (display_progress () called from
   // qqc.receive_and_place_dataset_questions ()).
   if (! (qwizdata[i_qwiz].use_dataset || qwizdata[i_qwiz].use_dataset_questions_htm)) {
      q.display_progress (i_qwiz, true);
   }
   $ ('#next_button_text-qwiz' + i_qwiz).html (T ('Next question'));
}


// -----------------------------------------------------------------------------
this.bck_question = function (i_qwiz, go_to_beg_f) {

   // If "disabled" don't do anything.
   if (! $ ('.bck-question-qwiz' + i_qwiz).hasClass ('hover')) {
      return;
   }

   // Signal that we've gone back.
   qwizdata[i_qwiz].bck_f = true;

   // Current user question.  Save in order to hide (below).
   var i_user_question = qwizdata[i_qwiz].i_user_question;
   var i_current_user_question = i_user_question;

   if (go_to_beg_f) {

      // If dataset with intro page, go there.  Save "current question".
      // Disable back buttons, enable forward,  "--" for question number.
      if (qwizdata[i_qwiz].use_dataset && qwizdata[i_qwiz].dataset_intro_f) {
         qwizdata[i_qwiz].saved_i_question = qwizdata[i_qwiz].i_question;
         $ ('.bck-question-qwiz' + i_qwiz).css ({color: 'lightgray'}).removeClass ('hover');
         $ ('.fwd-question-qwiz' + i_qwiz).css ({color: 'gray'}).addClass ('hover');
         $ ('span.question-number-qwiz' + i_qwiz).html ('--');
         qwizdata[i_qwiz].i_question = -1;
         q.display_login (i_qwiz, false, 'use_dataset_options');
         return;
      } else {

         // Go to first question.
         i_user_question = qwizdata[i_qwiz].i_first_user_question;
      }
   } else {

      // Go back.  Catch beginning of quiz in case control not correctly
      // disabled.
      i_user_question = qwizdata[i_qwiz].questions[i_user_question].i_user_prev_question;
      if (i_user_question == -1) {
         return;
      }
   }

   // Hide current.
   var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_current_user_question;
   $ ('#' + qwizq_id).hide ();
   if (document_qwiz_mobile) {
      $ ('#mobile_' + qwizq_id).hide ();
   }

   // In case was showing summary (if summary="false" option, shows "Take this
   // quiz again" on last question), hide it.
   $ ('div#summary-qwiz' + i_qwiz).hide ();


   qwizdata[i_qwiz].i_user_question = i_user_question;
   var question = qwizdata[i_qwiz].questions[i_user_question];

   // If no more available after this, "disable" to-beginning and back buttons.
   // Keep to-beginning enabled if dataset with intro.
   if (go_to_beg_f || question.i_user_prev_question == -1) {
      var $bck = $ ('.bck-question-qwiz' + i_qwiz);
      if (qwizdata[i_qwiz].use_dataset && qwizdata[i_qwiz].dataset_intro_f) {
         $bck = $bck.last ();
      }
      $bck.css ({color: 'lightgray'}).removeClass ('hover');
   }

   // Question number.
   var user_question_number = question.user_question_number;
   $ ('span.question-number-qwiz' + i_qwiz).html (user_question_number);

   // "Enable" forward and to-end buttons.
   $ ('.fwd-question-qwiz' + i_qwiz).css ({color: 'gray'}).addClass ('hover');

   // Show this question.
   qwizq_id = 'qwiz' + i_qwiz + '-q' + i_user_question;
   $ ('#' + qwizq_id).show ();
   if (document_qwiz_mobile) {
      $ ('#mobile_' + qwizq_id).show ();
   }
}


// -----------------------------------------------------------------------------
this.fwd_question = function (i_qwiz, go_to_end_f) {

   // If "disabled" don't do anything.
   if (! $ ('.fwd-question-qwiz' + i_qwiz).hasClass ('hover')) {
      return;
   }

   // Current question or dataset intro -- hide it.
   if (qwizdata[i_qwiz].i_question == -1) {
      $ ('#qwiz_login-qwiz' + i_qwiz).hide ();
   } else {
      var i_user_question = qwizdata[i_qwiz].i_user_question;
      var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_user_question;
      $ ('#' + qwizq_id).hide ();
      if (document_qwiz_mobile) {
         $ ('#mobile_' + qwizq_id).hide ();
      }
   }

   if (go_to_end_f) {

      // If on dataset intro, go to saved "current question".
      if (qwizdata[i_qwiz].i_question == -1) {
         qwizdata[i_qwiz].i_question = qwizdata[i_qwiz].saved_i_question;
      }
      i_user_question = qwizdata[i_qwiz].i_question;
   } else {

      // If on dataset intro, go to saved "next question."  Reset "current
      // question" to saved value.
      if (qwizdata[i_qwiz].i_question == -1) {
         i_user_question = qwizdata[i_qwiz].i_first_user_question;
         qwizdata[i_qwiz].i_question = qwizdata[i_qwiz].saved_i_question;
      } else {

         // Go forward.
         i_user_question = qwizdata[i_qwiz].questions[i_user_question].i_user_next_question;
      }
   }
   qwizdata[i_qwiz].i_user_question = i_user_question;
   var question = qwizdata[i_qwiz].questions[i_user_question];

   // See if we're back to current question
   if (i_user_question == qwizdata[i_qwiz].i_question) {

      // Yes, current question.  Unset flag.  "Disable" forward and to-end
      // buttons.
      qwizdata[i_qwiz].bck_f = false;
      $ ('.fwd-question-qwiz' + i_qwiz).css ({color: 'lightgray'}).removeClass ('hover');
   }

   // Question number.
   var user_question_number = question.user_question_number;
   $ ('span.question-number-qwiz' + i_qwiz).html (user_question_number);

   // Show this question, or summary.
   if (i_user_question == qwizdata[i_qwiz].n_questions) {
      $ ('div#summary-qwiz' + i_qwiz).show ();
   } else {
      qwizq_id = 'qwiz' + i_qwiz + '-q' + i_user_question;
      $ ('#' + qwizq_id).show ();
      if (document_qwiz_mobile) {
         $ ('#mobile_' + qwizq_id).show ();
      }

      // If summary="false" option, need to show summary on final question.
      if (! qwizdata[i_qwiz].summary_b) {
         if (user_question_number == qwizdata[i_qwiz].n_questions) {
            $ ('div#summary-qwiz' + i_qwiz).show ();
         }
      }
   }

   // "Enable" to-beginning and back buttons.  In case coming from dataset
   // intro, see if back button should be enabled.
   var $bck = $ ('.bck-question-qwiz' + i_qwiz);
   if (question.i_user_prev_question == -1) {
      $bck = $bck.first ();
   }
   $bck.css ({color: 'gray'}).addClass ('hover');
}


// -----------------------------------------------------------------------------
this.display_question = function (i_qwiz, i_question, start_quiz_b) {
   var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_question;

   // Hide feedback in case previously displayed.  jQuery operator "^=" is for
   // "startswith."
   var $qwizq = $ ('div#' + qwizq_id);
   if (debug[0]) {
      console.log ('[display_question] start_quiz_b:', start_quiz_b);
      console.log ('[display_question] $qwizq:', $qwizq);
   }
   $qwizq.find ('[id^=' + qwizq_id + '-a]').hide ();

   // And for mobile version, if exists.
   var $mobile_qwizq = $ ('div#mobile_' + qwizq_id);
   $mobile_qwizq.find ('[id^=mobile_' + qwizq_id + '-a]').hide ();

   // Do regular and mobile versions have any <br name="qwiz_img" ... /> tags?
   // Set back to <img... /> tags.
   var $qwiz_img = $qwizq.find ('input[name="qwiz_img"]');
   if ($qwiz_img.length) {
      $qwiz_img.changeElements ('img');
      $mobile_qwizq.find ('input[name="qwiz_img"]').changeElements ('img');
   }


   // See if labeled diagram.
   var qwizzled_b = $qwizq.hasClass ('qwizzled');
   if (qwizzled_b) {

      // If labeled diagram not previously initialized, do initializations:
      // targets no longer draggable, size image wrappers, labels, targets
      // clickable (as an alternative to dragging).
      if (! qwizdata[i_qwiz].$qwizzled) {
         qwizdata[i_qwiz].$qwizzled = {};
      }
      if (typeof (qwizdata[i_qwiz].$qwizzled[qwizq_id]) == 'undefined') {
         if (q.qwizard_b) {
            q.init_qwizzled2 ($qwizq, i_qwiz, i_question);
         } else {
            q.init_qwizzled ($qwizq, i_qwiz, i_question);
         }
      }

      // Reset progress bar counts.
      qwizdata[i_qwiz].n_labels_correct = 0;
      qwizdata[i_qwiz].n_label_attempts = 0;

      // If previously-answered incorrectly, restore state.
      if (qwizdata[i_qwiz].answered_correctly[i_question] === false) {
         $qwizq.replaceWith (qwizdata[i_qwiz].$qwizzled[qwizq_id]);

         // replaceWith () clobbers; need to reset.
         var $qwizq = $ ('div#' + qwizq_id);

         // As in restart_quiz (), re-initialize the cloned object.
         qwizdata[i_qwiz].$qwizzled[qwizq_id] = $ ('div#' + qwizq_id).clone (true);

         // Also, put into place labels that were previously placed correctly.
         // These delays (to make sure any asynchronous preliminaries get done
         // first) may not be necessary, but they won't hurt.
         var delay_init_drag_and_drop = function () {
            if (debug[8]) {
               console.log ('[display_question > delay_init_drag_and_drop] i_qwiz:', i_qwiz, ', i_question:', i_question);
            }
            q.init_drag_and_drop ($qwizq[0]);
         };
         // DKTMP DEDRAG.  Don't do in qwizard.
         if (! q.qwizard_b) {
            setTimeout (delay_init_drag_and_drop, 100);
         }

         var delay_place_labels = function () {
            place_labels (i_qwiz, i_question, qwizq_id);
         };
         setTimeout (delay_place_labels, 200);
      }

      // This collects multiple spans (text targets) if they're spread across
      // a text target.  If don't have qtarget_sib... just count, but de-dup
      // sibs.
      var n_label_targets = 0;
      var target_count = {};
      $qwizq.find ('span.qwizzled_target').not ('.decoy').each (function () {
         var classes = $ (this).attr ('class');
         var m = classes.match (/qtarget_sib-[0-9]*/);
         if (m) {
            var qwizzled_target_assoc_id = m[0];
            target_count[qwizzled_target_assoc_id] = 1;
         } else {

            // Backwards compatibility.
            m = classes.match (/qwizzled_target-[0-9]*/);
            if (m) {
               var qwizzled_target_assoc_id = m[0];
               target_count[qwizzled_target_assoc_id] = 1;
            } else {
               n_label_targets++;
            }
         }
      });

      // Regular targets.  No de-dup necessary.
      n_label_targets += $qwizq.find ('div.qwizzled_target').not ('.decoy').length;

      qwizdata[i_qwiz].n_label_targets = n_label_targets + Object.keys (target_count).length;
      display_qwizzled_progress (i_qwiz);
   }

   // If starting quiz and on small screen, switch to full-screen view for
   // quiz container, and mobile-responsive version of question/choices.
   if (start_quiz_b && qqc.is_mobile ()) {
      q.go_mobile (i_qwiz);
   } else if (document_qwiz_mobile) {

      // Mobile version of question if there is one.
      var $mobile_qwizq = $ ('#mobile_qwiz' + i_qwiz + '-q' + i_question);
      if ($mobile_qwizq.length) {
         $mobile_qwizq.show ();
      } else {
         $qwizq.show ();
      }

      // Scroll to top in any case.
      window.scrollTo (0, 1);
   } else {

      // Display regular version of question.
      if (debug[0]) {
         console.log ('[display_question] $qwizq:', $qwizq);
      }
      // For some reason show () didn't do the job on qwizzled question in
      // Chrome.
      $qwizq.css ('display', 'block');

      // If qwizard, and editor hasn't been initiated for editable fields in
      // this question, do so -- but not for use-dataset questions.
      if (q.qwizard_b) {
         var init_b = false;
         if (qwizdata[i_qwiz].use_dataset_question_ids[i_question]) {
            var $qwiz_editable = $qwizq.find ('.qwiz_editable');
            if ($qwiz_editable.length) {
               $qwiz_editable.removeClass ('qwiz_editable');
               init_b = true;
            }
         } else if ($qwizq.find ('div[contenteditable]').length == 0) {
            var hangman_labeled_diagram_f = $qwizq.find ('div.hangman_image').length;
            qwizard.init_tinymce ('div#' + qwizq_id + ' .qwiz_editable', false,
                                  hangman_labeled_diagram_f);
            init_b = true;
         }
         if (init_b) {

            // If "show-me-the-answer" question, reset button text -- tinyMCE
            // initialization seems to clobber.  Give init_tinymce () a chance
            // to complete.
            var reset_i_question = i_question;
            if (debug[0]) {
               console.log ('[display_question] reset_i_question:', reset_i_question);
            }
            var delay_reset = function () {
               qwizard.reset_show_me_button_text (reset_i_question);

               // Also, if textentry or hangman question, reset parts editable
               // fields (TinyMCE seems to mangle sometimes).
               if (typeof qwizdata[i_qwiz].parts_htm[reset_i_question] != 'undefined') {
                  var parts = qwizdata[i_qwiz].parts_htm[reset_i_question];
                  var n_parts = parts.length;
                  for (var i_part=1; i_part <= n_parts; i_part++) {
                     var part_htm = parts[i_part];
                     $ ('#qwiz' + i_qwiz + '-q' + reset_i_question + ' div.qwiz-part' + i_part).html (part_htm);
                     if (debug[12]) {
                        console.log ('part_htm:', part_htm);
                     }
                  }
               }

               // And, with reset, reinitialize autocomplete.
               q.init_textentry_autocomplete ($qwizq);
            }
            setTimeout (delay_reset, 300);

         }
      }
   }

   if (qwizzled_b) {

      // If layout table is wider than default qwiz width (defines border),
      // set wider for now.  Get width of table.  Add 10px for border and
      // padding of qwiz div.
      var table_width = 10 + $qwizq.find ('table.qwizzled_table').outerWidth ();
      if (debug[0]) {
         console.log ('[display_question] table_width:', table_width, ', initial_width:', qwizdata[i_qwiz].initial_width);
      }

      // Don't do if mobile view.
      if (! document_qwiz_mobile
                              && table_width > qwizdata[i_qwiz].initial_width) {
         $ ('#qwiz' + i_qwiz).css ('width', table_width + 'px');

         // Set flag to reset width on next question.
         qwizdata[i_qwiz].width_reset = true;
      }
      if (q.qwizard_b) {

         // Set up label tooltips and disable browser context menu on labels.
         var $labels = $qwizq.find ('.qwizzled_highlight_label');
         /* DKTMP DEDRAG
         qwizard.create_label_tooltips ($labels);
         qwizard.disable_browser_context_menu ($labels);
         */
         $labels.addClass ('no_move');
      }
   } else {

      // See if this is a [textentry] question that uses terms.
      if (qwizdata[i_qwiz].textentry && qwizdata[i_qwiz].textentry[i_question]) {
         var $textentry = $ ('#textentry-qwiz' + i_qwiz + '-q' + i_question);
         if (! qwizdata[i_qwiz].textentry[i_question].textentry_suggest_b) {

            // No-suggestions textentry question.  Enable check-answer button
            // from the get-go.
            var $check_answer = $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question);
            $check_answer.find ('button.textentry_check_answer').removeClass ('qwiz_button_disabled');
            qwizdata[i_qwiz].check_answer_disabled_b = false;
            $check_answer.css ({display: 'inline-block'});
         } else if (! qwizdata[i_qwiz].textentry[i_question].single_char_b) {

            // .................................................................
            // [textentry] question.
            // Use terms given with [terms]...[/terms] for this quiz; otherwise
            // load default terms if haven't done so already.
            if (qwizdata[i_qwiz].terms) {

               // Only do this once per quiz.
               if (! qwizdata[i_qwiz].textentry_terms_metaphones) {
                  qwizdata[i_qwiz].textentry_terms_metaphones = qqc.process_textentry_terms (qwizdata[i_qwiz].terms);
               }
            } else {
               if (! default_textentry_terms_metaphones) {
                  var plugin_url = qqc.get_qwiz_param ('url', './');
                  var terms_data = '';
                  if (content == 'body' && plugin_url == './') {
                     if (typeof (document_qwiz_terms) != 'undefined') {
                        terms_data = document_qwiz_terms;
                     }
                  } else {
                     terms_data = qqc.get_textentry_terms (plugin_url + 'terms.txt', qwizdata);
                  }
                  default_textentry_terms_metaphones = qqc.process_textentry_terms (terms_data);
               }
            }

            // Also need to process additional terms for this quiz, if any.
            // Only do once per quiz.
            if (qwizdata[i_qwiz].add_terms) {
               if (! qwizdata[i_qwiz].add_textentry_terms_metaphones) {
                  qwizdata[i_qwiz].add_textentry_terms_metaphones = qqc.process_textentry_terms (qwizdata[i_qwiz].add_terms);
               }
            }


            qwizdata[i_qwiz].check_answer_disabled_b = true;
            qwizdata[i_qwiz].textentry_n_hints = 0;

            // Calculate metaphones of answers -- both correct and incorrect  --
            // up to first blank following a non-blank.
            textentry_answers[i_qwiz] = qwizdata[i_qwiz].textentry[i_question].answers;
            textentry_answer_metaphones[i_qwiz]
               = textentry_answers[i_qwiz].map (function (answer) {
                                                   answer = answer.replace (/\s*(\S+)\s.*/, '\$1');
                                                   return qqc.metaphone (answer);
                                                });

            if (qwizdata[i_qwiz].textentry[i_question].use_terms_b) {

               // List of terms (term, metaphone pairs) for this question: (1) default
               // or specific to this qwiz; plus (2) additional terms for this quiz,
               // if any; and (3) answers (correct and incorrect) for this question.
               // Singular or plural in each case.
               var singular_plural;
               if (qwizdata[i_qwiz].textentry[i_question].textentry_plural_b) {
                  singular_plural = 'plural';
               } else {
                  singular_plural = 'singular';
               }

               // (1) Quiz-specific or default.
               if (qwizdata[i_qwiz].terms) {
                  current_question_textentry_terms_metaphones[i_qwiz]
                     = qwizdata[i_qwiz].textentry_terms_metaphones[singular_plural];
               } else {
                  current_question_textentry_terms_metaphones[i_qwiz]
                     = default_textentry_terms_metaphones[singular_plural];
               }

               // (2) Additional.
               if (qwizdata[i_qwiz].add_terms) {
                  current_question_textentry_terms_metaphones[i_qwiz]
                     = current_question_textentry_terms_metaphones[i_qwiz]
                          .concat (qwizdata[i_qwiz].add_textentry_terms_metaphones[singular_plural]);
               }
            } else {
               current_question_textentry_terms_metaphones[i_qwiz] = [];
            }

            // (3) Answers.
            var textentry_answers_metaphones
               = textentry_answers[i_qwiz].map (function (answer) {
                                           return [answer, qqc.metaphone (answer)];
                                        });
            if (debug[6]) {
               console.log ('[display_question] textentry_answers_metaphones: ', textentry_answers_metaphones);
            }
            current_question_textentry_terms_metaphones[i_qwiz]
                  = current_question_textentry_terms_metaphones[i_qwiz]
                                         .concat (textentry_answers_metaphones);

            // Sort and de-dupe.
            current_question_textentry_terms_metaphones[i_qwiz]
               = qqc.sort_dedupe_terms_metaphones (current_question_textentry_terms_metaphones[i_qwiz]);

            if (debug[6]) {
               console.log ('[display_question] current_question_textentry_terms_metaphones[i_qwiz].length: ', current_question_textentry_terms_metaphones[i_qwiz].length);
               console.log ('[display_question] current_question_textentry_terms_metaphones[i_qwiz].slice (0, 10): ', current_question_textentry_terms_metaphones[i_qwiz].slice (0, 10));
               var i_start = current_question_textentry_terms_metaphones[i_qwiz].length - 10;
               if (i_start > 0) {
                  console.log ('[display_question] current_question_textentry_terms_metaphones[i_qwiz].slice (' + i_start + '): ', current_question_textentry_terms_metaphones[i_qwiz].slice (i_start));
               }
            }

            // Set minlength for autocomplete suggestions for this question.
            var question = qwizdata[i_qwiz].textentry[i_question];
            var minlength = question.textentry_minlength;
            var correct_answer_length = question.first_correct_answer.length;
            if (correct_answer_length < minlength) {
               minlength = correct_answer_length;
            }

            // If not yet initialized do so.
            if (! $textentry.autocomplete ('instance')) {
               q.init_textentry_autocomplete ($qwizq);
            }
            $textentry.autocomplete ('option', 'minLength', minlength);

            // Set placeholder now.  Also reset "Check answer" button.
            var placeholder;
            var check_answer;
            if (minlength <= 1) {
               placeholder = T ('Type a letter/number');
               check_answer = T ('Type a letter');
            } else {
               minlength = Math.max (minlength, 3);
               placeholder = T ('Type %s+ letters/numbers, then select');
               placeholder = placeholder.replace ('%s', minlength);

               check_answer = T ('Type %s+ letters');
               check_answer = check_answer.replace ('%s', minlength);
            }
            $textentry.attr ('placeholder', placeholder);
            $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question + ' button.textentry_check_answer').html (check_answer);

            // Save.
            qwizdata[i_qwiz].check_answer = check_answer;

            // Needed in find_matching_terms ().
            question.textentry_minlength = minlength;

            // Show "Check answer" and "Hint" buttons.  "Check answer" starts
            // out gray (but not actually disabled, so click provides alert
            // message).
            var $check_answer = $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question);
            $check_answer.find ('button.textentry_check_answer').addClass ('qwiz_button_disabled');
            qwizdata[i_qwiz].check_answer_disabled_b = true;
            $check_answer.css ({display: 'inline-block'});

            // Hint starts out not visible.  If first question of no-intro quiz,
            // set up for mouseenter to start timeout.
            if (i_question == 0 && (q.no_intro_b[i_qwiz]
                                           || qwizdata[i_qwiz].n_questions == 1)) {
               $ ('div#qwiz' + i_qwiz).attr ('onmouseenter', qname + '.start_hint_timeout (' + i_qwiz + ')');
            } else {

               // Otherwise, start timeout now (with question display).
               q.start_hint_timeout (i_qwiz);
            }
         }

         // Reset value of textentry box, if there is one, and also reenable in
         // case still disabled (after quiz restart).
         if ($textentry.length) {
            $textentry.val ('').removeAttr ('disabled');

            // Set focus to textentry box.  Don't do if first question and no
            // intro (avoid scrolling page to this quiz).  Focus can trigger
            // scroll > panel open in mobile view, so set flag not to open
            // panel.
            if ((qwizdata[i_qwiz].n_correct + qwizdata[i_qwiz].n_incorrect) != 0
                                                    || ! q.no_intro_b[i_qwiz]) {
               panel_exit_mobile_just_closed_b = true;
               if (! q.preview) {
                  $textentry.focus ();
               }
            }
         }
      } else {

         if (qwizdata[i_qwiz].information_question_b[i_question]) {

            // "Information-only question."  Reset next-button to "Continue".
            // Show button, record state, set as "correct" so don't return to it.
            $ ('#next_button_text-qwiz' + i_qwiz).html (T ('Continue'));
            q.position_show_next_button (i_qwiz);
            qwizdata[i_qwiz].next_button_show_b = true;

         } else if (typeof qwizdata[i_qwiz].hangman[i_question] != 'undefined') {
            var hangman               = qwizdata[i_qwiz].hangman[i_question];
            var n_hangman             = hangman.n_hangman;
            hangman.n_hangman_done    = 0;
            hangman.n_hangman_correct = 0;
            var first_f               = true;
            var hangman_final_entry;
            for (var i_choice=0; i_choice<n_hangman; i_choice++) {
               $hangman = $qwizq.find ('span.qwiz_hangman.qwiz_hangman_c' + i_choice);
               hangman_final_entry = hangman.hangman_final_entry[i_choice]

               // If previously answered incorrectly overall, and multiple
               // hangman fields, fill in any that were answered correctly
               // (don't make user re-do).
               if (qwizdata[i_qwiz].answered_correctly[i_question] === false) {
                  if (n_hangman > 1
                        && hangman.hangman_incorrect_chars[i_choice].length <= 3
                        && hangman.hangman_n_hints[i_choice] == 0) {
                     $hangman.find ('span.hangman_current_entry').html (hangman_final_entry);
                     hangman.n_hangman_done++;
                     hangman.n_hangman_correct++;
                     continue;
                  }
               }

               // Initial entry - just underscores for each letter.
               var hangman_current_entry = hangman_final_entry.replace (/>[a-z0-9]</gi, '>&ensp;<');
               hangman.hangman_current_entry[i_choice]
                          = hangman_current_entry.replace (/u>&ensp;</g, 'u>\t<');
               $hangman.find ('span.hangman_current_entry').html (hangman_current_entry);

               // Enable input in case previously disabled.  Reset incorrect
               // characters, number of hints, enable hint button.
               $hangman.find ('input').removeAttr ('disabled');
               hangman.hangman_incorrect_chars[i_choice] = '';
               hangman.hangman_incorrect_chars_before_hint[i_choice] = 3;
               hangman.hangman_n_hints[i_choice] = 0;
               $ ('#hangman_hint-qwiz' + i_qwiz + '-q' + i_question + '-c' + i_choice)
                  .removeAttr ('disabled')
                  .removeClass ('qwiz_button_disabled')
               $hangman[0].done_f = false;

               // Reset hangman status, hide message in case was shown.
               $hangman.find ('span.hangman_status').html ('');
               var msg;
               var hangman_answer = hangman.hangman_answer[i_choice];
               if (hangman_answer.search (/[a-z]/i) != -1) {
                  msg = T ('Type letters in the box');
               } else {
                  msg = T ('Type numbers in the box');
               }
               $hangman.find ('span.hangman_type_letters').html ('<span class="type_letters">' + msg + '</span>').show ();

               $qwizq.find ('div.qwiz_hangman_msg').hide ();

               // Set focus to first active hangman input box.  Don't do if
               // first question and no intro (avoid scrolling page to this
               // quiz).  Focus can trigger scroll > panel open in mobile view,
               // so set flag not to open panel.  In mobile view (at least in
               // Android default browser), highlights input box and doesn't
               // respond to first letter, so deselect.
               if ((   i_question != 0 || ! q.no_intro_b[i_qwiz]
                    || qwizdata[i_qwiz].answered_correctly[i_question] === false)
                                                                   && first_f) {
                  first_f = false;
                  panel_exit_mobile_just_closed_b = true;

                  // Don't let Hint button show in response to focus event.
                  var $hangman_input = $qwizq.find ('span.qwiz_hangman.qwiz_hangman_c' + i_choice + ' input');
                  suppress_hangman_hint_b = true;
                  $hangman_input[0].focus ();
               }
            }
         } else {

            // ....................................................................
            // Multiple-choice question.
            // Enable radio clicks in case previously disabled for this question.
            // Also, show radios unclicked.
            $ ('input[name=' + qwizq_id + ']').removeAttr ('disabled').prop ('checked', false);

            // For mobile version, make sure all choices showing.
            $ ('#mobile_' + qwizq_id + ' li.mobile_choice').show ();

            // In case one-choice "show the answer" button previously disabled,
            // or hidden, enable and show.
            $qwizq.find ('button.show_the_answer').removeAttr ('disabled').show ();

            if (! q.qwizard_b) {
               // Re-enable highlight choices on mouseover, cursor to indicate
               // clickable.
               $ ('.choices-' + qwizq_id).on ('mouseover', function () {
                  $ (this).css ({'cursor': 'pointer', 'color': '#045FB4'})
               }).on ('mouseout', function () {;
                  $ (this).css ({'cursor': 'text', 'color': 'black'})
               });
            }
         }
      }
   }
}


// -----------------------------------------------------------------------------
// Pay quizzes.  If user logged in, get whether paid; if not paid, if
// registration required see if within pre-registration period.  If user not
// logged in, set for viewing free preview questions.  Callback is
// set_pay_settings ().  Arguments (for login_ok ()) present if called from
// login.php with proceed_to_login_ok_f set.
this.pay_lock_settings = function (do_i_qwiz_deck, i_login_qwiz, escaped_session_id, remember_f) {

   // Pass relevant params to qwiz_qcards_common function.
   qqc.pay_lock_settings (qname, qwizdata, n_qwizzes, i_login_qwiz,
                          escaped_session_id, remember_f, do_i_qwiz_deck);
}


// -----------------------------------------------------------------------------
this.go_mobile = function (i_qwiz) {

   // Capture current scroll position.
   non_mobile_scrollLeft = window.scrollX;
   non_mobile_scrollTop  = window.scrollY;

   // Quiz container: save style, remove style, change class to mobile.
   var $qwiz = $ ('#qwiz' + i_qwiz);
   qwizdata[i_qwiz].qwiz_style = $qwiz.attr ('style');
   $qwiz.removeAttr ('style').removeClass ('qwiz').addClass ('qwiz-mobile');

   // Place quiz div directly into body, absolutely positioned at top.  First
   // put a placeholder div in so know where to put qwiz div back!
   $qwiz.after ('<div id="qwiz_div_placeholder"></div>');
   $qwiz.appendTo ('body');
   window.scrollTo (0, 0);
   $ ('body').css ({overflow: 'hidden'});

   // Hide qwiz icon -- don't need it, and seems to make vertical scrollbar
   // appear.
   $ ('#icon_qwiz' + i_qwiz).hide ();

   // Don't do changes for small screen if not on small screen.
   if (qqc.is_mobile () || ! document_qwiz_force_mobile_f) {

      // If any labeled diagrams in this quiz, change label header/message.
      $qwiz.find ('.qwizzled_label_head_standard').hide ();
      $qwiz.find ('.qwizzled_label_head_mobile').show ();

      // Mobile version of question if there is one.  Otherwise, show regular
      // question.  Don't do if on login or summary.
      var i_question = qwizdata[i_qwiz].i_question;
      if (i_question < qwizdata[i_qwiz].n_questions && ! qwizdata[i_qwiz].login_show_b) {
         var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_question;
         var $qwizq = $ ('#' + qwizq_id);
         var $mobile_qwizq = $ ('#mobile_qwiz' + i_qwiz + '-q' + i_question);
         if ($mobile_qwizq.length) {
            $mobile_qwizq.show ();
            $qwizq.hide ();
         } else {
            $qwizq.show ();
         }
      }

      // If labeled diagram question, reset "original" label positions (since
      // relative to document, and position in document now very different!).
      if (qwizdata[i_qwiz].$qwizzled && qwizdata[i_qwiz].$qwizzled[qwizq_id]) {
         reset_label_positions ($qwizq);

         // Also, unset width -- just use screen width.
         $qwiz.css ('width', '');
      }

      // Hide go-mobile icon and clickable target.
      $ ('.go-mobile-qwiz' + i_qwiz).hide ();

      // Show exit-mobile icon and clickable target.
      if (! document_qwiz_force_mobile_f) {
         $ ('.exit-mobile-qwiz' + i_qwiz).show ();

         // Hide Learn/test mode.
         $ ('#mode-qwiz' + i_qwiz).hide ();

         // Show exit-mobile slider icon.
         $ ('#icon-exit-mobile-qwiz' + i_qwiz).show ();

         // Show summary report "Return to page view" button.
         $ ('#summary-qwiz' + i_qwiz).find ('button.summary_exit_mobile_qwiz').show ();
      }

      // Set global var.
      document_qwiz_mobile = 'mobile_';
   }
}


// -----------------------------------------------------------------------------
this.open_panel_exit_mobile = function (i_qwiz) {

   $ ('#overlay-exit-mobile-qwiz' + i_qwiz)
      .show ()
      .animate ({top: '0px'}, 500);
   panel_exit_mobile_open_b = true;

   // Also hide default slide-icon.
   $ ('#icon-exit-mobile-qwiz' + i_qwiz).hide ();
}


// -----------------------------------------------------------------------------
this.close_panel_exit_mobile = function (overlay_el) {
   $ (overlay_el).animate ({top: '-100px'}, 500,
                           function () {
                              $ (this).hide ();

                              // Also show default slide-icon (easier to show
                              // all).
                              $ ('div.icon-exit-mobile-qwiz').show ();
                           });

   // Reposition window vertically so can have a scroll to detect.
   window.scrollTo ($ (window).scrollLeft (), 1);

   // Reset flags.
   panel_exit_mobile_open_b = false;
   panel_exit_mobile_just_closed_b = true;

   return false;
}


// -----------------------------------------------------------------------------
this.exit_mobile = function (i_qwiz) {

   // Quiz container: restore style, change class to standard.
   var $qwiz = $ ('#qwiz' + i_qwiz);
   $qwiz.attr ('style', qwizdata[i_qwiz].qwiz_style)
        .removeClass ('qwiz-mobile')
        .addClass ('qwiz');

   // If non-default width for this quiz, reset.
   if ($ ('#xqwiz' + i_qwiz).length) {
      $ ('#qwiz' + i_qwiz).css ('width', qwizdata[i_qwiz].initial_width + 'px');
   }

   // Place quiz back into content.  Re-enable scroll bars.  Scroll back to
   // previous position.
   $ ('#qwiz_div_placeholder').replaceWith ($qwiz);
   $ ('body').css ({overflow: ''});
   window.scrollTo (non_mobile_scrollLeft, non_mobile_scrollTop);

   // Reset exit panel.
   $ ('#overlay-exit-mobile-qwiz' + i_qwiz).css ({top: '-100px', display: 'none'});

   // Turn off scroll detect.
   $ (window).off ('scroll');

   // If any labeled diagrams in this quiz, change label header/message.
   $qwiz.find ('.qwizzled_label_head_standard').show ();
   $qwiz.find ('.qwizzled_label_head_mobile').hide ();

   var i_question = qwizdata[i_qwiz].i_question;
   var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_question;
   var $qwizq = $ ('#' + qwizq_id);
   if (i_question >= 0 && i_question < qwizdata[i_qwiz].n_questions
                                          && ! qwizdata[i_qwiz].login_show_b) {

      // Hide mobile version of question if there is one.
      var $mobile_qwizq = $ ('#mobile_qwiz' + i_qwiz + '-q' + i_question);
      $mobile_qwizq.hide ();

      // Show regular version.
      $qwizq.show ();
   }

   // If labeled diagram, accommodate large images, etc. and reset label
   // positions.
   if (qwizdata[i_qwiz].$qwizzled && qwizdata[i_qwiz].$qwizzled[qwizq_id]) {
      var table_width = 10 + $qwizq.find ('table.qwizzled_table').outerWidth ();
      if (table_width > qwizdata[i_qwiz].initial_width) {
         $qwiz.css ('width', table_width + 'px');

         // Set flag to reset width on next question.
         qwizdata[i_qwiz].width_reset = true;
      }
      reset_label_positions ($qwizq);
   }

   // Hide exit-mobile slider icon (easier just to hide them all).
   $ ('div.icon-exit-mobile-qwiz, div.icon-panel-exit-mobile-qwiz').hide ();

   // Hide exit-mobile icon (in progress bar).
   $ ('.exit-mobile-qwiz').hide ();

   // Also hide summary exit-mobile button.
   $ ('button.summary_exit_mobile_qwiz').hide ();

   // If still on small screen, show go-mobile icon and clickable target, and
   // learn/test mode.
   if (qqc.is_mobile ()) {
      $ ('.go-mobile-qwiz' + i_qwiz).show ();
      $ ('#mode-qwiz' + i_qwiz).show ();
   }

   // Unset global vars.
   document_qwiz_mobile = '';
   panel_exit_mobile_just_closed_b = false;
}


// -----------------------------------------------------------------------------
function reset_label_positions ($qwizq) {
   if (debug[8]) {
      console.log ('[reset_label_positions] $qwizq:', $qwizq);
   }
   $qwizq.find ('td.qwizzled_labels div.qwizzled_label').each (function () {

      // Use <li> position, in case label has been dragged.
      var label_offset = $ (this).parents ('li').offset ();
      if (debug[8]) {
         console.log ('[reset_label_positions] label_offset:', label_offset);
      }
      $ (this).data ('label_x', label_offset.left).data ('label_y', label_offset.top);
   });
}


// -----------------------------------------------------------------------------
this.start_hint_timeout = function (i_qwiz) {

   // Only execute this function once for this question.
   $ ('div#qwiz' + i_qwiz).removeAttr ('onmouseenter');

   var i_question = qwizdata[i_qwiz].i_question;
   var $check_answer = $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question);
   if (debug[0]) {
      console.log ('[start_hint_timeout] $check_answer.length:', $check_answer.length);
   }

   // Closure for setTimeout ().
   var show_hint_button = function () {
      $check_answer.find ('button.qwiz_textentry_hint')
         .removeAttr ('disabled')
         .html ('Hint').css ({display: 'inline-block'});
   }
   $check_answer.find ('button.qwiz_textentry_hint').html ('Hint').hide ();
   if (hint_timeout_sec >= 0) {
      show_hint_timeout[i_qwiz] = setTimeout (show_hint_button, hint_timeout_sec*1000);
   }
}


// -----------------------------------------------------------------------------
function process_multiple_choice (i_qwiz, i_question, htm, opening_tags) {

   // Create alternate divs -- desktop and mobile.
   var desktop_htm;
   var remaining_htm;

   // Span for default indented paragraph style for choices.  Want this ahead of
   // any opening tags user put in before first "[c]".
   var choices_html = '';
   var span_pos = qqc.opening_tag_shortcode_pos ('([c]|[c*])', htm);
   if (span_pos == htm.length) {
      errmsgs.push (T ('Did not find choices ("[c]")') + '.  qwiz: ' + (i_qwiz + 1) + ', ' + T ('question') + ' ' + (i_question + 1));
      desktop_htm = '';
      remaining_htm = '';
   } else {
      var question_htm = htm.substr (0, span_pos);
      if (debug[0]) {
         console.log ('[process_multiple_choice] span_pos: ', span_pos);
         console.log ('[process_multiple_choice] question_htm: ', question_htm);
      }

      // If quiz subject to recording, save question html -- without tags.
      // Also, replace non-breaking spaces and EOLs with space, multiple spaces
      // with single space, trim.  If dataset question: use dataset ID.
      if (qwizdata[i_qwiz].qrecord_id) {
         var q_and_a_text;
         if (qqc.isInteger (qwizdata[i_qwiz].dataset_id[i_question])) {
            q_and_a_text = qqc.remove_tags_eols (question_htm);
            qwizdata[i_qwiz].q_and_a_text[i_question]  = qqc.q_and_a_hash (q_and_a_text);
            qwizdata[i_qwiz].q_and_a_crc32[i_question] = qwiz_crc32 (htm);
         } else {
            qwizdata[i_qwiz].q_and_a_text[i_question]  = qwizdata[i_qwiz].dataset_id[i_question];
            qwizdata[i_qwiz].q_and_a_crc32[i_question] = 'dataset';
         }
      }

      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].type = 'multiple_choice';

         // Include opening tags -- formatting -- with question text, so
         // qwizard will save (and allow changes to) formatting.
         question_htm = qqc.shortcodes_to_video_elements (question_htm);
         qw.questions_cards[i_question].question_text = opening_tags + question_htm;
      }

      // Wrap in div for this qwiz and question.
      var bg_img_style = create_bg_img_style (i_qwiz, i_question);
      desktop_htm =   '<div id="qwiz' + i_qwiz + '-q' + i_question + '" class="qwizq"' + bg_img_style + '>\n'
                    +    '<div class="qwiz-question qwiz_editable">'
                    +       opening_tags + question_htm
                    +    '</div>';

      if (debug[1]) {
         console.log ('[process_multiple_choice] desktop_htm: ', desktop_htm);
      }

      remaining_htm = htm.substr (span_pos);

      // Include paragraph-close -- without this, if there's a paragraph-close
      // within the choices that corresponds to a previous unclosed paragraph,
      // then the next span won't work.
      // DKTMP
      //choices_html = '</p><span class="qwiz-choices">';
      choices_html = '</p>';
   }

   // Do choice tags.  Change [c] to radio button, enclose text in span to
   // highlight on mouseover.  Choice text includes opening tags and
   // corresponding closing tags (if any), up to next [c] tag or first [f] tag.
   // Count choice tags.
   var choice_tags = htm.match (/\[c\*{0,1}\]/gm);
   var n_choices = 0;
   if (choice_tags) {
      n_choices = choice_tags.length;
   }
   if (debug[0]) {
      console.log ('[process_multiple_choice] n_choices: ', n_choices);
   }
   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].n_choices = n_choices;
      qw.questions_cards[i_question].choices   = [];
      qw.questions_cards[i_question].feedbacks = [];
   }

   var n_correct = 0;

   var choice_start_tags = ['[c]', '[c*]'];
   var choice_next_tags  = ['[c]', '[c*]', '[x]'];

   var got_feedback_b = false;
   var i_fx = -1;
   var feedback_divs  = [];   // Feedback div for desktop.
   var feedback_items = [];   // Plain html for mobile.
   var choice_items   = [];   // "
   var i_choice_correct = 0;
   for (var i_choice=0; i_choice<n_choices; i_choice++) {

      // Find choice text -- from opening tags through [c] or [c*] up to
      // opening tags for next tag.  Delete it from remaining_htm.
      var choice_html = qqc.parse_html_block (remaining_htm, choice_start_tags,
                                              choice_next_tags);
      remaining_htm = remaining_htm.substr (choice_html.length);

      // See if there's feedback within the choice html.  Take it out of the
      // choice html.
      var r = process_feedback_item (choice_html, i_qwiz, i_question, i_choice);
      choice_html  = r.choice_html;

      if (r.feedback_div) {

         // If this is the last choice, and didn't previously get feedback
         // with choices, then may have all feedback items together following
         // choice items (backwards compatibility) or, if only one feedback
         // item, use same feedback for all choices.
         if (i_choice == n_choices-1 && ! got_feedback_b && n_choices != 1) {

            // Assume just got feedback for the first choice.
            feedback_divs[0] = r.feedback_div;
            feedback_items[0] = r.feedback_item_html;
            var n_feedback_items = 1;

            // If feedback given with [fx], save index.
            if (r.fx_b) {
               i_fx = 0;

               // [fx] does not count as an "item".
               n_feedback_items = 0;
            }

            // Look for rest.
            for (var i_feedback=1; i_feedback<n_choices; i_feedback++) {
               var r = process_feedback_item (choice_html, i_qwiz, i_question,
                                              i_feedback);
               choice_html  = r.choice_html;
               if (! r.feedback_div) {
                  break;
               }
               feedback_divs[i_feedback] = r.feedback_div;
               feedback_items[i_feedback] = r.feedback_item_html;
               if (r.fx_b) {
                  if (i_fx == -1) {
                     i_fx = i_feedback;
                  } else {
                     errmsgs.push (T ('Got more than one [fx]') + '.  qwiz: ' + (i_qwiz + 1) + ', ' + T('question') + ' ' + (1 + i_question));
                  }
               } else {

                  // [fx] does not count as an "item".
                  n_feedback_items++;
               }
            }

            // Either got just one feedback item (which we'll interpret as
            // applying to the last choice), or should get one item for each
            // choice.
            if (n_feedback_items == 1 || i_fx != -1) {

               // Move that item to the last choice.
               feedback_divs[n_choices-1] = feedback_divs[0];
               feedback_divs[0] = '';
               feedback_items[n_choices-1] = feedback_items[0];
               feedback_items[0] = '';
               if (i_fx == 0) {
                  i_fx = n_choices - 1;
               }
            } else {

               // Check got them all.
               if (n_feedback_items != n_choices) {
                  errmsgs.push (T ('Number of feedback items does not match number of choices') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T('question') + ' ' + (1 + i_question));
               } else {

                  // First feedback item needs to have ID updated to indicate
                  // really belongs to first choice.
                  feedback_divs[0] = feedback_divs[0].replace (/(qwiz[0-9]+-q[0-9]+-a)[0-9]+/, '\$10');
               }
            }
         } else {

            // Create a div for the feedback we just processed.
            got_feedback_b = true;
            if (r.fx_b) {
               if (i_fx == -1) {
                  i_fx = feedback_divs.length;
               } else {
                  errmsgs.push (T ('Got more than one [fx]') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T('question') + ' ' + (1 + i_question));
               }
            }
            feedback_divs.push (r.feedback_div);
            feedback_items.push (r.feedback_item_html);

            // Check that there's not more than one feedback item accompanying
            // this (not-last) choice.
            var r = process_feedback_item (choice_html, i_qwiz, i_question,
                                           i_feedback);
            if (r.feedback_div) {
               errmsgs.push (T ('More than one feedback shortcode [f] or [fx] given with a choice') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question) + ', ' + T ('choice') + ' ' + (1 + i_choice));
            }
         }
      } else {

         // No feedback given for this choice.  Record with empty "div".
         feedback_divs.push ('');
         feedback_items.push ('');
      }
      if (debug[2]) {
         console.log ('[process_multiple_choice] feedback_divs:', feedback_divs);
      }

      // Save for mobile.
      var choice_item = choice_html.replace (/\[c\*{0,1}\]/m, '');
      choice_items.push (choice_item);
      if (n_choices > 1
            || (q.qwizard_b
                   && qwizdata[i_qwiz].qwizard_multiple_choice_b[i_question])) {

         // Replace [c] or [c*] with radio button.  Start span for choice text
         // (separate from radio button).
         var r = create_radio_button_html (i_qwiz, i_question, i_choice,
                                           choice_tags[i_choice]);
         if (r.correct) {
            n_correct++;
            i_choice_correct = i_choice;
         }
         choice_html = choice_html.replace (/\[c\*{0,1}\]/m, r.htm + '<span class="qwiz-choice qwiz_editable" data-i_choice="' + i_choice + '">');

         // Assemble with span to make choice clickable and highlight on
         // mouseover.  If starts with a paragraph or header tag, but does not
         // end with corresponding closing tag, move the span _after_ the
         // opening tag.
         if (choice_html.substr (0, 2) == '<p' || choice_html.substr (0, 2) == '<h') {
            var len = choice_html.length;

            // len - 4 is third character from end. </p> </h1>
            //                                      4321 54321
            if (choice_html.substr (len - 4, 3) != '</p'
                && choice_html.substr (len - 5, 3) != '</h') {
               var end_opening_tag_pos = choice_html.indexOf ('>');
               if (end_opening_tag_pos != -1) {
                  choices_html += choice_html.substr (0, end_opening_tag_pos + 1);
                  choice_html = choice_html.substr (end_opening_tag_pos + 1);
               }
            } else {

               // End choice text span inside the closing tag.
               choice_html = choice_html.replace (/<\/(p|h[1-6])>$/, '</span>$&');
            }
         }

         // End choice text span.
         choice_html += '</span>';

         // For qwizard, save what's inside the span as the choice text.
         if (set_qwizard_data_b) {
            var m = choice_html.match (/<span class="qwiz-choice[^>]+>([^]*?)<\/span>/);
            var choice = qqc.shortcodes_to_video_elements (m[1])
            qw.questions_cards[i_question].choices[i_choice] = choice;
         }

         var qwiz_question = 'qwiz' + i_qwiz + '-q' + i_question;
         var qwiz_question_choice = qwiz_question + '-a' + i_choice;
         var style = '';
         if (q.qwizard_b) {
            style = ' style="cursor: text;"';
         }
         choices_html += '<span class="choices-' + qwiz_question + ' choice-' + qwiz_question_choice + ' qwiz-choices" onclick="' + qname + '.process_choice (\'' + qwiz_question_choice + '\')" data-i_choice="' + i_choice + '"' + style + '>\n'
                      +     '<span class="qwiz-choice" data-i_choice="' + i_choice + '">'
                      +        choice_html
                      +     '</span>'
                      + '</span>';
      } else {

         // Only one choice - do as regular button rather than radio.  Click
         // goes to "Got-it/Repeat" choice presentation, unless it's a one-
         // question quiz.  Left margin to stay clear of Qwiz icon on first page.
         choice_html = choice_html.replace (/\[c\*{0,1}\]/m, '');
         n_correct = 1;
         var onclick;
         if (qwizdata[i_qwiz].n_questions == 1) {
            onclick = qname + '.process_choice (\'qwiz' + i_qwiz + '-q' + i_question + '-a0\', true)';
         } else {
            onclick = qname + '.show_answer_got_it_or_not (' + i_qwiz + ', ' + i_question + ', this)';
         }
         var button_label = choice_html;
         if (button_label.indexOf ('[show_me_placeholder]') != -1) {
            button_label = 'Show me the answer';
         }
         choices_html += '<button class="qwiz_button show_the_answer qwiz-choice qwiz_editable" data-i_choice="0" style="margin-left: 20px;" onclick="' + onclick + '">';
         choices_html +=    button_label;
         choices_html += '</button>\n';

         if (set_qwizard_data_b) {
            qw.questions_cards[i_question].type = 'show_me';
            choice_html = qqc.shortcodes_to_video_elements (choice_html);
            qw.questions_cards[i_question].choices[i_choice] = choice_html;
         }

         // Require feedback for "Show-the-answer" type question.
         if (! feedback_divs[0]) {
            errmsgs.push (T ('Feedback [f] is required for a one-choice question') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
         }
      }
   }

   if (set_qwizard_data_b && qw.questions_cards[i_question].type != 'show_me') {

      // Let qwizard know choices are "inline" if no paragraph, heading, or
      // break tags in choices span (except at beginning).
      if (debug[12]) {
         console.log ('[process_multiple_choice] choices_html:', choices_html);
      }
      // Skip any at beginning (before first sub-span).  First look past
      // '</p><span class="qwiz-choices">'.
      //  0----+----1----+----2----+----3
      var pos_sub_span1 = choices_html.substr (31).indexOf ('<span');
      if (pos_sub_span1 != -1) {
         if (choices_html.substr (31 + pos_sub_span1).search (/<p|<h[1-6]|<br/) == -1) {
            qw.questions_cards[i_question].choices_inline = true;
         }
      }
   }
   desktop_htm += choices_html;

   // Include clearing div in case image floating left or right (needed to
   // expand parent div and its border).
   desktop_htm += '<div style="clear: both;"></div>\n';

   if (debug[1]) {
      console.log ('[process_multiple_choice] desktop_htm: ', desktop_htm);
   }

   // Check that one and only one choice is marked correct.
   if (n_correct == 0) {
      errmsgs.push (T ('No choice was marked correct') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
   } else if (n_correct > 1) {
      errmsgs.push (T ('More than one choice was marked correct') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
   } else {
      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].correct_choice = i_choice_correct;
      }
   }

   // ..........................................................................
   // If got [fx], use that feedback for all empty feedback items except correct
   // choice.  Otherwise, create canned feedback for any empty feedback items.
   for (var i_choice=0; i_choice<n_choices; i_choice++) {
      if (! feedback_divs[i_choice]) {
         if (i_fx != -1 && i_choice != i_choice_correct) {

            // Reset ID to match choice.
            feedback_divs[i_choice] = feedback_divs[i_fx].replace (/(qwiz[0-9]+-q[0-9]+-a)[0-9]+/, '\$1' + i_choice);
         } else {
            var response = q.canned_feedback (i_choice == i_choice_correct);
            feedback_divs[i_choice]
                               = create_feedback_div_html (i_qwiz, i_question,
                                                           i_choice, response);
            feedback_items[i_choice] = response;
            //DKTMP
            //if (set_qwizard_data_b) {
            //   qw.questions_cards[i_question].feedbacks[i_choice] = response;
            //}
         }
      }
   }

   // Add feedback divs to html string.
   feedback_divs = feedback_divs.join ('\n');
   desktop_htm += feedback_divs;

   if (set_qwizard_data_b) {
      for (var i_choice=0; i_choice<n_choices; i_choice++) {
         qw.questions_cards[i_question].feedbacks[i_choice]
                  = qqc.shortcodes_to_video_elements (feedback_items[i_choice]);
      }
   }

   // If "show-the-answer" (single-choice) question, buttons for "Got it" and
   // "Need more practice".
   if (n_choices == 1 && (qwizdata[i_qwiz].n_questions > 1 || qwizdata[i_qwiz].use_dataset)) {
      desktop_htm += create_got_it_or_not ('', i_qwiz, i_question);
   }
   if (debug[2]) {
      console.log ('[process_multiple_choice] desktop_htm: ', desktop_htm);
   }

   // Close question div.
   desktop_htm += '</div>\n';

   // Assemble mobile div.
   var qwiz_question = 'mobile_qwiz' + i_qwiz + '-q' + i_question;
   var mobile_htm = [];
   mobile_htm.push ('<div id="mobile_qwiz' + i_qwiz + '-q' + i_question + '" class="qwizq-mobile">\n');
   mobile_htm.push (   opening_tags + question_htm);
   mobile_htm.push (   '<ul class="mobile_choices">');
   for (var i_choice=0; i_choice<n_choices; i_choice++) {
      var qwiz_question_choice = qwiz_question + '-a' + i_choice;
      var data_correct = i_choice == i_choice_correct ? 'data-correct="1"' : '';

      // If "show-the-answer" (single-choice) question, different processing,
      // unless a single-question quiz.
      var onclick;
      if (n_choices > 1 || qwizdata[i_qwiz].n_questions == 1) {
         onclick = qname + '.process_choice (\'' + qwiz_question_choice + '\')';
      } else {
         onclick = qname + '.show_answer_got_it_or_not (' + i_qwiz + ', ' + i_question + ', this)';
      }
      mobile_htm.push (    '<li id="choice-' + qwiz_question_choice + '" class="mobile_choice" onclick="' + onclick  + '" ' + data_correct + '>');
      mobile_htm.push (       '<div class="mobile_choice">');
      mobile_htm.push (          choice_items[i_choice]);
      mobile_htm.push (       '</div>');
      mobile_htm.push (    '</li>');
   }
   mobile_htm.push (   '</ul>');
   mobile_htm.push (   '<div style="clear: both;"></div>');

   // Mobile version of feedback divs.
   mobile_htm.push (feedback_divs.replace (/id="qwiz/gm, 'id="mobile_qwiz'));
   mobile_htm.push ('</div>');

   // Also, if "show-the-answer" (single-choice) question, buttons for "Got it"
   // and "Need more practice".
   if (n_choices == 1 && (qwizdata[i_qwiz].n_questions > 1 || qwizdata[i_qwiz].use_dataset)) {
      mobile_htm.push (create_got_it_or_not ('mobile_', i_qwiz, i_question));
   }

   mobile_htm = mobile_htm.join ('\n');

   // Return both divs.
   return desktop_htm + '\n' + mobile_htm;
}


// -----------------------------------------------------------------------------
function process_textentry (i_qwiz, i_question, htm, opening_tags) {

   // If this is first textentry question for this quiz, create data object.
   // Also set flag to initialize autocomplete or single-char capture.
   if (! qwizdata[i_qwiz].textentry) {
      qwizdata[i_qwiz].textentry = {};
      textentry_b = true;
   }

   // Capture text up to first [c] or [c*], not including opening tags before.
   var question_text = '';
   var c_pos = qqc.opening_tag_shortcode_pos ('([c*]|[c])', htm);
   if (c_pos < htm.length) {
      question_text = htm.substr (0, c_pos);
   } else {
      errmsgs.push (T ('No answer-word given') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
   }

   // If recording, save.
   if (qwizdata[i_qwiz].qrecord_id) {
      if (qqc.isInteger (qwizdata[i_qwiz].dataset_id[i_question])) {

         // Replace [textentry] shortcode with _______.
         var q_and_a_text = qqc.remove_tags_eols (question_text);
         q_and_a_text     = q_and_a_text.replace (/\[textentry[^\]]*\]/, '_________');
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qqc.q_and_a_hash (qqc.remove_tags_eols (q_and_a_text));

         // Let's use all of the question html for the checksum (avoid issues
         // with differences only in images or answer words, which are not in
         // q_and_a_text).
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = qwiz_crc32 (htm);
      } else {

         // Dataset questions: use dataset ID.
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qwizdata[i_qwiz].dataset_id[i_question];
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = 'dataset';
      }
   }

   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].type = 'textentry';

      // Include opening tags -- formatting -- with question text, so qwizard
      // will save (and allow changes to) formatting.
      question_text = qqc.shortcodes_to_video_elements (question_text);
      qw.questions_cards[i_question].question_text = opening_tags + question_text;
      qw.questions_cards[i_question].choices = [];
      qw.questions_cards[i_question].correct_choice_fs = [];
      qw.questions_cards[i_question].feedbacks = [];
   }

   // See if attributes: plurals, minlength, etc.
   var textentry_plural_b = false;
   var textentry_suggest_b = true;
   var textentry_minlength = 3;
   var use_dict_b  = default_use_dict == 'true';
   var use_terms_b = default_use_terms == 'true';
   var single_char_b = false;
   var m = htm.match (/\[textentry([^\]]*)\]/m);
   var attributes = '';
   if (m) {
      attributes = m[1];
      if (attributes) {

         // Look for "plural=" attribute.  Match regular double-quote, or
         // left- or right-double-quote.
         attributes = qqc.replace_smart_quotes (attributes);
         textentry_plural_b = get_attr (attributes, 'plural') == 'true';

         // "suggest=" attribute.  False will override everything else.
         textentry_suggest_b = get_attr (attributes, 'suggest') != 'false';

         // "minlength=" attribute.
         var attr_val = get_attr (attributes, 'minlength');
         if (attr_val != '') {
            textentry_minlength = attr_val;
         }

         // "use_terms=" attribute.
         var use_terms = get_attr (attributes, 'use_terms');
         if (use_terms) {
            use_terms_b = use_terms != 'false';
         }

         // "use_dict=" attribute.
         var use_dict = get_attr (attributes, 'use_dict');
         if (use_dict) {
            use_dict_b = use_dict != 'false';
         }

         // "single_char=" attribute.
         single_char_b = get_attr (attributes, 'single_char') == 'true';
      }
   }
   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].type = single_char_b ? 'one_letter_answer' : 'textentry';
      qw.questions_cards[i_question].textentry_attributes = attributes;
      qw.questions_cards[i_question].textentry_plural_b = textentry_plural_b;
   }

   // Remaining htm - choices and feedback.
   var remaining_htm = htm.substr (c_pos);
   htm = htm.substr (0, c_pos);

   // Replace [textentry] with input textbox.  Placeholder will be set later (in
   // display_question ()).
   var classname;
   var style;
   if (single_char_b) {
      classname = classname = 'qwiz_single_char_entry';
      style     = 'style="width: 2rem;" ';
   } else if (textentry_suggest_b) {
      classname = 'qwiz_textentry';
      style     = 'style="width: 18em;" ';
   } else {
      classname = 'qwiz_textentry_no_suggest';
      style     = 'style="width: 18em;" ';
   }
   var input = '<input type="text" id="textentry-qwiz' + i_qwiz + '-q' + i_question + '" class="' + classname + '" ' + style + 'onfocus="' + qname + '.set_textentry_i_qwiz (event, this)" />';

   // If qwizard, editable div comes in two pieces -- before and after textentry
   // input field.
   var new_htm;
   var re = new RegExp ('\\[textentry[^\\]]*\\]');
   if (q.qwizard_b) {
      new_htm = create_hangman_textentry_editable_divs (i_qwiz, i_question,
                                                        opening_tags, [input],
                                                        'textentry', re, htm);
   } else {
      new_htm =   '<div id="qwiz' + i_qwiz + '-q' + i_question + '" class="qwizq">\n'
                +    opening_tags + htm.replace (re, input);
   }

   // Add check-answer and hint buttons for each question.
   new_htm +=  '<br />'
             + '<div id="textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question + '" class="textentry_check_answer_div">\n'
             +    '<button class="qwiz_button qwiz_button_disabled textentry_check_answer" onclick="' + qname + '.textentry_check_answer (' + i_qwiz + ')">'
             +        T ('Check answer')
             +    '</button>\n'
             +    '&emsp;\n'
             +    '<button class="qwiz_button qwiz_textentry_hint" style="display: none; font-size: 11px; padding: 2px 2px; border-radius: 5px;" onclick="' + qname + '.textentry_hint (' + i_qwiz + ')" disabled>'
             +        T ('Hint')
             +    '</button>\n'
             + '</div>\n';

   // Look for choices and feedback (interleaved only, feedback optional).
   // Save as data, delete here.
   var n_correct = 0;

   var choice_start_tags = ['[c]', '[c*]'];
   var choice_next_tags  = ['[c]', '[c*]', '[x]'];

   var got_feedback_b = false;
   var feedback_divs = [];

   // Choices.  Object for this question set to array of choices.
   qwizdata[i_qwiz].textentry[i_question] = {};
   qwizdata[i_qwiz].textentry[i_question].choices = [];
   qwizdata[i_qwiz].textentry[i_question].textentry_plural_b = textentry_plural_b;
   qwizdata[i_qwiz].textentry[i_question].textentry_suggest_b = textentry_suggest_b;
   qwizdata[i_qwiz].textentry[i_question].textentry_minlength = textentry_minlength;
   qwizdata[i_qwiz].textentry[i_question].use_terms_b = use_terms_b;
   qwizdata[i_qwiz].textentry[i_question].use_dict_b = use_dict_b;
   qwizdata[i_qwiz].textentry[i_question].single_char_b = single_char_b;
   qwizdata[i_qwiz].textentry[i_question].choices_correct = [];
   qwizdata[i_qwiz].textentry[i_question].answers = [];
   qwizdata[i_qwiz].textentry[i_question].first_correct_answer = '';
   qwizdata[i_qwiz].check_answer_disabled_b = true;

   // Loop over [c]s.
   var i_choice = 0;
   var default_choice_given_b = false;
   while (true) {
      var choice_html = qqc.parse_html_block (remaining_htm, choice_start_tags,
                                              choice_next_tags);
      if (choice_html == 'NA') {
         break;
      }
      remaining_htm = remaining_htm.substr (choice_html.length);

      // See if there's feedback within the choice html.
      var r = process_feedback_item (choice_html, i_qwiz, i_question, i_choice);
      choice_html  = r.choice_html;
      var feedback_item_html = '';

      if (r.feedback_div) {
         got_feedback_b = true;
         feedback_divs.push (r.feedback_div);
         feedback_item_html = r.feedback_item_html;

         // Check that there's not more than one feedback item accompanying
         // this choice.
         var r = process_feedback_item (choice_html, i_qwiz, i_question,
                                        i_choice);
         if (r.feedback_div) {
            errmsgs.push (T ('More than one feedback shortcode [f] given with choice') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question) + ', ' + T ('choice') + ' ' + (1 + i_choice));
         }
      } else {

         // No feedback given for this choice.  Record with empty "div".
         feedback_divs.push ('');
      }
      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].feedbacks[i_choice]
                        = qqc.shortcodes_to_video_elements (feedback_item_html);
      }

      // Parse choice data.  [c] or [c*] followed by semicolon-separated list
      // of potential answers.
      var correct_b = choice_html.search (/\[c\*\]/) != -1;
      if (correct_b) {
         n_correct++;
      }

      // Delete up through [c] or [c*].
      choice_html = choice_html.replace (/[^]*\[c\*{0,1}\]/, '');

      // Delete any tags and EOLs and non-breaking spaces.  Don't do if still
      // qwizard placeholder.
      if (! (q.qwizard_b && choice_html.indexOf ('placeholder') != -1)) {
         choice_html = choice_html.replace (/<[^>]+>|\n|&nbsp;/g, '');
      }

      // Error if just blanks and semicolons.
      if (choice_html.replace (';', '').search (/\S/) == -1) {
         errmsgs.push (T ('No word(s) given for [textentry] choice') + '.  qwiz: ' + (i_qwiz + 1) + ', ' + T ('question') + ' ' + (1 + i_question) + ', ' + T ('choice') + ' ' + (1 + i_choice));
      }

      // Split on semicolons.
      var alts = choice_html.split (/\s*;\s*/);

      // Eliminate any blank entries.
      var nonblank_alts = [];
      for (var i=0; i<alts.length; i++) {
         if (alts[i].search (/\S/) != -1) {
            nonblank_alts.push (qqc.trim (alts[i]));
         }
      }

      // If default choice/feedback ("*" entered), set indicator.
      if (nonblank_alts[0] == '*') {
         default_choice_given_b = true;
         if (correct_b) {
            errmsgs.push (T ('For [textentry] question, wildcard choice ("*", for any other user entry) cannot be marked correct "[c*]"') +  '.  qwiz: ' + (i_qwiz + 1) + ', ' + T ('question') + ' ' + (1 + i_question) + ', ' + T ('choice') + ' ' + (1 + i_choice));
         }
         /*
         if (feedback_divs[i_choice] == '') {
            errmsgs.push (T ('For [textentry] question, wildcard choice ("*", for any other user entry) must be accompanied by feedback "[f]"'));
         }
         */
      }

      // Save these, associated with this choice.
      qwizdata[i_qwiz].textentry[i_question].choices.push (nonblank_alts);
      qwizdata[i_qwiz].textentry[i_question].choices_correct.push (correct_b);
      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].choices.push (nonblank_alts.join (';'));
         qw.questions_cards[i_question].correct_choice_fs.push (correct_b ? 1 : 0);
      }

      // Save first correct answer -- for hint.
      if (correct_b) {
         if (nonblank_alts.length && qwizdata[i_qwiz].textentry[i_question].first_correct_answer == '') {
            qwizdata[i_qwiz].textentry[i_question].first_correct_answer = nonblank_alts[0];
         }
      }

      // Also save as simple array for this question.  Make sure no duplicates
      // (wouldn't want same answer to be both correct and incorrect!).
      var n_alts = nonblank_alts.length;
      for (var i=0; i<n_alts; i++) {
         if (qwizdata[i_qwiz].textentry[i_question].answers.indexOf (nonblank_alts[i]) != -1
                                           && nonblank_alts != 'Enter word') {
            errmsgs.push (T ('Answer given in more than one choice') + ': ' + nonblank_alts[i] + '.  qwiz: ' + (i_qwiz + 1) + ', ' + T ('question') + ' ' + (1 + i_question) + ', ' + T ('choice') + ' ' + (1 + i_choice));
         }
      }
      qwizdata[i_qwiz].textentry[i_question].answers
                 = qwizdata[i_qwiz].textentry[i_question].answers.concat (nonblank_alts);
      i_choice++;
   }
   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].n_choices = i_choice;
   }

   // If default choice not given, add it.
   if (! default_choice_given_b) {
      i_choice++;
      qwizdata[i_qwiz].textentry[i_question].choices.push (['*']);
      qwizdata[i_qwiz].textentry[i_question].choices_correct.push (false);
      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].choices.push ('*');
         qw.questions_cards[i_question].correct_choice_fs.push (0);
         qw.questions_cards[i_question].n_choices = i_choice;
      }
   }
   var n_choices = i_choice;

   // Include clearing div in case image floating left or right (needed to
   // expand parent div and its border).
   new_htm += '<div style="clear: both;"></div>\n';

   // Check that got at least one correct choice.
   if (n_correct == 0) {
      errmsgs.push (T ('No choice was marked correct') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
   }

   // ..........................................................................
   // Create canned feedback for any empty feedback items
   for (var i_choice=0; i_choice<n_choices; i_choice++) {
      if (! feedback_divs[i_choice]) {
         var response = q.canned_feedback (qwizdata[i_qwiz].textentry[i_question].choices_correct[i_choice]);
         feedback_divs[i_choice] = create_feedback_div_html (i_qwiz, i_question,
                                                             i_choice, response);
         if (set_qwizard_data_b) {
            qw.questions_cards[i_question].feedbacks[i_choice] = response;
         }
      }
   }

   // Add feedback divs to html string.
   new_htm += feedback_divs.join ('\n');

   // Close question div.
   new_htm += '</div>\n';
   if (debug[2] || debug[12]) {
      console.log ('[process_textentry] new_htm: ', new_htm);
   }

   return new_htm;
}


// -----------------------------------------------------------------------------
function process_hangman (i_qwiz, i_question, htm, opening_tags) {

   // Is it a hangman labeled diagram?
   var hangman_labeled_diagram_f = htm.indexOf ('hangman_img_wrapper') != -1;

   // Capture text up to opening tags, if any, before first [c] or [c*].
   var c_pos = qqc.opening_tag_shortcode_pos ('([c*]|[c])', htm);
   if (c_pos < htm.length) {
      question_text = htm.substr (0, c_pos);
   } else {
      errmsgs.push (T ('Did not get [c] or [c*] (hangman answer) with [hangman]') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
      c_pos = 0;
   }

   // If recording, save.
   if (qwizdata[i_qwiz].qrecord_id) {
      if (qqc.isInteger (qwizdata[i_qwiz].dataset_id[i_question])) {

         // Replace [hangman] shortcodes with _______.
         var q_and_a_text = qqc.remove_tags_eols (question_text);
         q_and_a_text = q_and_a_text.replace (/\[hangman[^\]]*\]/g, '_________');
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qqc.q_and_a_hash (qqc.remove_tags_eols (q_and_a_text));

         // Let's use all of the question html for the checksum (problems when
         // hangman questions based on different images and answer words --
         // neither of which are in q_and_a_text).
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = qwiz_crc32 (htm);
      } else {

         // Dataset questions: use dataset ID.
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qwizdata[i_qwiz].dataset_id[i_question];
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = 'dataset';
      }
   }
   var hangman_max_hints = default_hangman_max_hints;

   // Look for "hint=" attribute for first only -- applies to all.
   var m = htm.match (/\[hangman([^\]]*)\]/m);
   var attributes = '';
   if (m) {
      attributes = m[1];
      if (attributes) {
         attributes = qqc.replace_smart_quotes (attributes);
         var hints = get_attr (attributes, 'hints');
         if (hints) {
            if (hints.search (/[^0-9]/) == -1) {
               hangman_max_hints = parseInt (hints, 10);
            } else {
               errmsgs.push (T ('"hints" for [hangman] should be a number') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
            }
         }
      }
   }
   var type                         = 'hangman';
   var labeled_diagram_opening_tags = opening_tags;
   if (hangman_labeled_diagram_f) {
      type                         = 'hangman_labeled_diagram';
      labeled_diagram_opening_tags = '';
   }
   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].type = type;
      qw.questions_cards[i_question].hangman_attributes = attributes;

      // Include opening tags -- formatting -- with question text, so qwizard
      // will save (and allow changes to) formatting.
      question_text = qqc.shortcodes_to_video_elements (question_text);

      qw.questions_cards[i_question].question_text = labeled_diagram_opening_tags + question_text;
      qw.questions_cards[i_question].choices = [];
      qw.questions_cards[i_question].correct_choice_fs = [];
      qw.questions_cards[i_question].feedbacks = [];
   }

   var question_htm = htm.substr (0, c_pos);

   var m = question_htm.match (/\[hangman/g);
   var n_hangman = m.length;

   // Hangman answer(s).  Start with [c] or [c*].
   var remaining_htm = htm.substr (c_pos);
   var choice_start_tags = ['[c]', '[c*]'];
   var choice_next_tags  = ['[c]', '[c*]', '[x]', '<div class="qwizzled_question_bottom_border_title"'];

   var got_feedback_b = false;
   var feedback_divs = [];

   // Loop over [c]s.
   var hangman_re = new RegExp ('\\[hangman[^\\]]*\\]');
   var hangman_spans = [];
   var i_choice = 0;
   while (true) {
      var hangman_answer = '';
      var hangman_answer_length = 0;
      var choice_html = qqc.parse_html_block (remaining_htm, choice_start_tags,
                                              choice_next_tags);
      if (choice_html == 'NA') {
         break;
      }
      remaining_htm = remaining_htm.substr (choice_html.length);

      // See if there's feedback within the choice html.
      var r = process_feedback_item (choice_html, i_qwiz, i_question, i_choice);
      choice_html  = r.choice_html;

      if (r.feedback_div) {
         got_feedback_b = true;

         feedback_divs.push (r.feedback_div);
         if (set_qwizard_data_b) {
            qw.questions_cards[i_question].feedbacks[i_choice]
                      = qqc.shortcodes_to_video_elements (r.feedback_item_html);
         }

         // Check that there's not more than one feedback item accompanying
         // this choice.
         var r = process_feedback_item (choice_html, i_qwiz, i_question,
                                        i_choice);
         if (r.feedback_div) {
            errmsgs.push (T ('More than one feedback shortcode [f] given with hangman answer') + '.  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question) + ', ' + T ('choice') + ' ' + (1 + i_choice));
         }
      } else {

         // No feedback given for this choice.  Record with empty "div".
         feedback_divs.push ('');
      }

      // Get hangman answer.
      var hangman_match = choice_html.match (/\[c\*{0,1}\]([^\[]*)/m);
      if (hangman_match) {
         hangman_answer = hangman_match[1];

         // Delete any tags and EOLs and non-breaking spaces.
         hangman_answer = hangman_answer.replace (/<[^>]+>|\n|&nbsp;/g, '');
         hangman_answer = qqc.trim (hangman_answer);
         hangman_answer_length = hangman_answer.length;
         if (debug[0]) {
            console.log ('[process_hangman] hangman_answer:', hangman_answer);
         }
      }

      // Save the answer.
      if (typeof qwizdata[i_qwiz].hangman[i_question] == 'undefined') {
         qwizdata[i_qwiz].hangman[i_question] = {};
      }
      if (! qwizdata[i_qwiz].hangman[i_question].hangman_answer) {
         qwizdata[i_qwiz].hangman[i_question].hangman_answer          = [];
         qwizdata[i_qwiz].hangman[i_question].hangman_final_entry     = [];
         qwizdata[i_qwiz].hangman[i_question].hangman_current_entry   = [];
         qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars = [];
         qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars_before_hint
                                                                      = [];
         qwizdata[i_qwiz].hangman[i_question].hangman_n_hints         = [];
      }
      if (i_choice == 0) {
         qwizdata[i_qwiz].hangman[i_question].n_hangman               = n_hangman;
         qwizdata[i_qwiz].hangman[i_question].hangman_max_hints       = hangman_max_hints;
         qwizdata[i_qwiz].hangman[i_question].n_hangman_done          = 0;
         qwizdata[i_qwiz].hangman[i_question].n_hangman_correct       = 0;
      }

      qwizdata[i_qwiz].hangman[i_question].hangman_answer[i_choice] = hangman_answer;
      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].choices[i_choice] = hangman_answer;
         qw.questions_cards[i_question].correct_choice_fs[i_choice] = 1;
      }
      qwizdata[i_qwiz].hangman[i_question].hangman_n_hints[i_choice] = 0;

      // Thin-space-separated characters, individually underscored (except for
      // non-alpha characters).
      var hangman_final_entry = qqc.create_hangman_entry (hangman_answer);

      // Save.  Substitute a single character (tab) for &ensp; in saved value.
      qwizdata[i_qwiz].hangman[i_question].hangman_final_entry[i_choice] = hangman_final_entry;

      // Create canned feedback if item not given.
      if (! feedback_divs[i_choice]) {
         var response = q.canned_feedback (true);
         feedback_divs[i_choice] = create_feedback_div_html (i_qwiz, i_question, i_choice, response);
         if (set_qwizard_data_b) {
            qw.questions_cards[i_question].feedbacks[i_choice] = response;
         }
      }

      // Just en-spaces for input value -- so user can click anywhere in input
      // text box.  oninput needed for Android Firefox.
      var input_value = new Array (hangman_answer_length).join ('&ensp;');
      var input_focus = q.qwizard_b ? ' onfocus="qwizard.update_hangman_options_menu_choice (this, ' + i_choice + ')"' : '';
      var hangman_span =  '<span class="qwiz_hangman qwiz_hangman_c' + i_choice + '" onkeyup="' + qname + '.hangman_show (this, true)" onmouseenter="' + qname + '.hangman_show (this)" onmouseleave="' + qname + '.hangman_hide (this)">'
                        +    '<span class="hangman_current_entry hangman_entry">'
                        +    '</span>'
                        +    '<input type="text" oninput="' + qname + '.hangman_keyup (this, event,\' ' + input_value + '\', ' + i_qwiz + ', ' + i_question + ', ' + i_choice + ')" value="' + input_value + '" onblur="' + qname + '.hangman_hide (this.parentElement)"' + input_focus + ' />';
      if (hangman_max_hints) {
         var title;
         if (hangman_max_hints < hangman_answer.replace (/[^a-z0-9]/i, '').length) {
            title = ' title="' + T ('You can get') + ' ' + qqc.number_to_word (hangman_max_hints) + ' ' + T ('hint') + ' ' + Tplural ('letter', 'letters', hangman_max_hints) + ', ' + T ('but your answer will count as incorrect') + '"';
         } else {
            title = ' title="' + T ('You can get hints, but your answer will count as incorrect') + '"';
         }
         hangman_span  +=     '<button id="hangman_hint-qwiz' + i_qwiz + '-q' + i_question + '-c' + i_choice + '" class="qwiz_button hangman_hint" onmouseenter="' + qname + '.hangman_show (this.parentElement)" onclick="' + qname + '.hangman_hint (' + i_qwiz + ', ' + i_question + ', ' + i_choice + ')"' + title + '>'
                        +        T ('Hint')
                        +    '</button>\n';
      }

      // "Type letters..." is too much clutter in wizard and in labeled
      // diagrams.
      if (! q.qwizard_b && ! hangman_labeled_diagram_f) {
         hangman_span  +=    '<span class="hangman_type_letters">'
                        +    '</span>';
      }
      hangman_span     +=    '<span class="hangman_status">'
                        +    '</span>'
                        + '</span>';

      if (q.qwizard_b && ! hangman_labeled_diagram_f) {
         hangman_spans.push (hangman_span);
      } else {
         question_htm = question_htm.replace (hangman_re, hangman_span);
      }
      i_choice++;
   }

   // Take out the (possibly "encoded", if old style) [q].
   question_htm = question_htm.replace (/\[(<code><\/code>)*q[^\]]*\]/, '');

   // Check that number of hangman answers (hangman words) matches number of
   // [hangman] entries.
   if (i_choice != n_hangman) {
      errmsgs.push ('Number of [hangman] shortcodes does not match number of hangman words [c].  qwiz: ' + (1 + i_qwiz) + ', ' + T ('question') + ' ' + (1 + i_question));
   }
   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].n_choices = n_hangman;
   }

   // If qwizard, editable div comes in multiple pieces -- before, between, and
   // at end of hangman input shortcodes.
   var new_htm;
   if (q.qwizard_b && ! hangman_labeled_diagram_f) {
      new_htm = create_hangman_textentry_editable_divs (i_qwiz, i_question,
                                                        labeled_diagram_opening_tags, hangman_spans,
                                                        'hangman', hangman_re,
                                                        question_htm);
   } else {
      if (q.qwizard_b) {

         // Hangman labeled diagram for wizard editing -- move hangman fields
         // div outside qwizzled canvas div.
         var canvas_pos = question_htm.search (/<div[^>]+qwizzled_canvas/);
         if (canvas_pos != -1) {
            var hangman_fields_pos = question_htm.search (/<div[^>]+hangman_labeled_diagram_fields/);
            if (hangman_fields_pos != 1) {
               var hangman_fields = qqc.find_matching_block (question_htm.substr (hangman_fields_pos));
               question_htm = question_htm.replace (hangman_fields, '');
               question_htm = question_htm.substr (0, canvas_pos) + hangman_fields
                              + question_htm.substr (canvas_pos);
            }
         }
      }
      var bg_img_style = create_bg_img_style (i_qwiz, i_question);
      new_htm =   '<div id="qwiz' + i_qwiz + '-q' + i_question + '" class="qwizq"' + bg_img_style + '>\n'
                +    labeled_diagram_opening_tags + question_htm;
   }
   if (debug[0]) {
      console.log ('[process_hangman] new_htm:', new_htm);
   }

   // Include clearing div in case image floating left or right (needed to
   // expand parent div and its border).
   new_htm += '<div style="clear: both;"></div>\n';

   // Add feedback div.
   new_htm += feedback_divs.join ('\n');

   // Add hangman message div.
   new_htm += '<div class="qwiz_hangman_msg"></div>';

   // Close question div.
   new_htm += '</div>';

   return new_htm;
}


// -----------------------------------------------------------------------------
this.hangman_show = function (qwiz_hangman_el, keyup_f) {
   if (suppress_hangman_hint_b) {
      suppress_hangman_hint_b = false;
      return false;
   }
   var $qwiz_hangman = $ (qwiz_hangman_el);

   // Hide "Type letters..." callout.
   $qwiz_hangman.find ('span.hangman_type_letters').hide ();


   // If delay hide of hint button and status in progress, cancel.
   clearTimeout (qwiz_hangman_el.i_hint_timeout);

   // If keypress, start a new timeout to hide the hint button and status
   // if no activity (additional keypresses) for a while.
   if (keyup_f) {
      var hide_hint_button = function () {
         $qwiz_hangman.find ('button.hangman_hint, span.hangman_status').fadeOut (1000);
      }
      qwiz_hangman_el.i_hint_timeout = setTimeout (hide_hint_button, 750);
   }

   // Show hint button and status if not yet finished.
   if (! qwiz_hangman_el.done_f) {
      $qwiz_hangman.find ('button.hangman_hint').show ();

      // Show status only if has content.
      var $hangman_status =  $qwiz_hangman.find ('span.hangman_status')
      if ($hangman_status.html ()) {
         $hangman_status.show ();
      }
   }
}


// -----------------------------------------------------------------------------
this.hangman_hide = function (qwiz_hangman_el) {
   var $qwiz_hangman = $ (qwiz_hangman_el);

   // After delay, hide hint button and status.
   var hide_hint_button = function () {
      $qwiz_hangman.find ('button.hangman_hint, span.hangman_status').fadeOut (500);
   }
   qwiz_hangman_el.i_hint_timeout = setTimeout (hide_hint_button, 100);
}


// -----------------------------------------------------------------------------
function create_hangman_textentry_editable_divs (i_qwiz, i_question,
                                                 opening_tags, span_inputs,
                                                 hangman_textentry, re, htm) {
   htm = qqc.shortcodes_to_video_elements (htm);

   var new_htm = [];
   var bg_img_style = create_bg_img_style (i_qwiz, i_question);
   new_htm.push ('<div id="qwiz' + i_qwiz + '-q' + i_question + '" class="qwizq qwizard_line_height"' + bg_img_style + '>\n');

   if (typeof qwizdata[i_qwiz].parts_htm[i_question] == 'undefined') {
      qwizdata[i_qwiz].parts_htm[i_question] = []
   }

   // Find the position of each opening tag/[textentry ...]/[hangman ...]
   // shortcode.
   var t_pos = 0;
   var t_block;
   var i_part = 1;
   var htm_length = htm.length;
   while (true) {
      var remaining_htm = htm.substr (t_pos);
      var ii_pos = remaining_htm.search (re);
      if (ii_pos == -1) {
         break;
      }
      t_pos = t_pos + ii_pos;
      var part_htm = remaining_htm.substr (0, ii_pos);
      if (i_part == 1) {
         part_htm = opening_tags + part_htm;
      }

      // If there's an unmatched <p...> or an unmatched </p> at the end, delete
      // it.  If part is empty add a non-breaking space -- easier to click on.
      part_htm = qqc.remove_unmatched_tag (part_htm, i_part == 1);
      if (part_htm.search (/\S/) == -1) {
         part_htm = '&nbsp;';
      }

      // Save each  part -- reset editable div after editor initialized.
      qwizdata[i_qwiz].parts_htm[i_question][i_part] = part_htm;

      var m = remaining_htm.match (re);
      if (debug[9]) {
         console.log ('[create_hangman_textentry_editable_divs] m[0]:', m[0]);
      }
      t_pos += m[0].length;

      // Continue assembly of pieces.
      new_htm.push ('<div class="qwiz-question qwiz-question-' + hangman_textentry + ' qwiz-parts qwiz-part' + i_part + ' qwiz-inline qwiz_editable" data-i_part="' + i_part + '">\n');
      new_htm.push (   part_htm);
      new_htm.push ('</div>');
      new_htm.push (span_inputs[i_part-1]);

      i_part++;
   }

   // Final part.
   var part_htm = htm.substr (t_pos);
   part_htm = qqc.remove_unmatched_tag (part_htm, false, true);
   if (part_htm.search (/\S/) == -1) {
      part_htm = '&nbsp;';
   }
   qwizdata[i_qwiz].parts_htm[i_question][i_part] = part_htm;
   new_htm.push ('<div class="qwiz-question qwiz-question-' + hangman_textentry + ' qwiz-parts qwiz-part' + i_part + ' qwiz-inline qwiz_editable" data-i_part="' + i_part + '">\n');
   new_htm.push (   part_htm);
   new_htm.push ('</div>');

   return new_htm.join ('');
}


// -----------------------------------------------------------------------------
this.hangman_keyup = function (input_el, event, default_value, i_qwiz, i_question, i_choice) {

   // If no separate intro page, and this is first question, hide qwiz icon.
   if (i_question == 0 && (   q.no_intro_b[i_qwiz]
                           || qwizdata[i_qwiz].n_questions == 1)) {
      $ ('div#icon_qwiz' + i_qwiz).hide ();
   }

   // Get current input, reset to blank default.
   var value = input_el.value;
   input_el.value = default_value;
   if (debug[9]) {
      console.log ('[hangman_keyup] value.charCodeAt:', value.charCodeAt (0), value.charCodeAt (1), value.charCodeAt (2), value.charCodeAt (3));
   }

   // Ignore if not in [A-Za-z0-9].  Typing quickly can produce more than one
   // character.
   var keychars = value.replace (/[^a-z0-9]/gi, '');
   if (keychars == '') {
      return false;
   }
   keychars = keychars.toLowerCase ();
   if (debug[9]) {
      console.log ('[hangman_keyup] keychars:', keychars);
   }

   // Update entry.  If characters are in answer, replace.  If not, add to
   // incorrect-letters list.
   var current_entry = qwizdata[i_qwiz].hangman[i_question].hangman_current_entry[i_choice];
   var final_entry   = qwizdata[i_qwiz].hangman[i_question].hangman_final_entry[i_choice];

   // Loop over characters.
   var done_f;
   var n_chars = keychars.length;
   for (var i=0; i<n_chars; i++) {
      var keychar = keychars[i];
      var done_f = update_hangman_input (keychar, current_entry, final_entry,
                                          i_qwiz, i_question, i_choice, input_el);
      if (done_f) {
         break;
      }
   }

   // Show status; only first 8 incorrect letters.
   if (! done_f) {

      // Hide any previous feedback (other hangman fields).
      $ ('div#qwiz' + i_qwiz + '-q' + i_question + ' div.qwiz-feedback').hide ();
      $ (input_el).parents ('div.qwizq').find('div.qwiz_hangman_msg').hide ();

      if (qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars[i_choice]) {
         var hangman_incorrect_chars_before_hint
                = qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars_before_hint[i_choice];
         var hangman_incorrect_chars_display
            = qqc.create_hangman_incorrect_chars_display (qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars[i_choice], hangman_incorrect_chars_before_hint, true);
         $ (input_el).parents ('span.qwiz_hangman.qwiz_hangman_c' + i_choice).find ('span.hangman_status').html (hangman_incorrect_chars_display).show ();
      }
   }

   return true;
}


// -----------------------------------------------------------------------------
function update_hangman_input (keychar, current_entry, final_entry,
                               i_qwiz, i_question, i_choice, input_el, hint_f) {
   var done_f = false;
   var good_char_b = false;
   var i_pos = -1;

   // Final-entry characters are each between <u></u> pairs.
   var re = new RegExp ('u>' + keychar + '</u', 'i');
   while (true) {
      var m = final_entry.substr (i_pos + 1).match (re);
      if (! m ) break;
      i_pos += m.index + 2;
      current_entry = qqc.setCharAt (current_entry, i_pos + 1, m[0][2]);
      good_char_b = true;
   }
   if (debug[9]) {
      console.log ('[update_hangman_input] keychar:', keychar, ', good_char_b:', good_char_b);
   }

   var hangman = qwizdata[i_qwiz].hangman[i_question];
   var hangman_incorrect_chars = hangman.hangman_incorrect_chars[i_choice];
   if (good_char_b) {
      hangman.hangman_current_entry[i_choice] = current_entry;
      var local_current_entry = current_entry.replace (/\t/g, '&ensp;');
      $ (input_el).parents ('span.qwiz_hangman.qwiz_hangman_c' + i_choice).find ('span.hangman_current_entry').html (local_current_entry);

      // Did this character complete the hangman (all <u>\t</u> filled in)?
      var done_f = current_entry.indexOf ('<u>\t</u>') == -1;
      var all_done_f;
      if (done_f) {
         var n_hangman = hangman.n_hangman;
         hangman.n_hangman_done++;
         all_done_f = hangman.n_hangman_done == n_hangman;

         // Yes.  Don't accept further input.
         $ (input_el).attr ('disabled', true);

         // Set flag so hint not reshown on mouseenter.
         input_el.parentElement.done_f = true;

         // "Correct" if three or fewer incorrect characters and no hints.
         var correct_b = hangman_incorrect_chars.length <= 3
                                      && hangman.hangman_n_hints[i_choice] == 0;
         if (correct_b) {
            hangman.n_hangman_correct++;

            // Show feedback.  Hide others if any.
            if (n_hangman > 1) {
               $ ('div#qwiz' + i_qwiz + '-q' + i_question + ' div.qwiz-feedback').hide ();
               $ ('div#qwiz' + i_qwiz + '-q' + i_question + ' div.qwiz_hangman_msg').hide ();
            }
            $ ('#qwiz' + i_qwiz + '-q' + i_question + '-a' + i_choice).show ();

            // Record statistics if all hangman entries done.
            if (all_done_f && hangman.n_hangman_correct == n_hangman ) {
               if (! q.qwizard_b) {
                  qwizdata[i_qwiz].n_correct++;
               }
            }
         } else {

            // Not correct.  Show message; hide other feedback if any.
            if (n_hangman > 1) {
               $ ('div#qwiz' + i_qwiz + '-q' + i_question + ' div.qwiz-feedback').hide ();
            }
            var msg;
            if (qwizdata[i_qwiz].repeat_incorrect_b) {
               msg = '<hr />' + T ('Now you\'ve got it, but remember your answer -- you\'ll see this word again soon.');
            } else {
               msg = '<hr />' + T ('Sorry, you entered more than three incorrect letters.');
            }
            $ ('div#qwiz' + i_qwiz + '-q' + i_question + ' div.qwiz_hangman_msg').html (msg).show ();

            // Statistics.
            if (all_done_f && hangman.n_hangman_correct != n_hangman ) {
               if (! q.qwizard_b) {
                  qwizdata[i_qwiz].n_incorrect++;
               }
            }
         }
         if (all_done_f) {
            if (! q.qwizard_b) {
               qwizdata[i_qwiz].answered_correctly[i_question] = n_hangman == hangman.n_hangman_correct;
            }

            // Blur focus on entry textbox -- so <Return> can bubble to
            // trigger next-button click.
            input_el.blur ();

            // If recording, jjax call.
            if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {
               var hangman_answer = hangman.hangman_answer[i_choice];
               var data = {q_and_a_text:  btoa (encodeURIComponent (qwizdata[i_qwiz].q_and_a_text[i_question])),
                           q_and_a_crc32: qwizdata[i_qwiz].q_and_a_crc32[i_question],
                           i_question:    qwizdata[i_qwiz].dataset_id[i_question],
                           unit:          qwizdata[i_qwiz].unit[i_question],
                           type:          'hangman',
                           response:      hangman_answer,
                           correct_b:     correct_b ? 1 : '',
                           confirm:       'js'};
               qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
            }

            // If topics, statistics by topics this question.
            update_topic_statistics (i_qwiz, i_question, correct_b);

            update_progress_show_next (i_qwiz);
         } else {

            // Still others to do.  Set focus to first of these.  Reshow "Type
            // letters in box" for any others (focus () will trigger hide of
            // "Type letters...").
            var first_f = true;
            for (var i_choice=0; i_choice<n_hangman; i_choice++) {
               if (hangman.hangman_current_entry[i_choice]
                                     != hangman.hangman_final_entry[i_choice]) {
                  var $qwiz_hangman = $ ('div#qwiz' + i_qwiz + '-q' + i_question + ' span.qwiz_hangman.qwiz_hangman_c' + i_choice);
                  if (first_f) {
                     $qwiz_hangman.find ('input').focus ();
                     first_f = false;
                  } else {
                     $qwiz_hangman.find ('span.hangman_type_letters').show ();
                  }
               }
            }
         }

         // Exit loop over characters.
         done_f = true;
      }
   } else {

      // Letter incorrect.  Update status.  Do if not already there.
      keychar = keychar.toLowerCase ();
      if (hangman_incorrect_chars.indexOf (keychar) == -1) {
         hangman_incorrect_chars += keychar;
      }
      hangman.hangman_incorrect_chars[i_choice] = hangman_incorrect_chars;
      if (debug[9]) {
         console.log ('[hangman_keyup] hangman_incorrect_chars:', hangman_incorrect_chars);
      }
   }

   return done_f;
}


// -----------------------------------------------------------------------------
this.hangman_hint = function (i_qwiz, i_question, i_choice) {

   // Allow specified number of hints -- each hint gives first unfilled-blank
   // letter.
   qwizdata[i_qwiz].hangman[i_question].hangman_n_hints[i_choice]++;
   if (qwizdata[i_qwiz].hangman[i_question].hangman_n_hints[i_choice] > qwizdata[i_qwiz].hangman[i_question].hangman_max_hints) {
      return false;
   } else if (qwizdata[i_qwiz].hangman[i_question].hangman_n_hints[i_choice] == qwizdata[i_qwiz].hangman[i_question].hangman_max_hints) {

      // Disable hint button and close the tooltip.
      $ ('#hangman_hint-qwiz' + i_qwiz + '-q' + i_question + '-c' + i_choice)
         .attr ('disabled', true)
         .addClass ('qwiz_button_disabled');
         //.tooltip ('close');

   } else {

      // Show incorrect characters from here on with red background.
      qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars_before_hint[i_choice]
                  = qwizdata[i_qwiz].hangman[i_question].hangman_incorrect_chars[i_choice].length;
   }

   // Get correct answer.
   var hangman_answer = qwizdata[i_qwiz].hangman[i_question].hangman_answer[i_choice];

   // Get current entry and find position -- in answer -- of first still-blank
   // letter.  Hangman characters in answer are A-Z, a-z, and 0-9.  Hangman
   // characters in current entry are between <u></u> pairs (either character
   // or \t -- indicates blank).
   var current_entry = qwizdata[i_qwiz].hangman[i_question].hangman_current_entry[i_choice];
   var matches = current_entry.match (/<u>.<\/u>/g);
   var i_pos = matches.indexOf ('<u>\t</u>');
   if (debug[9]) {
      console.log ('[hangman_hint] matches:', matches);
      console.log ('[hangman_hint] i_pos:', i_pos);
   }

   if (i_pos != -1) {
      var final_entry = qwizdata[i_qwiz].hangman[i_question].hangman_final_entry[i_choice];

      // Extract the corresponding character.
      matches = final_entry.match (/<u>.<\/u>/g);
      var hint_char = matches[i_pos][3];

      // Substitute into current input.
      var qwizq_id = 'qwiz' + i_qwiz + '-q' + i_question;
      var $qwizq = $ ('div#' + qwizq_id);
      var $hangman_input = $qwizq.find ('span.qwiz_hangman.qwiz_hangman_c' + i_choice + ' input');
      var input_el = $hangman_input[0];
      update_hangman_input (hint_char, current_entry, final_entry,
                            i_qwiz, i_question, i_choice, input_el, true);
      $hangman_input.focus ();
   }
}


// -----------------------------------------------------------------------------
function single_char_textentry_keyup (e) {
   var input_el = e.target;
   if (debug[6]) {
      console.log ('[single_char_textentry_keyup] input_el:', input_el);
   }

   // Get first character.  Ignore if not in alphanumeric.
   var value = input_el.value;
   if (value.search (/[a-z0-9]/i) == -1) {
      input_el.value = '';
      return false;
   }

   // Get i_qwiz from id.  Looks like "textentry-qwiz0-q0".
   var id = input_el.id;
   var i_qwiz = id.match (/qwiz([0-9]+)/)[1];
   if (debug[6]) {
      console.log ('[single_char_textentry_keyup] i_qwiz:', i_qwiz);
   }
   if (qwizdata[i_qwiz].user_question_number == 1) {
      $ ('div#icon_qwiz' + i_qwiz).hide ();
   }

   // Process answer -- same logic as textentry.
   q.textentry_check_answer (i_qwiz, true);
}


// -----------------------------------------------------------------------------
function process_feedback_item (choice_html, i_qwiz, i_question, i_choice) {

   var feedback_start_tags = ['[f]', '[fx]'];
   var feedback_next_tags  = ['[f]', '[fx]', '[x]'];

   if (debug[2]) {
      console.log ('[process_feedback_item] choice_html: ', choice_html);
   }
   var feedback_item_html = qqc.parse_html_block (choice_html, feedback_start_tags,
                                                  feedback_next_tags);
   var feedback_div = '';
   var fx_b;
   if (feedback_item_html != 'NA') {

      // Yes.  Take out of the choice html.
      choice_html = choice_html.replace (feedback_item_html, '');
      if (debug[2]) {
         console.log ('[process_feedback_item] feedback_item_html: ', feedback_item_html);
      }

      // Set flag if [fx].
      fx_b = feedback_item_html.indexOf ('[fx]') != -1;

      // Delete [f] or [fx].
      feedback_item_html = feedback_item_html.replace (/\[fx{0,1}\]/, '');
      feedback_div = create_feedback_div_html (i_qwiz, i_question, i_choice,
                                               feedback_item_html);
   } else {
      feedback_item_html = '';
   }
   if (debug[2]) {
      console.log ('[process_feedback_item] feedback_div:', feedback_div);
      console.log ('[process_feedback_item] choice_html: ', choice_html);
      console.log ('[process_feedback_item] fx_b:        ', fx_b);
   }

   return {'feedback_div':       feedback_div,
           'choice_html':        choice_html,
           'feedback_item_html': feedback_item_html,
           'fx_b':               fx_b};
}


// -----------------------------------------------------------------------------
function process_qwizzled (i_qwiz, i_question, question_htm, opening_tags,
                           question_shortcode) {

   if (debug[0]) {
      console.log ('[process_qwizzled] question_htm: ', question_htm);
   }

   // Labeled diagram.  First see that has been properly processed by qwizzled:
   // no unwrapped labels, and that each label has been associated with a
   // target.
   // DKTMP
   // ...

   // See if labels placement specified by "attribute", e.g., [q labels="top"].
   // Default is "right".  Find attributes, if any.
   var labels_position = '';
   var m = question_shortcode.match (/\[(<code><\/code>)*q([^\]]*)\]/m);
   if (m) {
      var attributes = m[2];
      if (attributes) {

         // Look for "labels=" attribute.  Match regular double-quote, or
         // left- or right-double-quote.
         attributes = qqc.replace_smart_quotes (attributes);
         labels_position = get_attr (attributes, 'labels');
         labels_position = labels_position.toLowerCase ();
         if (debug[0]) {
            console.log ('[process_qwizzled] labels_position:', labels_position);
         }
      }
   }

   // Begin with standard question div, plus beginning of layout table --
   // "canvas" on left, labels on right, and feedback below both.
   // canvas div will get put into <td>...</td>.
   var new_htm  = '<div id="qwiz' + i_qwiz + '-q' + i_question + '" '
   // DKTMP DEDRAG
   if (q.qwizard_b) {
      question_htm = qqc.shortcodes_to_video_elements (question_htm);
   } else {
      new_htm     +=   'onmouseover="' + qname + '.init_drag_and_drop (this)" ';
   }
   //DKTMP qwizzled
   new_htm +=          'class="qwizq qwizzled">'
             +    '<table class="qwizzled_table">'
             +    '<tr class="qwizzled_table">' + question_htm;
   if (debug[0]) {
      console.log ('[process_qwizzled] new_htm: ', new_htm);
   }

   // Turn "canvas" div into table cell.  Find extent of div.
   var canvas_div_pos = new_htm.indexOf ('<div class="qwizzled_canvas');
   if (canvas_div_pos == -1) {
      errmsgs.push (T ('Did not find target "drop-zones" for labels.  Please check that all labels and target "drop zones" were correctly processed and saved during the edit of this page') + '.  qwiz: ' + (i_qwiz + 1) + ', ' + T ('question') + ' ' + (i_question + 1));
      return '';
   }
   var div_html = qqc.find_matching_block (new_htm.substr (canvas_div_pos));
   if (! div_html) {
      errmsgs.push ('Did not find end of image area.  Please check that all labels and target "drop zones" were correctly processed and saved during the edit of this page.');
      return '';
   }
   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].type = 'labeled_diagram';
      qw.questions_cards[i_question].question_text = div_html;
      qw.questions_cards[i_question].labels = [];
      qw.questions_cards[i_question].feedback_corrects = [];
      qw.questions_cards[i_question].feedback_incorrects = [];
   }
   var remaining_htm = new_htm.substr (canvas_div_pos + div_html.length);
   new_htm = new_htm.substr (0, canvas_div_pos + div_html.length);

   // Wrap qwizzled_canvas div in <td>...</td>.  Add id to be able to identify
   // qwizard tinyMCE editor.  Add similar class for sake of
   // qwizard.init_tinymce ().  Keep remaining classes.
   var qwizq_id = 'qwizzled_canvas-qwiz' + i_qwiz + '-q' + i_question;
   var td_canvas = '<td class="qwizzled_table"><div id="' + qwizq_id + '"' + div_html.substring (4) + '</td>';
   td_canvas = td_canvas.replace ('class="', 'class="' + qwizq_id + ' ');

   // Set q_and_a_text whether or not user is logged in -- may log in after
   // setup.
   if (qwizdata[i_qwiz].qrecord_id) {
      if (qqc.isInteger (qwizdata[i_qwiz].dataset_id[i_question])) {
         var question_htm_wo_tags = qqc.remove_tags_eols (question_htm);
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qqc.q_and_a_hash (question_htm_wo_tags);
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = qwiz_crc32 (question_htm);
         if (debug[0]) {
            console.log ('[process_qwizzled] qwizdata[i_qwiz].q_and_a_text[i_question]:', qwizdata[i_qwiz].q_and_a_text[i_question]);
            console.log ('[process_qwizzled] qwizdata[i_qwiz].q_and_a_crc32[i_question]:', qwizdata[i_qwiz].q_and_a_crc32[i_question]);
         }
      } else {

         // Dataset questions: use dataset ID.
         qwizdata[i_qwiz].q_and_a_text[i_question]  = qwizdata[i_qwiz].dataset_id[i_question];
         qwizdata[i_qwiz].q_and_a_crc32[i_question] = 'dataset';
      }
   }

   // Add on label cell and feedback cell.
   var td_labels_style = '';
   if (labels_position == 'left') {
      td_labels_style = ' style="padding-right: 5px;"'
   }
   var td_labels_add_class = '';
   if (labels_position != 'top' && labels_position != 'bottom') {
      td_labels_add_class = ' qwizzled_labels_left_right';
   }
   var td_labels   = '<td class="qwizzled_table qwizzled_labels' + td_labels_add_class + '"' + td_labels_style + '>'
                   +    '<div class="qwizzled_labels_border">'
                   +        'Q-LABELS-Q'
                   +        '<div style="clear: both;"></div>\n'
                   +    '</div>'
                   + '</td>';
   var td_feedback = '<td class="qwizzled_table qwizzled_feedback" colspan="2">QWIZZLED-FEEDBACK-Q</td>';
                 //+ '</tr>'
                 //+ '</table>';

   // Different table setups (after first "<tr">, already in new_htm, above.
   var table_html;
   if (labels_position == "top") {
      table_html =            td_labels + '</tr>'
                   + '<tr class="qwizzled_table">' + td_canvas + '</tr>';
   } else if (labels_position == "bottom") {
      table_html =            td_canvas + '</tr>'
                   + '<tr class="qwizzled_table">' + td_labels + '</tr>';
   } else if (labels_position == "left") {
      table_html =            td_labels + td_canvas + '</tr>';
                   + '<tr class="qwizzled_table">' + td_canvas + '</tr>';
   } else {

      // Default ("right").
      table_html =            td_canvas + td_labels + '</tr>';
                   + '<tr class="qwizzled_table">' + td_canvas + '</tr>';
   }
   if (debug[0]) {
      console.log ('[process_qwizzled] table_html.substr (0, 100):', table_html.substr (0, 100));
   }

   // Feedback is always below.  Finish table.
   table_html +=      '<tr class="qwizzled_table">' + td_feedback + '</tr>'
                 + '</table>';

   new_htm = new_htm.replace (div_html, table_html);

   // Take out the (possibly "encoded", if old style) [q].
   new_htm = new_htm.replace (/\[(<code><\/code>)*q[^\]]*\]/, '');

   if (debug[0]) {
      console.log ('[process_qwizzled] new_htm:', new_htm);
      console.log ('[process_qwizzled] remaining_htm:', remaining_htm);
   }

   // . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
   // Process labels and feedback -- feedback is optional, but must immediately
   // follow label, if given.  Do label by label -- look for feedback associated
   // with each.  First take out [<code></code>l] (old style) or [l].
   remaining_htm = remaining_htm.replace (/\[(<code><\/code>)*l\]/gm, '');
   var label_divs = [];
   var i_label = 0;
   while (true) {
      var label_div_pos = remaining_htm.search (/<div[^>]+class=".*?qwizzled_label/m);
      if (label_div_pos == -1) {
         break;
      }
      var label_div = qqc.find_matching_block (remaining_htm.substr (label_div_pos));
      if (debug[0]) {
         console.log ('[process_qwizzled] label_div:', label_div);
      }

      // Number the labels with id.  Make bulleted list.  Skip initial <div>
      // new opening div with id, here), but keep closing </div>.
      var new_label_div = '<li><div id="label-qwiz' + i_qwiz + '-q' + i_question + '-a' + i_label + '"'
                          + label_div.substr (4) + '</li>';
      label_divs.push (new_label_div);
      if (set_qwizard_data_b) {
         qw.questions_cards[i_question].labels.push (label_div);
      }

      remaining_htm = remaining_htm.replace (label_div, '');
      i_label++;
   }
   if (debug[0]) {
      console.log ('[process_qwizzled] label_divs:', label_divs);
   }

   // Put labels in labels area.
   var label_head =   '<p class="qwizzled_label_head qwizzled_label_head_standard">Move each item to its correct <span class="qwizzled_target_border">place</span></p>\n'
                    + '<p class="qwizzled_label_head qwizzled_label_head_mobile">Click an item, then its correct <span class="qwizzled_target_border">place</span></p>\n'
                    + '<p class="qwizzled_label_head qwizzled_label_head_label_clicked">Click the correct <span class="qwizzled_target_border">place</span> for the label</p>';
   var ul;
   if (labels_position == "top" || labels_position == "bottom") {
      ul = '<ul class="qwizzled_labels qwizzled_labels_inline">';
   } else {
      ul = '<ul class="qwizzled_labels qwizzled_labels_std">';
   }
   new_htm = new_htm.replace ('Q-LABELS-Q', label_head + ul + label_divs.join ('\n') + '</ul>');

   // ..........................................................................
   // Process feedback -- [f*] (label correctly placed) and [fx] (label not
   // correctly placed).
   var feedback_html = remaining_htm;
   var feedback_divs = [];
   var feedback_start_tags = ['[f*]', '[fx]'];
   var feedback_next_tags =  ['[f*]', '[fx]', '[x]', '<div class="qwizzled_question_bottom_border_title"'];
   var i_item = 0;
   while (true) {
      var feedback_item_html
                       = qqc.parse_html_block (feedback_html, feedback_start_tags,
                                               feedback_next_tags);
      if (feedback_item_html == 'NA') {
         break;
      }

      // Take item out of remaining html.
      feedback_html = feedback_html.replace (feedback_item_html, '');

      // Flag which are correct and which not.
      var c_x;
      if (feedback_item_html.search (/\[f\*\]/) != -1) {
         c_x = 'c';
      } else {
         c_x = 'x';
      }

      // Delete [f*] or [fx].
      feedback_item_html = feedback_item_html.replace (/\[f[\*x]\]/, '');

      if (debug[2]) {
         console.log ('[process_qwizzled] feedback_item_html: ', feedback_item_html);
      }

      // Create a div for each.
      feedback_divs.push (
            create_feedback_div_html (i_qwiz, i_question, parseInt (i_item/2, 10),
                                      feedback_item_html, c_x)
      );

      if (set_qwizard_data_b) {
         if (c_x == 'c') {
            qw.questions_cards[i_question].feedback_corrects.push (feedback_item_html);
         } else {
            qw.questions_cards[i_question].feedback_incorrects.push (feedback_item_html);
         }
      }
      i_item++;
   }
   if (debug[0]) {
      console.log ('[process_qwizzled] feedback_divs:', feedback_divs);
      console.log ('[process_qwizzled] feedback_html:', feedback_html);
   }

   // Check that number of feedback items corresponds to number of labels.
   var n_labels = label_divs.length;
   var n_feedback_items = feedback_divs.length;
   if (n_labels*2 != n_feedback_items) {
      errmsgs.push (T ('Number of feedback items') + ' (' + n_feedback_items + ') ' + T ('does not match number of labels') + ' (' + n_labels + ').  qwiz: ' + (1 + i_qwiz) + ', question ' + (1 + i_question) + ' labeled diagram' + '\n'
                    + '(' + T ('There should be two feedback items -- correct and incorrect -- for each label') + ')');
   }

   // Add finished-diagram feedback div.
   var htm = '<div class="qwiz-feedback" id="qwiz' + i_qwiz + '-q' + i_question + '-ff"></div>\n';
   feedback_divs.push (htm);

   new_htm = new_htm.replace ('QWIZZLED-FEEDBACK-Q', feedback_divs.join (''));

   // Close question div.
   new_htm += '</div>\n';

   if (set_qwizard_data_b) {
      qw.questions_cards[i_question].n_labels = n_labels;
   }

   if (debug[2]) {
      console.log ('[process_qwizzled] new_htm: ', new_htm);
   }

   return new_htm;
}


// -----------------------------------------------------------------------------
this.init_drag_and_drop = function (qwizq_elm) {

   if (debug[0]) {
      console.log ('[init_drag_and_drop] qwizq_elm:', qwizq_elm);
   }
   var $qwizq = $ (qwizq_elm);

   // Do this only once for this qwizzled question.  Remove attribute.
   $qwizq.removeAttr ('onmouseover');

   $qwizq.find ('td.qwizzled_labels div.qwizzled_label').each (function () {
      if (debug[0] || debug[8]) {
         console.log ('[init_drag_and_drop] $ (this):', $ (this));
         console.log ('[init_drag_and_drop] \'td.qwizzled_labels div.qwizzled_label\':', $ (this)[0]);
      }

      // Save position relative to document with label.
      var label_offset = $ (this).offset ();
      if (debug[8]) {
         console.log ('[init_drag_and_drop] label_offset:', label_offset);
      }
      $ (this).data ('label_x', label_offset.left).data ('label_y', label_offset.top);

      $ (this).draggable ({
         containment:   $ (this).parents ('table.qwizzled_table'),
         start:         function (event, ui) {

                           // If label previously incorrectly placed, reset
                           // things ("try again").
                           q.label_dragstart ($ (this));
                        },
         /* DKTMP DEDRAG
         stop:          function (event, ui) {
                           // If qwizard, and has a tooltip, re-enable.
                           if (q.qwizard_b) {
                              $qwizzled_highlight_label = $ (this).children ();
                              if ($qwizzled_highlight_label.tooltip ('instance')) {
                                 if (debug[8]) {
                                    console.log ('[init_drag_and_drop > drag stop] $qwizzled_highlight_label:', $qwizzled_highlight_label);
                                 }
                                 $qwizzled_highlight_label.tooltip ('enable');
                              }
                           }
                        },
         */
      }).addClass ('qwizzled_label_unplaced');
   });

   // Targets as drop zones.  Droppable when pointer over target.
   $qwizq.find ('.qwizzled_target').droppable ({
      accept:           '.qwizzled_label',
      hoverClass:       'qwizzled_target_hover',
      drop:             function (event, ui) {

                           // Provide feedback, next-step options.
                           q.label_dropped ($ (this), ui.draggable);
                        },
      tolerance:        'pointer',
   });
}


// -----------------------------------------------------------------------------
// If [h] (or [H]), capture header tag/text, including opening tags before
// [h], up to intro ([i]) if allowed, or question ([q]).  Delete header from
// intro.
function process_header (htm, i_qwiz, i_question, intro_b) {
   var qtags = ['[h]'];
   var qnext_tags = ['[q]', '[q ', '<div class="qwizzled_question'];
   if (intro_b != undefined) {
      qnext_tags.push ('[i]');
   }

   // Global variable.  Limit to first 1000 characters.
   header_html = qqc.parse_html_block (htm.substr (0, 1000), qtags, qnext_tags, true);
   if (header_html != 'NA' && header_html != '') {

      // Error if text before [h].
      var htmx = htm.substr (0, 200);
      htmx = qqc.trim (htmx);
      var i_pos = qqc.opening_tag_shortcode_pos ('[h]', htmx);
      htmx = htmx.substr (i_pos, 5);
      var header_htmlx = header_html.replace (/<br[^>]*>/g, '');
      header_htmlx = qqc.trim (header_htmlx).substr (0, 5);
      if (htmx != header_htmlx) {
         errmsgs.push (T ('Text before header') + ' [h].  qwiz: ' + (i_qwiz + 1));
      }

      // Delete header from htm.
      htm = htm.replace (header_html, '');

      // Delete [h] from header.
      header_html = header_html.replace (/\[h\]/ig, '');

      // If there's an unmatched </p> or the like near the beginning, add a
      // matching opening tag.
      header_html = qqc.balance_closing_tag (header_html);

      // Delete any empty paragraphs or the like.
      header_html = header_html.replace (/<(p|h[1-6])[^>]*><\/(p|h[1-6])>/g, '');

      // Image in the header?  Decode (encoded as <input name="qwiz_img"... />
      // to delay load.
      header_html = qqc.decode_image_tags (header_html);
   }

   return htm;
}


// -----------------------------------------------------------------------------
this.display_summary_and_exit = function (i_qwiz) {

   // Enable to-beginning and back buttons.
   if (! q.qwizard_b) {
      $ ('.bck-question-qwiz' + i_qwiz).css ({color: 'gray'}).addClass ('hover');
   }

   // Nothing (except exit text, if any) if summary="false".
   if (qwizdata[i_qwiz].summary_b) {
      var report_html = [];

      // Overall.
      var n_questions = qwizdata[i_qwiz].n_questions;
      var n_correct   = qwizdata[i_qwiz].n_correct;
      var n_incorrect = qwizdata[i_qwiz].n_incorrect;

      if (qwizdata[i_qwiz].repeat_incorrect_b) {

         var quiz_set = qwizdata[i_qwiz].use_dataset && qwizdata[i_qwiz].dataset_intro_f ? 'set' : 'quiz';
         report_html.push ('<p><b>' + T ('Congratulations, you\'re done!') + '</b></p>');
         if (n_incorrect == 0) {
            report_html.push ('<p>' + T ('In this') + ' ' + qqc.number_to_word (n_questions) + '-' + T ('question') + ' ' + T (quiz_set) + ', ' + T ('you answered every question correctly on the first try!') + '</p>');
         } else {
            report_html.push ('<p>' + T('In finishing this') + ' ' + qqc.number_to_word (n_questions) + '-' + T ('question') + ' ' + T (quiz_set) + ', ' + T ('you entered') + ' ' + qqc.number_to_word (n_incorrect) + ' ' + T ('incorrect') + ' ' + Tplural ('answer', 'answers', n_incorrect) + '.</p>');
         }
      } else {
         if (n_incorrect == 0) {
            report_html.push ('<p>' + T ('Congratulations, you answered all questions correctly') + '.</p>');
         } else {
            report_html.push ('<p>' + T ('Your score is') + ' ' + qqc.number_to_word (n_correct) + ' ' + T ('out of') + ' ' + qqc.number_to_word (n_questions) + ' ' + T ('questions') + '.</p>');
         }
      }

      // By topic.
      var n_topics = 0;
      if (qwizdata[i_qwiz].topics) {
         n_topics = qwizdata[i_qwiz].topics.length;
      }
      if (n_topics == 1) {
         var topic = qwizdata[i_qwiz].topics[0];
         if (topic != 'Other') {
            var all_both_n;
            if (n_questions == 1) {
               report_html.push ('<p>The question was about topic &ldquo;' + topic + '.&rdquo;</p>');
            } else {

               if (n_questions == 2) {
                  all_both_n = T ('Both');
               } else {
                  all_both_n = T ('All') + ' '+ qqc.number_to_word (n_questions);
               }
               report_html.push ('<p>' + all_both_n + ' ' + Tplural ('question', 'questions', n_questions) + ' were about topic &ldquo;' + topic + '.&rdquo;</p>');
            }
         }
      } else if (n_topics > 1 && n_incorrect > 0) {

         // We'll show only topics where user got some incorrect.  See which.
         var incorrect_topics = [];
         for (var i_topic=0; i_topic<n_topics; i_topic++) {
            var topic = qwizdata[i_qwiz].topics[i_topic];
            var n_topic_correct = qwizdata[i_qwiz].topic_statistics[topic].n_correct;
            var n_topic_incorrect = qwizdata[i_qwiz].topic_statistics[topic].n_incorrect;
            var n_topic_items = n_topic_correct + n_topic_incorrect;
            if (n_topic_incorrect > 0) {
               var topic_text = '<strong>' + topic + '</strong>: ' + qqc.number_to_word (n_topic_items) + ' ' + Tplural ('question', 'questions', n_topic_items) + ', ' + qqc.number_to_word (n_topic_incorrect) + ' ' + T ('incorrect');
               incorrect_topics.push (topic_text);
            }
         }
         var n_incorrect_topics = incorrect_topics.length;
         var topic_list_html = '<p class="topic_list">';
         if (n_incorrect_topics > 1) {
            topic_list_html += T ('These are the topics of questions that you answered incorrectly') + ':<br />';
            for (var i=0; i<n_incorrect_topics; i++) {
               incorrect_topics[i] = '&bull; ' + incorrect_topics[i];
            }
         } else {
            topic_list_html += T ('The topic of the only question you answered incorrectly is' + ' ');
         }
         topic_list_html += incorrect_topics.join ('; ') + '.';
         topic_list_html += '</p>';
         report_html.push (topic_list_html);
      }

      // Place in report div.
      $ ('#summary_report-qwiz' + i_qwiz).html (report_html.join ('\n'));
   }

   // If qwizard, and editor hasn't been initiated for exit text, do so.
   var $summary = $ ('#summary-qwiz' + i_qwiz);
   if (q.qwizard_b) {
      if ($summary.find ('div[contenteditable]').length == 0) {
         qwizard.init_tinymce ('div#qwiz_exit-qwiz' + i_qwiz + '.qwiz_editable');
      }
   }

   // Show summary div.  Set i_question as indicator for re-display if login.
   var $qwiz_img = $summary.find ('input[name="qwiz_img"]');
   if ($qwiz_img.length) {
      $qwiz_img.changeElements ('img');
   }
   $summary.show ();
   qwizdata[i_qwiz].i_question = n_questions;
}


// -----------------------------------------------------------------------------
function check_qwiz_tag_pairs (htm) {
   var new_htm = '';

   // Match "[qwiz]" or "[/qwiz]".
   var matches = htm.match (/\[qwiz|\[\/qwiz\]/gm);
   if (matches) {
      var n_tags = matches.length;
      var error_b = false;

      if (n_tags % 2 != 0) {
         error_b = true;
      } else {

         // Check proper pairs.
         for (var i=0; i<n_tags; i++) {
            if (i % 2 == 0) {
               if (matches[i] != '[qwiz') {
                  error_b = true;
                  break;
               }
            } else {
               if (matches[i] != '[/qwiz]') {
                  error_b = true;
                  break;
               }
            }
         }
      }
      if (error_b) {

         // If we're inside an excerpt, no error.  Delete from '[qwiz]' up to
         // '<p class="more-link' if possible,  In any event non-null return
         // signals not to process.
         if (htm.indexOf ('more-link') != -1
                               || htm.indexOf ('search-entry-readmore') != -1) {

            var pos_qwiz = htm.indexOf ('[qwiz');
            var pos_more = htm.search (/<[pa] class="more-link/);
            if (pos_more != -1) {
               new_htm = htm.substring (0, pos_qwiz) + htm.substr (pos_more);
            } else {
               new_htm = htm;
            }
         } else {
            errmsgs.push (T ('Unmatched [qwiz] - [/qwiz] pairs.'));
         }
      }
   }

   return new_htm;
}


// -----------------------------------------------------------------------------
function create_radio_button_html (i_qwiz, i_question, i_choice, choice_tag) {
   var htm = '';

   // Data attribute to mark correct choice.
   var data_correct = '';
   var correct = 0;
   if (choice_tag == '[c*]') {
      data_correct = 'data-correct="1" ';
      correct = 1;
   }
   var title = '';
   var onclick = ' onclick="' + qname + '.process_choice (\'qwiz' + i_qwiz + '-q' + i_question + '-a' + i_choice + '\')"';
   if (q.qwizard_b) {
      title = ' title="Click to see feedback for this answer choice"';
   }
   htm += '<input type="radio" id="radio-qwiz' + i_qwiz + '-q' + i_question + '-a' + i_choice + '" name="qwiz' + i_qwiz + '-q' + i_question + '" ' + data_correct + ' style="border: none;"' + onclick + title + ' />\n';

   if (debug[1]) {
      console.log ('[create_radio_button_html] htm: ', htm);
   }

   return {'correct': correct,
           'htm':     htm};
}


// -----------------------------------------------------------------------------
this.process_choice = function (feedback_id, correct_b, got_it_more_practice_b) {

   // Hide others, show this one.
   // feedback_id looks like:  qwiz0-q0-a0   or  mobile_qwiz0-q0-a0
   //                          (qwiz #, question #, answer #).
   // Identify quiz and question.  Greedy search matches to final "-".
   var matches = feedback_id.match (/(.*)-/);
   var qwizq_id = matches[1];

   // Qwiz number.  Non-greedy search.
   var qwiz_id = feedback_id.match (/(qwiz.*?)-/)[1];
   i_qwiz = parseInt (qwiz_id.substr (4), 10);
   if (debug[0]) {
      console.log ('[process_choice] feedback_id: ', feedback_id, ', qwizq_id: ', qwizq_id, ', i_qwiz: ', i_qwiz);
   }

   // If recording and this is first interaction (no-intro, single-question
   // quiz) record as start time.
   if (qwizdata[i_qwiz].record_start_b && document_qwiz_user_logged_in_b) {
      qwizdata[i_qwiz].record_start_b = false;
      var data = {qrecord_id_ok: qwizdata[i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
      qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
   }

   // If mobile, hide choices except the one picked, then show feedback.
   var disabled;
   var mobile_b = feedback_id.substr (0, 6) == 'mobile';
   if (mobile_b) {
      $ ('#' + qwizq_id + ' li.mobile_choice').hide ();
      $ ('#' + qwizq_id + ' .qwiz-feedback').hide ();
      $ ('#' + feedback_id).show ();
      $choice = $ ('#choice-' + feedback_id);
      correct_b = !! $choice.data ('correct');
      $choice.show ();
      disabled = '';
   } else {
      disabled = $ ('input[name=' + qwizq_id + ']').attr ('disabled');
   }

   // Don't do if already disabled.
   if (disabled != 'disabled') {

      // If mobile, process regular choices, too, in case user exits mobile
      // mode.  mobile_qwiz0...
      //        0----+----1
      if (mobile_b) {
         feedback_id = feedback_id.substr (7);
         qwizq_id    = qwizq_id.substr (7);
      }
      $ ('#' + qwizq_id + ' .qwiz-feedback').hide ();
      $ ('#' + feedback_id).show ();

      // In case clicked on text rather than radio, show radio as clicked.
      // If "show-the-answer" question, substituted button for radio, so skip.
      // In "show-the-answer" case, correct_b set via argument.
      var elm = document.getElementById ('radio-' + feedback_id);
      if (elm) {

         elm.checked = true;

         // Don't disable further radio clicks if qwizard.
         if (! q.qwizard_b) {
            $ ('input[name=' + qwizq_id + ']').attr ('disabled', true);
         }

         // Also, don't show pointer cursor on paragraphs, and turn off highlighting.
         $ ('.choices-' + qwizq_id).on('mouseover', function () {
            $ (this).css ({'cursor': 'text', 'color': 'black'});
         });

         // undefined -> boolean false.
         correct_b = !! $ ('#radio-' + feedback_id).data ('correct');
      }

      // Record statistics.  Don't do if qwizard.
      var i_question = feedback_id.match (/-q([0-9]+)-/)[1];
      if (! q.qwizard_b) {
         qwizdata[i_qwiz].answered_correctly[i_question] = correct_b;
         if (correct_b) {
            qwizdata[i_qwiz].n_correct++;
         } else {

            // Record number of incorrect responses.
            qwizdata[i_qwiz].n_incorrect++;
         }
      }

      // If recording, jjax call.
      if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {

         // Include which choice was made and text of choice.  Take tags out
         // of choice text and replace non-breaking spaces and EOLs with space,
         // multiple spaces with single space, trim.
         var i_choice = feedback_id.match(/-a([0-9]+)$/)[1];
         var choice_text = $ ('#' + qwizq_id + ' .choice-' + feedback_id).html ();
         if (choice_text) {
            choice_text = qqc.remove_tags_eols (choice_text);
         } else {

            // Must be a "show-the-answer" question/button.  Get button text
            // and feedback text.
            choice_text = qqc.remove_tags_eols ($ ('#' + qwizq_id + ' button.qwiz_button').html ())
                          + '\t'
                          + qqc.remove_tags_eols ($ ('#' + feedback_id).html ());
         }

         // Delay this a bit (in case race with record-start, above).
         var delay_jjax = function () {
            var data = {q_and_a_text:  btoa (encodeURIComponent (qwizdata[i_qwiz].q_and_a_text[i_question])),
                        q_and_a_crc32: qwizdata[i_qwiz].q_and_a_crc32[i_question],
                        i_question:    qwizdata[i_qwiz].dataset_id[i_question],
                        unit:          qwizdata[i_qwiz].unit[i_question],
                        type:          'multiple_choice',
                        response:      choice_text,
                        i_choice:      i_choice,
                        correct_b:     correct_b ? 1 : '',
                        confirm:       'js'};
            qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
         }
         setTimeout (delay_jjax, 1000);
      }

      // If topics, statistics by topics this question.
      update_topic_statistics (i_qwiz, i_question, correct_b);

      // If no separate intro page, hide qwiz icon now.
      if (i_question == 0 && (q.no_intro_b[i_qwiz]
                                        || qwizdata[i_qwiz].n_questions == 1)) {
         $ ('div#icon_qwiz' + i_qwiz).hide ();
      }

      // Update progress and show next button.  If "show-the-answer" question
      // (user clicked either "Got it" or "Need more practice"), update progress
      // and move to next question.
      if (got_it_more_practice_b) {

         // Hide "Got it" and "Need more practice" buttons.
         $ ('#' + document_qwiz_mobile + 'show_answer_got_it_or_not-qwiz' + i_qwiz + '-q' + i_question).hide ();

         // Show the "show-the-answer" button in case come around again.
         $ ('#qwiz' + i_qwiz + '-q' + i_question).find ('button.show_the_answer').show ();

         // Do this later if qwizard.
         if (! q.qwizard_b) {
            q.display_progress (i_qwiz);
         }
         q.next_question (i_qwiz);
      } else {
         update_progress_show_next (i_qwiz);
      }
   }
};


// -----------------------------------------------------------------------------
// "Show-the-answer" button clicked (in a multi-question quiz).  Show feedback
// and present "Got it" and "Need more practice" buttons in place of "Next
// question".
this.show_answer_got_it_or_not = function (i_qwiz, i_question, show_me_button_el) {

   // Hide "show the answer" button.
   if (! q.qwizard_b) {
      show_me_button_el.style.display = 'none';
   }

   // Show feedback.
   $ ('#' + document_qwiz_mobile + 'qwiz' + i_qwiz + '-q' + i_question + '-a0').show ();

   // Show "Got it" and "Need more practice" buttons.
   $ ('#' + document_qwiz_mobile + 'show_answer_got_it_or_not-qwiz' + i_qwiz + '-q' + i_question).show ();
}


// -----------------------------------------------------------------------------
function create_got_it_or_not (mobile_, i_qwiz, i_question) {

   var htm = '';
   htm += '<div id="' + mobile_ + 'show_answer_got_it_or_not-qwiz' + i_qwiz + '-q' + i_question + '" class="show_answer_got_it_or_not">\n';
   htm +=    '<button class="qwiz_button" onclick="' + qname + '.process_choice (\'qwiz' + i_qwiz + '-q' + i_question + '-a0\', false, true)">\n';
   htm +=       T ('Need more practice');
   htm +=    '</button>\n';
   htm +=    '&emsp;';
   htm +=    '<button class="qwiz_button" onclick="' + qname + '.process_choice (\'qwiz' + i_qwiz + '-q' + i_question + '-a0\', true, true)">\n';
   htm +=       T ('Got it!');
   htm +=    '</button>\n';
   htm += '</div>\n';

   return htm;
}


// -----------------------------------------------------------------------------
function update_topic_statistics (i_qwiz, i_question, correct_b) {
   var question_topics = qwizdata[i_qwiz].question_topics[i_question];
   if (question_topics) {
      for (var ii=0; ii<question_topics.length; ii++) {
         var topic = question_topics[ii];
         if (correct_b) {
            qwizdata[i_qwiz].topic_statistics[topic].n_correct++;
         } else {
            qwizdata[i_qwiz].topic_statistics[topic].n_incorrect++;
         }
      }
   }
}


// -----------------------------------------------------------------------------
function update_progress_show_next (i_qwiz) {
   if (qwizdata[i_qwiz].n_questions > 1 || qwizdata[i_qwiz].use_dataset) {
      q.display_progress (i_qwiz);

      // "Next" button.  If finished, change text.
      var n_done = qwizdata[i_qwiz].n_correct;
      if (! qwizdata[i_qwiz].repeat_incorrect_b) {
         n_done += qwizdata[i_qwiz].n_incorrect;
      }
      if (n_done == qwizdata[i_qwiz].n_questions) {
         if (qwizdata[i_qwiz].summary_b) {
            $ ('#next_button_text-qwiz' + i_qwiz).html (T ('View summary report'));
         } else {
            $ ('#summary-qwiz' + i_qwiz).show ();
         }

         // If recording, and either a use_dataset="..." quiz (in which case
         // the php script will check whether all questions have now been
         // completed correctly) or a regular quiz where all questions have
         // been answered correctly, then check -- and if all questions
         // correct -- record first completion time for sake of leaderboard
         // data.  Wait a bit so previous correct answer can be recorded.
         if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {
            if (   qwizdata[i_qwiz].use_dataset
                || qwizdata[i_qwiz].repeat_incorrect_b
                || qwizdata[i_qwiz].n_incorrect == 0  ) {

               // Closure.
               var wait_completed = function () {
                  var data = {type: 'completed', confirm: 'js'};
                  qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
               }
               setTimeout (wait_completed, 2000);
            }
         }
      }

      // If mobile, update position in case some window change (e.g.,
      // orientation).
      if (n_done < qwizdata[i_qwiz].n_questions || qwizdata[i_qwiz].summary_b) {
         q.position_show_next_button (i_qwiz);
      }

      // Record state.
      qwizdata[i_qwiz].next_button_show_b = true;
   }
   if (qwizdata[i_qwiz].n_questions == 1) {

      // Single-question quiz.  Show exit html if any.
      $( '#qwiz' + i_qwiz + ' div.single-question_exit').show ();
   }
}


// -----------------------------------------------------------------------------
this.display_progress = function (i_qwiz, came_from_start_b) {
   if (debug[0]) {
      console.log ('[display_progress] qwizdata[i_qwiz].i_question:', qwizdata[i_qwiz].i_question, ', came_from_start_b:', came_from_start_b);
   }

   var progress_html;
   var n_attempts = qwizdata[i_qwiz].n_correct + qwizdata[i_qwiz].n_incorrect;
   var n_done = qwizdata[i_qwiz].n_correct;
   if (! qwizdata[i_qwiz].repeat_incorrect_b) {
      n_done += qwizdata[i_qwiz].n_incorrect;
   }

   if (q.qwizard_b) {
      if (qwizdata[i_qwiz].i_question == -1 && ! came_from_start_b) {
         progress_html = T ('Questions in this quiz:') + ' ' + qwizdata[i_qwiz].n_questions;
      } else {
         var display_i_question = qwizdata[i_qwiz].i_question + 1;
         if (came_from_start_b) {
            display_i_question++;
         }
         progress_html = 'Question ' + display_i_question + ' of ' + qwizdata[i_qwiz].n_questions;
      }
   } else {
      var n_to_go = qwizdata[i_qwiz].n_questions - n_done;
      if (n_attempts == 0) {
         progress_html = T ('Questions in this quiz:') + ' ' + n_to_go;
      } else {
         var questions;
         var incorrect;
         if (document_qwiz_mobile) {
            questions = T ('Qs');
            incorrect = T ('not');
         } else {
            questions = T ('questions');
            incorrect = T ('incorrect');
         }
         progress_html = qwizdata[i_qwiz].n_questions + ' ' + questions + ', ' + n_attempts + ' ' + Tplural ('response', 'responses', n_attempts) + ', ' + qwizdata[i_qwiz].n_correct + ' ' + T ('correct') + ', ' + qwizdata[i_qwiz].n_incorrect + ' ' + incorrect + ', ' + n_to_go + ' ' + T ('to go');
      }
   }
   $ ('#progress-qwiz' + i_qwiz).html (progress_html).show ();
}


// -----------------------------------------------------------------------------
function display_qwizzled_progress (i_qwiz) {

   var i_question  = qwizdata[i_qwiz].i_question + 1;
   var n_questions = qwizdata[i_qwiz].n_questions;
   var progress_html = '';
   if (n_questions > 1 && ! qwizdata[i_qwiz].random_b) {
      progress_html = 'Q #' + i_question + '/' + n_questions + '; ';
   }
   progress_html += 'Correctly labeled ' + qwizdata[i_qwiz].n_labels_correct + ' out of ' + qwizdata[i_qwiz].n_label_targets + ' items';

   // Do show () in case single-question qwiz.
   $ ('#progress-qwiz' + i_qwiz).html (progress_html).show ();
}


// -----------------------------------------------------------------------------
function create_feedback_div_html (i_qwiz, i_question, i_item, item_html, c_x) {

   var local_c_x = '';
   if (c_x != undefined) {
      local_c_x = c_x;
   }
   var htm = '<div id="qwiz' + i_qwiz + '-q' + i_question + '-a' + i_item + local_c_x + '" class="qwiz-feedback">\n';
   if (! local_c_x) {
      htm += '<hr style="margin: 0px;" />\n';
   }
   var classname = '';
   if (local_c_x) {

      // Qwizzled feedback.
      if (local_c_x == 'c') {
         classname = 'qwizzled-correct_feedback';
      } else {
         classname = 'qwizzled-incorrect_feedback';
      }
      htm += '<div class="' + classname + ' qwiz_editable" data-i_choice="' + i_item + '">' + item_html + '</div>';
   } else {

      // Multiple-choice question feedback.
      if (q.qwizard_b) {
         item_html = qqc.shortcodes_to_video_elements (item_html);
      }

      // Item html may include [restart].  Convert to button to re-try this
      // question.
      item_html = create_restart_button (i_qwiz, item_html, true);
      htm += '<span class="qwiz-feedback-span qwiz_editable" data-i_choice="' + i_item + '">' + item_html + '</span>';
   }

   // Include clearing div in case image floating left or right.
   htm += '<div style="clear: both;"></div>\n';
   htm += '</div>\n';
   if (debug[2]) {
      console.log ('[create_feedback_div_html] htm: ', htm);
   }

   return htm;
}


// -----------------------------------------------------------------------------
this.canned_feedback = function (correct_b) {

   var response;
   if (correct_b) {
      var i = Math.floor (Math.random () * correct.length);
      response = correct[i];
   } else {
      var i = Math.floor (Math.random () * incorrect.length);
      response = incorrect[i];
   }
   response = '<p><strong>' + response + '</strong></p>';

   if (debug[0]) {
      console.log ('[canned_feedback] response:', response);
   }
   return response;
}


// -----------------------------------------------------------------------------
var find_matching_terms = function (request, response) {

   // If this is first question, hide qwiz icon.
   if (qwizdata[textentry_i_qwiz].user_question_number == 1) {
      $ ('div#icon_qwiz' + textentry_i_qwiz).hide ();
   }

   var entry = request.term.toLowerCase ();
   var entry_metaphone = qqc.metaphone (entry);
   if (debug[6]) {
      console.log ('[find_matching_terms] entry_metaphone; ', entry_metaphone);
   }

   // See if first character of entry metaphone matches first character of any
   // answer metaphone.  If so, determine shortest answer metaphone that
   // matches.
   var required_entry_length = 100;
   var required_metaphone_length = 100;
   var i_question = qwizdata[textentry_i_qwiz].i_question;
   var minlength = qwizdata[textentry_i_qwiz].textentry[i_question].textentry_minlength;
   for (var i=0; i<textentry_answer_metaphones[textentry_i_qwiz].length; i++) {
      if (entry[0] == textentry_answers[textentry_i_qwiz][i][0].toLowerCase ()) {
         required_entry_length = Math.min (required_entry_length, textentry_answers[textentry_i_qwiz][i].length);
         if (debug[6]) {
            console.log ('[find_matching_terms] entry[0]:', entry[0], ', textentry_answers[textentry_i_qwiz][i][0]:', textentry_answers[textentry_i_qwiz][i][0]);
         }
      }
      if (entry_metaphone[0] == textentry_answer_metaphones[textentry_i_qwiz][i][0]) {
         required_metaphone_length = Math.min (required_metaphone_length, textentry_answer_metaphones[textentry_i_qwiz][i].length);
         if (debug[6]) {
            console.log ('[find_matching_terms] textentry_answer_metaphones[textentry_i_qwiz][i]:', textentry_answer_metaphones[textentry_i_qwiz][i], ', required_metaphone_length:', required_metaphone_length);
         }
      }
   }
   if (required_entry_length == 100) {
      required_entry_length = minlength;
   } else {
      required_entry_length -= 2;
      required_entry_length = Math.min (minlength, required_entry_length);
   }

   if (required_metaphone_length != 100) {
      required_metaphone_length--;
      if (required_metaphone_length < 2) {
         required_metaphone_length = 2;
      } else if (required_metaphone_length > 4) {
         required_metaphone_length = 4;
      }
   }
   if (debug[6]) {
      console.log ('[find_matching_terms] required_entry_length:', required_entry_length, ', required_metaphone_length:', required_metaphone_length);
   }

   // Entry consisting of repeated single character doesn't count as "long".
   // Replace any three or more of same character in a row with just one.
   var deduped_entry = entry.replace (/(.)\1{2,}/gi, '\$1');
   if (deduped_entry.length < required_entry_length && entry_metaphone.length < required_metaphone_length) {
      textentry_matches[textentry_i_qwiz] = [];
      lc_textentry_matches[textentry_i_qwiz] = [];
      find_matching_terms2 (response, deduped_entry);

   } else {
      if (debug[6]) {
         console.log ('[find_matching_terms] request.term:', request.term,', entry_metaphone:', entry_metaphone, ', entry_metaphone.length:', entry_metaphone.length);
      }

      // Combine words from terms lists with those from full dictionary.  First,
      // terms lists.
      textentry_matches[textentry_i_qwiz]
            = $.map (current_question_textentry_terms_metaphones[textentry_i_qwiz],
                     function (term_i) {
         var ok_f;
         if (entry_metaphone == '') {

            // A number, or perhaps other non-alpha characters.  Match similar
            // terms.
            ok_f = term_i[1] == ''
                             || term_i[0].toLowerCase ().indexOf (entry) === 0;
         } else {
            ok_f = term_i[1].indexOf (entry_metaphone) === 0
                             || term_i[0].toLowerCase ().indexOf (entry) === 0;
         }
         if (ok_f) {
            if (debug[6]) {
               console.log ('[find_matching_terms] term_i:', term_i);
            }
            return term_i[0];
         }
      });
      if (debug[6]) {
         console.log ('[find_matching_terms] textentry_matches[textentry_i_qwiz]:', textentry_matches[textentry_i_qwiz]);
      }

      // Add dictionary result, if flag set.
      if (qwizdata[textentry_i_qwiz].textentry[i_question].use_dict_b) {

         // Add terms to dictionary processing.
         var plural_f = qwizdata[textentry_i_qwiz].textentry[i_question].textentry_plural_b ? 1 : 0;
         var data = {action:          'textentry_suggestions',
                     entry:           encodeURIComponent (entry),
                     entry_metaphone: encodeURIComponent (entry_metaphone),
                     n_hints:         qwizdata[textentry_i_qwiz].textentry_n_hints,
                     terms:           btoa (encodeURIComponent (JSON.stringify (textentry_matches[textentry_i_qwiz]))),
                     plural_f:        plural_f};

         // If on qwizcards wizard page, go direct to WordPress db.
         var url;
         if (qwizcards_page_f) {
            url = qqc.get_qwiz_param ('server_loc', 'http://qwizcards.com/admin') + '/qwizard_textentry_suggestions.php';
         } else {
            url = qqc.get_qwiz_param ('ajaxurl', '');
         }
         $.ajax ({
            type:       'POST',
            url:        url,
            data:       data,
            dataType:   'json',
            error:      function (xhr, desc) {
                           if (debug[0]) {
                              console.log ('[find_matching_terms] error desc:', desc);
                           }
                        },
            success:    function (data) {
                           textentry_matches[textentry_i_qwiz] = data;
                           find_matching_terms2 (response, deduped_entry);
                        }
         });
      } else {
         find_matching_terms2 (response, deduped_entry);
      }
   }
}


// -----------------------------------------------------------------------------
function find_matching_terms2 (response, deduped_entry) {

   if (textentry_matches[textentry_i_qwiz].length) {
      lc_textentry_matches[textentry_i_qwiz]
         = textentry_matches[textentry_i_qwiz].map (function (item) {
                                                       return item.toLowerCase ();
                                                    });
      if (debug[6]) {
         console.log ('[find_matching_terms2] textentry_matches[textentry_i_qwiz]:', textentry_matches[textentry_i_qwiz]);
      }
   }

   // If entry length is minlength (default 3) or more, and matches-list does
   // not include first correct answer, and haven't used up hints, enable hint.
   if (debug[6]) {
      console.log ('[find_matching_terms2] deduped_entry.length: ', deduped_entry.length, ', textentry_matches[textentry_i_qwiz].length: ', textentry_matches[textentry_i_qwiz].length, ', qwizdata[textentry_i_qwiz].textentry_n_hints: ', qwizdata[textentry_i_qwiz].textentry_n_hints);
   }
   var i_question = qwizdata[textentry_i_qwiz].i_question;
   var minlength = qwizdata[textentry_i_qwiz].textentry[i_question].textentry_minlength;
   if (deduped_entry.length >= minlength && qwizdata[textentry_i_qwiz].textentry_n_hints < 5) {
      var lc_first_correct_answer = qwizdata[textentry_i_qwiz].textentry[i_question].first_correct_answer.toLowerCase ();
      if (typeof (lc_textentry_matches[textentry_i_qwiz]) == 'undefined'
            || lc_textentry_matches[textentry_i_qwiz].indexOf (lc_first_correct_answer) == -1) {
         $ ('#textentry_check_answer_div-qwiz' + textentry_i_qwiz + '-q' + i_question + ' button.qwiz_textentry_hint')
            .removeAttr ('disabled')
            .removeClass ('qwiz_button_disabled')
            .css ({display: 'inline-block'});
         if (q.qwizard_b) {
            $ ('#qwiz' + textentry_i_qwiz + '-q' + i_question + ' .qwiz-feedback').hide ();
         }
      }
   }
   response (textentry_matches[textentry_i_qwiz]);
}


// -----------------------------------------------------------------------------
// When menu closed: if current entry doesn't fully match anything on the last
// set of matches, disable "Check answer".
function menu_closed (e) {

   var lc_entry = e.target.value.toLowerCase ();
   var i_question = qwizdata[textentry_i_qwiz].i_question;

   // Since triggered by keyup, if entry is shorter than number of hints (user
   // has deleted characters), restore to hint value.
   var n_hints = qwizdata[textentry_i_qwiz].textentry_n_hints;
   if (lc_entry.length < n_hints) {
      var textentry_hint_val = qwizdata[textentry_i_qwiz].textentry[i_question].first_correct_answer.substr (0, n_hints);
      e.target.value = textentry_hint_val;
   }

   // Do only if "Check answer" not already disabled.
   if (! qwizdata[textentry_i_qwiz].check_answer_disabled_b) {
      if (debug[6]) {
         console.log ('[menu_closed] textentry_matches[textentry_i_qwiz]: ', textentry_matches[textentry_i_qwiz]);
      }
      if (typeof (lc_textentry_matches[textentry_i_qwiz]) == 'undefined'
           || lc_textentry_matches[textentry_i_qwiz].indexOf (lc_entry) == -1) {
         $ ('#textentry_check_answer_div-qwiz' + textentry_i_qwiz + '-q' + i_question + ' button.textentry_check_answer')
            .addClass ('qwiz_button_disabled')
            .html (qwizdata[textentry_i_qwiz].check_answer);
         qwizdata[textentry_i_qwiz].check_answer_disabled_b = true;
         if (q.qwizard_b) {
            $ ('#qwiz' + textentry_i_qwiz + '-q' + i_question + ' .qwiz-feedback').hide ();
         }
      }
   }

   // Since done on keyup (that is, if any typing), cancel automatic
   // presentation (if in progress) if have minlength characters.
   // Do only if there is a timeout in progress.
   if (show_hint_timeout[textentry_i_qwiz]) {
      var $textentry = $ ('#textentry-qwiz' + textentry_i_qwiz + '-q' + i_question);
      var n_chars = $textentry.val ().length;

      var minlength = qwizdata[textentry_i_qwiz].textentry[i_question].textentry_minlength;
      if (n_chars >= minlength) {
         clearTimeout (show_hint_timeout[textentry_i_qwiz]);
         show_hint_timeout[textentry_i_qwiz] = 0;
      }
   }
}


// -----------------------------------------------------------------------------
// When suggestion menu shown: (1) if the matches list shown includes the first
// correct answer, then set flag that hint not needed; (2) if current entry
// _fully_ matches anything on the matches list shown, then enable "Check
// answer"; otherwise disable "Check answer".
function menu_shown (e) {

   // If recording and this is first interaction (no-intro, single-question
   // quiz), record as start time.
   if (qwizdata[textentry_i_qwiz].record_start_b && document_qwiz_user_logged_in_b) {
      qwizdata[textentry_i_qwiz].record_start_b = false;
      var data = {qrecord_id_ok: qwizdata[textentry_i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
      qqc.jjax (qname, textentry_i_qwiz, qwizdata[textentry_i_qwiz].qrecord_id, 'record_response_v3', data);
   }

   // Lowercase entry and matches list.
   var lc_entry = e.target.value.toLowerCase ();

   // Does matches list include first correct answer?
   var i_question = qwizdata[textentry_i_qwiz].i_question;
   var lc_first_correct_answer = qwizdata[textentry_i_qwiz].textentry[i_question].first_correct_answer.toLowerCase ();
   if (lc_textentry_matches[textentry_i_qwiz].indexOf (lc_first_correct_answer) != -1) {
      $ ('#textentry_check_answer_div-qwiz' + textentry_i_qwiz + '-q' + i_question + ' button.qwiz_textentry_hint')
         .attr ('disabled', true)
         .addClass ('qwiz_button_disabled');
   }
   if (lc_textentry_matches[textentry_i_qwiz].indexOf (lc_entry) != -1) {
      $ ('#textentry_check_answer_div-qwiz' + textentry_i_qwiz + '-q' + i_question + ' button.textentry_check_answer')
         .removeClass ('qwiz_button_disabled')
         .html (T ('Check answer'));
      qwizdata[textentry_i_qwiz].check_answer_disabled_b = false;
   } else {
      $ ('#textentry_check_answer_div-qwiz' + textentry_i_qwiz + '-q' + i_question + ' button.textentry_check_answer')
         .addClass ('qwiz_button_disabled')
         .html (qwizdata[textentry_i_qwiz].check_answer);
      qwizdata[textentry_i_qwiz].check_answer_disabled_b = true;
   }
}


// -----------------------------------------------------------------------------
this.textentry_check_answer = function (i_qwiz, single_char_b, qwizard_i_choice) {
   var i_question = qwizdata[i_qwiz].i_question;
   var $textentry = $ ('#textentry-qwiz' + i_qwiz + '-q' + i_question);
   if (debug[6]) {
      entry = $textentry.val ();
      console.log ('[textentry_check_answer] $textentry.val ():', $textentry.val ());
   }


   if (! qwizdata[i_qwiz].textentry[i_question].textentry_suggest_b) {

      // No-suggestions question.  If nothing entered, do nothing.  First, if
      // recording, and this is first interaction (no-intro, single-question
      // quiz), record as start time.
      if (qwizdata[i_qwiz].record_start_b && document_qwiz_user_logged_in_b) {
         qwizdata[i_qwiz].record_start_b = false;
         var data = {qrecord_id_ok: qwizdata[i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
         qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
      }

      var entry = $textentry.val ();
      if (entry == '') {
         return false;
      }
   }
   if (! single_char_b) {
      if (! q.qwizard_b) {
         if (qwizdata[i_qwiz].check_answer_disabled_b) {
            alert (Tcheck_answer_message);

            // Show hint button.
            $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question + ' button.qwiz_textentry_hint')
               .removeAttr ('disabled')
               .removeClass ('qwiz_button_disabled')
               .css ({display: 'inline-block'});
            return;
         }

         // Hide "Check answer" button div.
         $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question).hide ();
      }
   }

   // Blur entry so jQuery knows to hide suggestion list -- in case "Check
   // answer" triggered with <Enter>.
   $textentry.blur ();

   // Disable further entry.
   if (! q.qwizard_b) {
      $textentry.attr ('disabled', true);
   }

   // See if entry among choices; identify default choice ("*").
   var entry = $textentry.val ().toLowerCase ();
   var i_choice = -1;
   var correct_b = false;
   var n_choices = qwizdata[i_qwiz].textentry[i_question].choices.length;
   var i_default_choice;
   for (var i=0; i<n_choices; i++) {
      var alts = qwizdata[i_qwiz].textentry[i_question].choices[i];
      if (alts[0] == '*') {
         i_default_choice = i;
      } else {
         var ok_f = false;
         if (q.qwizard_b && typeof (qwizard_i_choice) != 'undefined') {
            if (alts == 'Enter word' && i == qwizard_i_choice) {
               ok_f = true;
            }
         }
         if (! ok_f) {
            var lc_alts = alts.map (function (item) {
                                       return item.toLowerCase ();
                                    });
            ok_f = lc_alts.indexOf (entry) != -1;
         }
         if (ok_f) {

            // Yes, this one.  Correct?
            correct_b = qwizdata[i_qwiz].textentry[i_question].choices_correct[i];
            i_choice = i;
            break;
         }
      }
   }
   if (i_choice == -1) {
      i_choice = i_default_choice;
   }

   // Show feedback.
   $ ('#qwiz' + i_qwiz + '-q' + i_question + '-a' + i_choice).show ();

   // Update statistics.
   if (! q.qwizard_b) {
      qwizdata[i_qwiz].answered_correctly[i_question] = correct_b;
      if (correct_b) {
         qwizdata[i_qwiz].n_correct++;
      } else {

         // Record number of incorrect responses.
         qwizdata[i_qwiz].n_incorrect++;
      }

      // If recording, jjax call.
      if (qwizdata[i_qwiz].qrecord_id && document_qwiz_user_logged_in_b) {

         // Include text entered.
         var data = {q_and_a_text:  btoa (encodeURIComponent (qwizdata[i_qwiz].q_and_a_text[i_question])),
                     q_and_a_crc32: qwizdata[i_qwiz].q_and_a_crc32[i_question],
                     i_question:    qwizdata[i_qwiz].dataset_id[i_question],
                     unit:          qwizdata[i_qwiz].unit[i_question],
                     type:          'textentry',
                     response:      entry,
                     i_choice:      -1,
                     correct_b:     correct_b ? 1 : '',
                     confirm:       'js'};
         qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);
      }
      update_topic_statistics (i_qwiz, i_question, correct_b);
   }

   // Update progress and show next button -- only if more than one question.
   update_progress_show_next (i_qwiz);
}


// -----------------------------------------------------------------------------
// Provide first letters of first correct answer as hint.
this.textentry_hint = function (i_qwiz) {

   // Cancel any previous timer.
   clearTimeout (show_hint_timeout[i_qwiz]);
   show_hint_timeout[i_qwiz] = 0;

   qwizdata[i_qwiz].textentry_n_hints++;

   var i_question = qwizdata[i_qwiz].i_question;
   var textentry_hint_val = qwizdata[i_qwiz].textentry[i_question].first_correct_answer.substr (0, qwizdata[i_qwiz].textentry_n_hints);

   // Also show suggestions for hint, if any.
   $ ('#textentry-qwiz' + i_qwiz + '-q' + i_question).val (textentry_hint_val).focus ().trigger ('keydown');

   // Disable hint button, reset label.
   $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question + ' button.qwiz_textentry_hint').attr ('disabled', true)
      .addClass ('qwiz_button_disabled')
      .html ('Another hint');

   // But set timer to show again.  Closure.
   var $check_answer = $ ('#textentry_check_answer_div-qwiz' + i_qwiz+ '-q' + i_question );
   var show_hint_button = function () {
      $check_answer.find ('button.qwiz_textentry_hint')
         .removeAttr ('disabled')
         .removeClass ('qwiz_button_disabled');
   }
   if (hint_timeout_sec >= 0) {
      show_hint_timeout[i_qwiz] = setTimeout (show_hint_button, hint_timeout_sec*1000);
   }
}


// -----------------------------------------------------------------------------
this.set_textentry_i_qwiz = function (e, input_el) {

   // See which quiz this is.  Save in global (private) variable.
   // id looks like textentry-qwiz0-q0
   var id = input_el.id;
   textentry_i_qwiz = id.match (/[0-9]+/)[0];
   if (debug[6]) {
      console.log ('[set_textentry_i_qwiz] textentry_i_qwiz: ', textentry_i_qwiz);
   }
   // Don't propagate click.
   e.stopPropagation ();

   // Also, if qwizard, hide any feedback already showing.
   if (q.qwizard_b) {
      var qwizq = id.match (/qwiz.*/)[0];
      $ ('#' + qwizq + ' .qwiz-feedback').hide ();
   }
}


// -----------------------------------------------------------------------------
// When item selected, enable check answer and set text.
this.item_selected = function () {
   var i_question = qwizdata[textentry_i_qwiz].i_question;
   $ ('#textentry_check_answer_div-qwiz' + textentry_i_qwiz + '-q' + i_question + ' button.textentry_check_answer')
      .removeClass ('qwiz_button_disabled')
      .html (T ('Check answer'));
   qwizdata[textentry_i_qwiz].check_answer_disabled_b = false;
}


// -----------------------------------------------------------------------------
this.keep_next_button_active = function () {
   next_button_active_b = true;
   $ ('.next_button').show ();
}


// -----------------------------------------------------------------------------
this.position_show_next_button = function (i_qwiz) {
   var $next_button = $ ('#next_button-qwiz' + i_qwiz);
   $next_button.show ();
}


// -----------------------------------------------------------------------------
this.hide_menu_and_display_login = function (i_qwiz, add_team_member_f,
                                            login_alt, msg, proceed_to_pay_f,
                                            pay_now_sign_up) {
   var $container = $ ('div.qwiz_icon_and_menu_container.qwiz' + i_qwiz + ' div.qwiz_icon_trigger_and_menu');
   $container.removeClass ('qwiz-hover');
   q.display_login (i_qwiz, add_team_member_f, login_alt, msg, proceed_to_pay_f,
                    pay_now_sign_up);
   var delay_reset = function () {
      $container.addClass ('qwiz-hover');
   }
   setTimeout (delay_reset, 500);
}


// -----------------------------------------------------------------------------
this.display_login = function (i_qwiz, add_team_member_f, login_alt, msg,
                               proceed_to_pay_f, pay_now_sign_up) {
   if (! login_alt) {
      login_alt = '';
   }

   if (! add_team_member_f && ! login_alt) {

      // Stop any icons bouncing.
      $ ('div.qwiz-usermenu_icon').removeClass ('qwiz-icon-bounce');
   }

   var i_question = qwizdata[i_qwiz].i_question;
   if (i_question == -1) {

      // Hide intro (intro -- if any -- shows with the question of a single-
      // question quiz).
      $ ('.intro-qwiz' + i_qwiz).hide ();

      // If intro was showing, can hide qwiz icon now.
      if (! q.no_intro_b[i_qwiz]) {
         $ ('div#icon_qwiz' + i_qwiz).hide ();
      }
   } else if (i_question >= qwizdata[i_qwiz].n_questions) {

      // Hide summary.
      $ ('#summary-qwiz' + i_qwiz).hide ();
   } else {

      // Don't show textentry "Check answer" button if showing.  Record state.
      $textentry_check_answer_div = $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_question);
      if ($textentry_check_answer_div.is (':visible')) {
         $textentry_check_answer_div.hide ();
         qwizdata[i_qwiz].textentry_check_answer_show_b = true;
      } else {
         qwizdata[i_qwiz].textentry_check_answer_show_b = false;
      }

      // Hide current question -- regular and mobile version, if there.
      var qwizq_id = 'qwiz' + i_qwiz + '-q' + qwizdata[i_qwiz].i_user_question;
      $ ('#' + qwizq_id + ', #' + document_qwiz_mobile + qwizq_id).hide ();

      // Hide back-forward controls.
      $ ('.bbfe-qwiz' + i_qwiz).css ({visibility: 'hidden'});
      $ ('span.question-number-qwiz' + i_qwiz).css ({visibility: 'hidden'});
   }

   // Don't show next button.
   $ ('#next_button-qwiz' + i_qwiz).hide ();

   if (login_alt == 'progress_bars' || login_alt == 'leaderboard') {

      // Gets data, callback sets html.
      qqc.create_progress_bars (qname, qwizdata, i_qwiz, login_alt);
   } else if (login_alt == 'feedback') {
      qqc.create_provide_feedback_screen (qname, i_qwiz, qwizdata[i_qwiz].i_question);
   } else if (login_alt == 'use_dataset_options') {
      qqc.create_use_dataset_options (qname, qwizdata, i_qwiz);
   } else if (login_alt == 'pay') {
      qqc.create_pay_screen (qname, qwizdata, i_qwiz, msg, pay_now_sign_up);
   } else if (login_alt == 'maker_pay') {
      qqc.create_maker_pay_screen (qname, qwizdata, i_qwiz, msg);
   } else if (login_alt == 'enroll') {
      qqc.create_enroll_screen (qname, i_qwiz);
   } else if (login_alt == 'register') {
      qqc.create_register_taker_screen (qname, i_qwiz);
   } else {
      $ ('#qwiz_login-qwiz' + i_qwiz).html (get_login_html (i_qwiz, add_team_member_f, msg, proceed_to_pay_f)).show ();
      qqc.init_hide_show_password ('#qwiz_password-qwiz' + i_qwiz);
      if (! qwizdata[i_qwiz].display_pay_screen) {
         $ ('#qwiz_username-qwiz' + i_qwiz).focus ();
      }
   }

   // Set indicator that login is displayed.
   qwizdata[i_qwiz].login_show_b = true;
}


// -----------------------------------------------------------------------------
this.login = function (i_qwiz, add_team_member_f, proceed_to_pay_f) {

   add_team_member_f = add_team_member_f ? 1 : 0;
   proceed_to_pay_f  = proceed_to_pay_f ? 1 : 0;

   // In case previously declined login option, unset cookie and local flag.
   $.removeCookie ('qwiz_declined_login', {path: '/'});
   document_qwiz_declined_login_b = false;

   // Have we got username and password?
   var $username = $ ('#qwiz_username-qwiz' + i_qwiz);
   var username = $username.val ();
   if (! username ) {
      alert (T ('Please enter User name'));
      $username.focus ();
      return false;
   }

   if (add_team_member_f) {

      // Check if this username already on team list.
      var usernames = document_qwiz_username.split ('; ');
      if (usernames.indexOf (username) != -1) {
         alert ('User ' + username + ' is already on your team.');
         return false;
      }
   }

   var $password = $ ('#qwiz_password-qwiz' + i_qwiz);
   var password = $password.val ();
   if (! password) {
      alert (T ('Please enter Password'));
      $password.focus ();
      return false;
   }

   // Blur password in case submit via <Enter>.
   $password.blur ();

   // We'll send "SHA3" of password.
   var sha3_password = CryptoJS.SHA3 (password).toString ();

   var remember_f;
   if (add_team_member_f) {
      remember_f = document_qwiz_remember_f;
   } else {

      // Pass state of "Remember" checkbox.
      remember_f = $ ('#qwiz_login-qwiz' + i_qwiz + ' input[type="checkbox"]').prop('checked') ? 1 : 0;
      document_qwiz_remember_f = remember_f;
   }

   // Do jjax call.
   var data = {username: username, sha3_password: sha3_password, remember_f: remember_f, add_team_member_f: add_team_member_f};
   if (add_team_member_f) {
      data.previous_username = document_qwiz_username;
   }
   if (proceed_to_pay_f) {
      data.proceed_to_pay_f = '1';
   }
   qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'login', data);

   return false;
}


// -----------------------------------------------------------------------------
this.login_ok = function (i_qwiz, session_id, remember_f, proceed_to_pay_f) {

   // Success.  Create session cookie, valid for this session, or -- if flag
   // set -- 1 day, good for whole site.  Value set by server.  Callback
   // script (in login.php) also saves session ID as global (document) variable
   // document_qwiz_session_id.
   var options = {path: '/'};
   if (remember_f == 1) {
      options.expires = 1;
   }
   $.cookie ('qwiz_session_id', document_qwiz_session_id, options);

   // Set flag, record login in cookie that expires in (default) 40 min
   // (expires option specified in days).
   document_qwiz_user_logged_in_b = true;
   var login_timeout_min = qqc.get_qwiz_param ('login_timeout_min', 40);
   options.expires = login_timeout_min/(24.0*60.0);
   $.cookie ('qwiz_current_login_lt_nmin_ago', 1, options);

   // Set user menus.
   qqc.set_user_menus_and_icons ();

   if (qwiz_ && qwiz_.any_pay_quiz_f) {
      qwiz_.pay_lock_settings ();
   }
   if (qcard_ && qcard_.any_pay_deck_f) {
      qcard_.pay_lock_settings ();
   }

   // Hide login.
   $ ('#qwiz_login-qwiz' + i_qwiz).hide ();

   // Unset indicator that login is displayed.
   qwizdata[i_qwiz].login_show_b = false;

   // If recording any quizzes, reset flag to record start times on first
   // interaction with quizzes.
   if (q.qrecord_b) {
      for (var ii_qwiz=0; ii_qwiz<n_qwizzes; ii_qwiz++) {
         if (qwizdata[ii_qwiz].qrecord_id) {
            qwizdata[ii_qwiz].record_start_b = true;
         }

         // Also, set indicator to re-check whether (new?) user will get
         // credit for each quiz.
         if (qwizdata[ii_qwiz].qrecord_id) {
            qwizdata[ii_qwiz].qrecord_id_ok = 'check credit';
         }
      }
   }

   // If proceed-to-pay flag set, do not actually want to continue if this
   // quiz is ok to use.
   if (proceed_to_pay_f) {
      if (qwizdata[i_qwiz].pay_quiz_ok == 'paid') {
         proceed_to_pay_f = false;
      }
   }

   if (proceed_to_pay_f) {
      q.display_login (i_qwiz, false, 'pay');
   } else {
      if (qwizdata[i_qwiz].display_pay_screen) {

         // Re-show intro.
         $ ('.intro-qwiz' + i_qwiz).show ();
      } else {
         q.login_ok_start_quiz (i_qwiz);
      }
   }

   return false;
}


// -----------------------------------------------------------------------------
this.login_ok_start_quiz = function (i_qwiz) {
   var i_user_question = qwizdata[i_qwiz].i_user_question;
   if (i_user_question == -1) {

      // Go to first question or, if was on dataset_units_topics card, back
      // to that.
      q.next_question (i_qwiz);
   } else {

      // Re-display current question or summary.
      redisplay_current_question (i_qwiz, i_user_question);

      // Record start.
      qwizdata[i_qwiz].record_start_b = false;
      var data = {qrecord_id_ok: qwizdata[i_qwiz].qrecord_id_ok, type: 'start', confirm: 'js'};
      qqc.jjax (qname, i_qwiz, qwizdata[i_qwiz].qrecord_id, 'record_response_v3', data);

      // Show next button if was showing.  If mobile, update position.
      if (qwizdata[i_qwiz].next_button_show_b) {
         q.position_show_next_button (i_qwiz);
      }

      // Show textentry "Check answer" button if was showing.
      if (qwizdata[i_qwiz].textentry_check_answer_show_b) {
         $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_user_question).css ({display: 'inline-block'});
      }
   }
}


// -----------------------------------------------------------------------------
this.login_not_ok = function (i_qwiz) {

   // Invalid login.  Error message.
   $ ('#qwiz_login-qwiz' + i_qwiz + ' p.login_error').show ();
   if (debug[0]) {
      console.log ('[login_not_ok] $ (\'#qwiz_login-qwiz' + i_qwiz + ' p.login_error\'):', $ ('#qwiz_login-qwiz' + i_qwiz + ' p.login_error'));
   }
}


// -----------------------------------------------------------------------------
this.no_login = function (i_qwiz, add_team_member_f, progress_bars_f) {

   // Skip login.  Hide login.  If checkbox checked, set cookie and local flag
   // to skip in the future.
   if (! (add_team_member_f || progress_bars_f)) {
      if ($ ('#qwiz_login-qwiz' + i_qwiz + ' input[type="checkbox"]').prop('checked')) {
         $.cookie ('qwiz_declined_login', 1, {path: '/'});
         document_qwiz_declined_login_b = true;
      }

      // Stop any icons bouncing.
      $ ('div.qwiz-usermenu_icon').removeClass ('qwiz-icon-bounce');
   }

   // Hide login.
   $ ('#qwiz_login-qwiz' + i_qwiz).hide ();

   // Unset indicator that login is displayed.
   qwizdata[i_qwiz].login_show_b = false;

   // If on intro, go to next question.  Otherwise, show current question
   // or summary.  If showing progress or feedback, re-display intro and "start
   // quiz" button.
   var i_user_question = qwizdata[i_qwiz].i_user_question;
   if (i_user_question == -1) {
      if (progress_bars_f) {
         $ ('.intro-qwiz' + i_qwiz).show ();
         $ ('#next_button-qwiz' + i_qwiz).show ();
      } else {
         q.next_question (i_qwiz, true);
      }
   } else {

      // Re-display current question or summary.
      redisplay_current_question (i_qwiz, i_user_question);

      // Show next button if was showing.
      if (qwizdata[i_qwiz].next_button_show_b) {
         q.position_show_next_button (i_qwiz);
      }

      // Show textentry "Check answer" button if was showing.
      if (qwizdata[i_qwiz].textentry_check_answer_show_b) {
         $ ('#textentry_check_answer_div-qwiz' + i_qwiz + '-q' + i_user_question).show ();
      }
   }

   return false;
}


// -----------------------------------------------------------------------------
this.icon_no_login = function (i_qwiz, add_team_member_f) {

   // Stop icon from bouncing.  If checkbox checked, set cookie and local flag
   // to skip bouncing/login in the future.
   $ ('div.qwiz-usermenu_icon').removeClass ('qwiz-icon-bounce');

   if (! add_team_member_f) {
      if ($ ('#usermenu-qwiz' + i_qwiz + ' input[type="checkbox"]').prop('checked')) {
         $.cookie ('qwiz_declined_login', 1, {path: '/'});
         document_qwiz_declined_login_b = true;
      }
   }
}


// -----------------------------------------------------------------------------
function redisplay_current_question (i_qwiz, i_question) {

   // If mobile view and there's a mobile version of question, display that,
   // otherwise regular version.
   if (i_question < qwizdata[i_qwiz].n_questions) {
      if (document_qwiz_mobile) {
         var $mobile_qwizq = $ ('#mobile_qwiz' + i_qwiz + '-q' + i_question);
         if ($mobile_qwizq.length) {
            $mobile_qwizq.show ();
         } else {
            $ ('#qwiz' + i_qwiz + '-q' + i_question).show ();
         }
      } else {
         $ ('#qwiz' + i_qwiz + '-q' + i_question).show ();
      }
   } else {
      $ ('#summary-qwiz' + i_qwiz).show ();
   }

   // Show back-forward controls.
   $ ('.bbfe-qwiz' + i_qwiz).css ({visibility: 'visible'});
   $ ('span.question-number-qwiz' + i_qwiz).css ({visibility: 'visible'});
}


// -----------------------------------------------------------------------------
function get_attr (htm, attr_name, plural_ok_b) {
   var attr_value = qqc.get_attr (htm, attr_name);
   if (plural_ok_b && ! attr_value) {
      attr_value = qqc.get_attr (htm, attr_name + 's');
   }

   return attr_value;
}


// -----------------------------------------------------------------------------
this.get_qwizdata = function (i_qwiz, variable) {

   return qwizdata[i_qwiz][variable];
}


// -----------------------------------------------------------------------------
this.set_qwizdata = function (i_qwiz, variable, value) {
   if (i_qwiz == -1) {
      var s = variable + ' = ' + value;
      eval (s);
   } else {
      qwizdata[i_qwiz][variable] = value;
   }
}


// -----------------------------------------------------------------------------
function T (string) {
   return qqc.T (string);
}


// -----------------------------------------------------------------------------
function Tplural (word, plural_word, n) {
   return qqc.Tplural (word, plural_word, n);
}


// -----------------------------------------------------------------------------
function inarray0 (array_of_arrays, query) {
   var len = array_of_arrays.length;
   for (var i=0; i<len; i++) {
      if (array_of_arrays[i][0] == query) {
         return true;
      }
   }

   return false;
}


// =============================================================================
// Close - isolate namespace.
};


// -----------------------------------------------------------------------------
qwizf.call (qwiz_);