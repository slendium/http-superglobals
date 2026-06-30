<?php

namespace Slendium\HttpSuperglobals;

use Slendium\Http\Response;

/**
 * Utility that prints a {@see Response} to the output buffer.
 *
 * Note that the `:status` pseudo-header must be used to change the status code of a response.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class GlobalResponse {

	private const FIELD_STATUS = ':status';

	/**
	 * Outputs the given response to the output buffer, using `echo` and `header()`.
	 * @since 1.0
	 */
	public static function output(Response $response): void {
		self::outputHeaders($response);
		self::outputBody($response);
	}

	private static function outputHeaders(Response $response): void {
		foreach ($response->headers as $header) {
			match ($header->name) {
				self::FIELD_STATUS => self::outputStatus($header->value),
				default => \header((string)$header)
			};
		}
	}

	private static function outputBody(Response $response): void {
		foreach ($response->body as $part) {
			echo $part;
		}
	}

	private static function outputStatus(string $value): void {
		if (!\is_numeric($value)) {
			throw new ResponseException("Unexpected non-numeric value for `:status` header: $value");
		}

		$value = (int)$value;
		if ($value < 100 || $value > 599) {
			throw new ResponseException("Expected value for `:status` to be int<100,599>, not $value");
		}

		\header(' ', true, $value);
	}

	private function __construct() { }

}
