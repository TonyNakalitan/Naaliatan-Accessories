let currentStep = 1;
const totalSteps = 5;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload handlers
    initializeFileUpload();
    
    // Initialize character search if exists
    const searchInput = document.getElementById('characterSearch');
    if (searchInput) {
        searchInput.addEventListener('input', filterCharacters);
    }

    // Initialize color picker
    initializeColorPicker();
});

function moveStep(delta) {
    const nextStep = currentStep + delta;
    console.log(`moveStep called: currentStep=${currentStep}, delta=${delta}, nextStep=${nextStep}`);
    
    if (nextStep < 1 || nextStep > totalSteps) {
        console.log('Invalid step, returning');
        return;
    }

    const currentStepEl = document.querySelector(`[data-step="${currentStep}"]`);
    const nextStepEl = document.querySelector(`[data-step="${nextStep}"]`);

    if (!currentStepEl || !nextStepEl) {
        console.log('Step elements not found, returning');
        return;
    }

    currentStepEl.classList.remove('active');
    nextStepEl.classList.add('active');

    // Initialize color picker when moving to step 4
    if (nextStep === 4) {
        initializeColorPicker();
    }

    // Update confirmation data when moving to step 5
    if (nextStep === 5) {
        console.log('Moving to step 5 - updating confirmation summary');
        updateConfirmationSummary();
    }

    updateStepIndicators(currentStep, nextStep);
    currentStep = nextStep;
    console.log(`Successfully moved to step ${currentStep}`);
    
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

    // Update confirmation if visible
    updateConfirmationIfVisible();
}

function updateConfirmationIfVisible() {
    // Only update confirmation if we're on step 5
    if (currentStep === 5) {
        updateConfirmationSummary();
    }
}

function updateColorPreview(hex) {
    const colorPreview = document.getElementById('colorPreview');
    const hexDisplay = document.getElementById('hexDisplay');
    const colorPicker = document.getElementById('colorPicker');

    if (!hex) return;

    // Remove # if present and ensure uppercase
    hex = hex.replace('#', '').toUpperCase();

    // Validate hex format
    if (/^[0-9A-F]{6}$/i.test(hex)) {
        const color = '#' + hex;
        if (colorPreview) colorPreview.style.backgroundColor = color;
        if (hexDisplay) hexDisplay.textContent = color;
        if (colorPicker) colorPicker.value = color;

        // Update form field
        const hexInput = document.querySelector('input[name*="colorHex"]');
        if (hexInput) hexInput.value = hex;
    }
}

function updateHexFromPicker(color) {
    const hex = color.replace('#', '').toUpperCase();
    updateColorPreview(hex);

    // Update form field
    const hexInput = document.querySelector('input[name*="colorHex"]');
    if (hexInput) hexInput.value = hex;
}

function selectPresetColor(hex) {
    updateColorPreview(hex);

    // Update form field
    const hexInput = document.querySelector('input[name*="colorHex"]');
    if (hexInput) hexInput.value = hex;

    // Update confirmation if visible
    updateConfirmationIfVisible();
}

function initializeColorPicker() {
    const hexInput = document.querySelector('input[name*="colorHex"]');
    const colorPicker = document.getElementById('colorPicker');

    if (hexInput && hexInput.value) {
        // If there's already a value, update the preview
        updateColorPreview(hexInput.value);
    } else if (hexInput && colorPicker) {
        // Set default color if no value exists
        const defaultColor = 'FF6B6B';
        hexInput.value = defaultColor;
        colorPicker.value = '#' + defaultColor;
        updateColorPreview(defaultColor);
    }
}

function updateConfirmationSummary() {
    // Get form values
    const nameInput = document.querySelector('input[name*="name"]');
    const codeInput = document.querySelector('input[name*="productCode"]');
    const priceInput = document.querySelector('input[name*="price"]');
    const descriptionInput = document.querySelector('textarea[name*="description"]');
    const characterSelect = document.querySelector('select[name*="character"]');
    const colorHexInput = document.querySelector('input[name*="colorHex"]');

    // Update confirmation elements
    const confirmName = document.getElementById('confirmName');
    const confirmCode = document.getElementById('confirmCode');
    const confirmPrice = document.getElementById('confirmPrice');
    const confirmDescription = document.getElementById('confirmDescription');
    const confirmCharacter = document.getElementById('confirmCharacter');
    const confirmColor = document.getElementById('confirmColor');

    if (confirmName && nameInput) {
        confirmName.textContent = nameInput.value || 'Not specified';
    }

    if (confirmCode && codeInput) {
        confirmCode.textContent = codeInput.value ? `#${codeInput.value.toUpperCase()}` : 'Not specified';
    }

    if (confirmPrice && priceInput) {
        const priceValue = parseFloat(priceInput.value);
        if (!isNaN(priceValue)) {
            confirmPrice.textContent = `${priceValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        } else {
            confirmPrice.textContent = '0.00';
        }
    }

    if (confirmDescription && descriptionInput) {
        confirmDescription.textContent = descriptionInput.value || 'No description provided';
    }

    if (confirmCharacter && characterSelect) {
        const selectedOption = characterSelect.options[characterSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            confirmCharacter.textContent = selectedOption.text;
        } else {
            confirmCharacter.textContent = 'None selected';
        }
    }

    if (confirmColor && colorHexInput) {
        confirmColor.textContent = colorHexInput.value ? `#${colorHexInput.value.toUpperCase()}` : 'Not specified';
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

function loadCharacterPage(page) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('char_page', page);
    window.location.href = currentUrl.toString();
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
