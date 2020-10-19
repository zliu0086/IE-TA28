<?php

if (!current_user_can('edit_others_pages')) {
	die("Your user account does not have access to these settings");
}


wp_enqueue_style(
    'hdq_admin_style',
    plugin_dir_url(__FILE__) . 'css/hdq_admin.css?v='.HDQ_PLUGIN_VERSION
);
        
wp_enqueue_script(
    'hdq_admin_script',
    plugins_url('/js/hdq_admin.js?v='.HDQ_PLUGIN_VERSION, __FILE__),
    array('jquery', 'jquery-ui-draggable'),
    HDQ_PLUGIN_VERSION,
    true
);

// options
$hd_qu_fb = sanitize_text_field(get_option("hd_qu_fb"));
$hd_qu_tw = sanitize_text_field(get_option("hd_qu_tw"));
$hd_qu_next = sanitize_text_field(get_option("hd_qu_next"));
$hd_qu_finish = sanitize_text_field(get_option("hd_qu_finish"));
$hd_qu_start = sanitize_text_field(get_option("hd_qu_start"));
$hd_results = sanitize_text_field(get_option("hd_results"));
$hd_qu_authors = sanitize_text_field(get_option("hd_qu_authors"));
$hd_qu_percent = sanitize_text_field(get_option("hd_qu_percent"));
$hd_qu_adcode = stripcslashes(urldecode(get_option("hd_qu_adcode")));

