<?php

include_once 'admin_include.php';

$lesson = null;
if(isset($_GET['lesson_id']) && !empty($_GET['lesson_id'])){
    $lesson = $DB->get_record('eblix_lessons', ['id'=>$_GET['lesson_id']]);
}else{
    redirect($CFG->wwwroot.'/lessons/list.php', 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}

if($lesson == null){
    redirect($CFG->wwwroot.'/lessons/list.php', 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}

$topic_data = null;
if(isset($_GET['topic_id']) && !empty($_GET['topic_id'])){
    $topic_data = $DB->get_record('eblix_topics', ['id'=>$_GET['topic_id']]);
}

$context = context_system::instance();

//$editor = editors_get_preferred_editor(FORMAT_HTML);
//$editor->use_editor('content',['context' => $context, 'autosave' => true, 'enable_filemanagement'=>true], ['return_types' => FILE_EXTERNAL]);

$last_topic = $DB->get_record_sql("SELECT * FROM ".$db_prefix."eblix_topics where lesson_id='".$lesson->id."' order by sort_order DESC");

$new_sort_order = 10;
if($last_topic != null && isset($last_topic->sort_order) && !empty($last_topic->sort_order)){
    $new_sort_order = $last_topic->sort_order + 10;
}

if($topic_data != null){
    $PAGE->set_title('Update Topic');
    $PAGE->set_heading('Update Topic');
}else{
    $PAGE->set_title('Create A Topic');
    $PAGE->set_heading('Create A Topic for '.$lesson->lesson);
}

echo $OUTPUT->header();

?>

<script src="https://cdn.tiny.cloud/1/eao9fle14lrlquirwkrdzonsqhwx4p5036yybl48i9slb6m7/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<div class="row">
    <div class="col-12">
        <form action="<?= $CFG->wwwroot ?>/lessons/topic_store.php" method="post">
            <input type="hidden" name="topic_id" value="<?= ($topic_data != null)? $topic_data->id : '0'; ?>">
            <input type="hidden" name="lesson_id" value="<?= $lesson->id; ?>">
            <input type="hidden" name="course_id" value="<?= $lesson->course_id; ?>">
            <div class="form-group">
                <label for="topic_name">Topic Name</label>
                <input type="text" maxlength="225" class="form-control" id="topic_name" name="topic_name" placeholder="Topic Name" value="<?= ($topic_data != null)? $topic_data->topic : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="sort_order">Sort order</label>
                <input type="number" min="0" class="form-control col-2" id="sort_order" name="sort_order" placeholder="Sort Order" value="<?= ($topic_data != null)? $topic_data->sort_order : $new_sort_order; ?>" >
            </div>

            <div class="form-group">
                <label for="reading_time">Minimum reading time</label>
                <div class="input-group">
                    <input type="number" min="0" step="0.1" class="form-control col-2" id="reading_time" name="reading_time" placeholder="Reading Time" aria-label="Reading Time" aria-describedby="reading_time-addon" value="<?= ($topic_data != null)? $topic_data->reading_time : '5'; ?>" >
                    <div class="input-group-append">
                        <span class="input-group-text" id="reading_time-addon">minutes</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" name="content" class="form-control" rows="15" cols="150" spellcheck="true"><?= ($topic_data != null)? $topic_data->topic_content : ''; ?></textarea>
            </div>

            <div class="form-group text-right mt-3">
                <input type="submit" value="Save" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>
<!--<script src="assets/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content');
</script>-->

<script>
    tinymce.init({
        selector: "textarea",
        /*plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking imagetools",
            "table contextmenu directionality emoticons paste textcolor responsivefilemanager code fullscreen"
        ],*/
        //toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
        //toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code | fullscreen",

        plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap mentions quickbars linkchecker emoticons advtable responsivefilemanager code fullscreen',
        toolbar1: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons',
        toolbar2: 'fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
        image_advtab: true ,

        external_filemanager_path:"<?= $CFG->wwwroot ?>/lessons/responsive_filemanager/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "<?= $CFG->wwwroot ?>/lessons/responsive_filemanager/filemanager/plugin.min.js"}
    });
</script>

<?php
echo $OUTPUT->footer();

?>
