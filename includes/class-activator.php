<?php
/**
 * Activation and deactivation handler
 */
class AIPL_Activator {
    
    /**
     * Activate plugin
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Set default options
        self::set_default_options();
        
        // Create default terms
        self::create_default_terms();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Deactivate plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Prompt statistics table
        $table_stats = $wpdb->prefix . 'aipl_prompt_stats';
        
        $sql_stats = "CREATE TABLE $table_stats (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            prompt_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            action varchar(50) NOT NULL,
            ip_address varchar(100),
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY prompt_id (prompt_id),
            KEY user_id (user_id),
            KEY action (action)
        ) $charset_collate;";
        
        // Favorites table
        $table_favorites = $wpdb->prefix . 'aipl_favorites';
        
        $sql_favorites = "CREATE TABLE $table_favorites (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            prompt_id bigint(20) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_prompt (user_id, prompt_id),
            KEY prompt_id (prompt_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_stats);
        dbDelta($sql_favorites);
        
        // Add version to options
        add_option('aipl_db_version', '1.0.0');
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $defaults = array(
            'aipl_default_view' => 'grid',
            'aipl_prompts_per_page' => 12,
            'aipl_enable_copy_tracking' => 1,
            'aipl_enable_favorites' => 1,
            'aipl_color_scheme' => 'default',
            'aipl_enable_search' => 1,
            'aipl_enable_filters' => 1,
            'aipl_show_stats' => 1
        );
        
        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }
    
    /**
     * Create default terms
     */
    private static function create_default_terms() {
        // Default categories
        $categories = array(
            'Content Creation',
            'Data Analysis',
            'Code Generation',
            'Research',
            'Creative Writing',
            'Email Marketing',
            'SEO',
            'Education'
        );
        
        foreach ($categories as $category) {
            if (!term_exists($category, 'prompt_category')) {
                wp_insert_term($category, 'prompt_category');
            }
        }
        
        // Default AI models
        $models = array(
            'GPT-4' => 'OpenAI GPT-4',
            'GPT-3.5' => 'OpenAI GPT-3.5',
            'Claude' => 'Anthropic Claude',
            'Claude Instant' => 'Anthropic Claude Instant',
            'Gemini' => 'Google Gemini',
            'Llama' => 'Meta Llama'
        );
        
        foreach ($models as $slug => $name) {
            if (!term_exists($slug, 'ai_model')) {
                wp_insert_term($name, 'ai_model', array('slug' => sanitize_title($slug)));
            }
        }
        
        // Default difficulty levels
        $difficulties = array(
            'Beginner',
            'Intermediate',
            'Advanced',
            'Expert'
        );
        
        foreach ($difficulties as $difficulty) {
            if (!term_exists($difficulty, 'difficulty_level')) {
                wp_insert_term($difficulty, 'difficulty_level');
            }
        }
        
        // Default workspace
        if (!term_exists('Default', 'prompt_workspace')) {
            wp_insert_term('Default', 'prompt_workspace', array(
                'description' => 'Default workspace for all prompts'
            ));
        }
    }
}
