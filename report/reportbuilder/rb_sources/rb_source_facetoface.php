<?php

defined('MOODLE_INTERNAL') || die();

class rb_source_facetoface extends rb_base_source {
    public $base, $joinlist, $columnoptions, $filteroptions;

    function __construct() {
        $this->base = '{facetoface}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = array(
            new rb_column_option(
                'course',
                'fullname',
                'Course Fullname',
                'base.fullname'
            )
        );
        $this->filteroptions = array();
        $this->sourcetitle   = "Face-to-Face";

        parent::__construct();
    }
    
    protected function define_joinlist() {

        $joinlist = array(
            new rb_join(
                'mods',
                'LEFT',
                '(SELECT cm.course, ' .
                sql_group_concat(sql_cast2char('m.name'), '|', true) .
                " AS list FROM {course_modules} cm LEFT JOIN {modules} m ON m.id = cm.module GROUP BY cm.course)",
                'mods.course = base.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
        );

        // include some standard joins
        $this->add_course_category_table_to_joinlist($joinlist,
            'base', 'category');
        $this->add_tag_tables_to_joinlist('course', $joinlist, 'base', 'id');
        $this->add_cohort_course_tables_to_joinlist($joinlist, 'base', 'id');

        return $joinlist;
    }
}
?>