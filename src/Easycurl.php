<?php

namespace App\Console\Commands\easycurl\src;
use \Curl\Curl;
class EasyCurl 
{
    const VERSION = '1.0.0';
    public $curl ;
    public $config;
    /**
     * Construct
     *
     * @access public
     * @param  $config
     * @throws \ErrorException
     */
    public function __construct( $config = array() ){
        // set fetch url
        if( !isset( $config["url"] ) ){
            throw new \ErrorException('url must specify');
        }
        $this->curl = new Curl();
        //set method
        if( !isset( $config["method"] ) ){
            $config["method"] = "get";
        }
        $config["method"] = strtolower($config["method"]);
        //set header 
        if( is_array( $config["header"] ) ){
            foreach ($config["header"] as $key => $value) {
                 $this->curl->setHeader($key,$value);
                 //解压gzip
                 if( $value === "gzip" ){
                    $this->curl->setOpt(CURLOPT_ENCODING , 'gzip');
                 }
            }
        }
        //set referrer
        if( isset( $config["referrer"] ) ){
            $this->curl->setReferrer( $config["referrer"] );
        }
        //set userAgent
        if( isset( $config["userAgent"] ) ){
            $this->curl->setUserAgent( $config["userAgent"] );
        }
        // set timeout
        if( isset( $config["timeout"] ) ){
            $this->curl->setTimeout( $config["timeout"] );
        }
        $this->config = $config;
    }
    /**
     * success
     *
     * @access public
     * @param  $callback
     */
    public function success($callback){
        $this->curl->success($callback);
    }
    /**
     * Error
     *
     * @access public
     * @param  $callback
     */
    public function error($callback){
        $this->curl->error($callback);
    }

    /**
     * get php-curl-class
     *
     * @access public
     *
     * @return obj
     */
    public function getCurl(){
        return $this->curl;
    }
    public function start(){
        $method = $this->config["method"];
        $url = $this->config["url"];
        $data = isset( $this->config["data"] ) ? $this->config["data"] : array();
        $this->curl->{$method}($url, $data); 
    }
    public function end(){
        $this->curl->close();        
    }

}
