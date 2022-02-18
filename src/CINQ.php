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

require 'Friends.php';
require 'Groups.php';
require 'Utils.php';
require 'EventListener.php';

global $args;
$args = getopt('l:r:');

if(empty($args['l']) || empty($args['r'])) exit('未指定 local host 或 remote host. 请使用命令行参数 "-l" 或 "-r" 指定正确的值.');

echo(
	'CINQ Is Not QQ - 1.0.0'.PHP_EOL.
	'Copyright (C) 2022 Peaksol'.PHP_EOL.
	'本程序为自由软件, 在 GNU Affero 通用公共许可证第三版的约束下, 你可以自由地使用, 修改和/或再分发此软件.'.PHP_EOL.
	'本作品的版权持有人及其分发者和修改者不为你提供任何显式或隐式的品质担保, 且不对你的损失负有任何责任.'.PHP_EOL.PHP_EOL
);

global $account_info;
$account_info = json_decode(get_endpoint('get_login_info')) or exit('无法获取账号信息.');
echo '已登录账号 '.$account_info->data->nickname.' ('.$account_info->data->user_id.').'.PHP_EOL;

$listener = parallel\run($listener, [$args['l']]);

while(true) {
	switch(select_from_list('CINQ', ['消息', '好友', '群组', '退出'])) {
		case 1:
			echo 'TODO';
			break;
		case 2:
			display_friends_home();
			break;
		case 3:
			display_groups_home();
			break;
		case 4:
			get_cinq_endpoint(['request' => 'halt']);
			break 2;
	}
}