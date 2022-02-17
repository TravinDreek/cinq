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

require 'Utils.php';
require 'EventListener.php';

$cli_args = getopt('l:r:');
if(empty($cli_args['l']) || empty($cli_args['r'])) exit('未指定 local host 或 remote host. 请使用命令行参数 "-l" 或 "-r" 指定正确的值.');

echo(
	'Copyright (C) 2022 Peaksol'.PHP_EOL.
	'This program is licensed under the GNU Affero General Public License version 3 (AGPLv3).'.PHP_EOL
);

parallel\run($listener, [$cli_args['l']]);

while(true) {
	$msg = rtrim(fgets(STDIN));
	file_get_contents($cli_args['r'].'/send_group_msg?'.http_build_query([
		'group_id' => 813878453, // 群号
		'message' => $msg
	]));
}