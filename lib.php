<?php
// Shared helpers for ISIT307 Assignment 1 (txt-only storage)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//file reading and writing leaderboard
const LEADERBOARD_FILE = 'leaderboard.txt';

// HTML escape helper
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// Simple redirect helper
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// Load leaderboard entries into an associative array
function load_leaderboard_assoc(): array {
    $entries = [];
    //check file exists
    if (!file_exists(LEADERBOARD_FILE)) {
        return $entries;
    }
    //read file line by line using explode  
    $lines = file(LEADERBOARD_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(',', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $name = trim($parts[0]);
        $scoreRaw = trim($parts[1]);

        if ($name === '') {
            continue;
        }

        $entries[$name] = (int)$scoreRaw;
    }

    return $entries;
}

//save leaderboard entries from an associative array
function save_leaderboard_assoc(array $entries): void {
    $out = '';
    foreach ($entries as $name => $score) {
        $name = str_replace(["\n", "\r", ',' ], [' ', ' ', ' '], (string)$name);
        $out .= $name . ',' . (int)$score . "\n";
    }
    file_put_contents(LEADERBOARD_FILE, $out);
}

/**
 * Save this session's score to the leaderboard (incrementally).
 *
 * Why incremental?
 * - Users may open the leaderboard multiple times in a session.
 * - We should only add the NEW points since the last time we saved.
 *
 * Uses:
 * - $_SESSION['name']
 * - $_SESSION['total_score']
 * - $_SESSION['last_saved_total'] (created automatically)
 */
function save_session_score(): void {
    if (!isset($_SESSION['name']) || trim((string)$_SESSION['name']) === '') {
        return;
    }

    if (!isset($_SESSION['total_score'])) {
        return;
    }

    $name = trim((string)$_SESSION['name']);
    $currentTotal = (int)$_SESSION['total_score'];
    $lastSaved = (int)($_SESSION['last_saved_total'] ?? 0);

    $delta = $currentTotal - $lastSaved;

    $entries = load_leaderboard_assoc();

    // Ensure player exists even if score is 0
    if (!isset($entries[$name])) {
        $entries[$name] = 0;
    }

    // Apply delta if any
    if ($delta !== 0) {
        $entries[$name] += $delta;
    }

    save_leaderboard_assoc($entries);

    $_SESSION['last_saved_total'] = $currentTotal;
}


//page header and footer helpers
function page_header(string $title, string $subtitle = ''): void {
    echo "<!DOCTYPE html>\n";
    echo "<html><head>\n";
    echo "<meta charset='utf-8'>\n";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'>\n";
    echo "<title>" . h($title) . "</title>\n";
    echo "<link rel='stylesheet' href='style.css'>\n";
    echo "</head><body>\n";
    echo "<div class='wrapper'>\n";
    echo "<div class='brand'>\n";
    echo "  <div class='logo'>🌟</div>\n";
    echo "  <div>\n";
    echo "    <h1>" . h($title) . "</h1>\n";
    if ($subtitle !== '') {
        echo "    <p class='subtitle'>" . h($subtitle) . "</p>\n";
    }
    echo "  </div>\n";
    echo "</div>\n";
}

function page_footer(): void {
    echo "<div class='footer'>Made for kids — have fun learning! 🧠✨</div>\n";
    echo "</div></body></html>";
}
