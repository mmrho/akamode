//category-section
document.addEventListener('DOMContentLoaded', () => {
  const grid = document.querySelector('.grid');
  const prevBtn = document.querySelector('.arrow-btn[aria-label="Previous"]');
  const nextBtn = document.querySelector('.arrow-btn[aria-label="Next"]');
  
  if (!grid || !prevBtn || !nextBtn) return;
  
  const computeScrollAmount = () => {
      const card = grid.querySelector('.card');
      if (!card) return 0;
      const style = getComputedStyle(grid);
      const gap = parseFloat(style.columnGap) || 0;
      return card.offsetWidth + gap;
  };
  
  let scrollAmount = computeScrollAmount();
  
  // به‌روزرسانی scrollAmount در resize
  window.addEventListener('resize', () => {
      scrollAmount = computeScrollAmount();
  });
  
  nextBtn.addEventListener('click', () => {
      grid.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  });
  
  prevBtn.addEventListener('click', () => {
      grid.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  });
});

//info-section
document.addEventListener("DOMContentLoaded", () => {
  const accordionItems = document.querySelectorAll(".info-item");

  accordionItems.forEach((item) => {
    if (item.classList.contains("is-open")) {
      const content = item.querySelector(".info-content");

      content.style.maxHeight = content.scrollHeight + "px";
    }
  });

  document.querySelectorAll(".info-header").forEach((header) => {
    header.addEventListener("click", () => {
      const currentItem = header.parentElement;
      const content = currentItem.querySelector(".info-content");

      /*
                  accordionItems.forEach(item => {
                      if (item !== currentItem && item.classList.contains('is-open')) {
                          item.classList.remove('is-open');
                          item.querySelector('.accordion-content').style.maxHeight = null;
                      }
                  });
                  */

      if (currentItem.classList.contains("is-open")) {
        currentItem.classList.remove("is-open");
        content.style.maxHeight = null;
      } else {
        currentItem.classList.add("is-open");
        content.style.maxHeight = content.scrollHeight + "px";
      }
    });
  });
});








