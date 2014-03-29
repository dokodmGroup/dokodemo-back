<?php

namespace Mapper;

/**
 * 数据读取模型抽象类
 *
 * @package Mapper
 */
abstract class AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = null;

    /**
     * 返回 Zend 的适配器
     * @return \Zend\Db\Adapter\Adapter
     */
    public function _getAdapter() {
        static $dbAdapter = null;

        if (!$dbAdapter) {
            $conf = \Yaf\Registry::get('config')->get('resources.database.params');
            if (!$conf) {
                throw new \Exception('数据库连接必须设置');
            }
            $dbAdapter = new \Zend\Db\Adapter\Adapter($conf->toArray());
        }

        return $dbAdapter;
    }

    /**
     * 返回 Zend 的 TableGateway，用于写
     * @return \Zend\Db\TableGateway\TableGateway
     */
    protected function _getDbTableGateway() {
        $tableGateway = new \Zend\Db\TableGateway\TableGateway($this->_tableName, $this->_getAdapter());
        return $tableGateway;
    }

    /**
     * 返回 Zend 的 TableGateway
     * @return Zend\Db\Sql\Select
     */
    protected function _getDbSelect() {
        $sql = new \Zend\Db\Sql\Sql($this->_getAdapter(), $this->_tableName);
        return $sql->select();
    }

    /**
     * 开启事务
     */
    public function beginTransaction() {
        $this->_getAdapter()->getDriver()->getConnection()->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commit() {
        $this->_getAdapter()->getDriver()->getConnection()->commit();
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        $this->_getAdapter()->getDriver()->getConnection()->rollback();
    }

    /**
     * @param int $id,主键
     * @return \SiteModel | null
     */
    public function find($id) {
        $resultSet = $this->_getDbTableGateway()->select(array('id' => $id));
        return $resultSet->current();
    }

    /**
     * 获取所有的行.
     * 
     * @param  \Closure|string|array|\Zend\Db\Sql\Predicate\PredicateInterface $where
     * @param string|array $order
     * @param int $count
     * @return array
     */
    public function fetchAll($columns = null, $where = null, $order = null, $count = null, $offset = null, $group = null) {
        $adapter = $this->_getAdapter();

        $sql    = new \Zend\Db\Sql\Sql($adapter, $this->_tableName);
        $select = $sql->select();
        if ($columns) {
            $select->columns($columns);
        }
        if ($where) {
            $isArray = false;
            foreach ($where as $v) {
                if (is_array($v)) {
                    $isArray = true;
                    break;
                }
            }
            if ($isArray) {
                foreach ($where as $k => $v) {
                    if ($k == 'or') {
                        $select->where($v, \Zend\Db\Sql\Where::OP_OR);
                    } else {
                        $select->where($v);
                    }
                }
            } else {
                $select->where($where);
            }
        }
        if ($count) {
            $select->limit($count);
        }
        if ($offset) {
            $select->offset($offset);
        }
        if ($order) {
            $select->order($order);
        }
        if ($group) {
            $select->group($group);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $rows         = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();

        return $rows;
    }

    /**
     * insert data
     * 
     * @param array $data
     * @return int|false
     */
    public function insert($data, $isReturnLastInsertValue = false) {
        $dbTableGateway = $this->_getDbTableGateway();
        $result         = $dbTableGateway->insert($data);
        if ($isReturnLastInsertValue && $result) {
            return $dbTableGateway->getLastInsertValue();
        }

        return $result;
    }

    /**

     * Updates existing data.
     *
     * @param array $data
     * @param string|array|closure $where
     * @return int The number of rows updated.
     */
    public function update($data, $where) {
        return $this->_getDbTableGateway()->update($data, $where);
    }

    /**
     * remove existing data.
     *
     * @param Where|\Closure|string|array $where
     * @return int The number of rows deleted.
     */
    public function remove($where) {
        return $this->_getDbTableGateway()->delete($where);
    }

}
