# easycurl
There are a lot of popular of the PHP cURL extension wrapper, but these library interface is too complex.So create a more friendly and simple curl Library.
```php
require __DIR__ . '/vendor/autoload.php';

use \Easycurl\Easycurl;

$curl = new Easycurl(array(
                /*
                    des: need fetch data of url
                    @var string
                    specify: must 
                */
                "url"=>"http://www.baidu.com",
                /*
                    des: set user agent
                    @var string
                    specify: option 
                */
                "userAgent"=>"",
                /*
                    des: fetch method
                    @var string
                    specify: option , default get
                */
                "method"=>"get",
                /*
                    des: set fetch request header
                    @var array
                    specify: option 
                */
                "header"=>array(
                    "Host" => "www.baidu.com",
                    "Connection" => "keep-alive",
                    "Accept-Encoding" => "gzip"
                ),
                /*
                    des: set referrer
                    @var string
                    specify: option 
                */
                "referrer"=>"",
                /*
                    des: set request data
                    @var array
                    specify: option 
                */
                "data"=> array()
            )
        );

$curl->start();

```

```php