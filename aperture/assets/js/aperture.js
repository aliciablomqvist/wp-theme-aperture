/**
 * Aperture Theme JavaScript
 * Photography portfolio interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // === FADE IN ON SCROLL ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    // Observe all fade-in elements
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach(el => observer.observe(el));
    
    // Add fade-in to sections
    const sections = document.querySelectorAll('.aperture-project-card, .aperture-grid-item');
    sections.forEach(section => {
        section.classList.add('fade-in');
        observer.observe(section);
    });
    
    // === FULLSCREEN GALLERY ===
    let currentImageIndex = 0;
    let galleryImages = [];
    
    // Create fullscreen container
    const fullscreen = document.createElement('div');
    fullscreen.className = 'aperture-fullscreen';
    fullscreen.innerHTML = `
        <button class="aperture-fullscreen-close">×</button>
        <button class="aperture-fullscreen-nav aperture-fullscreen-prev">‹</button>
        <img src="" alt="Fullscreen image">
        <button class="aperture-fullscreen-nav aperture-fullscreen-next">›</button>
    `;
    document.body.appendChild(fullscreen);
    
    const fullscreenImg = fullscreen.querySelector('img');
    const closeBtn = fullscreen.querySelector('.aperture-fullscreen-close');
    const prevBtn = fullscreen.querySelector('.aperture-fullscreen-prev');
    const nextBtn = fullscreen.querySelector('.aperture-fullscreen-next');
    
    // Collect all gallery images
    function initGallery() {
        const gridItems = document.querySelectorAll('.aperture-grid-item img, .wp-block-gallery img, .wp-block-image img');
        galleryImages = Array.from(gridItems).map(img => ({
            src: img.src,
            alt: img.alt
        }));
        
        // Add click handlers
        gridItems.forEach((img, index) => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', () => openFullscreen(index));
        });
    }
    
    function openFullscreen(index) {
        currentImageIndex = index;
        updateFullscreenImage();
        fullscreen.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeFullscreen() {
        fullscreen.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function updateFullscreenImage() {
        if (galleryImages[currentImageIndex]) {
            fullscreenImg.src = galleryImages[currentImageIndex].src;
            fullscreenImg.alt = galleryImages[currentImageIndex].alt;
        }
    }
    
    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        updateFullscreenImage();
    }
    
    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        updateFullscreenImage();
    }
    
    // Event listeners
    closeBtn.addEventListener('click', closeFullscreen);
    nextBtn.addEventListener('click', nextImage);
    prevBtn.addEventListener('click', prevImage);
    
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!fullscreen.classList.contains('active')) return;
        
        if (e.key === 'Escape') closeFullscreen();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });
    
    // Click outside to close
    fullscreen.addEventListener('click', (e) => {
        if (e.target === fullscreen) {
            closeFullscreen();
        }
    });
    
    // Initialize gallery
    setTimeout(initGallery, 500);
    
    // === SMOOTH SCROLL ===
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // === PARALLAX EFFECT ===
    let ticking = false;
    
    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.aperture-editorial-image img');
        
        parallaxElements.forEach(element => {
            const speed = 0.3;
            const yPos = scrolled * speed;
            element.style.transform = `translateY(${yPos}px)`;
        });
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    });
    
    // === GRID HOVER EFFECT ===
    const gridItems = document.querySelectorAll('.aperture-grid-item');
    gridItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.zIndex = '100';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
    
    // === LAZY LOADING ===
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src || img.src;
        });
    } else {
        // Fallback for older browsers
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // === PROJECT CARD ANIMATION ===
    const projectCards = document.querySelectorAll('.aperture-project-card');
    projectCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // === LOADING SCREEN ===
    window.addEventListener('load', function() {
        const loading = document.querySelector('.aperture-loading');
        if (loading) {
            setTimeout(() => {
                loading.classList.add('hidden');
                setTimeout(() => loading.remove(), 500);
            }, 300);
        }
    });
    
    // === IMAGE PRELOADER ===
    function preloadImages() {
        const images = document.querySelectorAll('img[data-preload]');
        images.forEach(img => {
            const preloadImg = new Image();
            preloadImg.src = img.dataset.preload;
        });
    }
    
    preloadImages();
    
    console.log('📸 Aperture theme loaded successfully');
});
