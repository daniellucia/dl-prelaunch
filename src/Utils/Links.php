<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class Links
{

    private $entity;
    private $key = 'links';

    public function __construct()
    {

        $this->entity = new Entity();

        add_action('dlpl_table_head', [$this, 'head']);
        add_action('dlpl_table_row', [$this, 'row']);
        add_action('dlpl_before_scrape', [$this, 'scrape'], 10, 2);
    }

    public function head()
    {
        echo '<th>' . __('Links', 'dl-prelaunch') . '</th>';
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);
        echo '<td>';
        if (isset($meta[$this->key])) {
            echo $meta[$this->key];
        } else {
            echo __('--', 'dl-prelaunch');
        }
        echo  '</td>';
    }

    public function scrape($dom, $url)
    {
        $count = $dom->getElementsByTagName('a');
        $this->entity->saveMeta($url, [$this->key =>  count($count)]);
    }
}
