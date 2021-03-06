<?php

include 'functions.php';

require_login();

global $USER, $COURSE;

$lesson_id = optional_param('lesson_id', 0, PARAM_INT);

$lesson_data = null;
if(isset($lesson_id) && !empty($lesson_id)){
    $lesson_data = $DB->get_record('eblix_lessons', ['id'=>$lesson_id]);
}else{
    redirect($CFG->wwwroot);
}

if(!is_siteadmin()){
    $context = context_course::instance($lesson_data->course_id);
    $is_enrolled = is_enrolled($context, $USER->id,'',true);
    if(!$is_enrolled){
        redirect($CFG->wwwroot, 'You have not enrolled for this course.', null, \core\output\notification::NOTIFY_WARNING);
    }
}

if($lesson_data == null){
    redirect($CFG->wwwroot, 'Something went wrong', null, \core\output\notification::NOTIFY_WARNING);
}


//$viewed_topics = $DB->get_records('eblix_student_views', ['lesson_id'=>$lesson_data->id, 'user_id'=> $USER->id]);
$reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id]);
$reading_amount = '0';
if(!empty($reading_data)){
    $reading_amount = $reading_data->reading_time / 60;
}

//$topic_count = $DB->count_records('eblix_topics', ['lesson_id'=>$lesson_data->id]);

