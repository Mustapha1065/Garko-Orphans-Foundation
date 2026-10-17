/**
 * GOF Platform - Main JavaScript file
 */

const GOF = (function() {
    'use strict';

    // Toast System
    const toast = {
        container: null,
        
        init: function() {
            if (!document.getElementById('gof-toast-container')) {
                const container = document.createElement('div');
                container.id = 'gof-toast-container';
                document.body.appendChild(container);
                this.container = container;
            } else {
                this.container = document.getElementById('gof-toast-container');
            }
        },

        show: function(message, type = 'info') {
            if (!this.container) this.init();

            let icon = 'bi-info-circle';
            if (type === 'success') icon = 'bi-check-circle';
            if (type === 'error') icon = 'bi-exclamation-octagon';
            if (type === 'warning') icon = 'bi-exclamation-triangle';

            const toastEl = document.createElement('div');
            toastEl.className = `gof-toast gof-toast-${type}`;
            toastEl.innerHTML = `
                <div class="gof-toast-indicator"></div>
                <div class="gof-toast-content">
                    <i class="bi ${icon} gof-toast-icon"></i>
                    <div class="gof-toast-message">${message}</div>
                </div>
                <button class="gof-toast-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            `;

            this.container.appendChild(toastEl);
            
            // Trigger reflow to enable transition
            void toastEl.offsetWidth;
            toastEl.classList.add('show');

            setTimeout(() => {
                toastEl.classList.remove('show');
                setTimeout(() => toastEl.remove(), 300);
            }, 4000);
        }
    };

    // Confirm Dialog
    const confirm = function(message) {
        return new Promise((resolve) => {
            const confirmed = window.confirm(message);
            resolve(confirmed);
        });
    };

    // AJAX Helper
    const ajax = {
        post: async function(url, data) {
            try {
                const isFormData = data instanceof FormData;
                const options = {
                    method: 'POST',
                    body: isFormData ? data : JSON.stringify(data)
                };

                if (!isFormData) {
                    options.headers = {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    };
                }

                const response = await fetch(url, options);
                if (!response.ok) throw new Error('Network response was not ok');
                return await response.json();
            } catch (error) {
                console.error('AJAX Error:', error);
                throw error;
            }
        }
    };

    // Formatters
    const format = {
        currency: function(amount, currency = 'NGN') {
            return new Intl.NumberFormat('en-NG', { style: 'currency', currency: currency }).format(amount);
        },
        date: function(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
        }
    };

    // Init functionality on DOM Content Loaded
    document.addEventListener('DOMContentLoaded', function() {
        toast.init();

        // 1. Sidebar Toggle (Mobile)
        const sidebarToggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('gofSidebar');
        if (sidebarToggleBtn && sidebar) {
            sidebarToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992 && sidebar.classList.contains('show') && !sidebar.contains(e.target) && e.target !== sidebarToggleBtn && !sidebarToggleBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
        }

        // 2. Auto-dismiss flash alerts
        const flashes = document.querySelectorAll('.gof-flash');
        flashes.forEach(flash => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(flash);
                bsAlert.close();
            }, 4000);
        });

        // 3. Form dirty state warning
        let isFormDirty = false;
        const dirtyForms = document.querySelectorAll('form.dirty-check');
        dirtyForms.forEach(form => {
            form.addEventListener('change', () => { isFormDirty = true; });
            form.addEventListener('submit', () => { isFormDirty = false; });
        });
        window.addEventListener('beforeunload', (e) => {
            if (isFormDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // 4. File upload preview
        const imageInputs = document.querySelectorAll('input[type="file"].image-preview');
        imageInputs.forEach(input => {
            input.addEventListener('change', function() {
                const targetId = this.dataset.previewTarget;
                if (!targetId) return;
                const target = document.getElementById(targetId);
                if (target && this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        target.src = e.target.result;
                        target.style.display = 'block';
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });

        // 5. Initialize Bootstrap tooltips & popovers
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // 6. Notification Mock Fetch (Dropdown animate)
        const notifBtn = document.getElementById('notifBtn');
        if (notifBtn) {
            notifBtn.addEventListener('show.bs.dropdown', () => {
                // Here we would fetch /api/notifications
                console.log('Fetching notifications...');
            });
        }
        
        // 7. Password Toggle
        const passToggles = document.querySelectorAll('.password-toggle');
        passToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = document.getElementById(this.dataset.target);
                if(input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = '<i class="bi bi-eye-slash"></i>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<i class="bi bi-eye"></i>';
                    }
                }
            });
        });

        // 8. Chart.js Defaults if loaded
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#6B7280';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 46, 30, 0.9)';
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 6;
        }
    });

    return {
        toast,
        confirm,
        ajax,
        format
    };

})();
