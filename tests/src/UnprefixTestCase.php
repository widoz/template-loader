<?php
/**
 * UnprefixTestCase
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

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

/**
 * Class UnprefixTestCase
 *
 * @since   ${SINCE}
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
class UnprefixTestCase extends TestCase
{
    protected static $sourcePath;

    /**
     * Define Common WordPress Functions
     *
     * @since 1.2.1
     */
    protected function defineCommonWPFunctions()
    {
        Monkey\Functions::when('__')->returnArg(1);
        Monkey\Functions::when('esc_url')->returnArg(1);
        Monkey\Functions::when('esc_html__')->returnArg(1);
        Monkey\Functions::when('esc_html_x')->returnArg(1);
        Monkey\Functions::when('sanitize_key')->alias(function ($key) {
            return preg_replace('/[^a-z0-9_\-]/', '', strtolower($key));
        });
        Monkey\Functions::when('wp_parse_args')->alias(function ($args, $defaults) {
            if (is_object($args)) {
                $r = get_object_vars($args);
            } elseif (is_array($args)) {
                $r =& $args;
            } else {
                wp_parse_str($args, $r);
            }

            if (is_array($defaults)) {
                return array_merge($defaults, $r);
            }

            return $r;
        });
        Monkey\Functions::when('plugin_dir_path')->justReturn(static::$sourcePath);
        Monkey\Functions::when('untrailingslashit')->alias(function ($val) {
            return rtrim($val, '/');
        });
    }

    /**
     * SetUp
     *
     * @since 1.0.0
     */
    protected function setUp()
    {
        parent::setUp();
        Monkey::setUpWP();
        self::defineCommonWPFunctions();
    }

    /**
     * TearDown
     *
     * @since 1.0.0
     */
    protected function tearDown()
    {
        Monkey::tearDownWP();
        parent::tearDown();
    }

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::$sourcePath = dirname(dirname(__DIR__));
    }
}
