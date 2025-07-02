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
	 * @var boolean Indicates whether content is required in the response body.
	 */
	public bool $contentRequired = true;

	/**
	 * The data type.
	 *
	 * @var boolean|string The data type.  True, if it is multipart form data.
	 *                     Implying some of the data may be binary. If it is a
	 *                     string, the string indicates the type of data
	 *                     ('body', 'json', 'form_params').
	 */
	public bool|string $dataType = false;

	/**
	 * The error required flag.
	 *
	 * @var boolean Indicates whether an error field is expected in the
	 *              response or not.
	 */
	 public bool $errorRequired = true;

	 /**
	 * The is error flag.
	 *
	 * @var boolean Indicates whether an error is expected or not.
	 */
	public bool $isError = false;

	public function __construct(array $overrides = [])
	{
		$this->dataType = false;
		$this->isError = false;
		$this->errorRequired = true;
		$this->contentRequired = true;

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
