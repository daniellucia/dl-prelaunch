<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class H
{

    private $entity;
    private $list = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    public function __construct()
    {

        $this->entity = new Entity();


        add_action('dlpl_table_head', [$this, 'head']);
        add_action('dlpl_table_row', [$this, 'row']);
        add_action('dlpl_before_scrape', [$this, 'scrape'], 10, 2);
    }

    public function head()
    {
        foreach ($this->list as $h) {
            echo '<th>' . strtoupper($h) . '</th>';
        }
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);

        foreach ($this->list as $h) {
            echo '<td>';
            if (isset($meta[$h])) {
                if ((int)$meta[$h] == 1) {
                    echo '<strong style="color:green;">' . $meta[$h] . '</strong>';
                } elseif ((int)$meta[$h] > 1) {
                    if ($h == 'h1') {
                        echo '<strong style="color:red;">' . $meta[$h] . '</strong>';
                    } else {
                        echo '<strong style="color:orange;">' . $meta[$h] . '</strong>';
                    }
                } else {
                    echo '<strong style="color:red;">' . $meta[$h] . '</strong>';
                }
            } else {
                echo __('--', 'dl-prelaunch');
            }

            echo  '</td>';
        }
    }

    public function scrape($dom, $url)
    {
        foreach ($this->list as $h) {
            $count = $dom->getElementsByTagName($h);
            $this->entity->saveMeta($url, [$h =>  count($count)]);
        }
    }
}
