<?php

/**
 * Copyright (c) 2017, Skalfa LLC
 * All rights reserved.
 * 
 * ATTENTION: This commercial software is intended for exclusive use with SkaDate Dating Software (http://www.skadate.com) and is licensed under SkaDate Exclusive License by Skalfa LLC.
 * 
 * Full text of this license can be found at http://www.skadate.com/sel.pdf
 */

class SUBRED_CLASS_EventHandler
{
    public function __construct(){}

    public function init()
    {
        $em = OW::getEventManager();
        $em->bind(OW_EventManager::ON_AFTER_ROUTE, array($this, "onAfterRoute"));
    }

    public function onAfterRoute(){

        if(!OW::getUser()->isAuthenticated()){
            return;
        }

        $redirectToSubscribe = SUBRED_BOL_Service::getInstance()->redirectToSubscribe(OW::getUser()->getId());

        if( !$redirectToSubscribe ){
            return;
        }

        $attributes = array(
            OW_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'MEMBERSHIP_CTRL_Subscribe',
            OW_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'index'
        );

        OW::getRequestHandler()->setCatchAllRequestsAttributes("subred.redirect_to_subscribe", $attributes);
        OW::getRequestHandler()->addCatchAllRequestsExclude("subred.redirect_to_subscribe", 'BASE_CTRL_User', 'signOut');
        OW::getRequestHandler()->addCatchAllRequestsExclude("subred.redirect_to_subscribe", 'BILLINGPAYPAL_CTRL_Order');
    }
}