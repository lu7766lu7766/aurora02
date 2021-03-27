<?php
/**
 * bug params can't empty sting and 0 , they will be null
 */
namespace comm;

class DB
{
    private $sql = [];
    private $table = '';
    private $params = [];
    private $operators = [SQL::IS, SQL::NOT, SQL::NULL, SQL::EXISTS, SQL::ISNOT];
    private $orderBy;
    private $first_field;
    private $dba;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->dba = new DBA();
    }

    static public function table(string $table)
    {
        $query_builder = new DB($table);
        return $query_builder;
    }

    /**
     * select欄位，可以不給參數會帶入 *
     * 參數格式1：field1, field2, field3...
     * 參數格式2：[field1, field2, field3...]
     * * 接受callback
     * @param $args
     * @return $this
     */
    public function select(...$args)
    {
        /**
         * @param array $args [arg1, arg2] => [arg1 => arg1, arg2 => arg2] /OR/ [[arg1, arg2]] => [arg1 => arg1, arg2 => arg2]
         * @return array
         */
        $selectArgsFormat = function (array $_args) {
            return is_array($_args[0]) ? $_args[0] : $_args;
        };
        $convertAS = function ($name) {
            return (is_int($name) ? '' : (' ' . SQL:: AS . ' ' . $name));
        };
        $this->sql[] = SQL::SELECT;
        $fields = [];
        if (count($args)) {
            $this->first_field = is_array($args[0]) ? $args[0][0] : $args[0];
            /** @var $args get same format */
            $args = $selectArgsFormat($args);
            foreach ($args as $name => $field) {
                if (is_callable($field)) {
                    $fields[] = '(' . $field($this) . ')' . $convertAS($name);
                } else {
                    $fields[] = $field . $convertAS($name);
                }
            }
        } else { // no fields
            $this->sql[] = '*';
        }
        $this->sql[] = join(',', $this->addArrayTemplateString($fields));
        $this->sql[] = SQL::FROM;
        $this->sql[] = $this->table;
        $this->sql[] = SQL::WITHNOLOCK;
        return $this;
    }

    private function addArrayTemplateString(array $fields)
    {
        return array_map(function ($field) {
            return $this->addTemplateString($field);
        }, array_unique($fields));
    }

    /**
     * addTemplateString 加上欄位符號，但sqlserver無法使用
     * @return DBA
     */
    private function addTemplateString(string $field)
    {
//        return '`'.$field.'`';
        return $field;
    }

    /**
     * sum can't move $this->sql in this function
     * @param $field /OR/ function
     */
    public function sum($arg, $as = '')
    {
        $sql = [];
        $sql[] = SQL::SUM;
        $sql[] = '(';
        if (is_callable($arg)) {
            $sql[] = $arg($this);
        } else {
            $sql[] = $arg;
        }
        $sql[] = ')';
        if (!empty($as)) {
            $sql[] = SQL:: AS;
            $sql[] = $as;
        }
        return join(' ', $sql);
    }

    /**
     * count can't move $this->sql in this function
     * @param $field /OR/ function
     */
    public function count($arg, $as = '')
    {
        $sql = [];
        $sql[] = SQL::COUNT;
        $sql[] = '(';
        if (is_callable($arg)) {
            $sql[] = $arg($this);
        } else {
            $sql[] = $arg;
        }
        $sql[] = ')';
        if (!empty($as)) {
            $sql[] = SQL:: AS;
            $sql[] = $as;
        }
        return join(' ', $sql);
    }

    /**
     * cast can't move $this->sql in this function
     * @param $field /OR/ function
     */
    public function cast($field, $type, $as = '')
    {
        $sql = [];
        $sql[] = SQL::CAST;
        $sql[] = '(';
        $sql[] = $field;
        $sql[] = SQL:: AS;
        $sql[] = $type;
        $sql[] = ')';
        if (!empty($as)) {
            $sql[] = SQL:: AS;
            $sql[] = $as;
        }
        return join(' ', $sql);
    }

    /**
     * leftJoin
     */
    public function leftJoin(...$args)
    {
        $this->sql[] = SQL::LEFTJOIN;
        $this->sql[] = $args[0];
        $this->sql[] = SQL::WITHNOLOCK;
        $this->sql[] = SQL::ON;
        $len = count($args);
        for ($i = 1; $i < $len; $i++) {
            $this->sql[] = $args[$i];
        }
        return $this;
    }

    /**
     * insert sql
     * 參數格式1:[ field1 => value1, field2 => value2...]
     * 參數格式2:[[ field1 => value1, field2 => value2...], [ field1 => value11, field2 => value22...]]
     * @param array $args
     * @return $this
     */
    public function insert(array $args)
    {
        $this->sql[] = SQL::INSERT;
        $this->sql[] = $this->table;
        $fields = [];
        $fields_count = 0;
        $values_count = 0;
        /** @var $args get same format */
        $args = $this->args2array($args);
        foreach ($args as $params) {
            $values_count++;
            $fields_count = 0;
            foreach ($params as $field => $value) {
                $fields_count++;
                $fields[] = $field;
                $this->params[] = $value;
            }
        }
        $this->sql[] = '(';
        $this->sql[] = join(',', $this->addArrayTemplateString($fields));
        $this->sql[] = ')';
        $this->sql[] = SQL::VALUES;
        $vals = [];
        for ($i = 0; $i < $values_count; $i++) {
            $tmp = '';
            $tmp .= '(';
            $values = [];
            for ($j = 0; $j < $fields_count; $j++) {
                $values[] = '?';
            }
            $tmp .= join(',', $values);
            $tmp .= ')';
            $vals[] = $tmp;
        }
        $this->sql[] = join(',', $vals);
        return $this;
    }

    /**
     * let params like [arg1,arg2,arg3...] become [[arg1,arg2,arg3]]
     * @param $args
     * @return array
     */
    private function args2array(array $args)
    {
        $res = $args;
        if (!is_array($args[0])) {
            $res = [];
            $res[] = $args;
        }
        return $res;
    }

    /**
     * update sql
     * @param array $args [ field1 => value1, field2 => value2...]
     * @return $this
     */
    public function update(array $args)
    {
        $this->sql[] = SQL::UPDATE;
        $this->sql[] = $this->table;
        $this->sql[] = SQL::SET;
        $updates = [];
        foreach ($args as $field => $value) // must be array
        {
            $sql = '';
            $sql .= $field;
            $sql .= '=';
            $sql .= '?';
            $this->params[] = $value;
            $updates[] = $sql;
        }
        $this->sql[] = join(',', $updates);
        return $this;
    }

    /**
     * delete sql
     * @return $this
     */
    public function delete()
    {
        $this->sql[] = SQL::DELETE;
        $this->sql[] = SQL::FROM;
        $this->sql[] = $this->table;
        return $this;
    }

    /**
     * truncate sql
     * @return $this
     */
    public function truncate()
    {
        $this->sql[] = SQL::TRUNCATE;
        $this->sql[] = $this->table;
        return $this;
    }

    /**
     * has ever run where
     * return bool
     */
    public function hasWhere()
    {
        return in_array(SQL::WHERE, $this->sql);
    }

    /**
     * where sql and start
     * @param ...$args same with where()
     * @return $this
     */
    public function andWhere(...$args)
    {
        $this->sql[] = SQL:: AND;
        $this->sql[] = '(';
        $this->where(...$args);
        $this->sql[] = ')';
        return $this;
    }

    /**
     * where sql where start
     * @param ...$args function($this) /OR/ field, value /OR/ field, operators, value /OR/ [[field, value] /OR/ [field, operators, value]....]
     * @return $this
     */
    public function where(...$args)
    {
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        }
        if (is_array($args[0])) // is array
        {
            $operators = $args[1] ?? SQL:: AND;
            $condition = [];
            foreach ($args[0] as $params) {
                $res = $this->proccessWhere($params);
                $condition[] = join(' ', $res['result']);
                $this->params = array_merge($this->params, $res['params']);
            }
            $this->sql[] = join(' ' . $operators . ' ', $condition);
        } else { // not array
            $res = $this->proccessWhere($args);
            $this->sql = array_merge($this->sql, $res['result']);
            $this->params = array_merge($this->params, $res['params']);
        }
        return $this;
    }

    /**
     * @param $args params不可以塞入 '' 會被轉換出null
     * @return array
     */
    private function proccessWhere($args)
    {
        $res = [];
        $params = [];
        if (count($args) == 1 && is_callable($args[0])) {
            $args[0]($this);
        } else {
            if (count($args) == 2) {
                $res[] = $this->addTemplateString($args[0]);
                if ($this->inOperators($args[1])) {
                    $res[] = $args[1];
                } else {
                    $res[] = '=';
                    $res[] = '?';
                    $params[] = $args[1];
                }
            } else {
                if (count($args) == 3) {
                    $res[] = $this->addTemplateString($args[0]);
                    $res[] = $args[1];
                    if (is_null($args[2])) {
                        $res[] = 'null';
                    } else {
                        $res[] = '?';
                        $params[] = $args[2]; // empty($args[2])? "''":
                    }
                }
            }
        }
        return ['result' => $res, 'params' => $params];
    }

    /**
     * inOperators 是否有運算符
     * @return DBA
     */
    private function inOperators($str)
    {
        $res = false;
        foreach ($this->operators as $operator) {
            if (strpos($str, $operator) !== false) {
                $res = true;
                break;
            }
        }
        return $res;
    }

    /**
     * where sql or start
     * @param ...$args same with where()
     * @return $this
     */
    public function orWhere(...$args)
    {
        $this->sql[] = SQL:: OR;
        $this->sql[] = '(';
        $this->where(...$args);
        $this->sql[] = ')';
        return $this;
    }

    /**
     * where sql not in (...)
     * @param $field string
     * @param $values array
     * @return $this
     */
    public function whereNotIn(string $field, array $values, string $prefix = SQL:: AND)
    {
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        } else {
            $this->sql[] = $prefix;
        }
        $this->sql[] = SQL::NOT;
        $this->whereIn($field, $values);
        return $this;
    }

    /**
     * where sql in (...)
     * @param $field string
     * @param $values array
     * @return $this
     */
    public function whereIn(string $field, array $values, string $prefix = SQL:: AND)
    {
        if (!count($values)) {
            return $this;
        }
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        } else {
            $this->sql[] = $prefix;
        }
        $this->sql[] = $field;
        $this->sql[] = SQL::IN;
        $this->sql[] = '(';
        $sql = [];
        foreach ($values as $val) {
            $sql[] = '?';
            $this->params[] = $val;
        }
        $this->sql[] = join(',', $sql);
        $this->sql[] = ')';
        return $this;
    }

    /**
     * where sql not in (...)
     * @param $arg function
     * @return $this
     */
    public function whereNotExists($arg, string $prefix = SQL:: AND)
    {
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        } else {
            $this->sql[] = $prefix;
        }
        $this->sql[] = SQL::NOT;
        $this->whereExists($arg);
        return $this;
    }

    /**
     * where sql in (...)
     * @param $arg function
     * @return $this
     */
    public function whereExists($arg, string $prefix = SQL:: AND)
    {
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        } else {
            $this->sql[] = $prefix;
        }
        $this->sql[] = SQL::EXISTS;
        $this->sql[] = '(';
        $this->sql[] = call_user_func($arg)->export();
        $this->sql[] = ')';
        return $this;
    }

    /**
     * where sql not between value1 and value2
     * @param $field string
     * @param $values array [value1, value2]
     * @return $this
     */
    public function whereNotBetween(string $field, array $values, string $prefix = SQL:: AND)
    {
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        } else {
            $this->sql[] = $prefix;
        }
        $this->sql[] = SQL::NOT;
        $this->whereBetween($field, $values, $prefix);
        return $this;
    }

    /**
     * where sql between value1 and value2
     * @param $field string
     * @param $values array [value1, value2]
     * @return $this
     */
    public function whereBetween(string $field, array $values, string $prefix = SQL:: AND)
    {
        if (!in_array(SQL::WHERE, $this->sql)) {
            $this->sql[] = SQL::WHERE;
        } else {
            $this->sql[] = $prefix;
        }
        $this->sql[] = $field;
        $this->sql[] = SQL::BETWEEN;
        $this->sql[] = '?';
        $this->sql[] = SQL:: AND;
        $this->sql[] = '?';
        $this->params[] = $values[0];
        $this->params[] = $values[1];
        return $this;
    }

    /**
     * limit sql
     * @param ...$args $limit /OR/ $offset, $limit
     * @return $this
     * @throws Exception must start with select and has orderBy first
     */
    public function limit(...$args)
    {
        if ($this->sql[0] == SQL::SELECT && (!empty($this->orderBy) || !empty($this->first_field))) {
            if (count($args) == 1) {
                $offset = 1;
                $limit = $args[0];
            } else {
                if (count($args) == 2) {
                    $offset = $args[0];
                    $limit = $args[1];
                }
            }
            $limit = $offset + $limit - 1;
            if (!empty($this->orderBy)) {
                array_splice($this->sql, 1, 0, ["ROW_NUMBER() over (order by {$this->orderBy}) rownum,"]);
            } else {
                array_splice($this->sql, 1, 0, ["ROW_NUMBER() over (order by {$this->first_field} asc) rownum,"]);
            }
            array_splice($this->sql, 0, 0, [SQL::SELECT . ' * ' . SQL::FROM . ' (']);
            array_splice($this->sql, array_search(SQL::ORDER, $this->sql), 10,
                [") as tmp where rownum between {$offset} and {$limit}"]);
        } else {
            throw new Exception('there are no relation to order datas');
        }
        // ROW_NUMBER() over (order by ID asc) rownum'
        return $this;
    }

    /**
     * order by sql
     * @param ...$args field, order /OR/ [[field1, order], [field2, order]...]
     * @return $this
     */
    public function orderBy(...$args)
    {
        $convertCallable = function ($_arg) {
            return is_callable($_arg) ? $_arg($this) : $_arg;
        };
        $this->sql[] = SQL::ORDER;
        if (is_array($args[0])) {
            $condition = [];
            foreach ($args[0] as $arg) {
                $condition[] = $convertCallable($arg[0]) . ' ' . $arg[1] ?? SQL::ASC;
            }
            $orderBy = join(',', $condition);
        } else {
            $orderBy = $convertCallable($args[0]) . ' ' . $args[1] ?? SQL::ASC;
        }
        $this->orderBy = $orderBy;
        $this->sql[] = $orderBy;
        return $this;
    }

    /**
     * is null
     * @param field
     * @return $this
     */
    public function isNull($field)
    {
        $this->sql[] = "{$field} is null";
        return $this;
    }

    /**
     * is not null
     * @param field
     * @return $this
     */
    public function isNotNull($field)
    {
        $this->sql[] = "{$field} is not null";
        return $this;
    }

    /**
     * group by sql
     * @param ...$args field, order /OR/ [[field1, order], [field2, order]...]
     * @return $this
     */
    public function groupBy(...$args)
    {
        $this->sql[] = SQL::GROUP;
        $groupBy = [];
        foreach ($args as $arg) {
            $groupBy[] = $arg;
        }
        $this->sql[] = join(',', $groupBy);
        return $this;
    }

    /**
     * having by sql
     * @param ...$args field, order /OR/ [[field1, order], [field2, order]...]
     * @return $this
     */
    public function having(...$args)
    {
        $this->sql[] = SQL::HAVING;
        $groupBy = [];
        foreach ($args as $arg) {
            $groupBy[] = $arg;
        }
        $this->sql[] = join(',', $groupBy);
        return $this;
    }

    /**
     * add sql directive
     * @param $sql
     * @param array $params
     * @return $this
     */
    public function addRaw($sql, $params = [])
    {
        $this->sql[] = $sql;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * output combine sql
     * @return mixed
     */
    public function export()
    {
        return $this->dba->mergeSQL($this->getSQL(), $this->params);
    }

    /**
     * output merge sql
     * @return mixed
     */
    public function getSQL()
    {
        return join(' ', $this->sql);
    }

    /**
     * execute sql
     * @return DBA
     */
    public function insertID()
    {
        if (in_array(SQL::INSERT, $this->sql)) {
            $this->dba->connect();
            $stmt = $this->exec();
            return $this->dba->insert_id($stmt);
        }
        return 0;
    }

    /**
     * execute sql
     * @return DBA
     */
    public function exec()
    {
        $this->dba->connect();
        return $this->dba->exec($this->getSQL(), $this->params);
    }

    /**
     * getAll from sql
     * @return DBA
     */
    public function get()
    {
        $this->dba->connect();
        return $this->dba->getAll($this->getSQL(), $this->params);
    }

    /**
     * let params like [[arg1,arg2,arg3...]] become [arg1,arg2,arg3...]
     * @param $args
     * @return array
     */
//    private function array2args (array $args)
//    {
//        $res = $args;
//        if (is_array($args[0]))
//        {
//            $res = $args[0];
//        }
//        return $res;
//    }
    /**
     * @param array $fields
     * @return array
     */
    public function setDBA(DBA $dba)
    {
        $this->dba = $dba;
        return $this;
    }
}

class SQL
{
    const SELECT = 'select';
    const INSERT = 'insert into';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const WHERE = 'where';
    const ORDER = 'order by';
    const GROUP = 'group by';
    const HAVING = 'having';
    const AND = 'and';
    const OR = 'or';
    const NOT = 'not';
    const IS = 'is';
    const NULL = 'null';
    const ISNOT = 'is not';
    const IN = 'in';
    const EXISTS = 'exists';
    const SUM = 'sum';
    const CAST = 'cast';
    const COUNT = 'count';
    const AS = 'as';
    const VALUES = 'values';
    const SET = 'set';
    const FROM = 'from';
    const LEFTJOIN = 'left join';
    const WITHNOLOCK = 'with (nolock)';
    const ON = 'on';
    const TRUNCATE = 'truncate table';
    const BETWEEN = 'between';
    const ASC = 'asc';
}

?>
