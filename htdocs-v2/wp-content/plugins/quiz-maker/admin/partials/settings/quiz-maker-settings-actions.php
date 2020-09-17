<?php
class Quiz_Maker_Settings_Actions {
    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
    }

    public function store_data($data){
//        var_dump($data); die();
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        if( isset($data["settings_action"]) && wp_verify_nonce( $data["settings_action"], 'settings_action' ) ){
            $success = 0;
            $paypal_client_id = isset($data['ays_paypal_client_id']) ? $data['ays_paypal_client_id'] : '';
            $paypal_payment_terms = isset($data['ays_paypal_payment_terms']) ? $data['ays_paypal_payment_terms'] : '';
            $roles = (isset($data['ays_user_roles']) && !empty($data['ays_user_roles'])) ? $data['ays_user_roles'] : array('administrator');
            $mailchimp_username = isset($data['ays_mailchimp_username']) ? $data['ays_mailchimp_username'] : '';
            $mailchimp_api_key = isset($data['ays_mailchimp_api_key']) ? $data['ays_mailchimp_api_key'] : '';
            $mailchimp = array(
                'username' => $mailchimp_username,
                'apiKey' => $mailchimp_api_key
            );
            
			$monitor_client  = isset($data['ays_monitor_client']) ? $data['ays_monitor_client'] : '';
			$monitor_api_key = isset($data['ays_monitor_api_key']) ? $data['ays_monitor_api_key'] : '';
			$monitor         = array(
				'client' => $monitor_client,
				'apiKey' => $monitor_api_key
			);

			$slack_client = isset($data['ays_slack_client']) ? $data['ays_slack_client'] : '';
			$slack_secret = isset($data['ays_slack_secret']) ? $data['ays_slack_secret'] : '';
			$slack_token  = !empty($data['ays_slack_token']) ? $data['ays_slack_token'] : '';
			$slack        = array(
				'client' => $slack_client,
				'secret' => $slack_secret,
				'token'  => $slack_token,
			);

			$active_camp_url     = isset($data['ays_active_camp_url']) ? $data['ays_active_camp_url'] : '';
			$active_camp_api_key = isset($data['ays_active_camp_api_key']) ? $data['ays_active_camp_api_key'] : '';
			$active_camp         = array(
				'url'    => $active_camp_url,
				'apiKey' => $active_camp_api_key
			);

			$zapier_hook = isset($data['ays_zapier_hook']) ? $data['ays_zapier_hook'] : '';
			$zapier      = array(
				'hook' => $zapier_hook
			);

            
            $paypal_options = array(
                "paypal_client_id" => $paypal_client_id,
                "payment_terms"    => $paypal_payment_terms,
            );
            
            $ays_leadboard_count      = isset($data['ays_leadboard_count']) ? $data['ays_leadboard_count'] : '5';
            $ays_leadboard_width      = isset($data['ays_leadboard_width']) ? $data['ays_leadboard_width'] : '0';
            $ays_gleadboard_count     = isset($data['ays_gleadboard_count']) ? $data['ays_gleadboard_count'] : '5';
            $ays_gleadboard_width     = isset($data['ays_gleadboard_width']) ? $data['ays_gleadboard_width'] : '0';

            $leaderboard = array(                
                'individual' => array(
                    'count' => $ays_leadboard_count,
                    'width' => $ays_leadboard_width
                ),
                'global' => array(
                    'count' => $ays_gleadboard_count,
                    'width' => $ays_gleadboard_width
                )
            );

            $start_button           = (isset($data['ays_start_button']) && $data['ays_start_button'] != '') ? $data['ays_start_button'] : 'Start' ;
            $next_button            = (isset($data['ays_next_button']) && $data['ays_next_button'] != '') ? $data['ays_next_button'] : 'Next' ;
            $previous_button        = (isset($data['ays_previous_button']) && $data['ays_previous_button'] != '') ? $data['ays_previous_button'] : 'Prev' ;
            $clear_button           = (isset($data['ays_clear_button']) && $data['ays_clear_button'] != '') ? $data['ays_clear_button'] : 'Clear' ;
            $finish_button          = (isset($data['ays_finish_button']) && $data['ays_finish_button'] != '') ? $data['ays_finish_button'] : 'Finish' ;
            $see_result_button      = (isset($data['ays_see_result_button']) && $data['ays_see_result_button'] != '') ? $data['ays_see_result_button'] : 'See Result' ;
            $restart_quiz_button    = (isset($data['ays_restart_quiz_button']) && $data['ays_restart_quiz_button'] != '') ? $data['ays_restart_quiz_button'] : 'Restart quiz' ;
            $send_feedback_button   = (isset($data['ays_send_feedback_button']) && $data['ays_send_feedback_button'] != '') ? $data['ays_send_feedback_button'] : 'Send feedback' ;
            $load_more_button       = (isset($data['ays_load_more_button']) && $data['ays_load_more_button'] != '') ? $data['ays_load_more_button'] : 'Load more' ;
            $exit_button            = (isset($data['ays_exit_button']) && $data['ays_exit_button'] != '') ? $data['ays_exit_button'] : 'Exit' ;
            $check_button           = (isset($data['ays_check_button']) && $data['ays_check_button'] != '') ? $data['ays_check_button'] : 'Check' ;
            $login_button           = (isset($data['ays_login_button']) && $data['ays_login_button'] != '') ? $data['ays_login_button'] : 'Log In' ;

            $buttons_texts = array(
                'start_button'          => $start_button,
                'next_button'           => $next_button,
                'previous_button'       => $previous_button,
                'clear_button'          => $clear_button,
                'finish_button'         => $finish_button,
                'see_result_button'     => $see_result_button,
                'restart_quiz_button'   => $restart_quiz_button,
                'send_feedback_button'  => $send_feedback_button,
                'load_more_button'      => $load_more_button,
                'exit_button'           => $exit_button,
                'check_button'          => $check_button,
                'login_button'          => $login_button,
            );



                        
            $question_default_type = isset($data['ays_question_default_type']) ? $data['ays_question_default_type'] : '';                        
            $ays_answer_default_count = isset($data['ays_answer_default_count']) ? $data['ays_answer_default_count'] : '';
            $right_answer_sound = isset($data['ays_right_answer_sound']) ? $data['ays_right_answer_sound'] : '';
            $wrong_answer_sound = isset($data['ays_wrong_answer_sound']) ? $data['ays_wrong_answer_sound'] : '';

            // Questions title length
            $question_title_length = (isset($data['ays_question_title_length']) && intval($data['ays_question_title_length']) != 0) ? absint(intval($data['ays_question_title_length'])) : 5;
            if($question_title_length == 0){
                $question_title_length = 5;
            }

            // Do not store IP adressess
            $disable_user_ip = (isset( $data['ays_disable_user_ip'] ) && $data['ays_disable_user_ip'] == 'on') ? 'on' : 'off';
            
            $options = array(
                "question_default_type"    => $question_default_type,
                "ays_answer_default_count" => $ays_answer_default_count,
                "right_answer_sound"       => $right_answer_sound,
                "wrong_answer_sound"       => $wrong_answer_sound,
                "question_title_length"    => $question_title_length,
                "disable_user_ip"          => $disable_user_ip,
            );
            
            $del_stat = "";
            $month_count = isset($data['ays_delete_results_by']) ? intval($data['ays_delete_results_by']) : null;
            if($month_count !== null && $month_count > 0){
                $year = intval( date( 'Y', current_time('timestamp') ) );
                $dt = intval( date( 'n', current_time('timestamp') ) );
                $month = $dt - $month_count;
                if($month < 0){
                    $month = 12 - $month;
                    if($month > 12){
                        $mn = $month % 12;
                        $mnac = ($month - $mn) / 12;
                        $month = 12 - ($mn);
                        $year -= $mnac;
                    }
                }elseif($month == 0){        
                    $month = 12;
                    $year--;
                }
                $sql = "DELETE FROM " . $wpdb->prefix . "aysquiz_reports 
                        WHERE YEAR(end_date) = '".$year."' 
                          AND MONTH(end_date) <= '".$month."'";
                $res = $wpdb->query($sql);
                if($res >= 0){
                    $del_stat = "&del_stat=ok&mcount=".$data['ays_delete_results_by'];
                }
            }
            
            $result = update_option(
                'ays_quiz_integrations',
                json_encode($paypal_options)
            );

            // if($result){
            //     $success++;
            // }
            // $result = $this->ays_update_setting('user_roles', json_encode($roles));
            // if($result){
            //     $success++;
            // }
            // $result = $this->ays_update_setting('mailchimp', json_encode($mailchimp));
            // if($result){
            //     $success++;
            // }
            // $result = $this->ays_update_setting('monitor', json_encode($monitor));
            // if ($result) {
            // 	$success++;
            // }
            // $result = $this->ays_update_setting('slack', json_encode($slack));
            // if ($result) {
            // 	$success++;
            // }
            // $result = $this->ays_update_setting('active_camp', json_encode($active_camp));
            // if ($result) {
            // 	$success++;
            // }
            // $result = $this->ays_update_setting('zapier', json_encode($zapier));
            // if ($result) {
            // 	$success++;
            // }
            // $result = $this->ays_update_setting('leaderboard', json_encode($leaderboard));
            // if ($result) {
            //     $success++;
            // }
            $result = $this->ays_update_setting('buttons_texts', json_encode($buttons_texts));
            if ($result) {
                $success++;
            }
            $result = $this->ays_update_setting('options', json_encode($options));
            if ($result) {
                $success++;
            }

            $message = "saved";
            if($success > 0){
                $tab = "";
                if(isset($data['ays_quiz_tab'])){
                    $tab = "&ays_quiz_tab=".$data['ays_quiz_tab'];
                }
                $url = admin_url('admin.php') . "?page=quiz-maker-settings" . $tab . '&status=' . $message . $del_stat;
//                var_dump($url);
                wp_redirect( $url );
            }
        }
        
    }

    public function get_data(){
        $data = get_option( "ays_quiz_integrations" );
        if($data == null || $data == ''){
            return array();
        }else{
            return json_decode( get_option( "ays_quiz_integrations" ), true );
        }
    }

    public function get_db_data(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT * FROM ".$settings_table;
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(count($results) > 0){
            return $results;
        }else{
            return array();
        }
    }    
    
    public function check_settings_meta($metas){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        foreach($metas as $meta_key){
            $sql = "SELECT COUNT(*) FROM ".$settings_table." WHERE meta_key = '".$meta_key."'";
            $result = $wpdb->get_var($sql);
            if(intval($result) == 0){
                $this->ays_add_setting($meta_key, "", "", "");
            }
        }
        return false;
    }
    
    public function check_setting_user_roles(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT COUNT(*) FROM ".$settings_table." WHERE meta_key = 'user_roles'";
        $result = $wpdb->get_var($sql);
        if(intval($result) == 0){
            $roles = json_encode(array('administrator'));
            $this->ays_add_setting("user_roles", $roles, "", "");
        }
        return false;
    }
        
    public function get_reports_titles(){
        global $wpdb;

        $sql = "SELECT {$wpdb->prefix}aysquiz_quizes.id,{$wpdb->prefix}aysquiz_quizes.title FROM {$wpdb->prefix}aysquiz_quizes";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }
    
    public function ays_get_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = '".$meta_key."'";
        $result = $wpdb->get_var($sql);
        if($result != ""){
            return $result;
        }
        return false;
    }
    
    public function ays_add_setting($meta_key, $meta_value, $note = "", $options = ""){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $result = $wpdb->insert(
            $settings_table,
            array(
                'meta_key'    => $meta_key,
                'meta_value'  => $meta_value,
                'note'        => $note,
                'options'     => $options
            ),
            array( '%s', '%s', '%s', '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }
    
    public function ays_update_setting($meta_key, $meta_value, $note = null, $options = null){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $value = array(
            'meta_value'  => $meta_value,
        );
        $value_s = array( '%s' );
        if($note != null){
            $value['note'] = $note;
            $value_s[] = '%s';
        }
        if($options != null){
            $value['options'] = $options;
            $value_s[] = '%s';
        }
        $result = $wpdb->update(
            $settings_table,
            $value,
            array( 'meta_key' => $meta_key, ),
            $value_s,
            array( '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }
    
    public function ays_delete_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $wpdb->delete(
            $settings_table,
            array( 'meta_key' => $meta_key ),
            array( '%s' )
        );
    }

    public function quiz_settings_notices($status){

        if ( empty( $status ) )
            return;

        if ( 'saved' == $status )
            $updated_message = esc_html( __( 'Changes saved.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz attribute .', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz attribute deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
    
}