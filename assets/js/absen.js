document.addEventListener('DOMContentLoaded', function () {
    var qrImg = document.getElementById('qr-img');
    var countdownEl = document.getElementById('countdown');
    var statusEl = document.getElementById('qr-status');
    var jamEl = document.getElementById('jam-sekarang');

    var currentToken = null;
    var countdownTimer = null;
    var pollTimer = null;
    var secondsLeft = 30;

    function tickClock() {
        var now = new Date();
        jamEl.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
    }
    tickClock();
    setInterval(tickClock, 1000);

    function renderQr(url) {
        var qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(url);
        qrImg.src = qrApiUrl;
    }

    function setStatus(text, kind) {
        statusEl.textContent = text;
        statusEl.className = 'qr-status ' + kind;
    }

    function generateToken() {
        fetch('api/generate_token.php')
            .then(function (res) { return res.json(); })
            .then(function (data) {
                currentToken = data.token;
                renderQr(data.scan_url);
                secondsLeft = data.expires_in;
                countdownEl.textContent = secondsLeft;
                setStatus('Menunggu scan...', 'waiting');
                startPolling();
                startCountdown();
            });
    }

    function startCountdown() {
        clearInterval(countdownTimer);
        countdownTimer = setInterval(function () {
            secondsLeft--;
            countdownEl.textContent = secondsLeft > 0 ? secondsLeft : 0;
            if (secondsLeft <= 0) {
                clearInterval(countdownTimer);
                generateToken();
            }
        }, 1000);
    }

    function startPolling() {
        clearInterval(pollTimer);
        pollTimer = setInterval(function () {
            if (!currentToken) return;
            fetch('api/check_status.php?token=' + encodeURIComponent(currentToken))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status === 'terpakai') {
                        setStatus('✓ Absen tercatat', 'success');
                        clearInterval(pollTimer);
                        clearInterval(countdownTimer);
                        // Muat ulang halaman sebentar biar jam masuk/keluar & riwayat ter-update
                        setTimeout(function () { window.location.reload(); }, 2000);
                    }
                });
        }, 2000);
    }

    generateToken();
    startCountdown();
});
