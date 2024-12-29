<?php

namespace DLPL\Prelaunch;

class Editor
{

    private $version;
    private $scraper;
    private $url;

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('wp_ajax_dlpl_get_links', [$this, 'get_links']);
        add_action('wp_ajax_dlpl_empty', [$this, 'empty']);

        $this->scraper = new Scraper();
        $this->url = new Entity();

        if (! function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugin_data = get_plugin_data(DL_PRELAUNCH_FULL_PATH_FILE);
        $this->version = $plugin_data['Version'];
    }

    public function enqueue_styles($hook)
    {
        $screen = get_current_screen();

        if ($screen && isset($_GET['page']) && Tools::strStartsWith($_GET['page'], 'dl-prelaunch')) {
            wp_enqueue_style(
                'dl-prelaunch-styles',
                plugin_dir_url(__FILE__) . '../assets/css/styles.css',
                [],
                $this->version
            );

            wp_enqueue_script(
                'dl-prelaunch-js',
                plugin_dir_url(__FILE__) . '../assets/js/scripts.js',
                ['jquery'],
                $this->version,
                true
            );


            wp_localize_script('dl-prelaunch-js', 'dl_mass_editor', [
                'translations' => [
                    'saving' => __('Saving...', 'dl-prelaunch'),
                    'saved' => __('Saved', 'dl-prelaunch'),
                    'error' => __('Error saving', 'dl-prelaunch'),
                    'allSaved' => __('All changes have been saved.', 'dl-prelaunch'),
                    'nothingToSave' => __('Nothing to save.', 'dl-prelaunch'),
                    'of' => __('of', 'dl-prelaunch'),
                    'ajaxurl' => admin_url('admin-ajax.php'),
                ],
            ]);
        }
    }

    public function render_editor_page()
    {
?>
        <div class="wrap">

            <h1><?php _e('Prelaunch:', 'dl-prelaunch'); ?></h1>

            <table class="wp-list-table widefat dl-url-list">
                <?php echo $this->table(); ?>
            </table>
        </div>

        <div class="dl-prelaunch-actions">
            <div>
                <button class="button button-primary" id="dl-prelaunch-execute"><?php _e('Start', 'dl-prelaunch'); ?></button>
                <button class="button button-secondary" id="dl-prelaunch-reset"><?php _e('Reset', 'dl-prelaunch'); ?></button>
            </div>
            <span class="dl-prelaunch-status"><?php _e('Ready', 'dl-prelaunch'); ?></span>
        </div>

    <?php
    }

    private function table()
    {


        $links = $this->url->get();

        ob_start();
    ?>
        <thead>
            <tr>
                <th><?php _e('URL', 'dl-prelaunch'); ?></th>
                <?php do_action('dlpl_table_head'); ?>
                <th><?php _e('Read', 'dl-prelaunch'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $link) : ?>
                <tr>
                    <td class="link <?php echo ($link->read ? 'read' : ''); ?>">
                        <a href="<?php echo $link->url; ?>" target="_blank"><?php echo $link->url; ?></a>
                    </td>
                    <?php do_action('dlpl_table_row', $link); ?>
                    <td style="font-size: 11px;"><?php echo $link->read ? __('Yes', 'dl-prelaunch') : __('No', 'dl-prelaunch'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

<?php

        return ob_get_clean();
    }

    public function get_links()
    {
        $url = $_POST['url'];

        try {
            $links = $this->scraper->get($url);
            $this->url->read($url);

            if (!empty($links)) {
                foreach ($links as $link) {
                    $this->url->insert($link);
                }
            }

            wp_send_json_success([
                'message' => __('Form successfully saved.', 'dl-prelaunch'),
                'links' => $links,
                'table' => $this->table()
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => __($e->getMessage(), 'dl-prelaunch')]);
        }
    }

    public function empty()
    {
        $this->url->empty();
        $this->url->insert(home_url(), true);

        wp_send_json_success([
            'message' => __('Form successfully saved.', 'dl-prelaunch'),
            'table' => $this->table()
        ]);
    }
}
