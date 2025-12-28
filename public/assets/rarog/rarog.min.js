/*!
 * Rarog JS Core v3.0.0
 * Vanilla JS utilities for interactive components (dropdown, collapse, modal).
 * Author: TheSkiF4er <dev@cajeer.ru>
 * License: Apache-2.0
 */

const _dropdownInstances = new WeakMap();
const _collapseInstances = new WeakMap();
const _modalInstances = new WeakMap();
const _offcanvasInstances = new WeakMap();
const _toastInstances = new WeakMap();
const _tooltipInstances = new WeakMap();
const _popoverInstances = new WeakMap();
const _datepickerInstances = new WeakMap();
const _datetimePickerInstances = new WeakMap();
const _selectInstances = new WeakMap();
const _comboboxInstances = new WeakMap();
const _tagsInputInstances = new WeakMap();
const _dataTableInstances = new WeakMap();
const _maskHandlers = Object.create(null);

const _eventBusListeners = new Map();

const RarogConfig = {
  debug:
    typeof window !== "undefined" &&
    !!(window.RAROG_DEBUG || window.RAROG_DEV || window.RAROG_DEBUG_MODE)
};

function _debugLog(...args) {
  if (!RarogConfig.debug || typeof console === "undefined") return;
  console.log("[Rarog]", ...args);
}

function _debugWarn(...args) {
  if (!RarogConfig.debug || typeof console === "undefined") return;
  console.warn("[Rarog]", ...args);
}

function _emitOnBus(type, payload) {
  const set = _eventBusListeners.get(type);
  if (!set || set.size === 0) return;
  set.forEach(handler => {
    try {
      handler(payload);
    } catch (error) {
      if (RarogConfig.debug && typeof console !== "undefined") {
        console.error("[Rarog Events]", error);
      }
    }
  });
}

const Events = {
  on(type, handler) {
    if (!type || typeof handler !== "function") return;
    let set = _eventBusListeners.get(type);
    if (!set) {
      set = new Set();
      _eventBusListeners.set(type, set);
    }
    set.add(handler);
  },
  off(type, handler) {
    const set = _eventBusListeners.get(type);
    if (!set) return;
    set.delete(handler);
    if (set.size === 0) {
      _eventBusListeners.delete(type);
    }
  },
  emit(type, payload = {}) {
    _debugLog("event", type, payload);
    _emitOnBus(type, payload);
  }
};

function _dispatchEvent(element, name, detail = {}) {
  if (!element || typeof CustomEvent === "undefined") return;
  const evt = new CustomEvent(name, {
    bubbles: true,
    cancelable: false,
    detail
  });
  element.dispatchEvent(evt);
  _emitOnBus(name, { element, detail });
}
olveTarget(trigger, explicitTarget) {
  if (explicitTarget) return explicitTarget;
  if (!trigger || typeof document === "undefined") return null;

  const selector =
    trigger.getAttribute("data-rg-target") ||
    trigger.getAttribute("href");

  if (!selector || selector === "#" || selector === "") return null;

  try {
    return document.querySelector(selector);
  } catch (e) {
    return null;
  }
}

function _getFocusableElements(container) {
  if (!container) return [];
  const selectors = [
    "a[href]",
    "button:not([disabled])",
    "input:not([disabled]):not([type='hidden'])",
    "select:not([disabled])",
    "textarea:not([disabled])",
    "[tabindex]:not([tabindex='-1'])"
  ];
  return Array.from(container.querySelectorAll(selectors.join(","))).filter(
    el => el.offsetParent !== null || el === document.activeElement
  );
}

/* -------------------------------------------------------------------------- */
/* Dropdown                                                                   */
/* -------------------------------------------------------------------------- */

class Dropdown {
  constructor(trigger, options = {}) {
    if (!trigger) {
      throw new Error("Rarog.Dropdown: trigger element is required");
    }
    this._trigger = trigger;
    this._menu = _resolveTarget(trigger, options.target);

    this._isOpen = false;
    this._onDocumentClick = this._handleDocumentClick.bind(this);
    this._onKeydown = this._handleKeydown.bind(this);

    this._initA11y();

    _dropdownInstances.set(trigger, this);
  }

  _initA11y() {
    if (!this._trigger) return;
    this._trigger.setAttribute("aria-haspopup", "menu");
    this._trigger.setAttribute("aria-expanded", "false");

    if (this._menu && this._menu.id) {
      this._trigger.setAttribute("aria-controls", this._menu.id);
    }
    if (this._menu && !this._menu.hasAttribute("role")) {
      this._menu.setAttribute("role", "menu");
    }
  }

  _handleDocumentClick(event) {
    if (!this._isOpen) return;
    const target = event.target;
    if (
      !this._trigger.contains(target) &&
      (!this._menu || !this._menu.contains(target))
    ) {
      this.hide();
    }
  }

  _handleKeydown(event) {
    if (!this._isOpen || !this._menu) return;

    if (event.key === "Escape") {
      event.preventDefault();
      this.hide();
      this._trigger.focus();
      return;
    }

    const items = _getFocusableElements(this._menu);
    if (!items.length) return;

    let index = items.indexOf(document.activeElement);

    if (event.key === "ArrowDown") {
      event.preventDefault();
      index = index === -1 ? 0 : (index + 1) % items.length;
      items[index].focus();
    } else if (event.key === "ArrowUp") {
      event.preventDefault();
      if (index === -1) {
        items[items.length - 1].focus();
      } else {
        index = (index - 1 + items.length) % items.length;
        items[index].focus();
      }
    }
  }

  show() {
    if (this._isOpen) return;
    this._isOpen = true;

    if (this._menu) {
      this._menu.removeAttribute("hidden");
      this._menu.classList.add("rg-open");
    }
    this._trigger.setAttribute("aria-expanded", "true");

    if (typeof document !== "undefined") {
      document.addEventListener("click", this._onDocumentClick);
      document.addEventListener("keydown", this._onKeydown);
    }
  }

