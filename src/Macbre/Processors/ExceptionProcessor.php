<?php

namespace Macbre\Logger\Processors;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

/**
 * Formats the 'exception' field passed in $context when logging errors
 *
 * Example:
 *
 * $logger->error('Exception raised when doing foo and bar', [
 *   'exception' => $e
 * ]);
 */
class ExceptionProcessor implements ProcessorInterface {
	/**
	 * @param  LogRecord $record
	 * @return LogRecord
	 */
	public function __invoke(LogRecord $record): LogRecord {
		$recordContext = $record['context'];
		$exception = $recordContext['exception'] ?? null;

		if ($exception instanceof \Exception) {
			// build a new entry for the exception
			$recordContext['exception'] = [
				'class' => get_class($exception),
				'message' => $exception->getMessage(),
				'code'  => $exception->getCode(),
				'trace' => array_map(function($item) {
					if (!empty($item['file'])) {
						return sprintf('%s:%d', $item['file'], $item['line']);
					}
					else {
						return sprintf('%s%s%s', $item['class'], $item['type'], $item['function']);
					}
				}, $exception->getTrace())
			];

			// and now replace the record
			$record = new LogRecord(
				datetime: $record->datetime,
				channel: $record->channel,
				level: $record->level,
				message: $record->message,
				context: $recordContext,
				extra: $record->extra,
				formatted: $record->formatted,
			);
		}

		return $record;
	}
}