<?php

class AdminRecordPermissions
{
    public function CanAllUsersViewRecords()
    {
        return true;
    }    
    
    public function HasEditGrant($dataset) 
    {
        return true;
    }
    
    public function HasViewGrant($dataset) 
    { 
        return true;
    }
    
    public function HasDeleteGrant($dataset) 
    { 
        return true;
    }

    public function AdminGrant()
    {
        return true;
    }
}

class DataSourceRecordPermission
{
    private $canAllView, $canAllDelete, $canAllEdit;
    private $canOwnerView, $canOwnerDelete, $canOwnerEdit;
    private $ownerIdField;
    
    public function __construct($ownerIdField, $canAllView, $canAllDelete, $canAllEdit, 
        $canOwnerView, $canOwnerDelete, $canOwnerEdit)
    {
        $this->ownerIdField = $ownerIdField;
        $this->canAllView = $canAllView;
        $this->canAllDelete = $canAllDelete;
        $this->canAllEdit = $canAllEdit;
        $this->canOwnerView = $canOwnerView;
        $this->canOwnerDelete = $canOwnerDelete;
        $this->canOwnerEdit = $canOwnerEdit;
    }
    
    public function CanAllUsersViewRecords()
    {
        return $this->canAllView;
    }
    
    public function HasEditGrant($dataset, $userId) 
    {
        $ownerId = $dataset->GetFieldValueByName($this->ownerIdField);
        return $ownerId == $userId ? $this->canOwnerEdit : $this->canAllEdit;
    }
    
    public function HasViewGrant($dataset, $userId) 
    { 
        $ownerId = $dataset->GetFieldValueByName($this->ownerIdField);
        return $ownerId == $userId ? $this->canOwnerView : $this->canAllView;
    }
    
    public function HasDeleteGrant($dataset, $userId) 
    { 
        $ownerId = $dataset->GetFieldValueByName($this->ownerIdField);
        return $ownerId == $userId ? $this->canOwnerDelete : $this->canAllDelete;
    }

    public function AdminGrant()
    {
        return false;
    }
}

class UserDataSourceRecordPermission
{
    private $userId;
    private $dataSourceRecordPermission;
    
    public function __construct($userId, $dataSourceRecordPermission)
    {
        $this->userId = $userId;
        $this->dataSourceRecordPermission = $dataSourceRecordPermission;
    }

    public function CanAllUsersViewRecords()
    {
        return $this->dataSourceRecordPermission->CanAllUsersViewRecords();
    }    
    
    public function HasEditGrant($dataset) 
    {
        return $this->dataSourceRecordPermission->HasEditGrant($dataset, $this->userId);
    }
    
    public function HasViewGrant($dataset) 
    { 
        return $this->dataSourceRecordPermission->HasViewGrant($dataset, $this->userId);
    }
    
    public function HasDeleteGrant($dataset) 
    { 
        return $this->dataSourceRecordPermission->HasDeleteGrant($dataset, $this->userId);
    }        
}

class NullUserDataSourceRecordPermission
{
    public function HasEditGrant($dataset) 
    {
        return true;
    }
    
    public function HasViewGrant($dataset) 
    { 
        return true;
    }
    
    public function HasDeleteGrant($dataset) 
    { 
        return true;
    }         
}

class HardCodedDataSourceRecordPermissionRetrieveStrategy 
{
    private $dataSourceRecordPermissions;
    
    public function __construct($dataSourceRecordPermissions)
    {
        $this->dataSourceRecordPermissions = $dataSourceRecordPermissions;
    }
    
    public function GetUserRecordPermissionsForDataSource($dataSourceName, $userId)     
    {
        if (isset($this->dataSourceRecordPermissions[$dataSourceName]))
            return new UserDataSourceRecordPermission($userId, $this->dataSourceRecordPermissions[$dataSourceName]);
        else
            return null;
    }
}

class NullDataSourceRecordPermissionRetrieveStrategy 
{
    public function GetUserRecordPermissionsForDataSource($dataSourceName, $userId)     
    {
        return new NullUserDataSourceRecordPermission();
    }
}

?>