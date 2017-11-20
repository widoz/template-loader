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

namespace TemplateLoader\Tests;

use Brain\Monkey\Functions;
use TemplateLoader\DataStorage;
use TemplateLoader\Plugin;
use TemplateLoader\Loader;

/**
 * Class LoaderTest
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class LoaderTest extends UnprefixTestCase
{
    /**
     * @var Loader The loader instance
     */
    private $loader;

    /**
     * Test That Template Path Is Array Even If Pass Null
     */
    public function testThatTemplatePathIsArrayEvenIfPassNull()
    {
        // Null is the default value assigned to the templatePath property
        // when the instance is created.
        $this->loader->setTemplatePath(null);

        // Must be an empty array.
        $this->assertInternalType('array', $this->loader->getTemplatePath());
    }

    /**
     * Test Set null as template path
     */
    public function testThatNullProduceAnEmptyTemplatePathContainer()
    {
        // Null is the default value assigned to the templatePath property
        // when the instance is created.
        $this->loader->setTemplatePath(null);

        // Must be an empty array.
        $this->assertEmpty($this->loader->getTemplatePath());
    }

    /**
     * Test Set Template Path
     */
    public function testNotEmptyTemplatePathIfFileExists()
    {
        $templatePath = '/tests/assets/existsFile.php';
        $this->loader->setTemplatePath($templatePath);

        $this->assertNotEmpty($this->loader->getTemplatePath());
    }

    /**
     * Test Correct File Path Within Template Path Container
     */
    public function testCorrectFilePathWithinTemplatePathContainer()
    {
        $templatePath = '/tests/assets/existsFile.php';
        $this->loader->setTemplatePath($templatePath);

        $this->assertEquals(['tests/assets/existsFile.php'], $this->loader->getTemplatePath());
    }

    /**
     * Test Output Render With Empty Data
     */
    public function testOutputRenderWithEmptyData()
    {
        $filePath = '/tests/assets/existsFile.php';
        Functions::when('locate_template')->justReturn(rtrim(self::$sourcePath, '/') . $filePath);

        $this->loader
            ->setTemplatePath($filePath)
            ->setData(new \stdClass())
            ->render();

        $this->expectOutputString('This is an existing File php');

        return $this->loader;
    }

    /**
     * Test Output Render With Data Set
     */
    public function testOutputRenderWithDataSet()
    {
        $filePath = '/tests/assets/fileTestDataProperty.php';

        Functions::when('locate_template')->justReturn(rtrim(self::$sourcePath, '/') . $filePath);

        $data                 = new \stdClass();
        $data->propertyToTest = 'Value of the property to test';

        $this->loader
            ->setTemplatePath($filePath)
            ->setData($data)
            ->render();

        $this->expectOutputString($data->propertyToTest);
    }

    /**
     * Test non locate file path generate an exception
     *
     * @expectedException \Exception
     */
    public function testLocateFileDoesnotExists()
    {
        $filePath = '/tests/assets/notExistsFile.php';

        // The file must not be found within the theme.
        Functions::when('locate_template')->justReturn('');

        $this->loader
            ->setTemplatePath($filePath)
            ->render();
    }

    /**
     * Test Default Template Path
     *
     * This test that template within the child and theme doesn't exists or not loaded and we have set a
     * default path.
     */
    public function testDefaultTemplatePath()
    {
        $defaultFilePath = rtrim(self::$sourcePath, '/') . '/tests';

        // This file shouldn't be found.
        Functions::when('locate_template')->justReturn('');

        $this->loader = new Loader('loader_slug', new DataStorage, '/assets/existsFile.php', $defaultFilePath);
        // Data must be set or template will not included.
        $this->loader->setData(new \stdClass());

        ob_start();
        $located = $this->loader->render();
        ob_get_clean();

        $this->assertSame($defaultFilePath . '/assets/existsFile.php', $located);
    }

    /**
     * Test Set Template Path Return Loader InterfaceInstance
     */
    public function testSetTemplatePathReturnLoaderInterfaceInstance()
    {
        $this->assertInstanceOf('TemplateLoader\LoaderInterface', $this->loader->setTemplatePath(''));
    }

    /**
     * Test Set Data Path Return LoaderInterface Instance
     */
    public function testSetDataPathReturnLoaderInterfaceInstance()
    {
        $this->assertInstanceOf('TemplateLoader\LoaderInterface', $this->loader->setData(new \stdClass()));
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->loader = new Loader('loader_slug', new DataStorage);
        parent::setUp();
    }
}
