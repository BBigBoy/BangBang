<?php
/**
 * Class Own_Validate
 */
class Own_Validate
{
    /* 定义变量可能的类型。具体取值来自于gettype方法
    * gettpe 返回的字符串的可能值为：
    * “boolean”、“integer”、“double”（由于历史原因，如果是 float 则返回“double”，而不是“float”）
    * “string”、“array”、“object”、“resource”、“NULL”、“unknown type”*/
    const BOOLEAN_VAR = 'boolean';
    const INTEGER_VAR = 'integer';
    const DOUBLE_VAR = 'double';
    const STRING_VAR = 'string';
    const ARRAY_VAR = 'array';
    const OBJECT_VAR = 'object';
    const RESOURCE_VAR = 'resource';
    const NULL_VAR = 'NULL';
    const UNKNOWN_VAR = 'unknown type';
///
    /**
     *数据类型
     */
    const TYPE = 'type';
    /**
     *验证中通用的验证条件
     */
    const COMMON_VALI = 'common-validate';
    /**
     *通用验证条件所使用的变量
     */
    const COMMON_VALI_VARS = 'common-validate-var';
    /**
     *是否允许该变量为空（不存在）
     */
    const IS_NULL = 'is_null';
    /**
     *字符串的最大长度
     */
    const MAX_LEN = 'max_len';
    /**
     *字符串的最小长度
     */
    const MIN_LEN = 'min_len';
    /**
     *int类型变量的最大值
     */
    const MAX_INT = 'max_int';
    /**
     *int类型变量的最小值
     */
    const MIN_INT = 'min_int';
    /**
     *用于表达验证规则的数组
     */
    const VALI_RULE_ARR = 'vali_rule_arr';
    /**
     *数组类型变量的最大元素个数
     */
    const MAX_ARR_LEN_VAR = 'max_arr_len';
    /**
     *逻辑或
     */
    const LOGIC_OR_VAR = '_or';
    /**
     *在“或”逻辑中，允许存在变量个数的最大值。
     * 比如“1、2、3、4、5这几个数是或的关系，那么最多允许从他们中取出几个值，就是LOGIC_OR_MULTI_VAR”
     */
    const LOGIC_OR_MULTI_VAR = '_or_multi';


    /**
     *数据格式，比如手机号、邮箱地址
     */
    const FORMAT = 'format';
    /**
     *手机号格式
     */
    const MOBILE_FORMAT = 'mobile';
    /**
     *邮箱格式
     */
    const EMAIL_FORMAT = 'email';

    /**
     * 校验方法传入参数合法性
     * 使用本方法的时候，对于$valiRuleArr数组的键值，建议直接引用本类中设置的常量，
     * 这样既有利于防范人为错误，也便于程序后续升级
     * @param $funcParamsArr array 方法传入的参数数组
     * @param $valiRuleArr array 方法传入的验证规则数组,如果验证规则为空数组，
     * 则默认无需验证，直接返回true
     * $valiRuleArr构造示例：
     * $valiRuleArr['example1']=array(
     * self::TYPE => self::STRING_VAR,
     * self::IS_NULL=> true, self::MAX_LEN => 30);
     * $valiRuleArr['example2']=array(
     * self::TYPE => self::INTEGER_VAR);
     * $valiRuleArr['example3']=array(
     * self::TYPE => self::ARRAY_VAR,
     * self::MAX_ARR_LEN_VAR => 50,
     * self::VALI_RULE_ARR => array(self::TYPE => self::INTEGER_VAR));
     * @return bool 验证成功返回true，否则返回false,返回false前都会通过errorLog记录错误信息
     */
    public
    static function validateFuncParam($funcParamsArr, $valiRuleArr = array())
    {
        return self::validateFuncParamInner($funcParamsArr, $valiRuleArr);
    }

