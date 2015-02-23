<?php
define('NS_LIST', 1);
define('NS_COMBOBOX', 2);

class PageNavigatorPage
{
    private $isCurrent;
    private $caption;
    private $page;
    private $pageNumber;
    private $prefix;
    private $linkBuilder;
    private $hint;
    private $renderText;
    private $shortCut = null;

    function __construct($page, $caption, $pageNumber, $isCurrent, $linkBuilder, $prefix = '', $hint = '', $renderText=true)
    {
        $this->page = $page;
        $this->caption = $caption;
        $this->isCurrent = $isCurrent;
        $this->pageNumber = $pageNumber;
        $this->prefix = $prefix;
        $this->linkBuilder = $linkBuilder;
        $this->hint = $hint;
        $this->renderText = $renderText;
    }

    public function HasShortCut()
    {
        return isset($this->shortCut) && !is_null($this->shortCut);
    }

    public function GetShortCut()
    {
        return $this->shortCut;
    }

    public function SetShortCut($value)
    {
        $this->shortCut = $value;
    }

    function GetHint() { return $this->renderText ? $this->page->RenderText($this->hint) : $this->hint; }
    function GetPage() { return $this->page; }
    function IsCurrent() { return $this->isCurrent; }
    function GetPageCaption() { return $this->renderText ? $this->page->RenderText($this->caption) : $this->caption; }

    function GetPageLink()
    {
        $result = $this->linkBuilder;
        if (isset($this->pageNumber))
            $result->AddParameter($this->prefix . 'page', $this->pageNumber);
        else
            $result->RemoveParameter($this->prefix . 'page');
        return $result->GetLink();
    }
}

class BasePageNavigator
{
    function __construct()
    { }

    function AddCurrentPageParameters(&$linkBuilder)
    { }

    function ProcessMessages()
    { }

    function Accept($Renderer)
    { }
}

class AbstractPageNavigator
{
    private $page;
    private $dataset;
    private $name;
    private $pages;
    private $prefix;
    private $caption;
    private $pagaNavigatorList;

    private $currentPageNumber;
    private $ignorePageNavigationOperations = array(
        OPERATION_PRINT_ALL
        //, OPERATION_EXCEL_EXPORT
        //, OPERATION_WORD_EXPORT
        //, OPERATION_XML_EXPORT
        //, OPERATION_CSV_EXPORT
        //, OPERATION_PDF_EXPORT
    );

    function __construct($name, $page, $dataset, $caption, $pagaNavigatorList, $prefix = null)
    {
        $this->name = $name;
        $this->page = $page;
        $this->dataset = $dataset;
        $this->pages = array();
        $this->prefix = isset($prefix) ? $prefix : $name;
        $this->caption = $caption;
        $this->pages = null;
        $this->pagaNavigatorList = $pagaNavigatorList;
    }

    function GetName() { return $this->name; }
    function GetPagaNavigatorList() { return $this->pagaNavigatorList; }
    function GetPage() { return $this->page; }
    function GetCaption() { return $this->caption; }
    function CurrentPageNumber() { return $this->currentPageNumber; }
    function GetPrefix() { return $this->prefix; }

    function GetPages()
    {
        assert(isset($this->pages));
        return $this->pages;
    }

    function ApplyPageToDataset($currentPageNumber, $dataset)
    { }

    function FillPages(&$pages, $currentPage, $linkBuilder)
    { }

    function HasSetPageRequest()
    {
        return GetApplication()->IsGETValueSet($this->prefix . 'page');
    }

    function GetPageFromRequest()
    {
        return GetApplication()->GetGETValue($this->prefix . 'page');
    }

    public function NeedResetPage()
    {
        return false; //(!GetApplication()->HasPostGetRequestParameters());
    }

    function SessionContainsStoredPage()
    {
        return GetApplication()->IsSessionVariableSet($this->prefix . 'page');
    }

    function StorePageToSession()
    {
        GetApplication()->SetSessionVariable($this->prefix . 'page', $this->currentPageNumber);
    }

    function RestorePageFromSession()
    {
        $this->currentPageNumber = GetApplication()->GetSessionVariable($this->prefix . 'page');
    }

