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
  const portfolioData = {
    1: { count: 45, loaded: 0, images: [] },
    2: { count: 74, loaded: 0, images: [] },
    3: { count: 45, loaded: 0, images: [] }
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

  function loadPortfolioImages(project, count = 8, append = false) {
    const data = portfolioData[project];
    if (!data) return;

    if (!append) {
      portfolioGrid.innerHTML = '';
      data.loaded = 0;
      data.images = [];
    }

    const start = data.loaded + 1;
    const end = Math.min(start + count - 1, data.count);
    
    for (let i = start; i <= end; i++) {
      const path = generateImagePath(project, i);
      const imageIndex = data.images.length; // Индекс в массиве загруженных изображений
      data.images.push({ path, num: i });
      
      const item = document.createElement('div');
      item.className = 'portfolio__item';
      item.innerHTML = `<img src="${path}" alt="Дизайн интерьера - проект ${project}, изображение ${i}" loading="lazy">`;
      item.addEventListener('click', () => openLightbox(imageIndex));
      portfolioGrid.appendChild(item);
    }

    data.loaded = end;

    // Toggle load more button
    if (loadMoreBtn) {
      if (data.loaded >= data.count) {
        loadMoreBtn.style.display = 'none';
      } else {
        loadMoreBtn.style.display = '';
      }
    }

    // Trigger animation
    setTimeout(() => {
      portfolioGrid.classList.add('stagger--visible');
    }, 100);
  }

  // Portfolio tabs
  document.querySelectorAll('.portfolio__tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.portfolio__tab').forEach(t => t.classList.remove('portfolio__tab--active'));
      tab.classList.add('portfolio__tab--active');
      
      currentProject = tab.dataset.project;
      portfolioData[currentProject].loaded = 0;
      portfolioGrid.classList.remove('stagger--visible');
      loadPortfolioImages(currentProject);
    });
  });

  // Load more
  loadMoreBtn.addEventListener('click', () => {
    loadPortfolioImages(currentProject, 8, true);
  });

  // Initial load
  loadPortfolioImages('1');

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

})();

