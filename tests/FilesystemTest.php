<?php
namespace TemplateLoaderTests;

use PHPUnit\Framework\TestCase;

/**
 * @author     Guido Scialfa <dev@guidoscialfa.com>
 * @copyright  Copyright (c) 2017, Guido Scialfa
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2
 *
 * Copyright (C) 2017 Guido Scialfa <dev@guidoscialfa.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
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
 * Class FilesystemTest
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class FilesystemTest extends TestCase
{
    use CommonFilesystemFunctionsTrait;

    /**
     * @var \TemplateLoader\Filesystem
     */
    private $filesystem;

    /**
     * Prevent Path Traversal
     */
    public function testNotContainDotDotSlash()
    {
        $path = '/../this/is/a/../path/../';

        // Sanitize the path.
        $result = $this->filesystem->sanitizePath($path);

        $this->assertNotContains('../', $result);
    }

    /**
     * Test that Directory doesn't exists
     */
    public function testDirNotExists()
    {
        self::incCommonFunctions();

        // Retrieve the file path.
        $result = $this->filesystem->getPluginDirPath('/this/not/exists');

        $this->assertInternalType('boolean', $result);
        $this->assertEquals(false, $result);
    }

    /**
     * Test that a file exists within the plugin directory
     */
    public function testFileExistsWithinPluginDirPath()
    {
        self::incCommonFunctions();

        $filePath = '/tests/assets/existsFile.php';
        $path     = $this->filesystem->getPluginDirPath($filePath);

        $this->assertInternalType('string', $path);
        $this->assertNotEmpty($path);
    }

    /**
     * FilesystemTest constructor
     */
    public function __construct()
    {
        $this->filesystem = new \TemplateLoader\Filesystem();
    }

    /**
     * Set Up
     */
    public function setUp()
    {
        \WP_Mock::setUp();
    }

    /**
     * Tear Down
     */
    public function tearDown()
    {
        \WP_Mock::tearDown();
    }
}