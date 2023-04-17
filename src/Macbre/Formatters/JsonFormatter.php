<?php

namespace Macbre\Logger\Formatters;

use Monolog\LogRecord;

/**
 * Custom logs JSON formatter.
 *
 * Adds some extra data to the JSON:
 *  - source_host
 */
class JsonFormatter extends \Monolog\Formatter\JsonFormatter {

	/**
	 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-date-format.html#mapping-date-format
	 */
	const DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

	/**
	 * @param LogRecord $record
	 * @return string the JSON-formatted record
	 */
	public function format(LogRecord $record): string {
		$normalized = $this->normalizeRecord($record);

		unset($normalized['datetime']);
		unset($normalized['level']);
		unset($normalized['level_name']);

		$entry = [
			'@timestamp' => self::now(),
			'severity' => strtolower($record['level_name']),
			'source_host' => gethostname(),
		];

		$normalized = array_merge($normalized, $entry);

		return $this->toJson($normalized, true) . ($this->appendNewline ? "\n" : '');
	}

	/**
	 * Return a elasticsearch compatible timestamp with microseconds
	 *
	 * @see http://stackoverflow.com/a/17909891/5446110
	 * @return string
	 */
	private static function now(): string {
		$ret = date(self::DATE_FORMAT);

		// add microseconds
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);

		return str_replace('000000', $micro, $ret);
	}
}
