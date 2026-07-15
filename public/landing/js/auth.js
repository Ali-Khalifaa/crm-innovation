(function($) {
   'use strict';

   var FIELD_ALIASES = {
      password_confirmation: ['password_confirm']
   };

   function showAuthToast(message, type) {
      type = type || 'success';
      var existing = document.querySelector('.auth-toast');
      if (existing) existing.remove();

      var toast = document.createElement('div');
      toast.className = 'auth-toast ' + type;
      toast.setAttribute('role', 'alert');
      toast.textContent = message;
      document.body.appendChild(toast);

      setTimeout(function() {
         toast.style.transition = 'opacity 0.4s';
         toast.style.opacity = '0';
         setTimeout(function() { toast.remove(); }, 400);
      }, 2500);
   }

   function clearAuthFormErrors(form) {
      $(form).find('.auth-field-error').removeClass('show').text('');
      $(form).find('.is-invalid').removeClass('is-invalid');
   }

   function findFormInput(form, field) {
      var input = form.querySelector('[name="' + field + '"]');
      if (input) return input;

      var aliases = FIELD_ALIASES[field] || [];
      for (var i = 0; i < aliases.length; i++) {
         input = form.querySelector('[name="' + aliases[i] + '"]');
         if (input) return input;
      }

      return null;
   }

   function showAuthFormErrors(form, errors) {
      clearAuthFormErrors(form);
      if (!errors) return;

      Object.keys(errors).forEach(function(field) {
         var messages = errors[field];
         var msg = Array.isArray(messages) ? messages[0] : messages;
         var input = findFormInput(form, field);
         if (input) input.classList.add('is-invalid');

         var errorEl = form.querySelector('.auth-field-error[data-for="' + field + '"]');
         if (errorEl) {
            errorEl.textContent = msg;
            errorEl.classList.add('show');
         }
      });
   }

   function saveAuthSession(payload) {
      if (!payload) return;

      var token = payload.access_token || payload.token;
      if (token) {
         localStorage.setItem('crm_token', token);
      }
      if (payload.user) {
         localStorage.setItem('crm_user', JSON.stringify(payload.user));
      }
      if (payload.tenant) {
         localStorage.setItem('crm_tenant', JSON.stringify(payload.tenant));
      }
   }

   function buildAuthPayload(form) {
      var authType = form.getAttribute('data-auth-type') || 'login';

      if (authType === 'register') {
         var billingCycle = form.querySelector('[name="billing_cycle"]:checked');
         return {
            name: (form.querySelector('[name="name"]') || {}).value || '',
            company_name: (form.querySelector('[name="company_name"]') || {}).value || '',
            email: (form.querySelector('[name="email"]') || {}).value || '',
            phone: (form.querySelector('[name="phone"]') || {}).value || '',
            password: (form.querySelector('[name="password"]') || {}).value || '',
            password_confirmation: (form.querySelector('[name="password_confirmation"]') || {}).value || '',
            plan_id: parseInt((form.querySelector('[name="plan_id"]:checked') || form.querySelector('[name="plan_id"]') || {}).value, 10) || null,
            billing_cycle: billingCycle ? billingCycle.value : 'monthly'
         };
      }

      return {
         email: (form.querySelector('[name="email"]') || {}).value || '',
         password: (form.querySelector('[name="password"]') || {}).value || ''
      };
   }

   $('.password-toggle').on('click', function() {
      var $input = $(this).siblings('input');
      var $icon = $(this).find('i');
      if ($input.attr('type') === 'password') {
         $input.attr('type', 'text');
         $icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
         $input.attr('type', 'password');
         $icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
   });

   $(document).ready(function() {
      $('.auth-ajax-form').on('submit', function(e) {
         e.preventDefault();

         var form = this;
         var $form = $(form);
         var $btn = $form.find('.auth-submit-btn');
         var btnText = $btn.data('original-text') || $btn.text();
         var loadingText = $form.data('loading') || btnText;
         var networkError = $form.data('network-error') || 'Network error. Please try again.';
         var redirectUrl = $form.data('redirect') || '/crm/dashboard';

         if (!$btn.data('original-text')) {
            $btn.data('original-text', btnText);
         }

         clearAuthFormErrors(form);
         $btn.prop('disabled', true).addClass('disabled').text(loadingText);

         fetch(form.action, {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'Accept': 'application/json',
               'X-Requested-With': 'XMLHttpRequest',
               'Accept-Language': document.documentElement.lang || 'ar'
            },
            body: JSON.stringify(buildAuthPayload(form))
         })
         .then(function(res) {
            return res.json().then(function(data) {
               return { ok: res.ok, status: res.status, data: data };
            }).catch(function() {
               return { ok: res.ok, status: res.status, data: {} };
            });
         })
         .then(function(result) {
            if (result.ok && result.data.success !== false && result.data.data) {
               saveAuthSession(result.data.data);
               showAuthToast(result.data.message || 'OK', 'success');
               setTimeout(function() {
                  window.location.href = redirectUrl;
               }, 600);
               return;
            }

            if (result.status === 422 && result.data.errors) {
               showAuthFormErrors(form, result.data.errors);
               if (result.data.message) {
                  showAuthToast(result.data.message, 'error');
               }
               return;
            }

            showAuthToast(result.data.message || networkError, 'error');
         })
         .catch(function() {
            showAuthToast(networkError, 'error');
         })
         .finally(function() {
            $btn.prop('disabled', false).removeClass('disabled').text(btnText);
         });
      });
   });
})(jQuery);
