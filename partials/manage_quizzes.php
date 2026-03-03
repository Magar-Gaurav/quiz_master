<?php
session_start();
include '../connection/db.php';

if(isset($_POST['action'])){
    header('Content-Type: application/json');
    $response=['status'=>'error','msg'=>'Unknown error'];

    /* Add Quiz */
    if($_POST['action']==='add_quiz'){
        $title=trim($_POST['quiz_title']??'');
        if($title!==''){
            $stmt=$conn->prepare("INSERT INTO quizzes(title) VALUES(?)");
            $stmt->bind_param("s",$title);
            if($stmt->execute()){
                $response=['status'=>'success','id'=>$stmt->insert_id,'title'=>$title];
            } else $response['msg']=$stmt->error;
            $stmt->close();
        } else $response['msg']="Quiz title cannot be empty";
        echo json_encode($response); exit;
    }

    /* Delete Quiz */
    if($_POST['action']==='delete_quiz'){
        $quizId=intval($_POST['quiz_id']);
        $questions=$conn->query("SELECT id FROM questions WHERE quiz_id=$quizId");
        while($q=$questions->fetch_assoc()) $conn->query("DELETE FROM options WHERE question_id=".$q['id']);
        $conn->query("DELETE FROM questions WHERE quiz_id=$quizId");
        $stmt=$conn->prepare("DELETE FROM quizzes WHERE id=?");
        $stmt->bind_param("i",$quizId); $stmt->execute(); $stmt->close();
        echo json_encode(['status'=>'success']); exit;
    }

    /* Add Question */
    if($_POST['action']==='add_question'){
        $quizId=intval($_POST['quiz_id']);
        $text=trim($_POST['question_text']??'');
        if($text!==''){
            $stmt=$conn->prepare("INSERT INTO questions(quiz_id,question_text) VALUES(?,?)");
            $stmt->bind_param("is",$quizId,$text);
            if($stmt->execute()){
                $response=['status'=>'success','id'=>$stmt->insert_id,'text'=>$text];
            } else $response['msg']=$stmt->error;
            $stmt->close();
        } else $response['msg']="Question cannot be empty";
        echo json_encode($response); exit;
    }

    /* Delete Question */
    if($_POST['action']==='delete_question'){
        $qid=intval($_POST['question_id']);
        $conn->query("DELETE FROM options WHERE question_id=$qid");
        $stmt=$conn->prepare("DELETE FROM questions WHERE id=?");
        $stmt->bind_param("i",$qid); $stmt->execute(); $stmt->close();
        echo json_encode(['status'=>'success']); exit;
    }

    /* Add 4 Options */
    if($_POST['action']==='add_4_options'){
        $qid=intval($_POST['question_id']);
        $options=$_POST['options']??[];
        $correctIndex=intval($_POST['correct_index']??0);
        if(count($options)===4){
            foreach($options as $i=>$text){
                $text=trim($text);
                $isCorrect=($i==$correctIndex)?1:0;
                $stmt=$conn->prepare("INSERT INTO options(question_id,option_text,is_correct) VALUES(?,?,?)");
                $stmt->bind_param("isi",$qid,$text,$isCorrect);
                $stmt->execute(); $stmt->close();
            }
            $response=['status'=>'success','options'=>$options,'correct'=>$correctIndex];
        } else $response['msg']="You must provide 4 options";
        echo json_encode($response); exit;
    }
}

