<?php

namespace Macbre\Logger\Formatters;

use Macbre\Tests\TestCase;

/**
 * @covers \Macbre\Logger\Formatters\JsonFormatter
 */
class JsonFormatterTest extends TestCase
{
	/**
	 * @throws \JsonException
	 */
	public function testFormatsTheLogEntry() {
		$record = self::getLogRecord(
			message: 'Foo bar',
			context: [
				'foo' => 'bar',
			],
			extra: [
				'yes' => 42,
			]
		);

		$formatted = (new JsonFormatter)->format($record);
		$this->assertStringStartsWith('{"', $formatted, 'It\'s a proper JSON');

		$parsed = json_decode($formatted, associative: true, flags: JSON_THROW_ON_ERROR);

		$this->assertEquals('2023-04-18T08:25:23.123456+00:00', $parsed['@timestamp']);
		$this->assertEquals($record->message, $parsed['message']);
		$this->assertEquals('info', $parsed['severity']);
		$this->assertEquals($record->channel, $parsed['channel']);
		$this->assertEquals($record['context'], $parsed['context']);
		$this->assertEquals($record['extra'], $parsed['extra']);
		$this->assertEquals('test.foo.net', $parsed['source_host']);
	}
}

function gethostname(): string {
	return 'test.foo.net';
}

function date(string $_format): string {
	return "2023-04-18T08:25:23.000000+00:00";
}

function microtime(): float {
	return 1681806383.123456;
}
