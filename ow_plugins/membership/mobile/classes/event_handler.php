<?php

/**
 * Copyright (c) 2016, Skalfa LLC
 * All rights reserved.
 *
 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under SkaDate Exclusive License by Skalfa LLC.
 *
 * Full text of this license can be found at http://www.skadate.com/sel.pdf
 */

/**
 * @author Pryadkin Sergey <GiperProger@gmai.com>
 * @package ow_plugins.membership.classes
 * @since 1.8.0
 */
class MEMBERSHIP_MCLASS_EventHandler
{
    /**
     * @var MEMBERSHIP_MCLASS_EventHandler
     */
    private static $classInstance;

    /**
     * @return MEMBERSHIP_MCLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() { }


    public function init()
    {
        OW::getEventManager()->bind('base.collect_subscribe_menu', array($this, 'getPluginForMenu'));
    }

    public function getPluginForMenu( BASE_CLASS_EventCollector $event )
    {
        $event->add(
            array(
                'label' => OW::getLanguage()->text('membership', 'memberships'),
                'url' => OW::getRouter()->urlForRoute('membership_subscribe'),
                'iconClass' => 'ow_ic_calendar',
                'key' => 'membership',
                'order' => 1
            )
        );
    }





}