$quizzes=$conn->query("SELECT * FROM quizzes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Quizzes</title>
<style>
/* Base */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #0f172a;
    color: #f1f5f9;
    padding: 20px;
    margin: 0;
}
h2 { text-align: center; color: #38bdf8; }

/* Container */
.quiz-container {
    max-width: 900px;
    margin: 20px auto;
}

/* Quiz Card */
.quiz-block {
    background: #1e293b;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}
.quiz-block h3 {
    margin: 0;
    color: #facc15;
}
.questions-list {
    margin-top: 15px;
}

/* Inputs & Buttons */
input[type=text] {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    border-radius: 6px;
    border: none;
    outline: none;
    background: #334155;
    color: #f1f5f9;
}
input[type=text]::placeholder { color: #94a3b8; }

button {
    margin-top: 10px;
    padding: 8px 14px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: 0.2s;
}
#add-quiz-btn { background: linear-gradient(90deg,#3b82f6,#0ea5e9); color: #fff; }
#add-quiz-btn:hover { opacity: 0.9; }

.add-question-btn { background: #22c55e; color: #fff; }
.add-question-btn:hover { opacity: 0.9; }

.delete-btn { background: #ef4444; color: #fff; }
.delete-btn:hover { opacity: 0.9; }

.add-4-options-btn { background: #f59e0b; color: #fff; }
.add-4-options-btn:hover { opacity: 0.9; }

/* Option blocks */
.option-block {
    background: #334155;
    padding: 8px 12px;
    margin-top: 5px;
    border-radius: 6px;
}
.option-block.correct { background: #16a34a; }

/* Option form */
.option-form {
    background: #1e293b;
    padding: 12px;
    border-radius: 8px;
    margin-top: 10px;
}
.option-form label { display: flex; align-items: center; margin-top: 8px; gap: 10px; }
.option-form input[type=text] { flex: 1; }

/* Success Message */
.success-msg {
    color: #22c55e;
    font-weight: 600;
    display: none;
    margin-left: 10px;
}

/* Responsive */
@media (max-width: 600px){
    .quiz-block, .option-form { padding: 15px; }
    input[type=text] { padding: 8px; }
    button { padding: 6px 10px; font-size: 14px; }
}
</style>
</head>
<body>
<div class="quiz-container">
<h2>Manage Quizzes</h2>

<!-- Add Quiz -->
<input type="text" id="new-quiz-title" placeholder="Enter new quiz title">
<button id="add-quiz-btn">Add Quiz</button>

<div id="quizzes-list">
<?php while($quiz=$quizzes->fetch_assoc()): ?>
<div class="quiz-block" data-quiz-id="<?= $quiz['id'] ?>">
  <h3><?= htmlspecialchars($quiz['title']) ?></h3>
  <button class="delete-btn delete-quiz-btn">Delete Quiz</button>

  <input type="text" class="new-question-text" placeholder="Enter question">
  <button class="add-question-btn">Add Question</button>

  <div class="questions-list">
  <?php
    $questions=$conn->query("SELECT * FROM questions WHERE quiz_id=".$quiz['id']);
    while($q=$questions->fetch_assoc()):
      $opts=$conn->query("SELECT * FROM options WHERE question_id=".$q['id']);
  ?>
    <div class="quiz-block" data-question-id="<?= $q['id'] ?>">
      <strong><?= htmlspecialchars($q['question_text']) ?></strong>
      <button class="delete-btn delete-question-btn">Delete Question</button>
      <?php while($o=$opts->fetch_assoc()): ?>
        <div class="option-block <?= $o['is_correct']?'correct':'' ?>"><?= htmlspecialchars($o['option_text']) ?> <?= $o['is_correct']?'(✔ Correct)':'' ?></div>
      <?php endwhile; ?>
      <?php if($opts->num_rows===0): ?>
      <div class="option-form">
        <?php for($i=0;$i<4;$i++): ?>
        <label>
          <input type="radio" name="correct_index_<?= $q['id'] ?>" value="<?= $i ?>" <?= $i===0?'checked':'' ?>> Correct
          <input type="text" name="options[]" placeholder="Option <?= $i+1 ?>">
        </label>
        <?php endfor; ?>
        <button class="add-4-options-btn">Add 4 Options</button>
        <span class="success-msg">Options added!</span>
      </div>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
  </div>
</div>
<?php endwhile; ?>
</div>
</div>

<script>
// AJAX Helper
async function postData(fd){ const res=await fetch('',{method:'POST',body:fd}); return await res.json(); }

document.addEventListener('click', async e=>{
  if(e.target.id==='add-quiz-btn'){
    const title=document.getElementById('new-quiz-title').value.trim();
    if(!title)return alert('Enter quiz title');
    const fd=new FormData(); fd.append('action','add_quiz'); fd.append('quiz_title',title);
    const res=await postData(fd);
    if(res.status==='success'){
      const div=document.createElement('div'); div.className='quiz-block'; div.dataset.quizId=res.id;
      div.innerHTML=`<h3>${res.title}</h3>
        <button class="delete-btn delete-quiz-btn">Delete Quiz</button>
        <input type="text" class="new-question-text" placeholder="Enter question">
        <button class="add-question-btn">Add Question</button>
        <div class="questions-list"></div>`;
      document.getElementById('quizzes-list').prepend(div);
      document.getElementById('new-quiz-title').value='';
    } else alert(res.msg||'Error adding quiz');
  }

  if(e.target.classList.contains('delete-quiz-btn')){
    const quizDiv=e.target.closest('.quiz-block');
    const fd=new FormData(); fd.append('action','delete_quiz'); fd.append('quiz_id',quizDiv.dataset.quizId);
    const res=await postData(fd); if(res.status==='success') quizDiv.remove(); else alert(res.msg||'Error');
  }

  if(e.target.classList.contains('add-question-btn')){
    const quizDiv=e.target.closest('.quiz-block'); const input=quizDiv.querySelector('.new-question-text'); const text=input.value.trim();
    if(!text)return alert('Enter question');
    const fd=new FormData(); fd.append('action','add_question'); fd.append('quiz_id',quizDiv.dataset.quizId); fd.append('question_text',text);
    const res=await postData(fd);
    if(res.status==='success'){
      const qDiv=document.createElement('div'); qDiv.className='quiz-block'; qDiv.dataset.questionId=res.id;
      qDiv.innerHTML=`<strong>${res.text}</strong>
        <button class="delete-btn delete-question-btn">Delete Question</button>
        <div class="option-form">
          ${[0,1,2,3].map(i=>`<label><input type="radio" name="correct_index_${res.id}" value="${i}" ${i===0?'checked':''}> Correct <input type="text" name="options[]" placeholder="Option ${i+1}"></label>`).join('')}
          <button class="add-4-options-btn">Add 4 Options</button>
          <span class="success-msg">Options added!</span>
        </div>`;
      quizDiv.querySelector('.questions-list').prepend(qDiv); input.value='';
    } else alert(res.msg||'Error adding question');
  }

  if(e.target.classList.contains('delete-question-btn')){
    const qDiv=e.target.closest('[data-question-id]'); const fd=new FormData(); fd.append('action','delete_question'); fd.append('question_id',qDiv.dataset.questionId);
    const res=await postData(fd); if(res.status==='success') qDiv.remove(); else alert(res.msg||'Error');
  }

  if(e.target.classList.contains('add-4-options-btn')){
    const qDiv=e.target.closest('[data-question-id]'); const optionForm=qDiv.querySelector('.option-form');
    const options=[...optionForm.querySelectorAll('input[name="options[]"]')].map(i=>i.value);
    const correctIndex=parseInt(optionForm.querySelector('input[type=radio]:checked').value);
    const fd=new FormData(); fd.append('action','add_4_options'); fd.append('question_id',qDiv.dataset.questionId);
    options.forEach(o=>fd.append('options[]',o)); fd.append('correct_index',correctIndex);
    const res=await postData(fd);
    if(res.status==='success'){
      optionForm.style.display='none'; optionForm.querySelector('.success-msg').style.display='inline';
      options.forEach((opt,idx)=>{
        const div=document.createElement('div'); div.className='option-block'+(idx===correctIndex?' correct':''); div.textContent=opt+(idx===correctIndex?' (✔ Correct)':''); qDiv.appendChild(div);
      });
    } else alert(res.msg||'Error adding options');
  }
});
</script>
</body>
</html>