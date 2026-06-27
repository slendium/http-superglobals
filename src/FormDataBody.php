<?php

namespace Slendium\HttpSuperglobals;

use IteratorAggregate;
use Override;
use Traversable;

use Slendium\Http\Content\Structured;

/**
 * @internal
 * @implements IteratorAggregate<string>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FormDataBody implements IteratorAggregate, Structured {

	#[Override]
	public _PostView $root;

	public function __construct() {
		$this->root = new _PostView;
	}

	#[Override]
	public function getIterator(): Traversable {
		$contents = \file_get_contents('php://input');
		if ($contents !== false) {
			yield $contents;
		}
	}

}