    function ResetPageNumber()
    {
        GetApplication()->UnSetSessionVariable($this->prefix . 'page');
        $this->currentPageNumber = null;
    }

    function ProcessMessages()
    {
        if ($this->HasSetPageRequest() && !$this->NeedResetPage())
        {
            $this->currentPageNumber = $this->GetPageFromRequest();
            $this->StorePageToSession();
        }
        elseif (!$this->NeedResetPage() && $this->SessionContainsStoredPage())
        {
            $this->RestorePageFromSession();
        }
        else
        {
            $this->ResetPageNumber();
        }

        if (!in_array(GetOperation(), $this->ignorePageNavigationOperations))
        {
            $this->ApplyPageToDataset($this->currentPageNumber, $this->dataset);
        }
    }

    function BuildPages($linkBuilder)
    {
        $this->pages = array();
        $this->FillPages($this->pages, $this->currentPageNumber, $linkBuilder);
    }

    function AddCurrentPageParameters(&$linkBuilder)
    {
        $linkBuilder->AddParameter($this->prefix . 'page', $this->CurrentPageNumber());
    }

    function Accept($Renderer)
    {
        $Renderer->RenderCustomPageNavigator($this);
    }
}

class PageNavigator
{
    private $name;
    private $dataset;
    private $rowsPerPage;
    private $pageNumber;
    private $pages;
    private $page;
    private $rowCount = null;
    private $pageCount = null;
    private $recordsPerPageValues;
    private $previosPageLink;
    private $nextPageLink;
    
    private $ignorePageNavigationOperations = array(OPERATION_PRINT_ALL, OPERATION_EXCEL_EXPORT, OPERATION_WORD_EXPORT, OPERATION_XML_EXPORT, OPERATION_CSV_EXPORT, OPERATION_PDF_EXPORT);

    function __construct($name, $page, $Dataset, $defaultRowPerPage = 20, $recordsPerPageValues = null)
    {
        $this->name = $name;
        $this->page = $page;
        $this->dataset = $Dataset;
        $this->pageNumber = 0;
        if ($recordsPerPageValues == null)
            $this->recordsPerPageValues = array(10,20,50,100,0);
        else
            $this->recordsPerPageValues = $recordsPerPageValues;
        $this->previosPageLink = null;
        $this->nextPageLink = null;
    }
    
    
    function GetRecordsPerPageValues()
    {
        $result = array();         
        foreach($this->recordsPerPageValues as $value)
            $result[$value] = $value == 0 ? 'ALL' : $value;
        return $result;
    }

    public function GetRowsPerPage() { return $this->rowsPerPage; }
    
    function SetRowsPerPage($RowsPerPage)
    {
        $this->rowsPerPage = $RowsPerPage;
        $this->defaultRowPerPage = $RowsPerPage;
    }

    private function NeedResetPage()
    {
        $result = false; //(!GetApplication()->HasPostGetRequestParameters());
        return $result; 
    }

    function ResetPageNumber()
    {
        GetApplication()->UnSetSessionVariable($this->GetSessionPrefix() . 'page');
        $this->pageNumber = 0;
        
        //GetApplication()->UnSetSessionVariable('recperpage');
        //$this->rowsPerPage = $this->defaultRowPerPage;
    }

    private function GetSessionPrefix() {

        return $this->page->GetGrid()->GetId();
    }

