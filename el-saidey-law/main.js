/* El-Saidey Law — interactions (Rukn system) */
window.addEventListener("load", () => {
  setTimeout(() => document.getElementById("loader")?.classList.add("out"), 900);
});

const hdr = document.getElementById("hdr");
const fab = document.getElementById("fab");
const onScroll = () => {
  const y = window.scrollY;
  hdr?.classList.toggle("scrolled", y > 40);
  fab?.classList.toggle("show", y > 500);
};
window.addEventListener("scroll", onScroll, { passive: true });
onScroll();

function toggleMob(open) {
  document.getElementById("mob")?.classList.toggle("open", open);
}

(function () {
  const box = document.getElementById("particles");
  if (!box) return;
  for (let i = 0; i < 26; i++) {
    const p = document.createElement("span");
    p.className = "particle";
    const s = Math.random() * 4 + 2;
    p.style.width = p.style.height = s + "px";
    p.style.left = Math.random() * 100 + "%";
    p.style.top = Math.random() * 100 + "%";
    p.style.opacity = Math.random() * 0.5 + 0.2;
    p.style.animationDelay = Math.random() * 8 + "s";
    p.style.animationDuration = Math.random() * 8 + 8 + "s";
    box.appendChild(p);
  }
})();

const io = new IntersectionObserver(
  (entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        e.target.classList.add("on");
        io.unobserve(e.target);
      }
    });
  },
  { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }
);
document.querySelectorAll(".rv,.rv-l").forEach((el) => io.observe(el));

const counted = new Set();
function animateCount(el) {
  if (counted.has(el)) return;
  counted.add(el);
  const target = parseFloat(el.dataset.count);
  const dec = parseInt(el.dataset.dec || "0", 10);
  const suffix = el.dataset.suffix || "";
  const dur = 1600;
  const start = performance.now();
  function step(now) {
    const t = Math.min((now - start) / dur, 1);
    const eased = 1 - Math.pow(1 - t, 3);
    const val = target * eased;
    el.textContent = (dec ? val.toFixed(dec) : Math.floor(val).toLocaleString("en-US")) + suffix;
    if (t < 1) requestAnimationFrame(step);
    else el.textContent = (dec ? target.toFixed(dec) : Math.floor(target).toLocaleString("en-US")) + suffix;
  }
  requestAnimationFrame(step);
}
const cio = new IntersectionObserver(
  (entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        animateCount(e.target);
        cio.unobserve(e.target);
      }
    });
  },
  { threshold: 0.4 }
);
document.querySelectorAll("[data-count]").forEach((el) => cio.observe(el));

function faqT(q) {
  const item = q.parentElement;
  const ans = item.querySelector(".faq-a");
  const open = item.classList.contains("faq-open");
  document.querySelectorAll(".faq-item.faq-open").forEach((i) => {
    i.classList.remove("faq-open");
    i.querySelector(".faq-a").style.maxHeight = null;
  });
  if (!open) {
    item.classList.add("faq-open");
    ans.style.maxHeight = ans.scrollHeight + "px";
  }
}

document.querySelectorAll(".faq-cats button").forEach((b) =>
  b.addEventListener("click", () => {
    document.querySelectorAll(".faq-cats button").forEach((x) => x.classList.remove("active"));
    b.classList.add("active");
    const f = b.dataset.fc;
    document.querySelectorAll(".faq-item").forEach((it) => {
      it.classList.toggle("hide", !(f === "all" || it.dataset.cat === f));
    });
  })
);

document.querySelectorAll(".filters button").forEach((b) =>
  b.addEventListener("click", () => {
    document.querySelectorAll(".filters button").forEach((x) => x.classList.remove("active"));
    b.classList.add("active");
    const f = b.dataset.f;
    document.querySelectorAll(".proj").forEach((p) => {
      p.classList.toggle("hide", !(f === "all" || p.dataset.cat === f));
    });
  })
);

const track = document.getElementById("rvTrack");
let rvIdx = 0;
function rvPer() {
  return window.innerWidth <= 640 ? 1 : window.innerWidth <= 1024 ? 2 : 3;
}
function rvMax() {
  return track ? Math.max(0, track.children.length - rvPer()) : 0;
}
function rvApply() {
  if (!track || !track.children.length) return;
  rvIdx = Math.min(Math.max(rvIdx, 0), rvMax());
  const card = track.children[0];
  const step = card.getBoundingClientRect().width + 16;
  track.style.transform = `translateX(${rvIdx * step}px)`;
}
function rvMove(dir) {
  rvIdx += dir;
  if (rvIdx > rvMax()) rvIdx = 0;
  if (rvIdx < 0) rvIdx = rvMax();
  rvApply();
}
window.addEventListener("resize", rvApply);
rvApply();
if (track) {
  let rvTimer = setInterval(() => rvMove(1), 5000);
  track.parentElement?.addEventListener("mouseenter", () => clearInterval(rvTimer));
  track.parentElement?.addEventListener("mouseleave", () => {
    rvTimer = setInterval(() => rvMove(1), 5000);
  });
}

document.querySelectorAll('a[href^="#"]').forEach((a) => {
  a.addEventListener("click", (e) => {
    const id = a.getAttribute("href");
    if (!id || id === "#" || id.length < 2) return;
    const t = document.querySelector(id);
    if (t) {
      e.preventDefault();
      window.scrollTo({ top: t.getBoundingClientRect().top + window.scrollY - 90, behavior: "smooth" });
      toggleMob(false);
    }
  });
});

(function () {
  const btn = document.getElementById("fnBtn");
  if (!btn) return;
  const svc = document.getElementById("fnSvc");
  const city = document.getElementById("fnCity");
  const res = document.getElementById("fnResult");
  const times = {
    طلخا: "خلال ساعة",
    المنصورة: "خلال ساعة",
    الدقهلية: "خلال ساعات",
    القاهرة: "خلال يوم عمل",
    "مصر — عن بُعد": "رد سريع عن بُعد",
  };
  btn.addEventListener("click", () => {
    const s = svc.value;
    const c = city.value;
    document.getElementById("frTitle").textContent = s + " — " + c;
    document.getElementById("frSub").textContent =
      "المستشار عيد الصعيدي جاهز لدراسة موقفك في " + c + " بخطوات واضحة.";
    document.getElementById("frTime").textContent = times[c] || "خلال ساعات";
    res.hidden = false;
    res.scrollIntoView({ behavior: "smooth", block: "center" });
  });
})();

document.addEventListener("click", (e) => {
  const b = e.target.closest(".btn");
  if (!b) return;
  const r = document.createElement("span");
  r.className = "ripple";
  const rect = b.getBoundingClientRect();
  const size = Math.max(rect.width, rect.height);
  r.style.width = r.style.height = size + "px";
  r.style.left = e.clientX - rect.left - size / 2 + "px";
  r.style.top = e.clientY - rect.top - size / 2 + "px";
  b.appendChild(r);
  setTimeout(() => r.remove(), 600);
});

const year = document.getElementById("year");
if (year) year.textContent = String(new Date().getFullYear());
