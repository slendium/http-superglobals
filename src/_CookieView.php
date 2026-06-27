<?php

namespace Slendium\HttpSuperglobals;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use LogicException;
use Override;
use Traversable;

/**
 * @internal
 * @implements ArrayAccess<string,?string>
 * @implements IteratorAggregate<string,string>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class _CookieView implements ArrayAccess, Countable, IteratorAggregate {

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return isset($_COOKIE[$offset]);
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		return isset($_COOKIE[$offset]) && \is_string($_COOKIE[$offset])
			? $_COOKIE[$offset]
			: null;
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new LogicException('Unexpected attempt to modify the global request cookies');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('Unexpected attempt to modify the global request cookies');
	}

	#[Override]
	public function count(): int {
		return \count($_COOKIE);
	}

	#[Override]
	public function getIterator(): Traversable {
		foreach ($_COOKIE as $name => $value) {
			if (\is_scalar($value)) {
				yield (string)$name => (string)$value;
			}
		}
	}

}
