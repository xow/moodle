<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010-2013 Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jonathan Newman <jonathan.newman@catalyst.net.nz>
 * @package totara
 * @subpackage totara_core
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/totara/core/totara.php');

/**
 *  * Resize an image to fit within the given rectange, maintaing aspect ratio
 *
 * @param string Path to image
 * @param string Destination file - without file extention
 * @param int Width to resize to
 * @param int Height to resize to
 * @param string Force image to this format
 *
 * @global $CFG
 * @return string Path to new file else false
 */
function resize_image($originalfile, $destination, $newwidth, $newheight, $forcetype = false) {
    global $CFG;

    require_once($CFG->libdir.'/gdlib.php');

    if(!(is_file($originalfile))) {
        return false;
    }

    $imageinfo = GetImageSize($originalfile);
    if (empty($imageinfo)) {
        return false;
    }

    $image = new stdClass;

    $image->width  = $imageinfo[0];
    $image->height = $imageinfo[1];
    $image->type   = $imageinfo[2];

    $ratiosrc = $image->width / $image->height;

    if ($newwidth/$newheight > $ratiosrc) {
        $newwidth = $newheight * $ratiosrc;
    } else {
        $newheight = $newwidth / $ratiosrc;
    }

    switch ($image->type) {
    case IMAGETYPE_GIF:
        if (function_exists('ImageCreateFromGIF')) {
            $im = ImageCreateFromGIF($originalfile);
            $outputformat = 'png';
        } else {
            notice('GIF not supported on this server');
            return false;
        }
        break;
    case IMAGETYPE_JPEG:
        if (function_exists('ImageCreateFromJPEG')) {
            $im = ImageCreateFromJPEG($originalfile);
            $outputformat = 'jpeg';
        } else {
            notice('JPEG not supported on this server');
            return false;
        }
        break;
    case IMAGETYPE_PNG:
        if (function_exists('ImageCreateFromPNG')) {
            $im = ImageCreateFromPNG($originalfile);
            $outputformat = 'png';
        } else {
            notice('PNG not supported on this server');
            return false;
        }
        break;
    default:
        return false;
    }

    if ($forcetype) {
        $outputformat = $forcetype;
    }

    $destname = $destination.'.'.$outputformat;

    if (function_exists('ImageCreateTrueColor') and $CFG->gdversion >= 2) {
        $im1 = ImageCreateTrueColor($newwidth,$newheight);
    } else {
        $im1 = ImageCreate($newwidth, $newheight);
    }
    ImageCopyBicubic($im1, $im, 0, 0, 0, 0, $newwidth, $newheight, $image->width, $image->height);

    switch($outputformat) {
    case 'jpeg':
        imagejpeg($im1, $destname, 90);
        break;
    case 'png':
        imagepng($im1, $destname, 9);
        break;
    default:
        return false;
    }
    return $destname;
}


/**
 * hook to add extra sticky-able page types.
 */
function local_get_sticky_pagetypes() {
    return array(
        // not using a constant here because we're doing funky overrides to PAGE_COURSE_VIEW in the learning path format
        // and it clobbers the page mapping having them both defined at the same time
        'Totara' => array(
            'id' => 'Totara',
            'lib' => '/totara/core/lib.php',
            'name' => 'Totara'
        ),
    );
}

/**
 * Require login for ajax supported scripts
 *
 * @see require_login()
 */
function ajax_require_login($courseorid = null, $autologinguest = true, $cm = null, $setwantsurltome = true,
        $preventredirect = false) {
    if (is_ajax_request($_SERVER)) {
        try {
            require_login($courseorid, $autologinguest, $cm, $setwantsurltome, true);
        } catch (require_login_exception $e) {
            ajax_result(false, $e->getMessage());
            exit();
        }
    } else {
        require_login($courseorid, $autologinguest, $cm, $setwantsurltome, $preventredirect);
    }
}

/**
 * Return response to AJAX request
 * @param bool $success
 * @param string $message
 */
function ajax_result($success = true, $message = '') {
    if ($success) {
        echo 'success';
    } else {
        header('HTTP/1.0 500 Server Error');
        echo $message;
    }
}

/**
 * Drop table if exists
 *
 * @param string $table
 * @return bool
 */
function sql_drop_table_if_exists($table) {
    global $DB;
    switch ($DB->get_dbfamily()) {
        case 'mssql':
            $sql = "IF OBJECT_ID('dbo.{$table}','U') IS NOT NULL DROP TABLE dbo.{$table}";
            break;
        case 'mysql':
            $sql = "DROP TABLE IF EXISTS `{$table}`";
            break;
        case 'postgres':
        default:
            $sql = "DROP TABLE IF EXISTS \"{$table}\"";
            break;
    }
    return $DB->execute($sql);
}

