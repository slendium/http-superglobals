<?php

namespace Slendium\HttpSuperglobals;

use Exception;
use Override;
use Stringable;
use ReflectionClass;
use RuntimeException;

use Slendium\Http\Network\IpAddress;
use Slendium\Http\Network\SocketAddress;
use Slendium\Http\Networked;
use Slendium\Http\ReadOnlyNetworked;
use Slendium\Http\Request;
use Slendium\Http\Uri;

/**
 * A request object that simply reflects the relevant values in the PHP superglobals `$_GET`, `$_POST`,
 * `$_COOKIE`, `$_FILES` and `$_SERVER`.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class GlobalRequest implements Request {

	#[Override]
	public readonly iterable $headers;

	#[Override]
	public readonly iterable $body;

	#[Override]
	public readonly iterable $trailers;

	/**
	 * @since 1.0
	 * @return Networked<Request>
	 */
	public static function createNetworkedInstance(): Networked {
		return new ReadOnlyNetworked(self::getSocketAddress(), new self); // @phpstan-ignore return.type (GlobalRequest not covariant with Request, no real effect)
	}

	/** @since 1.0 */
	public function __construct() {
		$this->headers = new GlobalHeaders;
		$this->body = self::getBody();
		$this->trailers = [ ];
	}

	/** @return iterable<Stringable|string> */
	private static function getBody(): iterable {
		$typeInput = isset($_SERVER['HTTP_CONTENT_TYPE']) && \is_string($_SERVER['HTTP_CONTENT_TYPE'])
			? $_SERVER['HTTP_CONTENT_TYPE']
			: 'application/x-www-form-urlencoded';

		$parameterStart = \strpos($typeInput, ';');
		$type = $parameterStart === false
			? $typeInput
			: \substr($typeInput, 0, $parameterStart);

		return match(\trim($type)) {
			'application/x-www-form-urlencoded', 'multipart/form-data' => new FormDataBody,
			'application/json' => new JsonBody,
			default => [ ]
		};
	}

	private static function getSocketAddress(): SocketAddress {
		return new ReflectionClass(SocketAddress::class)->newLazyGhost(static function (SocketAddress $obj) {
			if (!isset($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'])) {
				throw new RuntimeException('Missing $_SERVER with keys REMOTE_ADDR and REMOTE_PORT, check your setup');
			}

			if (!\is_string($_SERVER['REMOTE_ADDR'])) {
				throw new RuntimeException('Expected $_SERVER[\'REMOTE_ADDR\'] to contain a scalar value, not '.\get_debug_type($_SERVER['REMOTE_ADDR']));
			}

			$port = \is_numeric($_SERVER['REMOTE_PORT'])
				? (int)$_SERVER['REMOTE_PORT']
				: -1;
			if ($port < 0 || $port > 65535) {
				throw new RuntimeException('Expected $_SERVER[\'REMOTE_PORT\'] to be int<0,65535>');
			}

			$ip = IpAddress::fromString($_SERVER['REMOTE_ADDR']);
			if ($ip instanceof Exception) {
				throw new RuntimeException('Could not parse $_SERVER[\'REMOTE_ADDR\'] into a valid IP address', previous: $ip);
			}

			$obj->__construct($ip, $port);
		});
	}

}
