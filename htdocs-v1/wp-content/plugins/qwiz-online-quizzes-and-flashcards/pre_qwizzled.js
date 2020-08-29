// Preliminary - locate editing area, show menu if not wizard.

// Called by TinyMCE startup or by added "Q" button (both via qwiz_tinymce.js).

// Do-nothing function for old IE.
if (! window.console) {
   window.console = {log: function(){} };
}


// =============================================================================
// Isolate namespace.
pre_qwizzled = {};
var pre_qwizzledf = function () {
// =============================================================================
//

// Debug settings.
var debug = [];
debug.push (false);    // 0 - general.

var $ = jQuery;

// Private data, but global to this pre_qwizzled instance.
var q = this;

// The identifier -- including qualifiers like "#" -- of the editing frame on
// WordPress.
var edit_area_selector = 'iframe#content_ifr, iframe#wpb_tinymce_content_ifr';
var $edit_area;


// -----------------------------------------------------------------------------
this.load_qwizzled_if_needed = function (ed, qwiz_button_b) {

   // Called from qwiz_tinymce.js.
   if (debug[0]) {
      console.log ('[load_qwizzled_if_needed] ed:', ed);
   }

   // Don't do if using wizard.
   if (qwizzled.qwizard_b) {
      return false;
   }
   var ok_f = false;
   var gutenberg_f = false;

   // Is the "classic" editor (in iframe) being used?
   if ($ (edit_area_selector).length) {
      if (debug[0]) {
         console.log ('[load_qwizzled_if_needed] $ (edit_area_selector):', $ (edit_area_selector));
      }

      // Is the visual editing frame present?
      if ($ (edit_area_selector).is (':visible')) {
         $edit_area = $ (edit_area_selector).contents ().find ('body');
         if ($edit_area.length > 0) {
            ok_f = true;
         }
      }
   } else {

      // No. should be Gutenberg block editor.
      gutenberg_f = true;

      if ($ (ed.targetElm).hasClass ('wp-block-freeform')
          || $ (ed.targetElm).hasClass ('wp-block-qwizcards-blocks-editable')) {
         $edit_area = $ (ed.targetElm);
         if (debug[0]) {
            console.log ('[load_qwizzled_if_needed (Gutenberg)] $edit_area:', $edit_area);
         }

         // Do only for Q-button press.
         ok_f = qwiz_button_b;
      }
   }
   if (ok_f) {

      // If auto-started (not Q button press) in qwiz_tinymce.js, see if [qwiz]
      // or [qdeck] on page.  Show menu only if one is.
      if (! qwiz_button_b) {
         var $contains_qwiz  = $edit_area.find ('*:contains("[qwiz")');
         var $contains_qdeck = $edit_area.find ('*:contains("[qdeck")');
         if (debug[0]) {
            console.log ('[load_qwizzled_if_needed] $contains_qwiz:', $contains_qwiz, ', $contains_qdeck:', $contains_qdeck);
         }
         if ($contains_qwiz.length == 0 && $contains_qdeck.length == 0) {

            // No [qwiz] or [qdeck] on page.  Do nothing.
            return false;
         }
      }

      // Proceed to qwizzled.  Closure to pass editor instance.
      function menu_start () {
         if (debug[0]) {
            var msec = new Date ().getTime ();
            console.log ('[pre_qwizzled.js > menu_start] msec:', msec);
         }
         if (typeof (qwizzled) == 'undefined') {
            setTimeout (menu_start, 10);
         } else {
            qwizzled.show_main_menu (ed, qwiz_button_b);
         }
      }

      setTimeout (menu_start, 10);
   } else {

      // Couldn't find editing window.  Error message only if Q button pressed.
      if (qwiz_button_b) {
         console.log ('[load_qwizzled_if_needed] ed:', ed);
         if (gutenberg_f) {
            alert ('Could not find editor block.');
         } else {
            alert ('Could not find editing window.  You need to be editing a page or post in Visual mode.');
         }
         return false;
      }
   }
}


// =============================================================================
// Close - isolate namespace.
};


// -----------------------------------------------------------------------------
pre_qwizzledf.call (pre_qwizzled);


