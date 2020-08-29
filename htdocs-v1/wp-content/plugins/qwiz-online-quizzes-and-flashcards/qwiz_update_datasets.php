<?php
// This file include'd in qwiz-online-quizzes-wp-plugin.php (plugin directory)
// and in qwizard_create_page.php (admin directory).

// -----------------------------------------------------------------------------
function qwiz_update_datasets ($html, $post_id, $prev_text, $qwizard_page) {
   global $debug;

   // Get array of questions that are in any quiz or deck with a dataset=
   // attribute from current page.  Also, get positions of each relevant quiz/
   // deck in the text.  Finally, get json string of qwizard question/card
   // structures (saved in page html by qwizard or qwiz_admin_footer_post_func).

   // Get rid of old-style <code></code> in labeled-diagram question and label
   // shortcodes ([<code></code>q and [<code></code>l).
   $html = preg_replace ('/\[<code><\/code>([ql])/', '[$1', $html, -1, $count);
   if ($debug[5]) {
      error_log ('[qwiz_update_datasets] $count: ' . $count);
   }

   list ($qwiz_qdeck_poss, $qwiz_qdeck_lens, $qdata,
         $any_dataset_id_f, $any_no_dataset_id_f, $maker_session_id)
                               = qwiz_parse_dataset_questions ($html, $post_id);
   if (! $qdata || ! $qdata['htmls']) {
      if ($debug[5]) {
         error_log ('[qwiz_update_datasets] html:' . $html);
      }
      return $html;
   } else {

      // If any questions with dataset_id...
      if ($any_dataset_id_f) {

         // Compare with previous version of page.  Get previous set of
         // questions.
         if ($post_id) {
            $prev_html = qwiz_get_previous_version ($post_id);
         } else {
            $prev_html = $prev_text;
         }
         if ($debug[5]) {
            error_log ('[qwiz_update_datasets] prev_html: ' . $prev_html);
         }

         // $u = unused.
         list ($u, $u, $prev_qdata, $prev_any_dataset_id_f, $u, $u)
                                 = qwiz_parse_dataset_questions ($prev_html, 0);

         // If differences, set 'new_modified' variable/property.
         list ($qdata, $any_renumber_or_no_json_f)
                      = qwiz_compare_dataset_questions ($qdata, $prev_qdata,
                                                        $prev_any_dataset_id_f);
      }

      // Get from database all non-blank questions from each dataset that are
      // from this page.  If any of these not there now, "delete" from database
      // (send blank new version of question/card to database, below).
      if ($post_id) {
         $permalink = get_permalink ($post_id);
      } else {
         $permalink = $qwizard_page;
      }
      $dataset_ids_to_blank
                  = qwiz_find_deleted_dataset_questions ($permalink,
                                                         $qdata['dataset_ids']);

      // Update qdata array.
      if (count ($dataset_ids_to_blank)) {
         qwiz_blank_deleted_dataset_questions ($dataset_ids_to_blank, $qdata);
      }

      // For questions without dataset_id= attribute, create and add to [q]
      // shortcode.  Also, add or update question_number= attribute.
      // Do so in both the page html and the parsed array (to be saved to db).
      if ($any_no_dataset_id_f || $any_renumber_or_no_json_f) {
         qwiz_update_dataset_ids ($qwiz_qdeck_poss, $qwiz_qdeck_lens, $qdata,
                                  $html);
      }

      // Send new questions and modified questions, if any of either, to
      // Qwizcards db.
      if ($debug[0]) {
         error_log ('[qwiz_update_datasets] $qdata[\'new_modified\']: ' . print_r ($qdata['new_modified'], true));
      }
      if ($qdata['new_modified']) {

         // There are new or modified questions.  If maker session ID not set,
         // won't be able to do database update.  Set update warning message
         // instead.  (If there is a session ID, update_dataset_questions.php
         // will check if still valid.)
         if ($maker_session_id) {
            qwiz_dataset_questions_to_db ($qdata, $post_id, $maker_session_id,
                                          $permalink);
         } else {
            $update_msg = 'Warning: dataset questions/cards not added to/updated in Qwizcards database.  '
                          . 'You must log in to do so.  Click the "Q" icon. ';
            if ($post_id) {
               qwiz_save_dataset_update_msg ($post_id, $update_msg);
            }
         }
      }
   }

   return $html;
}