    /**
     * 校验方法传入参数合法性
     * 使用本方法的时候，对于$valiRuleArr数组的键值，建议直接引用本类中设置的常量，
     * 这样既有利于防范人为错误，也便于程序后续升级
     * @param $funcParamsArr array 方法传入的参数数组
     * @param $valiRuleArr array 方法传入的验证规则数组,如果验证规则为空数组，
     * 则默认无需验证，直接返回true
     * @param string $errMsg 错误信息。因为校验数组中可能存在嵌套，
     * 校验方法会进行递归，为了遵循一次校验一次记录的规则，采用引用的方式实现
     * @param int $errorNum 错误序号，每次调用从1开始排序。  同样以引用的方式传递
     * @param bool $isRecursion 是否为递归调用，如果是递归，不将错误信息记录到数据库
     * @return bool 验证成功返回true，否则返回false,返回false前都会通过errorLog记录错误信息
     */
    public
    static function validateFuncParamInner($funcParamsArr, $valiRuleArr = array()
        , &$errMsg = '', &$errorNum = 0, $isRecursion = false)
    {
        if (is_array($funcParamsArr) && is_array($valiRuleArr)) {
            if (isset($valiRuleArr[self::LOGIC_OR_VAR])) {
                $tmpORValiRuleArr = $valiRuleArr[self::LOGIC_OR_VAR];
                if (!is_array($valiRuleArr[self::LOGIC_OR_VAR][0])) {
                    unset($valiRuleArr[self::LOGIC_OR_VAR]);
                    $valiRuleArr[self::LOGIC_OR_VAR][0] = $tmpORValiRuleArr;
                }
                foreach ($valiRuleArr[self::LOGIC_OR_VAR] as $itemKey => $orItem) {
                    if (!is_int($itemKey)) {
                        continue;
                    }
                    $varNum = 0;//标记这些变量中，实际存在的变量个数
                    $orMultiNum = 1;//在这些变量中允许存在的最大个数，默认为1个
                    foreach ($orItem as $paramKey => $orParam) {
                        if ($paramKey === self::LOGIC_OR_MULTI_VAR) {
                            $orMultiNum = $orParam;
                            continue;
                        }
                        if (is_array($orParam)) {
                            $hasNonExistVar = true;
                            foreach ($orParam as $param) {
                                if (!isset($funcParamsArr[$param])) {
                                    $hasNonExistVar = false;
                                    if (isset($valiRuleArr[$param])) {
                                        //避免错误的$param值造成验证错误
                                        $valiRuleArr[$param][self::IS_NULL] = true;
                                    }
                                }
                            }
                            if ($hasNonExistVar === true) {
                                $varNum++;
                            }
                            continue;
                        } elseif (is_string($orParam)) {
                            if (isset($funcParamsArr[$orParam])) {
                                $varNum++;
                            } else {
                                if (isset($valiRuleArr[$orParam])) {
                                    //避免错误的$param值造成验证错误
                                    $valiRuleArr[$orParam][self::IS_NULL] = true;
                                }
                            }
                        }
                    }
                    if (!(($orMultiNum >= $varNum) && ($varNum !== 0))) {
                        $errMsg .= (++$errorNum . '、 ' . '变量个数不符合规定！' . $varNum . "\n");
                    }
                }
                unset($valiRuleArr[self::LOGIC_OR_VAR]);
            }
            if (isset($valiRuleArr[Own_Validate::COMMON_VALI])) {
                if (is_array($valiRuleArr[Own_Validate::COMMON_VALI][Own_Validate::COMMON_VALI_VARS])) {
                    foreach ($valiRuleArr[Own_Validate::COMMON_VALI][Own_Validate::COMMON_VALI_VARS] as $varName) {
                        self::validateVar($funcParamsArr[$varName],
                            $valiRuleArr[Own_Validate::COMMON_VALI], $varName, $errMsg, $errorNum);
                    }
                }
                unset($valiRuleArr[Own_Validate::COMMON_VALI]);
            }
            foreach ($valiRuleArr as $valiParamName => $valiParam) {
                self::validateVar($funcParamsArr[$valiParamName], $valiParam, $valiParamName, $errMsg, $errorNum);
            }
        } else {
            $errMsg .= (++$errorNum . '、 ' . '$funcParamsArr或$valiRuleArr不是数组类型，当前$funcParamsArr的类型为' . gettype($funcParamsArr) . ',$valiRuleArr的类型为' . gettype($valiRuleArr) . "\n");
        }
        if ($errMsg === '') {
            return true;
        } else {
            if (!$isRecursion) {
                errorLog($errMsg);
            }
            return false;
        }
    }

