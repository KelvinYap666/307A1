<?php
require_once 'lib.php';
//index == main page

// Handle form submit BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $selected_quiz = $_POST['quiz_options'] ?? 'animals';
    $_SESSION['name'] = trim((string)($_POST['name'] ?? ''));
    $_SESSION['game_started'] = true;

    // Reset these when starting a fresh game
    $_SESSION['total_score'] = 0;
    $_SESSION['last_saved_total'] = 0;

    if ($selected_quiz === 'environment') {
        redirect('environment.php');
    }
    redirect('animal.php');
}
//header  
page_header('The World Around Us', 'Pick a quiz and start playing!');
?>

<div class="card">
  <form method="post" autocomplete="off">

    <div class="row">
      <div class="field">
        <label for="name">Your nickname</label>
        <input id="name" type="text" name="name" placeholder="e.g., Darrick" required>
        <div class="small">Tip: Use the same nickname to build your total score on the leaderboard.</div>
      </div>
    </div>

    <div class="hr"></div>

    <div class="q">
      <p>Choose a quiz</p>
      <label><input type="radio" name="quiz_options" value="animals" checked> 🐾 Animals (Fill in the blanks)</label><br>
      <label><input type="radio" name="quiz_options" value="environment"> 🌿 Environment (True / False)</label>
    </div>

    <div class="btnbar">
      <input class="primary" type="submit" name="submit" value="Start Game">
      <a class="btn ghost" href="leaderboard.php">View Leaderboard</a>
    </div>

  </form>
</div>

<?php page_footer(); ?>