    function ProcessMessages()
    {
        if (GetApplication()->IsGETValueSet('page'))
        {
            $this->pageNumber = GetApplication()->GetGETValue('page') - 1;
            GetApplication()->SetSessionVariable($this->GetSessionPrefix() . 'page', $this->pageNumber);
        }
        elseif (!$this->NeedResetPage() && GetApplication()->IsSessionVariableSet($this->GetSessionPrefix() . 'page'))
        {
            $this->pageNumber = GetApplication()->GetSessionVariable($this->GetSessionPrefix() . 'page');
        }
        else
        {
            $this->ResetPageNumber();
        }
        
        if (GetApplication()->IsGETValueSet('recperpage'))
        {
            $this->rowsPerPage = GetApplication()->GetGETValue('recperpage');
            GetApplication()->SetSessionVariable($this->GetSessionPrefix() . 'recperpage', $this->rowsPerPage);
        }
        elseif (GetApplication()->IsSessionVariableSet($this->GetSessionPrefix() . 'recperpage'))
        {
            $this->rowsPerPage = GetApplication()->GetSessionVariable($this->GetSessionPrefix() . 'recperpage');
        }
        else 
            $this->rowsPerPage = $this->defaultRowPerPage;

        if ($this->pageNumber >= $this->GetPageCount())
            $this->pageNumber = $this->GetPageCount() - 1;
        elseif($this->pageNumber < 0)
            $this->pageNumber = 0;
        
        if (!in_array(GetOperation(), $this->ignorePageNavigationOperations))
        {
            if (($this->rowsPerPage != 0) && ($this->GetRowCount() != 0))
            {
                $this->dataset->SetUpLimit($this->pageNumber * $this->rowsPerPage);
                $this->dataset->SetLimit($this->rowsPerPage);
            }
        }
    }

    public function GetPageCount()
    {
        if (!isset($this->pageCount))
        {
            if ($this->rowsPerPage != 0)
            {
                $this->pageCount = floor($this->GetRowCount() / $this->rowsPerPage) +
                    ((floor($this->GetRowCount() / $this->rowsPerPage) == ($this->GetRowCount() / $this->rowsPerPage))? 0 : 1);
            }
            else
                $this->pageCount = 1;   
        }
        return $this->pageCount;
    }

    public function CurrentPageNumber()
    {
        return $this->pageNumber + 1;
    }

    public function GetRowCount() 
    { 
        if (!isset($this->rowCount))
            $this->rowCount = $this->RetrieveRowCount();
        return $this->rowCount; 
    }

    protected function RetrieveRowCount()
    {
        return $this->dataset->GetTotalRowCount();
    }

    function GetHintForPage($number, $shortCut = null)
    {
        $page = $number - 1;
        $rowCount = $this->rowCount;
        $rowsPerPage = $this->rowsPerPage;
        
        $startRecord = $page * $rowsPerPage + 1;
        $endRecord = min(array(($page + 1) * $rowsPerPage, $rowCount));
        
        $result = sprintf($this->page->GetLocalizerCaptions()->GetMessageString('RecordsMtoKFromN'), 
            $startRecord, $endRecord, $rowCount);
        if (isset($shortCut))
            $result .= ";\n" . $shortCut;
        return $result;
    }
    
    function GetPageCountForPageSize($pageSize)
    {
        if ($pageSize != 0)
        {
            return floor($this->GetRowCount() / $pageSize) +
                ((floor($this->GetRowCount() / $pageSize) == ($this->GetRowCount() / $pageSize))? 0 : 1);
        }
        else
            return 1;   
    }

