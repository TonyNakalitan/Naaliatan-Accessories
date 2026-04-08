 let currentStep = 1;
        const totalSteps = 2;

        function moveStep(delta) {
            const nextStep = currentStep + delta;
            if (nextStep < 1 || nextStep > totalSteps) return;

            document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');
            document.querySelector(`[data-step="${nextStep}"]`).classList.add('active');

            updateStepIndicators(currentStep, nextStep);
            currentStep = nextStep;
            
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            if (currentStep > 1) {
                prevBtn.classList.remove('opacity-0', 'pointer-events-none');
            } else {
                prevBtn.classList.add('opacity-0', 'pointer-events-none');
            }

            if (currentStep === totalSteps) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function updateStepIndicators(oldStep, newStep) {
            const oldPill = document.getElementById(`pill-${oldStep}`);
            const newPill = document.getElementById(`pill-${newStep}`);

            if (newStep > oldStep) {
                oldPill.classList.remove('active');
                oldPill.classList.add('completed');
                oldPill.querySelector('span').classList.replace('text-indigo-600', 'text-emerald-600');
            } else {
                newPill.classList.remove('completed');
                newPill.querySelector('span').classList.replace('text-emerald-600', 'text-indigo-600');
            }
            
            newPill.classList.add('active');
        }

        function syncPreview(type, val) {
            const elements = {
                quantity: 'previewQuantity',
                notes: 'previewNotes'
            };

            const target = document.getElementById(elements[type]);
            if (!target) return;

            if (!val || val.trim() === '') {
                const defaults = {
                    quantity: '0',
                    notes: 'No notes added yet...'
                };
                target.innerText = defaults[type];
                if (type === 'notes') target.classList.add('italic');
            } else {
                target.innerText = val;
                if (type === 'notes') target.classList.remove('italic');
            }
        }

        function selectProduct(el, id, name, code) {
            // Remove selected class from all cards
            document.querySelectorAll('.product-card').forEach(c => c.classList.remove('selected'));
            // Add selected class to clicked card
            el.classList.add('selected');
            
            // Set the hidden select value
            let selectElement = document.querySelector('select[name*="product"]');
            if (selectElement) {
                selectElement.value = id;
                console.log('Product selected:', id, name);
            } else {
                console.error('Product select element not found');
            }
            
            // Update the preview
            document.getElementById('previewProductName').textContent = name;
            document.getElementById('previewProductCode').textContent = '#' + code;
        }

        function filterProducts() {
            const searchTerm = document.getElementById('productSearch').value.toLowerCase();
            const products = document.querySelectorAll('.product-item');
            
            products.forEach(prod => {
                const name = prod.getAttribute('data-product-name');
                if (name.includes(searchTerm)) {
                    prod.style.display = '';
                } else {
                    prod.style.display = 'none';
                }
            });
        }

        // Form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const stockForm = document.getElementById('stockForm');
            const submitBtn = document.getElementById('submitBtn');

            if (stockForm && submitBtn) {
                stockForm.addEventListener('submit', function(e) {
                    // Show loading state
                    const btnText = document.getElementById('submitBtnText');
                    const btnLoading = document.getElementById('submitBtnLoading');
                    
                    if (btnText && btnLoading) {
                        btnText.classList.add('hidden');
                        btnLoading.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    }
                });
            }
        });