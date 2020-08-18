<?php
require_once dirname(__FILE__).'/Parent.php';
require_once dirname(__FILE__).'/../../class/Slack.php';

class Slack_Test extends Parent_Class
{
	function test_get_user_list() {
		$mock_content = $this->get_mock('users.list?token=');

		$content = Slack::get_user_list();
		$this->assertSame($mock_content, $content);
	}

	function test_get_conversations_list() {
		$mock_content = $this->get_mock('conversations.list?token=&limit=1000');

		$content = Slack::get_conversations_list();
		$this->assertSame($mock_content, $content);
	}

	function test_get_conversations_history() {
		$mock_content = $this->get_mock('conversations.history?token=&limit=1000&channel=C03A246PG');

		$content = Slack::get_conversations_history('C03A246PG');
		$this->assertSame($mock_content, $content);
	}

	function test_get_conversations_history_oldest() {
		$mock_content = $this->get_mock('conversations.history?token=&limit=1000&channel=C03A246PG&oldest=1234567');

		$content = Slack::get_conversations_history('C03A246PG', '', '1234567');
		$this->assertSame($mock_content, $content);
	}

	function test_get_conversations_history_cursor() {
		$mock_content = $this->get_mock('conversations.history?token=&limit=1000&channel=C03A246PG&cursor=bmV4dF90czoxNTk3MTA0MDY5MDU5NDAw');

		$content = Slack::get_conversations_history('C03A246PG', 'bmV4dF90czoxNTk3MTA0MDY5MDU5NDAw');
		$this->assertSame($mock_content, $content);
	}

	function test_get_conversations_replies() {
		$mock_content = $this->get_mock('conversations.replies?token=&limit=1000&channel=C03A246PG&ts=1597107641.061900');

		$content = Slack::get_conversations_replies('C03A246PG', '1597107641.061900');
		$this->assertSame($mock_content, $content);
	}
}