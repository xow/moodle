<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Filter converting URLs in the text to HTML links
 *
 * @package    filter
 * @subpackage externalprotocol
 * @copyright  2014 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class filter_externalprotocol extends moodle_text_filter {

    /**
     * @var array global configuration for this filter
     *
     * This might be eventually moved into parent class if we found it
     * useful for other filters, too.
     */
    protected static $globalconfig;

    /**
     * Apply the filter to the text
     *
     * @see filter_manager::apply_filter_chain()
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = array()) {
        if (!isset($options['originalformat'])) {
            // if the format is not specified, we are probably called by {@see format_string()}
        }
        if (in_array($options['originalformat'], explode(',', $this->get_global_config('formats')))) {
            $text = $this->convert_protocols($text);
        }
        return $text;
    }

    /**
     * Returns the global filter setting
     *
     * If the $name is provided, returns single value. Otherwise returns all
     * global settings in object. Returns null if the named setting is not
     * found.
     *
     * @param mixed $name optional config variable name, defaults to null for all
     * @return string|object|null
     */
    protected function get_global_config($name=null) {
        $this->load_global_config();
        if (is_null($name)) {
            return self::$globalconfig;

        } elseif (array_key_exists($name, self::$globalconfig)) {
            return self::$globalconfig->{$name};

        } else {
            return null;
        }
    }

    /**
     * Makes sure that the global config is loaded in self::$globalconfig
     *
     * @return void
     */
    protected function load_global_config() {
        if (is_null(self::$globalconfig)) {
            self::$globalconfig = get_config('filter_externalprotocol');
        }
    }

    /**
     * Given some text this function converts any external embedded content URLs it finds from one protocol to another
     *
     * @param string $text Passed in by reference. The string to be searched for external content.
     */
    protected function convert_protocols($text) {
        global $CFG;

        $text = $this->convert_tags(array(array('[A-Za-z]*', 'src')), 'http', 'https', $text);
        $text = $this->convert_tags(array(array('link', 'href')), 'http', 'https', $text);
        return $text;
    }

    /**
     * Converts given attributes of given tags
     *
     * @param array[] $pairs An array of hashes: tag as the key and attribute as the value
     * @param $oldprotocol Old protocol, e.g. http
     * @param $oldprotocol New protocol, e.g. https
     * @param $text Text to be converted
     */
    protected function convert_tags($pairs, $oldprotocol, $newprotocol, $text) {

        $whitelist = $this->get_blacklist();

        $blacklist = $this->get_whitelist();

        foreach ($pairs as $pair) {
            $text = $this->convert_tag_protocol($pair[0], $pair[1], $oldprotocol, $newprotocol, $text, $whitelist, $blacklist);
        }

        return $text;
    }

    /**
     * Converts the protocol of urls in a given attribute of all instances of a given tag
     *
     * @param string $text The string to be searched for the tags.
     * @return string The string, with protocols replaced
     */
    protected function convert_tag_protocol($tag, $attribute, $oldprotocol, $newprotocol, $text, $whitelist, $blacklist) {
        $search = '/(<' . $tag . '\s[^>]*' . $attribute . '\s*=\s*["\'])' . $oldprotocol .
                            '(:\/\/)([^"\'\/]*)(' . $whitelist . ')([^"\'])/';

        return preg_replace($search, '$1' . $newprotocol . '$2' . '$3$4$5', $text);
    }

    protected function get_blacklist() {
        if (!empty(self::$globalconfig->blacklist)) {
            return implode(explode(',', self::$globalconfig->blacklist), '|');
        } else {
            return '[A-Za-z]+'; // Any.
        }
    }

    protected function get_whitelist() {
        if (!empty(self::$globalconfig->whitelist)) {
            return implode(explode(',', self::$globalconfig->whitelist), '|');
        } else {
            return '[A-Za-z]+'; // Any.
        }
    }
}
