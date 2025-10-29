<?php
declare(strict_types=1);

// Safe HTML
function h(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Price formatting
function money(float $n): string {
    return number_format($n, 2);
}

// Newlines to <br>, safely
function n2br(?string $s): string {
    return nl2br(h($s ?? ''));
}

// Checkbox/boolean to 0/1
function is_checked($v): int {
    if ($v === null) return 0;
    $v = strtolower(trim((string)$v));
    return in_array($v, ['1','true','on','yes'], true) ? 1 : 0;
}
