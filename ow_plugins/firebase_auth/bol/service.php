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

class FIREBASEAUTH_BOL_Service 
{
    use OW_Singleton;

    /**
     * Plugin key
     */
    const PLUGIN_KEY = 'firebaseauth';

    /**
     * Twitter provider
     */
    const TWITTER_PROVIDER = 'twitter.com';

    /**
     * Facebook provider
     */
    const FACEBOOK_PROVIDER = 'facebook.com';

    /**
     * Google provider
     */
    const GOOGLE_PROVIDER = 'google.com';

    /**
     * Linkedin provider
     */
    const LINKEDIN_PROVIDER = 'linkedin.com';

    /**
     * Instagram provider
     */
    const INSTAGRAM_PROVIDER = 'instagram.com';

    /**
     * Get all registered providers
     * 
     * @return array
     */
    public function getAllRegisteredProviders()
    {
        return [
            FIREBASEAUTH_BOL_Service::TWITTER_PROVIDER => 'Twitter',
            FIREBASEAUTH_BOL_Service::FACEBOOK_PROVIDER => 'Facebook',
            FIREBASEAUTH_BOL_Service::GOOGLE_PROVIDER => 'Google',
            FIREBASEAUTH_BOL_Service::LINKEDIN_PROVIDER => 'LinkedIn',
            FIREBASEAUTH_BOL_Service::INSTAGRAM_PROVIDER => 'Instagram'
        ];
    }

    /**
     * Get plugin build
     *
     * @return integer
     */
    public function getPluginBuild()
    {
        return OW::getPluginManager()->getPlugin(self::PLUGIN_KEY)->getDto()->getBuild();
    }

    /**
     * Is firebase auth enabled
     */
    public function isFirebaseAuthEnabled()
    {
        return $this->getApiKey() != '' && $this->getAuthDomain() != '';
    }

    /**
     * Get auth domain
     * 
     * @return string
     */
    public function getAuthDomain()
    {
        return OW::getConfig()->getValue(self::PLUGIN_KEY, 'auth_domain');
    }

    /**
     * Set auth domain
     * 
     * @param string $authDomain
     * @return void
     */
    public function setAuthDomain($authDomain)
    {
        return OW::getConfig()->saveConfig(self::PLUGIN_KEY, 'auth_domain', $authDomain);
    }

    /**
     * Get api key
     * 
     * @return string
     */
    public function getApiKey()
    {
        return OW::getConfig()->getValue(self::PLUGIN_KEY, 'api_key');
    }

    /**
     * Set api key
     * 
     * @param string $apiKey
     * @return void
     */
    public function setApiKey($apiKey)
    {
        return OW::getConfig()->saveConfig(self::PLUGIN_KEY, 'api_key', $apiKey);
    }

    /**
     * Get enabled providers
     * 
     * @return array
     */
    public function getEnabledProviders()
    {
        return json_decode(OW::getConfig()->getValue(self::PLUGIN_KEY, 'enabled_providers'), true);
    }

    /**
     * Set enabled providers
     * 
     * @param array $providers
     * @return void
     */
    public function setEnabledProviders(array $providers)
    {
        return OW::getConfig()->saveConfig(self::PLUGIN_KEY, 'enabled_providers', json_encode($providers));
    }

    /**
     * Get max displayed providers
     * 
     * @return integer
     */
    public function getMaxDisplayedProviders()
    {
        return (int) OW::getConfig()->getValue(self::PLUGIN_KEY, 'max_displayed_providers');
    }

    /**
     * Set max displayed providers
     * 
     * @param integer $maxProviders
     * @return void
     */
    public function setMaxDisplayedProviders($maxProviders)
    {
        OW::getConfig()->saveConfig(self::PLUGIN_KEY, 'max_displayed_providers', $maxProviders);
    }

    /**
     * Is register allowed
     * 
     * @param string $inviteCode
     * @return boolean
     */
    public function isRegisterAllowed($inviteCode = '')
    {
        // check the join by invitations
        if ( (int) OW::getConfig()->
                getValue('base', 'who_can_join') === BOL_UserService::PERMISSIONS_JOIN_BY_INVITATIONS )
        {
            return false;
        }

        return true;
    }