/**
 * Reorder elements based on order field
 *
 * @param int $id Element ID
 * @param int $pos It's new relative position
 * @param string $table Table name
 * @param string $parentfield Field name
 * @param string $orderfield Order field name
 */
function db_reorder($id, $pos, $table, $parentfield, $orderfield='sortorder') {
    global $DB;
    $transaction = $DB->start_delegated_transaction();
    $sql = 'SELECT tosort.id
              FROM {'.$table.'} tosort
              LEFT JOIN {'.$table.'} element
                ON (element.'.$parentfield.' = tosort.'.$parentfield.')
             WHERE element.id = ?
               AND tosort.id <> ?
             ORDER BY tosort.'.$orderfield;
    $records = $DB->get_records_sql($sql, array($id, $id));
    $newpos = 0;
    $todb = new stdClass();
    $todb->id = $id;
    $todb->$orderfield = $pos;
    foreach ($records as $record) {
        if ($newpos == $pos) {
            ++$newpos;
        }
        $record->$orderfield = $newpos;
        $DB->update_record($table, $record);
        ++$newpos;
    }
    $DB->update_record($table, $todb);
    $transaction->allow_commit();
}

/**
 * Include code to pull in site version check code to notify the admin if
 * their site is not on the most current release.
 *
 * This function should only be included on the admin notification page.
 */
function totara_site_version_tracking() {
    global $CFG, $PAGE, $TOTARA;

    require_once($CFG->dirroot.'/totara/core/js/lib/setup.php');
    local_js();

    //Params for JS
    $totara_version = $TOTARA->version;
    $major_version = substr($TOTARA->version, 0, 3);
    $siteurl = parse_url($CFG->wwwroot);
    if (!empty($siteurl['scheme'])) {
        $protocol = $siteurl['scheme'];
    } else if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $protocol = 'https';
    } else {
        $protocol = 'http';
    }

    $PAGE->requires->strings_for_js(array('unsupported_branch_text', 'supported_branch_text', 'supported_branch_old_release_text'), 'totara_core', $major_version);
    $PAGE->requires->strings_for_js(array('old_release_text_singular', 'old_release_text_plural', 'old_release_security_text_singular', 'old_release_security_text_plural', 'totarareleaselink'), 'totara_core');

    $args = array('args' => '{"totara_version":"'.$totara_version.'", "major_version":"'.$major_version.'", "protocol":"'.$protocol.'"}');

    $jsmodule = array(
        'name' => 'totara_version_tracking',
        'fullpath' => '/totara/core/js/version_tracking.js',
        'requires' => array('json'));
    $PAGE->requires->js_init_call('M.totara_version_tracking.init', $args, false, $jsmodule);

}

function totara_core_cron() {

    // Temporary manager tasks.
    totara_update_temporary_managers();

    return true;
}

function totara_update_temporary_managers() {
    global $CFG, $DB;

    if (empty($CFG->enabletempmanagers)) {
        // Unassign all current temporary managers.
        if ($rs = $DB->get_recordset('temporary_manager', null, '', 'userid')) {
            mtrace('Removing obsolete temporary managers...');
            foreach ($rs as $tmassignment) {
                totara_unassign_temporary_manager($tmassignment->userid);
            }
        }

        return true;
    }

    if (!empty($CFG->tempmanagerrestrictselection)) {
        // Ensure only users that are currently managers are assigned as temporary managers.
        // We need this check for scenarios where tempmanagerrestrictselection was previously disabled.
        $sql = "SELECT DISTINCT tm.userid
                  FROM {temporary_manager} tm
             LEFT JOIN {pos_assignment} pa ON tm.tempmanagerid = pa.managerid
                 WHERE pa.managerid IS NULL";
        if ($rs = $DB->get_recordset_sql($sql)) {
            mtrace('Removing non-manager temporary managers...');
            foreach ($rs as $assignment) {
                totara_unassign_temporary_manager($assignment->userid);
            }
        }
    }

    // Remove expired temporary managers.
    $timenow = time();
    $expiredmanagers = $DB->get_records_select('temporary_manager', 'expirytime < ?', array($timenow));
    if (!empty($expiredmanagers)) {
        mtrace('Removing expired temporary managers...');

        foreach ($expiredmanagers as $m) {
            totara_unassign_temporary_manager($m->userid);
        }

        mtrace('DONE Removing expired temporary managers');
    }
}
