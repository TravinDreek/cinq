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

function log_to_console($text) {
	if(is_array($text)) foreach($text as $line) echo date('[y-m-d h:i:s] ', time()).$line.PHP_EOL;
	else echo date('[y-m-d h:i:s] ', time()).$text.PHP_EOL;
}

function select_from_list($title, array $choices) : int {
	echo PHP_EOL.'##### '.$title.' #####'.PHP_EOL;
	foreach($choices as $k => $v) echo ($k + 1).'. '.$v.PHP_EOL;
	$choice = 0;
	do {
		echo '操作: ';
		$choice = intval(fgets(STDIN));
	} while($choice > count($choices) || $choice < 1);
	return $choice;
}

function get_input($title, $label = '输入 (留空则取消)') : string {
	echo PHP_EOL.'##### '.$title.' #####'.PHP_EOL;
	echo $label.': ';
	return rtrim(fgets(STDIN));
}

function get_endpoint($endpoint, $query = []) {
	return file_get_contents($GLOBALS['args']['r'].'/'.$endpoint.'?'.http_build_query($query));
}

function get_cinq_endpoint($data = []) {
	$data['post_type'] = 'cinq';
	return file_get_contents('http://'.$GLOBALS['args']['l'], false, stream_context_create([
		'http' => [
			'method' => 'POST',
			'header' => 'Content-type:application/json',
			'content' => json_encode($data)
		]
	]));
}