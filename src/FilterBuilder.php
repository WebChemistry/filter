<?php

declare(strict_types=1);

namespace WebChemistry\Filter;

use Kdyby\Translation\ITranslator;

class FilterBuilder {

	public static $globalDefaults = [
		'limitPerPage' => 10,
		'ajax' => false,
		'paginatorFile' => NULL,
	];

	/** @var FilterOptions */
	private $options;

	/** @var ITranslator */
	private $translator;

	/** @var callable[] */
	public $onCreate = [];

	/** @var callable[] */
	public $onFetch = [];

	public function __construct(?ITranslator $translator = null) {
		$this->options = new FilterOptions();
		$this->translator = $translator;

		$this->options->limitPerPage = self::$globalDefaults['limitPerPage'];
		$this->options->ajax = self::$globalDefaults['ajax'];
		$this->options->paginatorFile = self::$globalDefaults['paginatorFile'];
	}

	/**
	 * @param array $limits
	 */
	public function setLimits(array $limits) {
		$this->options->limits = $limits;
	}

	public function addOrder(string $name, string $label, array $values): self {
		$this->options->orderValues[$name] = $label;
		$this->options->order[$name] = $values;

		return $this;
	}

	public function setPaginatorFile(?string $paginatorFile): self {
		$this->options->paginatorFile = $paginatorFile;

		return $this;
	}

	public function setSource(callable $callback, array $options = []) {
		$this->options->source = $callback;
		$this->options->sourceOptions = $options;
	}

	public function setLimitPerPage(?int $limitPerPage): self {
		$this->options->limitPerPage = $limitPerPage;

		return $this;
	}

	public function addLink(string $field, string $type, $val = null): self {
		$this->options->links[$field] = true;
		$this->options->defaults[$field] = $val;
		$this->options->types[$field] = $type;

		return $this;
	}

	public function addFormBuilderCallback(callable $callback, string $name): self {
		$this->options->forms[$name] = $callback;

		return $this;
	}

	public function setAjax(bool $ajax = true): self {
		$this->options->ajax = $ajax;

		return $this;
	}

	public function setSnippets(array $snippets): self {
		$this->options->snippets = $snippets;

		return $this;
	}

	public function createFilter(): Filter {
		$filter = new Filter($this->options, $this->translator);
		$filter->onFetch = $this->onFetch;
		foreach ($this->onCreate as $callback) {
			$callback($filter);
		}

		return $filter;
	}

}