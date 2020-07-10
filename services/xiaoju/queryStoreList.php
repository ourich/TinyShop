<?php
//基本参数
include_once "./header.php";
$queryData = array('pageIndex' => 1, 'pageSize' => 100);
$info = curl_xiaoJu('queryStoreList ', $queryData);
print_r($info);