<?php

namespace Forms;

/**
 * 表单抽象模型
 */
class AbstractModel {

    /**
     * 表单字段
     * 
     * @var array
     */
    protected $_fields = array();

    /**
     * 构造方法
     * 
     * @param array $initFieldNames 需要初始化的表单字段名称
     */
    public function __construct($data) {
        $this->setData($data);
    }

    /**
     * 获取字段提示信息
     * 
     * @param string $field
     */
    public function getFieldMessage($field = null) {
        $fields = $this->getFields();
        if (!$field) {
            $fieldsMessage = array();
            foreach ($fields as $field) {
                if (isset($field['message']) && $field['message']) {
                    $fieldsMessage[$field['name']] = $field['message'];
                }
            }
            return $fieldsMessage;
        }

        return $fields[$field]['message'];
    }

    /**
     * 设置字段的值
     * 
     * @param array $data
     */
    public function setData($data) {
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $this->_fields)) {
                $this->_fields[$k]['value'] = trim($v);
            }
        }
    }

    /**
     * 获取字段的值
     * 
     * @param string $fieldName
     * @return mix
     */
    public function getFieldValue($fieldName = null) {
        $fields = $this->getFields();
        if (!$fieldName) { //获取所有字段的值
            $fieldsValue = array();
            foreach ($fields as $field) {
                $fieldsValue[$field['name']] = isset($field['value']) ? $field['value'] : '';
            }

            return $fieldsValue;
        }

        foreach ($fields as $field) {
            if ($field['name'] == $fieldName) {
                return isset($field['value']) ? $field['value'] : null;
            }
        }

        return null;
    }

    /**
     * 校验所有字段的值
     * 
     * @param array $data
     * @return boolean
     */
    public function validate() {
        $result = true;
        foreach ($this->_fields as $fieldName => $field) {
            if (!empty($field['validate'])) {
                foreach ($field['validate'] as $validate) {
                    switch ($validate["type"]) {
                        case "string":
                            if (!$this->_validateLength(array(
                                        "value" => $field["value"],
                                        "min"   => $validate["min"],
                                        "max"   => $validate["max"],
                                    ))) {
                                $result = false;
                                $this->setFieldMessage($fieldName, $validate["msg"]);
                            };
                            break;
                        case "int":
                            if (!$this->_validateInt(array(
                                        "value" => $field["value"],
                                        "min"   => $validate["min"],
                                        "max"   => $validate["max"],
                                    ))) {
                                $result = false;
                                $this->setFieldMessage($fieldName, $validate["msg"]);
                            };
                            break;
                        default:
                            break;
                    }
                }
            }

            //检测各个字段自己的校验方法
            $methodName = 'validate' . ucfirst(preg_replace_callback('/_\w/i'
                                    , create_function('$matches', 'return strtoupper(ltrim($matches[0],"_"));')
                                    , $fieldName));
            if (method_exists($this, $methodName)) {
                if (!$this->$methodName()) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * 设置字段的属性值
     * 
     * @param string $fieldName
     * @param array $attrs
     */
    public function setFiledsAttr($fieldName, $attrs) {
        foreach ($attrs as $k => $v) {
            $this->_fields[$fieldName][$k] = $v;
        }
    }

    /**
     * 获取字段的属性值
     * 
     * @param string $fieldName
     * @param array|string $attrs
     */
    public function getFieldAttrs($fieldName, $attrs) {
        if (is_string($attrs)) {
            if (!empty($this->_fields[$fieldName][$attrs])) {
                return $this->_fields[$fieldName][$attrs];
            }
            return null;
        }

        $return = array();
        foreach ($attrs as $attr) {
            $return[$attr] = empty($this->_fields[$fieldName][$attr]) ? null : $this->_fields[$fieldName][$attr];
        }

        return $return;
    }

    /**
     * 设置字段是否需要校验
     * 
     * @param string $fieldName
     * @param boolean $isNeedValidate
     */
    public function setFieldIsNeedValidate($fieldName, $isNeedValidate) {
        $this->setFiledsAttr($fieldName, array('validate' => $isNeedValidate));
    }

    /**
     * 设置字段允许为空
     * 
     * @param string $fieldName
     * @param boolean $isNeedValidate
     */
    public function setFieldAllowEmpty($fieldName) {
        $this->setFiledsAttr($fieldName, array('allow_empty' => true));
    }

    /**
     * 检测字段是否允许为空
     * 
     * @param string $fieldName
     * @return boolean
     */
    public function isFieldAllowEmpty($fieldName) {
        $result = $this->getFieldAttrs($fieldName, 'allow_empty');
        if ($result === true) {
            return true;
        }

        return false;
    }

    /**
     * 设置字段的提示信息
     * 
     * @param string $fieldName
     * @param string $message
     */
    public function setFieldMessage($fieldName, $message) {
        if (empty($this->_fields[$fieldName])) {
            throw new \Exception("字段中没有叫" . $fieldName);
        }
        $this->_fields[$fieldName]['message'] = $message;
    }

    /**
     * 增加字段
     * 
     * @param string $fieldName
     */
    public function addField($fieldName) {
        $allFieldsIni = $this->getAllFieldsIni();
        if (!isset($allFieldsIni[$fieldName])) {
            throw new \Exception('Form field is not exists.');
        }
        $this->_fields[$fieldName] = $allFieldsIni[$fieldName];
    }

    /**
     * 移除字段
     * 
     * @param string $fieldName
     */
    public function removeField($fieldName) {
        if (isset($this->_fields[$fieldName])) {
            unset($this->_fields[$fieldName]);
        }
    }

    /**
     * 获取所有字段
     * 
     * @return array
     */
    public function getFields() {
        return $this->_fields;
    }

    /**
     * 校验字符串长度
     * 
     * @param array $options
     * @return boolean
     */
    protected function _validateLength($options) {
        $length = mb_strlen($options['value']);
        if (isset($options['min'])) {
            if ($length < $options['min']) {
                return false;
            }
        }
        if (isset($options['max'])) {
            if ($length > $options['max']) {
                return false;
            }
        }
        return true;
    }

    /**
     * 校验整形数字
     * 
     * @param array $options
     * @return boolean
     */
    protected function _validateInt($options) {
        $num = $options['value'];
        if (isset($options['min']) && isset($options['max'])) {
            $int_options = array("options" => array("min_range" => $options['min'], "max_range" => $options['max']));
        } else if (isset($options['min'])) {
            $int_options = array("options" => array("min_range" => $options['min']));
        } else if (isset($options['max'])) {
            $int_options = array("options" => array("max_range" => $options['max']));
        }
        $result = filter_var($num, FILTER_VALIDATE_INT, $int_options);
        if ($result === FALSE) {
            return false;
        }
        return true;
    }

}
