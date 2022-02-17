<?php
/*
 * Copyright (C) 2020-2022 Spelako Project
 * 
 * This file is part of SpelakoOne.
 * Permission is granted to use, modify and/or distribute this program 
 * under the terms of the GNU Affero General Public License version 3.
 * You should have received a copy of the license along with this program.
 * If not, see <https://www.gnu.org/licenses/agpl-3.0.html>.
 * 
 * 此文件是 SpelakoOne 的一部分.
 * 在 GNU Affero 通用公共许可证第三版的约束下,
 * 你有权使用, 修改, 复制和/或传播该软件.
 * 你理当随同本程序获得了此许可证的副本.
 * 如果没有, 请查阅 <https://www.gnu.org/licenses/agpl-3.0.html>.
 * 
 */

$listener = function($localhost) {
	$socket = stream_socket_server('tcp://'.$localhost);
	echo '开始在 '.$localhost.' 上监听请求...'.PHP_EOL;
	while(true) if($conn = @stream_socket_accept($socket, -1)) {
		$datagram = fread($conn, 1024);
		$header_length = strpos($datagram, "\r\n\r\n") + 4;
		$content_length = intval(substr($datagram, strpos($datagram, 'Content-Length: ') + 16));
		if($header_length + $content_length > 1024) $datagram .= fread($conn, $header_length + $content_length - strlen($datagram));

		fwrite($conn, "HTTP/1.1 200 OK\r\n\r\n");
		fclose($conn);

		$content = json_decode(substr($datagram, $header_length));
		if(
			$content->post_type == 'message'
			&& $content->message_type == 'group'
			&& $content->group_id == 813878453 // 群号
		) {
			echo(date('[y-m-d h:i:s] ', time()).vsprintf(
				'%s (%s): %s',
				[
					$content->sender->nickname,
					$content->user_id,
					$content->message
				]
			).PHP_EOL);
		}
	}
};