<?php

require_once 'components/common.php'; // TODO: remove
require_once 'components/common_utils.php'; // TODO: remove
require_once 'components/error_utils.php'; // TODO: remove
require_once 'database_engine/commands.php';
require_once 'database_engine/select_command.php';

class SMSQLException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}

$connectionPool = array();

register_shutdown_function('FinalizeConnectionPool');

function FinalizeConnectionPool() {

    global $connectionPool;
    foreach ($connectionPool as $hash => $connection)
        $connection->Disconnect();
}

interface IEngDataReader {

}

interface IEngConnection {
    /**
     * @param string $sql
     * @return IEngDataReader
     */
    function CreateDataReader($sql);

    /**
     * @return bool
     */
    function Connected();

    /**
     * @param string $sql
     * @return void
     */
    function ExecSQL($sql);

    /**
     * @param string $sql
     * @return mixed
     */
    function ExecScalarSQL($sql);

    /**
     * @return void
     */
    function Connect();

    /**
     * @return void
     */
    function Disconnect();

    /**
     * @return bool
     */
    public function SupportsLastInsertId();

    /**
     * @return mixed
     */
    public function GetLastInsertId();

    /**
     * @param string $sql
     * @param array $array
     * @return void
     */
    function ExecQueryToArray($sql, &$array);
}

abstract class ConnectionFactory {


    private function GetConnectionParamsHash($connectionParams) {
        $result = '';
        foreach ($connectionParams as $key => $value) {
            $result .= $value;
        }
        $result .= get_class($this);
        if (function_exists('md5'))
            return md5($result);
        else
            return $result;
    }

    /**
     * @param $AConnectionParams
     * @return EngConnection
     */
    public final function CreateConnection($AConnectionParams) {
        global $connectionPool;
        if (!isset($connectionPool[$this->GetConnectionParamsHash($AConnectionParams)])) {
            $connectionPool[$this->GetConnectionParamsHash($AConnectionParams)] =
                $this->DoCreateConnection($AConnectionParams);
        }
        return $connectionPool[$this->GetConnectionParamsHash($AConnectionParams)];

    }

    /**
     * @param array $AConnectionParams
     * @return EngConnection
     */
    public abstract function DoCreateConnection($AConnectionParams);

    /**
     * @abstract
     * @param EngConnection $AConnection
     * @param string $ASQL
     * @return EngDataReader
     */
    abstract function CreateDataset($AConnection, $ASQL);

    public function CreateEngCommandImp() {
        return new EngCommandImp($this);
    }

    public function CreateSelectCommand() {
        return new SelectCommand($this->CreateEngCommandImp());
    }

    public function CreateUpdateCommand() {
        return new UpdateCommand($this->CreateEngCommandImp());
    }

    public function CreateInsertCommand() {
        return new InsertCommand($this->CreateEngCommandImp());
    }

    public function CreateDeleteCommand() {
        return new DeleteCommand($this->CreateEngCommandImp());
    }

    public function CreateCustomSelectCommand($sql) {
        return new CustomSelectCommand($sql, $this->CreateEngCommandImp());
    }

    public function CreateCustomUpdateCommand($sql) {
        if (is_array($sql))
            return new MultiStatementUpdateCommand($sql, $this->CreateEngCommandImp());
        else
            return new CustomUpdateCommand($sql, $this->CreateEngCommandImp());
    }

    public function CreateCustomInsertCommand($sql) {
        if (is_array($sql))
            return new MultiStatementInsertCommand($sql, $this->CreateEngCommandImp());
        else
            return new CustomInsertCommand($sql, $this->CreateEngCommandImp());
    }

    public function CreateCustomDeleteCommand($sql) {
        if (is_array($sql))
            return new MultiStatementDeleteCommand($sql, $this->CreateEngCommandImp());
        else
            return new CustomDeleteCommand($sql, $this->CreateEngCommandImp());
    }
}

abstract class EngConnection implements IEngConnection {
    private $connectionParams;
    private $connected;

    public $OnAfterConnect;

