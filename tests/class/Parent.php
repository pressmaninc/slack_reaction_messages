<?php
use PHPUnit\Framework\TestCase;

define('SLACK_API_URL', '/Users/anahara/slack_reaction_messages/tests/mock/');
define('TOKEN', '');
define('SLLEP_SECOND', 0);

class Parent_Class extends TestCase
{
	function get_mock($name) {
		$mock_file = dirname(__FILE__).'/../mock/' . $name;
		return json_decode(file_get_contents($mock_file), true);
	}
}