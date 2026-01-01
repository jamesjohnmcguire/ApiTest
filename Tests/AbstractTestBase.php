<?php

/**
 * An end to end API tester.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2026 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.7.35
 * @link      https://github.com/jamesjohnmcguire/ApiTest
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test base class.
 *
 * Contains all the common functionality for all tests.
 */
abstract class AbstractTestBase extends TestCase
{
	/**
	 * From address.
	 *
	 * @var string
	 */
	protected string $fromAddress = 'donotreply@example.com';

	/**
	 * Recipients.
	 *
	 * @var array<string>
	 */
	protected array $recipients =
	[
		'donotreply@gmail.com',
		'jahmic@gmail.com'
	];

	/**
	 * Set up method.
	 *
	 * @return void
	 */
	protected function setUp() : void
	{
	}
}
