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
  window.portfolioData = {};
  let currentProject = null;
  let currentLightboxIndex = 0;
  let portfolioProjects = [];

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

  // Handle mobile menu links explicitly
  const mobileMenuLinks = mobileMenu.querySelectorAll('.mobile-menu__link');
  mobileMenuLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.stopPropagation(); // Prevent overlay handler from firing
      const href = this.getAttribute('href');
      if (href && href.startsWith('#')) {
        e.preventDefault();
        closeMobileMenu();
        const target = document.querySelector(href);
        if (target) {
          const headerHeight = header.offsetHeight;
          const targetPosition = Math.max(0, target.offsetTop - headerHeight);
          setTimeout(() => {
            window.scrollTo({
              top: targetPosition,
              behavior: 'smooth'
            });
          }, 300);
        }
      }
    }, true); // Use capture phase to handle before other handlers
  });

  // Close on overlay click (but not on links or buttons inside)
  mobileMenu.addEventListener('click', (e) => {
    // Close only if click is directly on the menu container (overlay), not on child elements
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
  function formatPhoneNumber(digits) {
    // Ограничиваем до 10 цифр (номер после +7)
    digits = digits.substring(0, 10);
    
    // Форматируем в +7 (___) ___-__-__
    let formatted = '+7';
    
    if (digits.length > 0) {
      formatted += ' (' + digits.substring(0, 3);
    }
    if (digits.length >= 4) {
      formatted += ') ' + digits.substring(3, 6);
    }
    if (digits.length >= 7) {
      formatted += '-' + digits.substring(6, 8);
    }
    if (digits.length >= 9) {
      formatted += '-' + digits.substring(8, 10);
    }
    
    return formatted;
  }

  function getCursorPosition(newValue) {
    // Простая логика: всегда ставим курсор в конец строки
    // Это гарантирует последовательный ввод цифр
    const minPos = newValue.startsWith('+7') ? 3 : (newValue.startsWith('+') ? 1 : 0);
    return Math.max(minPos, newValue.length);
  }

  document.querySelectorAll('input[type="tel"]').forEach(input => {
    let lastValue = input.value || '';
    let lastInputChar = '';
    
    input.addEventListener('beforeinput', (e) => {
      // Сохраняем последний введенный символ
      if (e.inputType === 'insertText' || e.inputType === 'insertCompositionText') {
        lastInputChar = e.data || '';
      }
    });
    
    input.addEventListener('input', (e) => {
      const target = e.target;
      const oldValue = lastValue || '';
      const inputValue = target.value;
      
      // Определяем, добавляется ли символ или удаляется
      const isAdding = inputValue.length > oldValue.length;
      
      let result = '';
      let phoneDigits = '';
      
      // Если поле пустое, обрабатываем первый символ
      if (!oldValue || oldValue.trim() === '') {
        const firstChar = lastInputChar || inputValue[0] || '';
        
        // Если вбиваю +, добавляй +
        if (firstChar === '+') {
          result = '+';
          phoneDigits = '';
        }
        // Если вбиваю 7, добавляй +7
        else if (firstChar === '7') {
          result = '+7';
          phoneDigits = '';
        }
        // Если вбиваю 8, заменяй 8 на +7
        else if (firstChar === '8') {
          result = '+7';
          phoneDigits = '';
        }
        // Если остальные цифры, добавляй +7 и потом цифру
        else if (/\d/.test(firstChar)) {
          phoneDigits = firstChar;
          result = formatPhoneNumber(phoneDigits);
        }
        // Если ничего не введено
        else {
          result = '';
        }
      } else {
        // Поле не пустое - обрабатываем как обычно
        const digits = inputValue.replace(/\D/g, '');
        
        if (digits.length === 0) {
          // Проверяем, есть ли плюс
          if (inputValue.includes('+')) {
            result = '+';
          } else {
            result = '';
          }
        } else {
          const firstDigit = digits[0];
          
          // Если первый символ 8 или 7, убираем его
          if (firstDigit === '8' || firstDigit === '7') {
            phoneDigits = digits.substring(1);
          } else {
            phoneDigits = digits;
          }
          
          result = formatPhoneNumber(phoneDigits);
        }
      }
      
      // Сохраняем новое значение
      target.value = result;
      lastValue = result;
      lastInputChar = '';
      
      // Корректируем позицию курсора - просто ставим в конец
      if (result) {
        const newCursorPos = getCursorPosition(result);
        target.setSelectionRange(newCursorPos, newCursorPos);
      }
    });

    input.addEventListener('focus', (e) => {
      const target = e.target;
      const value = target.value || '';
      
      // При фокусе не устанавливаем автоматически +7, оставляем поле как есть
      // Если поле пустое, просто оставляем пустым
      if (value && value.trim() !== '') {
        // Форматируем существующее значение, если нужно
        const digits = value.replace(/\D/g, '');
        if (digits.length > 0) {
          const firstDigit = digits[0];
          let phoneDigits = '';
          
          if (firstDigit === '8' || firstDigit === '7') {
            phoneDigits = digits.substring(1);
          } else {
            phoneDigits = digits;
          }
          
          const formatted = formatPhoneNumber(phoneDigits);
          if (formatted !== value) {
            target.value = formatted;
            lastValue = formatted;
          }
        } else if (value === '+') {
          lastValue = '+';
        }
      }
    });

    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const target = e.target;
      const pasted = (e.clipboardData || window.clipboardData).getData('text');
      
      // Обрабатываем вставленный текст
      const digits = pasted.replace(/\D/g, '');
      let result = '';
      
      if (digits.length > 0) {
        const firstDigit = digits[0];
        let phoneDigits = '';
        
        if (firstDigit === '8' || firstDigit === '7') {
          phoneDigits = digits.substring(1);
        } else {
          phoneDigits = digits;
        }
        
        result = formatPhoneNumber(phoneDigits);
      } else if (pasted.includes('+')) {
        result = '+';
      }
      
      target.value = result;
      lastValue = result;
      target.setSelectionRange(result.length, result.length);
    });

    input.addEventListener('keydown', (e) => {
      const target = e.target;
      const cursorPos = target.selectionStart || 0;
      const value = target.value || '';
      
      // Если пользователь пытается удалить +7 или +, предотвращаем это
      if ((e.key === 'Backspace' || e.key === 'Delete')) {
        if (value.startsWith('+7') && cursorPos <= 3) {
          e.preventDefault();
          target.setSelectionRange(3, 3);
        } else if (value.startsWith('+') && !value.startsWith('+7') && cursorPos <= 1) {
          e.preventDefault();
          target.setSelectionRange(1, 1);
        }
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
  
  // Загрузка портфолио из API
  async function loadPortfolioFromAPI() {
    try {
      const response = await fetch('api/portfolio.php');
      if (!response.ok) {
        throw new Error('Ошибка загрузки портфолио');
      }
      portfolioProjects = await response.json();
      
      // Инициализируем portfolioData
      portfolioProjects.forEach(project => {
        window.portfolioData[project.id] = {
          id: project.id,
          name: project.name,
          images: project.images,
          loaded: 0
        };
      });
      
      // Создаём табы динамически
      createPortfolioTabs();
      
      // Загружаем первый проект
      if (portfolioProjects.length > 0) {
        currentProject = String(portfolioProjects[0].id);
        loadPortfolioImages(currentProject, 2, false);
      }
    } catch (error) {
      console.error('Ошибка загрузки портфолио:', error);
      // Fallback на статическое портфолио, если API недоступно
      initFallbackPortfolio();
    }
  }
  
  function scrollTabIntoView(tab) {
    const tabsContainer = document.querySelector('.portfolio__tabs');
    if (!tabsContainer || !tab) return;
    
    const containerRect = tabsContainer.getBoundingClientRect();
    const tabRect = tab.getBoundingClientRect();
    const scrollLeft = tabsContainer.scrollLeft;
    
    if (tabRect.left < containerRect.left) {
      tabsContainer.scrollTo({
        left: scrollLeft + (tabRect.left - containerRect.left) - 16,
        behavior: 'smooth'
      });
    } else if (tabRect.right > containerRect.right) {
      tabsContainer.scrollTo({
        left: scrollLeft + (tabRect.right - containerRect.right) + 16,
        behavior: 'smooth'
      });
    }
  }
  
  function createPortfolioTabs() {
    const tabsContainer = document.querySelector('.portfolio__tabs');
    if (!tabsContainer || portfolioProjects.length === 0) return;
    
    tabsContainer.innerHTML = '';
    
    portfolioProjects.forEach((project, index) => {
      const tab = document.createElement('button');
      tab.className = 'portfolio__tab';
      if (index === 0) {
        tab.classList.add('portfolio__tab--active');
      }
      tab.textContent = project.name;
      tab.dataset.project = project.id;
      
      tab.addEventListener('click', () => {
        document.querySelectorAll('.portfolio__tab').forEach(t => t.classList.remove('portfolio__tab--active'));
        tab.classList.add('portfolio__tab--active');
        currentProject = String(project.id);
        loadPortfolioImages(currentProject, 2, false);
        scrollTabIntoView(tab);
      });
      
      tabsContainer.appendChild(tab);
    });
    
    initPortfolioTabsSlider();
  }
  
  function initPortfolioTabsSlider() {
    const tabsContainer = document.querySelector('.portfolio__tabs');
    if (!tabsContainer) return;
    
    // Прокручиваем активный таб в видимую область при загрузке
    const activeTab = tabsContainer.querySelector('.portfolio__tab--active');
    if (activeTab) {
      setTimeout(() => scrollTabIntoView(activeTab), 100);
    }
  }
  
  function initFallbackPortfolio() {
    // Fallback на старые данные, если API недоступно
    const portfolioFiles = {
      1: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,36,37,38,39,40,41,42,43,44,45],
      2: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74],
      3: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45]
    };
    
    Object.keys(portfolioFiles).forEach(id => {
      window.portfolioData[id] = {
        id: parseInt(id),
        name: `Проект ${id}`,
        images: portfolioFiles[id].map(num => ({
          url: `img/portfolio/${id}/${num}.webp`,
          filename: `${num}.webp`
        })),
        loaded: 0
      };
    });
    
    currentProject = '1';
    loadPortfolioImages(currentProject, 2, false);
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
    const data = window.portfolioData[project];
    if (!data || !portfolioGrid) return;

    if (!append) {
      portfolioGrid.innerHTML = '';
      data.loaded = 0;
      currentPatternIndex = 0;
      portfolioGrid.classList.remove('stagger--visible');
    }

    const images = data.images || [];
    let imagesAdded = 0;
    
    // Загружаем указанное количество паттернов
    for (let p = 0; p < patternsToLoad; p++) {
      const pattern = getNextPattern();
      
      for (let i = 0; i < pattern.length; i++) {
        if (data.loaded >= images.length) break;
        
        const image = images[data.loaded];
        const path = image.url || image.path;
        const imageIndex = data.loaded;
        
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
        img.alt = `Дизайн интерьера ${data.name} от студии Анны Пакселевой в Калининграде — фото ${imageIndex + 1} реализованного интерьера`;
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
  }

  // Load more
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
      if (currentProject) {
        loadPortfolioImages(currentProject, 2, true); // Добавляем 2 паттерна
      }
    });
  }

  // Initial load - загружаем из API
  if (portfolioGrid) {
    loadPortfolioFromAPI();
  }

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
    if (!currentProject) return;
    const data = window.portfolioData[currentProject];
    if (!data || !data.images || !data.images[currentLightboxIndex]) return;
    
    const image = data.images[currentLightboxIndex];
    const path = image.url || image.path;
    lightboxImage.src = path;
    lightboxImage.alt = `Дизайн интерьера ${data.name} от студии Анны Пакселевой в Калининграде — фото ${currentLightboxIndex + 1} реализованного интерьера`;
  }

  function nextImage() {
    if (!currentProject) return;
    const data = window.portfolioData[currentProject];
    if (!data || !data.images || data.images.length === 0) return;
    currentLightboxIndex = (currentLightboxIndex + 1) % data.images.length;
    updateLightboxImage();
  }

  function prevImage() {
    if (!currentProject) return;
    const data = window.portfolioData[currentProject];
    if (!data || !data.images || data.images.length === 0) return;
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
  document.querySelectorAll('a[href^="#"]:not(.mobile-menu__link)').forEach(anchor => {
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
      
      if (!href || href === '#!') return;
      
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
      } else {
        // If target not found, just close menu and allow default behavior
        if (mobileMenu.classList.contains('mobile-menu--open')) {
          closeMobileMenu();
        }
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