// -----------------------------------------------------------------------------
function qwiz_parse_dataset_questions ($html, $post_id) {
   global $debug;

   // ..........................................................................
   // Preliminary: recover quiz and deck json structures from temporary db
   // storage.
   if ($post_id) {
      list ($maker_session_id, $qwizzes_questions, $qdecks_cards)
                                             = qwiz_get_dataset_json ($post_id);
   } else {
      $qwizzes_questions = '';
      $qdecks_cards      = '';
      $maker_session_id  = '';
   }

   // ..........................................................................
   // Look for quizzes/decks with dataset=.  Parse out questions/cards of each.
   // preg_match_all not producing result for long quiz/dataset (144 questions).
   // Do "by hand."
   $any_dataset_id_f    = false;
   $any_no_dataset_id_f = false;

   // Array over quizzes/decks.
   $qwiz_qdeck_poss = array ();
   $qwiz_qdeck_lens = array ();

   // Arrays over questions/cards.
   $qdata = array ('i_qwiz_qdecks'          => array (),
                   'i_qwiz_qdeck_questions' => array (),
                   'htmls'                  => array (),
                   'jsons'                  => array (),
                   'qwiz_qdecks'            => array (),
                   'datasets'               => array (),
                   'dataset_ids'            => array (),
                   'question_numbers'       => array (),
                   'units'                  => array (),
                   'topics'                 => array (),
                   'difficulties'           => array (),
                   'new_modified'           => array ()
                  );

   $i_qwiz_qdeck   = 0;
   $i_qwiz         = 0;
   $i_deck         = 0;
   $remaining_html = $html;
   $n_before_remaining = 0;
   // Loop over quizzes/decks.
   //$n_matches = preg_match_all ('/(\[qwiz|\[qdeck)[^\]]*?\sdataset="([^"]+)[\s\S]*?(\[\/qwiz\]|\[\/qdeck\])/',
   //                             $html, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
   while (preg_match ('/(\[qwiz|\[qdeck)[^\]]*?\sdataset="([^"]+)/',
                      $remaining_html, $matches, PREG_OFFSET_CAPTURE)) {
      if ($debug[6]) {
         //error_log ('[qwiz_parse_dataset_questions] $remaining_html: ' . $remaining_html);
         //error_log ('[qwiz_parse_dataset_questions] strpos ($remaining_html, \'[qwiz dataset="\'): ' . strpos ($remaining_html, '[qwiz dataset="'));
         //error_log ('[qwiz_parse_dataset_questions] strpos ($remaining_html, \'[/qwiz]\'): ' . strpos ($remaining_html, '[/qwiz]'));
         error_log ('[qwiz_parse_dataset_questions] $matches: ' . print_r ($matches, true));
      }

      // Question index within each quiz/deck.
      $i_qwiz_qdeck_question = 0;

      $remaining_html_qwiz_deck_pos = $matches[0][1];
      $qwiz_qdeck_pos               = $n_before_remaining + $remaining_html_qwiz_deck_pos;
      $qwiz_qdeck                   = $matches[1][0];
      $dataset                      = $matches[2][0];

      $remaining_html = substr ($remaining_html, $remaining_html_qwiz_deck_pos);
      $n_before_remaining += $remaining_html_qwiz_deck_pos;

      $questions_cards_pos = strpos ($remaining_html, ']') + 1;
      $remaining_html = substr ($remaining_html, $questions_cards_pos);
      $n_before_remaining += $questions_cards_pos;

      $end_match_f = preg_match ('/(\[\/qwiz\]|\[\/qdeck\])/', $remaining_html,
                                 $end_matches, PREG_OFFSET_CAPTURE);
      $end_questions_cards_pos = $end_matches[0][1];
      $qwiz_qdeck_end = $n_before_remaining + $end_questions_cards_pos;

      $qwiz_qdeck_html = substr ($remaining_html, 0, $end_questions_cards_pos);
      if ($debug[6]) {
         error_log ('[qwiz_parse_dataset_questions] $end_matches: ' . print_r ($end_matches, true));
         error_log ('                               $qwiz_qdeck_html: ' . $qwiz_qdeck_html);
      }

      $remaining_html = substr ($remaining_html, $end_questions_cards_pos);
      $n_before_remaining += $end_questions_cards_pos;


      // Take off "[" from "[qwiz...".
      $qwiz_qdeck = substr ($qwiz_qdeck, 1);

      // Save offset and length in arrays over quizzes/decks.
      $qwiz_qdeck_len = $qwiz_qdeck_end - $qwiz_qdeck_pos;
      if ($debug[6]) {
         error_log ('[qwiz_parse_dataset_questions] $qwiz_qdeck_len: ' . $qwiz_qdeck_len);
         error_log ('                               strlen ($qwiz_qdeck_html): ' . strlen ($qwiz_qdeck_html));
      }
      $qwiz_qdeck_poss[] = $qwiz_qdeck_pos;
      $qwiz_qdeck_lens[] = $qwiz_qdeck_len;

      // Split html for this quiz/deck into questions, but keep the [q]
      // shortcodes ("splitters") as elements of the array.  Include any
      // opening tags in the splitters(<[^\/][^>]*>\s*)*?.  Due to the
      // parenthesized sub-expressions, if there are opening tags we get two
      // tag elements.  We'll need to ignore the second.
      $pieces = preg_split ('/((<[^\/][^>]*>\s*)*?)(\[q\]|\[q [^\]]+\]|\[x\])/',
                            $qwiz_qdeck_html, NULL,
                            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
      if ($debug[6]) {
         error_log ('[qwiz_parse_dataset_questions] $pieces: ' . print_r ($pieces, true));
      }

      // Don't keep the first piece: includes [qwiz] and [h], [i] shortcodes, if
      // any.  But parse out unit=, topic=, and difficulty= attributes, if any.
      $piece = $pieces[0];
      $default_unit       = qwiz_get_attr ($piece, 'unit');
      $default_topic      = qwiz_get_attr ($piece, 'topic');
      $default_difficulty = qwiz_get_attr ($piece, 'difficulty');
      if ($debug[6]) {
         error_log ('[qwiz_parse_dataset_questions] $piece: ' . $piece);
         error_log ('[qwiz_parse_dataset_questions] $default_unit: ' . $default_unit);
      }

      $n_pieces = count ($pieces);
      $i_piece = 1;
      $question_html = '';
      while ($i_piece < $n_pieces) {

         // First two pieces are opening tags for this question, if any.
         // If just tags and whitespace, keep, and skip next piece.
         $piece = $pieces[$i_piece];
         $piece_wo_tags_whitespace = preg_replace ('/<[^>]+>*|\s*/', '', $piece);
         if ($piece_wo_tags_whitespace == '') {
            $question_html .= $piece;
            $i_piece += 2;
         }

         // If this is next-to-last piece, see if [x] shortcode.  If so, then
         // ignore.
         if ($i_piece == $n_pieces - 2 && $pieces[$i_piece] == '[x]') {
            break;
         }

         // This is the [q ...] shortcode.  Add it to this question, see if
         // has dataset_id= attribute.
         $question_shortcode = $pieces[$i_piece];
         $question_html .= $question_shortcode;
         $dataset_id = qwiz_get_attr ($question_shortcode, 'dataset_id');
         if ($dataset_id) {

            // If dataset_id does not match current dataset name (may have
            // changed), then treat it as new (no dataset_id=).
            // Looks like dataset_name|4ff429e4eb5e9
            preg_match ('/[^|]+/', $dataset_id, $dmatches);
            $id_dataset_name = $dmatches[0];
            if ($id_dataset_name != $dataset) {
               $dataset_id = '';
               $any_no_dataset_id_f = true;
            } else {
               $any_dataset_id_f = true;
            }
         } else {
            $any_no_dataset_id_f = true;
         }

         // Get question_number, unit=, topic=, and difficulty= attributes, if
         // any.
         $question_number = qwiz_get_attr ($question_shortcode, 'question_number');
         $unit            = qwiz_get_attr ($question_shortcode, 'unit');
         if (! $unit) {
            $unit = $default_unit;
         }
         $topic           = qwiz_get_attr ($question_shortcode, 'topic');
         if (! $topic) {
            $topic = $default_topic;
         }
         $difficulty      = qwiz_get_attr ($question_shortcode, 'difficulty');
         if (! $difficulty) {
            $difficulty = $default_difficulty;
         }

         $i_piece++;

         // This is the content -- finishes this question.  Save.
         // If this is the last piece, take off [/qwiz] or [/qdeck].
         $question_content = $pieces[$i_piece];
         if ($debug[6]) {
            error_log ('[qwiz_parse_dataset_questions] $question_content: ' . $question_content);
         }
         if ($i_piece == $n_pieces - 1) {
            if (substr ($question_content, -7) == '[/qwiz]') {
               $i_remove = 7;
            } else if (substr ($question_content, -8) == '[/qcard]') {
               $i_remove = 8;
            } else {
               $i_remove = 0;
            }
            if ($i_remove) {
               $question_content = substr ($question_content, 0, -$i_remove);
            }
         }
         $question_html .= $question_content;

         // If there are more pieces (questions), see if next "opening tags"
         // includes <img...> or <input...>.  If so, add to this question
         // and delete from piece so doesn't show up in next question.
         if ($i_piece + 1 < $n_pieces) {
            $piece = $pieces[$i_piece+1];
            $img_input_match_f = preg_match ('/.*<(img|input)[^>]+>/', $piece,
                                             $img_input_matches);
            if ($img_input_match_f) {
               $question_html .= $img_input_matches[0];
               $pieces[$i_piece+1] = str_replace ($img_input_matches[0], '', $piece);
               if ($debug[6]) {
                  error_log ('[qwiz_parse_dataset_questions] $img_input_matches[0]: ' . $img_input_matches[0]);
               }
            }
         }

         // If processing new html (and not previous html from db), and
         // shortcode does not have json="true", add that, so if json structure
         // has not been saved previously, the question/card will now appear to
         // be modified, and so will be saved (along with json structure) to db.
         if ($post_id != 0) {

            // $2 -- the second subset match (json="true" if there) -- is
            // ignored/discarded if it is there and json="true" is added.
            $question_html = preg_replace ('/(\[q\s[^\]]*?)(\s*json=.true.\s*)*([^\]]*\])/', '$1json="true" $3', $question_html);
         }

         $qdata['i_qwiz_qdecks'][]          = $i_qwiz_qdeck;
         $qdata['i_qwiz_qdeck_questions'][] = $i_qwiz_qdeck_question;
         $qdata['htmls'][]                  = $question_html;
         $qdata['qwiz_qdecks'][]            = $qwiz_qdeck;
         $qdata['datasets'][]               = $dataset;
         $qdata['dataset_ids'][]            = $dataset_id;
         $qdata['question_numbers'][]       = $question_number;
         $qdata['units'][]                  = $unit;
         $qdata['topics'][]                 = $topic;
         $qdata['difficulties'][]           = $difficulty;

         if ($qwiz_qdeck == 'qwiz') {
            if ($debug[5]) {
               error_log ("[qwiz_parse_dataset_questions] i_qwiz: $i_qwiz, i_qwiz_qdeck_question: $i_qwiz_qdeck_question");
            }
            if ($qwizzes_questions) {
               $qdata['jsons'][]            = $qwizzes_questions[$i_qwiz][$i_qwiz_qdeck_question];
            } else {
               $qdata['jsons'][]            = '';
            }
         } else {
            if ($debug[5]) {
               error_log ("[qwiz_parse_dataset_questions] i_deck: $i_deck, i_deck: $i_qwiz_qdeck_question");
            }
            if ($qdecks_cards) {
               $qdata['jsons'][]            = $qdecks_cards[$i_deck][$i_qwiz_qdeck_question];
            } else {
               $qdata['jsons'][]            = '';
            }
         }

         // Set for next.
         $i_qwiz_qdeck_question++;
         $question_html = '';
         $i_piece++;
      }
      $i_qwiz_qdeck++;
      if ($qwiz_qdeck == 'qwiz') {
         $i_qwiz++;
      } else {
         $i_deck++;
      }
   }

   if ($debug[5]) {
      error_log ('[qwiz_parse_dataset_questions] $qwiz_qdeck_poss: ' . print_r ($qwiz_qdeck_poss, true));
      error_log ('[qwiz_parse_dataset_questions] $qdata: ' . print_r ($qdata, true));
      error_log ('[qwiz_parse_dataset_questions] $any_dataset_id_f: ' . $any_dataset_id_f);
      error_log ('[qwiz_parse_dataset_questions] $any_no_dataset_id_f: ' . $any_no_dataset_id_f);
   }
   return array ($qwiz_qdeck_poss, $qwiz_qdeck_lens, $qdata,
                 $any_dataset_id_f, $any_no_dataset_id_f, $maker_session_id);
}


// -----------------------------------------------------------------------------
function qwiz_compare_dataset_questions ($qdata, $prev_qdata, $prev_any_dataset_id_f) {
   global $debug;
   if ($debug[5]) {
      error_log ('[qwiz_compare_dataset_questions] $qdata: ' . print_r ($qdata, true));
      error_log ('[qwiz_compare_dataset_questions] $prev_qdata: ' . print_r ($prev_qdata, true));
   }

   // For current questions with dataset ID, see if change from previous.
   $any_renumber_or_no_json_f = false;
   $n_questions = count ($qdata['dataset_ids']);
   for ($i_question=0; $i_question<$n_questions; $i_question++) {
      $dataset_id = $qdata['dataset_ids'][$i_question];
      if ($dataset_id) {

         // Is there a previous?
         $i_prev_question = false;
         if ($prev_any_dataset_id_f) {
            $i_prev_question = array_search ($dataset_id, $prev_qdata['dataset_ids']);
         }
         $question_number_change_f = false;
         $no_json_f                = false;
         if ($i_prev_question !== false) {

            // Yes.  Compare.  First see if question number not set (question
            // numbers begin with 1) or has changed.
            $question_number      = $qdata['question_numbers'][$i_question];
            $prev_question_number = $prev_qdata['question_numbers'][$i_prev_question];
            if (! $question_number || $question_number != $prev_question_number) {
               $qdata['new_modified'][$i_question] = true;
               $question_number_change_f = true;

               // Update.
               $qdata['question_numbers'][$i_question] = $i_question + 1;
            }

            // See if json="true" already there.
            $n = preg_match ('/json=.true./', $prev_qdata['htmls'][$i_prev_question]);
            if (! $n) {
               $no_json_f = true;
            }
            if ($question_number_change_f || $no_json_f) {
               $any_renumber_or_no_json_f = true;
            } else {

               // Question number is the same and json="true" is there.  See if
               // any part of html has changed.
               $html      = $qdata['htmls'][$i_question];
               $prev_html = $prev_qdata['htmls'][$i_prev_question];
               if ($debug[5] && $i_question == 0) {
                  error_log ('[qwiz_compare_dataset_questions] $html: ' . $html);
                  error_log ('[qwiz_compare_dataset_questions] $prev_html: ' . $prev_html);
               }
               if ($html != $prev_html) {
                  $qdata['new_modified'][$i_question] = true;
               }
            }
         } else {

            // Not there (perhaps no previous version of copied-and-pasted
            // questions).  Count as modified; set flag that will need a
            // question number.
            $qdata['new_modified'][$i_question] = true;
            $any_renumber_or_no_json_f = true;
            if ($debug[5]) {
               error_log ('[qwiz_compare_dataset_questions] not there - $i_question: ' .$i_question);
            }
         }
      }
   }
   if ($debug[5]) {
      error_log ('[qwiz_compare_dataset_questions] $qdata[\'new_modified\']: ' . print_r ($qdata['new_modified'], true));
      error_log ('[qwiz_compare_dataset_questions] $any_renumber_or_no_json_f: ' . $any_renumber_or_no_json_f);
   }

   return array ($qdata, $any_renumber_or_no_json_f);
}


// -----------------------------------------------------------------------------
function qwiz_update_dataset_ids ($qwiz_qdeck_poss, $qwiz_qdeck_lens,
                                                              &$qdata, &$html) {
   global $debug;

   if ($debug[5]) {
      error_log ('[qwiz_update_dataset_ids] $qwiz_qdeck_poss: ' . print_r ($qwiz_qdeck_poss, true));
      error_log ('[qwiz_update_dataset_ids] $qdata: ' . print_r ($qdata, true));
   }

   // Loop over questions, in reverse order -- so positions within html remain
   // correct.
   $n_questions = count ($qdata['dataset_ids']);
   $prev_qwiz_qdeck = -1;
   for ($i_question=$n_questions-1; $i_question>=0; $i_question--) {

      // See which quiz/deck.  $i_qwiz_qdeck set to -1 signals just blanking
      // deleted question -- no modifications here.
      $i_qwiz_qdeck = $qdata['i_qwiz_qdecks'][$i_question];
      if ($i_qwiz_qdeck == -1) {
         continue;
      }

      // If this is first time for this quiz/deck, get position of quiz/deck
      // and portion of html specific to this quiz/deck.
      if ($i_qwiz_qdeck != $prev_qwiz_qdeck) {
         if ($debug[5]) {
            error_log ('[qwiz_update_dataset_ids] $i_qwiz_qdeck: ' . $i_qwiz_qdeck);
         }

         // New quiz/deck.  If was working on a previous one, reassemble
         // (modified) html.
         if ($prev_qwiz_qdeck != -1) {
            $html = $before_qwiz_qdeck . $qwiz_qdeck_html . $after_qwiz_qdeck;
         }
         $qwiz_qdeck_pos = $qwiz_qdeck_poss[$i_qwiz_qdeck];
         $qwiz_qdeck_len = $qwiz_qdeck_lens[$i_qwiz_qdeck];
         $before_qwiz_qdeck = substr ($html, 0, $qwiz_qdeck_pos);
         $qwiz_qdeck_html   = substr ($html, $qwiz_qdeck_pos, $qwiz_qdeck_len);
         $after_qwiz_qdeck  = substr ($html, $qwiz_qdeck_pos + $qwiz_qdeck_len);

         $prev_qwiz_qdeck = $i_qwiz_qdeck;

         // Find the [q] shortcodes, with offset positions.
         $n_matches = preg_match_all ('/\[q\]|\[q [^\]]+\]/', $qwiz_qdeck_html,
                                      $matches, PREG_OFFSET_CAPTURE, PREG_SET_ORDER);
         if ($debug[5]) {
            error_log ('[qwiz_update_dataset_ids] $qwiz_qdeck_html: ' . substr ($qwiz_qdeck_html, 0, 200) . "\n ...\n " . substr ($qwiz_qdeck_html, -200));
            error_log ('[qwiz_update_dataset_ids] $matches: ' . print_r ($matches, true));
         }
      }

      // Index of question within this deck.
      $i_qwiz_qdeck_question = $qdata['i_qwiz_qdeck_questions'][$i_question];

      // Shortcode, offset.
      $shortcode = $matches[0][$i_qwiz_qdeck_question][0];
      $pos       = $matches[0][$i_qwiz_qdeck_question][1];
      $len = strlen ($shortcode);

      // Pieces before and after shortcode.
      $before = substr ($qwiz_qdeck_html, 0, $pos);
      $after  = substr ($qwiz_qdeck_html, $pos + $len);

      // Make sure that json data reflects new or update for dataset_id (json
      // structure created by qwizard before we got here and created/updated
      // dataset_id).
      $jsons_i = $qdata['jsons'][$i_question];
      if (isset ($jsons_i['question_attributes'])) {
         $json_question_attributes = $jsons_i['question_attributes'];
      } else {
         $json_question_attributes = '';
      }

      // New dataset ID only for those without.
      if (! $qdata['dataset_ids'][$i_question]) {

         // Create ID.  Includes dataset name.  Save for this question.
         $dataset_id = qwiz_create_dataset_id ($qdata['datasets'][$i_question]);
         $qdata['dataset_ids'][$i_question] = $dataset_id;
         $qdata['new_modified'][$i_question] = true;

         // Could be updating dataset_id=.  See if there.

         $new_dataset_attr = 'dataset_id="' . $dataset_id . '"';
         if (strpos ($shortcode, 'dataset_id=') !== false) {

            // Update.
            $shortcode                = preg_replace ('/dataset_id="[^"]+"/', $new_dataset_attr, $shortcode);
            $json_question_attributes = preg_replace ('/dataset_id="[^"]+"/', $new_dataset_attr, $json_question_attributes);
         } else {

            // Not there.  Add dataset_id to end of shortcode.
            $shortcode                = substr ($shortcode,                0, -1) . ' ' . $new_dataset_attr . ']';
            $json_question_attributes = substr ($json_question_attributes, 0, -1) . ' ' . $new_dataset_attr;
         }
      }

      // Question number.  Update if there, or add to shortcode.  Similar for
      // json.
      $i_question_number = $qdata['i_qwiz_qdeck_questions'][$i_question] + 1;
      $new_question_number_attr = 'question_number="' . $i_question_number . '"';
      if (strpos ($shortcode, 'question_number=') !== false) {

         // Update.
         $old_shortcode = $shortcode;
         $shortcode                = preg_replace ('/question_number="[^"]+"/', $new_question_number_attr, $shortcode);
         $json_question_attributes = preg_replace ('/question_number="[^"]+"/', $new_question_number_attr, $json_question_attributes);
         if ($shortcode != $old_shortcode) {
            $qdata['new_modified'][$i_question] = true;
         }
      } else {

         // Not there.  Add dataset_id to end of shortcode.
         $shortcode                = substr ($shortcode               , 0, -1) . ' ' . $new_question_number_attr . ']';
         $json_question_attributes = substr ($json_question_attributes, 0, -1) . ' ' . $new_question_number_attr;
         $qdata['new_modified'][$i_question] = true;
      }
      $qdata['question_numbers'][$i_question] = $i_question_number;

      // Add json="true" to shortcode.  $2 -- the second subset match
      // (json="true" if there) -- is ignored/discarded if it is there and
      // json="true" is added in all cases.
      $shortcode = preg_replace ('/(\[q\s[^\]]*?)(\s*json=.true.\s*)*([^\]]*\])/', '$1json="true" $3', $shortcode);

      // Change to new shortcode plus remaining html.
      $qwiz_qdeck_html = $before . $shortcode . $after;

      // Also modify shortcode in parsed question.
      $qdata['htmls'][$i_question] = preg_replace ('/\[q\]|\[q [^\]]+\]/', $shortcode,
                                                   $qdata['htmls'][$i_question]);

      // Finally, modify json.
      $qdata['jsons'][$i_question]['question_attributes'] = $json_question_attributes;
   }

   // Finished loop over questions (and implicitly, quizzes/decks).  Finish up
   // -- include final modified quiz/deck html.
   $html = $before_qwiz_qdeck . $qwiz_qdeck_html . $after_qwiz_qdeck;

   if ($debug[6]) {
      error_log ('[qwiz_update_dataset_ids] $html: ' . $html);
   }
   if ($debug[5]) {
      error_log ('[qwiz_update_dataset_ids] $qdata[\'dataset_ids\']: ' . print_r ($qdata['dataset_ids'], true));
      error_log ('[qwiz_update_dataset_ids] $qdata[\'new_modified\']: ' . print_r ($qdata['new_modified'], true));
   }
}


// -----------------------------------------------------------------------------
function qwiz_create_dataset_id ($dataset) {

   // Dataset name plus string derived from microtime.  Make sure dataset name
   // doesn't include double-quotes.
   // DKTMP or non-ascii chars.
   $dataset_name = preg_replace ('/["|]/', '', $dataset);

   //                                   0----+----1----+----2
   // microtime () returns string like: 0.41844100 1455768730.  Second field is
   // current unix time; first field is fraction of second (only first six
   // digits relevant).  Likely to be unique within this dataset.
   $string = microtime ();

   // Make microtime string into an integer.  (Adding zero does the trick;
   // intval () does not!)
   $string = substr ($string, 2, 6) . substr ($string, 11);
   $int_microtime = $string + 0;

   // Use hex version.
   $dataset_id = $dataset_name . '|' . sprintf ('%x', $int_microtime);

   return $dataset_id;
}


// -----------------------------------------------------------------------------
// Add version of question with blank html to qdata array.
function qwiz_blank_deleted_dataset_questions ($dataset_ids_to_blank, &$qdata) {
   foreach ($dataset_ids_to_blank as $dataset_id) {
      $i_pos = strpos ($dataset_id, '|');
      $dataset = substr ($dataset_id, 0, $i_pos);
      $i_question = count ($qdata['datasets']);
      $qdata['new_modified'][$i_question] = true;

      $qdata['i_qwiz_qdecks'][]          = -1;
      $qdata['i_qwiz_qdeck_questions'][] = '';
      $qdata['htmls'][]                  = '';
      $qdata['jsons'][]                  = '';
      $qdata['qwiz_qdecks'][]            = '';
      $qdata['datasets'][]               = $dataset;
      $qdata['dataset_ids'][]            = $dataset_id;
      $qdata['question_numbers'][]       = '';
      $qdata['units'][]                  = '';
      $qdata['topics'][]                 = '';
      $qdata['difficulties'][]           = '';
   }
}


// -----------------------------------------------------------------------------
function qwiz_parse_qrecord_ids ($html) {
   global $debug;

   // Look for quizzes/decks with qrecord_id.  For those with use_dataset=,
   // create list to do unit counts from dataset_question_dataset table.
   // For those without use_dataset=, see if unit= specified for quiz/deck or
   // any question/card.  Count questions/unit, send separately for update of
   // qwiz_qdeck_unit table.

   // Match quizzes/decks with qrecord_id.  Get everything through closing
   // [/qwiz] or [/qdeck] (may be nothing, in case of use_dataset=, but those
   // may include [use unit="..."  shortcodes] -- DKTMP: still to be handled).
   $n_matches = preg_match_all ('/(\[qwiz|\[qdeck)[^\]]*?\sqrecord_id\s*=\s*"[^"]+[\s\S]*?(\[\/qwiz\]|\[\/qdeck\])/',
                                $html, $matches, PREG_SET_ORDER);
   if ($debug[6]) {
      error_log ('[qwiz_parse_qrecord_ids] $matches: ' . print_r ($matches, true));
   }
   if (! $n_matches) {
      return array ('', '', '');
   }

   // See if any also have use_dataset=.  Loop over quizzes/decks.
   $use_dataset_qrecord_ids = array ();
   $use_dataset_datasets    = array ();
   $qrecord_id_n_questions  = array ();
   for ($i_qwiz_qdeck=0; $i_qwiz_qdeck<$n_matches; $i_qwiz_qdeck++) {

      // Each element of matches array is an array over three "substring"
      // matches: whole thing, [qwiz|[qdeck, [/qwiz]|[/qdeck].
      $match = $matches[$i_qwiz_qdeck][0];

      // Search for attributes just in [qwiz...] or [qdeck...] shortcode.
      $shortcode_end_pos = strpos ($match, ']');
      if ($shortcode_end_pos === false) {
         $shortcode = '';
      } else {
         $shortcode = substr ($match, 0, $shortcode_end_pos);
      }
      $qrecord_id  = qwiz_get_attr ($shortcode, 'qrecord_id');
      $use_dataset = qwiz_get_attr ($shortcode, 'use_dataset');
      if ($use_dataset) {
         $use_dataset_qrecord_ids[] = $qrecord_id;
         $use_dataset_datasets[]    = $use_dataset;

         // DKTMP: look for [use unit="" topics=""].
      } else {

         $qrecord_id_n_questions[$qrecord_id] = array ();

         // Not use_dataset=.  See if quiz/deck has default unit=.
         $default_unit = qwiz_get_attr ($shortcode, 'unit');
         if ($debug[5]) {
            error_log ("[qwiz_parse_qrecord_ids] qrecord_id: $qrecord_id, default_unit: $default_unit");
         }

         // Find each question tag.  See if question-specific unit; otherwise,
         // assign default.  Count by unit.
         $n_q_matches = preg_match_all ('/\[q\]|\[q\s[^\]]*/', $match, $q_matches);
         if ($debug[5]) {
            error_log ('[qwiz_parse_qrecord_ids] q_matches: ' . print_r ($q_matches, true));
         }
         for ($i_question=0; $i_question<$n_q_matches; $i_question++) {
            $unit = qwiz_get_attr ($q_matches[0][$i_question], 'unit');
            if (! $unit) {
               $unit = $default_unit;
            }
            if (! isset ($qrecord_id_n_questions[$qrecord_id][$unit])) {
               $qrecord_id_n_questions[$qrecord_id][$unit] = 0;
            }
            $qrecord_id_n_questions[$qrecord_id][$unit]++;
         }

         // Erase count for this qrecord_id no units specified for any question
         // in this quiz/deck.
         if (count ($qrecord_id_n_questions[$qrecord_id]) == 1) {
            $keys = array_keys ($qrecord_id_n_questions[$qrecord_id]);
            $unit = $keys[0];
            if ($unit == '') {
               unset ($qrecord_id_n_questions[$qrecord_id]);
            }
         }
      }
   }
   if ($debug[5]) {
      error_log ('[qwiz_parse_qrecord_ids] $qrecord_id_n_questions: ' . print_r ($qrecord_id_n_questions, true));
   }

   return array ($use_dataset_qrecord_ids,
                 $use_dataset_datasets,
                 $qrecord_id_n_questions);
}


