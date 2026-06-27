<?php

namespace Slendium\HttpSuperglobals;

use Override;
use RuntimeException;

use Slendium\Http\Content\Structured;
use Slendium\Http\Field;

/**
 * @internal
 * @implements Structured<string,mixed>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class GlobalPathHeader implements Field, Structured {

	#[Override]
	public string $name {
		get => ':path';
	}

	#[Override]
	public string $value {
		get => isset($_SERVER['REQUEST_URI']) && \is_string($_SERVER['REQUEST_URI'])
			? $_SERVER['REQUEST_URI']
			: throw new RuntimeException('Expected $_SERVER[\'REQUEST_URI\'] to be a string');
	}

	#[Override]
	public _GetView $root;

	public function __construct() {
		$this->root = new _GetView;
	}

	/** @return non-empty-string */
	public function __toString(): string {
		return "{$this->name}: {$this->value}";
	}

}
