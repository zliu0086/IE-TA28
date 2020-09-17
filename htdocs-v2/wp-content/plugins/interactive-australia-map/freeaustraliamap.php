<?php
/*
Plugin Name: Interactive Australia Map
Plugin URI: https://fla-shop.com
Description: High-quality map plugin of Australia for WordPress. The map depicts states and features color, font, link and popup customization
Text Domain: freeaustralia-html5-map
Domain Path: /languages
Version: 3.1.8
Author: Fla-shop.com
Author URI: https://www.fla-shop.com
License: GPLv2 or later
*/

add_action('plugins_loaded', 'freeaustralia_html5map_plugin_load_domain' );
function freeaustralia_html5map_plugin_load_domain() {
    load_plugin_textdomain( 'freeaustralia-html5-map', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=='freeaustralia-html5-map-export') { freeaustralia_html5map_plugin_export(); }
if (isset($_REQUEST['action']) && $_REQUEST['action']=='freeaustralia-html5-map-export-csv') { freeaustralia_html5map_plugin_export_csv(); }

add_action('admin_menu', 'freeaustralia_html5map_plugin_menu');


function freeaustralia_html5map_plugin_menu() {

    global $wp_roles;

    $role = "freeaustraliahtml5map_manage_role";

    add_menu_page(__('Australia Map', 'freeaustralia-html5-map'), __('Australia Map', 'freeaustralia-html5-map'), $role, 'freeaustralia-html5-map-options', 'freeaustralia_html5map_plugin_options' );

    add_submenu_page('freeaustralia-html5-map-options', __('General Settings', 'freeaustralia-html5-map'), __('General Settings', 'freeaustralia-html5-map'), $role, 'freeaustralia-html5-map-options', 'freeaustralia_html5map_plugin_options' );
    add_submenu_page('freeaustralia-html5-map-options', __('Detailed settings', 'freeaustralia-html5-map'), __('Detailed settings', 'freeaustralia-html5-map'), $role, 'freeaustralia-html5-map-states', 'freeaustralia_html5map_plugin_states');
    add_submenu_page('freeaustralia-html5-map-options', __('Tools', 'freeaustralia-html5-map'), __('Tools', 'freeaustralia-html5-map'), $role, 'freeaustralia-html5-map-tools', 'freeaustralia_html5map_plugin_tools');
    add_submenu_page('freeaustralia-html5-map-options', __('Map Preview', 'freeaustralia-html5-map'), __('Map Preview', 'freeaustralia-html5-map'), $role, 'freeaustralia-html5-map-view', 'freeaustralia_html5map_plugin_view');

    add_submenu_page('freeaustralia-html5-map-options', __('Maps dashboard', 'freeaustralia-html5-map'), __('Maps', 'freeaustralia-html5-map'), $role, 'freeaustralia-html5-map-maps', 'freeaustralia_html5map_plugin_maps');



}

function freeaustralia_html5map_plugin_nav_tabs($page, $map_id)
{
?>
<h2 class="nav-tab-wrapper">
    <a href="?page=freeaustralia-html5-map-options&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'options' ? 'nav-tab-active' : '' ?>"><?php _e('General settings', 'freeaustralia-html5-map') ?></a>
    <a href="?page=freeaustralia-html5-map-states&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'states' ? 'nav-tab-active' : '' ?>"><?php _e('Detailed settings', 'freeaustralia-html5-map') ?></a>
    <a href="?page=freeaustralia-html5-map-tools&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'tools' ? 'nav-tab-active' : '' ?>"><?php _e('Tools', 'freeaustralia-html5-map') ?></a>
    <a href="?page=freeaustralia-html5-map-view&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'view' ? 'nav-tab-active' : '' ?>"><?php _e('Preview', 'freeaustralia-html5-map') ?></a>
</h2>
<?php
}

function freeaustralia_html5map_plugin_map_selector($page, $map_id, &$options) {
?>
<script type="text/javascript">
jQuery(function($){
    $('select[name=map_id]').change(function() {
        location.href='admin.php?page=freeaustralia-html5-map-<?php echo $page ?>&map_id='+$(this).val();
    });
    $('.tipsy-q').tipsy({gravity: 'w'}).find('span').css('cursor', 'default');
});
</script>
<span class="title" style="width: 100px"><?php echo __('Select a map:', 'freeaustralia-html5-map'); ?> </span>
    <select name="map_id" style="width: 185px;">
        <?php foreach($options as $id => $map_data) { ?>
            <option value="<?php echo $id; ?>" <?php echo ($id==$map_id) ? 'selected' : '';?>><?php echo $map_data['name'] . (isset($map_data['type']) ? " ($map_data[type])" : ''); ?></option>
        <?php } ?>
    </select>
    <span class="tipsy-q" original-title="<?php esc_attr_e('Select a map for editing and previewing', 'freeaustralia-html5-map'); ?>">[?]</span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="admin.php?page=freeaustralia-html5-map-maps" class="page-title-action tipsy-q" original-title="<?php esc_attr_e('List of all maps, creating a new map, backup', 'freeaustralia-html5-map'); ?>"><?php _e('Maps dashboard', 'freeaustralia-html5-map'); ?></a>
<?php
}

function freeaustralia_html5map_plugin_messages($successes, $errors) {
    if ($successes and is_array($successes)) {
        echo "<div class=\"updated\"><ul>";
        foreach ($successes as $s) {
            echo "<li>" . (is_array($s) ? "<strong>$s[0]</strong>$s[1]" : $s) . "</li>";
        }
        echo "</ul></div>";
    }

    if ($errors and is_array($errors)) {
        echo "<div class=\"error\"><ul>";
        foreach ($errors as $s) {
            echo "<li>" . (is_array($s) ? "<strong>$s[0]</strong>$s[1]" : $s) . "</li>";
        }
        echo "</ul></div>";
    }
}

function freeaustralia_html5map_plugin_options() {
    include('editmainconfig.php');
}

function freeaustralia_html5map_plugin_states() {
    include('editstatesconfig.php');
}

function freeaustralia_html5map_plugin_tools() {
    include('maptools.php');
}

function freeaustralia_html5map_plugin_maps() {
    include('mapslist.php');
}

function freeaustralia_html5map_plugin_view() {

    $options = freeaustralia_html5map_plugin_get_options();
    $option_keys = is_array($options) ? array_keys($options) : array();
    $map_id  = (isset($_REQUEST['map_id'])) ? intval($_REQUEST['map_id']) : array_shift($option_keys) ;

?>
<div class="wrap freeaustralia-html5-map main full">
    <h2><?php _e('Map Preview', 'freeaustralia-html5-map') ?></h2>
    <br />
<div class="left-block">
    <form method="POST" class="">
    <?php freeaustralia_html5map_plugin_map_selector('view', $map_id, $options) ?>
    <br /><br />
    </form>
    <style type="text/css">
        .freeaustraliaHtml5MapBold {font-weight: bold}
    </style>
<?php
    freeaustralia_html5map_plugin_nav_tabs('view', $map_id);
    echo '<p>'.sprintf(__('Use shortcode %s for install this map', 'freeaustralia-html5-map'), '<span class="freeaustraliaHtml5MapBold">[freeaustraliahtml5map id="'.$map_id.'"]</span>').'</p>';

    echo do_shortcode('<div style="width: 99%">[freeaustraliahtml5map id="'.$map_id.'"]</div>'); ?>
        </div>
        <div class="qanner">
            <a href="https://www.fla-shop.com/products/wp-plugins/oceania/au/?utm_source=australia&utm_medium=dashboard&utm_campaign=image" target="_blank"><img src="<?php echo freeaustralia_html5map_plugin_get_static_url("html5maps_img.png") . "?r=".time();?>" border="0" width="161" height="601"></a>
        </div>

        <div class="clear"></div>
    </div>
<?php
}

add_action('admin_init','freeaustralia_html5map_plugin_scripts');

function freeaustralia_html5map_plugin_scripts(){
    if ( is_admin() ){

        wp_register_style('jquery-tipsy', plugins_url('/static/css/tipsy.css', __FILE__));
        wp_enqueue_style('jquery-tipsy');
        wp_register_style('freeaustralia-html5-map-adm', plugins_url('/static/css/mapadm.css', __FILE__), array(), '3.1.8');
        wp_enqueue_style('freeaustralia-html5-map-adm');
        wp_register_style('freeaustralia-html5-map-style', plugins_url('/static/css/map.css', __FILE__), array(), '3.1.8');
        wp_enqueue_style('freeaustralia-html5-map-style');
        wp_enqueue_style('farbtastic');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('farbtastic');
        wp_enqueue_script('tiny_mce');
        wp_register_script('jquery-tipsy', plugins_url('/static/js/jquery.tipsy.js', __FILE__));
        wp_enqueue_script('jquery-tipsy');
    }
    else {

        wp_register_style('freeaustralia-html5-map-style', plugins_url('/static/css/map.css', __FILE__), array(), '3.1.8');
        wp_enqueue_style('freeaustralia-html5-map-style');

        wp_register_script('raphael', plugins_url('/static/js/raphael.min.js', __FILE__));
        wp_enqueue_script('raphael');

        wp_enqueue_script('jquery');

    }

    wp_register_script('freeaustralia-html5-map-nicescroll', plugins_url('/static/js/jquery.nicescroll.js', __FILE__));
    wp_enqueue_script('freeaustralia-html5-map-nicescroll');
}

add_action('wp_enqueue_scripts', 'freeaustralia_html5map_plugin_scripts_method');

function freeaustralia_html5map_plugin_scripts_method() {
    wp_enqueue_script('jquery');
    wp_register_style('freeaustralia-html5-map-style', plugins_url('/static/css/map.css', __FILE__));
    wp_enqueue_style('freeaustralia-html5-map-style');

    wp_register_script('freeaustralia-html5-map-nicescroll', plugins_url('/static/js/jquery.nicescroll.js', __FILE__));
    wp_enqueue_script('freeaustralia-html5-map-nicescroll');
}


add_shortcode('freeaustraliahtml5map', 'freeaustralia_html5map_plugin_content');

function freeaustralia_html5map_plugin_enqueue_js($js = null) {
    static $arr = array();
    if ($js) {
        $arr[] = $js;
    } else {
        echo implode("", $arr);
    }
}

function freeaustralia_html5map_plugin_chk_color($val) {
    $val = sanitize_text_field($val);
    if ($val and $val[0] != '#')
        $val = '#' . $val;
    
    return $val;
}

function freeaustralia_html5map_plugin_chk_url($val) {
    return esc_url($val, null, 'url');
}

function freeaustralia_html5map_plugin_escape_fonts($fonts, $addFonts = false) {
    $fonts = $fonts ? explode(',', $fonts) : array();
    if ($addFonts)
        $fonts = array_merge($fonts, explode(',', $addFonts));

    foreach ($fonts as &$f) {
        $f = '\''.trim($f, ' \'"').'\'';
    }
    return implode(',', $fonts);
}

function freeaustralia_html5map_plugin_strcmp($a, $b) {
    if (is_array($a) and isset($a['name'])) {
        $a = $a['name'];
        $b = $b['name'];
    } else if (is_object($a) and isset($a->name)) {
        $a = $a->name;
        $b = $b->name;
    } else {
        return 0;
    }
    if (class_exists('\Collator')) {
        static $coll = null;
        if (is_null($coll)) {
            $coll = new \Collator("");
            $coll->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);
        }
        return $coll->compare($a, $b);
    }
    if (function_exists('strcoll')) {
        return strcoll($a, $b);
    }
    return strnatcmp($a, $b);
}

