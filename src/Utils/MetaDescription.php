<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class MetaDescription
{

    private $entity;
    private $key = 'meta-description';

    public function __construct()
    {

        $this->entity = new Entity();

        add_action('dlpl_table_head', [$this, 'head']);
        add_action('dlpl_table_row', [$this, 'row']);
        add_action('dlpl_before_scrape', [$this, 'scrape'], 10, 2);
    }

    public function head()
    {
        echo '<th>' . __('Meta description', 'dl-prelaunch') . '</th>';
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);
        echo '<td>';
        if (isset($meta[$this->key])) {
            if ((int)$meta[$this->key] > 0) {
                echo '<strong style="color:green;">' . __('OK', 'dl-prelaunch') . '</strong>';
            } else {
                echo '<strong style="color:red;">' . $meta[$this->key] . '</strong>';
            }
        } else {
            echo __('--', 'dl-prelaunch');
        }
        echo  '</td>';
    }

    public function scrape($dom, $url)
    {
        $metaTags = $dom->getElementsByTagName('meta');
        $count = [];
        foreach ($metaTags as $meta) {
            if (strtolower($meta->getAttribute('name')) === 'description') {
                $count[] = $meta->getAttribute('content');
                break;
            }
        }

        $this->entity->saveMeta($url, [$this->key =>  count($count)]);
    }
}
