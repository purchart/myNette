<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\DI\Config;

use Nette;
use Nette\DI\Definitions;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Context;
use Nette\Schema\Expect;
use Nette\Schema\Schema;


/**
 * Service configuration schema.
 */
class DefinitionSchema implements Schema
{
	use Nette\SmartObject;

	/** @var Nette\DI\ContainerBuilder */
	private $builder;


	public function __construct(Nette\DI\ContainerBuilder $builder)
	{
		$this->builder = $builder;
	}


	public function complete($def, Context $context)
	{
		if ($def === [false]) {
			return (object) $def;
		}

		if (Helpers::takeParent($def)) {
			$def['reset']['all'] = true;
		}

		foreach (['arguments', 'setup', 'tags'] as $k) {
			if (isset($def[$k]) && Helpers::takeParent($def[$k])) {
				$def['reset'][$k] = true;
			}
		}

		$def = $this->expandParameters($def);
		$type = $this->sniffType(end($context->path), $def);
		$def = $this->getSchema($type)->complete($def, $context);
		if ($def) {
			$def->defType = $type;
		}

		return $def;
	}


	public function merge($def, $base)
	{
		if (!empty($def['alteration'])) {
			unset($def['alteration']);
		}

		return Nette\Schema\Helpers::merge($def, $base);
	}


	/**
	 * Normalizes configuration of service definitions.
	 */
	public function normalize($def, Context $context)
	{
		if ($def === null || $def === false) {
			return (array) $def;

		} elseif (is_string($def) && interface_exists($def)) {
			return ['implement' => $def];

		} elseif ($def instanceof Statement && is_string($def->getEntity()) && interface_exists($def->getEntity())) {
			$res = ['implement' => $def->getEntity()];
			if (array_keys($def->arguments) === ['tagged']) {
				$res += $def->arguments;
			} elseif (count($def->arguments) > 1) {
				$res['references'] = $def->arguments;
			} elseif ($factory = array_shift($def->arguments)) {
				$res['factory'] = $factory;
			}

			return $res;

		} elseif (!is_array($def) || isset($def[0], $def[1])) {
			return ['factory' => $def];

		} elseif (is_array($def)) {
			if (isset($def['create']) && !isset($def['factory'])) {
				$def['factory'] = $def['create'];
				unset($def['create']);
			}

			if (isset($def['class']) && !isset($def['type'])) {
				if ($def['class'] instanceof Statement) {
					$key = end($context->path);
					trigger_error(sprintf("Service '%s': option 'class' should be changed to 'factory'.", $key), E_USER_DEPRECATED);
					$def['factory'] = $def['class'];
					unset($def['class']);
				} elseif (!isset($def['factory']) && !isset($def['dynamic']) && !isset($def['imported'])) {
					$def['factory'] = $def['class'];
					unset($def['class']);
				}
			}

			foreach (['class' => 'type', 'dynamic' => 'imported'] as $alias => $original) {
				if (array_key_exists($alias, $def)) {
					if (array_key_exists($original, $def)) {
						throw new Nette\DI\InvalidConfigurationException(sprintf(
							"Options '%s' and '%s' are aliases, use only '%s'.",
							$alias,
							$original,
							$original
						));
					}

					$def[$original] = $def[$alias];
					unset($def[$alias]);
				}
			}

			return $def;

		} else {
			throw new Nette\DI\InvalidConfigurationException('Unexpected format of service definition');
		}
	}


	public function completeDefault(Context $context)
	{
	}


	private function sniffType($key, array $def): string
	{
		if (is_string($key)) {
			$name = preg_match('#^@[\w\\\\]+$#D', $key)
				? $this->builder->getByType(substr($key, 1), false)
				: $key;

			if ($name && $this->builder->hasDefinition($name)) {
				return get_class($this->builder->getDefinition($name));
			}
		}

		if (isset($def['implement'], $def['references']) || isset($def['implement'], $def['tagged'])) {
			return Definitions\LocatorDefinition::class;

		} elseif (isset($def['implement'])) {
			return method_exists($def['implement'], 'create')
				? Definitions\FactoryDefinition::class
				: Definitions\AccessorDefinition::class;

		} elseif (isset($def['imported'])) {
			return Definitions\ImportedDefinition::class;

		} elseif (!$def) {
			throw new Nette\DI\InvalidConfigurationException("Service '$key': Empty definition.");

		} else {
			return Definitions\ServiceDefinition::class;
		}
	}


	private function expandParameters(array $config): array
	{
		$params = $this->builder->parameters;
		if (isset($config['parameters'])) {
			foreach ((array) $config['parameters'] as $k => $v) {
				$v = explode(' ', is_int($k) ? $v : $k);
				$params[end($v)] = $this->builder::literal('$' . end($v));
			}
		}

		return Nette\DI\Helpers::expand($config, $params);
	}


	private static function getSchema(string $type): Schema
	{
		static $cache;
		$cache = $cache ?: [
			Definitions\ServiceDefinition::class => self::getServiceSchema(),
			Definitions\AccessorDefinition::class => self::getAccessorSchema(),
			Definitions\FactoryDefinition::class => self::getFactorySchema(),
			Definitions\LocatorDefinition::class => self::getLocatorSchema(),
			Definitions\ImportedDefinition::class => self::getImportedSchema(),
		];
		return $cache[$type];
	}


	private static function getServiceSchema(): Schema
	{
		return Expect::structure([
			'type' => Expect::type('string'),
			'factory' => Expect::type('callable|Nette\DI\Definitions\Statement'),
			'arguments' => Expect::array(),
			'setup' => Expect::listOf('callable|Nette\DI\Definitions\Statement|array:1'),
			'inject' => Expect::bool(),
			'autowired' => Expect::type('bool|string|array'),
			'tags' => Expect::array(),
			'reset' => Expect::array(),
			'alteration' => Expect::bool(),
		]);
	}


	private static function getAccessorSchema(): Schema
	{
		return Expect::structure([
			'type' => Expect::string(),
			'implement' => Expect::string(),
			'factory' => Expect::type('callable|Nette\DI\Definitions\Statement'),
			'autowired' => Expect::type('bool|string|array'),
			'tags' => Expect::array(),
		]);
	}


	private static function getFactorySchema(): Schema
	{
		return Expect::structure([
			'type' => Expect::string(),
			'factory' => Expect::type('callable|Nette\DI\Definitions\Statement'),
			'implement' => Expect::string(),
			'arguments' => Expect::array(),
			'setup' => Expect::listOf('callable|Nette\DI\Definitions\Statement|array:1'),
			'parameters' => Expect::array(),
			'references' => Expect::array(),
			'tagged' => Expect::string(),
			'inject' => Expect::bool(),
			'autowired' => Expect::type('bool|string|array'),
			'tags' => Expect::array(),
			'reset' => Expect::array(),
		]);
	}


	private static function getLocatorSchema(): Schema
	{
		return Expect::structure([
			'implement' => Expect::string(),
			'references' => Expect::array(),
			'tagged' => Expect::string(),
			'autowired' => Expect::type('bool|string|array'),
			'tags' => Expect::array(),
		]);
	}


	private static function getImportedSchema(): Schema
	{
		return Expect::structure([
			'type' => Expect::string(),
			'imported' => Expect::bool(),
			'autowired' => Expect::type('bool|string|array'),
			'tags' => Expect::array(),
		]);
	}
}
