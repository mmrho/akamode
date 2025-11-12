console.log("header.js is loaded!");
try {
  class HeaderManager {
    constructor() {
      this.isMobile = window.matchMedia("(max-width: 849.98px)").matches;
      this.elements = {
        mobile: null,
        desktop: null,
      };
      this.listeners = new Map();
      this.scrollPosition = 0;
      this.resizeTimeout = null;
      this.scrollRafId = null;
      this.isScrolling = false;

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
          shopping: {
            icon: document.getElementById("shoppingBagIcon"),
            panel: document.getElementById("mobile-shopping-panel"),
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

      return this.elements;
    }

    // Validation (simplified)
    validate(mode = this.isMobile ? 'mobile' : 'desktop') {
      const els = this.getElements()[mode];
      if (mode === 'mobile') {
        return !!(els.menu.btn && els.menu.nav && els.menu.overlay && els.search.icon && els.search.bar && els.shopping.icon && els.shopping.panel);
      } else {
        return !!(els.search.icon && els.search.bar && els.overlay);
      }
    }

    // Scroll lock
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
      ['menu-open', 'search-open', 'shopping-open', 'search-open-desktop'].forEach(cls => {
        document.body.classList.remove(cls);
      });
      this.unlockBody();
    }

    // Menu handlers (Mobile)
    handleMenuToggle(e) {
      console.log("Menu toggle triggered"); // Debug
      if (!this.isMobile) return;
      // FIXED: Removed preventDefault() - unnecessary for button clicks
      const els = this.getElements().mobile;
      const isOpen = els.menu.nav?.classList.contains('active');
      if (isOpen) {
        this.closeMenu();
      } else if (els.search.bar?.classList.contains('active')) {
        this.closeSearchMobile();
      } else if (els.shopping.panel?.classList.contains('active')) {
        this.closeShopping();
      } else {
        this.openMenu();
      }
    }

    openMenu() {
      console.log("Opening menu"); // Debug
      const els = this.getElements().mobile;
      els.menu.btn?.classList.add('active');
      els.menu.nav?.classList.add('active');
      els.menu.overlay?.classList.add('active');
      document.body.classList.add('menu-open');
      els.headerContent?.classList.add('menu-open');
      const navContent = els.menu.nav?.querySelector('.mobile-nav-content');
      navContent?.classList.add('active');
      this.lockBody();
    }

    closeMenu() {
      console.log("Closing menu"); // Debug
      const els = this.getElements().mobile;
      els.menu.btn?.classList.remove('active');
      els.menu.nav?.classList.remove('active');
      els.menu.overlay?.classList.remove('active');
      document.body.classList.remove('menu-open');
      els.headerContent?.classList.remove('menu-open');
      const navContent = els.menu.nav?.querySelector('.mobile-nav-content');
      navContent?.classList.remove('active');
      this.unlockBody();
    }

    // Search Mobile
    handleSearchMobile(e) {
      console.log("Mobile search triggered"); // Debug
      if (!this.isMobile) return;
      // FIXED: Removed preventDefault()
      const els = this.getElements().mobile;
      const isOpen = els.search.bar?.classList.contains('active');
      isOpen ? this.closeSearchMobile() : this.openSearchMobile();
    }

    openSearchMobile() {
      console.log("Opening mobile search"); // Debug
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
    }

    closeSearchMobile() {
      console.log("Closing mobile search"); // Debug
      const els = this.getElements().mobile;
      els.menu.btn?.classList.remove('active');
      els.search.bar?.classList.remove('active');
      els.menu.overlay?.classList.remove('active');
      document.body.classList.remove('search-open');
      els.headerContent?.classList.remove('search-open');
      this.unlockBody();
    }

    // Shopping Mobile
    handleShopping(e) {
      console.log("Shopping triggered"); // Debug
      if (!this.isMobile) return;
      // FIXED: Removed preventDefault()
      const els = this.getElements().mobile;
      const isOpen = els.shopping.panel?.classList.contains('active');
      isOpen ? this.closeShopping() : this.openShopping();
    }

    openShopping() {
      console.log("Opening shopping"); // Debug
      const els = this.getElements().mobile;
      els.menu.btn?.classList.add('active');
      els.shopping.panel?.classList.add('active');
      els.menu.overlay?.classList.add('active');
      document.body.classList.add('shopping-open');
      els.headerContent?.classList.add('shopping-open');
      this.lockBody();
    }

    closeShopping() {
      console.log("Closing shopping"); // Debug
      const els = this.getElements().mobile;
      els.menu.btn?.classList.remove('active');
      els.shopping.panel?.classList.remove('active');
      els.menu.overlay?.classList.remove('active');
      document.body.classList.remove('shopping-open');
      els.headerContent?.classList.remove('shopping-open');
      this.unlockBody();
    }

    // Search Desktop
    handleSearchDesktop(e) {
      console.log("Desktop search triggered"); // Debug
      if (this.isMobile) return;
      // FIXED: Removed preventDefault()
      const els = this.getElements().desktop;
      const isOpen = els.search.bar?.classList.contains('active');
      isOpen ? this.closeSearchDesktop() : this.openSearchDesktop();
    }

    openSearchDesktop() {
      console.log("Opening desktop search"); // Debug
      const els = this.getElements().desktop;
      els.search.bar?.classList.add('active');
      els.overlay?.classList.add('active');
      document.body.classList.add('search-open-desktop');
      this.lockBody();
      setTimeout(() => {
        const input = els.search.bar?.querySelector('input');
        input?.focus();
      }, 100);
    }

    closeSearchDesktop() {
      console.log("Closing desktop search"); // Debug
      const els = this.getElements().desktop;
      els.search.bar?.classList.remove('active');
      els.overlay?.classList.remove('active');
      document.body.classList.remove('search-open-desktop');
      this.unlockBody();
    }

    // Submenu (delegation)
    initSubmenu() {
      if (!this.isMobile) return;
      const handleSubmenu = (e) => {
        const link = e.target.closest('.mobile-nav-link[data-has-submenu="true"]');
        if (!link) return;
        // FIXED: Removed preventDefault() - handled by <a> tag if needed, but for toggle it's optional
        const parentItem = link.closest('.mobile-nav-item');
        if (parentItem) {
          parentItem.classList.toggle('submenu-open');
        }
      };
      document.addEventListener('click', handleSubmenu, { passive: true });
      this.listeners.set('submenu', handleSubmenu);
    }

    // Scroll effect (throttled)
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

    // Keyboard
    initKeyboard() {
      const handleKey = (e) => {
        if (e.key !== 'Escape') return;
        if (this.isMobile) {
          if (document.body.classList.contains('search-open')) this.closeSearchMobile();
          else if (document.body.classList.contains('menu-open')) this.closeMenu();
          else if (document.body.classList.contains('shopping-open')) this.closeShopping();
        } else if (document.body.classList.contains('search-open-desktop')) {
          this.closeSearchDesktop();
        }
      };
      document.addEventListener('keydown', handleKey);
      this.listeners.set('keyboard', handleKey);
    }

    // Touch prevent
    initTouchPrevent() {
      if (!this.isMobile) return;
      const preventTouchmove = (e) => {
        const openClasses = ['menu-open', 'search-open', 'shopping-open'];
        if (openClasses.some(cls => document.body.classList.contains(cls)) &&
            !e.target.closest('#mobile-nav, #mobile-search-bar, #mobile-shopping-panel')) {
          e.preventDefault();
        }
      };
      document.addEventListener('touchmove', preventTouchmove, { passive: false });
      this.listeners.set('touchmove', preventTouchmove);
    }

    // Bind events (passive safe now)
    bindEvents() {
      // Cleanup
      this.listeners.forEach((listener, key) => {
        if (typeof listener === 'function') {
          (key === 'scroll' ? window : document).removeEventListener(key, listener);
        } else if (listener.target) {
          listener.target.removeEventListener(listener.type, listener.handler);
        }
      });
      this.listeners.clear();

      console.log(`Binding events for ${this.isMobile ? 'mobile' : 'desktop'}...`); // Debug

      if (!this.validate()) {
        console.error(`Cannot bind: Elements missing for ${this.isMobile ? 'mobile' : 'desktop'}`);
        return;
      }

      if (this.isMobile) {
        // Delegation with closest (passive: true safe)
        const handleMobileClick = (e) => {
          const menuBtn = e.target.closest('#menuBtnIcon');
          if (menuBtn) return this.handleMenuToggle(e);

          const searchIcon = e.target.closest('#searchIcon');
          if (searchIcon) return this.handleSearchMobile(e);

          const shoppingIcon = e.target.closest('#shoppingBagIcon');
          if (shoppingIcon) return this.handleShopping(e);

          const overlay = e.target.closest('#mobile-nav-overlay');
          if (overlay) return this.closeMenu();
        };
        document.addEventListener('click', handleMobileClick, { passive: true });
        this.listeners.set('mobileClick', handleMobileClick);

        this.initSubmenu();
        this.initHeaderScroll();
        this.initTouchPrevent();
      } else {
        // Desktop: Direct listeners (passive: true safe)
        const els = this.getElements().desktop;
        if (els.search.icon) {
          els.search.icon.addEventListener('click', (e) => this.handleSearchDesktop(e), { passive: true });
          this.listeners.set('desktopSearch', { type: 'click', handler: (e) => this.handleSearchDesktop(e), target: els.search.icon });
        }
        if (els.overlay) {
          els.overlay.addEventListener('click', () => this.closeSearchDesktop(), { passive: true });
          this.listeners.set('desktopOverlay', { type: 'click', handler: () => this.closeSearchDesktop(), target: els.overlay });
        }
      }

      this.initKeyboard();
    }

    // Resize handler
    handleResize() {
      const newIsMobile = window.matchMedia("(max-width: 991px)").matches;
      if (newIsMobile === this.isMobile) return;

      console.log(`Viewport changed to ${newIsMobile ? 'mobile' : 'desktop'}`); // Debug
      this.isMobile = newIsMobile;
      this.closeAllPanels();
      this.bindEvents();
    }

    // Init
    init() {
      console.log("Initializing header..."); // Debug
      this.bindEvents();

      // Debounced resize
      const debouncedResize = () => {
        clearTimeout(this.resizeTimeout);
        this.resizeTimeout = setTimeout(() => this.handleResize(), 150);
      };
      window.addEventListener('resize', debouncedResize, { passive: true });
      this.listeners.set('resize', { type: 'resize', handler: debouncedResize, target: window });

      // Initial check
      setTimeout(() => this.handleResize(), 0);
    }

    // Cleanup
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

  // Global instance
  let headerManager = null;

  document.addEventListener("DOMContentLoaded", () => {
    console.log("DOM fully loaded!");
    headerManager = new HeaderManager();
  });

  // Cleanup on unload
  window.addEventListener('beforeunload', () => {
    if (headerManager) headerManager.destroy();
  });

} catch (error) {
  console.error("Error in header script:", error);
}