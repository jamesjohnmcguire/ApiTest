<?php

/**
 * Mass Mailer
 *
 * Description: Mass Mailer PHP Library.
 *
 * @category  PHPWebService
 * @package   MassMailer
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2024 by DotPix
 * @license   Proprietary
 * @version   1.5.34
 * @link      https://github.com/jamesjohnmcguire/MassMailer
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
	 * @var array
	 */
	protected array $recipients =
	[
		'donotreply@gmail.com',
		'jahmic@gmail.com'
	];

	/**
	 * Server.
	 *
	 * @var string
	 */
	private string $server = 'local';

	/**
	 * Set up method.
	 *
	 * @return void
	 */
	protected function setUp() : void
	{
	}
}
