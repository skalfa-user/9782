<?php

$plugin = OW::getPluginManager()->getPlugin('svideo');

$classesToAutoload = array(
    'VideoProviders' => $plugin->getRootDir() . 'classes' . DS . 'video_providers.php'
);

OW::getAutoloader()->addClassArray($classesToAutoload);

OW::getRouter()->addRoute(new OW_Route('svideo.admin_index', 'admin/svideo', 'SVIDEO_CTRL_Admin', 'index'));

OW::getRouter()->addRoute(new OW_Route('svideo_list_index', 'svideo/', 'SVIDEO_CTRL_Svideo', 'viewList'));