    /**
     * Authenticate user
     * 
     * @param string $providerId
     * @param string $remoteId
     * @param string $username
     * @param string $email
     * @param string $photo
     * @return array
     *      string type
     *      integer id
     */
    public function authenticateUser( $providerId, $remoteId, $username = '', $email = '', $photo = '' )
    {
        // try to find a user profile using the remote id and provider id 
        $authAdapter = new FIREBASEAUTH_CLASS_FirebaseAuthAdapter($providerId, $remoteId);

        if ( $authAdapter->isRegistered() )
        {
            $authResult = OW::getUser()->authenticate($authAdapter);

            if ( $authResult->isValid() )
            {
                return [
                    'logged',
                    $authResult->getUserId()
                ];
            }

            throw new Exception('Auth failed');
        }

        // create a new user profile
        return [
            'created',
            $this->registerUser($providerId, $remoteId, $username, $email, $photo)
        ];
    }

    /**
     * Register user
     * 
     * @param string $providerId
     * @param integer $remoteId
     * @param string $username
     * @param string $email
     * @param string $photo
     * @return integer
     */
    protected function registerUser($providerId, $remoteId, $username = '', $email = '', $photo = '')
    {
        $userByEmail = BOL_UserService::getInstance()->findByEmail($email);

        // user is already registered by a some provider
        if ( $userByEmail !== null )
        {
            return $userByEmail->id;
        }

        $realName = $username;

        // process user data
        $username = mb_strtolower(str_replace(' ', '', trim($username)));

        $username = $username && UTIL_Validator::isUserNameValid($username) 
                && !BOL_UserService::getInstance()->isExistUserName($username) ? $username : $this->generateUsername();

        $email = $email && UTIL_Validator::isEmailValid($email)  
                && !BOL_UserService::getInstance()->isExistEmail($email) ? $email : $this->generateUserEmail();

        $password = uniqid();

        $event = new OW_Event(OW_EventManager::ON_BEFORE_USER_REGISTER, [
            'username' => $username,
            'password' => $password,
            'email' => $email,

        ]);

        OW::getEventManager()->trigger($event);

        // create a user's profile
        $user = BOL_UserService::getInstance()->createUser($username, $password, $email, null, true);

        // save a photo
        if ( $photo )
        {
            switch ($providerId) {
                case self::FACEBOOK_PROVIDER :
                    $photo = $photo . '?type=large&?return_ssl_resources=0'; // wee need a large photo

                    break;
                    
                case self::TWITTER_PROVIDER :
                    // https://developer.twitter.com/en/docs/accounts-and-users/user-profile-images-and-banners.html
                    $photo = str_replace([
                        '_normal', 
                        '_bigger', 
                        '_mini'
                    ], [
                        '', 
                        '', 
                        '', 
                    ], $photo); // wee need a large photo

                    break;
            }

            BOL_AvatarService::getInstance()->setUserAvatar($user->id, $photo, [
                'isModerable' => true,
                'trackAction' => false
            ]);
        }

        // save a real name
        BOL_QuestionService::getInstance()->saveQuestionsData([
            'realname' => $realName
        ], $user->id);

        // authenticate
        $authAdapter = new FIREBASEAUTH_CLASS_FirebaseAuthAdapter($providerId, $remoteId);
        $authAdapter->register($user->id);

        $authResult = OW_Auth::getInstance()->authenticate($authAdapter);

        if ( $authResult->isValid() )
        {
            $event = new OW_Event(OW_EventManager::ON_USER_REGISTER, [
                'method' => FIREBASEAUTH_CLASS_FirebaseAuthAdapter::PROVIDER_PREFIX . '.' . $providerId,
                'userId' => $user->id,
                'params' => []
            ]);

            OW::getEventManager()->trigger($event);

            return $user->id;
        }

        throw new Exception('Register user failed');
    }

    /**
     * Generate username
     * 
     * @return string
     */
    protected function generateUsername()
    {
        $username =  substr(uniqid(time() . OW_PASSWORD_SALT), 0, 32);

        if ( BOL_UserService::getInstance()->isExistUserName($username) )
        {
            return $this->generateUsername();
        }

        return $username;
    }

    /**
     * Generate user email
     * 
     * @return string
     */
    protected function generateUserEmail()
    {
        $adminEmail = OW::getConfig()->getValue('base', 'site_email');
        $parseAdminEmail = explode('@', $adminEmail);

        $email = $parseAdminEmail[0] . '+user' . uniqid(time() . OW_PASSWORD_SALT) . '@' . $parseAdminEmail[1];

        if ( BOL_UserService::getInstance()->isExistEmail($email) )
        {
            return $this->generateUserEmail();
        }

        return $email;
    }
}
