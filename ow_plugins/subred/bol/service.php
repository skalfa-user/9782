<?php

/**
 * Copyright (c) 2017, Skalfa LLC
 * All rights reserved.
 *
 * ATTENTION: This commercial software is intended for exclusive use with SkaDate Dating Software (http://www.skadate.com) and is licensed under SkaDate Exclusive License by Skalfa LLC.
 *
 * Full text of this license can be found at http://www.skadate.com/sel.pdf
 */

/**
 * @author Sergey Pryadkin <GiperProger@gmail.com>
 */
class SUBRED_BOL_Service {

    const MALE_ACCOUNT_TYPE_HASH = '808aa8ca354f51c5a3868dad5298cd72';

    /**
     * Class instance
     *
     * @var SUBRED_BOL_Service
     */
    private static $classInstance;

    /**
     * Class constructor
     *
     */
    protected function __construct() {}

    /**
     * Returns class instance
     *
     * @return SUBRED_BOL_Service
     */
    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function redirectToSubscribe($userId)
    {
        $userObject = BOL_UserService::getInstance()->findUserById($userId);

        $userRoleList = BOL_AuthorizationService::getInstance()->findUserRoleList($userId);

        if( count($userRoleList) > 1 || $userObject->accountType != self::MALE_ACCOUNT_TYPE_HASH ){
            return false;
        }

        return true;
    }
}
