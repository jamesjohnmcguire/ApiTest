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
			$options->dataType = 'multipart';
		}

		$options->errorExpected = $isError;
		$options->contentRequired = true;

		$responseContent = $this->apiEndPointTest(
			$method,
			$endPoint,
			$data,
			$options);

			return $responseContent;
	}
}