    function CreateNavigatorPages($currentPage, $pageCount, $linkBuilder)
    {
        $nextPages = array();
        $prevPages = array();

        for($i = $currentPage - 1; $i > max($currentPage - 4, 0); $i--)
            $prevPages[] = new PageNavigatorPage($this->page, $i, $i, false, $linkBuilder, '', $this->GetHintForPage($i), false);

        if ($currentPage - 10 > 0)
            $prevPages[] = new PageNavigatorPage($this->page, $currentPage - 10, $currentPage - 10, false, $linkBuilder, '', $this->GetHintForPage($currentPage - 10), false);

        if ($currentPage - 50 > 0)
            $prevPages[] = new PageNavigatorPage($this->page, $currentPage - 50, $currentPage - 50, false, $linkBuilder, '', $this->GetHintForPage($currentPage - 50), false);

        if ($currentPage - 100 > 0)
            $prevPages[] = new PageNavigatorPage($this->page, $currentPage - 100, $currentPage - 100, false, $linkBuilder, '', $this->GetHintForPage($currentPage - 100), false);

        if ($currentPage > 1)
        {
            $linkBuilder->AddParameter('page', $currentPage - 1);
            $this->previosPageLink = $linkBuilder->GetLink();
            $prevPageLink = new PageNavigatorPage($this->page, '<', $currentPage - 1, false, $linkBuilder, '', $this->GetHintForPage($currentPage - 1, 'Ctrl + left'), false);
            $prevPageLink->SetShortCut('Ctrl+left');
            $prevPages[] = $prevPageLink;
        }

        if ($currentPage - 3 > 1)
            $prevPages[] = new PageNavigatorPage($this->page, '<< '.$this->page->GetLocalizerCaptions()->GetMessageString('First'), 1, false, $linkBuilder, '', $this->GetHintForPage(1), false);


        for($i = $currentPage + 1; $i < min($currentPage + 4, $pageCount + 1); $i++)
            $nextPages[] = new PageNavigatorPage($this->page, $i, $i, false, $linkBuilder, '', $this->GetHintForPage($i), false);

        if ($currentPage + 10 < $pageCount)
            $nextPages[] = new PageNavigatorPage($this->page, $currentPage + 10, $currentPage + 10, false, $linkBuilder, '', $this->GetHintForPage($currentPage + 10), false);

        if ($currentPage + 50 < $pageCount)
            $nextPages[] = new PageNavigatorPage($this->page, $currentPage + 50, $currentPage + 50, false, $linkBuilder, '', $this->GetHintForPage($currentPage + 50), false);

        if ($currentPage + 100 < $pageCount)
            $nextPages[] = new PageNavigatorPage($this->page, $currentPage + 100, $currentPage + 100, false, $linkBuilder, '', $this->GetHintForPage($currentPage + 100), false);

        if ($currentPage < $pageCount)
        {
            $linkBuilder->AddParameter('page', $currentPage + 1);
            $this->nextPageLink = $linkBuilder->GetLink();
            $nextPageLink = new PageNavigatorPage($this->page, '>', $currentPage + 1, false, $linkBuilder, '', $this->GetHintForPage($currentPage + 1, 'Ctrl + right'), false);
            $nextPageLink->SetShortCut('Ctrl+right');
            $nextPages[] = $nextPageLink;
        }
        if ($currentPage + 3 < $pageCount)
            $nextPages[] = new PageNavigatorPage($this->page, $this->page->GetLocalizerCaptions()->GetMessageString('Last') . ' >>', $pageCount, false, $linkBuilder, '', $this->GetHintForPage($pageCount), false);

        $result = array();
        for($i = (count($prevPages) - 1); $i >= 0; $i--)
            $result[] = $prevPages[$i];
        $result[] = new PageNavigatorPage($this->page, $currentPage, $currentPage, true, $linkBuilder, '', $this->GetHintForPage($currentPage), false);
        for($i = 0; $i < count($nextPages); $i++)
            $result[] = $nextPages[$i];

        return $result;
    }

    function HasPreviosPage()
    {
        return isset($this->previosPageLink);
    }
    
    function HasNextPage()
    {
        return isset($this->nextPageLink);
    }
    
    function NextPageLink()
    {
        return $this->nextPageLink;
    }
    
    function PreviosPageLink()
    {
        return $this->previosPageLink;
    }
    
    function BuildPages($linkBuilder)
    {
        $this->pages = array();
        $this->FillPages($linkBuilder);
    }

    private function FillPages($linkBuilder)
    {
        $this->pages = $this->CreateNavigatorPages($this->CurrentPageNumber(), $this->GetPageCount(), $linkBuilder);
    }
    
    public function GetCurrentPageGetParameters()
    {
        $result = $this->page->CreateLinkBuilder();
        return $result->GetParameters();
    }
    
    function GetPages()
    {
        assert(isset($this->pages));
        return $this->pages;
    }

    function AddCurrentPageParameters(&$linkBuilder)
    {
        $linkBuilder->AddParameter('page', $this->CurrentPageNumber());
    }

    function Accept($Renderer)
    {
        $Renderer->RenderPageNavigator($this);
    }
}

class CustomPageNavigator extends AbstractPageNavigator
{
    private $userPartitions;
    public $OnGetPartitions;
    public $OnGetPartitionCondition;
    private $allowViewAllRecords;
    private $navigationStyle;

    function __construct($name, $page, $dataset, $caption, $pagaNavigatorList, $prefix = null)
    {
        parent::__construct($name, $page, $dataset, $caption, $prefix);
        $this->OnGetPartitions = new Event();
        $this->OnGetPartitionCondition = new Event();
        $this->userPartitions = null;
        $this->allowViewAllRecords = false;
        $this->navigationStyle = NS_LIST;
    }

