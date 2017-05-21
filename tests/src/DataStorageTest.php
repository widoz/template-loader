<?php
/**
 * DataStorageTest
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @copyright Copyright (c) 2017, Guido Scialfa
 * @license   GNU General Public License, version 2
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
namespace TemplateLoaderTests;

use TemplateLoader\DataStorage;

class DataStorageTest extends UnprefixTestCase
{
    /**
     * @return DataStorage
     */
    public function testOffsetSet()
    {
        $dataStorage        = new DataStorage();
        $dataStorage['key'] = 'value';

        $this->assertCount(1, $dataStorage);

        return $dataStorage;
    }

    /**
     * @depends testOffsetSet
     */
    public function testOffsetExists($dataStorage)
    {
        $this->assertArrayHasKey('key', $dataStorage);
    }

    /**
     * @depends testOffsetSet
     */
    public function testIsset($dataStorage)
    {
        $assert = isset($dataStorage['key']);
        $this->assertTrue($assert);
    }

    /**
     * @depends testOffsetSet
     */
    public function testOffsetGet($dataStorage)
    {
        $this->assertSame('value', $dataStorage['key']);
    }

    /**
     * @depends testOffsetSet
     */
    public function testOffsetUnset($dataStorage)
    {
        unset($dataStorage['key']);

        $this->assertEmpty($dataStorage);
    }
}
