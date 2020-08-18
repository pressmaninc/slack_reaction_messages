<?php
require_once dirname(__FILE__).'/Parent.php';
require_once dirname(__FILE__).'/../../class/Util.php';

class Util_Test extends Parent_Class
{
	function setUp() : void {
		$this->util = new Util;
	}

	public function test_get_all_user() {
		$all_user = $this->util->get_all_user();

		$mock = $this->get_mock('users.list?token=');
		$users["USLACKBOT"] = $mock['members'][0];
		$this->assertSame($all_user, $users);
	}

	public function test_get_public_channel_list() {
		$channel = $this->util->get_public_channel_list();

		$mock = $this->get_mock('conversations.list?token=&limit=1000');
		$this->assertSame($mock, $channel);
	}

	public function test_get_all_message_by_channel_id() {
		$message_list = $this->util->get_all_message_by_channel_id('C03A246PG');

		$mock = $this->get_mock('conversations.history?token=&limit=1000&channel=C03A246PG');
		$mock2 = $this->get_mock('conversations.history?token=&limit=1000&channel=C03A246PG&cursor=bmV4dF90czoxNTk3MTA0MDY5MDU5NDAw');
		$mock_message_list = array_merge($mock2['messages'],$mock['messages']);

		$this->assertSame($mock_message_list, $message_list);
	}

	function test_find_reaction_message_list() {
		$message_list = [
			'channel1' => [
				[
					'text' => '1',
					'reactions' => [
						[
							'name' => 'huga'
						],
						[
							'name' => 'hoge'
						],
					]
				],
				[
					'text' => '2',
					'reactions' => [
						[
							'name' => 'huga'
						],
					]
				]
			],
			'channel2' => [
				[
					'text' => '3',
				],
				[
					'text' => '4',
					'reactions' => [
						[
							'name' => 'hoge'
						],
					]
				],
			],
			'channel3' => [
				[
					'text' => '5',
				],
			]
		];
		$message_list = $this->util->find_reaction_message_list($message_list, 'hoge');

		$result = [
			'channel1' => [
				[
					'text' => '1',
					'reactions' => [
						[
							'name' => 'huga'
						],
						[
							'name' => 'hoge'
						],
					],
					'reaction' => [
						'name' => 'hoge'
					]
				],
			],
			'channel2' => [
				[
					'text' => '4',
					'reactions' => [
						[
							'name' => 'hoge'
						],
					],
					'reaction' => [
						'name' => 'hoge'
					]
				],
			],
		];
		$this->assertsame($message_list, $result);
	}

	function test_add_thread() {
		$message_list = [
			'C03A246PG' => [
				[
					'ts' => '1597107641.061900',
					'thread_ts' => '1597107641.061900',
				]
			]
		];

		$message_list = $this->util->add_thread($message_list);

		$mock = dirname(__FILE__).'/../mock/conversations.replies?token=&limit=1000&channel=C03A246PG&ts=1597107641.061900';
		$mock_content = json_decode(file_get_contents($mock), true);
		$mock_content['messages'][0]['parent_ts'] = '1597107641.061900';
		$mock_content['messages'][1]['parent_ts'] = '1597107641.061900';

		$expected_value = [
			'C03A246PG' => [
				[
					'ts' => '1597107641.061900',
					'thread_ts' => '1597107641.061900',
				],
				$mock_content['messages'][0],
				$mock_content['messages'][1],
			]
		];

		$this->assertSame($message_list,$expected_value);
	}
}
