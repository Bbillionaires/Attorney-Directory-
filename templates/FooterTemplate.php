<?php // FooterTemplate.php ?>
  </main>

  <!-- Footer -->
  <footer class="site-wrap mt-8 mb-6">
    <div class="glass p-5 text-slate-300 text-sm flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <i data-lucide="shield-check" class="w-5 h-5"></i>
        <span>© <?= date('Y') ?> Attorney Directory. All rights reserved.</span>
      </div>
      <div class="flex items-center gap-3">
        <a class="hover:underline" href="/public_list.php">Public Directory</a>
        <a class="hover:underline" href="/list.php">All Items</a>
        <a class="hover:underline" href="/add.php">Add new</a>
      </div>
    </div>
<a href="/add.php" id="floatAdd" class="fixed bottom-6 right-6 btn-primary shadow-lg flex items-center gap-2">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
  </svg> Add New
</a>
  </footer>

  <script>
    // lucide icons
    window.lucide && lucide.createIcons();
    // mobile menu
    const btn = document.getElementById('menuBtn');
    const nav = document.getElementById('mobileNav');
    btn && btn.addEventListener('click', ()=> nav.classList.toggle('hidden'));
  </script>
</body>
</html>
