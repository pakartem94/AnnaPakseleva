/**
 * Anna Pakseleva Design Studio
 * Mobile-specific JavaScript
 * Optimized for mobile devices (max-width: 767px)
 */

(function() {
  'use strict';

  // Проверка, что мы на мобильном устройстве
  function isMobile() {
    const width = window.innerWidth;
    return width < 768;
  }

  // ===========================================
  // PORTFOLIO - ПОЛНАЯ ПЕРЕРАБОТКА ДЛЯ МОБИЛЬНЫХ
  // ===========================================

  // Убираем inline стили grid-column и grid-row для мобильных
  // Размеры задаются через CSS (предзаполненное распределение)
  function removeInlineGridStyles() {
    if (!isMobile()) return;

    const portfolioGrid = document.getElementById('portfolioGrid');
    if (!portfolioGrid) return;

    const items = portfolioGrid.querySelectorAll('.portfolio__item');
    items.forEach((item) => {
      // Полностью удаляем inline стили всеми возможными способами
      if (item.style.gridColumn) {
        item.style.removeProperty('grid-column');
      }
      if (item.style.gridRow) {
        item.style.removeProperty('grid-row');
      }
      // Очищаем через прямое присваивание
      item.style.gridColumn = '';
      item.style.gridRow = '';
      // Удаляем через setProperty с пустым значением
      try {
        item.style.setProperty('grid-column', '', 'important');
        item.style.setProperty('grid-row', '', 'important');
      } catch(e) {
        // Если не поддерживается important, просто очищаем
        item.style.gridColumn = '';
        item.style.gridRow = '';
      }
      // Удаляем атрибут style полностью и восстанавливаем только нужные
      const otherStyles = {};
      for (let i = 0; i < item.style.length; i++) {
        const prop = item.style[i];
        if (prop !== 'grid-column' && prop !== 'grid-row') {
          otherStyles[prop] = item.style.getPropertyValue(prop);
        }
      }
      item.style.cssText = '';
      Object.keys(otherStyles).forEach(prop => {
        item.style.setProperty(prop, otherStyles[prop]);
      });
    });
  }
  
  // Делаем функцию доступной глобально
  window.removeInlineGridStyles = removeInlineGridStyles;

  // Перехватываем и переопределяем создание элементов портфолио
  function interceptPortfolioCreation() {
    if (!isMobile()) return;

    // Ждем, пока main.js загрузится и создаст функцию loadPortfolioImages
    let attempts = 0;
    const maxAttempts = 200; // 10 секунд максимум
    
    const checkAndIntercept = setInterval(() => {
      attempts++;
      
      // Проверяем, есть ли функция loadPortfolioImages
      if (typeof window.loadPortfolioImages === 'function') {
        // Если еще не переопределили
        if (!window.loadPortfolioImagesOriginal) {
          // Сохраняем оригинальную функцию
          window.loadPortfolioImagesOriginal = window.loadPortfolioImages;
          
          // Переопределяем функцию для мобильных - загружаем ровно 6 элементов
          window.loadPortfolioImages = function(project, patternsToLoad, append) {
            const portfolioGrid = document.getElementById('portfolioGrid');
            if (!portfolioGrid) return;
            
            // Получаем данные проекта
            const portfolioData = window.portfolioData || {};
            const data = portfolioData[project];
            if (!data || !data.images) return;
            
            const images = data.images || [];
            if (images.length === 0) return;
            
            // Получаем текущее состояние
            let currentLoaded = data.loaded || 0;
            
            if (!append) {
              portfolioGrid.innerHTML = '';
              data.loaded = 0;
              currentLoaded = 0;
              portfolioGrid.classList.remove('stagger--visible');
            }
            
            // Загружаем ровно 6 элементов каждый раз
            const elementsToLoad = 6;
            const startIndex = currentLoaded;
            const endIndex = Math.min(startIndex + elementsToLoad, images.length);
            
            // Загружаем ровно 6 элементов (или меньше, если изображений не хватает)
            let loadedCount = 0;
            for (let i = startIndex; i < endIndex; i++) {
              // Проверяем, что индекс не выходит за пределы массива
              if (i >= images.length) break;
              
              const image = images[i];
              if (!image) {
                console.warn('Пропущено изображение с индексом:', i);
                continue;
              }
              
              const path = image.url || image.path;
              const imageIndex = i;
              
              const item = document.createElement('div');
              item.className = 'portfolio__item';
              // На мобильных не устанавливаем inline стили
              
              if (append) {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
              }
              
              const img = document.createElement('img');
              img.src = path;
              img.alt = `Дизайн интерьера ${data.name} от студии Анны Пакселевой в Калининграде — фото ${i + 1} реализованного интерьера`;
              img.loading = 'lazy';
              
              // Обработка ошибки загрузки изображения
              img.onerror = function() {
                console.warn('Не удалось загрузить изображение:', path);
                // Показываем placeholder вместо удаления элемента
                img.style.backgroundColor = '#f0f0f0';
                img.style.minHeight = '100px';
              };
              
              item.appendChild(img);
              item.addEventListener('click', () => {
                if (typeof window.openLightbox === 'function') {
                  window.openLightbox(imageIndex);
                }
              });
              
              portfolioGrid.appendChild(item);
              loadedCount++;
            }
            
            // Обновляем счетчик загруженных элементов
            data.loaded = startIndex + loadedCount;
            
            // Проверяем, что все элементы добавлены
            const actualItems = portfolioGrid.querySelectorAll('.portfolio__item');
            if (actualItems.length !== data.loaded) {
              console.warn('Несоответствие: элементов в DOM:', actualItems.length, 'ожидалось:', data.loaded);
            }
            
            // Toggle load more button
            const loadMoreBtn = document.getElementById('loadMorePortfolio');
            if (loadMoreBtn) {
              if (data.loaded >= images.length) {
                loadMoreBtn.style.display = 'none';
              } else {
                loadMoreBtn.style.display = 'block';
              }
            }
            
            // Trigger animation only for initial load
            if (!append) {
              setTimeout(() => {
                portfolioGrid.classList.add('stagger--visible');
              }, 100);
            }
            
            // Сразу после создания элементов убираем inline стили
            removeInlineGridStyles();
            
            // Используем requestAnimationFrame для следующего кадра
            requestAnimationFrame(() => {
              removeInlineGridStyles();
            });
            
            // И еще раз через микро-задержку
            setTimeout(() => {
              removeInlineGridStyles();
            }, 0);
            
            setTimeout(() => {
              removeInlineGridStyles();
            }, 10);
          };
        }
        
        clearInterval(checkAndIntercept);
      } else if (attempts >= maxAttempts) {
        clearInterval(checkAndIntercept);
      }
    }, 50);
  }

  // Наблюдатель за изменениями в портфолио
  function observePortfolioChanges() {
    if (!isMobile()) return;

    const portfolioGrid = document.getElementById('portfolioGrid');
    if (!portfolioGrid) return;

    // Создаем наблюдатель с немедленным удалением inline стилей
    const observer = new MutationObserver((mutations) => {
      let hasNewItems = false;
      
      mutations.forEach((mutation) => {
        if (mutation.addedNodes.length > 0) {
          hasNewItems = true;
        }
      });
      
      if (hasNewItems) {
        // Убираем inline стили сразу
        requestAnimationFrame(() => {
          removeInlineGridStyles();
        });
        
        // И еще раз через микро-задержку
        setTimeout(() => {
          removeInlineGridStyles();
        }, 0);
      }
    });

    observer.observe(portfolioGrid, {
      childList: true,
      subtree: false
    });
  }

  // Инициализация портфолио для мобильных
  function initMobilePortfolio() {
    if (!isMobile()) return;

    const portfolioGrid = document.getElementById('portfolioGrid');
    if (!portfolioGrid) return;

    // Сначала перехватываем создание элементов (до того как они созданы)
    interceptPortfolioCreation();
    
    // Устанавливаем наблюдатель
    observePortfolioChanges();
    
    // Убираем стили у существующих элементов
    removeInlineGridStyles();
    
    // Убираем стили несколько раз с задержками
    setTimeout(() => {
      removeInlineGridStyles();
    }, 50);
    
    setTimeout(() => {
      removeInlineGridStyles();
    }, 150);
    
    setTimeout(() => {
      removeInlineGridStyles();
    }, 300);
  }

  // Оптимизация touch-событий для мобильных
  function optimizeTouchEvents() {
    if (!isMobile()) return;

    // Улучшаем обработку свайпов в lightbox
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) return;

    let touchStartX = 0;
    let touchStartY = 0;
    let touchEndX = 0;
    let touchEndY = 0;

    lightbox.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
      touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    lightbox.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      touchEndY = e.changedTouches[0].screenY;
      handleSwipe();
    }, { passive: true });

    function handleSwipe() {
      const swipeThreshold = 50;
      const diffX = touchStartX - touchEndX;
      const diffY = touchStartY - touchEndY;

      // Проверяем, что это горизонтальный свайп, а не вертикальный
      if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > swipeThreshold) {
        if (diffX > 0) {
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

  // Оптимизация изображений для мобильных
  function optimizeImagesForMobile() {
    if (!isMobile()) return;

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
        rootMargin: '100px' // Начинаем загрузку заранее на мобильных
      });

      images.forEach(img => {
        if (img.dataset.src) {
          imageObserver.observe(img);
        }
      });
    }
  }

  // Слайдер тарифов для мобильных
  function initTariffsSlider() {
    if (!isMobile()) return;

    const tariffsGrid = document.querySelector('.tariffs__grid');
    if (!tariffsGrid) return;

    // Добавляем индикатор прокрутки, если нужно
    let isScrolling = false;
    
    tariffsGrid.addEventListener('scroll', () => {
      if (!isScrolling) {
        window.requestAnimationFrame(() => {
          // Можно добавить индикатор прокрутки
          isScrolling = false;
        });
        isScrolling = true;
      }
    }, { passive: true });

    // Плавная прокрутка к следующей карточке при клике
    const tariffCards = tariffsGrid.querySelectorAll('.tariff-card');
    tariffCards.forEach((card, index) => {
      card.addEventListener('click', () => {
        if (index < tariffCards.length - 1) {
          const nextCard = tariffCards[index + 1];
          const cardRect = nextCard.getBoundingClientRect();
          const gridRect = tariffsGrid.getBoundingClientRect();
          const scrollLeft = tariffsGrid.scrollLeft;
          const targetScroll = scrollLeft + (cardRect.left - gridRect.left) - (gridRect.width / 2) + (cardRect.width / 2);
          
          tariffsGrid.scrollTo({
            left: targetScroll,
            behavior: 'smooth'
          });
        }
      });
    });
  }

  // Оптимизация мобильного меню
  function optimizeMobileMenu() {
    if (!isMobile()) return;

    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuLinks = mobileMenu?.querySelectorAll('.mobile-menu__link');
    
    if (!mobileMenuLinks) return;

    // Закрываем меню при клике на ссылку
    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', () => {
        setTimeout(() => {
          const burger = document.getElementById('burger');
          if (burger) {
            burger.click(); // Закрываем меню
          }
        }, 100);
      });
    });

    // Предотвращаем скролл body при открытом меню
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
          const isOpen = mobileMenu.classList.contains('mobile-menu--open');
          if (isOpen) {
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
          } else {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
          }
        }
      });
    });

    if (mobileMenu) {
      observer.observe(mobileMenu, {
        attributes: true,
        attributeFilter: ['class']
      });
    }
  }

  // Оптимизация форм для мобильных
  function optimizeFormsForMobile() {
    if (!isMobile()) return;

    // Автоматическое изменение типа input для телефонов
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
      input.setAttribute('inputmode', 'numeric');
      input.setAttribute('pattern', '[0-9]*');
    });

    // Предотвращаем zoom при фокусе на input (iOS)
    const inputs = document.querySelectorAll('input, textarea, select');
    const viewport = document.querySelector('meta[name="viewport"]');
    
    if (viewport && /iPhone|iPad|iPod/.test(navigator.userAgent)) {
      inputs.forEach(input => {
        input.addEventListener('focus', () => {
          const fontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
          if (fontSize < 16) {
            viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
          }
        });

        input.addEventListener('blur', () => {
          viewport.setAttribute('content', 'width=device-width, initial-scale=1.0');
        });
      });
    }
  }

  // Оптимизация производительности для мобильных
  function optimizePerformance() {
    if (!isMobile()) return;

    // Отключаем анимации для пользователей с предпочтением reduced-motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      document.documentElement.style.setProperty('--transition-fast', '0s');
      document.documentElement.style.setProperty('--transition-base', '0s');
      document.documentElement.style.setProperty('--transition-slow', '0s');
    }

    // Дебаунс для scroll событий
    let scrollTimeout;
    const originalScrollHandler = window.onscroll;
    
    window.addEventListener('scroll', () => {
      if (scrollTimeout) {
        window.cancelAnimationFrame(scrollTimeout);
      }
      scrollTimeout = window.requestAnimationFrame(() => {
        if (originalScrollHandler) {
          originalScrollHandler();
        }
      });
    }, { passive: true });
  }

  // Инициализация при загрузке
  function init() {
    // Инициализируем портфолио для мобильных
    initMobilePortfolio();
    
    // Убираем стили сразу при инициализации
    removeInlineGridStyles();
    
    // Убираем стили после небольших задержек для надежности
    setTimeout(() => {
      removeInlineGridStyles();
    }, 50);
    
    setTimeout(() => {
      removeInlineGridStyles();
    }, 150);
    
    setTimeout(() => {
      removeInlineGridStyles();
    }, 300);
    
    // После полной загрузки страницы
    if (document.readyState === 'complete') {
      removeInlineGridStyles();
    } else {
      window.addEventListener('load', () => {
        removeInlineGridStyles();
      });
    }
    
    // Другие оптимизации
    optimizeTouchEvents();
    optimizeImagesForMobile();
    initTariffsSlider();
    optimizeMobileMenu();
    optimizeFormsForMobile();
    optimizePerformance();

    // Периодически проверяем и убираем inline стили
    const styleInterval = setInterval(() => {
      if (isMobile()) {
        removeInlineGridStyles();
      } else {
        clearInterval(styleInterval);
      }
    }, 500);

    setTimeout(() => clearInterval(styleInterval), 15000);

    // Переинициализация при изменении размера окна
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        if (isMobile()) {
          initMobilePortfolio();
          removeInlineGridStyles();
          initTariffsSlider();
        }
      }, 250);
    }, { passive: true });
  }

  // Запуск после загрузки DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
