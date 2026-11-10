</main>
    </div>
</div>

<footer class="py-6 text-center text-xs text-stone-500">
    &copy; <?= date('Y') ?> SPACENEST — Tim PPL FTST UNISMA 45 Bekasi. All rights reserved.
</footer>

<script>
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