<?php

declare(strict_types=1);

namespace Arcanedev\LaravelLang\Tests;

use Arcanedev\LaravelLang\Contracts\TransPublisher;

/**
 * Class     TransPublisherTest
 *
 * @package  Arcanedev\LaravelLang\Tests\Services
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class TransPublisherTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelLang\Contracts\TransPublisher */
    private $publisher;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        $this->publisher = $this->app->make(TransPublisher::class);
    }

    public function tearDown(): void
    {
        unset($this->publisher);

        parent::tearDown();
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            TransPublisher::class,
            \Arcanedev\LaravelLang\TransPublisher::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->publisher);
        }
    }

    /** @test */
    public function it_can_publish(): void
    {
        $locale = 'es';

        static::assertTrue($this->publisher->publish($locale));

        // Clean the lang folder content.
        $this->cleanLangDirectory($locale);

        static::assertTrue($this->publisher->publish($locale));

        // Delete the lang folder.
        $this->deleteLangDirectory($locale);
    }

    /** @test */
    public function it_can_not_publish_if_is_not_forced(): void
    {
        $locale = 'es';

        static::assertTrue($this->publisher->publish($locale));

        try {
            $this->publisher->publish($locale);
        }
        catch(\Arcanedev\LaravelLang\Exceptions\LangPublishException $e) {
            static::assertEquals(
                'You can\'t publish the translations because the [es] folder is not empty. '.
                'To override the translations, try to clean/delete the [es] folder or force the publication.',
                $e->getMessage()
            );
        }

        // Delete the lang folder.
        $this->deleteLangDirectory($locale);
    }

    /** @test */
    public function it_can_publish_on_force(): void
    {
        $locale = 'es';

        static::assertTrue($this->publisher->publish($locale));
        static::assertTrue($this->publisher->publish($locale, true));

        // Delete the lang folder.
        $this->deleteLangDirectory($locale);
    }

    /** @test */
    public function it_can_skip_if_locale_is_english(): void
    {
        static::assertTrue($this->publisher->publish('en'));
    }

    /** @test */
    public function it_must_throw_an_exception_on_unsupported_locale(): void
    {
        $this->expectException(\Arcanedev\LaravelLang\Exceptions\LangPublishException::class);
        $this->expectExceptionMessage('The locale [arcanedev] is not supported.');

        $this->publisher->publish('arcanedev');
    }
}
