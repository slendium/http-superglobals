<?php

namespace Slendium\HttpSuperglobals;

use ArrayAccess;
use IteratorAggregate;
use LogicException;
use OutOfBoundsException;
use Override;
use Traversable;

use Slendium\Http\Field;
use Slendium\Http\ReadOnlyField;

/**
 * @internal
 * @implements ArrayAccess<?(lowercase-string&non-empty-string),?Field>
 * @implements IteratorAggregate<Field>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class GlobalHeaders implements ArrayAccess, IteratorAggregate {

	/** @var array<string,?Field> */
	private array $fields = [ ];

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return $this[$offset] !== null;
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		if (!\is_string($offset)) {
			return null;
		}

		if (\array_key_exists($offset, $this->fields)) {
			return $this->fields[$offset];
		}

		return $this->fields[$offset] = match($offset) {
			':method' => isset($_SERVER['REQUEST_METHOD']) && \is_string($_SERVER['REQUEST_METHOD'])
				? new ReadOnlyField(':method', $_SERVER['REQUEST_METHOD'])
				: null,
			':path' => new GlobalPathHeader,
			':authority' => isset($_SERVER['SERVER_NAME']) && \is_string($_SERVER['SERVER_NAME'])
				? new ReadOnlyField(':authority', $_SERVER['SERVER_NAME'])
				: null,
			':scheme' => new ReadOnlyField(':scheme', self::getSchemeFromServerGlobal()),
			'cookie' => new GlobalCookieHeader,
			default => self::tryCreateHeaderFromServerGlobal($offset)
		};
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new LogicException('Global request headers are immutable');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('Global request headers are immutable');
	}

	#[Override]
	public function getIterator(): Traversable {
		foreach ([ ':method', ':path', ':authority', ':scheme' ] as $pseudoName) {
			if (isset($this[$pseudoName])) {
				yield $this[$pseudoName];
			}
		}

		foreach ($_SERVER as $key => $value) {
			if (\str_starts_with($key, 'HTTP_') && \is_scalar($value)) {
				yield $this[self::serverKeyToFieldName($key)]; // @phpstan-ignore generator.valueType (never returns NULL here)
			}
		}
	}

	/** @return 'http'|'https' */
	private static function getSchemeFromServerGlobal(): string {
		return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])
			? 'https'
			: 'http';
	}

	/** @param lowercase-string&non-empty-string $name */
	private function tryCreateHeaderFromServerGlobal(string $name): ?Field {
		$key = self::fieldNameToServerKey($name);

		return $this->fields[$name] = isset($_SERVER[$key]) && \is_string($_SERVER[$key])
			? new ReadOnlyField($name, $_SERVER[$key])
			: null;
	}

	private static function fieldNameToServerKey(string $name): string {
		return \strtoupper($name)
			|> (static fn($x) => \str_replace('-', '_', $x))
			|> (static fn($x) => "HTTP_$x");
	}

	private static function serverKeyToFieldName(string $key): string {
		return \substr($key, 5)
			|> \strtolower(...)
			|> (static fn($x) => \str_replace('_', '-', $x));
	}

}
