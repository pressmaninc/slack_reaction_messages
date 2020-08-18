<?php
class Csv
{
	function __construct($slack_url, $datetime) {
		$this->file = 'csv/'.$datetime.'.csv';
		$this->f = fopen($this->file, "w");
		$this->slack_url = $slack_url;
	}

	function write_row($row) {
		fputcsv($this->f, $row);
	}

	function write_colmun() {
		$colmun = ['投稿者', 'メッセージ', 'リアクション数', 'リンク'];
		$this->write_row($colmun);
	}

	function write($channel_id,  $message, $user_list) {
		$link = $this->slack_url.'/archives/' . $channel_id . '/p' . $message['ts'];
		if (isset($message['parent_ts'])) {
			$link.= '?thread_ts='.$message['parent_ts'];
		}

		$row = [
			$user_list[$message['user']]['name'], // 投稿者
			$message['text'], // メッセージ
			$message['reactions'][0]['count'], // リアクション数
			$link, // リンク
		];
		$this->write_row($row);
	}
}