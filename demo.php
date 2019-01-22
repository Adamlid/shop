<?php
/**
 * @description:
 * @author: plh
 * @Date: 2018/11/28 13:47
 */

namespace app\api\controller\v1;

use think\Db;
use think\Request;
use app\lib\exception\HuiChuanException;

class OrderReturn
{
    /**
     * 订单回传
     * @param Request $request
     * @return \think\response\Json
     * @throws \app\lib\exception\HuichuanException
     * @throws \app\lib\exception\ProductException
     */
    public function get(Request $request)
    {
        $param = $this->request_param = input('post.', [], 'trim');
    try{

        // 参数校验
        // 校验参数是否完整
        if (!isset($param['partner_id']) || !isset($param['data']) || !isset($param['version']) || !isset($param['validation'])){
            throw new HuiChuanException([
                'errorCode' => 1001,
                'msg' => "参数不完整",
            ]);
        }

        // 校验数据格式是否正确
        $json_str = base64_decode($param['data']);
        json_decode($json_str);
        if(!json_last_error() == JSON_ERROR_NONE){
            throw new HuiChuanException([
                'errorCode' => 1004,
                'msg' => "data 数据格式有误",
            ]);
        }
        // 校验版本号

        // 校验partner_id是否存在
        if ($param['partner_id'] != 560006661001){
            throw new HuiChuanException([
                'errorCode' => 1003,
                'msg' => "partner_id不存在或无效",
            ]);
        }

        // 校验签名
        $sign = md5($param['data'] . $param['partner_id'] . 123456);
        //var_dump($param['data']);var_dump($param['partner_id']);var_dump($sign);var_dump($param['validation']);die;
        if ($sign != $param['validation']){
            throw new HuiChuanException([
                'errorCode' => 1005,
                'msg' => "签名错误",
            ]);
        }

        // 校验data是否符合规范
        $this->data = json_decode(base64_decode(trim($this->request_param['data'])), true);
        if (!is_array( $this->data) || !isset( $this->data['orders']) || !is_array( $this->data['orders'])){
            throw new HuiChuanException([
                'errorCode' => 1004,
                'msg' => "data参数错误",
            ]);
        }elseif (count( $this->data['orders']) > 100){
            throw new HuiChuanException([
                'errorCode' => 1008,
                'msg' => "报文数量超过限制",
            ]);
        }
    }catch (HuiChuanException $e)
    {
        return json([
            'success' => false,
            'error_code' => $e -> errorCode,
            'error_msg' =>  $e -> msg,
            'remark' => "",
            'error_info' => ""
        ]);
    }


        $errorMc=[];
        $i = 0;
//    var_dump($errorMc);die;
        $now = date('Y-m-d H:i:s',time());
        foreach ($this->data['orders'] as $k1 => $v1){
            foreach ($v1['order_info']['sub_mailno'] as $k2 => $v2) {

                $order_no = isset($v1['order_no']) ? $v1['order_no'] : '';
                $serial_no = isset($v1['serial_no']) ? $v1['serial_no'] : '';
                $order_status = isset($v1['order_status']) ? $v1['order_status'] : '';
                $status_msg = isset($v1['status_msg']) ? $v1['status_msg'] : '';
                $mailno = isset($v1['order_info']['mailno']) ? $v1['order_info']['mailno'] : '';
                $sub_mailno = isset($v2) ? $v2 : '';
                $piece = isset($v1['order_info']['piece']) ? $v1['order_info']['piece'] : '';
                $sender_site_code = isset($v1['order_info']['sender_site_code']) ? $v1['order_info']['sender_site_code'] : '';
                $receiver_site_code = isset($v1['order_info']['receiver_site_code']) ? $v1['order_info']['receiver_site_code'] : '';
                $settle_weight = isset($v1['order_info']['settle_weight']) ? $v1['order_info']['settle_weight'] : '';
                $settle_volume = isset($v1['order_info']['settle_volume']) ? $v1['order_info']['settle_volume'] : '';
                $collect_fee = isset($v1['order_info']['collect_fee']) ? $v1['order_info']['collect_fee'] : '';
                $insurance_fee = isset($v1['order_info']['insurance_fee']) ? $v1['order_info']['insurance_fee'] : '';
                $freight_charge = isset($v1['order_info']['freight_charge']) ? $v1['order_info']['freight_charge'] : '';
                $support_value = isset($v1['order_info']['support_value']) ? $v1['order_info']['support_value'] : '';
                $pack_type = isset($v1['order_info']['sub_print_info']['pack_type']) ? $v1['order_info']['sub_print_info']['pack_type'] : '';
                $service_type = isset($v1['order_info']['sub_print_info']['service_type']) ? $v1['order_info']['sub_print_info']['service_type'] : '';
                $shipping_method = isset($v1['order_info']['sub_print_info']['shipping_method']) ? $v1['order_info']['sub_print_info']['shipping_method'] : '';
                $sub_type = isset($v1['order_info']['sub_print_info']['sub_type']) ? $v1['order_info']['sub_print_info']['sub_type'] : '';
                $weight_vol = isset($v1['order_info']['sub_print_info']['weight_vol']) ? $v1['order_info']['sub_print_info']['weight_vol'] : '';
                $final_center_name = isset($v1['order_info']['sub_print_info']['final_center_name']) ? $v1['order_info']['sub_print_info']['final_center_name'] : '';
                $final_first_site_name = isset($v1['order_info']['sub_print_info']['final_first_site_name']) ? $v1['order_info']['sub_print_info']['final_first_site_name'] : '';
                $final_second_site_name = isset($v1['order_info']['sub_print_info']['final_second_site_name']) ? $v1['order_info']['sub_print_info']['final_second_site_name'] : '';
                $receiver_address = isset($v1['order_info']['sub_print_info']['receiver_address']) ? $v1['order_info']['sub_print_info']['receiver_address'] : '';
                $send_date = isset($v1['order_info']['sub_print_info']['send_date']) ? $v1['order_info']['sub_print_info']['send_date'] : '';
                $route = isset($v1['order_info']['sub_print_info']['route']) ? $v1['order_info']['sub_print_info']['route'] : '';
                $print_num = isset($v1['order_info']['sub_print_info']['print_num']) ? $v1['order_info']['sub_print_info']['print_num'] : '';
                $less_than = isset($v1['order_info']['sub_print_info']['less_than']) ? $v1['order_info']['sub_print_info']['less_than'] : '';

                $sql = "select mail_no from wlydxx where order_number = '{$order_no}'";
                $res2 =  $result = Db::query($sql);
                $res = array_column($res2, 'mail_no');
                if (!in_array($mailno,$res)){
                    $errorMc['success'] = "true" ;
                    $errorMc['error_code'] = "0000" ;
                    $errorMc['error_msg'] = "成功" ;
                    $errorMc['remark'] = '' ;
                    $errorMc['error_info'][$i]['serial_no'] = $serial_no ;
                    $errorMc['error_info'][$i]['error_code'] = 2001 ;
                    $errorMc['error_info'][$i]['error_msg'] = '运单号异常' ;
                    $i++;
                    continue;
                }

                $sql="insert into wlydhc(hc_time,order_no,serial_no,order_status,status_msg,mailno,sub_mailno,piece,sender_site_code,receiver_site_code,settle_weight,settle_volume,collect_fee,insurance_fee,freight_charge,support_value,pack_type,service_type,shipping_method,sub_type,weight_vol,sender_site_name,final_center_name,final_first_site_name,final_second_site_name,receiver_address,send_date,route,print_num,less_than) values('$now','{$order_no}','{$serial_no}','{$order_status}','{$status_msg}','{$mailno}','{$sub_mailno}','{$piece}','{$sender_site_code}','{$receiver_site_code}','{$settle_weight}','{$settle_volume}','{$collect_fee}','{$insurance_fee}','{$freight_charge}','{$support_value}',
'{$pack_type}','{$service_type}','{$shipping_method}','{$sub_type}','{$weight_vol}',
'上海快运总部','{$final_center_name}','{$final_first_site_name}','{$final_second_site_name}','{$receiver_address}','{$send_date}','{$route}','{$print_num}','{$less_than}')";
                Db::execute($sql);
            }
        }

    if (!empty($errorMc)){
        $errorMc['error_info'] = array_unique($errorMc['error_info'], SORT_REGULAR);
        return json($errorMc);
    }

    return json([
        'success' => true,
        'error_code' => '0000',
        'error_msg' => "成功",
        'remark' => "",
        'error_info' => "",
    ]);
    }
}