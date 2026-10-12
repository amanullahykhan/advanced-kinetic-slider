/**
 * Advanced Kinetic Slider — Frontend Handler
 * Commercial-grade Elementor widget handler.
 *
 * Fixes vs. v1.0:
 *  - loop setting was hard-coded to always be `true` (dataSettings.loop || true
 *    is always truthy). Now respects the actual boolean.
 *  - Swiper instance is properly destroyed on re-init (Elementor editor fires
 *    element_ready repeatedly on every settings change — without destroy()
 *    old instances/listeners leaked and stacked).
 *  - Autoplay pause/resume is wired to a visible progress bar instead of a
 *    black box.
 *  - Background videos are played/paused in sync with slide activation.
 *  - Keyboard navigation + pause-on-hover + prefers-reduced-motion respected.
 */
class AdvancedSliderHandler extends elementorModules.frontend.handlers.Base {

	getDefaultSettings() {
		return {
			selectors: {
				wrapper: '.adv-slider-wrapper',
				container: '.adv-swiper',
				next: '.swiper-button-next',
				prev: '.swiper-button-prev',
				pagination: '.swiper-pagination',
				progressBar: '.adv-slider-progress-bar',
				counterCurrent: '.adv-slider-counter .current',
				counterTotal: '.adv-slider-counter .total',
				toggleBtn: '.adv-slider-toggle',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		const $wrapper = this.$element.find( selectors.wrapper );
		return {
			$wrapper,
			$container: $wrapper.find( selectors.container ),
			$next: $wrapper.find( selectors.next ),
			$prev: $wrapper.find( selectors.prev ),
			$pagination: $wrapper.find( selectors.pagination ),
			$progressBar: $wrapper.find( selectors.progressBar ),
			$counterCurrent: $wrapper.find( selectors.counterCurrent ),
			$counterTotal: $wrapper.find( selectors.counterTotal ),
			$toggleBtn: $wrapper.find( selectors.toggleBtn ),
		};
	}

	/**
	 * Reads a boolean-ish value out of a data attribute safely.
	 * jQuery's .data() already casts "true"/"false" strings to booleans,
	 * but we guard against stray truthy strings like "yes"/"1" too, since
	 * this value is generated from PHP wp_json_encode of a PHP bool.
	 */
	static toBool( value, fallback = false ) {
		if ( typeof value === 'boolean' ) return value;
		if ( value === undefined || value === null || value === '' ) return fallback;
		return [ 'yes', '1', 'true', true, 1 ].indexOf( value ) !== -1;
	}

	onInit() {
		super.onInit();
		this.destroySwiper();

		if ( ! this.elements.$container.length ) {
			return;
		}

		const raw = this.elements.$wrapper.data( 'swiper-settings' ) || {};
		this.config = raw;
		this.prefersReducedMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		this.initSwiper();
		this.bindProgressBar();
		this.bindCounter();
		this.bindToggle();
		this.bindKeyboard();
		this.bindKenBurnsVideoSync();
	}

	async initSwiper() {
		const cfg = this.config;
		const loopEnabled = AdvancedSliderHandler.toBool( cfg.loop, false );
		const autoplayEnabled = AdvancedSliderHandler.toBool( cfg.autoplay, false ) && ! this.prefersReducedMotion;
		const navEnabled = AdvancedSliderHandler.toBool( cfg.navigation, true );
		const paginationEnabled = AdvancedSliderHandler.toBool( cfg.pagination, true );

		const swiperOptions = {
			effect: cfg.effect || 'slide',
			speed: parseInt( cfg.speed, 10 ) || 800,
			loop: loopEnabled,
			slidesPerView: 1,
			watchSlidesProgress: true,
			a11y: {
				enabled: true,
				prevSlideMessage: 'Previous slide',
				nextSlideMessage: 'Next slide',
			},
			navigation: navEnabled ? {
				nextEl: this.elements.$next[ 0 ],
				prevEl: this.elements.$prev[ 0 ],
			} : false,
			pagination: paginationEnabled ? {
				el: this.elements.$pagination[ 0 ],
				clickable: true,
			} : false,
			on: {
				slideChangeTransitionStart: () => this.handleSlideChange(),
				init: () => this.handleSlideChange(),
			},
		};

		if ( cfg.effect === 'fade' ) {
			swiperOptions.fadeEffect = { crossFade: true };
		}
		if ( cfg.effect === 'coverflow' ) {
			swiperOptions.coverflowEffect = {
				rotate: 30, stretch: 0, depth: 100, modifier: 1, slideShadows: false,
			};
		}

		if ( autoplayEnabled ) {
			swiperOptions.autoplay = {
				delay: parseInt( cfg.autoplaySpeed, 10 ) || 5000,
				disableOnInteraction: false,
				pauseOnMouseEnter: AdvancedSliderHandler.toBool( cfg.pauseOnHover, true ),
			};
		}

		const Swiper = elementorFrontend.utils.swiper;
		this.swiper = await new Swiper( this.elements.$container, swiperOptions );

		// Keep a handle on autoplay delay for the progress bar even if
		// Swiper's internal params get normalized differently.
		this.autoplayDelay = swiperOptions.autoplay ? swiperOptions.autoplay.delay : 0;
		this.autoplayEnabled = !! swiperOptions.autoplay;
	}

	destroySwiper() {
		if ( this.swiper && typeof this.swiper.destroy === 'function' ) {
			this.swiper.destroy( true, true );
			this.swiper = null;
		}
		if ( this._progressRaf ) {
			cancelAnimationFrame( this._progressRaf );
			this._progressRaf = null;
		}
	}

	onElementChange() {
		// Elementor editor: re-init cleanly when widget settings change.
		this.onInit();
	}

	/* -----------------------------------------------------------
	 * Autoplay progress bar
	 * ----------------------------------------------------------- */
	bindProgressBar() {
		if ( ! this.elements.$progressBar.length || ! this.swiper ) return;

		const bar = this.elements.$progressBar[ 0 ];

		const runBar = () => {
			if ( ! this.autoplayEnabled || ! this.swiper || ! this.swiper.autoplay || ! this.swiper.autoplay.running ) {
				return;
			}
			bar.classList.remove( 'is-animating' );
			bar.style.width = '0%';
			// Force reflow so the transition restarts cleanly each slide.
			// eslint-disable-next-line no-unused-expressions
			bar.offsetHeight;
			bar.style.transitionDuration = this.autoplayDelay + 'ms';
			bar.classList.add( 'is-animating' );
			bar.style.width = '100%';
		};

		this.swiper.on( 'slideChangeTransitionStart', runBar );
		this.swiper.on( 'autoplayStart', runBar );
		this.swiper.on( 'autoplayStop', () => {
			bar.classList.remove( 'is-animating' );
		} );
		this.swiper.on( 'autoplayPause', () => {
			bar.style.transitionDuration = '0ms';
		} );
		this.swiper.on( 'autoplayResume', runBar );

		if ( this.autoplayEnabled ) runBar();
	}

	/* -----------------------------------------------------------
	 * Slide counter
	 * ----------------------------------------------------------- */
	bindCounter() {
		if ( ! this.elements.$counterCurrent.length || ! this.swiper ) return;
		const total = this.swiper.slides.length - ( this.swiper.loopedSlides ? this.swiper.loopedSlides * 2 : 0 );
		this.elements.$counterTotal.text( String( total ).padStart( 2, '0' ) );
		this.updateCounter();
		this.swiper.on( 'slideChange', () => this.updateCounter() );
	}

	updateCounter() {
		if ( ! this.swiper ) return;
		const idx = ( this.swiper.realIndex !== undefined ? this.swiper.realIndex : this.swiper.activeIndex ) + 1;
		this.elements.$counterCurrent.text( String( idx ).padStart( 2, '0' ) );
	}

	/* -----------------------------------------------------------
	 * Pause / play toggle button
	 * ----------------------------------------------------------- */
	bindToggle() {
		if ( ! this.elements.$toggleBtn.length || ! this.swiper ) return;
		this.elements.$toggleBtn.off( 'click.advSlider' ).on( 'click.advSlider', () => {
			if ( ! this.swiper.autoplay ) return;
			if ( this.swiper.autoplay.running ) {
				this.swiper.autoplay.stop();
				this.elements.$toggleBtn.attr( 'data-state', 'paused' );
			} else {
				this.swiper.autoplay.start();
				this.elements.$toggleBtn.attr( 'data-state', 'playing' );
			}
		} );
	}

	/* -----------------------------------------------------------
	 * Keyboard navigation (left/right arrows when slider is focused)
	 * ----------------------------------------------------------- */
	bindKeyboard() {
		if ( ! this.swiper ) return;
		this.elements.$wrapper.attr( 'tabindex', '0' );
		this.elements.$wrapper.off( 'keydown.advSlider' ).on( 'keydown.advSlider', ( e ) => {
			if ( e.key === 'ArrowRight' ) this.swiper.slideNext();
			if ( e.key === 'ArrowLeft' ) this.swiper.slidePrev();
		} );
	}

	/* -----------------------------------------------------------
	 * Background video: play only while its slide is active
	 * ----------------------------------------------------------- */
	bindKenBurnsVideoSync() {
		if ( ! this.swiper ) return;
		const syncVideos = () => {
			this.elements.$container.find( '.adv-slide-bg-video' ).each( ( i, vid ) => {
				const $slide = jQuery( vid ).closest( '.swiper-slide' );
				if ( $slide.hasClass( 'swiper-slide-active' ) ) {
					vid.currentTime = 0;
					const playPromise = vid.play();
					if ( playPromise && playPromise.catch ) playPromise.catch( () => {} );
				} else {
					vid.pause();
				}
			} );
		};
		this.swiper.on( 'slideChangeTransitionStart', syncVideos );
		syncVideos();
	}

	handleSlideChange() {
		// Placeholder hook kept for extension (e.g. custom events, GA tracking).
		this.elements.$wrapper.trigger( 'adv-slider:slideChange', [ this.swiper ] );
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	const addHandler = ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( AdvancedSliderHandler, {
			$element,
		} );
	};
	elementorFrontend.hooks.addAction( 'frontend/element_ready/advanced_slider.default', addHandler );
} );
