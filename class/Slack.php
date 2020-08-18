<?php
class Slack
{
	const URL = SLACK_API_URL;
	const TOKEN = TOKEN;

	/**
	 * Slack APIにリクエストを投げる
	 *
	 * @param string $endpoint
	 * @param array $params
	 * @return array
	 */
	private static function api(string $endpoint, array $params=[]) : array {
		$url = self::URL . $endpoint . '?token=' . self::TOKEN;
		foreach ($params as $key => $value) {
			$url.= '&' . $key . '=' . $value;
		}

		$response = file_get_contents($url);
		return json_decode($response, true);
	}

	/**
	 * 全ユーザーを取得する
	 *
	 * @return array
	 */
	public static function get_user_list() : array {
		return self::api('users.list');
	}

	/**
	 * Slackの全パブリックチャンネルを取得する
	 * LIMIT 1000でベタ書きだがチャンネル数が1000件超えることは無い想定
	 *
	 * @return array
	 */
	public static function get_conversations_list() : array {
		return self::api('conversations.list', [
			'limit' => 1000,
		]);
	}

	/**
	 * 指定チャンネルからメッセージとイベントの履歴を取得する
	 *
	 * @param string $channel_id チャンネルID
	 * @param string $cursor ページング時の次取得カーソル
	 * @param string $oldest 何時以降のデータを取得するか
	 * @return array
	 */
	public static function get_conversations_history( string $channel_id, string $cursor = '', string $oldest = '' ) : array {
		$params = [
			'limit' => 1000,
			'channel' => $channel_id,
		];

		if ($oldest) {
			$params['oldest'] = $oldest;
		}

		if ($cursor) {
			$params['cursor'] = $cursor;
		}

		return self::api('conversations.history', $params);
	}

	/**
	 * 指定チャンネル、メッセージのスレッドを返却する
	 *
	 * @param string $channel_id
	 * @param mixed $ts
	 * @return array
	 */
	public static function get_conversations_replies( string $channel_id, $ts ) : array {
		$params = [
			'limit' => 1000,
			'channel' => $channel_id,
			'ts' => $ts,
		];

		return self::api('conversations.replies', $params);
	}
}
