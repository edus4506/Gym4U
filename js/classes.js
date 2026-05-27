(() => {
  function showMessage(text, type) {
    const old = document.querySelector('.alert-ajax');
    if (old) old.remove();

    const msg = document.createElement('p');
    msg.className = 'alert alert-' + type + ' alert-ajax';
    msg.textContent = text;
    document.querySelector('main').prepend(msg);

    setTimeout(() => msg.remove(), 3000);
  }

  function initEnrollButtons() {
    const forms = document.querySelectorAll('.enroll-form');
    if (forms.length === 0) return;

    forms.forEach((form) => {
      form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);
        const mode = formData.get('mode');
        const row = form.closest('tr');

        const response = await fetch('ajax/enroll.php', {
          method: 'POST',
          body: formData,
        });

        const data = await response.json();

        if (!data.success) {
          const messages = {
            'full': 'That class is already full.',
            'members-only': 'Only members can enroll in classes.',
            'already-enrolled': 'You are already enrolled in that class.',
            'invalid': 'That action could not be completed.',
            'not-logged-in': 'Please login to enroll.',
          };
          showMessage(messages[data.error] || 'Something went wrong.', 'error');
          return;
        }

        const capacityCell = row.querySelectorAll('td')[2];
        if (capacityCell) {
          capacityCell.textContent = data.enrolled + '/' + data.capacity;
        }

        const newForm = document.createElement('form');
        newForm.className = 'enroll-form';
        newForm.method = 'post';
        newForm.action = 'actions/class_enrollment_action.php';

        const csrfInput = form.querySelector('input[name="csrf_token"]');

        newForm.innerHTML = `
          <input type="hidden" name="schedule_id" value="${formData.get('schedule_id')}">
          <input type="hidden" name="csrf_token" value="${csrfInput ? csrfInput.value : ''}">
          <input type="hidden" name="mode" value="${mode === 'enroll' ? 'cancel' : 'enroll'}">
          <button class="btn ${mode === 'enroll' ? 'btn-cancel' : 'btn-enroll'}" type="submit">
            ${mode === 'enroll' ? 'Cancel' : 'Enroll'}
          </button>
        `;

        form.replaceWith(newForm);
        initFormListener(newForm);

        const successMessages = {
          'enroll': 'Enrollment confirmed.',
          'cancel': 'Enrollment cancelled.',
        };
        showMessage(successMessages[mode], 'success');

        row.classList.toggle('enrolled-row', mode === 'enroll');
      });
    });
  }

  function initFormListener(form) {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const formData = new FormData(form);
      const mode = formData.get('mode');
      const row = form.closest('tr');

      const response = await fetch('ajax/enroll.php', {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();

      if (!data.success) {
        const messages = {
          'full': 'That class is already full.',
          'members-only': 'Only members can enroll in classes.',
          'already-enrolled': 'You are already enrolled in that class.',
          'invalid': 'That action could not be completed.',
          'not-logged-in': 'Please login to enroll.',
        };
        showMessage(messages[data.error] || 'Something went wrong.', 'error');
        return;
      }

      const capacityCell = row.querySelectorAll('td')[2];
      if (capacityCell) {
        capacityCell.textContent = data.enrolled + '/' + data.capacity;
      }

      const newForm = document.createElement('form');
      newForm.className = 'enroll-form';
      newForm.method = 'post';
      newForm.action = 'actions/class_enrollment_action.php';

      const csrfInput = form.querySelector('input[name="csrf_token"]');

      newForm.innerHTML = `
        <input type="hidden" name="schedule_id" value="${formData.get('schedule_id')}">
        <input type="hidden" name="csrf_token" value="${csrfInput ? csrfInput.value : ''}">
        <input type="hidden" name="mode" value="${mode === 'enroll' ? 'cancel' : 'enroll'}">
        <button class="btn ${mode === 'enroll' ? 'btn-cancel' : 'btn-enroll'}" type="submit">
          ${mode === 'enroll' ? 'Cancel' : 'Enroll'}
        </button>
      `;

      form.replaceWith(newForm);
      initFormListener(newForm);

      const successMessages = {
        'enroll': 'Enrollment confirmed.',
        'cancel': 'Enrollment cancelled.',
      };
      showMessage(successMessages[mode], 'success');

      row.classList.toggle('enrolled-row', mode === 'enroll');
    });
  }

  window.initEnrollButtons = initEnrollButtons;

  document.addEventListener('DOMContentLoaded', () => {
    initEnrollButtons();
  });
})();
