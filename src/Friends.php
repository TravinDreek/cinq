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

function get_friend_label($friend) : string {
	return $friend->nickname == $friend->remark ?
		$friend->remark.' ('.$friend->user_id.')' :
		$friend->remark.' ('.$friend->nickname.', '.$friend->user_id.')';
}

function display_friends_home() {
	$friends = json_decode(get_endpoint('get_friend_list'))->data;
	if(!$friends) {
		echo '无法获取好友列表.'.PHP_EOL;
		return;
	}
	while(true) {
		switch(select_from_list('CINQ > 好友', ['显示所有好友', '搜索好友', '返回'])) {
			case 1:
				$friends_formatted = array();
				foreach($friends as $friend) {
					array_push($friends_formatted, get_friend_label($friend));
				}
				array_push($friends_formatted, '返回');
				$choice = select_from_list('CINQ > 好友 > 好友列表', $friends_formatted);
				if($choice == count($friends_formatted)) break;
				display_friend_profile($friends[$choice - 1]);
				break;
			case 2:
				$keyword = get_input('CINQ > 好友 > 搜索好友');
				if(!$keyword) break;
				$friends_matched = array();
				$friends_matched_formatted = array();
				foreach($friends as $friend) {
					if(stripos($friend->nickname, $keyword) !== false || stripos($friend->remark, $keyword) !== false || $keyword == $friend->user_id) {
						array_push($friends_matched, $friend);
						array_push($friends_matched_formatted, get_friend_label($friend));
					}
				}
				if(!$friends_matched) {
					echo '找不到任何好友匹配此备注, 昵称或号码.'.PHP_EOL;
					break;
				}
				array_push($friends_matched_formatted, '返回');
				$choice = select_from_list('CINQ > 好友 > 搜索好友 > 搜索结果', $friends_matched_formatted);
				if($choice == count($friends_matched_formatted)) break;
				display_friend_profile($friends_matched[$choice - 1]);
				break;
			case 3:
				break 2;
		}
	}
}

function display_friend_profile($friend) {
	while(true) {
		switch(select_from_list('CINQ > 好友 > '.get_friend_label($friend), ['查看聊天', '发送消息', '返回'])) {
			case 1:
				display_friend_chat($friend);
				break;
			case 2:
				$msg = get_input('发送消息至 '.get_friend_label($friend));
				get_endpoint('send_private_msg', ['user_id' => $friend->user_id, 'message' => $msg]);
				get_cinq_endpoint(['request' => 'archive_message', 'message' => [
					'post_type' => 'message',
					'message_type' => 'private',
					'user_id' => $GLOBALS['account_info']->data->user_id,
					'message' => $msg,
					'sender' => [
						'nickname' => $GLOBALS['account_info']->data->nickname
					]
				]]);
				break;
			case 3:
				break 2;
		}
	}
}

function display_friend_chat($friend) {
	echo(
		'===================='.PHP_EOL.
		'正在显示与 '.get_friend_label($friend).' 的聊天.'.PHP_EOL.
		'欲发送消息或返回, 请按下回车键.'.PHP_EOL.
		'===================='.PHP_EOL
	);
	get_cinq_endpoint(['request' => 'enable_message_echo', 'type' => 'private', 'id' => $friend->user_id, 'my_id' => $GLOBALS['account_info']->data->user_id]);
	fgets(STDIN);
	get_cinq_endpoint(['request' => 'disable_message_echo']);
}