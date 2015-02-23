<?php

require_once 'datasource_security_info.php';
require_once 'user_grants_manager.php';

class HardCodedUserAuthorization extends AbstractUserAuthorization {
    private $grantsManager;
    private $userIds;

    public function __construct(
        UserGrantsManager $grantsManager,
        $userIds) {
        $this->grantsManager = $grantsManager;
        $this->userIds = $userIds;
    }

    public function GetCurrentUserId() {
        if (isset($this->userIds[$this->GetCurrentUser()]))
            return $this->userIds[$this->GetCurrentUser()];
        else
            return null;
    }

    public function GetCurrentUser() {
        return GetCurrentUser();
    }

    public function HasAdminGrant($userName) {
        return $this->grantsManager->HasAdminGrant($userName);
    }

    public function IsCurrentUserLoggedIn() {
        return $this->GetCurrentUser() != 'guest';
    }

    public function GetUserRoles($userName, $dataSourceName) {
        return $this->grantsManager->GetSecurityInfo($userName, $dataSourceName);
    }
}

class SimpleIdentityCheckStrategy extends IdentityCheckStrategy {
    private $userInfos;
    private $passwordHasher;

    public function __construct($userInfos, $passwordEncryption = ENCRYPTION_NONE) {
        $this->userInfos = $userInfos;
        $this->passwordHasher = HashUtils::CreateHasher($passwordEncryption);
    }

    private function CheckPasswordEquals($actualPassword, $expectedPassword) {
        return $this->passwordHasher->CompareHash($expectedPassword, $actualPassword);
    }

    public function CheckUsernameAndPassword($username, $password, &$errorMessage) {
        if (isset($this->userInfos[$username]) && $this->CheckPasswordEquals($password, $this->userInfos[$username])) {
            $errorMessage = null;
            return true;
        } else {
            $errorMessage = 'The username/password combination you entered was invalid.';
            return false;
        }
    }
}

?>