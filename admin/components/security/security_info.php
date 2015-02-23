<?php

require_once 'base_user_auth.php';
require_once 'record_level_permissions.php';

#region Auth utils functions
// TODO : move to utils class

$currentUser = null;

function SetCurrentUser($userName)
{
    global $currentUser;
    $currentUser = $userName;
}

function GetCurrentUser()
{
    // TODO : use SuperGlobals
    global $currentUser;
    if (isset($currentUser))
        return $currentUser;
    if (isset($_COOKIE['username']))
        return $_COOKIE['username'];
    else
        return 'guest';
}

// TODO : remove this function
function GetUserGrantInfo($username, $tableName)
{
    global $userGrants;
    if (isset($userGrants[$username]))
        if (isset($userGrants[$username][$tableName]))
            return $userGrants[$username][$tableName];
}

function GetCurrentUserGrantForDataSource($dataSourceName)
{
    return GetApplication()->GetCurrentUserGrants($dataSourceName);
}

function GetCurrentUserRecordPermissionsForDataSource($dataSourceName)
{
    return GetApplication()->GetCurrentUserRecordPermissionsForDataSource($dataSourceName);
}

#endregion

?>
