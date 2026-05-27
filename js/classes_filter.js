(() => {
    const form  = document.querySelector('.filter-group');
    const tbody = document.querySelector('.schedule-box tbody');
    if (!form || !tbody) return;

    function buildActionCell(slot) {
        const td = document.createElement('td');

        if (slot.action === 'login') {
            const a = document.createElement('a');
            a.className = 'btn btn-enroll';
            a.href = 'login.php';
            a.textContent = 'Login';
            td.appendChild(a);

        } else if (slot.action === 'members-only') {
            const span = document.createElement('span');
            span.className = 'muted';
            span.textContent = 'Members only';
            td.appendChild(span);

        } else if (slot.action === 'full') {
            const span = document.createElement('span');
            span.className = 'badge badge-full';
            span.textContent = 'Full';
            td.appendChild(span);

        } else {
            const f = document.createElement('form');
            f.className = 'enroll-form';
            f.method = 'post';
            f.action = 'actions/class_enrollment_action.php';
            f.dataset.scheduleId = slot.id;
            f.innerHTML =
                '<input type="hidden" name="schedule_id" value="' + slot.id + '">' +
                '<input type="hidden" name="csrf_token" value="' + slot.csrf_token + '">' +
                '<input type="hidden" name="mode" value="' + slot.action + '">' +
                '<button class="btn ' + (slot.action === 'cancel' ? 'btn-cancel' : 'btn-enroll') + '" type="submit">' +
                (slot.action === 'cancel' ? 'Cancel' : 'Enroll') +
                '</button>';
            td.appendChild(f);
        }

        return td;
    }

    function buildRow(slot) {
        const tr = document.createElement('tr');
        if (slot.action === 'cancel') tr.setAttribute('enrolled-row', '');

        const th = document.createElement('th');
        th.scope = 'row';
        th.textContent = slot.class_name;

        const tdTrainer = document.createElement('td');
        const a = document.createElement('a');
        a.href = 'trainer_profile.php?id=' + slot.trainer_id;
        a.textContent = slot.trainer_name;
        tdTrainer.appendChild(a);

        const tdDay = document.createElement('td');
        tdDay.textContent = slot.day + ', ' + slot.time;

        const tdCap = document.createElement('td');
        tdCap.textContent = slot.enrolled + '/' + slot.capacity;

        tr.appendChild(th);
        tr.appendChild(tdTrainer);
        tr.appendChild(tdDay);
        tr.appendChild(tdCap);
        tr.appendChild(buildActionCell(slot));

        return tr;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const params = new URLSearchParams(new FormData(form));
        const response = await fetch('ajax/classes_filter.php?' + params.toString());
        const slots = await response.json();

        tbody.innerHTML = '';

        if (slots.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="5">No classes found.</td>';
            tbody.appendChild(tr);
            return;
        }

        slots.forEach(slot => tbody.appendChild(buildRow(slot)));

        if (typeof window.initEnrollButtons === 'function') {
            window.initEnrollButtons();
        }
    });
})();
