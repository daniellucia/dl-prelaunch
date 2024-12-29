<?php

namespace DLPL\Prelaunch;

class Entity
{

    private $table_name = 'prelaunch';
    private $wpdb;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . $this->table_name;
    }

    public function activate($network_wide)
    {

        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") === $this->table_name) {
            return;
        }

        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE `{$this->table_name}` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `url` text NOT NULL,
                `meta` text NOT NULL,
                `read` tinyint(4) NOT NULL,
                `root` tinyint(4) NOT NULL,
                `created_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
                ) $charset_collate;
                ";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);

        $this->insert(home_url(), true);
    }

    public function deactivate()
    {
        if ($this->wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") === $this->table_name) {
            $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name};");
        }
    }

    public function get()
    {
        return $this->wpdb->get_results("SELECT * FROM {$this->table_name}");
    }

    public function insert($url, $root = false)
    {
        $url = esc_url(trim($url));
        $exists = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name} WHERE url = '{$url}'");

        if ($exists) {
            return;
        }

        $this->wpdb->insert($this->table_name, ['url' => $url]);

        if ($root) {
            $this->saveMeta($url, ['root' => 1]);
        }
    }

    public function empty()
    {
        $this->wpdb->query("TRUNCATE TABLE {$this->table_name}");
    }

    public function read($url)
    {
        $this->wpdb->update($this->table_name, ['read' => 1], ['url' => $url]);
    }

    public function saveMeta($url, $meta)
    {
        $meta = array_merge($this->meta($url), $meta);
        $this->wpdb->update($this->table_name, ['meta' => json_encode($meta)], ['url' => $url]);
    }

    public function meta($url): array
    {
        $row = $this->wpdb->get_row("SELECT * FROM {$this->table_name} WHERE url = '{$url}'");

        if (!$row) {
            return [];
        }

        $meta = json_decode($row->meta, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return $meta;
    }
}