// See if the user has posted us some information
if (isset($_POST['hdq_about_options_nonce'])) {
    $hdq_nonce = $_POST['hdq_about_options_nonce'];
    if (wp_verify_nonce($hdq_nonce, 'hdq_about_options_nonce') != false) {
        $hd_qu_fb = sanitize_text_field($_POST["hd_qu_fb"]);
        $hd_qu_tw = sanitize_text_field($_POST["hd_qu_tw"]);
        $hd_qu_next = sanitize_text_field($_POST["hd_qu_next"]);
        $hd_qu_finish = sanitize_text_field($_POST["hd_qu_finish"]);
        $hd_qu_start = sanitize_text_field($_POST["hd_qu_start"]);
        $hd_results = sanitize_text_field($_POST["hd_results"]);

        $hd_qu_authors = "";
        if (isset($_POST["hd_qu_authors"])) {
            $hd_qu_authors = "yes";
        }
        $hd_qu_percent = "";
        if (isset($_POST["hd_qu_percent"])) {
            $hd_qu_percent = "yes";
        }
        $hd_qu_adcode = urlencode($_POST["hd_qu_adcode"]);

        update_option("hd_qu_fb", $hd_qu_fb);
        update_option("hd_qu_tw", $hd_qu_tw);
        update_option("hd_qu_next", $hd_qu_next);
        update_option("hd_qu_finish", $hd_qu_finish);
        update_option("hd_qu_start", $hd_qu_start);
        update_option("hd_results", $hd_results);
        update_option("hd_qu_authors", $hd_qu_authors);
        update_option("hd_qu_percent", $hd_qu_percent);
        update_option("hd_qu_adcode", $hd_qu_adcode);
    }
}
                              
                              
?>
<div id="main" style="max-width: 800px; background: #f3f3f3; border: 1px solid #ddd; margin-top: 2rem">
    <div id="header">
        <h1 id="heading_title" style="margin-top:0">
            HD Quiz - About / Options
        </h1>
    </div>


    <p>HD Quiz was designed and developed to be one of the easiest and most hassle free quiz builders for WordPress. If
        you
        have any questions, or need support, please contact me at the <a
            href="https://wordpress.org/support/plugin/hd-quiz" target="_blank">official WordPress HD Quiz support
            forum</a>.</p>

    <p>As I continue to develop HD Quiz, more features, options, customizations, and settings will be introduced. If you
        have enjoyed HD Quiz, then I would sure appreciate it if you could <a
            href="https://wordpress.org/support/plugin/hd-quiz/reviews/#new-post" target="_blank">leave an honest
            review</a>. It's the little things that make building systems like this worthwhile ❤.</p>

	<hr style = "margin-top:2rem"/>
	
	<form id = "hdq_settings" method="post">
		
	
	<input type="hidden" name="hdq_submit_hidden" value="Y">
	<?php wp_nonce_field('hdq_about_options_nonce', 'hdq_about_options_nonce');?>	
	
	<div style = "display: grid; grid-template-columns: 1fr max-content; align-items: center;">		
    <h2>
        Settings
    </h2>
	<div>
		<input type="submit" title = "save HDQ settings" class="hdq_button" id="hdq_save_settings" value="SAVE">
	</div>
	</div>
    <h4 style="margin-bottom: 1rem">
        Social Share
    </h4>
    <div class="hdq_row hdq_col-1-1">

        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_fb">
                Facebook APP ID <span class="hdq_tooltip">
                    ?
                    <span class="hdq_tooltip_content">
                        <span>This is needed to allow Facebook to share dynamic content - the results of the quiz. If
                            this is not used, then Facebook will share the page without the results.</span>
                    </span>
                </span>
            </label>
            <input type="text" class="hdq_input" id="hd_qu_fb" name="hd_qu_fb" value="<?php echo $hd_qu_fb; ?>"
                placeholder="leave blank to use default sharing">
        </div>

        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_tw">
                Twitter Handle <span class="hdq_tooltip">
                    ?
                    <span class="hdq_tooltip_content">
                        <span>This is used if you have sharing results enabled. The sent tweet will contain a mention to
                            your account for extra exposure. </span>
                    </span>
                </span>
            </label>
            <input type="text" class="hdq_input" id="hd_qu_tw" name="hd_qu_tw" value="<?php echo $hd_qu_tw; ?>"
                placeholder="do NOT include the @ symbol">
        </div>
    </div>

    <h4 style="margin-bottom: 1rem">
        Rename / Translate
    </h4>
    <p>
        <small>The following translations will update the text on the quizzes.</small>
    </p>
    <div class="hdq_row hdq_col-1-1">

        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_finish">
                Rename "Finish" Button
            </label>
            <input type="text" class="hdq_input" id="hd_qu_finish" name="hd_qu_finish" value="<?php echo $hd_qu_finish; ?>" placeholder="">
        </div>

        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_next">
                Rename "Next" Button
            </label>
            <input type="text" class="hdq_input" id="hd_qu_next" name="hd_qu_next" value="<?php echo $hd_qu_next; ?>" placeholder="">
        </div>

        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_results">
                Rename "Results" text
            </label>
            <input type="text" class="hdq_input" id="hd_results" name="hd_results" value="<?php echo $hd_results; ?>" placeholder="">
        </div>
		
        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_start">
                Rename "QUIZ START" text <span class="hdq_tooltip">
                    ?
                    <span class="hdq_tooltip_content">
                        <span>Used if you are a timer feature, or for direct links to the quiz on category/search pages</span>
                    </span>
                </span>
            </label>
            <input type="text" class="hdq_input" id="hd_qu_start" name="hd_qu_start" value="<?php echo $hd_qu_start; ?>" placeholder="">
        </div>		
    </div>

    <h4 style="margin-bottom: 1rem">
        Extra Settings
    </h4>
    <div class="hdq_row hdq_col-1-1">
        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_authors">
                Allow Authors Access To Create Quizzes <span class="hdq_tooltip">
                    ?
                    <span class="hdq_tooltip_content">
                        <span>By default, only Editors or Admins can add or edit questions. Enabling this will allow
                            Authors to
                            create quizzes as well.</span>
                    </span>
                </span></label>

            <div class="hdq_checkbox_container">
                <div class="hdq_checkbox">
                    <input type="checkbox" value="yes" data-type="radio" class="hdq_checkbox_input" name="hd_qu_authors"
                        id="hd_qu_authors" <?php if ($hd_qu_authors === "yes") {
    echo "checked";
} ?>>
                    <label for="hd_qu_authors"></label>
                </div>
                <label for="hd_qu_authors"></label>
            </div>
        </div>

        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_percent">
                Enable Percent Results <span class="hdq_tooltip">
                    ?
                    <span class="hdq_tooltip_content">
                        <span>By default, HD Quiz will only show the score as a fraction (example: 9/10). Enabling this
                            will also show the score as a percentage (example: 90%) </span>
                    </span>
                </span></label>

            <div class="hdq_checkbox_container">
                <div class="hdq_checkbox">
                    <input type="checkbox" value="yes" data-type="radio" class="hdq_checkbox_input" name="hd_qu_percent"
                        id="hd_qu_percent" <?php if ($hd_qu_percent === "yes") {
    echo "checked";
} ?>>
                    <label for="hd_qu_percent"></label>
                </div>
                <label for="hd_qu_percent"></label>
            </div>
        </div>
    </div>

    <div class="hdq_row">
        <div class="hdq_input_item">
            <label class="hdq_input_label" for="hd_qu_adcode">
                Adset code <span class="hdq_tooltip">
                    ?
                    <span class="hdq_tooltip_content">
                        <span>If you are using Google Adsense or something similar, you can paste your ad code here. HD
                            Quiz will display the ad after every 5th question.</span>
                    </span>
                </span>
            </label>
            <textarea rows="6" class="hdq_input" id="hd_qu_adcode" name="hd_qu_adcode"
                placeholder="paste code here"><?php echo $hd_qu_adcode; ?></textarea>
        </div>
    </div>

	</form>
	
    <div class="hdq_highlight" id="hd_patreon">
        <div id="hd_patreon_icon">
            <img src="https://dev.harmonicdesign.ca/wp-content/plugins/hd-quiz/includes/settings/../images/hd_patreon.png"
                alt="Donate">
        </div>
        <p>
            HD Quiz is a 100% free plugin developed in my spare time, and as such, I get paid in nothing but good will
            and positive reviews. If you are enjoying HD Quiz and would like to show your support, please consider
            contributing to my <a href="https://www.patreon.com/harmonic_design" target="_blank">patreon page</a> to
            help continued development. Every little bit helps, and I am fuelled by ☕.
        </p>
    </div>
    <br />

    <h2>Upcoming Features</h2>
    <p>I am developing HD Quiz in my spare time, but still plan to add the following features at some point</p>
    <ul class="hdq_list">
        <li>Quiz styler <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This
                        would allow you to change the fonts and colours of the quizzes across your
                        site</span></span></span></li>
        <li>Logged in only <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>This
                        would hide the quiz for non-registered users.</span></span></span></li>
        <li>Global / default quiz options <span class="hdq_tooltip hdq_tooltip_question">?<span
                    class="hdq_tooltip_content"><span>These would become the default for all quizzes, but could be
                        overridden on a per-quiz basis.</span></span></span></li>
        <li>Translation ready (please contacting me if you are interested in helping with translations)</li>
    </ul>

    <h2>Quick Documentation</h2>	
	<center>
		<iframe width="560" height="315" src="https://www.youtube.com/embed/IgDada_WqNw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		<p>
			<small>watch a full tutorial on how to use HD Quiz</small>
		</p>
		
	</center>
	<br/>
	
    <p>HD Quiz was designed to be as easy and intuitive to use as possible. However, I understand that some guidance
        might still be needed. The following are the "quick steps" needed to create your first quiz!</p>	
	
    <div class="hdq_accordion">
        <h3>Using A Quiz - Adding Quiz To a Page</h3>
        <div>
            <p>
                HD Quiz uses shortcodes to render a quiz, so you can place a quiz almost anywhere on your site!
            </p>
            <ul class="hdq_list">
                <li>To find the shortcode for a quiz, select HD Quiz -&gt; Quizzes in the left menu.</li>
                <li>You will now see a list of all of your quizzes in a table, with the shortcode listed. <span class="hdq_tooltip hdq_tooltip_question">?<span class="hdq_tooltip_content"><span>Example shortcode <code>[HDquiz quiz = "xxx"]</code></span></span></span></li>
                <li>Copy and paste the shortcode into any page or post you want to render that quiz!</li>
            </ul>
            <p>
                <strong>Gutenberg</strong>: HD Quiz is also fully Gutenberg compatible, and I even coded in a custom
                Gutenberg block to make adding quizzes easier than ever! If you are using Gutenberg, then you can add
                the HD Quiz block. A block will be added to your editor and will automatically populate a list of all of
                your quizzes. Simply select the quiz you wish to add and save.
            </p>
        </div>
    </div>
    <div class="hdq_accordion">
        <h3>Changing Question Order</h3>
        <div>
            <p>
                The latest and greatest version of HD Quiz makes creating custom question order easier than
                ever! When
                editing a quiz, you can simply drag and drop to change the order of the questions. Just
                remember to save
                the quiz when done!
            </p>
        </div>
    </div>
    <div class="hdq_accordion">
        <h3>Need More Help?</h3>
        <div>
            <p>
                This is a free WordPress plugin, so I just get pure unfiltered satisfaction knowing that you use
                and love HD Quiz.</p>
            <p>So, loyal HD Quiz user, if you need help, please don't hesitate to leave us a message or question on the
                <a href="https://wordpress.org/support/plugin/hd-quiz" target="_blank">official WordPress HD Quiz
                    Support Forum</a>, or on our own <a href="http://harmonicdesign.ca/hd-quiz/" target="_blank">support
                    page at Harmonic Design</a>.</p>
        </div>
    </div>
</div>