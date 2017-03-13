<?php
namespace TemplateLoaderTests;

/**
 * Common Filesystem Functions Trait
 *
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
 * Class CommonFilesystemFunctionsTrait
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 * @package TemplateLoader\Tests
 */
trait CommonFilesystemFunctionsTrait
{
    /**
     * Include Common functions
     */
    protected static function incCommonFunctions()
    {
        \WP_Mock::wpFunction('plugin_dir_path', [
            'return' => dirname(__DIR__) . '/',
            'times'  => '0+',
        ]);
        \WP_Mock::wpFunction('untrailingslashit', [
            'return' => dirname(__DIR__),
            'times'  => '0+',
        ]);
    }
}
