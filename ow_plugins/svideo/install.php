<?php

OW::getPluginManager()->addPluginSettingsRouteName('svideo', 'svideo.admin_index');

if (!OW::getConfig()->configExists('svideo', 'video'))
{
    OW::getConfig()->addConfig('svideo', 'video', '');
}

if (!OW::getConfig()->configExists('svideo', 'title'))
{
    OW::getConfig()->addConfig('svideo', 'title', '');
}

if (!OW::getConfig()->configExists('svideo', 'description'))
{
    OW::getConfig()->addConfig('svideo', 'description', '');
}

OW::getLanguage()->importLangsFromDir(__DIR__ . DS . 'langs', true, true);