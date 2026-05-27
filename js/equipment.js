(() => {
    const form = document.querySelector('.filter-form-equipment');
    const tbody =  document.querySelector('table tbody');
    if(!form || !tbody) return;

    function buildRow(item) {
        const tr = document.createElement('tr');

        const th = document.createElement('th');
        th.scope = 'row';
        th.textContent = item.name;

        const tdQty = document.createElement('td');
        tdQty.textContent = item.quantity;

        const badge = document.createElement('span');
        badge.className = 'badge badge-' + item.status;
        badge.textContent = item.status.charAt(0).toUpperCase() + item.status.slice(1);
        const tdStatus = document.createElement('td');
        tdStatus.appendChild(badge);

        tr.appendChild(th);
        tr.appendChild(tdQty);
        tr.appendChild(tdStatus);
        return tr;
    }

    form.addEventListener('submit', async(e) => {
        e.preventDefault();

        const params = new URLSearchParams(new FormData(form));
        const response = await fetch('ajax/equipment.php?' + params.toString());
        const equipment = await response.json();

        tbody.innerHTML = '';

        if(equipment.length ===  0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="3">No equipment found.</td>';
            tbody.appendChild(tr);
            return;
        }

        equipment.forEach(item => tbody.appendChild(buildRow(item)));
    })

})();
