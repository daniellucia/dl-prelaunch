<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class Favicon
{

    private $entity;
    private $key = 'favicon';

    public function __construct()
    {

        $this->entity = new Entity();

        add_action('dlpl_table_head', [$this, 'head']);
        add_action('dlpl_table_row', [$this, 'row']);
        add_action('dlpl_before_scrape', [$this, 'scrape'], 10, 2);
    }

    public function head()
    {
        echo '<th>' . __('favicon', 'dl-prelaunch') . '</th>';
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);

        echo '<td>';
        if (isset($meta['root']) && $meta['root'] == 1) {
            if (isset($meta[$this->key]) && (int)$meta[$this->key] == 0) {
                echo '<strong style="color:red;">' . __('Not exists', 'dl-prelaunch') . '</strong>';
            } else {
                echo '<strong style="color:green;">' . __('Exists', 'dl-prelaunch') . '</strong>';
            }
        } else {
            echo __('--', 'dl-prelaunch');
        }
        echo  '</td>';
    }

    public function scrape($dom, $url)
    {

        $meta = $this->entity->meta($url);
        if (isset($meta['root']) && $meta['root'] == 0) {
            return;
        }

        $favicon = 0;
        $links = $dom->getElementsByTagName('link');
        foreach ($links as $link) {
            if ($link->hasAttribute('rel')) {
                $rel = strtolower($link->getAttribute('rel'));
                if (strpos($rel, 'icon') !== false) {
                    $href = $link->getAttribute('href');
                    $faviconUrl = strpos($href, 'http') === 0 ? $href : rtrim($url, '/') . '/' . ltrim($href, '/');
                    if (@file_get_contents($faviconUrl) !== false) {
                        $favicon = 1;
                    }
                }
            }
        }

        $this->entity->saveMeta($url, [$this->key =>  $favicon]);
    }
}
