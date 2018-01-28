<?php
/**
 * Template Loader
 *
 * @since      1.0.0
 * @package    TemplateLoader
 * @copyright  Copyright (c) 2016, Guido Scialfa
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

namespace TemplateLoader;

/**
 * Class Engine
 *
 * @version 1.0.0
 * @package TemplateLoader
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Loader implements LoaderInterface
{
    /**
     * Name
     *
     * @since  1.0.0
     *
     * @var string The slug of the current template instance.
     */
    private $slug;

    /**
     * Templates Path
     *
     * @since  1.0.0
     *
     * @var array The list of the view files
     */
    private $templatesPath;

    /**
     * Data
     *
     * @since  1.0.0
     *
     * @var \stdClass The data object
     */
    private $data;

    /**
     * Data Storage
     *
     * @since  2.0.0
     *
     * @var DataStorage The instance of the storage where store the rendered templates
     */
    private $dataStorage;

    /**
     * Default Path
     *
     * @since 3.0.0
     *
     * @var string The default path where search for the template file.
     */
    private $defaultPath;

    /**
     * Construct
     *
     * @since 1.0.0
     * @since 4.0.0 Removed template and default path parameters.
     *
     * @param string      $slug    The slug of the current template instance.
     * @param DataStorage $storage A data storage instance where store found and used templates
     *                             path.
     */
    public function __construct($slug, DataStorage $storage)
    {
        $this->slug        = Sanitizer::sanitizeSlugRegExp($slug);
        $this->dataStorage = $storage;
        $this->data        = null;
        $this->defaultPath = null;
    }

    /**
     * @inheritdoc
     */
    public function withData(DataInterface $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function usingTemplate($template)
    {
        $template = (array)$template;
        // Sanitize and retrieve the found template path.
        $this->templatesPath = array_map(
            ['TemplateLoader\\Sanitizer', 'sanitizePathRegExp'],
            $template
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fallbackToTemplate($path)
    {
        $this->defaultPath = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        // Retrieve the template from the storage if exists.
        // Try to retrieve the file path for the template otherwise.
        $filePath = isset($this->dataStorage[$this->slug]) ?
            $this->dataStorage[$this->slug] :
            $this->locateFile();

        // Empty string or bool depend by the conditional above.
        if (! file_exists($filePath)) {
            throw new \Exception(sprintf(
                'Template Loader: No way to locate the template %s.',
                $filePath
            ));
        }

        /**
         * Filter Data
         *
         * @since 1.0.0
         *
         * @param \stdClass $data The template data.
         * @param string    $slug The slug of the current template instance.
         */
        $data = apply_filters('tmploader_template_engine_data', $this->data, $this->slug);

        /**
         * Filter Specific Data
         *
         * @since 1.0.0
         *
         * @param \stdClass $data The template data.
         */
        $data = apply_filters("tmploader_template_engine_data_{$this->slug}", $data);

        // If data is empty, no other actions is needed.
        if (! $data) {
            return '';
        }

        /**
         * Filter Path
         *
         * @since 2.1.0
         *
         * @param string    $filePath The path of the file.
         * @param \stdClass $data     The template data.
         */
        $filePath = apply_filters('tmploader_template_file_path', $filePath, $data);

        // Bind $data so, we can referrer to `$this` within the template.
        $includePathClosure = \Closure::bind(function () use ($filePath) {
            include $filePath;
        }, $data);

        // Include the template from the closure.
        $includePathClosure();

        // After the template has been rendered, store it for a next use.
        $this->dataStorage[$this->slug] = $filePath;

        return $filePath;
    }

    /**
     * Locate template file
     *
     * Locate the file path for the view, hierarchy try to find the file within the child, parent
     * and last within the plugin.
     *
     * @uses   locate_template() To locate the view file within the theme (child or parent).
     *
     * @since  2.1.0
     *
     * @return string The found file path. Empty string if not found.
     */
    private function locateFile()
    {
        // Try to retrieve the theme file path from child or parent for first.
        // Fallback to Plugin templates path.
        $filePath = locate_template($this->templatesPath, false, false);

        // Looking for the file within the plugin if allowed.
        if (! $filePath) {
            $filePath = $this->defaultPath;
        }

        return $filePath;
    }
}
