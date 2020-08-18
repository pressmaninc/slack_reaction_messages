<?php
require_once './class/Slack.php';

class Util
{
	/**
	 * 全ユーザーを取得する
	 * 配列keyはユーザーのIDとなる
	 *
	 * @return array
	 */
	function get_all_user() : array {
		$users = Slack::get_user_list();
		// IDでユーザー情報を参照したいため、keyをidに入れ替える
		foreach($users['members'] as $user) {
			$user_list[$user['id']] = $user;
		}

		return $user_list;
	}

	function get_public_channel_list() : array {
		$list = Slack::get_conversations_list();
		return $list;
	}

	/**
	 * 指定チャンネルの全メッセージを取得する
	 *
	 * @param string $channel_id
	 * @return array
	 */
	function get_all_message_by_channel_id(string $channel_id, $old_date = '') : array {
		$oldest = '';
		if ($old_date) {
			$oldest = strtotime($old_date);
		}

		$next_cursor = '';
		$message_list = [];

		while(true) {
			// 連続のアクセスはSlack側で許可していないため
			sleep(SLLEP_SECOND);

			$data = Slack::get_conversations_history($channel_id, $next_cursor, $oldest);

			// 戻り値の配列にメッセージを結合する
			$message_list = array_merge($data['messages'], $message_list);

			// next_cursorに値がある場合、まだデータが残っている。
			// next_cursorを使用して次のデータを取得しにいく
			if (isset($data['response_metadata']['next_cursor']) && $data['response_metadata']['next_cursor']) {
				$next_cursor = $data['response_metadata']['next_cursor'];
				continue;
			}

			break;
		}
		return $message_list;
	}

	/**
	 * 指定リアクションがついているメッセージのみ抽出する
	 *
	 * @param array $message_list
	 * @return array
	 */
	function find_reaction_message_list(array $message_list, string $reaction_name) : array {
		$return = [];
		foreach($message_list as $channel_id => $messages) {
			foreach($messages as $message) {
				if (!isset($message['reactions'])) {
					continue;
				}

				foreach($message['reactions'] as $reaction) {
					if ($reaction['name'] === $reaction_name) {
						$message['reaction'] = $reaction;
						$return[$channel_id][] = $message;
						break;
					}
				}
			}
		}

		return $return;
	}

	/**
	 * メッセージの配列にスレッドのメッセージ情報も追加する
	 *
	 * @param array $message_list
	 * @return array
	 */
	function add_thread(array $message_list) : array {
		foreach($message_list as $channel_id => $messages) {
			foreach($messages as $message) {
				if (!isset($message['thread_ts']) || !$message['thread_ts']) {
					continue;
				}

				sleep(SLLEP_SECOND);
				$threds = Slack::get_conversations_replies($channel_id, $message['thread_ts']);
				foreach($threds['messages'] as $thred) {
					$thred['parent_ts'] = $message['ts'];
					$message_list[$channel_id][] = $thred;
				}
			}
		}

		return $message_list;
	}
}