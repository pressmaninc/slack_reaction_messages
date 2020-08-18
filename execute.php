<?php
require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('memory_limit', '16G');

define('TOKEN', $_ENV['TOKEN']);
define('SLACK_API_URL', $_ENV['SLACK_API_URL']);
define('OLDEST', $_ENV['OLDEST']);
define('SLACK_URL', $_ENV['SLACK_URL']);
define('REACTION', $_ENV['REACTION']);
define('SLLEP_SECOND', $_ENV['SLLEP_SECOND']);

require_once './class/Util.php';
require_once './class/Csv.php';

$util = new Util;
$csv = new Csv(SLACK_URL, date('YmdHis'));

$message_list = [];
// 全ユーザーを取得
$user_list = $util->get_all_user();
// 全公開チャンネルを取得
$channel_list = $util->get_public_channel_list();

foreach( $channel_list['channels'] as $channel ) {
	echo $channel['name']."のメッセージを取得\n";
	$id = $channel['id'];
	// チャンネルのメッセージを取得して結合していく
	$message_list[$id] = $util->get_all_message_by_channel_id($id, OLDEST);
}

echo "メッセージに紐付くスレッドを取得中\n";
$message_list = $util->add_thread($message_list);

$message_list = $util->find_reaction_message_list($message_list, REACTION);

$csv->write_colmun();
foreach($message_list as $channel_id => $messages) {
	foreach($messages as $message) {
		$csv->write($channel_id, $message, $user_list);
	}
}
