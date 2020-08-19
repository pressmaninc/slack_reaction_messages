<?php
require_once dirname(__FILE__).'/Parent.php';
require_once dirname(__FILE__).'/../../class/Csv.php';

class Csv_Test extends Parent_Class
{
	function setUp() : void {
		$this->datetime = date('YmdHis');
		$this->filepath = 'csv/'.$this->datetime.'.csv';
		$this->csv = new Csv('hoge', $this->datetime);
	}

	function tearDown() : void {
		unlink($this->filepath);
	}

	function test_construct() {
		$this->assertSame($this->csv->file, $this->filepath);
		$this->assertSame($this->csv->slack_url, 'hoge');
	}

	function test_write_row() {
		$this->csv->write_row(['hoge','huga']);
		$this->csv->write_row(['1','2']);

		$content = trim(file_get_contents($this->filepath));
		$this->assertSame($content, "hoge,huga\n1,2");
	}

	function test_write_colmun() {
		$this->csv->write_colmun();

		$content = trim(file_get_contents($this->filepath));
		$this->assertSame($content, '投稿者,メッセージ,リアクション数,リンク');
	}

	function test_write() {
		$this->csv->write(
			'hoge',
			[
				'user' => 'USLACKBOT',
				'ts' => '12345678',
				'text' => 'text',
				'reactions' => [
					[
						'name' => 'clap::skin-tone-2',
						'count' => '1',
					]
				]
			],
			[
				'USLACKBOT' => [
					'name' => 'slackbot'
				]
			]
		);
		$this->csv->write(
			'hoge',
			[
				'user' => 'USLACKBOT',
				'ts' => '12345678',
				'parent_ts' => '098765',
				'text' => 'text2',
				'reactions' => [
					[
						'name' => 'clap::skin-tone-2',
						'count' => '2',
					]
				]
			],
			[
				'USLACKBOT' => [
					'name' => 'slackbot'
				]
			]
		);

		$content = trim(file_get_contents($this->filepath));
		$this->assertSame($content, "slackbot,text,1,hoge/archives/hoge/p12345678\nslackbot,text2,2,hoge/archives/hoge/p12345678?thread_ts=098765");
	}

	function test_close() {
		$result = $this->csv->close();
		$this->assertTrue($result);
	}
}