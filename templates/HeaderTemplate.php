<?php
// HeaderTemplate.php — modern shell (Tailwind CDN + Inter + Lucide)
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Attorney Directory</title>

  <!-- Inter font -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/styles.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
          colors: {
            brand: { DEFAULT:'#0ea5e9', dark:'#0b7db2' },   /* cyan/blue */
            ink: { 100:'#f8fafc', 300:'#cbd5e1', 600:'#475569', 800:'#0f172a' }
          }
        }
      }
    }
  </script>

  <!-- Lucide icons -->
  <script defer src="https://unpkg.com/lucide@latest"></script>

  <!-- Base styles to auto-pretty existing markup without rewriting PHP -->
  <style>
    html,body{background:linear-gradient(180deg,#0b1220 0%,#0e1b2a 100%); min-height:100%; color:#e5e7eb;}
    .site-wrap{max-width:1200px;margin:auto;padding:24px;}
    /* “Glass” surface */
    .glass{background:rgba(255,255,255,0.06); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.08); border-radius:16px;}
    .hero{background:radial-gradient(1200px 300px at 20% -10%, rgba(34,211,238,.25), transparent 45%), 
                       radial-gradient(900px 240px at 80% -10%, rgba(59,130,246,.25), transparent 45%);}
    /* Make any UL that contains items behave like a responsive card grid */
    main ul{display:grid; grid-template-columns:repeat(1,minmax(0,1fr)); gap:16px;}
    @media(min-width:640px){ main ul{grid-template-columns:repeat(2,minmax(0,1fr));}} grid-flow-row-dense
    @media(min-width:1024px){ main ul{grid-template-columns:repeat(3,minmax(0,1fr));}}
    main li{list-style:none; border-radius:16px; background:rgba(255,255,255,.08); padding:18px;  grid-flow-row-dense
            border:1px solid rgba(255,255,255,.12); transition:transform .2s, box-shadow .2s;}
    main li:hover{transform:translateY(-2px); box-shadow:0 10px 30px rgba(0,0,0,.25);}
    /* Price badge */
    .price, .badge{display:inline-block; border-radius:9999px; padding:.25rem .6rem; 
                   background:rgba(14,165,233,.15); color:#93c5fd; font-weight:600; font-size:.875rem;}
    /* Buttons */
    .btn{display:inline-flex; align-items:center; gap:.5rem; border-radius:12px; padding:.55rem .9rem; 
         background:#0ea5e9; color:white; font-weight:600;}
    .btn:hover{background:#0b7db2}
    .btn.secondary{background:rgba(255,255,255,.12)} .btn.secondary:hover{background:rgba(255,255,255,.2)}
    /* Clean up any accidental literal  on pages */
    body :is(p,small,div,span){ white-space:pre-wrap; }
  </style>
  <link rel="stylesheet" href="/assets/styles.css"/>
</head>
<body class="font-sans">
  <!-- Header -->
  <header class="sticky top-0 z-50 border-b border-white/10 bg-slate-900/70 backdrop-blur">
    <div class="site-wrap flex items-center justify-between py-3">
      <a href="/" class="flex items-center gap-2 text-white">
        <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2l7 4v6c0 5-3 9-7 10c-4-1-7-5-7-10V6z"/></svg>
        <span class="text-lg font-semibold tracking-wide">Attorney Directory</span>
      </a>
      <nav class="hidden sm:flex items-center gap-1">
        <a href="/" class="px-3 py-2 rounded-lg text-slate-200 hover:bg-white/10">Home</a>
        <a href="/list.php" class="px-3 py-2 rounded-lg text-slate-200 hover:bg-white/10">All Items</a>
        <a href="/add.php" class="btn">Add new</a>
      </nav>
      <button class="sm:hidden px-3 py-2 rounded-lg text-slate-200 hover:bg-white/10" id="menuBtn" aria-label="Menu">
        ☰
      </button>
    </div>
    <div id="mobileNav" class="sm:hidden hidden border-t border-white/10 bg-slate-900/90">
      <div class="site-wrap py-2 flex flex-col gap-1">
        <a href="/" class="px-3 py-2 rounded-lg text-slate-200 hover:bg-white/10">Home</a>
        <a href="/list.php" class="px-3 py-2 rounded-lg text-slate-200 hover:bg-white/10">All Items</a>
        <a href="/add.php" class="btn">Add new</a>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <div class="site-wrap hero">
    <div class="glass p-6 md:p-8 text-slate-100">
      <div class="flex items-center gap-3">
        <i data-lucide="scale" class="w-6 h-6"></i>
        <h1 class="text-2xl md:text-3xl font-semibold">Professional Legal Templates</h1>
      </div>
      <p class="mt-2 text-slate-300">Browse, compare, and purchase agreements. Clean UI, simple checkout.</p>
    </div>
  </div>

  <!-- Main -->
  <main class="site-wrap mt-6">
