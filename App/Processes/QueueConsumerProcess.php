<?php
namespace App\Processes;

use App\Models\OrderModel;
use App\Models\WechatUserModel;
use App\Queues\OrderCreateQueue;
use App\Queues\OrderTopUpCheckQueue;
use App\Queues\OrderTopUpConfirmQueue;
use App\Queues\OrderTopUpFailNotifyCustomerServiceQueue;
use App\Queues\OrderTopUpFailNotifyUserQueue;
use App\Queues\OrderTopUpFailRefundQueue;
use App\Queues\OrderTopUpSuccessNotifyUserQueue;
use App\Util\Common;
use App\Util\Paynet;
use App\Util\RedisQueue;
use App\Util\WechatOfficialAccount;
use App\Util\WechatPay;
use EasySwoole\Queue\Job;
use EasySwoole\Component\Process\AbstractProcess;

class QueueConsumerProcess extends AbstractProcess
{
    protected $appConfig;

    protected function run($arg)
    {
        echo 'QueueConsumer 进程启动'.PHP_EOL;

        $this->appConfig = \EasySwoole\EasySwoole\Config::getInstance()->getConf('APP');

        //【订单创建队列】队列消费
        go(function (){
            OrderCreateQueue::getInstance()->consumer()->listen(function (Job $job){
                $orderData = $job->getJobData();

                $order = OrderModel::create($orderData);
                $order->save();

                OrderCreateQueue::getInstance()->consumer()->confirm($job); //任务确认

                //记录
                \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                    'input' => $orderData,
                    'output' => $order
                ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.orderQueryQueue');
            });
        });

        //【订单充值检查队列】队列消费
        go(function (){
            OrderTopUpCheckQueue::getInstance()->consumer()->listen(function (Job $job){
                $order = $job->getJobData();

                $paynetCheckRes = Paynet::transactionCheck($order);
                if(isset($paynetCheckRes['transaction'])){ //充值检查成功
                    $order->top_up_ext_order_number = $paynetCheckRes['transaction']['id'];
                    $order->top_up_status = OrderModel::TOP_UP_STATUS_CHECK;
                    $order->update();
                    
                    //生产订单充值确认任务
                    RedisQueue::produceTrustedTask(OrderTopUpConfirmQueue::getInstance(), $order);

                    OrderTopUpCheckQueue::getInstance()->consumer()->confirm($job); //任务确认

                    //记录
                    \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                        'input' => $order,
                        'output' => []
                    ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.orderTopUpCheckQueue');
                }
            });
        });

        //【订单充值确认队列】队列消费
        go(function (){
            OrderTopUpConfirmQueue::getInstance()->consumer()->listen(function (Job $job){
                $order = $job->getJobData();

                $transactionConfirmRes = Paynet::transactionConfirm($order);
                if(isset($transactionConfirmRes['transaction'])){ //充值确认成功
                    $order->top_up_ext_order_number = $transactionConfirmRes['transaction']['id'];
                    $order->top_up_status = OrderModel::TOP_UP_STATUS_CONFIRM;
                    $order->update();

                    OrderTopUpConfirmQueue::getInstance()->consumer()->confirm($job); //任务确认

                    //记录
                    \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                        'input' => $order,
                        'output' => $transactionConfirmRes
                    ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.orderTopUpConfirmQueue');
                }
            });
        });

        //【充值订单成功通知用户】队列消费
        go(function (){
            OrderTopUpSuccessNotifyUserQueue::getInstance()->consumer()->listen(function (Job $job){
                $order = $job->getJobData();

                $result = null;

                switch ($order->resource) {
                    case 'WECHAT_OFFICIAL_ACCOUNT':
                        //通知微信公众号用户
                        $openid = WechatUserModel::create()->where('uuid', $order->uuid)->val('openid');
                        //用户是否关注
                        $wechatUser = WechatOfficialAccount::getServiceAccount()->user->get($openid);

                        $isSubscribe = $wechatUser['subscribe'];
                        if ($isSubscribe) {
                            $result = WechatOfficialAccount::getServiceAccount()->templateMessage->send([
                                'touser' => $openid,
                                'template_id' => 'ubN0C4pQmQPu6R-gKKj4Js-hl4-p_-KxHbNu8-cPn1g',
                                'url' => $this->appConfig['appUrl'].'/index/member?item_name=topup_details&pay_order_number='.$order->pay_order_number,
                                'data' => [
                                    'first' => '您已充值成功',
                                    'keyword1' => '充值成功',
                                    'keyword2' => $order->pay_created_at,
                                    'keyword3' => $order->pay_body,
                                    'keyword4' => sprintf("%.2f", $order->pay_real_amount / 100).'元',
                                    'keyword5' => '微信支付',
                                    'remark' => '充值到账时间请以手机短信通知为准。感谢您使用全酋付！祝您生活愉快！',
                                ],
                            ]);
                        }
                        
                        break;
                    
                    default:
                        # code...
                        break;
                }

                OrderTopUpSuccessNotifyUserQueue::getInstance()->consumer()->confirm($job); //任务确认

                //记录
                \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                    'input' => $order,
                    'output' => $result
                ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.orderTopUpSuccessNotifyUserQueue');
            });
        });

        //【充值订单失败通知用户】队列消费
        go(function (){
            OrderTopUpFailNotifyUserQueue::getInstance()->consumer()->listen(function (Job $job){
                $order = $job->getJobData();

                $result = null;

                switch ($order->resource) {
                    case 'WECHAT_OFFICIAL_ACCOUNT':
                        //通知微信公众号用户
                        $openid = WechatUserModel::create()->where('uuid', $order->uuid)->val('openid');
                        //用户是否关注
                        $wechatUser = WechatOfficialAccount::getServiceAccount()->user->get($openid);

                        $isSubscribe = $wechatUser['subscribe'];
                        if ($isSubscribe) {
                            $result = WechatOfficialAccount::getServiceAccount()->templateMessage->send([
                                'touser' => $openid,
                                'template_id' => 'R7Z5zZxuvjCb319C1PEvY5AQvnVBLVfU1CuYrDPAbIA',
                                'url' => $this->appConfig['appUrl'].'/index/member?item_name=topup_details&pay_order_number='.$order->pay_order_number,
                                'data' => [
                                    'first' => '尊敬的全酋付用户，您所购买的充值产品未充值成功。金额已退回，请注意查收！',
                                    'keyword1' => "订单不成功可能有以下几个原因；\r\n
                                    1.号码异常:如号码错误，过期，欠费，付费模式，未实名认证等；\r\n
                                    2.运营商方面:维护, 或流量包冲突；\r\n
                                    3.网络问题，可以稍后再试；\r\n
                                    如有其他问题请联系客服微信 (darling1920) ,24小时在线。",
                                    'keyword2' => ($order->pay_real_amount/100).'元',
                                    'remark' => '如未收到退款，请联系客服。感谢您使用全酋付！祝您生活愉快！',
                                ],
                            ]);
                        }
                        
                        break;
                    
                    default:
                        # code...
                        break;
                }

                OrderTopUpFailNotifyUserQueue::getInstance()->consumer()->confirm($job); //任务确认

                //记录
                \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                    'input' => $order,
                    'output' => $result
                ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.orderTopUpSuccessNotifyUserQueue');
            });
        });

        //【充值订单失败通知客服】队列消费
        go(function (){
            OrderTopUpFailNotifyCustomerServiceQueue::getInstance()->consumer()->listen(function (Job $job){
                $order = $job->getJobData();

                $result = null;

                //通知微信公众号客服
                $openid = 'oMnqI57s7sKOZ7n0VotNrmIDy0Lo';
                //客服是否关注
                $wechatUser = WechatOfficialAccount::getServiceAccount()->user->get($openid);

                $isSubscribe = $wechatUser['subscribe'];
                if($isSubscribe){
                    $result = WechatOfficialAccount::getServiceAccount()->templateMessage->send([
                        'touser' => $openid,
                        'template_id' => 'R7Z5zZxuvjCb319C1PEvY5AQvnVBLVfU1CuYrDPAbIA',
                        'url' => '',
                        'data' => [
                            'first' => '有新到失败订单信息',
                            'keyword1' => "支付交易号【{$order->pay_order_number}】交易失败!请前往后台处理",
                            'keyword2' => '充值金额（人民币）：'.($order->pay_real_amount/100).'元',
                            'remark' => '此信息仅限客服查收',
                        ],
                    ]);
                }

                OrderTopUpFailNotifyCustomerServiceQueue::getInstance()->consumer()->confirm($job); //任务确认
                
                //记录
                \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                    'input' => $order,
                    'output' => $result,
                ]), 'wechatServiceAccountPushTask.topupOrderSuccessNotifyCustomerService');
            });
        });

        //【充值订单失败退款】队列消费
        go(function (){
            OrderTopUpFailRefundQueue::getInstance()->consumer()->listen(function (Job $job){
                $order = $job->getJobData();
                //退款
                $wechatPay = (new WechatPay)->getWechatPay();
                $pay_refund_order_number = Common::generateOrderNumber('TK');
                $notifyUrl = $this->appConfig['appUrl'].'/wechatPay/orderNofify';

                $refund = new \EasySwoole\Pay\WeChat\RequestBean\Refund();
                $refund->setOutTradeNo($order->pay_order_number);
                $refund->setOutRefundNo($pay_refund_order_number);
                $refund->setTotalFee($order->pay_total_amount);
                $refund->setRefundFee($order->pay_real_amount);
                $refund->setNotifyUrl($notifyUrl);
                $result = $wechatPay->refund($refund);

                $order->pay_refund_order_number = $pay_refund_order_number;
                $order->pay_status = OrderModel::PAY_STATUS_REFUND;
                $order->pay_refund_time = time();
                $order->top_up_status = OrderModel::TOP_UP_STATUS_FAIL;

                $order->update();
                
                OrderTopUpFailRefundQueue::getInstance()->consumer()->confirm($job); //任务确认

                 //记录
                 \EasySwoole\EasySwoole\Logger::getInstance()->info(json_encode([
                    'input' => $order,
                    'output' => $result
                ], JSON_UNESCAPED_UNICODE), 'QueueConsumerProcess.orderTopUpFailRefundQueue');
            });
        });
    }
}