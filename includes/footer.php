</main>
    </div>
</div>

<footer class="py-6 text-center text-xs text-stone-500">
    &copy; <?= date('Y') ?> SPACENEST — Tim PPL FTST UNISMA 45 Bekasi. All rights reserved.
</footer>

<script>
// Keep the sidebar highlight in sync with dashboard sections.
(function () {
    var links = Array.from(document.querySelectorAll('.site-sidebar a'));
    if (!links.length) return;

    function updateActiveLink() {
        var current = new URL(window.location.href);
        var active = links.find(function (link) {
            var target = new URL(link.href, current);
            return target.pathname === current.pathname
                && (current.hash ? target.hash === current.hash : !target.hash);
        });

        links.forEach(function (link) {
            var isActive = link === active;
            link.classList.toggle('on', isActive);
            if (isActive) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });
    }

    updateActiveLink();
    window.addEventListener('hashchange', updateActiveLink);
})();

// Pencarian suara (hanya tampil bila browser mendukung)
(function () {
    var mic = document.getElementById('mic'), q = document.getElementById('q');
    var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!mic) return;
    if (!SR) { mic.remove(); return; }
    mic.addEventListener('click', function () {
        var r = new SR(); r.lang = 'id-ID';
        r.onresult = function (e) { q.value = e.results[0][0].transcript; q.dispatchEvent(new Event('input')); };
        r.start();
    });
})();
</script>
</body>
</html>