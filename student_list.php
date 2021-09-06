<?php

include_once 'admin_include.php';

$lesson_id = optional_param('lesson_id', 0, PARAM_INT);
$lesson = $DB->get_record('eblix_lessons', ['id'=>$_GET['lesson_id']]);

if(empty($lesson_id) || $lesson_id < 1 || empty($lesson)){
    redirect($CFG->wwwroot.'/lessons/list.php', 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}

$PAGE->set_title('Student Views');
$PAGE->set_heading('Student views for '.$lesson->lesson);

echo $OUTPUT->header();

echo '<script src="https://kit.fontawesome.com/9e05556956.js" crossorigin="anonymous"></script>';

$limitfrom = 0;
$limitnum = 100;
$page = 1;
if(isset($_GET['page']) && !empty($_GET['page'])){
    $page = $_GET['page'];
    $limitfrom = (($_GET['page'] - 1) * 10);
}



$total_records = count($DB->get_records_sql('SELECT * FROM '. $db_prefix.'eblix_student_views WHERE lesson_id = "'.$lesson_id.'" GROUP BY user_id', [], 0, 0));
$total_pages = ceil($total_records / $limitnum);

$students = $DB->get_records_sql('SELECT * FROM '. $db_prefix.'eblix_student_views WHERE lesson_id = "'.$lesson_id.'" GROUP BY user_id', [], $limitfrom, $limitnum);

$topic_count = $DB->count_records('eblix_topics', ['lesson_id'=>$lesson->id]);

?>
    <div class="row">
        <div class="col-md-6">
            <a href="<?= $CFG->wwwroot ?>/lessons/list.php" type="button" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back to list</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col" width="50%">User</th>
                    <th scope="col" class="text-center" width="15%">Percentage</th>
                    <th scope="col" class="text-center">Viewed Topics</th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($students) <= 0) { ?>
                    <tr>
                        <td colspan="3">No Students view any topics for this lesson</td>
                    </tr>
                <?php } else {
                    foreach ($students as $student) {
                        $user = $DB->get_record('user', ['id'=>$student->user_id]);
                        $viewed_topics = $DB->get_records('eblix_student_views', ['lesson_id'=>$lesson->id, 'user_id'=>$student->user_id]);

                        $viewed_topics_array = array();
                        foreach ($viewed_topics as $viewed_topic){
                            $viewed_topics_array[] = $viewed_topic->topic_id;
                        }

                        $viewed_topics_all = $DB->get_records_sql('SELECT * FROM '.$db_prefix.'eblix_topics WHERE id IN ('.implode(',',$viewed_topics_array).') ORDER BY sort_order ASC', [], 0, 0);;
                        ?>
                        <tr>
                            <td><?= $user->firstname.' '.$user->lastname ?></td>
                            <td class="text-center"><?= round((count($viewed_topics) / $topic_count ) * 100); ?>%</td>
                            <td>
                                <small>
                                    <?php foreach ($viewed_topics_all as $item){
                                        echo $item->topic.'<br/>';
                                    } ?>
                                </small>

                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>

            <?php
            $pagLink = "<nav aria-label='Page navigation example'><ul class='pagination justify-content-end'>";
            for ($i=1; $i<=$total_pages; $i++) {
                $active = '';
                if($page == $i){
                    $active = 'active';
                }
                if($i == $page || $i >= $page - 2 || $i <= $page + 2){
                    $pagLink .= "<li class='page-item ".$active."'><a class='page-link' href='".$CFG->wwwroot."/lessons/list.php?page=".$i."'>".$i."</a></li>";
                }
            }
            echo $pagLink . "</ul></nav>";
            ?>

        </div>
    </div>

<?php

echo $OUTPUT->footer();

?>