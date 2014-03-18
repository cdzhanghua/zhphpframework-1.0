<?php
/**
 * Created by PhpStorm.
 * User: zhanghua
 * Date: 14-2-5
 * Time: 23:28
 * QQ: 746502560qq.com
 */
class validate
{
    /**
     * 验证数字
     * 参数： 字符串  长度
     * 说明：validate_d($string,$length) 该函数依据传递的字符和长度来验证 是否为数字 长度是否符合
     */
    public static function validate_d($string = null, $length = null)
    {
        $pattern = (!empty($length)) ? '|\d{' . $length . '}|' : '|\d|';
        $tmp = preg_match_all($pattern, trim($string), $matchAll);
         if ($tmp) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证数字字母下划线
     * 参数：字符串  长度
     */
    public  static function validate_w($string = null, $length = null)
    {
        $pattern = (!empty($length)) ? '|\w{' . $length . '}|' : '|\w|';
        $tmp = preg_match_all($pattern, trim($string), $mattchAll);
        if ($tmp) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证email
     * 参数：字符串
     */
    public static function validate_Email($email = null)
    {
        $pattern = '/(\w+[-._]?\w+)+(@)([a-zA-Z0-9]+\.)+(com|cn|net|com.cn)?/';
        $tmp = preg_match_all($pattern, trim($email), $mattchAll);
        if ($tmp) {
            return true;
        } else {
            return false;
        }
    }


    /**
     *	数据基础验证-是否是身份证
     *
     * 	@param  string $value 需要验证的值
     *  @return bool
     */
    public static function validate_card($value)
    {
        $tmp = preg_match_all("/^(\d{15}|\d{17}[\dx])$/i", trim($value), $mattchAll);
        if ($tmp) {
            return true;
        } else {
            return false;
        }
    }
    /**
     *	数据基础验证-是否是中文
     *  参数： string $value 需要验证的值
     *  返回 bool
     */
    public static function validate_gbk($value = null)
    {
        $tmp = preg_match_all("/^([\xE4-\xE9][\x80-\xBF][\x80-\xBF])+$/", trim($value),
            $mattchAll);
        if ($tmp) {
            return true;
        } else {
            return false;
        }
    }

        /**
         *	数据基础验证-是否是QQ
         * 	@param  string $value 需要验证的值
         *  @return bool
         */
        public static function  validate_qq($value)
        {
            $tmp = preg_match_all('/^[1-9]\d{4,12}$/', trim($value), $matttchAll);
            if ($tmp) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	数据基础验证-是否是邮政编码
         *
         * 	@param  string $value 需要验证的值
         *  @return bool
         */
       public static function  validate_zip($value)
        {
            $tmp = preg_match_all('/^[1-9]\d{5}$/', trim($value), $matttchAll);
            if ($tmp) {
                return true;
            } else {
                return false;
            }
        }
        /**
         *	数据基础验证-是否是URL
         * 	@param  string $value 需要验证的值
         *  @return bool
         */
        public static function validate_url($value)
        {
            $tmp = preg_match_all('/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
                trim($value), $matttchAll);
            if ($tmp) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	数据基础验证-是否是移动电话
         * 	@param  string $value 需要验证的值
         *  @return bool
         */
        public static function validate_phone($value)
        {
            $tmp = preg_match_all('/^((\(\d{2,3}\))|(\d{3}\-))?(13|15)\d{9}$/', trim($value),
                $matttchAll);
            if ($tmp) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *	数据基础验证-是否是电话
         * 	@param  string $value 需要验证的值
         *  @return bool
         */
        public static function validate_mobile($value)
        {
            $tmp = preg_match_all('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',
                trim($value), $matttchAll);
            if ($tmp) {
                return true;
            } else {
                return false;
            }
        }

}
?>