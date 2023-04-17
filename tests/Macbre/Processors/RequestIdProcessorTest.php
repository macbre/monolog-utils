<?php

namespace Macbre\Tests\Macbre\Processors;

use Macbre\Logger\Processors\RequestIdProcessor;
use Macbre\Tests\TestCase;

/**
 * @covers \Macbre\Logger\Processors\RequestIdProcessor
 */
class RequestIdProcessorTest extends TestCase
{
	public function testFormatsTheException() {
		$record = self::getLogRecord(
			message: 'Hi',
			extra: [
				'yes' => 42,
			]
		);

		$expectedExtra = [
			'yes' => 42,
			'request_id' => RequestIdProcessor::getRequestId(),
		];

		$instance = new RequestIdProcessor();
		$res = $instance->__invoke($record);

		$this->assertEquals($expectedExtra, $res['extra']);

		// consecutive calls should return the same unique ID
		$instance = new RequestIdProcessor();
		$res = $instance->__invoke($record);
		$this->assertEquals($expectedExtra, $res['extra']);
	}

}
