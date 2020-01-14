<?php

/**
 * Copyright (c) 2019, Skalfa LLC
 * All rights reserved.
 *
 * ATTENTION: This commercial software is intended for exclusive use with SkaDate Dating Software (http://www.skadate.com)
 * and is licensed under SkaDate Exclusive License by Skalfa LLC.
 *
 * Full text of this license can be found at http://www.skadate.com/sel.pdf
 */

class FIREBASEAUTH_CLASS_SettingsForm extends Form
{
    /**
     * Class constructor
     * 
     * @param array $params
     */
    public function __construct( array $params ) 
    {
        // process params
        $formName = !empty($params['name']) ? $params['name'] : 'settings-form';

        parent::__construct($formName);

        // api key
        $apiKey = new TextField('api_key');
        $apiKey->setRequired(true);
        $apiKey->setValue(FIREBASEAUTH_BOL_Service::getInstance()->getApiKey());
        $apiKey->setLabel(OW::getLanguage()->text(FIREBASEAUTH_BOL_Service::PLUGIN_KEY, 'form_api_title_label'));

        $this->addElement($apiKey);

        // auth  domain
        $authDomain = new TextField('auth_domain');
        $authDomain->setRequired(true);
        $authDomain->setValue(FIREBASEAUTH_BOL_Service::getInstance()->getAuthDomain());
        $authDomain->setLabel(OW::getLanguage()->text(FIREBASEAUTH_BOL_Service::PLUGIN_KEY, 'form_auth_domain_title_label'));

        $this->addElement($authDomain);

        // process providers
        $processedProviders = [];
        $allProviders = FIREBASEAUTH_BOL_Service::getInstance()->getAllRegisteredProviders();

        foreach ( $allProviders as $providerKey => $providerLabel )
        {
            $processedProviders[$providerKey] = 
                    '<span class="firebase-auth-provider" data-provider="' . $providerKey . '"></span>' . $providerLabel;
        }

        // providers
        $providers = new CheckboxGroup('enabled_providers');
        $providers->setValue(FIREBASEAUTH_BOL_Service::getInstance()->getEnabledProviders());
        $providers->setOptions($processedProviders);
        $providers->setColumnCount(3);

        $this->addElement($providers);

        // submit
        $submit = new Submit('submit');
        $submit->setValue(OW::getLanguage()->text(FIREBASEAUTH_BOL_Service::PLUGIN_KEY, 'form_submit_label'));
        $this->addElement($submit);
    }

    /**
     * Save settings
     * 
     * @return void
     */
    public function saveSettings()
    {
        FIREBASEAUTH_BOL_Service::getInstance()->setApiKey($this->getElement('api_key')->getValue());
        FIREBASEAUTH_BOL_Service::getInstance()->setAuthDomain($this->getElement('auth_domain')->getValue());
        FIREBASEAUTH_BOL_Service::getInstance()->setEnabledProviders($this->getElement('enabled_providers')->getValue());
    }
}
