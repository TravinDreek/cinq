<?php
/*
 * Copyright (C) 2020-2022 Peaksol
 * 
 * This file is part of CINQ.
 * Permission is granted to use, modify and/or distribute this program 
 * under the terms of the GNU Affero General Public License version 3.
 * You should have received a copy of the license along with this program.
 * If not, see <https://www.gnu.org/licenses/agpl-3.0.html>.
 * 
 * 此文件是 CINQ 的一部分.
 * 在 GNU Affero 通用公共许可证第三版的约束下,
 * 你有权使用, 修改, 复制和/或传播该软件.
 * 你理当随同本程序获得了此许可证的副本.
 * 如果没有, 请查阅 <https://www.gnu.org/licenses/agpl-3.0.html>.
 * 
 */

$listener = function($localhost) {
	$socket = stream_socket_server('tcp://'.$localhost);
	$stack = array();
	$message_echo = ['enabled' => false];

	while(true) if($conn = @stream_socket_accept($socket, -1)) {
		$datagram = fread($conn, 1024);
		$header_length = strpos($datagram, "\r\n\r\n") + 4;
		$content_length = intval(substr($datagram, strpos($datagram, 'Content-Length: ') + 16));
		if($header_length + $content_length > 1024) $datagram .= fread($conn, $header_length + $content_length - strlen($datagram));

		fwrite($conn, "HTTP/1.1 204 No Content\r\n\r\n");
		fclose($conn);

		$content = json_decode(substr($datagram, $header_length));
		if($content->post_type == 'cinq') {
			switch($content->request) {
				case 'enable_message_echo':
					foreach($stack as $message) {
						if($message->message_type == $content->type && $content->id == (($content->type == 'private') ? $message->user_id : $message->group_id))
						echo date('[y-m-d h-i-s] ', time()).(
							$message->message_type == 'group' ? (
								$message->sender->card && $message->sender->card != $message->sender->nickname ?
								$message->sender->card.' ('.$message->sender->nickname.', '.$message->user_id.'): '.$message->message :
								$message->sender->nickname.' ('.$message->user_id.'): '.$message->message
							) :
							$message->sender->nickname.' ('.$message->user_id.'): '.$message->message
						).PHP_EOL;
					}
					$message_echo = [
						'enabled' => true,
						'type' => $content->type,
						'id' => $content->id
					];
					break;
				case 'disable_message_echo':
					$message_echo['enabled'] = false;
					break;
				case 'archive_message':
					array_push($stack, $content->message);
					break;
				case 'halt':
					break 2;
			}
		}
		else {
			if($content->post_type == 'message') {
				array_push($stack, $content);
				if(
					$message_echo['enabled']
					&& $message_echo['type'] == $content->message_type
					&& ($content->user_id == $message_echo['id'] || $content->group_id == $message_echo['id'])
				)
				echo date('[y-m-d h-i-s] ', time()).(
					$content->message_type == 'group' ? (
						$content->sender->card && $content->sender->card != $content->sender->nickname ?
						$content->sender->card.' ('.$content->sender->nickname.', '.$content->user_id.'): '.$content->message :
						$content->sender->nickname.' ('.$content->user_id.'): '.$content->message
					) :
					$content->sender->nickname.' ('.$content->user_id.'): '.$content->message
				).PHP_EOL;
			}
		}
	}
};