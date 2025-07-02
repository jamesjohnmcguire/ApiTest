<?php

/**
 * An end to end API tester.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2025 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.5.15
 * @link      https://github.com/jamesjohnmcguire/ApiTest
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest;

class ApiOptions
{
	/**
	 * The content required flag.
	 *
	 * @var boolean
	 */
	public bool $contentRequired = true;

	/**
	 * The data type.
	 *
	 * @var boolean|string
	 */
	public bool|string $dataType = false;

	/**
	 * The error required flag.
	 *
	 * @var boolean
	 */
	 public bool $errorRequired = true;

	 /**
	 * The is error flag.
	 *
	 * @var boolean
	 */
	public bool $isError = false;

	public function __construct(array $overrides = [])
	{
		foreach ($overrides as $key => $value)
		{
			$exists = property_exists($this, $key);

			if (true === $exists)
			{
				$this->$key = $value;
			}
		}
	}
}
