<?php

//  define('SHOW_VARIABLES', 1);
//  define('DEBUG_LEVEL', 1);

//  error_reporting(E_ALL ^ E_NOTICE);
//  ini_set('display_errors', 'On');

set_include_path('.' . PATH_SEPARATOR . get_include_path());


require_once 'components/utils/system_utils.php';

//  SystemUtils::DisableMagicQuotesRuntime();

SystemUtils::SetTimeZoneIfNeed('America/Tegucigalpa');

function GetGlobalConnectionOptions()
{
    return array(
  'database' => '/opt/apache-2.2.22/htdocs/nutrients/data/gcoos_data_v3b_wq.sqlite'
);
}

function HasAdminPage()
{
    return false;
}

function GetPageInfos()
{
    $result = array();
    $result[] = array('caption' => 'Organization', 'short_caption' => 'Organization', 'filename' => 'organization.php', 'name' => 'organization');
    $result[] = array('caption' => 'Platform', 'short_caption' => 'Platform', 'filename' => 'platform.php', 'name' => 'platform');
    $result[] = array('caption' => 'Sensor', 'short_caption' => 'Sensor', 'filename' => 'sensor.php', 'name' => 'sensor');
    return $result;
}

function GetPagesHeader()
{
    return '';
}

function GetPagesFooter()
{
    return ''; 
    }

function ApplyCommonPageSettings($page, $grid)
{
    $page->SetShowUserAuthBar(true);
    $grid->BeforeUpdateRecord->AddListener('Global_BeforeUpdateHandler');
    $grid->BeforeDeleteRecord->AddListener('Global_BeforeDeleteHandler');
    $grid->BeforeInsertRecord->AddListener('Global_BeforeInsertHandler');
}

/*
  Default code page: 1252
*/
function GetAnsiEncoding() { return 'windows-1252'; }

function Global_BeforeUpdateHandler($page, $rowData, &$cancel, &$message, $tableName)
{

}

function Global_BeforeDeleteHandler($page, $rowData, &$cancel, &$message, $tableName)
{

}

function Global_BeforeInsertHandler($page, $rowData, &$cancel, &$message, $tableName)
{

}

function GetDefaultDateFormat()
{
    return 'Y-m-d';
}

function GetFirstDayOfWeek()
{
    return 0;
}

?>