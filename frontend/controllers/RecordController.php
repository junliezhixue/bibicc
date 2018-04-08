<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class RecordController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionChart()
    {
	return $this->render('chart');
    }

    /**
     * 发送邮件类 参数 $data 需要三个必填项 包括 邮件主题`$data['subject']`、接收邮件的人`$data['to']`和邮件内容 `$data['content']`
     * @param Array $data
     * @return bool $result 发送成功 or 失败
     */
    public function actionSendmail()
    {
        $ren = Yii::$app->mailer->compose()
        ->setFrom('1412279986@qq.com')
        ->setTo('2273449524@qq.com')
        ->setSubject('测试连接')
        ->setTextBody('测试一下邮件发送');
	if ($ren->send()) {
		echo 'success';
	}else {
		echo 'failse';
	}
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionUpload()
    {
        $zipfile = $_FILES['file'];
	if (!$zipfile) {
            return json_encode(['code' => 500, 'msg' => '缺少参数']);die;
        }
        $type = $zipfile['type'];
        $ziparr = ['application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/octet-stream'];
        if (!in_array($type, $ziparr)) {
            return json_encode(['code' => 300, 'msg' => '请上传zip格式文件']);
        }
        //解压开始的时间
        $starttime = explode(' ',microtime());
        //打开压缩包
        $resource = zip_open(iconv("utf-8", "gb2312", $zipfile['tmp_name']));
        $i = 0;
        $data = [];
        //遍历读取压缩包里面的一个个文件
        while ($dir_resource = zip_read($resource)) {
            //如果能打开则继续
            if (zip_entry_open($resource,$dir_resource)) {
                //获取当前项目的名称,即压缩包里面当前对应的文件名
                $file_name = zip_entry_name($dir_resource);
                $file_type = explode('.', $file_name);
                $file_type_arr = ['jpg', 'jpeg', 'gif', 'png', 'wbmp'];
                if (!array_key_exists(1, $file_type) || !in_array($file_type[1], $file_type_arr)) {
                    array_push($data, ['file' => $file_name, 'error' => '格式不正确']);
                    continue;
                }
                //以最后一个“/”分割,再用字符串截取出路径部分
                $file_path = './uploadimgs/';
                //如果路径不存在，则创建一个目录，true表示可以创建多级目录
                if(!is_dir($file_path)){
                    mkdir($file_path,0777,true);
                }
                if (is_dir($file_path . $file_name)) {
                    unlink($file_path . $file_name);
                }
                //读取这个文件
                $file_size = zip_entry_filesize($dir_resource);
                //最大读取2M，如果文件过大，跳过解压，继续下一个
                if($file_size > (1024 * 1024 * 2)){
                    array_push($data, ['file' => $file_name, 'error' => '文件过大']);
                    continue;
                }
                $file_content = zip_entry_read($dir_resource,$file_size);
                //关闭当前
		$ifp = fopen($file_path . $file_name, "wb");
        	fwrite($ifp, $file_content);
        	fclose($ifp);
                zip_entry_close($dir_resource);
            }
        }
        //关闭压缩包
        zip_close($resource);
        //解压结束的时间
        $endtime = explode(' ',microtime());
        $thistime = $endtime[0] + $endtime[1] - ($starttime[0] + $starttime[1]);
        $thistime = round($thistime,3); //保留3为小数
        return json_encode(['code' => 200, 'msg' => '上传完成', 'data' => $data]);
    }
}
