<?php
require_once 'lib.php';

// Handle actions from leaderboard page
$action = $_POST['action'] ?? null;

// Redirect based on action
if ($action === 'play_animals') {
    redirect('animal.php');
}
if ($action === 'play_environment') {
    redirect('environment.php');
}
if ($action === 'main_menu') {
    redirect('index.php');
}
if ($action === 'exit') {
    // Save current session score (if any) before ending
    save_session_score();
    session_destroy();
    redirect('index.php');
}

// Load leaderboard entries
$entries = [];
$assoc = load_leaderboard_assoc();
foreach ($assoc as $name => $score) {
    $entries[] = ['name' => $name, 'score' => (int)$score];
}

// Sorting option
$sort = $_GET['sort'] ?? 'score';
if ($sort === 'name') {
    usort($entries, fn($a, $b) => strcmp($a['name'], $b['name']));
} else {
    usort($entries, fn($a, $b) => $b['score'] <=> $a['score']);
}

page_header('Leaderboard', 'Who has the biggest score?');
?>

<div class="card">
  <div class="row" style="justify-content:space-between; align-items:center;">
    <?php if (!empty($_SESSION['name'])): ?>
      <div class="pill">👧🧒 Player: <?php echo h($_SESSION['name']); ?></div>
      <div class="pill">⭐ Session total: <?php echo (int)($_SESSION['total_score'] ?? 0); ?></div>
    <?php else: ?>
      <div class="pill">👀 Viewing as guest</div>
      <div class="pill">Tip: Start a game to earn points!</div>
    <?php endif; ?>
  </div>

  <div class="hr"></div>

  <div class="notice">
    Sort by:
    <a href="leaderboard.php?sort=score">Highest Score</a>
    &nbsp;|&nbsp;
    <a href="leaderboard.php?sort=name">Name</a>
  </div>

  <div class="hr"></div>

  <table class="table">
    <tr>
      <th>Rank</th>
      <th>Name</th>
      <th>Total Score</th>
    </tr>

    <?php if (empty($entries)): ?>
      <tr><td colspan="3">No scores yet — be the first!</td></tr>
    <?php else: ?>
      <?php foreach ($entries as $i => $e): ?>
        <tr>
          <td><?php echo $i + 1; ?></td>
          <td><?php echo h($e['name']); ?></td>
          <td><?php echo (int)$e['score']; ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>

  <div class="hr"></div>

  <form method="post">
    <div class="btnbar">
      <button class="btn ghost" type="submit" name="action" value="play_animals">🐾 Play Animals</button>
      <button class="btn ghost" type="submit" name="action" value="play_environment">🌿 Play Environment</button>
      <button class="btn primary" type="submit" name="action" value="main_menu">Main Menu</button>
      <?php if (!empty($_SESSION['name'])): ?>
        <button class="btn bad" type="submit" name="action" value="exit">Exit & Save</button>
      <?php else: ?>
        <button class="btn bad" type="submit" name="action" value="main_menu">Exit</button>
      <?php endif; ?>
    </div>
  </form>

  <div class="small" style="margin-top:10px;">
    Note: Your score is saved automatically when you view the leaderboard or exit.
  </div>
</div>

<?php page_footer(); ?>
