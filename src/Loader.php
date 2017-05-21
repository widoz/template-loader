<?php
namespace TemplateLoader;

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

/**
 * Class Engine
 *
 * @version 1.0.0
 * @package TemplateLoader
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
class Loader
{
    /**
     * Template Slug Sanitize Pattern
     *
     * @since  1.0.0
     * @access protected
     *
     * @var string The pattern used to sanitize the template slug.
     */
    const TEMPLATE_SLUG_SANITIZE_PATTERN = '/[^a-z0-9\-\_]*/';

    /**
     * Template Path Sanitize Pattern
     *
     * @since  1.0.0
     * @access protected
     *
     * @var string The pattern used to sanitize the templates path.
     */
    const TEMPLATE_PATH_SANITIZE_PATTERN = '/[^a-zA-Z0-9\/\-\_\.]+/';

    /**
     * Name
     *
     * @since  1.0.0
     * @access protected
     *
     * @var string The slug of the current template instance.
     */
    protected $slug;

    /**
     * Templates Path
     *
     * @since  1.0.0
     * @access protected
     *
     * @var array The list of the view files
     */
    protected $templatesPath;

    /**
     * Data
     *
     * @since  1.0.0
     * @access protected
     *
     * @var \stdClass The data object
     */
    protected $data;

    /**
     * Filesystem
     *
     * @since  1.0.0
     * @access protected
     *
     * @var Filesystem The class instance
     */
    protected $filesystem;

    /**
     * Sanitize Template Slug
     *
     * @since  1.0.0
     * @access protected
     *
     * @param string $slug The slug of the template.
     *
     * @return string The sanitize slug. May be empty.
     */
    protected function sanitizeTemplateSlug($slug)
    {
        return str_replace('-', '_', preg_replace(self::TEMPLATE_SLUG_SANITIZE_PATTERN, '', $slug));
    }

    /**
     * Sanitize template file path
     *
     * @todo   Explode every path and sanitize every part?
     *
     * @since  1.0.0
     * @access protected
     *
     * @param array $tmplPath The paths for the view.
     *
     * @return array The sanitize templates path
     */
    protected function sanitizeTemplatePath(array $tmplPath)
    {
        $tmp = array();

        foreach ($tmplPath as $path) {
            // Sanitize template path and remove the path separator.
            // locate_template build the path in this way {STYLESHEET|TEMPLATE}PATH . '/' . $template_name.
            $tmp[] = $this->filesystem->sanitizePath(
                trim(preg_replace(self::TEMPLATE_PATH_SANITIZE_PATTERN, '', $path), '/')
            );
        }

        return $tmp;
    }

    /**
     * Retrieve the file path
     *
     * @since  1.0.0
     * @access protected
     *
     * @param array $tmplPath The paths of the view files.
     *
     * @return string The first path found. Empty string if not found.
     */
    protected function getPluginFilePath(array $tmplPath)
    {
        $path = '';

        if (is_array($tmplPath)) {
            foreach ($tmplPath as $path) {
                // Get the file path from the current template path item.
                $path = $this->getPluginFilePath($path);

                // We have the file?
                if (file_exists($path)) {
                    break;
                }
            }
        } elseif (is_string($tmplPath)) {
            $path = $this->filesystem->getPluginDirPath($tmplPath);
        }

        return $path;
    }

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @param string       $slug         The slug of the current template instance.
     * @param Filesystem   $filesystem   The filesystem class instance to use internally.
     * @param string|array $templatePath The template paths where looking for the template file. Optional.
     */
    public function __construct($slug, Filesystem $filesystem, $templatePath = null)
    {
        $this->slug       = $this->sanitizeTemplateSlug($slug);
        $this->filesystem = $filesystem;
        $this->data       = null;

        $this->setTemplatePath($templatePath);
    }

    /**
     * Set Data
     *
     * Set the data for the view.
     *
     * @since  1.0.0
     * @access public
     *
     * @param \stdClass $data The data for the view.
     *
     * @return void
     */
    public function setData(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Set Templates Path
     *
     * Set the templates path. Where to search for a valid file for the template.
     * This also sanitize the templates path.
     *
     * @since  1.0.0
     * @access public
     *
     * @param array|string $templatesPath The templates path.
     */
    public function setTemplatePath($templatesPath)
    {
        $templatesPath = (array)$templatesPath;
        // Sanitize and retrieve the found template path.
        $this->templatesPath = $this->sanitizeTemplatePath($templatesPath);
    }

    /**
     * Get Templates Path
     *
     * @since  1.0.0
     * @access public
     *
     * @return array The templates path list
     */
    public function getTemplatePath()
    {
        return $this->templatesPath;
    }

    /**
     * Get the file path
     *
     * Retrieve the file path for the view, hierarchy try to find the file within the child, parent and last within
     * the plugin.
     *
     * @uses   locate_template() To locate the view file within the theme (child or parent).
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The found file path. Empty string if not found.
     */
    public function getFilePath()
    {
        // Try to retrieve the theme file path from child or parent for first.
        // Fallback to Plugin templates path.
        $filePath = locate_template($this->templatesPath, false, false);

        /**
         * Use Plugin
         *
         * @since 1.0.0
         *
         * @param        string    'yes' To search within the plugin directory. False otherwise.
         * @param string $filePath The current view path.
         */
        $usePlugin = apply_filters('tmploader_use_plugin', 'yes', $filePath);

        // Looking for the file within the plugin if allowed.
        if (! $filePath && 'yes' === $usePlugin) {
            $filePath = $this->getPluginFilePath($this->templatesPath);
        }

        return $filePath;
    }

    /**
     * Render
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \Exception In case the template path is incorrect or cannot be located.
     *
     * @return string The template filename if one is located.
     */
    public function render()
    {
        // Try to retrieve the file path for the template.
        $filePath = $this->getFilePath();

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

        // If data is empty, no other actions are needed.
        if (! $data) {
            return '';
        }

        // Include the template.
        // Don't use include_once because some templates/views may need to be included multiple times.
        // @todo create a loaderInclude and pass $data. Avoid using $this within the file.
        // @codingStandardsIgnoreStart
        include $filePath;

        // @codingStandardsIgnoreEng

        return $filePath;
    }
}
