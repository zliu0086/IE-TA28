<?php


function ef_card_design_options(){
 global $wpdb;
 global $ef_table_three;
 $ef_table_three = $wpdb->prefix . "ef_card_design";
 
 $sqlcard="SELECT * FROM $ef_table_three";
 $getcarddesigns=$wpdb->get_row($sqlcard);
?>

<form method="post" action="" id="ef_options_form" >
<input type = "hidden" name = "fi" value = "x5" />;

<div style="width:350px; both:clear">
<h3 style="color:gray;margin:1em 0 .2em .2em;">FlashCard size:</h3>
<table class="widefat fixed" >
<tr>
<td style="width:60%;">Width(px):</td>
<td><input style="width:60%;" type="number" size="5" class="required" name="cardwidth" value="<?php echo $getcarddesigns->cardwidth ?>" /></td>
</tr>
<tr>
<td>Height(px):</td>
<td><input style="width:60%;" type="number" size="5" class="required" name="cardheight" value="<?php echo $getcarddesigns->cardheight ?>" /></td>
</tr>
<tr>
<td>Border Radius(%):</td>
<td><input style="width:60%;" type="number" size="5" class="required" name="bradius" value="<?php echo $getcarddesigns->bradius?>" /></td>
</tr>
</table>
<h3 style="color:gray;margin:1em 0 .2em .2em;">Front side:</h3>
<table class="widefat fixed" >
<tr>
<td style="width:60%;">Background color:</td>
<td><input type="color" size="10" class="required" name="frontbgcolor" value="<?php echo $getcarddesigns->frontbgcolor?>" /></td>
</tr>
<tr>
<td>Text color:</td>
<td><input type="color" size="10" class="required" name="fronttextcolor" value="<?php echo $getcarddesigns->fronttextcolor ?>" /></td>
</tr>

<tr>
<td style="width:60%;">Text size (px):</td>
<td><input type="number" style="width:60%;" size="5" class="required" name="fronttextsize" value="<?php echo $getcarddesigns->fronttextsize ?>" /></td>
</tr>
</table>
<h3 style="color:gray;margin:1em 0 .2em .2em;">Back side:</h3>
<table class="widefat fixed" >
<tr>
<td style="width:60%;">Background color:</td>
<td><input type="color" size="10" class="required" name="backbgcolor" value="<?php echo $getcarddesigns->backbgcolor ?>" /></td>
</tr>
<tr>
<td style="width:60%;">Text color:</td>
<td><input type="color" size="10" class="required" name="backtextcolor" value="<?php echo $getcarddesigns->backtextcolor ?>" /></td>
</tr>
<tr>
<td style="width:60%;">Text size(px):</td>
<td><input style="width:60%;" type="number" size="5" class="required" name="backtextsize" value="<?php echo $getcarddesigns->backtextsize ?>" /></td>
</tr>
</table>
<p class="submit">
<input type="submit" onclick="ef_validate_form(this.id)" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>

<?php
if ($_POST['fi'] == 'x5') {
global $wpdb;
global $ef_table_three;
$ef_table_three = $wpdb->prefix . "ef_card_design";
$sqlcard="SELECT * FROM $ef_table_three";
 $getcarddesigns=$wpdb->get_row($sqlcard);
$width=$getcarddesigns->cardwidth;
 $wpdb->delete($ef_table_three, array(
 'cardwidth' => $width
 ));
 
 $wpdb->insert($ef_table_three, array(
 'cardwidth' => $_POST['cardwidth'],
 'cardheight' => $_POST['cardheight'],
 'bradius' => $_POST['bradius'],
 'frontbgcolor' => $_POST['frontbgcolor'],
 'fronttextcolor' => $_POST['fronttextcolor'],
 'fronttextsize' => $_POST['fronttextsize'],
 'backbgcolor' => $_POST['backbgcolor'],
 'backtextcolor' => $_POST['backtextcolor'],
 'backtextsize' => $_POST['backtextsize']
  ));
  
  echo '<script type="text/javascript">window.location = "' . str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . '";</script> ';

}
}
?>