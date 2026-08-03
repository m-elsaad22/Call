(() => {
  const doc = document;
  const body = doc.body;
  const progressBar = doc.querySelector(".progress__bar");
  const nav = doc.querySelector("[data-nav]");
  const menuToggle = doc.querySelector("[data-menu-toggle]");
  const mobileMenu = doc.querySelector("[data-mobile-menu]");
  const cursor = doc.querySelector(".cursor");
  const heroImg = doc.querySelector("[data-parallax]");
  const yearEl = doc.querySelector("[data-year]");
  const reveals = [...doc.querySelectorAll("[data-reveal]")];
  const services = [...doc.querySelectorAll("[data-service]")];

  if (yearEl) yearEl.textContent = String(new Date().getFullYear());

  const finePointer = window.matchMedia("(pointer: fine)").matches;
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  if (!finePointer) body.classList.add("no-fine-pointer");

  /* Scroll progress + nav state */
  const onScroll = () => {
    const scrollTop = window.scrollY;
    const max = doc.documentElement.scrollHeight - window.innerHeight;
    const ratio = max > 0 ? (scrollTop / max) * 100 : 0;
    if (progressBar) progressBar.style.width = `${ratio}%`;
    if (nav) nav.classList.toggle("is-scrolled", scrollTop > 24);

    if (!reduceMotion && heroImg) {
      const shift = Math.min(scrollTop * 0.18, 120);
      heroImg.style.transform = `scale(1.08) translate3d(0, ${shift}px, 0)`;
    }
  };

  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* Mobile menu */
  const setMenu = (open) => {
    body.classList.toggle("is-menu-open", open);
    if (!mobileMenu) return;
    mobileMenu.hidden = !open;
    if (menuToggle) {
      menuToggle.setAttribute("aria-label", open ? "إغلاق القائمة" : "فتح القائمة");
    }
  };

  menuToggle?.addEventListener("click", () => {
    setMenu(!body.classList.contains("is-menu-open"));
  });

  mobileMenu?.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => setMenu(false));
  });

  /* Custom cursor */
  if (finePointer && cursor && !reduceMotion) {
    let x = window.innerWidth / 2;
    let y = window.innerHeight / 2;
    let cx = x;
    let cy = y;
    let raf = 0;

    const tick = () => {
      cx += (x - cx) * 0.18;
      cy += (y - cy) * 0.18;
      cursor.style.transform = `translate(${cx}px, ${cy}px) translate(-50%, -50%)`;
      raf = requestAnimationFrame(tick);
    };

    window.addEventListener(
      "pointermove",
      (e) => {
        x = e.clientX;
        y = e.clientY;
        cursor.classList.remove("is-hidden");
      },
      { passive: true }
    );

    window.addEventListener("pointerleave", () => cursor.classList.add("is-hidden"));
    window.addEventListener("pointerover", (e) => {
      const target = e.target.closest("a, button");
      cursor.classList.toggle("is-active", Boolean(target));
    });

    raf = requestAnimationFrame(tick);
  }

  /* Hero entrance + reveal on scroll */
  const heroReveals = [...doc.querySelectorAll(".hero [data-reveal]")];
  heroReveals.forEach((el, i) => {
    el.style.setProperty("--delay", `${160 + i * 110}ms`);
  });

  if (reduceMotion) {
    reveals.forEach((el) => el.classList.add("is-in"));
  } else if ("IntersectionObserver" in window) {
    const otherReveals = reveals.filter((el) => !el.closest(".hero"));
    otherReveals.forEach((el, i) => {
      el.style.setProperty("--delay", `${(i % 4) * 70}ms`);
    });

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-in");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.18, rootMargin: "0px 0px -8% 0px" }
    );

    otherReveals.forEach((el) => io.observe(el));
  } else {
    reveals.forEach((el) => el.classList.add("is-in"));
  }

  /* Services accordion */
  services.forEach((item) => {
    const trigger = item.querySelector(".service__trigger");
    if (!trigger) return;

    trigger.addEventListener("click", () => {
      const isOpen = item.classList.contains("is-open");
      services.forEach((other) => {
        other.classList.remove("is-open");
        other.querySelector(".service__trigger")?.setAttribute("aria-expanded", "false");
      });
      if (!isOpen) {
        item.classList.add("is-open");
        trigger.setAttribute("aria-expanded", "true");
      }
    });
  });

  /* Magnetic buttons */
  if (finePointer && !reduceMotion) {
    doc.querySelectorAll(".btn, .nav__cta, .channel--accent").forEach((el) => {
      el.addEventListener("pointermove", (e) => {
        const rect = el.getBoundingClientRect();
        const dx = e.clientX - (rect.left + rect.width / 2);
        const dy = e.clientY - (rect.top + rect.height / 2);
        el.style.transform = `translate(${dx * 0.12}px, ${dy * 0.18}px)`;
      });
      el.addEventListener("pointerleave", () => {
        el.style.transform = "";
      });
    });
  }
})();
