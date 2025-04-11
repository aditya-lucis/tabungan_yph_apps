document.addEventListener('DOMContentLoaded', function () {
    // Ambil token dari meta
    const csrfToken = "{{ csrf_token() }}";

    // Event untuk klik notifikasi
    document.querySelectorAll('.notif-link').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();

        const notifId = this.dataset.id;
        const urlToRedirect = this.getAttribute('href');

        fetch(`/notifications/read/${notifId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({})
        }).then(res => res.json())
          .then(data => {
            // Mark as read di tampilan
            const notifItem = this.closest('.notif-item');
            notifItem?.classList.remove('new');

            // Update jumlah unread
            const notifCounterText = document.querySelector('.az-notification-text');
            const notifBell = document.querySelector('.az-header-notification > a');
            const remaining = document.querySelectorAll('.notif-item.new').length;

            if (notifCounterText) {
              notifCounterText.textContent = `You have ${remaining} unread notification`;
            }

            if (notifBell && remaining === 0) {
              notifBell.classList.remove('new');
            }

            // Redirect jika bukan #
            if (urlToRedirect !== '#') {
              window.location.href = urlToRedirect;
            }
          })
          .catch(err => {
            console.error('Gagal update notifikasi:', err);
            alert('Gagal mengupdate notifikasi.');
          });
      });
    });

    // Event untuk collapse/expand notifikasi
    const toggleBtn = document.getElementById('toggleNotif');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        document.querySelectorAll('.extra-notif').forEach(el => {
          el.classList.toggle('d-none');
        });
        this.textContent = this.textContent === 'View All Notifications' ? 'Hide Extra Notifications' : 'View All Notifications';
      });
    }
  });