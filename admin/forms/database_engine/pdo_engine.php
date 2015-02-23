<?php

include_once("engine.php");

abstract class PDOConnection extends EngConnection
{
    private $connection;
    private $connectionError = '';

    protected abstract function CreatePDOConnection();

    protected abstract function DoAfterConnect();

    protected function DoConnect()
    {
        try
        {
            $this->connection = $this->CreatePDOConnection();
            $this->DoAfterConnect();
            return true;
        }
        catch (PDOException $e)
        {
            $this->connectionError = $e->getMessage();
            return false;
        }
    }

    protected function DoDisconnect()
    { }

    protected function DoCreateDataReader($sql)
    {
        return new PDODataReader($this, $sql);
    }

    public function GetConnectionHandle()
    {
        return $this->connection;
    }

    protected function DoExecSQL($sql)
    {
        return !($this->connection->exec($sql) === false);
    }

    public function GetLastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function ExecScalarSQL($sql)
    {
        $queryHandle = $this->connection->query($sql);
        if (!$queryHandle)
            return false;
        $row = $queryHandle->fetch(PDO::FETCH_NUM);
        if (!$row)
        {
            return false;
        }
        else
        {
            return $row[0];
        }
    }

	public function ExecQueryToArray($sql, &$array)
	{
        $queryHandle = $this->connection->query($sql);
        if (!$queryHandle)
            return;
        while ($row = $queryHandle->fetch(PDO::FETCH_ASSOC))
            $array[] = $row;
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

    public function SupportsLastInsertId()
    {
        return true;
    }
}

class PDODataReader extends EngDataReader
{
    # private

    private $statement;
    private $lastFetchedRow;
    private $nativeTypes;
    private $lastException;

    # protected

    function GetColumnNativeType($fieldName)
    {
        if (isset($this->nativeTypes[$fieldName]))
            return $this->nativeTypes[$fieldName];
        else
            return '';
    }

    function FetchField() { echo "not supprted"; }

    function FetchFields()
    {
        for($i = 0; $i < $this->statement->columnCount(); $i++)
        {
            $columnMetadata = $this->statement->getColumnMeta($i);
            $this->AddField($columnMetadata['name']);
            if (isset($columnMetadata['native_type']))
                $this->nativeTypes[$columnMetadata['name']] = $columnMetadata['native_type'];
        }
    }

    function DoOpen()
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
            $this->lastException = $e;
            return false;
        }
    }

    # public
    function __construct($connection, $sql)
    {
        parent::__construct($connection, $sql);
        $this->statement = null;
        $this->nativeTypes = array();
    }

    function Opened()
    {
        return $this->statement ? true : false;
    }

    function Seek($rowIndex)
    { }

    function Next()
    {
        try
        {
            $this->lastFetchedRow = $this->statement->fetch();
            if($this->lastFetchedRow)
            {
                $this->TransformFetchedValues();
                return true;
            }
            else
                return false;
        }
        catch(PDOException $e)
        {
            $this->lastException = $e;
            return false;
        }
    }

    function DoTransformFetchedValue($fieldName, &$fetchedValue)
    {
        return $fetchedValue;
    }

    function TransformFetchedValues()
    {
        for($i = 0; $i < $this->FieldCount(); $i++)
            $this->lastFetchedRow[$this->GetField($i)] =
                $this->DoTransformFetchedValue($this->GetField($i), $this->lastFetchedRow[$this->GetField($i)]);
    }

    function GetFieldValueByName($fieldName)
    {
        return $this->GetActualFieldValue($fieldName, $this->lastFetchedRow[$fieldName]);
    }

    protected function LastError()
    {
        if (isset($this->lastException))
        {
            return $this->lastException->getMessage();
        }
        else
            return parent::LastError();
    }    

    function CursorPosition(){}
    function Prev(){}
    function First(){}
    function Last(){}
}
