<?php

require_once 'page.php';
require_once 'common.php';
require_once 'superglobal_wrapper.php';
require_once 'security/base_user_auth.php';
require_once 'security/record_level_permissions.php';

require_once 'utils/array_utils.php';
require_once 'renderers/list_renderer.php';


class Application implements IVariableContainer
{
    /** @var Page */
    private $mainPage;

    /** @var HTTPHandler[] */
    private $httpHandlers;

    /** @var \SuperGlobals */
    private $superGlobals;
    
    private $userAuthorizationStrategy;
    private $dataSourceRecordPermissionRetrieveStrategy;
    
    #region IVariableContainer implementation
    private $variableFuncs = array(
        'CURRENT_USER_ID'   => 'return $app->IsCurrentUserLoggedIn() ? $app->GetCurrentUserId() : \'\';',
        'CURRENT_USER_NAME' => 'return $app->IsCurrentUserLoggedIn() ? $app->GetCurrentUser() : \'\';'
        );

    public function FillVariablesValues(&$values)
    {
        $values = array();
        foreach($this->variableFuncs as $name => $code)
        {
            $function = create_function('$app', $code);
            $values[$name] = $function($this);
        }
    }

    public function FillAvailableVariables(&$variables)
    {
        return array_keys($this->variableFuncs);
    }    
    #endregion

    public function __construct()
    {
        session_start();
        $this->httpHandlers = array();
        $this->superGlobals = new SuperGlobals();
        //
        $this->userAuthorizationStrategy = new NullUserAuthorization();
        $this->dataSourceRecordPermissionRetrieveStrategy = new NullDataSourceRecordPermissionRetrieveStrategy();
    }

    #region SuperGlobals delegates

    public function HasPostGetRequestParameters()
    {
        if (count($_POST) == 0 && count($_GET) == 0)
        {
            return false;
        }
        elseif (count($_POST) == 0 && (count($_GET) == 1 && isset($_GET['hname'])))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function GetSuperGlobals()
    {
        return $this->superGlobals;
    }

    public function RefineInputValue($value)
    {
        return $this->superGlobals->RefineInputValue($value);
    }

    public function IsPOSTValueSet($name)
    {
        return $this->superGlobals->IsPostValueSet($name);
    }

    public function GetPOSTValue($name)
    {
        return $this->superGlobals->GetPostValue($name);
    }

    public function IsGETValueSet($name)
    {
        return $this->superGlobals->IsGetValueSet($name);
    }

    public function GetGETValue($name)
    {
        return $this->superGlobals->GetGetValue($name);
    }

    public function IsSessionVariableSet($name)
    {
        return $this->GetSuperGlobals()->IsSessionVariableSet($name);
    }

    public function SetSessionVariable($name, $value)
    {
        $this->GetSuperGlobals()->SetSessionVariable($name, $value);
    }

    public function GetSessionVariable($name)
    {
        return $this->GetSuperGlobals()->GetSessionVariable($name);
    }

    public function UnSetSessionVariable($name)
    {
        $this->GetSuperGlobals()->UnSetSessionVariable($name);
    }

    #endregion
    
    private function IsHTTPHandlerProcessingRequested()
    {
        return $this->GetSuperGlobals()->IsGetValueSet('hname');
    }

    private function GetRequestedHTTPHandlerName()
    {
        return $this->GetSuperGlobals()->GetGetValue('hname');
    }

    public function Run()
    {
        if ($this->IsHTTPHandlerProcessingRequested())
        {
            $this->ProcessHTTPHandlers();
        }
        else
        {
            $this->mainPage->BeginRender();
            $this->mainPage->EndRender();
        }
    }

    public function SetMainPage(Page $page)
    {
        $this->mainPage = $page;
    }

    public function RegisterHTTPHandler(HTTPHandler $httpHandler)
    {
        $this->httpHandlers[] = $httpHandler;
    }

    /**
     * @param string $name
     * @return HTTPHandler
     */
    public function GetHTTPHandlerByName($name)
    {
        return ArrayUtils::Find(
            $this->httpHandlers,
            create_function('$handler', "return \$handler->GetName() == '$name';")
            );
    }

    public function ProcessHTTPHandlers()
    {
        $renderer = new ViewAllRenderer($this->mainPage->GetLocalizerCaptions());
        $HTTPHandler = $this->GetHTTPHandlerByName($this->GetRequestedHTTPHandlerName());
        if (isset($HTTPHandler))
        {
            echo $HTTPHandler->Render($renderer);
        }
    }

    #region Security delegates

    public function GetUserAuthorizationStrategy()
    {
        return $this->userAuthorizationStrategy;
    }

    public function SetUserAuthorizationStrategy($userAuthorizationStrategy)
    {
        $this->userAuthorizationStrategy = $userAuthorizationStrategy;
    }

    public function GetCurrentUser()
    {
        return $this->GetUserAuthorizationStrategy()->GetCurrentUser();
    }

    public function HasAdminGrantForCurrentUser()
    {
        return $this->GetUserAuthorizationStrategy()->HasAdminGrant($this->GetCurrentUser());
    }

    public function GetCurrentUserId()
    {
        return $this->GetUserAuthorizationStrategy()->GetCurrentUserId();
    }

    public function IsCurrentUserLoggedIn()
    {
        return $this->GetUserAuthorizationStrategy()->IsCurrentUserLoggedIn();
    }

    public function GetUserRoles($userName, $dataSourceName)
    {
        return $this->GetUserAuthorizationStrategy()->GetUserRoles($userName, $dataSourceName);
    }

    /**
     * GetCurrentUserGrants
     *
     * @return DataSourceSecurityInfo security info for specified datasource and current user
     */
    public function GetCurrentUserGrants($dataSourceName)
    {
        $currentUser = $this->GetCurrentUser();
        return $this->GetUserRoles($currentUser, $dataSourceName);
    }
    
    #endregion

    #region Record level security delegates

    public function SetDataSourceRecordPermissionRetrieveStrategy($value)
    { 
        $this->dataSourceRecordPermissionRetrieveStrategy = $value; 
    }

    public function GetDataSourceRecordPermissionRetrieveStrategy()
    { 
        return $this->dataSourceRecordPermissionRetrieveStrategy; 
    }

    public function GetCurrentUserRecordPermissionsForDataSource($dataSourceName)
    {
        if ($this->GetCurrentUserGrants($dataSourceName)->AdminGrant())
            return new AdminRecordPermissions();
        else
            return $this->GetUserRecordPermissionsForDataSource($dataSourceName, $this->GetCurrentUserId());
    }

    public function GetUserRecordPermissionsForDataSource($dataSourceName, $userId)
    {
        return $this->GetDataSourceRecordPermissionRetrieveStrategy()->
        GetUserRecordPermissionsForDataSource($dataSourceName, $userId);
    }

    #endregion 

    private $settedOperation = null;

    function SetOperation($value)
    {
        $this->settedOperation = $value;
    }

    function GetOperation()
    {
        if (isset($this->settedOperation))
            return $this->settedOperation;
        else
        {
            if(isset($_GET[OPERATION_PARAMNAME]))
            {
                return $_GET[OPERATION_PARAMNAME];
            }
            else if (isset($_POST[OPERATION_PARAMNAME]))
            {
                return $_POST[OPERATION_PARAMNAME];
            }
            else
            {
                return OPERATION_VIEWALL;
            }
        }
    }
}

?>