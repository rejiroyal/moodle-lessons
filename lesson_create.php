<?php

include_once 'admin_include.php';

$courses = $DB->get_records('course', [], $sort='fullname', $fields='*', $limitfrom=0, $limitnum=0);

$lesson_data = null;
if(isset($_GET['lesson_id']) && !empty($_GET['lesson_id'])){
    $lesson_data = $DB->get_record('eblix_lessons', ['id'=>$_GET['lesson_id']]);
}

if($lesson_data != null){
    $PAGE->set_title('Update Lesson');
    $PAGE->set_heading('Update Lesson');
}else{
    $PAGE->set_title('Create A Lesson');
    $PAGE->set_heading('Create A Lesson');
}


echo $OUTPUT->header();

?>

<div class="row">
    <div class="col-12">
        <form action="<?= $CFG->wwwroot ?>/lessons/lesson_store.php" method="post">
            <input type="hidden" name="lesson_id" value="<?= ($lesson_data != null)? $lesson_data->id : '0'; ?>">
            <div class="form-group">
                <label for="course_id">Course</label>
                <select class="form-control" name="course_id" id="course_id" onchange="getSortOrder()" required>
                    <option value="">Select A Course</option>
                    <?php if(count($courses) > 0) { foreach ($courses as $course) { if($course->format != 'site') { ?>
                        <option value="<?= $course->id ?>" <?= (($lesson_data != null && $lesson_data->course_id == $course->id) || (isset($_GET['course_id']) && $_GET['course_id'] == $course->id) )? 'selected="selected"' : ''; ?> ><?= $course->fullname ?></option>
                    <?php  } } } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="lesson_name">Lesson Name</label>
                <input type="text" maxlength="225" class="form-control" id="lesson_name" name="lesson_name" placeholder="Lesson Name" value="<?= ($lesson_data != null)? $lesson_data->lesson : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort order</label>
                <input type="number" min="0" class="form-control col-4" id="sort_order" name="sort_order" placeholder="Sort Order" value="<?= ($lesson_data != null)? $lesson_data->sort_order : ''; ?>" >
            </div>
            <div class="form-group text-right">
                <input type="submit" value="Save" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>

<script>

    $( document ).ready(function() {
        getSortOrder();
    });

    function getSortOrder() {
        var course_id = $('#course_id').val();
        $.ajax({
            url: 'lesson_store.php',
            type: 'GET',
            data : { get_sort_order : '1', course_id : course_id },
        }).done(function (resp) {
            $('#sort_order').val(resp);
        });
    }
</script>
<?php
echo $OUTPUT->footer();

?>
