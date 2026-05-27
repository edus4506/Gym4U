let stars = document.getElementsByClassName("star");

function gfg(n) {
    remove();
    const cls = ["one", "two", "three", "four", "five"][n - 1];
    for (let i = 0; i < n; i++) {
        stars[i].className = "star " + cls;
    }
    const input = document.getElementById("rating-value");
    if (input) input.value = n;
}

function remove() {
    for (let i = 0; i < 5; i++) {
        stars[i].className = "star";
    }
}

(() => {
  const form = document.querySelector('.review-form');
  if (!form) return;

  function showMessage(text, type) {
    const old = document.querySelector('.alert-ajax');
    if (old) old.remove();
    const msg = document.createElement('p');
    msg.className = 'alert alert-' + type + ' alert-ajax';
    msg.textContent = text;
    form.insertAdjacentElement('beforebegin', msg);
    setTimeout(() => msg.remove(), 4000);
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    const response = await fetch('ajax/review.php', {
      method: 'POST',
      body: formData,
    });

    const data = await response.json();

    if (!data.success) {
      const messages = {
        'not-enrolled': 'You are not enrolled in that class.',
        'not-attended': 'That class has not happened yet.',
        'members-only': 'Only members can leave reviews.',
        'invalid-input': 'Please fill in all fields.',
        'not-logged-in': 'Please login to leave a review.',
      };
      showMessage(messages[data.error] || 'Something went wrong.', 'error');
      return;
    }

    showMessage('Review submitted successfully!', 'success');
    form.reset();
    remove();
  });
})();