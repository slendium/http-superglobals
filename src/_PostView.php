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
 * @implements ArrayAccess<string,mixed>
 * @implements IteratorAggregate<string,mixed>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class _PostView implements ArrayAccess, Countable, IteratorAggregate {

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return isset($_POST[$offset]);
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		return $_POST[$offset] ?? null;
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new LogicException('Unexpected attempt to modify the global request body arguments');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('Unexpected attempt to modify the global request body arguments');
	}

	#[Override]
	public function count(): int {
		return \count($_POST);
	}

	#[Override]
	public function getIterator(): Traversable {
		foreach ($_POST as $name => $value) {
			if (\is_scalar($value)) {
				yield (string)$name => (string)$value;
			}
		}
	}

}
