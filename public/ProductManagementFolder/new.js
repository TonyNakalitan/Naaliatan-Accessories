let currentStep = 1;
const totalSteps = 4;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload handlers
    initializeFileUpload();
    
    // Initialize character search if exists
    const searchInput = document.getElementById('characterSearch');
    if (searchInput) {
        searchInput.addEventListener('input', filterCharacters);
    }
});

function moveStep(delta) {
    const nextStep = currentStep + delta;
    if (nextStep < 1 || nextStep > totalSteps) return;

    const currentStepEl = document.querySelector(`[data-step="${currentStep}"]`);
    const nextStepEl = document.querySelector(`[data-step="${nextStep}"]`);

    if (!currentStepEl || !nextStepEl) return;

    currentStepEl.classList.remove('active');
    nextStepEl.classList.add('active');

    updateStepIndicators(currentStep, nextStep);
    currentStep = nextStep;
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (currentStep > 1) {
        if (prevBtn) {
            prevBtn.classList.remove('opacity-0', 'pointer-events-none');
        }
    } else {
        if (prevBtn) {
            prevBtn.classList.add('opacity-0', 'pointer-events-none');
        }
    }

    if (currentStep === totalSteps) {
        if (nextBtn) nextBtn.classList.add('hidden');
        if (submitBtn) submitBtn.classList.remove('hidden');
    } else {
        if (nextBtn) nextBtn.classList.remove('hidden');
        if (submitBtn) submitBtn.classList.add('hidden');
    }
}

function updateStepIndicators(oldStep, newStep) {
    const oldPill = document.getElementById(`pill-${oldStep}`);
    const newPill = document.getElementById(`pill-${newStep}`);

    if (!oldPill || !newPill) return;

    if (newStep > oldStep) {
        oldPill.classList.remove('active');
        oldPill.classList.add('completed');
        const oldSpan = oldPill.querySelector('span');
        if (oldSpan) {
            oldSpan.classList.replace('text-indigo-600', 'text-emerald-600');
        }
    } else {
        newPill.classList.remove('completed');
        const newSpan = newPill.querySelector('span');
        if (newSpan) {
            newSpan.classList.replace('text-emerald-600', 'text-indigo-600');
        }
    }
    
    newPill.classList.add('active');
}

function syncPreview(type, val) {
    const elements = {
        name: 'previewName',
        code: 'previewCode',
        description: 'previewDesc',
        price: 'previewPrice'
    };

    const target = document.getElementById(elements[type]);
    if (!target) return;

    if (!val || val.trim() === '') {
        const defaults = {
            name: 'Product Name',
            code: '#PRODUCT-CODE',
            description: 'Product description will appear here...',
            price: '₱0.00'
        };
        target.innerText = defaults[type];
    } else {
        if (type === 'price') {
            const numVal = parseFloat(val);
            if (!isNaN(numVal)) {
                target.innerText = `₱${numVal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
        } else if (type === 'code') {
            target.innerText = `#${val.toUpperCase()}`;
        } else {
            target.innerText = val;
        }
    }
}

function selectChar(el, id, name) {
    if (!el || !id || !name) return;

    // Remove selected class from all cards
    document.querySelectorAll('.character-card').forEach(c => c.classList.remove('selected'));
    // Add selected class to clicked card
    el.classList.add('selected');
    
    // Set the hidden select value
    let selectElement = document.querySelector('select[name*="character"]');
    if (selectElement) {
        selectElement.value = id;
        console.log('Character selected:', id, name);
    } else {
        console.error('Character select element not found');
    }
    
    // Update the preview badge
    const charBadge = document.getElementById('previewChar');
    if (charBadge) {
        charBadge.textContent = name;
        charBadge.setAttribute('class', 'px-2 py-0.5 backdrop-blur-md rounded-full text-white text-[9px] sm:text-[10px] font-semibold border border-indigo-400 bg-indigo-600');
    }
}

function filterCharacters() {
    const searchInput = document.getElementById('characterSearch');
    if (!searchInput) return;

    const searchTerm = searchInput.value.toLowerCase();
    const characters = document.querySelectorAll('.character-item');
    
    characters.forEach(char => {
        const name = char.getAttribute('data-character-name');
        if (name && name.includes(searchTerm)) {
            char.style.display = '';
        } else {
            char.style.display = 'none';
        }
    });
}

function initializeFileUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.querySelector('input[type="file"]');
    const productForm = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');

    // Form submission handler
    if (productForm && submitBtn) {
        productForm.addEventListener('submit', function(e) {
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

    if (dropZone && fileInput) {
        // Click handler for drop zone
        dropZone.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                fileInput.click();
            }
        });

        // Drag and drop handlers
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', function() {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFile(fileInput);
            }
        });

        // File input change handler
        fileInput.addEventListener('change', function() {
            handleFile(this);
        });
    }
}

function handleFile(input) {
    const file = input.files[0];
    if (!file) return;

    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image file (JPEG, PNG, GIF, or WebP)');
        input.value = '';
        return;
    }

    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
        alert('File size must be less than 5MB');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const imgResult = document.getElementById('imgResult');
        const previewMainImg = document.getElementById('previewMainImg');
        const dropZoneUI = document.getElementById('dropZoneUI');
        const previewContainer = document.getElementById('previewContainer');

        if (imgResult) imgResult.src = e.target.result;
        if (previewMainImg) previewMainImg.src = e.target.result;
        if (dropZoneUI) dropZoneUI.classList.add('hidden');
        if (previewContainer) previewContainer.classList.remove('hidden');
    };
    
    reader.onerror = () => {
        alert('Error reading file. Please try again.');
        input.value = '';
    };
    
    reader.readAsDataURL(file);
}

function resetFile(event) {
    if (event) {
        event.stopPropagation();
    }
    
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.value = '';
    }

    const imgResult = document.getElementById('imgResult');
    const previewMainImg = document.getElementById('previewMainImg');
    const dropZoneUI = document.getElementById('dropZoneUI');
    const previewContainer = document.getElementById('previewContainer');

    if (imgResult) imgResult.src = '';
    if (previewMainImg) {
        previewMainImg.src = 'https://placehold.co/400x500/f1f5f9/94a3b8?text=Product+Image';
    }
    if (dropZoneUI) dropZoneUI.classList.remove('hidden');
    if (previewContainer) previewContainer.classList.add('hidden');
}
