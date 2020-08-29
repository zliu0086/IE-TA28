<?php

//Option page for adding and managing flashcards
function ef_options_page()
{
 global $wpdb;
 global $ef_table_one;
 global $ef_table_two;
 
 
 
 $delno = htmlspecialchars($_GET['delete']);
 $editno = htmlspecialchars($_GET['edit']);
 if ($delno == 0) {
 if ($editno == 0) {
 
 //displays table of content
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $sql = "SELECT flashid FROM $ef_table_one";
 $sqllist = "SELECT * FROM $ef_table_two";
 $row = $wpdb->get_results($sql);
 $flname = $wpdb->get_results($sqllist);
 echo "<h3>Table of content</h3>";
 echo "<table class = 'widefat fixed'>";
 echo "<tr><td>Flashcard names</td>";
 echo "<td>Shortcode </td>";
 echo "<td> Action</td></tr>";
 foreach ($flname as $fl) {
 echo "<tr><td> <a href = '" . str_replace('%7E', '~', $_SERVER['REQUEST_URI']) . '&amp;edit=' . $fl->flashid . "'>" . $fl->flashcardname . "</a></td>";
 echo "<td>[easy_flashcard id=" . $fl->flashid . "] </td>";
 echo '<td> <a href = "' . str_replace('%7E', '~', $_SERVER['REQUEST_URI']) . '&amp;delete=' . $fl->flashid . '">Delete </a></td></tr>';
 }
 echo "</table>";
 
 
 //displays input forms
 echo "<h3>Create new flashcards</h3>";
 
 echo "<form action='' method=POST id='ef_form'>";
 echo '<input type = "hidden" name = "bravo" value = "x1" />';
 echo 'Name your flashcards: <input type = "text" class="required" name = "flashcardname" />';
 echo "<table id='cards' class = 'widefat fixed'>";
 echo '<tr><td>';
 echo 'front side';
 echo '</td>';
 echo '<td>';
 echo 'back side';
 echo '</td></tr>';
 echo '<tr><td>';
 echo '<input type = "text" class="required" name = "front[]" />';
 echo '</td>';
 echo '<td>';
 echo '<input type = "text" class="required" name = "back[]" />';
 echo '</td></tr>';
 echo '</table>';
 echo '<input type = "button" value ="Insert Another Card" id="ef_insert_new" />';
 echo '<input type="submit" id="to_be" onclick="ef_validate_form(this.id)" name="Submit" class = "button-primary" value="Save" />';
 echo '</form>';
 
 if ($_POST['bravo'] == 'x1') {
 
 global $wpdb;
 global $ef_table_one;
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $sql1 = "SELECT * FROM $ef_table_two ORDER BY flashid DESC LIMIT 1;";
 $lastrow = $wpdb->get_row($sql1);
 $lastflashid = $lastrow->flashid;
 $lastflashid++;
 
 
 $wpdb->insert($ef_table_two, array(
 flashcardname => $_POST['flashcardname'],
 flashid => $lastflashid
 ));
 foreach (array_combine($_POST['front'], $_POST['back']) as $first => $second) {
 $wpdb->insert($ef_table_one, array(
 'frontside' => $first,
 'backside' => $second,
 'flashid' => $lastflashid
 ));
 }
 echo '<script type="text/javascript">window.location = "' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '";</script>';
 
 }
 } else {
 
 //displays form for edition
 global $wpdb;
 global $ef_table_one;
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 $sql3 = "SELECT * FROM $ef_table_one WHERE flashid=$editno";
 $editrow = $wpdb->get_results($sql3);
 $sqlename = "SELECT flashcardname FROM $ef_table_two WHERE flashid=$editno";
 $editname = $wpdb->get_row($sqlename);
 
 
 echo "<form action='' method=POST>";
 echo '<input type = "hidden" name = "delta" value = "x2" />';
 echo "Name your flashcards: <input type = 'text' name = 'flashcardname' class='required' value='" . $editname->flashcardname . "'/>";
 echo "<table id='cards' class = 'widefat fixed'>";
 echo '<tr><td>';
 echo 'front side';
 echo '</td>';
 echo '<td>';
 echo 'back side';
 echo '</td></tr>';
 $efd_count = 0;
 foreach ($editrow as $editss) {
 
 echo "<tr><td>";
 echo "<input type = 'text' class='required' name = 'front[]' value='" . $editss->frontside . "'/>";
 echo '</td>';
 echo '<td>';
 echo "<input type = 'text' class='required' name = 'back[]' value='" . $editss->backside . "'/>";
 echo '</td>';
 echo '<td></tr>';
 
 }
 echo '</table>';
 echo '<input type = "button" value ="Insert Another Card" id="ef_insert_new" />';
 echo '<input type="submit" name="Submit" class = "button-primary" value="Save" id="ef_check_form"/>';
 echo '</form>';
 if ($_POST['delta'] == 'x2') {
 global $wpdb;
 global $ef_table_one;
 global $ef_table_two;
 $ef_table_one = $wpdb->prefix . "ef_cards";
 $ef_table_two = $wpdb->prefix . "ef_index";
 echo $editno;
 $wpdb->delete($ef_table_two, array(
 'flashid' => $editno
 ));
 $wpdb->insert($ef_table_two, array(
 'flashcardname' => $_POST['flashcardname'],
 'flashid' => $editno
 ));
 $wpdb->delete($ef_table_one, array(
 'flashid' => $editno
 ));
 
 foreach (array_combine($_POST['front'], $_POST['back']) as $first => $second) {
 $wpdb->insert($ef_table_one, array(
 'frontside' => $first,
 'backside' => $second,
 'flashid' => $editno
 ));
 }
 
 
 $sql4 = "SELECT * FROM $ef_table_one WHERE flashid =$editno;";
 $lastrow = $wpdb->get_results($sql4);


$ef_url=admin_url("options-general.php?page=ef_flash");

echo '<script type="text/javascript">window.location = "' . $ef_url . '";</script>'; 
 }
 
 }
 } else {
 
 //displays delete page
 global $wpdb;
 global $ef_table_one;
 global $ef_table_two;
 $ef_table_two = $wpdb->prefix . "ef_index";
 
 $deletlist = "SELECT * FROM $ef_table_two WHERE flashid=$delno;";
 
 $deletname = $wpdb->get_row($deletlist);
 echo "</br>";
 echo "<h3>Do you really want to delete flashcards :<strong>" . $deletname->flashcardname . "</strong></h3>";
 echo "<form action='' method=POST>";
 echo '<input type = "hidden" name = "omega" value = "x3" />';
 echo '<input type="submit" name="Submit" value="Cancel and Go back" style="margin-right:20px;"/>';
 echo '</form>';
 echo "<form action='' method=POST>";
 echo '<input type = "hidden" name = "epsilon" value = "x4" />';
 echo '<input type="submit" name="Submit" class = "button-primary" value="Delete" />';
 echo '</form>';
 if ($_POST['epsilon'] == 'x4') {
 
 $wpdb->delete($ef_table_two, array(
 'flashid' => $delno
 ));
 
 $wpdb->delete($ef_table_one, array(
 'flashid' => $delno
 ));
 $ef_url=admin_url("options-general.php?page=ef_flash");

echo '<script type="text/javascript">window.location = "' . $ef_url . '";</script>'; 
 }
 if ($_POST['omega'] == 'x3') {
 
$ef_url=admin_url("options-general.php?page=ef_flash");

echo '<script type="text/javascript">window.location = "' . $ef_url . '";</script>'; 
 }
 
 
 
 }
}



//create dashboard links for menu and submenu
function ef_dashboard_link()
{
 add_options_page('eflash', 'flashcards', 'manage_options', 'ef_flash', 'ef_options_page');
 add_menu_page('eflash', 'Manage Flashcards', 'manage_options', 'ef_flash', 'ef_options_page');
 add_submenu_page('ef_flash', 'flashcard settings', 'Flashcard Settings', 'manage_options', 'ef_settings_submenu', 'ef_card_design_options');
}

add_action('admin_menu', 'ef_dashboard_link');

?>