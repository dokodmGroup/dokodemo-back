<?php

namespace Medoo;

class AbstractModel {

    public $db = null;

    public $condition = [];
    private $table = '';
    private $field;
    private $order;
    private $conditionString = '';
    private $join;

    // 数据库表达式转换器
    protected $exp = [
        'eq'=>'=',
        'neq'=>'<>',
        'gt'=>'>',
        'egt'=>'>=',
        'lt'=>'<',
        'elt'=>'<=',
        'notlike'=>'NOT LIKE',
        'like'=>'LIKE',
        'in'=>'IN',
        'notin'=>'NOT IN',
        'not in'=>'NOT IN',
        'between'=>'BETWEEN',
        'not between'=>'NOT BETWEEN',
        'notbetween'=>'NOT BETWEEN'
    ];

    private $joinOpt = [
        'LEFT' => '[>]',
        'RIGHT' => '[<]',
        'FULL' => '[<>]',
        'INNER' => '[><]'
    ];

    public function __construct()
    {
        // loading config
        $database_cfg = \Yaf\Registry::get('config')->resources->database->params->toArray();
        $this->table = $this->getTableName();
        $dbcfg = [
            'database_type' => (false === strpos($database_cfg['driver'], 'pdo_')) ? $database_cfg['driver'] : substr($database_cfg['driver'], 4),
            'database_name' => $database_cfg['database'],
            'server' => $database_cfg['hostname'],
            'username' => $database_cfg['username'],
            'password' => $database_cfg['password'],
            'charset' => $database_cfg['charset'] ?? 'UTF8',
            'port' => $database_cfg['port'] ?? 3306,
            'command' => $database_cfg['driver_options'] ?? [],
        ];
        $this->db = new Medoo($dbcfg);
    }

    public function select()
    {
        $condition = $this->condition ?: null;
        $join = $this->join ?: null;
        return $this->db->select(
            $this->table,
            $this->field ?: '*',
            $condition) ?: [];
    }

    public function field($field)
    {
        $this->field = $field;
    }
    public function where($condition)
    {
        $this->condition = $this->condition ?: [
            'AND' => [],
            'OR' => [],
        ];
        if (is_string($condition)) {
            $this->condition['AND'][] = $condition;
        } else {
            foreach($condition as $key => $value) {
                $this->parseConditionItem($key, $value);
            }
        }
        if (empty($this->condition['AND'])) {
            unset($this->condition['AND']);
        }
        if (empty($this->condition['OR'])) {
            unset($this->condition['OR']);
        }
        return $this;
    }

    public function join(array $join)
    {
        foreach($join as $s_join) {
            if (!is_array($s_join)) {
                throw new \Exception('ERROR JOIN VALUE');
            }
            if (count($s_join) === 3 && 
            isset($s_join[0]) && 
            in_array(strtoupper($s_join[0]), ['INNER', 'LEFT', 'RIGHT', 'FULL'])) {
                $join_str = strtoupper($s_join[0]) . ' JOIN ';
                array_shift($s_join);
            } else if (count($s_join) === 2){
                $join_str = 'INNER JOIN';
            } else {
                throw new \Exception('ERROR JOIN VALUE');
            }
            $cond = $s_join[1];
            $replacement = 'strtoupper("$1")';
            $cond = preg_replace_callback('/(and|or|xor)/', function($m) {
                return strtoupper($m[1]);
            }, $cond);
            $cond = \preg_replace('/(?<!`)\b[^AND|OR|XOR|\.|=|\s]+\b(?!`)/', "`$0`", $cond);
            $table = trim($s_join[0]);
            if (strpos(trim($table), '`') === false) {
                $table = "`{$table}`";
            }
            $join_str .= " {$table} on {$cond}";
            $this->join[] = $join_str;
        }
    }

    /**
     * 对单项条件处理
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * Kanzaki Tsukasa
     */
    private function parseConditionItem(string $key, $value)
    {
        if (false !== strpos($key,'|')) {
            $key_arr = explode('|', $key);
            foreach($key_arr as $s_key) {
                $this->condition['OR'][] = [$s_key => $value];
            }
        }
    }

    private function getTableName()
    {
        $class_arr = explode('\\', \get_called_class());
        $class = array_pop($class_arr);
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", substr($class, 0, -5)), "_"));
    }

    public function debug()
    {
        $this->db->debug();
        return $this;
    }

    public function error()
    {
        return $this->db->error();
    }

    public function getDb()
    {
        return $this->db;
    }
}