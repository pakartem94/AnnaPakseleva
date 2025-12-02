/**
 * Anna Pakseleva Design Studio
 * Tablet-specific JavaScript
 * Optimized for tablets (768px - 1199px)
 */

(function() {
  'use strict';

  // Проверка, что мы на планшете
  function isTablet() {
    const width = window.innerWidth;
    return width >= 768 && width <= 1199;
  }

  // Адаптация портфолио для планшетов
  function adaptPortfolioForTablet() {
    if (!isTablet()) return;

    const portfolioGrid = document.getElementById('portfolioGrid');
    if (!portfolioGrid) return;

    // Для планшетов используем сетку 4 колонки вместо 6
    // Это уже обрабатывается в CSS, но можем добавить JS-оптимизации
    const items = portfolioGrid.querySelectorAll('.portfolio__item');
    
    items.forEach((item, index) => {
      // Добавляем небольшую задержку для плавной загрузки
      item.style.transitionDelay = `${(index % 8) * 0.05}s`;
    });
  }

  // Оптимизация touch-событий для планшетов
  function optimizeTouchEvents() {
    if (!isTablet()) return;

    // Улучшаем обработку свайпов в lightbox
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;

    let touchStartX = 0;
    let touchEndX = 0;

    lightbox.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    lightbox.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    }, { passive: true });

    function handleSwipe() {
      const swipeThreshold = 50;
      const diff = touchStartX - touchEndX;

      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          // Swipe left - next image
          const nextBtn = lightbox.querySelector('.lightbox__nav--next');
          if (nextBtn) nextBtn.click();
        } else {
          // Swipe right - prev image
          const prevBtn = lightbox.querySelector('.lightbox__nav--prev');
          if (prevBtn) prevBtn.click();
        }
      }
    }
  }

  // Адаптация размера изображений для планшетов
  function optimizeImagesForTablet() {
    if (!isTablet()) return;

    // Lazy loading с приоритетом для видимых изображений
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.removeAttribute('data-src');
            }
            imageObserver.unobserve(img);
          }
        });
      }, {
        rootMargin: '50px' // Начинаем загрузку заранее
      });

      images.forEach(img => {
        if (img.dataset.src) {
          imageObserver.observe(img);
        }
      });
    }
  }

  // Слайдер тарифов для планшетов (только горизонтальная прокрутка, без навигации)
  function initTariffsSlider() {
    if (!isTablet()) return;

    const tariffsGrid = document.querySelector('.tariffs__grid');
    if (!tariffsGrid) return;

    // Удаляем существующую навигацию, если есть
    const navWrapper = tariffsGrid.parentNode.querySelector('.tariffs__nav-wrapper');
    if (navWrapper) {
      navWrapper.remove();
    }
  }

  // Инициализация при загрузке
  function init() {
    adaptPortfolioForTablet();
    optimizeTouchEvents();
    optimizeImagesForTablet();
    initTariffsSlider();

    // Переинициализация при изменении размера окна
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        if (isTablet()) {
          adaptPortfolioForTablet();
          initTariffsSlider();
        }
      }, 250);
    });
  }

  // Запуск после загрузки DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

