(() => {
  const form = document.querySelector('.filter-group');
  const list = document.querySelector('.admin-list');
  if (!form || !list) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const params = new URLSearchParams(new FormData(form));
    const response = await fetch('ajax/manage_equipment_filter.php?' + params.toString());
    const equipment = await response.json();

    const visibleIds = new Set(equipment.map(eq => eq.id));

    list.querySelectorAll('.inline-admin-form').forEach(f => {
      const equipmentId = parseInt(f.querySelector('input[name="equipment_id"]').value);
      f.style.display = visibleIds.has(equipmentId) ? '' : 'none';
    });
  });
})();
