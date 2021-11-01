<?php

include 'functions.php';

require_login();

$topic_id = optional_param('topic_id', 0, PARAM_INT);
$topic_data = null;
if(isset($topic_id) && !empty($topic_id) && $topic_id > 0){
    $topic_data = $DB->get_record('eblix_topics', ['id'=>$topic_id]);
}else{
    redirect($CFG->wwwroot, 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}
if($topic_data == null){
    redirect($CFG->wwwroot, 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}

$lesson_data = $DB->get_record('eblix_lessons', ['id'=>$topic_data->lesson_id]);

if($lesson_data == null){
    redirect($CFG->wwwroot.'/lessons/list.php', 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}

if(!is_siteadmin()){
    $context = context_course::instance($lesson_data->course_id);
    $is_enrolled = is_enrolled($context, $USER->id,'',true);
    if(!$is_enrolled){
        redirect($CFG->wwwroot, 'You have not enrolled for this course.', null, \core\output\notification::NOTIFY_WARNING);
    }
}

$topics = $DB->get_records('eblix_topics', ['lesson_id'=>$lesson_data->id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);

$all_lessons = $DB->get_records('eblix_lessons', ['course_id'=>$lesson_data->course_id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);

$reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id]);
$remaining_reading_time =  $lesson_data->reading_time;
$remaining_reading_time_m =  '00';
$remaining_reading_time_s =  '00';
$read_complete = 0;
if(!empty($reading_data)){
    $remaining_mins = $reading_data->reading_time / 60;
    if($remaining_mins >= $lesson_data->reading_time){
        $read_complete = 1;
    }else{
        $remaining_reading_time = $remaining_reading_time - $remaining_mins;
        $remaining_reading_time_m = floor($remaining_reading_time);      // 1
        $remaining_reading_time_s = $remaining_reading_time - $remaining_reading_time_m;

        //list($remaining_reading_time_m, $remaining_reading_time_s) = explode('.', $remaining_reading_time);
        $remaining_reading_time_s_set = (string) ($remaining_reading_time_s/10 * 60);


        $remaining_reading_time_s = (isset($remaining_reading_time_s_set[0]))? $remaining_reading_time_s_set[0] : '0';
        $remaining_reading_time_s .= (isset($remaining_reading_time_s_set[1]))? $remaining_reading_time_s_set[1] : '0';
    }
}else{
    $remaining_reading_time_m = $lesson_data->reading_time;      // 1
    $remaining_reading_time_s = '00';
}

/*$viewed_topics = $DB->get_records('eblix_student_views', ['lesson_id'=>$lesson_data->id, 'user_id'=>$USER->id]);
$viewed_topics_array = array();
foreach ($viewed_topics as $viewed_topic){
    $viewed_topics_array[] = $viewed_topic->topic_id;
}
$viewed_topics_array[] = $topic_data->id;
$pending_lesson_data = $DB->get_record_sql('SELECT *,'.$db_prefix.'eblix_topics.id as topic_id FROM '.$db_prefix.'eblix_lessons JOIN '.$db_prefix.'eblix_topics ON '.$db_prefix.'eblix_lessons.id = '.$db_prefix.'eblix_topics.lesson_id WHERE '.$db_prefix.'eblix_topics.course_id = "2" AND '.$db_prefix.'eblix_topics.id NOT IN ('.implode(',',$viewed_topics_array).') ORDER BY '.$db_prefix.'eblix_lessons.sort_order ASC, '.$db_prefix.'eblix_topics.sort_order ASC');*/

$course = get_course($lesson_data->course_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Evolution Hospitality Institute - ">
    <meta name="author" content="Evolution Hospitality Institute">
    <title><?= $lesson_data->lesson ?></title>
    <link rel="apple-touch-icon" sizes="57x57" href="assets/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="assets/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="assets/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="assets/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="assets/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="assets/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Main styles for this application-->
    <link href="assets/css/style.css" rel="stylesheet">

    <script>

    </script>
    <link href="assets/assets/vendors/@coreui/chartjs/css/coreui-chartjs.css" rel="stylesheet">
</head>
<body class="c-app">
    <?php include 'sidebar.php';?>
<div class="c-wrapper c-fixed-components">
    <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
        <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
                data-class="c-sidebar-show">
            <svg class="c-icon c-icon-lg">
                <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>
        <a class="c-header-brand d-lg-none" href="#">
            <img width="118" height="46" alt="CoreUI Logo"src="assets/img/logo_gray.png">
        </a>
        <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
                data-class="c-sidebar-lg-show" responsive="true">
            <svg class="c-icon c-icon-lg">
                <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>
        <ul class="c-header-nav d-md-down-none">
            <!--<li class="c-header-nav-item px-3"><a class="c-header-nav-link" href="#">Dashboard</a></li>-->
        </ul>
        <ul class="c-header-nav ml-auto mr-4">
            <li class="c-header-nav-item dropdown"><a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="c-avatar">
                        <?= $OUTPUT->user_picture($USER, array('size'=>50 , 'class'=>'c-avatar-img', 'alttext'=> true, 'link'=>false))?>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right pt-0">
                    <div class="dropdown-header bg-light py-2"><strong>Account</strong></div>
                    <div class="dropdown-divider"></div>
                    <?= $OUTPUT->user_picture($USER, array('size'=>150 , 'class'=>'dropdown-item', 'alttext'=> true))?>
                    <a class="dropdown-item">
                        <svg class="c-icon mr-2">
                            <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-mood-very-good"></use>
                        </svg> <?= $USER->firstname ?> <?= $USER->lastname ?>
                    </a>
                </div>
            </li>
        </ul>
        <div class="c-subheader px-3">
            <!-- Breadcrumb-->
            <ol class="breadcrumb border-0 m-0">
                <li class="breadcrumb-item"><a href="<?= $CFG->wwwroot ?>">Evolution</a></li>
                <li class="breadcrumb-item"><a href="<?= $CFG->wwwroot ?>/lessons/course.php?course_id=<?= $course->id ?>"><?= $course->fullname ?></a></li>
                <li class="breadcrumb-item"><a href="<?= $CFG->wwwroot ?>/lessons/?lesson_id=<?=$lesson_data->id?>"><?= $lesson_data->lesson ?></a></li>
                <li class="breadcrumb-item"><?= $topic_data->topic ?></li>
                <!-- Breadcrumb Menu-->
            </ol>
        </div>
    </header>
    <div class="c-body">
        <main class="c-main">
            <div class="container-fluid">
                <div class="fade-in">

                    <div class="row">
                        <div class="col-sm-12 col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2>
                                        <?= $topic_data->topic ?>

                                        <div class="card-header-actions">
                                            <small style="font-size: 11px">Minimum reading time for lesson : <?= $lesson_data->reading_time ?> mins</small>
                                            <?php if($read_complete === 1) { ?>
                                            <svg class="c-icon small text-success" id="reading_icon" style="margin-top: 0.35rem !important;">
                                                <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-flag-alt"></use>
                                            </svg>
                                            <?php }else{ ?>
                                            <svg class="c-icon small text-warning" id="reading_icon" style="margin-top: 0.35rem !important;">
                                                <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-av-timer"></use>
                                            </svg>
                                            <?php } ?>
                                        </div>
                                    </h2>
                                    <?php if($read_complete === 1) { ?>
                                        <small class="mt-2">You have completed this lesson.</small>
                                    <?php }else{ ?>
                                        <small class="mt-2 countdown"></small>
                                    <?php } ?>
                                </div>
                                <div class="card-body">
                                    <?= $topic_data->topic_content ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php
                        $previous_link = '';
                        $previous_text = '';
                        $previous = $DB->get_record_sql('select * from '.$db_prefix.'eblix_topics where sort_order = (select max(sort_order) from '.$db_prefix.'eblix_topics where sort_order < '.$topic_data->sort_order.' AND lesson_id = '.$topic_data->lesson_id.' ) AND lesson_id = '.$topic_data->lesson_id);
                        if(!empty($previous)){
                            $previous_link = $CFG->wwwroot.'/lessons/page.php?topic_id='.$previous->id;
                            $previous_text = 'Previous Topic';
                        }else{
                            $previous = $DB->get_record_sql('select * from '.$db_prefix.'eblix_lessons where sort_order = (select max(sort_order) from '.$db_prefix.'eblix_lessons where sort_order < '.$lesson_data->sort_order.' AND course_id = '.$lesson_data->course_id.' ) AND course_id = '.$lesson_data->course_id);
                            if(!empty($previous)){
                                $previous_link = $CFG->wwwroot.'/lessons/?lesson_id='.$previous->id;
                                $previous_text = 'Previous Lesson';
                            }
                        }

                        $next_link = '';
                        $next_text = '';
                        $next_topic_id = '';
                        $next_lesson_id = '';
                        $next = $DB->get_record_sql('select * from '.$db_prefix.'eblix_topics where sort_order = (select min(sort_order) from '.$db_prefix.'eblix_topics where sort_order > '.$topic_data->sort_order.' AND lesson_id = '.$topic_data->lesson_id.' ) AND lesson_id = '.$topic_data->lesson_id);
                        if(!empty($next)){

                            $quiz_topic = $DB->get_records('eblix_topics', ['lesson_id'=>$lesson_data->id], $sort='sort_order desc', $fields='*', $limitfrom=0, $limitnum=1);
                            if(!empty($quiz_topic)){
                                foreach ($quiz_topic as $quiz_topic){}
                            }

                            $disable = false;
                            if(!empty($quiz_topic) && $quiz_topic->id == $next->id){
                                if($read_complete === 1){
                                    $disable = false;
                                }else{
                                    $disable = true;
                                }
                            }

                            $next_link = $CFG->wwwroot.'/lessons/page.php?topic_id='.$next->id;
                            $next_text = 'Next Topic';
                            $next_topic_id = $next->id;
                        }else{
                            $next = $DB->get_record_sql('select * from '.$db_prefix.'eblix_lessons where sort_order = (select min(sort_order) from '.$db_prefix.'eblix_lessons where sort_order > '.$lesson_data->sort_order.' AND course_id = '.$lesson_data->course_id.' ) AND course_id = '.$lesson_data->course_id);

                            $lesson_reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id]);

                            $lesson_read_complete = false;
                            if(!empty($lesson_reading_data)) {
                                if (($lesson_reading_data->reading_time / 60) >= $lesson_data->reading_time) {
                                    $lesson_read_complete = true;
                                }
                            }

                            $lesson_quiz_pass = checkQuizPass($lesson_data->id, $USER, $DB);

                            if($lesson_read_complete == true && $lesson_quiz_pass == true){
                                if(!empty($next)){
                                    $next_lesson_id = $next->id;
                                    $next_link = $CFG->wwwroot.'/lessons/?lesson_id='.$next->id;
                                    $next_text = 'Next Lesson';
                                }
                            }
                        }
                        ?>

                        <div class="col-sm-6 text-left">
                            <?php if(!empty($previous_link)){ ?>
                            <a class="btn btn-md btn-info pull-right" href="<?=$previous_link?>">
                                <svg class="c-icon small text-primary" id="">
                                    <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-arrow-thick-left"></use>
                                </svg>
                                <?= $previous_text ?>
                            </a>
                            <?php } ?>
                        </div>
                        <div class="col-sm-6 text-right">
                            <?php if(!empty($next_link)){ ?>
                            <a class="btn btn-md btn-info <?php if((!empty($next_topic_id) && $read_complete === 0 && !$lesson_quiz_pass) || (!empty($next_topic_id) && $disable)) { ?> disabled <?php } ?>"  href="<?=$next_link?>" id="next_btn">
                                <svg class="c-icon small text-primary" id="">
                                    <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-arrow-thick-right"></use>
                                </svg>
                                <?= $next_text ?>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="c-footer">
            <div><a href="https://www.evolution.edu.au/">Evolution Hospitality Institute</a> © <?= date('Y')?>.</div>
            <div class="ml-auto">Powered by&nbsp;<a href="https://www.eblix.com.au/">eBlix Technologies</a></div>
        </footer>
    </div>
</div>
<script src="assets/js/jquery-3.5.1.min.js"></script>
<input type="hidden" value="<?= ($read_complete === 1)? '1' : '0'?>" id="read_complete">
<script>
    $(document).ready(function() {
        /*var reading_time = parseInt('<?= $topic_data->reading_time ?>');
        reading_time = reading_time * 60 * 1000;
        setTimeout(studentView, reading_time);*/

        setInterval(function(){
            var read_complete = $('#read_complete').val();
                if(read_complete == '0'){
                    studentReadingTime();
                }
            }, 20 * 1000);



        var timer2 = "<?= $remaining_reading_time_m ?>:<?= $remaining_reading_time_s ?>";

        if(timer2 != '00:00' && timer2 != '0:0'){
            var interval = setInterval(function() {
                var read_complete = $('#read_complete').val();

                var timer = timer2.split(':');
                var minutes = parseInt(timer[0], 10);
                var seconds = parseInt(timer[1], 10);
                --seconds;
                minutes = (seconds < 0) ? --minutes : minutes;
                seconds = (seconds < 0) ? 59 : seconds;
                seconds = (seconds < 10) ? '0' + seconds : seconds;

                var read_complete = $('#read_complete').val();
                if(read_complete == '1'){
                    $('.countdown').html('Lesson Completed');
                    clearInterval(interval)
                }else{
                    $('.countdown').html('Lesson complete in - ' + minutes + ':' + seconds);
                }
                timer2 = minutes + ':' + seconds;
            }, 1000);
        }else{
            $('.countdown').html('Lesson Completed');
        }

    });

    /*function studentView() {
        var topic_id = '<?= $topic_data->id ?>';
        var lesson_id = '<?= $topic_data->lesson_id ?>';
        $.ajax({
            url: 'functions.php',
            type: 'POST',
            data : { action : 'student_views', topic_id : topic_id, lesson_id : lesson_id },
        }).done(function (resp) {
            $('#reading_icon').removeClass('text-warning').addClass('text-success').html('<use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-flag-alt"></use>');
            $('#next_btn').removeClass('disabled');
            $('#topic_link_<?= $next_topic_id?>').attr('href','<?= $next_link?>');

        });
    }*/



    function studentReadingTime() {
        var lesson_id = '<?= $topic_data->lesson_id ?>';
        $.ajax({
            url: 'functions.php',
            type: 'POST',
            data : { action : 'reading_time' , lesson_id : lesson_id },
            cache : false,
        }).done(function (resp) {
            if(resp == '1'){
                $('#read_complete').val('1');
                $('#reading_icon').removeClass('text-warning').addClass('text-success').html('<use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-flag-alt"></use>');
                $('#next_btn').removeClass('disabled');

                <?php if(!empty($next_lesson_id)){ ?>
                $('.lesson_link_<?= $next_lesson_id?>').each(function(){
                    if (!$(this).hasClass('quiz_topic')) {
                        var href = $(this).data('href');
                        $(this).attr('href',href);
                    }
                });
                <?php } ?>
            }
        });
    }
</script>

<!-- CoreUI and necessary plugins-->
<script src="assets/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
<!--[if IE]><!-->
<script src="assets/vendors/@coreui/icons/js/svgxuse.min.js"></script>
<!--<![endif]-->
<!-- Plugins and scripts required by this view-->
<script src="assets/vendors/@coreui/chartjs/js/coreui-chartjs.bundle.js"></script>
<script src="assets/vendors/@coreui/utils/js/coreui-utils.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>