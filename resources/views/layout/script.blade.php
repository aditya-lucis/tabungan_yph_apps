
<script src="{{ URL::asset('assets/master/lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ URL::asset('assets/master/lib/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/master/js/azia.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = "{{ csrf_token() }}";
    const notifList = document.getElementById('notif-list');
    const notifText = document.getElementById('notifText');
    const notifBell = document.getElementById('notifBell');
    const toggleBtn = document.getElementById('toggleNotif');
    const toggleWrapper = document.getElementById('notifToggleWrapper');
    const maxToShow = 5;
    let notifications = [];

    // Fetch notifications
    fetch('/notifications/list')
      .then(res => res.json())
      .then(data => {
        notifications = data.notifications;
        const unreadCount = data.unreadCount;

        notifText.textContent = `You have ${unreadCount} unread notification`;
        if (unreadCount > 0) notifBell.classList.add('new');

        renderNotifications(notifications);
      });

    function renderNotifications(list) {
      notifList.innerHTML = '';

      list.forEach((notif, index) => {
        const item = document.createElement('div');
        item.className = `media notif-item ${notif.isUnread ? 'new' : ''} ${index >= maxToShow ? 'extra-notif d-none' : ''}`;
        item.dataset.id = notif.id;

        item.innerHTML = `
          <div class="az-img-user">
            <span class="${notif.isUnread ? 'text-primary' : 'text-muted'}">${notif.icon}</span>
          </div>
          <div class="media-body">
            <a href="${notif.url}" class="notif-link text-dark d-block mb-1" 
              data-id="${notif.id}" 
              data-href="${notif.url}" 
              style="cursor: pointer; text-decoration: none;">
              ${notif.message}
            </a>
            <span class="text-muted">${notif.created_at}</span>
          </div>
        `;
        notifList.appendChild(item);
      });

      // Show toggle if needed
      if (list.length > maxToShow) {
        toggleWrapper.classList.remove('d-none');
      }
    }

    // Toggle show more
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        document.querySelectorAll('.extra-notif').forEach(el => el.classList.toggle('d-none'));
        this.textContent = this.textContent === 'View All Notifications'
          ? 'Hide Extra Notifications'
          : 'View All Notifications';
      });
    }

    // Read on click
    notifList.addEventListener('click', function (e) {
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

          const remaining = document.querySelectorAll('.notif-item.new').length;
          notifText.textContent = `You have ${remaining} unread notification`;
          if (remaining === 0) notifBell.classList.remove('new');

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
