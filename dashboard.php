<?php 
require('connection.php');
$student_id = $_COOKIE['auth'];
include('inc/header.php');
?>


<div class="jumbotron mt-2 mt-sm-0">
    <h2 class="text-right">Student Dashboard</h2>
</div>


        <?php
        require('connection.php');

        $sql="SELECT 
            batch_student.*,
            batch.name AS batch_name,
            subject.name AS subject_name,
            course.name AS course_name
            FROM batch_student 
            INNER JOIN batch ON batch_student.batch_id = batch.id
            INNER JOIN subject ON batch_student.subject_id = subject.id
            INNER JOIN student ON batch_student.student_id = student.id
            INNER JOIN course ON subject.course_id = course.id
            WHERE batch_student.deleted != '1' 
            AND batch_student.active != '0'
            AND batch.deleted != '1'
            AND batch_student.student_id = '$student_id'
            ORDER BY batch.name ASC";
        $query=mysqli_query($connect,$sql);
        $x=1;
        while($row= mysqli_fetch_array($query)){
            $batch_id = $row['batch_id'];

        ?>
                <div class="accordion" id="accordionExample">
                    <div class="card">
                        <div class="card-header" id="heading-<?=$x?>" data-toggle="collapse" data-target="#collapse-<?=$x?>" aria-expanded="true" aria-controls="collapse-<?=$x?>">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button">
                                    <span><i class="fa fa-plus-circle mr-5" aria-hidden="true"></i></span>
                                </button>
                                <div class="d-inline">
                                    <span class="btn btn-warning"><?=$row['course_name']?></span>
                                    <span class="btn btn-secondary"><?=$row['subject_name']?></span>
                                    <span class="btn btn-primary"><?=$row['batch_name']?></span>
                                </div>
                            </h2>
                        </div>

                        <div id="collapse-<?=$x?>" class="collapse" aria-labelledby="heading-<?=$x?>" data-parent="#accordionExample">
                            <div class="card-body">
                                <table class="table">   
                                <?php
                                    $sql1="SELECT * FROM session WHERE batch_id = '$batch_id' AND deleted !='1'";
                                    $query1=mysqli_query($connect,$sql1);
                                    while($row1= mysqli_fetch_array($query1)){
                                ?>
                                        <tr>
                                            <td><?=$row1['name']?></td>
                                            <td>
                                                <?php if(!$row1['completed']){ ?>
                                                    <a class="badge badge-primary" target="_blank" href="<?=$row1['link']?>">Visit</a>
                                                <?php } else {?>
                                                    <a class="badge badge-success" href="#">Completed</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php
                                    }                         
                                    ?>                            
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

        <?php
        $x++;
        }
        ?>





<?php include('inc/footer.php');?>

