/**
 * Reviews Loader
 * Loads reviews from database and displays them
 */

(function() {
  'use strict';

  async function loadReviews() {
    const grid = document.getElementById('reviewsGrid');
    if (!grid) return;

    try {
      const response = await fetch('api/reviews.php');
      if (!response.ok) {
        throw new Error('Failed to load reviews');
      }
      
      const reviews = await response.json();
      
      if (!reviews || reviews.length === 0) {
        grid.innerHTML = '<div class="review-card"><p class="review-card__text">Отзывы скоро появятся</p></div>';
        return;
      }
      
      const reviewsToShow = 4;
      let reviewsShown = 0;
      
      reviews.forEach((review, index) => {
        const card = document.createElement('div');
        card.className = 'review-card';
        if (index >= reviewsToShow) {
          card.classList.add('review-card--hidden');
        }
        
        const initials = review.avatar_initials || 
          review.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        
        const starSvg = '<svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        const stars = Array(review.rating).fill(0).map(() => starSvg).join('');
        
        card.innerHTML = `
          <div class="review-card__header">
            <div class="review-card__avatar">${initials}</div>
            <div class="review-card__info">
              <div class="review-card__name">${review.name}</div>
              <div class="review-card__project">${review.project}</div>
            </div>
            <div class="review-card__stars">
              ${stars}
            </div>
          </div>
          <p class="review-card__text">${review.text}</p>
        `;
        
        grid.appendChild(card);
        reviewsShown++;
      });
      
      // Setup "Load More" button
      const hiddenReviews = grid.querySelectorAll('.review-card--hidden');
      const loadMoreBtn = document.getElementById('loadMoreReviews');
      
      if (hiddenReviews.length > 0 && loadMoreBtn) {
        loadMoreBtn.style.display = 'block';
        let currentShown = reviewsToShow;
        
        loadMoreBtn.addEventListener('click', () => {
          const toShow = Array.from(hiddenReviews).slice(0, 4);
          toShow.forEach(review => {
            review.classList.remove('review-card--hidden');
            review.style.opacity = '0';
            review.style.transform = 'translateY(20px)';
            setTimeout(() => {
              review.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
              review.style.opacity = '1';
              review.style.transform = 'translateY(0)';
            }, 10);
          });
          currentShown += toShow.length;
          if (currentShown >= reviews.length) {
            loadMoreBtn.style.display = 'none';
          }
        });
      } else if (loadMoreBtn) {
        loadMoreBtn.style.display = 'none';
      }
    } catch (error) {
      console.error('Error loading reviews:', error);
      const grid = document.getElementById('reviewsGrid');
      if (grid) {
        grid.innerHTML = '<div class="review-card"><p class="review-card__text">Ошибка загрузки отзывов</p></div>';
      }
    }
  }

  // Load reviews when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadReviews);
  } else {
    loadReviews();
  }
})();

