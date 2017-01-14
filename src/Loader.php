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

    /**
     * Template Path
     *
     * @since  1.0.0
     * @access protected
     *
     * @var string The template path
     */
    protected $templatePath;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @param string    $name         The name of the current template instance.
     * @param \stdClass $data         The data object containing the data for the view template.
     * @param string    $templatePath The path of the template to use.
     */
    public function __construct($name, \stdClass $data, $templatePath)
    {
        $this->name         = $name;
        $this->data         = $data;
        $this->templatePath = $templatePath;
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
        if (is_string($this->templatePath)) {
            // Sanitize template path and remove the path separator.
            // locate_template build the path in this way {STYLESHEET|TEMPLATE}PATH . '/' . $template_name.
            $this->templatePath = ltrim(preg_replace('[^a-zA-Z0-9\-\_]', '', $this->templatePath), '/');
        }

        if (! $this->templatePath) {
            throw new \Exception(__('Template Loader, wrong path format'));
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
        $data = apply_filters("tmploader_template_engine_data_{$this->name}", $this->data);

        // If data is empty, no other actions is needed.
        if (! $data) {
            return '';
        }

        // Retrieve the theme file path from child or parent.
        $viewPath = locate_template($this->templatePath, false, false);

        if (! $viewPath) {
            if (! is_array($this->templatePath)) {
                $viewPath = Filesystem::getPluginDirPath($this->templatePath);
            } else {
                foreach ($this->templatePath as $path) {
                    $viewPath = Filesystem::getPluginDirPath($path);
                    if (file_exists($viewPath)) {
                        break;
                    }
                }
            }
        }

        // Empty string or bool depend by the conditional above.
        if (! file_exists($viewPath)) {
            throw new \Exception(sprintf(
                __('Template Loader: No way to locate the template %s.'),
                $viewPath
            ));
        }

        // Include the template.
        // Don't use include_once because some templates/views may need to be included multiple times.
        // @todo create a loaderInclude and pass $data. Avoid using $this within the file.
        include $viewPath;

        return $viewPath;
    }
}
