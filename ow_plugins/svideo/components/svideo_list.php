<?php

class SVIDEO_CMP_SvideoList extends OW_Component
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $video = trim(OW::getConfig()->getValue('svideo', 'video'));
        $title = trim(OW::getConfig()->getValue('svideo', 'title'));
        $description = trim(OW::getConfig()->getValue('svideo', 'description'));

        $this->assign('video', $video);
        $this->assign('title', $title);
        $this->assign('description', $description);

        if (empty($video))
        {
            $this->setVisible(false);
        }

        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('svideo')->getStaticCssUrl() . 'svideo.css');
    }

    /**
     * On before render
     */
    public function onBeforeRender()
    {
        parent::onBeforeRender();
    }
}