function freeaustralia_html5map_plugin_rstrcmp($a, $b) {
    return freeaustralia_html5map_plugin_strcmp($b, $a);
}

function freeaustralia_html5map_plugin_sort_regions_list(&$list, $sort = null) {
    $old = @setlocale(LC_COLLATE, 0);
    @setlocale(LC_COLLATE, 'en_US.utf8');
    if ($sort == 'asc') {
        uasort($list, 'freeaustralia_html5map_plugin_strcmp');
    } else if ($sort == 'desc') {
        uasort($list, 'freeaustralia_html5map_plugin_rstrcmp');
    }
    @setlocale(LC_COLLATE, $old);
}



function freeaustralia_html5map_plugin_prepare_tooltip_css($options, $prefix) {
    $commentCss = '';
    if ( ! empty($options['popupCommentColor'])) {
        $commentCss .= "\t\t\t\tcolor: $options[popupCommentColor];\n";
    }
    if ( ! empty($options['popupCommentFontSize'])) {
        $commentCss .= "\t\t\t\tfont-size: $options[popupCommentFontSize]px;\n";
    }
    if ( ! empty($options['popupCommentFontFamily'])) {
        $commentCss .= "\t\t\t\tfont-family: ".freeaustralia_html5map_plugin_escape_fonts($options['popupCommentFontFamily']).";\n";
    }

    $popupTitleCss = '';
    if ( ! empty($options['popupNameColor'])) {
        $popupTitleCss .= "\t\t\t\tcolor: $options[popupNameColor];\n";
    }
    if ( ! empty($options['popupNameFontSize'])) {
        $popupTitleCss .= "\t\t\t\tfont-size: $options[popupNameFontSize]px;\n";
    }
    if ( ! empty($options['popupNameFontFamily'])) {
        $popupTitleCss .= "\t\t\t\tfont-family: ".freeaustralia_html5map_plugin_escape_fonts($options['popupNameFontFamily']).";\n";
    }
    $result = "$prefix .fm-tooltip-name {
$popupTitleCss
}
$prefix .fm-tooltip-comment {
$commentCss
}
$prefix .fm-tooltip-comment p {
$commentCss
}";
    return $result;
}

function freeaustralia_html5map_plugin_escape_js_string($s, $q = '"') {
    return "$q" . str_replace($q, "\\$q", $s) . "$q";
}

function freeaustralia_html5map_plugin_get_map_js_url($options)
{
    $map_file          = plugins_url('/static/', __FILE__)."js/map.js";
    if (isset($options['externalMapPath']) and $options['externalMapPath'])
        $map_file = $options['externalMapPath'];
    else
        $map_file = $options['defaultMapPath'];
    return $map_file;
}

