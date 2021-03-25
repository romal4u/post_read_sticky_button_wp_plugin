<?php

if (! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

if (!class_exists('veepru_read_unread_report')) {
    class veepru_read_unread_report extends WP_List_Table
    {
        public function __construct()
        {
            parent::__construct([
            'singular' => 'vee_read_unread_report_link',
            'plural' => 'vee_read_unread_report_links',
            'ajax' => false
        ]);
        }

        public function get_columns()
        {
            return $columns= array(
            'id'=>__('ID'),
            'post_name'=>__('Page / Post Title'),
            'user_name'=>__('User Name'),
            'read_time'=>__('Read At')
        );
        }

        public function get_sortable_columns()
        {
            return $sortable = array(
            'post_name'=>['post_name',true],
            'user_name'=>['user_name',true],
            'read_time'=>['read_time',true]
        );
        }
        public function get_hidden_columns()
        {
            return [
            'id'
        ];
        }

        public function prepare_items()
        {
            // $this->_column_headers = $this->get_column_info();
            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array(
                $columns,
                $hidden,
                $sortable
            );
            /** Process bulk action */
            // $this->process_bulk_action();
            $per_page = $this->get_items_per_page('records_per_page', 10);
            $current_page = $this->get_pagenum();
            $total_items = self::record_count();
            $data = self::get_records($per_page, $current_page);
        
            //  print_r($data);
            $this->set_pagination_args(
                [
                'total_items' => $total_items, //WE have to calculate the total number of items
                'per_page' => $per_page // WE have to determine how many items to show on a page
            ]
            );
            $this->items = $data;
        }

        public static function get_records($per_page = 10, $page_number = 1)
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'pre_read_unread';
            $posttbl = $wpdb->prefix.'posts';
            $usrtbl = $wpdb->prefix.'users';

            $selcol = 'ru.id, p.post_name, if(u.display_name != "",u.display_name,u.user_nicename) user_name, ru.read_time';


            $sql = "select $selcol from $table_name ru join $posttbl p on (ru.post_id=p.ID)
            join $usrtbl u on (u.ID = ru.user_id)";
        
            // if (isset($_REQUEST['s'])) {
            //     $sql.= ' where user_name LIKE "%' . $_REQUEST['s'] . '%" or post_name LIKE "%' . $_REQUEST['s'] . '%"';
            // }

            if (!empty($_REQUEST['orderby'])) {
                $sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
                $sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
            }


            $sql.= " LIMIT $per_page";
            $sql.= ' OFFSET ' . ($page_number - 1) * $per_page;

            $result = $wpdb->get_results($sql);
            return $result;
        }

        public function no_items()
        {
            _e('No record found in the database.', 'bx');
        }

        public static function record_count()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'pre_read_unread';

            $sql = "SELECT COUNT(*) FROM $table_name";
            return $wpdb->get_var($sql);
        }
    
        public function column_default($item, $column_name)
        {
            global $wpdb;
            switch ($column_name) {
            default:
                return $item->$column_name;
            break;
        }
        }
    }
}
