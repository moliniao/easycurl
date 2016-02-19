<?php

namespace App\Console\Commands;
use App\model\DscunUser;
use Illuminate\Console\Command;
use \Curl\Curl;
use Log;
class Dscun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dscun:fetch';
    protected $config = array(
        "url"=>"http://dscapp.dscun.link/api/feeds/feeds_id/",
        "userAgent"=>"单身村 2.2.2 rv:2.2.2.2 (iPhone; iPhone OS 9.2; zh_CN)",
        "header"=>array(
            "meet-token" => "d0eab6f0d7aa3262c63a1d668780639b",
            "Host" => "dscapp.dscun.link",
            "Connection" => "keep-alive",
            "client-version" => "2.2.2.2",
            "app-version" => "2.0.1",
            "Accept-Encoding" => "gzip"
        ),
        "referrer"=>""
    );

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取单身村数据';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Create a new command instance.
     *
     * @return void
     */
    private function getUserInfo($getUrl,$userId){
        $hadUser = DscunUser::where("user_id",$userId)->get()->first();
        $url = $getUrl.$userId;
        $that = $this;
        if( empty( $hadUser ) ){
            $curlConfig = $this->config;
            $curl = new Curl();
            if( is_array( $curlConfig["header"] ) ){
                foreach ($curlConfig["header"] as $key => $value) {
                     $curl->setHeader($key,$value);
                     //解压gzip
                     if( $value === "gzip" ){
                        $curl->setOpt(CURLOPT_ENCODING , 'gzip');
                     }
                }
            }
            //设置reffer
            if( isset( $curlConfig["referrer"] ) ){
                $curl->setReferrer( $curlConfig["referrer"] );
            }
            //设置userAgent
            if( isset( $curlConfig["userAgent"] ) ){
                $curl->setUserAgent( $curlConfig["userAgent"] );
            }
            $curl->success(function($instance) use($curlConfig,$that,$url){
                Log::info('success fetch url '. $url);
                $listData = json_decode( $instance->response,true );
                $userInfo = $listData['data'];
                //动态修改token
                //$that->config["header"]['meet-token'] = md5($userInfo["installation_id"]);
                $that->saveUserInfo($userInfo);
            });
            $curl->error(function($instance) use($curlConfig,$url){
                Log::info('fail fetch url '. $url .'Error: ' . $instance->errorCode . ': ' . $instance->errorMessage);
            });
            Log::info('start fetch url '. $url);
            $curl->get($url);
        }
    }
    private function saveUserInfo($userInfo){
        $userInfo["user_id"] = $userInfo["id"];
        DscunUser::create($userInfo);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $curlConfig = $this->config;
        $that = $this;
        for ($i=10732; $i >=0 ; $i = $i-20) {
            sleep(5);
            $curlConfig["url"] = "http://dscapp.dscun.link/api/feeds/feeds_id/" . $i . "/count/-20";
            $curl = new Curl();
            //设置头信息
            if( is_array( $curlConfig["header"] ) ){
                foreach ($curlConfig["header"] as $key => $value) {
                     $curl->setHeader($key,$value);
                     //解压gzip
                     if( $value === "gzip" ){
                        $curl->setOpt(CURLOPT_ENCODING , 'gzip');
                     }
                }
            }

            //设置reffer
            if( isset( $curlConfig["referrer"] ) ){
                $curl->setReferrer( $curlConfig["referrer"] );
            }
            //设置userAgent
            if( isset( $curlConfig["userAgent"] ) ){
                $curl->setUserAgent( $curlConfig["userAgent"] );
            }
            $curl->success(function($instance) use($curlConfig,$that){

                Log::info('success fetch url '. $curlConfig["url"]);
                $listData = json_decode( $instance->response,true );

                foreach ($listData["data"]['feeds'] as $key => $talkInfo) {
                    $that->getUserInfo("http://dscapp.dscun.link/api/user/",$talkInfo["user_id"]);

                    foreach ($talkInfo["comment"] as $userKey => $userInfo) {
                        $that->getUserInfo("http://dscapp.dscun.link/api/user/",$userInfo["user_id"]);
                    }
                }
            });
            $curl->error(function($instance) use($curlConfig){

                Log::info('fail fetch url '. $curlConfig["url"].'Error: ' . $instance->errorCode . ': ' . $instance->errorMessage);
            });
            Log::info('start fetch url '. $curlConfig["url"]);
            $curl->get($curlConfig["url"]);
        }
       
    }
}
