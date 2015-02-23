<?php

require_once 'components/page.php';
require_once 'components/security/datasource_security_info.php';
require_once 'components/security/security_info.php';
require_once 'components/security/hardcoded_auth.php';
require_once 'components/security/user_grants_manager.php';

$users = array('admin' => 'gcoos1');

$usersIds = array('admin' => -1);

$dataSourceRecordPermissions = array();

$grants = array('guest' => 
        array()
    ,
    'defaultUser' => 
        array('organization' => new DataSourceSecurityInfo(false, false, false, false),
        'platform' => new DataSourceSecurityInfo(false, false, false, false),
        'sensor' => new DataSourceSecurityInfo(false, false, false, false))
    ,
    'admin' => 
        array('organization' => new DataSourceSecurityInfo(false, false, false, false),
        'platform' => new DataSourceSecurityInfo(false, false, false, false),
        'sensor' => new DataSourceSecurityInfo(false, false, false, false))
    );

$appGrants = array('guest' => new DataSourceSecurityInfo(false, false, false, false),
    'defaultUser' => new DataSourceSecurityInfo(true, false, false, false),
    'admin' => new AdminDataSourceSecurityInfo());

$tableCaptions = array('organization' => 'organization',
'platform' => 'platform',
'sensor' => 'sensor');

function SetUpUserAuthorization()
{
    global $usersIds;
    global $grants;
    global $appGrants;
    global $dataSourceRecordPermissions;
    $userAuthorizationStrategy = new HardCodedUserAuthorization(new HardCodedUserGrantsManager($grants, $appGrants), $usersIds);
    GetApplication()->SetUserAuthorizationStrategy($userAuthorizationStrategy);

GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(
    new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}

function GetIdentityCheckStrategy()
{
    global $users;
    return new SimpleIdentityCheckStrategy($users, );
}

?>