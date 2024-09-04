<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-slideshare "></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("online_exam/index")?>"><?=$this->lang->line('panel_title')?></a></li>
            <li class="active"><?=$this->lang->line('menu_edit')?> <?=$this->lang->line('panel_title')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-10">
                <form class="form-horizontal" role="form" method="post">
                    <?php
                    if(form_error('name'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="name" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_name")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="name" name="name" value="<?=set_value('name', $online_exam->name)?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('name'); ?>
                        </span>
                    </div>


                    <?php
                    if(form_error('description'))
                        echo "<div class='form-group has-error'>";
                    else
                        echo "<div class='form-group'>";
                    ?>
                        <label for="description" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_description")?>
                        </label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="description" name="description" ><?=set_value('description', $online_exam->description)?></textarea>
                        </div>
                        <span class="col-sm-3 control-label">
                            <?php echo form_error('description'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('usertype'))
                        echo "<div class='form-group has-error' style='display:none;'>";
                    else
                        echo "<div class='form-group' style='display:none;'>";
                    ?>
                        <label for="usertype" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_usertype")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array = array(0 => $this->lang->line("online_exam_select"));
                            if(inicompute($usertypes)) {
                                foreach ($usertypes as $usertype) {
                                    $array[$usertype->usertypeID] = $usertype->usertype;
                                }
                            }
                            echo form_dropdown("usertype", $array, set_value("usertype", $userTypeID), "id='usertype' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('usertype'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('classes'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="classes" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_class")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array = array(0 => $this->lang->line("online_exam_select"));
                            if(inicompute($classes)) {
                                foreach ($classes as $class) {
                                    $array[$class->classesID] = $class->classes;
                                }
                            }
                            echo form_dropdown("classes", $array, set_value("classes", $online_exam->classID), "id='classes' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('classes'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('section'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="section" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_section")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array = array(0 => $this->lang->line("online_exam_select"));
                            if(inicompute($sections)) {
                                foreach ($sections as $section) {
                                    $array[$section->sectionID] = $section->section;
                                }
                            }
                            echo form_dropdown("section", $array, set_value("section", $online_exam->sectionID), "id='section' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('section'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('studentGroup'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="studentGroup" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_studentGroup")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array = array(0 => $this->lang->line("online_exam_select"));
                            if(inicompute($groups)) {
                                foreach ($groups as $group) {
                                    $array[$group->studentgroupID] = $group->group;
                                }
                            }
                            echo form_dropdown("studentGroup", $array, set_value("studentGroup", $online_exam->studentGroupID), "id='studentGroup' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('studentGroup'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('subject'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="subject" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_subject")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array = array(0 => $this->lang->line("online_exam_select"));
                            if(inicompute($subjects)) {
                                foreach ($subjects as $subject) {
                                    $array[$subject->subjectID] = $subject->subject;
                                }
                            }
                            echo form_dropdown("subject", $array, set_value("subject", $online_exam->subjectID), "id='subject' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('subject'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('instruction'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="instruction" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_instruction")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $arrayInstruction = array(0 => $this->lang->line("online_exam_select"));
                            if(inicompute($instructions)) {
                                foreach ($instructions as $instruction) {
                                    $arrayInstruction[$instruction->instructionID] = $instruction->title;
                                }
                            }
                            echo form_dropdown("instruction", $arrayInstruction, set_value("instruction", $online_exam->instructionID), "id='instruction' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('instruction'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('examStatus'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="examStatus" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_exam_status")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $arrayStatus['0'] = $this->lang->line("online_exam_select");
                                $arrayStatus['1'] = $this->lang->line("online_exam_one_time");
                                $arrayStatus['2'] = $this->lang->line("online_exam_multiple_time");
                                echo form_dropdown("examStatus", $arrayStatus, set_value("examStatus",$online_exam->examStatus), "id='examStatus' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('examStatus'); ?>
                        </span>
                    </div>

                    <?php
                        if(form_error('randomQuestion'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                        ?>
                        <label for="randomQuestion" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_random_question")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array['0'] = $this->lang->line("online_exam_select");
                            $array['1'] = $this->lang->line("online_exam_yes");
                            $array['2'] = $this->lang->line("online_exam_no");
                            echo form_dropdown("randomQuestion", $array, set_value("randomQuestion",$online_exam->random), "id='randomQuestion' class='form-control select2'");
                            ?>
                        </div>
                            <span class="col-sm-4 control-label">
                              <?php echo form_error('randomQuestion'); ?>
                            </span>
                        </div>


                    <?php
                    if(form_error('type'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="type" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_type")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $arrayType = array(0 => $this->lang->line("online_exam_select"));
                                if(inicompute($types)) {
                                    foreach ($types as $type) {
                                        $arrayType[$type->examTypeNumber] = $type->title;
                                    }
                                }
                                echo form_dropdown("type", $arrayType, set_value("type", $online_exam->examTypeNumber), "id='type' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('type'); ?>
                        </span>
                    </div>

                       
                    <?php
                    if(form_error('duration'))
                        echo "<div class='form-group has-error' id='durationDiv'>";
                    else
                        echo "<div class='form-group' id='durationDiv'>";
                    ?>
                        <label for="duration" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_duration")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="duration" name="duration" value="<?=set_value('duration', $online_exam->duration)?>" placeholder="<?=$this->lang->line("online_exam_minute")?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('duration'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('startdate'))
                        echo "<div class='form-group has-error' id='startdateDiv'>";
                    else
                        echo "<div class='form-group' id='startdateDiv'>";
                    ?>
                        <label for="startdate" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_startdatetime")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="startdate" name="startdate" value="<?=set_value('startdate', !is_null($online_exam->startDateTime) ? date('d-m-Y', strtotime($online_exam->startDateTime) ) : '' )?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('startdate'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('enddate'))
                        echo "<div class='form-group has-error' id='enddateDiv'>";
                    else
                        echo "<div class='form-group' id='enddateDiv'>";
                    ?>
                        <label for="enddate" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_enddatetime")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="enddate" name="enddate" value="<?=set_value('enddate', !is_null($online_exam->endDateTime) ? date('d-m-Y', strtotime($online_exam->endDateTime) ) : '')?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('enddate'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('startdatetime'))
                        echo "<div class='form-group has-error' id='startdatetimeDiv'>";
                    else
                        echo "<div class='form-group' id='startdatetimeDiv'>";
                    ?>
                        <label for="startdatetime" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_startdatetime")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="startdatetime" name="startdatetime" value="<?=set_value('startdatetime', !is_null($online_exam->startDateTime) ? date('d-m-Y h:i a', strtotime($online_exam->startDateTime)) : '' )?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('startdatetime'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('enddatetime'))
                        echo "<div class='form-group has-error' id='enddatetimeDiv'>";
                    else
                        echo "<div class='form-group' id='enddatetimeDiv'>";
                    ?>
                        <label for="enddatetime" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_enddatetime")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="enddatetime" name="enddatetime" value="<?=set_value('enddatetime', !is_null($online_exam->endDateTime) ? date('d-m-Y h:i a', strtotime($online_exam->endDateTime)) : '' )?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('enddatetime'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('markType'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="markType" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_markType")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $markTypeArray[0]   = $this->lang->line("online_exam_select");
                                $markTypeArray[5]   = $this->lang->line("online_exam_percentage");
                                $markTypeArray[10]  = $this->lang->line("online_exam_fixed");

                                echo form_dropdown("markType", $markTypeArray, set_value("markType", $online_exam->markType), "id='markType' class='form-control select2'"); 
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('markType'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('percentage'))
                        echo "<div class='form-group has-error'>";
                    else
                        echo "<div class='form-group'>";
                    ?>
                        <label for="percentage" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_passValue")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="percentage" name="percentage" value="<?=set_value('percentage', $online_exam->percentage)?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('percentage'); ?>
                        </span>
                    </div>
                    <?php
                        if(form_error('showResultAfterExam'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                        ?>
                        <label for="showResultAfterExam" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_showResultAfterExam")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $arrayStatus['0'] = $this->lang->line("online_exam_select");
                            $arrayStatus['1'] = $this->lang->line("online_exam_yes");
                            $arrayStatus['2'] = $this->lang->line("online_exam_no");
                            echo form_dropdown("showResultAfterExam", $arrayStatus, set_value("showResultAfterExam",$online_exam->showResultAfterExam), "id='showResultAfterExam' class='form-control select2'");
                            ?>
                        </div>
                            <span class="col-sm-4 control-label">
                              <?php echo form_error('showResultAfterExam'); ?>
                            </span>
                        </div>
                    <?php
                        if(form_error('showMarkAfterExam'))
                            echo "<div class='form-group has-error' >";
                        else
                            echo "<div class='form-group' >";
                        ?>
                        <label for="showMarkAfterExam" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_showMarkAfterExam")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $arrayStatusShow['0'] = $this->lang->line("online_exam_select");
                            $arrayStatusShow['1'] = $this->lang->line("online_exam_yes");
                            $arrayStatusShow['2'] = $this->lang->line("online_exam_no");
                            echo form_dropdown("showMarkAfterExam", $arrayStatusShow, set_value("showMarkAfterExam",$online_exam->showMarkAfterExam), "id='showMarkAfterExam' class='form-control select2'");
                            ?>
                        </div>
                            <span class="col-sm-4 control-label">
                              <?php echo form_error('showMarkAfterExam'); ?>
                            </span>
                        </div>


                    <div class="form-group <?=form_error('ispaid') ? 'has-error' : '' ?>" id="ispaidDiv" >
                        <label for="ispaid" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_payment_status")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $array = [
                                    5 => $this->lang->line("online_exam_select"),
                                    0 => $this->lang->line("online_exam_free"),
                                    1 => $this->lang->line("online_exam_paid")
                                ];
                                echo form_dropdown("ispaid", $array, set_value("ispaid", $online_exam->paid), "id='ispaid' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('ispaid'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('validDays'))
                        echo "<div class='form-group has-error' id='validDaysDiv'>";
                    else
                        echo "<div class='form-group' id='validDaysDiv'>";
                    ?>
                        <label for="validDays" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_validDays")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="validDays" name="validDays" value="<?=set_value('validDays', $online_exam->validDays)?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('validDays'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('cost'))
                        echo "<div class='form-group has-error' id='costDiv'>";
                    else
                        echo "<div class='form-group' id='costDiv'>";
                    ?>
                        <label for="cost" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_cost")?>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="cost" name="cost" value="<?=set_value('cost', number_format($online_exam->cost,2,'.',''))?>">
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('cost'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('judge'))
                        echo "<div class='form-group has-error' style='display: none;'>";
                    else
                        echo "<div class='form-group' style='display: none;'>";
                    ?>
                        <label for="judge" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_judge")?>
                        </label>
                        <div class="col-sm-6">
                            <?php
                            $array = [
                                0 => $this->lang->line("online_exam_auto"),
                                1 => $this->lang->line("online_exam_manually")
                            ];
                            echo form_dropdown("judge", $array, set_value("judge", $online_exam->judge), "id='judge' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('judge'); ?>
                        </span>
                    </div>

                    <?php
                    if(form_error('published'))
                        echo "<div class='form-group has-error' >";
                    else
                        echo "<div class='form-group' >";
                    ?>
                        <label for="published" class="col-sm-2 control-label">
                            <?=$this->lang->line("online_exam_published")?>
                            <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-6">
                            <?php
                                $array['0'] = $this->lang->line("online_exam_select");
                                $array['1'] = $this->lang->line("online_exam_yes");
                                $array['2'] = $this->lang->line("online_exam_no");
                                echo form_dropdown("published", $array, set_value("published",$online_exam->published), "id='published' class='form-control select2'");
                            ?>
                        </div>
                        <span class="col-sm-4 control-label">
                            <?php echo form_error('published'); ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("update_class")?>" >
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('.select2').select2();

    
    $(document).ready(function() {
        var type = "<?=$posttype?>";
        if(type == 0) {
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
        } else if(type == 2) {
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
        } else if(type == 4) {
            $('#startdateDiv').show();
            $('#enddateDiv').show();

            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();

            $('#startdate').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY'
            });
            $('#enddate').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY'
            });
        } else if(type == 5) {
            $('#startdatetimeDiv').show();
            $('#enddatetimeDiv').show();

            $('#enddateDiv').hide();
            $('#startdateDiv').hide();

            $('#startdatetime').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY hh:mm a'
            });
            $('#enddatetime').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY hh:mm a'
            });
        } else {
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
            $('#validDaysDiv').hide();
            $('#costDiv').hide();
        }
    });
    
    $('#type').change(function() {
        var type = $(this).val();
        if(type == 0) {
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
        } else if(type == 2) {
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
        } else if(type == 4) {
            $('#startdateDiv').show();
            $('#enddateDiv').show();

            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();

            $('#startdate').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY'
            });
            $('#enddate').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY'
            });
        } else if(type == 5) {
            $('#startdatetimeDiv').show();
            $('#enddatetimeDiv').show();

            $('#startdateDiv').hide();
            $('#enddateDiv').hide();

            $('#startdatetime').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY hh:mm a'
            });
            $('#enddatetime').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY hh:mm a'
            });
        }
    });

    $(function () {
        $('#startdatetimeDiv').hide();
        $('#startdatetimeDiv').show();
        var type = '<?=$posttype?>';
        
        if(type == 0) {
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
        } else if(type == 2 ) {
            $('#startdateDiv').hide();
            $('#enddateDiv').hide();
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
        } else if (type == 4) {
            $('#startdatetimeDiv').hide();
            $('#enddatetimeDiv').hide();
            $('#startdate').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY'
            });
            $('#enddate').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY'
            });
        } else if (type == 5) {
            $('#startdatetime').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY hh:mm a'
            });
            $('#enddatetime').datetimepicker({
                viewMode: 'years',
                format: 'DD-MM-YYYY hh:mm a'
            });
        }

        $('#validDaysDiv').hide();
        $('#costDiv').hide();
    });

    $("#classes").change(function() {
        var id = $(this).val();
        if(parseInt(id)) {
            if(id === '0') {
                $('#sectionID').val(0);
            } else {
                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('online_exam/getSection')?>",
                    data: {"id" : id},
                    dataType: "html",
                    success: function(data) {
                        $('#section').html(data);
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "<?=base_url('online_exam/getSubject')?>",
                    data: {"classID" : id},
                    dataType: "html",
                    success: function(data) {
                        $('#subject').html(data);
                    }
                });
            }
        }
    });

    $('#ispaid').change(function(event) {
        if($(this).val() == 1) {
            $('#costDiv').show();
        } else {
            $('#costDiv').hide();
        }
    });

    $(document).ready(function() {
        if($('#ispaid').val() == 1) {
            $('#costDiv').show();
        } else {
            $('#costDiv').hide();
        }
    });
</script>