function freeaustralia_html5map_plugin_get_static_url($url)
{
    return plugins_url('/static/', __FILE__).ltrim($url, '/');
}

function freeaustralia_html5map_plugin_get_raphael_js_url()
{
    return '//cdn.html5maps.com/3d_party/raphael.min.js';
}

function freeaustralia_html5map_plugin_content($atts, $content) {
    static $firstRun = true;
    $dir               = plugins_url('/static/', __FILE__);
    $siteURL           = get_site_url();
    $options           = freeaustralia_html5map_plugin_get_options();
    $option_keys       = is_array($options) ? array_keys($options) : array();
    $toSelect          = array();
    $selectColors      = array();
    
    $map_id = isset($atts['id']) ? intval($atts['id']) : array_shift($option_keys);

    if (isset($options[$map_id])) {
        $options = $options[$map_id];
    } else {
        $map_id  = array_shift($option_keys);
        $options = array_shift($options);
    }
    if (isset($atts['select'])) {
        $toSelect = array_map('trim', explode(',', $atts['select']));
        $toSelect = preg_grep('/\S+/', $toSelect);

        if (count($toSelect) and (preg_match('/^#([\da-f]{3}|[\da-f]{6})/i', $toSelect[count($toSelect)-1]) or !preg_match('/^[a-z]{1,}\d+$/i', $toSelect[count($toSelect)-1])))
            array_unshift($selectColors, array_pop($toSelect));

        if (count($toSelect) and (preg_match('/^#([\da-f]{3}|[\da-f]{6})/i', $toSelect[count($toSelect)-1]) or !preg_match('/^[a-z]{1,}\d+$/i', $toSelect[count($toSelect)-1])))
            array_unshift($selectColors, array_pop($toSelect));
    }
    $defOptions = freeaustralia_html5map_plugin_map_defaults('', 1, true);
    foreach ($defOptions as $k => $v) {
        if (!isset($options[$k]))
            $options[$k] = $v;
    }
    $prfx              = "_$map_id";
    $isResponsive      = $options['isResponsive'];
    $stateInfoArea     = $options['statesInfoArea'];
    $respInfo          = $isResponsive ? ' htmlMapResponsive' : '';
    $type_id           = 0;
    $style             = (!empty($options['maxWidth']) && $isResponsive) ? 'max-width:'.intval($options['maxWidth']).'px' : '';

    static $count = 0;
    static $print_action_registered = false;

    $settings_file = freeaustralia_html5map_plugin_settings_url($map_id, $options);

    wp_register_script('raphaeljs', freeaustralia_html5map_plugin_get_raphael_js_url(), array(), '3.1.8');
    wp_register_script('freeaustralia-html5-map-mapjs_'.$type_id, freeaustralia_html5map_plugin_get_map_js_url($options), array('raphaeljs'), '3.1.8');
    wp_register_script('freeaustralia-html5-map-map_cfg_'.$map_id, $settings_file, array('raphaeljs', 'freeaustralia-html5-map-mapjs_'.$type_id));
    wp_enqueue_script('freeaustralia-html5-map-map_cfg_'.$map_id);

    $select_options = "";
    $states = json_decode($options['map_data'], true);
    freeaustralia_html5map_plugin_sort_regions_list($states, 'asc');
    foreach ($states as $sid => $s) {
        if ($options['areaListOnlyActive'] and !$s['link'])
            continue;
        if (isset($s['hidden']) and $s['hidden'])
            continue;
        $select_options .= "\t<option value=\"$sid\">".htmlspecialchars($s['name'])."</option>\n";
    }

    $stateInfoUrl = freeaustralia_html5map_plugin_ajax_url('state_info', array('map_id' => $map_id, 'sid' => ''));
    $groupInfoUrl = freeaustralia_html5map_plugin_ajax_url('group_info', array('map_id' => $map_id, 'gid' => ''));

    $mapInit = "
        <!-- start Fla-shop.com HTML5 Map -->";
    $mapInit .= "
        <div class='freeaustraliaHtml5Map$stateInfoArea$respInfo' style='$style'>";

    $containerStyle  = '';
    $areasJs = '';
    $dropDownHtml = '';
    $dropDownJS = '';
    $selectJs = '';
    if ($options['areasList']) {

        $options['listWidth'] = intval($options['listWidth']) ;
        if ($options['listWidth']<=0) { $options['listWidth'] = 20; }

        $areasList = freeaustralia_html5map_plugin_areas_list($options,$count);

        if ($areasList) {
            $areasJs = '
                jQuery(document).ready(function($) {

                    $( window ).resize(function() {
                        $("#freeaustralia-html5-map-areas-list_'.$count.'").show().css({height: jQuery("#freeaustralia-html5-map-map-container_'.$count.' .fm-map-container").height() + "px"}).niceScroll({cursorwidth:"8px"});
                    });

                    $("#freeaustralia-html5-map-areas-list_'.$count.'").show().css({height: jQuery("#freeaustralia-html5-map-map-container_'.$count.' .fm-map-container").height() + "px"}).niceScroll({cursorwidth:"8px"});

                    $("#freeaustralia-html5-map-areas-list_'.$count.' a").click(function() {

                        var id  = $(this).data("key");
                        var map = freeaustraliahtml5map_map_'.$count.';

                        html5map_onclick(null,id,map);

                        return false;
                    });

                    $("#freeaustralia-html5-map-areas-list_'.$count.' a").on("mouseover",function() {

                        var id  = $(this).data("key");
                        var map = freeaustraliahtml5map_map_'.$count.';

                        map.stateHighlightIn(id);

                    });

                    $("#freeaustralia-html5-map-areas-list_'.$count.' a").on("mouseout",function() {

                        var id  = $(this).data("key");
                        var map = freeaustraliahtml5map_map_'.$count.';

                        map.stateHighlightOut(id);

                    });

                });';

            $containerStyle = 'width: '.($options['statesInfoArea']!='right' ? 100-$options['listWidth'].'%' : 60-$options['listWidth'].'%' ).'; float: left';

            if ($options['areasListShowDropDown']) {
                $showOnMobile = '';
                if ($options['areasListShowDropDown'] == 'mobile') {
                    $showOnMobile = 'mobile-only';
                } else {
                    $areasList = '';
                    $areasJs = '';
                    $containerStyle = '';
                }
                $dropDownHtml = "<div class='freeaustraliaHtml5MapSelector $showOnMobile'><select id='freeaustralia-html5-map-selector_{$count}'>
                    <option value=''>".__('Select an area', 'freeaustralia-html5-map')."</option>
                    $select_options
                </select></div>";

                $dropDownJS = "jQuery('#freeaustralia-html5-map-selector_{$count}').change(function() {
                        var sid = jQuery(this).val();
                        if (hightlighted)
                                freeaustraliahtml5map_map_{$count}.stateHighlightOut(hightlighted);

                        hightlighted = sid;

                        if (sid) {
                            freeaustraliahtml5map_map_{$count}.stateHighlightIn(sid);

                            html5map_onclick(null,sid,freeaustraliahtml5map_map_{$count});
                        }
                    });\n";
            }

            $mapInit.= $areasList;
        }
    }

    if (count($toSelect)) {
        $arr = array(
            'color' => freeaustralia_html5map_plugin_escape_js_string($selectColors ? $selectColors[0] : '#ff0000'),
            'colorOver' => freeaustralia_html5map_plugin_escape_js_string(count($selectColors) == 2 ? $selectColors[1] : '#8f1d21')
        );

        foreach ($toSelect as $stId) {
            $stId = freeaustralia_html5map_plugin_escape_js_string($stId);
            $selectJs .= "if (freeaustraliahtml5map_map_{$count}.mapConfig.map_data[$stId]) {freeaustraliahtml5map_map_{$count}.mapConfig.map_data[$stId].color = $arr[color]; freeaustraliahtml5map_map_{$count}.mapConfig.map_data[$stId].colorOver = $arr[colorOver]; }\n";
            $selectJs .= "else if (freeaustraliahtml5map_map_{$count}.mapConfig.points[$stId]) {freeaustraliahtml5map_map_{$count}.mapConfig.points[$stId].color = $arr[color]; freeaustraliahtml5map_map_{$count}.mapConfig.points[$stId].colorOver = $arr[colorOver]; }\n";
        }
    }


    $mapInit.="$dropDownHtml<div id='freeaustralia-html5-map-map-container_{$count}' class='freeaustraliaHtml5MapContainer' data-map-variable='freeaustraliahtml5map_map_{$count}'></div>";

    if ($options['statesInfoArea']=='bottom') { $mapInit.="<div style='clear:both; height: 20px;'></div>"; }

    $customJs = "";
    if ($options['customJs']) {
        $customJs = "(function (map, containerId, mapId) {\n{$options['customJs']}\n})(freeaustraliahtml5map_map_{$count}, 'freeaustralia-html5-map-map-container_{$count}', $map_id);";
    }

    $mapInit.= "
            <style>
                #freeaustralia-html5-map-map-container_{$count} {
                    $containerStyle
                }
                ".freeaustralia_html5map_plugin_prepare_tooltip_css($options, "#freeaustralia-html5-map-map-container_{$count}")."
                @media only screen and (max-width: 480px) {
                    #freeaustralia-html5-map-map-container_{$count} {
                        float: none;
                        width: 100%;
                    }
                }
            </style>";
    $mapJs =  "<script type=\"text/javascript\">
            jQuery(function(){
                var hightlighted = null;
                freeaustraliahtml5map_map_{$count} = new FlaShopFreeAustraliaMap(freeaustraliahtml5map_map_cfg_{$map_id});
                $selectJs
                freeaustraliahtml5map_map_{$count}.draw('freeaustralia-html5-map-map-container_{$count}');
                freeaustraliahtml5map_map_{$count}.on('mousein', function(ev, sid, map) {
                    if (hightlighted && sid != hightlighted) {
                        map.stateHighlightOut(hightlighted);
                        hightlighted = null;
                    }
                });
                $areasJs
                $customJs

                var html5map_onclick = function(ev, sid, map) {
                var cfg      = freeaustraliahtml5map_map_cfg_{$map_id};
                var link     = map.fetchStateAttr(sid, 'link');
                var is_group = map.fetchStateAttr(sid, 'group');
                var popup_id = map.fetchStateAttr(sid, 'popup-id');
                var is_group_info = false;

                if (typeof cfg.map_data[sid] !== 'undefined')
                        jQuery('#freeaustralia-html5-map-selector_{$count}').val(sid);
                    else
                        jQuery('#freeaustralia-html5-map-selector_{$count}').val('');

                if (is_group==undefined) {

                    if (sid.substr(0,1)=='p') {
                        popup_id = map.fetchPointAttr(sid, 'popup_id');
                        link         = map.fetchPointAttr(sid, 'link');
                    }

                } else if (typeof cfg.groups[is_group]['ignore_link'] == 'undefined' || ! cfg.groups[is_group].ignore_link)  {
                    link = cfg.groups[is_group].link;
                    popup_id = cfg.groups[is_group]['popup_id'];
                    is_group_info = true;
                }
                if (link == '#info') {
                    var id = is_group_info ? is_group : (sid.substr(0,1)=='p' ? sid : map.fetchStateAttr(sid, 'id'));
                    jQuery('#freeaustralia-html5-map-state-info_{$count}').html('". __('Loading...', 'freeaustralia-html5-map') ."');
                    jQuery.ajax({
                        type: 'POST',
                        url: (is_group_info ? '$groupInfoUrl' : '$stateInfoUrl') + id,
                        success: function(data, textStatus, jqXHR){
                            jQuery('#freeaustralia-html5-map-state-info_{$count}').html(data);
                            " . (($options['statesInfoArea'] == 'bottom' AND $options['autoScrollToInfo']) ? "
                            jQuery(\"html, body\").animate({ scrollTop: jQuery('#freeaustralia-html5-map-state-info_{$count}').offset().top - ".$options['autoScrollOffset']." }, 1000);" : "") . "
                        },
                        dataType: 'text'
                    });

                    return false;
                }

                    if (ev===null && link!='') {
                        if (!jQuery('.html5dummilink').length) {
                            jQuery('body').append('<a href=\"#\" class=\"html5dummilink\" style=\"display:none\"></a>');
                        }

                        jQuery('.html5dummilink').attr('href',link).attr('target',(map.fetchStateAttr(sid, 'isNewWindow') ? '_blank' : '_self'))[0].click();

                    }

                };
                freeaustraliahtml5map_map_{$count}.on('click',html5map_onclick);

                $dropDownJS

            });
            </script>";
    if ( ! $options['delayCodeOutput'])
        $mapInit .= $mapJs;
    $mapInit.= "<div id='freeaustralia-html5-map-state-info_{$count}' class='freeaustraliaHtml5MapStateInfo'>".
            (empty($options['defaultAddInfo']) ? '' : apply_filters('the_content',$options['defaultAddInfo']))
            ."</div>
            </div>
            <div style='clear: both'></div>
            <!-- end HTML5 Map -->
    ";

    $count++;

    if ($options['delayCodeOutput']) {
        freeaustralia_html5map_plugin_enqueue_js($options['minimizeOutput'] ? preg_replace('/\s+/', ' ', $mapJs) : $mapJs);

        if ( ! $print_action_registered) {
            if (is_admin()) {
                add_action('admin_footer', 'freeaustralia_html5map_plugin_enqueue_js', 1000);
            } else {
                add_action('wp_footer', 'freeaustralia_html5map_plugin_enqueue_js', 1000);
            }
            $print_action_registered = true;
        }
    }

    if ($options['minimizeOutput'])
        $mapInit = preg_replace('/\s+/', ' ', $mapInit);
    return $mapInit;
}


