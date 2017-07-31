<?php
/**
 * Sanitizer
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

namespace TemplateLoader;

/**
 * Class Sanitizer
 *
 * @since   2.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 * @package TemplateLoader
 */
class Sanitizer
{
    /**
     * Slug Sanitizer Pattern
     *
     * @since  2.0.0
     *
     * @var string The pattern for array keys
     */
    const SLUG_SANITIZE_PATTERN = '/[^a-z0-9\-\_]*/';

    /**
     * Path Sanitizer Pattern
     *
     * @since  2.0.0
     *
     * @var string The pattern to sanitize the paths
     */
    const PATH_SANITIZE_PATTERN = '/[^a-zA-Z0-9\/\-\_\.]+/';

    /**
     * Sanitize path
     *
     * @since  2.0.0
     *
     * @param string $path The path to sanitize
     *
     * @return string The sanitized path.
     */
    public static function sanitizePath($path)
    {
        while (false !== strpos($path, '..')) {
            $path = str_replace('..', '', $path);
        }

        $path = ('/' !== $path) ? $path : '';

        return $path;
    }

    /**
     * Sanitize Slug By RegExp
     *
     * @since  1.0.0
     *
     * @param string $slug The slug to sanitize.
     *
     * @return string The sanitize slug. May be empty.
     */
    public static function sanitizeSlugRegExp($slug)
    {
        return preg_replace(static::SLUG_SANITIZE_PATTERN, '', $slug);
    }

    /**
     * Sanitize file path By RegExp
     *
     * @since  1.0.0
     *
     * @param string $path The path to sanitize.
     *
     * @return string The sanitized path
     */
    public static function sanitizePathRegExp($path)
    {
        // Sanitize template path and remove the path separator.
        // locate_template build the path in this way {STYLESHEET|TEMPLATE}PATH . '/' . $template_name.
        return self::sanitizePath(
            trim(preg_replace(static::PATH_SANITIZE_PATTERN, '', $path), '/')
        );
    }
}
