console.log("header.js is loaded!");

try {
  class HeaderManager {
    constructor() {
      // 991.98px standard breakpoint
      this.isMobile = window.matchMedia("(max-width: 849.98px)").matches;
      
      this.elements = {
        mobile: null,
        desktop: null,
      };
      this.ribbon = null;
      this.listeners = new Map();
      this.scrollPosition = 0;
      this.resizeTimeout = null;
      
      this.init();
    }

    // Lazy load and cache elements
    getElements() {
      if (!this.elements.mobile) {
        this.elements.mobile = {
          menu: {
            btn: document.getElementById("menuBtnIcon"),
            nav: document.getElementById("mobile-nav"),
            overlay: document.getElementById("mobile-nav-overlay"),
          },
          search: {
            icon: document.getElementById("searchIcon"),
            bar: document.getElementById("mobile-search-bar"),
            input: null,
          },
          // REMOVED: Shopping elements are no longer managed by JS
          close: {
            icon: document.getElementById("closeIcon"),
          },
          headerContent: document.querySelector(".mobile-header-content"),
          body: document.body,
        };
      }

      if (!this.elements.desktop) {
        this.elements.desktop = {
          search: {
            icon: document.getElementById("searchIcon-D"),
            bar: document.getElementById("desktop-search-bar"),
            input: null,
          },
          overlay: document.getElementById("desktop-nav-overlay"),
          body: document.body,
        };
      }

      if (!this.ribbon) {
        this.ribbon = document.querySelector('.ribbon-section');
      }

      return this.elements;
    }

    // Validation
    validate(mode = this.isMobile ? 'mobile' : 'desktop') {
      const els = this.getElements()[mode];
      
      if (mode === 'mobile') {
        // Removed shopping checks
        const checks = {
            menuBtn: !!els.menu.btn,
            nav: !!els.menu.nav,
            overlay: !!els.menu.overlay,
            searchIcon: !!els.search.icon,
            searchBar: !!els.search.bar,
            closeIcon: !!els.close.icon
        };

        const isValid = Object.values(checks).every(Boolean);
        
        if (!isValid) {
            console.warn('Mobile Validation Failed. Missing elements:', checks);
        }

        return isValid;
      } else {
        return !!(els.search.icon && els.search.bar && els.overlay);
      }
    }

    lockBody() {
      try {
        this.scrollPosition = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${this.scrollPosition}px`;
        document.body.style.overflow = 'hidden';
        document.body.style.width = '100%';
      } catch (err) {
        console.error('Error locking body:', err);
      }
    }

    unlockBody() {
      try {
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.overflow = '';
        document.body.style.width = '';
        window.scrollTo(0, this.scrollPosition);
      } catch (err) {
        console.error('Error unlocking body:', err);
      }
    }

    // Close all panels
    closeAllPanels() {
      // Removed 'shopping-open' from cleanup list
      const classesToRemove = ['menu-open', 'search-open', 'search-open-desktop'];
      document.body.classList.remove(...classesToRemove);

      const els = this.getElements().mobile;
      
      els.menu.btn?.classList.remove('active');
      els.menu.nav?.classList.remove('active');
      els.menu.overlay?.classList.remove('active');
      
      els.headerContent?.classList.remove('menu-open', 'search-open');
      
      const navContent = els.menu.nav?.querySelector('.mobile-nav-content');
      navContent?.classList.remove('active');
      
      els.search.bar?.classList.remove('active');
      // Removed shopping panel cleanup
      els.close.icon?.classList.remove('active');

      const menuLabel = els.menu.btn?.querySelector('.menu-label');
      if (menuLabel) menuLabel.textContent = '';
      
      this.unlockBody();
      this.showRibbon();
    }

    hideRibbon() {
      if (this.ribbon) this.ribbon.classList.remove('visible');
    }

    showRibbon() {
      if (this.ribbon) this.ribbon.classList.add('visible');
    }

    // --- Mobile Handlers ---

    handleMenuToggle(e) {
      if (!this.isMobile) return;
      const els = this.getElements().mobile;
      
      const isOpen = els.menu.nav?.classList.contains('active');
      
      if (isOpen) {
        this.closeMenu();
      } else if (els.search.bar?.classList.contains('active')) {
        this.closeSearchMobile();
      } else {
        // Removed shopping close check
        this.openMenu();
      }
    }

    openMenu() {
      const els = this.getElements().mobile;
      els.menu.btn?.classList.add('active');
      els.menu.nav?.classList.add('active');
      els.menu.overlay?.classList.add('active');
      
      document.body.classList.add('menu-open');
      els.headerContent?.classList.add('menu-open');
      
      const navContent = els.menu.nav?.querySelector('.mobile-nav-content');
      navContent?.classList.add('active');
      
      this.lockBody();
      els.close.icon?.classList.add('active');
      
      const menuLabel = els.menu.btn?.querySelector('.menu-label');
      if (menuLabel) menuLabel.textContent = 'منو';
      
      this.hideRibbon();
    }

    closeMenu() {
      const els = this.getElements().mobile;
      els.menu.btn?.classList.remove('active');
      els.menu.nav?.classList.remove('active');
      els.menu.overlay?.classList.remove('active');
      
      document.body.classList.remove('menu-open');
      els.headerContent?.classList.remove('menu-open');
      
      const navContent = els.menu.nav?.querySelector('.mobile-nav-content');
      navContent?.classList.remove('active');
      
      els.close.icon?.classList.remove('active');
      
      const menuLabel = els.menu.btn?.querySelector('.menu-label');
      if (menuLabel) menuLabel.textContent = '';
      
      this.unlockBody();
      this.showRibbon();
    }

    handleSearchMobile(e) {
      if (!this.isMobile) return;
      const els = this.getElements().mobile;
      const isOpen = els.search.bar?.classList.contains('active');
      isOpen ? this.closeSearchMobile() : this.openSearchMobile();
    }

    openSearchMobile() {
      const els = this.getElements().mobile;
      els.menu.btn?.classList.add('active');
      els.search.bar?.classList.add('active');
      els.menu.overlay?.classList.add('active');
      
      document.body.classList.add('search-open');
      els.headerContent?.classList.add('search-open');
      
      setTimeout(() => {
        els.search.input = els.search.bar?.querySelector('input');
        els.search.input?.focus();
      }, 100);
      
      this.lockBody();
      els.close.icon?.classList.add('active');
      
      const menuLabel = els.menu.btn?.querySelector('.menu-label');
      if (menuLabel) menuLabel.textContent = 'جستجو در محصولات';
      
      this.hideRibbon();
    }

    closeSearchMobile() {
      const els = this.getElements().mobile;
      els.menu.btn?.classList.remove('active');
      els.search.bar?.classList.remove('active');
      els.menu.overlay?.classList.remove('active');
      
      document.body.classList.remove('search-open');
      els.headerContent?.classList.remove('search-open');
      els.close.icon?.classList.remove('active');
      
      const menuLabel = els.menu.btn?.querySelector('.menu-label');
      if (menuLabel) menuLabel.textContent = '';
      
      this.unlockBody();
      this.showRibbon();
    }

    // REMOVED: handleShopping, openShopping, closeShopping functions

    handleClose(e) {
      if (this.isMobile) {
        if (document.body.classList.contains('search-open')) this.closeSearchMobile();
        else if (document.body.classList.contains('menu-open')) this.closeMenu();
        // Removed shopping check
      } else if (document.body.classList.contains('search-open-desktop')) {
        this.closeSearchDesktop();
      }
    }

    // --- Desktop Handlers ---

    handleSearchDesktop(e) {
      if (this.isMobile) return;
      const els = this.getElements().desktop;
      const isOpen = els.search.bar?.classList.contains('active');
      isOpen ? this.closeSearchDesktop() : this.openSearchDesktop();
    }

    openSearchDesktop() {
      const els = this.getElements().desktop;
      els.search.bar?.classList.add('active');
      els.overlay?.classList.add('active');
      document.body.classList.add('search-open-desktop');
      this.lockBody();
      setTimeout(() => {
        const input = els.search.bar?.querySelector('input');
        input?.focus();
      }, 100);
      this.hideRibbon();
    }

    closeSearchDesktop() {
      const els = this.getElements().desktop;
      els.search.bar?.classList.remove('active');
      els.overlay?.classList.remove('active');
      document.body.classList.remove('search-open-desktop');
      this.unlockBody();
      this.showRibbon();
    }

    // --- Submenu Logic ---

    initSubmenu() {
      if (!this.isMobile) return;
      
      const handleSubmenu = (e) => {
        const link = e.target.closest('.mobile-nav-link[data-has-submenu="true"]');
        if (!link) return;
        
        const parentItem = link.closest('.mobile-nav-item');
        if (parentItem) {
          parentItem.classList.toggle('submenu-open');
        }
      };
      
      document.addEventListener('click', handleSubmenu, { passive: true });
      this.listeners.set('submenu', handleSubmenu);
    }

    // --- Scroll Effect ---

    initHeaderScroll() {
      if (!this.isMobile) return;
      const headerContainer = document.querySelector('.mobile-header-container');
      if (!headerContainer) return;

      let ticking = false;
      const updateScroll = () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > 50) {
          headerContainer.classList.add('scrolled');
        } else {
          headerContainer.classList.remove('scrolled');
        }
        ticking = false;
      };

      const throttledScroll = () => {
        if (!ticking) {
          requestAnimationFrame(updateScroll);
          ticking = true;
        }
      };

      window.addEventListener('scroll', throttledScroll, { passive: true });
      this.listeners.set('scroll', { type: 'scroll', handler: throttledScroll, target: window });
    }

    initKeyboard() {
      const handleKey = (e) => {
        if (e.key !== 'Escape') return;
        if (this.isMobile) {
          if (document.body.classList.contains('search-open')) this.closeSearchMobile();
          else if (document.body.classList.contains('menu-open')) this.closeMenu();
          // Removed shopping check
        } else if (document.body.classList.contains('search-open-desktop')) {
          this.closeSearchDesktop();
        }
      };
      document.addEventListener('keydown', handleKey);
      this.listeners.set('keyboard', handleKey);
    }

    initTouchPrevent() {
      if (!this.isMobile) return;
      
      const preventTouchmove = (e) => {
        // Removed shopping-open from the check
        const openClasses = ['menu-open', 'search-open'];
        const isPanelOpen = openClasses.some(cls => document.body.classList.contains(cls));
        
        if (isPanelOpen) {
           const isInsideNav = e.target.closest('#mobile-nav');
           const isInsideSearch = e.target.closest('#mobile-search-bar');
           // Removed isInsideShop check

           if (!isInsideNav && !isInsideSearch) {
             e.preventDefault();
           }
        }
      };
      
      document.addEventListener('touchmove', preventTouchmove, { passive: false });
      this.listeners.set('touchmove', preventTouchmove);
    }

    // --- Bind Events ---

    bindEvents() {
      this.listeners.forEach((listener, key) => {
        if (typeof listener === 'function') {
          (key === 'scroll' ? window : document).removeEventListener(key, listener);
        } else if (listener.target) {
          listener.target.removeEventListener(listener.type, listener.handler);
        }
      });
      this.listeners.clear();

      console.log(`Binding events for ${this.isMobile ? 'mobile' : 'desktop'}...`);

      if (!this.validate()) {
        console.error(`Cannot bind: Elements missing for ${this.isMobile ? 'mobile' : 'desktop'}`);
        return; 
      }

      if (this.isMobile) {
        const handleMobileClick = (e) => {
          
          const menuBtn = e.target.closest('#menuBtnIcon');
          if (menuBtn) return this.handleMenuToggle(e);

          const searchIcon = e.target.closest('#searchIcon');
          if (searchIcon) return this.handleSearchMobile(e);

          // REMOVED: Shopping click listener
          // Since it's an <a> tag now, we let the browser handle the click naturally.

          const closeIcon = e.target.closest('#closeIcon');
          if (closeIcon) return this.handleClose(e);

          const overlay = e.target.closest('#mobile-nav-overlay');
          if (overlay) return this.closeMenu();
        };

        document.addEventListener('click', handleMobileClick, { passive: true });
        this.listeners.set('mobileClick', handleMobileClick);

        this.initSubmenu();
        this.initHeaderScroll();
        this.initTouchPrevent();
      } else {
        const els = this.getElements().desktop;
        
        if (els.search.icon) {
          const handler = (e) => this.handleSearchDesktop(e);
          els.search.icon.addEventListener('click', handler, { passive: true });
          this.listeners.set('desktopSearch', { type: 'click', handler: handler, target: els.search.icon });
        }
        
        if (els.overlay) {
          const handler = () => this.closeSearchDesktop();
          els.overlay.addEventListener('click', handler, { passive: true });
          this.listeners.set('desktopOverlay', { type: 'click', handler: handler, target: els.overlay });
        }
      }

      this.initKeyboard();
    }

    handleResize() {
      const newIsMobile = window.matchMedia("(max-width: 991.98px)").matches;
      if (newIsMobile === this.isMobile) return;

      console.log(`Viewport changed to ${newIsMobile ? 'mobile' : 'desktop'}`);
      this.isMobile = newIsMobile;
      this.closeAllPanels();
      this.bindEvents();
    }

    init() {
      console.log("Initializing header...");
      this.bindEvents();

      const debouncedResize = () => {
        clearTimeout(this.resizeTimeout);
        this.resizeTimeout = setTimeout(() => this.handleResize(), 150);
      };
      
      window.addEventListener('resize', debouncedResize, { passive: true });
      this.listeners.set('resize', { type: 'resize', handler: debouncedResize, target: window });
    }

    destroy() {
      this.listeners.forEach((listener, key) => {
        if (typeof listener === 'function') {
          (key === 'scroll' ? window : document).removeEventListener(key, listener);
        } else if (listener.target) {
          listener.target.removeEventListener(listener.type, listener.handler);
        }
      });
      this.listeners.clear();
    }
  }

  let headerManager = null;

  document.addEventListener("DOMContentLoaded", () => {
    headerManager = new HeaderManager();
  });

  window.addEventListener('beforeunload', () => {
    if (headerManager) headerManager.destroy();
  });

} catch (error) {
  console.error("Error in header script:", error);
}