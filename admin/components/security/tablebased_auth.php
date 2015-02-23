<?php

require_once 'datasource_security_info.php';
require_once 'security_info.php';
require_once 'user_grants_manager.php';
//
require_once 'database_engine/engine.php';
require_once 'components/common_utils.php';
require_once 'components/dataset/dataset.php';
require_once 'components/dataset/table_dataset.php';
require_once 'libs/phpass/PasswordHash.php';

class TableBasedUserAuthorization extends AbstractUserAuthorization {
    private $usersTable;
    private $userNameFieldName;
    private $userIdFieldName;
    private $dataset;

    /** @var \UserGrantsManager */
    private $grantsManager;

    public function __construct(
        $connectionFactory,
        $connectionOptions,
        $usersTable,
        $userNameFieldName,
        $userIdFieldName,
        UserGrantsManager $grantsManager) {
        $this->usersTable = $usersTable;
        $this->userIdFieldName = $userIdFieldName;
        $this->userNameFieldName = $userNameFieldName;
        $this->grantsManager = $grantsManager;

        $this->dataset = new TableDataset(
            $connectionFactory,
            $connectionOptions,
            $usersTable);
        $field = new StringField($userNameFieldName);
        $this->dataset->AddField($field, true);
        $field = new StringField($userIdFieldName);
        $this->dataset->AddField($field, false);
    }

    public function GetCurrentUserId() {
        $result = null;
        $this->dataset->AddFieldFilter(
            $this->userNameFieldName,
            new FieldFilter($this->GetCurrentUser(), '=', true));
        $this->dataset->Open();
        if ($this->dataset->Next())
            $result = $this->dataset->GetFieldValueByName($this->userIdFieldName);
        $this->dataset->Close();
        $this->dataset->ClearFieldFilters();
        return $result;
    }

    public function GetCurrentUser() {
        return GetCurrentUser();
    }

    public function IsCurrentUserLoggedIn() {
        return $this->GetCurrentUser() != 'guest';
    }

    public function GetUserRoles($userName, $dataSourceName) {
        return $this->grantsManager->GetSecurityInfo($userName, $dataSourceName);
    }

    public function HasAdminGrant($userName) {
        return $this->grantsManager->HasAdminGrant($userName);
    }

}

class TableBasedIdentityCheckStrategy {
    private $tableName;
    private $userNameFieldName;
    private $passwordFieldName;
    private $passwordEncryption;
    private $passwordHasher;
    private $userIdFieldName;

    private $dataset;

    /**
     * @param string $actualPassword
     * @param string $expectedPassword
     * @return bool
     */
    private function CheckPasswordEquals($actualPassword, $expectedPassword) {
        return $this->passwordHasher->CompareHash($expectedPassword, $actualPassword);
    }

    public function __construct($connectionFactory, $connectionOptions, $tableName, $userNameFieldName, $passwordFieldName, $passwordEncryption = '', $userIdFieldName = null) {
        $this->userNameFieldName = $userNameFieldName;
        $this->passwordFieldName = $passwordFieldName;
        $this->passwordHasher = HashUtils::CreateHasher($passwordEncryption);

        $this->dataset = new TableDataset(
            $connectionFactory,
            $connectionOptions,
            $tableName);
        $field = new StringField($userNameFieldName);
        $this->dataset->AddField($field, true);
        $field = new StringField($passwordFieldName);
        $this->dataset->AddField($field, false);
    }

    public function CheckUsernameAndPassword($username, $password, &$errorMessage) {
        $this->dataset->AddFieldFilter(
            $this->userNameFieldName,
            new FieldFilter($username, '=', true));
        $this->dataset->Open();
        if ($this->dataset->Next()) {
            $expectedPassword = $this->dataset->GetFieldValueByName($this->passwordFieldName);
            if ($this->CheckPasswordEquals($password, $expectedPassword)) {
                return true;
            } else {
                $errorMessage = 'The username/password combination you entered was invalid.';
                return false;
            }
        } else {
            $errorMessage = 'The username/password combination you entered was invalid.';
            return false;
        }
    }

}

?>