<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Студия дизайна интерьеров Анны Пакселевой в Калининграде. Создаём продуманные, эстетичные и функциональные интерьеры с инженерной точностью и сопровождением ремонта.">
  <title>Дизайн интерьера в Калининграде — Студия Анны Пакселевой</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="img/logo.PNG">
</head>
<body>
  <!-- Header -->
  <header class="header" id="header">
    <div class="container">
      <div class="header__inner">
        <a href="#hero" class="header__logo">
          <span class="header__logo-text">Анна Пакселева</span>
        </a>
        
        <nav class="nav">
          <ul class="nav__list">
            <li><a href="#services" class="nav__link">Услуги</a></li>
            <li><a href="#tariffs" class="nav__link">Тарифы</a></li>
            <li><a href="#portfolio" class="nav__link">Портфолио</a></li>
            <li><a href="#about" class="nav__link">О студии</a></li>
            <li><a href="#faq" class="nav__link">FAQ</a></li>
            <li><a href="#contact" class="nav__link">Контакты</a></li>
          </ul>
          <div class="nav__cta">
            <button class="btn btn--primary btn--sm" data-modal="consultation">Консультация</button>
          </div>
        </nav>
        
        <button class="burger" id="burger" aria-label="Меню">
          <span class="burger__line"></span>
          <span class="burger__line"></span>
          <span class="burger__line"></span>
        </button>
      </div>
    </div>
  </header>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <button class="mobile-menu__close" id="mobileMenuClose" aria-label="Закрыть меню">
      <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>
    <ul class="mobile-menu__list">
      <li><a href="#services" class="mobile-menu__link">Услуги</a></li>
      <li><a href="#tariffs" class="mobile-menu__link">Тарифы</a></li>
      <li><a href="#portfolio" class="mobile-menu__link">Портфолио</a></li>
      <li><a href="#about" class="mobile-menu__link">О студии</a></li>
      <li><a href="#faq" class="mobile-menu__link">FAQ</a></li>
      <li><a href="#contact" class="mobile-menu__link">Контакты</a></li>
    </ul>
    <div class="mobile-menu__cta">
      <button class="btn btn--primary btn--full" data-modal="consultation">Получить консультацию</button>
    </div>
  </div>

  <main>
    <!-- Hero Section -->
    <section class="hero" id="hero">
      <div class="hero__bg">
        <div class="hero__bg-pattern"></div>
      </div>
      <div class="container">
        <div class="hero__inner">
          <div class="hero__content">
            <div class="hero__badge">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
              15 проектов в год
            </div>
            <h1 class="hero__title">
              Дизайн интерьера <span>под ключ</span> в Калининграде
            </h1>
            <p class="hero__subtitle">
              Создаём продуманные, эстетичные и функциональные интерьеры с инженерной точностью и сопровождением ремонта — чтобы после ремонта вам не хотелось ничего переделывать.
            </p>
            <div class="hero__cta">
              <button class="btn btn--primary" data-modal="consultation">Получить консультацию</button>
              <a href="#portfolio" class="btn btn--secondary">Посмотреть проекты</a>
            </div>
          </div>
          <div class="hero__image">
            <div class="hero__image-wrapper">
              <img src="img/photo_2025-12-02 16.15.13.webp" alt="Дизайн интерьера от студии Анны Пакселевой" loading="eager">
            </div>
          </div>
        </div>
        <ul class="hero__features">
          <li class="hero__feature">
            <span class="hero__feature-icon">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            Команда специалистов под контролем руководителя
          </li>
          <li class="hero__feature">
            <span class="hero__feature-icon">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            3D-тур + фотореалистичные визуализации
          </li>
          <li class="hero__feature">
            <span class="hero__feature-icon">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            Рабочие чертежи для строителей
          </li>
          <li class="hero__feature">
            <span class="hero__feature-icon">
              <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
            Авторское сопровождение при реализации
          </li>
        </ul>
      </div>
    </section>

    <!-- Services Section -->
    <section class="section" id="services">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Что вы получаете</h2>
          <p class="section__subtitle">Полный цикл работ от первого брифа до финального декора</p>
        </header>
        
        <div class="services__grid stagger">
          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h3 class="service-card__title">Бриф и ТЗ</h3>
            <p class="service-card__text">Глубокое изучение вашего образа жизни, привычек и пожеланий. Формирование детального технического задания.</p>
          </div>
          
          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            </div>
            <h3 class="service-card__title">Планировочные решения</h3>
            <p class="service-card__text">2–3 варианта планировок с детальной проработкой функциональных зон и эргономики пространства.</p>
          </div>
          
          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            </div>
            <h3 class="service-card__title">3D-визуализации</h3>
            <p class="service-card__text">Фотореалистичные изображения вашего будущего интерьера с точной передачей атмосферы, стиля и материалов.</p>
          </div>
          
          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h3 class="service-card__title">Комплект чертежей</h3>
            <p class="service-card__text">Полная рабочая документация для строителей: планы, разрезы, узлы, схемы освещения и электрики.</p>
          </div>
          
          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            </div>
            <h3 class="service-card__title">Подбор материалов</h3>
            <p class="service-card__text">Оптимальные чистовые материалы, сантехника и освещение с учётом вашего бюджета.</p>
          </div>
          
          <div class="service-card">
            <div class="service-card__icon">
              <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3 class="service-card__title">Авторское сопровождение</h3>
            <p class="service-card__text">Контроль реализации проекта, консультации строителей и корректировки в процессе ремонта.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stages Section -->
    <section class="section stages" id="stages">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Этапы работы</h2>
          <p class="section__subtitle">Прозрачный процесс от заявки до сдачи проекта</p>
        </header>
        
        <div class="stages__timeline">
          <div class="stage">
            <span class="stage__number">1</span>
            <div class="stage__content">
              <h3 class="stage__title">Заявка</h3>
              <p class="stage__text">Вы оставляете заявку, мы связываемся для первичной консультации</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">2</span>
            <div class="stage__content">
              <h3 class="stage__title">Бриф</h3>
              <p class="stage__text">Детальное изучение ваших пожеланий, образа жизни и формирование ТЗ</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">3</span>
            <div class="stage__content">
              <h3 class="stage__title">Замер</h3>
              <p class="stage__text">Выезд на объект для точных обмеров и фиксации особенностей помещения</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">4</span>
            <div class="stage__content">
              <h3 class="stage__title">Планировки</h3>
              <p class="stage__text">Разработка 2-3 вариантов планировочных решений на выбор</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">5</span>
            <div class="stage__content">
              <h3 class="stage__title">Визуализации</h3>
              <p class="stage__text">Создание фотореалистичных 3D-изображений и виртуального тура</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">6</span>
            <div class="stage__content">
              <h3 class="stage__title">Чертежи</h3>
              <p class="stage__text">Подготовка полного комплекта рабочей документации для строителей</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">7</span>
            <div class="stage__content">
              <h3 class="stage__title">Сопровождение</h3>
              <p class="stage__text">Контроль реализации, консультации и корректировки в процессе ремонта</p>
            </div>
          </div>
          
          <div class="stage">
            <span class="stage__number">8</span>
            <div class="stage__content">
              <h3 class="stage__title">Финал</h3>
              <p class="stage__text">Сдача готового проекта и подбор финального декора</p>
            </div>
          </div>
        </div>
        
        <div class="text-center" style="margin-top: var(--space-xl);">
          <button class="btn btn--primary btn--lg" data-modal="consultation">Обсудить мой интерьер</button>
        </div>
      </div>
    </section>

    <!-- Tariffs Section -->
    <section class="section section--alt" id="tariffs">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Тарифы</h2>
          <p class="section__subtitle">Выберите подходящий формат сотрудничества</p>
        </header>
        
        <div class="tariffs__grid stagger">
          <!-- Стандарт -->
          <div class="tariff-card">
            <h3 class="tariff-card__name">Стандарт</h3>
            <div class="tariff-card__price">
              <span class="tariff-card__price-value">3 000 ₽</span>
              <span class="tariff-card__price-unit">/ м²</span>
            </div>
            <p class="tariff-card__description">Полный набор документов для реализации качественного, продуманного ремонта.</p>
            <div class="tariff-card__features">
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Детальный замер и обмер помещения</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>3 варианта планировочных решений</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Фотореалистичные визуализации</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Виртуальный 3D-тур</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Полный комплект строительных чертежей</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Подбор чистовых материалов и сантехники</span>
              </div>
            </div>
            <div class="tariff-card__cta">
              <button class="btn btn--outline" data-modal="tariff" data-tariff="Стандарт">Выбрать тариф</button>
            </div>
          </div>
          
          <!-- Расширенный -->
          <div class="tariff-card tariff-card--featured">
            <span class="tariff-card__badge">Популярный</span>
            <h3 class="tariff-card__name">Расширенный</h3>
            <div class="tariff-card__price">
              <span class="tariff-card__price-value">4 000 ₽</span>
              <span class="tariff-card__price-unit">/ м²</span>
            </div>
            <p class="tariff-card__description">Для тех, кто хочет максимально не отвлекаться на вопросы по реализации.</p>
            <div class="tariff-card__features">
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Всё из тарифа «Стандарт»</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Комплектация мебелью под стиль и бюджет</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Подбор встроенных решений</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>ТЗ для мебели индивидуального изготовления</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Чертежи и схемы для мебельщиков</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Помощь в подборе декора и текстиля</span>
              </div>
            </div>
            <div class="tariff-card__cta">
              <button class="btn btn--primary" data-modal="tariff" data-tariff="Расширенный">Выбрать тариф</button>
            </div>
          </div>
          
          <!-- Полный контроль -->
          <div class="tariff-card tariff-card--premium">
            <h3 class="tariff-card__name">Полный контроль</h3>
            <div class="tariff-card__price">
              <span class="tariff-card__price-value">5 000 ₽</span>
              <span class="tariff-card__price-unit">/ м²</span>
            </div>
            <p class="tariff-card__description">Для тех, кто хочет быть уверенным в качестве реализации при любой бригаде.</p>
            <div class="tariff-card__features">
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Всё из тарифа «Расширенный»</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Авторское сопровождение проекта</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Присутствие в чате с отделочниками</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Контроль соответствия проекту</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Корректировки по материалам и узлам</span>
              </div>
              <div class="tariff-card__feature">
                <span class="tariff-card__feature-icon">
                  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span>Внесение изменений по необходимости</span>
              </div>
            </div>
            <div class="tariff-card__cta">
              <button class="btn btn--gold" data-modal="tariff" data-tariff="Полный контроль">Выбрать тариф</button>
            </div>
          </div>
        </div>
        
        <p class="text-center text-muted" style="margin-top: var(--space-xl); font-size: var(--font-size-sm);">
          <strong>Важно:</strong> при ремонте партнёрской строительной компанией авторское сопровождение включено в каждый тариф бесплатно.
        </p>
      </div>
    </section>

    <!-- Portfolio Section -->
    <section class="section portfolio" id="portfolio">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Портфолио</h2>
          <p class="section__subtitle">Реализованные проекты студии</p>
        </header>
        
        <div class="portfolio__tabs">
          <button class="portfolio__tab portfolio__tab--active" data-project="1">Проект 1</button>
          <button class="portfolio__tab" data-project="2">Проект 2</button>
          <button class="portfolio__tab" data-project="3">Проект 3</button>
        </div>
        
        <div class="portfolio__grid stagger" id="portfolioGrid">
          <!-- Images loaded via JS -->
        </div>
        
        <div class="portfolio__more">
          <button class="btn btn--outline" id="loadMorePortfolio">Показать ещё</button>
        </div>
      </div>
    </section>

    <!-- Why Us Section -->
    <section class="section section--dark" id="why-us">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Почему с нами надёжно</h2>
          <p class="section__subtitle">Преимущества работы со студией Анны Пакселевой</p>
        </header>
        
        <div class="why-us__grid stagger">
          <div class="why-card">
            <div class="why-card__icon">
              <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3 class="why-card__title">Реализация пожеланий</h3>
            <p class="why-card__text">Каждый проект создаётся только после глубокого понимания вашего образа жизни и привычек.</p>
          </div>
          
          <div class="why-card">
            <div class="why-card__icon">
              <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <h3 class="why-card__title">Детальная документация</h3>
            <p class="why-card__text">Грамотная рабочая документация, понятная любой бригаде — без вопросов и переделок.</p>
          </div>
          
          <div class="why-card">
            <div class="why-card__icon">
              <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="why-card__title">Экономия без потерь</h3>
            <p class="why-card__text">Помогаем экономить без потери стиля. Знаем, где можно сэкономить, а на чём нельзя.</p>
          </div>
          
          <div class="why-card">
            <div class="why-card__icon">
              <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <h3 class="why-card__title">Бонусы к проекту</h3>
            <p class="why-card__text">3D-тур, бриф и ТЗ в подарок. Бесплатное сопровождение при работе с партнёрской бригадой.</p>
          </div>
          
          <div class="why-card">
            <div class="why-card__icon">
              <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3 class="why-card__title">Благодарные клиенты</h3>
            <p class="why-card__text">Большое количество выполненных проектов с искренней благодарностью от заказчиков.</p>
          </div>
          
          <div class="why-card">
            <div class="why-card__icon">
              <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="why-card__title">Поддержка на всех этапах</h3>
            <p class="why-card__text">Вы не остаётесь одни — от первого брифа до финального декора мы рядом.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pain Points Section -->
    <section class="section" id="pains">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Решаем ваши страхи</h2>
          <p class="section__subtitle">Понимаем ваши переживания и знаем, как с ними справиться</p>
        </header>
        
        <div class="pains__grid stagger">
          <div class="pain-card">
            <div class="pain-card__header">
              <div class="pain-card__icon">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M16 16s-1.5-2-4-2-4 2-4 2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
              </div>
              <p class="pain-card__pain">Страх, что дизайнер не услышит и сделает «не про нас»</p>
            </div>
            <div class="pain-card__body">
              <div class="pain-card__solution-label">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Решение
              </div>
              <p class="pain-card__solution">Я, как руководитель, создаю проект только после глубокого понимания вашего образа жизни. Первый этап — детальный бриф, анализ привычек, пожеланий и будущих сценариев использования пространства. Вы видите техническое задание ещё до начала работы.</p>
            </div>
          </div>
          
          <div class="pain-card">
            <div class="pain-card__header">
              <div class="pain-card__icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              </div>
              <p class="pain-card__pain">Красиво на картинке, но невозможно реализовать</p>
            </div>
            <div class="pain-card__body">
              <div class="pain-card__solution-label">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Решение
              </div>
              <p class="pain-card__solution">Каждое решение проверяется с точки зрения строительства. Инженерный подход + опыт в промышленном строительстве позволяют создавать визуализации, которые совпадают с реальным результатом. Полный комплект чертежей понятен любой бригаде.</p>
            </div>
          </div>
          
          <div class="pain-card">
            <div class="pain-card__header">
              <div class="pain-card__icon">
                <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              </div>
              <p class="pain-card__pain">Страх перед ошибками и хаосом в ремонте</p>
            </div>
            <div class="pain-card__body">
              <div class="pain-card__solution-label">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Решение
              </div>
              <p class="pain-card__solution">Вы не остаётесь одни — мы сопровождаем весь процесс. Мы на связи в общем чате с вашей бригадой, отвечаем на вопросы по реализации, вносим корректировки и контролируем соответствие проекту.</p>
            </div>
          </div>
          
          <div class="pain-card">
            <div class="pain-card__header">
              <div class="pain-card__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <p class="pain-card__pain">Страх переплатить и уйти в бесконечные расходы</p>
            </div>
            <div class="pain-card__body">
              <div class="pain-card__solution-label">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Решение
              </div>
              <p class="pain-card__solution">Мы помогаем экономить без потери стиля. У руководителя студии есть опыт и понимание стоимости материалов и работ. Она сразу говорит, где можно сэкономить, а на чём нельзя — ваш бюджет всегда под контролем.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Gifts Section -->
    <section class="section gifts" id="gifts">
      <div class="container">
        <div class="gifts__inner">
          <div class="gifts__content fade-in">
            <h2>Подарки к каждому проекту</h2>
            <p class="gifts__subtitle">Приятные бонусы для наших клиентов</p>
            <ul class="gifts__list">
              <li class="gift-item">
                <span class="gift-item__icon">
                  <svg viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </span>
                <span class="gift-item__text">Интерактивный 3D-тур по вашему будущему интерьеру</span>
              </li>
              <li class="gift-item">
                <span class="gift-item__icon">
                  <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </span>
                <span class="gift-item__text">Детальный бриф и техническое задание в подарок</span>
              </li>
              <li class="gift-item">
                <span class="gift-item__icon">
                  <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <span class="gift-item__text">Бесплатное сопровождение при работе с партнёрской бригадой</span>
              </li>
            </ul>
          </div>
          <div class="gifts__image fade-in">
            <div class="gifts__image-wrapper">
              <img src="img/photo_2025-12-02 16.15.15.webp" alt="Интерьер от студии Анны Пакселевой" loading="lazy">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="section" id="about">
      <div class="container">
        <div class="about__inner">
          <div class="about__image fade-in">
            <div class="about__decoration"></div>
            <div class="about__image-wrapper">
              <img src="img/photo_2025-12-02 16.15.17.webp" alt="Анна Пакселева — руководитель студии дизайна" loading="lazy">
            </div>
          </div>
          <div class="about__content fade-in">
            <h2>О руководителе студии</h2>
            <p class="about__name">Анна Пакселева</p>
            <p class="about__text">
              Меня зовут Анна Пакселева, я основатель студии интерьерного дизайна. Главное в моей работе — не просто создать красивый интерьер, а сделать его реализуемым, удобным и действительно подходящим людям, которые будут в нём жить.
            </p>
            <p class="about__text">
              У меня инженерно-строительное образование и опыт работы на стройках, поэтому я понимаю, как решения работают в реальности. Каждый проект я начинаю с логики и функциональности, а уже затем перехожу к эстетике.
            </p>
            <p class="about__text">
              Много внимания уделяю пониманию клиента: образ жизни, привычки, комфорт, особенности семьи. Для меня важно услышать человека полностью — чтобы интерьер стал их домом, а не просто набором модных решений.
            </p>
            
            <div class="about__highlights">
              <div class="about__highlight">
                <span class="about__highlight-icon">
                  <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </span>
                <span class="about__highlight-text">Инженерное образование</span>
              </div>
              <div class="about__highlight">
                <span class="about__highlight-icon">
                  <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </span>
                <span class="about__highlight-text">Опыт на стройках</span>
              </div>
              <div class="about__highlight">
                <span class="about__highlight-icon">
                  <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </span>
                <span class="about__highlight-text">Внимание к деталям</span>
              </div>
              <div class="about__highlight">
                <span class="about__highlight-icon">
                  <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </span>
                <span class="about__highlight-text">Индивидуальный подход</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Reviews Section -->
    <section class="section reviews" id="reviews">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Отзывы клиентов</h2>
          <p class="section__subtitle">Что говорят о работе со студией</p>
        </header>
        
        <div class="reviews__placeholder fade-in">
          <div class="reviews__placeholder-icon">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <p class="reviews__placeholder-text">Раздел отзывов скоро будет заполнен</p>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="section section--alt" id="faq">
      <div class="container">
        <header class="section__header fade-in">
          <h2 class="section__title">Часто задаваемые вопросы</h2>
          <p class="section__subtitle">Ответы на популярные вопросы о работе студии</p>
        </header>
        
        <div class="faq__list">
          <div class="faq-item">
            <button class="faq-item__question">
              Вы действительно учитываете наши пожелания, или проект будет «на вкус дизайнера»?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                Да, моя задача — создать интерьер, отражающий именно вашу жизнь и привычки. Перед началом работы я собираю подробное ТЗ, изучаю ваш образ жизни, сценарии использования пространства и только после этого перехожу к работе. Проект согласовывается на каждом этапе.
              </div>
            </div>
          </div>
          
          <div class="faq-item">
            <button class="faq-item__question">
              Ваши визуализации реально совпадают с тем, что получится в итоге?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                Да. Мой подход основан на инженерном опыте: все решения продуманы технически и полностью реализуемы. Строители получают детальные чертежи и понятные схемы. Материалы подбираются исходя из их наличия и удобства доставки.
              </div>
            </div>
          </div>
          
          <div class="faq-item">
            <button class="faq-item__question">
              Что входит в авторское сопровождение?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                Я присутствую в чате с бригадой, контролирую соответствие работ проекту, отвечаю на вопросы строителей, даю рекомендации по материалам и корректирую решения, если требуется. Вы не остаётесь один на один с ремонтом.
              </div>
            </div>
          </div>
          
          <div class="faq-item">
            <button class="faq-item__question">
              А если у нас своя бригада — вы сможете работать с ними?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                Да. Я работаю как с партнёрскими бригадами, так и с вашими специалистами. В последнем случае можно подключить авторское сопровождение, чтобы реализация прошла спокойно и без ошибок.
              </div>
            </div>
          </div>
          
          <div class="faq-item">
            <button class="faq-item__question">
              Можно ли экономить с вашим проектом?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                Да! Благодаря строительному опыту я знаю, где можно сэкономить без потери качества, а где экономить нельзя. Я всегда предлагаю оптимальные альтернативы и помогаю держать бюджет под контролем.
              </div>
            </div>
          </div>
          
          <div class="faq-item">
            <button class="faq-item__question">
              Сколько времени занимает разработка дизайн-проекта?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                В среднем — около 2 месяцев: планировки → визуализации → рабочие чертежи. Каждый этап начинается только после утверждения предыдущего — вы постоянно участвуете в процессе.
              </div>
            </div>
          </div>
          
          <div class="faq-item">
            <button class="faq-item__question">
              Может ли быть такое, что каких-то материалов не окажется в ходе реализации?
              <span class="faq-item__icon">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              </span>
            </button>
            <div class="faq-item__answer">
              <div class="faq-item__answer-inner">
                Да, такие ситуации бывают. Материалы имеют свойство заканчиваться. Но я всегда остаюсь на связи, чтобы подобрать замену, при этом не изменить конечный результат.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact" id="contact">
      <div class="container">
        <div class="contact__inner">
          <div class="contact__content fade-in">
            <h2>Свяжитесь с нами</h2>
            <p class="contact__subtitle">Заполните форму или напишите напрямую — мы ответим в течение дня</p>
            
            <div class="contact__info">
              <div class="contact__info-item">
                <span class="contact__info-icon">
                  <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </span>
                <div class="contact__info-text">
                  <a href="tel:+79000000000">+7 (900) 000-00-00</a>
                </div>
              </div>
              
              <div class="contact__info-item">
                <span class="contact__info-icon">
                  <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </span>
                <div class="contact__info-text">
                  Калининград и область
                </div>
              </div>
            </div>
            
            <div class="contact__socials">
              <a href="#" class="contact__social" aria-label="WhatsApp" data-analytics="whatsapp">
                <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              </a>
              <a href="#" class="contact__social" aria-label="Telegram" data-analytics="telegram">
                <svg viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
              </a>
            </div>
          </div>
          
          <div class="form fade-in" id="contactForm">
            <form id="mainForm" action="api/send.php" method="POST">
              <!-- Honeypot -->
              <div class="form__honeypot">
                <label for="website">Сайт</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
              </div>
              
              <input type="hidden" name="form_type" value="consultation">
              <input type="hidden" name="tariff" id="formTariff" value="">
              
              <div class="form__group">
                <label class="form__label" for="name">
                  Имя <span class="form__hint">— чтобы знать, как к вам обращаться</span>
                </label>
                <input type="text" class="form__input" id="name" name="name" required placeholder="Ваше имя">
              </div>
              
              <div class="form__group">
                <label class="form__label" for="phone">
                  Телефон / Telegram <span class="form__hint">— удобный способ связи</span>
                </label>
                <input type="tel" class="form__input" id="phone" name="phone" required placeholder="+7 (___) ___-__-__">
              </div>
              
              <div class="form__row">
                <div class="form__group">
                  <label class="form__label" for="object_type">Тип объекта</label>
                  <select class="form__select" id="object_type" name="object_type">
                    <option value="">Выберите тип</option>
                    <option value="apartment">Квартира</option>
                    <option value="house">Дом</option>
                    <option value="townhouse">Таунхаус</option>
                    <option value="other">Другое</option>
                  </select>
                </div>
                
                <div class="form__group">
                  <label class="form__label" for="area">Площадь (м²)</label>
                  <input type="number" class="form__input" id="area" name="area" placeholder="Примерная площадь">
                </div>
              </div>
              
              <div class="form__group">
                <label class="form__label" for="stage">На каком вы этапе?</label>
                <select class="form__select" id="stage" name="stage">
                  <option value="">Выберите этап</option>
                  <option value="planning">Только планирую ремонт</option>
                  <option value="layout_ready">Уже есть планировка</option>
                  <option value="team_ready">Уже есть бригада</option>
                  <option value="in_progress">Идёт ремонт, нужен контроль</option>
                  <option value="other">Другое</option>
                </select>
              </div>
              
              <div class="form__group">
                <label class="form__label" for="comment">
                  Комментарий <span class="form__hint">(необязательно)</span>
                </label>
                <textarea class="form__textarea" id="comment" name="comment" placeholder="Опишите пожелания или задайте вопрос"></textarea>
              </div>
              
              <div class="form__submit">
                <button type="submit" class="btn btn--primary btn--full btn--lg">Получить бесплатную консультацию</button>
              </div>
            </form>
            
            <div class="form__success" id="formSuccess">
              <div class="form__success-icon">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
              <h3 class="form__success-title">Заявка отправлена!</h3>
              <p class="form__success-text">Мы свяжемся с вами в ближайшее время</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer__inner">
        <a href="#hero" class="footer__logo">
          <span class="footer__logo-text">Анна Пакселева</span>
        </a>
        <p class="footer__copy">© <?= date('Y') ?> Студия дизайна интерьеров Анны Пакселевой. Калининград.</p>
        <div class="footer__links">
          <a href="#" class="footer__link">Политика конфиденциальности</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Modal -->
  <div class="modal" id="modal">
    <div class="modal__overlay" data-modal-close></div>
    <div class="modal__content">
      <button class="modal__close" data-modal-close aria-label="Закрыть">
        <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
      <div class="modal__body">
        <h3 class="modal__title" id="modalTitle">Получить консультацию</h3>
        <form id="modalForm" action="api/send.php" method="POST">
          <!-- Honeypot -->
          <div class="form__honeypot">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>
          
          <input type="hidden" name="form_type" id="modalFormType" value="consultation">
          <input type="hidden" name="tariff" id="modalTariff" value="">
          
          <div class="form__group">
            <label class="form__label" for="modal_name">Имя</label>
            <input type="text" class="form__input" id="modal_name" name="name" required placeholder="Ваше имя">
          </div>
          
          <div class="form__group">
            <label class="form__label" for="modal_phone">Телефон</label>
            <input type="tel" class="form__input" id="modal_phone" name="phone" required placeholder="+7 (___) ___-__-__">
          </div>
          
          <div class="form__group" id="modalTariffGroup" style="display: none;">
            <label class="form__label">Выбранный тариф</label>
            <input type="text" class="form__input" id="modalTariffDisplay" readonly>
          </div>
          
          <div class="form__submit">
            <button type="submit" class="btn btn--primary btn--full">Отправить заявку</button>
          </div>
        </form>
        
        <div class="form__success" id="modalSuccess">
          <div class="form__success-icon">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <h3 class="form__success-title">Заявка отправлена!</h3>
          <p class="form__success-text">Мы свяжемся с вами в ближайшее время</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Lightbox -->
  <div class="lightbox" id="lightbox">
    <button class="lightbox__close" aria-label="Закрыть">
      <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>
    <button class="lightbox__nav lightbox__nav--prev" aria-label="Предыдущее изображение">
      <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <div class="lightbox__content">
      <img src="" alt="" id="lightboxImage">
    </div>
    <button class="lightbox__nav lightbox__nav--next" aria-label="Следующее изображение">
      <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
  </div>

  <!-- Scripts -->
  <script src="js/main.js"></script>
</body>
</html>
