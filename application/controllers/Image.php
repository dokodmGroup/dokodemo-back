<?php

class ImageController extends \Our\Controller_AbstractRest {

    /** 
     * 改写分发函数
     */
    public function indexAction()
    {
        switch ($this->_method) {
            case 'GET':
                $action = 'index';
                break;
            case 'POST':
                $action = 'post';
                break;
            case 'PUT':
                $action = 'put';
                break;
            case 'delete':
                $action = 'delete';
                break;
            default:
                break;
        }
        if (method_exists($this, $action)) {
            $result = $this->$action($this->_request);
            if (is_array($result)) {
                try {
                    header('X-Info: ' . urlencode($result[1]));
                    http_response_code($result[0]);
                    echo json_encode($result[2]);
                } catch (\Exception $e) {
                    header('X-Info: ' . urlencode('Error: The return result is array and do not validated'));
                    http_response_code(500);
                    echo json_encode([]);
                }
            } else {
                return $result;
            }
        } else {
            throw new \Yaf\Exception\LoadFailed\Action('请求方法响应动作不存在');
        }
    }

    public function put(\Yaf\Request\Http $request) {
        session_start();
        // header('Access-Control-Allow-Origin: *');
        $session    = session_id();
        $input_path = 'php://input';
        self::createTempfileDirectory();
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
        $img_info = getimagesize($input_path);
        // 兼容代码块
        // 但会增加fopen和fclose带来的开销
        if ($img_info === false) {
            $img_info = self::getImageInfo($input_path);
        }
        // 兼容代码块STOP
        if ($img_info === false) {
            return array(400, '非法图片格式' . $err_msg);
        } elseif (empty($img_info['mime'])) {
            return array(500, '不受支持的图片类型，请等候解决方案！');
        } elseif (self::getImgExt($img_info, $ext) === false) {
            return array(500, '图片信息非法');
        } else {
            $src  = fopen($input_path, 'r');
            $path = "./temp/{$session}";
            if (file_exists($path) === false) {
                mkdir($path);
            }
            $main_name = self::randomFileName();
            $name      = "{$main_name}.{$ext}";
            $dst       = fopen("{$path}/{$name}", 'w');
            $result    = stream_copy_to_stream($src, $dst);
            fclose($src);
            fclose($dst);
            $data = [
                'size'    => $result,
                'session' => $session,
                // 'type' => $_SERVER['CONTENT_TYPE'],
                'name'    => $name,
                'width'   => $img_info[0],
                'height'  => $img_info[1],
                'mime'    => $img_info['mime'],
                'info'    => $img_info,
            ];
            return array(200, '上传成功', $data);
        }
    }

    private static function getImgExt(array $imgInfo, string &$ext = null): bool
    {
        $tmp_ext = '';
        if (isset($imgInfo[2]) &&
            is_int($imgInfo[2]) &&
            self::getImgExtFromPhpInside($imgInfo[2], $tmp_ext) === true) {
            $ext = $tmp_ext;
            return true;
        } elseif (isset($imgInfo['mime']) && is_string($imgInfo['mime'])) {
            $result = self::getImgExtFromMime($imgInfo['mime'], $tmp_ext);
            $ext    = empty($tmp_ext) ? '' : $tmp_ext;
            return $result;
        } else {
            return false;
        }
    }

    private static function getImgExtFromPhpInside(int $code, string &$ext = null): bool
    {
        $inside = [
            'gif', 'jpg', 'png', 'swf', 'psd',
            'bmp', 'tiff', 'tiff', 'jpc', 'jp2',
            'jpx', 'jb2', 'swc', 'iff', 'wbmp',
            'xbm',
        ];
        $code -= 1;
        if (isset($inside[$code])) {
            $ext = $inside[$code];
            return true;
        } else {
            return false;
        }
    }
    private static function getImgExtFromMime(string $mime, string &$ext = null): bool
    {
        if (preg_match("/(?<=image\/)\b\w+\b/", $mime, $result)) {
            switch ($result[0]) {
                case 'jpeg':
                case 'jpg':
                    $ext = 'jpg';
                    break;
                default:
                    $ext = $result[0];
                    break;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 提交修改并上传到 upyun
     * Kanzaki Tsukasa 2017-04-06
     * @param array $image 要求有'name'字段的二维数组表
     * @param array $notProcessImage 因出错而没有处理的图片表
     * @return void 无返回
     */
    public static function commit(array $images, array &$processImage = null, array &$notProcessImage = null)
    {
        $notProcessImage = [];
        $processImage    = [];
        if (empty($images)) {
            return;
        }
        session_start();
        $session   = session_id();
        foreach ($images as $value) {
            $path    = "./temp/{$session}/{$value['name']}";
            $err_msg = '';
            if (self::uploadImage($path, $err_msg) === false) {
                $value['err_msg']  = $err_msg;
                $notProcessImage[] = $value;
            } else {
                $img_info        = getimagesize($path);
                $value['width']  = $img_info[0];
                $value['height'] = $img_info[1];
                $value['url']    = "/source/{$value['name']}";
                $processImage[]  = $value;
            }
        }
    }

    private static function getImageInfo(string $address, string &$errorMsg = null): array
    {
        try {
            $src      = fopen($address, 'r');
            $dst_path = './temp/' . uniqid();
            $dst      = fopen($dst_path, 'w');
            $result   = stream_copy_to_stream($src, $dst);
            fclose($src);
            fclose($dst);
            if ($result > 0) {
                $info = getimagesize($dst_path);
                unlink($dst_path);
                if ($info !== false) {
                    return (array) $info;
                } else {
                    $errorMsg = '';
                    return [];
                }
            } else {
                unlink($dst_path);
                $errorMsg = '复制过程出现意外错误';
                return [];
            }
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            return [];
        }
    }

    private static function uploadImage(string $address, string &$errorMsg = null): bool
    {
        
        return true;
    }

    private static function createTempfileDirectory(string &$errorMsg = null): bool
    {
        $mkrs = false;
        try {
            if (file_exists('./temp') === false) {
                $mkrs = @mkdir('./temp');
                $err_msg = error_get_last();
                if ($mkrs === false) {
                    $errorMsg = error_get_last();
                }
            }
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
            return false;
        }
        return $mkrs;
    }

    private static function copyStandardInputToFile()
    {
        try {
            $src  = fopen('php://input', 'r');
            $path = "./temp/";
            if (file_exists($path) === false) {
                mkdir($path);
            }
            $main_name = time() . rand(10000, 99999) . uniqid();
            $name      = "{$main_name}";
            $dst       = fopen("{$path}/{$name}", 'w');
            $result    = stream_copy_to_stream($src, $dst);
            return json(['size' => $result]);
        } catch (\Exception $e) {
            return json(['error' => 'yes']);
        }
    }

    private static function randomFileName(): string
    {
        return uniqid(time() . rand(10000, 99999));
    }
}