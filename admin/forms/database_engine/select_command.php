<?php

require_once 'engine.php';
require_once 'commands.php';
require_once 'components/utils/string_utils.php';
require_once 'database_engine/filterable.php';

abstract class BaseSelectCommand extends EngCommand implements IFilterable {
    private $upLmit;
    private $limitCount;
    private $fieldInfos;
    private $orederByFields;
    private $joins;

    private $fieldFilters;
    private $compositeFieldFilters;
    private $customConditions;

    public function __construct(EngCommandImp $engCommandImp) {
        parent::__construct($engCommandImp);
        $this->upLmit = null;
        $this->limitCount = null;
        $this->fieldInfos = array();
        $this->orederByFields = array();
        $this->joins = array();
        $this->compositeFieldFilters = array();
        $this->customConditions = array();
        $this->fieldFilters = array();
    }

    #region Joins 

    protected final function HasJoins() {
        return count($this->joins) > 0;
    }

    protected final function GetJoinsClause() {
        $result = '';
        foreach ($this->joins as $joinInfo)
            StringUtils::AddStr($result, $this->GetCommandImp()->CreateJoinClause($joinInfo), ' ');
        return $result;
    }

    public final function AddJoin($joinKind, $table, $fieldName, $linkField, $tableAlias = null) {
        $this->joins[] = new JoinInfo(
            $joinKind, $table,
            $this->GetFieldByName($fieldName),
            $linkField,
            $tableAlias);
    }

    #endregion

    #region Ordering 

    protected final function HasOrdering() {
        return count($this->orederByFields) > 0;
    }

    protected final function GetOrderByClause() {
        if ($this->HasOrdering()) {
            $orderByField = '';
            foreach ($this->orederByFields as $fieldName => $orderType)
                StringUtils::AddStr($orderByField, $this->GetCommandImp()->GetFieldFullName($this->GetFieldByName($fieldName)) . ' ' . $orderType, ', ');
            return 'ORDER BY ' . $orderByField;
        } else
            return '';
    }

    /**
     * @param string $fieldName
     * @param string $orderType
     * @return void
     */
    public final function SetOrderBy($fieldName, $orderType) {
        $this->orederByFields[$fieldName] = $orderType;
    }

    #endregion

    #region Fields

    protected final function GetFieldListClase() {
        $result = StringUtils::EmptyStr;
        foreach ($this->GetFields() as $field)
            StringUtils::AddStr($result, $this->GetCommandImp()->GetFieldAsSQLInSelectFieldList($field), ', ');
        return $result;
    }

    protected function DoCreateFieldInfo($tableName, $fieldName, $fieldType, $alias) {
        return new FieldInfo($tableName, $fieldName, $fieldType, $alias);
    }

    /**
     * @param FieldType $fieldType
     */
    public final function AddField($tableName, $fieldName, $fieldType, $alias) {
        $this->fieldInfos[] = $this->DoCreateFieldInfo($tableName, $fieldName, $fieldType, $alias);
    }

    public final function GetFieldByName($name) {
        foreach ($this->fieldInfos as $field)
            if (isset($field->Alias) && $field->Alias != '' && $field->Alias == $name)
                return $field;
            elseif ($field->Name == $name)
                return $field;
        return null;
    }

    public final function GetFields() {
        return $this->fieldInfos;
    }

    #endregion    

    #region Record count limits

    protected function DoGetLimitClause($limitCount, $upLimit) {
        return $this->GetCommandImp()->GetLimitClause($limitCount, $upLimit);
    }

    protected final function GetLimitClause() {
        $result = '';
        $upLimit = $this->GetUpLmit();
        $limitCount = $this->GetLimitCount();
        if (isset($upLimit) && isset($limitCount)) {
            if ($limitCount <= 0)
                $limitCount = 1;
            if ($upLimit < 0)
                $upLimit = 0;
            $result = $this->DoGetLimitClause($limitCount, $upLimit);
        }
        return $result;
    }

    public final function GetUpLmit() {
        return $this->upLmit;
    }

    public final function SetUpLimit($upLimit) {
        $this->upLmit = $upLimit;
    }

    public final function GetLimitCount() {
        return $this->limitCount;
    }

    public final function SetLimitCount($limitCount) {
        $this->limitCount = $limitCount;
    }

    #endregion    

    #region Filters

