(() => {
  const form = document.querySelector('.filter-group');
  const list = document.querySelector('.admin-list');
  if (!form || !list) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const params = new URLSearchParams(new FormData(form));
    const response = await fetch('ajax/manage_users_filter.php?' + params.toString());
    const users = await response.json();

    const visibleIds = new Set(users.map(u => u.id));

    list.querySelectorAll('.inline-admin-form').forEach(f => {
      const userId = parseInt(f.querySelector('input[name="user_id"]').value);
      f.style.display = visibleIds.has(userId) ? '' : 'none';
    });
  });
})();
