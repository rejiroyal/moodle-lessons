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
        <?php foreach ($all_lessons as  $all_lesson) {
            $topics = $DB->get_records('eblix_topics', ['lesson_id'=>$all_lesson->id], $sort='sort_order', $fields='*', $limitfrom=0, $limitnum=0);
            $all_topic_count = 0;
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
                            $topic_reading_data = $DB->get_record('eblix_student_views', ['user_id'=>$USER->id,'lesson_id' => $lesson_data->id, 'topic_id' => $topic->id])
                            ?>
                            <li class="c-sidebar-nav-item">
                                <a class="c-sidebar-nav-link <?= ($topic_data->id == $topic->id)? 'c-active' : ''; ?>" <?php if(!empty($topic_reading_data) || $all_topic_count == 0 || !user_has_role_assignment($USER->id,5)) { ?> href="<?= $CFG->wwwroot ?>/lessons/page.php?topic_id=<?= $topic->id ?>" <?php } ?>>
                                    <span class="c-sidebar-nav-icon">
                                        <use xlink:href="assets/vendors/@coreui/icons/svg/free.svg#cil-notes"></use>
                                    </span>
                                    <?= add_br($topic->topic) ?>
                                </a>
                            </li>
                            <?php $all_topic_count++; } } ?>
                </ul>
            </li>
        <?php  }  ?>
    </ul>
    <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
            data-class="c-sidebar-minimized"></button>
</div>