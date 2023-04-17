<?php

namespace Macbre\Logger\Formatters;

use Macbre\Tests\TestCase;

/**
 * @covers \Macbre\Logger\Formatters\JsonFormatter
 */
class JsonFormatterTest extends TestCase
{
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

		$this->assertArrayHasKey('@timestamp', $parsed);
		$this->assertEquals($record->message, $parsed['message']);
		$this->assertEquals('info', $parsed['severity']);
		$this->assertEquals($record['context'], $parsed['context']);
		$this->assertEquals($record['extra'], $parsed['extra']);
		$this->assertEquals('test.foo.net', $parsed['source_host']);
	}
}

function gethostname(): string {
	return 'test.foo.net';
}
