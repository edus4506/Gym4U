(() => {
    const select = document.querySelector('.analytics-trainer-select');
    if (!select) return;

    async function loadTrainer(trainerId) {
        const response = await fetch('ajax/analytics_trainer.php?trainer_id=' + trainerId);
        const data = await response.json();
        if (data.error) return;

        updateHeader(data.trainer);
        updateCards(data.totals, data.topClass);
        updateBreakdown(data.classStats);
        updateReviews(data.recentReviews);
    }

    function updateHeader(trainer) {
        const h2 = document.querySelector('.analytics-trainer-name');
        if (h2) h2.textContent = '— ' + trainer.name;
    }

    function updateCards(totals, topClass) {
        document.querySelector('[data-card="enrolled"]').textContent    = totals.enrolled;
        document.querySelector('[data-card="fillRate"]').textContent    = totals.fillRate + '%';
        document.querySelector('[data-card="rating"]').textContent      = totals.overallRating !== null ? totals.overallRating + ' ★' : '—';
        document.querySelector('[data-card="reviews"]').textContent     = totals.totalReviews;
        document.querySelector('[data-card="slots"]').textContent       = totals.slotCount;

        const topEl    = document.querySelector('[data-card="topClass"]');
        const topLabel = document.querySelector('[data-card="topClassLabel"]');
        if (topClass) {
            topEl.textContent    = topClass.class_name;
            topLabel.textContent = 'Most Popular Class (' + topClass.enrolled + ' enrolled)';
        } else {
            topEl.textContent    = '—';
            topLabel.textContent = 'Most Popular Class';
        }
    }

    function updateBreakdown(classStats) {
        const container = document.querySelector('.analytics-breakdown');
        container.innerHTML = '';

        if (classStats.length === 0) {
            container.innerHTML = '<p class="muted">No classes assigned yet.</p>';
            return;
        }

        classStats.forEach(slot => {
            const pct = slot.capacity > 0
                ? Math.min(100, Math.round((slot.enrolled / slot.capacity) * 100))
                : 0;
            const full = slot.enrolled >= slot.capacity;

            const row = document.createElement('div');
            row.className = 'analytics-row';
            row.innerHTML = `
                <div class="analytics-row-main">
                    <strong>${slot.class_name}</strong>
                    <span class="muted">${slot.day} at ${slot.time}</span>
                </div>
                <div class="analytics-row-stats">
                    <div class="analytics-stat">
                        <span class="analytics-stat-label">Enrolled</span>
                        <span class="analytics-stat-value">${slot.enrolled} / ${slot.capacity}</span>
                        <div class="analytics-bar-track">
                            <div class="analytics-bar-fill ${full ? 'full' : ''}" style="width:${pct}%"></div>
                        </div>
                    </div>
                    <div class="analytics-stat">
                        <span class="analytics-stat-label">Avg Rating</span>
                        <span class="analytics-stat-value">${slot.avg_rating !== null ? slot.avg_rating + ' ★' : '—'}</span>
                    </div>
                    <div class="analytics-stat">
                        <span class="analytics-stat-label">Reviews</span>
                        <span class="analytics-stat-value">${slot.review_count}</span>
                    </div>
                </div>`;
            container.appendChild(row);
        });
    }

    function updateReviews(reviews) {
        const container = document.querySelector('.analytics-reviews');
        container.innerHTML = '';

        if (reviews.length === 0) {
            container.innerHTML = '<p class="muted">No reviews yet.</p>';
            return;
        }

        const grid = document.createElement('div');
        grid.className = 'division-grid';

        reviews.forEach(r => {
            const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
            const article = document.createElement('article');
            article.className = 'review-card';
            article.innerHTML = `
                <h3>${r.class_name}</h3>
                <div class="review-header">
                    <strong>${r.member_name}</strong>
                    <span>${stars}</span>
                </div>
                <p class="r-p">${r.comment ? r.comment : ''}</p>`;
            grid.appendChild(article);
        });

        container.appendChild(grid);
    }

    select.addEventListener('change', () => loadTrainer(select.value));
})();
