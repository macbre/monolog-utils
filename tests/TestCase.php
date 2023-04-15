<?php

namespace Macbre\Tests;

use Monolog\DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;

class TestCase extends \PHPUnit\Framework\TestCase {
	protected static function getLogRecord( string $message, Level $level = Level::Info, array $context = [], array $extra = []): LogRecord {
		return new LogRecord(
			datetime: new DateTimeImmutable(true),
			channel: 'monolog',
			level: $level,
			message: $message,
			context: $context,
			extra: $extra
		);
	}
}
