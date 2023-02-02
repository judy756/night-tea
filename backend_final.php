<?php 

$dbserverName = 'sql1.njit.edu';
$dbUsername = '';
$dbPassword = '';
$dbName = '';

$conn = mysqli_connect($dbserverName,$dbUsername,$dbPassword,$dbName);

if(!$conn){
    die('Connection failed '. mysqli_connect_error());
}
else{
  
  //login:
  if(isset($_POST['ucid'])){
    $ucid = $_POST['ucid'];
    $password = $_POST['password'];
    $sql = "select * from user where ucid = '$ucid' and password = '$password';";
    $result = mysqli_query($conn,$sql);
    $resultChecker = mysqli_num_rows($result);
    if($resultChecker > 0){
        $row = mysqli_fetch_assoc($result);
        echo json_encode($row);
    }
    else{
        echo "";
    }
  }
  
  //************  Teacher *******************
  
  //getAllTests
  if(isset($_POST['allTestsForTeacher'])){
    //include_once 'db_inc.php';
    $ucid=$_POST['ucid'];
    $sql = "SELECT `testID`, `title`, `difficulty`, `isPublished`, `dueDate` FROM `test` WHERE ucid = '$ucid';";
    $result = mysqli_query($conn,$sql);
    $resultChecker = mysqli_num_rows($result);
    if($resultChecker >0){
        $arr = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($arr, $row);
        }
        echo json_encode($arr);
    }
    else{
        return 0;
    }
  }
  
  //get All Teacher's Classes
  
  if(isset($_POST['allTeacherClasses'])){
    $ucid = $_POST['ucid'];
    $sql = "SELECT `classID` FROM `classstudent` WHERE studentID = '$ucid';";
    $result = mysqli_query($conn,$sql);
    $resultChecker = mysqli_num_rows($result);
    if($resultChecker >0){
        $arr = array();
        while($row = mysqli_fetch_assoc($result)){
            $classID = $row['classID'];
            $sql2 = "SELECT `className` FROM `class` WHERE classID = '$classID';";
            $result2 = mysqli_query($conn,$sql2);
            if($result2){
                $row2 = mysqli_fetch_assoc($result2);
                $arr[$classID] = $row2['className'];
            }
            else{
                echo 'error';
            }
        }
        echo json_encode($arr);
    }
    else{
        echo 0;
    }
  
  }
  
  //get All test takers
  
  if(isset($_POST['testTakers'])){
    $testID = $_POST['testID'];
    $sql = "SELECT `ucid`, `isSubmitted`, `isCorrected`, `grade` FROM `testtobetaken` WHERE testID = '$testID'";
    $result = mysqli_query($conn,$sql);
    $arr = array();
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        echo json_encode($arr);
    }
    else{
        echo 'error';
    }
  }
  
  
  //get student's test
  
  if(isset($_POST['studentTestForTeacher'])){
    $studentID = $_POST['studentID'];
    $testID = $_POST['testID'];
    $sql = "SELECT `questionID`, `answer`, `studentFname`, `functionNamePts`, `for`, `while`, `recursion`, `comment`, `score` FROM `testanswers` WHERE testID=$testID AND studentID='$studentID';";
    $result = mysqli_query($conn,$sql);

    $arr = array();
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            //get original constraints:
            $arr1 = array();
            $questionID = $row['questionID'];
            $sql2 = "SELECT `for`, `while`, `recursion` FROM `constraint` WHERE questionID='$questionID';";
            $arr1 = $row;
            $result2 = mysqli_query($conn,$sql2);
            if($result2 == false){
                echo 'errorGettingConstraints';
            }
            else{
                $row2 = mysqli_fetch_assoc($result2);
                $arr1['constraints'] = $row2;

                //get original testCases:
                $sql3 = "SELECT `case`, `answer` FROM `testcases` WHERE questionID = '$questionID';";
                $result3 = mysqli_query($conn,$sql3);
                if($result3){
                    $arr2 = array();
                    while($row3 = mysqli_fetch_assoc($result3)){
                        $arr3=array();
                        $caseID = $row3['case'];
                        $sql4 = "SELECT `caseAnswer`, `casePts` FROM `casesanswer` WHERE testID = '$testID' AND studentID='$studentID' AND questionID = '$questionID' AND caseID = '$caseID';";
                        $result4 = mysqli_query($conn,$sql4);
                        if($result4){
                            $row4 = mysqli_fetch_assoc($result4);
                            $arr3 = array_merge($arr3,$row3);
                            if($row4['casePts'] == null){
                              $row4['casePts'] = "";
                            }
                            if($row4['caseAnswer'] == null){
                              $row4['caseAnswer'] = "";
                            }
                            $arr3 = array_merge($arr3,$row4);
                            array_push($arr2,$arr3);
                        }
                    }
                    $arr1['cases'] = $arr2;
                    $sql5 = "SELECT `question`,`functionName` FROM `question` WHERE questionID='$questionID'";
                    $result5 = mysqli_query($conn,$sql5);
                    if($result5){
                        $row5 = mysqli_fetch_assoc($result5);
                        $arr1['functionName'] = $row5['functionName'];
                        $arr1['question'] = $row5['question'];
                    }
                    $sql6 = "SELECT `score` FROM `testquestions` WHERE questionID='$questionID' AND testID='$testID'";
                    $result6 = mysqli_query($conn,$sql6);
                    if($result6){
                        $row6 = mysqli_fetch_assoc($result6);
                        $arr1['originalScore'] = $row6['score'];
                    }
                    array_push($arr,$arr1);
                }
                else{
                    echo 'errorGettingCases';
                }
            }
        }
        
        echo json_encode($arr);
    }
    else{
        echo 'errorGettingTestAnswers';
    }
  }
  
  //get All questions
  
  if(isset($_POST['allQuestions'])){
      $sql = "select `questionID`,`question`,`category`,`difficulty` from question order by category";
    $result = mysqli_query($conn,$sql);
    $resultChecker = mysqli_num_rows($result);
    if($resultChecker > 0){
        $arr = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        echo json_encode($arr);
         
    }
    else {
        echo 0;
    }
  
  }
  
  //get all questions 2
  
  if(isset($_POST['allQuestions2'])){
      $sql = "select `questionID`,`question`,`category`,`difficulty`,`functionName` from question order by category";
    $result = mysqli_query($conn,$sql);
    $resultChecker = mysqli_num_rows($result);
    if($resultChecker > 0){
        $arr = array();
        while($row = mysqli_fetch_assoc($result)){
            array_push($arr,$row);
        }
        echo json_encode($arr);
         
    }
    else {
        echo 0;
    }
  
  }
  
  //add question
  if(isset($_POST['addQuestion'])){
     $question = $_POST["question"];
       $category = $_POST["category"];
       $difficulty= $_POST['difficulty'];
     $case = $_POST["case"];
       $caseA = $_POST["caseA"];
       $fName= $_POST['fName'];
     $for= $_POST['for'];
     $while= $_POST['while'];
     $recursion= $_POST['recursion'];
     
     $sql="INSERT INTO `question` (`questionID`, `question`, `category`, `difficulty`, `functionName`) VALUES (DEFAULT,'$question','$category','$difficulty', '$fName')";
    $result = mysqli_query($conn,$sql);
    if($result){
        $latest_id =  mysqli_insert_id($conn);
        $sql2 = "INSERT INTO `constraint`(`constraintID`, `questionID`, `for`, `while`, `recursion`) VALUES (DEFAULT,'$latest_id','$for','$while','$recursion')";
        $result3 = mysqli_query($conn,$sql2);
        if(!$result3){
                echo "Error: " . $sql2 . "<br>" . mysqli_error($conn);
            }
        for($i=0;$i<count($case);$i++){
            $sql1="INSERT INTO `testcases`(`testCaseID`, `questionID`, `case`, `answer`) VALUES (DEFAULT,'$latest_id','$case[$i]','$caseA[$i]')";
            $result2 = mysqli_query($conn,$sql1);
            if(!$result2){
                echo "Error: " . $sql1 . "<br>" . mysqli_error($conn);
            }
        }
        echo 1;
    }
    else {
        echo 0;
    }
  }
  
  // add test
  if(isset($_POST['insertTest'])){
    $title = $_POST['testTitle'];
      $scores = $_POST['score'];
      $ids = $_POST['ids'];
      $difficulty = $_POST['difficulty'];
    $ucid = $_POST['ucid'];
    $arr = array();
    for($i=0;$i<count($ids);$i++){
        $arr[$ids[$i]] = $scores[$i];
    }
    //print_r($arr);
    $sql = "INSERT INTO `test`(`testID`, `title`, `ucid`, `difficulty`, `isPublished`) VALUES (DEFAULT,'$title','$ucid','$difficulty',false)"; 
    $result = mysqli_query($conn,$sql);
    if($result){
        $latest_id =  mysqli_insert_id($conn);
        foreach($arr as $key => $value){
            $sql2 = "INSERT INTO `testquestions`(`testQuestionID`, `questionID`, `testID`, `score`) VALUES (DEFAULT,'$key','$latest_id','$value')";
          $result2 = mysqli_query($conn,$sql2);
          if(!$result2){
            echo "Error: " . $sql2 . "<br>" . mysqli_error($conn);
          }
        }
        echo 1;
      }
    else {
        echo 0;
    }
  
  }
  
  //publish test
  
  if(isset($_POST['publish'])){
    $testID = $_POST['testID'];
    $date = $_POST['date'];
    $classes = $_POST['class'];
    $autoGrade = $_POST['autoGrade']; 
    $flag = false;
    //lets get all the student of each class
    for($i=0;$i<count($classes);$i++){
        $classID = $classes[$i];
        $sql1 = "SELECT `studentID` FROM `classstudent` WHERE classID = '$classID'";
        $result1 = mysqli_query($conn,$sql1);
        if($result1){
            while($row1 = mysqli_fetch_assoc($result1)){
                $studentID=$row1['studentID'];
                $sql2 = "SELECT `isTeacher` FROM `user` WHERE ucid = '$studentID'";
                $result2 = mysqli_query($conn,$sql2);
                if($result2){
                    $row2 = mysqli_fetch_assoc($result2);
                    if($row2['isTeacher'] == '0'){
                        $sql3 = "INSERT INTO `testtobetaken`(`testID`, `ucid`, `isSubmitted`, `isCorrected`, `autoGrade`) VALUES ('$testID','$studentID',false,false,'$autoGrade')";
                        $result3 = mysqli_query($conn,$sql3);
                        if(!$result3){
                            $flag = true;
                        }
                    }
                }
            }
        }
    }
    if(!$flag){
        $sql = "UPDATE `test` SET `isPublished`= true,`dueDate`='$date' WHERE testID = '$testID'";
        $result = mysqli_query($conn,$sql);
        if(!$result){
            echo 0;
        }
        else{
         echo 1;
        }
    }
   else{
     echo 'error';
   }
  }
  
  //unpublish
  if(isset($_POST['unpublish'])){
    $testID = $_POST['testID'];
      $sql = "UPDATE `test` SET `isPublished`= false,`dueDate`= NULL WHERE testID = '$testID'";
      $result = mysqli_query($conn,$sql);
    if(!$result){
          echo 0;
      }
      else{
          echo 1;
      }
  }
  
  //insert test answers no auto grading
  
  if(isset($_POST['noAutoGrade'])){
    $test = $_POST['test'];
    $testID = $_POST['testID'];
    $answers = $_POST['answer'];
    $studentID = $_POST['studentID'];
    $sql = "UPDATE `testtobetaken` SET `isSubmitted`= true WHERE testID='$testID' AND ucid='$studentID';";
    $result = mysqli_query($conn,$sql);
    if($result){
      //keep in mind test's last child is the title (not question)
      for($i=0;$i<count($test)-1;$i++){
        $questionID = $test[$i]['questionID'];
        $answer = $answers[$i];
        $sql2 = "INSERT INTO `testanswers`(`testID`, `studentID`, `questionID`, `answer`, `comment`) VALUES ('$testID','$studentID','$questionID','$answer','Not corrected yet');";
        $result2 = mysqli_query($conn,$sql2);
        if($result2 == false){
          echo 0;
        }
      } 
      echo 1;   
    }
    else{
      echo 0;
    }
  }
  
  //insert test answers with auto grading
  
  if(isset($_POST['autoGrade'])){
    $testID = $_POST['testID'];
    $answer = $_POST['answer'];
    $studentID = $_POST['studentID'];
    $questionID = $_POST['questionID'];
    $nameUsed = $_POST['nameUsed'];
    $comment = $_POST['comment'];
    $score = $_POST['score'];
    $cases = $_POST['cases'];
    $deductedPoints = $_POST['deductedPoints'];
    $casesAnswer = $_POST['casesAnswer'];
    $newScore = $_POST['newScore'];
    
    $x1 = $deductedPoints['functionNamePts'];
    $x2 = $deductedPoints['for'];
    $x3 = $deductedPoints['while'];
    $x4 = $deductedPoints['recursion'];
    
    $sql = "INSERT INTO `testanswers`(`testID`, `studentID`, `questionID`, `answer`, `studentFname`, `functionNamePts`, `for`, `while`, `recursion`, `comment`, `score`) VALUES ('$testID','$studentID','$questionID','$answer','$nameUsed','$x1','$x2','$x3','$x4','$comment','$newScore');";
    $result = mysqli_query($conn,$sql);
    if($result){
      $count = 1;
      foreach($cases as $case => $cAnswer){
        $y = $deductedPoints['case'.$count++];
        $z = $casesAnswer[$case];
        //echo "<br>case = ".$case."<br>casePts = ".$y."<br>"."user answer = ".$z."<br>"."correct answer = ".$cAnswer."<br>";
        $sql2 = "INSERT INTO `casesanswer`(`testID`, `studentID`, `questionID`, `caseID`, `caseAnswer`, `casePts`) VALUES ('$testID','$studentID','$questionID','$case','$z','$y')";
        $result2 = mysqli_query($conn,$sql2);
        if($result2 == false){
          echo 0;
        }
      }
      echo 1;  
    }
    else{
      echo 0;
    }
  }
  
  //insert answers with auto grading last update
  if(isset($_POST['autoGrade2'])){
    $studentID = $_POST['studentID'];
    $testID = $_POST['testID'];
    $total = $_POST['grade'];
    $sql3 = "UPDATE `testtobetaken` SET `isSubmitted`=true,`isCorrected`=true,`grade`='$total' WHERE testID='$testID' AND ucid='$studentID';";
    $result3 = mysqli_query($conn,$sql3);
    if($result3 == false){
      echo 0;
    }
    echo 1;
  }
  
  //correct test
  if(isset($_POST['updateGrade'])){
    $functionNamePts = $_POST['functionNamePts'];
      $casePoints = $_POST['casePoints'];
      $constraintsPoints = $_POST['constraintsPoints'];
      $numberOfQuestions = $_POST['numberOfQuestions'];
    $testID =  $_POST['testID'];
    $studentID =  $_POST['studentID'];
    $questionID = $_POST['questionID'];
    $numberOfCasess = $_POST['numberOfCasess'];
    $comment = $_POST['comment'];
    $questionResult = $_POST['questionResult'];
    $finalS= $_POST['finalS'];
    $caseID = $_POST['caseID'];
     
     $counter1 = 0;
     $counter2 = 1;
     $counter3 = 2;
     //update testAnswers
     for($i=0;$i<$numberOfQuestions;$i++){
       $sql = "UPDATE `testanswers` SET `functionNamePts`='$functionNamePts[$i]',`for`='$constraintsPoints[$counter1]',`while`='$constraintsPoints[$counter2]',`recursion`='$constraintsPoints[$counter3]',`comment`='$comment[$i]',`score`='$questionResult[$i]' WHERE testID = '$testID' AND studentID = '$studentID' AND questionID = '$questionID[$i]';";
       
       $result = mysqli_query($conn,$sql);
       if($result == false){
         echo 0;
       }
       $counter1+=3;
       $counter2+=3;
       $counter3+=3;
     }
     //update cases
     
     $sql0 = "SELECT `testID` FROM `casesanswer` WHERE testID = '$testID' AND studentID = '$studentID';";
     $result0 = mysqli_query($conn,$sql0);
     $resultChecker = mysqli_num_rows($result0);
     if($resultChecker==0){
       $c =0;
       for($i=0;$i<$numberOfQuestions;$i++){
         for($j = 0;$j<$numberOfCasess[$i];$j++){
           $sql = "INSERT INTO `casesanswer`(`testID`, `studentID`, `questionID`, `caseID`, `caseAnswer`, `casePts`) VALUES 
                                           ('$testID','$studentID','$questionID[$i]','$caseID[$c]',' ','$casePoints[$c]');";
           $c++;
           $result = mysqli_query($conn,$sql);
           if($result == false){
             echo 0;
           }
         }
       }
     }
     else{
       $c =0;
       for($i=0;$i<$numberOfQuestions;$i++){
         for($j = 0;$j<$numberOfCasess[$i];$j++){
           $sql = "UPDATE `casesanswer` SET `casePts`= '$casePoints[$c]' WHERE testID = '$testID' AND studentID = '$studentID' AND questionID = '$questionID[$i]';";
           $c++;
           $result = mysqli_query($conn,$sql);
           if($result == false){
             echo 0;
           }
         }
       }
     }
     
     
      //update testToBeTaken
      $sql = "UPDATE `testtobetaken` SET `isCorrected`=true, `grade`='$finalS' WHERE ucid = '$studentID' AND testID='$testID';";
      $result = mysqli_query($conn,$sql);
      if($result == false){
        echo 0;
      }
      else{
        echo 1;
      }
     
  }
  
  //************  Student *******************
  
  //get all student's tests
  
  if(isset($_POST['allStudentTests'])){
    $studentID = $_POST['studentID'];
    $sql="SELECT `testID`, `isSubmitted`, `isCorrected`, `autoGrade`, `grade` FROM `testtobetaken` WHERE ucid = '$studentID'";
    $result = mysqli_query($conn,$sql);
    $arr = array();
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $testID = $row['testID'];
            $sql2 = "SELECT `title`,`isPublished`, `dueDate` FROM `test` WHERE testID ='$testID';";
            $result2 = mysqli_query($conn,$sql2);
            if($result2){
                $row2 = mysqli_fetch_assoc($result2);
                $r = array_merge($row,$row2);
                array_push($arr,$r);
            }
        }
        echo json_encode($arr);
    }
    else{
        echo "";
    }
  
  }
  
  //let student take test
  if(isset($_POST['studentTest'])){
    $testID = $_POST['testID'];
    $sql0 = "SELECT `title` FROM `test` WHERE testID='$testID';";
    $result0 = mysqli_query($conn,$sql0);
    if(!$result0){
        echo 'error';
    }
    $row0 = mysqli_fetch_assoc($result0);
    $sql = "SELECT `questionID`, `score` FROM `testquestions` WHERE testID='$testID';";
    $result = mysqli_query($conn,$sql);
    $arr = array();
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $arr1 = array();
            $arr1 = array_merge($arr1,$row);
            $questionID = $row['questionID'];
            $sql2 = "SELECT `question`, `functionName` FROM `question` WHERE questionID='$questionID';";
            $result2 = mysqli_query($conn,$sql2);
            if($result2){
                $row2 = mysqli_fetch_assoc($result2);
                $arr1 = array_merge($arr1,$row2);
                $sql3 = "SELECT `case`, `answer` FROM `testcases` WHERE questionID='$questionID';";
                $result3 = mysqli_query($conn,$sql3);
                if($result3){
                    $cases = array();
                    while($row3 = mysqli_fetch_assoc($result3)){
                        $cases[$row3['case']] = $row3['answer'];
                    }
                    $arr1['testCases'] = $cases;
                    $sql4 = "SELECT `for`, `while`, `recursion` FROM `constraint` WHERE questionID='$questionID';";
                    $result4 = mysqli_query($conn,$sql4);
                    if($result4){
                        $row4 = mysqli_fetch_assoc($result4);
                        $arr1['constraints'] = $row4;
                    }
                }
            }
            array_push($arr,$arr1);
        }
        array_push($arr,$row0['title']);
        echo json_encode($arr);
    }
    else{
        echo 'error';
    }
  
  }
}