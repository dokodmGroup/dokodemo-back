<?php

namespace Http;

/**
 * http数据来源抽象类
 */
class AbstractModel {

    public function __clone() {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

}
