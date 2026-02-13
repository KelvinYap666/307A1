<?php
require_once 'lib.php';
if (empty($_SESSION['name'])) {
    redirect('index.php');
}

//session started in environment.php
$questions = $_SESSION['environment_questions'] ?? [];

//initialize score counters
$score = 0;
$correctCount = 0;
$wrongCount = 0;

/* Process submitted answers
    if correct +2, if wrong or blank -1
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST['action'])) {
    foreach ($_SESSION['environment_questions'] as $i => $q) {
        $question = $q['question'];
        $correctAnswers[$question] = $q['answer'];
        $userAnswer = $_POST['ans'][$i] ?? null;

        if ($userAnswer === null) {
            $score -= 1;
            $wrongCount++;
            continue;
        }

        if (isset($correctAnswers[$question]) && $userAnswer === $correctAnswers[$question]) {
            $score += 2;
            $correctCount++;
        } else {
            $score -= 1;
            $wrongCount++;
        }
    }
    // Update total score in session
    $_SESSION['total_score'] = (int)($_SESSION['total_score'] ?? 0) + $score;
    $_SESSION['last_quiz'] = 'environment';
}

// Handle action buttons
$action = $_POST['action'] ?? null;
if ($action === 'exit') {
    save_session_score();

    $finalName = (string)($_SESSION['name'] ?? '');
    $finalScore = (int)($_SESSION['total_score'] ?? 0);

    session_destroy();

    page_header('Goodbye!', 'See you again soon!');
    echo "<div class='card'>";
    echo "<div class='pill'>👧🧒 Player: " . h($finalName) . "</div>";
    echo "<div class='hr'></div>";
    echo "<div class='pill good'>🏁 Final total score: " . $finalScore . "</div>";
    echo "<div class='btnbar' style='margin-top:16px;'>";
    echo "  <a class='btn primary' href='index.php'>Restart Game</a>";
    echo "  <a class='btn ghost' href='leaderboard.php'>View Leaderboard</a>";
    echo "</div>";
    echo "</div>";
    page_footer();
    exit;
}

if ($action === 'tryagain') {
    redirect('environment.php');
}

if ($action === 'switch') {
    $_SESSION['environment_randomKeys'] = [];
    redirect('animal.php');
}

if ($action === 'leaderboard') {
    save_session_score();
    redirect('leaderboard.php');
}

page_header('Environment Results', 'How did you do?');
?>

<div class="card">
  <div class="row" style="justify-content:space-between; align-items:center;">
    <div class="pill">👧🧒 Player: <?php echo h($_SESSION['name']); ?></div>
    <div class="pill">⭐ Total: <?php echo (int)($_SESSION['total_score'] ?? 0); ?></div>
  </div>

  <div class="hr"></div>

  <div class="row">
    <div class="pill good">✅ Correct: <?php echo (int)$correctCount; ?></div>
    <div class="pill bad">❌ Wrong / Blank: <?php echo (int)$wrongCount; ?></div>
    <div class="pill">🎯 This round: <?php echo (int)$score; ?></div>
  </div>

  <div class="hr"></div>

<!-- action buttons -->
  <form method="post">
    <div class="btnbar">
      <button class="btn ghost" type="submit" name="action" value="tryagain">Try Environment Again</button>
      <button class="btn ghost" type="submit" name="action" value="switch">Switch to Animals</button>
      <button class="btn primary" type="submit" name="action" value="leaderboard">Leaderboard</button>
      <button class="btn bad" type="submit" name="action" value="exit">Exit</button>
    </div>
  </form>

  <div class="notice" style="margin-top:14px;">
    Scoring: +2 correct, -1 wrong or blank.
  </div>
</div>

<?php page_footer(); ?>
