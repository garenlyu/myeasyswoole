//获取签名
Vue.prototype.startMatching = function(){
    console.log(111);
    that = this;
    that.privateData.userData.ws.send("hello");
};

//初始化websocket
Vue.prototype.initWebSocket = function(){
    that = this;
    that.privateData.userData.ws = new WebSocket("ws://myeasyswoole-websocket.top"); 
    //申请一个WebSocket对象，参数是服务端地址，同http协议使用http://开头一样，WebSocket协议的url使用ws://开头，另外安全的WebSocket协议使用wss://开头
    that.privateData.userData.ws.onopen = function(){
    　　//当WebSocket创建成功时，触发onopen事件
        console.log("open");
    　　that.privateData.userData.ws.send("hello"); //将消息发送到服务端
    }

    that.privateData.userData.ws.onmessage = function(e){
    　　//当客户端收到服务端发来的消息时，触发onmessage事件，参数e.data包含server传递过来的数据
    　　console.log(e.data);
    }
    that.privateData.userData.ws.onclose = function(e){
    　　//当客户端收到服务端发送的关闭连接请求时，触发onclose事件
    　　console.log("close");
    }
    that.privateData.userData.ws.onerror = function(e){
    　　//如果出现连接、处理、接收、发送数据失败的时候触发onerror事件
    　　console.log(error);
    }
};

