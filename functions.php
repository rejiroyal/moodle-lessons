<?php

require('../config.php');
global $USER, $COURSE, $CFG;

$db_prefix = $CFG->prefix;


function add_br($sting){
    return chunk_split($sting,26,'<br/>');
}

if(isset($_POST)){
    if(isset($_POST['action'])) {
        if (user_has_role_assignment($USER->id, 5)) {
            if ($_POST['action'] == 'student_views') {
                $topic_id = $_POST['topic_id'];
                $lesson_id = $_POST['lesson_id'];
                $pre_view_data = $DB->get_record('eblix_student_views', ['user_id' => $USER->id, 'lesson_id' => $lesson_id, 'topic_id' => $topic_id]);

                if ($pre_view_data == null) {
                    $data_arr = (object)['user_id' => $USER->id, 'lesson_id' => $lesson_id, 'topic_id' => $topic_id, 'viewed_times' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];
                    $DB->insert_record('eblix_student_views', $data_arr);
                } else {
                    $data_arr = (object)['id' => $pre_view_data->id, 'user_id' => $USER->id, 'lesson_id' => $lesson_id, 'topic_id' => $topic_id, 'viewed_times' => ($pre_view_data->viewed_times + 1), 'updated_at' => date('Y-m-d H:i:s')];
                    $DB->update_record('eblix_student_views', $data_arr);
                }
            }
        }
    }

    if(is_uploaded_file($_FILES['upload']['tmp_name'])){
        $time = time();
        $target_dir = "uploads/";
        $imageFileType = strtolower(pathinfo(basename($_FILES["upload"]["name"]),PATHINFO_EXTENSION));
        $image_name = $time.'.'.$imageFileType;
        $target_file = $target_dir.$image_name;

        move_uploaded_file($_FILES["upload"]["tmp_name"], $target_file);

        $return_array = array(
            "uploaded" => 1,
            "fileName" => $image_name,
            "url" => "uploads/".$image_name
        );
        echo json_encode($return_array);
    }
}