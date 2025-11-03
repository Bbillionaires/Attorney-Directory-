<?php /* Header */ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Attorney Directory</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/styles.css" />
</head>
<body class="bg-slate-950 text-slate-100">
  <header class="border-b border-slate-800 bg-slate-900/60 backdrop-blur">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-500/20 text-sky-400">⚖️</span>
        <a href="/" class="text-lg font-semibold tracking-wide">Attorney Directory</a>
      </div>
      <nav class="flex items-center gap-6 text-sm">
        <a class="hover:text-sky-300" href="/">Home</a>
        <a class="hover:text-sky-300" href="/public_list.php">All Items</a>
        <a class="rounded-lg bg-sky-500 px-3 py-1.5 text-slate-900 font-semibold hover:bg-sky-400" href="/add.php">Add new</a>
      </nav>
    </div>
  </header>
  <main class="mx-auto max-w-6xl px-4 py-8">
