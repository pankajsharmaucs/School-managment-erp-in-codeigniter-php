<div class="box">
    <!-- form start -->
    <div id="printablediv">
        <style type="text/css">

            .selected_div {
                background: #27c24c;
                border: thin white solid;
                border-radius: 50%;
                padding: 5px;
            }
            .clearfix {
                margin-bottom: 5px;
            }
            
            .question-body{
                font-size: 16px;
                font-weight: bold;
            }
            .question-body p {
                display: inline;
            }
            .question-body label { 
                font-size: 16px;
            }

            .question-body label h2 { 
                font-size: 16px;
                display: inline-block;
            }

            .question-answer {
                margin-top: 0px;
            }

            .table tr td {
                width: 50%;
            }

            .question-body .lb-mark {
                float: right;
                font-size: 16px;
                text-align: right;
            }

            .questionimg {
                width: 40% !important;
                padding-left: 10px;
                padding-top: 5px;
                height: 120px;
            }

            .headerInfo {
                margin-bottom: 5px;
            }

            .single_label {
                display: inline-block;
            }

            .singleFilup {
                display: inline-block;
                border-bottom: 1px solid #ddd;
                width: 50%;
            }

            @media print {
                .headerInfo {
                    margin-bottom: 30px;
                }
            }
        </style>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-12">
                    <?php 
                        if(inicompute($questions)) {
                        $i = 0;
                        foreach($questions as $question) {
                            $optionCount = $question->totalOption;
                            $i++; ?>
                            <div class="clearfix">
                                <div class="question-body">
                                    <label><b><?=$i?>.</b> <?=$question->question?></label>
                                </div>

                                <?php if($question->upload != '') { ?>
                                    <div>
                                        <img style="width:250px;height:150px;padding-left: 20px" src="<?=base_url('uploads/images/'.$question->upload)?>" alt="">
                                    </div>
                                <?php } ?>

                                <div class="question-answer">
                                    <table class="table">
                                        <tr>
                                        <?php
                                            $oc = 1;
                                            $tdCount = 0;
                                            $questionoptions = isset($question_options[$question->questionBankID]) ? $question_options[$question->questionBankID] : [];
                                            if(inicompute($questionoptions)) {
                                                $optionLabel = 'A';
                                                foreach ($questionoptions as $option) {
                                                    if($optionCount >= $oc) { $oc++;
                                                        if(isset($examquestionsuseranswer[$question->questionBankID]) && $option->optionID == $examquestionsuseranswer[$question->questionBankID]->optionID) {
                                                            if(isset($examquestionsanswer[$question->questionBankID]) && $examquestionsanswer[$question->questionBankID]->optionID==$examquestionsuseranswer[$question->questionBankID]->optionID) {
                                                                ?>
                                                                <td style="background: green;">
                                                                    <span style="color: #ffffff"><?= $optionLabel ?>.</span>
                                                                    <span style="color: #ffffff"><?= $option->name ?></span>
                                                                    <label for="option<?= $option->optionID ?>">
                                                                        <?php
                                                                        if (!is_null($option->img) && $option->img != "") { ?>
                                                                            <img class="questionimg"
                                                                                 src="<?= base_url('uploads/images/' . $option->img) ?>"/>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </label>
                                                                </td>
                                                                <?php
                                                                $optionLabel++;
                                                            }else { ?>
                                                                <td style="background: red;">
                                                                    <span
                                                                        style="color: #ffffff"><?= $optionLabel ?>.</span>
                                                                    <span
                                                                        style="color: #ffffff"><?= $option->name ?></span>
                                                                    <label for="option<?= $option->optionID ?>">
                                                                        <?php
                                                                        if (!is_null($option->img) && $option->img != "") { ?>
                                                                            <img class="questionimg"
                                                                                 src="<?= base_url('uploads/images/' . $option->img) ?>"/>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </label>
                                                                </td>

                                                                <?php

                                                                $optionLabel++;
                                                            }
                                                    } else {
                                                            if (isset($examquestionsanswer[$question->questionBankID]) && $option->optionID == $examquestionsanswer[$question->questionBankID]->optionID) { ?>
                                                                <td>
                                                                    <span><?=$optionLabel?>.</span>
                                                                    <span> <?=$option->name?></span>
                                                                    <span class="selected_div"><i class="fa fa-check text-white"></i></span>
                                                                    <label for="option<?=$option->optionID?>">
                                                                        <?php
                                                                        if(!is_null($option->img) && $option->img != "") { ?>
                                                                            <img class="questionimg" src="<?=base_url('uploads/images/'.$option->img)?>"/>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </label>
                                                                </td>
                                                                <?php
                                                                $optionLabel++;
                                                            }else {?>

                                                               <td>
                                                                <span><?=$optionLabel?>.</span>
                                                                <span><?=$option->name?></span>
                                                                <label for="option<?=$option->optionID?>">
                                                                    <?php
                                                                    if(!is_null($option->img) && $option->img != "") { ?>
                                                                        <img class="questionimg" src="<?=base_url('uploads/images/'.$option->img)?>"/>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </label>
                                                                </td>
                                                                <?php
                                                                $optionLabel++;
                                                            }
                                                        }
                                                    }
                                                    $tdCount++;
                                                    if($tdCount == 2) {
                                                        $tdCount = 0;
                                                        echo "</tr><tr>";
                                                    }
                                                }
                                            }
                                        ?>
                                        </tr>
                                    </table>
                                </div>

                            </div>
                    <?php } } ?>
                </div>
            </div><!-- row -->
        </div><!-- Body -->
    </div>
</div>