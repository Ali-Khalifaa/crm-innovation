var isRtl = document.documentElement.dir === 'rtl';

// Smooth scrolling for anchor links
         $(document).ready(function(){
            function scrollToTarget(target) {
               if (!target) return;
               target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            $('a[href*="#"]').not('[href="#"]').click(function(e) {
               if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                  var target = document.querySelector(this.hash);
                  if (target) {
                     e.preventDefault();
                     scrollToTarget(target);
                     if (history.pushState) {
                        history.pushState(null, null, this.hash);
                     }
                     return false;
                  }
               }
            });

            // Scroll to hash on page load
            if (window.location.hash) {
               var hashTarget = document.querySelector(window.location.hash);
               if (hashTarget) {
                  setTimeout(function() {
                     scrollToTarget(hashTarget);
                  }, 300);
               }
            }
         });

         // Initialize counter for statistics
         $(document).ready(function() {
            if ($('.counter').length) {
               $('.counter').counterUp({
                  delay: 10,
                  time: 2000
               });
            }
         });

         // Image sliders (hero + solutions)
         $(document).ready(function() {
            $('.hero-img-slider').each(function() {
               var $slider = $(this);
               var $slides = $slider.find('.hero-slide');
               var $dots = $slider.next('.hero-slider-dots').find('button');

               if ($slides.length <= 1) {
                  return;
               }

               var current = 0;

               function goToSlide(index) {
                  $slides.removeClass('active');
                  $dots.removeClass('active');
                  current = index;
                  $slides.eq(current).addClass('active');
                  $dots.eq(current).addClass('active');
               }

               setInterval(function() {
                  goToSlide((current + 1) % $slides.length);
               }, 4000);

               $dots.on('click', function() {
                  goToSlide($(this).index());
               });
            });
         });

         // Partner slider - single init (main.js won't touch crm-partner-slider)
         $(document).ready(function() {
            if ($('.crm-partner-slider').length && typeof $.fn.owlCarousel !== 'undefined') {
               $('.crm-partner-slider').owlCarousel({
                  loop: true,
                  margin: 30,
                  autoplay: true,
                  autoplayTimeout: 3000,
                  autoplayHoverPause: true,
                  smartSpeed: 800,
                  dots: false,
                  nav: false,
                  rtl: false,
                  responsive: {
                     0: { items: 2 },
                     400: { items: 3 },
                     600: { items: 4 },
                     992: { items: 6 }
                  }
               });
            }
         });

         // Fix Testimonial Slider - destroy main.js init and re-init with RTL
         $(window).on('load', function() {
            if ($('.testimonial-slider-1').length && typeof $.fn.owlCarousel !== 'undefined') {
               var $slider = $('.testimonial-slider-1');
               // Destroy and reinitialize after short delay to ensure main.js has finished
               setTimeout(function() {
                  if ($slider.data('owl.carousel')) {
                     $slider.trigger('destroy.owl.carousel').removeClass('owl-loaded');
                     $slider[0].innerHTML = $slider[0].innerHTML; // reset DOM
                  }
                  $slider.owlCarousel({
                     loop: true,
                     margin: 30,
                     nav: true,
                     dots: true,
                     rtl: isRtl,
                     autoplay: true,
                     autoplayTimeout: 5000,
                     autoplayHoverPause: true,
                     navText: isRtl ? ['<i class="fa fa-angle-right"></i>', '<i class="fa fa-angle-left"></i>'] : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                     responsive: {
                        0: { items: 1 },
                        768: { items: 2 },
                        992: { items: 3 }
                     }
                  });
               }, 500);
            }
         });

         // Landing AJAX forms (contact + newsletter)
         function showLandingToast(message, type) {
            type = type || 'success';
            var existing = document.querySelector('.landing-toast');
            if (existing) existing.remove();

            var toast = document.createElement('div');
            toast.className = 'landing-toast ' + type;
            toast.setAttribute('role', 'alert');
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(function() {
               toast.style.transition = 'opacity 0.4s';
               toast.style.opacity = '0';
               setTimeout(function() { toast.remove(); }, 400);
            }, 4500);
         }

         window.showLandingToast = showLandingToast;

         function clearLandingFormErrors(form) {
            $(form).find('.landing-field-error').removeClass('show').text('');
            $(form).find('.is-invalid').removeClass('is-invalid');
         }

         function showLandingFormErrors(form, errors) {
            clearLandingFormErrors(form);
            if (!errors) return;

            Object.keys(errors).forEach(function(field) {
               var messages = errors[field];
               var msg = Array.isArray(messages) ? messages[0] : messages;
               var input = form.querySelector('[name="' + field + '"]');
               if (input) input.classList.add('is-invalid');

               var errorEl = form.querySelector('.landing-field-error[data-for="' + field + '"]');
               if (!errorEl && form.parentElement) {
                  errorEl = form.parentElement.querySelector('.landing-field-error[data-for="' + field + '"]');
               }
               if (errorEl) {
                  errorEl.textContent = msg;
                  errorEl.classList.add('show');
               }
            });
         }

         function getCsrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
         }

         $(document).ready(function() {
            var sessionToast = document.getElementById('landing-toast');
            if (sessionToast) {
               setTimeout(function() {
                  sessionToast.style.transition = 'opacity 0.4s';
                  sessionToast.style.opacity = '0';
                  setTimeout(function() { sessionToast.remove(); }, 400);
               }, 4500);
            }

            $('.landing-ajax-form').on('submit', function(e) {
               e.preventDefault();

               var form = this;
               var $form = $(form);
               var $btn = $form.find('.landing-submit-btn');
               var btnText = $btn.data('original-text') || $btn.text();
               var successMsg = $form.data('success') || '';

               if (!$btn.data('original-text')) {
                  $btn.data('original-text', btnText);
               }

               clearLandingFormErrors(form);
               $btn.prop('disabled', true).addClass('disabled');

               var formData = new FormData(form);

               fetch(form.action, {
                  method: 'POST',
                  headers: {
                     'X-Requested-With': 'XMLHttpRequest',
                     'Accept': 'application/json',
                     'X-CSRF-TOKEN': getCsrfToken()
                  },
                  body: formData
               })
               .then(function(res) {
                  return res.json().then(function(data) {
                     return { ok: res.ok, status: res.status, data: data };
                  }).catch(function() {
                     return { ok: res.ok, status: res.status, data: {} };
                  });
               })
               .then(function(result) {
                  if (result.ok && result.data.success !== false) {
                     showLandingToast(result.data.message || successMsg, 'success');
                     form.reset();
                     clearLandingFormErrors(form);
                     return;
                  }

                  if (result.status === 422) {
                     if (result.data.errors) {
                        showLandingFormErrors(form, result.data.errors);
                     } else if (result.data.message) {
                        showLandingToast(result.data.message, 'error');
                     }
                     return;
                  }

                  showLandingToast(result.data.message || 'Something went wrong', 'error');
               })
               .catch(function() {
                  showLandingToast('Network error. Please try again.', 'error');
               })
               .finally(function() {
                  $btn.prop('disabled', false).removeClass('disabled').text(btnText);
               });
            });
         });
