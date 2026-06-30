<?php

namespace Slendium\HttpSuperglobalsTests;

use Override;

use Slendium\Http\Field;
use Slendium\Http\ReadOnlyField;
use Slendium\Http\Response;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MockResponse implements Response {

	#[Override]
	public iterable $trailers {
		get => [ ];
	}

	public function __construct(

		/** @var array<Field> */
		#[Override]
		public array $headers = [ ],

		/** @var array<string> */
		#[Override]
		public array $body = [ ],

	) { }

	/** @param lowercase-string&non-empty-string $name */
	public function addHeader(string $name, string $value): self {
		$this->headers[] = new ReadOnlyField($name, $value);
		return $this;
	}

}
