<?php

include_once 'admin_include.php';

if(isset($_POST) && isset($_POST['lesson_id'])){
    if($_POST['lesson_id'] == '0'){
        $data_arr = (object)['course_id'=>$_POST['course_id'], 'lesson'=>$_POST['lesson_name'], 'sort_order'=>$_POST['sort_order'],'created_by'=>$USER->id,'quiz_id'=>$_POST['quiz_id'],'reading_time'=>$_POST['reading_time'], 'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        $DB->insert_record('eblix_lessons', $data_arr);
        redirect($CFG->wwwroot.'/lessons/list.php?course_id='.$_POST['course_id'], 'Lesson added successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }else{
        $data_arr = (object)['id'=>$_POST['lesson_id'],'course_id'=>$_POST['course_id'], 'lesson'=>$_POST['lesson_name'],'quiz_id'=>$_POST['quiz_id'],'reading_time'=>$_POST['reading_time'], 'sort_order'=>$_POST['sort_order'], 'updated_at'=>date('Y-m-d H:i:s')];
        $DB->update_record('eblix_lessons', $data_arr);
        redirect($CFG->wwwroot.'/lessons/list.php?course_id='.$_POST['course_id'], 'Lesson updated successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

if(isset($_POST) && isset($_POST['lesson_delete'])){
    if($_POST['lesson_delete'] != '0'){
        $topics = $DB->get_records('eblix_topics', ['lesson_id'=>$_POST['lesson_delete']]);
        if(count($topics) > 0){
            redirect($CFG->wwwroot.'/lessons/list.php?course_id='.$_POST['course_id'], 'Lesson have one or more topics', null, \core\output\notification::NOTIFY_ERROR);
        }else{
            $DB->delete_records('eblix_lessons', ['id'=>$_POST['lesson_delete']]);
            redirect($CFG->wwwroot.'/lessons/list.php?course_id='.$_POST['course_id'], 'Lesson deleted successfully', null, \core\output\notification::NOTIFY_SUCCESS);
        }
    }
}

if(isset($_GET) && isset($_GET['get_sort_order']) && $_GET['get_sort_order'] == '1'){
    $new_sort_order = 10;
    $last_lesson = $DB->get_record_sql("SELECT * FROM ".$db_prefix."eblix_lessons where course_id='".$_GET['course_id']."' order by sort_order DESC");
    if($last_lesson != null && isset($last_lesson->sort_order) && !empty($last_lesson->sort_order)){
        $new_sort_order = $last_lesson->sort_order + 10;
    }
    echo $new_sort_order;
}