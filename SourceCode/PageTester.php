<?php

/**
 * An end to end Page tester.
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
 * Page Tester class.
 *
 * Contains all the automated API tests.
 */
class PageTester extends APITester
{
	/**
	 * Test API end point method.
	 *
	 * @param string                    $method        The HTTP method to use.
	 * @param string                    $endPoint      The API end point.
	 * @param null|array<string>|string $data          The JSON data to process.
	 * @param boolean                   $multiPartData Data is multipart form
	 *                                                 data. Implying some of
	 *                                                 the data may be binary.
	 * @param boolean                   $isError       Indicates whether an
	 *                                                 error is expected or not.
	 *
	 * @return string
	 *
	 * @deprecated since v1.5.18, use webPageTest() instead.
	 */
	public function testPage(
		string $method,
		string $endPoint,
		null | array | string $data,
		bool $multiPartData = false,
		bool $isError = false) : string
	{
		$options = new ApiOptions();

		if ($multiPartData === true)
		{
			$options->requestDataType = 'multipart';
		}

		$options->errorExpected = $isError;

		$responseContent = $this->webPageTest(
			$method,
			$endPoint,
			$data,
			$options);

		return $responseContent;
	}


	/**
	 * Test web page method.
	 *
	 * @param string                    $method     The HTTP method to use.
	 * @param string                    $endPoint   The API end point.
	 * @param null|array<string>|string $data       The JSON data to process.
	 * @param ApiOptions                $apiOptions The options object.
	 *                                              Contains various options.
	 *
	 * @throws \Exception If an unexpected exception occurs during the request.
	 *
	 * @return string
	 */
	public function webPageTest(
		string $method,
		string $endPoint,
		null | array | string $data = null,
		ApiOptions $apiOptions = new ApiOptions()) : ?string
	{
		$responseContent = $this->apiEndPointTest(
			$method,
			$endPoint,
			$data,
			$apiOptions);

		return $responseContent;
	}
}