    /**
     * 根据验证规则验证一个变量
     * @param $var mixed 被验证的变量
     * @param $valiParam array 验证的条件数组
     * @param $valiParamName string 被验证的变量的名称
     * @param string $errMsg 错误信息。因为校验数组中可能存在嵌套，
     * 校验方法会进行递归，为了遵循一次校验一次记录的规则，采用引用的方式实现
     * @param int $errorNum 错误序号，每次调用从1开始排序。  同样以引用的方式传递
     */
    private function validateVar($var, $valiParam, $valiParamName, &$errMsg = '', &$errorNum = 0)
    {
        $paramType = gettype($var);
        if (!(($paramType === $valiParam[self::TYPE])
            || ($paramType === self::NULL_VAR
                && isset($valiParam[self::IS_NULL])
                && $valiParam[self::IS_NULL] === true))
        ) {
            $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的类型应该为' . ($valiParam[self::TYPE]) . ',但是当前它的类型为' . $paramType . "\n");
        }
        if ($paramType === self::STRING_VAR) {
            $strLen = stringLength($var);
            if (isset($valiParam[self::MAX_LEN])
                && ($strLen > $valiParam[self::MAX_LEN])
            ) {
                $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的值为' . $var .
                    ',超过了最大长度' . $valiParam[self::MAX_LEN] . "\n");
            }
            if (isset($valiParam[self::MIN_LEN])
                && ($strLen < $valiParam[self::MIN_LEN])
            ) {
                $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的值为' . $var .
                    ',小于最小长度' . $valiParam[self::MAX_LEN] . "\n");
            }
            if (isset($valiParam[self::FORMAT])) {
                if ($valiParam[self::FORMAT] === self::MOBILE_FORMAT) {
                    if (!self::checkMobile($var))
                        $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的格式应该为手机号码' . "\n");
                } elseif ($valiParam[self::FORMAT] === self::EMAIL_FORMAT) {
                    if (!self::checkEmail($var))
                        $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的格式应该为邮箱地址' . "\n");
                }
            }
        } else if ($paramType === self::INTEGER_VAR) {
            if (isset($valiParam[self::MAX_INT])
                && ($var > $valiParam[self::MAX_INT])
            ) {
                $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的值为' . $var .
                    ',超过了最大值' . $valiParam[self::MAX_INT] . "\n");
            }
            if (isset($valiParam[self::MIN_INT])
                && ($var < $valiParam[self::MIN_INT])
            ) {
                $errMsg .= (++$errorNum . '、 ' . $valiParamName . '的值为' . $var .
                    ',低于最小值' . $valiParam[self::MIN_INT] . "\n");
            }
        } else if ($paramType === self::ARRAY_VAR) {
            if (isset($valiParam[self::MAX_ARR_LEN_VAR]) && (count($var)
                    > $valiParam[self::MAX_ARR_LEN_VAR])
            ) {
                $errMsg .= (++$errorNum . '、 ' . $valiParamName . '所定义的数组长度超过了限制' . "\n");
            } else {
                self::validateFuncParamInner($var, $valiParam[self::VALI_RULE_ARR], $errMsg, $errorNum, true);
            }
        }
    }

    /**
     * 检查手机号格式是否标准
     * @param $tel string 手机号的字符串格式
     * @return bool 验证通过返回true，失败返回false
     */
    private static function checkMobile($tel)
    {
        $length = stringLength($tel);
        if ($length == 11) {
            return ((float)$tel) > 10000000000;
        } elseif ($length == 12) {
            return ((float)$tel) > 100000000000;
        } else {
            return false;
        }
    }

    /**
     * 验证email的正确性
     * @param $email string 邮箱地址
     * @return bool 验证成功返回true，失败返回false
     */
    private static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }
}