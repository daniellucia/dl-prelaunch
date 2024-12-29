<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class Noindex
{

    private $entity;
    private $key = 'noindex';

    public function __construct()
    {

        $this->entity = new Entity();

        add_action('dlpl_table_head', [$this, 'head']);
        add_action('dlpl_table_row', [$this, 'row']);
        add_action('dlpl_before_scrape', [$this, 'scrape'], 10, 2);
    }

    public function head()
    {
        echo '<th>' . __('noindex', 'dl-prelaunch') . '</th>';
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);

        echo '<td>';
        if (isset($meta[$this->key])) {
            if (isset($meta[$this->key]) && (int)$meta[$this->key] == 0) {
                echo '<strong style="color:green;">' . __('Indexable', 'dl-prelaunch') . '</strong>';
            } else {
                echo '<strong style="color:red;">' . __('Not indexable', 'dl-prelaunch') . '</strong>';
            }
        }
        echo  '</td>';
    }

    public function scrape($dom, $url)
    {
        $noindex = 0;
        $metas = $dom->getElementsByTagName('meta');
        foreach ($metas as $meta) {
            if ($meta->hasAttribute('name') && strtolower($meta->getAttribute('name')) === 'robots') {
                $content = strtolower($meta->getAttribute('content'));
                if (strpos($content, 'noindex') !== false) {
                    $noindex = 1;
                }
            }
        }

        $this->entity->saveMeta($url, [$this->key =>  $noindex]);
    }
}
