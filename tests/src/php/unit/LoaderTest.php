<?php
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

namespace TemplateLoader\Tests\Unit;

use \Mockery as m;
use Brain\Monkey\Functions;
use TemplateLoader\DataStorage;
use TemplateLoader\Loader;
use TemplateLoader\Tests\UnprefixTestCase;

/**
 * Class LoaderTest
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
class LoaderTest extends UnprefixTestCase
{
    /**
     * Test Set Template Path
     */
    public function testNotEmptyTemplatePathIfFileExists()
    {
        $filePath = '/assets/existsEmptyFile.php';

        Functions::when('locate_template')->justReturn(rtrim(self::$sourcePath, '/') . $filePath);

        $modelInterfaceMock = \Mockery::mock('TemplateLoader\\ModelInterface');

        $loader = new Loader('loader_slug', new DataStorage());

        // Must be an empty array.
        $this->assertNotEmpty(
            $loader->withData($modelInterfaceMock)
                   ->usingTemplate($filePath)
                   ->render()
        );
    }

    /**
     * Test Output Render With Empty Data
     */
    public function testOutputRenderWithEmptyData()
    {
        $filePath = '/assets/existsFile.php';

        Functions::when('locate_template')->justReturn(rtrim(self::$sourcePath, '/') . $filePath);

        $modelInterfaceMock = \Mockery::mock('TemplateLoader\\ModelInterface');

        $loader = new Loader('loader_slug', new DataStorage());

        $loader
            ->withData($modelInterfaceMock)
            ->usingTemplate($filePath)
            ->render();

        $this->expectOutputString('This is an existing File php');
    }

    /**
     * Test Output Render With Data Set
     */
    public function testOutputRenderWithDataSet()
    {
        $filePath = '/assets/fileTestDataProperty.php';

        Functions::when('locate_template')->justReturn(rtrim(self::$sourcePath, '/') . $filePath);

        $dataMock                 = m::mock('TemplateLoader\\ModelInterface');
        $dataMock->propertyToTest = 'Value of the property to test';

        $loader = new Loader('template_slug', new DataStorage());

        $loader
            ->withData($dataMock)
            ->usingTemplate($filePath)
            ->render();

        $this->expectOutputString($dataMock->propertyToTest);
    }

    /**
     * Test non locate file path generate an exception
     *
     * @expectedException \Exception
     */
    public function testLocateFileDoesnotExists()
    {
        $filePath = '/assets/notExistsFile.php';

        // The file must not be found within the theme.
        Functions::when('locate_template')->justReturn('');

        $dataMock = m::mock('TemplateLoader\\ModelInterface');

        $loader = new Loader('template_slug', new DataStorage());

        $loader
            ->withData($dataMock)
            ->usingTemplate($filePath)
            ->render();
    }

    /**
     * Test Default Template Path
     *
     * This test that template within the child and theme doesn't exists or not loaded and we have
     * set a default path.
     */
    public function testDefaultTemplatePath()
    {
        // This file shouldn't be found.
        Functions::when('locate_template')->justReturn('');

        $dataMock = m::mock('TemplateLoader\\ModelInterface');

        $loader = new Loader('loader_slug', new DataStorage);
        // Data must be set or template will not included.
        $loader->withData($dataMock)
               ->usingTemplate('notExistsFile.php')
               ->butFallbackToTemplate(rtrim(self::$sourcePath, '/') . '/assets/existsEmptyFile.php');

        $located = $loader->render();

        $this->assertSame(self::$sourcePath . '/assets/existsEmptyFile.php', $located);
    }
}
