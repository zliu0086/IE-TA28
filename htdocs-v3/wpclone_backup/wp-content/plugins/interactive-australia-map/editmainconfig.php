<?php

$options = freeaustralia_html5map_plugin_get_options();
$option_keys = is_array($options) ? array_keys($options) : array();
$map_id  = (isset($_REQUEST['map_id'])) ? intval($_REQUEST['map_id']) : array_shift($option_keys) ;

$allow_default_zoom =0;if((isset($_POST['act_type']) && $_POST['act_type'] == 'freeaustralia-html5-map-main-save')) {

    $_REQUEST['options']['nameStroke']     = (isset($_REQUEST['options']['nameStroke'])) ? 1 : 0;
    $_REQUEST['options']['name']           = sanitize_text_field(stripslashes($_REQUEST['options']['name']));

    if ($_REQUEST['options']['popupCommentColor'] == 'default')
        $_REQUEST['options']['popupCommentColor'] = '';

    foreach (array('borderColor', 'nameColor', 'popupNameColor', 'popupCommentColor') as $field)
        $_REQUEST['options'][$field]    = $_REQUEST['options'][$field] ?
            freeaustralia_html5map_plugin_chk_color($_REQUEST['options'][$field]) : '';

    if ( ! empty($_REQUEST['options']['defaultAddInfo']))
        $_REQUEST['options']['defaultAddInfo'] = wp_kses_post(stripslashes($_REQUEST['options']['defaultAddInfo']));
    else
        $_REQUEST['options']['defaultAddInfo'] = '';

    if (isset($_REQUEST['options']['shadowWidth'])) {
        if (preg_match('/(\d+[\.,])?\d+/', $_REQUEST['options']['shadowWidth'])) {
            $v = str_replace(',','.', $_REQUEST['options']['shadowWidth']);
            if ($v > 10)
                $v = 10;
            elseif ($v < 0)
                $v = 0.2;
            $_REQUEST['options']['shadowWidth'] = $v;
        } else {
            $_REQUEST['options']['shadowWidth'] = 1.5;
        }
    }

    if (isset($_REQUEST['options']['borderWidth'])) {
        if (preg_match('/(\d+[\.,])?\d+/', $_REQUEST['options']['borderWidth'])) {
            $v = str_replace(',','.', $_REQUEST['options']['borderWidth']);
            if ($v > 3)
                $v = 3;
            elseif ($v < 0)
                $v = 0.2;
            $_REQUEST['options']['borderWidth'] = $v;
        } else {
            $_REQUEST['options']['borderWidth'] = 1.5;
        }
    }

    if (isset($_REQUEST['options']['nameStrokeWidth'])) {
        if (preg_match('/(\d+[\.,])?\d+/', $_REQUEST['options']['nameStrokeWidth'])) {
            $v = str_replace(',','.', $_REQUEST['options']['nameStrokeWidth']);
            if ($v > 3)
                $v = 3;
            elseif ($v < 0)
                $v = 0.2;
            $_REQUEST['options']['nameStrokeWidth'] = $v;
        } else {
            $_REQUEST['options']['nameStrokeWidth'] = 1.5;
        }
    }

    if (isset($_REQUEST['options']['nameStrokeOpacity'])) {
        if (preg_match('/(\d+[\.,])?\d+/', $_REQUEST['options']['nameStrokeOpacity'])) {
            $v = str_replace(',','.', $_REQUEST['options']['nameStrokeOpacity']);
            if ($v > 1)
                $v = 1;
            elseif ($v < 0)
                $v = 0.2;
            $_REQUEST['options']['nameStrokeOpacity'] = $v;
        } else {
            $_REQUEST['options']['nameStrokeOpacity'] = 0.5;
        }
    }


    $clearSlashes = array('nameFontFamily', 'popupCommentFontFamily', 'popupCommentFontFamily');

    foreach($_REQUEST['options'] as $key => $value) if ($key != 'defaultAddInfo' and $key != 'initialZoom') { $_REQUEST['options'][$key] = sanitize_text_field(in_array($key, $clearSlashes) ? stripslashes($value) : $value); }

    if ( ! isset($options[$map_id]['defaultAddInfo']))
        $options[$map_id]['defaultAddInfo'] = '';

    $options[$map_id] = wp_parse_args($_REQUEST['options'],$options[$map_id]);
    if ( ! empty($_REQUEST['options']['hideSN']))
        $options[$map_id]['hideSN'] = true;
    else
        unset($options[$map_id]['hideSN']);

    $options[$map_id]['shadowAllow'] = ( ! empty($_REQUEST['options']['shadowAllow']));
    $options[$map_id]['autoScrollToInfo'] = ( ! empty($_REQUEST['options']['autoScrollToInfo']));
    $options[$map_id]['autoScrollOffset'] = min(500, max(-500, (int)$_REQUEST['options']['autoScrollOffset']));
    $options[$map_id]['freezeTooltipOnClick'] = ( ! empty($_REQUEST['options']['freezeTooltipOnClick']));

    $options[$map_id]['areasList'] = ( ! empty($_REQUEST['options']['areasList']));
    if ($options[$map_id]['areasList']) {
        $options[$map_id]['tooltipOnHighlightIn'] = ( ! empty($_REQUEST['options']['tooltipOnHighlightIn']));
        $options[$map_id]['areaListOnlyActive'] = ( ! empty($_REQUEST['options']['areaListOnlyActive']));
        $options[$map_id]['areasListShowDropDown'] = $_REQUEST['options']['areasListShowDropDown'];
        if ( ! in_array($options[$map_id]['areasListShowDropDown'], array('always', 'mobile'))) {
            $options[$map_id]['areasListShowDropDown'] = false;
        }
    }
    $options[$map_id]['cacheSettings'] = ( ! empty($_REQUEST['options']['cacheSettings']));
    $options[$map_id]['tooltipOnMobileCentralize'] = ( ! empty($_REQUEST['options']['tooltipOnMobileCentralize']));
    $options[$map_id]['minimizeOutput'] = ( ! empty($_REQUEST['options']['minimizeOutput']));
    $options[$map_id]['delayCodeOutput'] = ( ! empty($_REQUEST['options']['delayCodeOutput']));
    
    if (isset($_REQUEST['df_type']) and $_REQUEST['df_type'] == '0') {
        unset($options[$map_id]['externalMapPath']);
    }

    if (isset($_REQUEST['options']['tooltipOnMobileWidth'])) {
        $tcw = (int)$_REQUEST['options']['tooltipOnMobileWidth'];
        if (!$tcw) $tcw = 80;
        $options[$map_id]['tooltipOnMobileWidth'] = min(100, max(50, $tcw)).'%';
    }

    $options[$map_id]['update_time'] = time();
    freeaustralia_html5map_plugin_save_options($options);

}

