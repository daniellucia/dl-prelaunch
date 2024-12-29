<?php

namespace DLPL\Prelaunch\Utils;

use DLPL\Prelaunch\Entity;

class Debug
{

    private $entity;
    private $key = 'debug';

    public function __construct()
    {

        $this->entity = new Entity();

        add_action('dlpl_table_head', [$this, 'head']);
        add_action('dlpl_table_row', [$this, 'row']);
    }

    public function head()
    {
        echo '<th>' . __('Debug', 'dl-prelaunch') . '</th>';
    }

    public function row($link)
    {
        $meta = $this->entity->meta($link->url);

        echo '<td>';
            echo '<pre>' . print_r($meta, true) . '</pre>';
        echo  '</td>';
    }
}
