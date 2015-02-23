<?php

require_once 'datasource_security_info.php';

class ServerSideUserAuthorization extends AbstractUserAuthorization
{
    private $rolesSecurityInfo;
    private $allRoles;
    private $guestUserName;
    private $allowGuestAccess;
    private $guestServerLogin;
    private $guestServerPassword;
    
    public function __construct($rolesSecurityInfo, $guestUserName, $allowGuestAccess, $guestServerLogin, $guestServerPassword)
    {
        $this->rolesSecurityInfo = $rolesSecurityInfo;
        $this->allRoles = new DataSourceSecurityInfo(true, true, true, true);
        $this->guestUserName = $guestUserName;
        $this->allowGuestAccess = $allowGuestAccess;
        $this->guestServerLogin = $guestServerLogin;
        $this->guestServerPassword = $guestServerPassword;
    }
    
    public function GetCurrentUserId() { return null; }
    
    public function GetCurrentUser() { return GetCurrentUser(); }
    public function IsCurrentUserLoggedIn() { return $this->GetCurrentUser() != 'guest'; }
    
    public function GetUserRoles($userName, $dataSourceName)
    {
        return $this->allRoles;
    }
    
    public function ApplyIdentityToConnectionOptions(&$connectoinOptions)
    {
        if ($this->GetCurrentUser() == $this->guestUserName)
        {
            if ($this->allowGuestAccess)
            {
                $connectoinOptions['username'] = $this->guestServerLogin;
                $connectoinOptions['password'] = $this->guestServerPassword;
            }
            else
                RaiseError(GetCaptions()->GetMessageString('GuestAccessDenied'));
        }
        else
        {
            $connectoinOptions['username'] = $this->GetCurrentUser();
            $connectoinOptions['password'] = $_COOKIE['password'];
        }
    }

    public function HasAdminGrant($userName)
    {
        return false;
    }
}

class ServerSideIdentityCheckStrategy
{
    private $connectionFactory;
    private $connectionOptions;

    public function __construct($connectionFactory, $connectionOptions)
    {
        $this->connectionFactory = $connectionFactory;
        $this->connectionOptions = $connectionOptions;
    }

    public function CheckUsernameAndPassword($username, $password, &$errorMessage)
    {
        $this->connectionOptions['username'] = $username;
        $this->connectionOptions['password'] = $password;
        
        $connection = $this->connectionFactory->CreateConnection($this->connectionOptions);
        $connection->Connect();
        if ($connection->Connected())
        {
            $errorMessage = null;
            $connection->Disconnect();
            return true;            
        }
        else
        {   
            $errorMessage = $connection->LastError();//'The username/password combination you entered was invalid.';
            return false;
        }
    }
}

?>