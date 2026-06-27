<?php

namespace Slendium\HttpSuperglobals;

use Override;

use Slendium\Http\Field;
use Slendium\Http\Content\Structured;

/**
 * @internal
 * @implements Structured<string,string>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class GlobalCookieHeader implements Field, Structured {

	#[Override]
	public string $name {
		get => 'cookie';
	}

	#[Override]
	public string $value {
		get => isset($_SERVER['HTTP_COOKIE']) && \is_string($_SERVER['HTTP_COOKIE'])
			? $_SERVER['HTTP_COOKIE']
			: '';
	}

	#[Override]
	public _CookieView $root;

	public function __construct() {
		$this->root = new _CookieView;
	}

	public function __toString(): string {
		return "{$this->name}: {$this->value}";
	}

}
