function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}

let uid = guid();
let number = 0;

var uri = "ws://127.0.0.1:9090";
function socket_connect(){
    socket = new WebSocket(uri);
    if(!socket || socket == undefined) return false;

    socket.onopen = function(){
        console.log('Connected to Server!');
        socket.send(uid);
    }

    socket.onerror = function(){
        console.log('Connection Failed!');
    }

    socket.onclose = function(){
        console.log('close');
    }

    socket.onmessage = function(e){
        let data = e.data;
        if(data.indexOf('session_id') >= 0){
            data = data.split(':');
            uid = parseInt(data[1],10);
        }else if(data.indexOf('number') >= 0){
            data = data.split(':');
            number += parseInt(data[1],10);
            $('#view-result').html(number);
        }

    }
}

$(document).ready(function(){
    socket_connect();
});

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

$('#btn-order').on('click',function (e) {
   e.preventDefault();
   $.ajax({
       type : "POST",
       url : "/order",
       data:{
           number:getRandomInt(1,100) ,
           product: "abc",
           uid: uid,
       },
       dataType: "json"
   })
});
