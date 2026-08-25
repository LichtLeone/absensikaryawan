document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-nis');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var token = document.getElementById('token').value;
        var nis = document.getElementById('nis').value;
        var resultBox = document.getElementById('result-box');
        var submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Memproses...';

        var body = new URLSearchParams();
        body.append('token', token);
        body.append('nis', nis);

        fetch('api/verify.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString(),
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    resultBox.innerHTML = '<p class="alert alert-success">' + data.message + '<br>Terima kasih, ' + data.nama + '.</p>';
                    form.style.display = 'none';
                } else {
                    resultBox.innerHTML = '<p class="alert alert-error">' + data.message + '</p>';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Konfirmasi Absen';
                }
            })
            .catch(function () {
                resultBox.innerHTML = '<p class="alert alert-error">Terjadi kesalahan koneksi. Coba lagi.</p>';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Konfirmasi Absen';
            });
    });
});
