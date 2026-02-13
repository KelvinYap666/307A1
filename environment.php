<?php
require_once 'lib.php';

if (empty($_SESSION['name'])) {
    redirect('index.php');
}

//session started in index.php
$_SESSION['last_quiz'] = 'environment';

//read file and store questions and answers in an array
$fileQn = 'environmentQn.txt';
$lines = file($fileQn, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$questions = [];
foreach ($lines as $line) {
    list($question, $ans) = explode(',', $line);
    $questions[]=[
      "question" => trim($question),
      "answer" => trim($ans)
    ];
}

//randomly select 4 questions for the quiz
shuffle($questions);
$selected = array_slice($questions, 0, 4);
$_SESSION['environment_questions'] = $selected;

//header
page_header('Environment Quiz', 'True or False?');
?>


<div class="card">
  <div class="row" style="justify-content:space-between; align-items:center;">
    <div class="pill">👧🧒 Player: <?php echo h($_SESSION['name']); ?></div>
    <div class="pill">⭐ Total: <?php echo (int)($_SESSION['total_score'] ?? 0); ?></div>
  </div>

  <div class="hr"></div>

  <!-- display webform page to type in answers -->
  <form action="environmentAns.php" method="post">
    <?php foreach ($_SESSION['environment_questions'] as $i => $q): ?>
      <div class="q">
        <p><?php echo h($q['question']); ?></p>
        <label><input type="radio" name="ans[<?php echo h($i); ?>]" value="True"> True</label>
        <label style="margin-left:14px;"><input type="radio" name="ans[<?php echo h($i); ?>]" value="False"> False</label>
      </div>
    <?php endforeach; ?>

    <!-- submit buttons -->
    <div class="btnbar">
      <input class="primary" type="submit" name="submit" value="Submit Answers">
      <a class="btn ghost" href="leaderboard.php">Leaderboard</a>
      <a class="btn ghost" href="index.php">Main Menu</a>
    </div>
  </form>
</div>

<?php page_footer(); ?>
