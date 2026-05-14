import './bootstrap';

// Import Swiper JS and modules
import Swiper, { Navigation, Pagination, Autoplay, EffectFade } from 'swiper';

// Import Swiper CSS
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

// Initialize Swiper after full page load
window.addEventListener('load', () => {
    const sliderEl = document.querySelector('.hero-product-slider');
    if (sliderEl) {
        new Swiper(sliderEl, {
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }
});