$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'freeaustralia_html5map_plugin_settings_link' );

function freeaustralia_html5map_plugin_settings_link($links) {
    $settings_link = '<a href="admin.php?page=freeaustralia-html5-map-options">'.__('Settings', 'freeaustralia-html5-map').'</a>';
    array_push($links, $settings_link);
    return $links;
}

function freeaustralia_html5map_plugin_ajax_url($action, $params = array()) {
    $url = admin_url('admin-ajax.php');

    $urlParams = array('action=freeaustraliahtml5map_' . $action);
    foreach ($params as $key => $value) {
        $urlParams[] = $key.'='.urlencode($value);
    }

    $url .= (strpos('?', $url) === false) ? '?' : '&';
    $url .= implode('&', $urlParams);

    return $url;
}

function freeaustralia_html5map_plugin_ajax_get_settings_js() {
    $mapId  = intval($_REQUEST['map_id']);
    $options = freeaustralia_html5map_plugin_get_options();
    $options = isset($options[$mapId]) ? $options[$mapId] : null;
    header( 'Content-Type: application/javascript' );

    if (!$options)
        wp_die();

    $options['map_data'] = str_replace('\\\\n','\\n',$options['map_data']);
    freeaustralia_html5map_plugin_print_map_settings($mapId, $options);

    wp_die();
}
add_action('wp_ajax_freeaustraliahtml5map_settings_js', 'freeaustralia_html5map_plugin_ajax_get_settings_js');
add_action('wp_ajax_nopriv_freeaustraliahtml5map_settings_js', 'freeaustralia_html5map_plugin_ajax_get_settings_js');

