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
 * ldap tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  Damyon Wiese, Iñaki Arenaza 2014
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/ldaplib.php');

class core_ldaplib_testcase extends advanced_testcase {

    public function test_ldap_addslashes() {
        // See http://tools.ietf.org/html/rfc4514#section-5.2 if you want
        // to add additional tests.

        $tests = array(
            array (
                'test' => 'Simplest',
                'expected' => 'Simplest',
            ),
            array (
                'test' => 'Simple case',
                'expected' => 'Simple\\20case',
            ),
            array (
                'test' => 'Medium ‒ case',
                'expected' => 'Medium\\20‒\\20case',
            ),
            array (
                'test' => '#Harder+case#',
                'expected' => '\\23Harder\\2bcase\\23',
            ),
            array (
                'test' => ' Harder (and); harder case ',
                'expected' => '\\20Harder\\20(and)\\3b\\20harder\\20case\\20',
            ),
            array (
                'test' => 'Really \\0 (hard) case!\\',
                'expected' => 'Really\\20\\5c0\\20(hard)\\20case!\\5c',
            ),
            array (
                'test' => 'James "Jim" = Smith, III',
                'expected' => 'James\\20\\22Jim\22\\20\\3d\\20Smith\\2c\\20III',
            ),
            array (
                'test' => '  <jsmith@example.com> ',
                'expected' => '\\20\\20\\3cjsmith@example.com\\3e\\20',
            ),
        );


        foreach ($tests as $test) {
            $this->assertSame($test['expected'], ldap_addslashes($test['test']));
        }
    }

    public function test_ldap_stripslashes() {
        // See http://tools.ietf.org/html/rfc4514#section-5.2 if you want
        // to add additional tests.

        // IMPORTANT NOTICE: While ldap_addslashes() only produces one
        // of the two defined ways of escaping/quoting (the ESC HEX
        // HEX way defined in the grammar in Section 3 of RFC-4514)
        // ldap_stripslashes() has to deal with both of them. So in
        // addition to testing the same strings we test in
        // test_ldap_stripslashes(), we need to also test strings
        // using the second method.

        $tests = array(
            array (
                'test' => 'Simplest',
                'expected' => 'Simplest',
            ),
            array (
                'test' => 'Simple\\20case',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Simple\\ case',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Simple\\ \\63\\61\\73\\65',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Medium\\ ‒\\ case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => 'Medium\\20‒\\20case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => 'Medium\\20\\E2\\80\\92\\20case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => '\\23Harder\\2bcase\\23',
                'expected' => '#Harder+case#',
            ),
            array (
                'test' => '\\#Harder\\+case\\#',
                'expected' => '#Harder+case#',
            ),
            array (
                'test' => '\\20Harder\\20(and)\\3b\\20harder\\20case\\20',
                'expected' => ' Harder (and); harder case ',
            ),
            array (
                'test' => '\\ Harder\\ (and)\\;\\ harder\\ case\\ ',
                'expected' => ' Harder (and); harder case ',
            ),
            array (
                'test' => 'Really\\20\\5c0\\20(hard)\\20case!\\5c',
                'expected' => 'Really \\0 (hard) case!\\',
            ),
            array (
                'test' => 'Really\\ \\\\0\\ (hard)\\ case!\\\\',
                'expected' => 'Really \\0 (hard) case!\\',
            ),
            array (
                'test' => 'James\\20\\22Jim\\22\\20\\3d\\20Smith\\2c\\20III',
                'expected' => 'James "Jim" = Smith, III',
            ),
            array (
                'test' => 'James\\ \\"Jim\\" \\= Smith\\, III',
                'expected' => 'James "Jim" = Smith, III',
            ),
            array (
                'test' => '\\20\\20\\3cjsmith@example.com\\3e\\20',
                'expected' => '  <jsmith@example.com> ',
            ),
            array (
                'test' => '\\ \\<jsmith@example.com\\>\\ ',
                'expected' => ' <jsmith@example.com> ',
            ),
            array (
                'test' => 'Lu\\C4\\8Di\\C4\\87',
                'expected' => 'Lučić',
            ),
        );

        foreach ($tests as $test) {
            $this->assertSame($test['expected'], ldap_stripslashes($test['test']));
        }
    }

    /**
     * Tests for ldap_normalise_objectclass.
     *
     * @dataProvider ldap_normalise_objectclass_provider
     * @param array $args Arguments passed to ldap_normalise_objectclass
     * @param string $expected The expected objectclass filter
     */
    public function test_ldap_normalise_objectclass($args, $expected) {
        $this->assertEquals($expected, call_user_func_array('ldap_normalise_objectclass', $args));
    }

