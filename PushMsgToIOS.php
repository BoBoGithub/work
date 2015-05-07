<?php
	//设置链接 证书地址	
	$streamContext = stream_context_create(
		array(
			'ssl' => array(
				'cafile'     => '/var/www/sandbox/common/conf/entrust_root_certification_authority.pem',
				'local_cert' => '/var/www/sandbox/conf/apns-1-dev.pem'
			)
		)
	);

        stream_context_set_option($streamContext, 'ssl','passphrase', '123456');

	$sURL = 'ssl://gateway.sandbox.push.apple.com:2195';

	//记着设置一下超时时间哈
	$socket = stream_socket_client($sURL, $nError, $sError, -1, STREAM_CLIENT_CONNECT, $streamContext);
	if(!$socket){
		exit("link fail.");
	}
	
	stream_set_blocking($socket, 0);
	stream_set_write_buffer($socket, 0);

	//接受消息的设备号 	
	$divice = '2dc73cdf51230de798d23254315df10766c3a0e96601ce42d3374628c9b7bebF';

	//发送消息设置
	//$msgArr['otherFiledKey'] = 'otherFiledValue';
	$msgArr['aps'] = array(
		'alert' => '你好, Hello World.'
	);
	$msg = json_encode($msgArr);
	//$msg = '{"aps":{"alert":"This is the test.","badge":1,"sound":"default"},type":1}';
		
	$con = pack('CNNnH*', 1, 1, time()+30, 32, $divice);
	$con.= pack('n', strlen($msg));
	$con.= $msg;

	$ret = fwrite($socket, $con);

	echo 'Send OK' .PHP_EOL;
?>