function freeaustralia_html5map_plugin_ajax_get_state_info() {
    $mapId  = intval($_REQUEST['map_id']);
    $options = freeaustralia_html5map_plugin_get_options();
    $options = isset($options[$mapId]) ? $options[$mapId] : null;
    if (!$options)
        wp_die();

    $stateId = $_GET['sid'];

    $info = $options['state_info'][$stateId];
    echo apply_filters('the_content',$info);

    wp_die();
}
add_action('wp_ajax_freeaustraliahtml5map_state_info', 'freeaustralia_html5map_plugin_ajax_get_state_info');
add_action('wp_ajax_nopriv_freeaustraliahtml5map_state_info', 'freeaustralia_html5map_plugin_ajax_get_state_info');

add_action('init', 'freeaustralia_html5map_plugin_settings', 100);

function freeaustralia_html5map_plugin_settings() {

    $is_map_call = false;
    foreach($_REQUEST as $key => $value) { if (strpos($key,'freeaustraliahtml5map')!==false) { $is_map_call = true; break; } }
    if (!$is_map_call) { return false; } else {
        remove_all_actions( 'wp_head' );
        remove_all_actions( 'wp_footer' );
    }

    $req_start = microtime(TRUE);
    if (isset($_REQUEST['freeaustraliahtml5map_js_data']) or
        isset($_REQUEST['freeaustraliahtml5map_get_state_info']) or
        isset($_REQUEST['freeaustraliahtml5map_get_group_info'])) {
        $map_id  = intval($_REQUEST['map_id']);
        $options = freeaustralia_html5map_plugin_get_options();
        $options = $options[$map_id];
        if ($options)
            $options['map_data'] = str_replace('\\\\n','\\n',$options['map_data']);
    }


    if( isset($_GET['freeaustraliahtml5map_js_data']) ) {

        header( 'Content-Type: application/javascript' );
        freeaustralia_html5map_plugin_print_map_settings($map_id, $options);
        echo '// Generated in '.(microtime(TRUE)-$req_start).' secs.';
        exit;
    }

    if(isset($_GET['freeaustraliahtml5map_get_state_info'])) {
        $stateId = $_GET['freeaustraliahtml5map_get_state_info'];

        $info = $options['state_info'][$stateId];
        echo apply_filters('the_content',$info);

        exit;
    }

}

function freeaustralia_html5map_plugin_prepare_comment($comment) {
    if (! $comment)
        return $comment;
        return apply_filters('the_content',$comment);
}

function freeaustralia_html5map_plugin_print_map_settings($map_id, &$map_options) {
    if ( ! $map_options) {
        ?>
        var	map_cfg = {
            map_data: {}
        };
        <?php
        return;
    }
    $data = json_decode($map_options['map_data'], true);
    $protected_shortnames = array();
    $siteURL           = get_site_url();
    foreach ($data as $sid => &$d)
    {
        if (isset($d['comment']))
            $d['comment'] = freeaustralia_html5map_plugin_prepare_comment($d['comment']);
        if (isset($d['_hide_name'])) {
            unset($d['_hide_name']);
            $d['name'] = '';
        }
        if (isset($map_options['hideSN']) AND ! in_array($sid, $protected_shortnames))
            $d['shortname'] = '';
        if (isset($d['link']))
            $d['link'] = strpos($d['link'], 'javascript:freeaustraliahtml5map_set_state_text') === 0 ? '#info' : $d['link'];
    }
    unset($d);
    $map_options['map_data'] = json_encode($data);
    $grps = array();
    $defOptions = freeaustralia_html5map_plugin_map_defaults('', 1, true);
    foreach ($defOptions as $k => $v) {
        if (!isset($map_options[$k]))
            $map_options[$k] = $v;
    }
    $jsEncodeOption = 0;
    $mapCfg = array();
    if($map_options['isResponsive']) {
        $mapCfg += array(
            'mapWidth' => 0
        );
    } else {
        $mapCfg += array(
            'mapWidth'  => $map_options['mapWidth'],
            'mapHeight' => $map_options['mapHeight']
        );
    }
    $mapCfg += array(
        'shadowAllow'       => !!$map_options['shadowAllow'],
        'shadowWidth'       => round($map_options['shadowWidth'], 2),
        'shadowOpacity'     => round($map_options['shadowOpacity'], 2),
        'shadowColor'       => $map_options['shadowColor'],
        'shadowX'           => round($map_options['shadowX'], 2),
        'shadowY'           => round($map_options['shadowY'], 2),

        'iPhoneLink'        => $map_options['iPhoneLink'] === 'false' ? false : (!!$map_options['iPhoneLink']),
        'isNewWindow'       => $map_options['isNewWindow'] === 'false' ? false : (!!$map_options['isNewWindow']),

        'borderWidth'       => round($map_options['borderWidth'], 2),
        'borderColor'       => $map_options['borderColor'],
        'borderColorOver'   => $map_options['borderColorOver'],

        'nameColor'         => $map_options['nameColor'],
        'nameColorOver'     => $map_options['nameColorOver'],
        'nameFontFamily'    => $map_options['nameFontFamily'],
        'nameFontSize'      => $map_options['nameFontSize'] . 'px',
        'nameFontWeight'    => $map_options['nameFontWeight'],

        'overDelay'         => $map_options['overDelay'],

        'nameStroke'        => !!$map_options['nameStroke'],
        'nameStrokeColor'   => $map_options['nameStrokeColor'],
        'nameStrokeColorOver'   => $map_options['nameStrokeColorOver'],
        'nameStrokeWidth'   => round($map_options['nameStrokeWidth'], 2),
        'nameStrokeOpacity' => round($map_options['nameStrokeOpacity'], 2),

        'freezeTooltipOnClick'      => !!$map_options['freezeTooltipOnClick'],
        'tooltipOnHighlightIn'      => !!$map_options['tooltipOnHighlightIn'],
        'tooltipOnMobileCentralize' => !!$map_options['tooltipOnMobileCentralize'],
        'tooltipOnMobileWidth'      => $map_options['tooltipOnMobileWidth'],
        'tooltipOnMobileVPosition'  => $map_options['tooltipOnMobileVPosition'],

        'mapId'         => $map_options['mapId'],
        'map_data'      => json_decode($map_options['map_data'], true),
    );
    ?>

    var freeaustraliahtml5map_map_cfg_<?php echo $map_id ?> = <?php echo json_encode($mapCfg, $jsEncodeOption) ?>;

    <?php
    if (file_exists($params_file = dirname(__FILE__).'/static/paths.json')) {
        echo "freeaustraliahtml5map_map_cfg_$map_id.map_params = ".file_get_contents($params_file).";\n";
    }
}


