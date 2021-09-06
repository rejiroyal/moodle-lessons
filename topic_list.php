<?php

include_once 'admin_include.php';

$lesson_id = optional_param('lesson_id', 0, PARAM_INT);
$lesson = $DB->get_record('eblix_lessons', ['id'=>$_GET['lesson_id']]);

if(empty($lesson_id) || $lesson_id < 1 || empty($lesson)){
    redirect($CFG->wwwroot.'/lessons/list.php', 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}

$PAGE->set_title('Custom Topics');
$PAGE->set_heading('Topics - '.$lesson->lesson);

echo $OUTPUT->header();

echo '<script src="https://kit.fontawesome.com/9e05556956.js" crossorigin="anonymous"></script>';

$topics = $DB->get_records('eblix_topics', ['lesson_id'=>$lesson_id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);

?>
    <div class="row">
        <div class="col-md-6">
            <a href="<?= $CFG->wwwroot ?>/lessons/list.php?course_id=<?= $lesson->course_id?>" type="button" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back to list</a>
        </div>
        <div class="col-md-6 text-right">
            <a href="<?= $CFG->wwwroot ?>/lessons/topic_create.php?lesson_id=<?= $lesson_id ?>" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Add Topic</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Topic</th>
                    <th scope="col" class="text-center" width="10%">Sort Order</th>
                    <th scope="col" class="text-center" width="12%">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($topics) <= 0) { ?>
                    <tr>
                        <td colspan="3">No Topics found for <?= $lesson->lesson ?></td>
                    </tr>
                <?php } else {
                    foreach ($topics as $topic) {
                        $course = get_course($topic->course_id);
                        ?>
                        <tr>
                            <td><?= $topic->topic ?></td>
                            <td class="text-center"><?= $topic->sort_order ?></td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-success" title="Edit" href="<?= $CFG->wwwroot ?>/lessons/topic_create.php?lesson_id=<?= $lesson->id?>&topic_id=<?=$topic->id?>"><i class="far fa-edit"></i></a>
                                <a class="btn btn-sm btn-danger delete-lesson" onclick="$('#topic_delete').val('<?= $topic->id?>');$('#topic_name').html('<?= $topic->topic ?>')" title="Delete" data-id="<?= $lesson->id?>" data-toggle="modal" data-target="#deleted_model"><i class="far fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="deleted_model" tabindex="-1" role="dialog" aria-labelledby="deleted_model_title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleted_model_title">Delete Lesson</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Delete "<span id="topic_name"></span>" topic?? This cannot be undone!!
                </div>
                <div class="modal-footer">
                    <form action="<?= $CFG->wwwroot ?>/lessons/topic_store.php" method="post">
                        <input type="hidden" id="topic_delete" name="topic_delete" value="0">
                        <input type="submit" class="btn btn-danger" value="Delete">
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<?php
echo $OUTPUT->footer();

?>