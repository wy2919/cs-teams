<?php

include_once "wxBizDataCrypt.php";


$appid = 'wx94690c9eec18ed98';
$sessionKey = 'wLRn/d61WBUDHAvnELKUZQ==';

$encryptedData="Wzdl8holYXWUZ06Qt6Au3kMKA1brGMy1ASEH+wUIyAANC3tZQj+sfpTloZX6eEBv2iKb5djRWvL1ughBrW00mAmSf6HVc271XvZ9e9KiA9R/uZ1BFIWBcWOKG6OWldB0LwY6Zb1iQh2KateEBfb/wQtInXl7nL2wIHOSG5J2iQ45lVA6nYt2WzTV07CQWPZhgAjkkPcua5nMhZv1m0+S7w==";

$iv = 'dFz++IAScDAIIimcnkdmKQ==';

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}