function freeaustralia_html5map_plugin_map_defaults($name='New map', $type=1, $baseOnly=false) {
    $defaults = array(
        'mapWidth'          =>500,
        'mapHeight'         =>500,
        'maxWidth'          =>800,
        'shadowAllow'       => true,
        'shadowWidth'       => 1.5,
        'shadowOpacity'     => 0.2,
        'shadowColor'       => "black",
        'shadowX'           => 0,
        'shadowY'           => 0,
        'iPhoneLink'        => true,
        'isNewWindow'       => false,
        'borderWidth'       => 1.01,
        'borderColor'       => "#ffffff",
        'borderColorOver'   => "#ffffff",
        'nameColor'         => "#ffffff",
        'nameColorOver'     => "#ffffff",
        'nameFontFamily'    => '',
        'nameFontSize'      =>10,
        'nameFontWeight'    => "bold",
        'overDelay'         => 300,
        'statesInfoArea'    => "bottom",
        'autoScrollToInfo'  => 0,
        'autoScrollOffset'  => 0,
        'isResponsive'      => "1",
        'nameStroke'        => true,
        'nameStrokeColor'   => "#000000",
        'nameStrokeColorOver'=> "#000000",
        'nameStrokeWidth'   =>  1.5,
        'nameStrokeOpacity' => 0.5,
        'freezeTooltipOnClick' => false,

        'areasList'         =>false,
        'areasListShowDropDown' => false,
        'areaListOnlyActive'=> false,
        'listWidth'         => '20',
        'listFontSize'      => '14',
        'popupNameColor'    => "#000000",
        'popupNameFontFamily'   => "",
        'popupNameFontSize'     => "20",
        'popupCommentColor'     => '',
        'popupCommentFontFamily'=> '',
        'popupCommentFontSize'  => '',
        'tooltipOnHighlightIn'  => true,
        'tooltipOnMobileCentralize' => true,
        'tooltipOnMobileWidth' => '80%',
        'tooltipOnMobileVPosition' => 'bottom',
        'minimizeOutput' => true,
        'delayCodeOutput' => false,
        'customJs' => '',

    );

    $initialStatesPath = dirname(__FILE__).'/static/settings_tpl.json';
    $defaults['mapId'] = '16rx9o';
    if ($baseOnly)
        return $defaults;
    $defaults['defaultMapPath']   = "//cdn.html5maps.com/libs/locator/3.0.7/au/map.js";
    $defaults['name']           = $name;
    $defaults['update_time']    = time();
    $defaults['map_data']       = file_get_contents($initialStatesPath);
    $defaults['cacheSettings']  = is_writable(dirname(__FILE__).'/static');
    $arr = json_decode($defaults['map_data'], true);
    foreach ($arr as $i) {
        $defaults['state_info'][$i['id']] = '';
    }

    return $defaults;
}

function freeaustralia_html5map_plugin_settings_url($map_id, &$map_options) {
    $cacheURL   = plugins_url('/static/cache', __FILE__);
    $siteURL    = get_site_url();
    //$phpURL     = "{$siteURL}/index.php?freeaustraliahtml5map_js_data=true&map_id=$map_id&r=".rand(11111,99999);
    $phpURL     = freeaustralia_html5map_plugin_ajax_url('settings_js', array('map_id' => $map_id, 'r' => rand(11111,99999)));

    if ( ! $map_options['update_time'])
        return $phpURL;

    if ( ! (isset($map_options['cacheSettings']) and $map_options['cacheSettings']))
        return $phpURL;

    $cache_name = "freeaustralia-html5-map-{$map_id}-{$map_options['update_time']}.js";
    $static_path = dirname(__FILE__).'/static';
    $cache_path  = "$static_path/cache";

    if (!is_writable($static_path))
        return $phpURL;

    if (file_exists("$cache_path/$cache_name"))
        return "$cacheURL/$cache_name";

    if (!file_exists($cache_path)) {
        if (is_writable($static_path))
            mkdir($cache_path);
        else
            return $phpURL;
    }

    if (freeaustralia_html5map_plugin_generate_cache($map_id, $map_options, $cache_path, $cache_name))
        return "$cacheURL/$cache_name";
    else
        return $phpURL;
}

function freeaustralia_html5map_plugin_generate_cache($map_id, &$map_options, $cache_path, $cache_name) {
    $name_prefix = "freeaustralia-html5-map-{$map_id}";
    $dh = opendir($cache_path);
    if (!$dh)
        return false;
    while ($file = readdir($dh)) {
        if (strpos($file, $name_prefix) !== false)
            unlink("$cache_path/$file");
    }
    closedir($dh);

    ob_start();
    freeaustralia_html5map_plugin_print_map_settings($map_id, $map_options);
    $cntnt = ob_get_clean();
    if (file_put_contents("$cache_path/$cache_name", $cntnt))
        return true;
    else
        return false;
}

function freeaustralia_html5map_plugin_group_defaults($name) {
    return array(
        'group_name' => $name,
        '_popup_over' => false,
        '_act_over' => false,
        '_clr_over' => false,
        '_ignore_group' => false,
        'name' => $name,
        'comment' => '',
        'info' => '',
        'image' => '',
        'link' => '',
        'color_map' => '#ffffff',
        'color_map_over' => '#ffffff'
    );
}


function freeaustralia_html5map_plugin_wp_editor_for_tooltip($content, $name = 'tooltip', $id = 'tooltip_editor') {
    wp_editor($content, $id, array(
        'wpautop'       => 1,
        'media_buttons' => 1,
        'textarea_name' => $name,
        'textarea_rows' => 5,
        'tabindex'      => null,
        'editor_css'    => '',
        'editor_class'  => '',
        'teeny'         => 0,
        'dfw'           => 0,
        'tinymce'       => 1,
        'quicktags'     => 1,
        'drag_drop_upload' => false
    ));
}
function freeaustralia_html5map_plugin_get_options($blog_id = null, $option_name = "freeaustraliahtml5map_options") {
    $res = is_multisite() ?
        get_blog_option(is_null($blog_id) ? get_current_blog_id() : $blog_id, 'freeaustraliahtml5map_options') :
        get_site_option($option_name);
    return $res ? $res : array();
}

