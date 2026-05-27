(() => {
    const form = document.querySelector('.filter-form-equipment');
    const tbody =  document.querySelector('table tbody');
    if(!form || !tbody) return;

    function buildRow(item) {
        const tr = document.createElement('tr');
        tr.innerHTML = ` 
            <th scope="row">${item.name}</th>
            <td>${item.quantity}</td>
            <td><span class="badge badge-${item.status}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></td>
        `;
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
