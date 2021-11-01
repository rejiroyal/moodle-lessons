<div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
    <div class="c-sidebar-brand d-lg-down-none">
        <img class="c-sidebar-brand-full" width="118" height="46" alt="CoreUI Logo" src="assets/img/logo_white.png">
        <img class="c-sidebar-brand-minimized" width="46" height="46" alt="CoreUI Logo" src="assets/img/logo_white.png">
    </div>
    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="<?= $CFG->wwwroot ?>/lessons/course.php?course_id=<?= $course->id ?>">
                <svg class="c-sidebar-nav-icon">
                    <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-bookmark"></use>
                </svg>
                <?= add_br($course->fullname); ?>
            </a>
        </li>
        <li class="c-sidebar-nav-title">All Lessons</li>
        <?php
        $all_topic_count = 0;
        $all_lesson_count = 0;
        $previous_lesson_id = '0';
        $previous_read_complete = '';
        $previous_quiz_pass = '';

        foreach ($all_lessons as  $all_lesson) {
            $all_lesson_count++;

            $topics = $DB->get_records('eblix_topics', ['lesson_id'=>$all_lesson->id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);

            $quiz_topic = $DB->get_records('eblix_topics', ['lesson_id'=>$all_lesson->id], $sort='sort_order desc', $fields='*', $limitfrom=0, $limitnum=1);
            $this_lesson_reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $all_lesson->id]);
            if(!empty($quiz_topic)){
                foreach ($quiz_topic as $quiz_topic){}
            }
            $read_complete = false;
            $remaining_mins = $this_lesson_reading_data->reading_time / 60;
            if($remaining_mins >= $all_lesson->reading_time){
                $read_complete = true;
            }

            ?>
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown <?= ($all_lesson->id == $lesson_data->id)? 'c-show' : '' ?> ">
                <a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle" href="#">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-notes"></use>
                    </svg>
                    <?= add_br($all_lesson->lesson) ?>
                </a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <?php $topic_count_l=1; if($topics != null){
                        foreach ($topics as $topic) {
                            //$topic_reading_data = $DB->get_record('eblix_student_views', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id, 'topic_id' => $topic->id])
                             ?>
                            <li class="c-sidebar-nav-item">
                                <!--<a id="topic_link_<?/*= $topic->id*/?>" class="c-sidebar-nav-link <?/*= ($topic_data->id == $topic->id)? 'c-active' : ''; */?>" href="<?/*= $CFG->wwwroot */?>/lessons/page.php?topic_id=<?/*= $topic->id */?>"   data-href="<?/*= $CFG->wwwroot */?>/lessons/page.php?topic_id=<?/*= $topic->id */?>">-->
                                <?php
                                    $disable = true;
                                    if($previous_lesson_id == '0'){
                                        $disable = false;
                                    }else{
                                        if($previous_read_complete ==  true && $previous_quiz_pass ==  true){
                                            $disable = false;
                                        }
                                    }

                                    if(!$disable && !empty($quiz_topic) && $quiz_topic->id == $topic->id){
                                        if($read_complete){
                                            $disable = false;
                                        }else{
                                            $disable = true;
                                        }
                                    }
                                ?>

                                <a id="topic_link_<?= $topic->id?>" class="c-sidebar-nav-link lesson_link_<?= $all_lesson->id?> <?= ($topic_data->id == $topic->id)? 'c-active' : ''; ?> <?php if(!empty($quiz_topic) && $quiz_topic->id == $topic->id){ ?> quiz_topic <?php } ?>" <?php if( $disable == false || !user_has_role_assignment($USER->id,5)) { ?> href="<?= $CFG->wwwroot ?>/lessons/page.php?topic_id=<?= $topic->id ?>"  <?php  } ?> data-href="<?= $CFG->wwwroot ?>/lessons/page.php?topic_id=<?= $topic->id ?>">
                                    <span class="c-sidebar-nav-icon">
                                        <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-notes"></use>
                                    </span>
                                    <?= add_br($topic->topic) ?>
                                </a>
                            </li>
                            <?php $all_topic_count++; }
                    } ?>
                </ul>
            </li>
        <?php
            $previous_lesson_id = $all_lesson->id;
            $lesson_reading_data = $DB->get_record('eblix_student_reading_times', ['user_id'=>$USER->id,'lesson_id' => $all_lesson->id]);

            $previous_read_complete = false;
            if(!empty($lesson_reading_data)) {
                if (($lesson_reading_data->reading_time / 60) >= $all_lesson->reading_time) {
                    $previous_read_complete = true;
                }
            }

            $previous_quiz_pass = checkQuizPass($all_lesson->id, $USER, $DB);

        }  ?>
    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
            data-class="c-sidebar-minimized"></button>
</div>