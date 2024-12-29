<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class HashLinks
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
        echo '<th>' . __('Links #', 'dl-prelaunch') . '</th>';
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);
        echo '<td>';
        if (isset($meta[$this->key])) {
            if ((int)$meta[$this->key] == 0) {
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
        $count = [];
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $c) {
            if ($c->getAttribute('href') == '#') {
                $count[] = $c->getAttribute('href');
            }
        }

        $this->entity->saveMeta($url, [$this->key =>  count($count)]);
    }
}
