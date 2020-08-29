<?php
/*
Plugin Name: Easy Flashcards
Plugin URI: 
Description: This lightweight plugin lets you create, design and customize flashcards the way you want them. All created flashcards are easily manageable through your admin panel
Author: Sage Sony
Version: 0.1
Author URI: https://profiles.wordpress.org/sagesony
*/

?>
<?php
 


 //define global variables
 
//global $ef_card_options_alpha;
//$ef_card_options_alpha = get_option('ef_settings');

global $wpdb;
global $ef_table_one;
global $ef_table_two;
global $ef_table_three;

//creates two tables-1)for storing flashcard contents 2)index of all flashcards

function ef_create_tables()
{
 global $wpdb;
 global $ef_table_one;
 global $ef_table_two;
 global $ef_table_three;
 
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $ef_table_three=$wpdb->prefix."ef_card_design";
 
 $sql = "CREATE TABLE $ef_table_one
(
frontside varchar(255),
backside varchar(255),
flashid varchar(255)
);";
 
 $sqllist = "CREATE TABLE $ef_table_two
(
flashcardname varchar(255),
flashid varchar(255)
);";

$sqldesign = "CREATE TABLE $ef_table_three
(
 cardwidth varchar(255),
 cardheight varchar(255),
 bradius varchar(255),
 frontbgcolor varchar(255),
 fronttextcolor varchar(255),
 fronttextsize varchar(255),
 backbgcolor varchar(255),
 backtextcolor varchar(255),
 backtextsize varchar(255)
 
);";
 require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

 dbDelta($sql);
 dbDelta($sqllist);
 dbDelta($sqldesign);
 
 
}

//Generates sample data for demo and fills in the tables

function ef_create_sample_data()
{
 
 global $wpdb;
 
 global $ef_table_one;
 global $ef_table_two;
 global $ef_table_three;
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $ef_table_three=$wpdb->prefix."ef_card_design";
 
 $flashid = 1;
 $ef_countries=array('England','France','USA','China','India','Japan');
$ef_capitals=array('London','Paris','Washington','Beijing','New Delhi','Tokyo');
 
 
 foreach (array_combine($ef_countries, $ef_capitals) as $ef_country => $ef_city) {
 
 $wpdb->insert($ef_table_one, array
(
 'frontside' => $ef_country,
 'backside' => $ef_city,
 'flashid' => $flashid
 
 ));
 
 }
 
$wpdb->insert($ef_table_two, array
(
 'flashcardname' => 'Sample Flashcards-Countries',
 'flashid' => $flashid
));

$wpdb->insert($ef_table_three, array
(
'cardwidth' =>200,
'cardheight'=> 100,
'bradius' =>5,
'frontbgcolor' =>'#353D6C',
'fronttextcolor' => '#FAF371',
'fronttextsize' => '20',
'backbgcolor' => '#F97B7B',
'backtextcolor' => '#FFFFFF',
'backtextsize' =>20

));
 
}


//Generates shortcodes for loading up flashcards

function ef_load_flashcards($atts)
{
 
 //global $ef_card_options_alpha;
 
 //$ef_card_options_alpha = get_option('ef_settings');
 
 global $wpdb;
 global $ef_table_one;
 global $ef_table_two;
 global $ef_table_three;
 
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $ef_table_three=$wpdb->prefix."ef_card_design";
 
 extract(shortcode_atts(array(
 'id' => '1'
 ), $atts));
 
 $sqlnumber = "SELECT * FROM $ef_table_one WHERE flashid=$id;";
 $getnumber = $wpdb->get_results($sqlnumber);
 $sqlcard="SELECT * FROM $ef_table_three";
 $getcarddesigns=$wpdb->get_row($sqlcard);
 
 foreach ($getnumber as $gn) 
{ 

 $ef_content .= "<div class='ef_flashcard' style='width:" . $getcarddesigns->cardwidth . "px;height:" . $getcarddesigns->cardheight . "px; border-radius:" . $getcarddesigns->bradius . "%'><div class='ef_front_face' style='background-color:" . $getcarddesigns->frontbgcolor . ";border-radius:" . $getcarddesigns->bradius . "% ' ><div class='ef_text_front' style='color:" . $getcarddesigns->fronttextcolor . ";font-size:" . $getcarddesigns->fronttextsize . "px'>" . $gn->frontside . "</div></div><div class='ef_back_face' style='background-color:" . $getcarddesigns->backbgcolor . ";border-radius:" . $getcarddesigns->bradius. "%'><p class='ef_text_back' style='color:" . $getcarddesigns->backtextcolor . "; font-size:" . $getcarddesigns->backtextsize. "px'>" . $gn->backside . "</p></div></div>";
 
 }
 

 return $ef_content;
}

// uninstalls on deactivation
function ef_plugin_Uninstall()
{
 
 global $wpdb;
 global $ef_table_one;
 global $ef_table_two;
 global $ef_table_three;
 
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $ef_table_three = $wpdb->prefix . "ef_card_design";
 
 $wpdb->query("DROP TABLE IF EXISTS $ef_table_one;");
 $wpdb->query("DROP TABLE IF EXISTS $ef_table_two;");
 $wpdb->query("DROP TABLE IF EXISTS $ef_table_three;");
 
}


// activation and deactivation hooks
add_shortcode('easy_flashcard', 'ef_load_flashcards');
register_activation_hook(__FILE__, 'ef_create_tables');
register_activation_hook(__FILE__, 'ef_create_sample_data');
register_deactivation_hook(__FILE__, 'ef_plugin_Uninstall');

 //includes
//include 'ef_card_appearance.php';
include 'ef_plugin_options.php';
include 'ef_enqueues.php';
include 'ef_card_design.php';
?>