    protected abstract function DoConnect();

    protected abstract function DoDisconnect();

    protected abstract function DoCreateDataReader($sql);

    public function ExecScalarSQL($sql) { }

    /**
     * @param $paramName
     * @return string
     */
    public function ConnectionParam($paramName) {
        return isset($this->connectionParams[$paramName]) ? $this->connectionParams[$paramName] : '';
    }

    public function HasConnectionParam($paramName) {
        return isset($this->connectionParams[$paramName]);
    }

    protected function FormatConnectionParams() {
        return $this->ConnectionParam('server');
    }

    public function __construct($connectionParams) {
        $this->connectionParams = $connectionParams;
        $this->OnAfterConnect = new Event();
        $this->serverVersion = new SMVersion(0, 0);
    }

    public function CreateDataReader($sql) {
        return $this->DoCreateDataReader($sql);
    }

    public function GetConnectionHandle() {
        return null;
    }

    public function IsDriverSupported() {
        return true;
    }

    protected function DoGetDBMSName() {
        return '';
    }

    protected function DoGetDriverExtensionName() {
        return 'database';
    }

    protected function DoGetDriverInstallationLink() {
        return 'http://www.php.net';
    }

    public function GetDriverNotSupportedMessage() {
        return sprintf(
            'We were unable to use the %s database because the %s extension for PHP is not installed. ' .
                'Check your PHP.ini to see how you can enable it. ' .
                '<a href="%s">Check out the documentation</a> to see how to install extension.',
            $this->DoGetDBMSName(),
            $this->DoGetDriverExtensionName(),
            $this->DoGetDriverInstallationLink()
        );
    }

    public function GetClientEncoding() {
        return $this->clientEncoding;
    }

    public function SetClientEncoding($value) {
        $this->clientEncoding = $value;
    }

    public function Connected() {
        return $this->connected;
    }

    protected function DoExecSQL($sql) {
    }

    public function ExecSQL($sql) {
        if (!$this->DoExecSQL($sql))
            RaiseError($this->LastError());
    }

    public function ExecSQLEx($sql) {
        if (!$this->DoExecSQL($sql))
            throw new SMSQLException('Cannot execute SQL: ' . $sql . "\n" . $this->LastError());
    }

    //public abstract function ExecScalarSQL($sql);

    public function ExecQueryToArray($sql, &$array) {
        $dataReader = $this->CreateDataReader($sql);
        $dataReader->Open();

        while ($dataReader->Next()) {
            $row = array();
            for ($i = 0; $i < $dataReader->FieldCount(); $i++) {
                $row[$dataReader->GetField($i)] =
                    $dataReader->GetFieldValueByName($dataReader->GetField($i));
            }
            $array[] = $row;
        }

        $dataReader->Close();
    }

    private function CheckDriverSupported() {
        if (!$this->IsDriverSupported()) {
            RaiseError(sprintf('Could not connect to %s: %s',
                $this->FormatConnectionParams(),
                $this->LastError()
            ));
        }
    }

    public function SupportsLastInsertId() {
        return false;
    }

    public function GetLastInsertId() {
        return 0;
    }

    public function Connect() {
        if (!$this->Connected()) {
            $this->CheckDriverSupported();

            $this->connected = $this->DoConnect();
            if (!$this->Connected()) {
                RaiseError(sprintf('Could not connect to %s: %s',
                    $this->FormatConnectionParams(),
                    $this->LastError()
                ));
            } else {
                $this->OnAfterConnect->Fire(array(&$this));
            }
        }
    }

    public function Disconnect() {
        if ($this->Connected()) {
            $this->DoDisconnect();
            $this->connected = false;
        }
    }

    public function DoLastError() {
        return '';
    }

    public function LastError() {
        if (!$this->IsDriverSupported())
            return $this->GetDriverNotSupportedMessage();
        else
            return $this->DoLastError();
    }

    /**
     * @return SMVersion
     */
    public function GetServerVersion() {
        return $this->serverVersion;
    }
}

