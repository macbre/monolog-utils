<?php

namespace Macbre\Logger\Processors;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Injects a unique per-request ID into extra fields of Monolog-generated log entry
 */
class RequestIdProcessor implements ProcessorInterface {
	static private ?string $requestId = null;

	/**
	 * Get per-request unique ID
	 *
	 * Example: 5654ba177058c8.07373029
	 *
	 * @return string
	 */
	public static function getRequestId(): string {
		if (self::$requestId === null) {
			self::$requestId = uniqid('', true);
		}

		return self::$requestId;
	}

	/**
	 * @param LogRecord|array $record
	 * @return array
	 */
	public function __invoke(LogRecord $record) {
		$record['extra']['request_id'] = self::getRequestId();
		return $record;
	}
}