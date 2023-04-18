<?php

namespace Macbre\Tests\Macbre;

use Macbre\Tests\TestCase;

class IntegrationTest extends TestCase
{
	// @see https://www.php.net/manual/en/wrappers.php.php
	const MEMORY_STREAM_NAME = 'php://memory';

	/**
	 * @covers \Macbre\Logger\Processors\ExceptionProcessor
	 * @covers \Macbre\Logger\Processors\RequestIdProcessor
	 * @covers \Macbre\Logger\Formatters\JsonFormatter
	 */
	public function testLogsToFile() {
		$logger = new \Monolog\Logger('my.app');

		$logger->pushProcessor(new \Macbre\Logger\Processors\ExceptionProcessor());
		$logger->pushProcessor(new \Macbre\Logger\Processors\RequestIdProcessor());

		$stream = fopen(self::MEMORY_STREAM_NAME, 'rw+');

		// write JSON-formatted logs to the temporary file
		$fileHandler = new \Monolog\Handler\StreamHandler($stream);
		$fileHandler->setFormatter(new \Macbre\Logger\Formatters\JsonFormatter());
		$logger->pushHandler($fileHandler);

		// and now let's use the logger...
		$logger->error('Foo Bar', [
			'exception' => new \Exception('An error', 123),
			'size' => 42
		]);

		// make sure that the in-memory stream is readable
		rewind($stream);

		$content = rtrim(stream_get_contents($stream));

		$this->assertStringStartsWith(prefix: '{', string: $content);
		$this->assertStringEndsWith(suffix: '}', string: $content);

		$parsed = json_decode($content, associative: true);
		$this->assertEquals('Foo Bar', $parsed['message']);
		$this->assertEquals('my.app', $parsed['channel']);
		$this->assertEquals('An error', $parsed['context']['exception']['message']);
	}
}
