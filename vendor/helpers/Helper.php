<?php
/**
 * Created by PhpStorm.
 * User: d
 * Date: 2018/3/31
 * Time: 10:40
 */

namespace vendor\helpers;


class Helper
{
    /**
     * 导出excle
     * @param array $data
     * @param array $title
     * @param string $filename
     */
    public static function excel($title = [], $data = [], $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $re = "<table border='1'><thead>";
        if ($title) {
            $re .= "<tr>";
            foreach ($title as $k => $v) {
                $re .= "<th style='background-color:rgb(189,215,238);'>" . iconv("UTF-8", "GBK//IGNORE", $v) . "</th>";
            }
        }
        $re .= "</tr></thead><tbody>";
        if ($data) {
            foreach ($data as $key => $val) {
                $re .= "<tr>";
                foreach ($val as $ck => $cv) {
                    $re .= "<td>" . iconv("UTF-8", "GBK//IGNORE", $cv) . "</td>";
                }
                $re .= "</tr>";
            }
            $re .= "</tbody></table>";
        }
        echo $re;
        exit();
    }

    /**
     * 导出复杂excle
     * @param array $data
     * @param string $filename
     */
    public static function complexExcel($data = [], $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $excel = "<table style='text-align: right' border='1'>";
        foreach ($data as $k => $v) {
            $blockInfo = explode('-', $k);
            $name = iconv("UTF-8", "GBK//IGNORE", $blockInfo[0]);
            $count = $blockInfo[1];
            $excel .= "<tr style='text-align: center'><td colspan='$count'>$name</td></tr>";
            foreach ($v as $row) {
                $excel .= "<tr>";
                foreach ($row as $one) {
                    $style = " style='background-color:rgb(189,215,238);'";
                    if (strpos($one, '!T') === false) {
                        $style = '';
                    }
                    $info = iconv("UTF-8", "GBK//IGNORE", str_replace('!T', '', $one));
                    $excel .= "<td$style>$info</td>";
                }
                $excel .= "</tr>";
            }
        }
        $excel .= "</table>";
        echo $excel;
        exit();
    }


