<?php

namespace Macbre\Tests\Macbre\Processors;

use Macbre\Logger\Processors\ExceptionProcessor;
use Macbre\Tests\TestCase;
use Monolog\LogRecord;

class TestException extends \Exception {}

/**
 * @covers \Macbre\Logger\Processors\ExceptionProcessor
 */
class ExceptionProcessorTest extends TestCase
{
	const EX_MESSAGE = 'Oh no, something went horribly wrong!';
	const EX_CODE = 42;

	public function testFormatsTheException() {
		$record = self::getLogRecord(
			message: 'Foo bar',
			context: [
				'foo' => 'bar', // this one should be kept
				'exception' => new TestException(self::EX_MESSAGE, self::EX_CODE),
			],
			extra: [
				'yes' => 42,
			]
		);

		$instance = new ExceptionProcessor();
		$res = $instance->__invoke($record);

		$this->assertInstanceOf(LogRecord::class, $res);
		$this->assertArrayHasKey('exception', $res['context']);
		$this->assertEquals($record['context']['foo'], $res['context']['foo']);
		$this->assertEquals($record['extra'], $res['extra']);

		$this->assertEquals(TestException::class, $res['context']['exception']['class']);
		$this->assertEquals(self::EX_MESSAGE, $res['context']['exception']['message']);
		$this->assertEquals(self::EX_CODE, $res['context']['exception']['code']);
	}

	public function testSkipsWhenThereIsNoException() {
		$record = self::getLogRecord(
			message: 'All went fine'
		);

		$instance = new ExceptionProcessor();
		$res = $instance->__invoke($record);

		$this->assertInstanceOf(LogRecord::class, $res);
		$this->assertEquals('All went fine', $res->message);
	}
}
