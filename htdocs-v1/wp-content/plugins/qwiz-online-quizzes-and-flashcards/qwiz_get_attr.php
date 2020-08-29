<?php
// -----------------------------------------------------------------------------
function qwiz_get_attr ($shortcode, $attribute) {
   $match_f = preg_match ('/' . $attribute . '\s*=\s*"([^"]+)"/', $shortcode,
                          $shortcode_matches);
   if ($match_f) {
      $attr_value = trim ($shortcode_matches[1]);
   } else {
      $attr_value = '';
   }

   return $attr_value;
}


function qwiz_get_attr2 ($shortcode, $attribute) {
   $attr_value = '';

   // If attribute value doesn't begin with quote, get characters up to space
   // or "]".
   $match_f = preg_match ('/' . $attribute . '\s*=\s*\S/', $shortcode,
                          $shortcode_matches);
   if ($match_f) {
      if (substr ($shortcode_matches[0], -1) == '"') {
         $attr_value = qwiz_get_attr ($shortcode, $attribute);
      } else {
         $match_f = preg_match ('/' . $attribute . '\s*=\s*([^\s\]]+)/', $shortcode,
                                $shortcode_matches2);
         $attr_value = $shortcode_matches2[1];
      }
   }
   return $attr_value;
}

