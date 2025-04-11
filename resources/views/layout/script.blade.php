
<script src="{{ URL::asset('assets/master/lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('assets/master/lib/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/master/js/azia.js') }}"></script>
<!-- <script src="{{ URL::asset('assets/master/js/notification.js') }}"></script> -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = "{{ csrf_token() }}";
    const toggleBtn = document.getElementById('toggleNotif');

    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        document.querySelectorAll('.extra-notif').forEach(function (el) {
          el.classList.toggle('d-none');
        });

        this.textContent = this.textContent === 'View All Notifications'
          ? 'Hide Extra Notifications'
          : 'View All Notifications';
      })
    }

    document.querySelector('.az-notification-list')?.addEventListener('click', function (e) {
      const target = e.target.closest('.notif-link');
      if (!target) return;

      e.preventDefault();

      const notifId = target.dataset.id;
      const urlToRedirect = target.dataset.href;

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
          const notifItem = target.closest('.notif-item');
          notifItem?.classList.remove('new');

          const notifCounterText = document.querySelector('.az-notification-text');
          const notifBell = document.querySelector('.az-header-notification > a');
          const remaining = document.querySelectorAll('.notif-item.new').length;

          if (notifCounterText) {
            notifCounterText.textContent = `You have ${remaining} unread notification`;
          }

          if (notifBell && remaining === 0) {
            notifBell.classList.remove('new');
          }

          // Redirect kalau bukan '#'
          if (urlToRedirect !== '#') {
            window.location.href = urlToRedirect;
          }
        })
        .catch(err => {
          console.error('Error saat update notifikasi:', err);
        });
    });
  });
</script>

@yield('script')
@yield('script-bottom')
