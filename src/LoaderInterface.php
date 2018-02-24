<?php
/**
 * LoaderInterface
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @package   TemplateLoader
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
 * Class LoaderInterface
 *
 * @since  2.1.0
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
interface LoaderInterface
{
    /**
     * Set Data
     *
     * Set the data for the view.
     *
     * @since 1.0.0
     * @since 4.0.0 Has been renamed to `withData`
     *
     * @param DataInterface $data The data for the view.
     *
     * @return LoaderInterface An instance of the LoaderInterface for chaining
     */
    public function withData(DataInterface $data);

    /**
     * Set Templates Path
     *
     * Set the templates path. Where to search for a valid file for the template.
     * This also sanitize the templates path.
     *
     * @since 1.0.0
     * @since 4.0.0 Has been renamed to `usingTemplate`
     *
     * @param array|string $templatesPath The templates path.
     *
     * @return LoaderInterface An instance of the LoaderInterface for chaining
     */
    public function usingTemplate($templatesPath);

    /**
     * Fallback to Template
     *
     * @since 4.0.0
     *
     * @param string $path The path to fallback in case the template path set isn't found.
     *                     Used generally into a plugin.
     *
     * @return LoaderInterface An instance of the LoaderInterface for chaining
     */
    public function butFallbackToTemplate($path);

    /**
     * Render
     *
     * @since 1.0.0
     *
     * @throws \Exception In case the template path is incorrect or cannot be located.
     *
     * @return string The template filename if one is located.
     */
    public function render();
}
