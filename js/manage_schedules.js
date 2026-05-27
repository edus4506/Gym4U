(() => {
  const form = document.querySelector('.filter-group');
  const list = document.querySelector('.admin-list');
  if (!form || !list) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const params = new URLSearchParams(new FormData(form));
    const response = await fetch('ajax/manage_schedules_filter.php?' + params.toString());
    const schedules = await response.json();

    const visibleIds = new Set(schedules.map(s => s.id));

    list.querySelectorAll('.inline-admin-form').forEach(f => {
      const scheduleId = parseInt(f.querySelector('input[name="schedule_id"]').value);
      f.style.display = visibleIds.has(scheduleId) ? '' : 'none';
    });
  });
})();