    protected final function HasCondition() {
        $condition = $this->GetFieldFilterCondition();
        return !empty($condition);
    }

    protected final function GetFieldFilterCondition() {
        $result = '';

        foreach ($this->fieldFilters as $fieldName => $filters)
            foreach ($filters as $filter)
                StringUtils::AddStr($result,
                    $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition(
                        $filter, $this->GetFieldByName($fieldName)),
                    ' AND ');

        foreach ($this->compositeFieldFilters as $filter) {
            StringUtils::AddStr($result,
                '(' . $this->GetCommandImp()->GetFilterConditionGenerator()->CreateCondition($filter, null) . ')',
                ' AND ');
        }

        foreach ($this->customConditions as $condition)
            StringUtils::AddStr($result, '(' . $condition . ')', ' AND ');

        return $result;
    }

    public final function AddFieldFilter($fieldName, $fieldFilter) {
        $this->fieldFilters[$fieldName][] = $fieldFilter;
    }

    public final function RemoveFieldFilter($fieldName, $fieldFilter) {
        if (isset($this->fieldFilters[$fieldName]))
            unset($this->fieldFilters[$fieldName][array_search($fieldFilter, $this->fieldFilters[$fieldName])]);
    }

    public final function AddCompositeFieldFilter($filterLinkType, $fieldNames, $fieldFilters) {
        $compositeFilter = new CompositeFilter($filterLinkType);
        for ($i = 0; $i < count($fieldNames); $i++)
            $compositeFilter->AddFilter(
                $this->GetFieldByName($fieldNames[$i]),
                $fieldFilters[$i]);
        $this->compositeFieldFilters[] = $compositeFilter;
    }

    public final function AddCustomCondition($condition) {
        if (!StringUtils::IsNullOrEmpty($condition))
            $this->customConditions[] = $condition;
    }

    public final function ClearFieldFilters() {
        foreach ($this->fieldFilters as $fieldName => $filterArray)
            unset($this->fieldFilters[$fieldName]);
    }

    #endregion

    #region Command building result

    public abstract function GetSQL();

    public abstract function GetSelectRecordCountSQL();

    #endregion
}

class CustomSelectCommand extends BaseSelectCommand {
    private $sql;
    const CustomSelectSubqueryAlias = 'SM_SOURCE_SQL';

    public function __construct($sql, EngCommandImp $engCommandImp) {
        parent::__construct($engCommandImp);
        $this->sql = $sql;
    }

    public function DoCreateFieldInfo($tableName, $fieldName, $fieldType, $alias) {
        if (!isset($tableName) || $tableName == '')
            return parent::DoCreateFieldInfo(
                $this->GetCommandImp()->QuoteTableIndetifier(self::CustomSelectSubqueryAlias),
                $fieldName, $fieldType, $alias);
        else
            return parent::DoCreateFieldInfo($tableName, $fieldName, $fieldType, $alias);
    }

    #region Command building result

    public function GetSQL() {
        if ($this->HasCondition() || $this->HasJoins() || $this->HasOrdering()) {
            $fieldList = $this->GetFieldListClase();

            $result = 'SELECT ' . $fieldList . ' FROM (' . $this->sql . ') ' . $this->GetCommandImp()->QuoteTableIndetifier(self::CustomSelectSubqueryAlias);

            StringUtils::AddStr($result, $this->GetJoinsClause(), StringUtils::Space);
            StringUtils::AddStr($result, $this->GetFieldFilterCondition(), ' WHERE ');
            StringUtils::AddStr($result, $this->GetOrderByClause(), StringUtils::Space);
        } else
            $result = $this->sql;

        return $result;
    }

    public function GetSelectRecordCountSQL() {
        $result = 'SELECT COUNT(*) FROM (' . $this->sql . ') ' . $this->GetCommandImp()->QuoteTableIndetifier(self::CustomSelectSubqueryAlias);

        StringUtils::AddStr($result, $this->GetJoinsClause(), ' ');
        StringUtils::AddStr($result, $this->GetFieldFilterCondition(), ' WHERE ');

        return $result;
    }

    public function Execute(EngConnection $connection) {
        $this->GetCommandImp()->SetServerVersion($connection->GetServerVersion());
        return $this->GetCommandImp()->ExecuteCustomSelectCommand($connection, $this);
    }

    #endregion
}

class SelectCommand extends BaseSelectCommand {
    private $sourceTable;
    private $sourceTableAlias;

