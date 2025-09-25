<?php
/**
 * Plugin Name: AI Prompt Library
 * Plugin URI: https://github.com/yourusername/prompt-archive
 * Description: Comprehensive AI prompt management system with workspaces and categories
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * Text Domain: ai-prompt-library
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AIPL_VERSION', '1.0.0');
define('AIPL_PLUGIN_FILE', __FILE__);
define('AIPL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AIPL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AIPL_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
final class AI_Prompt_Library {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once AIPL_PLUGIN_DIR . 'includes/class-activator.php';
        require_once AIPL_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once AIPL_PLUGIN_DIR . 'includes/class-taxonomies.php';
        require_once AIPL_PLUGIN_DIR . 'includes/class-ajax-handler.php';
        require_once AIPL_PLUGIN_DIR . 'includes/class-shortcodes.php';

        // Admin classes
        if (is_admin()) {
            require_once AIPL_PLUGIN_DIR . 'admin/class-admin-menu.php';
            require_once AIPL_PLUGIN_DIR . 'admin/class-settings.php';
            require_once AIPL_PLUGIN_DIR . 'includes/class-import-export.php';
        }
    }

    /**
     * Set locale
     */
    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
    }

    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'ai-prompt-library',
            false,
            dirname(AIPL_PLUGIN_BASENAME) . '/languages/'
        );
    }

    /**
     * Define admin hooks
     */
    private function define_admin_hooks() {
        if (is_admin()) {
            $admin = new AIPL_Admin_Menu();
            $settings = new AIPL_Settings();

            add_action('admin_menu', array($admin, 'add_menu_pages'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }
    }

    /**
     * Define public hooks
     */
    private function define_public_hooks() {
        $post_types = new AIPL_Post_Types();
        $taxonomies = new AIPL_Taxonomies();
        $shortcodes = new AIPL_Shortcodes();
        $ajax = new AIPL_Ajax_Handler();

        add_action('init', array($post_types, 'register'));
        add_action('init', array($taxonomies, 'register'));
        add_action('init', array($shortcodes, 'register'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'ai-prompt') !== false || get_post_type() === 'ai_prompt') {
            wp_enqueue_style(
                'aipl-admin',
                AIPL_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                AIPL_VERSION
            );

            wp_enqueue_script(
                'aipl-admin',
                AIPL_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                AIPL_VERSION,
                true
            );

            wp_localize_script('aipl-admin', 'aipl_admin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aipl_admin_nonce')
            ));
        }
    }

    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        if (is_post_type_archive('ai_prompt') || is_singular('ai_prompt') || $this->has_shortcode()) {
            wp_enqueue_style(
                'aipl-frontend',
                AIPL_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                AIPL_VERSION
            );

            wp_enqueue_script(
                'aipl-frontend',
                AIPL_PLUGIN_URL . 'assets/js/frontend.js',
                array('jquery'),
                AIPL_VERSION,
                true
            );

            wp_localize_script('aipl-frontend', 'aipl_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('aipl_nonce')
            ));
        }
    }

    /**
     * Check if page has shortcode
     */
    private function has_shortcode() {
        global $post;
        if (is_a($post, 'WP_Post')) {
            if (has_shortcode($post->post_content, 'ai_prompt_library') ||
                has_shortcode($post->post_content, 'ai_prompt_grid')) {
                return true;
            }
        }
        return false;
    }
}

/**
 * Plugin activation
 */
register_activation_hook(__FILE__, array('AIPL_Activator', 'activate'));

/**
 * Plugin deactivation
 */
register_deactivation_hook(__FILE__, array('AIPL_Activator', 'deactivate'));

/**
 * Initialize plugin
 */
function aipl_init() {
    return AI_Prompt_Library::instance();
}

// Start the plugin
aipl_init();
