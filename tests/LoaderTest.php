<?php
namespace TemplateLoaderTests;

use PHPUnit\Framework\TestCase;
use TemplateLoader\Filesystem;
use TemplateLoader\Loader;
use Andrew;

/**
 * @author     Guido Scialfa <dev@guidoscialfa.com>
 * @copyright  Copyright (c) 2017, Guido Scialfa
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2
 *
 * Copyright (C) 2017 Guido Scialfa <dev@guidoscialfa.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public Licensessss
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Class LoaderTest
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class LoaderTest extends TestCase
{
    use CommonFilesystemFunctionsTrait;

    /**
     * @var Loader The loader instance
     */
    private $loader;

    /**
     * Test that the slug is sanitized correctly
     */
    public function testSanitizeTemplateSlug()
    {
        $specialSlug = '/this/is/1234567890/a/special/!"£$%&/()=?^é*°§ç:;_/slug/';
        $proxy       = new Andrew\Proxy($this->loader);
        $slug        = $proxy->sanitizeTemplateSlug($specialSlug);

        $this->assertEquals(
            'thisis1234567890aspecial_slug',
            $slug
        );
    }

    /**
     * Test that the path is sanitized correctly
     */
    public function testSanitizeTemplatePath()
    {
        $specialPath = '/this/../path/doesnt/&/contain/non/ spaces /and/0123456789/special/chars/!"£$%&/()=?^*é°§ç:;';
        $proxy       = new Andrew\Proxy($this->loader);
        $path        = $proxy->sanitizeTemplatePath($specialPath);

        $this->assertEquals(
            'this//path/doesnt//contain/non/spaces/and/0123456789/special/chars',
            $path[0]
        );
    }

    /**
     * Test Set null as template path
     */
    public function testSetNullTemplatePath()
    {
        // Null is the default value assigned to the templatePath property
        // when the instance is created.
        $this->loader->setTemplatePath(null);

        // Must be an empty array.
        $this->assertInternalType('array', $this->loader->getTemplatePath());
        $this->assertEmpty($this->loader->getTemplatePath());
    }

    /**
     * Test that the getPluginFilePath load the correct template
     */
    public function testFoundExistingPluginFile()
    {
        self::incCommonFunctions();

        $paths = [
            '/this/file/doesnt/exists',
            '/tests/assets/existsFile.php',
        ];

        $proxy     = new Andrew\Proxy($this->loader);
        $foundPath = $proxy->getPluginFilePath($paths);

        $this->assertContains('/tests/assets/existsFile.php', $foundPath);
    }

    /**
     * Test Set Template Path
     */
    public function testSetTemplatePath()
    {
        $templatePath = '/tests/assets/existsFile.php';
        $this->loader->setTemplatePath($templatePath);

        $this->assertNotEmpty($this->loader->getTemplatePath());
        $this->assertEquals(['tests/assets/existsFile.php'], $this->loader->getTemplatePath());
    }

    /**
     * @depends testSetTemplatePath
     */
    public function testRender()
    {
        $this->markTestIncomplete();

        $filePath = '/tests/assets/existsFile.php';

        $this->loader->setTemplatePath($filePath);
        $response = $this->loader->render();
    }

    /**
     * LoaderTest constructor
     */
    public function __construct()
    {
        $this->loader = new Loader('loader_slug', new Filesystem());
    }

    /**
     * Setup
     */
    public function setUp()
    {
        \WP_Mock::setUp();
    }

    /**
     * TearDown
     */
    public function tearDown()
    {
        \WP_Mock::tearDown();
    }
}
