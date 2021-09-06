<?php

include_once 'admin_include.php';

$PAGE->set_title('Custom Lessons');
$PAGE->set_heading('Custom Lessons');

echo $OUTPUT->header();

$course_id = '';
if(isset($_GET) && isset($_GET['course_id'])){
    $course_id = $_GET['course_id'];
}

$courses = $DB->get_records('course', [], $sort='fullname', $fields='*', $limitfrom=0, $limitnum=0);

$limitfrom = 0;
$limitnum = 100;
$page = 1;
if(isset($_GET['page']) && !empty($_GET['page'])){
    $page = $_GET['page'];
    $limitfrom = (($_GET['page'] - 1) * 10);
}

$total_records = $DB->count_records('eblix_lessons',  ['course_id'=>$course_id]);
$total_pages = ceil($total_records / $limitnum);

$lessons = $DB->get_records('eblix_lessons', ['course_id'=>$course_id], $sort='course_id,sort_order', $fields='*', $limitfrom, $limitnum);


?>
    <div class="row">
        <div class="col-md-6 text-left">
            <select class="form-control" id="course_select" autocomplete="off">
                <option value="">Select A Course</option>
                <?php if(count($courses) > 0) { foreach ($courses as $course) { if($course->format != 'site') { ?>
                    <option value="<?= $course->id ?>" <?= ( $course_id == $course->id )? 'selected="selected"' : ''; ?> ><?= $course->fullname ?></option>
                <?php  } } } ?>
            </select>
        </div>
        <div class="col-md-6 text-right">
            <a href="<?= $CFG->wwwroot ?>/lessons/lesson_create.php?&course_id=<?= $course_id ?>" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Add Lesson</a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Course</th>
                    <th scope="col">Lesson</th>
                    <th scope="col" width="10%">Sort Order</th>
                    <th scope="col" class="text-center">Link</th>
                    <th scope="col" class="text-center" width="18%">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($course_id)) { ?>
                    <tr>
                        <td colspan="4">Please select a course.</td>
                    </tr>
                <?php }else{ ?>

                    <?php if(count($lessons) <= 0) { ?>
                        <tr>
                            <td colspan="4">No lessons found</td>
                        </tr>
                    <?php } else {
                        foreach ($lessons as $lesson) {
                            $course = get_course($lesson->course_id);
                            ?>
                            <tr>
                                <td><small><?= $course->fullname ?></small></td>
                                <td><?= $lesson->lesson ?></td>
                                <td class="text-center"><?= $lesson->sort_order ?></td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <a href="<?= $CFG->wwwroot ?>/lessons/course.php?course_id=<?=$lesson->course_id?>" target="_blank"><span class="small text-muted"  id="copy_<?= $lesson->id ;?>"><?= $CFG->wwwroot ?>/lessons/course.php?course_id=<?=$lesson->course_id?></span></a>
                                        </div>
                                        <div class="col-md-2">
                                            <a class="btn btn-sm btn-info" title="Copy Link" onclick="copyToClipboard($(this),$('#'+'copy_<?= $lesson->id ;?>'))"><i class="far fa-copy"></i></a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-warning" title="Topics" href="<?= $CFG->wwwroot ?>/lessons/topic_list.php?lesson_id=<?=$lesson->id?>"><i class="far fa-list-alt"></i></a>
                                    <a class="btn btn-sm btn-info" title="Student Data" href="<?= $CFG->wwwroot ?>/lessons/student_list.php?lesson_id=<?=$lesson->id?>" type="button"><i class="fa fa-users"></i></a>
                                    <a class="btn btn-sm btn-success" title="Edit" href="<?= $CFG->wwwroot ?>/lessons/lesson_create.php?lesson_id=<?=$lesson->id?>"><i class="far fa-edit"></i></a>
                                    <a class="btn btn-sm btn-danger delete-lesson" onclick="$('#lesson_delete').val('<?= $lesson->id?>');$('#lesson_name').html('<?= $lesson->lesson ?>')" title="Delete" data-id="<?= $lesson->id?>" data-toggle="modal" data-target="#deleted_model"><i class="far fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php } } ?>

                <?php } ?>

                </tbody>
            </table>

            <?php
            if(!empty($course_id)) {
                $pagLink = "<nav aria-label='Page navigation example'><ul class='pagination justify-content-end'>";
                for ($i=1; $i<=$total_pages; $i++) {
                    $active = '';
                    if($page == $i){
                        $active = 'active';
                    }
                    if($i == $page || $i >= $page - 2 || $i <= $page + 2){
                        $pagLink .= "<li class='page-item ".$active."'><a class='page-link' href='".$CFG->wwwroot."/lessons/list.php?page=".$i."&course_id=".$course_id."'>".$i."</a></li>";
                    }
                }
                echo $pagLink . "</ul></nav>";
            }
            ?>

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
                    Delete "<span id="lesson_name"></span>" lesson?? This cannot be undone!!
                </div>
                <div class="modal-footer">
                    <form action="<?= $CFG->wwwroot ?>/lessons/lesson_store.php" method="post">
                        <input type="hidden" id="lesson_delete" name="lesson_delete" value="0">
                        <input type="hidden" id="" name="course_id" value="<?=$course_id;?>">
                        <input type="submit" class="btn btn-danger" value="Delete">
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <script src="https://kit.fontawesome.com/9e05556956.js" crossorigin="anonymous"></script>
<script>
    function copyToClipboard(element,copy) {

        var link = copy.html();
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(link).select();
        document.execCommand("copy");
        $temp.remove();

        element.removeClass('btn-info').addClass('btn-success disabled');
        element.html('<i class="far fa-check-circle"></i>');
        copy.html('Link copied to clipboard.');
        copy.removeClass('text-muted').addClass('text-success');
        setTimeout(
                function(){
                    element.fadeIn().removeClass('btn-success disabled').addClass('btn-info');
                    element.fadeIn().html('<i class="far fa-copy"></i>');
                    copy.fadeIn().html(link);
                    copy.removeClass('text-success').addClass('text-muted');
                },
        3000);

    }

    $('#course_select').on('change',function (e) {
        getLessons();
    });

    function getLessons() {
        var course_id = $('#course_select').val();
        if(course_id != ''){
            window.location.href = "<?= $CFG->wwwroot ?>/lessons/list.php?course_id="+course_id;
        }
    }
</script>
<?php

echo $OUTPUT->footer();

?>