    /**
     * Data provider for the test_ldap_normalise_objectclass testcase.
     *
     * @return array of testcases.
     */
    public function ldap_normalise_objectclass_provider() {
        return array(
            'Empty value' => array(
                array(null),
                '(objectClass=*)',
            ),
            'Empty value with different default' => array(
                array(null, 'lion'),
                '(objectClass=lion)',
            ),
            'Supplied unwrapped objectClass' => array(
                array('objectClass=tiger'),
                '(objectClass=tiger)',
            ),
            'Supplied string value' => array(
                array('leopard'),
                '(objectClass=leopard)',
            ),
            'Supplied complex' => array(
                array('(&(objectClass=cheetah)(enabledMoodleUser=1))'),
                '(&(objectClass=cheetah)(enabledMoodleUser=1))',
            ),
        );
    }

    /**
     * Tests for ldap_connect_moodle.
     *
     * NOTE: in order to execute this test you need to set up OpenLDAP server with core,
     *       cosine, nis and internet schemas and add configuration constants to
     *       config.php or phpunit.xml configuration file. Also, some of the sub-tests need
     *       a non-reachable LDAP server, in order to test connection timeouts, LDAP
     *       server failover, etc., so you also need to define a non-reachable LDAP server.
     *
     * define('TEST_LDAPLIB_HOST_URL', 'ldap://127.0.0.1');
     * define('TEST_LDAPLIB_NON_REACHABLE_HOST_URL', 'ldap://10.255.255.1');
     * define('TEST_LDAPLIB_BIND_DN', 'cn=someuser,dc=example,dc=local');
     * define('TEST_LDAPLIB_BIND_PW', 'somepassword');
     * define('TEST_LDAPLIB_DOMAIN',  'dc=example,dc=local');
     *
     */
    public function test_ldap_connect_moodle() {
        $this->resetAfterTest();

        if (!defined('TEST_LDAPLIB_HOST_URL') or !defined('TEST_LDAPLIB_NON_REACHABLE_HOST_URL') or
                !defined('TEST_LDAPLIB_BIND_DN') or !defined('TEST_LDAPLIB_BIND_PW') or
                !defined('TEST_LDAPLIB_DOMAIN')) {
            $this->markTestSkipped('External LDAP test server not configured.');
        }

        // Run each indidivual test.
        $this->ldap_connect_timeout();
    }

    /**
     * Test that the ldap connection timeout option works as expected.
     */
    protected function ldap_connect_timeout() {
        $tests = array(
            array(
                'hosturl' => TEST_LDAPLIB_HOST_URL, // Url of the LDAP server to connect to.
                'timeout' => -1,    // Configured timeout (in integral seconds). -1 means no timeout (use operating system default timeout).
                'maxexpected' => 2, // Maximum expected elapsed connection/timeout time (in integral seconds).
                                    // Make it a bit higher than the specified timeout, to account for additional unknown latency sources.
                'shouldconnect' => true,
            ),
            array(
                'hosturl' => TEST_LDAPLIB_NON_REACHABLE_HOST_URL,
                'timeout' => 5,
                'maxexpected' => 7,
                'shouldconnect' => false,
            ),
            array(
                'hosturl' => TEST_LDAPLIB_NON_REACHABLE_HOST_URL,
                'timeout' => -1,
                'maxexpected' => 185, // According to http://man7.org/linux/man-pages/man7/tcp.7.html, the default value
                                      // of tcp_syn_retries in Linux corresponds to approximately 180 seconds. So play on
                                      // the safe side.
                'shouldconnect' => false,
            ),
        );

        $debuginfo = '';
        foreach ($tests as $test) {
            // Try to connect the server.
            $startime = microtime(true);
            $connection = ldap_connect_moodle($test['hosturl'], 3, 'rfc2307', TEST_LDAPLIB_BIND_DN,
                                              TEST_LDAPLIB_BIND_PW, LDAP_DEREF_NEVER, $debuginfo,
                                              false, $test['timeout']);
            $elapsed = microtime(true) - $startime;
            if (($connection === false) && ($test['shouldconnect'])) {
                $this->markTestSkipped('Cannot connect to LDAP test server (and we expected to!). Debug info: '.$debuginfo);
            }
            $this->assertLessThanOrEqual($test['maxexpected'], $elapsed);
        }
    }
}