  hide() {
    if (!this._isOpen) return;
    this._isOpen = false;

    if (this._menu) {
      this._menu.setAttribute("hidden", "");
      this._menu.classList.remove("rg-open");
    }
    this._trigger.setAttribute("aria-expanded", "false");

    if (typeof document !== "undefined") {
      document.removeEventListener("click", this._onDocumentClick);
      document.removeEventListener("keydown", this._onKeydown);
    }
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  static getInstance(trigger) {
    return _dropdownInstances.get(trigger) || null;
  }

  static getOrCreate(trigger, options) {
    return this.getInstance(trigger) || new Dropdown(trigger, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Collapse / Accordion                                                       */
/* -------------------------------------------------------------------------- */

class Collapse {
  constructor(trigger, options = {}) {
    if (!trigger) {
      throw new Error("Rarog.Collapse: trigger element is required");
    }
    this._trigger = trigger;
    this._target = _resolveTarget(trigger, options.target);

    this._isOpen = !(
      this._target &&
      (this._target.hasAttribute("hidden") ||
        this._target.getAttribute("aria-hidden") === "true")
    );

    this._initA11y();

    _collapseInstances.set(trigger, this);
  }

  _initA11y() {
    if (!this._trigger || !this._target) return;

    if (!this._target.id) {
      this._target.id = `rg-collapse-${Math.random().toString(36).slice(2, 8)}`;
    }

    this._trigger.setAttribute("aria-controls", this._target.id);
    this._trigger.setAttribute("aria-expanded", String(this._isOpen));
    this._trigger.setAttribute("role", "button");

    this._target.setAttribute("role", "region");
    this._target.setAttribute("aria-hidden", String(!this._isOpen));
  }

  show() {
    if (this._isOpen) return;
    this._isOpen = true;

    if (this._target) {
      this._target.removeAttribute("hidden");
      this._target.setAttribute("aria-hidden", "false");
      this._target.classList.add("rg-collapse-open");
    }
    this._trigger.setAttribute("aria-expanded", "true");
  }

  hide() {
    if (!this._isOpen) return;
    this._isOpen = false;

    if (this._target) {
      this._target.setAttribute("hidden", "");
      this._target.setAttribute("aria-hidden", "true");
      this._target.classList.remove("rg-collapse-open");
    }
    this._trigger.setAttribute("aria-expanded", "false");
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  static getInstance(trigger) {
    return _collapseInstances.get(trigger) || null;
  }

  static getOrCreate(trigger, options) {
    return this.getInstance(trigger) || new Collapse(trigger, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Modal                                                                      */
/* -------------------------------------------------------------------------- */

let _openModal = null;

class Modal {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Modal: element is required");
    }
    this._element = element;
    this._options = Object.assign(
      {
        backdrop: true,
        keyboard: true
      },
      options
    );

    this._isOpen = false;
    this._previouslyFocused = null;
    this._onKeydown = this._handleKeydown.bind(this);
    this._onClick = this._handleClick.bind(this);

    this._initA11y();

    _modalInstances.set(element, this);
  }

  _initA11y() {
    if (!this._element) return;

    this._element.setAttribute("role", "dialog");
    this._element.setAttribute("aria-modal", "true");
    if (!this._element.hasAttribute("aria-hidden")) {
      this._element.setAttribute("aria-hidden", "true");
    }
  }

  _handleKeydown(event) {
    if (!this._isOpen) return;

    if (event.key === "Escape" && this._options.keyboard) {
      event.preventDefault();
      this.hide();
      return;
    }

    if (event.key === "Tab") {
      const focusables = _getFocusableElements(this._element);
      if (!focusables.length) {
        event.preventDefault();
        return;
      }

      const currentIndex = focusables.indexOf(document.activeElement);
      let nextIndex = currentIndex;

      if (event.shiftKey) {
        nextIndex = currentIndex <= 0 ? focusables.length - 1 : currentIndex - 1;
      } else {
        nextIndex = currentIndex === focusables.length - 1 ? 0 : currentIndex + 1;
      }

      event.preventDefault();
      focusables[nextIndex].focus();
    }
  }

  _handleClick(event) {
    if (!this._isOpen) return;

    const target = event.target;

    // backdrop click (клик по самому .modal, а не по контенту внутри)
    if (this._options.backdrop && target === this._element) {
      this.hide();
      return;
    }

    // элементы с data-rg-dismiss="modal"
    const dismiss = target.closest("[data-rg-dismiss='modal']");
    if (dismiss && this._element.contains(dismiss)) {
      event.preventDefault();
      this.hide();
    }
  }

  _lockScroll() {
    if (typeof document === "undefined") return;
    const body = document.body;
    if (!body) return;

    if (body.getAttribute("data-rg-scroll-locked") === "true") return;

    const originalOverflow = body.style.overflow || "";
    body.setAttribute("data-rg-original-overflow", originalOverflow);
    body.style.overflow = "hidden";
    body.setAttribute("data-rg-scroll-locked", "true");
  }

  _unlockScroll() {
    if (typeof document === "undefined") return;
    const body = document.body;
    if (!body) return;

    if (body.getAttribute("data-rg-scroll-locked") !== "true") return;

    const originalOverflow = body.getAttribute("data-rg-original-overflow") || "";
    body.style.overflow = originalOverflow;
    body.removeAttribute("data-rg-original-overflow");
    body.removeAttribute("data-rg-scroll-locked");
  }

  show() {
    if (this._isOpen) return;

    if (_openModal && _openModal !== this) {
      _openModal.hide();
    }

    this._isOpen = true;
    _openModal = this;

    if (typeof document !== "undefined") {
      this._previouslyFocused = document.activeElement;
      document.addEventListener("keydown", this._onKeydown);
      document.addEventListener("click", this._onClick);
    }

    this._element.removeAttribute("aria-hidden");
    this._element.classList.add("rg-modal-open");

    this._lockScroll();

    // focus trap start
    const focusables = _getFocusableElements(this._element);
    if (focusables.length) {
      focusables[0].focus();
    } else {
      this._element.setAttribute("tabindex", "-1");
      this._element.focus();
    }
  }

  hide() {
    if (!this._isOpen) return;

    this._isOpen = false;
    _openModal = null;

    if (typeof document !== "undefined") {
      document.removeEventListener("keydown", this._onKeydown);
      document.removeEventListener("click", this._onClick);
    }

    this._element.setAttribute("aria-hidden", "true");
    this._element.classList.remove("rg-modal-open");

    this._unlockScroll();

    if (this._previouslyFocused && typeof this._previouslyFocused.focus === "function") {
      this._previouslyFocused.focus();
    }
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  static getInstance(element) {
    return _modalInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Modal(element, options);
  }
}

/* -------------------------------------------------------------------------- */

/* -------------------------------------------------------------------------- */
/* Offcanvas / Drawer                                                         */
/* -------------------------------------------------------------------------- */

class Offcanvas {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Offcanvas: element is required");
    }
    this._element = element;
    this._isOpen = element.classList.contains("is-open");
    this._backdrop = null;
    this._options = Object.assign(
      {
        backdrop: true,
        scroll: false
      },
      options
    );
    this._onKeydown = this._handleKeydown.bind(this);

    _offcanvasInstances.set(element, this);
  }

  show() {
    if (typeof document === "undefined") return;
    if (this._isOpen) return;

    this._isOpen = true;

    if (this._options.backdrop) {
      this._createBackdrop();
    }

    if (!this._options.scroll) {
      document.body.classList.add("rg-offcanvas-open");
    }

    this._element.classList.add("is-open");
    this._element.removeAttribute("aria-hidden");
    this._element.setAttribute("aria-modal", "true");
    this._element.setAttribute("role", "dialog");

    document.addEventListener("keydown", this._onKeydown);
    _dispatchEvent(this._element, "rg:offcanvas:show", { instance: this });
  }

  hide() {
    if (typeof document === "undefined") return;
    if (!this._isOpen) return;

    this._isOpen = false;

    this._element.classList.remove("is-open");
    this._element.setAttribute("aria-hidden", "true");
    this._element.removeAttribute("aria-modal");

    document.removeEventListener("keydown", this._onKeydown);

    if (!this._options.scroll) {
      document.body.classList.remove("rg-offcanvas-open");
    }

    this._removeBackdrop();
    _dispatchEvent(this._element, "rg:offcanvas:hide", { instance: this });
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  _createBackdrop() {
    if (this._backdrop || typeof document === "undefined") return;

    const backdrop = document.createElement("div");
    backdrop.className = "offcanvas-backdrop";
    document.body.appendChild(backdrop);

    requestAnimationFrame(() => {
      backdrop.classList.add("is-visible");
    });

    backdrop.addEventListener("click", () => {
      this.hide();
    });

    this._backdrop = backdrop;
  }

  _removeBackdrop() {
    const backdrop = this._backdrop;
    if (!backdrop) return;

    backdrop.classList.remove("is-visible");
    const remove = () => {
      if (backdrop.parentNode) {
        backdrop.parentNode.removeChild(backdrop);
      }
    };

    backdrop.addEventListener("transitionend", remove, { once: true });
    this._backdrop = null;
  }

  _handleKeydown(event) {
    if (event.key === "Escape") {
      event.preventDefault();
      this.hide();
    }
  }

  static getInstance(element) {
    return _offcanvasInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Offcanvas(element, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Toast                                                                      */
/* -------------------------------------------------------------------------- */

class Toast {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Toast: element is required");
    }
    this._element = element;
    this._isVisible = element.classList.contains("is-visible");
    this._options = Object.assign(
      {
        autoHide: true,
        delay: 5000
      },
      options
    );
    this._timeoutId = null;

    _toastInstances.set(element, this);
  }

  show() {
    if (this._isVisible) return;

    this._isVisible = true;
    this._element.classList.add("is-visible");
    this._element.setAttribute("role", "status");
    this._element.setAttribute("aria-live", "polite");

    _dispatchEvent(this._element, "rg:toast:show", { instance: this });

    if (this._options.autoHide) {
      this._clearTimer();
      this._timeoutId = setTimeout(() => {
        this.hide();
      }, this._options.delay);
    }
  }

  hide() {
    if (!this._isVisible) return;

    this._isVisible = false;
    this._element.classList.remove("is-visible");
    this._element.removeAttribute("aria-live");

    this._clearTimer();
    _dispatchEvent(this._element, "rg:toast:hide", { instance: this });
  }

  _clearTimer() {
    if (this._timeoutId) {
      clearTimeout(this._timeoutId);
      this._timeoutId = null;
    }
  }

  static getInstance(element) {
    return _toastInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Toast(element, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Tooltip                                                                    */
/* -------------------------------------------------------------------------- */

class Tooltip {
  constructor(trigger, options = {}) {
    if (!trigger) {
      throw new Error("Rarog.Tooltip: trigger element is required");
    }

    this._trigger = trigger;
    this._options = Object.assign(
      {
        placement: trigger.getAttribute("data-rg-placement") || "top"
      },
      options
    );

    this._tooltip = null;
    this._isVisible = false;
    this._originalTitle =
      trigger.getAttribute("data-rg-title") || trigger.getAttribute("title") || "";

    if (this._originalTitle) {
      trigger.setAttribute("data-rg-original-title", this._originalTitle);
      trigger.removeAttribute("title");
    }

    this._onMouseEnter = () => this.show();
    this._onMouseLeave = () => this.hide();
    this._onFocus = () => this.show();
    this._onBlur = () => this.hide();

    trigger.addEventListener("mouseenter", this._onMouseEnter);
    trigger.addEventListener("mouseleave", this._onMouseLeave);
    trigger.addEventListener("focus", this._onFocus);
    trigger.addEventListener("blur", this._onBlur);

    _tooltipInstances.set(trigger, this);
  }

  _createTooltip() {
    if (this._tooltip || typeof document === "undefined") return;
    const text = this._originalTitle || this._trigger.getAttribute("aria-label");
    if (!text) return;

    const el = document.createElement("div");
    el.className = "tooltip";
    el.dataset.rgPlacement = this._options.placement;

    const inner = document.createElement("div");
    inner.className = "tooltip-inner";
    inner.textContent = text;
    el.appendChild(inner);

    document.body.appendChild(el);
    this._tooltip = el;
  }

  show() {
    if (this._isVisible || typeof document === "undefined") return;

    this._createTooltip();
    if (!this._tooltip) return;

    this._position();
    this._tooltip.classList.add("is-visible");
    this._trigger.setAttribute("aria-describedby", this._tooltip.id || "");
    this._isVisible = true;

    _dispatchEvent(this._tooltip, "rg:tooltip:show", { instance: this });
  }

  hide() {
    if (!this._isVisible) return;
    if (!this._tooltip) return;

    this._tooltip.classList.remove("is-visible");
    this._trigger.removeAttribute("aria-describedby");
    this._isVisible = false;

    _dispatchEvent(this._tooltip, "rg:tooltip:hide", { instance: this });
  }

  _position() {
    if (!this._tooltip || typeof window === "undefined") return;

    const triggerRect = this._trigger.getBoundingClientRect();
    const tooltipRect = this._tooltip.getBoundingClientRect();
    const scrollY = window.scrollY || window.pageYOffset;
    const scrollX = window.scrollX || window.pageXOffset;

    let top = 0;
    let left = 0;

    if (this._options.placement === "bottom") {
      top = triggerRect.bottom + 8 + scrollY;
      left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2 + scrollX;
    } else {
      // top (default)
      top = triggerRect.top - tooltipRect.height - 8 + scrollY;
      left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2 + scrollX;
    }

    this._tooltip.style.top = `${Math.round(top)}px`;
    this._tooltip.style.left = `${Math.round(left)}px`;
  }

  static getInstance(trigger) {
    return _tooltipInstances.get(trigger) || null;
  }

  static getOrCreate(trigger, options) {
    return this.getInstance(trigger) || new Tooltip(trigger, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Popover                                                                    */
/* -------------------------------------------------------------------------- */

class Popover {
  constructor(trigger, options = {}) {
    if (!trigger) {
      throw new Error("Rarog.Popover: trigger element is required");
    }

    this._trigger = trigger;
    this._options = Object.assign(
      {
        placement: trigger.getAttribute("data-rg-placement") || "top",
        title: trigger.getAttribute("data-rg-popover-title") || "",
        content: trigger.getAttribute("data-rg-popover-content") || ""
      },
      options
    );

    this._popover = null;
    this._isVisible = false;

    this._onClick = event => {
      event.preventDefault();
      this.toggle();
    };

    trigger.addEventListener("click", this._onClick);
    _popoverInstances.set(trigger, this);
  }

  _createPopover() {
    if (this._popover || typeof document === "undefined") return;

    const el = document.createElement("div");
    el.className = "popover";
    el.dataset.rgPlacement = this._options.placement;

    if (this._options.title) {
      const header = document.createElement("div");
      header.className = "popover-header";
      header.textContent = this._options.title;
      el.appendChild(header);
    }

    const body = document.createElement("div");
    body.className = "popover-body";
    body.textContent = this._options.content;
    el.appendChild(body);

    document.body.appendChild(el);
    this._popover = el;
  }

  show() {
    if (this._isVisible) return;

    this._createPopover();
    if (!this._popover) return;

    this._position();
    this._popover.classList.add("is-visible");
    this._trigger.setAttribute("aria-expanded", "true");

    this._isVisible = true;
    _dispatchEvent(this._popover, "rg:popover:show", { instance: this });
  }

  hide() {
    if (!this._isVisible || !this._popover) return;

    this._popover.classList.remove("is-visible");
    this._trigger.setAttribute("aria-expanded", "false");

    this._isVisible = false;
    _dispatchEvent(this._popover, "rg:popover:hide", { instance: this });
  }

  toggle() {
    if (this._isVisible) {
      this.hide();
    } else {
      this.show();
    }
  }

  _position() {
    if (!this._popover || typeof window === "undefined") return;

    const triggerRect = this._trigger.getBoundingClientRect();
    const popRect = this._popover.getBoundingClientRect();
    const scrollY = window.scrollY || window.pageYOffset;
    const scrollX = window.scrollX || window.pageXOffset;

    let top = 0;
    let left = 0;

    if (this._options.placement === "bottom") {
      top = triggerRect.bottom + 8 + scrollY;
      left = triggerRect.left + triggerRect.width / 2 - popRect.width / 2 + scrollX;
    } else {
      // top (default)
      top = triggerRect.top - popRect.height - 8 + scrollY;
      left = triggerRect.left + triggerRect.width / 2 - popRect.width / 2 + scrollX;
    }

    this._popover.style.top = `${Math.round(top)}px`;
    this._popover.style.left = `${Math.round(left)}px`;
  }

  static getInstance(trigger) {
    return _popoverInstances.get(trigger) || null;
  }

  static getOrCreate(trigger, options) {
    return this.getInstance(trigger) || new Popover(trigger, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Event wrappers for existing components                                     */
/* -------------------------------------------------------------------------- */

function _attachEventsForClass(klass, getElement, name) {
  if (!klass || typeof klass.prototype !== "object") return;

  ["show", "hide"].forEach(methodName => {
    const original = klass.prototype[methodName];
    if (typeof original !== "function") return;

    klass.prototype[methodName] = function (...args) {
      const el = getElement(this);
      if (methodName === "show") {
        _dispatchEvent(el, `rg:${name}:show`, { instance: this });
      }

      const result = original.apply(this, args);

      if (methodName === "hide") {
        _dispatchEvent(el, `rg:${name}:hide`, { instance: this });
      }

      return result;
    };
  });
}

// Подвязываем события для Dropdown/Collapse/Modal
_attachEventsForClass(Dropdown, inst => inst._menu || inst._trigger, "dropdown");
_attachEventsForClass(Collapse, inst => inst._target, "collapse");
_attachEventsForClass(Modal, inst => inst._element, "modal");
/* -------------------------------------------------------------------------- */
/* Carousel                                                                   */
/* -------------------------------------------------------------------------- */

const _carouselInstances = new WeakMap();

class Carousel {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Carousel: element is required");
    }

    this._element = element;
    this._options = Object.assign(
      {
        interval: Number(element.getAttribute("data-rg-interval")) || 5000,
        autoplay: element.getAttribute("data-rg-autoplay") === "true",
        pauseOnHover: true
      },
      options
    );

    this._inner = element.querySelector(".carousel-inner");
    this._items = this._inner ? Array.from(this._inner.children) : [];
    this._indicators = Array.from(
      element.querySelectorAll("[data-rg-target='" + (element.id ? "#" + element.id : "") + "'][data-rg-slide-to]")
    );
    this._currentIndex = this._items.findIndex(item => item.classList.contains("is-active"));
    if (this._currentIndex < 0) this._currentIndex = 0;

    this._intervalId = null;

    if (!this._inner || this._items.length === 0) {
      _debugWarn("Rarog.Carousel: .carousel-inner with items not found", element);
    }

    this._onMouseEnter = () => {
      if (this._options.pauseOnHover) {
        this.pause();
      }
    };
    this._onMouseLeave = () => {
      if (this._options.autoplay) {
        this.play();
      }
    };

    this._onTouchStart = event => {
      this._touchStartX = event.touches ? event.touches[0].clientX : event.clientX;
    };
    this._onTouchEnd = event => {
      const endX = event.changedTouches ? event.changedTouches[0].clientX : event.clientX;
      const deltaX = endX - (this._touchStartX || 0);
      const threshold = 40;
      if (deltaX > threshold) {
        this.prev();
      } else if (deltaX < -threshold) {
        this.next();
      }
    };

    element.addEventListener("mouseenter", this._onMouseEnter);
    element.addEventListener("mouseleave", this._onMouseLeave);
    element.addEventListener("touchstart", this._onTouchStart, { passive: true });
    element.addEventListener("touchend", this._onTouchEnd);

    if (this._options.autoplay) {
      this.play();
    }

    _carouselInstances.set(element, this);
  }

  _updateIndicators() {
    if (!this._indicators.length || !this._element.id) return;
    this._indicators.forEach((indicator, index) => {
      if (index === this._currentIndex) {
        indicator.classList.add("is-active");
        indicator.setAttribute("aria-current", "true");
      } else {
        indicator.classList.remove("is-active");
        indicator.removeAttribute("aria-current");
      }
    });
  }

  _updateSlides() {
    if (!this._items.length) return;

    this._items.forEach((item, index) => {
      if (index === this._currentIndex) {
        item.classList.add("is-active");
        item.removeAttribute("aria-hidden");
      } else {
        item.classList.remove("is-active");
        item.setAttribute("aria-hidden", "true");
      }
    });

    this._updateIndicators();
  }

  next() {
    if (!this._items.length) return;
    this._currentIndex = (this._currentIndex + 1) % this._items.length;
    this._updateSlides();
    _dispatchEvent(this._element, "rg:carousel:next", { instance: this, index: this._currentIndex });
  }

  prev() {
    if (!this._items.length) return;
    this._currentIndex = (this._currentIndex - 1 + this._items.length) % this._items.length;
    this._updateSlides();
    _dispatchEvent(this._element, "rg:carousel:prev", { instance: this, index: this._currentIndex });
  }

  goTo(index) {
    if (!this._items.length) return;
    const normalized = Math.max(0, Math.min(this._items.length - 1, index));
    this._currentIndex = normalized;
    this._updateSlides();
    _dispatchEvent(this._element, "rg:carousel:goto", { instance: this, index: this._currentIndex });
  }

  play() {
    if (!this._options.interval || this._intervalId) return;
    this._intervalId = window.setInterval(() => this.next(), this._options.interval);
    _dispatchEvent(this._element, "rg:carousel:play", { instance: this });
  }

  pause() {
    if (this._intervalId) {
      window.clearInterval(this._intervalId);
      this._intervalId = null;
      _dispatchEvent(this._element, "rg:carousel:pause", { instance: this });
    }
  }

  destroy() {
    this.pause();
    this._element.removeEventListener("mouseenter", this._onMouseEnter);
    this._element.removeEventListener("mouseleave", this._onMouseLeave);
    this._element.removeEventListener("touchstart", this._onTouchStart);
    this._element.removeEventListener("touchend", this._onTouchEnd);
    _carouselInstances.delete(this._element);
  }

  static getInstance(element) {
    return _carouselInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Carousel(element, options);
  }
}

/* -------------------------------------------------------------------------- */
/* Stepper / Wizard                                                           */
/* -------------------------------------------------------------------------- */

const _stepperInstances = new WeakMap();

class Stepper {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Stepper: element is required");
    }

    this._element = element;
    this._options = Object.assign(
      {
        activeIndex: 0
      },
      options
    );

    this._steps = Array.from(element.querySelectorAll(".stepper-step"));
    this._contents = Array.from(element.querySelectorAll(".stepper-content"));
    this._currentIndex = this._options.activeIndex || 0;

    if (!this._steps.length) {
      _debugWarn("Rarog.Stepper: no .stepper-step elements found", element);
    }

    this._onClick = event => {
      const trigger = event.target.closest("[data-rg-step-to]");
      if (!trigger || !element.contains(trigger)) return;
      const value = trigger.getAttribute("data-rg-step-to");
      if (value == null) return;
      const index = parseInt(value, 10);
      if (!Number.isNaN(index)) {
        this.goTo(index);
      }
    };

    element.addEventListener("click", this._onClick);

    this._update();

    _stepperInstances.set(element, this);
  }

  _update() {
    this._steps.forEach((step, index) => {
      if (index === this._currentIndex) {
        step.classList.add("is-active");
        step.setAttribute("aria-current", "step");
      } else {
        step.classList.remove("is-active");
        step.removeAttribute("aria-current");
      }
    });

    this._contents.forEach((content, index) => {
      if (index === this._currentIndex) {
        content.classList.add("is-active");
        content.removeAttribute("hidden");
        content.setAttribute("aria-hidden", "false");
      } else {
        content.classList.remove("is-active");
        content.setAttribute("hidden", "");
        content.setAttribute("aria-hidden", "true");
      }
    });
  }

  next() {
    const nextIndex = Math.min(this._steps.length - 1, this._currentIndex + 1);
    if (nextIndex === this._currentIndex) return;
    this._currentIndex = nextIndex;
    this._update();
    _dispatchEvent(this._element, "rg:stepper:next", { instance: this, index: this._currentIndex });
  }

  prev() {
    const prevIndex = Math.max(0, this._currentIndex - 1);
    if (prevIndex === this._currentIndex) return;
    this._currentIndex = prevIndex;
    this._update();
    _dispatchEvent(this._element, "rg:stepper:prev", { instance: this, index: this._currentIndex });
  }

  goTo(index) {
    const normalized = Math.max(0, Math.min(this._steps.length - 1, index));
    if (normalized === this._currentIndex) return;
    this._currentIndex = normalized;
    this._update();
    _dispatchEvent(this._element, "rg:stepper:goto", { instance: this, index: this._currentIndex });
  }

  reset() {
    this._currentIndex = 0;
    this._update();
    _dispatchEvent(this._element, "rg:stepper:reset", { instance: this, index: this._currentIndex });
  }

  destroy() {
    this._element.removeEventListener("click", this._onClick);
    _stepperInstances.delete(this._element);
  }

  static getInstance(element) {
    return _stepperInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Stepper(element, options);
  }
}

class Datepicker {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Datepicker: element is required");
    }

    const root =
      element.matches("[data-rg-datepicker]") || element.classList.contains("rg-datepicker")
        ? element
        : element.closest("[data-rg-datepicker], .rg-datepicker") || element;

    this._element = root;
    this._options = Object.assign(
      {
        format: root.getAttribute("data-rg-format") || "yyyy-MM-dd"
      },
      options
    );

    this._input =
      root.tagName === "INPUT"
        ? root
        : root.querySelector("input[type='date'], input[type='text'], input");

    if (!this._input) {
      _debugWarn("Rarog.Datepicker: input not found", element);
    }

    this._popup = root.querySelector(".rg-datepicker-popup");
    if (!this._popup && typeof document !== "undefined") {
      this._popup = document.createElement("div");
      this._popup.className = "rg-datepicker-popup";
      this._popup.innerHTML =
        '<div class="rg-datepicker-header">' +
        '  <button type="button" class="rg-datepicker-prev" aria-label="Previous month">‹</button>' +
        '  <div class="rg-datepicker-title"></div>' +
        '  <button type="button" class="rg-datepicker-next" aria-label="Next month">›</button>' +
        "</div>" +
        '<div class="rg-datepicker-weekdays">' +
        "  <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>" +
        "</div>" +
        '<div class="rg-datepicker-grid"></div>';
      this._element.appendChild(this._popup);
    }

    this._grid = this._popup ? this._popup.querySelector(".rg-datepicker-grid") : null;
    this._title = this._popup ? this._popup.querySelector(".rg-datepicker-title") : null;
    this._currentDate = new Date();
    this._selectedDate = null;
    this._isOpen = false;

    this._onInputClick = event => {
      event.preventDefault();
      this.toggle();
    };

    this._onDocumentClick = event => {
      if (!this._isOpen) return;
      if (!this._element.contains(event.target)) {
        this.hide();
      }
    };

    this._onPrevClick = () => {
      const month = this._currentDate.getMonth();
      this._currentDate.setMonth(month - 1);
      this._render();
    };

    this._onNextClick = () => {
      const month = this._currentDate.getMonth();
      this._currentDate.setMonth(month + 1);
      this._render();
    };

    this._onGridClick = event => {
      const target = event.target.closest("[data-rg-date]");
      if (!target) return;
      const value = target.getAttribute("data-rg-date");
      if (!value) return;
      const date = new Date(value);
      if (isNaN(date.getTime())) {
        this._selectedDate = value;
      } else {
        this._selectedDate = date;
      }
      this._updateInputFromSelected();
      this.hide();
      _dispatchEvent(this._element, "rg:datepicker:select", { value: this._input ? this._input.value : null });
    };

    if (this._input) {
      this._input.addEventListener("focus", this._onInputClick);
      this._input.addEventListener("click", this._onInputClick);
    }
    if (typeof document !== "undefined") {
      document.addEventListener("click", this._onDocumentClick);
    }
    if (this._popup) {
      const prevBtn = this._popup.querySelector(".rg-datepicker-prev");
      const nextBtn = this._popup.querySelector(".rg-datepicker-next");
      if (prevBtn) prevBtn.addEventListener("click", this._onPrevClick);
      if (nextBtn) nextBtn.addEventListener("click", this._onNextClick);
      if (this._grid) this._grid.addEventListener("click", this._onGridClick);
    }

    this._render();
    _datepickerInstances.set(this._element, this);
  }

  _formatDate(date) {
    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return "";
    }
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    // Пока поддерживаем только yyyy-MM-dd
    return `${year}-${month}-${day}`;
  }

  _updateInputFromSelected() {
    if (!this._input) return;
    if (this._selectedDate instanceof Date) {
      this._input.value = this._formatDate(this._selectedDate);
    } else if (typeof this._selectedDate === "string") {
      this._input.value = this._selectedDate;
    }
  }

  _render() {
    if (!this._popup || !this._grid || !this._title) return;

    const year = this._currentDate.getFullYear();
    const month = this._currentDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const firstWeekday = (firstDay.getDay() + 6) % 7; // 0 = Monday
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    this._title.textContent = this._currentDate.toLocaleDateString(undefined, {
      month: "long",
      year: "numeric"
    });

    this._grid.innerHTML = "";

    for (let i = 0; i < firstWeekday; i++) {
      const placeholder = document.createElement("div");
      placeholder.className = "rg-datepicker-day rg-datepicker-empty";
      this._grid.appendChild(placeholder);
    }

    const today = new Date();
    const todayY = today.getFullYear();
    const todayM = today.getMonth();
    const todayD = today.getDate();

    for (let day = 1; day <= daysInMonth; day++) {
      const cellDate = new Date(year, month, day);
      const button = document.createElement("button");
      button.type = "button";
      button.className = "rg-datepicker-day";
      button.textContent = String(day);
      button.setAttribute("data-rg-date", this._formatDate(cellDate));

      if (
        year === todayY &&
        month === todayM &&
        day === todayD
      ) {
        button.classList.add("is-today");
      }

      if (this._input && this._input.value) {
        const currentValue = this._input.value;
        const cellValue = this._formatDate(cellDate);
        if (currentValue === cellValue) {
          button.classList.add("is-selected");
        }
      }

      this._grid.appendChild(button);
    }
  }

  show() {
    if (!this._popup) return;
    this._popup.removeAttribute("hidden");
    this._popup.classList.add("is-open");
    this._isOpen = true;
    _dispatchEvent(this._element, "rg:datepicker:show", {});
  }

  hide() {
    if (!this._popup) return;
    this._popup.setAttribute("hidden", "true");
    this._popup.classList.remove("is-open");
    this._isOpen = false;
    _dispatchEvent(this._element, "rg:datepicker:hide", {});
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  dispose() {
    if (this._input) {
      this._input.removeEventListener("focus", this._onInputClick);
      this._input.removeEventListener("click", this._onInputClick);
    }
    if (typeof document !== "undefined") {
      document.removeEventListener("click", this._onDocumentClick);
    }
    if (this._popup && this._grid) {
      this._grid.removeEventListener("click", this._onGridClick);
      const prevBtn = this._popup.querySelector(".rg-datepicker-prev");
      const nextBtn = this._popup.querySelector(".rg-datepicker-next");
      if (prevBtn) prevBtn.removeEventListener("click", this._onPrevClick);
      if (nextBtn) nextBtn.removeEventListener("click", this._onNextClick);
    }
    _datepickerInstances.delete(this._element);
  }

  static getInstance(element) {
    return _datepickerInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Datepicker(element, options);
  }
}

class DatetimePicker extends Datepicker {
  constructor(element, options = {}) {
    super(element, options);
    _datetimePickerInstances.set(this._element, this);
  }

  static getInstance(element) {
    return _datetimePickerInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new DatetimePicker(element, options);
  }
}

class Select {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Select: element is required");
    }

    this._element = element;
    this._options = Object.assign(
      {
        multiple: element.getAttribute("data-rg-multiple") === "true"
      },
      options
    );

    this._toggle =
      element.querySelector("[data-rg-select-toggle]") ||
      element.querySelector(".rg-select-toggle") ||
      element.querySelector("button");
    this._menu =
      element.querySelector("[data-rg-select-menu]") ||
      element.querySelector(".rg-select-menu");
    this._items = this._menu ? Array.from(this._menu.querySelectorAll("[data-rg-value], .rg-select-option")) : [];
    this._hiddenInput =
      element.querySelector("input[type='hidden']") ||
      element.querySelector("select.rg-select-native");

    this._isOpen = false;
    this._activeIndex = -1;
    this._value = this._options.multiple ? new Set() : null;

    this._onDocumentClick = event => {
      if (!this._isOpen) return;
      if (!this._element.contains(event.target)) {
        this.hide();
      }
    };

    this._onToggleClick = event => {
      event.preventDefault();
      this.toggle();
    };

    this._onItemClick = event => {
      const item = event.currentTarget;
      this._selectItem(item);
    };

    this._onKeyDown = event => {
      if (!this._isOpen && (event.key === "ArrowDown" || event.key === "ArrowUp" || event.key === "Enter" || event.key === " ")) {
        event.preventDefault();
        this.show();
        return;
      }

      if (!this._isOpen) return;

      if (event.key === "ArrowDown" || event.key === "ArrowUp") {
        event.preventDefault();
        this._moveActive(event.key === "ArrowDown" ? 1 : -1);
      } else if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        const current = this._items[this._activeIndex];
        if (current) this._selectItem(current);
      } else if (event.key === "Escape") {
        this.hide();
      }
    };

    if (this._toggle) {
      this._toggle.addEventListener("click", this._onToggleClick);
      this._toggle.addEventListener("keydown", this._onKeyDown);
      this._toggle.setAttribute("aria-haspopup", "listbox");
      this._toggle.setAttribute("aria-expanded", "false");
    }
    if (this._menu) {
      this._menu.setAttribute("role", "listbox");
    }

    this._items.forEach((item, index) => {
      item.setAttribute("role", "option");
      item.addEventListener("click", this._onItemClick);
      if (item.classList.contains("is-selected") || item.getAttribute("aria-selected") === "true") {
        this._activeIndex = index;
        if (this._options.multiple) {
          this._value.add(this._getItemValue(item));
        } else {
          this._value = this._getItemValue(item);
        }
      }
    });

    if (typeof document !== "undefined") {
      document.addEventListener("click", this._onDocumentClick);
    }

    this._syncHidden();
    _selectInstances.set(this._element, this);
  }

  _getItemValue(item) {
    const explicit = item.getAttribute("data-rg-value");
    if (explicit != null) return explicit;
    if (this._hiddenInput && this._hiddenInput.tagName === "SELECT") {
      const optionIndex = this._items.indexOf(item);
      const option = this._hiddenInput.options[optionIndex];
      return option ? option.value : item.textContent.trim();
    }
    return item.textContent.trim();
  }

  _syncHidden() {
    if (!this._hiddenInput) return;

    if (this._hiddenInput.tagName === "SELECT") {
      const values = this._options.multiple
        ? Array.from(this._value)
        : this._value != null
        ? [this._value]
        : [];
      Array.from(this._hiddenInput.options).forEach(option => {
        option.selected = values.includes(option.value);
      });
    } else {
      if (this._options.multiple) {
        this._hiddenInput.value = Array.from(this._value).join(",");
      } else {
        this._hiddenInput.value = this._value != null ? String(this._value) : "";
      }
    }
  }

  _updateToggleLabel() {
    if (!this._toggle) return;
    const labelSpan =
      this._toggle.querySelector(".rg-select-label") || this._toggle;
    let text = "";
    if (this._options.multiple) {
      const values = Array.from(this._value);
      text = values.length ? values.join(", ") : this._toggle.getAttribute("data-rg-placeholder") || "";
    } else {
      text =
        this._value != null
          ? String(this._value)
          : this._toggle.getAttribute("data-rg-placeholder") || "";
    }
    labelSpan.textContent = text;
  }

  _moveActive(delta) {
    if (!this._items.length) return;
    let next = this._activeIndex + delta;
    if (next < 0) next = this._items.length - 1;
    if (next >= this._items.length) next = 0;
    this._setActiveIndex(next);
  }

  _setActiveIndex(index) {
    this._items.forEach((item, i) => {
      if (i === index) {
        item.classList.add("is-active");
        item.setAttribute("aria-selected", "true");
        item.scrollIntoView({ block: "nearest" });
      } else {
        item.classList.remove("is-active");
        if (!item.classList.contains("is-selected")) {
          item.setAttribute("aria-selected", "false");
        }
      }
    });
    this._activeIndex = index;
  }

  _selectItem(item) {
    const value = this._getItemValue(item);

    if (this._options.multiple) {
      const isSelected = this._value.has(value);
      if (isSelected) {
        this._value.delete(value);
        item.classList.remove("is-selected");
        item.setAttribute("aria-selected", "false");
      } else {
        this._value.add(value);
        item.classList.add("is-selected");
        item.setAttribute("aria-selected", "true");
      }
    } else {
      this._value = value;
      this._items.forEach(it => {
        if (it === item) {
          it.classList.add("is-selected");
          it.setAttribute("aria-selected", "true");
        } else {
          it.classList.remove("is-selected");
          it.setAttribute("aria-selected", "false");
        }
      });
      this.hide();
    }

    this._syncHidden();
    this._updateToggleLabel();
    _dispatchEvent(this._element, "rg:select:change", { value: this._value });
  }

  show() {
    if (!this._menu) return;
    this._menu.removeAttribute("hidden");
    this._menu.classList.add("is-open");
    if (this._toggle) {
      this._toggle.setAttribute("aria-expanded", "true");
    }
    this._isOpen = true;
    if (this._activeIndex === -1 && this._items.length) {
      this._setActiveIndex(0);
    }
  }

  hide() {
    if (!this._menu) return;
    this._menu.setAttribute("hidden", "true");
    this._menu.classList.remove("is-open");
    if (this._toggle) {
      this._toggle.setAttribute("aria-expanded", "false");
    }
    this._isOpen = false;
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  dispose() {
    if (this._toggle) {
      this._toggle.removeEventListener("click", this._onToggleClick);
      this._toggle.removeEventListener("keydown", this._onKeyDown);
    }
    this._items.forEach(item => {
      item.removeEventListener("click", this._onItemClick);
    });
    if (typeof document !== "undefined") {
      document.removeEventListener("click", this._onDocumentClick);
    }
    _selectInstances.delete(this._element);
  }

  static getInstance(element) {
    return _selectInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Select(element, options);
  }
}

class Combobox {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.Combobox: element is required");
    }

    this._element = element;
    this._options = options;
    this._input =
      element.querySelector(".rg-combobox-input") ||
      element.querySelector("input[type='text'], input[type='search']");
    this._toggle =
      element.querySelector("[data-rg-combobox-toggle]") ||
      element.querySelector(".rg-combobox-toggle");
    this._list =
      element.querySelector(".rg-combobox-list") ||
      element.querySelector("[data-rg-combobox-list]");
    this._items = this._list ? Array.from(this._list.querySelectorAll("[data-rg-value], .rg-combobox-option")) : [];
    this._hiddenInput = element.querySelector("input[type='hidden']");

    this._isOpen = false;
    this._activeIndex = -1;

    this._onInput = event => {
      const query = event.target.value.toLowerCase();
      this._filter(query);
      if (!this._isOpen) this.show();
    };

    this._onKeyDown = event => {
      if (event.key === "ArrowDown" || event.key === "ArrowUp") {
        event.preventDefault();
        const delta = event.key === "ArrowDown" ? 1 : -1;
        this._moveActive(delta);
      } else if (event.key === "Enter") {
        event.preventDefault();
        const current = this._items[this._activeIndex];
        if (current) this._selectItem(current);
      } else if (event.key === "Escape") {
        this.hide();
      }
    };

    this._onToggleClick = event => {
      event.preventDefault();
      this.toggle();
      if (this._isOpen && this._input) {
        this._input.focus();
      }
    };

    this._onItemClick = event => {
      const item = event.currentTarget;
      this._selectItem(item);
    };

    this._onDocumentClick = event => {
      if (!this._isOpen) return;
      if (!this._element.contains(event.target)) {
        this.hide();
      }
    };

    if (this._input) {
      this._input.addEventListener("input", this._onInput);
      this._input.addEventListener("keydown", this._onKeyDown);
      this._input.setAttribute("role", "combobox");
      this._input.setAttribute("aria-autocomplete", "list");
    }
    if (this._toggle) {
      this._toggle.addEventListener("click", this._onToggleClick);
    }
    if (this._list) {
      this._list.setAttribute("role", "listbox");
    }
    this._items.forEach(item => {
      item.setAttribute("role", "option");
      item.addEventListener("click", this._onItemClick);
    });

    if (typeof document !== "undefined") {
      document.addEventListener("click", this._onDocumentClick);
    }

    _comboboxInstances.set(this._element, this);
  }

  _filter(query) {
    this._items.forEach(item => {
      const text = item.textContent.toLowerCase();
      const match = text.indexOf(query) !== -1;
      item.style.display = match ? "" : "none";
    });
    if (query) {
      const firstVisible = this._items.find(item => item.style.display !== "none");
      if (firstVisible) {
        this._activeIndex = this._items.indexOf(firstVisible);
        this._setActiveIndex(this._activeIndex);
      }
    }
  }

  _moveActive(delta) {
    const visibleItems = this._items.filter(item => item.style.display !== "none");
    if (!visibleItems.length) return;

    let currentItem = this._items[this._activeIndex];
    if (!currentItem || currentItem.style.display === "none") {
      currentItem = visibleItems[0];
    }
    let idx = visibleItems.indexOf(currentItem);
    idx += delta;
    if (idx < 0) idx = visibleItems.length - 1;
    if (idx >= visibleItems.length) idx = 0;

    const target = visibleItems[idx];
    this._activeIndex = this._items.indexOf(target);
    this._setActiveIndex(this._activeIndex);
  }

  _setActiveIndex(index) {
    this._items.forEach((item, i) => {
      if (i === index) {
        item.classList.add("is-active");
        item.setAttribute("aria-selected", "true");
        item.scrollIntoView({ block: "nearest" });
      } else {
        item.classList.remove("is-active");
        if (!item.classList.contains("is-selected")) {
          item.setAttribute("aria-selected", "false");
        }
      }
    });
  }

  _getItemValue(item) {
    const explicit = item.getAttribute("data-rg-value");
    return explicit != null ? explicit : item.textContent.trim();
  }

  _selectItem(item) {
    const value = this._getItemValue(item);
    if (this._input) {
      this._input.value = item.textContent.trim();
    }
    if (this._hiddenInput) {
      this._hiddenInput.value = value;
    }
    this._items.forEach(it => {
      if (it === item) {
        it.classList.add("is-selected");
        it.setAttribute("aria-selected", "true");
      } else {
        it.classList.remove("is-selected");
        it.setAttribute("aria-selected", "false");
      }
    });
    this.hide();
    _dispatchEvent(this._element, "rg:combobox:change", { value });
  }

  show() {
    if (!this._list) return;
    this._list.removeAttribute("hidden");
    this._list.classList.add("is-open");
    this._isOpen = true;
    _dispatchEvent(this._element, "rg:combobox:open", {});
  }

  hide() {
    if (!this._list) return;
    this._list.setAttribute("hidden", "true");
    this._list.classList.remove("is-open");
    this._isOpen = false;
    _dispatchEvent(this._element, "rg:combobox:close", {});
  }

  toggle() {
    if (this._isOpen) {
      this.hide();
    } else {
      this.show();
    }
  }

  dispose() {
    if (this._input) {
      this._input.removeEventListener("input", this._onInput);
      this._input.removeEventListener("keydown", this._onKeyDown);
    }
    if (this._toggle) {
      this._toggle.removeEventListener("click", this._onToggleClick);
    }
    this._items.forEach(item => {
      item.removeEventListener("click", this._onItemClick);
    });
    if (typeof document !== "undefined") {
      document.removeEventListener("click", this._onDocumentClick);
    }
    _comboboxInstances.delete(this._element);
  }

  static getInstance(element) {
    return _comboboxInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new Combobox(element, options);
  }
}

class TagsInput {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.TagsInput: element is required");
    }

    this._element = element;
    this._options = options;
    this._tagsContainer =
      element.querySelector(".rg-tags") ||
      element.querySelector("[data-rg-tags-container]");
    this._input =
      element.querySelector(".rg-tags-input-input") ||
      element.querySelector("input[type='text'], input[type='search']");
    this._hiddenInput = element.querySelector("input[type='hidden']");

    this._tags = [];

    this._onKeyDown = event => {
      if (event.key === "Enter" || event.key === ",") {
        event.preventDefault();
        const value = (this._input ? this._input.value : "").trim();
        if (value) {
          this.addTag(value);
          this._input.value = "";
        }
      } else if (event.key === "Backspace" && this._input && this._input.value === "") {
        const last = this._tags[this._tags.length - 1];
        if (last) {
          this.removeTag(last.value);
        }
      }
    };

    this._onClick = event => {
      if (this._input) {
        this._input.focus();
      }
    };

    this._onTagRemoveClick = event => {
      const button = event.currentTarget;
      const tagEl = button.closest("[data-rg-tag-value]");
      if (!tagEl) return;
      const value = tagEl.getAttribute("data-rg-tag-value");
      this.removeTag(value);
    };

    if (this._input) {
      this._input.addEventListener("keydown", this._onKeyDown);
    }
    this._element.addEventListener("click", this._onClick);

    if (this._hiddenInput && this._hiddenInput.value) {
      const initial = this._hiddenInput.value
        .split(",")
        .map(v => v.trim())
        .filter(Boolean);
      initial.forEach(value => this._createTagElement(value));
    }

    _tagsInputInstances.set(this._element, this);
  }

  _createTagElement(value) {
    if (!this._tagsContainer && typeof document !== "undefined") {
      this._tagsContainer = document.createElement("div");
      this._tagsContainer.className = "rg-tags";
      this._element.insertBefore(this._tagsContainer, this._input || null);
    }
    if (!this._tagsContainer || typeof document === "undefined") return;

    const tagEl = document.createElement("span");
    tagEl.className = "rg-tag";
    tagEl.setAttribute("data-rg-tag-value", value);

    const label = document.createElement("span");
    label.className = "rg-tag-label";
    label.textContent = value;

    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.className = "rg-tag-remove";
    removeBtn.setAttribute("aria-label", "Remove tag");
    removeBtn.innerHTML = "×";
    removeBtn.addEventListener("click", this._onTagRemoveClick);

    tagEl.appendChild(label);
    tagEl.appendChild(removeBtn);

    this._tagsContainer.appendChild(tagEl);
    this._tags.push({ value, element: tagEl });
  }

  _syncHidden() {
    if (!this._hiddenInput) return;
    this._hiddenInput.value = this._tags.map(t => t.value).join(",");
  }

  addTag(value) {
    if (!value) return;
    if (this._tags.some(t => t.value === value)) return;
    this._createTagElement(value);
    this._syncHidden();
    _dispatchEvent(this._element, "rg:tags-input:change", {
      values: this._tags.map(t => t.value)
    });
  }

  removeTag(value) {
    const idx = this._tags.findIndex(t => t.value === value);
    if (idx === -1) return;
    const tag = this._tags[idx];
    if (tag.element && tag.element.parentNode) {
      tag.element.parentNode.removeChild(tag.element);
    }
    this._tags.splice(idx, 1);
    this._syncHidden();
    _dispatchEvent(this._element, "rg:tags-input:change", {
      values: this._tags.map(t => t.value)
    });
  }

  clear() {
    this._tags.forEach(tag => {
      if (tag.element && tag.element.parentNode) {
        tag.element.parentNode.removeChild(tag.element);
      }
    });
    this._tags = [];
    this._syncHidden();
  }

  dispose() {
    if (this._input) {
      this._input.removeEventListener("keydown", this._onKeyDown);
    }
    this._element.removeEventListener("click", this._onClick);
    this._tags.forEach(tag => {
      const btn = tag.element.querySelector(".rg-tag-remove");
      if (btn) btn.removeEventListener("click", this._onTagRemoveClick);
    });
    this.clear();
    _tagsInputInstances.delete(this._element);
  }

  static getInstance(element) {
    return _tagsInputInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new TagsInput(element, options);
  }
}

class DataTable {
  constructor(element, options = {}) {
    if (!element) {
      throw new Error("Rarog.DataTable: element is required");
    }

    this._element = element;
    this._options = Object.assign(
      {
        pageSize: Number(element.getAttribute("data-rg-page-size")) || 10
      },
      options
    );

    this._table = element.tagName === "TABLE" ? element : element.querySelector("table");
    this._tbody = this._table ? this._table.querySelector("tbody") : null;
    this._rows = this._tbody ? Array.from(this._tbody.rows) : [];

    this._searchInput =
      element.querySelector("[data-rg-table-search]") ||
      element.querySelector("input[data-rg-role='table-search']");
    this._paginationContainer =
      element.querySelector("[data-rg-table-pagination]") ||
      element.querySelector(".rg-table-pagination");

    this._state = {
      search: "",
      sortKey: null,
      sortDir: "asc",
      page: 1
    };

    this._onSearchInput = event => {
      this._state.search = (event.target.value || "").toLowerCase();
      this._state.page = 1;
      this._apply();
    };

    this._onHeaderClick = event => {
      const th = event.currentTarget;
      const key = th.getAttribute("data-rg-sort");
      if (!key) return;

      if (this._state.sortKey === key) {
        this._state.sortDir = this._state.sortDir === "asc" ? "desc" : "asc";
      } else {
        this._state.sortKey = key;
        this._state.sortDir = "asc";
      }

      this._apply();
    };

    this._onPageClick = event => {
      event.preventDefault();
      const page = Number(event.currentTarget.getAttribute("data-rg-page"));
      if (!Number.isNaN(page)) {
        this._state.page = page;
        this._apply({ focusPagination: true });
      }
    };

    if (this._searchInput) {
      this._searchInput.addEventListener("input", this._onSearchInput);
    }

    if (this._table) {
      const sortableHeaders = this._table.querySelectorAll("thead th[data-rg-sort]");
      sortableHeaders.forEach(th => {
        th.classList.add("is-sortable");
        th.addEventListener("click", this._onHeaderClick);
      });
    }

    this._apply();
    _dataTableInstances.set(this._element, this);
  }

  _filterRows() {
    const search = this._state.search;
    if (!search) return this._rows.slice();

    return this._rows.filter(row => {
      const text = row.textContent || "";
      return text.toLowerCase().indexOf(search) !== -1;
    });
  }

  _sortRows(rows) {
    const key = this._state.sortKey;
    if (!key) return rows;

    const dir = this._state.sortDir === "desc" ? -1 : 1;

    const header = this._table.querySelector("thead th[data-rg-sort='" + key + "']");
    const index = header ? Array.from(header.parentNode.children).indexOf(header) : -1;
    if (index === -1) return rows;

    const type = header.getAttribute("data-rg-sort-type") || "string";

    return rows.slice().sort((a, b) => {
      const aCell = a.cells[index];
      const bCell = b.cells[index];
      const aText = aCell ? (aCell.textContent || "").trim() : "";
      const bText = bCell ? (bCell.textContent || "").trim() : "";

      if (type === "number") {
        const aNum = parseFloat(aText.replace(/\s+/g, "").replace(",", "."));
        const bNum = parseFloat(bText.replace(/\s+/g, "").replace(",", "."));
        if (Number.isNaN(aNum) || Number.isNaN(bNum)) {
          return dir * aText.localeCompare(bText);
        }
        if (aNum === bNum) return 0;
        return aNum > bNum ? dir : -dir;
      }

      return dir * aText.localeCompare(bText, undefined, { numeric: true });
    });
  }

  _paginateRows(rows) {
    if (!this._paginationContainer || !this._options.pageSize) {
      return { rows, page: 1, pages: 1 };
    }

    const total = rows.length;
    const pages = Math.max(1, Math.ceil(total / this._options.pageSize));
    let page = this._state.page;
    if (page < 1) page = 1;
    if (page > pages) page = pages;

    const start = (page - 1) * this._options.pageSize;
    const end = start + this._options.pageSize;

    return { rows: rows.slice(start, end), page, pages };
  }

  _renderPagination(page, pages) {
    if (!this._paginationContainer) return;

    this._paginationContainer.innerHTML = "";
    if (pages <= 1) return;

    const list = document.createElement("ul");
    list.className = "pagination";

    for (let i = 1; i <= pages; i++) {
      const li = document.createElement("li");
      li.className = "page-item" + (i === page ? " is-active" : "");

      const link = document.createElement("a");
      link.href = "#";
      link.className = "page-link";
      link.textContent = String(i);
      link.setAttribute("data-rg-page", String(i));
      link.addEventListener("click", this._onPageClick);

      li.appendChild(link);
      list.appendChild(li);
    }

    this._paginationContainer.appendChild(list);
  }

  _apply(options = {}) {
    if (!this._tbody) return;

    let rows = this._filterRows();
    rows = this._sortRows(rows);
    const paginated = this._paginateRows(rows);

    this._tbody.innerHTML = "";
    paginated.rows.forEach(row => {
      this._tbody.appendChild(row);
    });

    this._renderPagination(paginated.page, paginated.pages);

    const header = this._state.sortKey
      ? this._table.querySelector("thead th[data-rg-sort='" + this._state.sortKey + "']")
      : null;
    if (header) {
      this._table
        .querySelectorAll("thead th[data-rg-sort]")
        .forEach(th => th.classList.remove("is-sorted-asc", "is-sorted-desc"));
      header.classList.add(
        this._state.sortDir === "desc" ? "is-sorted-desc" : "is-sorted-asc"
      );
    }

    _dispatchEvent(this._element, "rg:table:update", {
      search: this._state.search,
      sortKey: this._state.sortKey,
      sortDir: this._state.sortDir,
      page: paginated.page,
      pages: paginated.pages
    });

    if (options.focusPagination && this._paginationContainer) {
      const activeLink = this._paginationContainer.querySelector(".page-item.is-active .page-link");
      if (activeLink) activeLink.focus();
    }
  }

  dispose() {
    if (this._searchInput) {
      this._searchInput.removeEventListener("input", this._onSearchInput);
    }
    if (this._table) {
      const sortableHeaders = this._table.querySelectorAll("thead th[data-rg-sort]");
      sortableHeaders.forEach(th => {
        th.removeEventListener("click", this._onHeaderClick);
      });
    }
    if (this._paginationContainer) {
      this._paginationContainer.innerHTML = "";
    }
    _dataTableInstances.delete(this._element);
  }

  static getInstance(element) {
    return _dataTableInstances.get(element) || null;
  }

  static getOrCreate(element, options) {
    return this.getInstance(element) || new DataTable(element, options);
  }
}

function _formatPatternDigits(rawValue, pattern) {
  const digits = (rawValue || "").replace(/\D+/g, "");
  if (!pattern) return digits;
  let result = "";
  let di = 0;
  for (let i = 0; i < pattern.length; i++) {
    const ch = pattern[i];
    if (ch === "X") {
      if (di >= digits.length) break;
      result += digits[di++];
    } else {
      result += ch;
    }
  }
  return result;
}

function _applyMaskToInput(element) {
  if (!element || element.dataset.rgMaskBound === "true") return;

  const type = element.getAttribute("data-rg-mask");
  if (!type) return;

  const handler = _maskHandlers[type];

  const onInput = event => {
    const value = event.target.value || "";
    let formatted = value;

    if (handler) {
      formatted = handler(value, element);
    } else if (type === "phone") {
      const pattern =
        element.getAttribute("data-rg-mask-pattern") || "+X (XXX) XXX-XX-XX";
      formatted = _formatPatternDigits(value, pattern);
    } else if (type === "card") {
      const pattern =
        element.getAttribute("data-rg-mask-pattern") || "XXXX XXXX XXXX XXXX";
      formatted = _formatPatternDigits(value, pattern);
    } else if (type === "custom") {
      const pattern = element.getAttribute("data-rg-mask-pattern");
      if (pattern) {
        formatted = _formatPatternDigits(value, pattern);
      }
    }

    if (formatted !== value) {
      const selectionStart = element.selectionStart;
      const selectionEnd = element.selectionEnd;
      event.target.value = formatted;
      if (
        typeof selectionStart === "number" &&
        typeof selectionEnd === "number" &&
        element === document.activeElement
      ) {
        const diff = formatted.length - value.length;
        const pos = selectionEnd + diff;
        element.setSelectionRange(pos, pos);
      }
    }
  };

  element.addEventListener("input", onInput);
  element.dataset.rgMaskBound = "true";
}

const InputMask = {
  register(name, handler) {
    if (!name || typeof handler !== "function") return;
    _maskHandlers[name] = handler;
  },
  apply(element) {
    _applyMaskToInput(element);
  }
};

/* Lifecycle helpers                                                          */
/* -------------------------------------------------------------------------- */

function init(root = document) {
  initDataApi(root || document);
}

function dispose(root = document) {
  if (typeof document === "undefined") return;
  const container = root || document;
  const tooltips = container.querySelectorAll(".tooltip");
  const popovers = container.querySelectorAll(".popover");
  tooltips.forEach(el => el.parentNode && el.parentNode.removeChild(el));
  popovers.forEach(el => el.parentNode && el.parentNode.removeChild(el));
  _emitOnBus("rg:core:dispose", { root: container });
}

function reinit(root = document) {
  dispose(root);
  init(root);
}

/* Data API                                                                   */
/* -------------------------------------------------------------------------- */

let _dataApiInitialized = false;

function initDataApi(root = document) {
  if (typeof document === "undefined") return;
  if (!root) return;

  if (!_dataApiInitialized) {
    _dataApiInitialized = true;

    document.addEventListener("click", event => {
    const toggle = event.target.closest("[data-rg-toggle]");
    const dismiss = event.target.closest("[data-rg-dismiss]");

    // toggle handlers
    if (toggle && root.contains(toggle)) {
      const type = toggle.getAttribute("data-rg-toggle");
      if (!type) return;

      if (type === "dropdown") {
        event.preventDefault();
        const instance = Dropdown.getOrCreate(toggle);
        instance.toggle();
      } else if (type === "collapse") {
        event.preventDefault();
        const instance = Collapse.getOrCreate(toggle);
        instance.toggle();
      } else if (type === "modal") {
        event.preventDefault();
        const target = _resolveTarget(toggle);
        if (!target) return;
        const instance = Modal.getOrCreate(target);
        instance.toggle();
      } else if (type === "offcanvas") {
        event.preventDefault();
        const target = _resolveTarget(toggle);
        if (!target) return;
        const instance = Offcanvas.getOrCreate(target);
        instance.toggle();
      } else if (type === "toast") {
        event.preventDefault();
        const target = _resolveTarget(toggle);
        if (!target) return;
        const instance = Toast.getOrCreate(target);
        instance.show();
      } else if (type === "tooltip") {
        event.preventDefault();
        Tooltip.getOrCreate(toggle);
      } else if (type === "popover") {
        event.preventDefault();
        Popover.getOrCreate(toggle).toggle();
      }

      return;
    }

    // dismiss handlers
    if (dismiss && root.contains(dismiss)) {
      const type = dismiss.getAttribute("data-rg-dismiss");
      if (!type) return;

      if (type === "modal") {
        const target = dismiss.closest(".modal");
        if (!target) return;
        const instance = Modal.getOrCreate(target);
        instance.hide();
      } else if (type === "offcanvas") {
        const target = dismiss.closest(".offcanvas");
        if (!target) return;
        const instance = Offcanvas.getOrCreate(target);
        instance.hide();
      } else if (type === "toast") {
        const target = dismiss.closest(".toast");
        if (!target) return;
        const instance = Toast.getOrCreate(target);
        instance.hide();
      }
    }
  });
  }

  // Инициализация tooltip/popover по data-атрибутам
  const tooltipTriggers = root.querySelectorAll("[data-rg-toggle='tooltip']");
  tooltipTriggers.forEach(trigger => {
    Tooltip.getOrCreate(trigger);
  });

  const popoverTriggers = root.querySelectorAll("[data-rg-toggle='popover']");
  popoverTriggers.forEach(trigger => {
    Popover.getOrCreate(trigger);
  });


  const datepickerElements = root.querySelectorAll("[data-rg-datepicker]");
  datepickerElements.forEach(element => {
    Datepicker.getOrCreate(element);
  });

  const datetimePickerElements = root.querySelectorAll("[data-rg-datetime-picker]");
  datetimePickerElements.forEach(element => {
    DatetimePicker.getOrCreate(element);
  });

  const selectElements = root.querySelectorAll("[data-rg-select]");
  selectElements.forEach(element => {
    Select.getOrCreate(element);
  });

  const comboboxElements = root.querySelectorAll("[data-rg-combobox]");
  comboboxElements.forEach(element => {
    Combobox.getOrCreate(element);
  });

  const tagsInputs = root.querySelectorAll("[data-rg-tags-input]");
  tagsInputs.forEach(element => {
    TagsInput.getOrCreate(element);
  });

  const dataTables = root.querySelectorAll("[data-rg-table]");
  dataTables.forEach(element => {
    DataTable.getOrCreate(element);
  });

  const maskedInputs = root.querySelectorAll("[data-rg-mask]");
  maskedInputs.forEach(element => {
    _applyMaskToInput(element);
  });
}

if (typeof document !== "undefined") {
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => initDataApi(document));
  } else {
    initDataApi(document);
  }
}

const Rarog = {
  Dropdown,
  Collapse,
  Modal,
  Offcanvas,
  Toast,
  Tooltip,
  Popover,
  Carousel,
  Stepper,
  Datepicker,
  DatetimePicker,
  Select,
  Combobox,
  TagsInput,
  DataTable,
  InputMask,
  Events,
  config: RarogConfig,
  /**
   * Включить/выключить debug-режим в рантайме.
   * Эквивалентно установке window.RAROG_DEBUG / RAROG_DEV, но управляется из кода.
   */
  setDebug(value) {
    RarogConfig.debug = !!value;
  },
  /**
   * Проверить, активен ли debug-режим.
   */
  isDebugEnabled() {
    return !!RarogConfig.debug;
  },
  initDataApi,
  init,
  dispose,
  reinit
};

export {
  Dropdown,
  Collapse,
  Modal,
  Offcanvas,
  Toast,
  Tooltip,
  Popover,
  Carousel,
  Stepper,
  Datepicker,
  DatetimePicker,
  Select,
  Combobox,
  TagsInput,
  DataTable,
  InputMask,
  Events,
  initDataApi,
  init,
  dispose,
  reinit,
  Rarog
};
export default Rarog;
