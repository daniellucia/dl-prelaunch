<?php

namespace DLPL\Prelaunch;

class Menu
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add']);
    }

    public function add()
    {
        
            
            add_submenu_page(
                'tools.php',
                __('Prelaunch', 'dl-prelaunch'),
                __('Prelaunch', 'dl-prelaunch'),
                'edit_posts',
                "dl-prelaunch",
                function ()  {
                    $editor = new Editor();
                    $editor->render_editor_page();
                }
            );
    }
}