function freeaustralia_html5map_plugin_save_options(&$options, $blog_id = null, $option_name = "freeaustraliahtml5map_options") {
    if ( is_multisite() ) {
        update_blog_option(is_null($blog_id) ? get_current_blog_id() : $blog_id, 'freeaustraliahtml5map_options', $options);
    } else {
        update_site_option($option_name,$options);
    }
}

function freeaustralia_html5map_plugin_delete_options($blog_id = null) {
    if ( is_multisite() ) {
        delete_blog_option(is_null($blog_id) ? get_current_blog_id() : $blog_id, 'freeaustraliahtml5map_options');
    } else {
        delete_site_option('freeaustraliahtml5map_options');
    }
}

register_activation_hook( __FILE__, 'freeaustralia_html5map_plugin_activation' );

function freeaustralia_html5map_plugin_activation() {

    $options = array(0 => freeaustralia_html5map_plugin_map_defaults());

    add_site_option('freeaustraliahtml5map_options', $options);

}

register_deactivation_hook( __FILE__, 'freeaustralia_html5map_plugin_deactivation' );

function freeaustralia_html5map_plugin_deactivation() {

}

register_uninstall_hook( __FILE__, 'freeaustralia_html5map_plugin_uninstall' );

function freeaustralia_html5map_plugin_uninstall() {
    delete_site_option('freeaustraliahtml5map_options');
}

add_filter('widget_text', 'do_shortcode');


function freeaustralia_html5map_plugin_export() {
    $maps = array();
    if (isset($_REQUEST['map_id']) and is_array($_REQUEST['map_id']))
        $maps = $_REQUEST['map_id'];
    elseif (isset($_REQUEST['maps']))
        $maps = explode(',', $_REQUEST['maps']);
    if ( ! $maps)
        return;
    $options = freeaustralia_html5map_plugin_get_options();

    foreach($options as $map_id => $option) {
        if (!in_array($map_id,$maps)) {
            unset($options[$map_id]);
        }
        unset($options[$map_id]['point_editor_settings']);
    }

    if (count($options)>0) {
        $options = json_encode($options);

        header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
        header('Content-Type: text/json');
        header('Content-Length: ' . (strlen($options)));
        header('Connection: close');
        header('Content-Disposition: attachment; filename="maps.json";');
        echo $options;

        exit();
    }

}

function freeaustralia_html5map_plugin_get_csv_import_export_keys($type) {
    switch ($type) {
        case 'states':
        return array(
            'id'            => 'id',
            'shortname'     => 'shortname',
            'name'          => 'name',
            'comment'       => 'comment',
            'info'          => 'info',
            'image'         => 'image',
            'link'          => 'link',
            'isNewWindow'   => 'isNewWindow',
            'clickAction'   => 'clickAction',
            'popupId'       => 'popup-id',
            'color'         => 'color_map',
            'colorOver'     => 'color_map_over',
            'class'         => 'class',
            'hideName'      => '_hide_name',
            'hideArea'      => 'hidden',
            'groupId'       => 'group'
        );
    }
    return array();
}

function freeaustralia_html5map_plugin_detect_export_click_action(&$row) {
    $action = "none";
    if (!isset($row['link'])) $action = "none";
    elseif(stripos($row['link'], "javascript:[\w_]+_set_state_text") !== false or $row['link'] == '#info' ) $action = "info";
    elseif(trim($row['link']) == "#popup") $action = "popup";
    elseif(trim($row['link']) != "") $action = "link";
    else $action = "none";
    $row['clickAction'] = $action;
}

function freeaustralia_html5map_plugin_export_csv() {
    if ( ! is_admin())
        return;
    remove_all_actions('wp_head');
    remove_all_actions('wp_footer');

    $all_options = freeaustralia_html5map_plugin_get_options();
    $options_keys = array_keys($all_options);
    $def_map_id = reset($options_keys);

    $map_id = isset($_GET['map_id']) ? (int)$_GET['map_id'] : $def_map_id;

    $map_options = &$all_options[$map_id];

    $field_delimiters = array(
        ',' => ',',
        ';' => ';',
        ':' => ':',
        'sp'=> ' ',
        'tb'=> "\t"
    );
    $text_delimiters = array(
        "'" => "'",
        '"' => '"',
        'n' => null
    );

    $fd = stripslashes($_REQUEST['field-delimiter']);
    $td = stripslashes($_REQUEST['text-delimiter']);
    if ( ! array_key_exists($fd, $field_delimiters)) {
        $fd = ',';
    }
    else {
        $fd = $field_delimiters[$fd];
    }
    if ( ! array_key_exists($td, $text_delimiters)) {
        $td = '"';
    }
    else {
        $td = $text_delimiters[$td];
    }

    $tmp_name = tempnam(sys_get_temp_dir(), 'mapcsv');
    $fh = fopen($tmp_name, 'w');
    $import_export_keys = freeaustralia_html5map_plugin_get_csv_import_export_keys('states');
    $header = array_keys($import_export_keys);
    $fields = array_values($import_export_keys);
    fputcsv($fh, $header, $fd, $td);
    $st_params = json_decode($map_options['map_data'], true);
    foreach ($st_params as $id => $params) {
        $params['id'] = $id;
        $params['info'] = isset($map_options['state_info'][$iid = preg_replace('/\D+/', '', $id)]) ?$map_options['state_info'][$iid] : '';
        freeaustralia_html5map_plugin_detect_export_click_action($params);
        $data = array();
        foreach(array('_hide_name', 'isNewWindow', 'hidden') as $f) $params[$f] = empty($params[$f]) ? '' : 'yes';
        foreach ($fields as $f) if ($f)
            $data[$f] = isset($params[$f]) ? $params[$f] : '';
        if ($params['clickAction'] !== 'link') $data['link'] = '';
        fputcsv($fh, $data, $fd, $td);
    }
    fclose($fh);
    header('Content-type: text/csv');
    header('Content-length: '.filesize($tmp_name));
    header('Connection: close');
    header('Content-Disposition: attachment; filename="freeaustraliahtml5map-'.$map_id.'.csv";');
    readfile($tmp_name);
    unlink($tmp_name);
    exit;
}


