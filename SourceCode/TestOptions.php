<?php

/**
 * Test Options class.
 *
 * Represents options for API endpoint testing.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2025 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.7.35
 * @link      https://github.com/jamesjohnmcguire/ApiTest
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest;

/**
 * Class TestOptions
 *
 * Contains configuration options for API endpoint testing.
 */
class TestOptions
{
	/**
	 * Indicates whether content is required in the response body.
	 *
	 * @var boolean
	 */
	public bool $contentRequired = true;

	/**
	 * Indicates whether an error is expected.  This may be by a thown
	 * exception or by a 'error' field in the response.
	 *
	 * @var boolean
	 */
	public bool $errorExpected = false;

	/**
	 * Additional Guzzle options to be included.
	 *
	 * @var null|array<string, bool|integer|object>
	 */
	public ?array $guzzleAdditionalOptions = null;

	/**
	 * The data type for the request.
	 * Can be one of the following values: 'body', 'form_params', 'json',
	 * 'multipart'.
	 *
	 * @var null|string
	 */
	public ?string $requestDataType = null;

	/**
	 * Indicates whether included basic asserts should be tried.
	 *
	 * @var boolean
	 */
	public bool $tryBasicAsserts = true;

	/**
	 * The user agent for the request.
	 *
	 * @var string
	 */
	public string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
		'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 ' .
		'Safari/537.36';

	/**
	 * TestOptions constructor.
	 *
	 * Initializes the API options with default values.
	 */
	public function __construct()
	{
	}
}
