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

defined('WPINC') || die;

/**
 * Class Engine
 *
 * @version   1.0.0
 * @package   TemplateLoader
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 */
class Loader
{
    /**
     * Name
     *
     * @since  1.0.0
     * @access protected
     *
     * @var string The name of the current template instance.
     */
    protected $name;

    /**
     * Data
     *
     * @since  1.0.0
     * @access protected
     *
     * @var \stdClass The data object
     */
    protected $data;

    protected function getPluginFilePath($tmplPath)
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
            $path = Filesystem::getPluginDirPath($tmplPath);
        } elseif (is_string($tmplPath)) {
            $path = Filesystem::getPluginDirPath($tmplPath);
        }

        return $path;
    }

    protected function sanitizeTemplatePath($tmplPath)
    {
        $tmp = null;

        if (is_string($tmplPath)) {
            // Sanitize template path and remove the path separator.
            // locate_template build the path in this way {STYLESHEET|TEMPLATE}PATH . '/' . $template_name.
            $tmp = ltrim(preg_replace('[^a-zA-Z0-9\-\_]', '', $tmplPath), '/');
        } elseif (is_array($tmplPath)) {
            foreach ($tmplPath as $path) {
                $tmp[] = $this->sanitizeTemplatePath($path);
            }
        }

        return $tmp;
    }

    protected function getFilePath($tmplPath)
    {
        $tmplPath = $this->sanitizeTemplatePath($tmplPath);

        // Retrieve the theme file path from child or parent.
        $filePath = locate_template($tmplPath, false, false);

        /**
         * Use Plugin
         *
         * @since 1.0.0
         *
         * @param string 'yes' To search within the plugin directory. False otherwise.
         */
        $usePlugin = apply_filters('tmploader_use_plugin', 'yes');

        // Looking for the file within the plugin if allowed.
        if (! $filePath && 'yes' === $usePlugin) {
            $filePath = $this->getPluginFilePath($tmplPath);
        }

        return $filePath;
    }

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @param string    $name The name of the current template instance.
     * @param \stdClass $data The data object containing the data for the view template.
     */
    public function __construct($name, \stdClass $data = null)
    {
        $this->name = $name;
        $this->data = $data;
    }

    public function setData(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Render
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \Exception                In case the template path is incorrect or cannot be located.
     * @throws \InvalidArgumentException In case the filePath is empty or incorrect.
     *
     * @return string The template filename if one is located.
     */
    public function render($tmpPath)
    {
        // Retrieve the file path.
        $filePath = $this->getFilePath($tmpPath);

        if (! $filePath) {
            throw new \InvalidArgumentException(__('Template Loader, wrong path format.'));
        }

        /**
         * Filter Data
         *
         * @since 1.0.0
         *
         * @param \stdClass $data The template data.
         * @param string    $name The name of the current template instance.
         */
        $data = apply_filters('tmploader_template_engine_data', $this->data, $this->name);

        /**
         * Filter Specific Data
         *
         * @since 1.0.0
         *
         * @param \stdClass $data The template data.
         */
        $data = apply_filters("tmploader_template_engine_data_{$this->name}", $data);

        // If data is empty, no other actions are needed.
        if (! $data) {
            return '';
        }

        // Empty string or bool depend by the conditional above.
        if (! file_exists($filePath)) {
            throw new \Exception(sprintf(
                __('Template Loader: No way to locate the template %s.'),
                $filePath
            ));
        }

        // Include the template.
        // Don't use include_once because some templates/views may need to be included multiple times.
        // @todo create a loaderInclude and pass $data. Avoid using $this within the file.
        include $filePath;

        return $filePath;
    }
}
