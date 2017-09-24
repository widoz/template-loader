<?php
/**
 * DataStorage
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @copyright Copyright (c) 2017, Guido Scialfa
 * @package   TemplateLoader
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
 * Class DataStorage
 *
 * @since   2.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 * @package TemplateLoader
 */
class DataStorage implements \ArrayAccess, \Countable
{
    /**
     * The Container
     *
     * @since  2.0.0
     *
     * @var array The container
     */
    protected $data;

    /**
     * DataStorage constructor
     *
     * @since 2.0.0
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * Offset Exists
     *
     * @since  2.0.0
     *
     * @param mixed $offset
     *
     * @return bool If offset exists or not
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset Get
     *
     * @since  2.0.0
     *
     * @param mixed $offset The offset form which retrieve the data.
     *
     * @return mixed The value at the passed offset
     */
    public function offsetGet($offset)
    {
        if (! $this->offsetExists($offset)) {
            throw new \OutOfBoundsException(sprintf(
                'Key %s does not exists',
                $offset
            ));
        }

        return $this->data[$offset];
    }

    /**
     * Offset Set
     *
     * @since  2.0.0
     *
     * @param mixed $offset The offset where store the data.
     * @param mixed $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * Unset Offset
     *
     * @since  2.0.0
     *
     * @param mixed $offset The offset to remove.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Count Data
     *
     * @since  2.0.0
     *
     * @return int The number of data set
     */
    public function count()
    {
        return count($this->data);
    }
}