abstract class EngDataReader {
    /** @var string */
    private $sql;
    /** @var \IEngConnection */
    private $connection;
    /** @var string[] */
    private $fieldList;

    /** @var int */
    private $rowLimit;

    /** @var array */
    private $fieldInfos;

    /**
     * @param IEngConnection $connection
     * @param string $sql
     */
    public function __construct($connection, $sql) {
        $this->fieldInfos = array();
        $this->connection = $connection;
        $this->sql = $sql;
        $this->fieldList = array();
        $this->rowLimit = -1;
    }

    #region Internal field names management

    /**
     * @return string
     */
    protected function FetchField() {
    }

    protected function FetchFields() {
        $Field = $this->FetchField();
        while ($Field) {
            $this->AddField($Field);
            $Field = $this->FetchField();
        }
    }

    protected function GetFieldIndexByName($fieldName) {
        return array_search($fieldName, $this->fieldList);
    }

    protected function AddField($field) {
        $this->fieldList[] = $field;
    }

    protected function ClearFields() {
        $this->fieldList = array();
    }

    public function FieldCount() {
        return count($this->fieldList);
    }

    public function GetField($index) {
        return $this->fieldList[$index];
    }

    #endregion

    #region Field management

    /**
     * @param FieldInfo $fieldInfo
     */
    public function AddFieldInfo(FieldInfo $fieldInfo) {
        if (isset($fieldInfo->Alias))
            $this->fieldInfos[$fieldInfo->Alias] = $fieldInfo;
        else
            $this->fieldInfos[$fieldInfo->Name] = $fieldInfo;
    }

    /**
     * @param string $fieldName
     * @return string
     */
    public function GetFieldInfoByFieldName($fieldName) {
        if (isset($this->fieldInfos[$fieldName]))
            return $this->fieldInfos[$fieldName];
        else
            return null;
    }

    #endregion

    protected abstract function DoOpen();

    public abstract function Opened();

    protected function DoClose() {
    }

    public function GetSQL() {
        return $this->sql;
    }

    public function SetSQL($sql) {
        $this->sql = $sql;
    }

    public function SetRowLimit($value) {
        $this->rowLimit = $value;
    }

    public function GetRowLimit() {
        return $this->rowLimit;
    }

    public function GetConnection() {
        return $this->connection;
    }

    public function Open() {
        if (!$this->Opened()) {
            $this->ClearFields();
            if (!$this->DoOpen()) {
                RaiseError($this->LastError());
            }
            if ($this->Opened()) {
                $this->FetchFields();
            }
        }
    }

    public function Close() {
        if ($this->Opened())
            $this->DoClose();
    }

    /**
     * @abstract
     * @return boolean
     */
    public abstract function Next();

    protected function LastError() {
        return $this->GetConnection()->LastError();
    }

    protected function GetDateTimeFieldValueByName(&$value) {
        if (isset($value))
            return SMDateTime::Parse($value, '%Y-%m-%d %H:%M:%S');
        else
            return null;
    }

    protected function GetDateFieldValueByName(&$value) {
        if (isset($value))
            return SMDateTime::Parse($value, '%Y-%m-%d');
        else
            return null;
    }

    protected function GetTimeFieldValueByName(&$value) {
        if (isset($value))
            return SMDateTime::Parse($value, '%H:%M:%S');
        else
            return null;
    }

    /**
     * @abstract
     * @param string $fieldName
     * @return mixed
     */
    public abstract function GetFieldValueByName($fieldName);

    protected function GetActualFieldValue(&$fieldName, $value) {
        $fieldInfo = $this->GetFieldInfoByFieldName($fieldName);
        if (!isset($fieldInfo))
            return $value;
        if ($fieldInfo->FieldType == ftDateTime)
            return $this->GetDateTimeFieldValueByName($value);
        elseif ($fieldInfo->FieldType == ftDate)
            return $this->GetDateFieldValueByName($value); elseif ($fieldInfo->FieldType == ftTime)
            return $this->GetTimeFieldValueByName($value); else {
            return $value;
        }
    }
}

?>