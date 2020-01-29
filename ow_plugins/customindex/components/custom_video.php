<?php

class CUSTOMINDEX_CMP_CustomVideo extends OW_Component
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * On before render
     */
    public function onBeforeRender()
    {
        parent::onBeforeRender();

        // add css
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->
                getPlugin(CUSTOMINDEX_BOL_Service::PLUGIN_KEY)->getStaticCssUrl() . 'custom_video.css');
    }
}
