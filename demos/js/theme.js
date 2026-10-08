(function(){
  const ham = document.querySelector(".ham");
  const drawer = document.querySelector(".drawer");
  if(ham && drawer){
    ham.addEventListener("click", () => drawer.classList.add("on"));
    drawer.addEventListener("click", (e) => {
      if(e.target === drawer || e.target.closest("[data-close]")) drawer.classList.remove("on");
    });
  }

  const form = document.querySelector("[data-wa-form]");
  if(form){
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const data = Object.fromEntries(new FormData(form).entries());
      const lines = [
        "طلب معاينة — ديمو ركن التطور",
        data.service ? "الخدمة: " + data.service : "",
        data.city ? "المدينة: " + data.city : "",
        data.name ? "الاسم: " + data.name : "",
        data.phone ? "الهاتف: " + data.phone : "",
        data.note ? "ملاحظات: " + data.note : ""
      ].filter(Boolean);
      const url = "https://wa.me/971586634710?text=" + encodeURIComponent(lines.join("\n"));
      window.open(url, "_blank", "noopener");
    });
  }

  document.querySelectorAll("[data-next]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const panels = [...document.querySelectorAll(".panel")];
      const current = panels.findIndex((p) => p.classList.contains("on"));
      if(current < panels.length - 1){
        panels[current].classList.remove("on");
        panels[current + 1].classList.add("on");
        document.querySelectorAll(".stepper span").forEach((s, i) => s.classList.toggle("on", i <= current + 1));
      }
    });
  });
  document.querySelectorAll("[data-prev]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const panels = [...document.querySelectorAll(".panel")];
      const current = panels.findIndex((p) => p.classList.contains("on"));
      if(current > 0){
        panels[current].classList.remove("on");
        panels[current - 1].classList.add("on");
        document.querySelectorAll(".stepper span").forEach((s, i) => s.classList.toggle("on", i <= current - 1));
      }
    });
  });
})();
