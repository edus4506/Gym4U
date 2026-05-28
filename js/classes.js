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

  function handleEnrollResponse(data, form, formData, mode, row) {
    if (!data.success) {
      const messages = {
        'full': 'That class is already full.',
        'members-only': 'Only members can enroll in classes.',
        'already-enrolled': 'You are already enrolled in that class.',
        'invalid': 'That action could not be completed.',
        'not-logged-in': 'Please login to enroll.',
        'limit-reached': 'You have reached the maximum number of classes for your plan.',
      };
      showMessage(messages[data.error] || 'Something went wrong.', 'error');
      return;
    }

    const capacityCell = row.querySelectorAll('td')[2];
    if (capacityCell) {
      capacityCell.textContent = data.enrolled + '/' + data.capacity;
    }

    const csrfInput = form.querySelector('input[name="csrf_token"]');
    const newForm = document.createElement('form');
    newForm.className = 'enroll-form';
    newForm.method = 'post';
    newForm.action = 'actions/class_enrollment_action.php';
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

    showMessage(mode === 'enroll' ? 'Enrollment confirmed.' : 'Enrollment cancelled.', 'success');
    row.classList.toggle('enrolled-row', mode === 'enroll');

    if (data.mode === 'enroll' && data.maxClasses !== null && data.enrolledCount >= data.maxClasses) {
      document.querySelectorAll('.enroll-form').forEach(f => {
        if (f.querySelector('input[name="mode"]')?.value === 'enroll') {
          const span = document.createElement('span');
          span.className = 'muted';
          span.textContent = 'Limit reached';
          span.dataset.scheduleId = f.querySelector('input[name="schedule_id"]').value;
          span.dataset.csrfToken  = f.querySelector('input[name="csrf_token"]').value;
          f.replaceWith(span);
        }
      });
    }

    if (data.mode === 'cancel') {
      document.querySelectorAll('.muted').forEach(span => {
        if (span.textContent === 'Limit reached') {
          const f = document.createElement('form');
          f.className = 'enroll-form';
          f.method = 'post';
          f.action = 'actions/class_enrollment_action.php';
          f.innerHTML = `
            <input type="hidden" name="schedule_id" value="${span.dataset.scheduleId}">
            <input type="hidden" name="csrf_token"  value="${span.dataset.csrfToken}">
            <input type="hidden" name="mode" value="enroll">
            <button class="btn btn-enroll" type="submit">Enroll</button>
          `;
          span.replaceWith(f);
          initFormListener(f);
        }
      });
    }
  }

  function initFormListener(form) {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const formData = new FormData(form);
      const mode = formData.get('mode');
      const row = form.closest('tr');
      const response = await fetch('ajax/enroll.php', { method: 'POST', body: formData });
      const data = await response.json();
      handleEnrollResponse(data, form, formData, mode, row);
    });
  }

  function initEnrollButtons() {
    const forms = document.querySelectorAll('.enroll-form');
    if (forms.length === 0) return;
    forms.forEach(form => initFormListener(form));
  }

  function initWaitlistButtons() {
    document.querySelectorAll('.waitlist-form').forEach(form => {
      form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(form);
        const mode = formData.get('mode');

        const response = await fetch('ajax/waitlist.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (!data.success) {
          const messages = {
            'premium-only':     'This feature is only available for Premium members.',
            'already-enrolled': 'You are already enrolled in this class.',
            'not-logged-in':    'Please login first.',
            'invalid':          'That action could not be completed.',
          };
          showMessage(messages[data.error] || 'Something went wrong.', 'error');
          return;
        }

        const csrfInput = form.querySelector('input[name="csrf_token"]');
        const scheduleInput = form.querySelector('input[name="schedule_id"]');

        if (data.mode === 'join') {
          form.querySelector('input[name="mode"]').value = 'leave';
          const btn = form.querySelector('button');
          btn.textContent = 'Leave Waitlist';
          btn.className = 'btn btn-waitlist-cancel';
          showMessage('You joined the waitlist. You are #' + data.position + ' in line.', 'success');
        } else {
          form.querySelector('input[name="mode"]').value = 'join';
          const btn = form.querySelector('button');
          btn.textContent = 'Join Waitlist';
          btn.className = 'btn btn-waitlist';
          showMessage('You left the waitlist.', 'success');
        }
      });
    });
  }

  window.initEnrollButtons = initEnrollButtons;
  window.initWaitlistButtons = initWaitlistButtons;

  document.addEventListener('DOMContentLoaded', () => {
    initEnrollButtons();
    initWaitlistButtons();
  });
})();
