document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    const container = document.getElementById('packageContainer');
    const dots = document.querySelectorAll('.dot');
    const currentPackageSpan = document.getElementById('currentPackage');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (!container) {
        console.error('Package container not found');
        return;
    }

    // Get total slides from data attribute or count slides
    const totalSlides = container.children.length;

    console.log('Slider initialized:', {
        totalSlides,
        container,
        dots: dots.length
    });

    function updateSlider() {
        container.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        dots.forEach((dot, index) => {
            dot.classList.toggle('bg-blue-500', index === currentSlide);
            dot.classList.toggle('bg-gray-300', index !== currentSlide);
        });
        
        if (currentPackageSpan) {
            currentPackageSpan.textContent = currentSlide + 1;
        }
        
        if (prevBtn) prevBtn.style.opacity = currentSlide === 0 ? '0.5' : '1';
        if (nextBtn) nextBtn.style.opacity = currentSlide === totalSlides - 1 ? '0.5' : '1';
    }

    function nextSlide() {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateSlider();
        }
    }

    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide--;
            updateSlider();
        }
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlider();
    }

    // Event listeners
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => goToSlide(index));
    });

    // Radio button change handler
    document.querySelectorAll('.package-radio').forEach((radio, index) => {
        radio.addEventListener('change', () => {
            if (radio.checked) goToSlide(index);
        });
    });

    // Touch/swipe support for mobile
    let startX = 0;
    container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });

    container.addEventListener('touchend', (e) => {
        const endX = e.changedTouches[0].clientX;
        const diff = startX - endX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? nextSlide() : prevSlide();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') prevSlide();
        if (e.key === 'ArrowRight') nextSlide();
    });

    // Initialize
    updateSlider();
});
