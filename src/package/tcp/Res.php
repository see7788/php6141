<?php
namespace php6141\tcp;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Response;

class Res extends Response
{
    private TcpConnection $connection;

    public function __construct(TcpConnection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    function ext_withBody_websocket(string $socketUrl, array $initInfo): self
    {
        $info = json_encode($initInfo);
        //$port = $this->getLocalPort();
        $html = "
                <script crossorigin='anonymous' src='https://cdn.bootcdn.net/ajax/libs/vue/2.6.12/vue.min.js'></script>
                <div id='app'>{{message}}</div>
                <script>
                    //判断当前浏览器是否支持WebSocket
                    if (!window.WebSocket) {
                        alert('当前浏览器 Not support websocket')
                    }
                    let wsUrl='$socketUrl'
                    let websocket = new WebSocket(wsUrl);
                    let heartCheck = {
                            timeout: 60000,//60秒
                            timeoutObj: null,
                            serverTimeoutObj: null,
                            reset: function(){
                                clearTimeout(this.timeoutObj);
                                clearTimeout(this.serverTimeoutObj);
                                return this;
                            },
                            start: function(){
                                this.timeoutObj = setTimeout(function(){
                                    websocket.send('{\"api\":\"心跳\"}');
                                }, this.timeout)
                            }
                        }
                    websocket.onclose = function () {
                        websocket = new WebSocket(wsUrl)
                    };
                    websocket.onerror = function () {
                        websocket = new WebSocket(wsUrl)
                    };
                    websocket.onopen = function () {
                        heartCheck.reset().start(); //心跳检测重置
                    };    
                    new Vue({
                                el: '#app',
                                data: {
                                    message:[],
                                },
                                methods: {
                                    onmessage(e){
                                       heartCheck.reset().start();
                                       let res=JSON.parse(e.data);
                                       let api=res['api'];
                                       let info=res['info']
                                       switch (api){
                                           case 'userInit':
                                           case '心跳':
                                                   break;
                                           case 'input':
                                                 this.message.push(info)  
                                                 break;
                                           default:
                                                console.log('未定义api：',api);
                                       }
                                         console.log(res);
                                    }
                                },
                                created: function () { 
                                    this.message=$info.map(v=>JSON.parse(v))
                                    websocket.onmessage=this.onmessage
                                }
                             })
                </script>
                ";
        $this->withBody($html);
        return $this;
    }

    function ext_withBody_sse(): self
    {
        $port = $this->connection->getLocalPort();
        $html = "
                <script crossorigin='anonymous' src='vue.js'></script>
                <div id='app'>{{message}}</div>
                <script>
                       //let socket = new WebSocket('ws://'+document.domain+':$port');
                       let sse = new EventSource(document.domain+':$port/sseget');
                       new Vue({
                                el: '#app',
                                data: {
                                    message: '0',
                                },
                                methods: {
                                    init(e){
                                        console.log(e)
                                        this.message = e.data
                                    }
                                },
                                created: function () {
                                    sse.onmessage=function(e) {
                                        console.log(e)
                                    }
                                   sse.addEventListener('init', this.init); 
                                   sse.addEventListener('on', this.init);
                                }
                             })
                </script>
                ";
        $this->withBody($html);
        return $this;
    }

    function send():self
    {
        $this->connection->send($this);
        return $this;
    }
}