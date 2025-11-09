// Toggle button state
const btn = document.getElementById("menuBtnIcon");
btn.addEventListener("click", () => {
  btn.classList.toggle("active");
  // here you can also toggle your mobile menu
});







        // جاوا اسکریپت برای مدیریت آکاردئون
        
        // اجرا بعد از بارگذاری کامل صفحه
        document.addEventListener('DOMContentLoaded', () => {

          const accordionItems = document.querySelectorAll('.info-item');

          // ۱. تنظیم حالت اولیه (باز کردن آیتم‌های .is-open)
          accordionItems.forEach(item => {
              if (item.classList.contains('is-open')) {
                  const content = item.querySelector('.info-content');
                  // ارتفاع کامل محتوا را به max-height می‌دهد تا باز شود
                  content.style.maxHeight = content.scrollHeight + 'px';
              }
          });

          // ۲. افزودن شنونده کلیک به هدرها
          document.querySelectorAll('.info-header').forEach(header => {
              header.addEventListener('click', () => {
                  const currentItem = header.parentElement;
                  const content = currentItem.querySelector('.info-content');

                  // بستن تمام آیتم‌های دیگر (اختیاری، اما حرفه‌ای است)
                  /*
                  accordionItems.forEach(item => {
                      if (item !== currentItem && item.classList.contains('is-open')) {
                          item.classList.remove('is-open');
                          item.querySelector('.accordion-content').style.maxHeight = null;
                      }
                  });
                  */
                  
                  // باز/بسته کردن آیتم فعلی
                  if (currentItem.classList.contains('is-open')) {
                      // اگر باز است، آن را ببند
                      currentItem.classList.remove('is-open');
                      content.style.maxHeight = null;
                  } else {
                      // اگر بسته است، آن را باز کن
                      currentItem.classList.add('is-open');
                      content.style.maxHeight = content.scrollHeight + 'px';
                  }
              });
          });

      });
