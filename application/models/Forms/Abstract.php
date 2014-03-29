<?php

namespace Forms;

/**
 * 表单抽象模型
 */
class AbstractModel {

    /**
     * 所有的表单字段配置
     * 
     * @var array
     */
    protected $_allFieldsIni = array();

    /**
     * 实际操作的表单字段
     * 
     * @var array
     */
    protected $_fields = array();

    /**
     * 构造方法
     * 
     * @param array $initFieldNames 需要初始化的表单字段名称
     */
    public function __construct(array $initFieldNames = array()) {
        if (!$initFieldNames) {
            $allIniFields = $this->getAllFieldsIni();
            foreach ($allIniFields as $field) {
                $this->addField($field['name']);
            }
        }
        foreach ($initFieldNames as $fieldName) {
            $this->addField($fieldName);
        }
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
    public function validate($data) {
        $this->setData($data);
        $result = true;
        foreach ($this->_fields as $fieldName => $fieldConfig) {
            if (isset($fieldConfig['validate']) && $fieldConfig['validate'] === false) {
                continue;
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
     * 获取所有设置的字段
     * 
     * @return array
     */
    public function getAllFieldsIni() {
        if (!$this->_allFieldsIni) {
            $this->setAllFieldsIni();
        }
        return $this->_allFieldsIni;
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

    /**
     * 校验时间格式
     * 
     * @param array $options
     * @return boolean
     */
    protected function _validateTime($options) {
        $time = $options['value'];
        return \Our\Common::checkDateIsValid($time, array('Y-m-d H:i:s'));
    }

    /**
     * 校验邮箱
     * 
     * @param array $options
     * @return boolean
     */
    protected function _validateEmail($options) {
        $email  = $options['value'];
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * 校验手机号码
     * 
     * @return boolean
     */
    public function validateMobile() {
        $mobile = $this->getFieldValue('mobile');
        if (!$mobile) {
            $this->setFieldMessage('mobile', '手机号码不能为空');
            return false;
        }

        if (!preg_match('/^1[3|4|5|8][0-9]\d{8}$/i', $mobile)) {
            $this->setFieldMessage('mobile', '手机号码格式错误');
            return false;
        }

        return true;
    }

    /**
     * 校验用户输入的密码
     * 
     * @return boolean
     */
    public function validatePassword() {
        $password = $this->getFieldValue('password');
        $options  = array('value' => $password, 'min' => 6);
        if (!$this->_validateLength($options)) {
            return $this->setFieldMessage('password', '密码长度6位到16位');
        }

        return true;
    }

    /**
     * 校验用户输入的验证码
     * 
     * @return boolean
     */
    public function validateCode() {
        $code    = $this->getFieldValue('code');
        $options = array('value' => $code, 'min' => 6, 'max' => 6);
        if (!$this->_validateLength($options)) {
            return $this->setFieldMessage('code', '验证码长度为6位');
        }

        return true;
    }

    /**
     * 校验页码
     * 
     * @return boolean
     */
    public function validatePage() {
        $page = $this->getFieldValue('page');
        if (!$page) {
            return true;
        }
        $options = array('value' => $page, 'min' => 1);
        if (!$this->_validateInt($options)) {
            $this->setFieldMessage('page', '页码不正确');
            return false;
        }

        return true;
    }

    /**
     * 校验条数
     * 
     * @return boolean
     */
    public function validateCount() {
        $count = $this->getFieldValue('count');
        if (!$count) {
            return true;
        }
        $options = array('value' => $count, 'min' => 1);
        if (!$this->_validateInt($options)) {
            $this->setFieldMessage('count', '数量不正确');
            return false;
        }

        return true;
    }

    /**
     * 校验地区编号
     * 
     * @return boolean
     */
    public function validateAddressId() {
        $addressId = $this->getFieldValue('address_id');
        $options   = array('value' => $addressId, 'min' => 1);
        if (!$this->_validateInt($options)) {
            $this->setFieldMessage('address_id', '地址编号不正确');
            return false;
        }

        return true;
    }

    /**
     * 校验订单编号
     * 
     * @return boolean
     */
    public function validateOrderId() {
        $orderId = $this->getFieldValue('order_id');
        $options = array('value' => $orderId, 'min' => 1);
        if (!$this->_validateInt($options)) {
            $this->setFieldMessage('order_id', '订单编号错误');
            return false;
        }

        return true;
    }

    /**
     * 校验用户编号
     * 
     * @return boolean
     */
    public function validateMemberId() {
        $memberId = $this->getFieldValue('member_id');
        if (!$memberId) {
            return true;
        }
        $options = array('value' => $memberId, 'min' => 1);
        if (!$this->_validateInt($options)) {
            $this->setFieldMessage('member_id', '用户编号不正确');
            return false;
        }

        return true;
    }

}