$defOptions = freeaustralia_html5map_plugin_map_defaults('', 1, true);
foreach ($defOptions as $k => $v) {
    if (!isset($options[$map_id][$k]))
        $options[$map_id][$k] = $v;
}

$mce_options = array(
    //'media_buttons' => false,
    'editor_height'   => 150,
    'textarea_rows'   => 20,
    'textarea_name'   => 'options[defaultAddInfo]',
    'tinymce' => array(
        'add_unload_trigger' => false,
    )
);

echo "<div class=\"wrap freeaustralia-html5-map main full\"><h2>" . __('HTML5 Map Config', 'freeaustralia-html5-map') . "</h2>";
?>
<script xmlns="http://www.w3.org/1999/html">
    jQuery(function($){
        $('.tipsy-q').tipsy({gravity: 'w'}).not('.page-title-action').css('cursor', 'default');

        $('.color~.colorpicker').each(function(){
            $(this).farbtastic($(this).prev().prev());
            $(this).hide();
            $(this).prev().prev().bind('focus', function(){
                $(this).next().next().fadeIn();
            });
            $(this).prev().prev().bind('blur', function(){
                $(this).next().next().fadeOut();
            });
        });

        $('input[name*=isResponsive]').change(function() {

            var resp = $('input[name*=isResponsive]:eq(0)').prop('checked') ? false : true;
            $('input[name*=maxWidth]').prop('disabled', !resp);
            $('input[name*=mapWidth],input[name*=mapHeight]').prop('disabled', resp);

        });
        $('input[name*=isResponsive]').trigger('change');

        $('input[name*=zoomEnable]').change(function() {

            var resp = $('input[name*=zoomEnable]:eq(0)').prop('checked') ? false : true;
            $('input[name*=zoomEnableControls],input[name*=zoomIgnoreMouseScroll],input[name*=zoomMax],input[name*=zoomStep],input[name*=zoomOnlyOnMobile]').prop('disabled', resp);

        });
        $('input[name*=tooltipOnMobileCentralize]').change(function() {
            var resp = $('input[name*=tooltipOnMobileCentralize]:eq(0)').prop('checked') ? false : true;
            $('input[name*=tooltipOnMobileWidth],select[name*=tooltipOnMobileVPosition]').prop('disabled', resp);

        });
        $('input[name*=shadowAllow]').change(function() {
            var resp = $('input[name*=shadowAllow]:eq(0)').prop('checked') ? false : true;
            $('input[name*=shadowWidth]').prop('disabled', resp);
        });
        $('input[name*=zoomEnable],input[name*=shadowAllow],input[name*=tooltipOnMobileCentralize]').trigger('change');

        $('input[name*=statesInfoArea]').change(function() {
            $('input[name*=autoScrollToInfo]').prop('disabled', $('input[name*=statesInfoArea]:checked').val() == 'bottom' ? false : true);
        }).trigger('change');

        $('input[name*="[areasList]"]').change(function() {
            $('input[name*=listWidth],input[name*=listFontSize],input[name*=tooltipOnHighlightIn],input[name*=areaListOnlyActive],select[name*=areasListShowDropDown],select[name*=areasListSorting]').prop('disabled', !$(this).prop('checked'));
        }).trigger('change');

        $('input[name*="autoScrollToInfo"]').change(function() {
            $('#autoScrollOffsetBlock').css('visibility', $(this).prop('checked') ? 'visible' : 'hidden');
        }).trigger('change');

    });
