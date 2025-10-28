<?php
// Not real auth — just a simple edit/delete token.
// Set EDIT_TOKEN in Render → Environment if you want it secret.
function edit_token(): string {
  return getenv('EDIT_TOKEN') ?: 'devtoken';
}
function check_token(?string $t): bool {
  return hash_equals(edit_token(), (string)$t);
}
