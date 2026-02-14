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

/**
 * Aperture Photobook Page Flip Effect
 * Interactive binder/book page flipping
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // === PAGE FLIP INTERACTION ===
    
    let currentPage = 0;
    const pages = document.querySelectorAll('.aperture-page, .aperture-binder-page, .aperture-spread');
    
    // Add click handlers to flip pages
    pages.forEach((page, index) => {
        // Add data attribute for page number
        page.dataset.pageNumber = index + 1;
        
        // Click to flip
        page.addEventListener('click', function(e) {
            // Don't flip if clicking on a link or button
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                return;
            }
            
            flipPage(page, index);
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                flipToNext();
            } else if (e.key === 'ArrowLeft') {
                flipToPrevious();
            }
        });
    });
    
    function flipPage(page, index) {
        // Add flipping class
        page.classList.add('flipping');
        
        // Play flip sound (optional)
        playPageFlipSound();
        
        // Scroll to next page after flip
        setTimeout(() => {
            if (pages[index + 1]) {
                pages[index + 1].scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
            page.classList.remove('flipping');
        }, 1200);
        
        currentPage = index + 1;
    }
    
    function flipToNext() {
        if (currentPage < pages.length - 1) {
            flipPage(pages[currentPage], currentPage);
        }
    }
    
    function flipToPrevious() {
        if (currentPage > 0) {
            currentPage--;
            pages[currentPage].scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    }
    
    // Optional: Play page flip sound
    function playPageFlipSound() {
        // You can add an audio element here
        // const audio = new Audio('/path/to/page-flip.mp3');
        // audio.play();
    }
    
    // === BINDER RING ANIMATION ===
    
    // Animate rings when page loads
    const binderPages = document.querySelectorAll('.aperture-binder-page');
    
    binderPages.forEach((page, index) => {
        page.style.opacity = '0';
        page.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            page.style.transition = 'all 0.6s ease';
            page.style.opacity = '1';
            page.style.transform = 'translateY(0)';
        }, index * 200);
    });
    
    // === PHOTO GRID LIGHTBOX ===
    
    const gridImages = document.querySelectorAll('.aperture-photo-grid img');
    
    gridImages.forEach(img => {
        img.style.cursor = 'pointer';
        
        img.addEventListener('click', function() {
            // Create lightbox
            const lightbox = document.createElement('div');
            lightbox.className = 'aperture-lightbox';
            lightbox.innerHTML = `
                <div class="lightbox-backdrop"></div>
                <img src="${this.src}" alt="${this.alt}">
                <button class="lightbox-close">×</button>
            `;
            
            document.body.appendChild(lightbox);
            document.body.style.overflow = 'hidden';
            
            // Fade in
            setTimeout(() => lightbox.classList.add('active'), 10);
            
            // Close lightbox
            const closeBtn = lightbox.querySelector('.lightbox-close');
            const backdrop = lightbox.querySelector('.lightbox-backdrop');
            
            [closeBtn, backdrop].forEach(el => {
                el.addEventListener('click', function() {
                    lightbox.classList.remove('active');
                    setTimeout(() => {
                        lightbox.remove();
                        document.body.style.overflow = '';
                    }, 300);
                });
            });
            
            // Close with ESC key
            document.addEventListener('keydown', function escClose(e) {
                if (e.key === 'Escape') {
                    closeBtn.click();
                    document.removeEventListener('keydown', escClose);
                }
            });
        });
    });
    
    // === PARALLAX PAGE EFFECT ===
    
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        
        pages.forEach((page, index) => {
            const speed = 0.1 + (index * 0.02);
            const yPos = -(scrolled * speed);
            
            if (page.querySelector('img')) {
                page.querySelector('img').style.transform = `translateY(${yPos}px)`;
            }
        });
    });
    
    // === PAGE COUNTER ===
    
    // Add page counter overlay
    const pageCounter = document.createElement('div');
    pageCounter.className = 'aperture-page-counter';
    pageCounter.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        font-family: 'Space Mono', monospace;
        font-size: 0.9rem;
        color: #666;
        background: rgba(255,255,255,0.9);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    document.body.appendChild(pageCounter);
    
    // Update counter on scroll
    let ticking = false;
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                updatePageCounter();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    function updatePageCounter() {
        const scrollPercent = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight));
        const currentPageNum = Math.min(pages.length, Math.ceil(scrollPercent * pages.length));
        
        pageCounter.textContent = `${currentPageNum || 1} / ${pages.length}`;
        pageCounter.style.opacity = scrollPercent > 0.05 ? '1' : '0';
    }
    
    // === SMOOTH PAGE TRANSITIONS ===
    
    // Add transition indicators
    pages.forEach((page, index) => {
        if (index > 0) {
            const separator = document.createElement('div');
            separator.className = 'page-separator';
            separator.style.cssText = `
                height: 3px;
                background: linear-gradient(to right, 
                    transparent, 
                    #DDD 20%, 
                    #DDD 80%, 
                    transparent);
                margin: 2rem 0;
            `;
            page.parentNode.insertBefore(separator, page);
        }
    });
    
    console.log('📖 Aperture Photobook loaded - ' + pages.length + ' pages');
});

// === LIGHTBOX STYLES (injected) ===
const lightboxStyles = document.createElement('style');
lightboxStyles.textContent = `
    .aperture-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .aperture-lightbox.active {
        opacity: 1;
    }
    
    .lightbox-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        cursor: pointer;
    }
    
    .aperture-lightbox img {
        position: relative;
        max-width: 90%;
        max-height: 90vh;
        object-fit: contain;
        z-index: 10001;
        box-shadow: 0 10px 50px rgba(0,0,0,0.5);
    }
    
    .lightbox-close {
        position: absolute;
        top: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        background: #FFFFFF;
        color: #000000;
        border: none;
        border-radius: 50%;
        font-size: 2rem;
        cursor: pointer;
        z-index: 10002;
        transition: all 0.3s ease;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lightbox-close:hover {
        transform: rotate(90deg);
        background: #000000;
        color: #FFFFFF;
    }
`;
document.head.appendChild(lightboxStyles);

/**
 * Aperture Photobook Page Flip Effect
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // === FLIPBOOK GALLERY ===
    const flipbooks = document.querySelectorAll('.aperture-flipbook-container');
    
    if (flipbooks.length === 0) {
        console.log('No flipbooks found on this page');
        return;
    }
    
    flipbooks.forEach(flipbook => {
        const pages = flipbook.querySelectorAll('.flipbook-page');
        const prevBtn = flipbook.querySelector('.flip-prev');
        const nextBtn = flipbook.querySelector('.flip-next');
        const indicator = flipbook.querySelector('.page-indicator');
        
        // Check if elements exist
        if (!prevBtn || !nextBtn || !indicator || pages.length === 0) {
            console.error('Flipbook elements missing');
            return;
        }
        
        let currentPage = 1;
        const totalPages = pages.length;
        
        console.log('Flipbook initialized with ' + totalPages + ' pages');
        
        // Update UI
        function updateUI() {
            indicator.textContent = currentPage + ' / ' + totalPages;
            prevBtn.disabled = (currentPage === 1);
            nextBtn.disabled = (currentPage === totalPages);
        }
        
        // Flip to page
        function flipToPage(pageNum) {
            if (pageNum < 1 || pageNum > totalPages) return;
            
            const oldPage = flipbook.querySelector('.flipbook-page.active');
            const newPage = flipbook.querySelector('.flipbook-page[data-page="' + pageNum + '"]');
            
            if (!newPage || oldPage === newPage) return;
            
            console.log('Flipping from page ' + currentPage + ' to ' + pageNum);
            
            // Flip animation
            oldPage.classList.remove('active');
            oldPage.classList.add('flipping-out');
            
            newPage.classList.add('flipping-in');
            
            setTimeout(function() {
                oldPage.classList.remove('flipping-out');
                newPage.classList.remove('flipping-in');
                newPage.classList.add('active');
                
                currentPage = pageNum;
                updateUI();
            }, 800);
        }
        
        // Event listeners
        prevBtn.addEventListener('click', function() {
            flipToPage(currentPage - 1);
        });
        
        nextBtn.addEventListener('click', function() {
            flipToPage(currentPage + 1);
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            // Only work if flipbook is visible
            const activeFlipbook = document.querySelector('.aperture-flipbook-container .flipbook-page.active');
            if (!activeFlipbook) return;
            
            if (e.key === 'ArrowRight') {
                nextBtn.click();
            } else if (e.key === 'ArrowLeft') {
                prevBtn.click();
            }
        });
        
        // Swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        flipbook.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, false);
        
        flipbook.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left → next
                nextBtn.click();
            }
            if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right → prev
                prevBtn.click();
            }
        }
        
        // Initialize
        updateUI();
        console.log('Flipbook controls ready');
    });
    
    console.log('📖 Aperture Photobook loaded - ' + document.querySelectorAll('.flipbook-page, .aperture-binder-page, .aperture-spread').length + ' pages');
});