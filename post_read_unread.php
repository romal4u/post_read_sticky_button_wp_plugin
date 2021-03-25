<?php
/**
 * Plugin Name: Post read unread floating sticky button
 * Plugin URI: https://blog.jeviwebstudio.com/smart-floating-sticky-buttons-for-post-and-page
 * Description: Used for track post is read or unread by user with sticky button at the bottom of each post.
 * Version: 1.0
 * Author: Romal Patel
 * Author URI: https://in.linkedin.com/in/romal-patel-8b116720
 */


if (!function_exists('veepru_read_unread_post_pages_activation')) {
    function veepru_read_unread_post_pages_activation()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        //* Create the teams table
        $table_name = $wpdb->prefix . 'pre_read_unread';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INTEGER NOT NULL AUTO_INCREMENT,
            user_id integer NULL,
            post_id integer NULL,
            read_time timestamp default current_timestamp,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql);
    }

    register_activation_hook(__FILE__, 'veepru_read_unread_post_pages_activation');
}

if (!function_exists('veepru_read_unread_post_pages_uninstall')) {
    function veepru_read_unread_post_pages_uninstall()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        //* Create the teams table
        $table_name = $wpdb->prefix . 'pre_read_unread';

        $wpdb->query('DROP TABLE IF EXISTS '.$table_name);
    }
    register_uninstall_hook(__FILE__, 'veepru_read_unread_post_pages_uninstall');
}

if (!function_exists('veepru_readUnreadrep_callack')) {
    function veepru_readUnreadrep_callack()
    {
        require_once __DIR__.'/vee_Read_unread_report.php';
        $wp_list_table = new veepru_read_unread_report();
        $wp_list_table->prepare_items(); ?>
        <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Post Read Report')?></h1>
        <hr class="wp-header-end">
        <form id="read_unread" method="get">
        <input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']); ?>">
        <?php
        $wp_list_table->display(); ?>
        </div>
        <?php
    }
}

if (!function_exists('veepru_read_unread_menu')) {
    function veepru_read_unread_menu()
    {
        add_menu_page(
            __('Post Read Report'),
            __('Post Read Report'),
            'manage_options',
            'vee_read_unread',
            'veepru_readUnreadrep_callack',
            'dashicons-media-spreadsheet',
            20
        );
    }
    add_action('admin_menu', 'veepru_read_unread_menu');
}



if (!function_exists('veepru_add_assets')) {
    add_action('wp_enqueue_scripts', 'veepru_add_assets');
    function veepru_add_assets()
    {
        if (is_user_logged_in()) {
            wp_enqueue_style('vee_pre_css', plugin_dir_url(__FILE__).'assets/css/veemain.css');
            wp_enqueue_script('vee_pre_js', plugin_dir_url(__FILE__).'assets/js/main.js', ['jquery']);
            wp_localize_script(
                'vee_pre_js',
                'vee_plugin_ajax_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'vee_post_id' => get_the_ID(),
                    'vee_read_unread_nonce' => wp_create_nonce("vee_hack_iam"),
                    'vee_already_read' => veepru_read_unread_post()
                )
            );
        }
    }
}


if (!function_exists('veepru_read_unread_post')) {
    function veepru_read_unread_post()
    {
        global $wpdb;
    
        $readCnt = $wpdb->get_var("select count(*) from ".$wpdb->prefix . "pre_read_unread where user_id = ".get_current_user_id()." and post_id = ".get_the_ID());
    
        return $readCnt;
    }
}


if (!function_exists('veepru_post_btn')) {
    add_action('the_content', 'veepru_post_btn');

    function veepru_post_btn($content)
    {
        $readCnt = veepru_read_unread_post();
    
        $btn = '';
        if ($readCnt > 0) {
            $btn = 'Already read!';
        }
    
        $content .= '<div id="read_unread">'.$btn.'</div>';
        return $content;
    }
}


if (!function_exists('veepru_save_read')) {
    add_action("wp_ajax_vee_save_read", "veepru_save_read");

    function veepru_save_read()
    {
        if (! wp_verify_nonce($_POST['nonce'], 'vee_hack_iam')) {
            die('Busted!');
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'pre_read_unread';

        $user_id = get_current_user_id();
        $post_id = intval($_POST['vee_post_id']);

        if ($post_id > 0) {
            $wpdb->insert($table_name, [
            'user_id' => $user_id,
            'post_id' => $post_id,
        ]);
        
            if ($wpdb->insert_id > 0) {
                echo __('success');
            } else {
                echo __('error');
            }
        } else {
            echo __('error');
        }

        exit();
    }
}
