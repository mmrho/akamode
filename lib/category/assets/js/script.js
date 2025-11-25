document.addEventListener('DOMContentLoaded', function() {
    // Filter Toggle
    const toggleFilter = () => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if(sidebar && overlay) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        }
    };
    
    const filterTrig = document.getElementById('filterTrigger');
    if(filterTrig) filterTrig.addEventListener('click', toggleFilter);
    
    const closeFilt = document.getElementById('closeFilter');
    if(closeFilt) closeFilt.addEventListener('click', toggleFilter);
    
    const overlayEl = document.getElementById('overlay');
    if(overlayEl) overlayEl.addEventListener('click', toggleFilter);

    // Accordion
    document.querySelectorAll('.f-head').forEach(head => {
        const content = head.nextElementSibling;
        content.style.maxHeight = content.scrollHeight + "px";
        head.addEventListener('click', () => {
            head.classList.toggle('collapsed');
            content.style.maxHeight = head.classList.contains('collapsed') ? "0px" : content.scrollHeight + "px";
        });
    });

    // Selections
    document.querySelectorAll('.size-opt, .color-opt').forEach(opt => {
        opt.addEventListener('click', function() {
            this.parentElement.querySelectorAll('.' + this.className.split(' ')[0]).forEach(s => s.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Slider
    const sliderContainer = document.getElementById('sliderContainer');
    const sliderThumb = document.getElementById('sliderThumb');
    const sliderFill = document.getElementById('sliderFill');
    const priceValue = document.getElementById('priceValue');
    let isDragging = false;

    function updateSlider(clientX) {
        if(!sliderContainer) return;
        const rect = sliderContainer.getBoundingClientRect();
        let diffX = rect.right - clientX;
        let percent = (diffX / rect.width) * 100;
        if (percent < 0) percent = 0;
        if (percent > 100) percent = 100;

        sliderThumb.style.right = percent + '%';
        sliderFill.style.width = percent + '%';
        priceValue.innerText = Math.round((50000000 * percent) / 100).toLocaleString() + ' تومان';
    }

    if(sliderThumb) {
        sliderThumb.addEventListener('mousedown', () => isDragging = true);
        window.addEventListener('mouseup', () => isDragging = false);
        window.addEventListener('mousemove', (e) => isDragging && updateSlider(e.clientX));
        sliderThumb.addEventListener('touchstart', () => isDragging = true);
        window.addEventListener('touchend', () => isDragging = false);
        window.addEventListener('touchmove', (e) => isDragging && updateSlider(e.touches[0].clientX));
        sliderContainer.addEventListener('click', (e) => updateSlider(e.clientX));
    }

    // Sort
    const sortTrigger = document.getElementById('sortTrigger');
    const sortDropdown = document.getElementById('sortDropdown');
    const sortLabel = document.getElementById('sortLabel');

    if(sortTrigger && sortDropdown) {
        sortTrigger.addEventListener('click', (e) => { e.stopPropagation(); sortDropdown.classList.toggle('show'); });
        window.addEventListener('click', (e) => !sortTrigger.contains(e.target) && sortDropdown.classList.remove('show'));
    }

    document.querySelectorAll('.sort-option').forEach(opt => {
        opt.addEventListener('click', function() {
            document.querySelectorAll('.sort-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            if(sortLabel) sortLabel.innerText = 'مرتب سازی بر اساس : ' + this.innerText;
            if(sortDropdown) sortDropdown.classList.remove('show');
        });
    });

    // Pagination
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if(!isNaN(this.innerText)) {
                document.querySelectorAll('.page-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const grid = document.querySelector('.product-grid');
                if(grid) window.scrollTo({top: grid.offsetTop - 100, behavior: 'smooth'});
            }
        });
    });
});
