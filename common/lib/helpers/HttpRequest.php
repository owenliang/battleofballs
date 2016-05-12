<?php

namespace common\lib\helpers;

final class HttpRequest {	
	/**
	 * 发送http请求
	 * @param string $url
	 * @param array $postData
	 * @param string $requestType
	 * @param string $returnType
	 * @return mixed
	 */
	public static function curlRequest($url, $postData = [], $requestType = 'POST', $returnType = '', $timeout = 1000) {
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1000);
	    curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
	
	    $requestType = strtoupper($requestType);
	    $returnType = strtoupper($returnType);
	
	    if ($requestType == 'POST') {
	        if (is_array($postData)) {
	            $postData = http_build_query($postData);
	        }
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	    }  else if ($requestType == 'JSON') {        
	        $postDataJson = json_encode($postData);
	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'Content-Type:application/json', 'Content-Length:' . strlen($postDataJson)));
	    }

	    $result = curl_exec($ch);
	    curl_close($ch);
	    
	    if ($returnType == 'JSON') {
	        $result = json_decode($result, true);
	    }
	    return $result;
	}

}
