(() => {
  const form = document.querySelector('.filter-form');
  const grid = document.querySelector('.trainer-grid');
  if (!form || !grid) return;

  function buildCard(t) {
    const article = document.createElement('article');
    article.className = 'trainer-card';
    article.id = t.slug;

    const img = document.createElement('img');
    img.className = 'trainer-image';
    img.src = 'images/trainers/' + t.slug + '.png';
    img.alt = 'Portrait of ' + t.name;
    img.onerror = () => { img.style.display = 'none'; };

    const content = document.createElement('div');
    content.className = 'trainer-content';
    content.innerHTML = `
      <h3>${t.name}</h3>
      <p class="trainer-role">${t.title}</p>
      <p>${t.bio}</p>
      <p class="trainer-meta"><strong>Experience:</strong> ${t.experience_years} years</p>
      <p class="trainer-meta"><strong>Certifications:</strong> ${t.certifications}</p>
      <p class="trainer-meta"><strong>Specializations:</strong> ${t.specializations}</p>
      <p class="trainer-meta"><strong>Classes:</strong> ${t.classes || 'N/A'}</p>
    `;

    article.appendChild(img);
    article.appendChild(content);
    return article;
  }

  async function applyFilter() {
    const params = new URLSearchParams(new FormData(form));

    const response = await fetch('actions/trainers_filter.php?' + params.toString());
    const trainers = await response.json();

    grid.innerHTML = '';

    if (trainers.length === 0) {
      const msg = document.createElement('p');
      msg.textContent = 'No trainers found matching your filters.';
      grid.appendChild(msg);
      return;
    }

    trainers.forEach(t => grid.appendChild(buildCard(t)));
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    applyFilter();
  });
})();