$all_lessons = $DB->get_records('eblix_lessons', ['course_id'=>$lesson_data->course_id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);;

$course = get_course($lesson_data->course_id);


$reading_percentage = round(( $reading_amount / $lesson_data->reading_time ) * 100);
if($reading_percentage > 100){
    $reading_percentage = 100;
}
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
    <link rel="icon" type="image/png" sizes="192x192" href="assets/img/logo_white.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/logo_white.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/img/logo_white.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/logo_white.png">
    <link rel="manifest" href="assets/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/img/logo_white.png">
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
        <div class="c-subheader px-3 d-none d-sm-block">
            <!-- Breadcrumb-->
            <ol class="breadcrumb border-0 m-0">
                <li class="breadcrumb-item"><a href="<?= $CFG->wwwroot ?>">Evolution</a></li>
                <li class="breadcrumb-item"><a href="<?= $CFG->wwwroot ?>/lessons/course.php?course_id=<?= $course->id ?>"><?= $course->fullname ?></a></li>
                <li class="breadcrumb-item"><?= $lesson_data->lesson ?></li>
                <!-- Breadcrumb Menu-->
            </ol>
        </div>
    </header>
    <div class="c-body">
        <main class="c-main">
            <div class="container-fluid">
                <div class="fade-in">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">Progress <?= (!user_has_role_assignment($USER->id,5))? '<small class="text-warning">Only for students</small>' : $reading_percentage.'%'; ?></div>
                                <div class="card-body">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="<?= $reading_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= ( $reading_amount / $lesson_data->reading_time ) * 100; ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2><?= $lesson_data->lesson ?></h2>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group">
                                        <?php $count = 1;

                                        $lesson_key = '';
                                        $lesson_count = 0;
                                        $lessons_array = array();
                                        $previous_read_complete = true;
                                        foreach ($all_lessons as  $all_lesson) {
                                            $lessons_array[] = $all_lesson->id;
                                            if($all_lesson->id == $lesson_data->id){
                                                $lesson_key = $lesson_count;
                                            }
                                            $lesson_count++;
                                        }

                                        $keys = array_keys($lessons_array);
                                        $pre_lesson = $lessons_array[$keys[array_search($lesson_key, $keys)-1]];

                                        if(!empty($pre_lesson)){
                                            $pre_lesson_reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $pre_lesson]);
                                            $remaining_mins = $pre_lesson_reading_data->reading_time / 60;
                                            if($remaining_mins < $lesson_data->reading_time){
                                                $previous_read_complete = false;
                                            }
                                        }

                                        $topics = $DB->get_records('eblix_topics', ['lesson_id'=>$lesson_data->id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);

                                        $quiz_topic = $DB->get_records('eblix_topics', ['lesson_id'=>$lesson_data->id], $sort='sort_order desc', $fields='*', $limitfrom=0, $limitnum=1);
                                        $this_lesson_reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id]);
                                        if(!empty($quiz_topic)){
                                            foreach ($quiz_topic as $quiz_topic){}
                                        }
                                        $read_complete = false;
                                        $remaining_mins = $this_lesson_reading_data->reading_time / 60;
                                        if($remaining_mins >= $lesson_data->reading_time){
                                            $read_complete = true;
                                        }

                                        if($topics != null){
                                            foreach ($topics as $topic) {

                                                $disable = false;
                                                if(!empty($lesson_data->quiz_id) && !empty($quiz_topic) && $quiz_topic->id == $topic->id){
                                                    if($read_complete){
                                                        $disable = false;
                                                    }else{
                                                        $disable = true;
                                                    }
                                                }

                                                //$topic_reading_data = $DB->get_record('eblix_student_views', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id, 'topic_id' => $topic->id])?>
                                                <a <?php if(!$disable && $previous_read_complete) { ?> href="<?= $CFG->wwwroot ?>/lessons/page.php?topic_id=<?= $topic->id ?>" <?php } ?>>
                                                    <li class="list-group-item"><span class="badge badge-info ml-auto mr-auto"><?= sprintf("%02d", $count)?></span> <?= $topic->topic?></li>
                                                </a>
                                                <?php $count++; }
                                        } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php
                        $previous = $DB->get_record_sql('select * from '.$db_prefix.'eblix_lessons where sort_order = (select max(sort_order) from '.$db_prefix.'eblix_lessons where sort_order < '.$lesson_data->sort_order.' AND course_id = '.$lesson_data->course_id.' )  AND course_id = '.$lesson_data->course_id);
                        $previous_link = '';
                        $previous_text = '';
                        if(!empty($previous)){
                            $previous_link = $CFG->wwwroot.'/lessons/?lesson_id='.$previous->id;
                            $previous_text = 'Previous Lesson';
                        }

                        $next = $DB->get_record_sql('select * from '.$db_prefix.'eblix_lessons where sort_order = (select min(sort_order) from '.$db_prefix.'eblix_lessons where sort_order > '.$lesson_data->sort_order.' AND course_id = '.$lesson_data->course_id.' )  AND course_id = '.$lesson_data->course_id);

                        $lesson_reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id]);

                        $lesson_read_complete = false;
                        if(!empty($lesson_reading_data)) {
                            if (($lesson_reading_data->reading_time / 60) >= $lesson_data->reading_time) {
                                $lesson_read_complete = true;
                            }
                        }

                        $lesson_quiz_pass = checkQuizPass($lesson_data->id, $USER, $DB);

                        $next_link = '';
                        $next_text = '';

                        if($lesson_read_complete == true && $lesson_quiz_pass == true){
                            if(!empty($next)){
                                $next_link = $CFG->wwwroot.'/lessons/?lesson_id='.$next->id;
                                $next_text = 'Next Lesson';
                            }
                        }

                        ?>

                        <div class="col-sm-6 text-left">
                            <?php if(!empty($previous_link)){ ?>
                                <a class="btn btn-md btn-info pull-right" href="<?=$previous_link?>">
                                    <svg class="c-icon small text-primary" id="reading_icon">
                                        <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-arrow-thick-left"></use>
                                    </svg>
                                    <?= $previous_text ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="col-sm-6 text-right">
                            <?php if(!empty($next_link)){ ?>
                                <a class="btn btn-md btn-info"  href="<?=$next_link?>">
                                    <svg class="c-icon small text-primary" id="reading_icon">
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
            <div><a href="https://www.evolution.edu.au/">Evolution Hospitality Institute</a> ?? <?= date('Y')?>.</div>
            <div class="ml-auto">Powered by&nbsp;<a href="https://www.eblix.com.au/">eBlix Technologies</a></div>
        </footer>
    </div>
</div>
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