<?php

function CheckPHPVersion()
{
    $versionString = phpversion();
    $version = explode('.', $versionString);
    if ($version[0] < 5)
    {
        header('Content-Type: text/html; charset=UTF-8');
        echo str_replace('{PHP_VERSION}', $versionString,
            file_get_contents('components/templates/unsupported_php_version.html'));
        exit;
    }
}

function CheckMbStringExtension()
{
    if (!function_exists("mb_strlen"))
    {
        header('Content-Type: text/html; charset=UTF-8');
        $result = file_get_contents('components/templates/required_extension.html');
        $result = str_replace('{MESSAGE}', 'mPDF requires mb_string functions', $result);
        $result = str_replace('{DETAILS}', 'Ensure that PHP is compiled with <strong>php_mbstring.dll</strong> enabled. <br/>See more: ' .
            '<a href="http://php.net/manual/en/mbstring.installation.php">http://php.net/manual/en/mbstring.installation.php</a>', $result);
        echo $result;
        exit;
    }
}

function CheckIconvExtension()
{
    if (!function_exists("iconv"))
    {
        header('Content-Type: text/html; charset=UTF-8');
        $result = file_get_contents('components/templates/required_extension.html');
        $result = str_replace('{MESSAGE}', 'mPDF requires iconv extension', $result);
        $result = str_replace('{DETAILS}', 'Ensure that PHP is compiled with <strong>php_iconv</strong> enabled. <br/>See more: ' .
            '<a href="http://www.php.net/manual/en/iconv.installation.php">http://www.php.net/manual/en/iconv.installation.php</a>', $result);
        echo $result;
        exit;
    }
}

?>