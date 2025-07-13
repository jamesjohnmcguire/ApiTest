<?php

/**
 * API Options class.
 *
 * Represents options for API endpoint testing.
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

/**
 * Class ApiOptions
 *
 * Contains configuration options for API endpoint testing.
 */
class ApiOptions
{
	/**
	 * Indicates whether content is required in the response body.
	 *
	 * @var boolean
	 */
	public bool $contentRequired = true;

	/**
	 * The data type for the request.
	 * True if multipart form data (implying some data may be binary).
	 * If a string, indicates the type of data ('body', 'json', 'form_params').
	 *
	 * @var boolean|string
	 */
	public bool|string $dataType = false;

	/**
	 * Indicates whether an error is expected.  This may be by a thown
	 * exception or by a 'error' field in the response.
	 *
	 * @var boolean
	 */
	public bool $errorExpected = false;

	/**
	 * ApiOptions constructor.
	 *
	 * Initializes the API options with default values.
	 */
	public function __construct()
	{
		$this->dataType = false;
		$this->errorExpected = false;
		$this->contentRequired = true;
	}
}