</script>
<br />
<div class="left-block">
<form method="POST" class="">
<?php 
    freeaustralia_html5map_plugin_map_selector('options', $map_id, $options);
    echo "<br /><br />\n";
    freeaustralia_html5map_plugin_nav_tabs('options', $map_id);
?>

    <p><?php echo __('Specify general settings of the map. To choose a color, click a color box, select the desired color in the color selection dialog and click anywhere outside the dialog to apply the chosen color.', 'freeaustralia-html5-map'); ?></p>
    <fieldset>
        <legend><?php echo __('Map Settings', 'freeaustralia-html5-map'); ?></legend>

        <span class="title"><?php echo __('Map name:', 'freeaustralia-html5-map'); ?> </span><input type="text" name="options[name]" value="<?php echo $options[$map_id]['name']; ?>" />
        <span class="tipsy-q" original-title="<?php esc_attr_e('Name of the map', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear"></div>

        <span class="title"><?php echo __('Layout type:', 'freeaustralia-html5-map'); ?> </span>
        <label><?php echo __('Not Responsive:', 'freeaustralia-html5-map'); ?> <input type="radio" name="options[isResponsive]" value=0 <?php echo !$options[$map_id]['isResponsive']?'checked':''?> /></label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label><?php echo __('Responsive:', 'freeaustralia-html5-map'); ?> <input type="radio" name="options[isResponsive]" value=1 <?php echo $options[$map_id]['isResponsive']?'checked':''?> /></label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Type of the layout', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear" style="margin-bottom: 10px"></div>

        <span class="title"><?php echo __('Map width:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[mapWidth]" value="<?php echo $options[$map_id]['mapWidth']; ?>" />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The width of the map', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear"></div>

        <span class="title"><?php echo __('Map height:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[mapHeight]" value="<?php echo $options[$map_id]['mapHeight']; ?>" />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The height of the map', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear"></div>

        <span class="title"><?php echo __('Max width:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[maxWidth]" value="<?php echo $options[$map_id]['maxWidth']; ?>" disabled />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The max width of the map', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear" style="height: 10px"></div>

        <hr/>
        <h4 class="title"><?php echo __('List of names:', 'freeaustralia-html5-map'); ?> </h4><br/>

        <div style="float: left; width: 50%; padding-top: 5px;">
        <span class="title"><?php echo __('Show list of names:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[areasList]" value="1" <?php echo (isset($options[$map_id]['areasList'])&&$options[$map_id]['areasList']) ?'checked':''?> />
        <span class="tipsy-q" original-title="<?php esc_attr_e('Show list of names', 'freeaustralia-html5-map'); ?>">[?]</span><br/>

        <span class="title"><?php echo __('Show only active areas:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[areaListOnlyActive]" value="1" <?php echo $options[$map_id]['areaListOnlyActive'] ?'checked':'' ?> disabled />
        <span class="tipsy-q" original-title="<?php esc_attr_e('Show areas, that contains links or any additional information', 'freeaustralia-html5-map'); ?>">[?]</span><br/>

        <span class="title"><?php echo __('Show name/tooltip on hover:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[tooltipOnHighlightIn]" value="1" <?php echo $options[$map_id]['tooltipOnHighlightIn'] ?'checked':'' ?> disabled />
        <span class="tipsy-q" original-title="<?php esc_attr_e('Show name/tooltip on hover', 'freeaustralia-html5-map'); ?>">[?]</span><br>

        <span class="title"><?php echo __('Dropdown list:', 'freeaustralia-html5-map'); ?> </span><select name="options[areasListShowDropDown]" disabled>
            <option value="" <?php echo $options[$map_id]['areasListShowDropDown'] == '' ? 'selected="selected"' : '' ?>><?php _e('do not show', 'freeaustralia-html5-map') ?></option>
            <option value="mobile" <?php echo $options[$map_id]['areasListShowDropDown'] == 'mobile' ? 'selected="selected"' : '' ?>><?php _e('only on mobile devices', 'freeaustralia-html5-map') ?></option>
            <option value="always" <?php echo $options[$map_id]['areasListShowDropDown'] == 'always' ? 'selected="selected"' : '' ?>><?php _e('on mobile and desktop', 'freeaustralia-html5-map') ?></option>
        </select>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Show dropdown list', 'freeaustralia-html5-map'); ?>">[?]</span><br/>
        </div>

        <div style="float: left; width: 50%;">
        <span class="title"><?php echo __('List width (%):', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[listWidth]" value="<?php echo $options[$map_id]['listWidth']; ?>" disabled />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The width of the list', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear"></div>

        <span class="title"><?php echo __('List font size:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[listFontSize]" value="<?php echo $options[$map_id]['listFontSize']; ?>" disabled />
        <span class="tipsy-q" original-title="<?php esc_attr_e('Font size of the list', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear"></div>
        </div>
        <div class="clear"></div>

<hr>
        <div style="float: left; width: 50%;">
        <label><span class="title"><?php echo __('Hide shortnames:', 'freeaustralia-html5-map'); ?> </span>
        <input type="checkbox" name="options[hideSN]" <?php echo isset($options[$map_id]['hideSN'])?'checked="checked"':''?> /></label>&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="tipsy-q" original-title="<?php esc_attr_e('Do not show shortnames on the map', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear" style="margin-bottom: 10px"></div>

        <label><span class="title"><?php echo __('Enable shadows:', 'freeaustralia-html5-map'); ?> </span>
        <input type="checkbox" name="options[shadowAllow]" <?php echo $options[$map_id]['shadowAllow']?'checked="checked"':''?> /></label>&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="tipsy-q" original-title="<?php esc_attr_e('Enable / disable shadows', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear" style="margin-bottom: 10px"></div>

        <label><span class="title"><?php echo __('Shadow width:', 'freeaustralia-html5-map'); ?> </span>
        <input class="span2" type="text" name="options[shadowWidth]" value="<?php echo (float)($options[$map_id]['shadowWidth']); ?>" /></label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Shadow width', 'freeaustralia-html5-map'); ?>">[?]</span><br />
        </div>

        <div style="float: left; width: 50%;">
        <label><span class="title" style="width: 250px"><?php echo __('Pin tooltip on click:', 'freeaustralia-html5-map'); ?> </span>
        <input type="checkbox" name="options[freezeTooltipOnClick]" <?php echo $options[$map_id]['freezeTooltipOnClick']?'checked="checked"':''?> /></label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Pin tooltip on click', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear" style="margin-bottom: 10px"></div>

        <label><span class="title" style="width: 250px"><?php echo __('Center tooltip on mobile devices:', 'freeaustralia-html5-map'); ?> </span>
        <input class="span2" type="checkbox" name="options[tooltipOnMobileCentralize]" <?php echo $options[$map_id]['tooltipOnMobileCentralize']?'checked="checked"':''; ?> /></label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Center tooltip on mobile devices', 'freeaustralia-html5-map'); ?>">[?]</span><br />
        <div class="clear" style="margin-bottom: 10px"></div>

        <label><span class="title" style="width: 250px"><?php echo __('Centered tooltip width:', 'freeaustralia-html5-map'); ?> </span>
        <input class="span2" type="text" name="options[tooltipOnMobileWidth]" value="<?php echo $options[$map_id]['tooltipOnMobileWidth']; ?>" style="width: 150px"/></label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Centered tooltip width (only on mobile devices)', 'freeaustralia-html5-map'); ?>">[?]</span><br />
<?php if (0) {  // temporary disabled due to pure implementation ?>
        <label><span class="title" style="width: 250px"><?php echo __('Centered tooltip vertical position:', 'freeaustralia-html5-map'); ?> </span>
        <select class="span2" name="options[tooltipOnMobileVPosition]">
            <option value="top" <?php echo $options[$map_id]['tooltipOnMobileVPosition'] == "top" ? 'selected':'' ?>>Over</option>
            <option value="center" <?php echo $options[$map_id]['tooltipOnMobileVPosition'] == "center" ? 'selected':'' ?>>Behind</option>
            <option value="bottom" <?php echo $options[$map_id]['tooltipOnMobileVPosition'] == "bottom" ? 'selected':'' ?>>Under</option>
        </select>
        </label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Tooltip vertical position (only on mobile devices)', 'freeaustralia-html5-map'); ?>">[?]</span><br />
<?php } ?>
        </div>
        <div class="clear" style="margin-bottom: 10px"></div>

<hr>
        <div style="float: left; width: 50%;">
        <span class="title"><?php echo __('Borders color:', 'freeaustralia-html5-map'); ?> </span><input class="color" type="text" name="options[borderColor]" value="<?php echo $options[$map_id]['borderColor']; ?>" style="background-color: #<?php echo $options[$map_id]['borderColor']; ?>" />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The color of borders on the map', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div>
        <div class="clear"></div>

        <span class="title"><?php echo __('Borders width:', 'freeaustralia-html5-map'); ?> </span><input class="" type="text" name="options[borderWidth]" value="<?php echo $options[$map_id]['borderWidth']; ?>" />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The width of borders on the map', 'freeaustralia-html5-map'); ?>">[?]</span>
        <div class="clear"></div>

        </div>

        <div style="float: left; width: 50%;">
        <span class="title"><?php echo __('Borders hover color:', 'freeaustralia-html5-map'); ?> </span><input class="color" type="text" name="options[borderColorOver]" value="<?php echo $options[$map_id]['borderColorOver']; ?>" style="background-color: #<?php echo $options[$map_id]['borderColorOver']; ?>" />
        <span class="tipsy-q" original-title="<?php esc_attr_e('The color of borders on the map while mouse is over this region', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div>
        <div class="clear"></div>

        </div>
    </fieldset>


    <fieldset>
        <legend><?php echo __('Content info', 'freeaustralia-html5-map'); ?></legend>
        <span class="title"><?php echo __('Additional Info area:', 'freeaustralia-html5-map'); ?> </span>
        <label><?php echo __('At right:', 'freeaustralia-html5-map') ?> <input type="radio" name="options[statesInfoArea]" value="right" <?php echo $options[$map_id]['statesInfoArea'] == 'right'?'checked="checked"':''?> /></label>&nbsp;&nbsp;&nbsp;&nbsp;
        <label><?php echo __('At bottom:', 'freeaustralia-html5-map') ?> <input type="radio" name="options[statesInfoArea]" value="bottom" <?php echo $options[$map_id]['statesInfoArea'] == 'bottom'?'checked="checked"':''?> /></label>
        <span class="tipsy-q" original-title="<?php esc_attr_e('Where to place an additional information about state', 'freeaustralia-html5-map'); ?>">[?]</span><br /><br/>
        <div style="float: left; width: 50%;">
            <label><input type="checkbox" name="options[autoScrollToInfo]" <?php echo (isset($options[$map_id]['autoScrollToInfo']) AND $options[$map_id]['autoScrollToInfo'])?'checked="checked"':''?>>
            <?php echo __('Automatically scroll to info area on click', 'freeaustralia-html5-map')?></label>
        </div>
        <div style="float: left; width: 50%; visibility: hidden" id="autoScrollOffsetBlock">
            <span class="title"><?php echo __('Offset from top:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[autoScrollOffset]" value="<?php echo $options[$map_id]['autoScrollOffset']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Offset in px to prevent fixed headers move on info area', 'freeaustralia-html5-map'); ?>">[?]</span>
        </div>
        <div class="clear"></div>
        <div id="action-info">
            <span class="title"><?php echo __('Default content:', 'freeaustralia-html5-map'); ?> <span class="tipsy-q" original-title="<?php esc_attr_e('Default content that will be shown in area for additional information', 'freeaustralia-html5-map'); ?>">[?]</span> </span>
            <br/><br/>
            <?php wp_editor(isset($options[$map_id]['defaultAddInfo']) ? $options[$map_id]['defaultAddInfo'] : '', 'defaultAddInfo', $mce_options); ?>
        </div>
    </fieldset>

    <fieldset class="font-sizes">
        <legend><?php echo __('Font sizes and colors', 'freeaustralia-html5-map'); ?></legend>

        <div style="float: left; width: 50%">
            <h4 class="settings-chapter">
                <?php echo __('Name displayed on the map', 'freeaustralia-html5-map'); ?>
            </h4>

            <span class="title"><?php echo __('Font family:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[nameFontFamily]" value="<?php echo htmlspecialchars($options[$map_id]['nameFontFamily']); ?>" style="width: 200px" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Font family of names on the map', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Font size:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[nameFontSize]" value="<?php echo $options[$map_id]['nameFontSize']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Font size of names on the map', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Color:', 'freeaustralia-html5-map'); ?> </span><input id='color' class="color" type="text" name="options[nameColor]" value="<?php echo $options[$map_id]['nameColor']; ?>" style="background-color: #<?php echo $options[$map_id]['nameColor']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The color of names on the map', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div><br />

            <span class="title"><?php echo __('Color over:', 'freeaustralia-html5-map'); ?> </span><input id='colorOver' class="color" type="text" name="options[nameColorOver]" value="<?php echo $options[$map_id]['nameColorOver']; ?>" style="background-color: #<?php echo $options[$map_id]['nameColorOver']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The color of names on the map while mouse is over', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div><br />

            <span class="title"><?php echo __('Name stroke:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[nameStroke]" value="1" <?php echo $options[$map_id]['nameStroke']?'checked':''?> autocomplete="off" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The stroke on regions names', 'freeaustralia-html5-map'); ?>">[?]</span><br />
            <div class="clear" style="margin-bottom: 10px"></div>

            <span class="title"><?php echo __('Stroke color:', 'freeaustralia-html5-map'); ?> </span><input id='scolor' class="color" type="text" name="options[nameStrokeColor]" value="<?php echo $options[$map_id]['nameStrokeColor']; ?>" style="background-color: #<?php echo $options[$map_id]['nameStrokeColor']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The color of names on the map', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div><br />

            <span class="title"><?php echo __('Stroke color over:', 'freeaustralia-html5-map'); ?> </span><input id='scoloro' class="color" type="text" name="options[nameStrokeColorOver]" value="<?php echo $options[$map_id]['nameStrokeColorOver']; ?>" style="background-color: #<?php echo $options[$map_id]['nameStrokeColorOver']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The color of names on the map while mouse is over', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div><br />

            <span class="title"><?php echo __('Stroke width:', 'freeaustralia-html5-map'); ?> </span><input id='swidth' type="text" name="options[nameStrokeWidth]" value="<?php echo $options[$map_id]['nameStrokeWidth']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Stroke width for names on the map', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Stroke opacity:', 'freeaustralia-html5-map'); ?> </span><input id='sopacity' type="text" name="options[nameStrokeOpacity]" value="<?php echo $options[$map_id]['nameStrokeOpacity']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Stroke opacity for names on the map', 'freeaustralia-html5-map'); ?>">[?]</span><br />

        </div>

        <div style="float: left; width: 50%">
            <h4 class="settings-chapter">
                <?php echo __('Tooltip name', 'freeaustralia-html5-map'); ?>
            </h4>

            <span class="title"><?php echo __('Font family:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[popupNameFontFamily]" value="<?php echo htmlspecialchars($options[$map_id]['popupNameFontFamily']); ?>" style="width: 200px" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Font family of names on the tooltip', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Font size:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[popupNameFontSize]" value="<?php echo $options[$map_id]['popupNameFontSize']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Font size of names on the tooltip', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Color:', 'freeaustralia-html5-map'); ?> </span><input id='pncolor' class="color" type="text" name="options[popupNameColor]" value="<?php echo $options[$map_id]['popupNameColor']; ?>" style="background-color: #<?php echo $options[$map_id]['popupNameColor']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The color of names on the tooltip', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div><br />

            <h4 class="settings-chapter">
                <?php echo __('Tooltip comment', 'freeaustralia-html5-map'); ?>
            </h4>

            <span class="title"><?php echo __('Font family:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[popupCommentFontFamily]" value="<?php echo htmlspecialchars($options[$map_id]['popupCommentFontFamily']); ?>" style="width: 200px" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Font family of content in the tooltip', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Font size:', 'freeaustralia-html5-map'); ?> </span><input class="span2" type="text" name="options[popupCommentFontSize]" value="<?php echo $options[$map_id]['popupCommentFontSize']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('Font size of content in the tooltip', 'freeaustralia-html5-map'); ?>">[?]</span><br />

            <span class="title"><?php echo __('Color:', 'freeaustralia-html5-map'); ?> </span><input id='pccolor' class="color" type="text" name="options[popupCommentColor]" value="<?php echo $options[$map_id]['popupCommentColor'] ? $options[$map_id]['popupCommentColor'] : 'default'; ?>" style="background-color: #<?php echo $options[$map_id]['popupCommentColor']; ?>" />
            <span class="tipsy-q" original-title="<?php esc_attr_e('The color of content in the tooltip', 'freeaustralia-html5-map'); ?>">[?]</span><div class="colorpicker"></div><br />
        </div>

    </fieldset>
    <fieldset style="margin-top: 2px;">
        <legend><?php _e( 'Resource', 'freeaustralia-html5-map' ) ?></legend>

        <span class="title" style="float: left; height: 130px; width: 15%;"><?php _e( 'Path to map data file:', 'freeaustralia-html5-map' ) ?> </span>
        <?php if (isset($options[$map_id]['defaultMapPath'])): ?>
        <div class="radio-block">
            <input type="radio" name="options[df_type]" value="0" <?php echo (!isset($options[$map_id]['externalMapPath']) OR $options[$map_id]['externalMapPath']=='') ? 'checked' : ''; ?> />
            <h4><?php _e( 'data file on html5maps.com', 'freeaustralia-html5-map' ) ?></h4><span class="tipsy-q" original-title="<?php esc_attr_e( 'Path to map data file', 'freeaustralia-html5-map' ) ?>">[?]</span>
            <div class="clear"></div>
            <input type="text" value="<?php echo htmlspecialchars($options[$map_id]['defaultMapPath']); ?>" readonly />
        </div>
        <?php endif; ?>

        <div class="radio-block">
            <input type="radio" name="options[df_type]" value="1" <?php echo (isset($options[$map_id]['externalMapPath']) AND $options[$map_id]['externalMapPath']) ? 'checked' : ''; ?> />
            <h4><?php _e( 'data file on your server', 'freeaustralia-html5-map' ) ?></h4><span class="tipsy-q" original-title="<?php esc_attr_e( 'Path to map data file', 'freeaustralia-html5-map' ) ?>">[?]</span>
            <div class="clear"></div>
            <input type="text" name="options[externalMapPath]" value="<?php echo isset($options[$map_id]['externalMapPath']) ? htmlspecialchars($options[$map_id]['externalMapPath']) : ''; ?>" />
        </div>

    </fieldset>
<?php
$cacheCanBeEnabled = is_writable(dirname(__FILE__).'/static');
$cacheEnabled = (isset($options[$map_id]['update_time']) and isset($options[$map_id]['cacheSettings']) and $options[$map_id]['cacheSettings']);
?>
    <fieldset class="font-sizes">
        <legend><?php echo __('Performance settings', 'freeaustralia-html5-map'); ?></legend>

        <span class="title"><?php echo __('Enable settings caching:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[cacheSettings]" value="1" <?php echo ($cacheEnabled AND $cacheCanBeEnabled) ?'checked':'' ?> <?php echo $cacheCanBeEnabled ? '' : 'disabled' ?> />
        <span class="tipsy-q" original-title="<?php esc_attr_e('This will increase map loading speed', 'freeaustralia-html5-map'); ?>">[?]</span><br />
        <?php if ( ! $cacheCanBeEnabled) { ?>
        <div class="error"><?php echo __('Settings cache cannot be enabled because plugins directory is not writable', 'freeaustralia-html5-map'); ?></div>
        <?php } ?>
        <span class="title"><?php echo __('Minimize code output:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[minimizeOutput]" value="1" <?php echo $options[$map_id]['minimizeOutput'] ?'checked':'' ?> />
        <span class="tipsy-q" original-title="<?php esc_attr_e('With this option enabled code output will be in one line', 'freeaustralia-html5-map'); ?>">[?]</span><br />

        <span class="title"><?php echo __('Delay javascript output:', 'freeaustralia-html5-map'); ?> </span><input type="checkbox" name="options[delayCodeOutput]" value="1" <?php echo $options[$map_id]['delayCodeOutput'] ?'checked':'' ?> />
        <span class="tipsy-q" original-title="<?php esc_attr_e('With this option enabled will be outputed in the end of the page', 'freeaustralia-html5-map'); ?>">[?]</span><br />
        <div class="clear" style="margin-bottom: 10px"></div>

    </fieldset>

    <input type="hidden" name="act_type" value="freeaustralia-html5-map-main-save" />
    <p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes', 'freeaustralia-html5-map'); ?>" class="button-primary" id="submit" name="submit"></p>

</form>
        </div>
        <div class="qanner">
            <a href="https://www.fla-shop.com/products/wp-plugins/oceania/au/?utm_source=australia&utm_medium=dashboard&utm_campaign=image" target="_blank"><img src="<?php echo freeaustralia_html5map_plugin_get_static_url("html5maps_img.png") . "?r=".time();?>" border="0" width="161" height="601"></a>
        </div>

        <div class="clear"></div>
</div>
<?php if ($allow_default_zoom):
$dir = plugins_url('/static/', __FILE__); ?>
<link rel='stylesheet' href='<?php echo $dir ?>css/map.css'>
<style>
#map-preview {
    margin: 0 auto;
    max-width: 700px;
}
#map-preview > div {
    margin: 5px;
}
#map-container {
    padding: 2px;
    border: 1px solid grey;
}
#map-preview .fm-tooltip {
    color: <?php echo $options[$map_id]['popupNameColor']; ?>;
    font-size: <?php echo $options[$map_id]['popupNameFontSize'].'px'; ?>
}
</style>
<script type='text/javascript' src='<?php echo freeaustralia_html5map_plugin_get_raphael_js_url() ?>'></script>
<script type='text/javascript' src='<?php echo freeaustralia_html5map_plugin_get_map_js_url($options[$map_id]) ?>'></script>
<?php
$map_data = (array)json_decode($options[$map_id]['map_data'], true);
foreach ($map_data as &$sd) {
    unset($sd["group"]);
    if (isset($sd['comment']))
        $sd['comment'] = freeaustralia_html5map_plugin_prepare_comment($sd['comment']);
}
unset($sd);
if (isset($options[$map_id]['points']) and $options[$map_id]['points']) foreach ($options[$map_id]['points'] as &$pt) {
    if (isset($pt['comment']))
        $pt['comment'] = freeaustralia_html5map_plugin_prepare_comment($pt['comment']);
}
$map_data = json_encode($map_data);
?>
<script>
    var map_cfg = {

    mapWidth        : 0,
    mapHeight       : 0,

    shadowAllow     : false,

    borderColor     : "<?php echo $options[$map_id]['borderColor']; ?>",
    borderColorOver     : "<?php echo $options[$map_id]['borderColorOver']; ?>",

    nameColor       : "<?php echo $options[$map_id]['nameColor']; ?>",
    popupNameColor      : "<?php echo $options[$map_id]['popupNameColor']; ?>",
    nameFontSize        : "<?php echo $options[$map_id]['nameFontSize'].'px'; ?>",
    popupNameFontSize   : "<?php echo $options[$map_id]['popupNameFontSize'].'px'; ?>",
    nameFontWeight      : "<?php echo $options[$map_id]['nameFontWeight']; ?>",

    zoomEnable              : true,
    zoomOnlyOnMobile        : false,
    zoomEnableControls      : true,
    zoomIgnoreMouseScroll   : false,
    zoomMax   : <?php echo $options[$map_id]['zoomMax']; ?>,
    zoomStep   : <?php echo $options[$map_id]['zoomStep']; ?>,
    initialZoom: null,

    pointColor            : "<?php echo $options[$map_id]['pointColor']?>",
    pointColorOver        : "<?php echo $options[$map_id]['pointColorOver']?>",
    pointBorderColor        : "<?php echo $options[$map_id]['pointBorderColor']?>",
    pointBorderColorOver    : "<?php echo $options[$map_id]['pointBorderColorOver']?>",
    pointNameColor        : "<?php echo $options[$map_id]['pointNameColor']?>",
    pointNameColorOver    : "<?php echo $options[$map_id]['pointNameColorOver']?>",
    pointNameStrokeColor        : "<?php echo $options[$map_id]['pointNameStrokeColor']?>",
    pointNameStrokeColorOver    : "<?php echo $options[$map_id]['pointNameStrokeColorOver']?>",
    pointNameFontSize    : "<?php echo $options[$map_id]['pointNameFontSize']?>",

    overDelay       : <?php echo $options[$map_id]['overDelay']; ?>,
    nameStroke      : <?php echo $options[$map_id]['nameStroke']?'true':'false'; ?>,
    nameStrokeColor : "<?php echo $options[$map_id]['nameStrokeColor']; ?>",
    map_data        : <?php echo $map_data; ?>,
    //ignoreLinks     : true,
    points          : <?php echo (isset($options[$map_id]['points']) and $options[$map_id]['points']) ? json_encode($options[$map_id]['points']) : '{}'; ?>
    };
<?php
    if (file_exists($params_file = dirname(__FILE__).'/static/paths.json')) {
        echo "map_cfg.map_params = ".file_get_contents($params_file).";\n";
    }
?>
        var map = new FlaShopFreeAustraliaMap(map_cfg);
</script>
<?php endif; ?>
