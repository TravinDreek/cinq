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

function get_group_label($group) : string {
	return $group->group_name.' ('.$group->group_id.')';
}

function display_groups_home() {
	$groups = json_decode(get_endpoint('get_group_list'))->data;
	if(!$groups) {
		echo '无法获取群组列表.'.PHP_EOL;
		return;
	}
	while(true) {
		switch(select_from_list('CINQ > 群组', ['显示所有群组', '搜索群组', '返回'])) {
			case 1:
				$groups_formatted = array();
				foreach($groups as $group) {
					array_push($groups_formatted, get_group_label($group));
				}
				array_push($groups_formatted, '返回');
				$choice = select_from_list('CINQ > 群组 > 群组列表', $groups_formatted);
				if($choice == count($groups_formatted)) break;
				display_group_profile($groups[$choice - 1]);
				break;
			case 2:
				$keyword = get_input('CINQ > 群组 > 搜索群组');
				if(!$keyword) break;
				$groups_matched = array();
				$groups_matched_formatted = array();
				foreach($groups as $group) {
					if(stripos($group->group_name, $keyword) !== false || $keyword == $group->group_id) {
						array_push($groups_matched, $group);
						array_push($groups_matched_formatted, get_group_label($group));
					}
				}
				if(!$groups_matched) {
					echo '找不到任何群组匹配此名称或号码.'.PHP_EOL;
					break;
				}
				array_push($groups_matched_formatted, '返回');
				$choice = select_from_list('CINQ > 群组 > 搜索群组 > 搜索结果', $groups_matched_formatted);
				if($choice == count($groups_matched_formatted)) break;
				display_group_profile($groups_matched[$choice - 1]);
				break;
			case 3:
				break 2;
		}
	}
}

function display_group_profile($group) {
	while(true) {
		switch(select_from_list('CINQ > 群组 > '.get_group_label($group), ['查看聊天', '发送消息', '查看群资料', '查看群员列表', '修改群名片', '返回'])) {
			case 1:
				display_group_chat($group);
				break;
			case 2:
				$msg = get_input('发送消息至 '.get_group_label($group));
				get_endpoint('send_group_msg', ['group_id' => $group->group_id, 'message' => $msg]);
				get_cinq_endpoint(['request' => 'archive_message', 'message' => [
					'post_type' => 'message',
					'message_type' => 'group',
					'user_id' => $GLOBALS['account_info']->data->user_id,
					'group_id' => $group->group_id,
					'message' => $msg,
					'sender' => [
						'card' => '',
						'nickname' => $GLOBALS['account_info']->data->nickname
					]
				]]);
				break;
			case 3:
				echo 'TODO'.PHP_EOL;
				break;
			case 4:
				echo 'TODO'.PHP_EOL;
				break;
			case 5:
				echo 'TODO'.PHP_EOL;
				break;
			case 6:
				break 2;
		}
	}
}

function display_group_chat($group) {
	echo(
		'===================='.PHP_EOL.
		'正在显示群 '.get_group_label($group).' 的聊天.'.PHP_EOL.
		'欲发送消息或返回, 请按下回车键.'.PHP_EOL.
		'===================='.PHP_EOL
	);
	get_cinq_endpoint(['request' => 'enable_message_echo', 'type' => 'group', 'id' => $group->group_id]);
	fgets(STDIN);
	get_cinq_endpoint(['request' => 'disable_message_echo']);
}