    public function __construct(EngCommandImp $engCommandImp) {
        parent::__construct($engCommandImp);
    }

    public function SetSourceTableName($sourceTable, $sourceTableAlias = null) {
        $this->sourceTable = $sourceTable;
        $this->sourceTableAlias = $sourceTableAlias;
    }

    #region Command building result

    public function GetSQL() {
        $fieldList = $this->GetFieldListClase();

        $afterSelectSql = $this->GetCommandImp()->GetAfterSelectSQL($this);
        if ($afterSelectSql != '')
            $afterSelectSql = ' ' . $afterSelectSql;

        $result = "SELECT$afterSelectSql $fieldList FROM " .
            $this->GetCommandImp()->QuoteTableIndetifier($this->sourceTable) .
            ((isset($this->sourceTableAlias) && $this->sourceTableAlias != '') ? ' ' . $this->sourceTableAlias : '');


        StringUtils::AddStr($result, $this->GetJoinsClause(), ' ');
        StringUtils::AddStr($result, $this->GetFieldFilterCondition(), ' WHERE ');
        StringUtils::AddStr($result, $this->GetOrderByClause(), ' ');
        StringUtils::AddStr($result, $this->GetLimitClause(), ' ');

        return $result;
    }

    public function GetSelectRecordCountSQL() {
        $result = 'SELECT COUNT(*) FROM ' . $this->GetCommandImp()->QuoteTableIndetifier($this->sourceTable);

        StringUtils::AddStr($result, $this->GetJoinsClause(), ' ');
        StringUtils::AddStr($result, $this->GetFieldFilterCondition(), ' WHERE ');

        return $result;
    }

    public function Execute(EngConnection $connection) {
        $this->GetCommandImp()->SetServerVersion($connection->GetServerVersion());
        return $this->GetCommandImp()->ExecuteSelectCommand($connection, $this);
    }

    #endregion
}

class FieldAggregationInfo {
    /** @var Aggregate */
    private $aggregate;

    /** @var string */
    private $alias;

    /**
     * @param Aggregate $aggregate
     * @param string $alias
     */
    public function __construct(Aggregate $aggregate, $alias = '') {
        $this->aggregate = $aggregate;
        $this->alias = $alias;
    }

    /**
     * @return Aggregate
     */
    public function GetAggregate() {
        return $this->aggregate;
    }

    public function GetAlias() {
        return $this->alias;
    }

    public function HasAlias() {
        return !StringUtils::IsNullOrEmpty($this->alias);
    }
}

class AggregationValuesQuery {
    private $selectCommand;
    private $engCommandImp;

    /** @var FieldAggregationInfo[] */
    private $fieldAggregations;

    const SubQueryAlias = 'SM_AGGR_ALIAS';

    public function __construct(BaseSelectCommand $selectCommand, EngCommandImp $engCommandImp) {
        $this->selectCommand = $selectCommand;
        $this->engCommandImp = $engCommandImp;
        $this->fieldAggregations = array();
    }

    public function AddAggregate($fieldName, Aggregate $aggregate, $alias = '') {
        $this->fieldAggregations[$fieldName] = new FieldAggregationInfo($aggregate, $alias);
    }

    public function GetSQL() {
        return StringUtils::Format(
            'SELECT %s FROM (%s) %s',
            $this->GetFieldListAsSQL(),
            $this->selectCommand->GetSQL(),
            self::SubQueryAlias);
    }

    private function GetFieldListAsSQL() {
        $result = '';
        foreach ($this->fieldAggregations as $fieldName => $aggregationInfo)
            if (!$aggregationInfo->HasAlias())
                StringUtils::AddStr($result,
                    $aggregationInfo->GetAggregate()->AsSQL($this->engCommandImp->QuoteIndetifier($fieldName)),
                    ', ');
            else
                StringUtils::AddStr($result,
                    $this->engCommandImp->GetAliasedAsFieldExpression(
                        $aggregationInfo->GetAggregate()->AsSQL($this->engCommandImp->QuoteIndetifier($fieldName)),
                        $this->engCommandImp->QuoteIndetifier($aggregationInfo->GetAlias())
                    ),
                    ', ');
        return $result;
    }

    public function Execute(EngConnection $connection) {
        return $this->engCommandImp->ExecuteReader($connection, $this->GetSQL());
    }
}

?>