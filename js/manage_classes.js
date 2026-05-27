(() => {
  const form = document.querySelector('.filter-group');
  const list = document.querySelector('.admin-list');
  if (!form || !list) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const params = new URLSearchParams(new FormData(form));
    const response = await fetch('ajax/manage_classes_filter.php?' + params.toString());
    const classes = await response.json();

    const visibleIds = new Set(classes.map(c => c.id));

    list.querySelectorAll('.inline-admin-form').forEach(f => {
      const classId = parseInt(f.querySelector('input[name="class_id"]').value);
      f.style.display = visibleIds.has(classId) ? '' : 'none';
    });
  });
})();
