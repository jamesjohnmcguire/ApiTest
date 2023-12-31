<?php

/**
 * API Test
 *
 * Description: An end to end API tester.
 * Version:     1.0.7
 * PHP version: 8.1.1
 *
 * @category  PHP
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2024 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.0.7
 * @link      https://github.com/jamesjohnmcguire/MassMailer
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest;

/**
 * Page Tester class.
 *
 * Contains all the automated API tests.
 */
class PageTester extends APITester
{
	/**
	 * Constructor method.
	 *
	 * @param string $host             The host for the tests.
	 * @param string $requestDataType  The request data type.
	 * @param string $responseDataType The response data type.
	 */
	public function __construct(
		string $host,
		string $requestDataType = 'text/HTML',
		string $responseDataType = 'text/HTML')
	{
		parent::__construct($host, $requestDataType, $responseDataType);
	}

	/**
	 * Test API end point method.
	 *
	 * @param string                $method        The HTTP method to use.
	 * @param string                $endPoint      The API end point.
	 * @param null | array | string $data          The JSON data to process.
	 * @param boolean               $multiPartData Data is multipart form data.
	 *                                             Implying some of the data
	 *                                             may be binary.
	 * @param boolean               $isError       Indicates whether an error
	 *                                             is expected or not.
	 *
	 * @return mixed
	 */
	public function testPage(
		string $method,
		string $endPoint,
		null | array | string $data,
		bool $multiPartData = false,
		bool $isError = false) : mixed
	{
		$responseContent = $this->testApiEndPoint(
			$method,
			$endPoint,
			$data,
			$multiPartData,
			$isError,
			false);

		return $responseContent;
	}
}
