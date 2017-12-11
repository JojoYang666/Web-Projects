<?php

include_once "wxBizMsgCrypt.php";

// 第三方发送消息给公众平台
$encodingAesKey = "Y0HuWf1bFYoYpa0Y4CWf0W00Hff1HY1FBy1Y01Cf4py";
$token = "cAUWiQ2uW1HIAHGMgmW4FIhwU28M44Rr";
$timeStamp = $_GET['timestamp'];
$nonce = $_GET['nonce'];
$appId = "wxacd541378e4eb791";
$text = "<xml>
            <ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName>
            <FromUserName><![CDATA[gh_7f083739789a]]></FromUserName>
            <CreateTime>1407743423</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[你好啊！我是Robin]]></Content>
            <MsgId>6060349595123187712</MsgId>
         </xml>";


$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
$encryptMsg = '';
$errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
if ($errCode == 0) {
    print("加密后: " . $encryptMsg . "\n");
} else {
    print($errCode . "\n");
}

$xml_tree = new DOMDocument();
$xml_tree->loadXML($encryptMsg);
$array_e = $xml_tree->getElementsByTagName('Encrypt');
$array_s = $xml_tree->getElementsByTagName('MsgSignature');
$encrypt = $array_e->item(0)->nodeValue;
$msg_sign = $array_s->item(0)->nodeValue;

$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
$from_xml = sprintf($format, $encrypt);

// 第三方收到公众号平台发送的消息
$msg = '';
$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
if ($errCode == 0) {
    print("解密后: " . $msg . "\n");
} else {
    print($errCode . "\n");
}
