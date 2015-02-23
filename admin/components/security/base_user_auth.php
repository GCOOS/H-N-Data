<?php

require_once 'datasource_security_info.php';
require_once 'components/utils/hash_utils.php';

abstract class AbstractUserAuthorization
{
    public abstract function GetCurrentUserId();

    public abstract function GetCurrentUser();

    public abstract function IsCurrentUserLoggedIn();

    public abstract function GetUserRoles($userName, $dataSourceName);

    public abstract function HasAdminGrant($userName);

    public function ApplyIdentityToConnectionOptions(&$connectoinOptions) { }
}

class NullUserAuthorization
{
    public function GetCurrentUser()
    {
        return null; 
    }
    
    public function GetUserRoles($userName, $dataSourceName)
    {
        return new AdminDataSourceSecurityInfo();
    } 
    
    public function IsCurrentUserLoggedIn() { }

    public function GetCurrentUserId()
    {
        return 0; 
    }    

    public function HasAdminGrant($userName)
    {
        return false;
    }

    public function ApplyIdentityToConnectionOptions(&$connectoinOptions) { }
}

class IdentityCheckStrategy
{
    function ApplyIdentityToConnectionOptions($connectionOptions) { }
}

?>