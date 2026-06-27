<?php

namespace Slendium\HttpSuperglobalsTests;

use PHPUnit\Framework\TestCase;
use Slendium\Http\Message\BodyArgs;
use Slendium\Http\Message\Cookies;
use Slendium\Http\Message\Headers;
use Slendium\Http\Message\QueryArgs;

use Slendium\HttpSuperglobals\GlobalRequest;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class GlobalRequestTest extends TestCase {

	public function test_cookie_shouldUseCookieSuperglobal(): void {
		$cookieName = '19a2c624-f027-4951-8a5f-a356275c9d71';
		$expectedResult = 'c6695832-6c2d-4709-bd7e-77bf9f4e2877';
		$_COOKIE[$cookieName] = $expectedResult;

		$result = Cookies::get(new GlobalRequest, $cookieName);

		$this->assertSame($expectedResult, $result);
	}

	public function test_query_shouldUseGetSuperglobal(): void {
		$argName = '98367bc9-4a92-4e61-a200-12a6465bd7c8';
		$expectedResult = 'f0b9a7b8-ab8c-4718-bffe-581aaf2f1e4a';
		$_GET[$argName] = $expectedResult;

		$result = QueryArgs::get(new GlobalRequest, $argName);

		$this->assertSame($expectedResult, $result);
	}

	public function test_body_shouldUsePostSuperglobal(): void {
		$argName = '80802bc9-380d-4b3b-9515-ad70e7eb56a2';
		$expectedResult = '280f5b92-491f-4ce3-9210-cb081fc409f8';
		$_POST[$argName] = $expectedResult;

		$result = BodyArgs::get(new GlobalRequest, $argName);

		$this->assertSame($expectedResult, $result);
	}

	public function test_header_shouldUseServerSuperglobal(): void {
		$expectedResult = 'b6923834-373f-4e2a-8aa5-80a3b5a9b11c';
		$_SERVER['HTTP_X_CUSTOM_HEADER'] = $expectedResult;

		$result = Headers::getFirst(new GlobalRequest, 'x-custom-header')?->value;

		$this->assertSame($expectedResult, $result);
	}

	public function test_createNetworkedInstance_shouldNotThrow(): void {
		$this->expectNotToPerformAssertions();

		GlobalRequest::createNetworkedInstance();
	}

}
