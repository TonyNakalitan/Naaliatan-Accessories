let currentStep = 1;
const totalSteps = 5;
let selectedAlignment = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload handlers
    initializeFileUpload();
    
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

function selectAlignment(alignment, element) {
    selectedAlignment = alignment;
    
    // Remove selected class from all alignment cards
    document.querySelectorAll('.alignment-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    if (element) {
        element.classList.add('selected');
    }
    
    // Set the hidden select value
    let selectElement = document.querySelector('select[name*="alignment"]');
    if (selectElement) {
        selectElement.value = alignment;
        console.log('Alignment selected:', alignment);
    } else {
        console.error('Alignment select element not found');
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

        // Store value WITHOUT # (# is shown as visual prefix in the input)
        const hexInput = document.querySelector('input[name*="colorCode"]');
        if (hexInput) hexInput.value = hex;
    }
}

function updateHexFromPicker(color) {
    const hex = color.replace('#', '').toUpperCase();
    updateColorPreview(hex);
}

function selectPresetColor(hex) {
    hex = hex.replace('#', '').toUpperCase();
    updateColorPreview(hex);

    // Update confirmation if visible
    updateConfirmationIfVisible();
}

function initializeColorPicker() {
    const hexInput = document.querySelector('input[name*="colorCode"]');
    const colorPicker = document.getElementById('colorPicker');

    if (hexInput && hexInput.value) {
        // If there's already a value, update the preview
        updateColorPreview(hexInput.value);
    } else if (hexInput && colorPicker) {
        // Set default color without # prefix
        const defaultHex = 'FF6B6B';
        hexInput.value = defaultHex;
        colorPicker.value = '#' + defaultHex;
        updateColorPreview(defaultHex);
    }
}

function updateConfirmationSummary() {
    // Get form values
    const nameInput = document.querySelector('input[name*="name"]');
    const creatorInput = document.querySelector('input[name*="creator"]');
    const descriptionInput = document.querySelector('textarea[name*="description"]');
    const alignmentSelect = document.querySelector('select[name*="alignment"]');
    const colorHexInput = document.querySelector('input[name*="colorCode"]');

    // Update confirmation elements
    const confirmName = document.getElementById('confirmName');
    const confirmCreator = document.getElementById('confirmCreator');
    const confirmAlignment = document.getElementById('confirmAlignment');
    const confirmColor = document.getElementById('confirmColor');
    const confirmDescription = document.getElementById('confirmDescription');

    if (confirmName && nameInput) {
        confirmName.textContent = nameInput.value || 'Not specified';
    }

    if (confirmCreator && creatorInput) {
        confirmCreator.textContent = creatorInput.value || 'Not specified';
    }

    if (confirmAlignment && alignmentSelect) {
        const selectedOption = alignmentSelect.options[alignmentSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            confirmAlignment.textContent = selectedOption.text;
        } else {
            confirmAlignment.textContent = 'None selected';
        }
    }

    if (confirmColor && colorHexInput) {
        const rawVal = colorHexInput.value ? colorHexInput.value.replace('#', '').toUpperCase() : null;
        confirmColor.textContent = rawVal ? '#' + rawVal : 'Not specified';
    }

    if (confirmDescription && descriptionInput) {
        confirmDescription.textContent = descriptionInput.value || 'No description provided';
    }
}

function initializeFileUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.querySelector('input[type="file"]');
    const characterForm = document.getElementById('characterForm');
    const submitBtn = document.getElementById('submitBtn');

    // Form submission handler
    if (characterForm && submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Submit button clicked');
            
            // Validate required fields
            const nameInput = document.querySelector('input[name*="name"]');
            const alignmentSelect = document.querySelector('select[name*="alignment"]');
            
            if (!nameInput || !nameInput.value.trim()) {
                alert('Please enter a character name');
                moveStep(-4); // Go back to step 1
                return;
            }
            
            if (!alignmentSelect || !alignmentSelect.value) {
                alert('Please select a character alignment');
                moveStep(-3); // Go back to step 2
                return;
            }
            
            // Show loading state
            const btnText = document.getElementById('submitBtnText');
            const btnLoading = document.getElementById('submitBtnLoading');
            
            if (btnText && btnLoading) {
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
            
            // Submit the form
            console.log('Submitting form...');
            characterForm.submit();
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
        previewMainImg.src = 'https://placehold.co/400x500/f1f5f9/94a3b8?text=Character+Image';
    }
    if (dropZoneUI) dropZoneUI.classList.remove('hidden');
    if (previewContainer) previewContainer.classList.add('hidden');
}
