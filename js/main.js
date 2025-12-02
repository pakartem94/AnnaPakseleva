/**
 * Anna Pakseleva Design Studio
 * Main JavaScript
 */

(function() {
  'use strict';

  // ===========================================
  // DOM Elements
  // ===========================================
  const header = document.getElementById('header');
  const burger = document.getElementById('burger');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileMenuClose = document.getElementById('mobileMenuClose');
  const modal = document.getElementById('modal');
  const modalTitle = document.getElementById('modalTitle');
  const modalForm = document.getElementById('modalForm');
  const modalFormType = document.getElementById('modalFormType');
  const modalTariff = document.getElementById('modalTariff');
  const modalTariffGroup = document.getElementById('modalTariffGroup');
  const modalTariffDisplay = document.getElementById('modalTariffDisplay');
  const modalSuccess = document.getElementById('modalSuccess');
  const mainForm = document.getElementById('mainForm');
  const formSuccess = document.getElementById('formSuccess');
  const lightbox = document.getElementById('lightbox');
  const lightboxImage = document.getElementById('lightboxImage');
  const portfolioGrid = document.getElementById('portfolioGrid');
  const loadMoreBtn = document.getElementById('loadMorePortfolio');

  // ===========================================
  // Portfolio Data
  // ===========================================
  // Реальные номера файлов в каждой папке (без пропусков)
  const portfolioFiles = {
    1: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,39,40,41,42,43,44,45],
    2: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74],
    3: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45]
  };
  const portfolioData = {
    1: { files: portfolioFiles[1], loaded: 0, images: [] },
    2: { files: portfolioFiles[2], loaded: 0, images: [] },
    3: { files: portfolioFiles[3], loaded: 0, images: [] }
  };
  let currentProject = '1';
  let currentLightboxIndex = 0;

  // ===========================================
  // Header Scroll Effect
  // ===========================================
  function handleScroll() {
    if (window.scrollY > 50) {
      header.classList.add('header--scrolled');
    } else {
      header.classList.remove('header--scrolled');
    }
  }

  window.addEventListener('scroll', handleScroll);
  handleScroll();

  // ===========================================
  // Mobile Menu
  // ===========================================
  function openMobileMenu() {
    mobileMenu.classList.add('mobile-menu--open');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileMenu() {
    mobileMenu.classList.remove('mobile-menu--open');
    document.body.style.overflow = '';
  }

  burger.addEventListener('click', openMobileMenu);
  mobileMenuClose.addEventListener('click', closeMobileMenu);

  // Close mobile menu on link click (handled in smooth scroll)
  // Also close on overlay click
  mobileMenu.addEventListener('click', (e) => {
    if (e.target === mobileMenu) {
      closeMobileMenu();
    }
  });
  
  // Close on ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && mobileMenu.classList.contains('mobile-menu--open')) {
      closeMobileMenu();
    }
  });

  // ===========================================
  // Modal
  // ===========================================
  function openModal(type = 'consultation', tariff = '') {
    modal.classList.add('modal--open');
    document.body.style.overflow = 'hidden';
    
    // Reset form
    modalForm.reset();
    modalForm.style.display = '';
    modalSuccess.classList.remove('form__success--show');

    if (type === 'tariff' && tariff) {
      modalTitle.textContent = 'Заявка на тариф';
      modalFormType.value = 'tariff';
      modalTariff.value = tariff;
      modalTariffDisplay.value = tariff;
      modalTariffGroup.style.display = '';
    } else {
      modalTitle.textContent = 'Получить консультацию';
      modalFormType.value = 'consultation';
      modalTariff.value = '';
      modalTariffGroup.style.display = 'none';
    }
  }

  function closeModal() {
    modal.classList.remove('modal--open');
    document.body.style.overflow = '';
  }

  // Modal triggers
  document.querySelectorAll('[data-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.modal;
      const tariff = btn.dataset.tariff || '';
      openModal(type, tariff);
    });
  });

  // Close modal
  document.querySelectorAll('[data-modal-close]').forEach(el => {
    el.addEventListener('click', closeModal);
  });

  // Close on ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModal();
      closeLightbox();
    }
  });

  // ===========================================
  // Phone Mask
  // ===========================================
  function formatPhone(value) {
    const digits = value.replace(/\D/g, '');
    let formatted = '+7';
    
    if (digits.length > 1) {
      formatted += ' (' + digits.substring(1, 4);
    }
    if (digits.length >= 4) {
      formatted += ') ' + digits.substring(4, 7);
    }
    if (digits.length >= 7) {
      formatted += '-' + digits.substring(7, 9);
    }
    if (digits.length >= 9) {
      formatted += '-' + digits.substring(9, 11);
    }
    
    return formatted;
  }

  document.querySelectorAll('input[type="tel"]').forEach(input => {
    input.addEventListener('input', (e) => {
      const start = e.target.selectionStart;
      const before = e.target.value.length;
      e.target.value = formatPhone(e.target.value);
      const after = e.target.value.length;
      e.target.setSelectionRange(start + after - before, start + after - before);
    });

    input.addEventListener('focus', (e) => {
      if (!e.target.value) {
        e.target.value = '+7';
      }
    });
  });

  // ===========================================
  // Form Submission
  // ===========================================
  async function submitForm(form, successEl) {
    const formData = new FormData(form);
    
    // Check honeypot
    if (formData.get('website')) {
      return;
    }

    // Add UTM parameters
    const urlParams = new URLSearchParams(window.location.search);
    formData.append('utm_source', urlParams.get('utm_source') || '');
    formData.append('utm_medium', urlParams.get('utm_medium') || '');
    formData.append('utm_campaign', urlParams.get('utm_campaign') || '');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData
      });

      const result = await response.json();

      if (response.ok && result.success) {
        form.style.display = 'none';
        successEl.classList.add('form__success--show');
        
        // Analytics event
        if (typeof ym !== 'undefined' && typeof window.YANDEX_METRIKA_ID !== 'undefined') {
          ym(window.YANDEX_METRIKA_ID, 'reachGoal', 'form_submit');
        }
      } else {
        const errorMsg = result.error || 'Произошла ошибка при отправке формы';
        alert(errorMsg);
      }
    } catch (error) {
      console.error('Form submission error:', error);
      alert('Произошла ошибка. Попробуйте позже или свяжитесь с нами напрямую.');
    }
  }

  mainForm.addEventListener('submit', (e) => {
    e.preventDefault();
    submitForm(mainForm, formSuccess);
  });

  modalForm.addEventListener('submit', (e) => {
    e.preventDefault();
    submitForm(modalForm, modalSuccess);
  });

  // ===========================================
  // FAQ Accordion
  // ===========================================
  document.querySelectorAll('.faq-item__question').forEach(question => {
    question.addEventListener('click', () => {
      const item = question.closest('.faq-item');
      const isOpen = item.classList.contains('faq-item--open');
      
      // Close all
      document.querySelectorAll('.faq-item').forEach(i => {
        i.classList.remove('faq-item--open');
      });
      
      // Toggle current
      if (!isOpen) {
        item.classList.add('faq-item--open');
      }
    });
  });

  // ===========================================
  // Portfolio
  // ===========================================
  function generateImagePath(project, num) {
    return `img/portfolio/${project}/${num}.webp`;
  }

  // Паттерны блоков для сетки 6 колонок
  // Каждый паттерн - это набор элементов, которые вместе образуют прямоугольник
  const gridPatterns = [
    // Паттерн 1: Большое вертикальное (2x4) + 4 маленьких (2x2 каждое)
    [
      { col: 2, row: 4 }, // большое вертикальное
      { col: 2, row: 2 }, // маленькое
      { col: 2, row: 2 }, // маленькое
      { col: 2, row: 2 }, // маленькое
      { col: 2, row: 2 }, // маленькое
    ],
    // Паттерн 2: Широкое горизонтальное (4x2) + 2 вертикальных (2x4)
    [
      { col: 4, row: 2 }, // широкое горизонтальное
      { col: 2, row: 4 }, // вертикальное
      { col: 2, row: 2 }, // маленькое
      { col: 2, row: 2 }, // маленькое
    ],
    // Паттерн 3: 3 средних (2x3 каждое) 
    [
      { col: 2, row: 3 },
      { col: 2, row: 3 },
      { col: 2, row: 3 },
    ],
    // Паттерн 4: Большое (3x3) + 3 маленьких
    [
      { col: 3, row: 3 },
      { col: 3, row: 3 },
      { col: 3, row: 3 },
      { col: 3, row: 3 },
    ],
    // Паттерн 5: 6 квадратных (2x2)
    [
      { col: 2, row: 2 },
      { col: 2, row: 2 },
      { col: 2, row: 2 },
      { col: 2, row: 2 },
      { col: 2, row: 2 },
      { col: 2, row: 2 },
    ],
  ];

  let currentPatternIndex = 0;

  function getNextPattern() {
    const pattern = gridPatterns[currentPatternIndex];
    currentPatternIndex = (currentPatternIndex + 1) % gridPatterns.length;
    return pattern;
  }

  function loadPortfolioImages(project, patternsToLoad = 2, append = false) {
    const data = portfolioData[project];
    if (!data || !portfolioGrid) return;

    if (!append) {
      portfolioGrid.innerHTML = '';
      data.loaded = 0;
      data.images = [];
      currentPatternIndex = 0;
      portfolioGrid.classList.remove('stagger--visible');
    }

    const files = data.files;
    let imagesAdded = 0;
    
    // Загружаем указанное количество паттернов
    for (let p = 0; p < patternsToLoad; p++) {
      const pattern = getNextPattern();
      
      for (let i = 0; i < pattern.length; i++) {
        if (data.loaded >= files.length) break;
        
        const fileNum = files[data.loaded];
        const path = generateImagePath(project, fileNum);
        const imageIndex = data.images.length;
        data.images.push({ path, num: fileNum });
        
        const item = document.createElement('div');
        item.className = 'portfolio__item';
        item.style.gridColumn = `span ${pattern[i].col}`;
        item.style.gridRow = `span ${pattern[i].row}`;
        
        if (append) {
          item.style.opacity = '1';
          item.style.transform = 'translateY(0)';
        }
        
        const img = document.createElement('img');
        img.src = path;
        img.alt = `Дизайн интерьера - проект ${project}, изображение ${fileNum}`;
        img.loading = 'lazy';
        
        item.appendChild(img);
        item.addEventListener('click', () => openLightbox(imageIndex));
        portfolioGrid.appendChild(item);
        
        data.loaded++;
        imagesAdded++;
      }
    }

    // Toggle load more button
    if (loadMoreBtn) {
      if (data.loaded >= files.length) {
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
  }

  // Portfolio tabs
  document.querySelectorAll('.portfolio__tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.portfolio__tab').forEach(t => t.classList.remove('portfolio__tab--active'));
      tab.classList.add('portfolio__tab--active');
      
      currentProject = tab.dataset.project;
      loadPortfolioImages(currentProject, 2, false); // Загружаем 2 паттерна
    });
  });

  // Load more
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      loadPortfolioImages(currentProject, 2, true); // Добавляем 2 паттерна
    });
  }

  // Initial load
  loadPortfolioImages('1', 2);

  // ===========================================
  // Lightbox
  // ===========================================
  function openLightbox(index) {
    currentLightboxIndex = index;
    updateLightboxImage();
    lightbox.classList.add('lightbox--open');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lightbox.classList.remove('lightbox--open');
    document.body.style.overflow = '';
  }

  function updateLightboxImage() {
    const data = portfolioData[currentProject];
    if (!data.images || !data.images[currentLightboxIndex]) return;
    
    const image = data.images[currentLightboxIndex];
    lightboxImage.src = image.path;
    lightboxImage.alt = `Дизайн интерьера - проект ${currentProject}, изображение ${image.num}`;
  }

  function nextImage() {
    const data = portfolioData[currentProject];
    if (!data.images || data.images.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex + 1) % data.images.length;
    updateLightboxImage();
  }

  function prevImage() {
    const data = portfolioData[currentProject];
    if (!data.images || data.images.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex - 1 + data.images.length) % data.images.length;
    updateLightboxImage();
  }

  lightbox.querySelector('.lightbox__close').addEventListener('click', closeLightbox);
  lightbox.querySelector('.lightbox__nav--next').addEventListener('click', nextImage);
  lightbox.querySelector('.lightbox__nav--prev').addEventListener('click', prevImage);

  // Close on overlay click
  lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) {
      closeLightbox();
    }
  });

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('lightbox--open')) return;
    
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
  });

  // ===========================================
  // Scroll Animations
  // ===========================================
  const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in--visible');
        if (entry.target.classList.contains('stagger')) {
          entry.target.classList.add('stagger--visible');
        }
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.fade-in, .stagger').forEach(el => {
    observer.observe(el);
  });

  // ===========================================
  // Smooth Scroll
  // ===========================================
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      
      // Handle empty hash (scroll to top)
      if (href === '#') {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (mobileMenu.classList.contains('mobile-menu--open')) {
          closeMobileMenu();
        }
        return;
      }
      
      if (!href) return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        // Close mobile menu if open
        const menuWasOpen = mobileMenu.classList.contains('mobile-menu--open');
        if (menuWasOpen) {
          closeMobileMenu();
        }
        
        const headerHeight = header.offsetHeight;
        const targetPosition = Math.max(0, target.offsetTop - headerHeight);
        
        // Small delay to ensure menu is closed before scroll
        setTimeout(() => {
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }, menuWasOpen ? 300 : 0);
      }
    });
  });

  // ===========================================
  // Analytics Events
  // ===========================================
  document.querySelectorAll('[data-analytics]').forEach(el => {
    el.addEventListener('click', () => {
      const event = el.dataset.analytics;
      
      // Yandex Metrika
      if (typeof ym !== 'undefined' && typeof window.YANDEX_METRIKA_ID !== 'undefined') {
        ym(window.YANDEX_METRIKA_ID, 'reachGoal', event);
      }
    });
  });

  // Phone click tracking
  document.querySelectorAll('a[href^="tel:"]').forEach(link => {
    link.addEventListener('click', () => {
      if (typeof ym !== 'undefined' && typeof window.YANDEX_METRIKA_ID !== 'undefined') {
        ym(window.YANDEX_METRIKA_ID, 'reachGoal', 'phone_click');
      }
    });
  });

  // ===========================================
  // Reviews Load More
  // ===========================================
  const loadMoreReviewsBtn = document.getElementById('loadMoreReviews');
  const hiddenReviews = document.querySelectorAll('.review-card--hidden');
  
  if (loadMoreReviewsBtn && hiddenReviews.length > 0) {
    let reviewsShown = 0;
    const reviewsToShow = 4;
    
    loadMoreReviewsBtn.addEventListener('click', () => {
      const reviewsToDisplay = Array.from(hiddenReviews).slice(reviewsShown, reviewsShown + reviewsToShow);
      
      reviewsToDisplay.forEach(review => {
        review.classList.remove('review-card--hidden');
        review.style.opacity = '0';
        review.style.transform = 'translateY(20px)';
        
        // Анимация появления
        setTimeout(() => {
          review.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
          review.style.opacity = '1';
          review.style.transform = 'translateY(0)';
        }, 10);
      });
      
      reviewsShown += reviewsToDisplay.length;
      
      // Скрыть кнопку, если все отзывы показаны
      if (reviewsShown >= hiddenReviews.length) {
        loadMoreReviewsBtn.style.display = 'none';
      }
    });
  } else if (loadMoreReviewsBtn && hiddenReviews.length === 0) {
    // Скрыть кнопку, если нет скрытых отзывов
    loadMoreReviewsBtn.style.display = 'none';
  }

})();

