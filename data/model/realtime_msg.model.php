<?php
/**
 * 消息通知记录
 *
 *
 *
 *
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
 * @since      File available since Release v1.1
 */
defined('InShopNC') or exit('Access Invalid!');

class realtime_msgModel extends Model{

    public function __construct() {
        parent::__construct();
    }

    /**
     * 增加记录
     *
     * @param
     * @return int
     */
    public function add($log_array) {
        $log_array['add_time'] = TIMESTAMP;
        $log_id = $this->table('realtime_msg')->insert($log_array);
        return $log_id;
    }

    /**
     * 增加邮件记录
     *
     * @param
     * @return int
     */
    public function addMail($number, $subject, $message) {
        $log_array = array();
        $log_array['to_id'] = $number;
        $log_array['subject'] = $subject;
        $log_array['log_msg'] = $message;
        $log_array['msg_type'] = 1;//类型:1邮件,2短信,3微信
        $log_id = $this->add($log_array);
        return $log_id;
    }

    /**
     * 增加短信记录
     *
     * @param
     * @return int
     */
    public function addShort($number, $message) {
        $log_array = array();
        $log_array['to_id'] = $number;
        $log_array['log_msg'] = $message;
        $log_array['msg_type'] = 2;//类型:1邮件,2短信,3微信
        $log_id = $this->add($log_array);
        return $log_id;
    }

    /**
     * 增加微信通知
     *
     * @param
     * @return int
     */
    public function addWx($mp_openid, $mp_msg_id, $data) {
        $log_array = array();
        $log_array['to_id'] = $mp_openid;
        $log_array['subject'] = $mp_msg_id;
        $log_array['log_msg'] = serialize($data);
        $log_array['msg_type'] = 3;//类型:1邮件,2短信,3微信
        $log_id = $this->add($log_array);
        return $log_id;
    }

    /**
     * 查询单条记录
     *
     * @param
     * @return array
     */
    public function getInfo($condition) {
        if (empty($condition)) {
            return false;
        }
        $result = $this->table('realtime_msg')->where($condition)->order('log_id desc')->find();
        return $result;
    }

    /**
     * 查询记录
     *
     * @param
     * @return array
     */
    public function getMsgList($condition = array(), $page = '', $limit = '', $order = 'log_id desc') {
        $result = $this->table('realtime_msg')->where($condition)->page($page)->limit($limit)->order($order)->select();
        return $result;
    }

    /**
     * 删除记录
     *
     * @param
     * @return bool
     */
    public function delMsg($log_array) {
        if (empty($log_array)) {
            return false;
        } else {
            $condition = array();
            $condition['log_id'] = $log_array['log_id'];
            unset($log_array['log_id']);
            $this->table('realtime_log')->insert($log_array);
            $result = $this->table('realtime_msg')->where($condition)->delete();
            return $result;
        }
    }

    /**
     * 发送邮件
     */
    public function send($to, $subject, $message) {
        require_once (BASE_DATA_PATH.DS.'api'.DS.'phpmailer'.DS.'class.phpmailer.php');
        require_once (BASE_DATA_PATH.DS.'api'.DS.'phpmailer'.DS.'class.smtp.php');
        $mail = new PHPMailer;
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->CharSet = "UTF-8";
        $mail->setLanguage('zh_cn');
        $mail->SMTPAuth = true;
        $mail->FromName = C('site_name');
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->addAddress($to);
        if (!empty($_POST['email_host']) && !empty($_POST['email_pass'])) {
            //$mail->SMTPDebug = 1;
            $mail->Host = trim($_POST['email_host']);
            $mail->Port = trim($_POST['email_port']);
            $mail->Username = trim($_POST['email_addr']);
            $mail->Password = trim($_POST['email_pass']);
        } else {
            $mail->Host = C('email_host');
            $mail->Port = C('email_port');
            $mail->Username = C('email_addr');
            $mail->Password = C('email_pass');
        }
        if ($mail->Port >= 465) {//支持SSL
            $mail->SMTPSecure = 'ssl';
        }
        $mail->From = $mail->Username;
        if (!$mail->send()) {//https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting
            throw new Exception("SMTP服务器连接失败。");//$mail->ErrorInfo
            return false;
        } else {
            return true;
        }
    }
}
