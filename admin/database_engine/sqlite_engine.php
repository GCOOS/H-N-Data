<?php

include_once("engine.php");

class SqliteConnectionFactory extends ConnectionFactory
{
    public function DoCreateConnection($AConnectionParams)
    {
        return new SqliteConnection($AConnectionParams);
    }

    public function CreateDataset($AConnection, $ASQL)
    {
        return new SqliteDataReader($AConnection, $ASQL);
    }

    public function CreateEngCommandImp()
    {
        return new SqliteEngCommandImp($this);
    }
}

class SqliteBaseCommandImp extends EngCommandImp
{
    public function GetFieldAsSQLInSelectFieldList($fieldInfo)
    {
        if (isset($fieldInfo->Alias) && $fieldInfo->Alias != '')
            return $this->GetFieldFullName($fieldInfo) . ' AS ' . $fieldInfo->Alias;
        else
            return $this->GetFieldFullName($fieldInfo) . ' AS ' . $fieldInfo->Name;
    }

    public function QuoteIndetifier($identifier)
    {
        return '"'.$identifier.'"';
    }

    public function GetFieldValueAsSQL($fieldInfo, $value)
    {
        if ($fieldInfo->FieldType == ftBlob)
        {
            if (is_array($value))
            {
                return '\'' . sqlite_escape_string(file_get_contents($value[0])) . '\'';
            }
            else
            {
                return '\'' . sqlite_escape_string($value) . '\'';
            }


        }
        else
            return parent::GetFieldValueAsSQL($fieldInfo, $value);
    }
    
    public function GetCastToCharExpresstion($value)
    {
        return $value;
    }

    public function SupportsDefaultValue()
    {
        return false;
    }
    public function DoExecuteCustomSelectCommand($connection, $command)
    {
        $upLimit = $command->GetUpLmit();
        $limitCount = $command->GetLimitCount();

        if (isset($upLimit) && isset($limitCount))
        {
            $sql = sprintf('SELECT * FROM (%s) a LIMIT %s, %s',
                $command->GetSQL(),
                $upLimit,
                $limitCount
            );
            $result = $this->GetConnectionFactory()->CreateDataset($connection, $sql);
            $result->Open();
            return $result;
        }
        else
        {
            return parent::DoExecuteSelectCommand($connection, $command);
        }
    }
}

class SqliteEngCommandImp extends SqliteBaseCommandImp
{
    public function EscapeString($string)
    {
        return sqlite_escape_string($string);
    }


}


class SqliteConnection extends EngConnection
{
    private $connectionHandle;
    private $connectionError = '';

    public function IsDriverSupported()
    {
        return function_exists('sqlite_open');
    }

    protected function DoGetDBMSName() {
        return 'SQLite';
    }

    protected function DoGetDriverExtensionName() {
        return 'sqlite';
    }

    protected function DoGetDriverInstallationLink() {
        return 'http://www.php.net/manual/en/sqlite.installation.php';
    }

    protected function DoConnect()
    {
        $this->connectionHandle = @sqlite_open($this->ConnectionParam('database'), 0666, $this->connectionError);
        if (!$this->connectionHandle)
            return false;
        return true;
    }

    protected function DoCreateDataReader($sql)
    {
        return new SqliteDataReader($this, $sql);
    }

    protected function DoDisconnect()
    {
        @sqlite_close($this->connectionHandle);
    }

    public function GetConnectionHandle()
    {
        return $this->connectionHandle;
    }

    protected function DoExecSQL($ASQL)
    {
        return @sqlite_exec($ASQL, $this->GetConnectionHandle()) ? true : false;
    }

    public function ExecScalarSQL($ASQL)
    {
        $queryHandle = @sqlite_query($ASQL, $this->GetConnectionHandle());
        $queryResult = @sqlite_fetch_array($queryHandle, SQLITE_NUM);
        return $queryResult[0];
    }

    public function SupportsLastInsertId()
    {
        return false;
    }

    public function GetLastInsertId()
    {
        return @sqlite_last_insert_rowid($this->GetConnectionHandle());
    }

    public function DoLastError()
    {
        if ($this->connectionHandle)
            return sqlite_error_string(sqlite_last_error($this->connectionHandle));
        else
            return $this->connectionError;
    }
}

class SqliteDataReader extends EngDataReader
{
    private $queryResult;
    private $lastFetchedRow;

    protected function FetchField()
    {
        echo "not supprted";
    }

    protected function FetchFields()
    {
        for($i = 0; $i < sqlite_num_fields($this->queryResult); $i++)
            $this->AddField(sqlite_field_name($this->queryResult, $i));
    }

    protected function DoOpen()
    {
        $this->queryResult = @sqlite_query($this->GetSQL(), $this->GetConnection()->GetConnectionHandle(), SQLITE_ASSOC);
        if ($this->queryResult)
            return true;
        else
            return false; 
    }

