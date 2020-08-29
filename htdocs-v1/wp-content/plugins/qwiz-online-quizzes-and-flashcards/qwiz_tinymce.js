(function() {
   var debug = false;

   // Register the button.
   tinymce.create ('tinymce.plugins.qwiz_tinymce', {
      init : function (ed, url) {
         if (debug) {
            console.log ('[qwiz_tinymce > init] ed:', ed);
         }

         // Part of init: run qwizzled.  Do only for main editor (in case
         // others are present).  In Gutenberg, only classic editor
         // ("freeform").
         if (ed.id == 'content'
                      || jQuery (ed.targetElm).hasClass ('wp-editor-area')
                      || jQuery (ed.targetElm).hasClass ('wp-block-freeform')) {
            run_qwizzled (ed);

            // Run-qwizzled button.
            ed.addButton ('button_q', {
               title:   'Qwizcards - show/restart editing menu',
               image:   qwizzled_params.url + 'images/icon_qwiz.png',
               onclick: function () {
                  if (typeof (qwizzled) == 'undefined') {
                     //console.log ('[qwiz_tinymce > create > load_qwizzled_if_needed]'); 

                     // Doesn't do load any more, but does set edit-area
                     // variables.
                     pre_qwizzled.load_qwizzled_if_needed (ed, true);
                  } else {
                     qwizzled.show_main_menu (ed, true);
                  }
               }
            });
         }
      },
      createControl : function (n, cm) {
         return null;
      },
   });

   // Start the buttons.
   tinymce.PluginManager.add ( 'qwizzled_button_script', tinymce.plugins.qwiz_tinymce );

   // Start qwizzled.js (show editing menu).
   function run_qwizzled (ed) {

      // Closure to pass editor instance.
      function run_pre_qwizzled () {
         if (debug) {
            var msec = new Date ().getTime ();
            console.log ('qwiz_tinymce.js [run_pre_qwizzled] msec', msec);
         }

         // Keep looking for pre_qwizzled until it shows up.
         if (typeof (pre_qwizzled) == 'undefined') {
            setTimeout (run_pre_qwizzled, 10);
         } else {

            // Doesn't do load any more, but does set edit-area variables.
            pre_qwizzled.load_qwizzled_if_needed (ed, false);
         }
      }

      setTimeout (run_pre_qwizzled, 10);
   }

})();
