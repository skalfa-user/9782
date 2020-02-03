<?php

/**
 * Copyright (c) 2019, Skalfa LLC
 * All rights reserved.
 *
 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.com/
 * and is licensed under Oxwall Store Commercial License.
 *
 * Full text of this license can be found at http://developers.oxwall.com/store/oscl
 */

abstract class FIREBASEAUTH_CLASS_AbstractEventHandler
{
    /**
     * Generic init
     *
     * @return void
     */
    public function genericInit()
    {
        OW::getEventManager()->bind('skmobileapp.get_translations', [$this, 'onGetAppTranslations']);
        OW::getEventManager()->bind('skmobileapp.get_application_config', [$this, 'onGetAppConfigs']);

        OW::getEventManager()->bind('base.members_only_exceptions', array($this, 'addGoogleException'));
        OW::getEventManager()->bind('base.splash_screen_exceptions', array($this, 'addGoogleException'));
        OW::getEventManager()->bind('base.password_protected_exceptions', array($this, 'addGoogleException'));
    }

    /**
     * Get translations for the skmobileapp
     *
     * @return void
     */
    public function onGetAppTranslations( OW_Event $event )
    {
        $langService = BOL_LanguageService::getInstance();
        $translations = [];
        $language = OW::getLanguage();

        $langs = $langService->findAllPrefixKeys($langService->findPrefixId(FIREBASEAUTH_BOL_Service::PLUGIN_KEY));

        if ( !empty($langs) )
        {
            foreach ( $langs as $item )
            {
                $translations[$item->key] = $language->text(FIREBASEAUTH_BOL_Service::PLUGIN_KEY, $item->key);
            }

            $event->add(FIREBASEAUTH_BOL_Service::PLUGIN_KEY, $translations);
        }
    }

    /**
     * Get configs for the skmobileapp
     *
     * @return void
     */
    public function onGetAppConfigs( OW_Event $event )
    {
        if ( FIREBASEAUTH_BOL_Service::getInstance()->isFirebaseAuthEnabled() )
        {
            $event->setData(array_merge(
                $event->getData(), [
                    'maxDisplayedAuthProviders' => FIREBASEAUTH_BOL_Service::getInstance()->getMaxDisplayedProviders(),
                    'authProviders' => FIREBASEAUTH_BOL_Service::getInstance()->getEnabledProviders()
                ]
            ));
        }
    }

    public function addGoogleException( BASE_CLASS_EventCollector $e )
    {
        $e->add(array('controller' => 'FIREBASEAUTH_CTRL_Connect', 'action' => 'authenticate'));
    }
}