    public function __construct($connection, $sql)
    {
        parent::__construct($connection, $sql);
        $this->queryResult = null;
    }

    public function Opened()
    {
        return $this->queryResult ? true : false;
    }

    public function Seek($ARowIndex)
    {
        sqlite_seek($this->queryResult, $ARowIndex);
    }

    public function Next()
    {
        $this->lastFetchedRow = sqlite_fetch_array($this->queryResult, SQLITE_ASSOC);
        return $this->lastFetchedRow ? true : false;
    }

    public function GetFieldValueByName($AFieldName)
    {
        return $this->GetActualFieldValue($AFieldName, $this->lastFetchedRow[$AFieldName]);
    }
}

class SQLitePDOCommandImp extends SqliteBaseCommandImp
{
    public function GetFieldValueAsSQL($fieldInfo, $value)
    {
        if ($fieldInfo->FieldType == ftBlob)
        {
            if (is_array($value))
            {
                return 'x\'' . bin2hex(file_get_contents($value[0])) . '\'';
            }
            else
            {
                return 'x\'' . bin2hex($value[0]) . '\'';
            }
        }
        else
            return parent::GetFieldValueAsSQL($fieldInfo, $value);
    }
}

class SqlitePDOConnectionFactory extends ConnectionFactory
{
    public function DoCreateConnection($AConnectionParams)
    {
        return new SqlitePDOConnection($AConnectionParams);
    }

    public function CreateDataset($AConnection, $ASQL)
    {
        return new SqlitePDODataReader($AConnection, $ASQL);
    }

    public function CreateEngCommandImp()
    {
        return new SQLitePDOCommandImp($this);
    }
}

class SqlitePDOConnection extends EngConnection
{
    private $connection;
    private $connectionError = '';

    public function IsDriverSupported()
    {
        return true;
    }

    protected function DoGetDBMSName() {
        return 'SQLite';
    }

    protected function DoGetDriverExtensionName() {
        return 'pdo_sqlite';
    }

    protected function DoGetDriverInstallationLink() {
        return 'http://www.php.net/manual/en/ref.pdo-sqlite.php';
    }

    protected function DoConnect()
    {
        try
        {
            $this->connection = new PDO('sqlite:' . $this->ConnectionParam('database'));
            return true;
        }
        catch (PDOException $e)
        {
            $this->connectionError = $e->getMessage();
            return false;
        }
    }

    protected function DoCreateDataReader($sql)
    {
        return new SqlitePDODataReader($this, $sql);
    }

    protected function DoDisconnect()
    { }

    public function GetConnectionHandle()
    {
        return $this->connection;
    }

    protected function DoExecSQL($ASQL)
    {
        return !($this->connection->exec($ASQL) === false);
    }

    public function SupportsLastInsertId()
    {
        return true;
    }

    public function GetLastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function ExecScalarSQL($ASQL)
    {
        $queryHandle = $this->connection->query($ASQL);
        if ($queryHandle)
        {
            $row = $queryHandle->fetch(PDO::FETCH_NUM);
            if ($row === false)
            {
                return false;
            }
            else
            {
                return $row[0];
            }
        }
        else
            return false;
    }

    public function DoLastError()
    {
        if ($this->connection)
        {
            $pdoErrorInfo = $this->connection->errorInfo();
            return $pdoErrorInfo[2];
        }
        else
        {
            return $this->connectionError;
        }
    }
}

class SqlitePDODataReader extends EngDataReader
{
    private $statement;
    private $lastFetchedRow;

    protected function FetchField()
    {
        echo "not supprted";
    }

    protected function FetchFields()
    {
        for($i = 0; $i < $this->statement->columnCount(); $i++)
        {
            $columnMetadata = $this->statement->getColumnMeta($i);
            $this->AddField($columnMetadata['name']);
        }
    }
    
    protected function DoOpen()
    {
        try
        {
            $this->statement = $this->GetConnection()->GetConnectionHandle()->query($this->GetSQL());
            if (!$this->statement)
                return false;
            return true;
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

	protected function DoClose()
	{ 
	   $this->statement->closeCursor();
	}    

    public function __construct($connection, $sql)
    {
        parent::__construct($connection, $sql);
        $this->statement = null;
    }

    public function Opened()
    {
        return $this->statement ? true : false;
    }

    public function Seek($rowIndex)
    { }

    public function Next()
    {
        try
        {
            $this->lastFetchedRow = $this->statement->fetch();
            if($this->lastFetchedRow)
                return true;
            else
                return false;
        }
        catch(PDOException $e)
        {
            return false;
        }
    }

    public function GetFieldValueByName($fieldName)
    {
        return $this->GetActualFieldValue($fieldName, $this->lastFetchedRow[$fieldName]);
    }
}

?>