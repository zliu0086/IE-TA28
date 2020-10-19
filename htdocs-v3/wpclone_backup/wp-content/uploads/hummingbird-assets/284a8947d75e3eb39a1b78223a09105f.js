/**handles:easy_flashcard**/
jQuery(document).ready(function(){

jQuery("#ef_form").validate();
jQuery("#ef_options_form").validate();

jQuery("#ef_insert_new").click(function(){
var count=jQuery('#cards tr').length +1;



var el="<td><input class=deletb  type = 'button' id='deletbutton"+count+ "'name = 'deletb[]' value='remove'/></td>";
jQuery("#cards tr").last().after("<tr  id='deletrow"+count+"'><td><input class='required' type = 'text' name = 'front[]' /></td><td><input class='required'  type = 'text' name = 'back[]' /></td>");
jQuery("td").last().after(el+"</tr>");

jQuery("#deletbutton"+count).on('click',function(){
jQuery("#deletrow"+count).remove();
})


})
})