<?php

namespace Slendium\HttpSuperglobals;

use ArrayObject;
use IteratorAggregate;
use Override;
use ReflectionClass;
use Traversable;

use Slendium\Http\Content\Structured;

/**
 * @internal
 * @implements IteratorAggregate<string>
 * @implements Structured<string,mixed>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class JsonBody implements IteratorAggregate, Structured {

	/** @var ArrayObject<string,mixed> */
	#[Override]
	public readonly ArrayObject $root;

	public function __construct() {
		$this->root = new ReflectionClass(ArrayObject::class)->newLazyGhost(static function($obj) {
			$raw = \file_get_contents('php://input');
			$json = $raw === false
				? [ ]
				: @\json_decode($raw, associative: true);

			if (!\is_array($json)) {
				$json = [ ];
			}

			$obj->__construct($json);
		});
	}

	#[Override]
	public function getIterator(): Traversable {
		$contents = \file_get_contents('php://input');
		if ($contents !== false) {
			yield $contents;
		}
	}

}
