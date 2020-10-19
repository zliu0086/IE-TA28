<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OXI_FLIP_BOX_PLUGINS\Page;

/**
 * Description of Settings
 *
 * @author biplo
 */
class Settings {

    use \OXI_FLIP_BOX_PLUGINS\Inc_Helper\CSS_JS_Loader;

    public $roles;
    public $saved_role;
    public $license;
    public $status;
    public $oxi_fixed_header;
    public $fontawesome;
    public $getfontawesome = [];

    /**
     * Constructor of Oxilab tabs Home Page
     *
     * @since 2.0.0
     */
    public function __construct() {
        $this->admin();
        $this->Render();
    }

    public function admin() {
        global $wp_roles;
        $this->roles = $wp_roles->get_names();
        $this->saved_role = get_option('oxi_addons_user_permission');
        $this->license = get_option('oxilab_flip_box_license_key');
        $this->status = get_option('oxilab_flip_box_license_status');
    }

    public function Render() {
        $this->admin_css_loader();
        ?>
        <div class="wrap">   
            <?php
            echo apply_filters('oxi-flip-box-plugin/admin_menu', TRUE);
            ?>
            <div class="oxi-addons-row">
                <br>
                <br>
                <h2><?php _e('User Settings'); ?></h2>
                <p>Settings for Flipbox - Image Overlay.</p>
                <form method="post" action="options.php">
                    <table class="form-table">
                        <?php settings_fields('oxi-flip-box-settings-group'); ?>
                        <?php do_settings_sections('oxi-flip-box-settings-group'); ?>
                        <tbody>
                            <tr valign="top">
                                <td scope="row">Who Can Edit?</td>
                                <td>
                                    <select name="oxi_addons_user_permission">
                                        <?php foreach ($this->roles as $key => $role) { ?>
                                            <option value="<?php echo $key; ?>" <?php selected($this->saved_role, $key); ?>><?php echo $role; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                    <label class="description" for="oxi_addons_user_permission"><?php _e('Select the Role who can manage This Plugins.'); ?> <a target="_blank" href="https://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table">Help</a></label>
                                </td>
                            </tr>                        
                            <tr valign="top">
                                <td scope="row">Font Awesome Support</td>
                                <td>
                                    <input type="radio" name="oxi_addons_font_awesome" value="yes" <?php checked('yes', get_option('oxi_addons_font_awesome'), true); ?>>YES
                                    <input type="radio" name="oxi_addons_font_awesome" value="no" <?php checked('', get_option('oxi_addons_font_awesome'), true); ?>>No
                                </td>
                                <td>
                                    <label class="description" for="oxi_addons_font_awesome"><?php _e('Load Font Awesome CSS at shortcode loading, If your theme already loaded select No for faster loading'); ?></label>
                                </td>
                            </tr> 
                        </tbody>
                    </table>
                    <?php submit_button(); ?>
                </form>
                <br>
                <br>
                <br>
                <br>
                <h2><?php _e('Product License Activation'); ?></h2>
                <p>Activate your copy to get direct plugin updates and official support.</p>
                <form method="post" action="options.php">
                    <?php settings_fields('oxilab_flip_box_license'); ?>
                    <table class="form-table">
                        <tbody>
                            <tr valign="top">
                                <th scope="row" valign="top">
                                    <?php _e('License Key'); ?>
                                </th>
                                <td>
                                    <input id="oxilab_flip_box_license_key" name="oxilab_flip_box_license_key" type="text" class="regular-text" value="<?php esc_attr_e($this->license); ?>" />
                                    <label class="description" for="oxilab_flip_box_license_key"><?php _e('Enter your license key'); ?></label>
                                </td>
                            </tr>
                            <?php if (!empty($this->license)) { ?>
                                <tr valign="top">
                                    <th scope="row" valign="top">
                                        <?php _e('Activate License'); ?>
                                    </th>
                                    <td>
                                        <?php if ($this->status !== false && $this->status == 'valid') { ?>
                                            <span style="color:green;"><?php _e('active'); ?></span>
                                            <?php wp_nonce_field('oxilab_flip_box_nonce', 'oxilab_flip_box_nonce'); ?>
                                            <input type="submit" class="button-secondary" name="oxilab_flip_box_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                                            <?php
                                        } else {
                                            wp_nonce_field('oxilab_flip_box_nonce', 'oxilab_flip_box_nonce');
                                            ?>
                                            <input type="submit" class="button-secondary" name="oxilab_flip_box_license_activate" value="<?php _e('Activate License'); ?>"/>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>  
        <?php
    }

}
