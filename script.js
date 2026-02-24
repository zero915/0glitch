(function () {
    var form = document.getElementById('subscribe-form');
    var area = document.getElementById('subscribe-area');
    var thankYou = document.getElementById('subscribe-thankyou');
    var errorEl = document.getElementById('subscribe-error');

    if (!form || !area || !thankYou) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var btn = document.getElementById('subscribe-btn');
        var origText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Sending…';
        }
        if (errorEl) {
            errorEl.classList.add('hidden');
            errorEl.innerHTML = '';
        }

        var body = new FormData(form);
        var req = new XMLHttpRequest();
        req.open('POST', 'subscribe.php');
        req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        req.responseType = 'json';

        req.onload = function () {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
            var data = req.response;
            if (data && data.ok) {
                area.classList.add('hidden');
                thankYou.classList.remove('hidden');
                if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
            } else {
                var msg = (data && data.error === 'invalid')
                    ? 'Please enter a valid email address.'
                    : 'Subscription is temporarily unavailable. Try again later.';
                if (errorEl) {
                    errorEl.innerHTML = '<p class="px-4 py-3 rounded-full bg-red-500/20 text-red-300 border border-red-500/40 max-w-lg mx-auto">' + msg + '</p>';
                    errorEl.classList.remove('hidden');
                }
            }
        };

        req.onerror = function () {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
            if (errorEl) {
                errorEl.innerHTML = '<p class="px-4 py-3 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 max-w-lg mx-auto">Something went wrong. Try again later.</p>';
                errorEl.classList.remove('hidden');
            }
        };

        req.send(body);
    });
})();
