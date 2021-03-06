<?php

require_once 'libs/phpass/PasswordHash.php';

define('ENCRYPTION_NONE', 0);
define('ENCRYPTION_MD5', 1);
define('ENCRYPTION_SHA1', 2);
define('ENCRYPTION_PHPASS', 3);

class HashUtils {
    public static function CreateHasher($encryptionType) {
        if (trim($encryptionType) == '')
            return new PlainStringHasher();
        else if (strtolower($encryptionType) == 'md5')
            return new MD5StringHasher();
        else if (strtolower($encryptionType) == 'sha1')
            return new SHA1StringHasher();
        else if (strtolower($encryptionType) == 'phpass')
            return new PHPassStringHasher();
        else
            return new CustomStringHasher($encryptionType);
    }
}

class PHPVersionMismatchException extends Exception {
}

abstract class StringHasher {

    /**
     * Empty constructor
     */
    public function __construct() {
    }

    /**
     * @abstract
     * @param string $string
     * @return string
     */
    public abstract function GetHash($string);

    /**
     * @abstract
     * @param string $hash
     * @param string $string
     * @return boolean
     */
    public function CompareHash($hash, $string) {
        return $hash == $this->GetHash($string);
    }
}

class PlainStringHasher extends StringHasher {
    /**
     * @param string $string
     * @return string
     */
    public function GetHash($string) {
        return $string;
    }
}

class MD5StringHasher extends StringHasher {
    /**
     * @param string $string
     * @return string
     */
    public function GetHash($string) {
        return md5($string);
    }
}

class SHA1StringHasher extends StringHasher {
    /**
     * @param string $string
     * @return string
     */
    public function GetHash($string) {
        return sha1($string);
    }
}

class CryptStringHasher extends StringHasher {
    /**
     * @param string $string
     * @return string
     */
    public function GetHash($string) {
        return crypt($string, '');
    }

    /**
     * @param string $hash
     * @param string $string
     * @return boolean
     */
    public function CompareHash($hash, $string) {
        return crypt($string, $hash) == $hash;
    }
}

class CustomStringHasher extends StringHasher {

    /** @var string */
    private $algorithmName;

    /**
     * @param string $algorithmName
     */
    public function __construct($algorithmName) {
        parent::__construct();
        $this->algorithmName = $algorithmName;
    }

    private function CheckPHPVersion() {
        if (version_compare(PHP_VERSION, '5.1.2', '<'))
            throw new PHPVersionMismatchException('Custom hash function requires php 5.1.2 or higher');
    }

    /**
     * @param string $string
     * @return string
     */
    public function GetHash($string) {
        $this->CheckPHPVersion($string);
        return hash($this->algorithmName, $string);
    }
}

class PHPassStringHasher extends StringHasher {
    /** @var \PasswordHash */
    private $hasher;

    public function __construct() {
        $this->hasher = new PasswordHash(8, FALSE);
    }

    /**
     * @param string $hash
     * @param string $string
     * @return boolean
     */
    public function CompareHash($hash, $string) {
        return $this->hasher->CheckPassword($string, $hash);
    }

    /**
     * @param string $string
     * @return string
     */
    public function GetHash($string) {
        return $this->hasher->HashPassword($string);
    }
}