    /**
     * 根据经纬度计算距离
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @param float $radius
     * @param int $round
     * @return float KM
     */
    public static function distance($lat1, $lon1, $lat2, $lon2, $radius = 6378.137, $round = 2)
    {
        $rad = floatval(M_PI / 180.0);
        $lat1 = floatval($lat1) * $rad;
        $lon1 = floatval($lon1) * $rad;
        $lat2 = floatval($lat2) * $rad;
        $lon2 = floatval($lon2) * $rad;
        $theta = $lon2 - $lon1;
        $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));
        if ($dist < 0) {
            $dist += M_PI;
        }
        $dist = $dist * $radius;
        return round($dist, $round);
    }

    /**
     * 计算时长
     * @param int $begin
     * @param int $end
     * @return string
     */
    public static function timeLong($begin = 0, $end = 0)
    {
        $duration = '00:00:00';
        if ($begin && $end && $begin <= $end) {
            //计算天数
            $timeDiff = $end - $begin;
            $day = intval($timeDiff / 86400);
            if ($day) {
                $res['日'] = $day;
            }
            //计算小时数
            $remain = $timeDiff % 86400;
            $res['小时'] = intval($remain / 3600);
            //计算分钟数
            $remain = $remain % 3600;
            $res['分'] = intval($remain / 60);
            //计算秒数
            $res['秒'] = $remain % 60;
            $duration = implode(':', $res);
        }
        return $duration;
    }

    /**
     * 计算时长
     * @param int $begin
     * @param int $end
     * @return string
     */
    public static function duration($begin = 0, $end = 0)
    {
        $duration = '';
        if ($begin && $end && $begin <= $end) {
            //计算天数
            $timeDiff = $end - $begin;
            $res['日'] = intval($timeDiff / 86400);
            //计算小时数
            $remain = $timeDiff % 86400;
            $res['小时'] = self::repair(intval($remain / 3600),2,0);
            //计算分钟数
            $remain = $remain % 3600;
            $res['分'] = self::repair(intval($remain / 60),2,0);
            //计算秒数
            $res['秒'] = self::repair($remain % 60,2,0);
            foreach ($res as $k => $v) {
                $val = (int) $v;
                if ($val) {
                    $duration .= $v . $k;
                }
            }
        }
        return $duration;
    }

    /**
     * 补全
     * @param $str
     * @param $len
     * @param $rep
     * @param int $type
     * @return string
     */
    public static function repair($str, $len, $rep, $type = 1)
    {
        $length = $len - strlen($str);
        if ($length < 1) return $str;
        if ($type == 1) {
            $str = str_repeat($rep, $length) . $str;
        } else {
            $str .= str_repeat($rep, $length);
        }
        return $str;
    }

    /**
     * 返回数组维度
     * @param array $array
     * @return int|mixed
     */
    public static function arrayDepth($array = [])
    {
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::arrayDepth($value) + 1;
                $max_depth = max($max_depth, $depth);
            }
        }
        return $max_depth;
    }

    /**
     * 生成唯一字符串
     * @param int $type 字符串的类型
     *   0-存数字字符串；1-小写字母字符串；2-大写字母字符串；3-大小写数字字符串；4-字符；
     *   5-数字，小写，大写，字符混合
     * @param int $length 字符串的长度
     * @param int $time [是否带时间1-带，0-不带
     * @return false|string
     */
    public static function randStr($type = 0, $length = 18, $time = 0)
    {
        $str = $time == 0 ? '' : date('YmdHis', time());
        switch ($type) {
            case 0:
                for ((int)$i = 0; $i <= $length; $i++) {
                    if (mb_strlen($str) == $length) {
                        $str = $str;
                    } else {
                        $str .= rand(0, 9);
                    }
                }
                break;
            case 1:
                for ((int)$i = 0; $i <= $length; $i++) {
                    if (mb_strlen($str) == $length) {
                        $str = $str;
                    } else {
                        $rand = "qwertyuioplkjhgfdsazxcvbnm";
                        $str .= $rand{mt_rand(0, 26)};
                    }
                }
                break;
            case 2:
                for ((int)$i = 0; $i <= $length; $i++) {
                    if (mb_strlen($str) == $length) {
                        $str = $str;
                    } else {
                        $rand = "QWERTYUIOPLKJHGFDSAZXCVBNM";
                        $str .= $rand{mt_rand(0, 26)};
                    }
                }
                break;
            case 3:
                for ((int)$i = 0; $i <= $length; $i++) {
                    if (mb_strlen($str) == $length) {
                        $str = $str;
                    } else {
                        $rand = "123456789qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM";
                        $str .= $rand{mt_rand(0, 35)};
                    }
                }
                break;
            case 4:
                for ((int)$i = 0; $i <= $length; $i++) {
                    if (mb_strlen($str) == $length) {
                        $str = $str;
                    } else {
                        $rand = "!@#$%^&*()_+=-~`";
                        $str .= $rand{mt_rand(0, 17)};
                    }
                }
                break;
            case 5:
                for ((int)$i = 0; $i <= $length; $i++) {
                    if (mb_strlen($str) == $length) {
                        $str = $str;
                    } else {
                        $rand = "123456789qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM!@#$%^&*()_+=-~`";
                        $str .= $rand{mt_rand(0, 52)};
                    }
                }
                break;
        }
        return $str;
    }

    /**
     * 获取用户IP
     * @return string
     */
    public static function getIp()
    {
        $arr_ip_header = array(
            'HTTP_CDN_SRC_IP',
            'HTTP_PROXY_CLIENT_IP',
            'HTTP_WL_PROXY_CLIENT_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        );
        $client_ip = 'unknown';
        foreach ($arr_ip_header as $key) {
            if (!empty($_SERVER[$key]) && strtolower($_SERVER[$key]) != 'unknown') {
                $client_ip = $_SERVER[$key];
                break;
            }
        }
        return $client_ip;
    }

    /**
     * 时间戳转换
     * @param int $timestamp
     * @param string $format
     * @return false|string
     */
    public static function realTime($timestamp = 0, $format = 'Y-m-d H:i:s')
    {
        return date($format, $timestamp);
    }

    /**
     * 验证手机号合法性
     * @param string $tel
     * @return bool
     * QAQ宇酱
     */
    public static function validateTel($tel = '')
    {
        if ($tel) {
            if (preg_match("/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/", $tel)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取关联数组的上一个键或下一个键
     * @param array $arr
     * @param string $now 当前键
     * @param string $do next/prev
     * @return string
     */
    public static function steps($arr = [], $now = '', $do = 'next')
    {
        if ($arr && $now && $do) {
            $arr = array_keys($arr);
            $now = array_search($now, $arr);
            if ($do == 'next' && isset($arr[$now + 1])) {
                return $arr[$now + 1];
            }
            if ($do == 'prev' && isset($arr[$now - 1])) {
                return $arr[$now - 1];
            }
        }
        return '';
    }

    /**
     * 将二维数组里某个键的值作为一维的键
     * @param string $key
     * @param array $arr
     * @return array
     */
    public static function changeKey($key = '', $arr = [])
    {
        $new = [];
        if ($key && $arr) {
            foreach ($arr as $v) {
                $new[$v[$key]] = $v;
            }
        }
        return $new;
    }

    /**
     * 一维数组转字符串
     * @param array $arr
     * @param string $link 链接符号
     * @param int $wrap 换行位置
     * @return string
     */
    public static function ArrToStr($arr = [], $link = '', $wrap = 0)
    {
        $str = '';
        if ($arr) {
            $count = count($arr);
            foreach ($arr as $k => $v) {
                if ($count - 1 == $k) {
                    $str .= $v;
                } elseif (($k + 1) % $wrap == 0) {
                    $str .= $v . '<br>';
                } else {
                    $str .= $v . $link;
                }
            }
        }
        return $str;
    }

    /**
     * 一维数组截取
     * @param array $arr
     * @param string $beginKey
     * @param string $endKey
     * @return array
     */
    public static function ArrSlice($arr = [], $beginKey = '', $endKey = '')
    {
        if ($arr && $beginKey) {
            $i = 0;
            $begin = 0;
            $end = 0;
            foreach ($arr as $k => $v) {
                if ($k == $beginKey) {
                    $begin = $i;
                }
                if ($k == $endKey) {
                    $end = $i;
                }
                $i++;
            }
            if ($begin && $end) {
                return array_slice($arr, $begin, $end - $begin + 1, true);
            }
            if ($begin) {
                return array_slice($arr, $begin, null, true);
            }
        }
        return $arr;
    }

    /**
     * 取出一维数组值并删除
     * @param array $data
     * @param string $key
     * @return bool|mixed
     */
    public static function ArrGetV(&$data = [], $key = '')
    {
        if ($data && $key && isset($data[$key])) {
            $value = $data[$key];
            unset($data[$key]);
            return $value;
        }
        return false;
    }

    /**
     * 设置数组的值
     * @param array $arr
     * @param string $value
     */
    public static function arrSet(&$arr = [], $value = '')
    {
        if ($arr) {
            foreach ($arr as &$v) {
                $v = $value;
            }
        }
    }

    /**
     * 给定目录,没有就创建
     * @param $name
     * @return bool
     */
    public static function mkDir($name)
    {
        if (file_exists($name)) {
            return true;
        }
        $dir = iconv("UTF-8", "GBK", $name);
        return mkdir($dir, 0777, true);
    }

    /**
     * 给定文件/目录,有就删除
     * @param $name
     * @return bool
     */
    public static function delDir($name)
    {
        if (file_exists($name)) {
            return unlink($name);
        }
        return true;
    }

    /**
     * 下载远程文件
     * @param $url
     * @param string $save_dir
     * @param string $filename
     * @param int $type
     * @return bool|string
     */
    public static function getRemoteFile($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);

        return $save_dir . $filename;
    }

}