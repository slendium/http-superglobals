<?php

namespace Slendium\HttpSuperglobalsTests;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

use Slendium\HttpSuperglobals\GlobalResponse;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class GlobalResponseTest extends TestCase {

	// headers_list() does not seem to work under PHPUnit
	// header application may not be testable without resorting to an adapter interface

	#[RunInSeparateProcess]
	public function test_headers_shouldApplyStatusCode(): void {
		$response = (new MockResponse)->addHeader(':status', '400');

		GlobalResponse::output($response);

		$this->assertSame(400, \http_response_code());
	}

	#[RunInSeparateProcess]
	public function test_body_shouldBeApplied(): void {
		$body = '940a12df-a344-486d-8ed6-9e8ecd839f5e';
		$response = new MockResponse(body: [ $body ]);

		\ob_start();
		GlobalResponse::output($response);
		$result = \ob_get_clean();

		$this->assertSame($body, $result);
	}

}
