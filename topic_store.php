<?php

include_once 'admin_include.php';

if(isset($_POST) && isset($_POST['topic_id'])){
    if($_POST['topic_id'] == '0'){
        $data_arr = (object)['course_id'=>$_POST['course_id'],'lesson_id'=>$_POST['lesson_id'], 'topic'=>$_POST['topic_name'], 'topic_content'=>$_POST['content'],'sort_order'=>$_POST['sort_order'],'reading_time'=>$_POST['reading_time'],'created_by'=>$USER->id, 'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        $DB->insert_record('eblix_topics', $data_arr);
        redirect($CFG->wwwroot.'/lessons/topic_list.php?lesson_id='.$_POST['lesson_id'], 'Topic added successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }else{
        $data_arr = (object)['id'=>$_POST['topic_id'],'course_id'=>$_POST['course_id'],'lesson_id'=>$_POST['lesson_id'], 'topic'=>$_POST['topic_name'], 'topic_content'=>$_POST['content'],'sort_order'=>$_POST['sort_order'],'reading_time'=>$_POST['reading_time'], 'updated_at'=>date('Y-m-d H:i:s')];
        $DB->update_record('eblix_topics', $data_arr);
        redirect($CFG->wwwroot.'/lessons/topic_list.php?lesson_id='.$_POST['lesson_id'], 'Topic updated successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

if(isset($_POST) && isset($_POST['topic_delete'])){
    if($_POST['topic_delete'] != '0'){
        $topic = $DB->get_record('eblix_topics', ['id'=>$_POST['topic_delete']]);
        $DB->delete_records('eblix_topics', ['id'=>$_POST['topic_delete']]);
        $DB->delete_records('eblix_student_views', ['topic_id'=>$_POST['topic_delete']]);
        redirect($CFG->wwwroot.'/lessons/topic_list.php?lesson_id='.$topic->lesson_id, 'Topic deleted successfully', null, \core\output\notification::NOTIFY_SUCCESS);
    }
}