function freeaustralia_html5map_plugin_import(&$errors) {
    $errors = array();
    $csv_types = array('text/csv','text/comma-separated-values','application/vnd.ms-excel');
    if(is_uploaded_file($_FILES['import_file']["tmp_name"])) {

        if (in_array($_FILES['import_file']['type'], $csv_types))
        {
            $errors[] = sprintf(__('CSV import should be done on the "<a href="%s">Import / Export</a>" tab', 'freeaustralia-html5-map'), "admin.php?page=freeaustralia-html5-map-tools");
            return false;
        }

        $hwnd = fopen($_FILES['import_file']["tmp_name"],'r');
        $data = fread($hwnd,filesize($_FILES['import_file']["tmp_name"]));
        fclose($hwnd);

        $data    = json_decode($data, true);

        if ($data) {
            $def_settings = file_get_contents(dirname(__FILE__).'/static/settings_tpl.json');
            $def_settings = json_decode($def_settings, true);
            $states_count = count($def_settings);
            $options = freeaustralia_html5map_plugin_get_options();

            foreach($data as $map_id => $map_data) {
                if (isset($map_data['map_data']) and $map_data['map_data']) {

                    $data = json_decode($map_data['map_data'], true);
                    $cur_count = $data ? count($data) : 0;
                    $c = $options ? max(array_keys($options))+1 : 0;
                    if ($cur_count != $states_count) {
                        $errors[] = sprintf(__('Failed to import "%s", looks like it is a wrong map. Got %d states when expected states count was: %d', 'freeaustralia-html5-map'), $map_data['name'], $cur_count, $states_count);
                        continue;
                    }
                    $map_data['update_time'] = time();
                    $map_data['map_data'] = preg_replace("/javascript:[\w_]+_set_state_text[^\(]*\([^\)]+\);/", "#info", $map_data['map_data']);
                    $options[]              = $map_data;
                } else {
                   $errors[] = sprintf(__('Section "%s" skipped cause it has no "map_data" block.', 'freeaustralia-html5-map'), $map_id);
                }

            }
            freeaustralia_html5map_plugin_save_options($options);
        } else {
            $errors[] = __('Failed to parse uploaded file. Is it JSON?', 'freeaustralia-html5-map');
        }

        unlink($_FILES['import_file']["tmp_name"]);

    } else {
        $errors[] = __('File uploading error!', 'freeaustralia-html5-map');
    }
    return !count($errors);
}

function freeaustralia_html5map_plugin_areas_list($options,$count) {

    $map_data = (array)json_decode($options['map_data']);
    $areas    = array();
    foreach($map_data as $key => $area) {
        if ($options['areaListOnlyActive'] and !$area->link)
            continue;
        if (isset($area->hidden) and $area->hidden)
            continue;
        $areas[$area->name] = array(
            'id'   => $area->id,
            'key'  => $key,
            'name' => $area->name,
        );
    }

    if (empty($areas))
        return '';

    freeaustralia_html5map_plugin_sort_regions_list($areas, 'asc');

    $options['listFontSize'] = intval($options['listFontSize'])>0 ? intval($options['listFontSize']) : 16;

    $html = "<div class=\"freeaustraliaHtml5Map-areas-list\" id=\"freeaustralia-html5-map-areas-list_{$count}\" style=\"width: ".$options['listWidth']."%;\" data-count=\"$count\">";

    foreach ($areas as $area) {
        $html.="<div class=\"freeaustraliaHtml5Map-areas-item\"><a href=\"#\" style=\"font-size: ".$options['listFontSize']."px\" data-key=\"".$area['key']."\" data-id=\"".$area['id']."\" >".$area['name']."</a></div>";
    }

    $html.= "</div>";

    return $html;
}

function freeaustralia_html5map_plugin_user_has_cap($allcaps, $cap, $args) {

    $user_id      = get_current_user_id();
    $current_user = get_user_by('id', $user_id);
    $allowed      = freeaustralia_html5map_plugin_get_options(null, 'freeaustraliahtml5map_goptions');

    if (!(isset($allowed['roles']) and is_array($allowed['roles']))) {
        $allowed['roles'] = array();
    }

    $allowed['roles']['administrator'] = true;

    foreach($allowed['roles'] as $role => $val) {
        if ($val && $current_user && in_array($role, (array)$current_user->roles)) {
            $allcaps['freeaustraliahtml5map_manage_role'] = true;
            break;
        }
    }

    return $allcaps;
}
add_filter('user_has_cap', 'freeaustralia_html5map_plugin_user_has_cap', 10, 3 );

function freeaustralia_html5map_plugin_convert_old_map() {
    if (get_option('freeaustraliahtml5map_imported_map_key'))
        return;

    $mapData        = get_option('freefreeaustraliahtml5map_map_data');
    if (! $mapData)
        return;
        
    $nameColor      = get_option('freefreeaustraliahtml5map_nameColor');
    //$nameFontSize   = get_option('freefreeaustraliahtml5map_nameFontSize');
    $nameFontSize   = '20';

    $mapData = json_decode($mapData, true);
    if (! $mapData)
        return;

    $name      = __('Automaticaly imported old map', 'freeaustralia-html5-map');
    $defaults  = freeaustralia_html5map_plugin_map_defaults($name, 1);
    $defaults['popupNameColor'] = $nameColor;
    if ($nameFontSize = intval($nameFontSize)) {
        $defaults['popupNameFontSize']   = $nameFontSize;
    }
    $defaults['imported'] = true;
    
    // Replace according to old map
    $colorsMap = array(
        'st1' => array('#1083b5', '#0d678d'),
        'st2' => array('#ff6600', '#e05a00'),
        'st3' => array('#d71d3d', '#c11a37'),
        'st4' => array('#329125', '#2c7f20'),
        'st5' => array('#ffcd00', '#dfb300'),
        'st6' => array('#ff3333', '#ce2929'),
        'st7' => array('#7a4a9f', '#633c81'),
        'st8' => array('#24abe7', '#209acf'),
    );
    
    $newMapData = json_decode($defaults['map_data'], true);

    foreach ($newMapData as $st => &$stData) {
        $old = $mapData[$st];
        $stData['name']         = $old['name'];
        $stData['link']         = $old['link'] ? $old['link'] : '';
        $stData['isNewWindow']  = $stData['link'] == '' ? false : true;
        $stData['image']        = $old['image'];
        $stData['comment']      = $old['comment'];
        
        if (isset($colorsMap[$st])) {
            $stData['color_map']      = $colorsMap[$st][0];
            $stData['color_map_over'] = $colorsMap[$st][1];
        }
        
        $i = $stData['id'];
        
        $info = get_option('freefreeaustraliahtml5map_state_info_'. $i, '');
        $defaults['state_info'][$i] = $info;
        
    }
    unset($stData);
    
    $defaults['map_data'] = json_encode($newMapData);

    $options = freeaustralia_html5map_plugin_get_options();
    $options[] = $defaults;
    
    $lastMapId = count($options) - 1;
    add_option('freeaustraliahtml5map_imported_map_key', $lastMapId);

    freeaustralia_html5map_plugin_save_options($options);

    delete_option('freefreeaustraliahtml5map_nameColor');
    delete_option('freefreeaustraliahtml5map_nameFontSize');
    delete_option('freefreeaustraliahtml5map_map_data');
    
    foreach ($newMapData as $st => $stData) {
        delete_option('freefreeaustraliahtml5map_state_info_'. $stData['id']);
    }

}
add_action('init', 'freeaustralia_html5map_plugin_convert_old_map', 100);

function freeaustralia_html5map_plugin_old_map_shortcode($attrs, $content) {
    $id = get_option('freeaustraliahtml5map_imported_map_key', null);
    if (is_null($id)) {
        return $content;
    }

    $options = freeaustralia_html5map_plugin_get_options();
    if (! (isset($options[$id]) and isset($options[$id]['imported'])))
        return $content;

    if (!is_array($attrs))
        $attrs = array();

    $attrs['id'] = $id;
    return freeaustralia_html5map_plugin_content($attrs, $content);
}
add_shortcode('freeaustraliamap01', 'freeaustralia_html5map_plugin_old_map_shortcode');