    function GetPageFromRequest()
    {
        if (parent::HasSetPageRequest())
            return parent::GetPageFromRequest();
        else
            return null;
    }

    function DoOnGetPartitions()
    {
        $result = array();
        $this->OnGetPartitions->Fire(array(&$result));
        return $result;
    }

    function DoOnGetPartitionCondition($currentPageNumber)
    {
        $condition = '';
        $this->OnGetPartitionCondition->Fire(array($currentPageNumber, &$condition));
        return $condition;
    }

    function FillUserPartitions()
    {
        if (!isset($this->userPartitions))
            $this->userPartitions = $this->DoOnGetPartitions();
    }

    public function NeedResetPage()
    {
        $result =
            (GetApplication()->IsGETValueSet($this->GetPrefix() . 'page')) &&
            (GetApplication()->GetGETValue($this->GetPrefix() . 'page')) == 'reset';
        return $result;
    }

    function FillPages(&$pages, $currentPage, $linkBuilder)
    {
        $this->FillUserPartitions();
        if (!isset($currentPage) || $currentPage == '')
        {
            $userPartitionsKeys = array_keys($this->userPartitions);            
            if ($this->GetAllowViewAllRecords())
                $currentPage = null;
            else
                $currentPage = $userPartitionsKeys[0];
        }
        if ($this->GetAllowViewAllRecords())
            $pages[] = new PageNavigatorPage(
                $this->GetPage(),
                $this->GetPage()->GetLocalizerCaptions()->GetMessageString('All'),
                'reset',
                $currentPage == null,
                $linkBuilder,
                $this->GetPrefix(), '',
                false);
        foreach($this->userPartitions as $partitionName => $partitionCaption)
            $pages[] = new PageNavigatorPage($this->GetPage(), $partitionCaption, $partitionName, ($currentPage != null) && $partitionName == $currentPage, $linkBuilder, $this->GetPrefix());
    }

    function ApplyPageToDataset($currentPageNumber, $dataset)
    {
        if (!isset($currentPageNumber) || $currentPageNumber == '')
        {
            $this->FillUserPartitions();
            $userPartitionsKeys = array_keys($this->userPartitions);
            if ($this->GetAllowViewAllRecords())
                $currentPageNumber = null;
            else
                $currentPageNumber = $userPartitionsKeys[0];
        }
        if (isset($currentPageNumber))
        {
            $condition = $this->DoOnGetPartitionCondition($currentPageNumber);
            if (isset($condition) && $condition != '')
                $dataset->AddCustomCondition($condition);
        }
    }

    function GetAllowViewAllRecords() { return $this->allowViewAllRecords; }
    function SetAllowViewAllRecords($value) { $this->allowViewAllRecords = $value; }
    
    function GetNavigationStyle() { return $this->navigationStyle; }
    function SetNavigationStyle($value) { $this->navigationStyle = $value; }
}

class CompositePageNavigator extends BasePageNavigator
{
    private $page;
    private $pageNavigators;

    function __construct($page)
    {
        parent::__construct();
        $this->page = $page;
        $this->pageNavigators = array();
    }

    function AddPageNavigator($pageNavigator)
    {
        $this->pageNavigators[] = $pageNavigator;
    }

    function AddCurrentPageParameters(&$linkBuilder)
    {
        foreach($this->pageNavigators as $pageNavigator)
            $pageNavigator->AddCurrentPageParameters($linkBuilder);
    }

    function CreateLinkBuilder()
    {
        return $this->page->CreateLinkBuilder();
    }

    function ProcessMessages()
    {
        foreach($this->pageNavigators as $pageNavigator)
            $pageNavigator->ProcessMessages();

        $linkBuilder = $this->CreateLinkBuilder();
        foreach($this->pageNavigators as $pageNavigator)
        {
            $pageNavigator->AddCurrentPageParameters($linkBuilder);
            $pageNavigator->BuildPages($linkBuilder->CloneLinkBuilder());
        }
    }

    function Accept($renderer)
    {
        $renderer->RenderCompositePageNavigator($this);
    }

    function GetPageNavigators() { return $this->pageNavigators; }

}
?>