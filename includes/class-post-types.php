<?php
/**
 * Register custom post types
 */
class AIPL_Post_Types {
    
    /**
     * Register post types
     */
    public function register() {
        $this->register_prompt_post_type();
    }
    
    /**
     * Register AI Prompt post type
     */
    private function register_prompt_post_type() {
        $labels = array(
            'name'                  => _x('AI Prompts', 'Post type general name', 'ai-prompt-library'),
            'singular_name'         => _x('Prompt', 'Post type singular name', 'ai-prompt-library'),
            'menu_name'             => _x('AI Prompts', 'Admin Menu text', 'ai-prompt-library'),
            'name_admin_bar'        => _x('Prompt', 'Add New on Toolbar', 'ai-prompt-library'),
            'add_new'               => __('Add New', 'ai-prompt-library'),
            'add_new_item'          => __('Add New Prompt', 'ai-prompt-library'),
            'new_item'              => __('New Prompt', 'ai-prompt-library'),
            'edit_item'             => __('Edit Prompt', 'ai-prompt-library'),
            'view_item'             => __('View Prompt', 'ai-prompt-library'),
            'all_items'             => __('All Prompts', 'ai-prompt-library'),
            'search_items'          => __('Search Prompts', 'ai-prompt-library'),
            'parent_item_colon'     => __('Parent Prompts:', 'ai-prompt-library'),
            'not_found'             => __('No prompts found.', 'ai-prompt-library'),
            'not_found_in_trash'    => __('No prompts found in Trash.', 'ai-prompt-library'),
            'featured_image'        => _x('Prompt Icon', 'Overrides the "Featured Image" phrase', 'ai-prompt-library'),
            'set_featured_image'    => _x('Set prompt icon', 'Overrides the "Set featured image" phrase', 'ai-prompt-library'),
            'remove_featured_image' => _x('Remove prompt icon', 'Overrides the "Remove featured image" phrase', 'ai-prompt-library'),
            'use_featured_image'    => _x('Use as prompt icon', 'Overrides the "Use as featured image" phrase', 'ai-prompt-library'),
            'archives'              => _x('Prompt archives', 'The post type archive label', 'ai-prompt-library'),
            'insert_into_item'      => _x('Insert into prompt', 'Overrides the "Insert into post" phrase', 'ai-prompt-library'),
            'uploaded_to_this_item' => _x('Uploaded to this prompt', 'Overrides the "Uploaded to this post" phrase', 'ai-prompt-library'),
            'filter_items_list'     => _x('Filter prompts list', 'Screen reader text', 'ai-prompt-library'),
            'items_list_navigation' => _x('Prompts list navigation', 'Screen reader text', 'ai-prompt-library'),
            'items_list'            => _x('Prompts list', 'Screen reader text', 'ai-prompt-library'),
        );
        
        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => array('slug' => 'prompts'),
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => false,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-format-chat',
            'supports'              => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'custom-fields',
                'author',
                'revisions'
            ),
            'show_in_rest'          => true,
            'rest_base'             => 'prompts',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('ai_prompt', $args);
        
        // Add custom columns to admin list
        add_filter('manage_ai_prompt_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_ai_prompt_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-ai_prompt_sortable_columns', array($this, 'sortable_columns'));
    }
    
    /**
     * Set custom columns
     */
    public function set_custom_columns($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $value) {
            if ($key == 'title') {
                $new_columns[$key] = $value;
                $new_columns['workspace'] = __('Workspace', 'ai-prompt-library');
                $new_columns['model'] = __('AI Model', 'ai-prompt-library');
                $new_columns['usage'] = __('Usage', 'ai-prompt-library');
            } else {
                $new_columns[$key] = $value;
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Custom column content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'workspace':
                $terms = get_the_terms($post_id, 'prompt_workspace');
                if ($terms && !is_wp_error($terms)) {
                    $workspace_names = array();
                    foreach ($terms as $term) {
                        $workspace_names[] = $term->name;
                    }
                    echo implode(', ', $workspace_names);
                } else {
                    echo '—';
                }
                break;
                
            case 'model':
                $terms = get_the_terms($post_id, 'ai_model');
                if ($terms && !is_wp_error($terms)) {
                    echo $terms[0]->name;
                } else {
                    echo '—';
                }
                break;
                
            case 'usage':
                global $wpdb;
                $table = $wpdb->prefix . 'aipl_prompt_stats';
                $count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $table WHERE prompt_id = %d AND action = 'copy'",
                    $post_id
                ));
                echo intval($count);
                break;
        }
    }
    
    /**
     * Sortable columns
     */
    public function sortable_columns($columns) {
        $columns['usage'] = 'usage';
        return $columns;
    }
}
