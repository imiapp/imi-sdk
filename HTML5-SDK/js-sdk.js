//获取topicId
function getTopicId(topicUrl, version, scope){
    var contentType = "application/json; charset=utf-8";
    var data = JSON.stringify({version:version,scope:scope});
    var topicIdData = originalAjax(topicUrl, contentType, data );
    if(topicIdData){
        topicIdData = JSON.parse(topicIdData);
        if( topicIdData['retCode'] != "0000000" || !topicIdData['result']['topicId'] || !topicIdData['result']['name']){
            alert("获取用户信息失败，请从新获取!");
            return false;
        }
    }
    return topicIdData['result'];
}

function pullData(requestUrl, topicId, scope){
    var sendData = JSON.stringify({topicId:topicId,scope:scope});
    var contentType = "application/json; charset=utf-8";
    var result = originalAjax(requestUrl, contentType, sendData);
    result = JSON.parse(result);
    if(result['retCode'] != "0000000" || !result['result']){
        // alert("获取用户信息失败，请从新获取!");
        return ;
    }
    return result['result'];
}

function originalAjax(url,contentType,requestData){
    var req = new XMLHttpRequest();
    var data = '';
    if(req){
        req.onreadystatechange = function(){
            if(req.readyState == 4){
                if(req.status == 200){
                    data = req.responseText;
                }else{
                    alert("网络错误，请确认网络连接！");
                }
            }
        };
        req.open("POST", url, false);
        req.setRequestHeader("Content-Type",contentType);
        req.send(requestData);
    }
    return data;
}